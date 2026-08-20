<?php

namespace App\Http\Controllers;

use App\Models\AuditeeModel;
use App\Models\AuditeeOfficeUserModel;
use App\Models\AuditManagementModel;
use App\Services\FileUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use DataTables;

class AuditeeController extends Controller
{
    protected $fileUploadService;

    // Inject the FileUploadService
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function audit_scheduledetails(Request $request)
    {
        try {
            $session = $request->session();
            if ($session->has('user')) {
                $user = $session->get('user');
                $userid = $user->userid ?? null;
            } else {
                return 'No user found in session.';
            }
            $userSessionChargeData = session('charge');
            $userdeptcode = $userSessionChargeData->deptcode;
            // return $userid;
            // $userid = '3';
            // $audit_scheduledetail = AuditeeModel::fetch_auditscheduledetails($userid, $userdeptcode);
            // $result = AuditeeModel::fetch_auditscheduledetails($userid, $userdeptcode);

            $auditmode = $request->input('auditmode');

            $result = AuditeeModel::fetch_auditscheduledetails($userid, $userdeptcode, $auditmode);
            $audit_scheduledetail = json_decode($result);
            // print_r( $audit_scheduledetail);

            // Check if audit details are found
            if (!$audit_scheduledetail || count($audit_scheduledetail) == 0) {
                return response()->json(['error' => true, 'message' => 'noauditscheduledetails'], 401);
            }
            foreach ($audit_scheduledetail as $item) {
                $item->encrypted_auditscheduleid = Crypt::encryptString($item->auditscheduleid);
                $item->encrypted_instid = Crypt::encryptString($item->instid);
            }

            $quartercode = $audit_scheduledetail[0]->auditquartercode;

            $AuditPeriod = AuditeeModel::AuditPeriod();

            if (!$AuditPeriod) {
                return response()->json(['error' => true, 'message' => 'noauditperioddetails'], 401);
            }
            // return  $quartercode;

            $auditperiod['from'] = $AuditPeriod->fromyear;
            $auditperiod['to'] = $AuditPeriod->toyear;

            $fetchcurrquarter = AuditManagementModel::getCurrentQuarter($userdeptcode, $quartercode);
            $str_Quarter = $fetchcurrquarter->quarterfrom;
            $str_Quarter = date('Y-m-01', strtotime($str_Quarter));

            $end_Quarter = $fetchcurrquarter->quarterto;
            $end_Quarter = date('Y-m-t', strtotime($end_Quarter));

            $Quarter = ['fromquarter' => $str_Quarter, 'toquarter' => $end_Quarter];

            // $nodaldetails = DB::table('audit.mst_institution')
            //     ->where('', $userdeptcode)
            //     ->get();
            return response()->json(['data' => $audit_scheduledetail, 'auditperiod' => $auditperiod, 'Quarter' => $Quarter]);  // Ensure the data is wrapped under "data"
        } catch (\Exception $e) {
            // Catch any unexpected errors and return a generic error response
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function auditee_partialchange(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->audit_scheduleid);
        $data = $request->all();

        $change_date = Carbon::createFromFormat('d/m/Y', $request->input('change_date'))->format('Y-m-d');
        $entry_date = Carbon::createFromFormat('d/m/Y', $request->input('entry_date'))->format('Y-m-d');

        $request->merge(['change_date' => $change_date]);
        $request->merge(['entry_date' => $change_date]);
        $request->validate([
            'part_remarks' => 'required',  // Ensures only digits, allows leading zeros
            'change_date' => 'required|date|date_format:Y-m-d|',  // Only alphabets (no numbers or symbols)
        ]);

        $data = [
            'auditeeremarks' => $request->input('part_remarks'),
            'auditeeproposeddate' => $request->input('change_date'),
            'entrymeetdate' => $request->input('entry_date'),
            'auditeeresponse' => 'P',
            'updatedon' => now(),
            'auditeeresponsedt' => now()  // Current timestamp for updated_at
        ];

        $audit_schedulepartial = AuditeeModel::update_partialchange($data, $auditscheduleid, 'P');

        if ($audit_schedulepartial) {
            return response()->json(['success' => 'Audit Schedule was partially changed successfully', 'audit_schedule' => $audit_schedulepartial]);
        }
    }

    public function audit_particulars(Request $request)
    {
        $scheduleid = Crypt::decryptString($request->scheduleid);

        $audit_particulars = AuditManagementModel::Selected_CFR($scheduleid);
        // print_r($audit_particulars);exit;

        $account_particulars = AuditeeModel::accountparticulars();
        if ($audit_particulars) {
            return response()->json([
                'data' => $audit_particulars,
                'account_particulars' => $account_particulars
            ]);
        } else {
            return response()->json(['error' => true, 'message' => 'failed'], 400);
        }
    }

    public function filterValuesByKeyword(array $data, string $keyword): array
    {
        $filteredValues = [];

        foreach ($data as $key => $value) {
            if (strpos($key, "-$keyword") !== false) {
                $filteredValues[] = $value;  // Add to filtered values array
            }
        }

        return $filteredValues;
    }

    public function uploadfile($data, $fileDetails)
    {
        $session = session('charge');

        $designationArray = [
            $session->deptcode,
            $session->regioncode,
            $session->distcode,
            $session->instid,
            View::shared('auditeefileuploadpath'),
        ];

        if (isset($fileDetails['tmp_name']) && $fileDetails['error'] === UPLOAD_ERR_OK) {
            $file = new UploadedFile(
                $fileDetails['tmp_name'],  // Path to the temporary file
                $fileDetails['name'],  // Original file name
                $fileDetails['type'],  // MIME type
                $fileDetails['error'],  // Error code
                true  // Test mode (skip file checks for temporary files)
            );
            // print_r($file->getPathname());

            // print_r($file);
            // $destinationPath = 'uploads/auditeeReply';
            $destinationPath = View::shared('auditeefileuploadpath');

            // $uploadResult = $this->fileUploadService->uploadFile($file, $destinationPath, '');

            $uploadResult = $this->fileUploadService->uploadFile($file, $destinationPath, '', $designationArray);

            if (is_array($uploadResult)) {
                return null;  // Return null if upload failed
            } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
                $uploadResultData = $uploadResult->getData(true);
                return $uploadResultData['fileupload_id'] ?? null;
            }

            return null;
        }

        return null;  // Return null if no valid file is provided
    }

    public function auditee_accept(Request $request)
    {
        $data = $request->all();
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);

        $request->validate([
            'nodalname' => [
                'required',
                'string',
            ],  // Ensures only digits, allows leading zeros
            'nodalmobile' => ['required', 'regex:/^[0-9]{10}$/'],  // Only alphabets (no numbers or symbols)
            'nodalemail' => 'required|email|max:50',  // Alphanumeric (letters and numbers)
            'nodaldesignation' => ['required', 'string', 'regex:/^[^:|~`^$]+$/'],  // No special characters "::|~`^$"
            'auditee_remarks' => ['required', 'string', 'regex:/^[^:|~`^$]+$/'],
        ], [
            'nodalmobile.regex' => 'Mobile number must be exactly 10 digits and contain only numbers.',
            'nodaldesignation.regex' => 'Designation should not contain special characters like :: | ~ ` ^ $',
            'auditee_remarks.regex' => 'Remarks should not contain special characters like :: | ~ ` ^ $',
        ]);

        $accountCodes = $this->filterValuesByKeyword($data, 'accountcode');
        $cfrSubcodes = $this->filterValuesByKeyword($data, 'cfrcode');
        $accountValues = $this->filterValuesByKeyword($data, 'accountvalues');
        $cfrValues = $this->filterValuesByKeyword($data, 'cfrvalues');
        $replystatus = $this->filterValuesByKeyword($data, 'cfrradio');
        $accountfilestatus = $this->filterValuesByKeyword($data, 'radio');

        $accountstatus = [];
        $fileDetails = [];
        foreach ($data as $key => $value) {
            if (strpos($key, '-radio') !== false) {
                $accountstatus[] = str_replace('-radio', '', $key);
            }
        }
        foreach ($_FILES as $name => $file) {
            // Check if the field name ends with '-accountfile' and if a file was uploaded
            if (strpos($name, '-accountfile') !== false) {
                $fileDetails[] = $file;  // Add the entire file details to the array
            }
        }

        if (count($accountCodes) === count($accountValues)) {
            foreach ($accountCodes as $index => $accountCode) {
                // Determine if a file matches the account code
                $isfileupload = in_array($accountCode, $accountstatus);

                if ($isfileupload) {
                    if ($accountfilestatus[$index] === 'Y') {
                        $fileuploadid = $this->uploadfile($data, $fileDetails[$index]);

                        // print_r($fileuploadid );
                    } else {
                        $fileuploadid = '0';
                    }
                } else {
                    $fileuploadid = '0';
                }

                $existingRecord = AuditeeModel::GetAccountParticulars($auditscheduleid, $accountCode);

                // Create a record for each account code with the corresponding file upload ID
                if (!$existingRecord) {
                    $Data = array();
                    $Data['auditscheduleid'] = $auditscheduleid;
                    $Data['accountCode'] = $accountCode;
                    $Data['remarks'] = $accountValues[$index];
                    $Data['fileuploadid'] = $fileuploadid;
                    $createdata = AuditeeModel::StoreAccountParticulars($Data);

                    if (!$createdata) {
                        return response()->json(['error' => true, 'message' => 'accountparticulars_notinsert'], 400);
                    }
                }
            }
        } else {
            return response()->json(['error' => true, 'message' => 'failed'], 400);
        }
        if (count($cfrSubcodes) === count($cfrValues)) {
            foreach ($cfrSubcodes as $key => $cfrSubcode) {
                // Check if the record already exists
                $existingRecord = AuditeeModel::GetCallforRecords($auditscheduleid, $cfrSubcode);

                if (!$existingRecord) {
                    $Data = array();
                    $Data['auditscheduleid'] = $auditscheduleid;
                    $Data['subtypecode'] = $cfrSubcode;
                    $Data['remarks'] = $cfrValues[$key];
                    $Data['replystatus'] = $replystatus[$key];
                    $createdata = AuditeeModel::StoreCallforRecords($Data);

                    if (!$createdata) {
                        return response()->json(['error' => true, 'message' => 'callforrecords_notinsert'], 400);
                    }
                }
            }
        } else {
            return response()->json(['error' => true, 'message' => 'failed'], 400);
        }

        $store_auditeeofficeusers = AuditeeModel::fetch_auditeeofficeusers($auditscheduleid);
        $content = json_decode($store_auditeeofficeusers->getContent(), true);
        $exists = $content['exists'];

        if ($exists == '1') {
            $auditeeres = 'A';
            $response = 'datasubmitsuccess';
        } else {
            $auditeeres = 'R';
            $response = 'movetonexttab';
        }

        $acceptdata = [
            'auditeeresponsedt' => now(),
            'auditeeresponse' => $auditeeres,
            'entrymeetdate' => now(),
            'auditeeremarks' => $request->auditee_remarks,
            'updatedon' => now(),
            'auditeeremarks' => $request->auditee_remarks,
            'nodalname' => $request->nodalname,
            'nodalmobile' => $request->nodalmobile,
            'nodalemail' => $request->nodalemail,
            'nodaldesignation' => $request->nodaldesignation,
        ];
        $audit_schedulepartial = AuditeeModel::update_partialchange($acceptdata, $auditscheduleid, $auditeeres);

        if ($audit_schedulepartial) {
            return response()->json(['success' => true, 'message' => $response]);
        } else {
            return response()->json(['error' => true, 'message' => 'failedtoupdate_as'], 400);
        }
    }

    public function auditee_acceptdetails(Request $request)
    {
        $auditscheduleid = $request->auditscheduleid;

        $account_particularsaccept = AuditeeModel::fetch_Accountaccepteddetails($auditscheduleid);
        $cfr_saccept = AuditeeModel::fetch_cfraccepteddetails($auditscheduleid);
        return response()->json([
            'data' => $account_particularsaccept,
            'cfr' => $cfr_saccept
        ]);
        // return response()->json(['data' => $account_particularsaccept]); // Ensure the data is wrapped under "data"
    }

    public function store_auditeeofficeusers(Request $request)
    {
        // Decrypt the auditscheduleid from the request
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);

        // Get the 'from' and 'to' dates from the request
        $from_dates = $request->input('officeuserfromdate');  // Array of 'from' dates
        $to_dates = $request->input('officeusertodate');  // Array of 'to' dates

        // Convert the dates in the arrays to 'Y-m-d' format
        $formatted_from_dates = array_map(function ($date) {
            return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
        }, $from_dates);

        $formatted_to_dates = [];

        foreach ($to_dates as $key => $date) {
            if (!empty($date)) {
                // If the 'to' date is not empty, convert it to the desired format
                $formatted_to_dates[$key] = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            } else {
                // If the 'to' date is empty, store null (or you can store an empty string, depending on your requirement)
                $formatted_to_dates[$key] = null;
            }
        }

        // Prepare the data to be inserted into the database
        $data = [
            'ofc_userid' => $request->input('officeuserid'),
            'ofc_user' => $request->input('officeusername'),
            'ofc_designation' => $request->input('officeuserdesignation'),
            'ofc_fromdate' => $formatted_from_dates,  // Store the array as a comma-separated string
            'ofc_enddate' => $formatted_to_dates,  // Store the array as a comma-separated string
            'updatedon' => now(),
            'auditscheduleid' => $auditscheduleid
        ];

        // Insert into the database (assuming you have a model or DB insert operation here)

        try {
            $store_auditeeofficeusers = AuditeeModel::store_auditeeofficeusers($data);

            // Check if insert was successful (assuming store_auditeeofficeusers returns a boolean or the inserted model)
            if (!$store_auditeeofficeusers) {
                // If insertion failed, throw an exception
                return response()->json(['error' => true, 'message' => 'auditeeusernotinserted'], 400);
            }

            // Query to check the audit response
            $table = 'audit.inst_auditschedule';
            $selectquery = DB::table($table)
                ->select('auditeeresponse')
                ->where('auditscheduleid', '=', $auditscheduleid)
                ->first();

            // Check the value of auditee response
            if ($selectquery->auditeeresponse == 'Y') {
                // If auditeeresponse is 'Y', update the audit status
                $acceptauditres = AuditeeModel::update_auditstatus($auditscheduleid);

                return response()->json(['success' => true, 'message' => 'datasubmitsuccess']);
            } else {
                return response()->json(['success' => true, 'message' => 'auditeeusersuccess']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'error: ' . $e->getMessage()]);
        }
    }

    public function fetch_auditeeofficeusers(Request $request)
    {
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        $store_auditeeofficeusers = AuditeeModel::fetch_auditeeofficeusers($auditscheduleid);
        $dfasd = $store_auditeeofficeusers->original;
        $exists = $dfasd['exists'];

        // Initialize arrays to hold all the formatted from and to dates
        $from_dates_array = [];
        $to_dates_array = [];

        foreach ($dfasd['fetch_auditeeofficeusers'] as $item) {
            // Convert the service_fromdate and service_todate to Y-m-d format
            $item->converted_service_fromdate = Carbon::createFromFormat('Y-m-d', $item->service_fromdate)->format('d/m/Y');

            if ($item->service_todate) {
                $item->converted_service_todate = Carbon::createFromFormat('Y-m-d', $item->service_todate)->format('d/m/Y');
            } else {
                $item->converted_service_todate = null;
            }
        }

        $return = ['exists' => $exists, 'fetch_auditeeofficeusers' => $dfasd['fetch_auditeeofficeusers']];

        return $return;
    }

    public function getfinancialyear()
    {
        $sessiondelcharge = session('charge');
        $sessioninstid = $sessiondelcharge->instid;

        $financialyear = AuditeeModel::getfinancialyear();
        $cpsform = AuditeeModel::checkCpsAllowed($sessioninstid);

        // dd($cpsform);

        return view('audit.auditee', compact('financialyear', 'cpsform'));
    }

    public function prfaudit_accept(Request $request)
    {
        $data = $request->all();
        $auditscheduleid = Crypt::decryptString($request->auditscheduleid);

        $accept_audit = AuditeeModel::prfaudit_accept($auditscheduleid);

        if ($accept_audit) {
            return response()->json(['success' => true, 'message' => 'The schedule has been accepted']);
        } else {
            return response()->json(['error' => true, 'message' => 'failedtoupdate_as'], 400);
        }
    }
}
