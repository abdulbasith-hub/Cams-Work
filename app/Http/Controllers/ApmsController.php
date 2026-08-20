<?php

namespace App\Http\Controllers;

use App\Models\ApmsModel;
use App\Models\BaseModel;
use App\Models\LagacyModel;
use App\Models\SmsmailModel;
use App\Services\PHPMailerService;
use App\Services\SmsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
// use DB;
use App\Services\FileUploadService;

class ApmsController extends Controller
{
    protected static $mstauditeeinscategory_table = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;

    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    // ---------------------------------------Retirement Para--------------------------------------------//
    public static function fetchdept(request $request)
    {
        $dept = ApmsModel::deptfetch();

        return view('apms.psaauditorremove', compact('dept'));
    }

    public function fetch_para_datas(Request $request)
    {
        try {
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;

            $paraid = Crypt::decryptString($request->input('paraid'));
            $followupid = $request->input('followupid');

            $apms_hlcid = filled($request->apms_hlcid)
                ? Crypt::decryptString($request->apms_hlcid)
                : null;

            $request->merge(['paraid' => $paraid]);

            $request->validate(
                [
                    'param' => 'required|in:view_para,view_flow,view_minutes',
                    'paraid' => ['nullable', 'integer', 'min:1'],
                ],
                [
                    'param.required' => 'Action type is required.',
                    'param.in' => 'Invalid action type selected.',
                    'paraid.required' => 'The paraid field is required.',
                    'paraid.integer' => 'The paraid field must be a valid integer.',
                    'paraid.min' => 'The paraid field must be greater than 0.',
                ]
            );

            $param = $request->param;
            $paraid = $request->paraid;
            $parahistory = '';

            switch ($param) {
                case 'view_para':
                    $result = ApmsModel::fetch_paradetails($paraid, $followupid);

                    $parahistory = ApmsModel::fetch_parahistory($paraid, $followupid);

                    break;

                case 'view_flow':
                    $result = ApmsModel::fetch_paraflow($paraid, $followupid);
                    break;

                case 'view_history':
                    $result = ApmsModel::fetch_parahistory($paraid, $followupid);
                    break;
                case 'view_minutes':
                    $result = ApmsModel::fetch_minutes($paraid, $apms_hlcid);
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid request type'
                    ], 400);
            }

            if ($result)
                return response()->json(['data' => $result, 'parahistory' => $parahistory]);
            else
                return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], 500);
            // echo 'An error occurred while fetching para details. Please try again later.';
        }
    }

    public function fetchRegion(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');

        $regions = ApmsModel::regionfetch($deptcode);
        // print_r($regions);

        return response()->json([
            'success' => true,
            'data' => $regions
        ]);
    }

    public function fetch_districts(Request $request)
    {
        $request->validate([
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $regioncode = $request->input('regioncode');

        $dist = ApmsModel::fetch_districts($regioncode);
        // print_r($regions);

        return response()->json([
            'success' => true,
            'data' => $dist
        ]);
    }

    public function fetchpsausers(Request $request)
    {
        $request->validate([
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);
        $deptcode = $request->input('deptcode');
        $regioncode = $request->input('regioncode');
        $distcode = $request->input('distcode');

        $users = ApmsModel::getusernameforpsaauditors($deptcode, $regioncode, $distcode);
        // print_r($users);exit;

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function pasauditorsupdate(Request $request)
    {
        $sessiondet = session('user');
        $userid = $sessiondet->userid;

        $request->validate([
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);
        $pasuser = $request->input('pasuser');

        $users = ApmsModel::removepsaAuditors($pasuser, $userid);

        return response()->json([
            'success' => true,
            'message' => 'psaupdate',
            'data' => $users
        ]);
    }

    public function fetchauditpsausers(Request $request)
    {
        $users = ApmsModel::fetchauditpsausers();
        // print_r($users);exit;

        return response()->json([
            'success' => true,
            'message' => 'psas',
            'data' => $users
        ]);
    }

    public function fetchretirementpara(Request $request)
    {
        $chargeData = session('charge');
        $sesroleactioncode = $chargeData->roleactioncode ?? null;

        if ($sesroleactioncode != view::shared('rtd_committee_roleaction')) {
            abort(403, 'Unauthorized access');
        }

        $result = ApmsModel::fetchretirenmentparsa();
        // return $result;
        $paras = $result['paras'];
        $rejoinderLimit = $result['rejoinderLimit'];

        $paraction = ApmsModel::fetchactions_basedonroleaction($sesroleactioncode);

        // 🔐 Encrypt paraid
        $paras = $paras->map(function ($row) {
            $row->encrypted_paraid = Crypt::encryptString($row->paraid);
            unset($row->paraid);
            return $row;
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $paras,
                'actions' => $paraction,
                'rejoinderLimit' => $rejoinderLimit,  // ✅ second value
            ]);
        }

        return view('apms.retirementparas');
    }

    public function fetch_para_datasforremoval(Request $request)
    {
        try {
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;

            // $paraid     = Crypt::decryptString($request->input('paraid'));

            $param = $request->param;
            $paraid = $request->paraid;
            $parahistory = '';
            switch ($param) {
                case 'view_para':
                    $result = ApmsModel::fetch_para_datasforremoval($paraid);
                    $parahistory = ApmsModel::fetch_parahistory($paraid);
                    break;

                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid request type'
                    ], 400);
            }

            // print_r($result);
            if ($result)
                return response()->json(['data' => $result, 'parahistory' => $parahistory]);
            else
                return response()->json(['data' => $result]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], 500);
            // echo 'An error occurred while fetching para details. Please try again later.';
        }
    }

    public function fetchresponsibilityremove(Request $request)
    {
        $chargeData = session('charge');
        $sesroleactioncode = $chargeData->roleactioncode ?? null;

        // print_r($request->all());exit;
        if ($sesroleactioncode != view::shared('rtd_committee_roleaction')) {
            abort(403, 'Unauthorized access');
        }

        $result = ApmsModel::fetchresponsibilityremove();

        $paraction = ApmsModel::fetchactions_basedonroleaction($sesroleactioncode);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'actions' => $paraction,
                'data' => $result,
            ]);
        }

        return view('apms.responsibilityremove');
    }

    public function downloadParaPdf($id)
    {
        $para = ApmsModel::get_removedLiability($id);

        $paranumbers = $para->pluck('parano')->unique()->implode(', ');
        $liabilityNames = $para->map(function ($item) {
            return $item->liabilityname . (!empty($item->liabilitygpfno) ? " ({$item->liabilitygpfno})" : '');
        })->implode(', ');
        // print_r($paranumbers);exit;
        // print_r($liabilityNames);

        // exit;

        $gpfNumbers = $para->pluck('liabilitygpfno')->filter()->implode(', ');  // only if exists
        $instename = $para->first()->instename ?? '________________';
        $insttname = $para->first()->insttname ?? '';
        $audit_period = $para->first()->audit_period ?? '________';
        $updatedon = $para->first()->updatedon ?? now()->format('d-m-Y');

        // print_r($para);exit;
        // return $para;
        if (!$para) {
            abort(404, 'Para not found');
        }

        // Build HTML directly
        $html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Responsibility Removal Certificate</title>
    <style>
        body { font-family: dejavusans; font-size: 14px; line-height: 1.8; margin: 0; padding: 20px; }
        .title { text-align: center; font-size: 18px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        .content { text-align: justify; margin-bottom: 50px; }
        .blank { font-weight: bold; border-bottom: 1px solid #000; padding: 0 5px; }
        .signature { margin-top: 60px; text-align: right; }
        .footer { font-size: 11px; text-align: left; margin-top: 600px; color: #110f0f;justify-content: flex; display: flex; }
    </style>
</head>
<body>
    <div class="title">
        Certificate regarding Action on Fixing / Removal of Responsibility in respect of Audit Para
    </div>

    <div class="content">
        This is to certify that in respect of Audit Para No. 
        <span class="blank">' . ($paranumbers ?? '________') . '</span>, 

        relating to Audit Report Year 
        <span class="blank">' . ($audit_period ?? '________') . '</span>, 

        pertaining to 
        <span class="blank">' . ($instename ?? '________________') . '</span>, 

        action regarding fixation / removal of responsibility has been examined. 
        Based on the replies and documents submitted in CAMS, it is certified that:

        <br><br>

        The subject matter relating to fixation of responsibility in respect of 
        <span class="blank">' . ($liabilityNames ?? '________') . '</span> 
        has been removed from the para; however, the audit para continues to be reflected as 
        “Pending” in the Audit Para Management System (APMS) of CAMS as on date 
        <span class="blank">' . (!empty($updatedon) ? date('d-m-Y', strtotime($updatedon)) : date('d-m-Y')) . '</span>.
    </div>


    <div class="footer">
        This system-generated certificate certifies that the assigned responsibilities 
        of the concerned APMS in CAMS have been officially removed as per approval. This document is generated electronically for record purposes and is valid without manual signature.
    </div>

</body>
</html>
';

        // mPDF
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'dejavusans'
        ]);

        $mpdf->WriteHTML($html);

        return response()->make(
            $mpdf->Output('Liabilty_' . $id . '.pdf', 'S'),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="Responsibility Remove Letter.pdf"'
            ]
        );
    }

    // public function fetch_para_datas(Request $request)
    // {
    //     try {
    //         $userdet = session('user');
    //         $chargedet = session('charge');

    //         $userid = $userdet->userid;
    //         $userchargeid = $chargedet->userchargeid;

    //         // $paraid = Crypt::decryptString($request->input('paraid'));
    //         $paraid = $request->input('paraid');

    //         throw_if(empty($paraid), new \Exception("Para ID not found"));

    //         $request->merge(['paraid' => $paraid]);

    //         $request->validate(
    //             [
    //                 'param' => 'required|in:view_para,view_flow,view_minutes',
    //                 'paraid' => ['required', 'integer', 'min:1'],
    //             ],
    //             [
    //                 'param.required' => 'Action type is required.',
    //                 'param.in' => 'Invalid action type selected.',
    //                 'paraid.required' => 'The paraid field is required.',
    //                 'paraid.integer' => 'The paraid field must be a valid integer.',
    //                 'paraid.min' => 'The paraid field must be greater than 0.',
    //             ]
    //         );

    //         $param = $request->param;
    //         $paraid = $request->paraid;

    //         switch ($param) {

    //             case 'view_para':
    //                 $result = ApmsModel::fetch_paradetails($paraid);
    //                 $liabilty = ApmsModel::fetch_liabiltydetails($paraid);
    //                 break;

    //             // case 'view_flow':
    //             //     $result = ApmsModel::fetch_paraflow($paraid);
    //             //     break;

    //             // case 'view_history':
    //             //     $result = ApmsModel::fetch_parahistory($paraid);
    //             //     break;
    //             // case 'view_minutes':
    //             //     $result = ApmsModel::fetch_minutes($paraid);
    //             //     break;

    //             default:
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Invalid request type'
    //                 ], 400);
    //         }

    //         // print_r($liabilty);exit;
    //         return response()->json([
    //             'success' => true,
    //             'para' => $result,
    //             'liability' => $liabilty
    //         ]);
    //     } catch (\Exception $e) {
    //         echo $e->getMessage();
    //         // echo 'An error occurred while fetching para details. Please try again later.';
    //     }
    // }

    //  public function update_retirement_paras(Request $request)
    // {
    //     $request->validate([
    //         'paras' => 'required|array|min:1',
    //         'paras.*.paraid' => 'required|string',
    //         'paras.*.actionid' => 'required|string',
    //         'action' => 'required|in:draft,finalize',

    //     ]);
    //     $chargedet = session('charge');

    //     $paras = collect($request->paras)->map(function ($row) {
    //         try {
    //             $row['paraid'] = Crypt::decryptString($row['paraid']);
    //         } catch (\Exception $e) {
    //             abort(400, 'Invalid para id');
    //         }

    //         if (!is_numeric($row['paraid']) || $row['paraid'] < 1) {
    //             abort(400, 'Invalid para id');
    //         }

    //         $row['paraid'] = (int) $row['paraid'];
    //         return $row;
    //     });

    //     $action = $request->action;

    //     // ✅ Convert date
    //     $mom_date = null;

    //     if ($request->action === 'finalize' && $request->filled('date')) {
    //         $mom_date = Carbon::createFromFormat('d/m/Y', $request->date)
    //             ->format('Y-m-d');
    //     }

    //     DB::beginTransaction();

    //     try {

    //         $fileuploadId = null;

    //         if ($request->hasFile('file')) {

    //             $file = $request->file('file');

    //             $mom_file = str_replace('-', '_', $mom_date);

    //             $destinationPath = 'uploads/retire/';
    //             $destinationarray = [
    //                 $chargedet->deptcode,
    //                 $chargedet->regioncode,
    //                 $chargedet->distcode,
    //                 $mom_file,
    //                 View::shared('retiredparas'),
    //             ];

    //             $uploadResult = $this->fileUploadService->uploadFile(
    //                 $file,
    //                 $destinationPath,
    //                 $request->uploadid ?? '',
    //                 $destinationarray
    //             );

    //             $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
    //             // return  $fileuploadId;
    //             // print_r($fileuploadId);exit;
    //             // throw_if(!$fileuploadId, new \Exception('File upload failed'));
    //         }

    //         foreach ($paras as $row) {

    //             ApmsModel::updateRetirementParaAction(
    //                 $row['paraid'],
    //                 $row['actionid'],
    //                 $action,
    //                 $fileuploadId,
    //                 $mom_date,
    //                 $chargedet->deptcode,
    //                 $chargedet->regioncode,
    //                 $chargedet->distcode,
    //                 $row['remarks'] ?? null

    //             );
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => $action === 'draft'
    //                 ? 'Paras saved as draft successfully'
    //                 : 'Paras finalized successfully'
    //         ]);
    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Server error',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function update_retirement_paras(Request $request)
    {
        $action = $request->action;
        $chargedet = session('charge');

        /*
         * |--------------------------------------------------------------------------
         * | ✅ LIABILITY STAGE UPDATE
         * |--------------------------------------------------------------------------
         */
        if ($action === 'liability_stage') {
            $request->validate([
                'statuses' => 'required|array|min:1',
                'statuses.*.followupliabilityid' => 'required|integer',
                'statuses.*.statusflag' => 'required|in:Y,C'
            ]);

            DB::beginTransaction();

            try {
                foreach ($request->statuses as $row) {
                    ApmsModel::updateLiabilityStatus(
                        $row['followupliabilityid'],
                        $row['statusflag'],
                        $chargedet->deptcode,
                        $chargedet->regioncode,
                        $chargedet->distcode
                    );
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'liabilty_status'
                ]);
            } catch (\Exception $e) {
                DB::rollBack();

                return response()->json([
                    'success' => false,
                    'message' => 'Server error',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        /*
         * |--------------------------------------------------------------------------
         * | ✅ DRAFT / FINALIZE LOGIC
         * |--------------------------------------------------------------------------
         */

        $request->validate([
            'paras' => 'required|array|min:1',
            'paras.*.paraid' => 'required|string',
            'paras.*.actionid' => 'required|string',
            'action' => 'required|in:draft,finalize',
        ]);

        $paras = collect($request->paras)->map(function ($row) {
            try {
                $row['paraid'] = Crypt::decryptString($row['paraid']);
            } catch (\Exception $e) {
                abort(400, 'Invalid para id');
            }

            if (!is_numeric($row['paraid']) || $row['paraid'] < 1) {
                abort(400, 'Invalid para id');
            }

            $row['paraid'] = (int) $row['paraid'];

            return $row;
        });

        $mom_date = null;

        if ($action === 'finalize' && $request->filled('date')) {
            $mom_date = Carbon::createFromFormat('d/m/Y', $request->date)
                ->format('Y-m-d');
        }

        DB::beginTransaction();

        try {
            $fileuploadId = null;

            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $mom_file = str_replace('-', '_', $mom_date);

                $destinationPath = 'uploads/retire/';

                $destinationarray = [
                    $chargedet->deptcode,
                    $chargedet->regioncode,
                    $chargedet->distcode,
                    $mom_file,
                    View::shared('retiredparas'),
                ];

                $uploadResult = $this->fileUploadService->uploadFile(
                    $file,
                    $destinationPath,
                    $request->uploadid ?? '',
                    $destinationarray
                );

                $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
            }

            foreach ($paras as $row) {
                ApmsModel::updateRetirementParaAction(
                    $row['paraid'],
                    $row['actionid'],
                    $action,
                    $fileuploadId,
                    $mom_date,
                    $chargedet->deptcode,
                    $chargedet->regioncode,
                    $chargedet->distcode,
                    $row['remarks'] ?? null
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $action === 'draft'
                    ? 'Paras saved as draft successfully'
                    : 'Paras finalized successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    //       public function update_retirement_paras(Request $request)
    // {
    //     $action = $request->action;
    //     $chargedet = session('charge');

    //     /*
    //     |--------------------------------------------------------------------------
    //     | ✅ LIABILITY STAGE UPDATE
    //     |--------------------------------------------------------------------------
    //     */
    //     if ($action === 'liability_stage') {

    //         $request->validate([
    //             'statuses' => 'required|array|min:1',
    //             'statuses.*.followupliabilityid' => 'required|integer',
    //             'statuses.*.statusflag' => 'required|in:Y,C'
    //         ]);

    //         DB::beginTransaction();

    //         try {

    //             foreach ($request->statuses as $row) {

    //                 ApmsModel::updateLiabilityStatus(
    //                     $row['followupliabilityid'],
    //                     $row['statusflag'],
    //                     $chargedet->deptcode,
    //                     $chargedet->regioncode,
    //                     $chargedet->distcode
    //                 );
    //             }

    //             DB::commit();

    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'liabilty_status'
    //             ]);

    //         } catch (\Exception $e) {

    //             DB::rollBack();

    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Server error',
    //                 'error' => $e->getMessage()
    //             ], 500);
    //         }
    //     }

    //     /*
    //     |--------------------------------------------------------------------------
    //     | ✅ DRAFT / FINALIZE LOGIC
    //     |--------------------------------------------------------------------------
    //     */

    //     $request->validate([
    //         'paras' => 'required|array|min:1',
    //         'paras.*.paraid' => 'required|string',
    //         'paras.*.actionid' => 'required|string',
    //         'action' => 'required|in:draft,finalize',
    //     ]);

    //     $paras = collect($request->paras)->map(function ($row) {

    //         try {
    //             $row['paraid'] = Crypt::decryptString($row['paraid']);
    //         } catch (\Exception $e) {
    //             abort(400, 'Invalid para id');
    //         }

    //         if (!is_numeric($row['paraid']) || $row['paraid'] < 1) {
    //             abort(400, 'Invalid para id');
    //         }

    //         $row['paraid'] = (int) $row['paraid'];

    //         return $row;
    //     });

    //     $mom_date = null;

    //     if ($action === 'finalize' && $request->filled('date')) {
    //         $mom_date = Carbon::createFromFormat('d/m/Y', $request->date)
    //             ->format('Y-m-d');
    //     }

    //     DB::beginTransaction();

    //     try {

    //         $fileuploadId = null;

    //         if ($request->hasFile('file')) {

    //             $file = $request->file('file');

    //             $mom_file = str_replace('-', '_', $mom_date);

    //             $destinationPath = 'uploads/retire/';

    //             $destinationarray = [
    //                 $chargedet->deptcode,
    //                 $chargedet->regioncode,
    //                 $chargedet->distcode,
    //                 $mom_file,
    //                 View::shared('retiredparas'),
    //             ];

    //             $uploadResult = $this->fileUploadService->uploadFile(
    //                 $file,
    //                 $destinationPath,
    //                 $request->uploadid ?? '',
    //                 $destinationarray
    //             );

    //             $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
    //         }

    //         foreach ($paras as $row) {

    //             ApmsModel::updateRetirementParaAction(
    //                 $row['paraid'],
    //                 $row['actionid'],
    //                 $action,
    //                 $fileuploadId,
    //                 $mom_date,
    //                 $chargedet->deptcode,
    //                 $chargedet->regioncode,
    //                 $chargedet->distcode,
    //                 $row['remarks'] ?? null
    //             );
    //         }

    //         DB::commit();

    //         return response()->json([
    //             'success' => true,
    //             'message' => $action === 'draft'
    //                 ? 'Paras saved as draft successfully'
    //                 : 'Paras finalized successfully'
    //         ]);

    //     } catch (\Exception $e) {

    //         DB::rollBack();

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Server error',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

    public function download_liability_certificate(Request $request)
    {
        try {
            $request->merge([
                'followupid' => $request->filled('followupid')
                    ? Crypt::decryptString($request->followupid)
                    : null,
            ]);

            $request->validate([
                'followupid' => ['required', 'integer'],
            ]);

            $followupid = $request->followupid;

            throw_if(empty($followupid), new \Exception('Followup Id not found'));

            $lang = $request->input('lang', 'en');

            // Fetch multiple rows
            $para = ApmsModel::get_responsible_Liability($followupid);

            // if (empty($paraList) || count($paraList) == 0) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'No liability details found'
            //     ], 404);
            // }
            $paranumbers = $para->pluck('parano')->unique()->implode(', ');
            $liabilityNames = $para->map(function ($item) {
                return $item->liabilityname . (!empty($item->liabilitygpfno) ? " ({$item->liabilitygpfno})" : '');
            })->implode(', ');
            // print_r($paranumbers);exit;
            // print_r($liabilityNames);

            // exit;

            $gpfNumbers = $para->pluck('liabilitygpfno')->filter()->implode(', ');  // only if exists
            $instename = $para->first()->instename ?? '________________';
            $insttname = $para->first()->insttname ?? '';
            $audit_period = $para->first()->audit_period ?? '________';
            $updatedon = $para->first()->updatedon ?? now()->format('d-m-Y');

            // print_r($para);exit;
            // return $para;
            if (!$para) {
                abort(404, 'Para not found');
            }

            // Build HTML directly
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Responsibility Removal Certificate</title>
                <style>
                    body { font-family: dejavusans; font-size: 14px; line-height: 1.8; margin: 0; padding: 20px; }
                    .title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; }
                    .content { text-align: justify; margin-bottom: 50px; }
                    .blank { font-weight: bold; border-bottom: 1px solid #000; padding: 0 5px; }
                    .signature { margin-top: 60px; text-align: right; }
                    .footer { font-size: 11px; text-align: left; margin-top: 600px; color: #110f0f;justify-content: flex; display: flex; }
                </style>
            </head>
            <body>
                <div class="title">
                    Certificate regarding Action on Fixing / Removal of Responsibility in respect of Audit Para
                </div>
                
                <div class="content">
                    This is to certify that in respect of Audit Para No. 
                    <span class="blank">' . ($paranumbers ?? '________') . '</span>, 
                
                    relating to Audit Report Year 
                    <span class="blank">' . ($audit_period ?? '________') . '</span>, 
                
                    pertaining to 
                    <span class="blank">' . ($instename ?? '________________') . '</span>, 
                
                    action regarding fixation / removal of responsibility has been examined. 
                    Based on the replies and documents submitted in CAMS, it is certified that:
                    
                    <br><br>
                    
                    The subject matter relating to fixation of responsibility in respect of 
                    <span class="blank">' . ($liabilityNames ?? '________') . '</span> 
                    has been removed from the para; however, the audit para continues to be reflected as 
                    “Pending” in the Audit Para Management System (APMS) of CAMS as on date 
                    <span class="blank">' . (!empty($updatedon) ? date('d-m-Y', strtotime($updatedon)) : date('d-m-Y')) . '</span>.
                </div>
                    
                    
                <div class="footer">
                    This system-generated certificate certifies that the assigned responsibilities 
                    of the concerned APMS in CAMS have been officially removed as per approval. This document is generated electronically for record purposes and is valid without manual signature.
                </div>
                    
            </body>
            </html>
            ';

            // mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'default_font' => 'dejavusans'
            ]);

            $mpdf->WriteHTML($html);

            return response()->make(
                $mpdf->Output('Liabilty_' . $followupid . '.pdf', 'S'),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="Responsibility Remove Letter.pdf"'
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getprocescode_basedonaction($actioncode, $roleactioncode)
    {
        $dlc_map = [
            View::shared('dlc_recoveryofloss') => View::shared('frwd_to_approver'),
            View::shared('dlc_writeoff') => View::shared('frwd_to_approver'),
            View::shared('dlc_pending') => View::shared('frwd_to_approver'),
            View::shared('dlc_appropriateaction') => View::shared('frwd_to_approver'),
            View::shared('dlc_accept') => View::shared('frwd_to_approver'),
            View::shared('dlc_forwardtodhlc') => View::shared('frwd_to_approver'),
            View::shared('dlc_forwardtoshlc') => View::shared('frwd_to_approver'),
            // View::shared('dlc_recoveryofloss')    => View::shared('DLC_to_auditee'),
            // View::shared('dlc_writeoff')          => View::shared('paraaccept'),
            // View::shared('dlc_pending')           => View::shared('pending_hlc'),
            // View::shared('dlc_appropriateaction') => View::shared('DLC_to_auditee'),
            // View::shared('dlc_accept')            => View::shared('paraaccept'),
            // View::shared('dlc_forwardtodhlc')     => View::shared('DLC_to_DEHLC'),
            // View::shared('dlc_forwardtoshlc')     => View::shared('DLC_to_SHLC'),
        ];

        $dept_map = [
            // View::shared('dlc_recoveryofloss')    => View::shared('DLC_to_auditee'),
            // View::shared('dlc_writeoff')          => View::shared('paraaccept'),
            // View::shared('dlc_pending')           => View::shared('pending_hlc'),
            // View::shared('dlc_appropriateaction') => View::shared('DLC_to_auditee'),
            // View::shared('dlc_accept')            => View::shared('paraaccept'),
            // View::shared('dlc_forwardtodhlc')     => View::shared('DLC_to_DEHLC'),
            // View::shared('dlc_forwardtoshlc')     => View::shared('dehlc_to_shlc'),
        ];

        $statel_map = [
            View::shared('dlc_recoveryofloss') => View::shared('slc_to_auditee'),
            View::shared('dlc_writeoff') => View::shared('paraaccept'),
            View::shared('dlc_pending') => View::shared('slc_to_auditee'),
            View::shared('dlc_appropriateaction') => View::shared('slc_to_auditee'),
            View::shared('dlc_accept') => View::shared('paraaccept'),
            // View::shared('dlc_forwardtodhlc')     => View::shared('DLC_to_DEHLC'),
            // View::shared('dlc_forwardtoshlc')     => View::shared('DLC_to_SHLC'),
        ];
        switch ($roleactioncode) {
            case View::shared('dlc_roleactioncode'):
                $map = $dlc_map;
                break;
            case View::shared('dehc_roleactioncode'):
                $map = $dept_map;
                break;
            case View::shared('shlc_roleactioncode'):
                $map = $statel_map;
                break;

            default:
                return '';
        }
        return $map[$actioncode] ?? '';
    }

    public function dept_state_hlc_dropdown($id)
    {
        try {
            $viewName = request()->route('viewName');

            $sessioncharge = session('charge');
            $userData = session('user');

            $apms_hlcid = Crypt::decryptString($id);
            $data = [
                'apms_hlcid' => $apms_hlcid
            ];
            throw_if(empty($apms_hlcid), new \Exception(' Details not found'));

            $apmshlc_data = ApmsModel::fetch_apms_hlcdetails($data, null);
            $committee_level = $apmshlc_data[0]->committee_level;

            foreach ($apmshlc_data as $all) {
                $all->encrypted_hlcid = Crypt::encryptString($all->apms_hlcid);
                unset($all->apms_hlcid);
            }
            $sessionusertypecode = $sessioncharge->usertypecode;
            $sessionroleactioncode = $sessioncharge->roleactioncode;
            $session_userid = $userData->userid;

            $dept = LagacyModel::commondeptfetch();
            $region = ApmsModel::region_valfetch();
            $district = LagacyModel::districtfetch();
            $actions = ApmsModel::fetchactions_basedonroleaction($committee_level);

            $session_userid = Crypt::encryptString($session_userid);

            return view($viewName, compact('dept', 'region', 'district', 'actions', 'apms_hlcid', 'apmshlc_data'));
        } catch (\Exception $e) {
            echo $e->getMessage();
            // echo 'An error occurred while loading. Please try again later.';
        }
    }

    public function hlc_dropdown()
    {
        try {
            $viewName = request()->route('viewName');

            $sessioncharge = session('charge');
            $userData = session('user');

            $sessionusertypecode = $sessioncharge->usertypecode;
            $sessionroleactioncode = $sessioncharge->roleactioncode;
            $session_userid = $userData->userid;

            $dept = LagacyModel::commondeptfetch();
            $region = ApmsModel::region_valfetch();
            $district = LagacyModel::districtfetch();
            $actions = ApmsModel::fetchactions_basedonroleaction($sessionroleactioncode);

            $session_userid = Crypt::encryptString($session_userid);

            return view($viewName, compact('dept', 'region', 'district', 'actions'));
        } catch (\Exception $e) {
            echo $e->getMessage();
            // echo 'An error occurred while loading. Please try again later.';
        }
    }

    public function meetdate_hlc(Request $request)
    {
        $sessioncharge = session('charge');
        $roleactioncode = $sessioncharge->roleactioncode;
        // Validate the input
        $request->validate([
            'region' => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'district' => ['required', 'string', 'regex:/^\d+$/'],
            'catcode' => ['required', 'string', 'regex:/^\d+$/'],
            'subcatcode' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'region.required' => 'The :attribute field is required.',
            'region.regex' => 'The :attribute field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex' => 'The deptcode field must be a valid number.',
            'district.required' => 'The district field is required.',
            'district.regex' => 'The district field must be a valid number.',
            'catcode.required' => 'The Category field is required.',
            'catcode.regex' => 'The Category field must be a valid number.',
            'subcatcode.required' => 'The Subcategory field is required.',
            'subcatcode.regex' => 'The Subcategory field must be a valid number.',
        ]);

        // Get the department code
        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');
        $district = $request->input('district');
        $catcode = $request->input('catcode');
        $subcatcode = $request->input('subcatcode') ?? null;

        throw_if(empty($request->input('catcode')), new \Exception('Category Details not found'));
        throw_if(empty($request->input('district')), new \Exception('District Details not found'));
        throw_if(empty($request->input('deptcode')), new \Exception('Department Details not found'));
        throw_if(empty($request->input('region')), new \Exception('Region Details not found'));

        $data = [
            'deptcode' => $request->input('deptcode'),
            'distcode' => $request->input('district'),
            'regioncode' => $request->input('region'),
            'catcode' => $request->input('catcode'),
            'subcatid' => $request->input('subcatcode'),
            'committee_level' => $request->input('committee_level')
        ];
        $institution = ApmsModel::getmeetdate($data, $roleactioncode);

        if ($institution->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $institution]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Institution found'], 200);
        }
    }

    public function dhlc_dropdown($id = null)
    {
        try {
            $viewName = request()->route('viewName');

            $sessionCharge = session('charge');
            $roleactioncode = $sessionCharge->roleactioncode ?? null;

            $hlcid = null;

            if ($id) {
                try {
                    $hlcid = Crypt::decryptString($id);
                    $params = [
                        'apms_hlcid' => $hlcid ?? null,
                        'processcode' => view::shared('reject_dlcpara')
                    ];

                    $dlcpara = ApmsModel::fetch_dlcparas($params);
                } catch (\Exception $e) {
                    $hlcid = null;
                    $dlcpara = collect();
                }
            } else {
                $dlcpara = collect();
            }

            $dept = LagacyModel::commondeptfetch();
            $region = LagacyModel::regionfetch();
            $district = LagacyModel::districtfetch();
            $actions = ApmsModel::fetchactions_basedonroleaction($roleactioncode);

            return view($viewName, compact(
                'dept',
                'region',
                'district',
                'actions',
                'hlcid',
                'dlcpara'
            ));
        } catch (\Exception $e) {
            return abort(500, 'An error occurred while loading.');
        }
    }

    public function fetch_apms_hlcdetails(Request $request)
    {
        try {
            // return $request->all();
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;
            $roleactioncode = $chargedet->roleactioncode;

            $request->validate([
                'regioncode' => ['nullable', 'string', 'regex:/^\d+$/'],
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
                'distcode' => ['nullable', 'string', 'regex:/^\d+$/'],
                // 'category' => ['required', 'string', 'regex:/^\d+$/'],
                // 'subcategory' => ['nullable', 'string', 'regex:/^\d+$/'],
            ], [
                'regioncode.required' => 'The :attribute field is required.',
                'regioncode.regex' => 'The :attribute field must be a valid number.',
                'deptcode.required' => 'The deptcode field is required.',
                'deptcode.regex' => 'The deptcode field must be a valid number.',
                'distcode.required' => 'The district field is required.',
                'distcode.regex' => 'The district field must be a valid number.',
                // 'category.required' => 'The Category field is required.',
                // 'category.regex'    => 'The Category field must be a valid number.',
                // 'subcategory.required' => 'The Subcategory field is required.',
                // 'subcategory.regex'    => 'The Subcategory field must be a valid number.',
            ]);

            // throw_if(empty($request->input('category')),  new \Exception("Category Details not found"));
            // throw_if(empty($request->input('distcode')),  new \Exception("District Details not found"));
            throw_if(empty($request->input('deptcode')), new \Exception('Department Details not found'));
            // throw_if(empty($request->input('regioncode')),  new \Exception("Region Details not found"));

            $data = [
                'deptcode' => $request->input('deptcode') ?? null,
                'distcode' => $request->input('distcode') ?? null,
                'regioncode' => $request->input('regioncode') ?? null,
                'catcode' => $request->input('catcode') ?? null,
                'subcatid' => $request->input('subcatid') ?? null,
                // 'catcode'    => '13',
                // 'subcatid'   => 5,
            ];

            $paradet = ApmsModel::fetch_apms_hlcdetails($data, $roleactioncode);
            // return $paradet;

            foreach ($paradet as $all) {
                $all->encrypted_hlcid = Crypt::encryptString($all->apms_hlcid);
                unset($all->apms_hlcid);
            }

            return response()->json(['data' => $paradet]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], 500);
        }
    }

    public function apms_getinstbaseonsubcatfollowup(Request $request)
    {
        // Validate the input
        $request->validate([
            'region' => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'district' => ['required', 'string', 'regex:/^\d+$/'],
            'catcode' => ['required', 'string', 'regex:/^\d+$/'],
            'subcatcode' => ['nullable', 'string', 'regex:/^\d+$/'],
        ], [
            'region.required' => 'The :attribute field is required.',
            'region.regex' => 'The :attribute field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex' => 'The deptcode field must be a valid number.',
            'district.required' => 'The district field is required.',
            'district.regex' => 'The district field must be a valid number.',
            'catcode.required' => 'The Category field is required.',
            'catcode.regex' => 'The Category field must be a valid number.',
            'subcatcode.required' => 'The Subcategory field is required.',
            'subcatcode.regex' => 'The Subcategory field must be a valid number.',
        ]);

        // Get the department code
        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');
        $district = $request->input('district');
        $catcode = $request->input('catcode');
        $subcatcode = $request->input('subcatcode') ?? null;

        $institution = LagacyModel::getinstbasedonsubcat($district, $regioncode, $deptcode, $catcode, $subcatcode);

        if ($institution->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $institution]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Institution found'], 200);
        }
    }

    public function fetch_dhlcparadetails(Request $request)
    {
        try {
            // return $request->all();
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;
            $roleactioncode = $chargedet->roleactioncode;

            $apms_hlcid = filled($request->apms_hlcid)
                ? Crypt::decryptString($request->apms_hlcid)
                : null;

            $request->validate([
                'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
                'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
                'distcode' => ['required', 'string', 'regex:/^\d+$/'],
                'catcode' => ['required', 'string', 'regex:/^\d+$/'],
                'subcatid' => ['nullable', 'string', 'regex:/^\d+$/'],
                'instid' => 'required',
            ], [
                'regioncode.required' => 'The :attribute field is required.',
                'regioncode.regex' => 'The :attribute field must be a valid number.',
                'deptcode.required' => 'The deptcode field is required.',
                'deptcode.regex' => 'The deptcode field must be a valid number.',
                'distcode.required' => 'The district field is required.',
                'distcode.regex' => 'The district field must be a valid number.',
                'catcode.required' => 'The Category field is required.',
                'catcode.regex' => 'The Category field must be a valid number.',
                'subcatid.required' => 'The Subcategory field is required.',
                'subcatid.regex' => 'The Subcategory field must be a valid number.',
            ]);

            throw_if(empty($request->input('catcode')), new \Exception('Category Details not found'));
            throw_if(empty($request->input('distcode')), new \Exception('District Details not found'));
            throw_if(empty($request->input('deptcode')), new \Exception('Department Details not found'));
            throw_if(empty($request->input('regioncode')), new \Exception('Region Details not found'));

            $data = [
                'deptcode' => $request->input('deptcode'),
                'distcode' => $request->input('distcode'),
                'regioncode' => $request->input('regioncode'),
                'catcode' => $request->input('catcode'),
                'subcatid' => $request->input('subcatid'),
                'instid' => $request->input('instid'),
            ];
            // return $data;
            $actroleactioncode = self::getAllowedActRoleAction($roleactioncode, 'actroleaction');
            $paradet = ApmsModel::fetch_dhlcparadetails($data, $apms_hlcid, $roleactioncode, $actroleactioncode);
            // return  $paradet;
            // if (
            //     $roleactioncode === View::shared('dehc_roleactioncode') ||
            //     $roleactioncode === View::shared('shlc_roleactioncode')
            // ) {
            $hlcdata = ApmsModel::fetch_apms_hlcdetails($data, $roleactioncode);
            // } else {
            //     $hlcdata = null;
            // }
            // Step 1: collect paraids from hlcdata
            // $hlcParaIds = [];
            $actioncode_hlc = [];
            $hlc_followup = [];

            if (!empty($hlcdata) && isset($hlcdata[0]->followup_action_map)) {
                $decoded = json_decode($hlcdata[0]->paraid, true);
                $decodedactioncodes = json_decode($hlcdata[0]->actioncode, true);
                $decodedfollowup_map_codes = json_decode($hlcdata[0]->followup_action_map, true);

                if (is_array($decoded)) {
                    $hlcParaIds = $decoded;
                }

                if (is_array($decodedfollowup_map_codes)) {
                    $actioncode_hlc = array_column($decodedfollowup_map_codes, 'actioncode');
                    $hlc_followup = array_column($decodedfollowup_map_codes, 'followupid');
                }
            }

            foreach ($paradet as $index => $all) {
                // default
                $all->hlc_para = 'N';

                if (in_array($all->followupid, $hlc_followup)) {
                    $all->hlc_para = 'Y';
                }

                // assign actioncode using loop index
                $all->actioncode = $actioncode_hlc[$index] ?? null;

                // encryption
                $all->encrypted_paraid = Crypt::encryptString($all->paraid);
                $all->encrypted_apms_hlcid = Crypt::encryptString($all->apms_hlcid);
            }

            if (!empty($hlcdata)) {
                foreach ($hlcdata as $hlc) {
                    if (isset($hlc->apms_hlcid)) {
                        $hlc->encrypted_hlcid = Crypt::encryptString($hlc->apms_hlcid);
                        unset($hlc->apms_hlcid);
                    }
                }
            }

            return response()->json(['data' => $paradet, 'hlcdata' => $hlcdata]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], 500);
        }
    }

    public function insert_apms_dlcaction(Request $request)
    {
        DB::beginTransaction();

        try {
            $sesuser = session('user');
            $sescharge = session('charge');

            $userid = $sesuser->userid;
            $userchargeid = $sescharge->userchargeid;
            $roleactioncode = $sescharge->roleactioncode;
            $usertypecode = $sescharge->usertypecode;

            $para_action = $request->input('para_action');

            if (in_array($roleactioncode, View::shared('dlc_roleaction'), true) && $para_action == 'finalise') {
                $request->merge([
                    'statusflag' => 'F',
                    'processcode' => 'FA',
                ]);
            }

            if ($roleactioncode == View::shared('shlc_roleactioncode') && $para_action == 'finalise') {
                $request->merge([
                    'statusflag' => 'F',
                    'processcode' => 'A',
                ]);
            }
            $insert_hlcData = self::insert_apms_hlc($request);
            // return $insert_hlcData;
            $inserted_hlcDet = $insert_hlcData->getData(true);
            $statusflag = $request->statusflag;

            if ($inserted_hlcDet['success'] != true) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => $inserted_hlcDet['message'],
                ]);
            }

            if (empty($inserted_hlcDet['data'])) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save minutes of meeting data',
                ]);
            }

            $apms_hlcid = $inserted_hlcDet['data'];

            $request->validate([
                'rows' => 'required|json',
                'rows.*.paraid' => 'required|string',
                'rows.*.actioncode' => 'required|string',
                'rows.*.instid' => 'required|integer',
                'rows.*.action_type' => 'required|in:draft,finalise',
            ]);

            $rows = json_decode($request->rows, true);
            throw_if(!is_array($rows), new \Exception('Invalid rows data'));

            $followup_actions[] = $rows[0]['followup_action'];

            foreach ($rows as $row) {
                // $paraid     = Crypt::decryptString($row['paraid']);
                // $actioncode = $row['actioncode'];
                $actiontype = $row['action_type'];
                $instid = $row['instid'];
                $followup_actions = $row['followup_action'];
                $followupid = $followup_actions['followupid'];
                $actioncode = $followup_actions['actioncode'];

                if ($statusflag == 'F') {
                    $upadate_followup = ApmsModel::upadate_followup($apms_hlcid, $followupid);
                }

                throw_if(empty($followupid), new \Exception('Para Details not found'));
            }
            // $processcode = $statusflag  == 'Y'
            //     ? View::shared('dlc_savedraft_processcode')
            //     : self::getprocescode_basedonaction($actioncode, $roleactioncode);

            if ($statusflag == 'Y') {
                $processcode = View::shared('dlc_savedraft_processcode');
            } else if ($statusflag == 'F') {
                if (in_array($roleactioncode, View::shared('dlc_roleaction'), true)) {
                    $processcode = View::shared('frwd_to_approver');
                } else {
                    $processcode = self::getprocescode_basedonaction($actioncode, $roleactioncode);
                }
            }

            // throw_if(empty($processcode), new \Exception("Process was not found"));
            $actroleactioncode = self::getAllowedActRoleAction($roleactioncode, 'actroleaction');

            if (in_array($roleactioncode, [
                View::shared('dlc_roleaction'),
            ])) {
                $toroleactioncode = View::shared('RJD_roleactioncode');
            } else {
                $toroleactioncode = $roleactioncode;
            }

            if ($statusflag == 'F') {
                $hlcdet = ApmsModel::gethlcvalues($apms_hlcid);

                $inst_data = [
                    'deptcode' => $request->deptcode,
                    'distcode' => $request->distcode,
                    'regioncode' => $request->regioncode,
                    'catcode' => $request->catcode,
                    'subcatid' => $request->subcatid,
                ];

                if (in_array($roleactioncode, View::shared('dlc_roleaction'), true)) {
                    $getforwarddetails = ApmsModel::getforwarddetails($instid, $toroleactioncode, $inst_data);
                    throw_if(empty($getforwarddetails), new \Exception('Forward details not found'));

                    $forwardedtousertypecode = 'AP';
                    $forwardedbyusertypecode = 'D';

                    throw_if(empty($getforwarddetails), new \Exception('Forward details not found'));
                    // return $getforwarddetails;
                    $forwardedtouserid = $getforwarddetails->deptuserid;
                    $forwardedtouserchargeid = $getforwarddetails->chargeid;
                } else {
                    $forwardedtouserid = null;
                    $forwardedtouserchargeid = null;
                    $forwardedtousertypecode = '';
                    $forwardedbyusertypecode = $usertypecode;
                }

                if (($processcode == View::shared('frwd_to_approver') || ($processcode == View::shared('approved_dlcpara')) && $statusflag == 'F')) {
                    // $insert_apmshlc_historydata

                    $insert_apmshlc_historydata = [
                        'apms_hlcid' => $apms_hlcid,
                        'processcode' => $processcode,
                        'followup_action_map' => $hlcdet[0]->followup_action_map,
                        'approved_para' => $hlcdet[0]->approved_para,
                        'rejected_para' => $hlcdet[0]->rejected_para,
                        'approver_remarks' => $hlcdet[0]->approver_remarks,
                        'forwardedbyusertypecode' => $forwardedbyusertypecode,
                        'forwardedtousertypecode' => $forwardedtousertypecode,
                        'statusflag' => 'Y',
                        'transstatus' => 'A',
                        'forwardedtouserid' => $forwardedtouserid,
                        'forwardedtouserchargeid' => $forwardedtouserchargeid,
                        'forwardedbyuserid' => $userid,
                        'forwardedbyuserchargeid' => $userchargeid,
                        'createdby' => $userid,
                        'createdon' => View::shared('get_nowtime'),
                    ];

                    ApmsModel::insert_apmshlc_historydata($insert_apmshlc_historydata, $apms_hlcid);
                }
            }
            // --forloop  }

            if ($roleactioncode == View::shared('shlc_roleactioncode') && $para_action == 'finalise') {
                $process_apms = self::processpara_apms($rows, $roleactioncode, $apms_hlcid);
            }
            DB::commit();

            $success_message = $para_action == 'finalise'
                ? 'Para detail(s) has been finalized successfully'
                : 'Para detail(s) has been saved/updated ';

            return response()->json([
                'success' => true,
                'message' => $success_message,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function processpara_apms($paradata, $committee_level, $apms_hlcid)
    {
        $session = session('user');
        $sessioncharge = session('charge');
        $userid = $session->userid;
        $userchargeid = $sessioncharge->userchargeid;
        $usertypecode = $sessioncharge->usertypecode;

        $committeelevel = $committee_level;
        $followup_action_map = $paradata;

        foreach ($followup_action_map as $item) {
            $followup_actions = $item['followup_action'];

            $followupid = $followup_actions['followupid'];
            $actioncode = $followup_actions['actioncode'];

            $followupudet = ApmsModel::getfollowupvalues($followupid);
            if (!$followupudet)
                continue;

            $processData = $this->getpara_processdatas(
                $actioncode,
                $committeelevel,
                $followupudet[0]->instid
            );

            $para_processcode = $processData['processcode'];
            $para_actroleactioncode = $processData['actroleactioncode'];
            $para_forwardtouserid = $processData['forwardedtouserid'];
            $para_forwardtouserchargeid = $processData['forwardedtouserchargeid'];

            /* UPDATE / INSERT trans_para */

            $trans_para = [
                'followupid' => $followupudet[0]->followupid,
                'instid' => $followupudet[0]->instid,
                'audityear' => $followupudet[0]->audityear,
                'paranumber' => $followupudet[0]->parano,
                'statusflag' => 'Y',
                'actioncode' => $actioncode,
            ];

            if (!empty($followupudet[0]->paraid)) {
                // UPDATE
                DB::table('audit.trans_para')
                    ->where('paraid', $followupudet[0]->paraid)
                    ->update([
                        'updatedby' => $userid,
                        'updatedon' => view::shared('get_nowtime'),
                        'usertypecode' => $usertypecode,
                        'actroleactioncode' => $para_actroleactioncode,
                        'apms_hlcid' => $apms_hlcid,
                        'processcode' => $para_processcode,
                        'forwardedtouserid' => $para_forwardtouserid,
                        'forwardedtouserchargeid' => $para_forwardtouserchargeid,
                    ]);

                $paraid = $followupudet[0]->paraid;
            } else {
                $trans_para['createdby'] = $userid;
                $trans_para['createdon'] = view::shared('get_nowtime');
                $trans_para['updatedby'] = $userid;
                $trans_para['updatedon'] = view::shared('get_nowtime');

                $trans_para['usertypecode'] = $usertypecode;
                $trans_para['actroleactioncode'] = $para_actroleactioncode;

                $trans_para['paratype'] = $followupudet[0]->paratype;
                $trans_para['apms_hlcid'] = $apms_hlcid;

                $trans_para['processcode'] = $para_processcode;

                $trans_para['forwardedtouserid'] = $para_forwardtouserid;
                $trans_para['forwardedtouserchargeid'] = $para_forwardtouserchargeid;

                // INSERT
                $paraid = DB::table('audit.trans_para')
                    ->insertGetId($trans_para, 'paraid');
            }

            /* INSERT HISTORY */
            $historydata = [
                'paraid' => $paraid,
                'paratype' => $followupudet[0]->paratype,
                'instid' => $followupudet[0]->instid,
                'audityear' => $followupudet[0]->audityear,
                'paranumber' => $followupudet[0]->paranumber,
                'statusflag' => 'Y',
                'followupid' => $followupid,
                'usertypecode' => $usertypecode,
                'processcode' => $para_processcode,
                'forwardedtouserid' => $para_forwardtouserid,
                'forwardedtochargeid' => $para_forwardtouserchargeid,
                // 'transactionno' => $paradet->transactionno,
                'forwardedon' => View::shared('get_nowtime'),
                'createdon' => View::shared('get_nowtime'),
                'createdby' => $userid,
                'forwardedbyuserid' => $userid,
                'forwardedbychargeid' => $userchargeid,
                'transstatus' => 'A',
                'actroleactioncode' => $para_actroleactioncode,
                'actioncode' => $actioncode,
                'rejoindercycle' => $followupudet[0]->rejoindercycle ?? null,
                'rejoinderstatus' => $followupudet[0]->rejoinderstatus ?? null,
                'rejectcount' => $followupudet[0]->rejectcount ?? null
            ];
            ApmsModel::insert_apms_historydata(
                $historydata,
                $paraid
            );
        }

        return true;
    }

    public function insert_apms_hlc(Request $request)
    {
        DB::beginTransaction();

        try {
            /* ---------------- USER DETAILS ---------------- */
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;
            $roleactioncode = $chargedet->roleactioncode;

            /* ---------------- VALIDATION ---------------- */
            $request->validate([
                'regioncode' => 'required',
                'deptcode' => 'required',
                'distcode' => 'required',
                'catcode' => 'required',
                'subcatid' => 'nullable',
                'mom_date' => 'required|date_format:d/m/Y',
                'statusflag' => 'required|in:Y,F',
                'instid' => 'required|array|min:1',
                'rows' => 'required',
                'selected_paras' => 'nullable|json'
            ]);
            $apms_hlcid = filled($request->apms_hlcid)
                ? Crypt::decryptString($request->apms_hlcid)
                : null;

            /* ---------------- DATE FORMAT ---------------- */
            $mom_date = Carbon::createFromFormat('d/m/Y', $request->mom_date)
                ->format('Y-m-d');

            /* ---------------- DECODE ROWS ---------------- */
            $rows = json_decode($request->input('rows'), true);

            throw_if(empty($rows), new \Exception('Para details not found'));

            /* ---------------- FILE UPLOAD ---------------- */
            $fileuploadId = null;
            $fileuploadId = $request->input('uploadid');
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                throw_if(
                    $file->getClientOriginalExtension() !== 'pdf',
                    new \Exception('Only PDF files are allowed')
                );

                $mom_file = str_replace('-', '_', $mom_date);

                $destinationPath = 'uploads/apms/';
                $destinationarray = [
                    $request->deptcode,
                    $request->regioncode,
                    $request->distcode,
                    $request->catcode,
                    $request->subcatid,
                    $mom_file,
                    View::shared('dlc_filefolder'),
                ];

                $uploadResult = $this->fileUploadService->uploadFile(
                    $file,
                    $destinationPath,
                    $request->uploadid ?? '',
                    $destinationarray
                );

                $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;

                throw_if(!$fileuploadId, new \Exception('File upload failed'));
            }

            /* ---------------- INSERT DATA ---------------- */
            $instid = json_encode($request->instid);  // array → JSON

            // $paraids     = [];
            $selectedparas = [];
            $actioncodes = [];
            $followup_actions = [];

            foreach ($rows as $row) {
                // $paraids[]     = Crypt::decryptString($row['paraid']);
                $actioncodes[] = $row['actioncode'];
                $followup_actions[] = $row['followup_action'];
                $selectedparas[] = $row['selected_paras'];
            }

            // $paraidJson     = json_encode($paraids);
            $actioncodeJson = json_encode($actioncodes);
            $selectedparasJson = json_encode($selectedparas);

            $followup_actionsJson = json_encode($followup_actions);
            $data = [
                'deptcode' => $request->deptcode,
                'distcode' => $request->distcode,
                'regioncode' => $request->regioncode,
                'catcode' => $request->catcode,
                'subcatid' => $request->subcatid,
                'mom_date' => $mom_date,
                'instid' => $instid,
                // 'actiocode'             => $actioncodeJson,
                // 'paraid'                => $paraidJson,
                'selected_paras' => $selectedparasJson,
                'statusflag' => $request->statusflag,
                'fileuploadid' => $fileuploadId,
                'committee_level' => $roleactioncode,
                'updatedby' => $userid,
                'updatedbyuserchargeid' => $userchargeid,
                'updatedon' => View::shared('get_nowtime'),
                'followup_action_map' => $followup_actionsJson
            ];

            if (!empty($apms_hlcid)) {
                $hlcvalues = ApmsModel::gethlcvalues($apms_hlcid);

                $rejected_paras = $hlcvalues[0]->rejected_para ?? [];
                if (is_string($rejected_paras)) {
                    $rejected_paras = json_decode($rejected_paras, true);
                }
                if (!empty($rejected_paras)) {
                    $rejected_paras = array_values(
                        array_diff($rejected_paras, $selectedparas)
                    );
                }

                $data['rejected_para'] = $rejected_paras;
            }

            if ($request->has('processcode')) {
                $data['processcode'] = $request->processcode;
            }

            if (empty($apms_hlcid)) {
                $data['createdbyuserchargeid'] = $userchargeid;
                $data['createdby'] = $userid;
                $data['createdon'] = View::shared('get_nowtime');
            }

            $inserted_data = ApmsModel::insert_apms_hlc($data, $apms_hlcid, $roleactioncode);

            if ($inserted_data) {
                DB::commit();

                return response()->json([
                    'success' => true,
                    'data' => $inserted_data->apms_hlcid
                ]);
            }
        } catch (DecryptException $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function insert_approveraction(Request $request)
    {
        // DB::beginTransaction();

        try {
            /* ---------------- USER DETAILS ---------------- */
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;
            $roleactioncode = $chargedet->roleactioncode;
            $usertypecode = $chargedet->usertypecode;

            /* ---------------- VALIDATION ---------------- */

            $apms_hlcid = filled($request->apms_hlcid)
                ? Crypt::decryptString($request->apms_hlcid)
                : null;

            $request->merge([
                'apms_hlcid' => $apms_hlcid
            ]);

            $request->validate([
                'apms_hlcid' => 'required|integer',
                'statusflag' => 'required|in:Y,F',
                'rows' => 'required',
                'approved_para' => ['required', 'regex:/^\d+(,\d+)*$/'],
                'rejected_para' => ['nullable', 'regex:/^\d+(,\d+)*$/'],
            ]);

            /* ---------------- PARA DECODE ---------------- */

            $approved_para = collect(explode(',', $request->approved_para))
                ->map(fn($v) => (int) $v)
                ->values()
                ->toJson();

            $rejected_para = $request->rejected_para
                ? collect(explode(',', $request->rejected_para))
                    ->map(fn($v) => (int) $v)
                    ->values()
                    ->toJson()
                : json_encode([]);

            $statusflag = $request->statusflag;

            /* ---------------- ROWS ---------------- */

            $rows = json_decode($request->input('rows'), true);
            throw_if(empty($rows), new \Exception('Para details not found'));

            $actioncodes = [];
            $followup_actions = [];
            $approver_remarks = [];

            foreach ($rows as $row) {
                $actioncodes[] = $row['actioncode'];
                $followup_actions[] = $row['followup_action'];
                $approver_remarks[] = $row['approver_remarks'];
            }

            $followup_actionsJson = json_encode($followup_actions);

            $hlcdet = ApmsModel::gethlcvalues($apms_hlcid);

            $data = [
                'deptcode' => $hlcdet[0]->deptcode,
                'distcode' => $hlcdet[0]->distcode,
                'regioncode' => $hlcdet[0]->regioncode,
                'catcode' => $hlcdet[0]->catcode,
                'subcatid' => $hlcdet[0]->subcatid,
                'committee_level' => $hlcdet[0]->committee_level,
                'mom_date' => $hlcdet[0]->mom_date,
                'approved_para' => $approved_para,
                'rejected_para' => $rejected_para,
                'updatedby' => $userid,
                'updatedbyuserchargeid' => $userchargeid,
                'updatedon' => View::shared('get_nowtime'),
                'followup_action_map' => $followup_actionsJson,
                'approver_remarks' => $approver_remarks
            ];

            if ($statusflag == 'F') {
                if ($rejected_para != '[]') {
                    $processcode = view::shared('reject_dlcpara');
                } else {
                    $processcode = view::shared('approved_dlcpara');
                }

                $data['processcode'] = $processcode;
            } else {
                $data['processcode'] = $request->processcode;
            }

            /* ---------------- INSERT HLC ---------------- */

            $inserted_data = ApmsModel::insert_apms_hlc($data, $apms_hlcid, $roleactioncode);

            if (!$inserted_data) {
                throw new \Exception('HLC update failed');
            }

            /* ---------------- PARA LOOP ---------------- */

            if ($statusflag == 'F') {
                $committeelevel = $hlcdet[0]->committee_level;
                $followup_action_map = json_decode($followup_actionsJson, true);

                foreach ($followup_action_map as $item) {
                    $followupid = $item['followupid'];
                    $actioncode = $item['actioncode'];

                    $followupudet = ApmsModel::getfollowupvalues($followupid);
                    if (!$followupudet)
                        continue;

                    $processData = $this->getpara_processdatas(
                        $actioncode,
                        $committeelevel,
                        $followupudet[0]->instid
                    );

                    $para_processcode = $processData['processcode'];
                    $para_actroleactioncode = $processData['actroleactioncode'];
                    $para_forwardtouserid = $processData['forwardedtouserid'];
                    $para_forwardtouserchargeid = $processData['forwardedtouserchargeid'];

                    /* UPDATE / INSERT trans_para */

                    $trans_para = [
                        'followupid' => $followupudet[0]->followupid,
                        'instid' => $followupudet[0]->instid,
                        'audityear' => $followupudet[0]->audityear,
                        'paranumber' => $followupudet[0]->parano,
                        'statusflag' => 'Y',
                        'actioncode' => $actioncode,
                    ];

                    if ($rejected_para == '[]') {
                        if (!empty($followupudet[0]->paraid)) {
                            // UPDATE
                            DB::table('audit.trans_para')
                                ->where('paraid', $followupudet[0]->paraid)
                                ->update([
                                    'updatedby' => $userid,
                                    'updatedon' => view::shared('get_nowtime'),
                                    'usertypecode' => $usertypecode,
                                    'actroleactioncode' => $para_actroleactioncode,
                                    'apms_hlcid' => $apms_hlcid,
                                    'processcode' => $para_processcode,
                                    'forwardedtouserid' => $para_forwardtouserid,
                                    'forwardedtouserchargeid' => $para_forwardtouserchargeid,
                                ]);

                            $paraid = $followupudet[0]->paraid;
                        } else {
                            $trans_para['createdby'] = $userid;
                            $trans_para['createdon'] = view::shared('get_nowtime');
                            $trans_para['updatedby'] = $userid;
                            $trans_para['updatedon'] = view::shared('get_nowtime');
                            $trans_para['usertypecode'] = $usertypecode;
                            $trans_para['actroleactioncode'] = $para_actroleactioncode;
                            $trans_para['paratype'] = $followupudet[0]->paratype;
                            $trans_para['apms_hlcid'] = $apms_hlcid;
                            $trans_para['processcode'] = $para_processcode;
                            $trans_para['forwardedtouserid'] = $para_forwardtouserid;
                            $trans_para['forwardedtouserchargeid'] = $para_forwardtouserchargeid;
                            $trans_para['auditee_liability'] = 'N';
                            $trans_para['liabilty_type'] = 'N';

                            // INSERT
                            $paraid = DB::table('audit.trans_para')
                                ->insertGetId($trans_para, 'paraid');
                        }

                        /* INSERT HISTORY */
                        $historydata = [
                            'paraid' => $paraid,
                            'paratype' => $followupudet[0]->paratype,
                            'instid' => $followupudet[0]->instid,
                            'audityear' => $followupudet[0]->audityear,
                            'paranumber' => $followupudet[0]->paranumber,
                            'statusflag' => 'Y',
                            'followupid' => $followupid,
                            'usertypecode' => $usertypecode,
                            'processcode' => $para_processcode,
                            'forwardedtouserid' => $para_forwardtouserid,
                            'forwardedtochargeid' => $para_forwardtouserchargeid,
                            // 'transactionno' => $paradet->transactionno,
                            'forwardedon' => View::shared('get_nowtime'),
                            'createdon' => View::shared('get_nowtime'),
                            'createdby' => $userid,
                            'forwardedbyuserid' => $userid,
                            'forwardedbychargeid' => $userchargeid,
                            'transstatus' => 'A',
                            'actroleactioncode' => $para_actroleactioncode,
                            'actioncode' => $actioncode,
                            'rejoindercycle' => $followupudet[0]->rejoindercycle ?? null,
                            'rejoinderstatus' => $followupudet[0]->rejoinderstatus ?? null,
                            'rejectcount' => $followupudet[0]->rejectcount ?? null
                        ];
                        ApmsModel::insert_apms_historydata(
                            $historydata,
                            $paraid
                        );
                    }
                }

                if ($processcode == view::shared('reject_dlcpara')) {
                    $forwardedtouserid = $hlcdet[0]->createdby;
                    $forwardedtouserchargeid = $hlcdet[0]->createdbyuserchargeid;
                    $forwardedtousertypecode = $committeelevel == view::shared('dlc_roleactioncode') ? view::shared('dlc_usertypecode') : view::shared('department_dlc_usertypecode');
                } else {
                    $forwardedtouserid = null;
                    $forwardedtousertypecode = null;
                    $forwardedtouserchargeid = null;
                }
                if ($statusflag == 'F') {
                    $insert_apmshlc_historydata = [
                        'apms_hlcid' => $apms_hlcid,
                        'processcode' => $processcode,
                        'followup_action_map' => $hlcdet[0]->followup_action_map,
                        'approved_para' => $hlcdet[0]->approved_para,
                        'rejected_para' => $hlcdet[0]->rejected_para,
                        'approver_remarks' => $hlcdet[0]->approver_remarks,
                        'forwardedbyusertypecode' => view::shared('aprrover_usertypecode'),
                        'forwardedtousertypecode' => $forwardedtousertypecode,
                        'statusflag' => 'Y',
                        'transstatus' => 'A',
                        'forwardedtouserid' => $hlcdet[0]->createdby,
                        'forwardedtouserchargeid' => $hlcdet[0]->createdbyuserchargeid,
                        'forwardedbyuserid' => $userid,
                        'forwardedbyuserchargeid' => $userchargeid,
                        'createdby' => $userid,
                        'createdon' => View::shared('get_nowtime'),
                    ];
                    ApmsModel::insert_apmshlc_historydata($insert_apmshlc_historydata, $apms_hlcid);
                }
                $success_message = $statusflag == 'F' ? $processcode == view::shared('approved_dlcpara') ? 'Para detail(s) has been approved' : 'Para detail(s) has been forwared to committee' : 'Para detail(s) has been saved/updated in a draft';
                return response()->json([
                    'success' => true,
                    'message' => $success_message,
                ]);
            }

            /* ---------------- FINAL COMMIT ---------------- */

            // DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Para details processed successfully'
            ]);
        } catch (DecryptException $e) {
            // DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        } catch (\Exception $e) {
            // DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function apms_getdeptbaseddetails(Request $request)
    {
        $validated = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex' => 'The deptcode field must be a valid number.',
        ]);

        try {
            $deptcode = $validated['deptcode'];

            $data = ApmsModel::fetchDeptBasedMasterData($deptcode);

            return response()->json([
                'success' => true,
                'regiondata' => $data['regions'],
                'catdata' => $data['categories'],
                'message' => 'Data fetched successfully'
            ]);
        } catch (\Throwable $e) {
            Log::error('APMS dept based fetch failed', [
                'deptcode' => $request->deptcode,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch department details. Please try again later.'
            ], 500);
        }
    }

    public function fetch_parastatus_hlc(Request $request)
    {
        try {
            // return $request->all();
            $userdet = session('user');
            $chargedet = session('charge');

            $userid = $userdet->userid;
            $userchargeid = $chargedet->userchargeid;
            $roleactioncode = $chargedet->roleactioncode;

            $apms_hlcid = filled($request->apms_hlcid)
                ? Crypt::decryptString($request->apms_hlcid)
                : null;

            $paradet = ApmsModel::fetch_parastatus_hlc($apms_hlcid);

            if ($paradet->isNotEmpty()) {
                $actioncode_hlc = [];
                $decodedfollowup_map_codes = json_decode($paradet[0]->followup_action_map, true);
                $actioncode_array = array_column($decodedfollowup_map_codes, 'actioncode');
                foreach ($paradet as $index => $all) {
                    $all->actioncode = $actioncode_array[$index] ?? null;
                    $all->encrypted_paraid = Crypt::encryptString($all->paraid);
                    // $all->encrypted_followupid = Crypt::encryptString($all->followupid);
                    $all->encrypted_apms_hlcid = Crypt::encryptString($all->apms_hlcid);
                    unset($all->paraid);
                }
            }

            return response()->json(['data' => $paradet]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], 500);
        }
    }

    public function fetch_init_dlcparas(Request $request)
    {
        try {
            $session = session('charge');
            $region = $session->regioncode;
            $dept = $session->deptcode;

            $dlcpara = $session = session('charge');

            $params = [
                'region' => $session->regioncode ?? null,
                'dept' => $session->deptcode ?? null,
                'district' => $session->distcode ?? null,
                'catcode' => $session->catcode ?? null,
                'subcatid' => $session->subcatid ?? null,
                'processcode' => view::shared('frwd_to_approver')
            ];

            $dlcactioncodes = view::shared('dlc_roleaction');  // array

            if (in_array($session->roleactioncode, $dlcactioncodes)) {
                $params['roleactioncode'] = $session->roleactioncode;
            }

            $dlcpara = ApmsModel::fetch_dlcparas($params);

            foreach ($dlcpara as $all) {
                $all->encrypted_apms_hlcid = Crypt::encryptString($all->apms_hlcid);
                unset($all->apms_hlcid);
            }

            return response()->json(['data' => $dlcpara]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], 500);
        }
    }

    public function fetch_returned_dlcparas(Request $request)
    {
        try {
            $session = session('charge');

            $params = [
                'region' => $session->regioncode ?? null,
                'dept' => $session->deptcode ?? null,
                'district' => $session->distcode ?? null,
                'catcode' => $session->catcode ?? null,
                'subcatid' => $session->subcatid ?? null,
                'processcode' => view::shared('reject_dlcpara')
            ];

            $dlcactioncodes = view::shared('dlc_roleaction');  // array

            if (in_array($session->roleactioncode, $dlcactioncodes)) {
                $params['roleactioncode'] = $session->roleactioncode;
            }

            $dlcpara = ApmsModel::fetch_dlcparas(
                $params
            );

            foreach ($dlcpara as $all) {
                $all->encrypted_apms_hlcid = Crypt::encryptString($all->apms_hlcid);
                unset($all->apms_hlcid);
            }

            return response()->json(['data' => $dlcpara]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
                // 'message' => 'Something went wrong fetching the para details'
            ], 500);
        }
    }

    public function getpara_processdatas($actioncode, $committee_level, $instid)
    {
        $processcode = null;
        $actroleactioncode = null;
        $forwardedtouserid = null;
        $forwardedtouserchargeid = null;

        /*
         * -------------------------------------------------
         * 🔹 CENTRALIZED SHARED VALUES
         * -------------------------------------------------
         */
        $shared = [
            // Roles
            'roles' => [
                'dlc' => View::shared('dlc_roleactioncode'),
                'delc' => View::shared('dehc_roleactioncode'),
                'slc' => View::shared('shlc_roleactioncode'),
            ],
            // Act Role Codes
            'act_roles' => [
                'dlc' => View::shared('dlc_actroleactioncode'),
                'delc' => View::shared('delc_actroleactioncode'),
                'slc' => View::shared('shlc_actroleactioncode'),
            ],
            // Actions
            'actions' => [
                'writeoff' => View::shared('dlc_writeoff'),
                'accept' => View::shared('dlc_accept'),
                'recovery' => View::shared('dlc_recoveryofloss'),
                'appropriate' => View::shared('dlc_appropriateaction'),
                'forward_shlc' => View::shared('dlc_forwardtoshlc'),
            ],
            // Process Codes
            'process' => [
                'accept' => View::shared('paraaccept'),
                'dlc_to_auditee' => View::shared('DLC_to_auditee'),
                'delc_to_auditee' => View::shared('dehlc_to_auditee'),
                'slc_to_auditee' => View::shared('slc_to_auditee'),
                'dlc_to_shlc' => View::shared('DLC_to_SHLC'),
                'delc_to_shlc' => View::shared('dehlc_to_shlc'),
            ]
        ];

        $roles = $shared['roles'];
        $actions = $shared['actions'];
        $process = $shared['process'];

        $instdet = ApmsModel::getInstdet($instid);

        if (!$instdet) {
            throw new \Exception('Institution details not found');
        }

        /*
         * -------------------------------------------------
         * 1️⃣ WRITE OFF / ACCEPT
         * -------------------------------------------------
         */
        if (in_array($actioncode, [$actions['writeoff'], $actions['accept']])) {
            $processcode = $process['accept'];
        }
        /*
         * -------------------------------------------------
         * 2️⃣ RECOVERY / APPROPRIATE ACTION
         * -------------------------------------------------
         */ elseif (in_array($actioncode, [$actions['recovery'], $actions['appropriate']])) {
            $processMap = [
                $roles['dlc'] => $process['dlc_to_auditee'],
                $roles['delc'] => $process['delc_to_auditee'],
                $roles['slc'] => $process['slc_to_auditee'],
            ];

            $processcode = $processMap[$committee_level] ?? null;

            $forwardedtouserid = $instdet[0]->auditeeuserid;
            $forwardedtouserchargeid = 5;

            throw_if(empty($forwardedtouserid), new \Exception('Forward details not found'));
        }
        /*
         * -------------------------------------------------
         * 3️⃣ FORWARD TO SHLC
         * -------------------------------------------------
         */ elseif ($actioncode == $actions['forward_shlc']) {
            $processMap = [
                $roles['dlc'] => $process['dlc_to_shlc'],
                $roles['delc'] => $process['delc_to_shlc'],
            ];

            $processcode = $processMap[$committee_level] ?? null;

            $forwarddetails = ApmsModel::getforwarddetails(
                $instid,
                $roles['slc'],
                [
                    'deptcode' => $instdet[0]->deptcode,
                    'distcode' => $instdet[0]->distcode,
                    'regioncode' => $instdet[0]->regioncode,
                    'catcode' => $instdet[0]->catcode,
                    'subcatid' => $instdet[0]->subcatid,
                ]
            );

            throw_if(empty($forwarddetails), new \Exception('Forward details not found'));

            $forwardedtouserid = $forwarddetails->deptuserid;
            $forwardedtouserchargeid = $forwarddetails->chargeid;
        }

        /*
         * -------------------------------------------------
         * 4️⃣ ACT ROLE ACTION CODE (CLEAN MAP)
         * -------------------------------------------------
         */
        $actroleMap = [
            $roles['dlc'] => $shared['act_roles']['dlc'],
            $roles['delc'] => $shared['act_roles']['delc'],
            $roles['slc'] => $shared['act_roles']['slc'],
        ];

        $actroleactioncode = $actroleMap[$committee_level] ?? null;

        return [
            'processcode' => $processcode,
            'actroleactioncode' => $actroleactioncode,
            'forwardedtouserid' => $forwardedtouserid,
            'forwardedtouserchargeid' => $forwardedtouserchargeid
        ];
    }

    // -----------------------------Helper Function------------------------------------//
    private function getAllowedActRoleAction($roleactioncode, $action)
    {
        static $roleMap = null;

        if ($roleMap === null) {
            $roleMap = [
                View::shared('dlc_roleactioncode') => View::shared('dlc_actroleactioncode'),
                View::shared('dehc_roleactioncode') => View::shared('delc_actroleactioncode'),
                View::shared('shlc_roleactioncode') => View::shared('shlc_actroleactioncode'),
            ];

            $toroleaction =
                [
                    View::shared('dlc_roleactioncode') => View::shared('RJD_roleactioncode'),
                    View::shared('dehc_roleactioncode') => View::shared('RJD_roleactioncode'),
                ];
        }

        switch ($action) {
            case 'actroleaction':
                $map = $roleMap;
                break;
            case 'toroleaction':
                $map = $toroleaction;
                break;

            default:
                return '';
        }
        $processcode = $map[$roleactioncode];

        if (empty($processcode)) {
            throw new \Exception('Process code not found for this state of para');
        }
        return $processcode;
    }
}
