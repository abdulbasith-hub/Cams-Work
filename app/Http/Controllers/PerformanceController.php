<?php

namespace App\Http\Controllers;

use App\Models\BaseModel;
use App\Models\FieldAuditModel;
use App\Models\PerformanceModel;
use App\Services\FileUploadService;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class PerformanceController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;

    public static function fetch_dept()
    {
        $dept = PerformanceModel::deptfetch();

        return view('Performance.praudit_title', compact('dept'));
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

        $category = PerformanceModel::getcategoryByDept($deptcode);

        if ($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function getSubcategory(Request $request)
    {
        $request->validate([
            'catcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $catcode = $request->input('catcode');

        $subcategoryData = PerformanceModel::getSubcategoryByCategory($catcode);

        if ($subcategoryData->isNotEmpty()) {
            return response()->json([
                'success' => true,
                'subcategoryData' => $subcategoryData
            ]);
        } else {
            return response()->json([
                'success' => false,
                'subcategoryData' => []
            ]);
        }
    }

    public function InsertUpdatePrauditmasterrecords(Request $request)
    {
        try {
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;
            $deptcodeInput = (string) $request->input('deptcode');

            $hideSubcategory = in_array($deptcodeInput, ['01', '05', '1', '5'], true);
            $subcategoryRules = [$hideSubcategory ? 'nullable' : 'required', 'regex:/^\d+$/'];
            $action = $request->input('action', 'insert');
            $praudittitleid = null;
            if ($action === 'update' && $request->filled('praudittitleid')) {
                $praudittitleid = Crypt::decryptString($request->input('praudittitleid'));
            }

            $request->validate([
                'deptcode' => ['required', 'regex:/^\d+$/'],
                'category' => ['required', 'regex:/^\d+$/'],
                'subcategory' => $subcategoryRules,
                'titleename' => ['required', 'string', 'min:10', 'max:150', 'not_regex:/(.)\1{5,}/u'],
                'titletname' => ['required', 'string', 'min:10', 'max:150', 'not_regex:/(.)\1{5,}/u'],
                // Correct regex for Y or N
                'status' => ['required', 'in:Y,N'],
                // File required on insert; optional on update
                'praudit_file' => [$action === 'insert' ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:2048'],
            ], [
                'deptcode.required' => 'Department is required.',
                'deptcode.regex' => 'Department must be numeric.',
                'category.required' => 'Category is required.',
                'category.regex' => 'Category must be numeric.',
                'subcategory.required' => 'Subcategory is required.',
                'subcategory.regex' => 'Subcategory must be numeric.',
                'titleename.required' => 'Title English Name is required.',
                'titleename.min' => 'Title English Name must be at least 10 characters.',
                'titleename.max' => 'Title English Name must not exceed 150 characters.',
                'titleename.not_regex' => 'Avoid long repeated characters in Title English Name.',
                'titletname.required' => 'Title Tamil Name is required.',
                'titletname.min' => 'Title Tamil Name must be at least 10 characters.',
                'titletname.max' => 'Title Tamil Name must not exceed 150 characters.',
                'titletname.not_regex' => 'Avoid long repeated characters in Title Tamil Name.',
                'status.required' => 'Status is required.',
                'status.in' => 'Status must be Y or N.',
                'praudit_file.required' => 'File upload is required.',
                'praudit_file.mimes' => 'Only PDF files are allowed.',
                'praudit_file.max' => 'File size must not exceed 3MB.',
            ]);
            $fileuploadId = null;
            $deptcode = $request->input('deptcode');
            $catcode = $request->input('category');
            $subcatid = $request->input('subcategory');
            $effectiveUploadId = $request->input('uploadid') ?: $request->input('existing_uploadid');
            $destinationarray = [
                $deptcode,
                'Performancesaudit',
                $catcode,
            ];

            if (!empty($subcatid) && $subcatid !== 'null') {
                $destinationarray[] = $subcatid;
            }
            // dd($request->all());
            if ($request->hasFile('praudit_file')) {
                $file = $request->file('praudit_file');

                $destinationPath = 'storage/uploads/';

                $uploadResult = $this->fileUploadService->uploadFile_removefolder(
                    $file,
                    $destinationPath,
                    $effectiveUploadId ?? '',
                    $destinationarray
                );

                $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
            } elseif ($action === 'update' && !empty($effectiveUploadId)) {
                $this->fileUploadService->relocateFilePathByUploadId($effectiveUploadId, $destinationarray);
                $fileuploadId = $effectiveUploadId;
            }

            $data = [
                'deptcode' => $request->input('deptcode'),
                'catcode' => $request->input('category'),
                'subcatid' => $request->input('subcategory'),
                'titleename' => $request->input('titleename'),
                'titletname' => $request->input('titletname'),
                'statusflag' => $request->input('status'),
                'updatedon' => View::shared('get_nowtime'),
                'updatedby' => $userid,
            ];

            if ($action === 'insert') {
                $data['createdon'] = View::shared('get_nowtime');
                $data['createdby'] = $userid;
            }

            if ($fileuploadId) {
                $data['fileuploadid'] = $fileuploadId;
            }
            // dd($data);
            // $data =

            $prtitledata = PerformanceModel::insertupdate_praudittitle($data, $praudittitleid);

            return response()->json([
                'success' => true,
                'data' => $prtitledata,
                'message' => 'prtitle_created'
            ], 200);
        } catch (\Exception $e) {
            $statusCode = $e->getCode();
            if (!is_int($statusCode) || $statusCode < 400 || $statusCode > 599) {
                $statusCode = 500;
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], $statusCode);
        }
    }

    public function fetch_prauditmasterrecords(Request $request)
    {
        $praudittitleid = $request->has('praudittitleid') ? Crypt::decryptString($request->praudittitleid) : null;
        $chargedel = PerformanceModel::fetch_prauditmasterrecords($praudittitleid, 'audit.mst_praudit_title');
        foreach ($chargedel as $all) {
            if (isset($all->praudittitleid)) {
                $all->encrypted_praudittitleid = Crypt::encryptString($all->praudittitleid);
            }
        }
        return response()->json([
            'success' => !$chargedel->isEmpty(),
            'message' => $chargedel->isEmpty() ? 'User not found' : '',
            'data' => $chargedel->isEmpty() ? null : $chargedel
        ], $chargedel->isEmpty() ? 404 : 200);
    }

    public static function fetchdeptforperformanceaudit()
    {
        $dept = PerformanceModel::Modaldeptfetch();
        $financialyear = PerformanceModel::getDFinancialyear();
        $pafinal = PerformanceModel::pafinalized();
        $quarter = PerformanceModel::getQuarter();
        $region = PerformanceModel::getRegion();
        $district = PerformanceModel::getDistrict();

        return view('Performance.prauditinstmapping', compact('dept', 'financialyear', 'quarter', 'region', 'district', 'pafinal'));
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

        $category = PerformanceModel::commoncategoryfetch($deptcode);
        $region = PerformanceModel::getRegionsByDept($deptcode);
        $mandaysandteam = PerformanceModel::getmandaysandteamsizeDept($deptcode);
        $priority = PerformanceModel::getpriority($deptcode);

        $teamsize = $mandaysandteam->first()->teamsize ?? '';
        $mandays = $mandaysandteam->first()->mandays ?? '';

        return response()->json([
            'success' => true,
            'categories' => $category,
            'regions' => $region,
            'teamsize' => $teamsize,
            'mandays' => $mandays,
            'priority' => $priority
        ]);
    }

    public function getsubCategoriesBasedOnperformance(Request $request)
    {
        $request->validate([
            // 'category' => ['required', 'array'],
            'category' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');

        $subcategory = PerformanceModel::commmonSubcategoryByCategory($category);

        return response()->json($subcategory);
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
        $district = PerformanceModel::getdistrictByregion($regioncode, $deptcode);

        // Return JSON response
        if ($district->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $district]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function gettitlebasedonsubcategorycommon(Request $request)
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

        $title = PerformanceModel::gettitlesubcategory($deptcode, $category, $subcatcode);

        return response()->json([
            'success' => true,
            'titles' => $title,
        ]);
    }

    public function getinstituionbasedondistcommon(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'category' => ['required', 'string', 'regex:/^\d+$/'],
            'subcatcode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'region' => ['required', 'string', 'regex:/^\d+$/'],
            'district' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');
        $deptcode = $request->input('deptcode');
        $subcatcode = $request->input('subcatcode');
        $regioncode = $request->input('region');
        $distcode = $request->input('district');

        $institution = PerformanceModel::getinstituionbasedondistcommon($deptcode, $category, $subcatcode, $regioncode, $distcode);

        return response()->json([
            'success' => true,
            'institutions' => $institution,
        ]);
    }

    public function performanceaudit_insertupdate(Request $request)
    {
        try {
            $request->validate([
                'financialyear' => 'required|string',
                'auditquarter' => 'required|in:Q1,Q2,Q3,Q4',
                'statusflag' => 'required|in:Y,N',
            ]);

            $action = $request->action;

            if ($action === 'insert') {
                $request->validate([
                    'instmappingcode' => 'required|array',
                    'instmappingcode.*' => 'integer',
                ]);
            } else {
                $request->validate([
                    'instmappingcode' => 'required|integer',
                ]);
            }

            $user = session('user');
            if (!$user || !isset($user->userid)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Session not found'
                ], 400);
            }

            $userid = $user->userid;
            $action = $request->action;

            $pafinal = PerformanceModel::pafinalized($request->deptcode, $request->distcode);

            if ($pafinal === 'F') {
                return response()->json([
                    'success' => false,
                    'message' => 'The plan is finalized. You cannot insert/update this record.'
                ], 400);
            }

            DB::beginTransaction();

            /*
             * |------------------------------------------
             * | INSERT (Multiple instid)
             * |------------------------------------------
             */
            if ($action === 'insert') {
                foreach ($request->instmappingcode as $instid) {
                    $data = [
                        'instid' => $instid,
                        'prioritycode' => $request->prioritycode,
                        'quartercode' => $request->auditquarter,
                        'finyearcode' => $request->financialyear,
                        'praudittitleid' => $request->titleid,
                        'statusflag' => $request->statusflag,
                        'createdon' => now(),
                        'createdby' => $userid,
                    ];

                    PerformanceModel::prauditinstmapping_insertupdate($data);
                }
            }

            /*
             * |------------------------------------------
             * | UPDATE (Single row update)
             * |------------------------------------------
             */
            if ($action === 'update') {
                $prauditmapid = Crypt::decryptString($request->prauditmapid);

                $data = [
                    'instid' => $request->instmappingcode,
                    'prioritycode' => $request->prioritycode,
                    'quartercode' => $request->auditquarter,
                    'finyearcode' => $request->financialyear,
                    'praudittitleid' => $request->titleid,
                    'statusflag' => $request->statusflag,
                    'updatedon' => now(),
                    'updatedby' => $userid,
                ];

                PerformanceModel::prauditinstmapping_insertupdate($data, $prauditmapid);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Performance Audit created / Updated Successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function performanceaudit_fetchdata(Request $request)
    {
        $prauditmapid = $request->has('prauditmapid') ? Crypt::decryptString($request->prauditmapid) : null;
        $performanceaudit = PerformanceModel::performanceaudit_fetchdata($prauditmapid, 'audit.mst_prauditinstmapping');
        foreach ($performanceaudit as $all) {
            $all->encrypted_prauditmapid = Crypt::encryptString($all->prauditmapid);

            unset($all->prauditmapid);
        }
        return response()->json([
            'success' => !$performanceaudit->isEmpty(),
            'message' => $performanceaudit->isEmpty() ? 'User not found' : '',
            'data' => $performanceaudit->isEmpty() ? null : $performanceaudit
        ], $performanceaudit->isEmpty() ? 404 : 200);
    }

    public function prf_dropdown($encrypted_auditscheduleid, Request $request)
    {
        try {
            if ($encrypted_auditscheduleid) {
                $auditscheduleid = Crypt::decryptString($encrypted_auditscheduleid);
                $instid = $request->query('instid');
                $auditmode = $request->query('auditmode');
                $auditplanid = $request->query('auditplanid');
            }

            if ($auditscheduleid === null) {
                throw new Exception('Audit schedule ID not found');
            }

            if ($auditmode != view()->shared('performance_audit')) {
                abort(403, 'Unauthorized access');
            }

            $chargeData = session('charge');
            $session_deptcode = $chargeData->deptcode;
            $session_usertypecode = $chargeData->usertypecode;
            $userData = session('user');
            $session_userid = $userData->userid;

            if ($session_userid === null) {
                throw new Exception('User ID not found');
            }

            $titleDetails = PerformanceModel::get_titledet($auditscheduleid);
            $scheduledel = FieldAuditModel::getscheduledel_basedonuser($session_userid, $auditscheduleid);
            $teamheaddel = FieldAuditModel::getAuditScheduleHeaddel($auditscheduleid);
            $severitydel = FieldAuditModel::getSeverity();
            $schemename = FieldAuditModel::getSchemename();
            $serious = FieldAuditModel::getSerious();
            $getMainobjection = FieldAuditModel::getMainobjection($session_userid, $auditscheduleid);

            $encrypted_userid = Crypt::encryptString($session_userid);

            if ($titleDetails->isEmpty()) {
                throw new Exception('Details not found');
            }

            $existingData = PerformanceModel::getExistingData($auditscheduleid, $session_userid);

            $fileUploads = null;
            $remarks = '';
            $fileuploadid = null;
            $fileUrl = null;

            if ($existingData) {
                $remarks = '';
                if (!empty($existingData->remarks)) {
                    $decodedRemarks = json_decode($existingData->remarks, true);
                    $remarks = $decodedRemarks ?? $existingData->remarks;
                }

                $fileuploadid = $existingData->fileuploadid ?? null;

                if ($fileuploadid) {
                    $fileUploads = PerformanceModel::getFileDetails($fileuploadid);

                    if ($fileUploads && isset($fileUploads->filepath)) {
                        $normalizedPath = $this->normalizeFilePath($fileUploads->filepath);

                        $fileUrl = asset($normalizedPath);

                        $fileUploads->full_url = $fileUrl;
                        $fileUploads->normalized_path = $normalizedPath;
                    }
                }
            }

            // dd($fileuploadid);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'titleDetails' => $titleDetails,
                    'fileUploads' => $fileUploads,
                    'auditscheduleid' => $auditscheduleid,
                    'auditplanid' => $auditplanid,
                    'scheduledel' => $scheduledel,
                    'remarks' => $remarks,
                    'fileuploadid' => $fileuploadid,
                    'fileUrl' => $fileUrl
                ]);
            }

            return view('Performance.per_fieldaudit', compact(
                'encrypted_auditscheduleid',
                'titleDetails',
                'fileUploads',
                'auditscheduleid',
                'auditplanid',
                'scheduledel',
                'remarks',
                'fileuploadid',
                'fileUrl'
            ));
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            echo $e->getMessage();
        }
    }

    private function normalizeFilePath($dbFilePath)
    {
        if (!$dbFilePath)
            return '';

        $normalizedPath = str_replace('\\', '/', $dbFilePath);

        $normalizedPath = preg_replace('/\/+/', '/', $normalizedPath);

        if (strpos($normalizedPath, 'uploads/') === 0) {
            $normalizedPath = '/' . $normalizedPath;
        } elseif (strpos($normalizedPath, '/uploads/') === 0) {
            $normalizedPath = '/' . $normalizedPath;
        } elseif (strpos($normalizedPath, 'storage/') === 0) {
            $normalizedPath = '/' . $normalizedPath;
        } elseif (strpos($normalizedPath, '/storage/') !== 0) {
            $normalizedPath = '/uploads/' . basename($normalizedPath);
        }

        return $normalizedPath;
    }

    public function savePrauditTranspara(Request $request)
    {
        try {
            $request->validate([
                'auditscheduleid' => 'required|integer',
                'auditplanid' => 'required|integer',
                'remarks' => 'required|string',
                'uploadid' => 'nullable|integer',
                'existing_uploadid' => 'nullable|integer',
                'statusflag' => 'required|in:E,F',
                'field_audit_file' => 'nullable|file|mimes:pdf|max:2048',
            ], [
                'field_audit_file.mimes' => 'The file must be a PDF document',
                'field_audit_file.max' => 'The file size must not exceed 2MB',
                'field_audit_file.file' => 'Please upload a valid file'
            ]);

            $user = session('user');

            if (!$user || empty($user->userid)) {
                throw new Exception('User not authenticated');
            }

            $createdby = $user->userid;

            $sessionchargedel = session('charge');
            $deptcode = $sessionchargedel->deptcode ?? '';
            $distcode = $sessionchargedel->distcode ?? '';

            $effectiveUploadId = $request->uploadid ?: $request->existing_uploadid;
            $fileuploadId = $effectiveUploadId;

            $destinationarray = [
                $deptcode,
                $distcode,
                'performance_audit',
                $request->auditscheduleid
            ];

            if ($request->hasFile('field_audit_file') && $request->file('field_audit_file')->isValid()) {
                $file = $request->file('field_audit_file');

                if ($file->getMimeType() !== 'application/pdf') {
                    throw new Exception('Invalid file type. Only PDF files are allowed.');
                }

                if ($file->getSize() > 2048 * 1024) {  // 2MB in bytes
                    throw new Exception('File size must not exceed 2MB.');
                }

                $uploadResult = $this->fileUploadService->uploadFile(
                    $file,
                    'uploads/',
                    $effectiveUploadId ?? '',
                    $destinationarray
                );

                if ($uploadResult && $uploadResult->getData()) {
                    $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
                }
            }

            $existingRecord = PerformanceModel::getExistingData($request->auditscheduleid, $createdby);

            $data = [
                'auditscheduleid' => $request->auditscheduleid,
                'auditplanid' => $request->auditplanid,
                'remarks' => $request->remarks,
                'fileuploadid' => $fileuploadId,
                'schteammemberid' => $createdby,
                'createdby' => $createdby,
                'statusflag' => $request->statusflag
            ];

            if ($existingRecord) {
                $data['updatedby'] = $createdby;
                PerformanceModel::updateRecord($existingRecord->praudittransid, $data);
                $message = $request->statusflag === 'F' ? 'Performance Audit finalized successfully' : 'Draft updated successfully';
            } else {
                PerformanceModel::createRecord($data);
                $message = $request->statusflag === 'F' ? 'Performance Audit finalized successfully' : 'Draft saved successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'statusflag' => $request->statusflag,
                'is_finalized' => $request->statusflag === 'F',
                'fileuploadid' => $fileuploadId
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving record: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkStatus(Request $request)
    {
        try {
            $request->validate([
                'auditscheduleid' => 'required|integer'
            ]);

            $user = session('user');
            $userid = $user->userid ?? null;

            if (!$userid) {
                return response()->json([
                    'exists' => false,
                    'message' => 'User not authenticated'
                ]);
            }

            $existingRecord = PerformanceModel::getExistingData($request->auditscheduleid, $userid);

            if ($existingRecord) {
                return response()->json([
                    'exists' => true,
                    'statusflag' => $existingRecord->statusflag ?? 'E'
                ]);
            }

            return response()->json([
                'exists' => false
            ]);
        } catch (Exception $e) {
            return response()->json([
                'exists' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


  // ---------------------Performance Audit Consolidation Report--------------------------//

    public static function prauditreport()
    {
        $departments = ReportModel::commondeptfetch();
        $Category = ReportModel::CategoryFetchData();
        $regions = ReportModel::regionfetch();
        $districts = ReportModel::districtfetch();
        $quarters = ReportModel::quarterfetch();
        $audityear = ReportModel::getAuditYear();
        $financialyear = ReportModel::getDFinancialyear();

        return view('report.prauditconsolidationreport', compact('Category', 'departments', 'regions', 'districts', 'quarters', 'audityear', 'financialyear'));
    }

    public static function getPerformanceAuditTitles(Request $request)
    {
        try {
            $request->validate([
                'deptcode' => 'required|string',
            ]);

            $deptcode = $request->input('deptcode');

            $titles = DB::table('audit.praudit_report as pr')
                ->join('audit.mst_praudit_title as mpt', function ($join) {
                    $join->on('mpt.praudittitleid', '=', 'pr.praudittitleid');
                })
                ->where('pr.deptcode', $deptcode)
                ->whereIn('pr.remarkstype', ['F', 'R'])
                ->whereIn('pr.statusflag', ['F', 'A', 'P'])
                ->where('mpt.statusflag', 'Y')
                ->distinct()
                ->select(
                    'mpt.praudittitleid',
                    'mpt.titleename',
                    'mpt.catcode',
                    'mpt.subcatid'
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => $titles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getPerformancereportAuditTitles(Request $request)
    {
        try {

            $request->validate([
                'deptcode' => 'required|string',
                'financialyear' => 'required|string',
            ]);

            $deptcode = $request->input('deptcode');
            $financialyear = $request->input('financialyear');

            $titles = DB::table('audit.mst_praudit_title as mpt')
                ->join('audit.mst_prauditinstmapping as mpim', function ($join) {
                    $join->on('mpim.praudittitleid', '=', 'mpt.praudittitleid');
                })
                ->where('mpt.deptcode', $deptcode)
                ->where('mpim.finyearcode', $financialyear)
                ->where('mpt.statusflag', 'Y')
                ->where('mpim.statusflag', 'F')
                ->distinct()
                ->select(
                    'mpt.praudittitleid',
                    'mpt.titleename',
                    'mpt.catcode',
                    'mpt.subcatid'
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => $titles,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getPerformanceAuditSummary(Request $request)
    {
        try {
            $request->validate([
                'deptcode' => 'required|string',
                'financialyear' => 'required|string',
                'praudittitleid' => 'required|integer',
            ]);

            $deptcode = $request->input('deptcode');
            $financialyear = $request->input('financialyear');
            $praudittitleid = $request->input('praudittitleid');

            $summary = DB::table('audit.auditplan as plan')
                ->join('audit.mst_institution as inst', 'plan.instid', '=', 'inst.instid')
                ->join('audit.mst_region as r', 'inst.regioncode', '=', 'r.regioncode')
                ->join('audit.mst_district as d', 'inst.distcode', '=', 'd.distcode')
                ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {
                    $join->on('prfmap.instid', '=', 'plan.instid')
                        ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                        ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                        ->where('prfmap.praudittitleid', '=', $praudittitleid);
                })
                ->Join('audit.inst_auditschedule as sch', 'plan.auditplanid', '=', 'sch.auditplanid')
                ->Join('audit.praudit_transpara as praudit', function ($join) {
                    $join->on('sch.auditscheduleid', '=', 'praudit.auditscheduleid')
                        ->on('plan.auditplanid', '=', 'praudit.auditplanid')
                        ->where('praudit.statusflag', 'F');
                })
                ->where('inst.deptcode', $deptcode)
                ->where('plan.auditmode', 'P')
                ->where('plan.statusflag', 'F')
                ->where('inst.statusflag', 'Y')
                ->where('sch.statusflag', 'F')
                ->whereNotNull('sch.entrymeetdate')
                ->select(
                    'r.regioncode',
                    'r.regionename as regionname',
                    'd.distcode',
                    'd.distename as districtname',

                    DB::raw('COUNT(DISTINCT inst.instid) as total_inst'),

                    DB::raw("
                    COUNT(DISTINCT CASE
                        WHEN sch.auditscheduleid IN (
                            SELECT p.auditscheduleid
                            FROM audit.praudit_transpara p
                            GROUP BY p.auditscheduleid
                            HAVING COUNT(*) = COUNT(
                                CASE
                                    WHEN p.prremarksverifyflag = 'Y'
                                    AND (
                                        p.fileuploadid IS NULL
                                        OR p.prfileverifyflag = 'Y'
                                    )
                                    THEN 1
                                END
                            )
                        )
                        THEN sch.auditscheduleid
                    END) AS finalized
                    "),

                    DB::raw("
                    COUNT(DISTINCT CASE
                        WHEN sch.auditscheduleid NOT IN (
                            SELECT p.auditscheduleid
                            FROM audit.praudit_transpara p
                            GROUP BY p.auditscheduleid
                            HAVING COUNT(*) = COUNT(
                                CASE
                                    WHEN p.prremarksverifyflag = 'Y'
                                    AND (
                                        p.fileuploadid IS NULL
                                        OR p.prfileverifyflag = 'Y'
                                    )
                                    THEN 1
                                END
                            )
                        )
                        THEN sch.auditscheduleid
                    END) AS pending
                    "),

                    DB::raw('COUNT(DISTINCT sch.auditscheduleid) as total_schedules')
                )
                ->groupBy('r.regioncode', 'r.regionename', 'd.distcode', 'd.distename')
                ->orderBy('r.regionename')
                ->orderBy('d.distename')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $summary,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getPerformanceAuditInstitutions(Request $request)
    {
        try {

            $request->validate([
                'deptcode' => 'required|string',
                'financialyear' => 'required|string',
                'praudittitleid' => 'required|integer',
                'regioncode' => 'required|string',
                'districtcode' => 'required|string',
            ]);

            $deptcode = $request->input('deptcode');
            $financialyear = $request->input('financialyear');
            $praudittitleid = $request->input('praudittitleid');
            $regioncode = $request->input('regioncode');
            $districtcode = $request->input('districtcode');
            $verificationStatus = $request->input('verification_status');

            $query = DB::table('audit.mst_institution as inst')

                ->join('audit.auditplan as plan', function ($join) use ($deptcode) {

                    $join->on('inst.instid', '=', 'plan.instid')
                        ->where('inst.deptcode', '=', $deptcode)
                        ->where('plan.auditmode', '=', 'P')
                        ->where('plan.statusflag', '=', 'F');
                })

                ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {

                    $join->on('prfmap.instid', '=', 'plan.instid')
                        ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                        ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                        ->where('prfmap.praudittitleid', '=', $praudittitleid);
                })

                ->join('audit.inst_auditschedule as sch', function ($join) {

                    $join->on('plan.auditplanid', '=', 'sch.auditplanid')
                        ->where('sch.statusflag', '=', 'F')
                        ->whereNotNull('sch.entrymeetdate');
                })

                ->where('inst.deptcode', $deptcode)
                ->where('inst.regioncode', $regioncode)
                ->where('inst.distcode', $districtcode)
                ->where('inst.statusflag', 'Y');

            /*
            |--------------------------------------------------------------------------
            | Verification Logic
            |--------------------------------------------------------------------------
            */

            $verificationCase = "
            CASE
                WHEN sch.auditscheduleid IN (
                    SELECT p.auditscheduleid
                    FROM audit.praudit_transpara p
                    WHERE p.statusflag = 'F'
                    GROUP BY p.auditscheduleid
                    HAVING COUNT(*) = COUNT(
                        CASE
                            WHEN p.prremarksverifyflag = 'Y'
                            AND (
                                p.fileuploadid IS NULL
                                OR p.prfileverifyflag = 'Y'
                            )
                            THEN 1
                        END
                    )
                )
                THEN 'Verified'
                ELSE 'Pending'
            END
        ";

            /*
            |--------------------------------------------------------------------------
            | Filter Verification Status
            |--------------------------------------------------------------------------
            */

            if ($verificationStatus === 'F') {

                $query->whereRaw("$verificationCase = 'Verified'");

            } elseif ($verificationStatus === 'pending') {

                $query->whereRaw("$verificationCase = 'Pending'");
            }

            $institutions = $query->select(
                'inst.instid',
                'inst.deptcode',
                'inst.instename',
                'inst.insttname',

                DB::raw("$verificationCase as verification_status"),

                DB::raw("
                CONCAT(
                    inst.instename,
                    ' - ',
                    plan.auditquartercode
                ) as inst_display_name
            "),

                'plan.auditplanid',
                'sch.auditscheduleid'
            )
                ->distinct()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $institutions,
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getPrauditReportRemarks(Request $request)
    {
        try {
            $request->validate([
                'deptcode' => 'required',
                'praudittitleid' => 'required|integer',
            ]);

            $deptcode = $request->input('deptcode');
            $praudittitleid = $request->input('praudittitleid');

            $reports = DB::table('audit.praudit_report')
                ->where('deptcode', $deptcode)
                ->where('praudittitleid', $praudittitleid)
                ->whereIn('statusflag', ['Y', 'P', 'A', 'F'])
                ->select('remarks', 'remarkstype', 'updatedby', 'updatedon', 'statusflag', 'fileuploadid')
                ->get();

            $findings = null;
            $recommendations = null;
            $attachments = null;
            $status = null;

            foreach ($reports as $report) {
                if ($report->remarkstype === 'F') {
                    $findings = $report;
                } elseif ($report->remarkstype === 'R') {
                    $recommendations = $report;
                } elseif ($report->remarkstype === 'A') {
                    $attachments = $report;
                    if ($attachments && $attachments->fileuploadid) {
                        $attachments->fileuploadid = json_decode($attachments->fileuploadid, true);
                    }
                }
            }

            // Determine if there's actual content in findings/recommendations
            $hasFindingsContent = false;
            $hasRecommendationsContent = false;

            if ($findings && $findings->remarks) {
                $decodedFindings = json_decode($findings->remarks, true);
                if (is_string($decodedFindings)) {
                    $decodedFindings = $decodedFindings;
                }
                $findingsText = strip_tags($decodedFindings ?? $findings->remarks);
                $hasFindingsContent = $findingsText && trim($findingsText) !== '' && trim($findingsText) !== '""';
            }

            if ($recommendations && $recommendations->remarks) {
                $decodedRecommendations = json_decode($recommendations->remarks, true);
                if (is_string($decodedRecommendations)) {
                    $decodedRecommendations = $decodedRecommendations;
                }
                $recommendationsText = strip_tags($decodedRecommendations ?? $recommendations->remarks);
                $hasRecommendationsContent = $recommendationsText && trim($recommendationsText) !== '' && trim($recommendationsText) !== '""';
            }

            // Only set status if there's actual content OR if status is finalized (P/A/F)
            if ($reports->isNotEmpty()) {
                $firstReport = $reports->first();
                $statusFromDb = $firstReport->statusflag;

                // If status is finalized (P, A, F), keep it
                if (in_array($statusFromDb, ['P', 'A', 'F'])) {
                    $status = $statusFromDb;
                }
                // If status is Y but no content, treat as null (no data)
                elseif ($statusFromDb === 'Y' && ! $hasFindingsContent && ! $hasRecommendationsContent) {
                    $status = null;
                }
                // Otherwise use the DB status
                else {
                    $status = $statusFromDb;
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'findings' => $findings,
                    'recommendations' => $recommendations,
                    'attachments' => $attachments,
                    'status' => $status,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getinstitutionremarksforpraudit(Request $request)
    {
        try {
            $request->validate([
                'instid' => 'required|integer',
                'praudittitleid' => 'required|integer',
            ]);

            $instid = $request->input('instid');
            $praudittitleid = $request->input('praudittitleid');

            // Get audit info
            $auditInfo = DB::table('audit.auditplan as plan')
                ->join('audit.inst_auditschedule as sch', 'plan.auditplanid', '=', 'sch.auditplanid')
                ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {
                    $join->on('prfmap.instid', '=', 'plan.instid')
                        ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                        ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                        ->where('prfmap.praudittitleid', '=', $praudittitleid);
                })
                ->where('plan.instid', $instid)
                ->where('plan.statusflag', 'F')
                ->where('plan.auditmode', 'P')
                ->where('sch.statusflag', 'F')
                ->whereNotNull('sch.entrymeetdate')
                ->select('plan.auditplanid', 'sch.auditscheduleid')
                ->first();

            if (! $auditInfo) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                ]);
            }

            $remarks = PerformanceModel::getinstitutionremarksforpraudit($instid);

            $overallTransparaRecord = DB::table('audit.praudit_transpara')
                ->where('auditplanid', $auditInfo->auditplanid)
                ->where('auditscheduleid', $auditInfo->auditscheduleid)
                ->where('statusflag', 'F')
                ->whereNull('schteammemberid')
                ->select('praudittransid', 'fileinreportflag')
                ->first();

            $fileFlags = [];
            if ($overallTransparaRecord && $overallTransparaRecord->fileinreportflag) {
                $fileFlags = json_decode($overallTransparaRecord->fileinreportflag, true) ?: [];
            }

            $memberFileFlags = [];
            foreach ($remarks as $remark) {
                if ($remark->fileuploadid && $remark->fileinreportflag === 'Y') {
                    $memberFileFlags[$remark->fileuploadid] = 'Y';
                }
            }

            $allFlags = array_merge($fileFlags, $memberFileFlags);

            return response()->json([
                'success' => true,
                'data' => $remarks,
                'file_flags' => $allFlags,
                'praudittransid' => $overallTransparaRecord->praudittransid ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function savePerformanceAuditRemarks(Request $request)
    {
        try {
            $request->validate([
                'praudittitleid' => 'required|integer',
                'findings' => 'required|string',
                'recommendations' => 'nullable|string',
                'attachments' => 'nullable|array',
                'attachments.*' => 'integer',
                'financialyear' => 'required|string',
            ]);

            $praudittitleid = $request->input('praudittitleid');
            $findings = $request->input('findings');
            $recommendations = $request->input('recommendations');
            $attachments = $request->input('attachments', []);
            $financialyear = $request->input('financialyear');
            $userid = session('user')->userid ?? session('user')->deptuserid;

            $hasFindings = $findings && trim($findings) !== '' &&
                        trim($findings) !== '<p></p>' &&
                        trim($findings) !== '<p>&nbsp;</p>';
            $hasRecommendations = $recommendations && trim($recommendations) !== '' &&
                                trim($recommendations) !== '<p></p>' &&
                                trim($recommendations) !== '<p>&nbsp;</p>';

            if (! $hasFindings) {
                return response()->json([
                    'success' => false,
                    'message' => 'Findings are required',
                ], 422);
            }

            // Get audit title information
            $titleInfo = DB::table('audit.mst_praudit_title')
                ->where('praudittitleid', $praudittitleid)
                ->where('statusflag', 'Y')
                ->select('deptcode', 'catcode', 'subcatid')
                ->first();

            if (! $titleInfo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Audit title not found',
                ], 404);
            }

            DB::beginTransaction();
            try {
                $updatedTypes = [];

                // Save Findings (Type: F, Sectioncode: 09)
                if ($hasFindings) {
                    $existingFindings = DB::table('audit.praudit_report')
                        ->where('praudittitleid', $praudittitleid)
                        ->where('remarkstype', 'F')
                        ->first();

                    $findingsData = [
                        'deptcode' => $titleInfo->deptcode,
                        'catcode' => $titleInfo->catcode,
                        'subcatid' => $titleInfo->subcatid,
                        'praudittitleid' => $praudittitleid,
                        'remarks' => json_encode($findings),
                        'remarkstype' => 'F',
                        'sectioncode' => '09',
                        'financialyear' => $financialyear,
                        'statusflag' => 'Y',
                        'updatedby' => $userid,
                        'updatedon' => View::shared('get_nowtime'),
                    ];

                    if ($existingFindings) {
                        DB::table('audit.praudit_report')
                            ->where('prauditreportid', $existingFindings->prauditreportid)
                            ->update($findingsData);
                        $updatedTypes[] = 'findings_updated';
                    } else {
                        $findingsData['createdby'] = $userid;
                        $findingsData['createdon'] = View::shared('get_nowtime');
                        DB::table('audit.praudit_report')->insert($findingsData);
                        $updatedTypes[] = 'findings_created';
                    }
                }

                // Save Recommendations (Type: R, Sectioncode: 10)
                if ($hasRecommendations) {
                    $existingRecommendations = DB::table('audit.praudit_report')
                        ->where('praudittitleid', $praudittitleid)
                        ->where('remarkstype', 'R')
                        ->first();

                    $recommendationsData = [
                        'deptcode' => $titleInfo->deptcode,
                        'catcode' => $titleInfo->catcode,
                        'subcatid' => $titleInfo->subcatid,
                        'praudittitleid' => $praudittitleid,
                        'remarks' => json_encode($recommendations),
                        'remarkstype' => 'R',
                        'sectioncode' => '10',
                        'financialyear' => $financialyear,
                        'statusflag' => 'Y',
                        'updatedby' => $userid,
                        'updatedon' => View::shared('get_nowtime'),
                    ];

                    if ($existingRecommendations) {
                        DB::table('audit.praudit_report')
                            ->where('prauditreportid', $existingRecommendations->prauditreportid)
                            ->update($recommendationsData);
                        $updatedTypes[] = 'recommendations_updated';
                    } else {
                        $recommendationsData['createdby'] = $userid;
                        $recommendationsData['createdon'] = View::shared('get_nowtime');
                        DB::table('audit.praudit_report')->insert($recommendationsData);
                        $updatedTypes[] = 'recommendations_created';
                    }
                }

                // Save Attachments (Type: A, Sectioncode: 13) - Store as JSON array
                $existingAttachments = DB::table('audit.praudit_report')
                    ->where('praudittitleid', $praudittitleid)
                    ->where('remarkstype', 'A')
                    ->first();

                $attachmentsData = [
                    'deptcode' => $titleInfo->deptcode,
                    'catcode' => $titleInfo->catcode,
                    'subcatid' => $titleInfo->subcatid,
                    'praudittitleid' => $praudittitleid,
                    'remarks' => null,
                    'remarkstype' => 'A',
                    'sectioncode' => '13',
                    'financialyear' => $financialyear,
                    'fileuploadid' => json_encode($attachments),
                    'statusflag' => 'Y',
                    'updatedby' => $userid,
                    'updatedon' => View::shared('get_nowtime'),
                ];

                if ($existingAttachments) {
                    DB::table('audit.praudit_report')
                        ->where('prauditreportid', $existingAttachments->prauditreportid)
                        ->update($attachmentsData);
                    $updatedTypes[] = 'attachments_updated';
                } else {
                    $attachmentsData['createdby'] = $userid;
                    $attachmentsData['createdon'] = View::shared('get_nowtime');
                    DB::table('audit.praudit_report')->insert($attachmentsData);
                    $updatedTypes[] = 'attachments_created';
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Remarks saved successfully',
                    'data' => $updatedTypes,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function verifyInstitutionReport(Request $request)
    {
        try {
            $request->validate([
                'instid' => 'required|integer',
                'praudittitleid' => 'required|integer',
                'verified' => 'required|in:Y,N,F',
            ]);

            $instid = $request->input('instid');
            $praudittitleid = $request->input('praudittitleid');
            $verified = $request->input('verified');
            $userid = session('user')->userid ?? session('user')->deptuserid;

            DB::beginTransaction();
            try {
                $auditInfo = DB::table('audit.auditplan as plan')
                    ->join('audit.inst_auditschedule as sch', 'plan.auditplanid', '=', 'sch.auditplanid')
                    ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {
                        $join->on('prfmap.instid', '=', 'plan.instid')
                            ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                            ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                            ->where('prfmap.praudittitleid', '=', $praudittitleid);
                    })
                    ->where('plan.instid', $instid)
                    ->where('plan.statusflag', 'F')
                    ->where('plan.auditmode', 'P')
                    ->where('sch.statusflag', 'F')
                    ->whereNotNull('sch.entrymeetdate')
                    ->select('plan.auditplanid', 'sch.auditscheduleid')
                    ->first();

                if (! $auditInfo) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Audit schedule not found for this institution',
                    ], 404);
                }

                $updated = DB::table('audit.praudit_transpara')
                    ->where('auditplanid', $auditInfo->auditplanid)
                    ->where('auditscheduleid', $auditInfo->auditscheduleid)
                    ->where('statusflag', 'F')
                    ->update([
                        'prreportverifyflag' => $verified,
                        'updatedby' => $userid,
                        'updatedon' => View::shared('get_nowtime'),
                    ]);

                DB::commit();

                $message = '';
                if ($verified === 'F') {
                    $message = 'Institution verified successfully';
                } else {
                    $message = 'Report marked as pending';
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getVerifiedInstitutions(Request $request)
    {
        try {
            $request->validate([
                'deptcode' => 'required|string',
                'praudittitleid' => 'required|integer',
            ]);

            $institutions = DB::table('audit.mst_institution as inst')
                ->join('audit.auditplan as plan', 'inst.instid', '=', 'plan.instid')
                ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($request) {
                    $join->on('prfmap.instid', '=', 'plan.instid')
                        ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                        ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                        ->where('prfmap.praudittitleid', '=', $request->praudittitleid);
                })
                ->join('audit.inst_auditschedule as sch', 'plan.auditplanid', '=', 'sch.auditplanid')
                ->join('audit.praudit_transpara as praudit', function ($join) {
                    $join->on('sch.auditscheduleid', '=', 'praudit.auditscheduleid')
                        ->on('plan.auditplanid', '=', 'praudit.auditplanid')
                        ->where('praudit.statusflag', 'F');
                })
                ->where('inst.deptcode', $request->deptcode)
                ->where('inst.statusflag', 'Y')
                ->where('plan.statusflag', 'F')
                ->where('sch.statusflag', 'F')
                ->select('inst.instid', 'inst.instename', 'praudit.prreportverifyflag as verification_status')
                ->distinct()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $institutions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function checkVerificationStatus(Request $request)
    {
        try {
            $request->validate([
                'instid' => 'required|integer',
                'praudittitleid' => 'required|integer',
            ]);

            $instid = $request->input('instid');
            $praudittitleid = $request->input('praudittitleid');

            // Check if this specific institution is verified
            $status = DB::table('audit.auditplan as plan')
                ->join('audit.inst_auditschedule as sch', 'plan.auditplanid', '=', 'sch.auditplanid')
                ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {
                    $join->on('prfmap.instid', '=', 'plan.instid')
                        ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                        ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                        ->where('prfmap.praudittitleid', '=', $praudittitleid);
                })
                ->join('audit.praudit_transpara as praudit', function ($join) {
                    $join->on('sch.auditscheduleid', '=', 'praudit.auditscheduleid')
                        ->on('plan.auditplanid', '=', 'praudit.auditplanid')
                        ->where('praudit.statusflag', 'F');
                })
                ->where('plan.instid', $instid)
                ->where('plan.statusflag', 'F')
                ->where('plan.auditmode', 'P')
                ->where('sch.statusflag', 'F')
                ->whereNotNull('sch.entrymeetdate')
                ->select('praudit.prreportverifyflag')
                ->first();

            $isVerified = $status && in_array($status->prreportverifyflag, ['F']);

            return response()->json([
                'success' => true,
                'is_verified' => $isVerified,
                'status' => $status->prreportverifyflag ?? null,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function finalizeReport(Request $request)
    {
        try {

            $request->validate([
                'praudittitleid' => 'required|integer',
            ]);

            $praudittitleid = $request->input('praudittitleid');
            $userid = session('user')->userid ?? session('user')->deptuserid;

            DB::beginTransaction();

            try {

                $verificationStatus = DB::table('audit.auditplan as plan')

                    ->join('audit.inst_auditschedule as sch', function ($join) {
                        $join->on('plan.auditplanid', '=', 'sch.auditplanid')
                            ->where('sch.statusflag', 'F');
                    })

                    ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {
                        $join->on('prfmap.instid', '=', 'plan.instid')
                            ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                            ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                            ->where('prfmap.praudittitleid', '=', $praudittitleid);
                    })

                    ->where('plan.statusflag', 'F')
                    ->where('plan.auditmode', 'P')
                    ->whereNotNull('sch.entrymeetdate')

                    ->select(
                        'plan.instid',
                        'sch.auditscheduleid',

                        DB::raw("
                        CASE
                            WHEN sch.auditscheduleid IN (
                                SELECT p.auditscheduleid
                                FROM audit.praudit_transpara p
                                WHERE p.statusflag = 'F'
                                GROUP BY p.auditscheduleid
                                HAVING COUNT(*) = COUNT(
                                    CASE
                                        WHEN p.prremarksverifyflag = 'Y'
                                        AND (
                                            p.fileuploadid IS NULL
                                            OR p.prfileverifyflag = 'Y'
                                        )
                                        THEN 1
                                    END
                                )
                            )
                            THEN 'Verified'
                            ELSE 'Unverified'
                        END as verification_status
                    ")
                    )

                    ->groupBy('plan.instid', 'sch.auditscheduleid')
                    ->get();

                $totalInstitutions = $verificationStatus
                    ->pluck('instid')
                    ->unique()
                    ->count();

                $verifiedInstitutions = $verificationStatus
                    ->filter(function ($item) {
                        return $item->verification_status === 'Verified';
                    })
                    ->pluck('instid')
                    ->unique();

                $verifiedCount = $verifiedInstitutions->count();

                $hasUnverified = $verifiedCount !== $totalInstitutions;

                if ($hasUnverified) {

                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => "Cannot finalize. {$verifiedCount} out of {$totalInstitutions} institutions are verified. All institutions must be verified before finalizing.",
                    ], 400);
                }

                $existingReports = DB::table('audit.praudit_report')
                    ->where('praudittitleid', $praudittitleid)
                    ->where('statusflag', 'Y')
                    ->get();

                if ($existingReports->count() > 0) {

                    foreach ($existingReports as $report) {

                        $updateData = [
                            'updatedby' => $userid,
                            'updatedon' => View::shared('get_nowtime'),
                        ];

                        if ($report->remarkstype == 'A') {

                            $files = json_decode($report->fileuploadid, true);

                            $hasFile = is_array($files) && count($files) > 0;

                            $updateData['statusflag'] = $hasFile ? 'F' : 'P';

                        } else {

                            $updateData['statusflag'] = 'P';
                        }

                        DB::table('audit.praudit_report')
                            ->where('prauditreportid', $report->prauditreportid)
                            ->update($updateData);
                    }
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Report finalized successfully',
                    'data' => [
                        'total_institutions' => $totalInstitutions,
                        'verified_count' => $verifiedCount,
                    ],
                ]);

            } catch (\Exception $e) {

                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getTitleFinalizationStatus(Request $request)
    {
        try {
            $request->validate([
                'praudittitleid' => 'required|integer',
            ]);

            $praudittitleid = $request->input('praudittitleid');

            $unverifiedInstitutions = DB::table('audit.mst_institution as mi')
                ->join('audit.auditplan as plan', 'mi.instid', '=', 'plan.instid')
                ->join('audit.inst_auditschedule as sch', 'plan.auditplanid', '=', 'sch.auditplanid')
                ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {
                    $join->on('prfmap.instid', '=', 'plan.instid')
                        ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                        ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                        ->where('prfmap.praudittitleid', '=', $praudittitleid);
                })
                ->join('audit.praudit_transpara as praudit', function ($join) {
                    $join->on('sch.auditscheduleid', '=', 'praudit.auditscheduleid')
                        ->on('plan.auditplanid', '=', 'praudit.auditplanid')
                        ->where('praudit.statusflag', 'F');
                })
                ->where('plan.statusflag', 'F')
                ->where('plan.auditmode', 'P')
                ->where('sch.statusflag', 'F')
                ->where('mi.statusflag', 'Y')
                ->where('prfmap.statusflag', 'F')
                ->whereNotNull('sch.entrymeetdate')
                ->select(
                    'mi.instid',
                    'mi.instename',
                    DB::raw("
                        CASE
                            WHEN sch.auditscheduleid IN (
                                SELECT p.auditscheduleid
                                FROM audit.praudit_transpara p
                                WHERE p.statusflag = 'F'
                                GROUP BY p.auditscheduleid
                                HAVING COUNT(*) = COUNT(
                                    CASE
                                        WHEN p.prremarksverifyflag = 'Y'
                                        AND (
                                            p.fileuploadid IS NULL
                                            OR p.prfileverifyflag = 'Y'
                                        )
                                        THEN 1
                                    END
                                )
                            )
                            THEN 'Verified'
                            ELSE 'Unverified'
                        END as verification_status
                    ")
                )
                ->distinct()
                ->get();

            $totalInstitutions = $unverifiedInstitutions->count();
            $unverifiedCount = $unverifiedInstitutions->where('verification_status', 'Unverified')->count();

            // Get only unverified institutions for display
            $unverifiedList = $unverifiedInstitutions
                ->where('verification_status', 'Unverified')
                ->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_institutions' => $totalInstitutions,
                    'unverified_institutions' => $unverifiedCount,
                    'unverified_list' => $unverifiedList,
                    'all_institutions' => $unverifiedInstitutions,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function view_prauditconsolidation()
    {
        $userData = session('user');
        $chargeData = session('charge');

        $sessiondeptcode = $chargeData->deptcode ?? null;

        if (! $sessiondeptcode) {
            abort(403, 'Department not found in session');
        }

        $session_userid = $userData->userid ?? null;
        $departments = ReportModel::commondeptfetch();
        $Category = ReportModel::CategoryFetchData();

        return view('report.listperformanceauditreport', compact('departments', 'Category'));
    }

    public function getconsolidationprauidtreport(Request $request)
    {
        $usercharge = session('charge');

        $deptcode = $request->deptcode ?: optional($usercharge)->deptcode;
        $praudittitleid = $request->praudittitleid;

        // dd($deptcode, $praudittitleid);
        $resultsNew = DB::table('audit.praudit_report as pr')
            ->join('audit.mst_praudit_title as t', 't.praudittitleid', '=', 'pr.praudittitleid')
            ->join('audit.mst_dept as md', 'md.deptcode', '=', 'pr.deptcode')
            ->where('pr.deptcode', $deptcode)
            ->where('pr.praudittitleid', $praudittitleid)
            ->whereIn('pr.remarkstype', ['F', 'R'])
            ->whereIn('pr.statusflag', ['P', 'A'])
            ->where('t.statusflag', 'Y')
            ->select(
                'md.deptelname',
                'md.depttlname',
                'pr.praudittitleid',
                'pr.catcode',
                'pr.subcatid',
                'pr.statusflag',
                't.titleename',
                't.titletname',
                'pr.deptcode',
                'pr.financialyear'
            )
            ->first();

        if ($resultsNew) {
            $resultsNew->enc_praudittitleid = Crypt::encryptString($resultsNew->praudittitleid);
            $resultsNew->enc_catcode = Crypt::encryptString($resultsNew->catcode);
            $resultsNew->enc_subcatid = Crypt::encryptString($resultsNew->subcatid);
            $resultsNew->enc_deptcode = Crypt::encryptString($resultsNew->deptcode);
            $resultsNew->enc_finyear = Crypt::encryptString($resultsNew->financialyear);
        }

        return response()->json(['data' => $resultsNew]);
    }

    public function showFindingsRecommendations($praudittitleid, $deptcode, $catcode, $subcatid, $finyear)
    {
        try {
            $praudittitleid = Crypt::decryptString($praudittitleid);
            $deptcode = Crypt::decryptString($deptcode);
            $catcode = Crypt::decryptString($catcode);
            $subcatid = Crypt::decryptString($subcatid);
            $financialyear = Crypt::decryptString($finyear);

            return view('Performance.performanceauditreport', compact('praudittitleid', 'deptcode', 'catcode', 'subcatid', 'financialyear'));
        } catch (\Exception $e) {
            abort(403, 'Invalid or tampered URL');
        }
    }

    public function uploadSectionFile(Request $request)
    {
        try {
            $validated = $request->validate([
                'praudittitleid' => 'required|integer',
                'deptcode' => 'required|string',
                'catcode' => 'required|string',
                'subcatid' => 'nullable',
                'sectioncode' => 'required|string',
                'file' => 'required|file|mimes:pdf|max:2048',
                'financialyear' => 'nullable|string',
            ]);

            // Check if record exists
            $existing = DB::table('audit.praudit_report')
                ->where('praudittitleid', $request->praudittitleid)
                ->where('deptcode', $request->deptcode)
                ->where('catcode', $request->catcode)
                ->where('sectioncode', $request->sectioncode)
                ->where('subcatid', $request->subcatid)
                ->first();

            // Prevent updates if already finalized
            if ($existing && ($existing->statusflag == 'F' || $existing->statusflag == 'A')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This section is already finalized and cannot be modified.',
                ], 403);
            }

            // Handle file upload
            $file = $request->file('file');
            $destinationPath = 'uploads/pr_report/';

            // Get existing fileupload IDs as array
            $existingFileuploadIds = [];
            $existingFileuploadId = null; // For passing to uploadFile method

            if ($existing && $existing->fileuploadid) {
                // Decode JSONB array to PHP array
                if (is_string($existing->fileuploadid)) {
                    $existingFileuploadIds = json_decode($existing->fileuploadid, true) ?? [];
                } elseif (is_array($existing->fileuploadid)) {
                    $existingFileuploadIds = $existing->fileuploadid;
                }

                // Get the last fileuploadid for the uploadFile method (if needed)
                if (! empty($existingFileuploadIds)) {
                    $existingFileuploadId = end($existingFileuploadIds);
                }
            }

            $destinationarray = [
                $request->deptcode,
                $request->praudittitleid,
                View::shared('pr_annexures'),
            ];

            // Upload new file
            $uploadResult = $this->fileUploadService->uploadFile(
                $file,
                $destinationPath,
                $existingFileuploadId, // Pass the last fileuploadid or null
                $destinationarray
            );

            $newFileuploadId = $uploadResult->getData()->fileupload_id ?? null;

            if (! $newFileuploadId) {
                return response()->json([
                    'success' => false,
                    'message' => 'File upload failed',
                ], 500);
            }

            // Add new fileuploadid to the array
            $existingFileuploadIds[] = (string) $newFileuploadId;

            // Remove duplicates and reindex
            $existingFileuploadIds = array_values(array_unique($existingFileuploadIds));

            $fileMetadata = [
                'fileuploadid' => $newFileuploadId,
                'filename' => $file->getClientOriginalName(),
                'filesize' => $file->getSize(),
                'filetype' => $file->getMimeType(),
                'filepath' => $destinationPath.$newFileuploadId.'.pdf',
                'uploaded_at' => View::shared('get_nowtime')->toDateTimeString(),
                'uploaded_by' => session('user')->userid,
            ];

            $data = [
                'praudittitleid' => $request->praudittitleid,
                'deptcode' => $request->deptcode,
                'catcode' => $request->catcode,
                'subcatid' => $request->subcatid,
                'sectioncode' => $request->sectioncode,
                'fileuploadid' => json_encode($existingFileuploadIds), // Store as JSON string
                'remarks' => json_encode($fileMetadata),
                'financialyear' => $request->financialyear,
                'updatedby' => session('user')->userid,
                'updatedon' => View::shared('get_nowtime'),
                'statusflag' => 'Y',
            ];

            if ($existing) {
                DB::table('audit.praudit_report')
                    ->where('prauditreportid', $existing->prauditreportid)
                    ->update($data);
                $message = 'File uploaded successfully';
            } else {
                $data['createdby'] = session('user')->userid;
                $data['createdon'] = View::shared('get_nowtime');

                // For new records, initialize with just the new fileuploadid
                $data['fileuploadid'] = json_encode([(string) $newFileuploadId]);

                DB::table('audit.praudit_report')->insert($data);
                $message = 'File uploaded successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'file' => $fileMetadata,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading file: '.$e->getMessage(),
            ], 500);
        }
    }

    public function clearSectionFile(Request $request)
    {
        try {
            $validated = $request->validate([
                'praudittitleid' => 'required|integer',
                'deptcode' => 'required|string',
                'catcode' => 'required|string',
                'subcatid' => 'nullable',
                'sectioncode' => 'required|string',
            ]);

            $existing = DB::table('audit.praudit_report')
                ->where('praudittitleid', $request->praudittitleid)
                ->where('deptcode', $request->deptcode)
                ->where('catcode', $request->catcode)
                ->where('sectioncode', $request->sectioncode)
                ->where('subcatid', $request->subcatid)
                ->first();

            if (! $existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Record not found',
                ], 404);
            }

            // Prevent removal if finalized
            if ($existing->statusflag == 'F' || $existing->statusflag == 'A') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove file from finalized section',
                ], 403);
            }

            // Get existing fileupload IDs
            $existingFileuploadIds = [];
            if ($existing->fileuploadid) {
                if (is_string($existing->fileuploadid)) {
                    $existingFileuploadIds = json_decode($existing->fileuploadid, true) ?? [];
                } elseif (is_array($existing->fileuploadid)) {
                    $existingFileuploadIds = $existing->fileuploadid;
                }
            }

            // Option 1: Clear all file IDs (empty array)
            $newFileuploadIds = [];

            // Option 2: Remove only the last file ID (uncomment if needed)
            // if (!empty($existingFileuploadIds)) {
            //     array_pop($existingFileuploadIds); // Remove last entry
            //     $newFileuploadIds = array_values($existingFileuploadIds); // Reindex
            // }

            // Update the record
            DB::table('audit.praudit_report')
                ->where('prauditreportid', $existing->prauditreportid)
                ->update([
                    'fileuploadid' => ! empty($newFileuploadIds) ? json_encode($newFileuploadIds) : null,
                    'remarks' => null,
                    'statusflag' => null,
                    'updatedby' => session('user')->userid,
                    'updatedon' => View::shared('get_nowtime'),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'File removed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing file: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getAllSections(Request $request)
    {
        try {
            $sections = DB::table('audit.mst_praudit_report_sections')
                ->where('statusflag', 'Y')
                ->orderBy('orderno')
                ->get();

            $contents = [];
            $statusFlags = [];

            if ($request->praudittitleid) {
                $savedData = DB::table('audit.praudit_report as pr')
                    ->where('pr.praudittitleid', $request->praudittitleid)
                    ->where('pr.deptcode', $request->deptcode)
                    ->where('pr.catcode', $request->catcode)
                    ->where('pr.subcatid', $request->subcatid)
                    ->select('pr.*')
                    ->get();

                foreach ($savedData as $data) {
                    if ($data->sectioncode) {
                        // For file upload sections (remarkstype 'A' or type 'F' sections)
                        if ($data->fileuploadid) {
                            // Decode JSON array of file IDs
                            $fileIds = json_decode($data->fileuploadid, true);

                            if (! empty($fileIds) && is_array($fileIds)) {
                                // Fetch file details for all file IDs
                                $fileDetails = DB::table('audit.fileuploaddetail')
                                    ->whereIn('fileuploadid', $fileIds)
                                    ->where('statusflag', 'Y')
                                    ->select(
                                        'fileuploadid',
                                        'filename',
                                        'mimetype',
                                        'filepath',
                                        'filesize',
                                        'uploadedby',
                                        'uploadedon',
                                        'usertypecode'
                                    )
                                    ->get();

                                $filesData = [];
                                foreach ($fileDetails as $file) {
                                    $filesData[] = [
                                        'fileuploadid' => $file->fileuploadid,
                                        'filename' => $file->filename,
                                        'mimetype' => $file->mimetype,
                                        'filepath' => $file->filepath,
                                        'filesize' => $file->filesize,
                                        'uploadedby' => $file->uploadedby,
                                        'uploadedon' => $file->uploadedon,
                                        'usertypecode' => $file->usertypecode,
                                    ];
                                }

                                // For sections with multiple files
                                if ($data->remarkstype === 'A' || count($filesData) > 1) {
                                    $contents[$data->sectioncode] = $filesData;
                                } else {
                                    // For backward compatibility - single file
                                    $contents[$data->sectioncode] = $filesData[0] ?? null;
                                }
                            } else {
                                $contents[$data->sectioncode] = null;
                            }
                        } else {
                            // For editor sections (remarks type 'F', 'R', etc.), decode remarks
                            if ($data->remarks) {
                                $decodedRemarks = json_decode($data->remarks, true);
                                // If it's a JSON object with content/html property
                                if (is_array($decodedRemarks) && isset($decodedRemarks['content'])) {
                                    $contents[$data->sectioncode] = $decodedRemarks['content'];
                                } elseif (is_array($decodedRemarks) && isset($decodedRemarks['html'])) {
                                    $contents[$data->sectioncode] = $decodedRemarks['html'];
                                } elseif (is_string($decodedRemarks)) {
                                    $contents[$data->sectioncode] = $decodedRemarks;
                                } else {
                                    $contents[$data->sectioncode] = $data->remarks;
                                }
                            } else {
                                $contents[$data->sectioncode] = '';
                            }
                        }

                        $statusFlags[$data->sectioncode] = [
                            'status' => $data->statusflag,
                            'has_data' => true,
                            'remarkstype' => $data->remarkstype ?? null,
                        ];
                    }
                }
            }

            $department = DB::table('audit.mst_praudit_title as prt')
                ->join('audit.mst_dept as md', 'prt.deptcode', '=', 'md.deptcode')
                ->where('prt.praudittitleid', $request->praudittitleid)
                ->first();

            return response()->json([
                'success' => true,
                'sections' => $sections,
                'contents' => $contents,
                'status' => $statusFlags,
                'department' => $department,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function saveSectionContent(Request $request)
    {
        try {
            $validated = $request->validate([
                'praudittitleid' => 'required|integer',
                'deptcode' => 'required|string',
                'catcode' => 'required|string',
                'subcatid' => 'nullable',
                'sectioncode' => 'required|string',
                'content' => 'nullable|string',
                'statusflag' => 'required|in:Y,F,A',
                'financialyear' => 'nullable|string',
            ]);

            $existing = DB::table('audit.praudit_report')
                ->where('praudittitleid', $request->praudittitleid)
                ->where('deptcode', $request->deptcode)
                ->where('catcode', $request->catcode)
                ->where('sectioncode', $request->sectioncode)
                ->where('subcatid', $request->subcatid)
                ->first();

            if ($existing && ($existing->statusflag == 'A')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This section is already finalized and cannot be modified.',
                ], 403);
            }

            $data = [
                'praudittitleid' => $request->praudittitleid,
                'deptcode' => $request->deptcode,
                'catcode' => $request->catcode,
                'subcatid' => $request->subcatid,
                'sectioncode' => $request->sectioncode,
                'financialyear' => $request->financialyear,
                'updatedby' => session('user')->userid,
                'updatedon' => View::shared('get_nowtime'),
                'statusflag' => $request->statusflag,
            ];

            if ($request->has('content')) {
                $data['remarks'] = json_encode($request->content);
            }

            if ($existing) {
                DB::table('audit.praudit_report')
                    ->where('prauditreportid', $existing->prauditreportid)
                    ->update($data);
                $message = 'Section updated successfully';
                $action = 'updated';
            } else {
                $data['createdby'] = session('user')->userid;
                $data['createdon'] = View::shared('get_nowtime');
                DB::table('audit.praudit_report')->insert($data);
                $message = 'Section saved successfully';
                $action = 'saved';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $request->statusflag,
                'action' => $action,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving section: '.$e->getMessage(),
            ], 500);
        }
    }

    public function getCompleteAuditReport(Request $request)
    {
        try {
            $sections = DB::table('audit.mst_praudit_report_sections')
                ->where('statusflag', 'Y')
                ->orderBy('orderno')
                ->get();

            $contents = DB::table('audit.praudit_report')
                ->where('praudittitleid', $request->praudittitleid)
                ->where('deptcode', $request->deptcode)
                ->where('catcode', $request->catcode)
                ->where('statusflag', 'F')
                ->get()
                ->keyBy('sectioncode');

            $reportData = $sections->map(function ($section) use ($contents) {
                $content = $contents->get($section->sectioncode);

                return [
                    'sectioncode' => $section->sectioncode,
                    'title_en' => $section->titleename,
                    'title_ta' => $section->titletname,
                    'orderno' => $section->orderno,
                    'content' => $content ? json_decode($content->remarks) : '',
                    'statusflag' => $content ? $content->statusflag : 'N',
                    'last_updated' => $content ? $content->updatedon : null,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $reportData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function finalizeCompleteReport(Request $request)
    {
        try {
            // Update all sections to status 'A'
            $updated = DB::table('audit.praudit_report')
                ->where('praudittitleid', $request->praudittitleid)
                ->where('deptcode', $request->deptcode)
                ->where('catcode', $request->catcode)
                ->where('subcatid', $request->subcatid)
                ->whereIn('sectioncode', $request->section_codes)
                ->update([
                    'statusflag' => 'A',
                    'updatedby' => session('user')->userid,
                    'updatedon' => View::shared('get_nowtime'),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Report finalized successfully',
                'updated_count' => $updated,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getFindingsRecommendations(Request $request)
    {
        try {
            $praudittitleid = $request->praudittitleid;
            $deptcode = $request->deptcode;
            $catcode = $request->catcode;
            $subcatid = $request->subcatid ?? null;

            $data = DB::table('audit.praudit_report')
                ->select('*')
                ->where('praudittitleid', $praudittitleid)
                ->where('deptcode', $deptcode)
                ->where('catcode', $catcode)
                ->where('subcatid', $subcatid)
                ->whereIn('remarkstype', ['F', 'R'])
                ->where('statusflag', 'F')
                ->orderBy('remarkstype')
                ->orderBy('createdon', 'desc')
                ->get();

            if ($data->isNotEmpty()) {
                $deptInfo = DB::table('audit.mst_dept')
                    ->where('deptcode', $deptcode)
                    ->where('statusflag', 'Y')
                    ->first();

                $deptName = $deptInfo->deptelname ?? '';
                $deptTName = $deptInfo->depttlname ?? '';

                $titleinfo = DB::table('audit.mst_praudit_title')
                    ->where('praudittitleid', $praudittitleid)
                    ->where('statusflag', 'Y')
                    ->first();

                $titleename = $titleinfo->titleename ?? '';
                $titletname = $titleinfo->titletname ?? '';

                foreach ($data as $item) {
                    $item->deptname = $deptName;
                    $item->depttname = $deptTName;
                    $item->titleename = $titleename;
                    $item->titletname = $titletname;
                    $item->remarks = json_decode($item->remarks);
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getFindingsRecommendations: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error loading report data',
            ], 500);
        }
    }

    /**
     * Helper method to clean and decode HTML remarks from JSON
     */
    private function cleanRemarks($remarks)
    {
        if (empty($remarks)) {
            return '';
        }

        // Decode JSON if it's JSON encoded
        $decodedRemarks = json_decode($remarks);

        if (json_last_error() === JSON_ERROR_NONE && $decodedRemarks) {
            // It's JSON, extract the content
            $remarks = isset($decodedRemarks->content) ? $decodedRemarks->content : '';
            if (empty($remarks) && isset($decodedRemarks->html)) {
                $remarks = $decodedRemarks->html;
            }
            if (empty($remarks) && is_string($decodedRemarks)) {
                $remarks = $decodedRemarks;
            }
        }

        // Decode HTML entities
        $remarks = html_entity_decode($remarks, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Convert double quotes to single quotes for better PDF compatibility
        $remarks = str_replace('"', "'", $remarks);

        $remarks = $this->sanitizeMpdfInlineStyles($remarks);

        // Convert encoding to UTF-8
        $remarks = mb_convert_encoding($remarks, 'UTF-8', 'auto');

        return $remarks;
    }

    private function sanitizeMpdfInlineStyles(string $html): string
    {
        return preg_replace_callback('/\sstyle=(["\'])(.*?)\1/is', function ($matches) {
            $safeStyles = [];

            foreach (explode(';', $matches[2]) as $style) {
                $style = trim($style);

                if ($style === '' || strpos($style, ':') === false) {
                    continue;
                }

                [$property, $value] = array_map('trim', explode(':', $style, 2));
                $property = strtolower($property);
                $value = trim($value);

                if ($value === '' || str_starts_with($property, 'mso-') || str_starts_with($property, '--')) {
                    continue;
                }

                if ($property === 'font-family') {
                    continue;
                }

                if (preg_match('/^border($|-)/i', $property)) {
                    $value = preg_replace('/\b(ridge|groove|inset|outset)\b/i', 'solid', $value);
                }

                $value = preg_replace('/\bwindowtext\b/i', '#000000', $value);
                $value = preg_replace('/\b(currentcolor|initial|unset|revert)\b/i', 'inherit', $value);

                if ($this->mpdfStyleHasUnsupportedColor($property, $value)) {
                    continue;
                }

                $safeStyles[] = $property.': '.$value;
            }

            return $safeStyles ? ' style="'.implode('; ', $safeStyles).'"' : '';
        }, $html);
    }

    private function mpdfStyleHasUnsupportedColor(string $property, string $value): bool
    {
        if (! preg_match('/(^|-)color$|^background$|^border($|-)/i', $property)) {
            return false;
        }

        if (stripos($value, 'var(') !== false || stripos($value, 'calc(') !== false || strpos($value, '/') !== false) {
            return true;
        }

        if (preg_match_all('/#[0-9a-f]{1,8}\b/i', $value, $matches)) {
            foreach ($matches[0] as $hex) {
                $length = strlen($hex) - 1;
                if (! in_array($length, [3, 6], true)) {
                    return true;
                }
            }
        }

        if (preg_match('/\b(rgba?|hsla?)\s*\(([^)]*)\)/i', $value, $matches)) {
            return strpos($matches[2], ',') === false;
        }

        if (preg_match('/\b(inherit|transparent)\b/i', $value)) {
            return true;
        }

        return false;
    }

    /**
     * Generate Performance Audit Report HTML for preview
     */
    public function generatePerformanceReport(Request $request)
    {
        try {
            $language = $request->lang === 'ta' ? 'ta' : 'en';
            $praudittitleid = $request->praudittitleid;
            $deptcode = $request->deptcode;
            $catcode = $request->catcode;
            $subcatid = $request->subcatid;
            $financialyear = $request->financialyear;

            if (! $praudittitleid || ! $deptcode) {
                return response()->json(['error' => 'Required fields are missing'], 400);
            }

            // Get department and title info
            $deptInfo = DB::table('audit.mst_dept')
                ->where('deptcode', $deptcode)
                ->where('statusflag', 'Y')
                ->first();

            $titleInfo = DB::table('audit.mst_praudit_title')
                ->where('praudittitleid', $praudittitleid)
                ->where('statusflag', 'Y')
                ->first();

            if (! $deptInfo || ! $titleInfo) {
                return response()->json(['error' => 'Department or Title not found'], 404);
            }

            // Get all sections with their content
            $sections = DB::table('audit.mst_praudit_report_sections')
                ->where('statusflag', 'Y')
                ->orderBy('orderno')
                ->get();

            $sectionContents = DB::table('audit.praudit_report')
                ->where('praudittitleid', $praudittitleid)
                ->where('deptcode', $deptcode)
                ->where('catcode', $catcode)
                ->where('financialyear', $financialyear)
                ->whereIn('statusflag', ['F', 'A'])
                ->get()
                ->keyBy('sectioncode');

            // Get financial year name
            $financialYearInfo = DB::table('audit.mst_financialyear')
                ->where('financialyearcode', $financialyear)
                ->where('statusflag', 'Y')
                ->first();

            // Get file attachments for type 'F' sections - Handle JSONB array properly
            $fileAttachments = DB::table('audit.praudit_report as pr')
                ->join('audit.mst_praudit_report_sections as mrs', function ($join) {
                    $join->on('mrs.sectioncode', '=', 'pr.sectioncode')
                        ->where('mrs.statusflag', 'Y')
                        ->where('mrs.type', 'F');
                })
                ->where('pr.praudittitleid', $praudittitleid)
                ->where('pr.deptcode', $deptcode)
                ->where('pr.catcode', $catcode)
                ->where('pr.financialyear', $financialyear)
                ->whereIn('pr.statusflag', ['F', 'A'])
                ->whereNotNull('pr.fileuploadid')
                ->select('pr.*', 'mrs.titleename', 'mrs.titletname', 'mrs.sectioncode as file_sectioncode')
                ->get();

            // Process each section's file attachments - decode JSONB array and fetch file details
            $processedAttachments = [];
            foreach ($fileAttachments as $attachment) {
                $sectionCode = $attachment->file_sectioncode;

                if (! isset($processedAttachments[$sectionCode])) {
                    $processedAttachments[$sectionCode] = [
                        'section_title_en' => $attachment->titleename,
                        'section_title_ta' => $attachment->titletname,
                        'files' => [],
                    ];
                }

                // Decode the JSONB array of file IDs
                $fileIds = json_decode($attachment->fileuploadid, true);

                if (! empty($fileIds) && is_array($fileIds)) {
                    // Fetch file details for all file IDs in this section
                    $fileDetails = DB::table('audit.fileuploaddetail')
                        ->whereIn('fileuploadid', $fileIds)
                        ->where('statusflag', 'Y')
                        ->select('fileuploadid', 'filename', 'filepath', 'filesize', 'mimetype')
                        ->get();

                    foreach ($fileDetails as $fileDetail) {
                        $processedAttachments[$sectionCode]['files'][] = (object) [
                            'sectioncode' => $sectionCode,
                            'filename' => $fileDetail->filename,
                            'filepath' => $fileDetail->filepath,
                            'filesize' => $fileDetail->filesize,
                            'mimetype' => $fileDetail->mimetype,
                            'fileuploadid' => $fileDetail->fileuploadid,
                        ];
                    }
                }
            }

            $dynamicData = [
                'DeptName' => $deptInfo->deptelname ?? '',
                'DistName' => $deptInfo->distelname ?? '',
                'TitleName' => $language === 'ta' ? ($titleInfo->titletname ?? '') : ($titleInfo->titleename ?? ''),
                'FinancialYear' => $financialYearInfo->financialyear ?? ($language === 'ta' ? 'தெரியாத கணக்கு ஆண்டு' : 'Unknown Audit Period'),
                'base64Image' => 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('site/image/tn__logo.png'))),
                'deptsize' => 24,
            ];

            // Generate report HTML
            $reportHtml = '';

            // First Page
            $reportHtml .= $this->renderFirstPage($dynamicData, $language);

            // Sections with content (both text and file sections)
            $sectionNumber = 1;
            foreach ($sections as $section) {
                $content = $sectionContents->get($section->sectioncode);

                // Get section title based on language
                $sectionTitle = $language === 'ta' ? $section->titletname : $section->titleename;

                // Add page break before each section (except first)
                $reportHtml .= '<div style="page-break-before: always;"></div>';

                // Section container with border
                $reportHtml .= '<div style="border: 2px solid #000; padding: 20px; margin: 20px 0; min-height: 500px;">';
                $reportHtml .= '<h2 style="text-align:center; margin-top:0; font-size:28px; font-family:'.($language === 'ta' ? 'latha' : 'times').';">'.$sectionNumber.'. '.htmlspecialchars($sectionTitle).'</h2>';
                $reportHtml .= '<hr style="border: 1px solid #000; margin: 20px 0;">';

                // Check if this is a file section (type 'F')
                if ($section->type === 'F') {
                    // Handle file/annexure section
                    $sectionData = isset($processedAttachments[$section->sectioncode]) ? $processedAttachments[$section->sectioncode] : null;
                    $sectionFiles = $sectionData ? $sectionData['files'] : [];

                    if (count($sectionFiles) > 0) {
                        foreach ($sectionFiles as $attachment) {
                            if ($attachment->filepath) {
                                $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $attachment->filepath);
                                $fullPath = storage_path('app/public/'.ltrim($filePath, '/'));

                                // Fix the URL path: Convert backslashes to forward slashes
                                $urlPath = str_replace('\\', '/', $filePath);
                                $assetPath = asset('storage/'.$urlPath);

                                $reportHtml .= '<div style="margin:20px 0; border:1px solid #ccc; padding:15px; border-radius:5px;">';
                                $reportHtml .= '<h4>'.htmlspecialchars($attachment->filename).'</h4>';
                                $reportHtml .= '<p><strong>Size:</strong> '.$this->formatFileSize($attachment->filesize).'</p>';

                                if (file_exists($fullPath)) {
                                    $mimeType = mime_content_type($fullPath);

                                    if ($mimeType === 'application/pdf') {
                                        $reportHtml .= '<embed src="'.$assetPath.'" type="application/pdf" style="width:100%; height:600px; border:1px solid #ccc;">';
                                    } elseif (strpos($mimeType, 'image/') === 0) {
                                        $reportHtml .= '<img src="'.$assetPath.'" style="max-width:100%; height:auto; border:1px solid #ccc;">';
                                    } else {
                                        $reportHtml .= '<p><a href="'.$assetPath.'" target="_blank">Download File</a></p>';
                                    }
                                } else {
                                    $reportHtml .= '<p style="color:red;">File not found: '.htmlspecialchars($attachment->filename).'</p>';
                                }

                                $reportHtml .= '</div>';
                            }
                        }
                    } else {
                        $reportHtml .= '<p style="text-align:center; color:#999;">'.($language === 'ta' ? 'இணைப்பு எதுவும் இல்லை' : 'No attachments available').'</p>';
                    }
                } else {
                    // Handle text section - Clean and decode HTML remarks from JSON
                    if ($content && $content->remarks) {
                        $remarks = $this->cleanRemarks($content->remarks);
                        $reportHtml .= '<div style="margin-top:30px; font-family:'.($language === 'ta' ? 'latha' : 'times').'; line-height:1.6;">'.$remarks.'</div>';
                    } else {
                        $reportHtml .= '<p style="text-align:center; color:#999;">'.($language === 'ta' ? 'உள்ளடக்கம் எதுவும் இல்லை' : 'No content available').'</p>';
                    }
                }

                $reportHtml .= '</div>';
                $sectionNumber++;
            }

            // End of Report - Centered properly
            $reportHtml .= '<div style="page-break-before: always;"></div>';
            $endTitle = $language === 'ta' ? 'அறிக்கையின் முடிவு' : 'End of the Report';
            $reportHtml .= '<div style="border: 2px solid #000; padding: 20px; margin: 20px 0; min-height: 500px; display: flex; align-items: center; justify-content: center; text-align: center;">';
            $reportHtml .= '<h2 style="font-size:30px; font-family:'.($language === 'ta' ? 'latha' : 'times').';">** '.$endTitle.' **</h2>';
            $reportHtml .= '</div>';

            // Encrypt data for download
            $encryptedData = [
                'praudittitleid' => Crypt::encryptString($praudittitleid),
                'deptcode' => Crypt::encryptString($deptcode),
                'catcode' => Crypt::encryptString($catcode),
                'financialyear' => Crypt::encryptString($financialyear ?? ''),
            ];

            return response()->json([
                'success' => true,
                'html' => $reportHtml,
                'encrypted' => $encryptedData,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download Performance Audit Report as PDF
     */
    public function downloadPerformanceReport(Request $request)
    {
        try {

            ini_set('pcre.backtrack_limit', 500000000); // Increased to 5 million
            ini_set('pcre.recursion_limit', 500000000); // Also increase recursion limit

            $language = $request->input('lang', 'en');

            try {
                $praudittitleid = Crypt::decryptString($request->praudittitleid);
                $deptcode = Crypt::decryptString($request->deptcode);
                $catcode = Crypt::decryptString($request->catcode);
                $financialyear = Crypt::decryptString($request->financialyear);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Invalid encrypted data'], 400);
            }

            // Get department and title info
            $deptInfo = DB::table('audit.mst_dept')
                ->where('deptcode', $deptcode)
                ->where('statusflag', 'Y')
                ->first();

            $titleInfo = DB::table('audit.mst_praudit_title')
                ->where('praudittitleid', $praudittitleid)
                ->where('statusflag', 'Y')
                ->first();

            if (! $deptInfo || ! $titleInfo) {
                return response()->json(['error' => 'Department or Title not found'], 404);
            }

            // Get all sections with their content
            $sections = DB::table('audit.mst_praudit_report_sections')
                ->where('statusflag', 'Y')
                ->orderBy('orderno')
                ->get();

            $sectionContents = DB::table('audit.praudit_report')
                ->where('praudittitleid', $praudittitleid)
                ->where('deptcode', $deptcode)
                ->where('catcode', $catcode)
                ->where('financialyear', $financialyear)
                ->whereIn('statusflag', ['F', 'A'])
                ->get()
                ->keyBy('sectioncode');

            // Get financial year name
            $financialYearInfo = DB::table('audit.mst_financialyear')
                ->where('financialyearcode', $financialyear)
                ->where('statusflag', 'Y')
                ->first();

            // Get file attachments for type 'F' sections - Handle JSONB array properly
            $fileAttachments = DB::table('audit.praudit_report as pr')
                ->join('audit.mst_praudit_report_sections as mrs', function ($join) {
                    $join->on('mrs.sectioncode', '=', 'pr.sectioncode')
                        ->where('mrs.statusflag', 'Y')
                        ->where('mrs.type', 'F');
                })
                ->where('pr.praudittitleid', $praudittitleid)
                ->where('pr.deptcode', $deptcode)
                ->where('pr.catcode', $catcode)
                ->where('pr.financialyear', $financialyear)
                ->whereIn('pr.statusflag', ['F', 'A'])
                ->whereNotNull('pr.fileuploadid')
                ->select('pr.*', 'mrs.titleename', 'mrs.titletname', 'mrs.sectioncode as file_sectioncode')
                ->get();

            // Process each section's file attachments - decode JSONB array and fetch file details
            $processedAttachments = [];
            foreach ($fileAttachments as $attachment) {
                $sectionCode = $attachment->file_sectioncode;

                if (! isset($processedAttachments[$sectionCode])) {
                    $processedAttachments[$sectionCode] = [
                        'section_title_en' => $attachment->titleename,
                        'section_title_ta' => $attachment->titletname,
                        'files' => [],
                    ];
                }

                // Decode the JSONB array of file IDs
                $fileIds = json_decode($attachment->fileuploadid, true);

                if (! empty($fileIds) && is_array($fileIds)) {
                    // Fetch file details for all file IDs in this section
                    $fileDetails = DB::table('audit.fileuploaddetail')
                        ->whereIn('fileuploadid', $fileIds)
                        ->where('statusflag', 'Y')
                        ->select('fileuploadid', 'filename', 'filepath', 'filesize', 'mimetype')
                        ->get();

                    foreach ($fileDetails as $fileDetail) {
                        $processedAttachments[$sectionCode]['files'][] = (object) [
                            'sectioncode' => $sectionCode,
                            'filename' => $fileDetail->filename,
                            'filepath' => $fileDetail->filepath,
                            'filesize' => $fileDetail->filesize,
                            'mimetype' => $fileDetail->mimetype,
                            'fileuploadid' => $fileDetail->fileuploadid,
                        ];
                    }
                }
            }

            $dynamicData = [
                'DeptName' => $deptInfo->deptelname ?? '',
                'DistName' => $deptInfo->distelname ?? '',
                'TitleName' => $language === 'ta' ? ($titleInfo->titletname ?? '') : ($titleInfo->titleename ?? ''),
                'FinancialYear' => $financialYearInfo->financialyear ?? ($language === 'ta' ? 'தெரியாத கணக்கு ஆண்டு' : 'Unknown Audit Period'),
                'base64Image' => 'data:image/png;base64,'.base64_encode(file_get_contents(public_path('site/image/tn__logo.png'))),
                'deptsize' => 24,
            ];

            // Initialize mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'fontDir' => array_merge(
                    (new \Mpdf\Config\ConfigVariables)->getDefaults()['fontDir'],
                    [public_path('fonts/tamil')]
                ),
                'fontdata' => (new \Mpdf\Config\FontVariables)->getDefaults()['fontdata'] + [
                    'noto1' => [
                        'R' => 'Latha.ttf',
                        'useOTL' => 0xFF,
                    ],
                    'noto' => [
                        'R' => 'times.ttf',
                        'useOTL' => 0xFF,
                    ],
                    'times' => [
                        'R' => 'times.ttf',
                        'useOTL' => 0xFF,
                    ],
                    'arial' => [
                        'R' => 'arial.ttf',
                        'useOTL' => 0xFF,
                    ],
                ],
                'default_font' => 'noto1',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_top' => 11,    // Top margin
                'margin_bottom' => 12, // Bottom margin
                'margin_left' => 10,   // Left margin
                'margin_right' => 11,   // Right margin
            ]);
            $borderHtml = '<div style="
                                position: fixed;
                                top: -10;
                                left: -20;
                                right: -20;
                                bottom: 20;
                                width: 100%;
                                height: 100%;
                                border: 2px solid black;
                                box-sizing: border-box;
                                padding:20px;
                                padding-bottom:none;
                                text-align: justify;
                            "></div>';

            $mpdf->SetHTMLHeader($borderHtml);
            $mpdf->SetHTMLFooter($borderHtml);

            $mpdf->SetHTMLFooter('<div style="text-align: center; font-size: 10pt; color: #555;">Page {PAGENO} of {nbpg}</div>');

            $firstPageHtml = $this->renderFirstPage($dynamicData, $language);
            $mpdf->WriteHTML($firstPageHtml);

            $sectionNumber = 1;
            foreach ($sections as $section) {
                $content = $sectionContents->get($section->sectioncode);

                $sectionTitle = $language === 'ta' ? $section->titletname : $section->titleename;

                if ($section->type === 'F') {
                    $sectionData = isset($processedAttachments[$section->sectioncode]) ? $processedAttachments[$section->sectioncode] : null;
                    $sectionFiles = $sectionData ? $sectionData['files'] : [];

                    if (count($sectionFiles) > 0) {
                        foreach ($sectionFiles as $fileIdx => $attachment) {
                            if ($attachment->filepath && ! empty($attachment->filepath)) {
                                $filePath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $attachment->filepath);
                                $fullPath = storage_path('app/public/'.ltrim($filePath, '/'));

                                if (file_exists($fullPath)) {
                                    $mimeType = mime_content_type($fullPath);

                                    $titleHtml = '<div style=" padding: 20px;">';
                                    $titleHtml .= '<h2 style="text-align:center; margin:0; font-size:28px;">'.$sectionNumber.'. '.htmlspecialchars($sectionTitle).'</h2>';

                                    $mpdf->AddPage();

                                    $titleHtml .= '<hr style=" margin: 20px 0;">';
                                    $titleHtml .= '</div>';
                                    $mpdf->WriteHTML($titleHtml);

                                    if ($mimeType === 'application/pdf') {
                                        try {
                                            $pageCount = $mpdf->SetSourceFile($fullPath);

                                            for ($i = 1; $i <= $pageCount; $i++) {
                                                if ($i > 1) {
                                                    $mpdf->AddPage();
                                                }

                                                $tplId = $mpdf->ImportPage($i);

                                                $size = $mpdf->getTemplateSize($tplId);

                                                $pageWidth = $mpdf->w - $mpdf->lMargin - $mpdf->rMargin;
                                                $pageHeight = $mpdf->h - $mpdf->tMargin - $mpdf->bMargin;

                                                $scaleX = $pageWidth / $size['width'];
                                                $scaleY = $pageHeight / $size['height'];
                                                $scale = min($scaleX, $scaleY);

                                                $x = $mpdf->lMargin + ($pageWidth - ($size['width'] * $scale)) / 2;
                                                $y = $mpdf->tMargin + ($pageHeight - ($size['height'] * $scale)) / 2;

                                                $mpdf->UseTemplate($tplId, $x, $y, $size['width'] * $scale, $size['height'] * $scale);
                                            }
                                        } catch (\Exception $e) {
                                            $mpdf->WriteHTML('<p style="color:red;">Error loading PDF: '.htmlspecialchars($e->getMessage()).'</p>');
                                        }
                                    } elseif (strpos($mimeType, 'image/') === 0) {
                                        // For images, embed directly
                                        $imageData = base64_encode(file_get_contents($fullPath));
                                        $imgHtml = '<div style="text-align:center; padding:20px;">';
                                        $imgHtml .= '<img src="data:'.$mimeType.';base64,'.$imageData.'" style="max-width:100%; height:auto;">';
                                        $imgHtml .= '</div>';
                                        $mpdf->WriteHTML($imgHtml);
                                        // REMOVED: $mpdf->Rect(5, 5, $mpdf->w - 10, $mpdf->h - 10);
                                    } else {
                                        // For other file types, show download info
                                        $infoHtml = '<div style=" padding: 20px; text-align:center;">';
                                        $infoHtml .= '<p><strong>File:</strong> '.htmlspecialchars($attachment->filename).'</p>';
                                        $infoHtml .= '<p><strong>File type:</strong> '.htmlspecialchars($mimeType).'</p>';
                                        $infoHtml .= '<p>This file type cannot be embedded in the PDF.</p>';
                                        $infoHtml .= '<p>Please check the system storage for this file.</p>';
                                        $infoHtml .= '</div>';
                                        $mpdf->WriteHTML($infoHtml);
                                        // REMOVED: $mpdf->Rect(5, 5, $mpdf->w - 10, $mpdf->h - 10);
                                    }
                                }
                            }
                        }
                    } else {
                        // No attachments found for this section
                        $mpdf->AddPage();
                        $html = '<div style=" padding: 20px; min-height: 500px; display: flex; flex-direction: column; align-items: center; justify-content: center;">';
                        $html .= '<h2 style="text-align:center; margin:0; font-size:28px;">'.$sectionNumber.'. '.htmlspecialchars($sectionTitle).'</h2>';
                        $html .= '<hr style=" margin: 20px 0; width: 100%;">';
                        $html .= '<p style="text-align:center; color:#999;">'.($language === 'ta' ? 'இணைப்பு எதுவும் இல்லை' : 'No attachments available').'</p>';
                        $html .= '</div>';
                        $mpdf->WriteHTML($html);
                        // REMOVED: $mpdf->Rect(5, 5, $mpdf->w - 10, $mpdf->h - 10);
                    }
                } else {
                    // Handle text section
                    $mpdf->AddPage();

                    $html = '<div style="padding: 20px;">';
                    $html .= '<h2 style="text-align:center; margin:0; font-size:28px;">'.$sectionNumber.'. '.htmlspecialchars($sectionTitle).'</h2>';
                    $html .= '<hr style=" margin: 20px 0; width: 100%;">';

                    if ($content && $content->remarks) {
                        $remarks = $this->cleanRemarks($content->remarks);
                        $html .= '<div style="margin-top:30px; line-height:1.6; font-family:latha;">'.$remarks.'</div>';
                    } else {
                        $html .= '<p style="text-align:center; color:#999;">'.($language === 'ta' ? 'உள்ளடக்கம் எதுவும் இல்லை' : 'No contents').'</p>';
                    }

                    $html .= '</div>';
                    $mpdf->WriteHTML($html);
                    // REMOVED: $mpdf->Rect(5, 5, $mpdf->w - 10, $mpdf->h - 10);
                }

                $sectionNumber++;
            }

            // End of Report - Centered properly
            $mpdf->AddPage();

            $endTitle = $language === 'ta'
                ? 'அறிக்கையின் முடிவு'
                : 'End of the Report';

            $html = '<div style="padding: 20px; margin: 20px 0; min-height: 500px; display: flex; align-items: center; justify-content: center; text-align: center;">';

            $html .= '<h2 style=" position: absolute;
                    top: 50%;
                    left: 0%;
                    transform: translate(50%, 50%);
                    text-align: center;
                    width: 100%;text-align:center; font-size:30px; font-family:'.($language === 'ta' ? 'latha' : 'times').';">**'.$endTitle.'**</h2>';

            $html .= '</div>';

            $mpdf->WriteHTML($html);

            // REMOVED: $mpdf->Rect(5, 5, $mpdf->w - 10, $mpdf->h - 10);

            // Output PDF
            $fileName = 'Performance_Audit_Report_'.View::shared('get_nowtime')->format('Ymd_His').'.pdf';

            return response()->streamDownload(function () use ($mpdf) {
                echo $mpdf->Output('', 'S');
            }, $fileName);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Render first page for HTML view and PDF
     */
    private function renderFirstPage($data, $language = 'en')
    {
        $fontFamily = $language === 'ta' ? 'latha' : 'times';

        // Create HTML for first page
        $html = '<div style="display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%; font-family: '.$fontFamily.'; margin-top: 50px;">';
        $html .= '<div style="text-align: center;">';

        // Department name
        $deptName = $data['DeptName'] ?? ($language === 'ta' ? 'தணிக்கை துறை' : 'Audit Department');
        $html .= '<h1 style="font-size: 32px; margin-bottom: 30px; font-family: '.$fontFamily.';">'.htmlspecialchars($deptName).'</h1>';

        // Add logo
        if (isset($data['base64Image'])) {
            $html .= '<img src="'.$data['base64Image'].'" style="width: 150px; margin-bottom: 50px;">';
        }

        // District name
        $distName = $data['DistName'] ?? ($language === 'ta' ? 'மாவட்டம்' : 'District');
        $html .= '<h2 style="font-size: 24px; margin-bottom: 30px; font-family: '.$fontFamily.';">'.htmlspecialchars($distName).'</h2>';

        // Audit Title Name
        $titleName = $data['TitleName'] ?? '';
        if ($titleName) {
            $html .= '<h2 style="font-size: 26px; margin-bottom: 30px; color: #333; font-family: '.$fontFamily.';">'.htmlspecialchars($titleName).'</h2>';
        }

        // Report title
        $reportTitle = $language === 'ta' ? 'செயல்திறன் தணிக்கை அறிக்கை' : 'Performance Audit Report';
        $html .= '<h2 style="font-size: 28px; margin-bottom: 40px; text-decoration: underline; font-family: '.$fontFamily.';">'.$reportTitle.'</h2>';

        // Financial year
        $financialYear = $data['FinancialYear'] ?? ($language === 'ta' ? 'நிதியாண்டு' : 'Financial Year');
        $html .= '<p style="font-size: 18px; margin-bottom: 20px; font-family: '.$fontFamily.';"><strong>'.($language === 'ta' ? 'நிதியாண்டு:' : 'Financial Year:').'</strong> '.htmlspecialchars($financialYear).'</p>';

        // Report date
        $html .= '<p style="font-size: 18px; margin-bottom: 20px; font-family: '.$fontFamily.';"><strong>'.($language === 'ta' ? 'அறிக்கை தேதி:' : 'Report Date:').'</strong> '.date('d-m-Y').'</p>';

        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Format file size to human readable format
     */
    private function formatFileSize($bytes)
    {
        if (! $bytes) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));

        return round($bytes / pow(1024, $i), 2).' '.$units[$i];
    }

    public static function getFileDetails(Request $request)
    {
        try {
            $request->validate([
                'fileuploadids' => 'required|array',
                'fileuploadids.*' => 'integer',
            ]);

            $fileIds = $request->input('fileuploadids');

            $files = DB::table('audit.fileuploaddetail')
                ->join('audit.praudit_transpara as pr', 'fileuploaddetail.fileuploadid', '=', 'pr.fileuploadid')
                ->whereIn('fileuploaddetail.fileuploadid', $fileIds)
                ->where('fileuploaddetail.statusflag', 'Y')
                ->where('pr.statusflag', 'F')
                ->where('pr.fileinreportflag', 'Y')
                ->select('fileuploaddetail.fileuploadid', 'fileuploaddetail.filename', 'fileuploaddetail.filepath', 'fileuploaddetail.filesize')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $files,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function getReportFileDetails(Request $request)
    {
        try {
            $request->validate([
                'fileuploadids' => 'required|array',
                'fileuploadids.*' => 'integer',
            ]);

            $fileIds = $request->input('fileuploadids');

            $files = DB::table('audit.fileuploaddetail')
                ->join('audit.praudit_report as pr', function ($join) {
                    $join->on('fileuploaddetail.fileuploadid', '=', DB::raw('ANY(ARRAY(SELECT jsonb_array_elements_text(pr.fileuploadid::jsonb)::integer))'));
                })
                ->whereIn('fileuploaddetail.fileuploadid', $fileIds)
                ->where('fileuploaddetail.statusflag', 'Y')
                ->whereIn('pr.statusflag', ['F', 'A', 'Y'])
                ->select(
                    'fileuploaddetail.fileuploadid',
                    'fileuploaddetail.filename',
                    'fileuploaddetail.filepath',
                    'fileuploaddetail.filesize'
                )
                ->get();

            return response()->json([
                'success' => true,
                'data' => $files,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function updateFileInReportFlag(Request $request)
    {
        try {
            $request->validate([
                'fileuploadid' => 'required|integer',
                'praudittitleid' => 'required|integer',
                'fileinreportflag' => 'required|in:Y,N',
            ]);

            $fileuploadid = $request->input('fileuploadid');
            $praudittitleid = $request->input('praudittitleid');
            $flag = $request->input('fileinreportflag');
            $financialyear = $request->input('financialyear');
            $userid = session('user')->userid ?? session('user')->deptuserid;

            DB::beginTransaction();

            try {
                $auditInfo = DB::table('audit.auditplan as plan')
                    ->join('audit.inst_auditschedule as sch', 'plan.auditplanid', '=', 'sch.auditplanid')
                    ->join('audit.praudit_transpara as para', function ($join) {
                        $join->on('para.auditplanid', '=', 'plan.auditplanid')
                            ->on('para.auditscheduleid', '=', 'sch.auditscheduleid')
                            ->where('para.statusflag', 'F');
                    })
                    ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {
                        $join->on('prfmap.instid', '=', 'plan.instid')
                            ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                            ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                            ->where('prfmap.praudittitleid', '=', $praudittitleid);
                    })
                    ->where('para.fileuploadid', $fileuploadid)
                    ->where('plan.statusflag', 'F')
                    ->where('plan.auditmode', 'P')
                    ->where('sch.statusflag', 'F')
                    ->whereNotNull('sch.entrymeetdate')
                    ->select('plan.auditplanid', 'sch.auditscheduleid', 'para.praudittransid', 'plan.financialyearcode')
                    ->first();

                if (! $auditInfo) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Audit schedule not found',
                    ], 404);
                }

                DB::table('audit.praudit_transpara')
                    ->where('praudittransid', $auditInfo->praudittransid)
                    ->update([
                        'fileinreportflag' => $flag,
                        'updatedby' => $userid,
                        'updatedon' => View::shared('get_nowtime'),
                    ]);

                $titleInfo = DB::table('audit.mst_praudit_title')
                    ->where('praudittitleid', $praudittitleid)
                    ->where('statusflag', 'Y')
                    ->select('deptcode', 'catcode', 'subcatid')
                    ->first();

                if (! $titleInfo) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => 'Audit title not found',
                    ], 404);
                }

                $existingAttachments = DB::table('audit.praudit_report')
                    ->where('praudittitleid', $praudittitleid)
                    ->where('remarkstype', 'A')
                    ->first();

                $currentAttachments = [];
                if ($existingAttachments && $existingAttachments->fileuploadid) {
                    $currentAttachments = json_decode($existingAttachments->fileuploadid, true) ?: [];
                }

                if ($flag === 'Y') {
                    if (! in_array($fileuploadid, $currentAttachments)) {
                        $currentAttachments[] = (int) $fileuploadid;
                    }
                } else {
                    $currentAttachments = array_filter($currentAttachments, function ($id) use ($fileuploadid) {
                        return $id != $fileuploadid;
                    });
                    $currentAttachments = array_values($currentAttachments); // Reindex array
                }

                $attachmentsData = [
                    'deptcode' => $titleInfo->deptcode,
                    'catcode' => $titleInfo->catcode,
                    'subcatid' => $titleInfo->subcatid,
                    'praudittitleid' => $praudittitleid,
                    'remarks' => null,
                    'remarkstype' => 'A',
                    'sectioncode' => '13',
                    'financialyear' => $financialyear,
                    'fileuploadid' => json_encode($currentAttachments),
                    'statusflag' => 'Y',
                    'updatedby' => $userid,
                    'updatedon' => View::shared('get_nowtime'),
                ];

                if ($existingAttachments) {
                    DB::table('audit.praudit_report')
                        ->where('prauditreportid', $existingAttachments->prauditreportid)
                        ->update($attachmentsData);
                } else {
                    $attachmentsData['createdby'] = $userid;
                    $attachmentsData['createdon'] = View::shared('get_nowtime');
                    DB::table('audit.praudit_report')->insert($attachmentsData);
                }

                // 4. Get all flags for all files for this audit (for response)
                $allFlags = DB::table('audit.praudit_transpara as pt')
                    ->join('audit.auditplan as plan', 'pt.auditplanid', '=', 'plan.auditplanid')
                    ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {
                        $join->on('prfmap.instid', '=', 'plan.instid')
                            ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                            ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                            ->where('prfmap.praudittitleid', '=', $praudittitleid);
                    })
                    ->where('pt.statusflag', 'F')
                    ->where('pt.fileuploadid', 'IS NOT', DB::raw('NULL'))
                    ->pluck('pt.fileinreportflag', 'pt.fileuploadid')
                    ->toArray();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => $flag === 'Y' ? 'File added to report attachments' : 'File removed from report attachments',
                    'data' => [
                        'fileuploadid' => $fileuploadid,
                        'flag' => $flag,
                        'all_flags' => $allFlags,
                        'report_attachments' => $currentAttachments,
                    ],
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function removeSectionFile(Request $request)
    {
        try {
            $request->validate([
                'praudittitleid' => 'required|integer',
                'deptcode' => 'required|string',
                'catcode' => 'required|string',
                'subcatid' => 'nullable',
                'sectioncode' => 'required|string',
                'fileuploadid' => 'required|integer',
                'financialyear' => 'nullable|string',
            ]);

            $userid = session('user')->userid ?? session('user')->deptuserid;

            DB::beginTransaction();

            // Get the existing record
            $existingRecord = DB::table('audit.praudit_report')
                ->where('praudittitleid', $request->praudittitleid)
                ->where('deptcode', $request->deptcode)
                ->where('catcode', $request->catcode)
                ->where('subcatid', $request->subcatid)
                ->where('sectioncode', $request->sectioncode)
                ->first();

            if (! $existingRecord) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Record not found',
                ], 404);
            }

            // Prevent removal if finalized
            if ($existingRecord->statusflag == 'F' || $existingRecord->statusflag == 'A') {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Cannot remove file from finalized section',
                ], 403);
            }

            // Get current file IDs array
            $currentFileIds = [];
            if ($existingRecord->fileuploadid) {
                $currentFileIds = json_decode($existingRecord->fileuploadid, true);
                if (! is_array($currentFileIds)) {
                    $currentFileIds = [];
                }
            }

            // Remove the specific file ID
            $fileuploadidToRemove = (int) $request->fileuploadid;
            $newFileIds = array_filter($currentFileIds, function ($id) use ($fileuploadidToRemove) {
                return (int) $id !== $fileuploadidToRemove;
            });
            $newFileIds = array_values($newFileIds); // Re-index array

            // Update the record with new file IDs array
            DB::table('audit.praudit_report')
                ->where('prauditreportid', $existingRecord->prauditreportid)
                ->update([
                    'fileuploadid' => ! empty($newFileIds) ? json_encode($newFileIds) : null,
                    'updatedby' => $userid,
                    'updatedon' => View::shared('get_nowtime'),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'File removed successfully',
                'remaining_files' => $newFileIds,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error removing file: '.$e->getMessage(),
            ], 500);
        }
    }


    public static function verifyMemberRemarks(Request $request)
    {
        try {
            $request->validate([
                'praudittransid' => 'required|integer',
                'verified' => 'required|in:Y,N',
            ]);

            $praudittransid = $request->input('praudittransid');
            $verified = $request->input('verified');
            $userid = session('user')->userid ?? session('user')->deptuserid;

            DB::table('audit.praudit_transpara')
                ->where('praudittransid', $praudittransid)
                ->where('statusflag', 'F')
                ->update([
                    'prremarksverifyflag' => $verified,
                    'updatedby' => $userid,
                    'updatedon' => View::shared('get_nowtime'),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Remarks verification updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public static function processFileActions(Request $request)
    {
        try {
            $request->validate([
                'praudittransid' => 'required|integer',
                'fileuploadid' => 'required|integer',
                'praudittitleid' => 'required|integer',
                'include_in_report' => 'required|in:Y,N',
                'verify_file' => 'required|in:Y,N',
                'financialyear' => 'required|string',
            ]);

            $praudittransid = $request->input('praudittransid');
            $fileuploadid = $request->input('fileuploadid');
            $praudittitleid = $request->input('praudittitleid');
            $includeInReport = $request->input('include_in_report');
            $verifyFile = $request->input('verify_file');
            $financialyear = $request->input('financialyear');
            $userid = session('user')->userid ?? session('user')->deptuserid;

            DB::beginTransaction();

            $updateData = [
                'updatedby' => $userid,
                'updatedon' => View::shared('get_nowtime'),
            ];

            if ($includeInReport === 'Y') {
                $updateData['fileinreportflag'] = 'Y';
            } elseif ($includeInReport === 'N') {
                $updateData['fileinreportflag'] = 'N';
            }

            if ($verifyFile === 'Y') {
                $updateData['prfileverifyflag'] = 'Y';
            } elseif ($verifyFile === 'N') {
                $updateData['prfileverifyflag'] = 'N';
            }

            DB::table('audit.praudit_transpara')
                ->where('praudittransid', $praudittransid)
                ->where('statusflag', 'F')
                ->update($updateData);

            $reportAttachments = [];
            if ($includeInReport === 'Y') {
                $titleInfo = DB::table('audit.mst_praudit_title')
                    ->where('praudittitleid', $praudittitleid)
                    ->where('statusflag', 'Y')
                    ->select('deptcode', 'catcode', 'subcatid')
                    ->first();

                if ($titleInfo) {
                    $existingAttachments = DB::table('audit.praudit_report')
                        ->where('praudittitleid', $praudittitleid)
                        ->where('remarkstype', 'A')
                        ->first();

                    $currentAttachments = [];
                    if ($existingAttachments && $existingAttachments->fileuploadid) {
                        $currentAttachments = json_decode($existingAttachments->fileuploadid, true) ?: [];
                    }

                    if (! in_array($fileuploadid, $currentAttachments)) {
                        $currentAttachments[] = (int) $fileuploadid;

                        $attachmentsData = [
                            'deptcode' => $titleInfo->deptcode,
                            'catcode' => $titleInfo->catcode,
                            'subcatid' => $titleInfo->subcatid,
                            'praudittitleid' => $praudittitleid,
                            'remarkstype' => 'A',
                            'sectioncode' => '13',
                            'financialyear' => $financialyear,
                            'fileuploadid' => json_encode($currentAttachments),
                            'statusflag' => 'Y',
                            'updatedby' => $userid,
                            'updatedon' => View::shared('get_nowtime'),
                        ];

                        if ($existingAttachments) {
                            DB::table('audit.praudit_report')
                                ->where('prauditreportid', $existingAttachments->prauditreportid)
                                ->update($attachmentsData);
                        } else {
                            $attachmentsData['createdby'] = $userid;
                            $attachmentsData['createdon'] = View::shared('get_nowtime');
                            DB::table('audit.praudit_report')->insert($attachmentsData);
                        }

                        $reportAttachments = $currentAttachments;
                    }
                }
            }

            // Get all flags for response
            $allFlags = DB::table('audit.praudit_transpara as pt')
                ->join('audit.auditplan as plan', 'pt.auditplanid', '=', 'plan.auditplanid')
                ->join('audit.mst_prauditinstmapping as prfmap', function ($join) use ($praudittitleid) {
                    $join->on('prfmap.instid', '=', 'plan.instid')
                        ->on('prfmap.finyearcode', '=', 'plan.financialyearcode')
                        ->on('prfmap.quartercode', '=', 'plan.auditquartercode')
                        ->where('prfmap.praudittitleid', '=', $praudittitleid);
                })
                ->where('pt.statusflag', 'F')
                ->whereNotNull('pt.fileuploadid')
                ->pluck('pt.fileinreportflag', 'pt.fileuploadid')
                ->toArray();

            DB::commit();

            $message = [];
            if ($includeInReport === 'Y') {
                $message[] = 'File added to report';
            }
            if ($verifyFile === 'Y') {
                $message[] = 'File verified';
            }

            return response()->json([
                'success' => true,
                'message' => implode(' & ', $message).' successfully',
                'updated_flags' => [
                    'all_flags' => $allFlags,
                    'report_attachments' => $reportAttachments,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // ---------------------Performance Audit Consolidation Report--------------------------//




    public function removeFile(Request $request)
    {
        try {
            $request->validate([
                'fileuploadid' => 'required|integer',
                'auditscheduleid' => 'required|integer'
            ]);

            $user = session('user');
            if (!$user || empty($user->userid)) {
                throw new Exception('User not authenticated');
            }

            $fileuploadid = $request->fileuploadid;
            $auditscheduleid = $request->auditscheduleid;
            $userid = $user->userid;

            $fileDetails = PerformanceModel::getFileDetails($fileuploadid);

            if (!$fileDetails) {
                throw new Exception('File not found');
            }

            $result = PerformanceModel::updateFileStatus($fileuploadid, 'N');

            if (!$result['success']) {
                throw new Exception('Failed to update file status');
            }

            if ($fileDetails && isset($fileDetails->filepath)) {
                $fullPath = public_path($fileDetails->filepath);

                if (!file_exists($fullPath)) {
                    $fullPath = public_path('storage/' . $fileDetails->filepath);
                }
                if (!file_exists($fullPath)) {
                    $fullPath = storage_path('app/public/' . $fileDetails->filepath);
                }

                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $existingRecord = PerformanceModel::getExistingData($auditscheduleid, $userid);

            if ($existingRecord) {
                PerformanceModel::updateFileUploadId($existingRecord->praudittransid, null, $userid);
            }

            return response()->json([
                'success' => true,
                'message' => 'File removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing file: ' . $e->getMessage()
            ], 500);
        }
    }
}
