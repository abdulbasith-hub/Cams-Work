<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;

use Carbon\Carbon;

use App\Models\UserManagementModel;
use Illuminate\Support\Facades\DB;


use App\Models\Charge;
use App\Models\AssignCharge;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{


    public function changeCharge(Request $request)
    {
        $selectedCharge = $request->input('change_charge');

        $userdel    =   session('user');
        $sessionuserid  =   $userdel->userid;

	$charge    = session('charge');
        $usertypecode = $charge->usertypecode;


    if ($usertypecode == 'A') {
        $charge = DB::table('audit.userchargedetails as uc')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid') // Adjust the columns as needed
            ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->leftJoin('audit.mst_auditeeins_category as cat', 'cat.catcode', '=', 'c.catcode')
            ->leftJoin('audit.mst_auditeeins_subcategory as sub', 'c.subcatid', '=', 'sub.auditeeins_subcategoryid')
            ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
            ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'c.deptcode')
            ->leftJoin('audit.mst_district as di', 'di.distcode', '=', 'c.distcode')
            ->leftJoin('audit.mst_region as r', 'r.regioncode', '=', 'c.regioncode')
            ->leftJoin('audit.auditor_instmapping as i', 'i.instmappingcode', '=', 'c.instmappingcode')
            ->leftJoin('audit.auditplanteammember as at', 'at.userid', '=', 'du.deptuserid')
            ->join('audit.mst_designation as d', 'd.desigcode', '=', 'du.desigcode')
            ->where('du.deptuserid', $sessionuserid)
            ->where('c.chargeid', $selectedCharge)
            ->where('uc.statusflag', '=', 'Y')
            ->select(
                'uc.chargeid',
                'uc.userid',
                'c.catcode',
                'c.subcatid',
                'c.chargedescription',
                'c.deptcode',
                'c.regioncode',
                'rtm.usertypecode',
                'c.distcode',
                'c.desigcode',
                'd.desigelname',
                'de.deptesname',
                'de.depttsname',
                'd.desigesname',
                'di.distsname',
                'r.regionename',
                'r.regiontname',
                'di.distename',
                'di.disttname',
                'd.desigelname',
                'du.username',
                'du.email',
                'rtm.roletypecode',
		'rm.roleactioncode',
                'du.lastlogin',
                'rm.rolemappingid',
                'uc.userchargeid',
                'at.teamhead as auditteamhead'
            ) // Select all columns from both tables
            ->first();
	}else if ($usertypecode == 'H') {
            $charge = DB::table('audit.hoduserchargedetails as uc')
                ->join('audit.auditee_dept as du', 'uc.userid', '=', 'du.auditeedeptid') // Adjust the columns as needed
                ->join('audit.hodchargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
                ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
                ->leftJoin('audit.roletypemapping as rtm', 'rtm.roletypemappingcode', '=', 'rm.roletypemappingcode')
                ->leftJoin('audit.mst_dept as de', 'de.deptcode', '=', 'c.deptcode')
                ->leftJoin('audit.mst_auditeedept as ade', 'ade.auditeedeptcode', '=', 'c.auditeedeptcode')

                ->join('audit.mst_designation as d', 'd.desigcode', '=', 'du.designation')
                ->where('du.auditeedeptid', $sessionuserid)
                ->where('c.chargeid', $selectedCharge)
                ->where('uc.chargeflag', 'P')
                ->where('uc.statusflag', '=', 'Y')
                ->where('du.statusflag', '=', 'Y')
                // ->where('uc.statusflag','=','Y' )
                ->select(
                    'ade.auditeedeptename',
                    'ade.auditeedepttname',
                    'c.auditeedeptcode',
                    'uc.chargeid',
                    'uc.userid',
                    'c.chargedescription',
                    'c.deptcode',
                    // 'c.regioncode',
                    'rtm.usertypecode',
                    // 'c.distcode',
                    'c.desigcode',
                    'd.desigelname',
                    'd.desigtlname',
                    'de.deptesname',
                    'de.depttlname',
                    'de.deptelname',
                    'de.deptesname',
                    'd.desigesname',
                    // 'di.distsname',
                    // 'r.regionename',
                    // 'r.regiontname',
                    // 'di.distename',
                    // 'di.disttname',
                    'd.desigelname',
                    // 'du.username',
                    // 'du.usertamilname',
                    'du.email',
                    'du.auditeedeptid',
                    'du.mobilenumber',
                    'rtm.roletypecode',
                    'rm.roleactioncode',
                    'du.lastlogin',
                    'rm.rolemappingid',
                    'uc.userchargeid',
                    //  'at.teamhead as auditteamhead',

                ) // Select all columns from both tables
                ->first();
        }

        unset($charge->userid);
        unset($charge->username);
        unset($charge->lastlogin);
        unset($charge->email);


         $menuQuery = DB::table('audit.rolemapping')
		->crossJoin(DB::raw("LATERAL jsonb_array_elements_text(menuid->'1') AS menu_id"))
		->join('audit.mst_menu', DB::raw('CAST(menu_id AS INTEGER)'), '=', 'audit.mst_menu.menuid')
		->select(
			DB::raw('CAST(menu_id AS INTEGER) as value'),
			'audit.mst_menu.menuurl as menuname'
		)
		->where('audit.rolemapping.rolemappingid', $charge->rolemappingid);

	$baseResults = $menuQuery->get();

	if ($charge->roleactioncode == view::shared('AuditorRoleactioncode')) {
		$selecttable = DB::table('audit.auditplanteammember')
			->where('teamhead', 'Y')
			->where('userid', $sessionuserid  )
			->get();

		if ($selecttable->isNotEmpty()) {

			$teamHeadMenus = DB::table('audit.mst_menu')
				->where('menuforteamhead', 'Y')
				->select(DB::raw('menuid as value'), 'menuurl as menuname')
				->get();

			$baseResults = $baseResults->merge($teamHeadMenus);
		}
	}

	$control_menu = $baseResults->pluck('value')->unique()->toArray();
	$menuurl = $baseResults->pluck('menuname')->unique()->filter()->values()->toArray();

	$charge->menu = $control_menu;
	$charge->menuname = $menuurl;

	session(['user' => $userdel  ]);


        session(['charge' => $charge]);

        return response()->json([
            'success' => true,
            'redirect_url' => $usertypecode == 'A' ? url('/home') : url('/home'),  // Redirect to dashboard after successful login
        ]);
    }


    public function creatuser_dropdownvalues($viewname)
    {
        // Since the session check has already been done via middleware, proceed with the logic

        $userdel    =   session('user');
        $sessionuserid  =   $userdel->userid;

        // Extract session data
        $chargeData = session('charge');
        $session_deptcode = $chargeData->deptcode ?? null;
        $session_roletypecode = $chargeData->roletypecode ?? null;
        $session_distcode = $chargeData->distcode ?? null;
        $session_regioncode = $chargeData->regioncode ?? null;


        // Validate and process the view name
        if (empty($viewname)) {
            return abort(400, 'The view name is empty.');
        }

        // Split the view name by '.' and get the last part as the page name
        $parts = explode('.', $viewname);
        $pagename = count($parts) > 1 ? end($parts) : null;

        if (!$pagename) {
            return abort(400, 'The view name does not contain a valid page identifier.');
        }

        // Fetch necessary data from the models
        $roleaction = UserManagementModel::roleactiondetail();
        $dept = UserManagementModel::deptdetail($pagename);
        // $distdel = UserManagementModel::distdetail($pagename);

        if (in_array($chargeData->roletypecode, [View::shared('Re_roletypecode'), View::shared('Ho_roletypecode'), View::shared('Dist_roletypecode')]))
            // $designation = UserManagementModel::designationdetail($pagename);
            $designation = UserManagementModel::getDesignationBasedonDept($session_deptcode, $pagename);

        else
            $designation = '';


        if ($pagename == 'createuser') {
            if (in_array($chargeData->roletypecode, [View::shared('Re_roletypecode'), View::shared('Ho_roletypecode'), View::shared('Dist_roletypecode')])) {
                //echo 'jo';
                $distdetails = UserManagementModel::getRegionDistrictInstDelBasedOnDept($session_deptcode, $session_regioncode, $session_distcode, 'district', $session_roletypecode, $pagename);
            } else    $distdetails    =   '';
        } else {
            $distdetails = ($chargeData->distcode) ? [
                'distcode' => $chargeData->distcode,
                'distename' => $chargeData->distename,
                'disttname' => $chargeData->disttname

            ] : '';
        }

        // print_r($distdetails);


        // Prepare region and district details based on session data
        $regiondetails = ($chargeData->regioncode) ? [
            'regioncode' => $chargeData->regioncode,
            'regionename' => $chargeData->regionename,
            'regiontname' => $chargeData->regiontname

        ] : '';



        // Fetch role type based on session values
        $roletype = '';
        if ($session_deptcode) {
            $roletype = UserManagementModel::roletypebasedon_sessionroletype($session_deptcode, $session_roletypecode, $pagename);
        }

        //print_r($roletype);

        // Return the view with all the necessary data
        return view($viewname, compact('dept', 'designation', 'roleaction', 'roletype', 'regiondetails', 'distdetails'));
    }


    public function getDesignationBasedonDept(Request $request)
    {
        $deptcode   =   $_REQUEST['deptcode'];
        $page       =   $_REQUEST['page'];

        $request->validate([
            'deptcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'     =>  'The :attribute field must be a valid number.',
        ]);

        // Fetch user data based on deptuserid
        $roletypedel = UserManagementModel::getDesignationBasedonDept($deptcode, $page); // Adjust query as needed

        if ($roletypedel) {
            return response()->json(['success' => true, 'data' => $roletypedel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }




    /******************************************** User Details - Form **************************************************/

    // public function storeOrUpdate(Request $request, $userId = null)
    // {

    //     $chargedel = session('charge');
    //     if (!$chargedel || !isset($chargedel->userchargeid)) {
    //         return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
    //     }

    //     // print_r($_POST);
    //     //dd($request->all());
    //     // Validation for user input

    //     // $data = $request->all();

    //     // // Add new fields to the data array
    //     // $data['created_at'] = View::shared('get_nowtime');  // Set the current timestamp for created_at
    //     // $data['updated_at'] = View::shared('get_nowtime');  // Set the current timestamp for updated_at

    //     if ($request->action == 'update') {
    //         $deptuserid = Crypt::decryptString($request->userid);
    //         $request->merge(['userid' => $deptuserid]);
    //     }


    //     $dob = Carbon::createFromFormat('d/m/Y', $request->input('dob'))->format('Y-m-d');
    //     $dor = Carbon::createFromFormat('d/m/Y', $request->input('dor'))->format('Y-m-d');
    //     $doj = Carbon::createFromFormat('d/m/Y', $request->input('doj'))->format('Y-m-d');

    //     // Manually inject the formatted date back into the request so that it gets validated properly
    //     $request->merge(['dob' => $dob]);
    //     $request->merge(['dor' => $dor]);
    //     $request->merge(['doj' => $doj]);

    //     $deptcode   =   $request->deptcode;

    //     if (in_array($chargedel->roletypecode, [View::shared('Re_roletypecode'), View::shared('Ho_roletypecode'), View::shared('Dist_roletypecode')]))
    //         $deptcode   =   $chargedel->deptcode;

    //     $request->merge(['deptcode' => $deptcode]);


    //     $distcode   =   $request->distcode;

    //     if (View::shared('Dist_roletypecode') == $chargedel->roletypecode) {
    //         $distcode   =   $chargedel->distcode;
    //         $request->merge(['distcode' => $distcode]);
    //     }




    //     $request->validate([
    //         'deptcode'      => ['required', 'string', 'regex:/^\d+$/'], // Ensures only digits, allows leading zeros
    //         'distcode'      => ['nullable', 'string', 'regex:/^\d+$/'], // Ensures only digits, allows leading zeros
    //         'username'      =>  ['required', 'regex:/^[A-Za-z\s]+$/', 'max:100'],         // Only alphabets (no numbers or symbols)
    //         'ifhrmsno'      =>  'required|alpha_num|max:20',             // Alphanumeric (letters and numbers)
    //         'gendercode'    =>  'required|alpha|max:1',
    //         'dob'           =>  'required|date|date_format:Y-m-d|before_or_equal:today|before:18 years ago',     //date_format:Y-m-d //'after:today' // after:start_date'
    //         'email'         =>  'required|email|max:100',                    // Valid email format
    //         'desigid'       =>  ['required', 'string', 'regex:/^\d+$/'], // Ensures only digits, allows leading zeros
    //         'doj'           =>  'required|date|before_or_equal:today|after:dob|date_format:Y-m-d',
    //         'dor'           =>  'required|date|after_or_equal:today|after:doj|after:dob|date_format:Y-m-d',
    //         // 'auditorflag'   =>  'required|alpha|max:1',
    //         'mobilenumber'  =>  'required|integer'
    //     ], [
    //         'required' => 'The :attribute field is required.',
    //         'alpha' => 'The :attribute field must contain only letters.',
    //         'integer' => 'The :attribute field must be a valid number.',
    //         'regex'     =>  'The :attribute field must be a valid number.',
    //         'alpha_num' => 'The :attribute field must contain only letters and numbers.',
    //         'email' => 'The :attribute field must be a valid email address.',
    //         'date' => 'The :attribute field must be a valid date.',
    //         'max' => 'The :attribute field must not exceed :max characters.',
    //         'before_or_equal' => 'The :attribute field must be before or equal to today.',
    //         'after_or_equal' => 'The :attribute field must be on or after :date.',
    //         'dob.before' => 'The date of birth (DOB) must be before 18 years ago.',
    //         'dob.after_or_equal' => 'The date of birth (DOB) must be today or in the future.',
    //         'doj.after:dob' => 'The date of joining must be greater than Date of birth.',
    //         'dor.after:doj' => 'The date of reliveing must be greater than Date of birth.',
    //         'dor.after:dob' => 'The date of reliveing  must be greater than date of joining.',
    //     ]);



    //     $data = [
    //         'deptcode' => $deptcode,

    //         'username' => $request->input('username'),
    //         'ifhrmsno' => $request->input('ifhrmsno'),
    //         'gendercode' => $request->input('gendercode'),
    //         'dob' => $request->input('dob'),
    //         'email' => $request->input('email'),
    //         'desigcode' => $request->input('desigid'),
    //         'doj' => $request->input('doj'),
    //         'dor' => $request->input('dor'),
    //         // 'auditorflag' => $request->input('auditorflag'),
    //         'mobilenumber' => $request->input('mobilenumber'),
    //         // 'roletypecode'  => $request->input('roletypecode'),
    //         'statusflag' => 'Y',
    //         'createdon' => View::shared('get_nowtime'),  // Current timestamp for created_at
    //         'updatedon' => View::shared('get_nowtime'),  // Current timestamp for updated_at
    //         'chargeassigned'    =>  'N'
    //     ];

    //     if ($request->input('distcode')) {
    //         $data['distcode'] = $distcode;
    //     } else    $data['distcode'] = null;

    //     if ($request->action == 'update')
    //         $userId =   $request->input('userid');
    //     else
    //         $userId =   null;


    //     // try {
    //     //     // Pass the current user ID (if available) for the update or create logic
    //     //     $user = UserModel::createIfNotExistsOrUpdate($data, $userId);

    //     //     if (!$user) {
    //     //         // If user already exists (based on conditions), return an error
    //     //         return response()->json(['error' => 'A user with the same email, phone, name, and address already exists.'], 400);
    //     //     }

    //     //     // Return success message
    //     //     return response()->json(['success' => 'User created/updated successfully', 'user' => $user]);
    //     // } catch (QueryException $e) {
    //     //     // Handle database exceptions (e.g., duplicate entry)
    //     //     return response()->json(['error' => 'Database error occurred: ' . $e->getMessage()], 500);
    //     // } catch (Exception $e) {
    //     //     // Handle other exceptions
    //     //     return response()->json(['error' => 'Something went wrong: ' . $e->getMessage()], 500);
    //     // }
    //     try {
    //         // Call the model method for create or update
    //         $user = UserManagementModel::createuser_insertupdate($data, $userId, 'audit.deptuserdetails');

    //         // If no user is returned, it means a conflict occurred and an exception was thrown
    //         return response()->json(['success' => 'usercreated', 'user' => $user]);
    //     } catch (\Exception $e) {
    //         // Catch the exception thrown by the model and return the error message
    //         return response()->json(['error' => $e->getMessage()], 409);
    //     }
    // }

    public function storeOrUpdate(Request $request, $userId = null)
    {

        $userdel    =   session('user');
        $sessionuserid  =   $userdel->userid;
        try {

            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }


            if ($request->action == 'update') {
                $deptuserid = Crypt::decryptString($request->userid);
                $request->merge(['userid' => $deptuserid]);
            }


            $dob = Carbon::createFromFormat('d/m/Y', $request->input('dob'))->format('Y-m-d');
            $dor = Carbon::createFromFormat('d/m/Y', $request->input('dor'))->format('Y-m-d');
            $doj = Carbon::createFromFormat('d/m/Y', $request->input('doj'))->format('Y-m-d');

            // Manually inject the formatted date back into the request so that it gets validated properly
            $request->merge(['dob' => $dob]);
            $request->merge(['dor' => $dor]);
            $request->merge(['doj' => $doj]);

            $deptcode   =   $request->deptcode;

            if (in_array($chargedel->roletypecode, [View::shared('Re_roletypecode'), View::shared('Ho_roletypecode'), View::shared('Dist_roletypecode')]))
                $deptcode   =   $chargedel->deptcode;

            $request->merge(['deptcode' => $deptcode]);


            $distcode   =   $request->distcode;

            if (View::shared('Dist_roletypecode') == $chargedel->roletypecode) {
                $distcode   =   $chargedel->distcode;
                $request->merge(['distcode' => $distcode]);
            }



            // Define validation rules
            // $rules = [
            //     'distcode' => ['nullable', 'string', 'regex:/^\d+$/'],
            //     'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            //     'email'    => ['required', 'email'],
            //     // Add your other fields here...
            // ];

            $rules = [
                'deptcode'      => ['required', 'string', 'regex:/^\d+$/'], // Ensures only digits, allows leading zeros
                'distcode'      => ['nullable', 'string', 'regex:/^\d+$/'], // Ensures only digits, allows leading zeros
                'user_name'      =>  ['required', 'regex:/^[A-Za-z\s]+$/', 'max:100'],         // Only alphabets (no numbers or symbols)
                // 'usertamilname' =>   ['required', 'regex:/^[A-Za-z\s]+$/', 'max:100'],
                'ifhrmsno'      =>  'required|alpha_num|max:20',             // Alphanumeric (letters and numbers)
                'gendercode'    =>  'required|alpha|max:1',
                'dob'           =>  'required|date|date_format:Y-m-d|before_or_equal:today|before:18 years ago',     //date_format:Y-m-d //'after:today' // after:start_date'
                'email'         =>  'required|email|max:100',                    // Valid email format
                'desigid'       =>  ['required', 'string', 'regex:/^\d+$/'], // Ensures only digits, allows leading zeros
                'doj'           =>  'required|date|before_or_equal:today|after:dob|date_format:Y-m-d',
                'dor'           =>  'required|date|after_or_equal:today|after:doj|after:dob|date_format:Y-m-d',
                // 'auditorflag'   =>  'required|alpha|max:1',
                'mobilenumber'  =>  'required|integer',
		'reservelist' =>    'required|in:Y,N',
		 'psaflag' =>    'required|in:Y,N',

            ];




            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $data = [
                'deptcode' => $deptcode,
                'username' => $request->input('user_name'),
                'usertamilname' => $request->input('usertamilname'),
                'ifhrmsno' => $request->input('ifhrmsno'),
                'gendercode' => $request->input('gendercode'),
                'dob' => $request->input('dob'),
                'email' => $request->input('email'),
                'desigcode' => $request->input('desigid'),
                'doj' => $request->input('doj'),
                'dor' => $request->input('dor'),
                // 'auditorflag' => $request->input('auditorflag'),
                'mobilenumber' => $request->input('mobilenumber'),
                // 'roletypecode'  => $request->input('roletypecode'),
                'statusflag' => 'Y',
                'updatedon' => View::shared('get_nowtime'),  // Current timestamp for updated_at
                'updatedby' =>  $sessionuserid,
                'chargeassigned'    =>  'N',
		'reservelist' => $request->input('reservelist'),
		  'psaflag' => $request->input('psaflag')



            ];

            if ($request->input('distcode')) {
                $data['distcode'] = $distcode;
            } else    $data['distcode'] = null;

            if ($request->action == 'update') {
                $userId =   $request->input('userid');
            } else {
                $userId =   null;
                $data['createdby'] = $sessionuserid;
                $data['createdon'] = View::shared('get_nowtime');
            }

            $user = UserManagementModel::createuser_insertupdate($data, $userId, 'audit.deptuserdetails');

            // If no user is returned, it means a conflict occurred and an exception was thrown
            return response()->json(['success' => 'usercreated', 'user' => $user], 201);


            // // If validation passes, proceed
            // return response()->json(['message' => 'User created successfully'], 201);

        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }




    // public function fetchUserData(Request $request)
    // {
    //     // Retrieve deptuserid from the request

    //     $deptuserid = Crypt::decryptString($request->deptuserid);

    //     $request->merge(['deptuserid' => $deptuserid]);

    //     $request->validate([
    //         'deptuserid'  =>  'required|integer'
    //     ], [
    //         'required' => 'The :attribute field is required.',
    //         'integer' => 'The :attribute field must be a valid number.'
    //     ]);


    //     // Ensure deptuserid is provided
    //     if (!$deptuserid) {
    //         return response()->json(['success' => false, 'message' => 'User ID not provided'], 400);
    //     }

    //     // Fetch user data based on deptuserid
    //     $user = UserModel::where('deptuserid', $deptuserid)->first(); // Adjust query as needed

    //     if ($user) {
    //         return response()->json(['success' => true, 'data' => $user]);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'User not found'], 404);
    //     }
    // }


    public function fetchUserData(Request $request)
    {
        try {
            // Check if userid is provided
            $userid = $request->filled('userid') ? Crypt::decryptString($request->userid) : null;

            // Fetch data using the model
            $userdel = UserManagementModel::fetchuserData($userid, 'audit.deptuserdetails');


            // Encrypt user IDs in results
            // $userdel->transform(function ($all) {
            //     $all->encrypted_userid = Crypt::encryptString($all->deptuserid);
            //     return $all;
            // });

            foreach ($userdel as $all) {
                $all->encrypted_userid = Crypt::encryptString($all->deptuserid);
            }

            // If userid is provided (edit mode)
            if ($userid) {
                if ($userdel->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found',
                        'data' => null
                    ], 404);
                }

                // Encrypt user IDs in results
                $userdel->transform(function ($all) {
                    $all->encrypted_userid = Crypt::encryptString($all->deptuserid);
                    return $all;
                });

                return response()->json([
                    'success' => true,
                    'message' => '',
                    'data' => $userdel
                ], 200);
            }

            // If userid is not provided (fetch mode)
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $userdel->isEmpty() ? null : $userdel
            ], 200);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }



    // public function fetchAllData()
    // {
    //     // Fetch all users
    //     // $users = UserModel::all(); // Or any other query to fetch users

    //     // $users = UserModel::query() // Start the query builder
    //     // ->join('audit.mst_dept as dept', 'deptuserdetails.deptcode', '=', 'dept.deptcode') // INNER JOIN with alias 'dept'
    //     // ->join('audit.mst_designation as desig', 'deptuserdetails.desigcode', '=', 'desig.desigcode') // INNER JOIN with alias 'desig'
    //     // ->select(
    //     //     'desig.desigesname',
    //     //     'desig.desigelname',
    //     //     'desig.desigtsname',
    //     //     'desig.desigtlname',
    //     //     'dept.deptesname',
    //     //     'dept.deptelname',
    //     //     'dept.depttsname',
    //     //     'dept.depttlname',
    //     //     'deptuserdetails.deptuserid',
    //     //     'deptuserdetails.deptcode',
    //     //     'deptuserdetails.username',
    //     //     'deptuserdetails.ifhrmsno',
    //     //     'deptuserdetails.gendercode',
    //     //     'deptuserdetails.dob',
    //     //     'deptuserdetails.email',
    //     //     'deptuserdetails.doj',
    //     //     'deptuserdetails.dor',
    //     //     'deptuserdetails.auditorflag',
    //     //     'deptuserdetails.mobilenumber'
    //     // )
    //     // ->where('deptuserdetails.statusflag', '=', 'Y') // Filter records where statusflag is 'Y'
    //     // ->orderBy('deptuserdetails.createdon', 'desc') // Order results by createdon in descending order
    //     // ->get(); // Execute and get the results as a collection



    //     foreach ($users as $user) {
    //         $user->encrypted_deptuserid = Crypt::encryptString($user->deptuserid);
    //     }



    //     // Return data in JSON format
    //     return response()->json(['data' => $users]); // Ensure the data is wrapped under "data"
    // }


    /******************************************** User Details - Form **************************************************/




    /******************************************** Charge Details - Form **************************************************/


    // public function charge_insertupdate1(Request $request)
    // {

    //     $chargedel = session('charge');
    //     if (!$chargedel || !isset($chargedel->userchargeid)) {
    //         return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
    //     }

    //     $userchargeid = $chargedel->userchargeid;
    //     $chargeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('chargeid')) : null;

    //     // Validate request data
    //     $validatedData = $request->validate([
    //         'chargedescription' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'max:100'],
    //         'roletypecode'      => 'required|string|regex:/^\d+$/',
    //         'roleactioncode'    => 'required|string|regex:/^\d+$/',
    //         'desigcode'         => 'required|string|regex:/^\d+$/',
    //     ]);

    //     $deptcode   =   $request->deptcode;
    //     $regioncode =   $request->regioncode;
    //     $distcode   =   $request->distcode;
    //     $instmappingcode    =   $request->instmappingcode;



    //     if (in_array($chargedel->roletypecode, [View::shared('Re_roletypecode'), View::shared('Ho_roletypecode'), View::shared('Dist_roletypecode')])) {
    //         $deptcode   =   $chargedel->deptcode;
    //         if (in_array($chargedel->roletypecode, [View::shared('Re_roletypecode'), View::shared('Dist_roletypecode')]))
    //             $regioncode =   $chargedel->regioncode;
    //         if ($chargedel->roletypecode == View::shared('Dist_roletypecode'))
    //             $distcode =   $chargedel->distcode;
    //     }



    //     // Prepare data for insertion or update
    //     $data = array_merge($validatedData, [
    //         'statusflag' => 'Y',
    //         'deptcode'   => $deptcode ?? null,
    //         'regioncode' => $regioncode ?? null,
    //         'distcode'   => $distcode ?? null,
    //         'instmappingcode' => $instmappingcode ?? null,
    //     ]);

    //     if ($request->input('action') === 'insert') {
    //         $data['createdon'] = View::shared('get_nowtime');
    //         $data['createdby'] = $userchargeid;
    //     }
    //     // print_r($data);

    //     try {
    //         $result = UserManagementModel::createcharge_insertupdate($data, $chargeid, 'audit.chargedetails');
    //         return response()->json(['success' => true, 'message' => 'createcharge_success']);
    //     } catch (\Exception $e) {
    //         // Return 422 for 'record already exists' and 500 for others
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    //     }
    // }



public function charge_insertupdate(Request $request)
    {
        $userdel    =   session('user');
        $sessionuserid  =   $userdel->userid;
        try {
            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }

            $userchargeid = $chargedel->userchargeid;
            $chargeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('chargeid')) : null;

            $roleactioncode = $request->input('roleactioncode');
            $deptcode = $request->input('deptcode');

            $requiresCategory = false;
			  $requiresSubCategory = false;
            if ($roleactioncode && $deptcode) {
                $roleactionDetails = DB::table('audit.mst_roleaction') // Adjust table name
                    ->where('roleactioncode', $roleactioncode)
                    ->where('statusflag', 'Y')
                    ->first();

                if ($roleactionDetails && $roleactionDetails->categoryflag == 'Y') {
                    $requiresCategory = true;
                }
                if ($roleactionDetails && $roleactionDetails->subcategoryflag == 'Y') {
                    $requiresSubCategory = true;
                }
            }

            // Validate request data
            $rules = [
                'chargedescription' => ['required', 'regex:/^[a-zA-Z\s]+$/', 'max:100'],
                'roletypecode'      => 'required|string|regex:/^\d+$/',
                'roleactioncode'    => 'required|string|regex:/^\d+$/',
                'desigcode'         => 'required|string|regex:/^\d+$/',
                'deptcode'         => 'required|string|regex:/^\d+$/',

            ];

            if ($requiresCategory) {
                $rules['catcode'] = 'required|string|regex:/^\d+$/';
            } else {
                $rules['catcode'] = 'nullable|string|regex:/^\d+$/';
            }

            if ($requiresSubCategory) {
                $rules['subcatid'] = 'nullable|integer';
            }


            if ($chargedel->deptcode)    $deptcode   =   $chargedel->deptcode;
            else  $deptcode   =   $request->deptcode;


            $request->merge(['deptcode' => $deptcode]);
            // dd($request->all());

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            // dd($request->all());




            $regioncode =   $request->regioncode;
            $distcode   =   $request->distcode;
            $instmappingcode    = $request->instmappingcode;
            $categorycode = $request->catcode; // Get categorycode from request
            $subcatid = $request->subcatid;




            if (in_array($chargedel->roletypecode, [View::shared('Re_roletypecode'), View::shared('Ho_roletypecode'), View::shared('Dist_roletypecode')])) {
                $deptcode   =   $chargedel->deptcode;
                if (in_array($chargedel->roletypecode, [View::shared('Re_roletypecode'), View::shared('Dist_roletypecode')]))
                    $regioncode =   $chargedel->regioncode;
                if ($chargedel->roletypecode == View::shared('Dist_roletypecode'))
                    $distcode =   $chargedel->distcode;
            }



            // Prepare data for insertion or update
            $data =   [
                'chargedescription' => $request->chargedescription,
                'roletypecode'      => $request->roletypecode,
                'roleactioncode'    => $request->roleactioncode,
                'desigcode'         => $request->desigcode,
                'statusflag' => 'Y',
                'deptcode'   => $deptcode ?? null,
                'regioncode' => $regioncode ?? null,
                'distcode'   => $distcode ?? null,
                'instmappingcode' => $instmappingcode ?? null,
                'catcode'      => $categorycode,
                'subcatid'      => $subcatid ?? null,
            ];

            if (in_array($request->roletypecode, [View::shared('Re_roletypecode'), View::shared('Ho_roletypecode'), View::shared('Dist_roletypecode')])) {
                // $deptcode   =   $chargedel->deptcode;
                if (in_array($request->roletypecode, [View::shared('Re_roletypecode'), View::shared('Dist_roletypecode')])) {
                    // $regioncode =   $chargedel->regioncode;
                    if ($request->roletypecode == View::shared('Dist_roletypecode')) {
                        // $distcode =   $chargedel->distcode;
                    } else  $data['distcode']   =   null;
                } else {
                    $data['regioncode']   =   null;
                    $data['distcode']   =   null;
                    $data['instmappingcode']   =   null;
                }
            } else {
                $data['deptcode']   =   null;
                $data['regioncode']   =   null;
                $data['distcode']   =   null;
                $data['instmappingcode']   =   null;
            }

            //    print_r($data);


            if (!$requiresCategory || empty($categorycode)) {
                $data['catcode'] = null;
            }


            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $sessionuserid;
                $data['updatedby'] = $sessionuserid;
                $data['updatedon'] = View::shared('get_nowtime');
            } elseif ($request->input('action') === 'update') {
                $data['updatedby'] = $sessionuserid;
                $data['updatedon'] = View::shared('get_nowtime');
            }



            //   print_r($data);
            //   exit();
            $result = UserManagementModel::createcharge_insertupdate($data, $chargeid, 'audit.chargedetails');
            return response()->json(['success' => true, 'message' => 'createcharge_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 400], 400);
        } catch (\Exception $e) {
            // Return 422 for 'record already exists' and 500 for others
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }


    public function getroletypecode_basedondept(Request $request)
    {
        $deptcode   =   $_REQUEST['deptcode'];
        $page       =   $_REQUEST['page'];

        $request->validate([
            'deptcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'     =>  'The :attribute field must be a valid number.',
        ]);

        // Fetch user data based on deptuserid
        $roletypedel = UserManagementModel::roletypebasedon_sessionroletype($deptcode, '', $page); // Adjust query as needed


        if ($roletypedel) {
            return response()->json(['success' => true, 'data' => $roletypedel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    public function getRoleactionBasedOnRoletype(Request $request)
    {
        $deptcode   =   $_REQUEST['deptcode'];
        $roletypecode   =   $_REQUEST['roletypecode'];
        $page       =   $_REQUEST['page'];

        $request->validate([
            'deptcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
            'roletypecode'  =>  ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'     =>  'The :attribute field must be a valid number.',
        ]);

        // Fetch user data based on deptuserid
        $roletypedel = UserManagementModel::getRoleactionBasedOnRoletype($deptcode, $roletypecode, $page); // Adjust query as needed

        if ($roletypedel) {
            return response()->json(['success' => true, 'data' => $roletypedel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }




    public function getRegionDistInstBasedOnDept(Request $request)
    {
        $page = $request->input('page');
        if ($page == 'createuser') {
            // Validate incoming request
            $validatedData = $request->validate([
                'deptcode'      => ['required', 'string', 'regex:/^\d+$/'],
            ], [
                'required' => 'The :attribute field is required.',
                'regex'    => 'The :attribute field must be a valid number.',
                'in'       => 'The :attribute field must be one of: region, district, institution.',
            ]);

            // Extract validated data
            $deptcode = $validatedData['deptcode'];

            // Fetch data from the model
            try {
                $roletypedel = UserManagementModel::getRegionDistrictInstDelBasedOnDept($deptcode, '', '', 'district', '', $page);

                if ($roletypedel) {
                    return response()->json(['success' => true, 'data' => $roletypedel]);
                }

                return response()->json(['success' => false, 'message' => 'Data not found'], 404);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        } else {
            // Validate incoming request
            $validatedData = $request->validate([
                'deptcode'      => ['required', 'string', 'regex:/^\d+$/'],
                'roletypecode'  => ['required', 'string', 'regex:/^\d+$/'],
                'regioncode'    => ['nullable', 'string', 'regex:/^\d+$/'],
                'distcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
                'valuefor'      => ['required', 'string', 'in:region,district,institution'], // Include "region"
            ], [
                'required' => 'The :attribute field is required.',
                'regex'    => 'The :attribute field must be a valid number.',
                'in'       => 'The :attribute field must be one of: region, district, institution.',
            ]);

            // Extract validated data
            $deptcode = $validatedData['deptcode'];
            $regioncode = $validatedData['regioncode'] ?? null;
            $distcode = $validatedData['distcode'] ?? null;
            $roletypecode = $validatedData['roletypecode'];
            $valuefor = $validatedData['valuefor'];

            // Additional validation for 'region'
            if ($valuefor === 'region' && !$deptcode) {
                return response()->json(['success' => false, 'message' => 'Department code is required for region.'], 422);
            }

            // Additional validation for 'district'
            if ($valuefor === 'district' && !$regioncode) {
                return response()->json(['success' => false, 'message' => 'Region code is required for district.'], 422);
            }

            // Additional validation for 'institution'
            if ($valuefor === 'institution' && in_array($roletypecode, [View::shared('Re_roletypecode'), View::shared('Dist_roletypecode')])) {
                if (!$regioncode) {
                    return response()->json(['success' => false, 'message' => 'Region code is required for institution.'], 422);
                }
                if ($roletypecode === View::shared('Dist_roletypecode') && !$distcode) {
                    return response()->json(['success' => false, 'message' => 'District code is required for this role type.'], 422);
                }
            }

            // Fetch data from the model
            try {
                $roletypedel = UserManagementModel::getRegionDistrictInstDelBasedOnDept(
                    $deptcode,
                    $regioncode,
                    $distcode,
                    $valuefor,
                    $roletypecode,
                    $page
                );

                if ($roletypedel) {
                    return response()->json(['success' => true, 'data' => $roletypedel]);
                }

                return response()->json(['success' => false, 'message' => 'Data not found'], 404);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        }
    }

    public function fetchchargeData(Request $request)
    {
        $chargeid = $request->has('chargeid') ? Crypt::decryptString($request->chargeid) : null;
        $chargedel = UserManagementModel::fetchchargeData($chargeid, 'audit.chargedetails');

        try {


            $chargeid = $request->has('chargeid') ? Crypt::decryptString($request->chargeid) : null;

            // Fetch data using the model
            $chargedel = UserManagementModel::fetchchargeData($chargeid, 'audit.chargedetails');

            foreach ($chargedel as $all) {
                $all->encrypted_chargeid = Crypt::encryptString($all->chargeid);
            }


            // If userid is provided (edit mode)
            if ($chargeid) {
                if ($chargedel->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not found',
                        'data' => null
                    ], 404);
                }

                // Encrypt user IDs in results
                $chargedel->transform(function ($all) {
                    $all->encrypted_chargeid = Crypt::encryptString($all->chargeid);
                    return $all;
                });

                return response()->json([
                    'success' => true,
                    'message' => '',
                    'data' => $chargedel
                ], 200);
            }

            // If userid is not provided (fetch mode)
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $chargedel->isEmpty() ? null : $chargedel
            ], 200);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }

    /******************************************** Charge Details - Form **************************************************/


    /******************************************** Assign Details - Form **************************************************/


    public function getdesignation_fromchargedet(Request $request)
    {
        $request->validate([
            'deptcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
            'roletypecode'  =>  ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'     =>  'The :attribute field must be a valid number.',
        ]);

        $data   =   array(
            'deptcode'  =>     $_REQUEST['deptcode'],
            'roletypecode'  =>     $_REQUEST['roletypecode'],
            'regioncode'  =>     $_REQUEST['regioncode'],
            'distcode'  =>     $_REQUEST['distcode'],
            'instmappingcode'  =>     $_REQUEST['instmappingcode']
        );


        // Fetch user data based on deptuserid
        $roletypedel = UserManagementModel::getDesignationFromChargeDetails('audit.mst_designation', $data); // Adjust query as needed

        if ($roletypedel) {
            return response()->json(['success' => true, 'data' => $roletypedel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

public function getchargedescription(Request $request)
    {
        $request->validate([
            'deptcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
            'roletypecode'  =>  ['required', 'string', 'regex:/^\d+$/'],
            'desigcode'     =>  ['required', 'string', 'regex:/^\d+$/']
        ], [
            'required' => 'The :attribute field is required.',
            'regex'     =>  'The :attribute field must be a valid number.',
        ]);

        $page = $_REQUEST['page'] ?? null;
        if (!$page) {
            $refererPath = parse_url($request->headers->get('referer', ''), PHP_URL_PATH) ?? '';

            if (str_contains($refererPath, '/assign_charge')) {
                $page = 'assigncharge';
            } elseif (str_contains($refererPath, '/additional_charge')) {
                $page = 'additionalcharge';
            } elseif (str_contains($refererPath, '/unassign_charge')) {
                $page = 'unassigncharge';
            }
        }

        $data   =   array(
            'deptcode'  =>     $_REQUEST['deptcode'],
            'roletypecode'  =>     $_REQUEST['roletypecode'],
            'regioncode'  =>     $_REQUEST['regioncode'],
            'distcode'  =>     $_REQUEST['distcode'],
            'instmappingcode'  =>     $_REQUEST['instmappingcode'],
            'desigcode'         =>  $_REQUEST['desigcode'],
            'page'              =>  $page
        );

        $roletypedel = UserManagementModel::getchargedescription('audit.mst_charge', $data); // Adjust query as needed

        if ($roletypedel) {
            return response()->json(['success' => true, 'data' => $roletypedel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }




    // public function getuserbasedonroletype(Request $request)
    // {
    //     $request->validate([
    //         'desigcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
    //         'distcode'  =>  ['nullable', 'string', 'regex:/^\d+$/'],
    //         'roletypecode'  =>  ['required', 'string', 'regex:/^\d+$/'],
    //     ], [
    //         'required' => 'The :attribute field is required.',
    //         'regex'     =>  'The :attribute field must be a valid number.',
    //     ]);

    //     $data   =   array(
    //         'desigcode'  =>     $_REQUEST['desigcode'],
    //         'distcode'  =>     $_REQUEST['distcode'],
    //         'roletypecode'  =>     $_REQUEST['roletypecode'],
    //         'regioncode'  =>     $_REQUEST['regioncode'],
    //         'page'  =>     $_REQUEST['page'],
    //     );
    //     if (($_REQUEST['page'] == 'additionalcharge')|| ($_REQUEST['page'] == 'unassigncharge')) {
    //         $data['chargeid']   =   $_REQUEST['chargeid'];
    //     }



    //     if ($_REQUEST['page'] == 'unassigncharge') {
    //         $roletypedel = UserManagementModel::getuserbasedonroletype_unassigncharge($data); // Adjust query as needed
    //     }
    //     else
    //     {
    //         $roletypedel = UserManagementModel::getuserbasedonroletype($data); // Adjust query as needed
    //     }

    //     if ($roletypedel) {
    //         return response()->json(['success' => true, 'data' => $roletypedel]);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'User Not Found'], 404);
    //     }
    // }


    public function getuserbasedonroletype(Request $request)
    {
        $request->validate([
            'desigcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
            'distcode'  =>  ['nullable', 'string', 'regex:/^\d+$/'],
            'roletypecode'  =>  ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'     =>  'The :attribute field must be a valid number.',
        ]);

        $data   =   array(
            'desigcode'  =>     $_REQUEST['desigcode'],
 	    'deptcode'  =>     $_REQUEST['deptcode'],
            'distcode'  =>     $_REQUEST['distcode'],
            'roletypecode'  =>     $_REQUEST['roletypecode'],
            'regioncode'  =>     $_REQUEST['regioncode'],
            'page'  =>     $_REQUEST['page'],
        );
        if (($_REQUEST['page'] == 'additionalcharge')|| ($_REQUEST['page'] == 'unassigncharge')) {
            $data['chargeid']   =   $_REQUEST['chargeid'];
        }

        if(($_REQUEST['page'] == 'additionalcharge'))
        {
            $data['include_otherdeptment']   =   $_REQUEST['include_otherdeptment'];
            $data['otherdeptcode']   =   $_REQUEST['otherdeptcode'];
            $data['otherdept_desigcode']   =   $_REQUEST['otherdept_desigcode'];
            $data['otherdept_roletypecode']   =   $_REQUEST['otherdept_roletypecode'];
            $data['deptcode']   =   $_REQUEST['deptcode'];
        }
        else
        {
            $data['include_otherdeptment']   =   'N';
        }


        if ($_REQUEST['page'] == 'unassigncharge') {
            $roletypedel = UserManagementModel::getuserbasedonroletype_unassigncharge($data); // Adjust query as needed
        }
        else
        {
            $roletypedel = UserManagementModel::getuserbasedonroletype($data); // Adjust query as needed
        }

        if ($roletypedel) {
            return response()->json(['success' => true, 'data' => $roletypedel]);
        } else {
            return response()->json(['success' => false, 'message' => 'User Not Found'], 404);
        }
    }





    // public function assigncharge_insertupdate(Request $request)
    // {
    //     // print_r($_POST);
    //     // exit;
    //     try {
    //         $page = $request->input('page');
    //         $chargedel = session('charge');
    //         if (!$chargedel || !isset($chargedel->userchargeid)) {
    //             return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
    //         }

    //         $userchargeid = $chargedel->userchargeid;
    //         $userchargeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('userchargeid')) : null;

    //         // Validate request data
    //         $rules = [
    //             'userid' => ['required', 'regex:/^\d+$/'],
    //             'chargeid'      => 'required|string|regex:/^\d+$/',
    //         ];

    //         // Create a validator instance
    //         $validator = Validator::make($request->all(), $rules);

    //         // If validation fails, throw an exception with a single message
    //         if ($validator->fails()) {
    //             throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
    //         }

    //         // Prepare data for insertion or update
    //         $data = [
    //             'statusflag' => 'Y',
    //             'userid'        =>  $request->input('userid'),
    //             'chargeid'        =>  $request->input('chargeid'),
    //             'chargefrom'   => View::shared('get_nowtime'),
    //         ];




    //         if ($page == 'assigncharge')     $data['chargeflag'] = 'P';
    //         if ($page == 'additionalcharge')     $data['chargeflag'] = 'A';


    //         if ($request->input('action') === 'insert') {
    //             $data['createdon'] = View::shared('get_nowtime');
    //             $data['createdby'] = $userchargeid;
    //         } else if ($request->input('action') === 'update') {
    //             $data['updatedon'] = View::shared('get_nowtime');
    //             $data['updatedby'] = $userchargeid;
    //         }
    //         //throw $th;
    //         $result = UserManagementModel::assigncharge_insertupdate($data, $userchargeid, 'audit.userchargedetails');
    //         return response()->json(['success' => true, 'message' => 'assignCharge_insert']);
    //     } catch (ValidationException $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
    //     } catch (\Exception $e) {
    //         // Return 422 for 'record already exists' and 500 for others
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    //     }
    // }
    public function assigncharge_insertupdate(Request $request)
    {
        //print_r($_POST);
        // exit;
        try {

 	$userdel    =   session('user');
        $sessionuserid=   $userdel->userid;

            $page = $request->input('page');
            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }

            $userchargeid = $chargedel->userchargeid;
            $userchargeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('userchargeid')) : null;

            // // Validate request data
            // $rules = [
            //     'userid' => ['required', 'regex:/^\d+$/'],
            //     'chargeid'      => 'required|string|regex:/^\d+$/',
            // ];

            $rules = [];

            // Check for Page 1 (where `userid` is an array and `chargeid` is validated as a string with regex)
            if ($request->is('assigncharge')) {
                $rules = [
                    'userid' => ['required', 'array'],
                    'chargeid' => 'required|string|regex:/^\d+$/',
                ];
            }

            // Check for Page 2 (where both `userid` and `chargeid` are integers)
            elseif ($request->is('additionalcharge')) {
                $rules = [
                    'userid' => ['required', 'integer'],
                    'chargeid' => 'required|integer',
                ];
            }
            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            // Prepare data for insertion or update
            $data = [
                'statusflag' => 'Y',
                // 'userid'        =>  $request->input('userid'),
                'chargeid'        =>  $request->input('chargeid'),
                'chargefrom'   => View::shared('get_nowtime'),
            ];






            if ($page == 'assigncharge')     $data['chargeflag'] = 'P';
            if ($page == 'additionalcharge')
            {
                $data['userid'] = $request->input('userid');
                $data['chargeflag'] = 'A';
            }




            if ($request->input('action') === 'insert') {
                $data['createdon'] =  View::shared('get_nowtime');
                $data['createdby'] = $sessionuserid;
 		$data['updatedon'] =  View::shared('get_nowtime');
                $data['updatedby'] = $sessionuserid;

            } else if ($request->input('action') === 'update') {
                $data['updatedon'] =  View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            //throw $th;

            $result = UserManagementModel::assigncharge_insertupdate($data, $userchargeid, 'audit.userchargedetails',$page,$request->input('userid'));
            return response()->json(['success' => true, 'message' => 'assignCharge_insert']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            // Return 422 for 'record already exists' and 500 for others
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }


    public function get_assignchargevalue()
    {
        try {
            // Retrieve session data
            $chargeData = session('charge');

            // Check if session data exists
            if (!$chargeData) {
                throw new \Exception('Session data is missing.');
            }

            $session_deptcode = $chargeData->deptcode ?? null;
            $session_roletypecode = $chargeData->roletypecode ?? null;

            // Fetch department details
            $dept = UserManagementModel::deptdetail('assigncharge');
            $roletype = null;

            // Fetch role type details if department code exists
            if ($session_deptcode) {
                $roletype = UserManagementModel::roletypebasedon_sessionroletype(
                    $session_deptcode,
                    $session_roletypecode,
                    'assigncharge'
                );
            }

            $data = [];
            $data['dept'] = $dept;
            $data['roletype'] = $roletype;

            // print_r($data);

            // Check if both dept and roletype have valid values
            if (!empty($dept)) {
                return response()->json([
                    'success' => true,
                    'message' => 'User Charge Created / Updated Successfully',
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid department or role type data.',
                ], 422);
            }
        } catch (\Exception $e) {
            // Handle exceptions gracefully
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }


    public function unassigncharge_insertupdate(Request $request)
    {
        try {
            // Get the current user from the session
            $userdel = session('user');
            $sessionuserid = $userdel->userid;

            // Retrieve the charge session data (this is assumed to be stored in session)
            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }

            // Capture the action parameter from the request
            $action = $request->action;

            // Validate the request data
            $rules = [
                'userid' => 'required|string|regex:/^\d+$/',
                'chargeid' => 'required|string|regex:/^\d+$/',
            ];

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            // Prepare data for the update
            $data = [
                'statusflag' => 'N',
                'chargeto' => View::shared('get_nowtime'),
                'updatedon' => View::shared('get_nowtime'),
                'updatedby' => $sessionuserid
            ];

            $wheredata = [
                'userid' => $request->input('userid'),
                'chargeid' => $request->input('chargeid'),
                'statusflag' => 'Y',
            ];

            // If the action is 'check', ensure that the user has no pending charges
            if ($request->actionfor == 'check') {

		    try {
			$additionalcharge_check = UserManagementModel::additionalcharge_check($request->input('userid'), $request->input('chargeid'));
                    //   return $additionalcharge_check;

                    if (!$additionalcharge_check) {
                        return response()->json(['success' => false, 'message' => 'User has additonal charge'], 400);
                    } else {
                    $result = UserManagementModel::checkUserHasNoPending($request->input('userid'),$request->input('chargeid'));

                    // Check if the result collection is empty
                    if ($result->isEmpty()) {
                        // No pending charges
                        return response()->json(['success' => true, 'message' => 'No pending charges for the user.']);
                    } else {
                        // Pending charges exist
                        return response()->json(['success' => false, 'message' => 'User has pending charges.'], 400);
		    }
		    }
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
                }

            } else {
                // Call the model function to unassign the charge
                $result = UserManagementModel::unassigncharge_insertupdate($data, $wheredata);
                return response()->json(['success' => true, 'message' => 'Charge unassigned successfully.']);
            }
        } catch (ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            // Handle any other exceptions (e.g., database errors)
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }



    public function fetchuserchargeData(Request $request)
    {


        try {

            $page = $request->input('page');
            // $chargeid = $request->has('chargeid') ? Crypt::decryptString($request->chargeid) : null;

            // Fetch data using the model
            $chargedel = UserManagementModel::fetchuserchargeData($page);

            // foreach ($chargedel as $all) {
            //     $all->encrypted_chargeid = Crypt::encryptString($all->chargeid);
            // }


            // If userid is provided (edit mode)
            // if ($chargeid) {
            //     if ($chargedel->isEmpty()) {
            //         return response()->json([
            //             'success' => false,
            //             'message' => 'User not found',
            //             'data' => null
            //         ], 404);
            //     }

            //     // Encrypt user IDs in results
            //     $chargedel->transform(function ($all) {
            //         $all->encrypted_chargeid = Crypt::encryptString($all->chargeid);
            //         return $all;
            //     });

            //     // return response()->json([
            //     //     'success' => true,
            //     //     'message' => '',
            //     //     'data' => $chargedel
            //     // ], 200);
            // }

            // If userid is not provided (fetch mode)
            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $chargedel->isEmpty() ? null : $chargedel
            ], 200);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid user ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }

    /******************************************** Assign Details - Form **************************************************/


    public function getroletypecodeDesignation_basedonotherdept(Request $request)
    {
        $deptcode   =   $_REQUEST['deptcode'];
        $page       =   $_REQUEST['page'];

        $request->validate([
            'deptcode'  =>  ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'     =>  'The :attribute field must be a valid number.',
        ]);

        // Fetch user data based on deptuserid
        $roletypedel = UserManagementModel::roletypebasedon_sessionroletype($deptcode, '', $page); // Adjust query as needed

        $designation = UserManagementModel::getDesignationBasedonDept($deptcode, $page); // Adjust query as needed

        if ($roletypedel) {
            return response()->json(['success' => true, 'roletypedel' => $roletypedel,'designation' => $designation]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }


    // HOD Charges

       public static function fetch_dept()
    {
        $dept = UserManagementModel::deptfetch();

        return view('usermanagement.hod_createcharge', compact('dept'));
    }


 public function fetchauditeedepartment(Request $request)
    {
        // Validate the input
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $deptcode = $request->input('deptcode');


        $audept = UserManagementModel::getauditeedept($deptcode);

        if($audept->isNotEmpty()) {
            return response()->json($audept);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }



  public function fetchdesination(Request $request)
{
    $request->validate([
        'deptcode' => ['required', 'regex:/^\d+$/'],
    ], [
        'required' => 'The :attribute field is required.',
        'regex'    => 'The :attribute field must be a valid number.',
    ]);

    $deptcode = $request->input('deptcode');

    $designation = UserManagementModel::fetchdesinationData($deptcode);

    if ($designation->isNotEmpty()) {
        return response()->json([
            'success' => true,
            'designation' => $designation
        ]);
    }

    return response()->json([
        'success' => false,
        'designation' => []
    ]);
}


 public function insertUpdatehodcreatecharge(Request $request)
    {

        try {
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;
            $deptcodeInput = $request->input('deptcode');

            // $hideSubcategory = in_array($deptcodeInput, ['01', '05', '1', '5'], true);
            // $subcategoryRules = [$hideSubcategory ? 'nullable' : 'required', 'regex:/^\\d+$/'];
            $action = $request->input('action', 'insert');
            $chargeid = null;
            if($action === 'update' && $request->filled('chargeid')) {
                $chargeid = Crypt::decryptString($request->input('chargeid'));
            }

            $request->validate([

                'deptcode' => ['required', 'regex:/^\d+$/'],
                'auditeedeptcode' => ['required', 'regex:/^\d+$/'],
                 'desigcode' => ['required', 'regex:/^\d+$/'],


            ], [

                'deptcode.required' => 'Department is required.',
                'deptcode.regex' => 'Department must be numeric.',

                'auditeedeptcode.required' => 'auditeedeptcode is required.',
                'auditeedeptcode.regex' => 'auditeedeptcode must be numeric.',

                'desigcode.required' => 'desigcode is required.',
                'desigcode.regex' => 'desigcode must be numeric.',

                'subcategory.required' => 'Subcategory is required.',
                'subcategory.regex' => 'Subcategory must be numeric.',

            ]);

        $rolemappingid = UserManagementModel::getroletypecode_basedondesig($request->input('deptcode'));

          $data = [
                'chargedescription' => $request->input('charge_des'),
                'deptcode' => $request->input('deptcode'),
                'auditeedeptcode' => $request->input('auditeedeptcode'),
                'desigcode' => $request->input('desigcode'),
                'rolemappingid' => $rolemappingid, // REQUIRED
               'statusflag' => $request->input('statusflagforcharge'),                 // REQUIRED
                'createdby' => $userid,
                'createdon' => View::shared('get_nowtime'),
                'updatedby' => $userid,
                'updatedon' => View::shared('get_nowtime'),
                'pwdupdate'=> 'Y'
            ];


            if($action === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userid;
            }

            // if($fileuploadId) {
            //     $data['fileuploadid'] = $fileuploadId;
            // }
            // dd($data);
// $data =
            $chargedata = UserManagementModel::insertUpdatehodcreatecharge($data, $chargeid);
// dd($prtitledata);
            return response()->json([
                'success' => true,
                'data' => $chargedata,
                'message' => 'hodcharge_created'
            ], 200);
        } catch (Exception $e) {
            $statusCode = $e->getCode();
            if(!is_int($statusCode) || $statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], $statusCode);
        }
    }






    public function fetchhod_createcharge(Request $request)
    {
        $chargeid = $request->has('chargeid') ? Crypt::decryptString($request->chargeid) : null;
        $chargedel = UserManagementModel::fetchhod_createcharge($chargeid, 'audit.hodchargedetails');
        // dd($chargedel);
        foreach($chargedel as $all) {
            if(isset($all->chargeid)) {
                $all->encrypted_chargeid = Crypt::encryptString($all->chargeid);
            }
        }
        return response()->json([
            'success' => !$chargedel->isEmpty(),
            'message' => $chargedel->isEmpty() ? 'User not found' : '',
            'data' => $chargedel->isEmpty() ? null : $chargedel
        ], $chargedel->isEmpty() ? 404 : 200);
    }



    //------------------------------------------HOD Charge create end -----------------------------------------------------------




    //------------------------------------------HOD Asssign charge start-----------------------------------------------------------

public static function fetch_deptfor_hodassigncharge()
    {
        $dept = UserManagementModel::deptfetch();

        return view('usermanagement.hod_assigncharge', compact('dept'));
    }


     public function fetchchargeforhodassigncharge(Request $request)
    {
        // dd($request->all());
        // Validate the input
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'auditeedeptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'desigcode' => ['required', 'string', 'regex:/^\d+$/'],

        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $deptcode = $request->input('deptcode');
        $auditeedeptcode = $request->input('auditeedeptcode');
        $desigcode = $request->input('desigcode');


        $charge = UserManagementModel::fetchchargeforhodassigncharge($deptcode,$auditeedeptcode,$desigcode);

        // dd($charge);
        if($charge->isNotEmpty()) {
            return response()->json($charge);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

 public function getCategoriesBasedOnDept(Request $request)
    {
        // Validate the input
        $request->validate([
            'deptcode' => ['required','regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $deptcode = $request->input('deptcode');


        $category = UserManagementModel::getcategoryByDept($deptcode);

        if($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

  public function getsubcategory(Request $request)
{
    $catcode = $request->input('catcode');

    // If single value convert to array
    if (!is_array($catcode)) {
        $catcode = $catcode ? [$catcode] : [];
    }
// dd($catcode);
    $subcategoryData = UserManagementModel::getSubcategoryByCategory($catcode);

    return response()->json([
        'success' => $subcategoryData->isNotEmpty(),
        'subcategoryData' => $subcategoryData
    ]);
}




    public function fetchuserforhodcharge(Request $request)
{
    $request->validate([
        'deptcode' => ['required', 'regex:/^\d+$/'],
        'auditeedeptcode' => ['required', 'regex:/^\d+$/'],
        'desigcode' => ['required', 'regex:/^\d+$/'],
    ]);

    $deptcode = $request->deptcode;
    $auditeedeptcode = $request->auditeedeptcode;
    $desigcode = $request->desigcode;

    // ✅ Handle multi-select safely
    $catcode = $request->input('category', []);
    $subcatid = $request->input('subcategory', []);
// dd($subcatid);

    // If single value convert to array
    if (!is_array($catcode)) {
        $catcode = [$catcode];
    }

    if (!is_array($subcatid)) {
        $subcatid = [$subcatid];
    }
    $user = UserManagementModel::fetchuserforhodassigcharge(
        $deptcode,
        $auditeedeptcode,
        $desigcode,
        $catcode,
        $subcatid
    );

    return response()->json([
        'success' => $user->isNotEmpty(),
        'user' => $user
    ]);
}





public function insertUpdatehodassigncharge(Request $request)
    {

        try {
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;
            $deptcodeInput = $request->input('deptcode');

            // $hideSubcategory = in_array($deptcodeInput, ['01', '05', '1', '5'], true);
            // $subcategoryRules = [$hideSubcategory ? 'nullable' : 'required', 'regex:/^\\d+$/'];
            $action = $request->input('action', 'insert');
            $userchargeid = null;
            if($action === 'update' && $request->filled('userchargeid')) {
                $userchargeid = Crypt::decryptString($request->input('userchargeid'));
            }

            $request->validate([

                'deptcode' => ['required', 'regex:/^\d+$/'],
                'auditeedeptcode' => ['required', 'regex:/^\d+$/'],
                 'desigcode' => ['required', 'regex:/^\d+$/'],


            ], [

                'deptcode.required' => 'Department is required.',
                'deptcode.regex' => 'Department must be numeric.',

                'auditeedeptcode.required' => 'auditeedeptcode is required.',
                'auditeedeptcode.regex' => 'auditeedeptcode must be numeric.',

                'desigcode.required' => 'desigcode is required.',
                'desigcode.regex' => 'desigcode must be numeric.',



            ]);


          $data = [
                'chargeid' => $request->input('chargeid'),
                'userid' => $request->input('userid'),
                'chargeflag' => 'P',
                 'statusflag' => 'Y',
                'chargefrom' => View::shared('get_nowtime'),
                'createdby' => $userid,
                'createdon' => View::shared('get_nowtime'),
                'updatedby' => $userid,
                'updatedon' => View::shared('get_nowtime'),
            ];

            if($action === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userid;
            }

            // if($fileuploadId) {
            //     $data['fileuploadid'] = $fileuploadId;
            // }
            // dd($data);
// $data =
            $chargedata = UserManagementModel::insertUpdatehodassigncharge($data, $userchargeid);
// dd($prtitledata);
            return response()->json([
                'success' => true,
                'data' => $chargedata,
                'message' => 'hodcharge_assig'
            ], 200);
        } catch (Exception $e) {
            $statusCode = $e->getCode();
            if(!is_int($statusCode) || $statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], $statusCode);
        }
    }


 public function fetchhodassigncharge(Request $request)
    {
        $userchargeid = $request->has('userchargeid') ? Crypt::decryptString($request->userchargeid) : null;
        $chargedel = UserManagementModel::fetchhodassigncharge($userchargeid, 'audit.hoduserchargedetails');
        // dd($chargedel);
        foreach($chargedel as $all) {
            if(isset($all->userchargeid)) {
                $all->encrypted_userchargeid = Crypt::encryptString($all->userchargeid);
            }
        }
        return response()->json([
            'success' => !$chargedel->isEmpty(),
            'message' => $chargedel->isEmpty() ? 'User not found' : '',
            'data' => $chargedel->isEmpty() ? null : $chargedel
        ], $chargedel->isEmpty() ? 404 : 200);
    }


}
