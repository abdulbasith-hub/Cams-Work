<?php

namespace App\Models;

use App\Helpers\CryptoHelper;
use App\Models\BaseModel;
use App\Services\PHPMailerService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use DateTime;

class SmsmailModel extends Model
{
    protected $smsService;
    protected $mailService;
    protected $PHPMailerService;

    protected static $deptartment_table = BaseModel::DEPARTMENT_TABLE;
    protected static $institution_table = BaseModel::INSTITUTION_TABLE;
    protected static $auditplan_table = BaseModel::AUDITPLAN_TABLE;
    protected static $temprankusers_table = BaseModel::TEMPRANKUSERS_TABLE;
    protected static $designation_table = BaseModel::DESIGNATION_TABLE;
    protected static $userdetail_table = BaseModel::USERDETAIL_TABLE;
    protected static $auditplanteam_table = BaseModel::AUDITPLANTEAM_TABLE;
    protected static $auditplanteammem_table = BaseModel::AUDITPLANTEAMMEM_TABLE;
    protected static $typeofaudit_table = BaseModel::TYPEOFAUDIT_TABLE;
    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $instauditschedule_table = BaseModel::INSTSCHEDULE_TABLE;
    protected static $instauditschedulemem_table = BaseModel::INSTSCHEDULEMEM_TABLE;
    protected static $yearcodemapping_table = BaseModel::MAPYEARCODE_TABLE;
    protected static $transaccountdetails_table = BaseModel::TRANSACCOUNTDETAILS_TABLE;
    protected static $accountparticulars_table = BaseModel::ACCOUNTPARTICULARS_TABLE;
    protected static $fileuploaddetail_table = BaseModel::FILEUPLOAD_TABLE;
    protected static $transcallforrec_table = BaseModel::TRANSCALLFORRECORDS_TABLE;
    protected static $automate_function = BaseModel::AUTOMATE_FUNCTION;
    protected static $finaliseplan_function = BaseModel::FINALISEPLAN_FUNCTION;
    protected static $auditquarter_table = BaseModel::AUDITQUARTER_TABLE;
    protected static $auditperiod_table = BaseModel::AUDITPERIOD_TABLE;
    protected static $callforrec_table = BaseModel::CALLFORRECORDS_AUDITEE_TABLE;
    protected static $mapregiondistrict_table = BaseModel::MAPREGIONDISTRICT_TABLE;
    protected static $dist_table = BaseModel::DIST_Table;
    protected static $auditdistrict_table = BaseModel::AUDITDISTRICT_TABLE;
    protected static $userchargedetail_table = BaseModel::USERCHARGEDETAIL_TABLE;
    protected static $chargedetail_table = BaseModel::CHARGEDETAIL_TABLE;
    protected static $readyforautomate_function = BaseModel::READYFORAUTOMATE_FUNCTION;
    protected static $auditor_instmapping_table = BaseModel::AUDITOR_INSTMAPPING_TABLE;
    protected static $subcategory_table = BaseModel::SUBCATEGORY_TABLE;
    // --------------------------------------OTP --------------------------------------------------------------------
    protected static $otpverify_table = BaseModel::OTPVERIFY_TABLE;

    public function __construct(SmsService $smsService = null, PHPMailerService $mailService = null)
    {
        parent::__construct();  // <-- Make sure to call parent constructor
        $this->smsService = $smsService ?? new SmsService();
        $mailer = $mailService ?? new PHPMailerService();
        $this->mailService = $mailer;
        $this->PHPMailerService = $mailer;
    }

    public function sendHelpdeskEmailNotification(?string $to, string $subject, string $headline, array $details = [], $cc = [])
    {
        $to = trim((string) $to);

        if ($to === '' || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid recipient email.';
        }

        $ccEmails = [];
        foreach ((array) $cc as $ccEmail) {
            $ccEmail = trim((string) $ccEmail);

            if ($ccEmail !== '' && filter_var($ccEmail, FILTER_VALIDATE_EMAIL)) {
                $ccEmails[] = $ccEmail;
            }
        }
        $ccEmails = array_values(array_unique($ccEmails));

        $detailRows = '';
        foreach ($details as $label => $value) {
            $detailRows .= '<tr>'
                . '<td style="width:34%;padding:11px 14px;border:1px solid #e2e8f0;font-weight:700;background:#f8fafc;color:#0f172a;vertical-align:top;">' . htmlspecialchars((string) $label, ENT_QUOTES, 'UTF-8') . '</td>'
                . '<td style="padding:11px 14px;border:1px solid #e2e8f0;color:#111827;vertical-align:top;">' . nl2br(htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8')) . '</td>'
                . '</tr>';
        }

        $htmlContent = '<div style="margin:0;padding:28px 12px;background:#f3f6fb;font-family:Arial,sans-serif;color:#111827;line-height:1.5;">'
            . '<div style="max-width:760px;margin:0 auto;background:#ffffff;border:1px solid #dbe5f2;border-radius:12px;overflow:hidden;box-shadow:0 10px 28px rgba(15,23,42,0.08);">'
            . '<div style="background:#5b7df0;padding:18px 22px;text-align:center;">'
            . '<h2 style="margin:0;color:#ffffff;font-size:22px;line-height:1.3;">' . htmlspecialchars($headline, ENT_QUOTES, 'UTF-8') . '</h2>'
            . '</div>'
            . '<div style="padding:22px;">'
            . '<table role="presentation" style="border-collapse:collapse;width:100%;font-size:14px;margin:0 auto;">' . $detailRows . '</table>'
            . '<p style="margin:18px 0 0;color:#64748b;font-size:13px;text-align:center;">Please login to CAMS Helpdesk for more details.</p>'
            . '</div>'
            . '</div>'
            . '</div>';

        return $this->mailService->sendEmail($to, $subject, $htmlContent, $ccEmails);
    }

    public function sendforgetpassword($data, $Lang)
    {
        /* SEND INTIMATION MAIL */
        $to = $data['email'];
        $subject = 'CAMS - Password Reset Request';
        // $body = '<h1>This is a test email</h1><p>Sending emails using PHPMailer in Laravel</p>';
        $htmlContent = file_get_contents(resource_path('views/Email/ForgotPassword.html'));
        $dynamicData = [
            'pwd_reset_url' => $data['redirect_url'],
            'pwd_reset' => $data['newpwd'],
            'username' => $data['username']
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        return $this->mailService->sendEmail($to, $subject, $htmlContent);
    }

     public function sendIntimation_mail($auditscheduleid)
    {
        $inst_det = DB::table(self::$instauditschedule_table . ' as sch')
            ->join(self::$auditplan_table . ' as plan', 'plan.auditplanid', '=', 'sch.auditplanid')
            ->join(self::$institution_table . ' as inst', 'plan.instid', '=', 'inst.instid')
            ->select(
                'inst.instename',
                DB::raw("TO_CHAR(sch.fromdate, 'DD/MM/YY') as fromdate"),
                DB::raw("TO_CHAR(sch.todate, 'DD/MM/YY') as todate"),
                'inst.nodalperson_ename',
                'inst.mobile',
                'inst.email',
                'inst.nodalperson_desigcode'
            )
            ->where('sch.auditscheduleid', $auditscheduleid)
            ->where('sch.statusflag', 'F')
            ->get();

        if ($inst_det->isEmpty()) {
            return 'No institution found.';
        }


        $auditperiod = $inst_det[0]->fromdate . ' - ' . $inst_det[0]->todate;
        $user_desig  = $inst_det[0]->nodalperson_ename . ' ( ' . $inst_det[0]->nodalperson_desigcode . ' )';


        $to = $inst_det[0]->email;

        $username = $inst_det[0]->nodalperson_ename;
        $heading = 'Audit Schedule Details';
        $subject = 'Audit Schedule Details';

        $htmlContent = file_get_contents(resource_path('views/Email/automateschedule.html'));
        $dynamicData = [
            'username' => $user_desig,
            'instename' =>  $inst_det[0]->instename,
            'heading' => $heading,
            'auditperiod' => $auditperiod

        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        return $this->mailService->sendEmail($to, $subject, $htmlContent);
    }

    public function sendscheduleInstitutes($distcode, $deptcode, $auditquartercode)
    {
        $results = DB::table(self::$institution_table . ' as ai')
            ->join('audit.temp_ranked_users as tru', 'ai.instid', '=', 'tru.instid')
            ->join(self::$userdetail_table . ' as du', 'du.deptuserid', '=', 'tru.deptuserid')
            ->select(
                'ai.instename',
                'tru.deptuserid',
                'du.username',
                'tru.quartercode'
            )
            ->where('tru.distcode', '=', $distcode)
            ->where('tru.deptcode', '=', $deptcode)
            ->where('tru.quartercode', '=', $auditquartercode)
            ->get();

        // Group results by username and then collect the institutions they belong to
        $grouped = $results->groupBy('deptuserid')->map(function ($userGroup) {
            return $userGroup->map(function ($item) {
                return $item->instename;
            })->unique()->toArray();
        });

        foreach ($grouped as $groupkey => $groupval) {
            $deptuser = DB::table(self::$userdetail_table)
                ->select(
                    'email',
                    'username'
                )
                ->where('deptuserid', '=', $groupkey)
                ->first();

            $emailaddress = $deptuser->email;
            $username = $deptuser->username;

            $dataappend = '';

            $slno = 1;

            foreach ($groupval as $key => $inst) {
                $dataappend .= '<tr><td width="10%" align="left" style="padding:18px; border:1px solid #ddd;">' . $slno . '</td><td width="90%" align="left" style="padding:18px; border:1px solid #ddd;">' . $inst . '</td></tr>';
                $slno++;
            }

            $to = $emailaddress;
            $subject = 'List of Institutions Scheduled for Audit-sent';
            // $body = '<h1>This is a test email</h1><p>Sending emails using PHPMailer in Laravel</p>';
            $htmlContent = file_get_contents(resource_path('views/Email/auditschedule3.html'));

            $explodequarter = explode('Q', $results[0]->quartercode);
            $dynamicData = [
                'username' => $username,
                'institute_list' => $dataappend,
                'quarterno' => $explodequarter[1]
            ];

            // Replace placeholders with dynamic values
            foreach ($dynamicData as $key => $value) {
                // Replace [key] with actual values
                $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
            }

            $this->mailService->sendEmail($to, $subject, $htmlContent);
        }
    }

    public function sendIntimation($audit_scheduleid)
    {
        /* SEND INTIMATION SMS */
        $institutions = DB::table('audit.inst_auditschedule as ins')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->where('ins.auditscheduleid', $audit_scheduleid)
            ->select('inst.instename', 'inst.mobile', 'ins.fromdate')
            ->get();

        if ($institutions->isEmpty()) {
            return 'No institution found.';
        }

        $instename = explode(',', $institutions[0]->instename)[0];
        $instename = substr(trim($instename), 0, 30);

        $mobileNumber = $institutions[0]->mobile;
        $fromdate = Carbon::createFromFormat('Y-m-d', $institutions[0]->fromdate)->format('d/m/Y');

        $data = [
            'inst_name' => $instename,
            'mobileNumber' => $mobileNumber,
            'fromdate' => $fromdate
        ];

        // $mobileNumber='7604969847';
        $Lang = 'en';

        return $this->smsService->sendSms($mobileNumber, '', $data, 'sent_intimation', $Lang);
    }

    public function send_exitmeetingsms($audit_scheduleid, $Lang)
    {
        $institutions = DB::table('audit.inst_auditschedule as ins')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->join('audit.audtieeuserdetails as auditee', 'inst.instid', '=', 'auditee.instid')
            ->join('audit.inst_schteammember as at', function ($join) {
                $join
                    ->on('at.auditscheduleid', '=', 'ins.auditscheduleid')
                    ->where('at.auditteamhead', '=', 'Y');
            })
            ->join('audit.deptuserdetails as du', 'at.userid', '=', 'du.deptuserid')
            ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->join('audit.mst_district as dist', 'dist.distcode', '=', 'du.distcode')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'du.deptcode')
            ->where('ins.auditscheduleid', $audit_scheduleid)
            ->select('inst.instename',
                'inst.mobile',
                'ins.exitmeetdate',
                'auditee.username as auditeeusername',
                'auditee.email as auditeeemail',
                'auditee.pwd as auditeepassword',
                'auditee.mobilenumber as auditeemobile',
                'du.username as auditorusername',
                'du.email as auditoremail',
                'du.mobilenumber as auditormobile',
                'desig.desigelname as auditordesig',
                'dist.distename as auditordistrict',
                'dept.deptesname as auditordept')
            ->get();

        if ($institutions->isEmpty()) {
            return 'No institution found.';
        }

        $mobileNumber = $institutions[0]->auditeemobile;

        $Instname = $institutions[0]->instename;
        $auditeename = $institutions[0]->auditeeusername;
        $auditorusername = $institutions[0]->auditorusername;
        // $mobileNumber = $institutions[0]->mobile;
        $exitmeetdate = Carbon::createFromFormat('Y-m-d', $institutions[0]->exitmeetdate)->format('d/m/Y');

        $exitmeetDuedate = Carbon::createFromFormat('Y-m-d', $institutions[0]->exitmeetdate)
            ->addDays(2)
            ->format('d/m/Y');

        // Start building the count query
        $countQuery = DB::table('audit.trans_auditslip as tas')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'tas.auditplanid')
            ->where('tas.auditscheduleid', $audit_scheduleid);

        // Add the selectRaw part for counting, with DISTINCT and GROUP BY to avoid duplicates
        $countQuery
            ->selectRaw("COUNT(DISTINCT CASE WHEN tas.processcode IS NOT NULL THEN tas.auditslipid END) as totalslips,
                                 COUNT(DISTINCT CASE WHEN tas.processcode = 'A' THEN tas.auditslipid END) as droppedslips,
                                 COUNT(DISTINCT CASE WHEN tas.processcode = 'X' THEN tas.auditslipid END) as convertedslips,
                                 COUNT(DISTINCT CASE WHEN tas.processcode NOT IN ('A', 'X') THEN tas.auditslipid END) as pendingslips
                               ")
            ->groupBy('tas.auditscheduleid');  // Group by 'auditscheduleid' to avoid duplicates

        // Execute the query and get the first result
        $countQuery = $countQuery->first();

        $data = json_decode(json_encode($countQuery), true);  // Convert stdClass to array

        // $data = json_decode($countQuery, true);

        // Access the values, ensuring they aren't negative
        $totalslips = isset($data['totalslips']) && $data['totalslips'] >= 0 ? $data['totalslips'] : 0;
        $droppedslips = isset($data['droppedslips']) && $data['droppedslips'] >= 0 ? $data['droppedslips'] : 0;
        $convertedslips = isset($data['convertedslips']) && $data['convertedslips'] >= 0 ? $data['convertedslips'] : 0;
        $pendingSlips = isset($data['pendingslips']) && $data['pendingslips'] >= 0 ? $data['pendingslips'] : 0;

        /* SEND EXIT MEETING MAIL */
        $to = $institutions[0]->auditeeemail;
        $subject = 'Exit Meeting of ' . $Instname . ' Communicated';
        $htmlContent = file_get_contents(resource_path('views/Email/ExitMeeting.html'));
        $dynamicData = [
            'auditeename' => $auditeename,
            'Institution Name' => $Instname,
            'meetdate' => $exitmeetdate,
            'auditor_head' => $auditorusername,
            'Designation' => $institutions[0]->auditordesig,
            'District' => $institutions[0]->auditordistrict,
            'Department' => $institutions[0]->auditordept,
            'totalslip' => $totalslips,
            'droppedslip' => $droppedslips,
            'convertedslip' => $convertedslips,
            'pendingslip' => $pendingSlips,
            'due_exitdate' => $exitmeetDuedate
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        $this->mailService->sendEmail($to, $subject, $htmlContent);
        /* SEND EXIT MEETING SMS */
        // $Lang = 'ta';
        $data = [
            'mobileNumber' => $mobileNumber,
            'exitmeetdate' => $exitmeetdate,
            'pendingslip' => $pendingSlips
        ];
        $Lang = 'en';

        return $this->smsService->sendSms($mobileNumber, '', $data, 'sent_exitmeeting', $Lang);
    }

    public function send_entrymeeting($audit_scheduleid)
    {
        $institutions = DB::table('audit.inst_auditschedule as ins')
            ->join('audit.auditplan as ap', 'ap.auditplanid', '=', 'ins.auditplanid')
            ->join('audit.mst_institution as inst', 'inst.instid', '=', 'ap.instid')
            ->join('audit.audtieeuserdetails as auditee', 'inst.instid', '=', 'auditee.instid')
            ->join('audit.inst_schteammember as at', function ($join) {
                $join
                    ->on('at.auditscheduleid', '=', 'ins.auditscheduleid')
                    ->where('at.auditteamhead', '=', 'Y');
            })
            ->join('audit.deptuserdetails as du', 'at.userid', '=', 'du.deptuserid')
            ->join('audit.mst_designation as desig', 'desig.desigcode', '=', 'du.desigcode')
            ->join('audit.mst_district as dist', 'dist.distcode', '=', 'du.distcode')
            ->join('audit.mst_dept as dept', 'dept.deptcode', '=', 'du.deptcode')
            ->where('ins.auditscheduleid', $audit_scheduleid)
            ->select('inst.instename',
                'inst.mobile',
                'ins.entrymeetdate',
                'auditee.username as auditeeusername',
                'auditee.email as auditeeemail',
                'auditee.pwd as auditeepassword',
                'du.username as auditorusername',
                'du.email as auditoremail',
                'du.mobilenumber as auditormobile',
                'desig.desigelname as auditordesig',
                'dist.distename as auditordistrict',
                'dept.deptesname as auditordept')
            ->get();

        if ($institutions->isEmpty()) {
            return 'No institution found.';
        }

        $mobileNumber = $institutions[0]->mobile;
        $entrymeetdate = Carbon::createFromFormat('Y-m-d', $institutions[0]->entrymeetdate)->format('d/m/Y');

        $Instname = $institutions[0]->instename;
        $auditeename = $institutions[0]->auditeeusername;
        $auditorusername = $institutions[0]->auditorusername;

        $camsurl = 'cams.tn.gov.in';

        // $password = CryptoHelper::decryptPassword($institutions[0]->auditeepassword);
        $password = 'Cams@123';

        /* SEND INTIMATION MAIL */
        $to = $institutions[0]->auditeeemail;
        $subject = 'Entry Meeting Schedule- Invitation to audit entry conference meeting';
        // $body = '<h1>This is a test email</h1><p>Sending emails using PHPMailer in Laravel</p>';
        $htmlContent = file_get_contents(resource_path('views/Email/EntryMeeting.html'));
        $dynamicData = [
            'auditeename' => $auditeename,
            'Institution Name' => $Instname,
            'meetdate' => $entrymeetdate,
            'auditor_head' => $auditorusername,
            'auditor_email' => $institutions[0]->auditoremail,
            'auditor_mobile' => $institutions[0]->auditormobile,
            'camsurl' => $camsurl,
            'username' => $institutions[0]->auditeeemail,
            'password' => $password,
            'Designation' => $institutions[0]->auditordesig,
            'District' => $institutions[0]->auditordistrict,
            'Department' => $institutions[0]->auditordept
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        return $this->mailService->sendEmail($to, $subject, $htmlContent);
    }

    public function sendotp_allocateplan($data, $username)
    {
        try {
            // Save OTP first
            $saveResult = self::saveOTP($data);

            if ($saveResult['status'] !== 'success') {
                return ['status' => 'error', 'message' => 'OTP save failed: ' . $saveResult['message']];
            }

            // Prepare email content
            $to = $data['email'];
            $subject = 'CAMS - OTP Password';
            $htmlContent = file_get_contents(resource_path('views/Email/allocateplan.html'));

            $dynamicData = [
                'rand_otp' => $data['otp'],
                'username' => $username
            ];

            // Replace placeholders
            foreach ($dynamicData as $key => $value) {
                $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
            }

            return $this->mailService->sendEmail($to, $subject, $htmlContent);
        } catch (\Exception $e) {
            \Log::error('sendotp_allocateplan error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to send OTP.', 'error' => $e->getMessage()];
        }
    }

    public static function saveOTP($data)
    {
        try {
            DB::table('audit.otp_verify')->insert([
                'userid' => $data['userid'],
                'email' => $data['email'],
                'otp' => $data['otp'],
                'is_verified' => 'N',
                'createdon' => View::shared('get_nowtime'),
            ]);

            return ['status' => 'success', 'message' => 'OTP saved successfully.'];
        } catch (\Exception $e) {
            // Optional: log the error
            \Log::error('Failed to save OTP: ' . $e->getMessage());

            return ['status' => 'error', 'message' => 'Failed to save OTP.', 'error' => $e->getMessage()];
        }
    }

    public static function verifyOTP($data)
    {
        try {
            $otpRecord = DB::table(self::$otpverify_table)
                ->where('email', $data['email'])
                ->where('otp', $data['otp'])
                ->where('is_verified', 'N')
                ->where('createdon', '>=', View::shared('get_nowtime')->subMinutes(2))  // 10 min expiry
                ->latest('createdon')
                ->first();

            if ($otpRecord) {
                DB::table(self::$otpverify_table)
                    ->where('id', $otpRecord->id)
                    ->update([
                        'is_verified' => 'Y',
                        'verifiedon' => now()
                    ]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            // Optional: log the error
            \Log::error('Failed to save OTP: ' . $e->getMessage());

            return ['status' => 'error', 'message' => 'Failed to verify OTP.', 'error' => $e->getMessage()];
        }
    }

    public function sendauditeerportmail($data, $Lang, $scheduleId, $instid)
    {
        try {
            $to = $data['email'];

            $issue_scheduledet = FormatModel::fetchissuingSchdet($scheduleId);
            //  return  $issue_scheduledet;
            if (empty($issue_scheduledet || $issue_scheduledet == '')) {
                throw new \Exception('Details not found');
            }

            $data['paracount'] = $issue_scheduledet[0]->para_count;
            $data['rcno'] = $issue_scheduledet[0]->rcno;
            $data['yearselected'] = $issue_scheduledet[0]->yearselected;
            // return  $data;

            $instname = $issue_scheduledet[0]->institutionname;
            $yearselected = $issue_scheduledet[0]->yearselected;
            $rcno = $issue_scheduledet[0]->rcno;

            //    return $issue_scheduledet;

            // $subject = 'CAMS - Audit Report Notification';
            $subject = 'Audit Report No: ' . $rcno . ' for the Year' . $yearselected . ' of ' . $instname;
            $date = new DateTime($data['issuedon']);
            $issued_date = $date->format('d-m-Y');
            $data['issuedondate'] = $issued_date;

            $ccdetails = FormatModel::getreportingofcrdetails($instid);
            // //   return  $ccdetails;
            $ccEmails[] = '';
            foreach ($ccdetails as $item) {
                if (!empty($item->email)) {
                    $ccEmails[] = trim($item->email);  // remove extra spaces
                } else {
                    $ccEmails[] = '';
                }
            }
            // //return  $ccdetails;
            // $ccEmails = ['sivaeceerd@gmail.com', 'swathinagarajann99@gmail.com'];
            // //   return $ccEmails;
            // $ccEmails = '';
            $htmlPath = resource_path('views/Email/Report-Downloadmail.html');
            if (!file_exists($htmlPath)) {
                throw new \Exception("Template not found at: $htmlPath");
            }

            $htmlContent = file_get_contents($htmlPath);

            foreach ($data as $key => $value) {
                $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
            }

            $sendmail = $this->mailService->sendEmail($to, $subject, $htmlContent, $ccEmails);
            // $sendmail = $this->mailService->sendEmail($to, $subject, $htmlContent);

            //  $sendmail = '1';
            if ($sendmail) {
                DB::table('audit.inst_auditschedule')
                    ->where('auditscheduleid', $scheduleId)
                    ->update([
                        'issuedon' => View::shared('get_nowtime'),
                        'issuedby' => $data['userid'],
                        'issuedflag' => 'Y',
                        'updatedon' => View::shared('get_nowtime'),
                        'updatedby' => $data['userid'],
                    ]);
                return true;
            } else {
                throw new \Exception('Mail failed to send');
            }
            if (!$sendmail) {
                throw new \Exception('Mail failed to send');
            }
        } catch (\Exception $e) {
            \Log::error('Mail sending failed:', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
            // return response()->json(['error' => 'Failed to sent Mail'], 500);
        }
    }

    public function sendmandaysextention($data)
    {
        try {
            // $to = $data[0]->email;

            $to = $data['email'];
            $schedulename = $data['schedulename'];
            $username = $data['username'];

            if ($data['for'] == 'request') {
                $text = 'This email is to inform you that you have received a manday request for ' . $schedulename;
                $heading = 'Intimation of Mandays Extension';
                $subject = 'Mandays Extension Intimation ';
            } elseif ($data['for'] == 'approval') {
                $text = 'This email is to inform you that your manday extension request for ' . $schedulename . ' has been approved.';
                $heading = 'Approval of Mandays Extension';
                $subject = 'Mandays Extension Approved ';
            }

            $subject = 'Mandays Extension Intimation ';
            $htmlContent = file_get_contents(resource_path('views/Email/MandaysExtention.html'));
            $dynamicData = [
                'tousername' => $username,
                'text' => $text,
                'heading' => $heading
            ];

            // Replace placeholders with dynamic values
            foreach ($dynamicData as $key => $value) {
                // Replace [key] with actual values
                $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
            }

            return $this->mailService->sendEmail($to, $subject, $htmlContent);
        } catch (\Exception $e) {
            // Optional: log the error
            \Log::error('Failed to save OTP: ' . $e->getMessage());

            return ['status' => 'error', 'message' => 'Failed to sent email.', 'error' => $e->getMessage()];
        }
    }

    public function sent_paramail($getforwarddetails, $processcode, $sesroleactioncode, $sessionusertypecode)
    {
        $to = $getforwarddetails->email;
        $name = $getforwarddetails->username;

        $msg = '';
        $subject = '';
        $desig = '';

        if ($sessionusertypecode == 'A') {
            if ($processcode == 'K') {
                $msg = 'The para has been forwarded  for your verification. Kindly review and provide your feedback ';
                $subject = 'Para Forwarded for Verification';
            } else {
                $msg = 'The para has been sent back to the you for clarification. Kindly provide the necessary details or explanation';
                $subject = 'Para Forwarded for Clarification';
            }
        } else {
            $msg = 'The para has been forwarded  for your verification. Kindly review and provide your feedback ';
            $subject = 'Para Forwarded for Verification';
        }

        $data = [
            'msg' => $msg,
            'subject' => $subject,
            'name' => $name
        ];

        $htmlPath = resource_path('views/Email/forwardpara.html');
        if (!file_exists($htmlPath)) {
            throw new \Exception("Template not found at: $htmlPath");
        }

        $htmlContent = file_get_contents($htmlPath);

        foreach ($data as $key => $value) {
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        $sendmail = $this->mailService->sendEmail($to, $subject, $htmlContent);

        if (!$sendmail) {
            throw new \Exception('Mail failed to send');
        } else {
            return true;
        }
    }

    public function sendotp_forQT($data, $username)
    {
        try {
            // Save OTP first
            $saveResult = self::saveOTP($data);

            if ($saveResult['status'] !== 'success') {
                return ['status' => 'error', 'message' => 'OTP save failed: ' . $saveResult['message']];
            }

            // Prepare email content
            $to = $data['email'];
            // print_r($to);
            // exit;
            $subject = 'CAMS - OTP Password';
            $htmlContent = file_get_contents(resource_path('views/Email/QT_otp.html'));

            $dynamicData = [
                'rand_otp' => $data['otp'],
                'username' => $username
            ];

            // Replace placeholders
            foreach ($dynamicData as $key => $value) {
                $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
            }

             return 'Message has been sent';
            // return $this->mailService->sendEmail($to, $subject, $htmlContent);
        } catch (\Exception $e) {
            \Log::error('sendotp_allocateplan error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to send OTP.', 'error' => $e->getMessage()];
        }
    }

    public function sendotp_forDataVerification($data, $username)
    {
        try {
            $saveResult = self::saveOTP($data);

            if ($saveResult['status'] !== 'success') {
                return ['status' => 'error', 'message' => 'OTP save failed: ' . $saveResult['message']];
            }

            $to = $data['email'];
            $subject = 'CAMS - Data Verification OTP';
            $htmlContent = file_get_contents(resource_path('views/Email/DataVerification_otp.html'));

            $dynamicData = [
                'rand_otp' => $data['otp'],
                'username' => $username
            ];

            foreach ($dynamicData as $key => $value) {
                $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
            }

            return 'Message has been sent';
            // return $this->mailService->sendEmail($to, $subject, $htmlContent);
        } catch (\Exception $e) {
            \Log::error('sendotp_forDataVerification error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to send OTP.', 'error' => $e->getMessage()];
        }
    }

    public static function verifyOTP_QT($data)
    {
        //  return true;
        try {
            $otpRecord = DB::table(self::$otpverify_table)
                ->where('email', $data['email'])
                ->where('otp', $data['otp'])
                ->where('is_verified', 'N')
                ->where('createdon', '>=', View::shared('get_nowtime')->subMinutes(2))  // 10 min expiry
                ->latest('createdon')
                ->first();

            if ($otpRecord) {
                DB::table(self::$otpverify_table)
                    ->where('id', $otpRecord->id)
                    ->update([
                        'is_verified' => 'Y',
                        'verifiedon' => now()
                    ]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            // Optional: log the error
            \Log::error('Failed to save OTP: ' . $e->getMessage());

            return ['status' => 'error', 'message' => 'Failed to verify OTP.', 'error' => $e->getMessage()];
        }
    }

    public static function verifyOTP_DataVerification($data)
    {
        try {
            $otpRecord = DB::table(self::$otpverify_table)
                ->where('email', $data['email'])
                ->where('otp', $data['otp'])
                ->where('is_verified', 'N')
                ->where('createdon', '>=', View::shared('get_nowtime')->subMinutes(2))
                ->latest('createdon')
                ->first();

            if ($otpRecord) {
                DB::table(self::$otpverify_table)
                    ->where('id', $otpRecord->id)
                    ->update([
                        'is_verified' => 'Y',
                        'verifiedon' => now()
                    ]);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            \Log::error('Failed to verify data verification OTP: ' . $e->getMessage());

            return ['status' => 'error', 'message' => 'Failed to verify OTP.', 'error' => $e->getMessage()];
        }
    }

    public function sendotp_forspillrevoke($data, $username, $instDetails = [])
    {
        try {
            // Save OTP first
            $saveResult = self::saveOTP($data);

            if ($saveResult['status'] !== 'success') {
                return ['status' => 'error', 'message' => 'OTP save failed: ' . $saveResult['message']];
            }

            // Prepare email content
            $to = $data['email'];
            // print_r($to);
            // exit;
            $subject = 'CAMS - Spillover Revoke';
            $htmlContent = file_get_contents(resource_path('views/Email/SpillOverRevoke.html'));

            $dynamicData = [
                'rand_otp' => $data['otp'],
                'username' => $username,
                'instname' => $instDetails['instname'] ?? '-',
                'teamsize' => $instDetails['teamsize'] ?? 0,
                'working_days' => $instDetails['working_days'] ?? 0,
                'total_working_mandays' => $instDetails['total_working_mandays'] ?? 0,
                'remaining_mandays' => $instDetails['remaining_mandays'] ?? 0,
                'manday_section' => (($instDetails['show_full_details'] ?? 'N') === 'Y')
                    ? 'Team Size: <b>' . ($instDetails['teamsize'] ?? 0) . '</b><br>
                    Working Days: <b>' . ($instDetails['working_days'] ?? 0) . '</b><br>
                    Total Working Mandays: <b>' . ($instDetails['total_working_mandays'] ?? 0) . '</b><br>
                    Remaining Mandays: <b>' . ($instDetails['remaining_mandays'] ?? 0) . '</b>'
                    : ''
            ];

            // Replace placeholders
            foreach ($dynamicData as $key => $value) {
                $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
            }

            //    return 'Message has been sent';
            return $this->mailService->sendEmail($to, $subject, $htmlContent);
        } catch (\Exception $e) {
            \Log::error('sendotp_allocateplan error: ' . $e->getMessage());
            return ['status' => 'error', 'message' => 'Failed to send OTP.', 'error' => $e->getMessage()];
        }
    }

    public static function VerifyOTP_revokespill($data)
    {
        //  return true;
        try {
            $otpRecord = DB::table(self::$otpverify_table)
                ->where('email', $data['email'])
                ->where('otp', $data['otp'])
                ->where('is_verified', 'N')
                ->where('createdon', '>=', View::shared('get_nowtime')->subMinutes(2))  // 10 min expiry
                ->latest('createdon')
                ->first();

            if ($otpRecord) {
                DB::table(self::$otpverify_table)
                    ->where('id', $otpRecord->id)
                    ->update([
                        'is_verified' => 'Y',
                        'verifiedon' => now()
                    ]);
                return true;
            }
            return false;
        } catch (\Exception $e) {
            // Optional: log the error
            \Log::error('Failed to save OTP: ' . $e->getMessage());

            return ['status' => 'error', 'message' => 'Failed to verify OTP.', 'error' => $e->getMessage()];
        }
    }
}
