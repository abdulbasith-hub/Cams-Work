<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Bind the SessionService to the container
        // $this->app->singleton(SessionService::class, function ($app) {
        //     return new SessionService();
        // });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->share('workallocation', 'WA');
        view()->share('datachange', 'CD');
        view()->share('DGA_roletypecode', '04');  // Set and share type1 globally in one line
        view()->share('Ho_roletypecode', '03');  // Set and share type1 globally in one line
        view()->share('Re_roletypecode', '02');  // Set and share type1 globally in one line
        view()->share('Dist_roletypecode', '01');  // Set and share type1 globally in one line
        view()->share('Admin_roletypecode', '05');  // Set and share type1 globally in one line
	    view()->share('NIC_roletypecode', '07');
        view()->share('auditeelogin', 'I');  // Set and share type1 globally in one line
        view()->share('auditorlogin', 'A');  // Set and share type1 globally in one line

        view()->share('get_nowtime', Carbon::now('Asia/Kolkata'));
        view()->share('Reject', 'I');
        view()->share('Forward', 'F');
        view()->share('Approve', 'A');
        view()->share('Insert', 'S');
        view()->share('accepted', 'A');



        view()->share('insert', 'insert');
        view()->share('update', 'update');

        view()->share('savebtn', 'savebtn');
        view()->share('savedraftbtn', 'savedraftbtn');
        view()->share('clearbtn', 'clearbtn');
        view()->share('updatebtn', 'updatebtn');
        view()->share('finalizebtn', 'finalizebtn');
        view()->share('cancelbtn', 'cancelbtn');
        view()->share('approvebtn', 'approvebtn');
        view()->share('forwardbtn', 'forwardbtn');

        view()->share('statecode', 'TN');

        view()->share('Auditeechargeid', '5');
        view()->share('Leavetransactiontypecode', '01');
        view()->share('auditor_roleactioncode', '04');
        view()->share('diversionTransactiontypecode', '07');


        view()->share('Inflag', 'I');
        view()->share('Outflag', 'O');

        view()->share('inactive', 'I');

        view()->share('I', 'Auditee');  // Set and share type1 globally in one line
        view()->share('A', 'Auditor');  // Set and share type1 globally in one line

        view()->share('uploadsfilefoldername', 'uploads');
        view()->share('slipfileuploadpath', 'slipauditor');
        view()->share('auditeefileuploadpath', 'auditeeReply');
        view()->share('alterplan', 'alterplan');
        view()->share('annexturepath', 'report_annexures');



        view()->share('auditeedefaultPass', 'Dgcams@2025');

        view()->share('transfercode', '06');
        view()->share('transferwithpromocode', '08');
        view()->share('promotioncode', '05');
        view()->share('AuditorRoleactioncode', '04');
        view()->share('current_quarter', 'Q1');

	 view()->share('lagacyfileuploadpath', 'lagacy');
        view()->share('financialaudit', ['01', '03', '14', '15', '16']);
        view()->share('lagacyparatype', 'L');
        view()->share('normalparatype', 'C');

        view()->share('PUroleactioncode', '18');
        view()->share('PUADroleactioncode', '19');
        view()->share('Dirroleactioncode', '08');

        view()->share('parafileuploadpath', 'Para');
view()->share('psa_roleaction', ['18', '19']);

        view()->share('months',  [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ]);

  	view()->share('MandaysExtenstion', '09');
	view()->share('LeaveIntransactiontypecode', '11');
view()->share('grievanceLetter', 'G');
        view()->share('AuditLetter', 'A');

  view()->share('Approve_Processcode', 'P');
        view()->share('Entry_processcode', 'E');
 view()->share('templateaudit', 'templateaudit');
view()->share('HOD_roletypecode', '08');
 view()->share('Admin_roleactioncode', '01');
view()->share('removal_response_action', '11');
        view()->share('removal_parts_action', '12');
        view()->share('respons_removal_processcode', 'PA');
        view()->share('parts_removal_processcode', 'PR');
		        view()->share('rtd_committee_roleaction', '20');
        view()->share('rtdcom_actroleactioncode', 'RC');
        view()->share('rtdcom_to_auditee', 'L');
        view()->share('auditee_to_rtdcom', 'V');
      view()->share('performance_audit', 'P');
      view()->share('roleaction_for_hodcharge', '17');
         view()->share('roletype_for_hodcharge', '08');
        view()->share('redo_time', '180');//seconds
        view()->share('Para_roleactioncode', '18');
        view()->share('Para_AD_roleactioncode', '19');

        view()->share('dept_hlc_state', '04');
        view()->share('state_hlc_state', '03');
        view()->share('district_hlc_state', '02');

        view()->share('dlc_filefolder', 'HLC');

        view()->share('dlc_roleactioncode', '23');
        view()->share('dehc_roleactioncode', '24');
        view()->share('shlc_roleactioncode', '25');

        view()->share('dlc_recoveryofloss', '04');
        view()->share('dlc_writeoff', '05');
        view()->share('dlc_accept', '01');
        view()->share('dlc_forwardtodhlc', '09');
        view()->share('dlc_pending', '10');
        view()->share('dlc_appropriateaction', '06');
        view()->share('dlc_forwardtoshlc', '07');

        view()->share('district_hlc_roletypecode', '01');
        view()->share('dehc_roletypecode', '03');
        view()->share('shlc_roletypecode', '03');

        view()->share('dlc_actroleactioncode', 'DL');
        view()->share('delc_actroleactioncode', 'DE');
        view()->share('shlc_actroleactioncode', 'SL');

        view()->share('dlc_savedraft_processcode', 'DE');
        view()->share('dehlc_to_shlc', 'DS');
        view()->share('pending_hlc', 'PH');
        view()->share('paraaccept', 'A');

        view()->share('DLC_to_auditee', 'B');
        view()->share('dehlc_to_auditee', 'BE');



        view()->share('auditee_to_DLC', 'D');
        view()->share('auditee_to_DEHLC', 'AE');
        view()->share('auditee_to_SHLC', 'AS');


        view()->share('DLC_to_DEHLC', 'DH');
        view()->share('DLC_to_SHLC', 'IS');


        view()->share('dept_hlc_allowed', ['DH', 'AE', 'X']);
        view()->share('state_hlc_allowed', ['AS', 'IS', 'X', 'DS']);
        view()->share('dist_hlc_allowed', ['D', 'X']);

        view()->share('dlc_roleaction', ['23', '24']);
        view()->share('frwd_to_approver', 'FA');
        view()->share('RJD_roleactioncode', '26');

        view()->share('hlcs_stateofparacode', ['02', '03', '04']);


        view()->share('board_cat', '29');
        view()->share('reject_dlcpara', 'R');
        view()->share('approved_dlcpara', 'P');


        view()->share('aprrover_usertypecode', 'AP');
        view()->share('dlc_usertypecode', 'D');
        view()->share('department_dlc_usertypecode', 'DE');


        view()->share('slc_to_auditee', 'SA');
        view()->share('auditee_to_DLC_process', ['D', 'AE', 'AS']);
        view()->share('DLC_TO_auditee_process', ['B', 'SA', 'BE']);

view()->share('otp_roleactioncodes', ['02', '07', '08']);

	view()->share('Helpdesk_roleactioncode', '27');
	view()->share('charge_membercount_limit', 1 );

	 view()->share('Stateadminchargeid', '1');
	        view()->share('NICAdminchargeid', '907');
	        view()->share('helpdesk_developer_roleactioncodes', ['21', '22']);
        view()->share('internal_cc_emails', [
            'abdbasi51@gmail.com',
            'basimohamed835@gmail.com',
        ]);

	view()->share('consolidation_entry', '21');
	view()->share('consolidation_approval', '22');

view()->share('autoremakrs', 'Auto Approved mandays for this leave.');

    }
}
