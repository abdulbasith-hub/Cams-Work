<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SmsmailModel;
use App\Services\SmsService;
use App\Services\PHPMailerService;
use Carbon\Month;
use Mpdf\Tag\I;
use App\Models\BaseModel;


use App\Models\LagacyModel;

use Illuminate\Http\Request;
// use DB;
use App\Services\FileUploadService;

class LagacyController extends Controller
{
    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;

    protected $fileUploadService;

    protected static $transpara_table = BaseModel::TRANSPARA_TABLE;
    protected static $parafileupload_table = BaseModel::PARAFILEUPLOAD_TABLE;


    public function updateleagacy_file(Request $request)
    {

        try {

            //return $request->all();

            $followupid = $request->filled('followupid') ? Crypt::decryptstring($request->followupid) : null;
            $instid = $request->filled('instid') ? Crypt::decrypt($request->instid) : null;

            throw_if(empty($followupid), new \Exception("Legacy details not found"));
            throw_if(empty($instid), new \Exception("Institution details not found"));

            $audityear = $request->input('yearcode');

            $fileUploadId = null;
            $fileupload = $request->file('fileupload');
            $destinationPath = '';

            if (! $fileupload) {
                throw new \Exception("File Details not Found");
            }


            $deactive_fileuploadids = $request->input('deactive_fileid') ? explode(',', $request->input('deactive_fileid')) : [];
            $active_fileuploadids = $request->input('active_fileid') ? explode(',', $request->input('active_fileid')) : [];

            if (!empty($deactive_fileuploadids)) {
                $this->fileUploadService->deactive_lagacyuploadefile($followupid, $deactive_fileuploadids);
            }

            if ($request->hasFile('fileupload')) {

                $uploadResult = $this->fileUploadService->lagacyMultipleFileUpload(
                    $fileupload,
                    $destinationPath,
                    $followupid,
                    $active_fileuploadids,
                    $deactive_fileuploadids,
                    '',
                    $instid,
                    $audityear
                );



                if (is_array($uploadResult) && isset($uploadResult['error'])) {
                    return response()->json(['errors' => $uploadResult['error']], 400);
                } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
                    $fileUploadId = $uploadResult->getData(true)['uploaded_files'];
                }

                if ($fileUploadId) {
                    $fileupload_stat = $this->fileUploadService->insert_lagacyfileupload($followupid, $fileUploadId);
                }

                // $fileuploadresult = json_decode($fileupload_stat, true);

                // return $fileupload_stat;

                if (isset($fileupload_stat)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Legacy data was updated successfully',
                    ]);
                } else {
                    throw new \Exception("Failed to upload");
                }
            }
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading Legacy File'
            ], 500);
        }
    }

    public static function fetchdeptforfollowup()
    {
        try {
            $dept = LagacyModel::commondeptfetch();
            $region = LagacyModel::regionfetch();
            $district = LagacyModel::districtfetch();

            return view('lagacy.initlagacy', compact('dept', 'region', 'district'));
        } catch (\Exception $e) {
            return view('lagacy.initlagacy', [
                'dept' => $dept ?? null,
                'region' => $region ?? null,
                'district' => $district ?? null,
                'errorMessage' => $e->getMessage(),
                'pageName' => 'initlagacy',
            ]);
        }
    }



    public function getinstbasedonsubcatfollowup(Request $request)
    {
        // Validate the input
        $request->validate([
            'region'   => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'district' => ['required', 'string', 'regex:/^\d+$/'],
            'catcode' => ['required', 'string', 'regex:/^\d+$/'],
            'subcatcode' => ['nullable', 'string', 'regex:/^\d+$/'],

        ], [
            'region.required' => 'The :attribute field is required.',
            'region.regex'    => 'The :attribute field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex'    => 'The deptcode field must be a valid number.',
            'district.required' => 'The district field is required.',
            'district.regex'    => 'The district field must be a valid number.',
            'catcode.required' => 'The Category field is required.',
            'catcode.regex'    => 'The Category field must be a valid number.',
            'subcatcode.required' => 'The Subcategory field is required.',
            'subcatcode.regex'    => 'The Subcategory field must be a valid number.',
        ]);

        // Get the department code
        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');
        $district = $request->input('district');
        $catcode = $request->input('catcode');
        $subcatcode = $request->input('subcatcode') ?? null;


        $institution = LagacyModel::getinstbasedonsubcat($district, $regioncode, $deptcode, $catcode, $subcatcode);
        foreach ($institution as $all) {
            $all->instid = Crypt::encryptString($all->instid);

            //   unset($all->instid);
        }
        if ($institution->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $institution]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Institution found'], 200);
        }
    }


    public function getcategorybasedondistfollowup(Request $request)
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

        // Get the department code
        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');
        $district = $request->input('district');


        $category = LagacyModel::getcategoryBydistrictchange($district, $regioncode, $deptcode);


        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }


    public function getdistrictbasedonregionfollowup(Request $request)
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


        $district = LagacyModel::getdistrictByregion($regioncode, $deptcode);

        if ($district->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $district]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }






    public function getcategoryBasedOninstforfollowup(Request $request)
    {
        $request->validate([
            'instmappingcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $instmappingcode = $request->input('instmappingcode');





        return response()->json([
            'success' => true,
            'category' => $category,
        ]);
    }



    public function getCategoriesBasedOnDeptforfollowup(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
        ]);

        // Get the department code
        $deptcode = $request->input('deptcode');
        // $category = LagacyModel::getcategoryBydept($deptcode);


        $regions = LagacyModel::getRegionsByDept($deptcode);

        return response()->json([
            'success' => true,
            // 'category' => $category,
            'regions' => $regions, // Include audit periods in the response
        ]);
    }


    public function getsubcatbasedoncategoryfollowup(Request $request)
    {

        $request->validate([
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
        ]);


        $category = $request->input('category');

        $subcategory = LagacyModel::getSubcategoryByCategory($category);

        return response()->json($subcategory);
    }
































    // Inject the FileUploadService
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function followup_dropdown(Request $request)
    {
        try {
            $instid = $request->query('inst');
            // $catId = $request->query('cat');
            // $catName = $request->query('catname');
            // $subcatId = $request->query('subcat');
            // $subcatName = $request->query('subcatname');

            $userData = session('user');
            $session_userid = $userData->userid;

            $instid = Crypt::decryptString($instid);
            if (empty($instid)) {
                throw new \Exception("Institution ID not found");
            }

            $data = LagacyModel::followup_dropdown($instid);

            //   return $data;
            $instData         = $data['inst']->first();

            if ($instData) {
                $instData->encrypted_instid = Crypt::encrypt($instData->instid);
                unset($instData->instid);
            }
            $catData          = $data['catDet']->first();
            $subcatData       = empty($data['subcatDet']) ? $data['subcatDet'] : $data['subcatDet']->first();
            $typeofauditData  = $data['typeofaudit'];
            $yearofaudit      = $data['yearofaudit'];
            dd($yearofaudit);
            $objectionData    = $data['objection'];
            $stateofpara    = $data['stateofpara'];
            $typeofpara    = $data['typeofpara'];


            $schemename = LagacyModel::getSchemename($instData->catcode,  $instData->subcatid);

            $severitydel = LagacyModel::getSeverity();

            $serious = LagacyModel::getSerious();

            $severities = [
                'L' => ['en' => 'Low', 'ta' => 'குறைந்த'],
                'M' => ['en' => 'Medium', 'ta' => 'மாதிரி'],
                'H' => ['en' => 'High', 'ta' => 'உயர்ந்த'],
            ];
            $session_userid = Crypt::encryptString($session_userid);

            return view('lagacy.followup2', compact('typeofpara', 'stateofpara', 'subcatData', 'catData', 'session_userid', 'serious', 'severitydel', 'schemename', 'instData', 'typeofauditData',         'objectionData', 'severities', 'yearofaudit',));
        } catch (\Exception $e) {
            Log::error("Error in Lagacy: " . $e->getMessage());
            echo $e->getMessage();
            return redirect()->back()->with([
                'errorMessage' => $e->getMessage(),
                'pageName' => 'followup'
            ]);
            //return redirect()->route('error')->with('error', 'An error occurred while processing the auditslip. Please try again later.');
        }
    }

    public function getminordet(Request $request)
    {
        $request->validate([
            'mainobjectionid'       => 'required',
        ], [
            'required' => 'The :attribute field is required.',
        ]);
        if ($request->mainobjectionid) {
            $minorobjectionData = LagacyModel::getminorobjection($request->mainobjectionid);
            return response()->json(['minorobjectionData' => $minorobjectionData]);
        }
    }


    public function followup_insert(Request $request)
    {



        // return $request->all();
        //check for same login
        $userdel = session('user');
        $chargeData = session('charge');

        $sessionDeptcode = $chargeData->deptcode ?? null;

        $session_userid = $userdel->userid ?? null;
        if (!$session_userid) {
            return response()->json(['error' => 'User session is invalid.'], 400);
        }

        $formsessionuserid = Crypt::decryptString($request->ens);

        if ($session_userid != $formsessionuserid)
            return response()->json(['success' => false, 'message' => 'Please refresh the page maintain one login at a time'], 402);

        $action = $request->input('action');


        $request->validate([
            'typeofauditcode'          => 'required|string',
            'parano'               => 'required|integer',
            'yearcode'                 => 'required|integer',
            'mainobjectionid'          => 'required|integer',
            'subobjectionid'           => 'required|integer',

            'severityid'               => 'required|max:1|string',
            'scheme'                   => 'required|alpha|in:Y,N|max:1',
            'liability'                => 'required|string|in:Y,N|max:1',
            'slipdetails'              => 'required|max:500|string|min:10',
            'schemename'               => ['nullable', 'required_if:scheme,Y', 'string', 'regex:/^\d+$/'],
            'serious'                  => 'required|string|max:2',
            'category'                 => 'required|string|max:2',
            'subcategory'              => 'required|string|max:2',
            'statusflag'               => 'required|string|in:Y,F',
            'typeofparacode'           => 'required|string|max:2',
            'stateofparacode'          => 'required|string|max:2',
            'lastactionmonth'          => 'nullable|string|max:2',
            'lastactionyear'           => 'nullable|integer',

        ]);

        $liabilitydel   =    $request->input('liability');

        if ($liabilitydel === 'Y') {

            $request->validate(
                [
                    'name'            => 'required|array',
                    'name.*'          => ['required', 'max:50', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],

                    // 'gpfno'           => 'required|array',
                    // 'gpfno.*'         => ['required',  'max:20', 'regex:/^\d+$/'],

                    'amount'          => 'required|array',
                    'amount.*'        => ['required', 'numeric', 'max:999999999'],

                    'designation'     => 'required|array',
                    'designation.*'   => ['required', 'max:50', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],

                    'notype'          => 'required|array',
                    'notype.*'        => ['required', 'max:20', 'regex:/^\d+$/'],  // Replace with your allowed types

                    'liabilityid'     => 'required|array',
                    'liabilityid.*'   => ['nullable', 'integer'],
                ],
                [
                    'name.required'            => 'The name field is required.',
                    'name.*.required'          => 'Liability name is required.',
                    'name.*.max'               => 'Liability name must not exceed 50 characters.',
                    'name.*.regex'             => 'Liability name must contain only letters and spaces.',

                    'gpfno.required'           => 'The GPF number field is required.',
                    'gpfno.*.required'         => 'Liability GPF number is required.',
                    'gpfno.*.max'              => 'Liability GPF number must not exceed 20 digits.',
                    'gpfno.*.regex'            => 'Liability GPF number must be numeric.',

                    'amount.required'          => 'The amount field is required.',
                    'amount.*.required'        => 'Liability amount is required.',
                    'amount.*.numeric'         => 'Liability amount must be a valid number.',
                    'amount.*.max'             => 'Each Liability amount must not exceed 999999999.',

                    'designation.required'     => 'Liability designation field is required.',
                    'designation.*.required'   => 'Liability designation is required.',
                    'designation.*.max'        => 'Liability designation must not exceed 50 characters.',

                    'notype.required'          => 'Liability Number type field is required.',
                    'notype.*.required'        => 'Liability Number type is required.',
                    'notype.*.max'             => 'Liability Number type must not exceed 20 characters.',
                    'notype.*.regex'           => 'Liability Number type must be numeric.',

                    'liabilityid.required'     => 'The liability ID field is required.',
                    'liabilityid.*.integer'    => 'Each liability ID must be an integer.',
                ]
            );

            $notype                  =   $request->input('notype');
            $name                    =   $request->input('name');

            $gpfno                   =   $request->input('gpfno');
            $amount                  =   $request->input('amount');
            $designation             =   $request->input('designation');
            $liabilityid             =   $request->input('liabilityid');
            $liabilitydel            =   $request->input('liabilityid');
            // $count_name     = count($name);
            $deleted_liabilityid    =   $request->input('deleted_liabilityid');
        }

        if (($request->input('scheme') == 'Y')) {
            $schemename   =   $request->input('schemename');
        }
        $content = json_encode(['content' => $request->input('remarks')]);
        $instid = crypt::decrypt($request->input('instid'));

        if (empty($instid))
            throw new \Exception("Institution ID not found");
        // $tempnumber = (int) $request->input('currentslipnumber', 0);
        // $tempnumber += 1;

        // return $tempnumber;

        //   return $tempnumber;
        $data = [

            //  'auditplanid'           => $request->input('auditplanid'),
            'instid'                    => $instid,

            'audityear'                 => json_encode([(int)$request->input('yearcode')]),
            'parano'                 => $request->input('parano'),
            'typeofauditcode'                 => $request->input('typeofauditcode'),
            // 'audityear'                 => $request->input('mainobjectionid'),

            'mainobjectionid'           => $request->input('mainobjectionid'),
            'subobjectionid'            => $request->input('subobjectionid'),
            // 'paranumber'              => $tempnumber,
            'paranumber'            => $request->input('currentslipnumber'),
            'severitycode'              => $request->input('severityid'),
            'liability'                 => $request->input('liability'),
            'schemastatus'              => $request->input('scheme'),
            'auditeeschemecode'         => $request->input('schemename'),
            'irregularitiescode'        => $request->input('serious'),
            'irregularitiescatcode'     => $request->input('category'),
            'irregularitiessubcatcode'  => $request->input('subcategory'),
            'slipdetails'               => $request->input('slipdetails'),
            'remarks'                   => $content,
            'statusflag'                => $request->input('statusflag'),
            'typeofparacode'                => $request->input('typeofparacode'),
            'stateofparacode'                => $request->input('stateofparacode'),
            'lastactionmonth'         =>  $request->input('lastactionmonth'),
            'lastactionyear'          =>   $request->input('lastactionyear'),

        ];

        if ($request->input('amount_involved')) {
            $data['amtinvolved'] = $request->input('amount_involved');
        }

        $fileUploadId = null;
        $fileupload = $request->file('fileupload');
        $destinationPath = '';

        $followupid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('followupid')) : null;
        //return $request->input('deactive_fileid');
        $deactive_fileuploadids = $request->input('deactive_fileid') ? explode(',', $request->input('deactive_fileid')) : [];
        //  return  $request->input('deactive_fileid');
        $active_fileuploadids = $request->input('active_fileid') ? explode(',', $request->input('active_fileid')) : [];
        if (!empty($deactive_fileuploadids)) {
            $this->fileUploadService->deactive_lagacyuploadefile($followupid, $deactive_fileuploadids);
        }

        $fileUploadId = null;

        if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('fileupload'))) {

            $uploadResult = $this->fileUploadService->lagacyMultipleFileUpload(
                $fileupload,
                $destinationPath,
                $followupid,
                $active_fileuploadids,
                $deactive_fileuploadids,
                '',
                $instid
            );

            if (is_array($uploadResult) && isset($uploadResult['error'])) {
                return response()->json(['errors' => $uploadResult['error']], 400);
            } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
                $fileUploadId = $uploadResult->getData(true)['uploaded_files'];
            }
        }
        // return $fileUploadId;


        if ($action === 'insert') {
            $processcode    =    'E';
            $data['processcode'] = 'E';
            $data['createdon'] = View::shared('get_nowtime');
            $data['createdby'] = $session_userid;

            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $session_userid;
        }





        if ($request->input('action') === 'insert') {
            $data['createdon'] =  View::shared('get_nowtime');
            $data['createdby'] =  $session_userid;
        }

        if ($request->input('action') === 'update') {
            $data['updatedon'] =  View::shared('get_nowtime');
            $data['updatedby'] =  $session_userid;
        }
        DB::beginTransaction();
        //  return $data;
        try {
            $followupdet = LagacyModel::createorinsertLagacydet($data, $followupid);
            //    return $followupdet;
            $followupid = $followupdet->followupid;
            $tempnumber = $followupdet->paranumber;


            if ($fileUploadId) {
                $this->fileUploadService->insert_lagacyfileupload($followupid, $fileUploadId);
            }

            if (($request->input('liability') == 'Y')) {
                $activestatus = '';

                if ($deleted_liabilityid) {
                    $deletedliabilitydel = explode(",", $deleted_liabilityid);
                    LagacyModel::deleteLiability($deletedliabilitydel, $session_userid);
                } else {
                    //  $activestatus = $request->input('activestatus');
                    $activestatus = 'A';
                }

                $asdas =   LagacyModel::insertupdateLiability($liabilitydel, $notype, $name, $gpfno, $designation, $amount, '', $followupid, $session_userid);
            }
            //  return $asdas;
            $status = $request->input('statusflag');
            if ($status == 'F') {
                $message = 'Detail has been  finalised';
            } else {
                $message = 'Detail as been  submitted successfully';
            }
            if ($followupid) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => ['followupid' => Crypt::encryptString($followupid), 'tempnumberform' => $tempnumber, 'selectedaudityear' => $followupdet->audityear]
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
        // if ($fileUploadId) {
        //     $this->fileUploadService->insert_slipfileupload($auditslipid, $fileUploadId, $rejoinderstatus, $rejoindercycle, $processcode);
        // }
        // return $data;

    }


    public static function fetch_lagacydata(Request $request)
    {
        try {
            $followupid = $request->filled('followupid') ? Crypt::decryptString($request->followupid) : null;
            $instid = $request->filled('instid') ? Crypt::decrypt($request->instid) : null;
            $action = $request->filled('action') ? $request->action : '';
            $yearcode = $request->filled('yearcode') ? $request->yearcode : '';
            // return $instid;
            $followupDet = LagacyModel::fetch_lagacydata($followupid, $instid, $action, $yearcode);
            //return  $followupDet;
            foreach ($followupDet as $all) {
                $all->encrypted_followupid = Crypt::encryptString($all->followupid);
                unset($all->followupid);
            }
            // if ($followupDet->isEmpty()) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Mapping Details not found not found',
            //         'data' => null
            //     ], 404);
            // }
            // return response()->json([
            //     'success' => true,
            //     'message' => '',
            //     'data' => $followupDet
            // ], 200);
            if ($followupid) {
                if ($followupDet->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Mapping Details not found not found',
                        'data' => null
                    ], 404);
                }

                // // Encrypt user IDs in results
                // $followupDet->transform(function ($all) {
                //     $all->encrypted_instid = Crypt::encryptString($all->instid);
                //     unset($all->lagacyid);
                //     return $all;
                // });

                return response()->json([
                    'success' => true,
                    'message' => '',
                    'data' => $followupDet
                ], 200);
            }


            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $followupDet->isEmpty() ? null : $followupDet
            ], 200);



            // Return data in JSON format
            // return response()->json($allMapallocationobjectionDet);
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


    //---------------------------- Audit Para Management System -------------------------------------------------//

    public function paramanagement_auditeedropdown(Request $request)
    {
        try {
            $viewName      = $request->route('viewName');
            $sessioncharge = session('charge');
            $userData      = session('user');
            $n_yearcode = request('n_yearcode');
            $parano = request('parano');
            $sessionusertypecode = $sessioncharge->usertypecode;
            $instid              = $sessioncharge->instid;
            $session_userid      = $userData->userid;

            if (empty($instid)) {
                throw new \Exception("Institution ID not found");
            }

            $data = LagacyModel::auditee_dropdown($instid, null);

            $instData         = $data['inst'];

            if ($instData) {
                $instData->encrypted_instid = Crypt::encryptString($instData->instid);
                unset($instData->instid);
            }
            $catData          = $data['catDet']->first();
            $subcatData       = empty($data['subcatDet']) ? $data['subcatDet'] : $data['subcatDet']->first();

            $yearofaudit      = $data['yearofaudit'];
            // dd($yearofaudit);
            $normalaudityear      = $data['normalaudityear'];

            $session_userid = Crypt::encryptString($session_userid);

            return view($viewName, compact('instData', 'normalaudityear',  'subcatData', 'catData', 'session_userid',  'yearofaudit', 'n_yearcode', 'parano'));
        } catch (\Exception $e) {
        }
    }

    public function auditeefollowup_dropdown($id, $instid, $paratype, $followupid)
    {
        try {
            $viewName = request()->route('viewName');

            $sessioncharge   = session('charge');
            $userData        = session('user');

            $sessionusertypecode = $sessioncharge->usertypecode;
            $session_userid      = $userData->userid;
            $followupid   = $followupid ? Crypt::decryptString($followupid) : null;
            $paraid       = $id != 'null' ?  Crypt::decryptString($id) : null;
            // return $paraid;
            $paratype     = $paratype ? $paratype : null;
            $instid       = $instid ? $instid : null;

            if ($sessionusertypecode == 'I' && empty($paraid)) {

                $paradet =   LagacyModel::fetch_paramanagement_auditee($followupid, $instid, 'edit', null, $paratype, '');

                $details = $paradet['data'];

                foreach ($details as $all) {
                    $all->encrypted_followupid = Crypt::encryptString($all->followupid);
                    //  unset($all->followupid);
                }

                $paradetails =   $paradet;

                $paradets = null;
                $paraid_decrypt =  $paraid == 'null' ? null : $id;
            } else {
                $paradetails = LagacyModel::fetch_lagacy_paradata($paraid, $paratype, $followupid);
                $paradets = $paradetails['data'];
                foreach ($paradets as $all) {
                    $all->encrypted_followupid = Crypt::encryptString($all->followupid);
                    //  unset($all->followupid);
                }

                $instid = $paradets[0]->instid;
                $paraid_decrypt = $id;
            }
            //  return $paradetails;
            if (empty($instid)) {
                throw new \Exception("Institution ID not found");
            }

            $data = LagacyModel::apms_dropdown($instid, $paradets);
            // print_r($data);
            // exit;
            // return $data;
            $instData         = $data['inst'];

            if ($instData) {
                $instData->encrypted_instid = Crypt::encryptString($instData->instid);
                //unset($instData->instid);
            }
            $catData          = $data['catDet']->first();
            $subcatData       = empty($data['subcatDet']) ? $data['subcatDet'] : $data['subcatDet']->first();
            $typeofauditData  = $data['typeofaudit'];
            $yearofaudit      = $data['yearofaudit'];
            $objectionData    = $data['objection'];
            $stateofpara      = $data['stateofpara'];
            $typeofpara       = $data['typeofpara'];
            $actiondata       = $data['actiondata'];
            $configdatas      = $data['configdatas'];
            $normalaudityear  = $data['normalaudityear'];


            $schemename = LagacyModel::getSchemename($instData->catcode,  $instData->subcatid);

            $severitydel = LagacyModel::getSeverity();

            $serious = LagacyModel::getSerious();

            $severities = [
                'L' => ['en' => 'Low', 'ta' => 'குறைந்த'],
                'M' => ['en' => 'Medium', 'ta' => 'மாதிரி'],
                'H' => ['en' => 'High', 'ta' => 'உயர்ந்த'],
            ];

            $session_userid = Crypt::encryptString($session_userid);
            //throw new \Exception("Institution ID not found");

            return view($viewName, compact('normalaudityear', 'configdatas', 'actiondata', 'paradetails', 'paraid_decrypt', 'typeofpara', 'stateofpara', 'subcatData', 'catData', 'session_userid', 'serious', 'severitydel', 'schemename', 'instData', 'typeofauditData', 'objectionData', 'severities', 'yearofaudit'));
        } catch (\Exception $e) {
            echo $e->getMessage();

            // return redirect()->back()->with([
            //     'errorMessage' => $e->getMessage(),
            //     'pageName' => $viewName
            // ]);
            // return redirect()->route('error')->with('error', 'An error occurred while fetching the Para Details. Please try again later.');
        }
    }

    public function fetch_paramanagement_auditee(Request $request)
    {
        try {
            $followupid   = $request->filled('followupid') ? Crypt::decryptString($request->followupid) : null;
            $instid       = $request->filled('instid') ? Crypt::decryptString($request->instid) : null;
            $action       = $request->filled('action') ? $request->action : '';
            $yearcode[]   = $request->filled('yearcode') ? $request->yearcode : '';
            $paratype     = $request->filled('paratype') ? $request->paratype : '';
            $paraid       = ($request->filled('paraid') && ($request->paraid != 'null')) ? Crypt::decryptString($request->paraid) : null;
            $paradet      = '';
            $parano     = $request->filled('parano') ? $request->parano : '';
            $parano     =  $parano == 'A' ? '' : $parano;

            $sessionuser = session('user');
            $sessioncharge = session('charge');
            $userid      = $sessionuser->userid;
            $param       =  $request->param;


            if ($param == 'year') {
                $followupdet = LagacyModel::fetch_parano($instid, $yearcode[0]);

                if ($followupdet) {
                    return response()->json([
                        'success' => true,
                        'message' => '',
                        'data' => $followupdet['data'],

                    ], 200);
                }
            }

            $followupDet = LagacyModel::fetch_paramanagement_auditee($followupid, $instid, $action, $yearcode[0],  $paratype, $parano);

            //   if ($action == 'P') {
            foreach ($followupDet['data'] as $all) {
                if (isset($all->paraid)) {
                    $all->encrypted_paraid = Crypt::encryptString($all->paraid);
                    $paraid = $all->paraid;
                    // unset($all->paraid);
                } else {
                    $all->encrypted_paraid = null;
                }
            }
            // }


            $sessionusertypecode  = $sessioncharge->usertypecode;
            if (($paraid != '' || $paraid != null)) {
                $paradet = LagacyModel::fetch_paradata($followupid, $paraid, $userid, $sessionusertypecode);
                //return 'asd';
                if (!empty($paradet['data'])) {

                    foreach ($paradet['data'] as $all) {
                        if (isset($all->paraid)) {
                            $all->encrypted_paraid = Crypt::encryptString($all->paraid);
                            //  unset($all->paraid);
                        } else {
                            $all->encrypted_paraid = null;
                        }
                    }
                }
            }
            //  return $followupDet['data'];
            foreach ($followupDet['data'] as $all) {
                $all->encrypted_followupid = Crypt::encryptString($all->followupid);

                // unset($all->followupid);
            }

            // return 'Asd';
            if ($followupid) {
                if ($followupDet['data']->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Para Details not found',
                        'data' => null
                    ], 404);
                }



                return response()->json([
                    'success' => true,
                    'message' => '',
                    'data' => $followupDet['data'],
                    'paradet' => $paradet
                ], 200);
            }


            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $followupDet['data']->isEmpty() ? null : $followupDet,
                'paradet' => $paradet
            ], 200);



            // Return data in JSON format
            // return response()->json($allMapallocationobjectionDet);
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid  ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching para data'
            ], 500);
        }
    }


    public function paradet_insert(Request $request)
    {

        // return $request->all();
        //check for same login
        $userdel = session('user');
        $chargeData = session('charge');

        $sessionDeptcode = $chargeData->deptcode ?? null;
        $sessionchargeid = $chargeData->userchargeid ?? null;
        $sessionusertypecode = $chargeData->usertypecode ?? null;
        $session_userid = $userdel->userid ?? null;
        $sesroleactioncode = $chargeData->roleactioncode ?? null;

        //session login check
        if (!$session_userid) {
            return response()->json(['error' => 'User session is invalid.'], 400);
        }

        $formsessionuserid = Crypt::decryptString($request->ens);

        if ($session_userid != $formsessionuserid)
            return response()->json(['success' => false, 'message' => 'Please refresh the page maintain one login at a time'], 402);


        //form-data
        $action = $request->input('action');

        $request->validate([

            'followupid' => 'required',
            'paratype' => 'required',
            'rejectcount' => 'nullable|integer',
            'stateofparacode' => 'nullable|string'


        ]);

        //Retiremwnt Details

        // return 'asd';

        //liability details
        $liabilitydel = collect();
        if ($sesroleactioncode == view::shared('PUADroleactioncode')) {
            $liabilityval = $request->input('liabilityval');
        } else {
            $liabilityval = $request->input('liabilityval');
            $liabilitydel = collect();
        }

        // return $liabilitydel;
        $notype = $request->input('notype');
        if ((is_array($notype))) {


            //  if ($liabilityval === 'Y' && $sesroleactioncode == view::shared('PUADroleactioncode') || ($sessionusertypecode == 'I' && $liabilityval == 'N') || ($sessionusertypecode == 'A')) {

            $request->validate(
                [
                    // 'name' => 'required|array',
                    // 'name.*' => ['required', 'max:50', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],

                    // // 'gpfno' => 'required|array',
                    // // 'gpfno.*' => ['required', 'max:20', 'regex:/^\d+$/'],

                    // //'amount' => 'required|array',
                    // //'amount.*' => ['required', 'numeric', 'gt:0', 'max:999999999999'],

                    // 'designation' => 'required|array',
                    // 'designation.*' => ['required', 'max:50', 'regex:/^[\p{Tamil}A-Za-z\s]+$/u'],

                    // 'notype' => 'required|array',
                    // 'notype.*' => ['required', 'max:20', 'regex:/^\d+$/'], // Replace with your allowed types

                    // 'liabilityid' => 'required|array',
                    // 'liabilityid.*' => ['nullable', 'integer'],

                    // 'remarks' => 'nullable|array',
                    // 'remarks.*' => ['nullable', 'max:100'],

                    // 'retiredflag' => 'nullable|array',
                    // 'retiredflag.*' => ['nullable', 'in:L,M,H'],

                    // 'retirementyear' => 'nullable|array',
                    // 'retirementyear.*' => ['nullable', 'digits:4'],

                    // 'retirementmonth' => 'nullable|array',
                    // 'retirementmonth.*' => ['nullable', 'digits:2'],

                ],
                [
                    // 'name.required' => 'The name field is required.',
                    // 'name.*.required' => 'Liability name is required.',
                    // 'name.*.max' => 'Liability name must not exceed 50 characters.',
                    // 'name.*.regex' => 'Liability name must contain only letters and spaces.',

                    // 'gpfno.required' => 'The GPF number field is required.',
                    // 'gpfno.*.required' => 'Liability GPF number is required.',
                    // 'gpfno.*.max' => 'Liability GPF number must not exceed 20 digits.',
                    // 'gpfno.*.regex' => 'Liability GPF number must be numeric.',

                    //'amount.required' => 'The amount field is required.',
                    // 'amount.*.required' => 'Liability amount is required.',
                    // 'amount.*.numeric' => 'Liability amount must be a valid number.',
                    // 'amount.*.max' => 'Each Liability amount must not exceed 999999999.',

                    // 'designation.required' => 'Liability designation field is required.',
                    // 'designation.*.required' => 'Liability designation is required.',
                    // 'designation.*.max' => 'Liability designation must not exceed 50 characters.',

                    // 'notype.required' => 'Liability Number type field is required.',
                    // 'notype.*.required' => 'Liability Number type is required.',
                    // 'notype.*.max' => 'Liability Number type must not exceed 20 characters.',
                    // 'notype.*.regex' => 'Liability Number type must be numeric.',

                    // 'liabilityid.required' => 'The liability ID field is required.',
                    // 'liabilityid.*.integer' => 'Each liability ID must be an integer.',


                    // //remarks.*.regex' => 'Remarks must contain valid characters',
                    // 'remarks.*.max' => 'Remarks must not exceed 100 characters',

                    // 'retiredflag.*.in' => 'Retirement Action must be valid ',

                    // 'retirementyear.*.digit' => 'Retirement Year must be valid',
                    // 'remtirementmonth.*.digit' => 'Retirement Month must be valid',
                ]
            );

            $notype = $request->input('notype');
            $name = $request->input('name');

            $gpfno = $request->input('gpfno');
            $amount = $request->input('amount');
            $designation = $request->input('designation');
            $liabilitydel = $request->input('liabilityid');
            $retiredflag = $request->input('retiredflag');
            $retirementyear = $request->input('retirementyear');
            $retirementmonth = $request->input('retirementmonth');
            $lagacyflag = $request->input('lagacyflag');
            foreach ($lagacyflag as &$flag) {
                if ($flag === null) {
                    $flag = 'N';
                }
            }
            unset($flag);
            // if ($retiredflag == 'Y') {
            // } else {
            // $retirementyear = collect();
            // $retirementmonth = collect();
            // }

            if ($sesroleactioncode == view::shared('PUADroleactioncode')) {

                $activestatus = $request->input('activestatus');
                //return $activestatus;
            } else {
                $activestatus = '';
            }

            if ($sessionusertypecode == 'I') {
                $liability_remarks = collect();
            } else {
                $liability_remarks = $request->input('remarks');
            }

            // $count_name = count($name);
            $deleted_liabilityid = $request->input('deleted_liabilityid');

            $liabilitydata =
                [
                    'liabilitydel' => $liabilitydel,
                    'notype' => $notype,
                    'name' => $name,
                    'gpfno' => $gpfno,
                    'designation' => $designation,
                    'amount' => $amount,
                    'liability_remarks' => $liability_remarks,
                    'activestatus' => $activestatus,
                    'retiredflag' => $retiredflag,
                    'retirementyear' => $retirementyear,
                    'retirementmonth' => $retirementmonth,
                    'lagacyflag' => $lagacyflag
                ];

            $auditeeflag = $sessionusertypecode == 'I' ? 'Y' : 'N';
            //  }
        } else {
            $liabilitydata = '';
        }
        //return  $liabilitydata;
        $retirementpara = false;

        if (!empty($liabilitydata)) {

            foreach ($liabilitydata['retiredflag'] as $flag) {
                $retirementFlags = view::shared('retirementFlags');

                if (in_array($flag, $retirementFlags, true)) {
                    $retirementpara = true;
                    break;
                }
            }
        }

        $content = json_encode(['content' => $request->input('auditeeremarks')]);
        $statusflag = $request->input('statusflag');
        $instid = crypt::decryptString($request->input('instid'));
        $followupid = crypt::decryptString($request->followupid);
        $actionfor = $request->input('actionfor');
        $rejoinderstatus = $request->input('rejoinderstatus');
        $rejoindercycle = $request->input('rejoindercycle');
        $old_processcode = $request->input('processcode');
        $paratype = $request->input('paratype');
        $rejectcount = $request->input('rejectcount');
        $oldrejectcount = $request->input('rejectcount');
        $stateofparacode = $request->input('stateofparacode');

        $yearcode[] = $request->input('n_yearcode');


        // $paraid = $request->input('action') === 'update' ? Crypt::decryptString($request->input('paraid')) : null;
        $paraid = $request->filled('paraid') ? Crypt::decryptString($request->input('paraid')) : null;


        // return $paraid;
        if (empty($instid))
            throw new \Exception("Institution ID not found");
        if (empty($followupid))
            throw new \Exception("Lagacy ID not found");


        if ($sessionusertypecode == 'I') {
            $actroleactioncode = 'I';
        } else if ($sessionusertypecode == 'A') {
            $actroleactioncode = $sesroleactioncode == View::shared('PUroleactioncode') ? 'A' : 'AD';
        } else
            throw new \Exception("Role Type was not defined");


        $actioncode = $sessionusertypecode == 'A' ? $request->input('actioncode') : null;



        $PU   = View::shared('PUroleactioncode');
        $PUAD = View::shared('PUADroleactioncode');



        switch ($actionfor) {

            case 'forward':
                if ($sessionusertypecode === 'I') {
                    if (in_array($stateofparacode, View::shared('hlcs_stateofparacode'))) {
                        $processcode = LagacyModel::getprocesscode_hlc($old_processcode, 'process');
                    } elseif ((in_array($old_processcode, View::shared('DLC_TO_auditee_process')))) {
                        $processcode = LagacyModel::getprocesscode_hlc($old_processcode, 'process');
                    } elseif ($retirementpara) {
                        $processcode = 'V';
                    } else {
                        $processcode = 'F';
                    }
                } elseif ($sesroleactioncode === $PU) {
                    $processcode = 'K';
                } elseif ($sesroleactioncode === $PUAD) {

                    if ($actioncode == View::shared('removal_response_action')) {
                        $processcode = View::shared('respons_removal_processcode');   // PA
                    } elseif ($actioncode == View::shared('removal_parts_action')) {
                        $processcode = View::shared('parts_removal_processcode');  // PR
                    } else {
                        $processcode = 'C';
                    }
                }

                break;



            case 'approve':

                if ($sesroleactioncode === $PUAD) {
                    $processcode = 'A';
                }

                break;

            case 'reject':

                $rejectcount++;

                if ($sesroleactioncode === $PUAD) {
                    $processcode = 'I';
                }

                break;


            case 'fresh':

                $processcode = $request->input('processcode');

                if (
                    $sessionusertypecode === 'I' &&
                    !in_array($old_processcode, View::shared('DLC_TO_auditee_process'))
                ) {
                    $processcode = 'E';
                }

                break;

            case 'rejoinder':

                $rejoindercycle = empty($rejoindercycle)
                    ? 1
                    : $rejoindercycle + 1;

                $rejoinderstatus = 'Y';
                $processcode = 'U';

                break;


            default:
                throw new \Exception("Action was not defined");
        }


        $data = [

            'instid' => $instid,
            'paratype' => $paratype,
            'audityear' => $yearcode[0],
            'paranumber' => $request->input('paranumber'),
            'para_remarks' => $content,
            'statusflag' => 'Y',
            'followupid' => $followupid,
            'usertypecode' => $sessionusertypecode,
            'actroleactioncode' => $actroleactioncode,
            'rejoindercycle' => $rejoindercycle,
            'rejoinderstatus' => $rejoinderstatus,
            'rejectcount' => $rejectcount,


        ];

        $sessionusertypecode == 'A' ? $data['actioncode'] = $request->input('actioncode') : null;
        if ($sessionusertypecode == 'I' && $liabilityval == 'N') {
            $data['auditee_liability'] = $request->input('auditee_liability');
            $data['liabilty_type'] = $request->input('auditee_Retirement_type');
        }

        $fileUploadId = null;
        $fileupload = $request->file('fileupload');
        $destinationPath = '';

        $data['processcode'] = $processcode;

        $deactive_fileuploadids = $request->input('deactive_fileid') ? explode(',', $request->input('deactive_fileid')) : [];
        $active_fileuploadids = $request->input('active_fileid') ? explode(',', $request->input('active_fileid')) : [];

        if (!empty($deactive_fileuploadids)) {
            $asdf = $this->fileUploadService->deactive_uploadedfile('paraid', $paraid, $deactive_fileuploadids, self::$parafileupload_table);
        }

        $fileUploadId = null;

        if ((($action === 'insert') || ($action === 'update')) && ($request->hasFile('fileupload'))) {

            $filefolderdet = LagacyModel::getfilefolderdet('para', $instid);

            $parts = explode(',', $yearcode[0][0]); // ["16", "10"]
            $file_yearpart = implode('_', $parts);
            $filefolderdet = array_merge($filefolderdet, [$paratype], [$file_yearpart]);



            $uploadResult = $this->fileUploadService->multipleFileUpload(

                self::$parafileupload_table,
                'paraid',
                $paraid,
                $filefolderdet,
                $fileupload,
                $destinationPath,
                $active_fileuploadids,
                $deactive_fileuploadids,
                $rejoinderstatus,
                $rejoindercycle,
                $processcode,
                $instid,
                $rejectcount
            );
            // return $uploadResult;
            if (is_array($uploadResult) && isset($uploadResult['error'])) {
                return response()->json(['errors' => $uploadResult['error']], 400);
            } elseif ($uploadResult instanceof \Illuminate\Http\JsonResponse) {
                $fileUploadId = $uploadResult->getData(true)['uploaded_files'];
            }
        }

        if ($action === 'insert' && empty($paraid)) {
            $data['createdon'] = View::shared('get_nowtime');
            $data['createdby'] = $session_userid;

            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $session_userid;
        } else {
            $data['updatedon'] = View::shared('get_nowtime');
            $data['updatedby'] = $session_userid;
        }

        if (!$paraid) {
            $transactionno = LagacyModel::getmaxtranactionno();
            $data['transactionno'] = $transactionno;
        }

        if (
            ($statusflag === 'F' && $actionfor === 'forward') ||
            $actionfor === 'rejoinder'
        ) {

            // if (in_array($processcode, $to_auditee_forwardProcess)) {

            //     $frddata = LagacyModel::getfrwd_details($paraid);

            //     $data['forwardedtouserid'] = $frddata[0]->forwardedbyuserid ?? null;
            //     $data['forwardedtouserchargeid'] = $frddata[0]->forwardedbychargeid ?? null;

            //     $past_actioncode = $frddata[0]->actroleactioncode ?? null;
            // } else {

            $getforwarddetails = LagacyModel::getforwarddetails(
                $instid,
                $sesroleactioncode,
                $actionfor,
                $paraid,
                $rejoinderstatus,
                $actroleactioncode,
                $rejectcount,
                $stateofparacode,
                $retirementpara,
                $processcode
            );


            throw_if(empty($getforwarddetails), new \Exception("Forward details not found"));

            $data['forwardedtouserid'] = $getforwarddetails->deptuserid;
            $data['forwardedtouserchargeid'] = $getforwarddetails->chargeid;
            // }
        } elseif ($actionfor === 'fresh' && in_array($processcode, ['K', 'F', 'U', 'PA', 'PR'])) {

            $data['forwardedtouserid'] = $session_userid;
            $data['forwardedtouserchargeid'] = $sessionchargeid;
        } else {

            $data['forwardedtouserid'] = null;
            $data['forwardedtouserchargeid'] = null;
        }


        DB::beginTransaction();
        //return $paraid;
        try {
            $paradet = LagacyModel::createorinsertparadet($data, $paraid);

            if ((is_array($notype))) {

                if ($sessionusertypecode == 'I') {

                    $auditee_liability = $request->auditee_liability;
                    $auditee_Retirement_type = $request->auditee_Retirement_type;

                    $audiee_laibilityupdate =
                        ($auditee_liability === 'Y') ||
                        ($auditee_liability === 'N' && $auditee_Retirement_type === 'Y');
                }

                if ($liabilityval === 'Y' && $sesroleactioncode == view::shared('PUADroleactioncode') || ($sessionusertypecode == 'I' && $audiee_laibilityupdate) || ($sessionusertypecode == 'A')) {


                    if ($deleted_liabilityid) {
                        $deletedliabilitydel = explode(",", $deleted_liabilityid);
                        LagacyModel::deleteLiability($deletedliabilitydel, $session_userid);
                    }

                    $updateliability = LagacyModel::insertupdateparaLiability($liabilitydel, $liabilitydata, $old_processcode, $followupid, $session_userid, $auditeeflag, $sesroleactioncode);
                    //return $updateliability;
                    // $updateliability = LagacyModel::insertupdateparaLiability($liabilitydel, '', $followupid, $session_userid, $activestatus);
                }
            }
            //return;


            // return $paradet;
            throw_if(empty($paradet), new \Exception("Action failed due to some errors"));

            $paraid = $paradet->paraid;
            $tempnumber = $paradet->paranumber;
            $paratype = $paradet->paratype;
            // return $paraid;
            if ($fileUploadId) {
                $this->fileUploadService->insert_fileupload(
                    'paraid',
                    $paraid,
                    $fileUploadId,
                    self::$parafileupload_table,
                    $rejoinderstatus,
                    $rejoindercycle,
                    $processcode,
                    $rejectcount
                );
            }


            if ($statusflag == 'F') {
                $historydata = [
                    'paraid' => $paradet->paraid,
                    'paratype' => $paratype,
                    'instid' => $instid,
                    'audityear' => $yearcode[0],
                    'paranumber' => $request->input('paranumber'),
                    'para_remarks' => $content,
                    'statusflag' => 'Y',
                    'followupid' => $followupid,
                    'usertypecode' => $sessionusertypecode,
                    'processcode' => $paradet->processcode,
                    'forwardedtouserid' => $paradet->forwardedtouserid,
                    'forwardedtochargeid' => $paradet->forwardedtouserchargeid,
                    'transactionno' => $paradet->transactionno,
                    'forwardedon' => View::shared('get_nowtime'),
                    'createdon' => View::shared('get_nowtime'),
                    'createdby' => $session_userid,
                    'forwardedbyuserid' => $session_userid,
                    'forwardedbychargeid' => $sessionchargeid,
                    'transstatus' => 'A',
                    'actroleactioncode' => $actroleactioncode,
                    'actioncode' => $paradet->actioncode,
                    'rejoindercycle' => $paradet->rejoindercycle,
                    'rejoinderstatus' => $paradet->rejoinderstatus,
                    'rejectcount' => $rejectcount
                ];

                $historydata = LagacyModel::insert_parahistorydata($historydata, $paraid, $liabilitydel, $sesroleactioncode, $liabilityval);
                // return $historydata;

                if ($historydata) {
                    $update_parafileupload = LagacyModel::update_parafileupload($processcode, $session_userid, $old_processcode, $paradet->paraid, $sessionusertypecode, $paradet->rejoinderstatus, $paradet->rejoindercycle, $oldrejectcount, $rejectcount);

                    $smsModel = new SmsmailModel(new SmsService(), new PHPMailerService());
                    if ($actionfor == 'forward' || $actionfor == 'rejoinder') {
                        // $sentsms = $smsModel->sent_paramail($getforwarddetails, $processcode, $sesroleactioncode, $sessionusertypecode);
                    }
                }


                switch ($processcode) {

                    case 'F':
                        $message = 'Para has been forwarded to Para Settlement Auditor';
                        break;
                    case 'K':
                        $message = 'Para has been forwarded to Para Settlement AD';
                        break;
                    case 'U':
                        $message = 'Para has been forwarded to Auditee';
                        break;
                    case 'I':
                        $message = 'Para has been Rejected';
                        break;
                    case 'A':
                        $message = 'Para has been Approved';
                        break;
                    default:
                        $message = 'Para has been processed';
                        break;
                }
            } else {
                $message = 'Detail has been submitted successfully';
            }
            if ($paraid) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => ['paraid' => Crypt::encryptString($paraid), 'followupid' => Crypt::encryptString($followupid), 'tempnumberform' => $tempnumber, 'paratype' => $paratype]
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to insert the Para ',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }





    public  function fetch_instparadetails()
    {
        try {

            $userdet = session('user');
            $chargedet = session('charge');

            $userid   = $userdet->userid;
            $chargeid = $chargedet->userchargeid;


            $details = LagacyModel::fetch_instparadetails($userid, $chargeid);

            foreach ($details as $all) {

                $all->encrypted_paraid = Crypt::encryptString($all->paraid);
                $all->encrypted_followupid = Crypt::encryptString($all->followupid);
                unset($all->paraid);
                unset($all->followupid);
            }

            return response()->json(['data' => $details]);
        } catch (\Exception $e) {
            // DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong fetching the para details'
            ], 500);
        }
    }


    public function fetch_paradata(Request $request)
    {
        try {


            $paraid = Crypt::decryptstring($request->input('paraid'));

            throw_if(empty($paraid), new \Exception("Para details not found"));

            $userdet = session('user');
            $chargedet = session('charge');

            $userid   = $userdet->userid;
            $chargeid = $chargedet->chargeid;

            $sessionusertypecode  = $chargedet->usertypecode;

            $paradets = LagacyModel::fetch_paradata(null, $paraid, $userid, $sessionusertypecode);
            $paradetails    = $paradets['data'];
            $historydetails = $paradets['historydata'];

            foreach ($paradetails as $all) {
                $all->encrypted_paraid = Crypt::encryptString($all->paraid);
                unset($all->paraid);
            }


            if ($paraid) {
                if (empty($paradets)) {
                    return response()->json([
                        'success' => false,
                        'message' => ' Details not found  ',
                        'data' => null
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'message' => '',
                    'data' => $paradetails,
                    'historydata' => $historydetails
                ], 200);
            }
        } catch (\Exception $e) {
            // DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong on fetching para details '
            ], 500);
        }
    }

    public function fetch_transparadetails()
    {
        try {

            $details = LagacyModel::fetch_transparadetails();

            return response()->json(['data' => $details]);
        } catch (\Exception $e) {
            // DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }






    public function parastatus_dropdown(Request $request)
    {
        $viewName   = $request->route('viewName');

        $session = session('charge');
        $deptcode = $session->deptcode;

        //  $legacyyear = LagacyModel::getlegacyyear($deptcode);
        $normalyear = LagacyModel::getaudityear($deptcode);

        return view($viewName, compact('normalyear'));
    }

    public function fetch_parastatus(Request $request)
    {
        try {
            $yearcode = $request->yearcode;
            $session = session('charge');
            $usertypecode = $session->usertypecode;


            throw_if(empty($yearcode), new \Exception("Audit Year has not been provided"));

            $parastatus_data = LagacyModel::fetch_parastatus($yearcode, $usertypecode);
            return response()->json(['data' => $parastatus_data]);
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

    public function fetch_historyparastatus(Request $request)
    {
        try {
            $paraid = $request->id;
            $session = session('charge');

            throw_if(empty($paraid), new \Exception("Para detail has  not been provided"));

            $parastatus_data = LagacyModel::fetch_historyparastatus($paraid);
            return response()->json(['data' => $parastatus_data]);
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

    public function delete_lagacydata(Request $request)
    {

        try {

            $followupid = $request->filled('followupid') ? Crypt::decryptstring($request->followupid) : null;
            $instid = $request->filled('instid') ? Crypt::decrypt($request->instid) : null;

            throw_if(empty($followupid), new \Exception("Legacy details not found"));
            throw_if(empty($instid), new \Exception("Institution details not found"));

            $session = session('user');
            $userid = $session->userid;

            $data = [
                'followupid' => $followupid,
                'instid' => $instid,
                'isduplicate' => 'Y',
                'statusflag' => 'Y',
                'createdby' => $userid,
                'createdon' => View::shared('get_nowtime')
            ];

            $deleted_lagacy = LagacyModel::delete_lagacydata($data, $userid);

            if ($deleted_lagacy) {
                return response()->json([
                    'success' => true,
                    'message' => 'Legacy data deleted successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete the legacy data',
                ]);
            }
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting Legacy DAta'
            ], 500);
        }
    }


    public function update_lagacydata(Request $request)
    {
        try {

            $followupid = $request->filled('followupid') ? Crypt::decryptstring($request->followupid) : null;
            $instid = $request->filled('instid') ? Crypt::decrypt($request->instid) : null;

            throw_if(empty($followupid), new \Exception("Legacy details not found"));
            throw_if(empty($instid), new \Exception("Institution details not found"));

            $session = session('user');
            $userid = $session->userid;

            //$oldaudityear = $request->input('oldyearcode');
            //$newaudityear = $request->input('newyearcode');
            $typeofparacode  = $request->input('typeofparacode');
            $parano = $request->input('parano');
            $oldaudityear = json_encode([(int)$request->input('oldyearcode')]);
            $newaudityear = json_encode([(int)$request->input('newyearcode')]);

            $data = [
                'followupid' => $followupid,
                'instid' => $instid,
                'oldaudityear' => $oldaudityear,
                'newaudityear' => $newaudityear,
                'statusflag' => 'Y',
                'createdby' => $userid,
                'createdon' => View::shared('get_nowtime'),
            ];

            $update_lagacy  = LagacyModel::update_lagacydata($data, $userid, $parano, $typeofparacode);


            if ($update_lagacy) {
                return response()->json([
                    'success' => true,
                    'message' => 'Legacy data was updated successfully',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update the legacy data',
                ]);
            }
        } catch (DecryptException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ID provided'
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'An error occurred while updating Legacy Data'
            ], 500);
        }
    }
}
