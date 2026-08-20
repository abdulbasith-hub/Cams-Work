ALTER TABLE audit.mandaysextension
ADD COLUMN IF NOT EXISTS leaveid integer;

ALTER TABLE audit.ind_leavedetail
ADD COLUMN IF NOT EXISTS autoapproved character(1);

ALTER TABLE audit.ind_leavedetail
ADD COLUMN IF NOT EXISTS approvedremarks character varying;

SET search_path TO audit;

UPDATE audit.mandaysextension AS me
SET createdbyroletypecode = NULL
FROM audit.ind_leavedetail AS ld
WHERE ld.leaveid = me.leaveid
  AND COALESCE(ld.autoapproved, 'N') = 'Y'
  AND me.createdbyroletypecode IS NOT NULL;

UPDATE audit.ind_leavedetail
SET approvedremarks = 'Auto Approved By System'
WHERE COALESCE(autoapproved, 'N') = 'Y'
  AND NULLIF(BTRIM(COALESCE(approvedremarks, '')), '') IS NULL;

CREATE OR REPLACE PROCEDURE audit.auto_approve_mandays_for_leave(
    IN p_leaveid integer,
    IN p_userid integer,
    IN p_deptcode character varying,
    IN p_fromdate date,
    IN p_todate date,
    IN p_createdbyroletypecode character varying,
    IN p_transactiontypecode character varying,
    IN p_approveprocesscode character varying,
    IN p_remarks character varying,
    IN p_systemuserid integer,
    IN p_sessionuserchargeid integer,
    OUT o_status character varying,
    OUT o_message text,
    OUT o_updated_count integer
)
LANGUAGE plpgsql
AS $$
DECLARE
    v_leave_days integer := 0;
    v_max_short_leave_days integer := 0;
    v_schedule record;
    v_old_exit_date date;
    v_new_exit_date date;
    v_date_extension_days integer := 0;
    v_mandays_extension integer := 0;
    v_remaining_days integer := 0;
    v_row_count integer := 0;
    v_inserted_id integer;
    v_leave_transactiontypecode character varying := '01';
BEGIN
    o_status := 'success';
    o_message := null;
    o_updated_count := 0;

    IF p_fromdate IS NULL OR p_todate IS NULL OR p_fromdate > p_todate THEN
        o_status := 'error';
        o_message := 'Invalid leave date range.';
        RETURN;
    END IF;

    IF p_sessionuserchargeid IS NULL THEN
        o_status := 'error';
        o_message := 'Session user charge id is required for auto-approved leave.';
        RETURN;
    END IF;

    SELECT COUNT(*)::integer
    INTO v_leave_days
    FROM generate_series(p_fromdate, p_todate, interval '1 day') AS gs(leave_date)
    WHERE EXTRACT(ISODOW FROM gs.leave_date) < 6
      AND NOT EXISTS (
          SELECT 1
          FROM audit.mst_holiday AS h
          WHERE h.statusflag = 'Y'
            AND h.holiday_date = gs.leave_date::date
      );

    SELECT COALESCE(maxshortleavedays, 0)
    INTO v_max_short_leave_days
    FROM audit.mst_dept
    WHERE statusflag = 'Y'
      AND deptcode = p_deptcode
    LIMIT 1;

    IF COALESCE(v_max_short_leave_days, 0) = 0 OR v_leave_days > v_max_short_leave_days THEN
        o_message := 'No auto mandays extension required.';
        RETURN;
    END IF;

    FOR v_schedule IN
        SELECT DISTINCT
            ap.auditplanid,
            ap.mandays,
            ap.totalmandays,
            ap.teamsize,
            ap.carryforwardflag,
            sch.auditscheduleid,
            sch.todate,
            sch.entrymeetdate,
            sch.proposedexitmeetdate,
            COALESCE(sch.workallocationflag, 'N') AS workallocationflag,
            COALESCE(sch.gracedays, 0) AS gracedays
        FROM audit.auditplanteammember AS aptm
        JOIN audit.auditplan AS ap
            ON ap.auditteamid = aptm.auditplanteamid
        JOIN audit.auditplanteam AS apt
            ON apt.auditplanteamid = aptm.auditplanteamid
        JOIN audit.inst_auditschedule AS sch
            ON sch.auditplanid = ap.auditplanid
        JOIN audit.inst_schteammember AS schmem
            ON schmem.auditscheduleid = sch.auditscheduleid
           AND schmem.userid = aptm.userid
        JOIN audit.mst_institution AS inst
            ON inst.instid = ap.instid
        WHERE aptm.userid = p_userid
          AND schmem.statusflag = 'Y'
          AND inst.statusflag = 'Y'
          AND inst.deptcode = p_deptcode
          AND sch.statusflag = 'F'
          AND sch.entrymeetdate IS NOT NULL
          AND sch.exitmeetdate IS NULL
          AND COALESCE(sch.proposedexitmeetdate, sch.todate) >= p_fromdate
          AND sch.entrymeetdate <= p_todate
        ORDER BY sch.entrymeetdate, sch.auditscheduleid
    LOOP
        v_old_exit_date := COALESCE(v_schedule.proposedexitmeetdate, v_schedule.todate);

        IF v_old_exit_date IS NULL THEN
            CONTINUE;
        END IF;

        v_date_extension_days := v_leave_days;
        v_mandays_extension := v_leave_days * GREATEST(COALESCE(v_schedule.teamsize, 1), 1);

        IF v_date_extension_days > 0 THEN
            SELECT audit.calculatetodatewithmandaysteamsize(
                v_old_exit_date,
                v_date_extension_days + 1,
                0,
                0,
                'workingdays'
            )
            INTO v_new_exit_date;
        ELSE
            v_new_exit_date := v_old_exit_date;
        END IF;

        IF v_new_exit_date IS NULL THEN
            o_status := 'error';
            o_message := 'Failed to calculate proposed exit meet date.';
            RETURN;
        END IF;

        IF v_date_extension_days > 0 AND v_new_exit_date <= v_old_exit_date THEN
            v_new_exit_date := v_old_exit_date;
            v_remaining_days := v_date_extension_days;

            WHILE v_remaining_days > 0 LOOP
                v_new_exit_date := v_new_exit_date + 1;

                IF EXTRACT(ISODOW FROM v_new_exit_date) < 6
                   AND NOT EXISTS (
                       SELECT 1
                       FROM audit.mst_holiday AS h
                       WHERE h.statusflag = 'Y'
                         AND h.holiday_date = v_new_exit_date
                   ) THEN
                    v_remaining_days := v_remaining_days - 1;
                END IF;
            END LOOP;
        END IF;

        INSERT INTO audit.mandaysextension (
            transactiontypecode,
            auditscheduleid,
            remarks,
            approvedremarks,
            statusflag,
            processcode,
            updatedby,
            updatedon,
            oldmandays,
            extramandays,
            newmandays,
            oldpurposedexitmeetdate,
            newpurposedexitmeetdate,
            teamsize,
            oldtotalmandays,
            newtotalmandays,
            createdbyroletypecode,
            leaveid,
            createdby,
            createdon
        ) VALUES (
            p_transactiontypecode,
            v_schedule.auditscheduleid,
            p_remarks,
            p_remarks,
            'Y',
            p_approveprocesscode,
            p_systemuserid,
            clock_timestamp(),
            v_schedule.mandays,
            v_mandays_extension,
            v_schedule.mandays + v_mandays_extension,
            v_old_exit_date,
            v_new_exit_date,
            v_schedule.teamsize,
            CASE
                WHEN COALESCE(v_schedule.carryforwardflag, 'N') = 'Y'
                    THEN COALESCE(v_schedule.totalmandays, 0)
                ELSE NULL
            END,
            CASE
                WHEN COALESCE(v_schedule.carryforwardflag, 'N') = 'Y'
                    THEN COALESCE(v_schedule.totalmandays, 0) + v_mandays_extension
                ELSE NULL
            END,
            NULL,
            p_leaveid,
            p_systemuserid,
            clock_timestamp()
        )
        RETURNING mandaysextensionid INTO v_inserted_id;

        IF v_inserted_id IS NULL THEN
            RAISE EXCEPTION 'Failed to insert mandays extension details.';
        END IF;

        UPDATE audit.inst_auditschedule
        SET proposedexitmeetdate = v_new_exit_date,
            gracedays = COALESCE(gracedays, 0) + v_date_extension_days,
            updatedby = p_systemuserid,
            updatedon = clock_timestamp()
        WHERE auditscheduleid = v_schedule.auditscheduleid;

        IF NOT FOUND THEN
            RAISE EXCEPTION 'Failed to update schedule details.';
        END IF;

        UPDATE audit.auditplan
        SET mandays = v_schedule.mandays + v_mandays_extension,
            totalmandays = CASE
                WHEN COALESCE(v_schedule.carryforwardflag, 'N') = 'Y'
                    THEN COALESCE(v_schedule.totalmandays, 0) + v_mandays_extension
                ELSE totalmandays
            END,
            updatedby = p_systemuserid,
            updatedon = clock_timestamp()
        WHERE auditplanid = v_schedule.auditplanid;

        IF NOT FOUND THEN
            RAISE EXCEPTION 'Failed to update audit plan details.';
        END IF;

        INSERT INTO audit.logothertrans_scheduledel (
            auditscheduleid,
            datatransfertypecode,
            workallocationstatus,
            fromuserid,
            updateplan,
            statusflag,
            createdby,
            createdon,
            leaveid
        ) VALUES (
            v_schedule.auditscheduleid,
            'NC',
            COALESCE(v_schedule.workallocationflag, 'N'),
            p_userid,
            'N',
            'Y',
            p_systemuserid,
            clock_timestamp(),
            p_leaveid
        )
        RETURNING othertrans_scheduledelid INTO v_inserted_id;

        IF v_inserted_id IS NULL THEN
            RAISE EXCEPTION 'Failed to insert schedule deletion log.';
        END IF;

        o_updated_count := o_updated_count + 1;
    END LOOP;

    UPDATE audit.ind_leavedetail
    SET processcode = p_approveprocesscode,
        autoapproved = 'Y',
        approvedremarks = 'Auto Approved By System',
        statusflag = 'Y',
        updatedby = p_systemuserid,
        updatedbyuserchargeid = p_systemuserid,
        updatedon = clock_timestamp()
    WHERE leaveid = p_leaveid
      AND userid = p_userid;

    GET DIAGNOSTICS v_row_count = ROW_COUNT;
    IF v_row_count = 0 THEN
        RAISE EXCEPTION 'Failed to update leave auto approval details.';
    END IF;

    UPDATE audit.historytransactions
    SET transstatus = 'I'
    WHERE leaveid = p_leaveid
      AND transactiontypecode = v_leave_transactiontypecode
      AND transstatus = 'A';

    INSERT INTO audit.historytransactions (
        leaveid,
        userid,
        transactiontypecode,
        processcode,
        remarks,
        transstatus,
        statusflag,
        forwardedbyuserchargeid,
        forwardedon
    ) VALUES (
        p_leaveid,
        p_userid,
        v_leave_transactiontypecode,
        p_approveprocesscode,
        'Auto Approved By System',
        'A',
        'Y',
        p_systemuserid,
        clock_timestamp()
    )
    RETURNING historytransactionsid INTO v_inserted_id;

    IF v_inserted_id IS NULL THEN
        RAISE EXCEPTION 'Failed to insert history transaction details.';
    END IF;

    UPDATE audit.transactiondetail
    SET userid = p_userid,
        transactiontypecode = v_leave_transactiontypecode,
        forwardedtouserchargeid = NULL,
        updatedbyuserchargeid = p_systemuserid,
        statusflag = 'Y',
        remarks = 'Auto Approved By System',
        updatedon = clock_timestamp()
    WHERE leaveid = p_leaveid;

    GET DIAGNOSTICS v_row_count = ROW_COUNT;
    IF v_row_count = 0 THEN
        INSERT INTO audit.transactiondetail (
            leaveid,
            userid,
            transactiontypecode,
            updatedbyuserchargeid,
            statusflag,
            createdbyuserchargeid,
            remarks,
            createdon,
            updatedon
        ) VALUES (
            p_leaveid,
            p_userid,
            v_leave_transactiontypecode,
            p_systemuserid,
            'Y',
            p_sessionuserchargeid,
            'Auto Approved By System',
            clock_timestamp(),
            clock_timestamp()
        )
        RETURNING transactiondetailid INTO v_inserted_id;

        IF v_inserted_id IS NULL THEN
            RAISE EXCEPTION 'Failed to insert transaction details.';
        END IF;
    END IF;

    o_message := 'Auto approved leave completed.';
EXCEPTION
    WHEN OTHERS THEN
        o_status := 'error';
        o_message := SQLERRM;
        o_updated_count := 0;
END;
$$;
