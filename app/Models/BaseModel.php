<?php

// app/Models/BaseModel.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    const HISTORYTRANS_HLC  = 'audit.historytrans_hlc';
    const AUDITPLANMAPPING_TABLE = 'audit.auditplanmapping';
    const GET_AUDITPLANNING_FUNC = 'audit.fn_get_auditplanning';
    const MST_FINANCIAL_TABLE = 'audit.mst_financialyear';
    const PRAUDIT_INSTMAP_TABLE = 'audit.mst_prauditinstmapping ';
    const MAPALLOCATIONOBJECTION_NEW_TABLE = 'audit.map_allocation_objection';
    const APMS_HLC_TABLE = 'audit.apms_hlc';
    const DEPT_TABLE = 'audit.mst_dept';
    const REGION_TABLE = 'audit.mst_region';
    const DIST_Table = 'audit.mst_district';
    const ROLETYPE = 'audit.mst_roletype';
    const DEPARTMENT_TABLE = 'audit.mst_dept';
    const SUBCATEGORY_TABLE = 'audit.mst_auditeeins_subcategory';
    const TRANSACTIONTYPE_TABLE = 'audit.mst_transactiontype';
    const OTHERTRANS_TABLE = 'audit.othertransactions';
    const STATE_TABLE = 'audit.mst_state';
    const MAPREGIONDISTRICT_TABLE = 'audit.map_regiondistrict';
    const supercheck_TABLE = 'audit.super_check';
    const AUDITEESCHEME_TABLE = 'audit.auditeescheme';
    const AUDITEEDEPARTMENT_TABLE = 'audit.mst_auditeedept';
    const IRREGULARITIES_TABLE = 'audit.mst_irregularities';
    const IRREGULARITIESCATEGORY_TABLE = 'audit.mst_irregularitiescategory';
    const IRREGULARITIESSUBCATEGORY_TABLE = 'audit.mst_irregularitiessubcategory';
	const MST_TRANSFERFROMTODEPT_TABLE = 'audit.mst_transferfromtodept';

    // ---------------------------------MAP CALL FOR RECORDS TABLE-----------------------------------------------------

    const CALLFORRECORDS_AUDITEE_TABLE = 'audit.callforrecords_auditee';
    const MAPCALLFORRECORDS_TABLE = 'audit.map_callforrecord';
    const MSTAUDITEEINSCATEGORY_TABLE = 'audit.mst_auditeeins_category';
    const TRANSCALLFORRECORDS_TABLE = 'audit.trans_callforrecords';
    const LEAVETYPE_TABLE = 'audit.mst_leavetype';
    const TEMPRANKUSERS_TABLE = 'audit.temp_ranked_users';
    const AUTOMATE_FUNCTION = 'audit.fnauditteamplanautomation';
    const FINALISEPLAN_FUNCTION = 'audit.distributeauditteamplan';
    const WORKALLOCATION_FUNCTION = 'audit.workallocationAutomation';
    const GROUP_TABLE = 'audit.group';
    const DESIGNATION_TABLE = 'audit.mst_designation';
    const TYPEOFAUDIT_TABLE = 'audit.mst_typeofaudit';
    const MAINOBJECTION_TABLE = 'audit.mst_mainobjection';
    // ------------------------------------SUB WORK ALLOCATION TABLES------------------------------------------------------
    const MAPINST_TABLE = 'audit.map_instdesig';

    // -----------------------------------------------END-----------------------------------------------------------------

    // --------------------------------------------END----------------------------------------------------------------
    // -------------------------------------------------Auditor institution mapping------------------------------------------------------------------------------------

    const AUDITOR_INSTMAPPING_TABLE = 'audit.auditor_instmapping';
    const DESIGNATION_Table = 'audit.mst_designation';

    // ------------------------------------WORK ALLOCATION TABLES------------------------------------------------------

    const MAJORWORKALLOCATION_TABLE = 'audit.mst_majorworkallocationtype';

    // -----------------------------------------------END-----------------------------------------------------------------

    // ------------------------------------SUB WORK ALLOCATION TABLES------------------------------------------------------

    const SUBWORKALLOCATION_TABLE = 'audit.mst_subworkallocationtype';
    const SLIPHISTORYTRANSACTION_TABLE = 'audit.sliphistorytransactions';
    // -----------------------------------------------END-----------------------------------------------------------------
    // ------------------------------------AUDITEE USER DETAILS TABLES------------------------------------------------------
    const INSTITUTION_TABLE = 'audit.mst_institution';
    const AUDITEEUSERDETAIL_TABLE = 'audit.audtieeuserdetails';
    const AUDITEEDEPT_TABLE = 'audit.mst_auditeedept';
    const AUDITEEDEPTREPORT_TABLE = 'audit.auditee_dept_reporting';

    // -----------------------------------------------END-----------------------------------------------------------------

    // Define common table names as constants or static properties
    //  const DEPT_TABLE = 'audit.mst_dept';
    // const DIST_Table = 'audit.mst_district';

    const MST_PRAUDIT_TITLE_TABLE = 'audit.mst_praudit_title';
    const AUDITDISTRICT_TABLE = 'audit.mst_auditdistrict';
    const REVENUEDISTRICT_TABLE = 'audit.mst_revenuedistrict';
    const ROLETYPE_TABLE = 'audit.mst_roletype';
    const ROLETYPEMAPPING_TABLE = 'audit.roletypemapping';
    const ROLEACTION_TABLE = 'audit.mst_roleaction';
    const ROLEMAPPING_TABLE = 'audit.rolemapping';
    const USERDETAIL_TABLE = 'audit.deptuserdetails';
    const CHARGEDETAIL_TABLE = 'audit.chargedetails';
    const USERCHARGEDETAIL_TABLE = 'audit.userchargedetails';
    // const REGION_TABLE      =    'audit.mst_region';
    const AUDITORINSTMAPPING_TABLE = 'audit.auditor_instmapping';
    const REPORTCONTENTS_table = 'audit.report_contents';
    const AUDITQUARTER_TABLE = 'audit.mst_auditquarter';
    const MAPYEARCODE_TABLE = 'audit.yearcode_mapping';
    const AUDITPERIOD_TABLE = 'audit.mst_auditperiod';
    const AUDITPLAN_TABLE = 'audit.auditplan';
    const AUDITPLANTEAM_TABLE = 'audit.auditplanteam';
    const AUDITPLANTEAMMEM_TABLE = 'audit.auditplanteammember';
    const INSTSCHEDULE_TABLE = 'audit.inst_auditschedule';
    const INSTSCHEDULEMEM_TABLE = 'audit.inst_schteammember';
    const TRANSACCOUNTDETAILS_TABLE = 'audit.trans_accountdetails';
    const ACCOUNTPARTICULARS_TABLE = 'audit.mst_accountparticulars';
    const FILEUPLOAD_TABLE = 'audit.fileuploaddetail';
    const TEAMMEMBER_Table = 'audit.auditplanteammember';
    // const TYPEOFAUDIT_TABLE = 'audit.mst_typeofaudit';
    const PROCESSFLAG_TABLE = 'audit.mst_process';
    const MAINOBJ_TABLE = 'audit.mst_mainobjection';
    const SUBOBJ_TABLE = 'audit.mst_subobjection';
    const TRANSAUDITSLIP_TABLE = 'audit.trans_auditslip';
    const TRANSWORKALLOCATION_TABLE = 'audit.trans_workallocation';
    const MAPWORKALLOCATION_TABLE = 'audit.map_workallocation';
    const MAJWORKALLOCATION_TABLE = 'audit.mst_majorworkallocationtype';
    const SLIPHISTORYDETAILS_TABLE = 'audit.sliphistorytransactions';
    const HOLIDAY_TABLE = 'audit.mst_holiday';
    // ////////////////////////followup//////////
    const MAPALLOCATIONOBJECTION_TABLE = 'audit.map_allocation_objection';
    const LAGACY_TABLE = 'audit.lagacy';

    // ------------------------------------Leave Management------------------------------------------------------

    const TRANSACTIONFLOW_TABLE = 'audit.transactionflow';
    const HISTORYTRANSACTION_TABLE = 'audit.historytransactions';
    const INDLEAVEDETAIL_TABLE = 'audit.ind_leavedetail';
    const TRANSACTIONDETAILTABLE = 'audit.transactiondetail';
    const INDLEAVEINDETAIL_TABLE = 'audit.ind_leavein_detail';

    // -----------------------------------------------END-----------------------------------------------------------------

    // --------------------------------------Functions--------------------------------------------------------------------
    const DATATRANSFERFROMTOUSER = 'audit.dataTransferFromToUser';
    const WORKALLOCATIONDISTRIBUTION = 'audit.workAllocationDistribution';
    const MIGRATEALLOCATIONSLIP_FUNC = 'audit.migrate_work_allocation_and_audit_slip';
    const READYFORAUTOMATE_FUNCTION = 'audit.readyforAutomatePlan';
    const NODATACHANGE_FUNCTION = 'audit.nodatachange';

    // --------------------------------------Inspection--------------------------------------------------------------------

    const AUDITINSPECTION_TABLE = 'audit.mst_auditinspection';
    const TRANSAUDITINSPECTION_TABLE = 'audit.trans_auditinspection';
    const INSPECTIONHISTORY_TABLE = 'audit.inspectionhistorytrans';
    // ---------------------------Audit Mode------------------------------------------------------------------------------
    const AUDITMODE_TABLE = 'audit.mst_auditmode';
    // --------------------------------------checklistplan--------------------------------------------------------------------
    const TEAMASSIGNMENTS_TABLE = 'audit.team_assignments';
    const LOOP_UNTILFINISHED_FUNCTION = 'audit.loop_until_finished';
    const CHECKLISTDETAIL_FUNCTION = 'audit.checklistdel';
    // --------------------------------------OTP --------------------------------------------------------------------
    const OTPVERIFY_TABLE = 'audit.otp_verify';
    // --------------------------------------Lagacy followup--------------------------------------------------------------------
    const TRANSFOLLOWUP_TABLE = 'audit.trans_followup';
    const FOLLOWUPLIABILITY_TABLE = 'audit.followup_liability';
    const LAGACYFILEUPLOAD_TABLE = 'audit.lagacyfileupload';
    const MST_STATEOFPARA_TABLE = 'audit.mst_stateofpara';
    const MST_TYPEOFPARA_TABLE = 'audit.mst_typeofpara';
    const TRANSPARA_TABLE = 'audit.trans_para';
    const PARAFILEUPLOAD_TABLE = 'audit.parafileupload';
    const HISTORYTRANSPARA_TABLE = 'audit.historytrans_para';
    const ACTIONPARA_TABLE = 'audit.mst_actionsonpara';
    const LIABILITY_TABLE = 'audit.liability';
    const SLIPFILEUPLOAD_TABLE = 'audit.slipfileupload';
    const SEVERITY_TABLE = 'audit.mst_severity';
    const LOGOTHERTRANS_SCHEDULEDELTABLE = 'audit.logothertrans_scheduledel';
    const MANDAYSEXTENSION = 'audit.mandaysextension';
    const SCHEULEREQUEST = 'audit.schedulerequest';
    const FUTUREPLANHEADTRANSFER = 'audit.futureplanheadtransfer';
    const LOGOTHERTRANSPLANDEL = 'audit.logothertrans_plandel';
    const fn_headchangeforplan = 'audit.headchangeforplan';
    const fn_calculateTodateWithMandaysTeamsize = 'audit.calculateTodateWithMandaysTeamsize';
    const ALTEREDINST_TABLE = 'audit.altered_institutiondetails';
    // -------------------------------- Template Audit------------------------------------
    const TEMPLATE_AUDIT_TYPE_TABLE = 'audit.mst_depttemplatetype';
    const TEMPLATE_AUDIT_HEADER_TABLE = 'audit.mst_templateheader';
    const TEMPLATE_AUDIT_MAIN_TYPE_TABLE = 'audit.mst_templatemaintype';
    const TEMPLATE_AUDIT_SUB_TYPE_TABLE = 'audit.mst_templatesubtype';
    const TEMPLATE_AUDIT_PLAN_TABLE = 'audit.templateauditplan';
    const TMPAUDIT_FORM_MAPPING_TABLE = 'audit.template_form_mapping';
    const MST_HRIAZONE_TABLE = 'audit.mst_hriazone';
    const MST_HRIACIRCLE_TABLE = 'audit.mst_hriacircle';
    const HRIA_DETAILS_TABLE = 'audit.hria_details';
    const TEMP_HRIA_TABLE = 'audit.template_hria';
    const TEMP_HRIA_REMARKS_TABLE = 'audit.template_hria_remarks';
    const LFA_DETAILS_TABLE = 'audit.lfa_details';
    const TEMP_LFA_TABLE = 'audit.template_lfa';
    const TEMP_LFA_REMARKS_TABLE = 'audit.template_lfa_remarks';
    const DCA_DETAILS_TABLE = 'audit.dca_details';
    const TEMP_DCA_TABLE = 'audit.template_dca';
    const TEMP_DCA_REMARKS_TABLE = 'audit.template_dca_remarks';
    const MILK_DETAILS_TABLE = 'audit.milk_details';
    const TEMP_MILK_TABLE = 'audit.template_milk';
    const TEMP_MILK_REMARKS_TABLE = 'audit.template_milk_remarks';
    const TEMP_AUDIT_MAPPING = 'audit.template_mapping';
    const SLIP_FILEUPLOAD_TABLE = 'audit.slipfileupload';
    const LOGFOLLOWUP_TABLE = 'audit.auditfollowup_updhistory';
    const HOD_CREATE_CHARGE = 'audit.hodchargedetails';
    const HOD_ASSIGN_CHARGE = 'audit.hoduserchargedetails';
    const TEMP_TEMPLATE_AUDIT_PLAN_TABLE = 'audit.temp_templateauditplan';

}
