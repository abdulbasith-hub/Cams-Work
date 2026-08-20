<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;

use Illuminate\Http\Request;
use DataTables;

use App\Models\WorkAllocationModel;
use App\Models\MinorTypeModel;
use App\Models\DeptModel;
use App\Models\MajorWorkAllocationtypeModel;
use App\Models\InstituteCategoryModel;


class WorkAllocationManagementController extends Controller
{

    public function Show_Workallocation(Request $request)
    {

        $dept = DeptModel::where('statusflag', '=', 'Y')
                        ->orderBy('orderid', 'asc')
                        ->get();

        $WorkAllocation = WorkAllocationModel::Majorworktypes();
        return view('audit/workallocation', compact('WorkAllocation','dept'));
    }

    public function Get_MajorWrkAllocation(Request $request)
    {
       //$GetWorkData = WorkAllocationModel::GetDeptCode($request->majortype); // Adjust query as needed

       $InstCategory = InstituteCategoryModel::where('statusflag', '=', 'Y')
                                            ->where('deptcode', $request->deptcode)
                                            ->get();

        $MajorrWorkAllocation = MajorWorkAllocationtypeModel::where('statusflag', 'Y')
                                                            ->where('deptcode', $request->deptcode)
                                                            ->get();

        return response()->json(['InstCategory' => $InstCategory,
                                 'MajorWorkAllocation' => $MajorrWorkAllocation
                                ]);

    }


    public function Get_MinorWrkAllocation(Request $request)
    {
        //$GetWorkData = WorkAllocationModel::GetDeptCode($request->majortype); // Adjust query as needed
        $MinorWorkAllocation = MinorTypeModel::where('statusflag', 'Y')
                                             ->where('deptcode', $request->deptcode)
                                             ->get();
        return $MinorWorkAllocation;

    }

    public function CreateWorkAllocation(Request $request)
    {
        // Define validation rules
        $rules = [
            'majortype' => 'required|string|max:4',
            'allocatedtowhom' => 'required|string|in:Y,N',
        ];

        // Conditionally add the `minortype` rule
        if ($request->input('minortype_available') == 1) {
            $rules['minortype'] = 'required|string|max:4';
        }

        $validatedData = $request->validate($rules);


        if((isset($_POST['workallocid']) && $_POST['workallocid'] != ''))
            $workallocid =   $request->input('workallocid');
        else
            $workallocid =   null;

        // Validate the request
        try {
                $InsertData =    ['majorworkallocationtypeid' => $request->majortype,
                                'minorworkallocationtypeid' => $request->minortype,
                                'teamhead'             => $request->allocatedtowhom,
                                'catcode'   =>$request->catcode,
                                'statusflag'                => 'Y'];

                $Insert = WorkAllocationModel::createIfNotExistsOrUpdate($InsertData,$workallocid);

                if (!$Insert) {
                    // If user already exists (based on conditions), return an error
                    return response()->json(['error' => 'Details already exists'], 400);
                }else
                {
                    return response()->json(['success' => 'Work allocation Created/Updated successfully.']);
                }


        } catch (QueryException $e) {
            // Handle any database-specific exceptions (e.g., duplicate entry)
            Log::error("Database error: " . $e->getMessage());
            throw new Exception("Database error occurred. Please try again later.");
        } catch (Exception $e) {
            // Handle any other general exceptions
            Log::error("General error: " . $e->getMessage());
            throw new Exception("Something went wrong: " . $e->getMessage());
        }





    }

    public function fetchAllData()
    {
        // Fetch all users
        $workallocation = WorkAllocationModel::fetchAllusers();

        $workallocation = $workallocation->map(function ($item, $key) {
            $item->allocatedwhom_label = $item->teamhead === 'N' ? 'Team Member' : ($item->teamhead === 'Y' ? 'Team Head' : 'Unknown');
            $item->Slno = $key + 1; // Add serial number starting from 1
            $item->encrypted_workallocationtypeid = Crypt::encryptString($item->workallocationtypeid);
            return $item;

        })->values(); // Reset keys to ensure sequential indexing

        return response()->json(['data' => $workallocation]); // Ensure the data is wrapped under "data"
    }


    public function fetchWorkData(Request $request)
    {

        // Retrieve deptuserid from the request
        $dynid = Crypt::decryptString($request->dynid);
        $request->merge(['dynid' => $dynid]);

        $request->validate(['dynid'  =>  'required|integer'
                            ], [
                                'required' => 'The :attribute field is required.',
                                'integer' => 'The :attribute field must be a valid number.'
                            ]);

         // Ensure deptuserid is provided
        if (!$dynid) {
            return response()->json(['success' => false, 'message' => 'Work ID not provided'], 400);
        }


        $WorkData = WorkAllocationModel::fetchCurrentUser($dynid); // Adjust query as needed

        if ($WorkData) {
            return response()->json(['success' => true, 'data' => $WorkData]);
        } else {
            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        }


    }

}


?>
