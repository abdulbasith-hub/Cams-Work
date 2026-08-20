<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use App\Models\AuditDiaryModel;
use App\Exports\AuditorDiaryExport;
use App\Models\InstAuditscheduleModel;
use Maatwebsite\Excel\Facades\Excel; // Correct import for the Excel facade
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use DataTables;

class AuditDiaryController extends Controller
{
    public function storeOrUpdateAuditDiary(Request $request)
    {

        $member = $request->input('member'); 

//echo $member;


        try {
            $memberid = Crypt::decryptString($member);
        } catch (\Exception $e) {
            abort(403, 'Invalid or tampered diary ID.');
        }


        $request->validate([
            'workallocationid' => 'required|array',
            'fromdate' => 'required|array',
            //'todate' => 'required|array',
            'remarks' => 'required|array',
            'percentage' =>'required|array',
            //'noofdays' =>'required|array',
        ]);

        $auditdiary = session('user');
        $userid = $auditdiary->userid;


//print_r( $request->workallocationid);


//print_r( $request->remarks);
//exit;




    foreach($request->workallocationid as $key => $val)
        {
            if (
                ($request->percentage[$key] != 100) || 
                (isset($request->fromdate[$key]) && $request->percentage[$key] == 100)
            )
            {
                 $auditDiaryData = ['workallocationid' => $request->workallocationid[$key],
                               'percentofcompletion' => $request->percentage[$key],
                               'fromdate' => $request->fromdate[$key],
                               'remarks' => $request->remarks[$key],
                               'createdon' => View::shared('get_nowtime'),
                               'updatedon' => View::shared('get_nowtime'),
                               'updatedby' => $userid,
                                'createdby' => $userid,
                               'schteammemberid' => $memberid,
                               'statusflag' => 'Y',
                              ];

     
            if($_POST['actiontype'] =='Update')
            {
                $auditDiaryData['updatedon'] = View::shared('get_nowtime');



                $auditdiaryid =   $request->auditdiaryid[$key];
            }else
            {
                 $auditdiaryid=null;
            }
            try
            {
                $AuditDiaryModel = AuditDiaryModel::createIfNotExistsOrUpdate($auditDiaryData,$auditdiaryid,$_POST['actiontype'],$userid);
              //  print_r($AuditDiaryModel);
                $successResults[] = $AuditDiaryModel; 
            } catch (QueryException $e)
            {
                $errorResults[] = 'Database error: ' . $e->getMessage();  // Collecting errors (optional)
            } catch (Exception $e)
            {
                // Handle other exceptions
                $errorResults[] = 'Error: ' . $e->getMessage();  // Collecting errors (optional)
            }
            }
           
        }

        // After the loop completes, return the final response
        if (isset($errorResults) && !empty($errorResults)) {
            // If there are any errors, return them
            return response()->json(['error' => $errorResults], 500);
        } else {
            // If everything is successful, return success
            return response()->json(['success' => 'Details created/updated successfully', 'data' => $successResults ?? []]);
        }

    }


    
    public function auditdiaryfinalize(Request $request){

        $schedule = $request->input('scheduleid'); 
        $member = $request->input('memberid'); 


        try {
            $scheduleid = Crypt::decryptString($schedule);
            $memberid = Crypt::decryptString($member);

        } catch (\Exception $e) {
            abort(403, 'Invalid or tampered diary ID.');
        }

        $memberCount = DB::table('audit.auditdiary')
        ->where('schteammemberid', $memberid)
        ->count();


        if ($memberCount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please submit the audit diary first before attempting to finalize!'
            ]);
        }


        $schedule = AuditDiaryModel::auditdiary_finalize($scheduleid,$memberid);

        return response()->json([
            'success' => true,
            'message' => 'Audit Diary has been finalized successfully.',
            ]);



    }


    public function showDiaryhistory(Request $request)
    {
        $diaryid = $request->input('id');

        
        $history = AuditDiaryModel::ShowHistory($diaryid);

        $workandgroup = AuditDiaryModel::Showworkandgroup($diaryid);

       // dd($history);
    
        return response()->json([
            'data' => $history,
        'workallocationename' => $workandgroup->majorworkallocationtypeename ?? 'N/A',
        'grouping' => $workandgroup->groupename ?? 'N/A'
        ]);

    }
    



    public function FetchworkallocationDetailsdropdown(Request $request)
    {

       $schedule = $request->query('schedule');
       $member = $request->query('member');

    try {
        $auditscheduleid = Crypt::decryptString($schedule);
        $schteammemberid = Crypt::decryptString($member);
    } catch (\Exception $e) {
        abort(403, "Invalid or tampered link");
    }

        //dd($schedule);
        $sessiondet = session('user');
        $sessionuserid = $sessiondet->userid;

       
        $workAllocation = AuditDiaryModel::Fetch_Cat_subCat($auditscheduleid, $schteammemberid);
       //print_r($workAllocation);
        $Workallocated_Category=[];
        $Workallocated_SubCategory=[];
        $WorkAllocationId=[];

        $HomeAuditDiaryTableData = AuditDiaryModel::auditdiarytablehomeforfetch1($sessionuserid, $auditscheduleid, $schteammemberid);

        $encryptedScheduleId = Crypt::encryptString($auditscheduleid);
        $encryptedTeamMemberId = Crypt::encryptString($schteammemberid);

        $DiaryData='nodata';
        if($workAllocation)
        {
            $DiaryData = AuditDiaryModel::DiaryFetchData($auditscheduleid, $schteammemberid);

            foreach($workAllocation as $workkey => $workval)
            {
                $Workallocated_Category[$workval->groupid]=$workval->groupename;
                $Workallocated_SubCategory[$workval->groupid][$workval->majorworkallocationtypeid]=$workval->majorworkallocationtypeename;
                $WorkAllocationId[$workval->groupid][$workval->majorworkallocationtypeid]=$workval->workallocationid;
            }
        }

            if($DiaryData!=='nodata')
            {
                if(sizeof($DiaryData) >0)
                {
                    foreach($DiaryData as $KeyDiary => $ValDiary)
                {
                    if($ValDiary->fromdate)
                    {
                        $FromDate[$ValDiary->workallocationid]=date('d-m-Y',strtotime($ValDiary->fromdate));
                    }else
                    {
                        $FromDate[$ValDiary->workallocationid]='';

                    }



                    $Remarks[$ValDiary->workallocationid]=$ValDiary->remarks;
                    $Percent[$ValDiary->workallocationid]=$ValDiary->percentofcompletion;
                    //$NoofDays[$ValDiary->workallocationid]=$ValDiary->noofdays;
                    $AuditDiaryId[$ValDiary->workallocationid]=$ValDiary->diaryid;
                    $actiontype='Update';
                }

                }



                return view('audit.auditdiary', compact('Workallocated_Category','Workallocated_SubCategory','WorkAllocationId','FromDate','Remarks','Percent','AuditDiaryId','actiontype','auditscheduleid','schteammemberid','HomeAuditDiaryTableData', 'encryptedScheduleId','encryptedTeamMemberId'));

            }else
            {
                $actiontype='Insert';
                return view('audit.auditdiary', compact('Workallocated_Category','Workallocated_SubCategory','WorkAllocationId','actiontype','auditscheduleid','schteammemberid','HomeAuditDiaryTableData','encryptedScheduleId','encryptedTeamMemberId'));
            }



    }

    public static function FetchDiarydetails(Request $request)
    {

        try {
            $auditscheduleid = Crypt::decryptString($request->input('schedule'));
            $schteammemberid = Crypt::decryptString($request->input('member'));
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'Invalid or tampered ID values.'
            ], 403);
        }

        $audits = AuditDiaryModel::fetchAllusers($auditscheduleid,$schteammemberid);
        return response()->json(['data' => $audits]); 

    }
   public static function FetchDiaryAlluserdetails(Request $request)
    {

        try {
            $auditscheduleid = Crypt::decryptString($request->input('schedule'));

        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'success' => false,
                'message' => 'Invalid or tampered ID values.'
            ], 403);
        }

        $audits = AuditDiaryModel::fetchAllusersdiary($auditscheduleid);
        return response()->json(['data' => $audits]);

    }
    public function downloadDiary()
    {
        $chargeData = session('charge');
        $userData = session('user');
        $session_userid = $userData->userid;
        //$workAllocation = AuditDiaryModel::Fetch_Cat_subCat();
        $workAllocation = AuditDiaryModel::DiaryFetchData();
        $fromdates = $workAllocation->pluck('fromdate');
        $subworkname=$workAllocation->pluck('subworkallocationtypeename');
        $combined = $fromdates->combine($subworkname);

        $lowestDate = $fromdates->min();
        $highestDate = $fromdates->max();

        $auditscheduleid = $workAllocation->first()->auditscheduleid;

        $WorkingOfficeGet=AuditDiaryModel::GetInstituteDetails($session_userid,$auditscheduleid);
        // Fetch metadata
        $metaData = [
            'name' => $userData->username,  // Example dynamic name
            'designation' =>$chargeData->desigelname,  // Example dynamic designation
            'working_office' =>$WorkingOfficeGet->instename, 
        ];

        // Generate calendar data for December 2024
        $calendarData = [];
        $start_date = new \DateTime($lowestDate);
        $end_date = new \DateTime($highestDate);

        while ($start_date <= $end_date) {
            $current_date = $start_date->format('Y-m-d');
            $day = $start_date->format('l');
            $details = '';
        
            // Check if the current date exists in the $combined array
            if (array_key_exists($current_date, $combined->toArray())) {
                $details = $combined[$current_date]; // Get subwork name for the date
            } elseif (in_array($day, ['Sunday', 'Saturday'])) {
                $details = 'Government Holiday';
            }
        
            // Add to calendar data
            $calendarData[] = [
                'date' => $start_date->format('d-m-Y'),
                'day' => $day,
                'details' => $details,
            ];
        
            // Increment the date
            $start_date->modify('+1 day');
        }

        // Get unique catworkname values
        $uniqueCatworkname = $workAllocation->pluck('majorworkallocationtypeename')->unique()->values()->all();

        // Initialize summaryData array
        $summaryData = [];

        // Loop through unique catworkname values to create 'Category Audit Duty' rows
        foreach ($uniqueCatworkname as $index => $name) {
            $summaryData[] = [
                'gist' => 'Category' . ($index + 1) . ' (' . $name . ') *',
                'total_days' => $fromdates->count(),
            ];
        }

        // Add fixed rows to the summaryData
        $fixedRows = [
            ['gist' => 'Staff Meeting', 'total_days' => ''],
            ['gist' => 'Casual Leave', 'total_days' => ''],
            ['gist' => 'Government Holidays', 'total_days' => ''],
            ['gist' => 'Loss of Pay', 'total_days' => ''],
            ['gist' => 'Total', 'total_days' => ''],
        ];

        $summaryData = array_merge($summaryData, $fixedRows);

        // Pass data to the export class and download
        return Excel::download(
            new AuditorDiaryExport($metaData, $calendarData, $summaryData),
            'Auditor_Diary_December_2024.xlsx'
        );
    }




    public function auditdiarytablehomefetch(Request $request)
{
    $sessiondet = session('user');
    $sessionuserid = $sessiondet->userid;

    $auditdiarytable = AuditDiaryModel::auditdiarytablehomeforfetch($sessionuserid);

    foreach ($auditdiarytable as $item) {
        if (isset($item->auditscheduleid)) {
            $item->encrypted_auditscheduleid = urlencode(Crypt::encryptString($item->auditscheduleid));
            $item->raw_auditscheduleid = $item->auditscheduleid; // 👈 for debugging

        }

        if (isset($item->schteammemberid)) {
            $item->encrypted_schteammemberid = urlencode(Crypt::encryptString($item->schteammemberid));
            $item->raw_schteammemberid = $item->schteammemberid; // 👈 for debugging

        }
    }



    return response()->json([
        'success' => !$auditdiarytable->isEmpty(),
        'message' => $auditdiarytable->isEmpty() ? 'User not found' : '',
        'data' => $auditdiarytable->isEmpty() ? null : $auditdiarytable
    ], $auditdiarytable->isEmpty() ? 404 : 200);
}


    public static function auditdiarydeptfetch()
{
    $dept = AuditDiaryModel::commondeptfetch(); 

    return view('audit.auditdiaryrevoke', compact('dept'));
}



public function getregionbasedondeptuditdiary(Request $request)
{
    $request->validate([
        'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'required' => 'The :attribute field is required.',
        'regex'    => 'The :attribute field must be a valid number.',
    ]);

    $deptcode = $request->input('deptcode');


    $regions = AuditDiaryModel::getRegionsByDept($deptcode);


    return response()->json([
        'success' => true,
        'data' => $regions
    ]);
}




public function getdistrictbasedonregionuditdiary(Request $request)
{
    $request->validate(
        [
            'region'   => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ],
        [
            'region.required'   => 'The region field is required.',
            'region.regex'      => 'The region field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex'    => 'The deptcode field must be a valid number.',
        ]
    );

    $regioncode = $request->input('region');
    $deptcode = $request->input('deptcode');


    $district = AuditDiaryModel::getdistrictByregion($regioncode, $deptcode);

    if ($district->isNotEmpty()) {
        return response()->json(['success' => true, 'data' => $district]);
    } else {
        return response()->json(['success' => false, 'message' => 'No regions found'], 404);
    }
}


public function getinstitutionbasedondistuditdiary(Request $request)
{
    // Validate the input
    $request->validate([
        'region'   => ['required', 'string', 'regex:/^\d+$/'],
        'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        'district' => ['required', 'string', 'regex:/^\d+$/'],
    ], [
        'region.required' => 'The :attribute field is required.',
        'region.regex'    => 'The :attribute field must be a valid number.',
        'deptcode.required' => 'The deptcode field is required.',
        'deptcode.regex'    => 'The deptcode field must be a valid number.',
        'district.required' => 'The district field is required.',
        'district.regex'    => 'The district field must be a valid number.',
    ]);

    $regioncode = $request->input('region');
    $deptcode = $request->input('deptcode');
    $district = $request->input('district');

	$institution = AuditDiaryModel::getinstitutionBydistrictchange($district, $regioncode, $deptcode);

    if ($institution->isNotEmpty()) {
        return response()->json(['success' => true, 'data' => $institution]);
    } else {
        return response()->json(['success' => false, 'message' => 'No Institutions found'], 200);
    }
}




public function getusernameBasedOninstitution(Request $request)
{
    // Validate the input
    $request->validate([
        
        'instmappingcode'   => ['required', 'string', 'regex:/^\d+$/']
    ], [
       
        'instmappingcode.required' => 'The :attribute field is required.',
        'instmappingcode.regex'    => 'The :attribute field must be a valid number.'
    ]);

    $instmappingcode = $request->input('instmappingcode');
    $regioncode = $request->input('region');
    $deptcode = $request->input('deptcode');
    $district = $request->input('district');
    
    
	$username = AuditDiaryModel::getusernameBasedOninstitution($instmappingcode);

    if ($username->isNotEmpty()) {
        return response()->json(['success' => true, 'data' => $username]);
    } else {
        return response()->json(['success' => false, 'message' => 'No Institutions found'], 200);
    }
}




public function auditdiary_fetchData(Request $request)
{

    $schteammemberid = $request->has('schteammemberid') ? Crypt::decryptString($request->schteammemberid) : null;
    $schteammember = AuditDiaryModel::auditdiaryfetchData('audit.inst_schteammember');


    if (is_iterable($schteammember)) {
        foreach ($schteammember as $all) {
            $all->encrypted_schteammemberid = Crypt::encryptString($all->schteammemberid);
            unset($all->schteammemberid);
        }
    }

    return response()->json([
        'success' => true,
        'message' => empty($schteammember) ? 'No Details found' : '',
        'data' => $schteammember ?? []
    ], 200);
}



public function auditdiary_insertupdate(Request  $request)
{
    // print_r($_REQUEST);

   try {


    $rules = [
        'deptcode' => 'required|string|regex:/^\d+$/',
        "regioncode" => 'required|string|regex:/^\d+$/',
        'distcode' => 'required|string|regex:/^\d+$/',
        'instmappingcode' => 'required|string|regex:/^\d+$/',
        'usernamefield' => 'integer',
        'revoke' => 'integer'

    ];



    $auditplan = session('user');
    if (!$auditplan || !isset($auditplan->userid)) {
        return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
    }
    $userchargeid = $auditplan->userid;


   $data = [
        'schteammemberid' => $request->schteammemberid ?? null,
        'usernamefield' => $request->usernamefield ?? null,
        'revoke' =>  $request->revoke ?? null,

    ];

   
    $result = AuditDiaryModel::auditdiary_insertupdate($data, 'audit.inst_schteammember',$userchargeid);
      return response()->json(['success' => true, 'message' => 'auditdiaryupdated']);

   } 
   catch (ValidationException $e) {
     return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
   }
    catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    }
}



}
