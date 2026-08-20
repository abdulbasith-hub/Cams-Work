<?php

namespace App\Http\Controllers;

use App\Http\Requests\MasterdesignationRequest;
use App\Models\BaseModel;
use App\Models\MastersModel;
use App\Models\UserManagementModel;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Exception;;

class MastersController extends Controller
{
    protected static $roletype = BaseModel::ROLETYPE;
    protected static $roletypemapping_table = BaseModel::ROLETYPEMAPPING_TABLE;
    protected static $department_table = BaseModel::DEPARTMENT_TABLE;
    protected static $region_table = BaseModel::REGION_TABLE;
    protected static $inst_table = BaseModel::INSTITUTION_TABLE;
    protected static $subcat_table = BaseModel::SUBCATEGORY_TABLE;
    protected static $category_table = BaseModel::SUBCATEGORY_TABLE;
    protected static $group_table = BaseModel::GROUP_TABLE;
    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;
    protected static $majorworkallocationtype_table = BaseModel::MAJORWORKALLOCATION_TABLE;
    protected static $callforrec_table = BaseModel::CALLFORRECORDS_AUDITEE_TABLE;
    protected static $mainobjection_table = BaseModel::MAINOBJECTION_TABLE;
    protected static $subobjection_table = BaseModel::SUBOBJ_TABLE;
    protected static $designation_table = BaseModel::DESIGNATION_TABLE;
    protected static $distTable = BaseModel::DIST_Table;

    // --------------------------------------------------------Irregularties Subcategory--------------------------------------------------------------------------------------------

    public function irregularitiescategoryfetch()
    {
        $irr = MastersModel::irregularitiesfetch();

        return view('masters.irregularitiessubcategory', compact('irr'));
    }

    public function getirrCategoriesBasedOnirr(Request $request)
    {
        // Validate the input
        $request->validate([
            'irregularitiescode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $irregularitiescode = $request->input('irregularitiescode');

        $category = MastersModel::getcategoryByIrr($irregularitiescode);

        if ($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function irregularitiessubcategory_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'irregularitiescode' => 'required|string|regex:/^\d+$/',
                'irregularitiescatcode' => 'required|string|regex:/^\d+$/',
                'irregularitiessubcatesname' => 'required|string|max:10',
                'irregularitiessubcatelname' => 'required|string|max:255',
                'irregularitiessubcattsname' => 'required|string|max:15',
                'irregularitiessubcattlname' => 'required|string|max:255',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $irregularitiessubcat = session('charge');
            if (!$irregularitiessubcat || !isset($irregularitiessubcat->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $irregularitiessubcat->userchargeid;
            $irregularitiessubcatid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('irregularitiessubcatid')) : null;

            $data = [
                'irregularitiescatcode' => $request->irregularitiescatcode ?? null,
                'irregularitiessubcatesname' => $request->irregularitiessubcatesname ?? null,
                'irregularitiessubcatelname' => $request->irregularitiessubcatelname ?? null,
                'irregularitiessubcattsname' => $request->irregularitiessubcattsname ?? null,
                'irregularitiessubcattlname' => $request->irregularitiessubcattlname ?? null,
                'statusflag' => $request->statusflag,
            ];
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::Forirregularitiessubcat_insertupdate($data, $irregularitiessubcatid, 'audit.mst_irregularitiessubcategory');
            return response()->json(['success' => true, 'message' => 'irregularitiessubcat_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function irregularitiessubcategory_fetchData(Request $request)
    {
        $irregularitiessubcatid = $request->has('irregularitiessubcatid') ? Crypt::decryptString($request->irregularitiessubcatid) : null;
        $irregularitiessubcat = MastersModel::irregularitiessubcat_fetch($irregularitiessubcatid, 'audit.mst_irregularitiessubcategory');

        foreach ($irregularitiessubcat as $all) {
            $all->encrypted_irregularitiessubcatid = Crypt::encryptString($all->irregularitiessubcatid);

            unset($all->irregularitiessubcatid);
        }
        return response()->json([
            'success' => true,
            'message' => $irregularitiessubcat->isEmpty() ? 'No schemes found' : '',
            'data' => $irregularitiessubcat
        ], 200);
    }

    // --------------------------------------------------------Irregularties Category-------------------------------------------------------------

    public function irregularitiesfetch()
    {
        $irr = MastersModel::irregularitiesfetch();

        return view('masters.irregularitiescategory', compact('irr'));
    }

    public function irregularitiescategory_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'irregularitiescode' => 'required|string|regex:/^\d+$/',
                'irregularitiescatesname' => 'required|string|max:10',
                'irregularitiescatelname' => 'required|string|max:100',
                'irregularitiescattsname' => 'required|string|max:15',
                'irregularitiescattlname' => 'required|string|max:100',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $irregularitiescat = session('charge');
            if (!$irregularitiescat || !isset($irregularitiescat->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $irregularitiescat->userchargeid;
            $irregularitiescatid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('irregularitiescatid')) : null;

            if ($request->input('action') === 'update' && $request->statusflag === 'N') {
                $parentCode = DB::table('audit.mst_irregularitiescategory')
                    ->where('irregularitiescatid', $irregularitiescatid)
                    ->value('irregularitiescatcode');

                $activeChildExists = DB::table('audit.mst_irregularitiessubcategory')
                    ->where('irregularitiescatcode', $parentCode)
                    ->where('statusflag', 'Y')
                    ->exists();

                if ($activeChildExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'childcatError'
                    ], 422);
                }
            }

            $data = [
                'irregularitiescode' => $request->irregularitiescode ?? null,
                'irregularitiescatesname' => $request->irregularitiescatesname ?? null,
                'irregularitiescatelname' => $request->irregularitiescatelname ?? null,
                'irregularitiescattsname' => $request->irregularitiescattsname ?? null,
                'irregularitiescattlname' => $request->irregularitiescattlname ?? null,
                'statusflag' => $request->statusflag,
            ];
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::Forirregularitiescat_insertupdate($data, $irregularitiescatid, 'audit.mst_irregularitiescategory');
            return response()->json(['success' => true, 'message' => 'irregularitiescat_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function irregularitiescategory_fetchData(Request $request)
    {
        $irregularitiescatid = $request->has('irregularitiescatid') ? Crypt::decryptString($request->irregularitiescatid) : null;
        $irregularitiescat = MastersModel::irregularitiescat_fetch($irregularitiescatid, 'audit.mst_irregularitiescategory');

        foreach ($irregularitiescat as $all) {
            $all->encrypted_irregularitiescatid = Crypt::encryptString($all->irregularitiescatid);

            unset($all->irregularitiescatid);
        }
        return response()->json([
            'success' => true,
            'message' => $irregularitiescat->isEmpty() ? 'No schemes found' : '',
            'data' => $irregularitiescat
        ], 200);
    }

    // --------------------------------------------------------Irregularities---------------------------------------------------------------------

    public function irregularities_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'irregularitiesesname' => 'required|string|max:2',
                'irregularitieselname' => 'required|string|max:30',
                'irregularitiestsname' => 'required|string|max:5',
                'irregularitiestlname' => 'required|string|max:30',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $irregularities = session('charge');
            if (!$irregularities || !isset($irregularities->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $irregularities->userchargeid;
            $irregularitiesid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('irregularitiesid')) : null;

            if ($request->input('action') === 'update' && $request->statusflag === 'N') {
                $parentCode = DB::table('audit.mst_irregularities')
                    ->where('irregularitiesid', $irregularitiesid)
                    ->value('irregularitiescode');

                $activeChildExists = DB::table('audit.mst_irregularitiescategory')
                    ->where('irregularitiescode', $parentCode)
                    ->where('statusflag', 'Y')
                    ->exists();

                if ($activeChildExists) {
                    return response()->json([
                        'success' => false,
                        'message' => 'childirrError'
                    ], 422);
                }
            }

            $data = [
                'irregularitiesesname' => $request->irregularitiesesname ?? null,
                'irregularitieselname' => $request->irregularitieselname ?? null,
                'irregularitiestsname' => $request->irregularitiestsname ?? null,
                'irregularitiestlname' => $request->irregularitiestlname ?? null,
                'statusflag' => $request->statusflag,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::Forirregularities_insertupdate($data, $irregularitiesid, 'audit.mst_irregularities');
            return response()->json(['success' => true, 'message' => 'irregularitiest_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function irregularities_fetchData(Request $request)
    {
        $irregularitiesid = $request->has('irregularitiesid') ? Crypt::decryptString($request->irregularitiesid) : null;
        $irregularities = MastersModel::irregularities_fetch($irregularitiesid, 'audit.mst_irregularities');

        foreach ($irregularities as $all) {
            $all->encrypted_irregularitiesid = Crypt::encryptString($all->irregularitiesid);

            unset($all->irregularitiesid);
        }
        return response()->json([
            'success' => true,
            'message' => $irregularities->isEmpty() ? 'No schemes found' : '',
            'data' => $irregularities
        ], 200);
    }

    // ---------------------------------------------------Auditee Department--------------------------------------------------------------

    public static function fetchdeptforauditeedepartment()
    {
        $dept = MastersModel::commondeptfetch();

        return view('masters.auditeedepartment', compact('dept'));
    }

    public function auditeedepartment_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'auditeedeptename' => 'required|string|max:200',
                'auditeedepttname' => 'required|string|max:200',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $auditeedept = session('charge');
            if (!$auditeedept || !isset($auditeedept->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $auditeedept->userchargeid;
            $auditeedeptid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('auditeedeptid')) : null;

            $data = [
                'deptcode' => $request->deptcode ?? null,
                'auditeedeptename' => $request->auditeedeptename ?? null,
                'auditeedepttname' => $request->auditeedepttname ?? null,
                'statusflag' => $request->statusflag,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::Forauditeedept_insertupdate($data, $auditeedeptid, 'audit.mst_auditeedept');
            return response()->json(['success' => true, 'message' => 'auditeedept_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function auditeedepartment_fetchData(Request $request)
    {
        $auditeedeptid = $request->has('auditeedeptid') ? Crypt::decryptString($request->auditeedeptid) : null;
        $auditeedept = MastersModel::auditeedepartment_fetch($auditeedeptid, 'audit.mst_auditeedept');

        foreach ($auditeedept as $all) {
            $all->encrypted_auditeedeptid = Crypt::encryptString($all->auditeedeptid);

            unset($all->auditeedeptid);
        }
        return response()->json([
            'success' => true,
            'message' => $auditeedept->isEmpty() ? 'No schemes found' : '',
            'data' => $auditeedept
        ], 200);
    }

    // -------------------------------------------Scheme--------------------------------------------------------------------

    public function scheme_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'category' => 'required|string|regex:/^\d+$/',
                'subcategory' => 'nullable|integer|',  // Allowing null
                'auditeeschemeesname' => 'required|string|max:50',
                'auditeeschemeelname' => 'required|string|max:400',
                'auditeeschemetsname' => 'required|string|max:50',
                'auditeeschemetlname' => 'required|string|max:400',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $auditeescheme = session('charge');
            if (!$auditeescheme || !isset($auditeescheme->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $auditeescheme->userchargeid;
            $auditeeschemeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('auditeeschemeid')) : null;

            $data = [
                'deptcode' => $request->deptcode ?? null,
                'catcode' => $request->category ?? null,
                'auditeeins_subcategoryid' => $request->subcategory ?? null,
                'auditeeschemeesname' => $request->auditeeschemeesname ?? null,
                'auditeeschemeelname' => $request->auditeeschemeelname ?? null,
                'auditeeschemetsname' => $request->auditeeschemetsname ?? null,
                'auditeeschemetlname' => $request->auditeeschemetlname ?? null,
                'statusflag' => $request->statusflag,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::Forscheme_insertupdate($data, $auditeeschemeid, 'audit.auditeescheme');
            return response()->json(['success' => true, 'message' => 'scheme_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public static function fetchdeptforscheme()
    {
        $dept = MastersModel::commondeptfetch();

        return view('masters.createscheme', compact('dept'));
    }

    public function getCategoriesBasedOnDeptforscheme(Request $request)
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

        $category = MastersModel::getcategoryByDeptsupercheck($deptcode);

        if ($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No Category found'], 404);
        }
    }

    public function getsubcatbasedoncategoryscheme(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');

        $subcategory = MastersModel::getSubcategoryByCategoryforsupercheck($category);

        return response()->json($subcategory);
    }

    public function scheme_fetchData(Request $request)
    {
        $auditeeschemeid = $request->has('auditeeschemeid') ? Crypt::decryptString($request->auditeeschemeid) : null;
        $auditeescheme = MastersModel::scheme_fetch($auditeeschemeid, 'audit.auditeescheme');

        foreach ($auditeescheme as $all) {
            $all->encrypted_auditeeschemeid = Crypt::encryptString($all->auditeeschemeid);

            unset($all->auditeeschemeid);
        }
        return response()->json([
            'success' => true,
            'message' => $auditeescheme->isEmpty() ? 'No schemes found' : '',
            'data' => $auditeescheme
        ], 200);
    }

    // ------------------------------------------------------Super Check List---------------------------------------------------

    public static function fetchdeptforsupercheck($index)
    {
        $dept = MastersModel::commondeptfetch();

        return view($index, compact('dept'));
    }

    public function supercheck_multiinsert(Request $request)
    {
        try {
            // $rules = [
            //     'deptcode' => 'required|string|regex:/^\d+$/',
            //     'category' => 'required|string|regex:/^\d+$/',
            //     'subcategory' => 'nullable|integer|', // Allowing null
            //     'heading_en' => 'required|string|max:300',
            //     'heading_ta' => 'required|string|max:400',
            //     'part_no' => 'required|integer',
            //     'sl_no' => 'required|integer',
            //     'checkpoint_en' => 'required|string|max:300',
            //     'checkpoint_ta' =>  'required|string|max:400',
            //     'question_type' =>  'required|string|max:1',
            //     'statusflag' => 'required|in:Y,N',
            // ];

            // $validator = Validator::make($request->all(), $rules);

            // // If validation fails, throw an exception with a single message
            // if ($validator->fails()) {
            //     throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            // }

            $supercheck = session('charge');
            if (!$supercheck || !isset($supercheck->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $supercheck->userchargeid;
            $supercheckid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('supercheckid')) : null;

            $data = [
                'deptcode' => $request->deptcode ?? null,
                'catcode' => $request->category ?? null,
                'subcatcode' => $request->subcategory ?? null,
                'heading_en' => $request->heading_en ?? null,
                'heading_ta' => $request->heading_ta ?? null,
                'part_no' => $request->part_no ?? null,
                'sl_no' => $request->sl_no ?? null,
                'checkpoint_en' => $request->checkpoint_en ?? null,
                'checkpoint_ta' => $request->checkpoint_ta ?? null,
                'question_type' => $request->question_type ?? null,
                'statusflag' => $request->statusflag,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::supercheck_multiinsert($data, $supercheckid, 'audit.super_check');
            return response()->json(['success' => true, 'message' => 'supercheck_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function getCategoriesBasedOnDeptforsupercheck(Request $request)
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

        $category = MastersModel::getcategoryByDeptsupercheck($deptcode);

        if ($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No Category found'], 404);
        }
    }

    public function getsubcatbasedoncategory(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');

        $subcategory = MastersModel::getSubcategoryByCategoryforsupercheck($category);

        return response()->json($subcategory);
    }

    public function supercheck_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'category' => 'required|string|regex:/^\d+$/',
                'subcategory' => 'nullable|integer|',  // Allowing null
                'heading_en' => 'required|string|max:300',
                'heading_ta' => 'required|string|max:400',
                'part_no' => 'required|integer',
                'sl_no' => 'required|integer',
                'checkpoint_en' => 'required|string|max:300',
                'checkpoint_ta' => 'required|string|max:400',
                'question_type' => 'required|string|max:1',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $supercheck = session('charge');
            if (!$supercheck || !isset($supercheck->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $supercheck->userchargeid;
            $supercheckid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('supercheckid')) : null;

            $data = [
                'deptcode' => $request->deptcode ?? null,
                'catcode' => $request->category ?? null,
                'subcatcode' => $request->subcategory ?? null,
                'heading_en' => $request->heading_en ?? null,
                'heading_ta' => $request->heading_ta ?? null,
                'part_no' => $request->part_no ?? null,
                'sl_no' => $request->sl_no ?? null,
                'checkpoint_en' => $request->checkpoint_en ?? null,
                'checkpoint_ta' => $request->checkpoint_ta ?? null,
                'question_type' => $request->question_type ?? null,
                'statusflag' => $request->statusflag,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::Forsupercheck_insertupdate($data, $supercheckid, 'audit.super_check');
            return response()->json(['success' => true, 'message' => 'supercheck_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function supercheck_fetchData(Request $request)
    {
        $supercheckid = $request->has('supercheckid') ? Crypt::decryptString($request->supercheckid) : null;
        $supercheck = MastersModel::supercheck_fetch($supercheckid, 'audit.super_check');
        // print_r($workallocation);
        foreach ($supercheck as $all) {
            $all->encrypted_supercheckid = Crypt::encryptString($all->supercheckid);

            unset($all->supercheckid);
        }
        return response()->json([
            'success' => !$supercheck->isEmpty(),
            'message' => $supercheck->isEmpty() ? 'Super Check not found' : '',
            'data' => $supercheck->isEmpty() ? null : $supercheck
        ], $supercheck->isEmpty() ? 404 : 200);
    }

    // -----------------------------------------------------Role Action--------------------------------------------------------------------------

    public function roleaction_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'roleactionesname' => 'required|string|max:10',
                'roleactionelname' => 'required|string|max:100',
                'roleactiontsname' => 'required|string|max:20',
                'roleactiontlname' => 'required|string|max:100',
                'statusflag' => 'required|in:Y,N',
            ];

            // $validator = Validator::make($request->all(), $rules);

            // if ($validator->fails()) {
            //     throw ValidationException::withMessages(['message' => 'Unauthorized','error' => 401]);
            // }

            $auditquarter = session('charge');
            if (!$auditquarter || !isset($auditquarter->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $auditquarter->userchargeid;
            $roleactionid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('roleactionid')) : null;

            $data = [
                // 'majorworkallocationtypeid' => $request->subworkid ?? null,
                'roleactionesname' => $request->roleactionesname ?? null,
                'roleactionelname' => $request->roleactionelname ?? null,
                'roleactiontsname' => $request->roleactiontsname ?? null,
                'roleactiontlname' => $request->roleactiontlname ?? null,
                'statusflag' => $request->statusflag,
            ];

            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::roleaction_insertupdate($data, $roleactionid, 'audit.mst_roleaction');
            return response()->json(['success' => true, 'message' => 'roleaction_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function roleaction_fetchData(Request $request)
    {
        $roleactionid = $request->has('roleactionid') ? Crypt::decryptString($request->roleactionid) : null;
        // dd($majorworkallocationtypeid);
        $roleaction = MastersModel::roleaction_fetch($roleactionid, 'audit.mst_auditquarter');
        // print_r($workallocation);
        foreach ($roleaction as $all) {
            $all->encrypted_roleactionid = Crypt::encryptString($all->roleactionid);
            // dd($majorworkallocationtypeid);

            unset($all->roleactionid);
        }
        return response()->json([
            'success' => !$roleaction->isEmpty(),
            'message' => $roleaction->isEmpty() ? 'User not found' : '',
            'data' => $roleaction->isEmpty() ? null : $roleaction
        ], $roleaction->isEmpty() ? 404 : 200);
    }

    // -------------------------------------------------------Audit Quarter-----------------------------------------
    public static function deptfetchforauditquarter()
    {
        $dept = MastersModel::commondeptfetch();

        return view('masters.createauditquarter', compact('dept'));
    }

    public function auditquarter_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'auditquarter' => 'required|string|max:100',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $auditquarter = session('charge');
            if (!$auditquarter || !isset($auditquarter->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $auditquarter->userchargeid;
            $auditquarterid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('auditquarterid')) : null;

            $data = [
                // 'majorworkallocationtypeid' => $request->subworkid ?? null,
                'deptcode' => $request->deptcode ?? null,
                'auditquarter' => $request->auditquarter ?? null,
                'statusflag' => $request->statusflag,
            ];

            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::auditquarter_insertupdate($data, $auditquarterid, 'audit.mst_auditquarter');
            return response()->json(['success' => true, 'message' => 'audit_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function auditquarter_fetchData(Request $request)
    {
        $auditquarterid = $request->has('auditquarterid') ? Crypt::decryptString($request->auditquarterid) : null;
        // dd($majorworkallocationtypeid);
        $auditquarter = MastersModel::audit_fetch($auditquarterid, 'audit.mst_auditquarter');
        // print_r($workallocation);
        foreach ($auditquarter as $all) {
            $all->encrypted_auditquarterid = Crypt::encryptString($all->auditquarterid);
            // dd($majorworkallocationtypeid);

            unset($all->auditquarterid);
        }
        return response()->json([
            'success' => !$auditquarter->isEmpty(),
            'message' => $auditquarter->isEmpty() ? 'User not found' : '',
            'data' => $auditquarter->isEmpty() ? null : $auditquarter
        ], $auditquarter->isEmpty() ? 404 : 200);
    }

    // ---------------------------------------Account Particular----------------------------------------------------------------------------

    public static function fetchdeptforaccounts()
    {
        $dept = MastersModel::Modaldeptfetch();
        //  $records = MastersModel::map_callforrecordsfetch();

        return view('masters.createaccountparticulars', compact('dept'));
    }

    public function accountparticular_fetchdata(Request $request)
    {
        $accountparticularsid = $request->has('accountparticularsid') ? Crypt::decryptString($request->accountparticularsid) : null;
        // dd($majorworkallocationtypeid);
        $accountparticular = MastersModel::accountparticulardatafetch($accountparticularsid, 'audit.mst_accountparticulars');
        // print_r($workallocation);
        foreach ($accountparticular as $all) {
            $all->encrypted_accountparticularsid = Crypt::encryptString($all->accountparticularsid);
            // dd($majorworkallocationtypeid);

            unset($all->accountparticularsid);
        }
        return response()->json([
            'success' => !$accountparticular->isEmpty(),
            'message' => $accountparticular->isEmpty() ? 'User not found' : '',
            'data' => $accountparticular->isEmpty() ? null : $accountparticular
        ], $accountparticular->isEmpty() ? 404 : 200);
    }

    public function getaccountcategoriesbasednndept(Request $request)
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

        $category = MastersModel::getcategoryByDept($deptcode);

        if ($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No Category found'], 404);
        }
    }

    public function getsubCategoriesBasedOncategory(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $catcode = $request->input('category');

        $subcategory = MastersModel::getsubCategoriesBasedOncategory($catcode);

        return response()->json([
            'success' => $subcategory->isNotEmpty(),
            'data' => $subcategory,
            'message' => $subcategory->isNotEmpty() ? '' : 'No Sub Category found'
        ], 200);  // Always return HTTP 200 OK
    }

    public function accountparticular_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'category' => 'required|string|regex:/^\d+$/',
                'subcategory' => 'nullable|required_if:category,!null|string|regex:/^\d+$/',
                'accountename' => 'required|string|max:50',
                'accounttname' => 'required|string|max:50',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $accountparticular = session('charge');
            if (!$accountparticular || !isset($accountparticular->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $accountparticular->userchargeid;
            $accountparticularsid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('accountparticularsid')) : null;

            $data = [
                // 'majorworkallocationtypeid' => $request->subworkid ?? null,
                'deptcode' => $request->deptcode ?? null,
                'catcode' => $request->category ?? null,
                'accountparticularsename' => $request->accountename ?? null,
                'accountparticularstname' => $request->accounttname ?? null,
                'auditeeins_subcategoryid' => $request->subcategory ?? null,
                'statusflag' => $request->statusflag,
            ];

            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::accountparticulars_insertupdate($data, $accountparticularsid, 'audit.mst_accountparticulars');
            return response()->json(['success' => true, 'message' => 'accountparticulars_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    // ---------------------------------------------------------------Role Type Mapping--------------------------------------------------------------

    public static function DeptandRoletypeFetchForRoletype()
    {
        $roletype = MastersModel::Roletypefetchdata();
        $dept = MastersModel::ForAuditmodalDeptfetch();

        return view('masters.createroletypemapping', compact('dept', 'roletype'));
    }

    public function roletypemapping_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'roletypecode' => 'required|string|regex:/^\d+$/',
                'reportto' => 'required|string|regex:/^\d+$/',
                'levelid' => 'required|integer',
                'statusflag' => 'required|in:Y,N',
            ];

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            // , [
            //     'distsname.required' => 'The District Short Name is required.',
            //     'distename.required' => 'The District English Name is required.',
            //     'disttname.required' => 'The District Tamil Name is required.',
            //     'districtcode.required' => 'The District Code is required.',
            //     'statecode.required' => 'The State is required.',

            //     'status.required' => 'The Status field is required.',
            //     'status.in' => 'The Status must be either "Y" or "N".',

            // ]);
            $roletypemapping = session('charge');
            if (!$roletypemapping || !isset($roletypemapping->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $roletypemapping->userchargeid;
            $roletypemappingid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('roletypemappingid')) : null;

            $data = [
                // 'majorworkallocationtypeid' => $request->subworkid ?? null,
                'deptcode' => $request->deptcode ?? null,
                'roletypecode' => $request->roletypecode ?? null,
                'parentcode' => $request->reportto ?? null,
                'levelid' => $request->levelid ?? null,
                'statusflag' => $request->statusflag,
            ];

            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::rolemapping_insertupdate($data, $roletypemappingid, 'audit.roletypemapping');
            return response()->json(['success' => true, 'message' => 'roletypemapping_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function roletypemapping_fetchData(Request $request)
    {
        $roletypemappingid = $request->has('roletypemappingid') ? Crypt::decryptString($request->roletypemappingid) : null;
        // dd($roletypemappingid);
        $roletypemap = MastersModel::getAllRoletypemappingmapping($roletypemappingid, 'audit.roletypemapping');
        // dd($roletypemappingid);
        foreach ($roletypemap as $all) {
            $all->encrypted_roletypemappingid = Crypt::encryptString($all->roletypemappingid);
            // dd($majorworkallocationtypeid);

            unset($all->roletypemappingid);
        }
        return response()->json([
            'success' => !$roletypemap->isEmpty(),
            'message' => $roletypemap->isEmpty() ? 'User not found' : '',
            'data' => $roletypemap->isEmpty() ? null : $roletypemap
        ], $roletypemap->isEmpty() ? 404 : 200);
    }

    // -----------------------------------------------------------------District------------------------------------------------------

    public function district_fetchData(Request $request)
    {
        $distid = $request->has('distid') ? Crypt::decryptString($request->distid) : null;
        // dd($majorworkallocationtypeid);
        $district = MastersModel::fetchdistrictData($distid, 'audit.mst_district');
        // print_r($workallocation);
        foreach ($district as $all) {
            $all->encrypted_distid = Crypt::encryptString($all->distid);
            // dd($majorworkallocationtypeid);

            unset($all->distid);
        }
        return response()->json([
            'success' => !$district->isEmpty(),
            'message' => $district->isEmpty() ? 'User not found' : '',
            'data' => $district->isEmpty() ? null : $district
        ], $district->isEmpty() ? 404 : 200);
    }

    public static function masterstatefetch()
    {
        $state = MastersModel::statefetch();

        return view('masters.createdistrict', compact('state'));
    }

    public function district_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'distsname' => 'required|string|max:5',
                'distename' => 'required|string|max:50',
                'disttname' => 'required|string|max:50',
                'distcode' => 'required|string|max:3',
                'statecode' => 'required|string|max:2',
                'statusflag' => 'required|in:Y,N',
            ];

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            // , [
            //     'distsname.required' => 'The District Short Name is required.',
            //     'distename.required' => 'The District English Name is required.',
            //     'disttname.required' => 'The District Tamil Name is required.',
            //     'districtcode.required' => 'The District Code is required.',
            //     'statecode.required' => 'The State is required.',

            //     'status.required' => 'The Status field is required.',
            //     'status.in' => 'The Status must be either "Y" or "N".',

            // ]);
            $district = session('charge');
            if (!$district || !isset($district->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $district->userchargeid;
            $distid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('distid')) : null;

            $data = [
                // 'majorworkallocationtypeid' => $request->subworkid ?? null,
                'distsname' => $request->distsname ?? null,
                'distename' => $request->distename ?? null,
                'disttname' => $request->disttname ?? null,
                'distcode' => $request->distcode ?? null,
                'statecode' => $request->statecode ?? null,
                'statusflag' => $request->statusflag,
            ];

            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::district_insertupdate($data, $distid, 'audit.mst_district');
            return response()->json(['success' => true, 'message' => 'district_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    // ---------------------------------------------------------------Department-------------------------------------------------------

    public function department_insertupdate(Request $request)
    {
        // print_r($_REQUEST);
        try {
            $rules = [
                'deptesname' => 'required|string|max:10',
                'deptelname' => 'required|string|max:100',
                'depttsname' => 'required|string|max:10',
                'depttlname' => 'required|string|max:100',
                'orderid' => 'required|integer',
                'levelid' => 'required|integer',
                'financialyear' => 'required|string|max:10',
                'authority' => 'required|string|max:2',
                'rejoinder' => 'required|integer',
                'membercount' => 'required|integer',
                'esculationdate' => 'required|integer',
                'fileuploadcount' => 'required|integer',
                'paraauthority' => 'required|string|max:10',
                'exitmeeting' => 'required|integer',
                'maximumleave' => 'required|integer',
                'liabilitycount' => 'required|integer',
                'auditee_ofcusercount' => 'required|integer',
                'statusflag' => 'required|in:Y,N',
            ];
            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            if ($validator->fails()) {
                throw ValidationException::withMessages([
                    'message' => $validator->errors()->first(),  // Show the first error message
                    'error' => 422,  // Use 422 for validation errors
                ]);
            }

            $dept = session('charge');
            if (!$dept || !isset($dept->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $dept->userchargeid;
            $deptid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('deptid')) : null;

            $data = [
                // 'majorworkallocationtypeid' => $request->subworkid ?? null,
                'deptesname' => $request->deptesname ?? null,
                'deptelname' => $request->deptelname ?? null,
                'depttsname' => $request->depttsname ?? null,
                'depttlname' => $request->depttlname ?? null,
                'orderid' => $request->orderid ?? null,
                'levelid' => $request->levelid ?? null,
                'financialyear' => $request->financialyear ?? null,
                'authority' => $request->authority ?? null,
                'rejoinderlimit' => $request->rejoinder ?? null,
                'membercount' => $request->membercount ?? null,
                'esculationdate' => $request->esculationdate ?? null,
                'fileuploadcount' => $request->fileuploadcount ?? null,
                'paraauthority' => $request->paraauthority ?? null,
                'exitmeetingdate' => $request->exitmeeting ?? null,
                'maxleave' => $request->maximumleave ?? null,
                'liabilitycount' => $request->liabilitycount ?? null,
                'auditee_ofcusercount' => $request->auditee_ofcusercount ?? null,
                'statusflag' => $request->statusflag,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            // dd($data);

            $result = MastersModel::department_insertupdate($data, $deptid, 'audit.mst_dept');
            return response()->json(['success' => true, 'message' => 'department_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function department_fetchData(Request $request)
    {
        $deptid = $request->has('deptid') ? Crypt::decryptString($request->deptid) : null;
        // dd($majorworkallocationtypeid);
        $department = MastersModel::fetchdepartmentData($deptid, 'audit.mst_dept');
        // print_r($workallocation);
        foreach ($department as $all) {
            $all->encrypted_deptid = Crypt::encryptString($all->deptid);
            // dd($majorworkallocationtypeid);

            unset($all->deptid);
        }
        return response()->json([
            'success' => !$department->isEmpty(),
            'message' => $department->isEmpty() ? 'User not found' : '',
            'data' => $department->isEmpty() ? null : $department
        ], $department->isEmpty() ? 404 : 200);
    }

    // catch (ValidationException $e) {
    //     return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
    // }
    // catch (\Exception $e) {
    //     return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    // }
    // }

    // //---------------------------------------------------------------Department-----------------------------------------------------------------------------------------

    // public function department_insertupdate(Request  $request)
    // {
    //     // print_r($_REQUEST);
    // try {
    //     $rules = [
    //         'deptesname' => 'required|string|max:10',
    //         'deptelname'  => 'required|string|max:100',
    //         'depttsname' => 'required|string|max:10',
    //         'depttlname' => 'required|string|max:100',
    //         'orderid' => 'required|integer',
    //         'levelid' => 'required|integer',
    //         'financialyear' => 'required|string|max:10',
    //         'authority'  => 'required|string|max:2',
    //         'rejoinder' => 'required|integer',
    //         'membercount' => 'required|integer',
    //         'esculationdate' => 'required|integer',
    //         'fileuploadcount'  => 'required|integer',
    //         'paraauthority' => 'required|string|max:10',
    //         'exitmeeting' => 'required|integer',
    //         'maximumleave'  => 'required|integer',
    //         'liabilitycount' => 'required|integer',
    //         'statusflag' => 'required|in:Y,N',

    //     ];
    //         // Create a validator instance
    //         $validator = Validator::make($request->all(), $rules);

    //         // If validation fails, throw an exception with a single message
    //         if ($validator->fails()) {
    //             throw ValidationException::withMessages(['message' => 'Unauthorized','error' => 401]);
    //         }
    //     // , [
    //     //    'deptesname.required' => 'The Department English Short Name is required.',
    //     //     'deptelname.required' => 'The Department English Long Name is required.',
    //     //     'depttsname.required' => 'The Department Tamil Short Name is required.',
    //     //     'depttlname.required' => 'The Department Tamil Long Name is required.',
    //     //     'orderid.required' => 'The Order field is required.',
    //     //     'levelid.required' => 'The Level field is required.',
    //     //     'financialyear.required' => 'The Financial Year field is required.',
    //     //     'authority.required' => 'The Authority field is required.',
    //     //     'rejoinder.required' => 'The Rejoinder field is required.',
    //     //     'membercount.required' => 'The Member Count field is required.',
    //     //     'esculationdate.required' => 'The Escalation Date field is required.',
    //     //     'fileuploadcount.required' => 'The File Upload Count field is required.',
    //     //     'paraauthority.required' => 'The Para Authority field is required.',
    //     //     'exitmeeting.required' => 'The Exit Meeting field is required.',
    //     //     'maximumleave.required' => 'The Maximum Leave field is required.',
    //     //     'liabilitycount.required' => 'The Liability Count field is required.',
    //     //     'status.required' => 'The Status field is required.',
    //     //     'status.in' => 'The Status must be either "Y" or "N".',

    //     // ]);
    //     $dept = session('charge');
    //     if (!$dept || !isset($dept->userchargeid)) {
    //         return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
    //     }
    //     $userchargeid = $dept->userchargeid;
    //     $deptid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('deptid')) : null;

    //     $data =  [
    //         //'majorworkallocationtypeid' => $request->subworkid ?? null,
    //         'deptesname' => $request->deptesname ?? null,
    //         'deptelname' => $request->deptelname ?? null,
    //         'depttsname'   => $request->depttsname ?? null,
    //         'depttlname' => $request->depttlname ?? null,
    //         'orderid' => $request->orderid ?? null,
    //         'levelid' => $request->levelid ?? null,
    //         'financialyear' => $request->financialyear ?? null,
    //         'authority' => $request->authority ?? null,
    //         'rejoinderlimit'   => $request->rejoinder ?? null,
    //         'membercount' => $request->membercount ?? null,
    //         'esculationdate' => $request->esculationdate ?? null,
    //         'fileuploadcount' => $request->fileuploadcount ?? null,
    //         'paraauthority' => $request->paraauthority ?? null,
    //         'exitmeetingdate'   => $request->exitmeeting ?? null,
    //         'maxleave' => $request->maximumleave ?? null,
    //         'liabilitycount' => $request->liabilitycount ?? null,
    //         'statusflag' => $request->statusflag,

    //     ];
    //     //print_r($data);
    //     if ($request->input('action') === 'insert') {
    //         $data['createdon'] = View::shared('get_nowtime');
    //         $data['createdby'] =  $userchargeid;
    //     }
    //     if ($request->input('action') === 'update') {
    //         $data['updatedon'] = View::shared('get_nowtime');
    //         $data['updatedby'] =  $userchargeid;
    //     }
    //     // dd($data);

    //     $result = MastersModel::department_insertupdate($data, $deptid, 'audit.mst_dept');
    //     return response()->json(['success' => true, 'message' => 'department_success']);

    //     }

    //     catch (ValidationException $e) {
    //         return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
    //     }
    //      catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    //     }
    // }

    // public function department_fetchData(Request $request)
    // {
    //     $deptid = $request->has('deptid') ? Crypt::decryptString($request->deptid) : null;
    //     //dd($majorworkallocationtypeid);
    //     $department = MastersModel::fetchdepartmentData($deptid, 'audit.mst_dept');
    //     // print_r($workallocation);
    //     foreach ($department as $all) {
    //         $all->encrypted_deptid = Crypt::encryptString($all->deptid);
    //         // dd($majorworkallocationtypeid);

    //         unset($all->deptid);
    //     }
    //     return response()->json([
    //         'success' => !$department->isEmpty(),
    //         'message' => $department->isEmpty() ? 'User not found' : '',
    //         'data' => $department->isEmpty() ? null : $department
    //     ], $department->isEmpty() ? 404 : 200);
    // }

    // -------------------------------------------------------Audit District-------------------------------------------------------------------
    public static function auditdistrictdeptfetch()
    {
        $dept = MastersModel::model_workallocationdeptfetch();

        return view('masters.createauditdistrict', compact('dept'));
    }

    public function auditdistrict_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        $validatedData = $request->validate([
            'deptcode' => 'required|string|regex:/^\d+$/',
            'auditdistsname' => 'required|string|max:3',
            'auditdistename' => 'required|string|max:50',
            'auditdisttname' => 'required|string|max:50',
            'status' => 'required|in:Y,N',
        ], [
            // 'subworkid.required' => 'The Subwork allocation field is required.',
            'deptcode,required' => 'The Department field is required.',
            // 'orderid.required' => 'The Order field is required.',
            'auditdistename.required' => 'The Audit District English Name is required.',
            'auditdisttname.required' => 'The Audit District Tamil Name is required.',
            'auditdistsname.required' => 'The Audit District Short Name is required.',
            'status.required' => 'The Status field is required.',
            'status.in' => 'The Status must be either "Y" or "N".',
        ]);
        $auditdistrict = session('charge');
        if (!$auditdistrict || !isset($auditdistrict->userchargeid)) {
            return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
        }
        $userchargeid = $auditdistrict->userchargeid;
        $auditdistid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('auditdistid')) : null;

        // $deptcode = $request->deptcode;
        // $enameFormatted = strtolower(str_replace(' ', '', trim($request->categoryename)));
        // $fnameFormatted = strtolower(str_replace(' ', '', trim($request->categorytname)));

        // $existing = MastersModel::checkExistingCategory($enameFormatted, $fnameFormatted, $deptcode, $auditeeins_categoryid);

        // if ($existing['englishExists'] && $existing['tamilExists']) {
        //     return response()->json(['success' => false, 'message' => 'DuplicationcategoryETname'], 422);
        // } elseif ($existing['englishExists']) {
        //     return response()->json(['success' => false, 'message' => 'DuplicationcategoryEname'], 422);
        // } elseif ($existing['tamilExists']) {
        //     return response()->json(['success' => false, 'message' => 'DuplicationcategoryTname'], 422);
        // }

        if ($request->input('action') === 'update') {
            if (!$request->filled('auditdistcode')) {
                // Retrieve existing catcode if not provided
                $auditdistcode = DB::table('audit.mst_auditdistrict')
                    ->where('auditdistid', $auditdistid)
                    ->value('auditdistcode');
            } else {
                $auditdistcode = $request->input('auditdistcode');
            }
        } else {
            // Generate a new catcode for insert
            $auditdistcode = MastersModel::auditdistcodeinsert();
        }

        $data = [
            // 'majorworkallocationtypeid' => $request->subworkid ?? null,
            'auditdeptcode' => $request->deptcode ?? null,
            'auditdistcode' => $auditdistcode,
            'auditdistsname' => $request->auditdistsname ?? null,
            'auditdistename' => $request->auditdistename ?? null,
            'auditdisttname' => $request->auditdisttname ?? null,
            'statusflag' => $request->status,
        ];
        $data['statecode'] = View::shared('statecode');

        // print_r($data);
        if ($request->input('action') === 'insert') {
            $data['createdon'] = View::shared('get_nowtime');
            $data['createdby'] = $userchargeid;
        }
        if ($request->input('action') === 'update') {
            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $userchargeid;
        }
        // dd($data);
        try {
            $result = MastersModel::auditdistrict_insertupdate($data, $auditdistid, 'audit.mst_auditdistrict');
            return response()->json(['success' => true, 'message' => 'auditdistrict_success']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function auditdistrict_fetchData(Request $request)
    {
        $auditdistid = $request->has('auditdistid') ? Crypt::decryptString($request->auditdistid) : null;
        // dd($majorworkallocationtypeid);
        $auditdist = MastersModel::fetchauditdistrictData($auditdistid, 'audit.mst_auditdistrict');
        // print_r($workallocation);
        foreach ($auditdist as $all) {
            $all->encrypted_auditdistid = Crypt::encryptString($all->auditdistid);
            // dd($majorworkallocationtypeid);

            unset($all->auditdistidid);
        }
        return response()->json([
            'success' => !$auditdist->isEmpty(),
            'message' => $auditdist->isEmpty() ? 'User not found' : '',
            'data' => $auditdist->isEmpty() ? null : $auditdist
        ], $auditdist->isEmpty() ? 404 : 200);
    }

    // --------------------------------------------------------------Audit Period------------------------------------------------------------

    public function auditperiod_insertupdate(Request $request)
    {
        $validatedData = $request->validate([
            'fromYearDropdown' => 'required|digits:4|integer',
            'toYearDropdown' => 'required|digits:4|integer',
            'status' => 'required|in:Y,N',
        ], [
            'fromYearDropdown.required' => 'The from year is required.',
            'fromYearDropdown.digits' => 'The from year must be a 4-digit number.',
            'toYearDropdown.required' => 'The to year is required.',
            'toYearDropdown.digits' => 'The to year must be a 4-digit number.',
            'status.required' => 'The Status Flag field is required.',
            'status.in' => 'The Status Flag must be either "Y" or "N".',
        ]);
        $auditperiod = session('charge');
        if (!$auditperiod || !isset($auditperiod->userchargeid)) {
            return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
        }
        $userchargeid = $auditperiod->userchargeid;
        $auditperiodid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('auditperiod')) : null;
        // $majorworkallocationtypeid = $request->input('majorworkallocationtypeid');
        $data = [
            'statusflag' => $request->status,
            'fromyear' => $request->fromYearDropdown ?? null,
            'toyear' => $request->toYearDropdown ?? null,
            //  'deptcode' => $request->deptcode ?? null,
            // 'catcode' => $request->category ?? null,
        ];

        // print_r($data);
        if ($request->input('action') === 'insert') {
            $data['createdon'] = View::shared('get_nowtime');
            $data['createdby'] = $userchargeid;
        }
        if ($request->input('action') === 'update') {
            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $userchargeid;
        }
        // dd($data);
        try {
            $result = MastersModel::createauditperiod_insertupdate($data, $auditperiodid, 'audit.mst_auditperiod');
            $language = $request->input('lang', 'en');
            $message = ($language === 'ta') ? 'வேலை ஒதுக்கீடு வெற்றிகரமாக உருவாக்கப்பட்டது / புதுப்பிக்கப்பட்டது' : 'Audit Period Created / Updated Successfully';
            // return response()->json(['success' => true, 'message' => 'Workallocation Created / Updated Successfully', 'data' => $result]);
            return response()->json(['success' => true, 'message' => $message, 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function auditperiod_fetchData(Request $request)
    {
        $auditperiodid = $request->has('auditperiodid') ? Crypt::decryptString($request->auditperiodid) : null;
        // dd($subworkallocationtypeid);
        $auditperiod = MastersModel::getAllauditperiodData($auditperiodid, 'audit.mst_subworkallocationtype');
        foreach ($auditperiod as $all) {
            $all->encrypted_auditperiod = Crypt::encryptString($all->auditperiodid);
            unset($all->auditperiodid);
        }
        return response()->json([
            'success' => !$auditperiod->isEmpty(),
            'message' => $auditperiod->isEmpty() ? 'User not found' : '',
            'data' => $auditperiod->isEmpty() ? null : $auditperiod
        ], $auditperiod->isEmpty() ? 404 : 200);
    }

    // ---------------------------------------------------------------Group------------------------------------------------------

    public static function fetchdeptforgroup()
    {
        $dept = MastersModel::model_groupdeptfetch();
        //  $records = MastersModel::map_callforrecordsfetch();

        return view('masters.creategroup', compact('dept'));
    }

    public function group_insertupdate(Request $request)
    {
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                // 'category' => 'required|string|regex:/^\d+$/',
                'groupename' => 'required|string|max:255',
                'grouptname' => 'required|string|max:255',
                'allocatedtowhom' => 'required|in:Y,N',
                'status' => 'required|in:Y,N',
            ];
            // , [
            //     'deptcode.required' => 'The Department Code field is required.',
            //     //'category.required' => 'The Category Name field is required.',
            //     'groupename.required' => 'The Group English Name field is required.',
            //     'grouptname.required' => 'The Group Tamil Name field is required.',
            //     'status.required' => 'The Status Flag field is required.',
            //     'status.in' => 'The Status Flag must be either "Y" or "N".',
            // ]);

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $group = session('charge');
            if (!$group || !isset($group->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $group->userchargeid;
            $groupid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('groupid')) : null;
            // $majorworkallocationtypeid = $request->input('majorworkallocationtypeid');

            $deptcode = $request->deptcode;
            $enameFormatted = strtolower(str_replace(' ', '', trim($request->groupename)));
            $fnameFormatted = strtolower(str_replace(' ', '', trim($request->grouptname)));
            $statusflag = ($request->status);

            $existing = MastersModel::checkExistingGroup($enameFormatted, $statusflag, $fnameFormatted, $deptcode, $groupid);

            if ($existing['englishExists'] && $existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'DuplicationgroupETname'], 422);
            } elseif ($existing['englishExists']) {
                return response()->json(['success' => false, 'message' => 'DuplicationgroupEname'], 422);
            } elseif ($existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'DuplicationgroupTname'], 422);
            }

            $data = [
                'statusflag' => $request->status,
                'groupename' => $request->groupename ?? null,
                'grouptname' => $request->grouptname ?? null,
                'allocatedtowhom' => $request->allocatedtowhom ?? null,
                'deptcode' => $request->deptcode ?? null,
                // 'catcode' => $request->category ?? null,
            ];

            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::group_insertupdate($data, $groupid, 'audit.group');
            return response()->json(['success' => true, 'message' => 'group_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function group_fetchData(Request $request)
    {
        $groupid = $request->has('groupid') ? Crypt::decryptString($request->groupid) : null;
        // dd($majorworkallocationtypeid);
        $group = MastersModel::fetchgroupData($groupid, 'audit.group');
        // print_r($workallocation);
        foreach ($group as $all) {
            $all->encrypted_groupid = Crypt::encryptString($all->groupid);
            // dd($majorworkallocationtypeid);

            unset($all->groupid);
        }
        return response()->json([
            'success' => !$group->isEmpty(),
            'message' => $group->isEmpty() ? 'User not found' : '',
            'data' => $group->isEmpty() ? null : $group
        ], $group->isEmpty() ? 404 : 200);
    }

    // --------------------------------Master Category form---------------------------------------------------------------

    public static function categorydeptfetch()
    {
        $dept = MastersModel::model_categorydeptfetch();

        return view('masters.createcategory', compact('dept'));
    }

    public function category_insertupdate(Request $request)
    {
        // print_r($_REQUEST);
        try {
            $rules = [
                // 'subworkid' => 'required|string|max:10',
                'deptcode' => 'required|string|regex:/^\d+$/',
                // 'orderid' => 'required|string|regex:/^\d+$/',
                'categoryename' => 'required|string|max:200',
                'categorytname' => 'required|string|max:200',
                'status' => 'required|in:Y,N',
                'subcategory' => 'required|in:Y,N',
            ];
            // , [
            // 'subworkid.required' => 'The Subwork allocation field is required.',
            //     'deptcode,required' => 'The Department field is required.',
            //     // 'orderid.required' => 'The Order field is required.',
            //     'categoryename.required' => 'The English Name field is required.',
            //     'categorytname.required' => 'The Tamil Name field is required.',
            //     'status.required' => 'The Status field is required.',
            //     'status.in' => 'The Status must be either "Y" or "N".',
            //     'subcategory' => 'The Sub Category must be either "Y" or "N".',

            // ]);
            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            $category = session('charge');
            if (!$category || !isset($category->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $category->userchargeid;
            $auditeeins_categoryid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('auditeeins_categoryid')) : null;

            $deptcode = $request->deptcode;
            $enameFormatted = strtolower(str_replace(' ', '', trim($request->categoryename)));
            $fnameFormatted = strtolower(str_replace(' ', '', trim($request->categorytname)));

            $existing = MastersModel::checkExistingCategory($enameFormatted, $fnameFormatted, $deptcode, $auditeeins_categoryid);

            if ($existing['englishExists'] && $existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'DuplicationcategoryETname'], 422);
            } elseif ($existing['englishExists']) {
                return response()->json(['success' => false, 'message' => 'DuplicationcategoryEname'], 422);
            } elseif ($existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'DuplicationcategoryTname'], 422);
            }

            // if ($request->input('action') === 'update') {
            //     if (!$request->filled('catcode')) {
            //         // Retrieve existing catcode if not provided
            //         $catcode = DB::table('audit.mst_auditeeins_category')
            //             ->where('auditeeins_categoryid', $auditeeins_categoryid)
            //             ->value('catcode');
            //     } else {
            //         $catcode = $request->input('catcode');
            //     }
            // } else {
            //     // Generate a new catcode for insert
            //     $catcode = MastersModel::catcodeinsert();
            // }

            $data = [
                // 'majorworkallocationtypeid' => $request->subworkid ?? null,
                'deptcode' => $request->deptcode ?? null,
                // 'catcode' => $catcode,
                //  'orderid' => $request->orderid ?? null,
                'catename' => $request->categoryename ?? null,
                'cattname' => $request->categorytname ?? null,
                'statusflag' => $request->status,
                'if_subcategory' => $request->subcategory,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::createcategory_insertupdate($data, $auditeeins_categoryid, 'audit.mst_auditeeins_category');
            return response()->json(['success' => true, 'message' => 'category_succes']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    // public function category_fetchData(Request $request)
    // {
    //     try {
    //         $auditeeins_categoryid = $request->has('auditeeins_categoryid') ? Crypt::decryptString($request->auditeeins_categoryid) : null;
    //         $audinstcategory = MastersModel::getCategoryFetch($auditeeins_categoryid, 'audit.mst_auditeeins_category');
    //         foreach ($audinstcategory as $all) {
    //             $all->auditeeins_categoryid = Crypt::encryptString($all->auditeeins_categoryid);
    //             unset($all->auditeeins_categoryid);
    //         }
    //         return response()->json([
    //             'success' => !$audinstcategory->isEmpty(),
    //             'message' => $audinstcategory->isEmpty() ? 'User not found' : '',
    //             'data' => $audinstcategory->isEmpty() ? [] : $audinstcategory
    //         ], 200);
    //        // print_r( $auditeeins_categoryi);
    //     } catch (\Exception $e) {
    //         Log::error('Error fetching auditor inst mapping data: ' . $e->getMessage());
    //         return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
    //     }
    // }

    public function category_fetchData(Request $request)
    {
        $auditeeins_categoryid = $request->has('auditeeins_categoryid') ? Crypt::decryptString($request->auditeeins_categoryid) : null;
        // dd($auditeeins_categoryid);
        $audinstcategory = MastersModel::getCategoryFetch($auditeeins_categoryid, 'audit.auditeeins_categoryid');
        // print_r($workallocation);
        foreach ($audinstcategory as $all) {
            $all->encryptauditeeins_categoryid = Crypt::encryptString($all->auditeeins_categoryid);
            // dd($majorworkallocationtypeid);

            unset($all->auditeeins_categoryid);
        }
        return response()->json([
            'success' => !$audinstcategory->isEmpty(),
            'message' => $audinstcategory->isEmpty() ? 'User not found' : '',
            'data' => $audinstcategory->isEmpty() ? null : $audinstcategory
        ], $audinstcategory->isEmpty() ? 404 : 200);
    }

    // --------------------------------------------------------------Auditor Institution Mapping----------------------------------------------------

    // public function auditorinstmapping_fetchData(Request $request)
    // {
    //     $instmappingid = $request->has('instmappingid') ? Crypt::decryptString($request->instmappingid) : null;
    //     //dd($subworkallocationtypeid);
    //     $audinstmapp = MastersModel::getAllAuditorInstmapping($instmappingid, 'audit.auditor_instmapping');
    //     foreach ($audinstmapp as $all) {
    //         $all->encrypted_instmappingid = Crypt::encryptString($all->instmappingid);
    //         unset($all->instmappingid);
    //     }
    //     return response()->json([
    //         'success' => !$audinstmapp->isEmpty(),
    //         'message' => $audinstmapp->isEmpty() ? 'User not found' : '',
    //         'data' => $audinstmapp->isEmpty() ? [] : $audinstmapp
    //     ],200);
    // }

    //  <<<------------------- Master Callfor Record Start ------------------->>>

    public function getCategoryByDept(Request $request)
    {
        $deptcode = $request->deptcode;
        $categories = DB::table('audit.mst_auditeeins_category')
            ->where('deptcode', $deptcode)
            ->get();
        return response()->json($categories);
    }

    public function callforrecords_insertupdate(Request $request)
    {
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'callforrecordsename' => 'required|string|max:200',
                'callforrecordstname' => 'required|string|max:200',
                'statusflag' => 'required|string|in:Y,N',
            ];

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            // Get user session
            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }
            $userchargeid = $chargedel->userchargeid;

            // Determine if it's an update or insert
            $callforrecordsid = $request->input('callforrecordsid') ? Crypt::decryptString($request->input('callforrecordsid')) : null;

            $deptcode = $request->deptcode;
            $enameFormatted = strtolower(str_replace(' ', '', trim($request->callforrecordsename)));
            $fnameFormatted = strtolower(str_replace(' ', '', trim($request->callforrecordstname)));

            $existing = MastersModel::checkExistingCallforrecords($enameFormatted, $fnameFormatted, $deptcode, $callforrecordsid);

            if ($existing['englishExists'] && $existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'DuplicationCallETname'], 422);
            } elseif ($existing['englishExists']) {
                return response()->json(['success' => false, 'message' => 'DuplicationCallEname'], 422);
            } elseif ($existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'DuplicationCallTname'], 422);
            }

            $data = [
                'deptcode' => $request->deptcode ?? null,
                'callforrecordsename' => $request->callforrecordsename ?? null,
                'callforrecordstname' => $request->callforrecordstname ?? null,
                'statusflag' => $request->statusflag
            ];

            if ($callforrecordsid) {
                $data['updatedon'] = now('Asia/Kolkata');
                $data['updatedby'] = $userchargeid;
            } else {
                $data['createdon'] = now('Asia/Kolkata');
                $data['createdby'] = $userchargeid;
            }

            $result = MastersModel::callforrecords_insertupdate($data, $callforrecordsid, 'audit.callforrecords_auditee');

            return response()->json(['success' => true, 'message' => 'callfor_created']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function callforrecords_fetchData(Request $request)
    {
        $callforrecordsid = $request->has('callforrecordsid') ? Crypt::decryptString($request->callforrecordsid) : null;

        $chargedel = MastersModel::fetchcallforrecordsData($callforrecordsid, 'audit.callforrecords_auditee');

        if (!$chargedel->isEmpty()) {
            foreach ($chargedel as $all) {
                $all->encrypted_callforrecordsid = Crypt::encryptString($all->callforrecordsid);
                unset($all->callforrecordsid);
            }
        }

        return response()->json([
            'success' => !$chargedel->isEmpty(),
            'message' => $chargedel->isEmpty() ? 'No records found' : '',
            'data' => $chargedel->isEmpty() ? null : $chargedel
        ], $chargedel->isEmpty() ? 404 : 200);
    }

    //  <<<------------------- Master Callfor Record End ------------------->>>

    // --------------------------------map_allc_obj---------------------------------------------------------------
    public function mapAllcObj_dropdown($index)
    {
        $dept = DB::table('audit.mst_dept')
            ->where('statusflag', 'Y')
            ->orderBy('orderid', 'asc')
            ->get();
        $cat = DB::table('audit.mst_auditeeins_category')
            ->where('statusflag', 'Y')
            ->orderBy('catcode', 'asc')
            ->get();
        $majorobjection = DB::table('audit.mst_mainobjection')
            ->where('statusflag', 'Y')
            // ->distinct()
            ->orderBy('objectionename', 'asc')
            ->get();
        $workallocation = DB::table('audit.mst_majorworkallocationtype as majorwork')
            ->where('statusflag', 'Y')
            ->orderBy('majorworkallocationtypeename', 'asc')
            ->get();

        return view($index, compact('dept', 'cat', 'workallocation', 'majorobjection'));
    }

    public function FilterByDept(Request $request)
    {
        if ($request->deptcode) {
            $categoryDet = MastersModel::fetchCategoryData(self::$mstauditeeinscategory_table, $request->deptcode);
            $majorworkallocationdet = MastersModel::fetchMajorWorkallocationData(self::$majorworkallocationtype_table, $request->deptcode);

            $callforrec = MastersModel::fetchcallforrecordDatabyDept(self::$callforrec_table, $request->deptcode);

            $majorobjectiondet = MastersModel::mainobjectionData(self::$mainobjection_table, $request->deptcode);
            $groupdet = MastersModel::getgroupDet(self::$group_table, $request->deptcode);

            return response()->json(['category' => $categoryDet, 'majorwork' => $majorworkallocationdet, 'mainobj' => $majorobjectiondet, 'callforrec' => $callforrec, 'group' => $groupdet]);
        }

        if ($request->catcode) {
            $cat_code = $request->input('catcode');
            $subcategoryData = MastersModel::getSubcategoryByCategory($cat_code);
            return response()->json(['subcategory' => $subcategoryData]);
        }

        if ($request->mainobjectionid) {
            $subObjectionDet = MastersModel::getsubobjection(self::$subobjection_table, $request->mainobjectionid);
            // return $subObjectionDet;
            return response()->json($subObjectionDet);
        }

        if ($request->majorworkallocationtypeid) {
            $subworkDet =
                DB::table('audit.mst_subworkallocationtype as sub')
                    // ->leftjoin('audit.mst_majorworkallocationtype as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
                    ->join('audit.mst_majorworkallocationtype as major', 'sub.majorworkallocationtypeid', '=', 'major.majorworkallocationtypeid')
                    ->where('major.majorworkallocationtypeid', $request->majorworkallocationtypeid)
                    ->where('sub.statusflag', 'Y')
                    ->select(
                        'sub.subworkallocationtypeid',
                        'sub.subworkallocationtypeename',
                        'sub.subworkallocationtypetname',
                    )
                    ->distinct()
                    ->get();

            return response()->json($subworkDet);
        }
    }

    public function insertmulti_mapWorkObj(Request $request)
    {
        $sessiondet = session('user');
        $userid = $sessiondet->userid;

        $tabledata = $request->tabledata;
        $tabledata = json_decode($tabledata, true);

        $mapallocationobjectionid = null;
        // return $tabledata;
        $multimapallocationobjectionDet = MastersModel::insertmulti_mapWorkObj($tabledata, $mapallocationobjectionid, 'audit.map_allocation_objection', $userid);
        // return $multimapallocationobjectionDet;
        return response()->json(['success' => true, 'message' => 'mapping_success']);
    }

    public function insertorupdate_mapWorkObj(Request $request)
    {
        $sessiondet = session('user');
        $userid = $sessiondet->userid;
        $request->validate([
            // 'subcategory'      => 'required',
            'maj_work' => 'required',
            'cat_code' => 'required',
            // 'subworkallocationtypeid'       => 'required',
            'mainobjectionid' => 'required',
            'subobjectionid' => 'required',
            'finaliseflag' => 'required',
            'callforrec' => 'required',
            'allocatedtowhom' => 'required',
            'groupid' => 'required',
        ], [
            'required' => 'The :attribute field is required.',
            'alpha' => 'The :attribute field must contain only letters.',
            'integer' => 'The :attribute field must be a valid number.',
            'regex' => 'The :attribute field must be a valid number.',
            'alpha_num' => 'The :attribute field must contain only letters and numbers.',
            'email' => 'The :attribute field must be a valid email address.',
        ]);
        $data = [
            'auditeeins_subcategoryid' => $request->subcategory,
            'majorworkallocationtypeid' => $request->maj_work,
            'catcode' => $request->cat_code,
            'subworkallocationtypeid' => $request->subworkallocationtypeid,
            'mainobjectionid' => $request->mainobjectionid,
            'subobjectionid' => $request->subobjectionid,
            'statusflag' => $request->finaliseflag,
            'mapcallforrecordsid' => $request->callforrec,
            'allocatetowhom' => $request->allocatedtowhom,
            'groupid' => $request->groupid,
        ];
        // $sub_obj = $request->input('subworkallocationtypeid	');
        // $sub_workArray = $request->input('subworkallocationtypeid');
        // if (!is_array($sub_workArray)) {
        //     return response()->json(['error' => 'Invalid JSON format for Sub Objections.'], 400);
        // }
        // unset($data->subworkallocationtypeid);
        // $data['subwork'] = $sub_workArray;

        // return $data;
        if ($request->action == 'update') {
            $mapallocationobjectionid = $request->filled('mapallocationobjectionid') ? Crypt::decryptString($request->mapallocationobjectionid) : null;
        } else
            $mapallocationobjectionid = null;

        $mapallocationobjectionDet = MastersModel::mapallocationobj_insertupdate($data, $mapallocationobjectionid, 'audit.map_allocation_objection', $userid);
        // return $mapallocationobjectionDet;
        return response()->json(['success' => true, 'message' => 'mapping_success']);

        // return $mapallocationobjectionDet;
        // return response()->json(['success' => 'Mapping Allocation Objection created/updated successfully', 'user' => $mapallocationobjectionDet]);

        return $data;
    }

    public function fetchall_mapallocationObj(Request $request)
    {
        $deptcode_filt = $request->filled('deptcode_filt')
            ? $request->deptcode_filt
            : null;
        $towhom = $request->filled('towhom')
            ? $request->towhom
            : null;
        try {
            $mapallocationobjectionid = $request->filled('mapallocationobjectionid')
                ? Crypt::decryptString($request->mapallocationobjectionid)
                : null;

            // Fetch using cursor for memory-safe streaming
            $cursor = MastersModel::fetchall_mapallocationObj(
                'audit.map_allocation_objection',
                $mapallocationobjectionid,
                $deptcode_filt,
                $towhom
            );

            // If ID is provided and cursor has zero rows → return 404
            if ($mapallocationobjectionid && $cursor->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mapping Details not found',
                    'data' => null
                ], 404);
            }

            // Streaming response
            return response()->stream(function () use ($cursor) {
                echo '{"success": true, "message": "", "data": [';

                $first = true;

                foreach ($cursor as $row) {
                    // Add encryption
                    $row->encrypted_mapid = Crypt::encryptString($row->mapallocationobjectionid);

                    // Convert only DB attributes (SAFE)

                    $plain = (array) $row;
                    $plain['encrypted_mapid'] = $row->encrypted_mapid;

                    if (!$first) {
                        echo ',';
                    } else {
                        $first = false;
                    }

                    echo json_encode($plain, JSON_UNESCAPED_UNICODE);
                    flush();
                }

                echo ']}';
            }, 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching mapping details'
            ], 500);
        }
    }

    // ------------------------------end-------------------------------------------------

    public function auditorinstmapping_fetchData(Request $request)
    {
        try {
            $instmappingid = $request->has('instmappingid') ? Crypt::decryptString($request->instmappingid) : null;
            $audinstmapp = MastersModel::getAllAuditorInstmapping($instmappingid, 'audit.auditor_instmapping');
            foreach ($audinstmapp as $all) {
                $all->encrypted_instmappingid = Crypt::encryptString($all->instmappingid);
                unset($all->instmappingid);
            }
            return response()->json([
                'success' => !$audinstmapp->isEmpty(),
                'message' => $audinstmapp->isEmpty() ? 'User not found' : '',
                'data' => $audinstmapp->isEmpty() ? [] : $audinstmapp
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching auditor inst mapping data: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

    // public function auditorinstmapping_fetchData(Request $request)
    // {
    //     try {
    //         $instmappingid = $request->has('instmappingid') ? Crypt::decryptString($request->instmappingid) : null;

    //         // Fetch data using the model (adjust based on your model method)
    //         $audinstmapp = MastersModel::getAllAuditorInstmapping($instmappingid, 'audit.auditor_instmapping');

    //         // Check if no data is returned and handle it properly
    //         if ($audinstmapp->isEmpty()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'No data found',
    //                 'data' => []
    //             ], 404); // Return 404 if no data found
    //         }

    //         // Encrypt instmappingid for each record and remove the original instmappingid
    //         foreach ($audinstmapp as $all) {
    //             $all->encrypted_instmappingid = Crypt::encryptString($all->instmappingid);
    //             unset($all->instmappingid);  // Optionally remove original instmappingid after encryption
    //         }

    //         // Return data with success message
    //         return response()->json([
    //             'success' => true,
    //             'message' => '',
    //             'data' => $audinstmapp  // Return the data as is
    //         ], 200);

    //     } catch (DecryptException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid instmappingid provided'
    //         ], 400);  // 400 Bad Request for invalid decryption

    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while fetching data'
    //         ], 500);
    //     }
    // }

    public static function DeptandRoletypeFetch()
    {
        $roletype = MastersModel::Roletypefetch();
        $dept = MastersModel::ForAuditmodalDeptfetch();
        $district = MastersModel::distfetch();

        return view('masters.auditorinstmapping', compact('dept', 'roletype', 'district'));
    }

    public function auditorinstmapping_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'roletypecode' => 'required|string|regex:/^\d+$/',
                'inst_ename' => 'required|string|max:200',
                'inst_tname' => 'required|string|max:300',
                'nodal_ename' => 'required|string|max:100',
                'nodal_tname' => 'required|string|max:100',
                'desigcode' => 'required|string|regex:/^\d+$/',
                'email' => 'required|email|max:100',
                'mobile' => 'required|digits:10',
                'address_ename' => 'required|string|max:200',
                'address_tname' => 'required|string|max:200',
                'pincode' => 'required|digits:6',
                'audittype' => 'required|string|max:2',
                'statusflag' => 'required|in:Y,N',
            ];

            switch ($request->roletypecode) {
                case '01':
                    $rules['deptcode'] = 'required|string|regex:/^\d+$/';
                    $rules['regioncode'] = 'required|string|regex:/^\d+$/';
                    $rules['distcode'] = 'required|string|regex:/^\d+$/';
                    break;

                case '02':
                    $rules['deptcode'] = 'required|string|regex:/^\d+$/';
                    $rules['regioncode'] = 'required|string|regex:/^\d+$/';
                    break;

                case '03':
                    $rules['deptcode'] = 'required|string|regex:/^\d+$/';
                    break;
            }

            // $messages = [
            //     'roletypecode.required' => 'The Role Type field is required.',
            //     'roletypecode.required' => 'The Role Type field is required.',
            //     'deptcode.required' => 'The Department Code field is required.',
            //     'deptcode.regex' => 'The Department Code must contain only digits.',
            //     'regioncode.required' => 'The Region Code field is required.',
            //     // 'deptcode' => in_array($request->roletypecode, ['01', '02', '03']) ? $request->deptcode : null,
            //     // 'regioncode' => in_array($request->roletypecode, ['01', '02']) ? $request->regioncode : null,
            //     // 'distcode' => $request->roletypecode === '01' ? $request->distcode : null,
            //     'inst_ename.required' => 'The Institution English Name field is required.',
            //     'inst_tname.required' => 'The Institution Tamil Name field is required.',
            //     'status.required' => 'The Status field is required.',
            //     'nodal_ename.required' => 'The Nodal Person English Name field is required.',
            //     'nodal_tname.required' => 'The Nodal Person Tamil Name field is required.',
            //     'designationcode.required' => 'The Designation field is required.',
            //     'email.required' => 'The Email field is required.',
            //     'email.email' => 'The Email must be a valid email address.',
            //     'mobile.required' => 'The Mobile Number field is required.',
            //     'mobile.digits' => 'The Mobile Number must contain only digits.',
            //     'mobile.min' => 'The Mobile Number must be at least 10 digits.',
            //     'address_ename.required' => 'The Address English Name field is required.',
            //     'address_tname.required' => 'The Address Tamil Name field is required.',
            //     'pincode.required' => 'The Pincode field is required.',
            //     'pincode.digits' => 'The Pincode must contain only digits.',
            //     'pincode.size' => 'The Pincode must be exactly 6 digits.',
            //     'status.in' => 'The Status must be either "Y" or "N".',
            // ];

            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            // Validate request data
            //   $validatedData = $request->validate($rules, $messages);

            $audinst_map = session('charge');
            if (!$audinst_map || !isset($audinst_map->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $audinst_map->userchargeid;
            $instmappingid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('audinstmappingcode')) : null;

            // $instmappingcode = $request->input('audinstmappingcode')
            //     ? $request->input('instmappingcode')
            //     : MastersModel::Instmappingcode();

            $data = [
                // 'instmappingcode' => $instmappingcode,
                'roletypecode' => $request->roletypecode,
                'deptcode' => $request->deptcode ?? null,
                'regioncode' => $request->regioncode ?? null,
                'distcode' => $request->distcode ?? null,
                'instename' => $request->inst_ename ?? null,
                'insttname' => $request->inst_tname ?? null,
                'nodalperson_ename' => $request->nodal_ename ?? null,
                'nodalperson_tname' => $request->nodal_tname ?? null,
                'nodalperson_desigcode' => $request->desigcode ?? null,
                'email' => $request->email ?? null,
                'mobile' => $request->mobile ?? null,
                'officeaddress_ename' => $request->address_ename ?? null,
                'officeaddress_tname' => $request->address_tname ?? null,
                'pincode' => $request->pincode ?? null,
                'audittype' => $request->audittype,
                'statusflag' => $request->statusflag,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::ForAuditauditorinstmapp_insertupdate($data, $instmappingid, 'audit.auditor_instmapping');
            return response()->json(['success' => true, 'message' => 'auditorinst_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function ForAuditorgetRegionBasedOnDept(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');

        $regions = MastersModel::ForAuditgetRegionsByDept($deptcode);
        $audittype = MastersModel::ForAuditgetaudittypeByDept($deptcode);

        return response()->json([
            'success' => true,
            'regions' => $regions,  // Regions data
            'audittype' => $audittype  // Audit types data
        ]);
    }

    public function ForAuditorgetdesignationbasedondept(Request $request)
    {
        // Validate the input
        $request->validate(
            [
                'deptcode' => ['required', 'string', 'r   egex:/^\d+$/'],
            ],
            [
                'deptcode.required' => 'The deptcode field is required.',
                'deptcode.regex' => 'The deptcode field must be a valid number.',
            ]
        );

        $deptcode = $request->input('deptcode');

        // Fetch regions from the model
        $designation = MastersModel::ForAuditgetdesignationByDept($deptcode);

        // Return JSON response
        if ($designation->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $designation]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    // ---------------------------subwork allocation----------------------------------------------

    public static function SubworkDeptFetch()
    {
        $dept = MastersModel::subwork_deptfetch();

        return view('masters.createsubworkallocation', compact('dept'));

        //  echo($auditee);
    }

    public function subworkallocation_insertupdate(Request $request)
    {
        // print_r($_REQUEST);
        try {
            $rules = [
                'workcode' => ['required', 'string', 'regex:/^\d+$/'],
                'deptcode' => 'required|string|regex:/^\d+$/',
                // 'orderid' => 'required|string|regex:/^\d+$/',
                'sub_ename' => 'required|string|max:300',
                'tname' => 'required|string|max:300',
                'status' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            // , [
            //     // 'subworkid.required' => 'The Subwork allocation field is required.',
            //     'deptcode,required' => 'The Department field is required.',
            //     'orderid.required' => 'The Order field is required.',
            //     'ename.required' => 'The English Name field is required.',
            //     'tname.required' => 'The Tamil Name field is required.',
            //     'status.required' => 'The Status field is required.',
            //     'status.in' => 'The Status must be either "Y" or "N".',
            // ]);
            $subworkdel = session('charge');
            if (!$subworkdel || !isset($subworkdel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $subworkdel->userchargeid;
            $subworkallocationtypeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('subworkallocationid')) : null;

            $ename = strtolower(str_replace(' ', '', $request->sub_ename));
            $tname = strtolower(str_replace(' ', '', $request->tname));
            $statusflag = ($request->status);
            $workcode = $request->workcode;

            // Check for duplicates
            $duplicateCheck = MastersModel::checkDuplicateForSubwork($workcode, $statusflag, $ename, $tname, $subworkallocationtypeid);

            if ($duplicateCheck['ename'] && $duplicateCheck['tname']) {
                return response()->json(['success' => false, 'message' => 'DuplicationsubworkETname'], 422);
            } elseif ($duplicateCheck['ename']) {
                return response()->json(['success' => false, 'message' => 'DuplicationsubworkEname'], 422);
            } elseif ($duplicateCheck['tname']) {
                return response()->json(['success' => false, 'message' => 'DuplicationsubworkTname'], 422);
            }

            $data = [
                // 'majorworkallocationtypeid' => $request->subworkid ?? null,
                'deptcode' => $request->deptcode ?? null,
                'majorworkallocationtypeid' => $request->workcode ?? null,
                'subworkallocationtypeename' => $request->sub_ename ?? null,
                'subworkallocationtypetname' => $request->tname ?? null,
                'statusflag' => $request->status,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::createsubworkallocation_insertupdate($data, $subworkallocationtypeid, 'audit.mst_subworkallocationtype');
            return response()->json(['success' => true, 'message' => 'subwork_succes']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function subworkallocationtype_fetchData(Request $request)
    {
        $subworkallocationtypeid = $request->has('subworkallocationtypeid') ? Crypt::decryptString($request->subworkallocationtypeid) : null;
        // dd($subworkallocationtypeid);
        $subwork = MastersModel::getAllSubWorkAllocationData($subworkallocationtypeid, 'audit.mst_subworkallocationtype');
        foreach ($subwork as $all) {
            $all->encrypted_subworkallocationtypeid = Crypt::encryptString($all->subworkallocationtypeid);
            unset($all->subworkallocationtypeid);
        }
        return response()->json([
            'success' => !$subwork->isEmpty(),
            'message' => $subwork->isEmpty() ? 'User not found' : '',
            'data' => $subwork->isEmpty() ? null : $subwork
        ], $subwork->isEmpty() ? 404 : 200);
    }

    // public function getworkallocationBasedOnDept(Request $request)
    // {
    //     $deptcode = $request->input('deptcode');

    //     if (!$deptcode) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Department code is required.'
    //         ], 400);
    //     }

    //     // Fetch work allocations based on the department code
    //     $workallocation = MastersModel::getworkallocationByDept($deptcode);

    //     if (!$workallocation || $workallocation->isEmpty()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'No work allocation found.',
    //             'data' => []
    //         ]);
    //     }

    //     // ✅ Correctly return JSON response (remove print_r)
    //     return response()->json([
    //         'success' => true,
    //         'data' => $workallocation
    //     ]);
    // }
    // <<<-------------------------------work allocation --------------------->>

    // <<<-------------------------------work allocation --------------------->>
    public static function workallocationdeptfetch()
    {
        $dept = MastersModel::model_workallocationdeptfetch();

        return view('masters.createworkallocation', compact('dept'));
    }

    // public function getCategoriesBasedOnDept(Request $request)
    // {
    //     // Get the deptcode from the request data
    //     $deptcode = $request->input('deptcode');

    //     // Fetch categories based on deptcode and statusflag
    //     $categories = DB::table('audit.mst_auditeeins_category')
    //                     ->where('deptcode', $deptcode)
    //                     ->where('statusflag', 'Y')
    //                     ->get(['catcode', 'catename']);

    //     return response()->json($categories);
    // }

    public function workallocation_insertupdate(Request $request)
    {
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                // 'category' => 'required|string|regex:/^\d+$/',
                'ename' => 'required|string|max:200',
                'fname' => 'required|string|max:200',
                'status' => 'required|in:Y,N',
            ];

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            // , [
            //     'deptcode.required' => 'The Department Code field is required.',
            //     //'category.required' => 'The Category Name field is required.',
            //     'ename.required' => 'The English Objection Name field is required.',
            //     'fname.required' => 'The Tamil Objection Name field is required.',
            //     'status.required' => 'The Status Flag field is required.',
            //     'status.in' => 'The Status Flag must be either "Y" or "N".',
            // ]);
            $workallocation = session('charge');
            if (!$workallocation || !isset($workallocation->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $workallocation->userchargeid;
            $majorworkallocationtypeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('workallocation')) : null;

            $deptcode = $request->deptcode;
            $enameFormatted = strtolower(str_replace(' ', '', trim($request->ename)));
            $fnameFormatted = strtolower(str_replace(' ', '', trim($request->fname)));
            $statusflag = ($request->status);

            $existing = MastersModel::checkExistingWorkAllocation($enameFormatted, $statusflag, $fnameFormatted, $deptcode, $majorworkallocationtypeid);

            if ($existing['englishExists'] && $existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'workETname'], 422);
            } elseif ($existing['englishExists']) {
                return response()->json(['success' => false, 'message' => 'workEname'], 422);
            } elseif ($existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'workTname'], 422);
            }

            $data = [
                'statusflag' => $request->status,
                'majorworkallocationtypeename' => $request->ename ?? null,
                'majorworkallocationtypetname' => $request->fname ?? null,
                'deptcode' => $request->deptcode ?? null,
                // 'catcode' => $request->category ?? null,
            ];

            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::createworkallocation_insertupdate($data, $majorworkallocationtypeid, 'audit.mst_majorworkallocationtype');

            return response()->json(['success' => true, 'message' => 'workallocation_created'], 201);

            // dd($data);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function workallocationtype_fetchData(Request $request)
    {
        $majorworkallocationtypeid = $request->has('majorworkallocationtypeid') ? Crypt::decryptString($request->majorworkallocationtypeid) : null;
        // dd($majorworkallocationtypeid);
        $workallocation = MastersModel::fetchworkallocationData($majorworkallocationtypeid, 'audit.mst_majorworkallocationtype');
        // print_r($workallocation);
        foreach ($workallocation as $all) {
            $all->encrypted_majorworkallocationtypeid = Crypt::encryptString($all->majorworkallocationtypeid);
            // dd($majorworkallocationtypeid);

            unset($all->majorworkallocationtypeid);
        }
        return response()->json([
            'success' => !$workallocation->isEmpty(),
            'message' => $workallocation->isEmpty() ? 'User not found' : '',
            'data' => $workallocation->isEmpty() ? null : $workallocation
        ], $workallocation->isEmpty() ? 404 : 200);
    }

    // -----------------------------------------work allocation end-----------------------------------------

    // -------------------------------------------AuditeeUser---------------------------------------------------------------------------------

    public static function auditee_deptfetch()
    {
        $dept = MastersModel::audit_deptfetch();

        return view('masters.auditeeuserdetails', compact('dept'));

        //  echo($auditee);
    }

    public function auditeeuserdetails_fetchData(Request $request)
    {
        $auditeeuserid = $request->has('auditeeuserid') ? Crypt::decryptString($request->auditeeuserid) : null;
        // dd($subworkallocationtypeid);
        $auditeeuser = MastersModel::getAllAudtieeUserDetails($auditeeuserid, 'audit.audtieeuserdetails');
        foreach ($auditeeuser as $all) {
            $all->encrypted_auditeeuserid = Crypt::encryptString($all->auditeeuserid);
            unset($all->auditeeuserid);
        }
        return response()->json([
            'success' => !$auditeeuser->isEmpty(),
            'message' => $auditeeuser->isEmpty() ? 'User not found' : '',
            'data' => $auditeeuser->isEmpty() ? null : $auditeeuser
        ], $auditeeuser->isEmpty() ? 404 : 200);
    }

    public function auditeeuserdetails_insertupdate(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode' => 'required|string|regex:/^\d+$/',
            'region' => 'required|string|regex:/^\d+$/',
            'district' => 'required|string|regex:/^\d+$/',
            'institution' => 'required|string|regex:/^\d+$/',
            'email' => 'required|string|max:255',
            'status' => 'required|in:Y,N',
        ], [
            'deptcode.required' => 'The Department field is required.',
            'region.required' => 'The Region field is required.',
            'district.required' => 'The District field is required.',
            'institution.required' => 'The Institution field is required.',
            'email.required' => 'The Email field is required.',
            'status.required' => 'The Status Flag field is required.',
            'status.in' => 'The Status Flag must be either "Y" or "N".',
        ]);
        $auditeeuserid = session('charge');
        if (!$auditeeuserid || !isset($auditeeuserid->userchargeid)) {
            return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
        }
        $userchargeid = $auditeeuserid->userchargeid;
        $auditeeuserid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('auduserdetails_id')) : null;
        // $majorworkallocationtypeid = $request->input('majorworkallocationtypeid');
        $data = [
            // 'deptcode' => $request->deptcode ?? null,
            // 'catcode' => $request->region ?? null,
            'instid' => $request->institution ?? null,
            'email' => $request->email ?? null,
            'statusflag' => $request->status,
        ];

        // print_r($data);
        if ($request->input('action') === 'insert') {
            $password = '123456';
            $hashedPassword = Hash::make($password);

            $data['pwd'] = $hashedPassword;
            $data['createdon'] = View::shared('get_nowtime');
            $data['createdby'] = $userchargeid;
        }

        if ($request->input('action') === 'update') {
            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $userchargeid;
        }
        // dd($data);
        try {
            $result = MastersModel::createauditeeuserdetails_insertupdate($data, $auditeeuserid, 'audit.audtieeuserdetails');
            return response()->json(['success' => true, 'message' => 'Auditee User Details Created / Updated Successfully', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function getRegionBasedOnDept(Request $request)
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

        // Fetch regions from the model
        $regions = MastersModel::getRegionsByDept($deptcode);

        // Return JSON response
        if ($regions->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $regions]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function getdistrictbasedonregion(Request $request)
    {
        // Validate the input
        $request->validate(
            [
                'region' => ['required', 'string', 'regex:/^\d+$/'],
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            ],
            [
                'region.required' => 'The region field is required.',
                'region.regex' => 'The region field must be a valid number.',
                'deptcode.required' => 'The deptcode field is required.',
                'deptcode.regex' => 'The deptcode field must be a valid number.',
            ]
        );

        // Get the department code
        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');

        // Fetch regions from the model
        $district = MastersModel::getdistrictByregion($regioncode, $deptcode);

        // Return JSON response
        if ($district->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $district]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    // public function getdistrictbasedonregion(Request $request)
    // {
    //    //$deptcode = $request->input('deptcode');
    //     $regioncode = $request->input('region');

    //     $categories = DB::table('audit.mst_institution as ins')
    //                     ->join('audit.mst_district as dis', 'ins.distcode', '=' , 'dis.distcode')
    //                    // ->join('audit.mst_region as reg', 'ins.regioncode', '=' , 'reg.regioncode')
    //                     ->select('dis.distename','dis.distcode')
    //                     ->distinct()
    //                    // ->where('ins.deptcode', $deptcode)
    //                     ->where('ins.regioncode', $regioncode)
    //                     ->where('ins.statusflag', 'Y')
    //                     ->get();

    //     return response()->json($categories);
    // }

    public function getinstitutionbasedondist(Request $request)
    {
        // Validate the input
        $request->validate([
            'region' => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'district' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'region.required' => 'The :attribute field is required.',
            'region.regex' => 'The :attribute field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex' => 'The deptcode field must be a valid number.',
            'district.required' => 'The district field is required.',
            'district.regex' => 'The district field must be a valid number.',
        ]);

        // Get the department code
        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');
        $district = $request->input('district');

        // Fetch regions from the model
        $institution = MastersModel::getinstitutionBydistrict($district, $regioncode, $deptcode);

        // Return JSON response
        if ($institution->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $institution]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    // public function getinstitutionbasedondist(Request $request)
    // {
    //     // Get the deptcode from the request data
    //     $distcode = $request->input('district');

    //     // Fetch categories based on deptcode and statusflag
    //     $categories = DB::table('audit.mst_institution as ins')
    //                     ->select('ins.instename','ins.instid')
    //                     ->distinct()
    //                     ->where('ins.distcode', $distcode)
    //                     ->where('ins.statusflag', 'Y')
    //                     ->get();

    //     return response()->json($categories);
    // }

    // --------------------------------------------map_call_for_records--------------------------
    public static function fetchdeptAndRecords()
    {
        $dept = MastersModel::ModaldeptForCallforrecords();
        //  $records = MastersModel::map_callforrecordsfetch();

        return view('masters.mapcallforrecords', compact('dept'));
    }

    public function getCallforrecordsBasedOnDept(Request $request)
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

        $callforrecords = MastersModel::getcallforrecordsByDept($deptcode);

        if ($callforrecords->isNotEmpty()) {
            return response()->json($callforrecords);
        } else {
            return response()->json(['success' => false, 'message' => 'No Call For Records found'], 404);
        }
    }

    public function getCategoriesBasedOnDept(Request $request)
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

        $category = MastersModel::getcategoryByDept($deptcode);

        if ($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    //    public function getCategoriesBasedOnDept(Request $request)
    //     {
    //         $deptcode = $request->input('deptcode');

    //         $categories = DB::table('audit.mst_auditeeins_category')
    //                         ->where('deptcode', $deptcode)
    //                         ->where('statusflag', 'Y')
    //                         ->get(['catcode', 'catename']);

    //         return response()->json($categories);
    //     }

    public function mapcallforrecords_insertupdate(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode' => 'required|string|regex:/^\d+$/',
            'category' => 'required|string|regex:/^\d+$/',
            'mapcallforrecordid' => 'required|string|max:255',
            'status' => 'required|in:Y,N',
        ], [
            'deptcode.required' => 'The Department Code field is required.',
            'category.required' => 'The Category Name field is required.',
            'mapcallforrecordid.required' => 'The Tamil Objection Name field is required.',
            'status.required' => 'The Status Flag field is required.',
            'status.in' => 'The Status Flag must be either "Y" or "N".',
        ]);
        $mapcallforrecords = session('charge');
        if (!$mapcallforrecords || !isset($mapcallforrecords->userchargeid)) {
            return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
        }
        $userchargeid = $mapcallforrecords->userchargeid;
        $mapcallforrecordid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('map_callforrecords')) : null;
        // $majorworkallocationtypeid = $request->input('majorworkallocationtypeid');
        $data = [
            'statusflag' => $request->status,
            'callforecordsid' => $request->mapcallforrecordid ?? null,
            // 'majorworkallocationtypetname' => $request->fname ?? null,
            // 'deptcode' => $request->deptcode ?? null,
            'catcode' => $request->category ?? null,
        ];

        // print_r($data);
        if ($request->input('action') === 'insert') {
            $data['createdon'] = View::shared('get_nowtime');
            $data['createdby'] = $userchargeid;
            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $userchargeid;
        }

        if ($request->input('action') === 'update') {
            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $userchargeid;
        }
        // dd($data);
        try {
            $result = MastersModel::createmapcallforrecords_insertupdate($data, $mapcallforrecordid, 'audit.map_callforrecord');
            return response()->json(['success' => true, 'message' => 'Map Call for records Created / Updated Successfully', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function mapcallforrecords_fetchData(Request $request)
    {
        $mapcallforrecordid = $request->has('mapcallforrecordid') ? Crypt::decryptString($request->mapcallforrecordid) : null;
        // dd($subworkallocationtypeid);
        $mapcallforrecord = MastersModel::getAllmapcallforrecords($mapcallforrecordid, 'audit.map_callforrecord');
        foreach ($mapcallforrecord as $all) {
            $all->encrypted_mapcallforrecordid = Crypt::encryptString($all->mapcallforrecordid);
            unset($all->mapcallforrecordid);
        }
        return response()->json([
            'success' => !$mapcallforrecord->isEmpty(),
            'message' => $mapcallforrecord->isEmpty() ? 'User not found' : '',
            'data' => $mapcallforrecord->isEmpty() ? null : $mapcallforrecord
        ], $mapcallforrecord->isEmpty() ? 404 : 200);
    }

    public function storeOrUpdate(Request $request)
    {
        $sessiondet = session('user');
        $userid = $sessiondet->userid;

        $request->validate([
            'deptesname' => 'required',
            'deptelname' => 'required',
            'depttsname' => 'required',
            'depttlname' => 'required',
            'address' => 'required',
            'landline' => 'required|numeric',
            'mobile' => ['required', 'regex:/^[6-9]\d{9}$/'],
            'slip_frwd_days' => 'required',
            'balance_sheet' => 'required|max:1',
            'rec_charg' => 'required|max:1',
            'rejoinder' => 'required|max:1',
            'part1' => 'required|max:1',
            'part2' => 'required|max:1',
            'part3' => 'required|max:1',
        ], [
            'required' => 'The :attribute field is required.',
        ]);

        $data = [
            'deptesname' => $request->input('deptesname'),
            'deptelname' => $request->input('deptelname'),
            'depttsname' => $request->input('depttsname'),
            'depttlname' => $request->input('depttlname'),
            'address' => $request->input('address'),
            'landlinenumber' => $request->input('landline'),
            'mobile' => $request->input('mobile'),
            'slipforwarddays' => $request->input('slip_frwd_days'),
            'autointimationtoinst' => $request->input('auto_int'),
            'balancesheetrequired' => $request->input('balance_sheet'),
            'receiptandcharges' => $request->input('rec_charg'),
            'rejoinderrequired' => $request->input('rejoinder'),
            'part1isreqiored' => $request->input('part1'),
            'part2isreqiored' => $request->input('part2'),
            'part3isreqiored' => $request->input('part3'),
            'statusflag' => 'Y',
            'createdon' => now(),  // Current timestamp for created_at
            'updatedon' => now(),  // Current timestamp for updated_at
            'createdby' => $userid,
            'updatedby' => $userid,
        ];
        if ($request->action == 'update')
            $deptid = $request->input('dept_id');
        else
            $deptid = null;
        $deptdet = MastersModel::createdept_insertupdate($data, $deptid, 'audit.mst_dept');
        return response()->json(['success' => 'Department created/updated successfully', 'user' => $deptdet]);
        // return $data;
    }

    public function fetchAlldata()
    {
        $deptConfigDetails = MastersModel::fetchAlldata('audit.mst_dept');
        foreach ($deptConfigDetails as $item) {
            $item->encrypted_deptid = Crypt::encryptString($item->deptid);
        }
        // Return data in JSON format
        return response()->json(['data' => $deptConfigDetails]);
    }

    public function fetchdept_data(Request $request)
    {
        $deptid = Crypt::decryptString($request->deptid);
        $single_deptConfigDetails = MastersModel::fetchdept_data($deptid, 'audit.mst_dept');

        if ($single_deptConfigDetails) {
            return response()->json(['success' => true, 'data' => $single_deptConfigDetails]);
        } else {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    public function index()
    {
        $dept = UserManagementModel::departmenttdetail();
        return view('masters.createdesignation', compact('dept'));
    }

    public function dept_drop($view)
    {
        $dept = UserManagementModel::deptdetail();
        return view($view, compact('dept'));
    }

    public function designation_insertupdate(Request $request)
    {
        try {
            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }
            $userchargeid = $chargedel->userchargeid;

            $chargeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('desigid')) : null;
            $rules = [
                'desigesname' => 'required|string|max:10',
                'desigelname' => 'required|string|max:100',
                'desigtsname' => 'required|string|max:10',
                'desigtlname' => 'required|string|max:100',
                'deptcode' => 'required|string|regex:/^\d+$/',
                'statusflag' => 'required|in:Y,N',
                'departmenttype' => 'required|in:Y,N',
            ];
            // , [
            //     // 'subworkid.required' => 'The Subwork allocation field is required.',
            //     'deptcode,required' => 'The Department field is required.',
            //     'desigesname.required' => 'The Designation Short Name is required.',
            //     'desigelname.required' => 'The Designation long Name is required.',
            //     'desigtsname.required' => 'The Designation Tamil short name is required.',
            //     'desigtlname.required' => 'The Designation Tamil long name is required.',
            //     'statusflag.required' => 'The Status field is required.',
            //     'statusflag.in' => 'The Status must be either "Y" or "N".',
            // ]);
            $count = DB::table('audit.mst_designation')->count();
            $data = [
                'statusflag' => $request->statusflag,
                'desigesname' => $request->desigesname ?? null,
                'desigelname' => $request->desigelname ?? null,
                'desigtsname' => $request->desigtsname ?? null,
                'desigtlname' => $request->desigtlname ?? null,
                'deptcode' => $request->deptcode ?? null,
                'isauditeeedepartment' => $request->departmenttype,
                // 'desigcode' => $request->input('desigid')
                //     ? $request->input('desigcode')
                //     : str_pad($count + 1, 2, '0', STR_PAD_LEFT),
            ];

            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::createdesignation_insertupdate($data, $chargeid, 'audit.mst_designation');
            return response()->json(['success' => true, 'message' => 'designation_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function designation_fetchData(Request $request)
    {
        $desigid = $request->has('desigid') ? Crypt::decryptString($request->desigid) : null;
        $chargedel = MastersModel::fetchdesignationData($desigid, 'audit.mst_designation');
        foreach ($chargedel as $all) {
            $all->encrypted_desigid = Crypt::encryptString($all->desigid);
        }
        return response()->json([
            'success' => !$chargedel->isEmpty(),
            'message' => $chargedel->isEmpty() ? 'User not found' : '',
            'data' => $chargedel->isEmpty() ? null : $chargedel
        ], $chargedel->isEmpty() ? 404 : 200);
    }

    //  <<<------------------- Master Designation End ------------------->>>

    //  <<<------------------- Master Region Start ------------------->>>

    public function region_insertupdate(Request $request)
    {
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'regionename' => 'required|string|max:255',
                'regiontname' => 'required|string|max:255',
                'statusflag' => 'required|in:Y,N',
            ];
            // , [
            //     'deptcode.required' => 'The Department Code field is required.',
            //     'regionename.required' => 'The English Region Name field is required.',
            //     'regiontname.required' => 'The Tamil Region Name field is required.',
            //     'statusflag.required' => 'The Status Flag field is required.',
            //     'statusflag.in' => 'The Status Flag must be either "Y" or "N".',
            // ]);
            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }
            $userchargeid = $chargedel->userchargeid;
            $regionid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('regionid')) : null;
            $count = DB::table('audit.mst_region')->count();

            $data = [
                'statusflag' => $request->statusflag,
                'regionename' => $request->regionename ?? null,
                'regiontname' => $request->regiontname ?? null,
                'deptcode' => $request->deptcode ?? null,
                // 'regioncode' => $request->input('regionid')
                //     ? $request->input('regioncode')
                //     : str_pad($count + 1, 2, '0', STR_PAD_LEFT),
            ];

            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::createregion_insertupdate($data, $regionid, 'audit.mst_region');
            return response()->json(['success' => true, 'message' => 'region_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function region_fetchData(Request $request)
    {
        $regionid = $request->has('regionid') ? Crypt::decryptString($request->regionid) : null;
        $chargedel = MastersModel::fetchregionData($regionid, 'audit.mst_region');
        foreach ($chargedel as $all) {
            $all->encrypted_regionid = Crypt::encryptString($all->regionid);
        }
        return response()->json([
            'success' => !$chargedel->isEmpty(),
            'message' => $chargedel->isEmpty() ? 'User not found' : '',
            'data' => $chargedel->isEmpty() ? null : $chargedel
        ], $chargedel->isEmpty() ? 404 : 200);
    }

    //  <<<------------------- Master Region End ------------------->>>
    //  <<<------------------- Master MainObjection Start ------------------->>>
    public function getCategoriesByDept(Request $request)
    {
        $deptcode = $request->deptcode;
        $categories = DB::table('audit.mst_auditeeins_category')
            ->where('deptcode', $deptcode)
            ->get();
        return response()->json($categories);
    }

    public function mainobjection_insertupdate(Request $request)
    {
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'objectionename' => 'required|string|max:200',
                'objectiontname' => 'required|string|max:200',
                'statusflag' => 'required|in:Y,N',
            ];

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            // , [
            //     'deptcode.required' => 'The Department Code field is required.',

            //     'objectionename.required' => 'The English Objection Name field is required.',
            //     'objectiontname.required' => 'The Tamil Objection Name field is required.',
            //     'statusflag.required' => 'The Status Flag field is required.',
            //     'statusflag.in' => 'The Status Flag must be either "Y" or "N".',
            // ]);
            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }
            $userchargeid = $chargedel->userchargeid;
            $mainobjectionid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('mainobjectionid')) : null;

            $deptcode = $request->deptcode;
            $enameFormatted = strtolower(str_replace(' ', '', trim($request->objectionename)));
            $tnameFormatted = strtolower(str_replace(' ', '', trim($request->objectiontname)));
            $statusflag = ($request->statusflag);

            // Check for duplication within the department
            $existing = MastersModel::checkExistingMainObjection($enameFormatted, $statusflag, $tnameFormatted, $deptcode, $mainobjectionid);

            if ($existing['englishExists'] && $existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'objectionETname'], 422);
            } elseif ($existing['englishExists']) {
                return response()->json(['success' => false, 'message' => 'objEname'], 422);
            } elseif ($existing['tamilExists']) {
                return response()->json(['success' => false, 'message' => 'objTname'], 422);
            }

            $data = [
                'statusflag' => $request->statusflag,
                'objectionename' => $request->objectionename ?? null,
                'objectiontname' => $request->objectiontname ?? null,
                'deptcode' => $request->deptcode ?? null,
            ];
            if ($request->input('action') === 'insert') {
                $data['createdon'] = now();
                $data['createdby'] = $userchargeid;
            }
            $result = MastersModel::createmainobjection_insertupdate($data, $mainobjectionid, 'audit.mst_mainobjection');
            return response()->json(['success' => true, 'message' => 'mainobjection_created']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function mainobjection_fetchData(Request $request)
    {
        $mainobjectionid = $request->has('mainobjectionid') ? Crypt::decryptString($request->mainobjectionid) : null;
        $chargedel = MastersModel::fetchmainobjectionData($mainobjectionid, 'audit.mst_mainobjection');
        foreach ($chargedel as $all) {
            $all->encrypted_mainobjectionid = Crypt::encryptString($all->mainobjectionid);
        }
        return response()->json([
            'success' => !$chargedel->isEmpty(),
            'message' => $chargedel->isEmpty() ? 'User not found' : '',
            'data' => $chargedel->isEmpty() ? null : $chargedel
        ], $chargedel->isEmpty() ? 404 : 200);
    }

    //  <<<------------------- Master MainObjection End ------------------->>>
    //  <<<------------------- Master SubObjection Start ------------------->>>
    public function subobjection_insertupdate(Request $request)
    {
        try {
            $rules = [
                'mainobjectionid' => 'required|integer',
                'deptcode' => 'required|string|regex:/^\d+$/',
                'subobjectionename' => 'required|string|max:300',
                'subobjectiontname' => 'required|string|max:300',
                'statusflag' => 'required|in:Y,N',
            ];
            // , [
            //     'mainobjectionid.required' => 'The Objection Name field is required.',
            //     'deptcode.required' => 'The Department field is required.',
            //     'subobjectiontname.required' => 'The Tamil Objection Name field is required.',
            //     'statusflag.required' => 'The Status Flag field is required.',
            //     'statusflag.in' => 'The Status Flag must be either "Y" or "N".',
            // ]);
            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }
            $userchargeid = $chargedel->userchargeid;
            $subobjectionid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('subobjectionid')) : null;
            $maxOrderId = DB::table('audit.mst_subobjection')->max('subobjectionid');
            // $orderid = $request->input('orderid')
            //     ? $request->input('orderid')
            //     : ($maxOrderId ? $maxOrderId + 1 : 1);

            $ename = strtolower(str_replace(' ', '', $request->subobjectionename));
            $tname = strtolower(str_replace(' ', '', $request->subobjectiontname));
            $statusflag = ($request->statusflag);
            $mainobjectionid = $request->mainobjectionid;

            // Check for duplicates
            $duplicateCheck = MastersModel::checkDuplicateForSubobj($mainobjectionid, $statusflag, $ename, $tname, $subobjectionid);

            if ($duplicateCheck['ename'] && $duplicateCheck['tname']) {
                return response()->json(['success' => false, 'message' => 'DuplicationsubobjETname'], 422);
            } elseif ($duplicateCheck['ename']) {
                return response()->json(['success' => false, 'message' => 'DuplicationsubobjEname'], 422);
            } elseif ($duplicateCheck['tname']) {
                return response()->json(['success' => false, 'message' => 'DuplicationsubobjTname'], 422);
            }

            $data = [
                'statusflag' => $request->statusflag,
                'deptcode' => $request->deptcode ?? null,
                'subobjectionename' => $request->subobjectionename ?? null,
                'subobjectiontname' => $request->subobjectiontname ?? null,
                'mainobjectionid' => $request->mainobjectionid ?? null,
                // 'orderid' => $orderid,
            ];
            if ($request->input('action') === 'insert') {
                $data['createdon'] = now();
                $data['createdby'] = $userchargeid;
            }
            $result = MastersModel::creatsubobjection_insertupdate($data, $subobjectionid, 'audit.mst_subobjection');
            return response()->json(['success' => true, 'message' => 'subobjection_created', 'data' => $result]);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    // public function subobjection_fetchData(Request $request)
    // {
    //     $subobjectionid = $request->has('subobjectionid') ? Crypt::decryptString($request->subobjectionid) : null;
    //     $chargedel = MastersModel::fetchsubobjectionData($subobjectionid, 'audit.mst_subobjection');
    //     foreach ($chargedel as $all) {
    //         $all->encrypted_subobjectionid = Crypt::encryptString($all->subobjectionid);
    //     }
    //     return response()->json([
    //         'success' => !$chargedel->isEmpty(),
    //         'message' => $chargedel->isEmpty() ? 'User not found' : '',
    //         'data' => $chargedel->isEmpty() ? null : $chargedel
    //     ], $chargedel->isEmpty() ? 404 : 200);
    // }

    // public function getobjectionBasedOnDept(Request $request)
    // {
    //     $request->validate([
    //         'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
    //     ], [
    //         'required' => 'The :attribute field is required.',
    //         'regex'    => 'The :attribute field must be a valid number.',
    //     ]);

    //     $deptcode = $request->input('deptcode');

    //     $objection = MastersModel::getobjectionByDept($deptcode);

    //     if ($objection->isNotEmpty()) {
    //         return response()->json(['success' => true, 'data' => $objection]);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'No regions found'], 404);
    //     }
    // }

    //  <<<------------------- Master MainObjection End ------------------->>>
    //  <<<------------------- Master Menu Form  Start ------------------->>>
    public function menu_insertupdate(Request $request)
    {
        $validatedData = $request->validate([
            'levelid' => 'required|string|max:5',
            'parentid' => 'nullable|string|max:5',
            'menuurl' => 'required|string|max:255',
            'menuename' => 'required|string|max:255',
            'menutname' => 'required|string|max:255',
            'iconname' => 'required|string|max:255',
        ], [
            'levelid.required' => 'The Department Code field is required.',
            'menuurl.required' => 'The Category Name field is required.',
            'menuename.required' => 'The English Objection Name field is required.',
            'menutname.required' => 'The Tamil Objection Name field is required.',
            'iconname.required' => 'The Tamil Objection Name field is required.',
        ]);
        if ($request->levelid == '2') {
            $rules['parentid'] = 'required|string|max:5';
        }
        $chargedel = session('charge');
        if (!$chargedel || !isset($chargedel->userchargeid)) {
            return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
        }
        $userchargeid = $chargedel->userchargeid;
        $menuid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('menuid')) : null;
        $parentid = ($request->levelid == '1') ? '0' : ($request->parentid ?? null);
        $maxOrderId = DB::table('audit.mst_menu')->max('menuid');
        $orderid = $request->input('orderid')
            ? $request->input('orderid')
            : ($maxOrderId ? $maxOrderId + 1 : 1);
        $parentorderid = null;
        if ($request->levelid == '1') {
            $count = DB::table('audit.mst_menu')->where('levelid', 1)->count();
            $parentorderid = $count + 1;
        }
        $data = array_merge($validatedData, [
            'menuename' => $request->menuename ?? null,
            'menutname' => $request->menutname ?? null,
            'menuurl' => $request->menuurl ?? null,
            'levelid' => $request->levelid ?? null,
            'parentid' => $parentid,
            'orderid' => $orderid,
            'iconname' => $request->iconname ?? null,
            'parentorderid' => $parentorderid
        ]);
        if ($request->input('action') === 'insert') {
            // $data['createdon'] = now();
            // $data['createdby'] =  $userchargeid;
        }
        try {
            $result = MastersModel::createmenu_insertupdate($data, $menuid, 'audit.mst_menu');
            return response()->json(['success' => true, 'message' => 'Charge Created / Updated Successfully', 'data' => $result]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    // public function menu_fetchData(Request $request)
    // {
    //     $menuid = $request->has('menuid') ? Crypt::decryptString($request->menuid) : null;
    //     $chargedel = (new MastersModel())->fetchmenuData($menuid, 'audit.mst_menu');
    //     foreach ($chargedel as $all) {
    //         $all->encrypted_menuid = Crypt::encryptString($all->menuid);
    //     }
    //     return response()->json([
    //         'success' => !$chargedel->isEmpty(),
    //         'message' => $chargedel->isEmpty() ? 'No Data Found' : '',
    //         'data' => $chargedel->isEmpty() ? null : $chargedel
    //     ], $chargedel->isEmpty() ? 404 : 200);
    // }
    public function menu_fetchData(Request $request)
    {
        $menuid = $request->has('menuid') ? Crypt::decryptString($request->menuid) : null;
        if ($menuid) {
            $chargedel = (new MastersModel())->fetchmenuData_record($menuid, 'audit.mst_menu');
        } else {
            $chargedel = (new MastersModel())->fetchmenuData($menuid, 'audit.mst_menu');
        }
        foreach ($chargedel as $all) {
            $all->encrypted_menuid = Crypt::encryptString($all->menuid);
        }
        return response()->json([
            'success' => !$chargedel->isEmpty(),
            'message' => $chargedel->isEmpty() ? 'No Data Found' : '',
            'data' => $chargedel->isEmpty() ? null : $chargedel
        ], $chargedel->isEmpty() ? 404 : 200);
    }

    public function saveOrderId(Request $request)
    {
        $menu = DB::table('audit.mst_menu')
            ->where('menuid', $request->menuid)
            ->first();
        $updated = DB::table('audit.mst_menu')
            ->where('menuid', $request->menuid)
            ->update([$request->type => $request->value]);

        if ($updated) {
            return response()->json(['success' => true, 'message' => 'Order ID updated successfully', 'data' => $request->menuId]);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to update orderid']);
        }
    }

    // public function mapAllcObj_dropdown()
    // {
    //     $dept = DB::table('audit.mst_dept')
    //         ->where('statusflag', 'Y')
    //         ->orderBy('orderid', 'asc')
    //         ->get();
    //     $cat = DB::table('audit.mst_auditeeins_category')
    //         ->where('statusflag', 'Y')
    //         ->orderBy('catcode', 'asc')
    //         ->get();
    //     $majorobjection = DB::table('audit.mst_mainobjection')
    //         ->where('statusflag', 'Y')
    //         // ->distinct()
    //         ->orderBy('objectionename', 'asc')
    //         ->get();
    //     $workallocation = DB::table('audit.mst_majorworkallocationtype as majorwork')

    //         ->where('statusflag', 'Y')
    //         ->orderBy('majorworkallocationtypeename', 'asc')
    //         ->get();

    //     return view('masters.mapallocationobjection', compact('dept', 'cat', 'workallocation', 'majorobjection'));
    // }
    // public function FilterByDept(Request $request)
    // {
    //     // return $request;
    //     if ($request->deptcode) {

    //         $category = DB::table('audit.mst_dept as md')
    //             ->join('audit.mst_auditeeins_category as mac', 'md.deptcode', '=', 'mac.deptcode')
    //             ->where('md.deptcode', $request->deptcode)
    //             ->select('md.deptcode', 'mac.catcode', 'mac.cattname', 'mac.catename', 'mac.auditeeins_categoryid')
    //             ->distinct()
    //             ->get();
    //         // return $category;
    //         $categoryDet = $category->map(function ($category) {
    //             return [
    //                 'deptcode' => $category->deptcode,
    //                 'catcode' => $category->catcode,
    //                 'cattname' => $category->cattname,
    //                 'catename' => $category->catename,
    //                 'catcode' => $category->catcode,
    //             ];
    //         })->toArray();

    //         return response()->json($categoryDet);
    //     }

    //     if ($request->catcode) {

    //         $callforrec = DB::table('audit.callforrecords_auditee as cr')
    //             ->leftjoin('audit.map_callforrecord as mc', 'mc.callforecordsid', '=', 'cr.callforrecordsid')
    //             ->where('mc.catcode', $request->catcode)
    //             ->select('cr.callforrecordsid', 'cr.callforrecordsename', 'cr.callforrecordstname', 'mc.mapcallforrecordid')
    //             ->distinct()
    //             ->get();
    //         return $callforrec;
    //         $callforrecDet = $callforrec->map(function ($callforrec) {
    //             return [
    //                 'mapcallforrecordid' => $callforrec->mapcallforrecordid,
    //                 'callforrecordsid' => $callforrec->callforrecordsid,
    //                 'callforrecordsename' => $callforrec->callforrecordsename,
    //                 'callforrecordstname' => $callforrec->callforrecordstname,

    //             ];
    //         })->toArray();

    //         return response()->json($callforrecDet);
    //     }
    //     if ($request->mainobjectionid) {

    //         $subObjection = DB::table('audit.mst_subobjection as sub')
    //             ->leftjoin('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 'sub.mainobjectionid')
    //             ->where('sub.mainobjectionid', $request->mainobjectionid)
    //             ->select('sub.subobjectionid', 'sub.subobjectionename', 'sub.subobjectiontname',)
    //             ->distinct()
    //             ->get();

    //         $subObjectionDet = $subObjection->map(function ($subObjection) {
    //             return [
    //                 'subobjectionid' => $subObjection->subobjectionid,
    //                 'subobjectionename' => $subObjection->subobjectionename,
    //                 'subobjectiontname' => $subObjection->subobjectiontname,

    //             ];
    //         })->toArray();

    //         return response()->json($subObjectionDet);
    //     }
    //     if ($request->majorworkallocationtypeid) {

    //         $subwork = DB::table('audit.map_workallocation as map')
    //             ->leftjoin('audit.mst_majorworkallocationtype as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
    //             ->leftjoin('audit.mst_subworkallocationtype as sub', 'sub.subworkallocationtypeid', '=', 'map.minorworkallocationtypeid')

    //             ->where('map.majorworkallocationtypeid', $request->majorworkallocationtypeid)
    //             ->select('sub.subworkallocationtypeid', 'sub.subworkallocationtypeename', 'sub.subworkallocationtypetname',)
    //             ->distinct()
    //             ->get();

    //         $subworkDet = $subwork->map(function ($subwork) {
    //             return [
    //                 'subworkallocationtypeid' => $subwork->subworkallocationtypeid,
    //                 'subworkallocationtypeename' => $subwork->subworkallocationtypeename,
    //                 'subworkallocationtypetname' => $subwork->subworkallocationtypetname,

    //             ];
    //         })->toArray();

    //         return response()->json($subwork);
    //     }
    // }
    // public function FilterByDept(Request $request)
    // {
    //     // return $request;
    //     if ($request->deptcode) {

    //         $category = DB::table('audit.mst_auditeeins_category')
    //             // ->join('audit.mst_auditeeins_category as mac', 'md.deptcode', '=', 'mac.deptcode')
    //             ->where('deptcode', $request->deptcode)
    //             ->select('deptcode', 'catcode', 'cattname', 'catename', 'auditeeins_categoryid')
    //             ->distinct()
    //             ->get();
    //         $majorworkallocationdet = DB::table('audit.mst_majorworkallocationtype')
    //             ->where('deptcode', $request->deptcode)
    //             ->where('statusflag', 'Y')
    //             ->select('deptcode', 'majorworkallocationtypeid', 'majorworkallocationtypeename', 'majorworkallocationtypetname')
    //             ->distinct()
    //             ->get();
    //         $majorobjectiondet = DB::table('audit.mst_mainobjection')
    //             ->where('deptcode', $request->deptcode)
    //             ->where('statusflag', 'Y')
    //             ->select('deptcode', 'mainobjectionid', 'objectionename', 'objectiontname')
    //             ->distinct()
    //             ->get();
    //         // return $majorworkallocationdet;
    //         $categoryDet = $category->map(function ($category) {
    //             return [
    //                 'deptcode' => $category->deptcode,
    //                 'catcode' => $category->catcode,
    //                 'cattname' => $category->cattname,
    //                 'catename' => $category->catename,
    //                 'catcode' => $category->catcode,
    //             ];
    //         })->toArray();

    //         return response()->json(['category' => $categoryDet, 'majorwork' => $majorworkallocationdet, 'mainobj' => $majorobjectiondet]);
    //     }

    //     if ($request->catcode) {

    //         $callforrec = DB::table('audit.callforrecords_auditee as cr')
    //             ->leftjoin('audit.map_callforrecord as mc', 'mc.callforecordsid', '=', 'cr.callforrecordsid')
    //             ->where('mc.catcode', $request->catcode)
    //             ->select('cr.callforrecordsid', 'cr.callforrecordsename', 'cr.callforrecordstname', 'mc.mapcallforrecordid')
    //             ->distinct()
    //             ->get();
    //         return $callforrec;
    //         $callforrecDet = $callforrec->map(function ($callforrec) {
    //             return [
    //                 'mapcallforrecordid' => $callforrec->mapcallforrecordid,
    //                 'callforrecordsid' => $callforrec->callforrecordsid,
    //                 'callforrecordsename' => $callforrec->callforrecordsename,
    //                 'callforrecordstname' => $callforrec->callforrecordstname,

    //             ];
    //         })->toArray();

    //         return response()->json($callforrecDet);
    //     }
    //     if ($request->mainobjectionid) {

    //         $subObjection = DB::table('audit.mst_subobjection as sub')
    //             ->leftjoin('audit.mst_mainobjection as main', 'main.mainobjectionid', '=', 'sub.mainobjectionid')
    //             ->where('sub.mainobjectionid', $request->mainobjectionid)
    //             ->select('sub.subobjectionid', 'sub.subobjectionename', 'sub.subobjectiontname',)
    //             ->distinct()
    //             ->get();

    //         $subObjectionDet = $subObjection->map(function ($subObjection) {
    //             return [
    //                 'subobjectionid' => $subObjection->subobjectionid,
    //                 'subobjectionename' => $subObjection->subobjectionename,
    //                 'subobjectiontname' => $subObjection->subobjectiontname,

    //             ];
    //         })->toArray();

    //         return response()->json($subObjectionDet);
    //     }
    //     if ($request->majorworkallocationtypeid) {

    //         $subwork = DB::table('audit.map_workallocation as map')
    //             ->leftjoin('audit.mst_majorworkallocationtype as major', 'major.majorworkallocationtypeid', '=', 'map.majorworkallocationtypeid')
    //             ->leftjoin('audit.mst_subworkallocationtype as sub', 'sub.subworkallocationtypeid', '=', 'map.minorworkallocationtypeid')

    //             ->where('map.majorworkallocationtypeid', $request->majorworkallocationtypeid)
    //             ->select('sub.subworkallocationtypeid', 'sub.subworkallocationtypeename', 'sub.subworkallocationtypetname',)
    //             ->distinct()
    //             ->get();

    //         $subworkDet = $subwork->map(function ($subwork) {
    //             return [
    //                 'subworkallocationtypeid' => $subwork->subworkallocationtypeid,
    //                 'subworkallocationtypeename' => $subwork->subworkallocationtypeename,
    //                 'subworkallocationtypetname' => $subwork->subworkallocationtypetname,

    //             ];
    //         })->toArray();

    //         return response()->json($subworkDet);
    //     }
    // }
    // public function insertorupdate_mapWorkObj(Request $request)
    // {
    //     $sessiondet = session('user');
    //     $userid =  $sessiondet->userid;
    //     $data = [
    //         'majorworkallocationtypeid' => $request->maj_work,
    //         'subworkallocationtypeid' => $request->subworkallocationtypeid,
    //         'mainobjectionid' => $request->mainobjectionid,
    //         'subobjectionid' => $request->subobjectionid,
    //         'statusflag' => $request->finaliseflag,
    //         'mapcallforrecordsid' => $request->callforrec,
    //         'created_by' => $userid,
    //         'updated_by' => $userid,
    //         'created_on' => View::shared('get_nowtime'),
    //         'updated_on' => View::shared('get_nowtime'),
    //     ];

    //     if ($request->action == 'update') {
    //         $mapallocationobjectionid = $request->filled('mapallocationobjectionid') ? Crypt::decryptString($request->mapallocationobjectionid) : null;
    //     } else
    //         $mapallocationobjectionid =   null;
    //     $mapallocationobjectionDet = MastersModel::mapallocationobj_insertupdate($data, $mapallocationobjectionid, 'audit.map_allocation_objection');
    //     return response()->json(['success' => 'Department created/updated successfully', 'user' => $mapallocationobjectionDet]);

    //     return $data;
    // }

    // public function fetchall_mapallocationObj(Request $request)
    // {
    //     try {
    //         $mapallocationobjectionid = $request->filled('mapallocationobjectionid') ? Crypt::decryptString($request->mapallocationobjectionid) : null;

    //         $MapallocationobjectionDet = MastersModel::fetchall_mapallocationObj('audit.map_allocation_objection', $mapallocationobjectionid);
    //         foreach ($MapallocationobjectionDet as $all) {
    //             $all->encrypted_mapid = Crypt::encryptString($all->mapallocationobjectionid);
    //         }
    //         if ($mapallocationobjectionid) {
    //             if ($MapallocationobjectionDet->isEmpty()) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Mapping Details not found not found',
    //                     'data' => null
    //                 ], 404);
    //             }

    //             // Encrypt user IDs in results
    //             $MapallocationobjectionDet->transform(function ($all) {
    //                 $all->encrypted_mapid = Crypt::encryptString($all->mapallocationobjectionid);
    //                 return $all;
    //             });

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => '',
    //                 'data' => $MapallocationobjectionDet
    //             ], 200);
    //         }

    //         // If userid is not provided (fetch mode)
    //         return response()->json([
    //             'success' => true,
    //             'message' => '',
    //             'data' => $MapallocationobjectionDet->isEmpty() ? null : $MapallocationobjectionDet
    //         ], 200);
    //         // return $allMapallocationobjectionDet;

    //         // Return data in JSON format
    //         // return response()->json($allMapallocationobjectionDet);
    //     } catch (DecryptException $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Invalid  ID provided'
    //         ], 400);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while fetching user data'
    //         ], 500);
    //     }
    // }

    // //---------------------------subwork allocation---------------------------

    // public function subworkallocation_insertupdate(Request  $request)
    // {
    //    // print_r($_REQUEST);

    //     $validatedData = $request->validate([
    //         'subworkid' => 'required|string|max:10',
    //         'orderid' => 'required|string|regex:/^\d+$/',
    //         'ename' => 'required|string|max:255',
    //         'tname' => 'required|string|max:255',
    //         'status' => 'required|in:Y,N',

    //     ], [
    //         'subworkid.required' => 'The Subwork allocation field is required.',
    //         'orderid.required' => 'The Order id Name field is required.',
    //         'ename.required' => 'The English Name field is required.',
    //         'tname.required' => 'The Tamil Name field is required.',
    //         'status.required' => 'The Status field is required.',
    //         'status.in' => 'The Status must be either "Y" or "N".',
    //     ]);
    //     $subworkdel = session('charge');
    //     if (!$subworkdel || !isset($subworkdel->userchargeid)) {
    //         return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
    //     }
    //     $userchargeid = $subworkdel->userchargeid;
    //    $subworkallocationtypeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('subworkallocationid')) : null;
    //    //$majorworkallocationtypeid = $request->input('majorworkallocationtypeid');

    //    $existingRecord = DB::table('audit.mst_subworkallocationtype')
    //    ->where('orderid', $request->orderid)
    //    ->when($subworkallocationtypeid, function ($query) use ($subworkallocationtypeid) {
    //        return $query->where('subworkallocationtypeid', '!=', $subworkallocationtypeid);
    //    })
    //    ->exists();

    //     if ($existingRecord) {
    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Order already exists. Please use a different one.'
    //     ], 422);
    //     }
    //     $data =  [
    //         'majorworkallocationtypeid' => $request->subworkid ?? null,
    //         'orderid' => $request->orderid ?? null,
    //         'subworkallocationtypeename'   => $request->ename ?? null,
    //         'subworkallocationtypetname' => $request->tname ?? null,
    //         'statusflag' => $request->status,

    //     ];
    //     //print_r($data);
    //     if ($request->input('action') === 'insert') {
    //         $data['createdon'] = View::shared('get_nowtime');
    //         $data['createdby'] =  $userchargeid;
    //     }
    //     if ($request->input('action') === 'update') {
    //         $data['createdon'] = View::shared('get_nowtime');
    //         $data['createdby'] =  $userchargeid;
    //     }
    //     // dd($data);
    //     try {
    //         $result = MastersModel::createsubworkallocation_insertupdate($data, $subworkallocationtypeid, 'audit.mst_subworkallocationtype');
    //         return response()->json(['success' => true, 'message' => 'Workallocation Created / Updated Successfully', 'data' => $result]);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    //     }
    // }

    // //<<<-------------------------------work allocation --------------------->>

    // public function getCategoriesBasedOnDept(Request $request)
    // {
    //     // Get the deptcode from the request data
    //     $deptcode = $request->input('deptcode');

    //     // Fetch categories based on deptcode and statusflag
    //     $categories = DB::table('audit.mst_auditeeins_category')
    //                     ->where('deptcode', $deptcode)
    //                     ->where('statusflag', 'Y')
    //                     ->get(['catcode', 'catename']);

    //     return response()->json($categories);
    // }

    // public function workallocationtype_fetchData(Request $request)
    // {
    //     $majorworkallocationtypeid = $request->has('majorworkallocationtypeid') ? Crypt::decryptString($request->majorworkallocationtypeid) : null;
    //    // dd($majorworkallocationtypeid);
    //     $workallocation = MastersModel::fetchworkallocationData($majorworkallocationtypeid, 'audit.mst_majorworkallocationtype');
    //     foreach ($workallocation as $all) {
    //         $all->encrypted_majorworkallocationtypeid = Crypt::encryptString($all->majorworkallocationtypeid);
    //         unset($all->majorworkallocationtypeid);

    //     }
    //     return response()->json([
    //         'success' => !$workallocation->isEmpty(),
    //         'message' => $workallocation->isEmpty() ? 'User not found' : '',
    //         'data' => $workallocation->isEmpty() ? null : $workallocation
    //     ], $workallocation->isEmpty() ? 404 : 200);
    // }

    // public function workallocation_insertupdate(Request  $request)
    // {
    //     $validatedData = $request->validate([
    //         'deptcode' => 'required|string|regex:/^\d+$/',
    //         'category' => 'required|string|regex:/^\d+$/',
    //         'ename' => 'required|string|max:255',
    //         'fname' => 'required|string|max:255',
    //         'status' => 'required|in:Y,N',
    //     ], [
    //         'deptcode.required' => 'The Department Code field is required.',
    //         'category.required' => 'The Category Name field is required.',
    //         'ename.required' => 'The English Objection Name field is required.',
    //         'fname.required' => 'The Tamil Objection Name field is required.',
    //         'status.required' => 'The Status Flag field is required.',
    //         'status.in' => 'The Status Flag must be either "Y" or "N".',
    //     ]);
    //     $workallocation = session('charge');
    //     if (!$workallocation || !isset($workallocation->userchargeid)) {
    //         return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
    //     }
    //     $userchargeid = $workallocation->userchargeid;
    //    $majorworkallocationtypeid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('workallocation')) : null;
    //    //$majorworkallocationtypeid = $request->input('majorworkallocationtypeid');
    //     $data =  [
    //         'statusflag' => $request->status,
    //         'majorworkallocationtypeename'   => $request->ename ?? null,
    //         'majorworkallocationtypetname' => $request->fname ?? null,
    //         'deptcode' => $request->deptcode ?? null,
    //         'catcode' => $request->category ?? null,
    //     ];

    //     //print_r($data);
    //     if ($request->input('action') === 'insert') {
    //         $data['createdon'] =  View::shared('get_nowtime');
    //         $data['createdby'] =  $userchargeid;
    //     }
    //     if ($request->input('action') === 'update') {
    //         $data['createdon'] =  View::shared('get_nowtime');
    //         $data['createdby'] =  $userchargeid;
    //     }
    //     // dd($data);
    //     try {
    //         $result = MastersModel::createworkallocation_insertupdate($data, $majorworkallocationtypeid, 'audit.mst_majorworkallocationtype');
    //         return response()->json(['success' => true, 'message' => 'Workallocation Created / Updated Successfully', 'data' => $result]);
    //     } catch (\Exception $e) {
    //         return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
    //     }
    // }
    // public function mapInst_dropdown()
    // {
    //     $roletype = DB::table(self::$roletype)
    //         ->where('statusflag', 'Y')
    //         // ->where('instituteflag', 'Y')
    //         ->orderBy('roletypecode', 'asc')
    //         ->get();
    //     $dept = DB::table('audit.mst_dept')
    //         ->where('statusflag', 'Y')
    //         ->orderBy('orderid', 'asc')
    //         ->get();
    //     $cat = DB::table('audit.mst_auditeeins_category')
    //         ->where('statusflag', 'Y')
    //         ->orderBy('catcode', 'asc')
    //         ->get();
    //     $auditquarter = DB::table('audit.mst_auditquarter')
    //         ->where('statusflag', 'Y')
    //         ->distinct()
    //         // ->orderBy('objectionename', 'asc')
    //         ->get();
    //     $region = DB::table('audit.mst_region')

    //         ->where('statusflag', 'Y')
    //         ->orderBy('regioncode', 'asc')
    //         ->get();
    //     $district = DB::table('audit.mst_district')

    //         ->where('statusflag', 'Y')
    //         ->orderBy('distename', 'asc')
    //         ->get();
    //     $designation = DB::table('audit.mst_designation')

    //         ->where('statusflag', 'Y')
    //         ->orderBy('desigelname', 'asc')
    //         ->get();

    //     return view('masters.mapinst', compact('dept', 'cat', 'auditquarter', 'region', 'district', 'designation', 'roletype'));
    // }

    public function mapInst_dropdown()
    {
        $inst_quarter = MastersModel::getquartersforinst();
        $roletype = DB::table(self::$roletype)
            ->where('statusflag', 'Y')
            // ->where('instituteflag', 'Y')
            ->orderBy('roletypecode', 'asc')
            ->get();
        $dept = DB::table('audit.mst_dept')
            ->where('statusflag', 'Y')
            ->orderBy('orderid', 'asc')
            ->get();
        $cat = DB::table('audit.mst_auditeeins_category')
            ->where('statusflag', 'Y')
            ->orderBy('catcode', 'asc')
            ->get();

        $region = DB::table('audit.mst_region')
            ->where('statusflag', 'Y')
            ->orderBy('regioncode', 'asc')
            ->get();
        $district = DB::table('audit.mst_district')
            ->where('statusflag', 'Y')
            ->orderBy('distename', 'asc')
            ->get();
        $designation = DB::table('audit.mst_designation')
            ->where('statusflag', 'Y')
            ->orderBy('desigelname', 'asc')
            ->get();

        $revenuedistrict = DB::table('audit.mst_revenuedistrict')
            ->where('statusflag', 'Y')
            ->orderBy('revenuedistename', 'asc')
            ->get();

        $auditmode = MastersModel::getauditmodeDetails();
        $quarterdetails = MastersModel::getoldandnewquarter();
        $currentquarter = 'Q2';
        return view('masters.mapinst', compact('quarterdetails', 'dept', 'cat', 'region', 'district', 'designation', 'roletype', 'revenuedistrict', 'currentquarter', 'auditmode', 'inst_quarter'));
    }

    public function FilterInst(Request $request)
    {
        try {
            if ($request->regioncode) {
                $request->validate([
                    'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
                ], [
                    'required' => 'The :attribute field is required.',
                    'regex' => 'The :attribute field must be a valid number.',
                ]);

                $districtdet = MastersModel::districtDet(self::$distTable, $request->regioncode, $request->deptcode);
                // return $districtdet;
                return response()->json(['districtdet' => $districtdet]);
            } else if ($request->catcode) {
                $request->validate([
                    'catcode' => ['required', 'string', 'regex:/^\d+$/'],
                ], [
                    'required' => 'The :attribute field is required.',
                    'regex' => 'The :attribute field must be a valid number.',
                ]);
                $subcategory = MastersModel::subCateDet(self::$subcat_table, $request->catcode);

                return response()->json(['subcategory' => $subcategory]);
            } else if ($request->deptcode) {
                $request->validate([
                    'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
                ], [
                    'required' => 'The :attribute field is required.',
                    'regex' => 'The :attribute field must be a valid number.',
                ]);
                $category = MastersModel::catDet(self::$department_table, $request->deptcode);
                $region = MastersModel::regionDet(self::$roletypemapping_table, $request->deptcode);
                // $desigDet       = MastersModel::designationDet(self::$designation_table,  $request->deptcode);
                $audieedept = MastersModel::getaudieedept($request->deptcode);
                $typeofaudit = MastersModel::gettypeofaudit($request->deptcode);

                $auditquarter = DB::table('audit.mst_auditquarter')
                    ->where('deptcode', $request->deptcode)
                    ->where('statusflag', 'Y')
                    ->distinct()
                    // ->orderBy('objectionename', 'asc')
                    ->get();
                return response()->json(['typeofaudit' => $typeofaudit, 'audieedept' => $audieedept, 'category' => $category, 'region' => $region, 'auditquarter' => $auditquarter]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching  data'
            ], 500);
        }
    }

    public function FilterAuditInst(Request $request)
    {
        if ($request->desigcode && $request->deptcode && $request->regioncode && $request->distcode && $request->instmappingcode) {
            $cond = [
                'desigcode' => $request->desigcode,
                'instmappingcode' => $request->instmappingcode,
                'deptcode' => $request->deptcode,
                'regioncode' => $request->regioncode,
                'distcode' => $request->distcode,
            ];
            $auditofficerDet = MastersModel::auditofficerDet($cond);

            // return $auditofficerDet;
            return response()->json(['auditorsuserdet' => $auditofficerDet]);
        } else if ($request->deptcode && $request->regioncode && $request->distcode && $request->instmappingcode) {
            $cond = [
                'deptcode' => $request->deptcode,
                'regioncode' => $request->regioncode,
                'distcode' => $request->distcode,
                'instmappingcode' => $request->instmappingcode
            ];
            $auditordet = MastersModel::getauditordetbasedon_inst($cond);
            return response()->json(['auditordet' => $auditordet]);
        } elseif ($request->deptcode && $request->regioncode && $request->distcode) {
            $cond = [
                'deptcode' => $request->deptcode,
                'regioncode' => $request->regioncode,
                'distcode' => $request->distcode
            ];

            $auditofficeDet = MastersModel::getOfficeDet($cond);
            $instid = $request->filled('instid') ? Crypt::decryptString($request->instid) : null;
            $cond['instid'] = $instid;

            $hubdesig = MastersModel::getAuditdesignationByDept($cond);
            $zonecode = MastersModel::fetchzonebasedondistforinst($cond);
            $parentinstDet = MastersModel::getParentinst($cond);
            return response()->json(['auditofficedet' => $auditofficeDet, 'parentinstdet' => $parentinstDet, 'hubdesig' => $hubdesig, 'zonecode' => $zonecode]);
        }
    }

    public function fetch_mapInstDet(Request $request)
    {
        try {
            $instid = $request->filled('instid') ? Crypt::decryptString($request->instid) : null;

            $formtype = $request->form;

            $deptcode = $request->input('deptcode');
            $quarter = $request->input('quarter');
            $auditmode = $request->input('filter_auditmode');

            $mapInstDet = MastersModel::fetch_mapInstDet(self::$inst_table, $instid, $deptcode, $quarter, $auditmode, $formtype);

            if ($formtype == 'fetch') {
                $audit_reportDet = '';
            } else {
                $audit_reportDet = MastersModel::fetch_auditreportDet($instid, $deptcode, $quarter, $auditmode);
            }

            // return  $audit_reportDet;
            foreach ($mapInstDet as $all) {
                $all->encrypted_instid = Crypt::encryptString($all->instid);
            }
            if ($instid) {
                if ($mapInstDet->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mapping Details not found not found',
                        'data' => null
                    ], 404);
                }

                foreach ($mapInstDet as $all) {
                    $all->encrypted_instid = Crypt::encryptString($all->instid);
                    $all->encrypted_auditeeuserid = Crypt::encryptString($all->auditeeuserid);
                }
                // Encrypt user IDs in results
                // $mapInstDet->transform(function ($all) {
                // $all->encrypted_instid = Crypt::encryptString($all->instid);
                // $all->encrypted_auditeeuserid = Crypt::encryptString($all->auditeeuserid);
                // return $all;
                // });

                $deptcode = $mapInstDet->pluck('deptcode')->first();
                $desigDet = MastersModel::designationDet(self::$designation_table, $deptcode);

                return response()->json([
                    'success' => true,
                    'message' => '',
                    'data' => $mapInstDet,
                    'audit_reportdata' => $audit_reportDet,
                    'desigdata' => $desigDet
                ], 200);
            }

            // If userid is not provided (fetch mode)
            // return response()->json([
            //     'success' => true,
            //     'message' => '',
            //     'data' => $mapInstDet->isEmpty() ? null : $mapInstDet,
            //     'audit_reportdata' => $audit_reportDet,
            // ], 200);
            // return $allMapallocationobjectionDet;

            // Return data in JSON format
            // return response()->json($allMapallocationobjectionDet);
            return response()->stream(function () use ($mapInstDet, $audit_reportDet) {
                echo '{"success": true, "message": "", "data": [';

                $first = true;

                foreach ($mapInstDet as $row) {
                    // process row
                    $row->encrypted_instid = Crypt::encryptString($row->instid);

                    if (!$first) {
                        echo ',';
                    } else {
                        $first = false;
                    }

                    echo json_encode($row);
                    flush();
                }

                echo '], "audit_reportdata": ' . json_encode($audit_reportDet) . '}';
            }, 200, [
                'Content-Type' => 'application/json'
            ]);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid  ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

  public function insertorupdate_mapInst(Request $request)
    {
        try {
            $sessiondet = session('user');
            $userid = $sessiondet->userid;

            $request->validate([
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
                'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
                'distcode' => ['required', 'string', 'regex:/^\d+$/'],
                'revenuedistcode' => ['required', 'string', 'regex:/^\d+$/'],
                'cat_code' => ['required', 'string', 'regex:/^\d+$/'],
                'subcatid' => ['nullable', 'string', 'regex:/^\d+$/'],
                'instename' => [
                    'required',
                    'string',
                    'min:10',
                    'max:200',
                    'regex:/^[A-Za-z0-9\-\.,\/\(\) ]+$/',
                    'regex:/^(?![0-9 ]+$).+$/',
                    'regex:/^(?!([\w\-\.,\/\(\) ])\1+$).+$/',
                ],
                'insttname' => [
                    'required',
                    'string',
                    'min:10',
                    'max:200',
                    'regex:/^[a-zA-Z\x{0B80}-\x{0BFF}0-9\-\.,\/\(\) ]+$/u',
                    'regex:/^(?![0-9 ]+$).+$/',
                    'regex:/^(?!([\x{0B80}-\x{0BFF}a-zA-Z0-9\-\.,\/\(\) ])\1+$).+$/u',
                ],
                'mandays' => 'nullable|integer',
                'audit_mode' => ['required', 'string', 'in:N,C,T,Q'],
                'nodalperson_ename' => [
                    'nullable',
                    'string',
                    'min:3',
                    'max:75',
                    'regex:/^[A-Za-z\. ]+$/',
                    'regex:/^(?!([A-Za-z\. ])\1+$).+$/',
                ],
                'nodalperson_tname' => [
                    'nullable',
                    'string',
                    'min:3',
                    'max:75',
                    'regex:/^[\x{0B80}-\x{0BFF}\. ]+$/u',
                    'regex:/^(?!([\x{0B80}-\x{0BFF}\. ])\1+$).+$/u',
                ],
                'nodalperson_desigcode' => [
                    'nullable',
                    'string',
                    'min:3',
                    'max:100',
                    'regex:/^[A-Za-z\x{0B80}-\x{0BFF}0-9\-\.,\/\(\) ]+$/u',
                    'regex:/^(?!([A-Za-z\x{0B80}-\x{0BFF}0-9\-\.,\/\(\) ])\1+$).+$/u',
                ],
                'auditee_ofaddr' => [
                    'nullable',
                    'string',
                    'min:3',
                    'max:250',
                    'regex:/^[A-Za-z\x{0B80}-\x{0BFF}0-9\-\.,\/\(\) ]+$/u',
                    'regex:/^(?!([A-Za-z\x{0B80}-\x{0BFF}0-9\-\.,\/\(\) ])\1+$).+$/u',
                ],
                'email' => ['nullable', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
                'mobile' => ['nullable', 'regex:/^(?!([6-9])\1{9}$)[6-9][0-9]{9}$/'],
                'reportemail.*' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
                'reportmobile.*' => ['nullable', 'regex:/^(?!([6-9])\1{9}$)[6-9][0-9]{9}$/'],
                'inst_kms' => $request->audit_mode !== 'T'
                    ? 'required|numeric|min:1|regex:/^\d{1,3}(\.\d{1,2})?$/'
                    : 'nullable|numeric|min:0',
                'inst_priority_kms' => $request->audit_mode !== 'T'
                    ? 'required|in:01,02'
                    : 'nullable',
            ], [
                'required' => 'The :attribute field is required.',
                'alpha' => 'The :attribute field must contain only letters.',
                'integer' => 'The :attribute field must be a valid number.',
                'regex' => 'The :attribute field must be valid.',
                'alpha_num' => 'The :attribute field must contain only letters and numbers.',
                'email' => 'The :attribute field must be a valid email address.',
                'reportemail.*.required' => ' report email is required.',
                'reportemail.*.email' => ' report email must be a valid email address.',
                'reportemail.*.regex' => ' Invalid Email Address',
                'reportmobile.*.required' => '  mobile number is required.',
                'reportmobile.*.integer' => '  mobile number must be a valid number.',
                'inst_kms.min' => 'Kilometre must be greater than 0',
            ]);

            if ($request->audit_mode != 'T') {
                $request->validate([
                    'categorization' => ['required', 'string', 'in:I,II,III,IV,V'],
                    'turnover' => ['required', 'regex:/^\d{1,12}(\.\d{1,2})?$/'],
                    'fees' => ['required', 'string', 'in:Y,N'],
                    'auditcertificate' => ['required', 'string', 'in:Y,N'],
                    'annadhanam_only' => ['required', 'string', 'in:Y,N'],
                    'risktype' => ['required', 'string', 'in:H,M,L'],
                    'rankorder' => 'required|string',
                ], [
                    'required' => 'The :attribute field is required.',
                    'alpha' => 'The :attribute field must contain only letters.',
                    'integer' => 'The :attribute field must be a valid number.',
                    'regex' => 'The :attribute field must be valid.',
                    'alpha_num' => 'The :attribute field must contain only letters and numbers.',
                    'email' => 'The :attribute field must be a valid email address.',
                ]);
            }

            $data = [
                'deptcode' => $request->deptcode,
                'regioncode' => $request->regioncode,
                'catcode' => $request->cat_code,
                'distcode' => $request->distcode,
                'revenuedistcode' => $request->revenuedistcode,
                'instename' => $request->instename,
                'insttname' => $request->insttname,
                'mandays' => $request->mandays,
                'risktype' => $request->risktype,
                'rankorder' => $request->rankorder,
                'hubtype' => $request->hubtypeid,
                'zonecode' => $request->zonecode,
                'hubdesigcode' => $request->hubdesigcode,
                'nodalperson_ename' => $request->nodalperson_ename,
                'nodalperson_tname' => $request->nodalperson_tname,
                'nodalperson_desigcode' => $request->nodalperson_desigcode,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'fees' => $request->fees,
                'typeofauditcode' => $request->typeofaudit,
                'categorization' => $request->categorization,
                'auditcertificate' => $request->auditcertificate,
                'annadhanam_only' => $request->annadhanam_only,
                'auditoffice' => $request->auditoffice,
                'auditofficedesignation' => $request->audit_office_desig,
                'auditaduserid' => $request->audit_ad,
                'auditeedeptcode' => $request->auditee_dept,
                'erpno' => $request->itms_no,
                'audit_quarter' => 'Q3',
                'leavedays' => $request->max_leave,
                'carryforward' => $request->cary_fwd,
                'consreport' => $request->cons_report,
                'assemblereport' => $request->assemble_report,
                'turnover' => $request->turnover,
                'statusflag' => 'Y',
                'auditeeofficeaddress' => $request->auditee_ofaddr ?? null,
                'auditmode' => $request->audit_mode,
                'teamsize' => $request->team_size,
                'parentinstid' => $request->parentinstid,
                'insttype' => $request->inst_type,
                'inst_kms' => $request->inst_kms,
                'inst_priority_kms' => $request->inst_priority_kms,
            ];

            $quarterArray = ['Q1', 'Q2', 'Q3', 'Q4'];
            $applicablefor = $request->applicablefor;

            foreach ($quarterArray as $quarter) {
                $data[$quarter] = in_array($quarter, $applicablefor) ? 'Y' : 'N';
            }

            $auditee_reportdata = [
                'auditeedeptcode' => $request->auditee_dept,
                'deptcode' => $request->deptcode,
                'auditee_report' => $request->auditee_report,
                'nodaldesignation' => $request->reportdesignation,
                'nodalemail' => $request->reportemail,
                'nodalmobile' => $request->reportmobile,
                'statusflag' => 'Y'
            ];

            if ($request->subcatid) {
                $data['subcatid'] = $request->subcatid;
            }

            if ($request->action == 'update') {
                $instid = $request->filled('instid') ? Crypt::decryptString($request->instid) : null;
                $auditeeuserid = $request->filled('auditeeuserid') ? Crypt::decryptString($request->auditeeuserid) : null;
                $apms_inst =  $request->apms_inst;
                $deptcode =  $request->deptcode;
                $olddeptcode =  $request->deptcode_hidden;
                if ($apms_inst === 'Y' && ($deptcode != $olddeptcode) && $deptcode = '02') {

                    $response = mastersmodel::update_apmsdata($instid);

                    if (!$response) {
                        return response()->json(['message' => 'APMS update failed (no response)', 'error' => 409], 409);
                    }

                    if (is_string($response)) {
                        $response = json_decode($response, true);
                    }

                    if ($response['status'] != 'success') {
                        return response()->json(['message' =>  $response['message'] ?? 'APMS update failed', 'error' => 409], 409);
                    }
                }
            } else {
                $instid = null;
                $auditeeuserid = null;
            }

            $mandaysAltered = false;
            if ($request->action == 'update' && $instid) {
                $oldInst = DB::table(self::$inst_table)
                    ->where('instid', $instid)
                    ->first();

                if ($oldInst) {
                    $alteredData = [];

                    $newMandays = $request->mandays;
                    $oldMandays = $oldInst->mandays;

                    if ($oldMandays != $newMandays) {
                        $mandaysDifference = abs($newMandays - $oldMandays);

                        if ($mandaysDifference > 5) {
                            $mandaysAltered = true;
                            $alteredData['new_mandays'] = $newMandays;
                            $alteredData['old_mandays'] = $oldMandays;
                            $alteredData['mandaysalteration'] = 'Y';
                        }
                    }

                    if ($oldInst->deptcode != $request->deptcode) {
                        $alteredData['deptcode'] = $request->deptcode;
                        $alteredData['old_deptcode'] = $oldInst->deptcode;
                    } else {
                        $alteredData['deptcode'] = $request->deptcode;
                        $alteredData['old_deptcode'] = $oldInst->deptcode;
                    }

                    if ($oldInst->catcode != $request->cat_code) {
                        $alteredData['catcode'] = $request->cat_code;
                        $alteredData['old_catcode'] = $oldInst->catcode;
                    } else {
                        $alteredData['catcode'] = $request->cat_code;
                        $alteredData['old_catcode'] = $oldInst->catcode;
                    }

                    $newSubcat = $request->subcatid ?? null;
                    if ($oldInst->subcatid != $newSubcat) {
                        $alteredData['subcatid'] = $newSubcat;
                        $alteredData['old_subcatid'] = $oldInst->subcatid;
                    } else {
                        $alteredData['subcatid'] = $newSubcat;
                        $alteredData['old_subcatid'] = $oldInst->subcatid;
                    }

                    $alteredData['instid'] = $instid;
                    $alteredData['createdby'] = $userid;
                    $alteredData['createdon'] = View::shared('get_nowtime');
                    $alteredData['updatedby'] = $userid;
                    $alteredData['updatedon'] = View::shared('get_nowtime');

                    if ($oldInst->deptcode != $request->deptcode ||
                            $oldInst->catcode != $request->cat_code ||
                            $oldInst->subcatid != $newSubcat ||
                            $mandaysAltered) {
                        DB::table('audit.altered_institutiondetails')->insert($alteredData);
                    }
                }
            }

            $institutedet = MastersModel::institute_insertupdate($auditee_reportdata, $data, $instid, self::$inst_table, $userid, $auditeeuserid);

            return response()->json(['success' => true, 'message' => 'inst_inserte']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public static function fetchdept()
    {
        $dept = MastersModel::FetchDepartment();
        return view('masters.createsubcategory', compact('dept'));
    }

    public function subcategory_insertupdate(Request $request)
    {
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'category' => 'required|string|max:5',
                'subcatename' => 'required|string|max:200',
                'subcattname' => 'required|string|max:200',
                'status' => 'required|in:Y,N',
            ];

            // Create a validator instance
            $validator = Validator::make($request->all(), $rules);

            // If validation fails, throw an exception with a single message
            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }
            // , [
            //     'deptcode.required' => 'The Department Code field is required.',
            //     'category.required' => 'The Category Name field is required.',
            //     'subcatename.required' => 'The Tamil Objection Name field is required.',
            //     'subcattname.required' => 'The Tamil Objection Name field is required.',
            //     'status.required' => 'The Status Flag field is required.',
            //     'status.in' => 'The Status Flag must be either "Y" or "N".',
            // ]);
            $subcategory = session('charge');
            if (!$subcategory || !isset($subcategory->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $subcategory->userchargeid;
            $subcategoryid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('subcatgeoryid')) : null;

            $ename = strtolower(str_replace(' ', '', $request->subcatename));
            $tname = strtolower(str_replace(' ', '', $request->subcattname));
            $categorycode = $request->category;

            // Check for duplicates
            $duplicateCheck = MastersModel::checkDuplicateForsubcat($categorycode, $ename, $tname, $subcategoryid);

            if ($duplicateCheck['ename'] && $duplicateCheck['tname']) {
                return response()->json(['success' => false, 'message' => 'DuplicationsubcatETname'], 422);
            } elseif ($duplicateCheck['ename']) {
                return response()->json(['success' => false, 'message' => 'DuplicationsubcatEname'], 422);
            } elseif ($duplicateCheck['tname']) {
                return response()->json(['success' => false, 'message' => 'DuplicationsubcatTname'], 422);
            }

            $data = [
                'statusflag' => $request->status,
                'subcatename' => $request->subcatename ?? null,
                'subcattname' => $request->subcattname ?? null,
                'catcode' => $request->category ?? null,
            ];

            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::subcategory_insertupdate($data, $subcategoryid, 'audit.mst_auditeeins_subcategory');
            return response()->json(['success' => true, 'message' => 'subcategory_created']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function subcategory_fetchData(Request $request)
    {
        $subcategorydid = $request->has('auditeeins_subcategoryid') ? Crypt::decryptString($request->auditeeins_subcategoryid) : null;
        $subcategory = MastersModel::getSubcategoryforrecords($subcategorydid, 'audit.mst_auditeeins_subcategory');
        foreach ($subcategory as $all) {
            $all->encrypted_auditeeins_subcategoryid = Crypt::encryptString($all->auditeeins_subcategoryid);
            unset($all->auditeeins_subcategoryid);
        }
        return response()->json([
            'success' => !$subcategory->isEmpty(),
            'message' => $subcategory->isEmpty() ? 'User not found' : '',
            'data' => $subcategory->isEmpty() ? null : $subcategory
        ], $subcategory->isEmpty() ? 404 : 200);
    }

    public function check_mapAllcObj(Request $request)
    {
        $tabledata = $request->tabledata;
        $tabledata = json_decode($tabledata, true);

        $check_existence = MastersModel::check_mapAllcObj($tabledata);
        return response()->json(['success' => true, 'exist_array' => $check_existence]);
    }

    public static function mappingworkallocationdeptfetch()
    {
        $dept = MastersModel::model_workallocationdeptfetch();

        return view('masters.mappingworkallocation', compact('dept'));
    }

    public static function mappingobjectiondeptfetch()
    {
        $dept = MastersModel::model_workallocationdeptfetch();

        return view('masters.mappingobjection', compact('dept'));
    }

    public function subworkallocation_mapping(Request $request)
    {
        // Retrieve the necessary inputs from the request
        $subworkallocationid = $request->input('subworkallocationid');  // This should be an array
        $majorworkallocationid = $request->input('majorworkallocationid');  // This should be a single value

        // Ensure that both parameters are provided and are valid
        if (empty($subworkallocationid) || empty($majorworkallocationid)) {
            return response()->json(['error' => 'Invalid input: subworkallocationid and majorworkallocationid are required'], 400);
        }

        try {
            // Call the model's method to update the subworkallocation
            $result = MastersModel::updatesubworkallocation($subworkallocationid, $majorworkallocationid);

            // Return a success response (adjust as needed)
            return response()->json(['success' => true, 'message' => 'Subwork allocation updated successfully.']);
        } catch (\Exception $e) {
            // If any exception occurs, return a failure response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function subobjectionupdate_mapping(Request $request)
    {
        // print_r($request->all());
        // Retrieve the necessary inputs from the request
        $mainobjectionid = $request->input('mainobjectionid');  // This should be an array
        $subobjectiond = $request->input('subobjectionid');  // This should be a single value

        // Ensure that both parameters are provided and are valid
        if (empty($subobjectiond) || empty($mainobjectionid)) {
            return response()->json(['error' => 'Invalid input: subworkallocationid and majorworkallocationid are required'], 400);
        }

        try {
            // Call the model's method to update the subworkallocation
            $result = MastersModel::updateobjection($subobjectiond, $mainobjectionid);

            // Return a success response (adjust as needed)
            return response()->json(['success' => true, 'message' => 'Subwork allocation updated successfully.']);
        } catch (\Exception $e) {
            // If any exception occurs, return a failure response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function subobjection_fetchData(Request $request)
    {
        $subobjectionid = $request->has('subobjectionid') ? Crypt::decryptString($request->subobjectionid) : null;
        $chargedel = MastersModel::fetchsubobjectionData($subobjectionid, 'audit.mst_subobjection');
        foreach ($chargedel as $all) {
            $all->encrypted_subobjectionid = Crypt::encryptString($all->subobjectionid);
        }
        return response()->json([
            'success' => !$chargedel->isEmpty(),
            'message' => $chargedel->isEmpty() ? 'User not found' : '',
            'data' => $chargedel->isEmpty() ? null : $chargedel
        ], $chargedel->isEmpty() ? 404 : 200);
    }

    public function getobjectionBasedOnDept(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');

        $objection = MastersModel::getobjectionByDept($deptcode);
        $subobjection = MastersModel::getsubobjectionbasedondept($deptcode);

        if ($objection->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $objection, 'subobjection' => $subobjection]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function getworkallocationBasedOnDept(Request $request)
    {
        $deptcode = $request->input('deptcode');

        if (!$deptcode) {
            return response()->json([
                'success' => false,
                'message' => 'Department code is required.'
            ], 400);
        }

        // Fetch work allocations based on the department code
        $workallocation = MastersModel::getworkallocationByDept($deptcode);
        $subworkallocation = MastersModel::getsubworkallocationbasedondept($deptcode);

        if (!$workallocation || $workallocation->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No work allocation found.',
                'data' => []
            ]);
        }

        // ✅ Correctly return JSON response (remove print_r)
        return response()->json([
            'success' => true,
            'data' => $workallocation,
            'subworkallocation' => $subworkallocation
        ]);
    }

    // ---------------------------------------------------Audit Inspection------------------------------------------------------------------

    public static function fetchdeptforauditinspection($index)
    {
        $dept = MastersModel::commondeptfetch();

        return view($index, compact('dept'));
    }

    public function auditinspectform_insertupdate(Request $request)
    {
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'desigcode' => 'required|string|regex:/^\d+$/',
                'category' => 'required|string|regex:/^[\dA]+$/',
                'heading_en' => 'required|array|max:10',
                'heading_en.*' => 'required|string|max:400',
                'heading_ta' => 'required|array|max:10',
                'heading_ta.*' => 'required|string|max:400',
                'part_no' => 'required|array',
                'part_no.*' => 'required|integer|digits_between:1,9',
                'checkpoint_en' => 'required|array|max:10',
                'checkpoint_en.*' => 'required|string|max:400',
                'checkpoint_ta' => 'required|array|max:10',
                'checkpoint_ta.*' => 'required|string|max:400',
                'statusflag' => 'required|array|in:Y,N',
                'statusflag.*' => 'in:Y,N',
            ];

            if ($request->input('if_subcategory') === 'Y') {
                $rules['subcategory'] = 'required|array';
                $rules['subcategory.*'] = 'required|regex:/^[\dA]+$/';
            } else {
                $rules['subcategory'] = 'nullable|array';
                $rules['subcategory.*'] = 'nullable|regex:/^[\dA]+$/';
            }

            // return $request;

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return $validator->error();

                // throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            // return 'sda';
            $finalData = [];

            if ($request->category == 'A') {
                $categoriesdata = MastersModel::getCategoryforinspection($request->deptcode);

                // mapping: categoryId => [subcategoryId1, subcategoryId2, ...] or empty array

                foreach ($categoriesdata as $category) {
                    $catId = $category->catcode;
                    $subcategories = MastersModel::getSubcategoryByCategoryforinspection($catId);

                    $subcatIds = [];
                    foreach ($subcategories as $subcat) {
                        $subcatIds[] = $subcat->auditeeins_subcategoryid;
                    }

                    $finalData[$catId] = $subcatIds;  // even if empty
                }
            } else {
                $catId = $request->category;
                $subcatIds = $request->subcategory ?? [];
                $finalData[$catId] = $subcatIds;
            }
            // return $finalData;

            $auditinspect = session('user');
            if (!$auditinspect || !isset($auditinspect->userid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $auditinspect->userid;
            $auditinspectid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('aifid')) : null;

            $counts = [
                count($request->heading_en),
                count($request->heading_ta),
                count($request->part_no),
                count($request->checkpoint_en),
                count($request->checkpoint_ta),
                // count($request->question_type),
            ];

            if (count(array_unique($counts)) !== 1) {
                return response()->json(['success' => false, 'message' => 'All field arrays must be of equal length.'], 422);
            }

            // $subcategories = $request->subcategory ?? [];

            $entryCount = $counts[0];
            $dataList = [];

            $dataList = [];

            foreach ($finalData as $categoryId => $subcatIds) {
                if (!empty($subcatIds)) {
                    // Loop through subcategories
                    foreach ($subcatIds as $subcategoryId) {
                        for ($i = 0; $i < $entryCount; $i++) {
                            $data = [
                                'deptcode' => $request->deptcode,
                                'desigcode' => $request->desigcode,
                                'catcode' => $categoryId,
                                'subcatid' => $subcategoryId,
                                'heading_en' => $request->heading_en[$i],
                                'heading_ta' => $request->heading_ta[$i],
                                'partno' => $request->part_no[$i],
                                'checkpoint_en' => $request->checkpoint_en[$i],
                                'checkpoint_ta' => $request->checkpoint_ta[$i],
                                'objectiontype' => 'O',
                                'statusflag' => isset($request->statusflag[$i]) ? $request->statusflag[$i] : 'N',
                            ];

                            $existingRecord = MastersModel::checkinspectionForDuplicate($data, $auditinspectid);
                            if ($existingRecord) {
                                return response()->json(['success' => false, 'message' => 'Duplicate record(s) found. No records were inserted.'], 422);
                            }

                            $dataList[] = $data;
                        }
                    }
                } else {
                    // No subcategories – insert using category only
                    for ($i = 0; $i < $entryCount; $i++) {
                        $data = [
                            'deptcode' => $request->deptcode,
                            'desigcode' => $request->desigcode,
                            'catcode' => $categoryId,
                            'subcatid' => null,  // or '' based on your DB requirement
                            'heading_en' => $request->heading_en[$i],
                            'heading_ta' => $request->heading_ta[$i],
                            'partno' => $request->part_no[$i],
                            'checkpoint_en' => $request->checkpoint_en[$i],
                            'checkpoint_ta' => $request->checkpoint_ta[$i],
                            'objectiontype' => 'O',
                            'statusflag' => isset($request->statusflag[$i]) ? $request->statusflag[$i] : 'N',
                        ];

                        $existingRecord = MastersModel::checkinspectionForDuplicate($data, $auditinspectid);
                        if ($existingRecord) {
                            return response()->json(['success' => false, 'message' => 'Duplicate record(s) found. No records were inserted.'], 422);
                        }

                        $dataList[] = $data;
                    }
                }
            }
            // return $dataList;
            // You can now insert $dataList using bulk insert or loop.

            foreach ($dataList as $data) {
                if ($request->input('action') === 'insert') {
                    $data['createdon'] = View::shared('get_nowtime');
                    $data['createdby'] = $userchargeid;
                    $data['updatedon'] = View::shared('get_nowtime');
                    $data['updatedby'] = $userchargeid;
                }

                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;

                MastersModel::auditinspect_insertupdate($data, $auditinspectid, 'audit.mst_auditinspection');
            }

            return response()->json(['success' => true, 'message' => 'auditinspect_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function auditinspectform_fetchData(Request $request)
    {
        $deptcode = $request->input('department');
        $catcode = $request->input('catcode');
        $subcatecode = $request->input('subcatecode');

        $aifid = $request->has('aifid') ? Crypt::decryptString($request->aifid) : null;
        $auditinspection = MastersModel::auditinspectform_fetchData($aifid, $deptcode, $catcode, $subcatecode);
        // print_r($workallocation);
        foreach ($auditinspection as $all) {
            $all->encrypted_aifid = Crypt::encryptString($all->aifid);

            unset($all->aifid);
        }
        return response()->json([
            'success' => !$auditinspection->isEmpty(),
            'message' => $auditinspection->isEmpty() ? 'Super Check not found' : '',
            'data' => $auditinspection->isEmpty() ? null : $auditinspection
        ], 200);
    }

    public function getsubcatbasedoncategoryinspection(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');

        $subcategory = MastersModel::getSubcategoryByCategoryforinspection($category);

        return response()->json($subcategory);
    }

    public function getCategoriesBasedOnDeptforinspection(Request $request)
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

        $category = MastersModel::getcategoryByDeptauditinspect($deptcode);
        $designation = MastersModel::getdesignationByDeptauditinspect($deptcode);

        return response()->json([
            'categorie' => $category,
            'designation' => $designation
        ]);
    }

    public static function templateslipdeptfetch()
    {
        $dept = MastersModel::commondeptfetch();

        return view('masters.templateslip', compact('dept'));
    }

    public function getcategoriesbasednndeptfortemplateslip(Request $request)
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

        $category = MastersModel::commoncategoryfetch($deptcode);

        if ($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No Category found'], 404);
        }
    }

    public function getsubcategoriesbasedncatfortemplateslip(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');

        $subcategory = MastersModel::commmonSubcategoryByCategory($category);

        return response()->json($subcategory);
    }

    public function templateslip_insertupdate(Request $request)
    {
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'category' => 'required|string|regex:/^\d+$/',
                'templateparaename' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (trim(strip_tags($value)) === '') {
                            $fail("The $attribute field cannot be empty.");
                        }
                    }
                ],
                'templateparatname' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (trim(strip_tags($value)) === '') {
                            $fail("The $attribute field cannot be empty.");
                        }
                    }
                ],
                'statusflag' => 'required|in:Y,N',
            ];

            if ($request->input('if_subcategory') === 'Y') {
                $rules['subcategory'] = 'required|array';
                $rules['subcategory.*'] = 'required|integer';
            } else {
                $rules['subcategory'] = 'nullable|array';
                $rules['subcategory.*'] = 'nullable|integer';
            }

            // $validator = Validator::make($request->all(), $rules);

            // if ($validator->fails()) {
            //     throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            // }

            $templateslip = session('charge');
            if (!$templateslip || !isset($templateslip->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }

            $userchargeid = $templateslip->userchargeid;
            $templateslipid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('tempslipid')) : null;

            $templateparaename = $request->input('templateparaename');
            $templateparatname = $request->input('templateparatname');

            $templateparaenameJson = json_encode([
                'content' => html_entity_decode($templateparaename, ENT_QUOTES, 'UTF-8')
            ]);

            $templateparatnameJson = json_encode([
                'content' => html_entity_decode($templateparatname, ENT_QUOTES, 'UTF-8')
            ]);

            $subcategories = $request->subcategory ?? [];

            $dataList = [];

            foreach ($subcategories as $subcategory) {
                $data = [
                    'deptcode' => $request->deptcode,
                    'catcode' => $request->category,
                    'auditeeins_subcategoryid' => $subcategory,
                    'templateparaename' => $templateparaenameJson,
                    'templateparatname' => $templateparatnameJson,
                    'statusflag' => $request->statusflag,
                    'updatedon' => View::shared('get_nowtime'),
                    'updatedby' => $userchargeid
                ];

                //  $existingRecord = MastersModel::checkForDuplicatetemplateslip($data, $templateslipid);
                // if ($existingRecord) {
                //     return response()->json([
                //         'success' => false,
                //         'message' => 'Duplicate record(s) found. No records were inserted.'
                //     ], 422);
                // }

                $dataList[] = $data;
            }

            foreach ($dataList as $data) {
                if ($request->input('action') === 'insert') {
                    $data['createdon'] = View::shared('get_nowtime');
                    $data['createdby'] = $userchargeid;
                }

                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;

                MastersModel::Fortemplateslip_insertupdate($data, $templateslipid, 'audit.audit_templateslip');
            }

            return response()->json(['success' => true, 'message' => 'Template Slip Creates Successfully']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function templateslip_fetchData(Request $request)
    {
        $tempslipid = $request->has('tempslipid') ? Crypt::decryptString($request->tempslipid) : null;
        $templateslip = MastersModel::templateslip_fetch($tempslipid, 'audit.audit_templateslip');

        foreach ($templateslip as $all) {
            $all->encrypted_tempslipid = Crypt::encryptString($all->tempslipid);

            unset($all->tempslipid);
        }
        return response()->json([
            'success' => true,
            'message' => $templateslip->isEmpty() ? 'No schemes found' : '',
            'data' => $templateslip
        ], 200);
    }

    public function deptfetchfornonaudithub()
    {
        $dept = MastersModel::commondeptfetch();

        return view('masters.createnonauditablehub', compact('dept'));
    }

    public function getcirclezonebasedonregiofornonaudit(Request $request)
    {
        // Validate the input
        $request->validate(
            [
                'commonzone' => ['required', 'string', 'regex:/^\d+$/'],
            ],
            [
                'commonzone.required' => 'The region field is required.',
                'commonzone.regex' => 'The region field must be a valid number.',
            ]
        );

        $commonzone = $request->input('commonzone');

        $circlezone = MastersModel::getcirclezonebasedonregiofornonaudit($commonzone);

        if ($circlezone->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $circlezone]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Circle Zone found'], 200);
        }
    }

    public function getcommonzonebasedonregiofornonaudit(Request $request)
    {
        // Validate the input
        $request->validate(
            [
                'distcode' => ['required', 'string', 'regex:/^\d+$/'],
            ],
            [
                'distcode.required' => 'The region field is required.',
                'distcode.regex' => 'The region field must be a valid number.',
            ]
        );

        $distcode = $request->input('distcode');

        $commonzone = MastersModel::getcommonzonebasedonregiofornonaudit($distcode);

        if ($commonzone->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $commonzone]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Common Zone found'], 200);
        }
    }

    public function Fornonaudithubgetregionbasedondept(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');

        $regions = MastersModel::commonRegionsByDept($deptcode);
        $designation = MastersModel::FornonAuditgetdesignationByDept($deptcode);

        return response()->json([
            'success' => true,
            'regions' => $regions,
            'desig' => $designation,
        ]);
    }

    public function getdistrictbasedonregiofornonaudit(Request $request)
    {
        // Validate the input
        $request->validate(
            [
                'region' => ['required', 'string', 'regex:/^\d+$/'],
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            ],
            [
                'region.required' => 'The region field is required.',
                'region.regex' => 'The region field must be a valid number.',
                'deptcode.required' => 'The deptcode field is required.',
                'deptcode.regex' => 'The deptcode field must be a valid number.',
            ]
        );

        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');

        $district = MastersModel::commondistrictByregion($regioncode, $deptcode);

        if ($district->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $district]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function nonaudithub_insertupdate(Request $request)
    {
        $chargedel = session('charge');

        if (in_array($chargedel->roletypecode, [View::shared('Re_roletypecode'), View::shared('Ho_roletypecode'), View::shared('Dist_roletypecode')])) {
            $deptcode = $chargedel->deptcode;

            $request->merge(['deptcode' => $deptcode]);
        }

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'regioncode' => 'required|string|regex:/^\d+$/',
                'distcode' => 'required|string|regex:/^\d+$/',
                'hubdesigcode' => 'required|string|regex:/^\d+$/',
                'email' => 'required|email|max:50',
                'commonzone' => 'nullable|string|regex:/^\d+$/',
                'circlezone' => 'nullable|integer',
                'mobile' => ['required', 'regex:/^[6-9]\d{9}$/'],
                'address' => 'required|string|max:200',
                // 'loginrequired' => 'required|in:Y,N',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $nonaudit = session('user');
            if (!$nonaudit || !isset($nonaudit->userid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userid = $nonaudit->userid;
            $hubid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('hubid')) : null;

            $data = [
                'deptcode' => $request->deptcode ?? null,
                'regioncode' => $request->regioncode ?? null,
                'distcode' => $request->distcode ?? null,
                'desigcode' => $request->hubdesigcode ?? null,
                'address' => $request->address ?? null,
                'email' => $request->email ?? null,
                'mobilenumber' => $request->mobile ?? null,
                'zonecode' => $request->commonzone ?? null,
                'circleid' => $request->circlezone ?? null,
                // 'loginrequired' => $request->loginrequired,
                'statusflag' => $request->statusflag,
            ];
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userid;
            }

            $result = MastersModel::Nonauditablehub_insertupdate($data, $hubid, 'audit.mst_nonaudit_hub');
            return response()->json(['success' => true, 'message' => 'nonauditablehub_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function nonaudithub_fetchData(Request $request)
    {
        $hubid = $request->has('hubid') ? Crypt::decryptString($request->hubid) : null;
        $auditablehub = MastersModel::fetchauditablehubData($hubid, 'audit.mst_nonaudit_hub');
        foreach ($auditablehub as $all) {
            $all->encrypted_hubid = Crypt::encryptString($all->hubid);
            unset($all->hubid);
        }
        return response()->json([
            'success' => !$auditablehub->isEmpty(),
            'message' => $auditablehub->isEmpty() ? 'User not found' : '',
            'data' => $auditablehub->isEmpty() ? null : $auditablehub
        ], $auditablehub->isEmpty() ? 404 : 200);
    }

    public function deptfetchforhub()
    {
        $dept = MastersModel::commondeptfetch();

        return view('masters.createdesignationforhub', compact('dept'));
    }

    public function hubdesignation_insertupdate(Request $request)
    {
        try {
            $chargedel = session('charge');

            if (in_array($chargedel->roletypecode, [View::shared('Re_roletypecode'), View::shared('Ho_roletypecode'), View::shared('Dist_roletypecode')]))
                $deptcode = $chargedel->deptcode;

            $request->merge(['deptcode' => $deptcode]);

            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'desigename' => 'required|string|max:200',
                'desigtname' => 'required|string|max:200',
                'statusflag' => 'required|in:Y,N',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            }

            $hubdesig = session('user');
            if (!$hubdesig || !isset($hubdesig->userid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }
            $userid = $hubdesig->userid;

            $hubdesigid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('hubdesigid')) : null;

            $data = [
                'deptcode' => $request->deptcode ?? null,
                'statusflag' => $request->statusflag,
                'desigename' => $request->desigename ?? null,
                'desigtname' => $request->desigtname ?? null,
            ];

            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userid;
            }
            $result = MastersModel::createhubdesignation_insertupdate($data, $hubdesigid, 'audit.mst_hubdesig');
            return response()->json(['success' => true, 'message' => 'hubdesignation_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function hubdesignation_fetchData(Request $request)
    {
        $hubdesigid = $request->has('hubdesigid') ? Crypt::decryptString($request->hubdesigid) : null;
        $hubdesignation = MastersModel::fetchhubdesignationData($hubdesigid, 'audit.mst_hubdesig');
        foreach ($hubdesignation as $all) {
            $all->encrypted_hubdesigid = Crypt::encryptString($all->hubdesigid);
            unset($all->hubdesigid);
        }
        return response()->json([
            'success' => !$hubdesignation->isEmpty(),
            'message' => $hubdesignation->isEmpty() ? 'User not found' : '',
            'data' => $hubdesignation->isEmpty() ? null : $hubdesignation
        ], $hubdesignation->isEmpty() ? 404 : 200);
    }

    public function alterinst_compact()
    {
        $auditmode = MastersModel::getauditmodeDetails();
        $quarterdetails = MastersModel::getoldandnewquarter();
        $inst_quarter = MastersModel::getquartersforinst();
        $dept = MastersModel::commondeptfetch();
        $data = '';

        return view('masters.alterinstitution', compact('dept', 'auditmode', 'quarterdetails', 'inst_quarter'));
    }

    public function fetch_alterinst(Request $request)
    {
        try {
            $deptcode = $request->input('deptcode');
            $quarter = $request->input('quarter');
            $auditmode = $request->input('filter_auditmode');

            $get_alterinstdet = MastersModel::get_alterinstdet($deptcode, $quarter, $auditmode);

            foreach ($get_alterinstdet as $all) {
                $all->encrypted_instid = Crypt::encryptString($all->instid);
                if ($all->detailid != NULL) {
                    $all->encrypted_detailid = Crypt::encryptString($all->detailid);
                } else {
                    $all->encrypted_detailid = NULL;
                }
                // unset($all->instid);
            }

            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $get_alterinstdet->isEmpty() ? null : $get_alterinstdet,
            ], 200);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid  ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }

    public function fetch_singlealterinst(Request $request)
    {
        try {
            $instid = $request->filled('instid') ? Crypt::decryptString($request->instid) : null;
            $detailid = $request->filled('detailid') ? Crypt::decryptString($request->detailid) : null;

            $deptcode = $request->input('deptcode');
            $quarter = $request->input('quarter');
            $regioncode = $request->input('regioncode');
            $distcode = $request->input('distcode');

            $cond = [
                'instid' => $instid,
                'deptcode' => $request->input('deptcode'),
                'quarter' => $request->input('quarter'),
                'regioncode' => $request->input('regioncode'),
                'distcode' => $request->input('distcode'),
            ];

            $get_alterinstdet = MastersModel::get_singlealterinstdet($deptcode, $quarter, $instid, $regioncode, $distcode, $cond, $detailid);

            foreach ($get_alterinstdet['instdata'] as $all) {
                $all->encrypted_instid = Crypt::encryptString($all->instid);
                if ($all->detailid != NULL) {
                    $all->encrypted_detailid = Crypt::encryptString($all->detailid);
                } else {
                    $all->encrypted_detailid = NULL;
                }
                // unset($all->instid);
            }

            return response()->json([
                'success' => true,
                'message' => 'sdf',
                'data' => $get_alterinstdet['instdata']->isEmpty() ? null : $get_alterinstdet['instdata'],
                'paraentinst' => $get_alterinstdet['parentinsdet'],
                'hubdesidetails' => $get_alterinstdet['hubdesidetails']
            ], 200);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid  ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                //  'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }

    public function insertorupdate_alterInst(Request $request)
    {
        //   dd($request->all());
        try {
            // return $request->all();
            $sessiondet = session('user');
            $userid = $sessiondet->userid;

            $request->validate([
                'old_auditmode' => ['nullable', 'string', 'in:N,C,T,Q'],
                'old_nodalperson_ename' => 'nullable|string|max:75',
                'old_nodalperson_tname' => 'nullable|string|max:75',
                'old_email' => ['nullable', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
                'old_mobile' => ['nullable', 'regex:/^(?!([6-9])\1{9}$)[6-9][0-9]{9}$/'],
                'mandays' => 'nullable|integer|min:1',
                'team_size' => 'nullable|integer|min:2|max:10',
                'audit_mode' => ['required', 'string', 'in:N,C,T,Q'],
                'nodalperson_ename' => 'nullable|string|max:75',
                'nodalperson_tname' => 'nullable|string|max:75',
                'email' => ['nullable', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
                'mobile' => ['nullable', 'regex:/^(?!([6-9])\1{9}$)[6-9][0-9]{9}$/']
            ], [
                'required' => 'The :attribute field is required.',
                'alpha' => 'The :attribute field must contain only letters.',
                'integer' => 'The :attribute field must be a valid number.',
                'regex' => 'The :attribute field must be valid.',
                'alpha_num' => 'The :attribute field must contain only letters and numbers.',
                'email' => 'The :attribute field must be a valid email address.',
                'min' => 'The :attribute must be at least :min.',
                'max' => 'The :attribute may not be greater than :max.',
            ]);

            $action = $request->action;
            $instid = Crypt::decryptString($request->instid);
            $data = [
                'instid' => $instid,
                'old_nodalperson_ename' => $request->old_nodalperson_ename,
                'old_nodalperson_tname' => $request->old_nodalperson_tname,
                'old_nodalperson_desigcode' => $request->old_nodalperson_desigcode,
                'old_email' => $request->old_email,
                'old_mobile' => $request->old_mobile,
                'old_auditmode' => $request->old_auditmode,
                'old_parentinstid' => $request->old_parentinstid,
                'old_insttype' => $request->old_insttype,
                'old_hubtype' => $request->old_hubtype,
                'old_hubdesigcode' => $request->old_hubdesigcode,
                'old_auditeeaddr' => $request->old_auditeeaddr ?? null,
                'old_mandays' => $request->old_mandays ?? null,
                'old_teamsize' => $request->old_teamsize ?? null,
                'new_mandays' => $request->mandays ?? null,
                'new_teamsize' => $request->team_size ?? null,
                'new_nodalperson_ename' => $request->nodalperson_ename,
                'new_nodalperson_tname' => $request->nodalperson_tname,
                'new_nodalperson_desigcode' => $request->nodalperson_desigcode,
                'new_email' => $request->email,
                'new_mobile' => $request->mobile,
                'new_auditeeofficeaddress' => $request->auditee_ofaddr ?? null,
                'new_auditmode' => $request->audit_mode,
                'new_parentinstid' => $request->parentinstid,
                'new_insttype' => $request->inst_type,
                'new_hubtype' => $request->hubtypeid,
                'new_hubdesigcode' => $request->hubdesigcode,
                'statusflag' => $request->statusflag,
            ];

            // $data['new_quarter'] = $request->applicablefor;
            //             $quarterArray = ['Q1', 'Q2', 'Q3', 'Q4'];
            //             $applicablefor = $request->applicablefor;

            // $
            //  dd($request->old_quarter);
            $quarterArray = ['Q1', 'Q2', 'Q3', 'Q4'];
            $applicablefor = $request->applicablefor;

            $instdata = [];

            foreach ($quarterArray as $quarter) {
                $instdata[$quarter] = in_array($quarter, $applicablefor) ? 'Y' : 'N';
            }

            $quarters = collect($request->applicablefor)->pluck(null)->all();
            $data['new_quarter'] = '{' . implode(',', $quarters) . '}';
            $data['old_quarter'] = '{' . implode(',', $request->old_quarter) . '}';

            if ($request->input('action') == 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userid;
            }

            //     dd($instdata);
            $detailid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('detailid')) : null;
            // return $detailid;
            $get_inserteddata = MastersModel::insertorupdate_alterInst($data, $detailid, $instdata);

            return response()->json([
                'success' => true,
                'message' => 'Data has been submitted successfully',
            ], 200);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid  ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                //  'message' => 'An error occurred while fetching user data'
            ], 500);
        }
    }

    public static function fetchdeptforhologindetails()
    {
        $dept = MastersModel::commondeptfetch();

        return view('masters.hologindetails', compact('dept'));
    }

    public function getCategoriesBasedOnDeptforhologin(Request $request)
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

        $category = MastersModel::getcategoryByDeptsupercheck($deptcode);

        $designation = MastersModel::getdesignationByDeptauditee($deptcode);
        $auditeedepartment = MastersModel::getDeptauditeedeptbydeptcode($deptcode);

        if ($category->isNotEmpty()) {
            // Return both categories and designations as JSON
            return response()->json([
                'success' => true,
                'categories' => $category,
                'designations' => $designation,
                'auditeedepartment' => $auditeedepartment,
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Category found'], 404);
        }
    }

    public function getsubcatbasedoncategoryhologin(Request $request)
    {
        $request->validate([
            'category' => ['required', 'string', 'regex:/^(A|\d+)(,(A|\d+))*$/']
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');

        $subcategory = MastersModel::getSubcategoryByCategoryforsupercheck($category);

        return response()->json($subcategory);
    }

    public function getinstbasedonsubcategoryhologin(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'catcode' => ['required', 'string', 'regex:/^(A|\d+)$/'],
            'subcatcode' => ['nullable', 'array'],
            'subcatcode.*' => ['regex:/^(A|\d+)$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');
        $catcode = $request->input('catcode');
        $subcatcode = $request->input('subcatcode');

        $institution = MastersModel::getinstbasedonsubcategoryauditee($deptcode, $catcode, $subcatcode);

        return response()->json($institution);
    }

    public function hologindetails_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'auditeedepartment' => 'required|string|regex:/^\d+$/',
                'category' => 'required|string|regex:/^(A|\d+)$/',
                'instmappingcode' => 'required|integer',
                'designation' => 'required|string|max:2',
                'email' => 'required|string|max:100',
                'mobile' => 'required|string|max:10',
                'statusflag' => 'required|in:Y,N',
            ];

            if ($request->input('if_subcategory') === 'Y') {
                $rules['subcategory'] = 'required|array';
                $rules['subcategory.*'] = ['required', 'string', 'regex:/^(\d+|A)$/'];
            } else {
                $rules['subcategory'] = 'nullable|array';
                $rules['subcategory.*'] = ['nullable', 'string', 'regex:/^(\d+|A)$/'];
            }
            // $validator = Validator::make($request->all(), $rules);

            // if ($validator->fails()) {
            //     throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            // }

            $auditeedept = session('charge');
            if (!$auditeedept || !isset($auditeedept->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $auditeedept->userchargeid;
            $auditeedeptid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('auditeedeptid')) : null;

            $categories = $request->input('category');
            $key = '1';

            if (is_array($categories)) {
                $category = json_encode([$key => $categories]);
            } else {
                $category = json_encode([$key => [$categories]]);
            }

            $instids = $request->input('instmappingcode');
            $key = '1';

            if (is_array($categories)) {
                $instid = json_encode([$key => $instids]);
            } else {
                $instid = json_encode([$key => [$instids]]);
            }

            $subcategoryArray = $request->input('subcategory');
            $key = '1';

            if ($subcategoryArray !== null) {
                if (!is_array($subcategoryArray)) {
                    $subcategoryArray = [$subcategoryArray];
                }
                $subcategoryJsonObj = [
                    $key => $subcategoryArray
                ];
                $subcategoryJson = json_encode($subcategoryJsonObj);
            } else {
                $subcategoryJson = null;
            }

            $data = [
                'deptcode' => $request->deptcode ?? null,
                'auditeedepartment' => $request->auditeedepartment ?? null,
                'profile_update' => 'Y',
                'pwd' => Hash::make(View::shared('auditeedefaultPass')),
                'catcode' => $category,
                'catcode' => $category,
                'auditeeins_subcategoryid' => $subcategoryJson,
                'instid' => $instid,
                'designation' => $request->desigcode ?? null,
                'email' => $request->email ?? null,
                'mobilenumber' => $request->mobile ?? null,
                'statusflag' => $request->statusflag,
            ];
            // print_r($data);
            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            $result = MastersModel::hologindetails_insertupdate($data, $auditeedeptid, 'audit.auditee_dept');
            return response()->json(['success' => true, 'message' => 'hodetails_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function hologindetails_fetchData(Request $request)
    {
        $auditeedeptid = $request->has('auditeedeptid') ? Crypt::decryptString($request->auditeedeptid) : null;
        $auditeedept = MastersModel::hologindetails_fetchData($auditeedeptid, 'audit.auditee_dept');

        foreach ($auditeedept as $all) {
            $all->encrypted_auditeedeptid = Crypt::encryptString($all->auditeedeptid);

            unset($all->auditeedeptid);
        }
        return response()->json([
            'success' => true,
            'message' => $auditeedept->isEmpty() ? 'No schemes found' : '',
            'data' => $auditeedept
        ], 200);
    }

    public static function audthorityofauditdeptfetch()
    {
        $dept = MastersModel::commondeptfetch();

        return view('masters.authorityofaudit2', compact('dept'));
    }

    public function getcategoriesbasednndeptforcommon(Request $request)
    {
        // Validate the input
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');

        $category = MastersModel::commoncategoryfetch($deptcode);

        if ($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No Category found'], 404);
        }
    }

    public function getsubcategoriesbasedncatforcommon(Request $request)
    {
        $request->validate([
            // 'category' => ['required', 'array'],
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');

        $subcategory = MastersModel::commmonSubcategoryByCategory($category);

        return response()->json($subcategory);
    }

    public function getinstituionbasedonsubcategorycommon(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'category' => ['required', 'string', 'regex:/^(\d+|A)$/'],
            'subcatcode' => ['nullable', 'regex:/^(\d+|A)$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');
        $deptcode = $request->input('deptcode');
        $subcatcode = $request->input('subcatcode');

        $institution = MastersModel::getinstituionbasedonsubcategory($deptcode, $category, $subcatcode);

        return response()->json($institution, 200);
    }

    public function authorityofaudit_insertupdate(Request $request)
    {
        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'category' => 'required|string|regex:/^(\d+|A)$/',
                'instmappingcode' => 'required|string|regex:/^(\d+|A)$/',
                'templateparaename' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (trim(strip_tags($value)) === '') {
                            $fail("The $attribute field cannot be empty.");
                        }
                    }
                ],
                'templateparatname' => [
                    'required',
                    function ($attribute, $value, $fail) {
                        if (trim(strip_tags($value)) === '') {
                            $fail("The $attribute field cannot be empty.");
                        }
                    }
                ],
                'statusflag' => 'required|in:Y,N',
            ];

            // dd($request->deptcode);
            // dd($request->category);
            // dd($request->statusflag);

            if ($request->input('if_subcategory') === 'Y') {
                $rules['subcategory'] = ['required', 'string', 'regex:/^(\d+|A)$/'];
            } else {
                $rules['subcategory'] = ['nullable', 'string', 'regex:/^(\d+|A)$/'];
            }

            $templateslip = session('user');
            if (!$templateslip || !isset($templateslip->userid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }

            $userchargeid = $templateslip->userid;

            $authority_id = $request->input('action') === 'update' ? Crypt::decryptString($request->input('authority_id')) : null;

            $templateparaenameJson = json_encode([
                'content' => html_entity_decode($request->input('templateparaename'), ENT_QUOTES, 'UTF-8')
            ]);

            $templateparatnameJson = json_encode([
                'content' => html_entity_decode($request->input('templateparatname'), ENT_QUOTES, 'UTF-8')
            ]);

            $subcategory = $request->input('subcategory');

            $data = [
                'deptcode' => $request->deptcode,
                'catcode' => $request->category,
                'subcatid' => $subcategory,
                'instid' => $request->instmappingcode,
                'auth_content_en' => $templateparaenameJson,
                'auth_content_ta' => $templateparatnameJson,
                'statusflag' => $request->statusflag,
            ];

            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }

            MastersModel::authorityofaudit_insertupdate($data, $authority_id, 'audit.map_authorityofaudit');

            return response()->json(['success' => true, 'message' => 'Authority Of Audit Created / Updated Successfully']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function authorityofaudit_fetchData(Request $request)
    {
        $authority_id = $request->has('authority_id') ? Crypt::decryptString($request->authority_id) : null;
        $authority = MastersModel::authorityofaudit_fetchData($authority_id, 'audit.map_authorityofaudit');

        foreach ($authority as $all) {
            $all->encrypted_authority_id = Crypt::encryptString($all->authority_id);

            unset($all->authority_id);
        }
        return response()->json([
            'success' => true,
            'message' => $authority->isEmpty() ? 'No schemes found' : '',
            'data' => $authority
        ], 200);
    }

    public function getRegexPattern(Request $request)
    {
        try {
            $deptcode = $request->deptcode;
            $cat_code = $request->cat_code;
            $subcatid = $request->subcatid;

            if (!$cat_code) {
                return response()->json([
                    'regex_pattern' => null,
                    'error' => 'Category code is required'
                ]);
            }

            $query = DB::table('audit.mst_auditeeins_subcategory')
                ->where('catcode', $cat_code);

            if ($subcatid) {
                $query->where('auditeeins_subcategoryid', $subcatid);
            }

            $regex = $query->value('regex');

            if (!$regex) {
                if ($deptcode == '01') {
                    $regex = '^TM0\d{5}$';
                }
            }

            return response()->json([
                'regex_pattern' => $regex,
                'debug' => [
                    'deptcode' => $deptcode,
                    'cat_code' => $cat_code,
                    'subcatid' => $subcatid,
                    'has_regex' => !empty($regex)
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching regex pattern: ' . $e->getMessage());

            return response()->json([
                'regex_pattern' => '^[A-Za-z0-9]{1,20}$',  // Fallback pattern
                'error' => 'Failed to fetch regex pattern',
                'debug' => $e->getMessage()
            ], 500);
        }
    }

    public function templatebankdetailsfetch(Request $request)
    {
        $bankid = $request->has('bankid') ? Crypt::decryptString($request->bankid) : null;

        $bank = MastersModel::fetchtemplatebankdetails($bankid, 'audit.mst_bank_master');

        foreach ($bank as $all) {
            $all->encrypted_bankid = Crypt::encryptString($all->bankid);

            unset($all->bankid);
        }
        return response()->json([
            'success' => !$bank->isEmpty(),
            'message' => $bank->isEmpty() ? 'User not found' : '',
            'data' => $bank->isEmpty() ? null : $bank
        ], $bank->isEmpty() ? 404 : 200);
    }

    public function templatebankdetails_insertupdate(Request $request)
    {
        try {
            $rules = [
                'bankname' => 'required|string|max:50',
                'ifsccode' => 'required|string|max:4',
                'statusflag' => 'required|in:Y,N',
            ];

            // $validator = Validator::make($request->all(), $rules);

            // if ($validator->fails()) {
            //     throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            // }

            $bank = session('user');
            if (!$bank || !isset($bank->userid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userid = $bank->userid;
            $bankid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('bankid')) : null;

            $data = [
                'statusflag' => $request->statusflag,
                'bank_name' => $request->bankname ?? null,
                'ifsc_code' => $request->ifsccode ?? null,
            ];

            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userid;
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userid;
            }

            $result = MastersModel::templatebankdetails_insertupdate($data, $bankid, 'audit.mst_bank_master');

            return response()->json(['success' => true, 'message' => 'templatebankdetails_created'], 201);

            // dd($data);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function zonefetchforcircle()
    {
        $district = MastersModel::getdistrict();

        return view('masters.hriacircle', compact('district'));
    }

    public function fetchzonebasedondist(Request $request)
    {
        $distcode = $request->input('distcode');

        if (!$distcode) {
            return response()->json([
                'success' => false,
                'message' => 'District code is required.'
            ], 400);
        }

        // Fetch work allocations based on the department code
        $zonename = MastersModel::fetchzonebasedondist($distcode);

        if (!$zonename || $zonename->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No work allocation found.',
                'data' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $zonename,
        ]);
    }

    public function circle_fetchData(Request $request)
    {
        $circleid = $request->has('circleid') ? Crypt::decryptString($request->circleid) : null;
        $circle = MastersModel::circle_fetchData($circleid, 'audit.mst_hriacircle');

        // dd($zone);
        foreach ($circle as $all) {
            $all->encrypted_circleid = Crypt::encryptString($all->circleid);
        }
        return response()->json([
            'success' => !$circle->isEmpty(),
            'message' => $circle->isEmpty() ? 'User not found' : '',
            'data' => $circle->isEmpty() ? null : $circle
        ], $circle->isEmpty() ? 404 : 200);
    }

    public function circle_insertupdate(Request $request)
    {
        try {
            $rules = [
                'zonecode' => 'required|string|regex:/^\d+$/',
                'circleename' => 'required|string|max:255',
                'circletname' => 'required|string|max:255',
                'statusflag' => 'required|in:Y,N',
            ];

            // $validator = Validator::make($request->all(), $rules);

            // if ($validator->fails()) {
            //     throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            // }

            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }
            $userchargeid = $chargedel->userchargeid;
            $circleid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('circleid')) : null;

            $data = [
                'circleename' => $request->circleename ?? null,
                'circletname' => $request->circletname ?? null,
                'zonecode' => $request->zonecode ?? null,
                'statusflag' => $request->statusflag,
            ];

            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
            }

            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::circle_insertupdate($data, $circleid, 'audit.mst_hriacircle');
            return response()->json(['success' => true, 'message' => 'circle_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function districtfetchforzone()
    {
        $district = MastersModel::getdistrict();

        return view('masters.hriazone', compact('district'));
    }

    public function zone_insertupdate(Request $request)
    {
        try {
            $rules = [
                'zoneename' => 'required|string|max:255',
                'zonetname' => 'required|string|max:255',
                'distcode' => 'required|string|regex:/^\d+$/',
                'statusflag' => 'required|in:Y,N',
            ];

            // $validator = Validator::make($request->all(), $rules);

            // if ($validator->fails()) {
            //     throw ValidationException::withMessages(['message' => 'Unauthorized', 'error' => 401]);
            // }

            $chargedel = session('charge');
            if (!$chargedel || !isset($chargedel->userchargeid)) {
                return response()->json(['success' => false, 'message' => 'Charge session not found or invalid.'], 400);
            }
            $userchargeid = $chargedel->userchargeid;
            $zoneid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('zoneid')) : null;

            $data = [
                'zoneename' => $request->zoneename ?? null,
                'zonetname' => $request->zonetname ?? null,
                'distcode' => $request->distcode ?? null,
                'statusflag' => $request->statusflag,
            ];

            if ($request->input('action') === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userchargeid;
            }
            if ($request->input('action') === 'update') {
                $data['updatedon'] = View::shared('get_nowtime');
                $data['updatedby'] = $userchargeid;
            }
            $result = MastersModel::zone_insertupdate($data, $zoneid, 'audit.mst_hriazone');
            return response()->json(['success' => true, 'message' => 'zone_success']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    public function zone_fetchData(Request $request)
    {
        $zoneid = $request->has('zoneid') ? Crypt::decryptString($request->zoneid) : null;
        $zone = MastersModel::zone_fetchData($zoneid, 'audit.mst_hriazone');

        // dd($zone);
        foreach ($zone as $all) {
            $all->encrypted_zoneid = Crypt::encryptString($all->zoneid);
        }
        return response()->json([
            'success' => !$zone->isEmpty(),
            'message' => $zone->isEmpty() ? 'User not found' : '',
            'data' => $zone->isEmpty() ? null : $zone
        ], $zone->isEmpty() ? 404 : 200);
    }
}
