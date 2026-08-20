<?php

namespace App\Http\Controllers;

// require_once app_path('../vendor/setasign/fpdf/fpdf.php');
// require_once app_path('../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/IOFactory.php');
// require_once app_path('../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Shared/File.php');
// require_once app_path('../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Reader/Xlsx.php');
// require_once app_path('../vendor/phpoffice/phpspreadsheet/src/PhpSpreadsheet/Reader/BaseReader.php');

// require_once __DIR__.'/../vendor/autoload.php';

ini_set('pcre.backtrack_limit', '100000000');  // Increase further if needed
ini_set('memory_limit', '32000M');  // Give more memory to PHP
use Exception;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

use Mpdf\Mpdf;
use Imagick;
// use App\Models\AuditDiaryModel;
// use App\Models\InstAuditscheduleModel;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpSpreadsheet\Writer\Html;  // optional, if rendering HTML
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\PhpWord;
use setasign\Fpdi\Fpdi;
use DOMDocument;
// use App\Http\Controllers\AuditSlipController;
// use App\Models\TransWorkAllocationModel;
use App\Models\AuditManagementModel;
use App\Models\FormatModel;
use App\Models\SmsmailModel;
use App\Services\FileUploadService;
use App\Services\PHPMailerService;
use App\Services\SmsService;
use Illuminate\Support\Facades\DB;
use App\Models\ReportModel;



class FormatController extends Controller
{
    // Define variables at the class level
    private $tamilfontfile = 'NotoSansTamil-Regular.ttf';
    private $tamilfontname = 'noto';

    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function DownloadReport(Request $request)
    {

        try {
            $auditscheduleid = Crypt::decryptString($request->audit_scheduleid);

            $instid = $request->instid;
            $financialyearcode = $request->financialyearcode;
            $spilloverflag = $request->spilloverflag;


            $lang = $request->input('lang');

            $action = $request->input('action', 'download');
            $download = $this->DownloadAuditReport($auditscheduleid, $lang, $spilloverflag, $instid, $financialyearcode, $action);
            $fileName = is_array($download) ? $download['fileName'] : $download;
            $filePath = is_array($download) ? $download['filePath'] : public_path('files/' . $fileName);
            $deleteAfterSend = is_array($download) ? ($download['deleteAfterSend'] ?? true) : true;
            $contentType = is_array($download) ? ($download['contentType'] ?? 'application/pdf') : 'application/pdf';



            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Stream the file to the user WITHOUT deleting it
            return response()->streamDownload(function () use ($filePath, $deleteAfterSend) {
                readfile($filePath);
                if ($deleteAfterSend && file_exists($filePath)) {
                    unlink($filePath);
                }
            }, $fileName, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            ]);
        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
            return response()->json(['error' => 'Invalid audit schedule ID'], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteAnnexureFile(Request $request)
    {
        $auditscheduleid = $request->input('auditscheduleid');
        $annexturetype = $request->input('key');

        $deleteannexture = FormatModel::DeleteAnnexture($auditscheduleid, $annexturetype);

        if ($deleteannexture) {
            return response()->json([
                'success' => true,
                'data' => 'annexture_deleted'
            ]);
        }
    }

    public function getUploadedAnnexures($auditscheduleid)
    {
        // Example structure
        $files = DB::table('audit.report_annextures as ann')
            ->join('audit.fileuploaddetail as fup', 'fup.fileuploadid', '=', 'ann.fileupload_id')
            ->select('fup.filepath', 'fup.filename', 'ann.statusflag', 'ann.annexture_type')
            ->where('ann.auditscheduleid', $auditscheduleid)
            ->where('ann.statusflag', '!=', 'N')
            ->orderby('ann.annexture_id', 'asc')
            ->get();
        $response = [];
        foreach ($files as $file) {
            $response[$file->annexture_type] = [
                'filename' => $file->filename,
                'filepath' => $file->filepath,
                'statusflag' => $file->statusflag,
            ];
        }
        return response()->json($response);
    }

    public function getAnnexureData(Request $request)
    {
        $auditscheduleid = $request->input('auditscheduleid');

        // Step 1: Get serious slip count once (only needed for non-serious slips)
        $seriousCount = 0;
        $serSlipRow = DB::table('audit.report_storesliporder')
            ->where('auditscheduleid', $auditscheduleid)
            ->select('ser_ordered_slips')
            ->first();

        if ($serSlipRow && $serSlipRow->ser_ordered_slips) {
            $seriousArray = json_decode($serSlipRow->ser_ordered_slips, true);
            if (is_array($seriousArray)) {
                $seriousCount = count($seriousArray);
            }
        }

        // Step 2: Fetch Annexure slips
        $ParaDetails = DB::table('audit.slipfileupload as fileup')
            ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'fileup.auditslipid')
            ->join('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'fileup.fileuploadid')
            ->where('slip.auditscheduleid', $auditscheduleid)
            // ->where('slip.processcode', 'X')
            ->whereIn('slip.processcode', ['X'])
            ->select(
                'slip.auditslipid',
                'slip.irregularitiescode',
                'slip.slipdetails',
                'fileup.fileuploadid',
                'filedet.filepath',
                'filedet.filename'
            )
            ->get();

        // Step 3: Attach ParaNo for each record
        $response = [];

        foreach ($ParaDetails as $para) {
            $code = $para->irregularitiescode;
            $slipId = $para->auditslipid;
            $slipOrderColumn = ($code === '01') ? 'ser_ordered_slips' : 'nonser_ordered_slips';

            $slipOrderRow = DB::table('audit.report_storesliporder')
                ->where('auditscheduleid', $auditscheduleid)
                ->select($slipOrderColumn)
                ->first();

            $paraNo = null;

            if ($slipOrderRow && $slipOrderRow->$slipOrderColumn) {
                $orderedArray = json_decode($slipOrderRow->$slipOrderColumn, true);
                if (is_array($orderedArray)) {
                    foreach ($orderedArray as $key => $id) {
                        if ((int) $id === (int) $slipId) {
                            $paraNo = ($code === '01') ? (int) $key : $seriousCount + (int) $key;
                            break;
                        }
                    }
                }
            }

            $response[] = [
                'auditslipid' => $slipId,
                'slipdetails' => $para->slipdetails,
                'filename' => $para->filename,
                'filepath' => $para->filepath,
                'parano' => $paraNo ? str_pad($paraNo, 4, '0', STR_PAD_LEFT) : '-',
            ];
        }

        return response()->json($response);
    }

    public function getstatusflagfromDB(Request $request)
    {
        $auditscheduleid = $request->input('auditscheduleid');

        $auditCertificate = DB::table('audit.report_auditcertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $Report_PanTan = DB::table('audit.report_pantan')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();
        $Report_lWF = DB::table('audit.report_labourwelfarefund')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $AccountDetails = DB::table('audit.report_accountdetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('statusflag', '!=', 'N')
            ->first();

        $levy_status = DB::table('audit.report_auditlevycertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $reportfinalizeflag = DB::table('audit.inst_auditschedule')
            ->where('auditscheduleid', $auditscheduleid)
            ->select('sendintimation')
            ->first();

        $serious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->count();
        $serious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '01')
            ->whereIn('processcode', ['X'])
            ->count();
        $seriousSlipFinalizable = $serious_report_storesliporderCount === $serious_trans_auditslipCount;
        $nonserious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->where('statusflag', 'Y')
            ->count();
        $nonserious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '02')
            ->whereIn('processcode', ['X'])
            ->count();
        // dd( $seriousSlipFinalizable);
        // Compare and store the result as a boolean
        $nonseriousSlipFinalizable = $nonserious_report_storesliporderCount === $nonserious_trans_auditslipCount;

        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $statusflag_storeslip_serious = $serious_report_storesliporder->statusflag ?? '';
        $statusflag_storeslip_nonserious = $Nonserious_report_storesliporder->statusflag ?? '';
        $statusflag_annexture = $annexture_finalize->statusflag ?? '';
        $statusflag_levystatus = $levy_status->statusflag ?? '';
        $statusflag_pendingpara = $pendingpara->statusflag ?? '';
        $statusflag_pdfreport = $reportfinalizeflag->sendintimation ?? '';
        return response()->json([
            'success' => true,
            'statusflags' => [
                'auditcertificate' => $statusflag_auditcertificate,
                'authorityofaudit' => $statusflag_authorityofaudit,
                'genesisofaudit' => $statusflag_GenesisofAudit,
                'accountdetails' => $statusflag_AccountDetails,
                'pantan' => $statusflag_pantan,
                'serious_storeslip' => $statusflag_storeslip_serious,
                'nonserious_storeslip' => $statusflag_storeslip_nonserious,
                'annextures' => $statusflag_annexture,
                'levystatus' => $statusflag_levystatus,
                'pendingpara' => $statusflag_pendingpara,
                'pdfreport' => $statusflag_pdfreport,
                'seriouscountdata' => $seriousSlipFinalizable,
                'nonseriouscountdata' => $nonseriousSlipFinalizable
            ],
        ]);
    }

    public function AnnextureUpload(Request $request)
    {
        // Validate if files are uploaded
        if (!$request->hasFile('annexure_files')) {
            return response()->json(['status' => false, 'message' => 'nofiles_choosen'], 400);
        }

        $files = $request->file('annexure_files');
        $destinationPath = 'uploads/report_annexures';

        $storedAnnexures = [];
        $session = session('charge');

        $designationArray = [
            $session->deptcode,
            $session->regioncode,
            $session->distcode,
            $request->input('auditscheduleid'),
            View::shared('annexturepath'),
        ];

        foreach ($files as $annextureType => $file) {
            if ($file && $file->isValid()) {
                // $uploadResult = $this->fileUploadService->uploadFileAnnexture($file, $destinationPath, '');
                $uploadResult = $this->fileUploadService->uploadFile($file, $destinationPath, '', $designationArray);

                $responseData = $uploadResult->getData();

                $fileuploadId = $responseData->fileupload_id ?? null;
                $filetype = $file->getClientOriginalExtension();

                $data = [
                    'annexture_type' => $annextureType,  // from input name key
                    'fileupload_id' => $fileuploadId,
                    'filetype' => $filetype,
                    'auditscheduleid' => $request->input('auditscheduleid')
                ];

                $storedAnnexure = FormatModel::annexturestore($data);
                $storedAnnexures[] = $storedAnnexure;
            }
        }

        $auditscheduleid = $request->input('auditscheduleid');

        $auditCertificate = DB::table('audit.report_auditcertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $Report_PanTan = DB::table('audit.report_pantan')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();
        $Report_lWF = DB::table('audit.report_labourwelfarefund')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $AccountDetails = DB::table('audit.report_accountdetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('statusflag', '!=', 'N')
            ->first();

        $levy_status = DB::table('audit.report_auditlevycertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $serious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->count();
        $serious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '01')
            ->whereIn('processcode', ['X'])
            ->count();
        $seriousSlipFinalizable = $serious_report_storesliporderCount === $serious_trans_auditslipCount;
        $nonserious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->where('statusflag', 'Y')
            ->count();
        $nonserious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '02')
            ->whereIn('processcode', ['X'])
            ->count();
        // Compare and store the result as a boolean
        $nonseriousSlipFinalizable = $nonserious_report_storesliporderCount === $nonserious_trans_auditslipCount;

        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $statusflag_storeslip_serious = $serious_report_storesliporder->statusflag ?? '';
        $statusflag_storeslip_nonserious = $Nonserious_report_storesliporder->statusflag ?? '';
        $statusflag_annexture = $annexture_finalize->statusflag ?? '';
        $statusflag_levystatus = $levy_status->statusflag ?? '';
        $statusflag_pendingpara = $pendingpara->statusflag ?? '';

        return response()->json([
            'success' => true,
            'statusflags' => [
                'auditcertificate' => $statusflag_auditcertificate,
                'authorityofaudit' => $statusflag_authorityofaudit,
                'genesisofaudit' => $statusflag_GenesisofAudit,
                'accountdetails' => $statusflag_AccountDetails,
                'pantan' => $statusflag_pantan,
                'serious_storeslip' => $statusflag_storeslip_serious,
                'nonserious_storeslip' => $statusflag_storeslip_nonserious,
                'annextures' => $statusflag_annexture,
                'levystatus' => $statusflag_levystatus,
                'pendingpara' => $statusflag_pendingpara,
                'seriouscountdata' => $seriousSlipFinalizable,
                'nonseriouscountdata' => $nonseriousSlipFinalizable
            ],
        ]);
    }

    public function saveSlipOrder(Request $request)
    {
        $data['order_auditslip'] = $request->input('slip_order_json');
        $data['auditscheduleid'] = $request->input('auditschid');
        $data['type'] = $request->input('type');
        $result = FormatModel::StoreSlipOrdering($data);
        return response()->json([
            'status' => 'success',
            'data' => $result,
        ]);
    }

    public function Finalize_PartA(Request $request)
    {
        $data = $request->only([
            'auditscheduleid',
            'instid',
            'partno'
        ]);
        $result = FormatModel::FinalizeReport($data);

        $auditscheduleid = $request->input('auditscheduleid');

        $auditCertificate = DB::table('audit.report_auditcertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $Report_PanTan = DB::table('audit.report_pantan')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $AccountDetails = DB::table('audit.report_accountdetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('statusflag', '!=', 'N')
            ->first();

        $levy_status = DB::table('audit.report_auditlevycertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $serious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->count();
        $serious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '01')
            ->whereIn('processcode', ['X'])
            ->count();
        $seriousSlipFinalizable = $serious_report_storesliporderCount === $serious_trans_auditslipCount;
        $nonserious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->where('statusflag', 'Y')
            ->count();
        $nonserious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '02')
            ->whereIn('processcode', ['X'])
            ->count();
        // Compare and store the result as a boolean
        $nonseriousSlipFinalizable = $nonserious_report_storesliporderCount === $nonserious_trans_auditslipCount;

        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $statusflag_storeslip_serious = $serious_report_storesliporder->statusflag ?? '';
        $statusflag_storeslip_nonserious = $Nonserious_report_storesliporder->statusflag ?? '';
        $statusflag_annexture = $annexture_finalize->statusflag ?? '';
        $statusflag_levystatus = $levy_status->statusflag ?? '';
        $statusflag_pendingpara = $pendingpara->statusflag ?? '';

        return response()->json([
            'success' => true,
            'data' => $result,
            'statusflags' => [
                'auditcertificate' => $statusflag_auditcertificate,
                'authorityofaudit' => $statusflag_authorityofaudit,
                'genesisofaudit' => $statusflag_GenesisofAudit,
                'accountdetails' => $statusflag_AccountDetails,
                'pantan' => $statusflag_pantan,
                'serious_storeslip' => $statusflag_storeslip_serious,
                'nonserious_storeslip' => $statusflag_storeslip_nonserious,
                'annextures' => $statusflag_annexture,
                'levystatus' => $statusflag_levystatus,
                'pendingpara' => $statusflag_pendingpara,
                'seriouscountdata' => $seriousSlipFinalizable,
                'nonseriouscountdata' => $nonseriousSlipFinalizable
            ],
        ]);
    }

   /* public function Report_Prefil(Request $request)
    {
        $formType = $request->input('whichtypeofform');
        $result = null;

        switch ($formType) {
            case 'audit_certificate':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'cer_typecode',
                    'cer_remarks',
                    'finaliseflag'
                ]);
                $result = FormatModel::StoreAuditCertificate($data);
                break;

            case 'authorityofaudit':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    // 'authorityofaudit',
                    'finaliseflag'  // replace with actual fields
                ]);
                $result = FormatModel::StoreAuthorityOfAudit($data);
                break;

            case 'institutedetailsentry':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'genesis_ckeditor',
                    'finaliseflag'  // replace with actual fields
                ]);
                $result = FormatModel::StoreInstituteGenesis($data);
                break;

            case 'accountdet':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    // 'accountdetails_ckeditor',
                    'finaliseflag'  // replace with actual fields
                ]);

                // Collect and encode each field separately
                $data['account_details'] = json_encode($request->input('account_details', []));
                $data['bank_account_number'] = json_encode($request->input('bank_account_number', []));
                $data['ob'] = json_encode($request->input('ob', []));
                $data['receipts'] = json_encode($request->input('receipts', []));
                $data['total'] = json_encode($request->input('total', []));
                $data['expenditure'] = json_encode($request->input('expenditure', []));
                $data['cb_cashbook'] = json_encode($request->input('cb_cashbook', []));
                $data['add'] = json_encode($request->input('add', []));
                $data['less'] = json_encode($request->input('less', []));
                $data['cb_passbook'] = json_encode($request->input('cb_passbook', []));
                $data['scheme'] = json_encode($request->input('scheme', []));
                $data['branch'] = json_encode($request->input('branch', []));
                $data['accounttype'] = json_encode($request->input('account_type', []));
                $result = FormatModel::StoreAccountDetails($data);
                break;

            case 'pan_tan':
                // print_r($request->all());exit;
                /*$data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'itfiling_issue',
                    'legal_complaince',
                    'financial_review',
                    //lwf
                    'lwf_1',
                    'lwf_2',
                    'lwf_3',
                    'lwf_4',
                ]);*/

    /*            $data = $request->only([
                    'auditscheduleid',
                    'instid',
                ]);

                $itfilings_issue = trim($request->input('itfiling_issue'));
                $legal_complaince = trim($request->input('legal_complaince'));
                $financial_review = trim($request->input('financial_review'));

                $auditscheduleid = $request->auditscheduleid;
                $instid = $request->instid;
                $formData = $request->input('tdsdata', []);

                $tds_avail = $request->input('tds_avail');

                if ($tds_avail == 02) {
                    DB::table('audit.report_tds_filed_details')
                        ->where('auditscheduleid', $auditscheduleid)
                        ->where('instid', $instid)
                        ->update([
                            'statusflag' => 'N',
                            'updated_on' => now(),
                        ]);
                } else {
                    // Step 1: Get all existing records from DB for this audit/inst
                    $existingRecords = DB::table('audit.report_tds_filed_details')
                        ->where('auditscheduleid', $auditscheduleid)
                        ->where('instid', $instid)
                        ->get();

                    // Step 2: Build a quick lookup of existing entries
                    $existingMap = [];
                    foreach ($existingRecords as $record) {
                        $key = $record->audityear . '|' . $record->filing_status . '|' . $record->auditquarter;
                        $existingMap[$key] = $record;
                    }

                    // Step 3: Track keys weâ€™ve processed
                    $processedKeys = [];

                    foreach ($formData as $row) {
                        $year = $row['year'] ?? '';
                        $status = $row['status'] ?? '';
                        $period = $row['period'] ?? '';
                        $remit = $row['remit'] ?? null;
                        $filed = $row['filed'] ?? null;

                        if (!$year || !$status || !$period)
                            continue;  // Skip incomplete rows

                        $key = $year . '|' . $status . '|' . $period;
                        $processedKeys[] = $key;

                        if (isset($existingMap[$key])) {
                            // Update if exists
                            DB::table('audit.report_tds_filed_details')
                                ->where('tds_id', $existingMap[$key]->tds_id)
                                ->update([
                                    'remit_on_time' => $remit,
                                    'returns_filed' => $filed,
                                    'updated_on' => now(),
                                    'statusflag' => 'Y'
                                ]);
                        } else {
                            // Insert new
                            DB::table('audit.report_tds_filed_details')->insert([
                                'auditscheduleid' => $auditscheduleid,
                                'instid' => $instid,
                                'audityear' => $year,
                                'filing_status' => $status,
                                'auditquarter' => $period,
                                'remit_on_time' => $remit,
                                'returns_filed' => $filed,
                                'created_on' => now(),
                                'updated_on' => now(),
                                'statusflag' => 'Y'
                            ]);
                        }
                    }

                    // Optional Step 4: Delete rows not present in form (if cleanup required)
                    $keysToKeep = collect($processedKeys)->flip();

                    foreach ($existingMap as $key => $record) {
                        if (!$keysToKeep->has($key)) {
                            DB::table('audit.report_tds_filed_details')
                                ->where('tds_id', $record->tds_id)
                                ->update([
                                    'statusflag' => 'N',
                                    'updated_on' => now(),
                                ]);
                        }
                    }
                }

                // Process GST data
                if ($request->filled('gstdata')) {
                    $gstData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'gstdata' => $request->input('gstdata'),
                    ];
                    $gstdata = FormatModel::StorePanTan($gstData, 'gst_data');
                    // print_r($gstdata);
                }

                // Process LWF data
                if ($request->filled('lwfdata')) {
                    $lwfData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'lwfdata' => $request->input('lwfdata'),
                    ];
                    FormatModel::StorePanTan($lwfData, 'lwf_data');
                }

                // Process TDS data (only if applicable)
                if ($request->filled('tdsdata')) {
                    $tdsData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'tdsdata' => $request->input('tdsdata'),
                    ];
                    FormatModel::StorePanTan($tdsData, 'tds_data');
                }

                // Process CKEditor fields only if not empty
                if (
                    !empty(trim(strip_tags($itfilings_issue))) ||
                    !empty(trim(strip_tags($legal_complaince))) ||
                    !empty(trim(strip_tags($financial_review)))
                ) {
                    $ckeditorData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'itfiling_issue' => $itfilings_issue,
                        'legal_complaince' => $legal_complaince,
                        'financial_review' => $financial_review,
                    ];
                    FormatModel::StorePanTan($ckeditorData, 'filabledata');
                }

                break;

            case 'audit_levy_form':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'levycertificate_ckeditor',
                    'finaliseflag'  // replace with actual fields
                ]);
                $result = FormatModel::StoreLevyCertificate($data);
                break;

            case 'pending_para_form':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'pendingparadet_ckeditor',
                    'finaliseflag'  // replace with actual fields
                ]);
                $result = FormatModel::StorePendingPara($data);
                break;

            case 'slipform':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'parnohidden',
                    'deptcode',
                    'slip_ckeditor',
                    'slipdetails_data',
                    'ordernohidden',
                    'file_upload_ids',
                    'irregularitycode'
                ]);

                $result = FormatModel::StoreParaDetails($data);
                break;

            default:
                return response()->json(['error' => 'Invalid form type'], 400);
        }

        $auditscheduleid = $request->input('auditscheduleid');

        $auditCertificate = DB::table('audit.report_auditcertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $Report_PanTan = DB::table('audit.report_pantan')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $AccountDetails = DB::table('audit.report_accountdetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('statusflag', '!=', 'N')
            ->first();

        $levy_status = DB::table('audit.report_auditlevycertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $serious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->count();
        $serious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '01')
            ->whereIn('processcode', ['X'])
            ->count();
        $seriousSlipFinalizable = $serious_report_storesliporderCount === $serious_trans_auditslipCount;
        $nonserious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->where('statusflag', 'Y')
            ->count();
        $nonserious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '02')
            ->whereIn('processcode', ['X'])
            ->count();
        // Compare and store the result as a boolean
        $nonseriousSlipFinalizable = $nonserious_report_storesliporderCount === $nonserious_trans_auditslipCount;

        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $statusflag_storeslip_serious = $serious_report_storesliporder->statusflag ?? '';
        $statusflag_storeslip_nonserious = $Nonserious_report_storesliporder->statusflag ?? '';
        $statusflag_annexture = $annexture_finalize->statusflag ?? '';
        $statusflag_levystatus = $levy_status->statusflag ?? '';
        $statusflag_pendingpara = $pendingpara->statusflag ?? '';

        return response()->json([
            'success' => true,
            'message' => 'saved_popup',
            'data' => $result,
            'statusflags' => [
                'auditcertificate' => $statusflag_auditcertificate,
                'authorityofaudit' => $statusflag_authorityofaudit,
                'genesisofaudit' => $statusflag_GenesisofAudit,
                'accountdetails' => $statusflag_AccountDetails,
                'pantan' => $statusflag_pantan,
                'serious_storeslip' => $statusflag_storeslip_serious,
                'nonserious_storeslip' => $statusflag_storeslip_nonserious,
                'annextures' => $statusflag_annexture,
                'levystatus' => $statusflag_levystatus,
                'pendingpara' => $statusflag_pendingpara,
                'seriouscountdata' => $seriousSlipFinalizable,
                'nonseriouscountdata' => $nonseriousSlipFinalizable
            ],
        ]);
    }
    */

    // changed on 30-06-2026


 private function normalizeSerializedJsonRequest(Request $request)
    {

	if (!$request->isJson()) {
            return;
        }

        $jsonData = $request->json()->all();

        if (!is_array($jsonData)) {
            return;
        }

        $isSerializedForm = array_is_list($jsonData)
            && isset($jsonData[0])
            && is_array($jsonData[0])
            && array_key_exists('name', $jsonData[0]);

        if (!$isSerializedForm) {
            $request->merge($jsonData);
            return;
        }

        $parsedData = [];
        foreach ($jsonData as $field) {
            if (!is_array($field) || !array_key_exists('name', $field)) {
                continue;
            }

            $this->assignSerializedField($parsedData, $field['name'], $field['value'] ?? '');
        }

        $request->merge($parsedData);
    }

    private function assignSerializedField(array &$data, string $name, $value): void
    {
        preg_match_all('/([^\[\]]+)|\[([^\]]*)\]/', $name, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return;
        }

        $keys = array_map(function ($match) {
            return $match[1] !== '' ? $match[1] : $match[2];
        }, $matches);

        $cursor = &$data;
        $lastIndex = count($keys) - 1;

        foreach ($keys as $index => $key) {
            $isLast = $index === $lastIndex;

            if ($key === '') {
                if ($isLast) {
                    $cursor[] = $value;
                    return;
                }

                $cursor[] = [];
                $key = array_key_last($cursor);
            }

            if ($isLast) {
                $cursor[$key] = $value;
                return;
            }

            if (!isset($cursor[$key]) || !is_array($cursor[$key])) {
                $cursor[$key] = [];
            }

            $cursor = &$cursor[$key];
        }
    }

    private function compactAccountDetailField(Request $request, string $field, int $rowCount): array
    {
        $values = array_values((array) $request->input($field, []));

        return array_pad(array_slice($values, 0, $rowCount), $rowCount, '');
    }

    private function stringInput($value): string
    {
        if (is_array($value)) {
            $value = end($value);
        }

        return trim((string) $value);
    }

    public function Report_Prefil(Request $request)
    {
        $this->normalizeSerializedJsonRequest($request);

        $formType = $request->input('whichtypeofform');
        $result = null;

        switch ($formType) {
            case 'audit_certificate':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'cer_typecode',
                    'cer_remarks',
                    'finaliseflag'
                ]);
                $result = FormatModel::StoreAuditCertificate($data);
                break;

            case 'authorityofaudit':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    // 'authorityofaudit',
                    'finaliseflag'  // replace with actual fields
                ]);
                $result = FormatModel::StoreAuthorityOfAudit($data);
                break;

            case 'institutedetailsentry':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'genesis_ckeditor',
                    'finaliseflag'  // replace with actual fields
                ]);
                $result = FormatModel::StoreInstituteGenesis($data);
                break;

            case 'accountdet':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    // 'accountdetails_ckeditor',
                    'finaliseflag'  // replace with actual fields
                ]);

                $accountFields = [
                    'account_details',
                    'bank_account_number',
                    'ob',
                    'receipts',
                    'total',
                    'expenditure',
                    'cb_cashbook',
                    'add',
                    'less',
                    'cb_passbook',
                    'scheme',
                    'branch',
                    'account_type',
                ];
                $rowCount = 0;

                foreach ($accountFields as $field) {
                    $rowCount = max($rowCount, count((array) $request->input($field, [])));
                }

                // Collect and encode each field separately
                $data['account_details'] = json_encode($this->compactAccountDetailField($request, 'account_details', $rowCount));
                $data['bank_account_number'] = json_encode($this->compactAccountDetailField($request, 'bank_account_number', $rowCount));
                $data['ob'] = json_encode($this->compactAccountDetailField($request, 'ob', $rowCount));
                $data['receipts'] = json_encode($this->compactAccountDetailField($request, 'receipts', $rowCount));
                $data['total'] = json_encode($this->compactAccountDetailField($request, 'total', $rowCount));
                $data['expenditure'] = json_encode($this->compactAccountDetailField($request, 'expenditure', $rowCount));
                $data['cb_cashbook'] = json_encode($this->compactAccountDetailField($request, 'cb_cashbook', $rowCount));
                $data['add'] = json_encode($this->compactAccountDetailField($request, 'add', $rowCount));
                $data['less'] = json_encode($this->compactAccountDetailField($request, 'less', $rowCount));
                $data['cb_passbook'] = json_encode($this->compactAccountDetailField($request, 'cb_passbook', $rowCount));
                $data['scheme'] = json_encode($this->compactAccountDetailField($request, 'scheme', $rowCount));
                $data['branch'] = json_encode($this->compactAccountDetailField($request, 'branch', $rowCount));
                $data['accounttype'] = json_encode($this->compactAccountDetailField($request, 'account_type', $rowCount));
                $result = FormatModel::StoreAccountDetails($data);
                break;

            case 'pan_tan':
                // print_r($request->all());exit;
                /*$data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'itfiling_issue',
                    'legal_complaince',
                    'financial_review',
                    //lwf
                    'lwf_1',
                    'lwf_2',
                    'lwf_3',
                    'lwf_4',
                ]);*/

                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                ]);

                $itfilings_issue = $this->stringInput($request->input('itfiling_issue'));
                $legal_complaince = $this->stringInput($request->input('legal_complaince'));
                $financial_review = $this->stringInput($request->input('financial_review'));

                $auditscheduleid = $request->auditscheduleid;
                $instid = $request->instid;
                $formData = $request->input('tdsdata', []);

                $tds_avail = $request->input('tds_avail');

                if ($tds_avail == 02) {
                    DB::table('audit.report_tds_filed_details')
                        ->where('auditscheduleid', $auditscheduleid)
                        ->where('instid', $instid)
                        ->update([
                            'statusflag' => 'N',
                            'updated_on' => now(),
                        ]);
                } else {
                    // Step 1: Get all existing records from DB for this audit/inst
                    $existingRecords = DB::table('audit.report_tds_filed_details')
                        ->where('auditscheduleid', $auditscheduleid)
                        ->where('instid', $instid)
                        ->get();

                    // Step 2: Build a quick lookup of existing entries
                    $existingMap = [];
                    foreach ($existingRecords as $record) {
                        $key = $record->audityear . '|' . $record->filing_status . '|' . $record->auditquarter;
                        $existingMap[$key] = $record;
                    }

                    // Step 3: Track keys weâ€™ve processed
                    $processedKeys = [];

                    foreach ($formData as $row) {
                        $year = $row['year'] ?? '';
                        $status = $row['status'] ?? '';
                        $period = $row['period'] ?? '';
                        $remit = $row['remit'] ?? null;
                        $filed = $row['filed'] ?? null;

                        if (!$year || !$status || !$period)
                            continue;  // Skip incomplete rows

                        $key = $year . '|' . $status . '|' . $period;
                        $processedKeys[] = $key;

                        if (isset($existingMap[$key])) {
                            // Update if exists
                            DB::table('audit.report_tds_filed_details')
                                ->where('tds_id', $existingMap[$key]->tds_id)
                                ->update([
                                    'remit_on_time' => $remit,
                                    'returns_filed' => $filed,
                                    'updated_on' => now(),
                                    'statusflag' => 'Y'
                                ]);
                        } else {
                            // Insert new
                            DB::table('audit.report_tds_filed_details')->insert([
                                'auditscheduleid' => $auditscheduleid,
                                'instid' => $instid,
                                'audityear' => $year,
                                'filing_status' => $status,
                                'auditquarter' => $period,
                                'remit_on_time' => $remit,
                                'returns_filed' => $filed,
                                'created_on' => now(),
                                'updated_on' => now(),
                                'statusflag' => 'Y'
                            ]);
                        }
                    }

                    // Optional Step 4: Delete rows not present in form (if cleanup required)
                    $keysToKeep = collect($processedKeys)->flip();

                    foreach ($existingMap as $key => $record) {
                        if (!$keysToKeep->has($key)) {
                            DB::table('audit.report_tds_filed_details')
                                ->where('tds_id', $record->tds_id)
                                ->update([
                                    'statusflag' => 'N',
                                    'updated_on' => now(),
                                ]);
                        }
                    }
                }

                // Process GST data
                if ($request->filled('gstdata')) {
                    $gstData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'gstdata' => $request->input('gstdata'),
                    ];
                    $gstdata = FormatModel::StorePanTan($gstData, 'gst_data');
                    // print_r($gstdata);
                }

                // Process LWF data
                if ($request->filled('lwfdata')) {
                    $lwfData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'lwfdata' => $request->input('lwfdata'),
                    ];
                    FormatModel::StorePanTan($lwfData, 'lwf_data');
                }

                // Process TDS data (only if applicable)
                if ($request->filled('tdsdata')) {
                    $tdsData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'tdsdata' => $request->input('tdsdata'),
                    ];
                    FormatModel::StorePanTan($tdsData, 'tds_data');
                }

                // Process CKEditor fields only if not empty
                if (
                    !empty(trim(strip_tags($itfilings_issue))) ||
                    !empty(trim(strip_tags($legal_complaince))) ||
                    !empty(trim(strip_tags($financial_review)))
                ) {
                    $ckeditorData = [
                        'auditscheduleid' => $data['auditscheduleid'],
                        'instid' => $data['instid'],
                        'itfiling_issue' => $itfilings_issue,
                        'legal_complaince' => $legal_complaince,
                        'financial_review' => $financial_review,
                    ];
                    FormatModel::StorePanTan($ckeditorData, 'filabledata');
                }

                break;

            case 'audit_levy_form':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'levycertificate_ckeditor',
                    'finaliseflag'  // replace with actual fields
                ]);
                $result = FormatModel::StoreLevyCertificate($data);
                break;

            case 'pending_para_form':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'pendingparadet_ckeditor',
                    'finaliseflag'  // replace with actual fields
                ]);
                $result = FormatModel::StorePendingPara($data);
                break;

            case 'slipform':
                $data = $request->only([
                    'auditscheduleid',
                    'instid',
                    'parnohidden',
                    'deptcode',
                    'slip_ckeditor',
                    'slipdetails_data',
                    'ordernohidden',
                    'file_upload_ids',
                    'irregularitycode'
                ]);

                $result = FormatModel::StoreParaDetails($data);
                break;

            default:
                return response()->json(['error' => 'Invalid form type'], 400);
        }

        $auditscheduleid = $request->input('auditscheduleid');

        $auditCertificate = DB::table('audit.report_auditcertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $Report_PanTan = DB::table('audit.report_pantan')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $AccountDetails = DB::table('audit.report_accountdetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('statusflag', '!=', 'N')
            ->first();

        $levy_status = DB::table('audit.report_auditlevycertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $pendingpara = DB::table('audit.report_pendingparadetails')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $serious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->count();
        $serious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '01')
            ->whereIn('processcode', ['X'])
            ->count();
        $seriousSlipFinalizable = $serious_report_storesliporderCount === $serious_trans_auditslipCount;
        $nonserious_report_storesliporderCount = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->where('statusflag', 'Y')
            ->count();
        $nonserious_trans_auditslipCount = DB::table('audit.trans_auditslip')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitiescode', '02')
            ->whereIn('processcode', ['X'])
            ->count();
        // Compare and store the result as a boolean
        $nonseriousSlipFinalizable = $nonserious_report_storesliporderCount === $nonserious_trans_auditslipCount;

        $statusflag_auditcertificate = $auditCertificate->statusflag ?? '';
        $statusflag_authorityofaudit = $AuthorityofAudit->statusflag ?? '';
        $statusflag_GenesisofAudit = $GenesisofAudit->statusflag ?? '';
        $statusflag_AccountDetails = $AccountDetails->statusflag ?? '';
        $statusflag_pantan = $Report_PanTan->statusflag ?? '';
        $statusflag_storeslip_serious = $serious_report_storesliporder->statusflag ?? '';
        $statusflag_storeslip_nonserious = $Nonserious_report_storesliporder->statusflag ?? '';
        $statusflag_annexture = $annexture_finalize->statusflag ?? '';
        $statusflag_levystatus = $levy_status->statusflag ?? '';
        $statusflag_pendingpara = $pendingpara->statusflag ?? '';

        return response()->json([
            'success' => true,
            'message' => 'saved_popup',
            'data' => $result,
            'statusflags' => [
                'auditcertificate' => $statusflag_auditcertificate,
                'authorityofaudit' => $statusflag_authorityofaudit,
                'genesisofaudit' => $statusflag_GenesisofAudit,
                'accountdetails' => $statusflag_AccountDetails,
                'pantan' => $statusflag_pantan,
                'serious_storeslip' => $statusflag_storeslip_serious,
                'nonserious_storeslip' => $statusflag_storeslip_nonserious,
                'annextures' => $statusflag_annexture,
                'levystatus' => $statusflag_levystatus,
                'pendingpara' => $statusflag_pendingpara,
                'seriouscountdata' => $seriousSlipFinalizable,
                'nonseriouscountdata' => $nonseriousSlipFinalizable
            ],
        ]);
    }








 public function view_fieldaudit()
    {
        $userData = session('user');
        $chargeData = session('charge');

        $sessiondeptcode =  $chargeData->deptcode;
        // print_r($sessiondeptcode);
        // exit;

        $session_userid = $userData->userid;
        $quatdetails = FormatModel::fetch_quarterdetails($sessiondeptcode,$page = null);
        return view('audit.listinstitute', compact('quatdetails'));
    }


    public function get_listinstitututes(Request $request)
    {
        $auditplanmappingid = $request->auditplanmappingid;

        $userData = session('user');
        $quarterode = $request->quartercode;
        $formname = $request->formname;

        $userChargeData = session('charge');
        $session_userid = $userData->userid;
            // print_r($userChargeData);
        if ($formname == 'downloadreport') {
            if (!empty($userChargeData->instid)) {
                $instid = $userChargeData->instid;
                $whom = 'AI';
            } else {
                $instid = $userChargeData->instid ?? null;
                // $whom = 'AU';
                $whom = 'AH';
            }
            $results = FormatModel::fetch_listinstitutes($session_userid, 'finalized', $instid, $whom, $quarterode, $auditplanmappingid);
        } else {
            $results = FormatModel::fetch_listinstitutes($session_userid, '', '', 'AH', $quarterode, $auditplanmappingid);
        }

        $resultsNew = [];
        foreach ($results as $all) {
            if ($all->exitmeetdate) {
                // Convert exitmeetdate to timestamp and add 6 days
                $exitmeetdate = strtotime($all->exitmeetdate);
                $dateAfter6Days = strtotime('+6 days', $exitmeetdate);

                // Get current date (only date part)
                $currdate = strtotime(date('Y-m-d'));

                // Check if current date is more than 6 days after exitmeetdate
                if ($currdate > $dateAfter6Days) {
                    $all->encrypted_financialyearcode = Crypt::encryptString($all->financialyearcode);
                    $all->encrypted_instid = Crypt::encryptString($all->instid);
                    $all->encrypted_auditscheduleid = Crypt::encryptString($all->auditscheduleid);
                    $all->formatted_fromdate = Controller::ChangeDateFormat($all->fromdate);
                    $all->formatted_todate = Controller::ChangeDateFormat($all->todate);
                    $all->formatted_entrydate = Controller::ChangeDateFormat($all->entrymeetdate);
                    $all->formatted_exitdate = Controller::ChangeDateFormat($all->exitmeetdate);
                    $resultsNew[] = $all;
                }
            }
        }

        // $results = json_encode($resultsNew);
        return response()->json(['data' => $resultsNew]);
    }

    public function audittrans_dropdown(Request $request)
    {
        $instid = Crypt::decryptString($request->id);
        $encrypted_financialyearcode = Crypt::decryptString($request->financialyear);

        if ($request->auditscheduleid) {
            $auditscheduleid = Crypt::decryptString($request->auditscheduleid);
        }

        // Echo the ID to verify it's being passed correctly
        // Access session data

        $chargeData = session('charge');

        $session_deptcode = $chargeData->deptcode;  // Accessing the department code from the session
        $session_usertypecode = $chargeData->usertypecode;
        $userData = session('user');
        $session_userid = $userData->userid;
        $yeardata = FormatModel::getfinancialyear($session_deptcode);

        // exit;
        $inst_details = FormatModel::FetchInstituteDetails($session_userid, $auditscheduleid);
        $spilloverflag = $inst_details[0]->spilloverflag;

        $FetchAuditslips = FormatModel::AllAuditSlips($instid, '01', $encrypted_financialyearcode, $auditscheduleid, $spilloverflag);

        $FetchAuditslips_NonIrrReg = FormatModel::AllAuditSlips($instid, '02', $encrypted_financialyearcode, $auditscheduleid, $spilloverflag);

        $auditCertificate = DB::table('audit.report_auditcertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $AuthorityofAudit = DB::table('audit.report_authorityofaudit')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $Report_PanTan = DB::table('audit.report_pantan')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $AccountDetails = DB::table('audit.report_accountdetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        if ($inst_details[0]->deptcode == '01') {
            $auditEndDate = '30/06/2025';
        } else {
            // Split the years
            $years = explode(',', $inst_details[0]->yearname);

            // Get the last year range
            $lastYearRange = trim(end($years));  // e.g. "2023-2024"

            // Split the range into start and end years
            $yearParts = explode('-', $lastYearRange);

            // Use the second part as the end year
            $endYear = isset($yearParts[1]) ? trim($yearParts[1]) : null;

            if ($endYear) {
                $auditEndDate = "31/03/$endYear";
                // echo "Audit End Date: $auditEndDate";
            }
        }
        $Master_Auditcertificate = DB::table('audit.mst_auditcertificatetype')
            ->select('cer_type_code', 'cer_content')
            ->get();
        // dd($Master_Auditcertificate);
        foreach ($Master_Auditcertificate as $cert) {
            $decodedContent = json_decode($cert->cer_content);
            // Check if decoding and content are valid
            if ($decodedContent && isset($decodedContent->content)) {
                $replacedContent = str_replace('[audityear]', $auditEndDate, $decodedContent->content);

                // Replace original cer_content with updated JSON
                $cert->cer_content = json_encode([
                    'content' => $replacedContent
                ]);
            }
        }

        $serious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '01')
            ->first();

        $Nonserious_report_storesliporder = DB::table('audit.report_paradetails')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('irregularitycode', '02')
            ->first();

        /* $ParaDetails = DB::table('audit.report_paradetails as para')
            ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'para.slip_id')
            ->where('para.auditscheduleid', $auditscheduleid)
            ->select('para.para_id', 'para.slip_id','para.orderid', 'slip.slipdetails', 'para.slip_attachments')
             ->orderBy('para.orderid', 'asc')
            ->get();

        foreach ($ParaDetails as $para) {
            $fileIds = json_decode($para->slip_attachments ?? '[]', true); // decode attachment IDs

            $files = DB::table('audit.fileuploaddetail')
                ->whereIn('fileuploadid', $fileIds)
                ->pluck('filename') // get just the names
                ->toArray();

            $para->filenames = implode(', ', $files); // attach comma-separated filenames
        }*/

        $ParaDetails = DB::table('audit.slipfileupload as fileup')
            ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'fileup.auditslipid')
            ->join('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'fileup.fileuploadid')
            ->where('slip.auditscheduleid', $auditscheduleid)
            // ->where('slip.processcode', 'X')
            ->whereIn('slip.processcode', ['X'])
            ->select('slip.slipdetails', 'fileup.fileuploadid', 'filedet.filepath', 'filedet.filename')
            ->get();

        $SerOrderingSlips = DB::table('audit.report_storesliporder')
            ->where('auditscheduleid', $auditscheduleid)
            ->select('ser_ordered_slips')
            ->first();

        $NonSerOrderingSlips = DB::table('audit.report_storesliporder')
            ->where('auditscheduleid', $auditscheduleid)
            ->select('nonser_ordered_slips')
            ->first();

        $annexture_finalize = DB::table('audit.report_annextures')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('statusflag', '!=', 'N')
            ->first();
        $instdel = json_decode($inst_details, true);

        $deptcode = $instdel[0]['deptcode'];
        $catcode = $instdel[0]['catcode'];
        $subcat = $instdel[0]['subcatid'];
        $subcatcode = null;
        $ifcategory = $instdel[0]['if_subcategory'];

        $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
            ->select(
                'auth.auth_content_en',
                'auth.auth_content_ta',
                DB::raw("
                    CASE
                        WHEN auth.deptcode IN ('02','03')
                        THEN auth.auth_content_ta
                        ELSE NULL
                    END AS auth_content_ta
                ")
            )
            ->where('auth.deptcode', $deptcode)
            ->where('auth.statusflag', 'Y')
            ->where(function ($q) use ($catcode) {
                $q
                    ->where('auth.catcode', $catcode)
                    ->orWhere('auth.catcode', 'A');
            })
            ->where(function ($q) use ($subcat) {
                $q
                    ->where('auth.subcatid', $subcat)
                    ->orWhere('auth.subcatid', 'A');
            })
            ->orderByRaw("
                CASE
                    WHEN auth.subcatid = ? THEN 1
                    WHEN auth.subcatid = 'A' THEN 2
                END
            ", [$subcat])
            ->first();
        /* $account_particulars = DB::table('audit.mst_accountparticulars_details as map')
                                ->join('audit.report_accountparticulars as rp', 'map.accpar_id', '=', 'rp.accpar_id')
                                ->select('map.accpar_ename','map.accpar_key')
                                ->where('rp.deptcode', $deptcode)
                                ->where('rp.catcode', $catcode)
                                ->get();*/

        $account_particulars = DB::table('audit.mst_accountparticulars_details as map')
            ->select('map.accpar_ename', 'map.accpar_key', 'map.accpar_tname')
            ->where('map.statusflag', 'Y')
            ->orderBy('map.orderid', 'asc')
            ->get();

        /*  if($deptcode == 03)
        {
            $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
                                ->select('auth.auth_content_en')
                                ->where('auth.deptcode', $deptcode)
                                //->where('auth.catcode', $catcode)
                                ->get();

        }else
        {
            $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
                                ->select('auth.auth_content_en')
                                ->where('auth.deptcode', $deptcode)
                                ->get();

        }*/

        $Report_lWF = DB::table('audit.report_labourwelfarefund')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $Report_GST = DB::table('audit.report_gstreturn_details')
            ->where('auditscheduleid', $auditscheduleid)
            ->first();

        $Report_Levy = DB::table('audit.report_auditlevycertificate')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $PendingparaDet = DB::table('audit.report_pendingparadetails')
            ->where('scheduleid', $auditscheduleid)
            ->first();

        $tdsFiledData = DB::table('audit.report_tds_filed_details')
            ->where('auditscheduleid', $auditscheduleid)
            ->where('statusflag', 'Y')
            ->get();

        // print_r($inst_details);
        // exit;

        return view('audit.transauditslip', compact('yeardata', 'Contentauthorityofaudit', 'account_particulars', 'annexture_finalize', 'Nonserious_report_storesliporder', 'serious_report_storesliporder', 'NonSerOrderingSlips', 'SerOrderingSlips', 'ParaDetails', 'Master_Auditcertificate', 'inst_details', 'FetchAuditslips_NonIrrReg', 'FetchAuditslips', 'auditCertificate', 'AuthorityofAudit', 'GenesisofAudit', 'Report_PanTan', 'AccountDetails', 'Report_lWF', 'Report_GST', 'Report_Levy', 'tdsFiledData', 'PendingparaDet', 'instid', 'encrypted_financialyearcode'));

        // You can also add logic to handle the ID if needed
    }

    public static function codeofethics()
    {
        $chargeData = session('charge');
        $userData = session('user');
        $session_userName = $userData->username;
        $session_DesigName = $chargeData->desigelname;

        $mpdf = new Mpdf();

        // Path to the HTML file
        $htmlFilePath = resource_path('views/pdf/codeofethics.html');  // Adjust path as needed

        // Set up the page (optional)
        $mpdf->AddPage();

        // Set the border properties (e.g., color, width)
        $mpdf->SetLineWidth(1);  // Set the border width
        $mpdf->SetDrawColor(0, 0, 0);  // Set the border color (Black)

        // Draw a border around the page (Rect(x, y, width, height))
        // You can adjust the dimensions as needed to control where the border appears.
        $mpdf->Rect(10, 10, 190, 277);  // (X, Y, Width, Height)

        $htmlContent = file_get_contents($htmlFilePath);

        $controller = new Controller();
        $currentDate = $controller->ChangeDateFormat(date('d-m-Y'));

        $dynamicData = [
            'name' => $session_userName,
            'designation' => $session_DesigName,
            'currentdate' => $currentDate
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace {{key}} with actual values
            $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
        }

        // Write the HTML content to the PDF
        $mpdf->WriteHTML($htmlContent);

        $filename = 'codeofethics.pdf';  // Change this to your desired file name

        // Output the PDF to browser with the specified filename for download
        return response($mpdf->Output($filename, 'D'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function entrymeeting(Request $request)
    {
        $language = $request->lang === 'ta' ? 'ta' : 'en';  // en or ta

        $auditscheduleid = $request->auditscheduleid;  // en or ta
        $auditscheduleid = Crypt::decryptString($auditscheduleid);

        // echo $auditscheduleid;
        // exit;
        $WorkingOfficeGet1 = FormatModel::GetSchedultedEventDetails($auditscheduleid, '', '', '');
        $WorkingOfficeGet = $WorkingOfficeGet1[0];
        // print_r($WorkingOfficeGet);exit;

        $ifSubcategory = DB::table('audit.mst_auditeeins_category')
            ->where('catcode', '=', $WorkingOfficeGet->catcode)
            ->value('if_subcategory');

        if ($ifSubcategory === 'Y') {
            $Subcatget = DB::table('audit.mst_auditeeins_subcategory')
                ->where('catcode', '=', $WorkingOfficeGet->catcode)
                ->value('subcatename', 'subcattname');
        } else {
            $Subcatget = '-';
        }

        if ($language == 'en') {
            $InstituteName = $WorkingOfficeGet->instename;
            $InstCategory = $WorkingOfficeGet->catename;
            $teamhead = $WorkingOfficeGet->teamhead_en;
            $teammembers = $WorkingOfficeGet->teammembers_en;
            $teamHead_Label = 'Team Head';
            $teamMem_Label = 'Team Members';
            $nodalpersondetails = '<span style="font-family:Times New Roman;">' . $WorkingOfficeGet->nodalname . '<br>' . $WorkingOfficeGet->nodaldesignation . '</span>';
        } else {
            $InstituteName = $WorkingOfficeGet->insttname;
            $InstCategory = $WorkingOfficeGet->cattname;
            $teamhead = $WorkingOfficeGet->teamhead_ta;
            $teammembers = $WorkingOfficeGet->teammembers_ta;
            $teamHead_Label = '????  ??????';
            $teamMem_Label = '???? ?????????????';
            $nodalpersondetails = '<span style="font-family:arial;">' . $WorkingOfficeGet->nodalname . '<br>' . $WorkingOfficeGet->nodaldesignation . '</span>';
        }
        // print_r($InstituteName);exit;
        $TypeofAudit = $WorkingOfficeGet->typeofauditename;
        $FinancialYear = $WorkingOfficeGet->yearname;

        // Extract Team Head name and designation
        if (preg_match('/^(.*?)\s*\((.*?)\)$/', trim($teamhead), $matches)) {
            $headName = trim($matches[1]);
            $headDesignation = trim($matches[2]);
        } else {
            $headName = trim($WorkingOfficeGet->teamhead_en);
            $headDesignation = '';
        }

        $TeamDetails = '<b>' . $teamHead_Label . '</b><br>' . $headName . '<br>(' . $headDesignation . ')<br>';

        $TeamDetails .= '<br><b>' . $teamMem_Label . '</b><br>';

        $teamMembers = explode(',', $teammembers);

        foreach ($teamMembers as $index => $member) {
            $member = trim($member);

            // Extract name and designation
            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $member, $matches)) {
                $name = trim($matches[1]);
                $designation = trim($matches[2]);
            } else {
                // Fallback if format doesn't match
                $name = $member;
                $designation = '';
            }

            $TeamDetails .= ($index + 1) . '. ' . $name . '<br>(' . $designation . ')<br><br>';
        }

        $entrymeetdate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($WorkingOfficeGet->entrymeetdate)));
        $proposedtodate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($WorkingOfficeGet->todate)));

        $ValuesEcho = array($InstituteName, $FinancialYear, '', $entrymeetdate, $WorkingOfficeGet->teamname, '', $WorkingOfficeGet->mandays, '', '', '', '', '', '', '');

        $entrymeetdate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($entrymeetdate)));
        $proposedtodate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($proposedtodate)));

        $dynamicData = [
            'Enter Institute Name' => $InstituteName,
            'Enter Institute Category' => $InstCategory,
            'Enter Institute SubCategory' => $Subcatget,
            'Enter Audit Year' => $FinancialYear,
            'Enter Entry Meet Date' => $entrymeetdate,
            'Enter Audit Team Details' => $TeamDetails,
            'Enter Man Days Allocated' => $WorkingOfficeGet->mandays,
            'Enter Proposed End Date' => $proposedtodate,
            'Enter Nodal officer name and details' => $nodalpersondetails
        ];

        $htmlFilePath = base_path('resources/views/pdf/entryorexitmeeting.html');
        $htmlContent = file_get_contents($htmlFilePath);

        $htmlContent1 = $this->loadreportcontents(2, $language);
        if ($htmlContent1) {
            $htmlContent = $htmlContent1;
        }

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        // Create mPDF instance
        if ($language == 'ta') {
            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'fontDir' => [public_path('fonts/Tamil')],  // Point mPDF to the directory of custom fonts
                'fontdata' => [
                    $this->tamilfontname => [
                        'R' => $this->tamilfontfile,  // Regular font
                    ],
                    'arial' => [
                        'R' => 'arial.ttf',  // Make sure to add Arial if you plan to use it for English content
                    ]
                ]
            ]);

            $fontfamily = $this->tamilfontname;
        } else {
            $mpdf = new Mpdf();
            $fontfamily = 'arial';
        }

        // Add a page
        $mpdf->AddPage();

        // Set the border properties (e.g., color, width)
        // $mpdf->SetLineWidth(1); // Set the border width
        $mpdf->SetDrawColor(0, 0, 0);  // Set the border color (Black)

        // Draw a border around the page (Rect(x, y, width, height))
        // $mpdf->Rect(10, 10, 190, 277); // (X, Y, Width, Height)

        // Write HTML content to the PDF
        $mpdf->WriteHTML($htmlContent);

        // Output the PDF to the browser
        return $mpdf->Output('entrymeeting.pdf', 'D');
    }

    public function exitmeeting(Request $request)
    {
        // $language = $request->lang;

        $language = $request->lang === 'ta' ? 'ta' : 'en';  // en or ta

        $auditscheduleid = $request->auditscheduleid;  // en or ta
        $auditscheduleid = Crypt::decryptString($auditscheduleid);

        $WorkingOfficeGet1 = FormatModel::GetSchedultedEventDetails($auditscheduleid, '', '', '');
        $WorkingOfficeGet = $WorkingOfficeGet1[0];
        if ($language == 'en') {
            $InstituteName = $WorkingOfficeGet->instename;
        } else {
            $InstituteName = $WorkingOfficeGet->insttname;
        }

        $TypeofAudit = $WorkingOfficeGet->typeofauditename;
        $FinancialYear = $WorkingOfficeGet->yearname;

        $entrymeetdate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($WorkingOfficeGet->entrymeetdate)));

        $ToDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($WorkingOfficeGet->todate)));

        $exitmeetdate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($WorkingOfficeGet->exitmeetdate)));

        // $ValuesEcho = array($InstituteName, $FinancialYear,$fromDate,$ToDate, $WorkingOfficeGet->mandays, '',$WorkingOfficeGet->teamname, '', '', '', '', '', '', '');

        $entrymeetdate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($entrymeetdate)));

        $currdate = date('d-m-Y');

        if ($language == 'en') {
            $InstituteName = $WorkingOfficeGet->instename;
            $InstCategory = $WorkingOfficeGet->catename;
            $teamhead = $WorkingOfficeGet->teamhead_en;
            $teammembers = $WorkingOfficeGet->teammembers_en;
            $teamHead_Label = 'Team Head';
            $teamMem_Label = 'Team Members';
            $nodalpersondetails = '<span style="font-family:Times New Roman;">' . $WorkingOfficeGet->nodalname . '<br>' . $WorkingOfficeGet->nodaldesignation . '</span>';
        } else {
            $InstituteName = $WorkingOfficeGet->insttname;
            $InstCategory = $WorkingOfficeGet->cattname;
            $teamhead = $WorkingOfficeGet->teamhead_ta;
            $teammembers = $WorkingOfficeGet->teammembers_ta;
            $teamHead_Label = '????  ??????';
            $teamMem_Label = '???? ?????????????';
            $nodalpersondetails = '<span style="font-family:arial;">' . $WorkingOfficeGet->nodalname . '<br>' . $WorkingOfficeGet->nodaldesignation . '</span>';
        }

        // Extract Team Head name and designation
        if (preg_match('/^(.*?)\s*\((.*?)\)$/', trim($teamhead), $matches)) {
            $headName = trim($matches[1]);
            $headDesignation = trim($matches[2]);
        } else {
            $headName = trim($WorkingOfficeGet->teamhead_en);
            $headDesignation = '';
        }

        $TeamDetails = '<b>' . $teamHead_Label . '</b><br>' . $headName . '<br>(' . $headDesignation . ')<br>';

        $TeamDetails .= '<br><b>' . $teamMem_Label . '</b><br>';

        $teamMembers = explode(',', $teammembers);

        foreach ($teamMembers as $index => $member) {
            $member = trim($member);

            // Extract name and designation
            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $member, $matches)) {
                $name = trim($matches[1]);
                $designation = trim($matches[2]);
            } else {
                // Fallback if format doesn't match
                $name = $member;
                $designation = '';
            }

            $TeamDetails .= ($index + 1) . '. ' . $name . '<br>(' . $designation . ')<br><br>';
        }

        $dynamicData = [
            'Enter Institute Name' => $InstituteName,
            'Enter Audit Year' => $FinancialYear,
            'Enter Audit Start Date' => $entrymeetdate,
            'Enter Proposed Exit Meeting Date' => $ToDate,
            'Enter Audit Team Details' => $TeamDetails,
            'Enter Allocated Man Days' => $WorkingOfficeGet->mandays,
            'Enter Exit Meeting Date' => $exitmeetdate,
            'Enter Exact Man Days' => $WorkingOfficeGet->mandays,
            'Enter Conference Date' => '',
            'Enter officer Details' => $nodalpersondetails,
            'Enter Data' => ''
        ];

        $htmlFilePath = base_path('resources/views/pdf/exitmeeting.html');
        $htmlContent = file_get_contents($htmlFilePath);
        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        // Create mPDF instance
        if ($language == 'ta') {
            $mpdf = new Mpdf([
                'fontDir' => [public_path('fonts/Tamil')],  // Point mPDF to the directory of custom fonts
                'fontdata' => [
                    $this->tamilfontname => [
                        'R' => $this->tamilfontfile,  // Regular font
                    ],
                    'arial' => [
                        'R' => 'arial.ttf',  // Make sure to add Arial if you plan to use it for English content
                    ]
                ]
            ]);
            $fontfamily = $this->tamilfontname;
        } else {
            $mpdf = new Mpdf();
            $fontfamily = 'arial';
        }

        // Add a page
        $mpdf->AddPage();

        // Set the border properties (e.g., color, width)
        // $mpdf->SetLineWidth(1); // Set the border width
        $mpdf->SetDrawColor(0, 0, 0);  // Set the border color (Black)

        // Draw a border around the page (Rect(x, y, width, height))
        // $mpdf->Rect(10, 10, 190, 277); // (X, Y, Width, Height)

        // Write HTML content to the PDF
        $mpdf->WriteHTML($htmlContent);

        // Output the PDF to the browser
        return $mpdf->Output('exitmeeting.pdf', 'D');
    }

    // Function to detect if a string contains any English characters
    public function containsEnglish($string)
    {
        return preg_match('/[a-zA-Z]/', $string);  // Check for English letters
    }

    public function previewgeneratepdf(Request $request)
    {
        // Initialize mPDF
        $mpdf = new \Mpdf\Mpdf();

        // Add a page
        $mpdf->AddPage();

        // Set the border properties (e.g., color, width)
        $mpdf->SetLineWidth(1);  // Set the border width
        $mpdf->SetDrawColor(0, 0, 0);  // Set the border color (Black)

        // Draw a border around the page (Rect(x, y, width, height))
        $mpdf->Rect(10, 10, 190, 277);  // (X, Y, Width, Height)

        // HTML content for the PDF
        $html = '
              <html>
              <head>
                  <style>

                  </style>
              </head>
              <body>
                  <div class="container">
                      <div class="header">
                         ArulMigu Kapaleeshwarar temple, Mylapore, Chennai
                      </div>
                      <div class="content">
                          <p>Financial Year of 2024 - 2025</p>
                      </div>
                  </div>
                  <div class="part-a">
                    <h3>PART A</h3>
                    <ol>
                        <li><i class="fa fa-calendar text-warning me-2"></i>Intimation Letter</li>
                        <li><i class="fa fa-calendar text-warning me-2"></i>Entry Meeting</li>
                        <li><i class="fa fa-file text-info me-2"></i>Code Of Ethics</li>
                        <li><i class="fa fa-file text-info me-2"></i>Minute of Meeting</li>
                        <li><i class="fa fa-file text-info me-2"></i>Work Allocation</li>
                        <li><i class="fa fa-file text-info me-2"></i>Exit Meeting</li>
                    </ol>
                </div>

              </body>
              </html>
          ';

        // Write the HTML content to mPDF
        $mpdf->WriteHTML($html);

        $pdfContent = $mpdf->Output('', 'S');

        // Output the PDF to the browser
        // return $mpdf->Output('tamil_pdf_example.pdf', 'I');

        // Return Base64 encoded PDF
        return response()->json([
            'status' => 'success',
            'pdf' => base64_encode($pdfContent),
        ]);
    }

    public function auditcertificate(Request $request)
    {
        $mpdf = new \Mpdf\Mpdf();

        // Path to the HTML file
        $htmlFilePath = resource_path('views/pdf/auditcertificate.html');  // Adjust path as needed

        // Set up the page (optional)
        $mpdf->AddPage();

        // Set the border properties (e.g., color, width)
        $mpdf->SetLineWidth(0.5);  // Set the border width
        $mpdf->SetDrawColor(0, 0, 0);  // Set the border color (Black)

        // Draw a border around the page (Rect(x, y, width, height))
        // You can adjust the dimensions as needed to control where the border appears.
        $mpdf->Rect(10, 10, 190, 277);  // (X, Y, Width, Height)

        $htmlContent = file_get_contents($htmlFilePath);

        // Write the HTML content to the PDF
        $mpdf->WriteHTML($htmlContent);

        $filename = 'auditcertificate.pdf';  // Change this to your desired file name

        // Output the PDF to browser with the specified filename for download
        return response($mpdf->Output($filename, 'I'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function finalize_auditreport(Request $request)
    {
        try {
            $data['iframeContent'] = trim($request->input('iframeContent'));
            $data['activeStep'] = $request->input('activeStep');
            $data['activeStepNo'] = $request->input('activeStepNo');

            if (empty($data)) {
                return response()->json(['error' => 'No content provided'], 400);
            }

            // Call the model function to store content
            FormatModel::storeReport($data);

            // Return a success response
            return response()->json([
                'status' => 'success',
                'message' => 'Content saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exitmeeting_editreport()
    {
        try {
            $fontName = 'Times New Roman';
            $defaultsize = 13;

            // Step 1: Load the HTML content from an HTML file
            $htmlFilePath2 = base_path('resources/views/pdf/exitmeeting.html');
            $fileFromTemplate = true;  // Flag to track if content is from file

            if (!File::exists($htmlFilePath2)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
            }

            // Step 2: Check if report exists
            $report = FormatModel::where('report_type', '5')->latest()->first();
            $htmlContent = '';

            if ($report && !empty($report->report_contents)) {
                $steptype = $_GET['step'];
                $explode = explode('exitmeeting_', $steptype);
                if ($explode[1] == 'en') {
                    $reportContent = json_decode($report->report_contents, true);
                } else {
                    $reportContent = json_decode($report->report_contents_ta, true);
                }
                if (isset($reportContent['content'])) {
                    $htmlContent .= ($reportContent['content']);

                    $fileFromTemplate = false;
                }
            } else {
                // $htmlContent .= '<h4>5. EXIT MEETING</h4>';
            }

            // If no report found, use template file content
            if ($fileFromTemplate) {
                $htmlContent .= File::get($htmlFilePath2);
            }

            // Load content from the JSON file
            $jsonFilePath = public_path('json/pdfcontent.json');
            $jsonContent = file_get_contents($jsonFilePath);
            $data = json_decode($jsonContent, true);
            $data = mb_convert_encoding($data, 'UTF-8', 'auto');

            $language = 'en';  // en or ta
            $title = $data['exitpdfword_' . $language]['title'];
            $tablecontents = $data['exitpdfword_' . $language];

            unset($tablecontents['title']);

            $tabledata = '';
            $sno = 1;
            $x = 0;

            $ValuesEcho = array('', '', '', '', '', '', '', '', '', '', '', '', '', '');
            foreach ($tablecontents as $tablekey => $tableval) {
                $tabledata .= '<tr><td class="lang">' . $sno . '</td><td class="lang">' . $tableval . '</td><td class="lang">:</td><td class="fillupfield englishcontent">' . (isset($ValuesEcho[$x]) ? $ValuesEcho[$x] : '') . '</td></tr>';
                $sno++;
                $x++;
            }

            $dynamicData = [
                'heading_title' => $title,
                'fontFamily' => $fontName,
                'tabledata' => $tabledata
            ];

            foreach ($dynamicData as $key => $value) {
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
            }

            // Step 4: Make content editable & apply necessary formatting
            // $htmlContent = $this->makeEditable($htmlContent);

            if ($fileFromTemplate) {
                $htmlContent = $this->addBordersToHtml($htmlContent);
            }

            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function codeofethics_editreport()
    {
        try {
            $fontName = 'Times New Roman';
            $defaultsize = 13;

            // Step 1: Load the HTML content from an HTML file
            $htmlFilePath2 = base_path('resources/views/pdf/codeofethics.html');

            if (!File::exists($htmlFilePath2)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
            }

            // Step 2: Check if report exists
            $report = FormatModel::where('report_type', '3')->latest()->first();
            $htmlContent = '';
            $fileFromTemplate = true;  // Flag to track if content is from file

            if ($report && !empty($report->report_contents)) {
                $steptype = $_GET['step'];
                $explode = explode('codeofethics_', $steptype);
                if ($explode[1] == 'en') {
                    $reportContent = json_decode($report->report_contents, true);
                } else {
                    $reportContent = json_decode($report->report_contents_ta, true);
                }
                if (isset($reportContent['content'])) {
                    $htmlContent = ($reportContent['content']);

                    $fileFromTemplate = false;
                }
            }

            // If no report found, use template file content
            if ($fileFromTemplate) {
                $htmlContent = File::get($htmlFilePath2);
            }

            $dynamicData = [
                'name' => '[Name]',
                'designation' => '[Designation]',
                'currentdate' => '[Current Date]'
            ];

            // Replace placeholders with dynamic values
            foreach ($dynamicData as $key => $value) {
                // Replace {{key}} with actual values
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
            }

            // Step 4: Make content editable & apply necessary formatting
            $htmlContent = $this->makeEditable($htmlContent);

            if ($fileFromTemplate) {
                $htmlContent = $this->addBordersToHtml($htmlContent);
            }
            header('Content-Type: text/html; charset=UTF-8');

            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function entrymeeting_editreport()
    {
        try {
            $fontName = 'Times New Roman';
            $defaultsize = 13;

            // Step 1: Load the HTML content from an HTML file
            $htmlFilePath2 = base_path('resources/views/pdf/entryorexitmeeting.html');
            $fileFromTemplate = true;  // Flag to track if content is from file

            if (!File::exists($htmlFilePath2)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath2], 404);
            }

            // Step 2: Check if report exists
            $report = FormatModel::where('report_type', '2')->latest()->first();
            $htmlContent = '';

            if ($report && !empty($report->report_contents)) {
                $steptype = $_GET['step'] ?? '';
                $explode = explode('entrymeeting_', $steptype);

                // Ensure index exists before accessing it
                $languageKey = isset($explode[1]) && $explode[1] === 'ta' ? 'report_contents_ta' : 'report_contents';

                $reportContent = json_decode($report->$languageKey, true, 512, JSON_UNESCAPED_UNICODE);

                if (!empty($reportContent['content'])) {
                    $htmlContent .= $reportContent['content'];
                    $fileFromTemplate = false;
                }
            } else {
                $htmlContent .= '<h4>2. ENTRY MEETING</h4>';
            }

            // If no report found, use template file content
            if ($fileFromTemplate) {
                $htmlContent .= File::get($htmlFilePath2);
            }

            // Step 3: Load content from the JSON file
            $jsonFilePath = public_path('json/pdfcontent.json');

            if (File::exists($jsonFilePath)) {
                $jsonContent = file_get_contents($jsonFilePath);
                $data = json_decode($jsonContent, true, 512, JSON_UNESCAPED_UNICODE);
            } else {
                return response()->json(['error' => 'JSON file not found'], 404);
            }

            $language = 'en';  // Change to 'ta' dynamically if needed
            $title = $data['entrypdfword_' . $language]['title'] ?? 'Entry Meeting';

            $tablecontents = $data['entrypdfword_' . $language] ?? [];
            unset($tablecontents['title']);  // Remove title from contents

            $tabledata = '';
            $sno = 1;
            $ValuesEcho = ['', '', '', '', '', '', '', '', '', '', '', '', '', ''];

            foreach ($tablecontents as $tablekey => $tableval) {
                $tabledata .= '<tr><td class="lang">' . $sno . '</td><td class="lang">' . $tableval . '</td><td class="lang">:</td><td class="fillupfield englishcontent">' . ($ValuesEcho[$sno - 1] ?? '') . '</td></tr>';
                $sno++;
            }

            // Step 4: Replace placeholders with dynamic data
            $dynamicData = [
                'heading_title' => $title,
                'fontFamily' => $fontName,
                'tabledata' => $tabledata
            ];

            foreach ($dynamicData as $key => $value) {
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
            }

            // Make content editable
            $htmlContent = $this->makeEditable($htmlContent);

            // Add borders if content is from the template file
            if ($fileFromTemplate) {
                $htmlContent = $this->addBordersToHtml($htmlContent);
            }

            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in entry/exit meeting: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function intimationletter()
    {
        try {
            // Step 1: Load the HTML content from the template file
            $htmlFilePath = base_path('resources/views/pdf/intimationletter.html');

            if (!File::exists($htmlFilePath)) {
                return response()->json(['error' => 'HTML file not found at ' . $htmlFilePath], 404);
            }

            // Step 2: Check if report exists
            $report = FormatModel::where('report_type', '1')->latest()->first();
            $htmlContent = '';
            $fileFromTemplate = true;  // Flag to track if content is from file

            if ($report && !empty($report->report_contents)) {
                $steptype = $_GET['step'];
                $explode = explode('intimationletter_', $steptype);
                if ($explode[1] == 'en') {
                    $reportContent = json_decode($report->report_contents, true);
                } else {
                    $reportContent = json_decode($report->report_contents_ta, true);
                }
                if (isset($reportContent['content'])) {
                    $htmlContent = ($reportContent['content']);
                    // $htmlContent = html_entity_decode($reportContent['content'], ENT_QUOTES, 'UTF-8');

                    // $htmlContent = mb_convert_encoding($htmlContent, 'UTF-8', 'auto');

                    $fileFromTemplate = false;
                }
            }

            // If no report found, use template file content
            if ($fileFromTemplate) {
                $htmlContent = File::get($htmlFilePath);
            }

            // Step 3: Define placeholders & replace them with default values
            $placeholders = [
                'from_name' => '[From Name]',
                'from_desig' => '[From Designation]',
                'from_location' => '[From Location]',
                'audit_fromdate' => '[Audit Start Date]',
                'currentdate' => '[Current Date]',
                'to_name' => '[To Name]',
                'to_desig' => '[To Designation]',
                'to_location' => '[To Location]'
            ];

            foreach ($placeholders as $key => $value) {
                $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
            }

            // Step 4: Make content editable & apply necessary formatting
            //  $htmlContent = $this->makeEditable($htmlContent);

            if ($fileFromTemplate) {
                $htmlContent = $this->makeEditable($htmlContent);

                $htmlContent = $this->addBordersToHtml($htmlContent);
            }

            // Set the default internal encoding to UTF-8

            // Ensure PHP outputs UTF-8 encoded content
            header('Content-Type: text/html; charset=UTF-8');

            // Step 5: Return formatted HTML response
            return response()->json([
                'res' => 'success',
                'html' => $htmlContent
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in intimationletter: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function PartC_Contents()
    {
        $StepForm = $_REQUEST['stepform'];
        $partc_other = $_REQUEST['partc_other'];
        $scheduleId = $_GET['scheduleid'];
        $auditscheduleid = $scheduleId;
        $flagset = false;

        if ($StepForm == 'receipts_charges') {
            $accountparticulars = FormatModel::accountparticulars($auditscheduleid, 1);
            $formHeading = 'Receipts & Charges';
            $flagset = true;
        }

        if ($StepForm == 'income_expendiature') {
            $accountparticulars = FormatModel::accountparticulars($auditscheduleid, 2);
            $formHeading = 'Income & Expendiature';
            $flagset = true;
        }

        if ($StepForm == 'account_investments') {
            $accountparticulars = FormatModel::accountparticulars($auditscheduleid, 4);
            $formHeading = 'Account Investments';
            $flagset = true;
        }

        if ($flagset == true) {
            if ($accountparticulars == 'nodata') {
                return response()->json([
                    'res' => 'success',
                    'formheading' => $formHeading
                ]);
            } else {
                $filepath = $accountparticulars->filepath;

                $rootpath = url('/');  // This will generate the correct URL based on your current domain
                $AccountParticularsFilepath = $rootpath . '/' . $filepath;
                $extension = pathinfo($filepath, PATHINFO_EXTENSION);
                $url = Storage::url($filepath);

                if ($url) {
                    if ($extension == 'pdf') {
                        return response()->json([
                            'res' => 'success',
                            'pdf_url' => $AccountParticularsFilepath,  // Send the HTML content
                            'formheading' => $formHeading,
                            'fileurl' => $url,
                            'extension' => 'pdf'
                        ]);
                    } else if ($extension == 'xlsx') {
                        // Load the Excel file
                        $AccountParticularsFilepath = public_path($filepath);

                        $spreadsheet = SpreadsheetIOFactory::load($AccountParticularsFilepath);
                        $sheetNames = $spreadsheet->getSheetNames();
                        $htmlOutput = '';

                        foreach ($spreadsheet->getAllSheets() as $index => $sheet) {
                            $sheetTitle = $sheetNames[$index];
                            $htmlOutput .= "<h3>Sheet: $sheetTitle</h3>";
                            $htmlOutput .= "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";

                            $rows = $sheet->toArray();  // Convert sheet to array

                            foreach ($rows as $rowIndex => $row) {
                                $htmlOutput .= '<tr>';
                                foreach ($row as $cell) {
                                    $tag = $rowIndex == 0 ? 'th' : 'td';  // First row as header
                                    $htmlOutput .= "<$tag style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($cell) . "</$tag>";
                                }
                                $htmlOutput .= '</tr>';
                            }

                            $htmlOutput .= '</table><br>';
                        }

                        return response()->json([
                            'res' => 'success',
                            'filedata' => $htmlOutput,  // Send the HTML contentphp artisan storage:link
                            'formheading' => $formHeading,
                            'fileurl' => $url,
                            'extension' => $extension
                        ]);
                    } else if ($extension == 'png' || $extension == 'jpeg') {
                        return response()->json([
                            'res' => 'success',
                            'filepath' => $AccountParticularsFilepath,  // Send the HTML content
                            'formheading' => $formHeading,
                            'fileurl' => $url,
                            'extension' => 'img'
                        ]);
                    }
                } else {
                    echo 'elsee';
                    return response()->json([
                        'res' => 'success',
                        'formheading' => $formHeading
                    ]);
                }
            }
        }

        if ($partc_other == 'partc_other') {
            $AccountParticularsFilepath = '';
            $formHeading = $StepForm;
        }

        return response()->json([
            'res' => 'success',
            'formheading' => $formHeading
        ]);
    }

    public function singleSlipDetails()
    {
        $scheduleId = $_GET['scheduleid'];
        $slipno = $_REQUEST['slipno'] ?? null;

        $GetauditSlips = FormatModel::FetchAuditSlipsbyID($scheduleId, $slipno);

        return $GetauditSlips;
    }

    public function previewWordforSingleFile()
    {
        try {
            $scheduleId = $_GET['scheduleid'];
            $lang = $_GET['lang'];
            $stepForm = $_REQUEST['stepform'];
            $slipno = $_REQUEST['slipno'] ?? null;

            $auditscheduleid = $scheduleId;
            $loaddata = $this->loadAllValues($auditscheduleid, $lang);
            $labels = $this->loadlabels();
            $nodata_avail = $labels[$lang]['nodata_avail'];

            $fontName = $loaddata['fontName'];
            $defaultSize = $loaddata['defaultSize'];

            $commonStyle = '
            <style>
                body {
                    font-family: Times New Roman;
                    font-size: 18pt !important;
                    line-height: 1.6;
                    margin: 0;
                    padding: 0;
                    color: #000;
                }

                .pdf-wrapper {
                    border: 2px solid black;
                    padding: 40px;
                    margin: 10mm;
                    height: calc(100% - 30mm); /* Adjust height for margins */
                    box-sizing: border-box;
                }

                h1, h2, h3 {
                    text-align: center;
                    color: #333;
                }

                .section-header {
                    text-align: center;
                    margin-bottom: 15px;
                    border-bottom: 1px solid #ccc;
                    padding-bottom: 5px;
                    font-size: 16pt;
                    font-weight: bold;
                }

                .section-content {
                    margin-top: 15px;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }

                table, th, td {
                    border: 1px solid #999;
                }

                th, td {
                    padding: 13px;
                    text-align: left;
                    vertical-align: top;
                }

                .letter-footer {
                    margin-top: 40px;
                    font-size: 10pt;
                    text-align: right;
                }

                .signature {
                    margin-top: 60px;
                }

                .page-break {
                    page-break-before: always;
                }

                .center {
                    text-align: center;
                    margin-top: 20px;
                    font-weight: bold;
                }
            </style>
        ';

            // You can add multiple sections like this dynamically

            $formHeading = '';
            $htmlContent = '<html><head><style>' . $commonStyle . '</style></head><body><div class="pdf-wrapper">';

            // HEADINGS & CONTENTS
            if ($stepForm == 'intimation') {
                $formHeading = '1. ????????? ??????';
                $htmlContent .= $this->loadintimationletter($scheduleId, $lang);
            } elseif ($stepForm == 'entrymeeting') {
                $formHeading = '2. Entry Meeting';
                $htmlContent .= $this->loadentrymeeting($scheduleId, $lang, $fontName);
            } elseif ($stepForm == 'codeofethics') {
                $formHeading = '3. Code of Ethics';
                $htmlContent .= $this->load_codeofethicscontents($scheduleId, $lang);
            } elseif ($stepForm == 'minutesofmeeting') {
                $formHeading = '4. Minutes of Meeting';
                $htmlContent .= "<div class='center'><strong>{$nodata_avail}</strong></div>";
            } elseif ($stepForm == 'workallocation') {
                $formHeading = '5. Work Allocation';
                $htmlContent .= $this->workallocationpdf($scheduleId, $lang);

                // $htmlContent .= $this->generateWorkAllocationHTML($auditscheduleid, $fontName, $defaultSize, 'singlereport', $lang);
            } elseif ($stepForm == 'exitmeeting') {
                $formHeading = '6. Exit Meeting';
                $htmlContent .= $this->loadexitmeeting($scheduleId, $lang, $fontName);
            } elseif ($stepForm == 'auditslips') {
                $formHeading = 'Audit Slip #' . $slipno;
                if ($slipno) {
                    $GetauditSlips = FormatModel::FetchAuditSlipsbyID($auditscheduleid, $slipno);
                    // $htmlContent .= $this->generateAuditSlipsHtml($GetauditSlips, $lang, $fontName, $defaultSize);
                    $htmlContent .= $this->AuditSlipLoadPDF($auditscheduleid, $GetauditSlips, $lang, '', '', '');
                } else {
                    $htmlContent .= "<div class='center'><strong>{$nodata_avail}</strong></div>";
                }
            } elseif ($stepForm == 'pendingpara') {
                $formHeading = 'Pending Para';
                $pendingparacount = FormatModel::Paracount($auditscheduleid, 'pendingpara')->pendingslips;
                $label = $labels[$lang]['pendingparacount'];
                $htmlContent .= "<div class='center'><strong>{$label} - {$pendingparacount}</strong></div>";
            } elseif ($stepForm == 'currentpara') {
                $formHeading = 'Current Para';
                $currentparacount = FormatModel::Paracount($auditscheduleid, 'currentpara')->totalslips;
                $label = $labels[$lang]['currentparacount'];
                $htmlContent .= "<div class='center'><strong>{$label} - {$currentparacount}</strong></div>";
            }

            $htmlContent .= '</div></body></html>';

            $mpdfContentFinal = self::applyFontByLanguage($htmlContent);  // Adds font and auto-break

            // Create and render the PDF with mPDF
            $mpdf = new Mpdf([
                'default_font' => $fontName,
                'format' => 'A4',
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 15,
                'margin_right' => 15,
            ]);

            $mpdf->WriteHTML($mpdfContentFinal);

            // Save the PDF file
            $fileName = 'AuditReport_' . now()->format('Y_m_d_H_i_s') . '.pdf';
            $filePath = public_path('files/' . $fileName);
            $mpdf->Output($filePath, \Mpdf\Output\Destination::FILE);

            return response()->json([
                'res' => 'success',
                'filename' => $fileName,
                'formheading' => $formHeading,
                'html' => $htmlContent,  // optional preview HTML
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in previewPDFforSingleFile: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    private function getPdfStyles($fontName, $defaultSize)
    {
        return "
            body { font-family: '{$fontName}'; font-size: {$defaultSize}px; }
            .center { text-align: center; margin: 10px 0; }
            table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { border: 1px solid #000; padding: 6px; text-align: left; }
            h4 { font-size: 16px; margin-top: 20px; }
        ";
    }

     private function loadAllValues($scheduleId, $lang, $spilloverflag, $instid, $financialyearcode)
    {
        $chargeData = session('charge');
        $userData = session('user');

        $auditscheduleid = $scheduleId;
        $workAllocation = FormatModel::fetch_allocatedwork($auditscheduleid);

        $data = FormatModel::GetSchedultedEventDetails($auditscheduleid, $spilloverflag, $instid, $financialyearcode);
        $data1 = json_decode(json_encode($data), true);  // convert to array
        // print_r($data1);
        // exit;
        usort($data1, function ($a, $b) {
            return (int) filter_var($b['auditquarter'], FILTER_SANITIZE_NUMBER_INT) <=>
                (int) filter_var($a['auditquarter'], FILTER_SANITIZE_NUMBER_INT);
        });
        $data = !empty($data1) ? $data1[0] : [];

        // 🔹 Handle subcategory
        $ifSubcategory = DB::table('audit.mst_auditeeins_category')
            ->where('catcode', $data['catcode'])
            ->value('if_subcategory');

        $Subcatget = ($ifSubcategory === 'Y')
            ? DB::table('audit.mst_auditeeins_subcategory')
                ->where('catcode', $data['catcode'])
                ->value($lang === 'ta' ? 'subcattname' : 'subcatename')
            : '-';

        // 🔹 Language setup
        if ($lang === 'en') {
            $teamHead_Label = 'Team Head';
            $teamMem_Label = 'Team Members';
            $langSuffix = '_en';
            $fontName = 'Times New Roman';
            $defaultSize = 13;
            $distLabel = ' District';
        } else {
            $teamHead_Label = 'குழு தலைவர்';
            $teamMem_Label = 'குழு உறுப்பினர்கள்';
            $langSuffix = '_ta';
            $fontName = 'Tau_Marutham, sans-serif';
            $defaultSize = 11;
            $distLabel = ' மாவட்டம்';
        }

        // 🔹 Helper: format each team block
        $formatTeamDetails = function ($head, $members, $headLabel, $memLabel, $teamNo = null) {
            if (empty($head) && empty($members))
                return '';

            if (preg_match('/^(.*?)\s*\((.*?)\)$/', trim($head), $matches)) {
                $headName = trim($matches[1]);
                $headDesignation = trim($matches[2]);
            } else {
                $headName = trim($head);
                $headDesignation = '';
            }

            $labelSuffix = $teamNo ? " ({$teamNo})" : '';
            $details = "<b>{$headLabel}</b><br>{$headName}<br>({$headDesignation})<br><br>";
            $details .= "<b>{$memLabel}</b><br>";

            $teamMembers = explode(',', $members);
            foreach ($teamMembers as $index => $member) {
                $member = trim($member);
                if (preg_match('/^(.*?)\s*\((.*?)\)$/', $member, $m)) {
                    $name = trim($m[1]);
                    $designation = trim($m[2]);
                } else {
                    $name = $member;
                    $designation = '';
                }
                $details .= ($index + 1) . ". {$name}<br>({$designation})<br><br>";
            }

            return $details;
        };

                $langDataTeams = [];
        $teamIndex = 0;

        foreach ($data1 as $record) {
            $head_en = $record['teamhead_en'] ?? '';
            $head_ta = $record['teamhead_ta'] ?? '';
            $members_en = $record['teammembers_en'] ?? '';
            $members_ta = $record['teammembers_ta'] ?? '';
            $quarter = $record['auditquarter'] ?? '';
            $entryMeet = $record['entrymeetdate'] ?? '';
            $exitMeet = $record['exitmeetdate'] ?? '';

            if (empty($head_en) && empty($members_en))
                continue;

            // 🔹 Format English block
            $teamDetails_en = $formatTeamDetails($head_en, $members_en, 'Team Head', 'Team Members', $teamIndex + 1);

            // 🔹 Format Tamil block
            $teamDetails_ta = $formatTeamDetails($head_ta, $members_ta, 'குழு தலைவர்', 'குழு உறுப்பினர்கள்', $teamIndex + 1);

            // 🔹 Store dynamic keys for each team
            $langDataTeams["Teamdetails{$teamIndex}_en"] = $teamDetails_en;
            $langDataTeams["Teamdetails{$teamIndex}_ta"] = $teamDetails_ta;

            $langDataTeams["TeamHead{$teamIndex}_en"] = $head_en;
            $langDataTeams["TeamHead{$teamIndex}_ta"] = $head_ta;

            $langDataTeams["TeamMembers{$teamIndex}_en"] = $members_en;
            $langDataTeams["TeamMembers{$teamIndex}_ta"] = $members_ta;

            $langDataTeams["TeamQuarter{$teamIndex}"] = $quarter;
            $langDataTeams["TeamEntryMeet{$teamIndex}"] = $entryMeet;
            $langDataTeams["TeamExitMeet{$teamIndex}"] = $exitMeet;

            $teamIndex++;
        }

        // 🔹 Base language values
        $langDataBase = $lang === 'ta' ? [
            'DeptName' => $data['depttlname'] ?? '',
            'InstituteName' => $data['insttname'] ?? '',
            'InstCategory' => $data['cattname'] ?? '',
            'InstSubcat' => $Subcatget,
            'TypeofAudit' => $data['typeofaudittname'] ?? '',
            'DistName' => ($data['disttname'] ?? '') . $distLabel,
            'designName' => $userData->desigtsname ?? '',
            'UserName' => $userData->usertname ?? '',
            'fontName' => $fontName,
            'defaultSize' => $defaultSize,
            // 'Teamdetails'   => $TeamDetails,
        ] : [
            'DeptName' => $data['deptelname'] ?? '',
            'InstituteName' => $data['instename'] ?? '',
            'InstCategory' => $data['catename'] ?? '',
            'InstSubcat' => $Subcatget,
            'TypeofAudit' => $data['typeofauditename'] ?? '',
            'DistName' => ($data['distename'] ?? '') . $distLabel,
            'designName' => $chargeData->desigelname ?? '',
            'UserName' => $userData->username ?? '',
            'fontName' => $fontName,
            'defaultSize' => $defaultSize,
            // 'Teamdetails'   => $TeamDetails,
        ];

        // print_r($data);
        // print_r($langDataBase);
        // print_r($langDataTeams);

        // exit;

        // 🔹 Merge and return
        return array_merge($data, $langDataBase, $langDataTeams);
    }

    private function loadreportcontents($reportype, $lang)
    {
        $report = FormatModel::where('report_type', $reportype)->latest()->first();
        $fileFromTemplate = true;  // Flag to track if content is from file
        $htmlContent = '';

        if ($report && !empty($report->report_contents)) {
            if ($lang == 'ta') {
                $reportContent = json_decode($report->report_contents_ta, true);
            } else {
                $reportContent = json_decode($report->report_contents, true);
            }

            if (isset($reportContent['content'])) {
                $htmlContent = $reportContent['content'];
            } else {
                $htmlContent = '<p>Content not found</p>';
            }
        }
        return $htmlContent;
    }

    private function loadintimationletterss($scheduleId, $lang)
    {
        $loaddata = $this->loadAllValues($scheduleId, $lang);
        $htmlContent = $this->loadreportcontents(1, $lang);

        // Convert HTML entities (ensure Tamil text is preserved)
        $htmlContent = html_entity_decode($htmlContent, ENT_QUOTES, 'UTF-8');

        $fromDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['fromdate'])));
        $toDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['todate'])));
        $entymeetdate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['entrymeetdate'])));

        // TeamMembers
        $dynamicData = [
            'Team Head Details' => $loaddata['TeamHead'],
            'Institution Details' => $loaddata['InstituteName'],
            'Department Name' => $loaddata['DeptName'],
            'RC No' => $loaddata['rcno'],
            'Audit Year' => $loaddata['yearname'],
            'From Date' => $fromDate,
            'To Date' => $toDate,
            'Entry Meeting Date' => $entymeetdate,
            'Current Date' => Controller::ChangeDateFormat(date('d-m-Y'))
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        // Handle Team Members and Designations
        $serialNumber = 1;
        $tbodyContent = '';

        // Convert comma-separated values into an array
        $teamMembersArray = explode(',', $loaddata['TeamMembers']);
        $teamDesignationsArray = isset($loaddata['TeamDesignations']) ? explode(',', $loaddata['TeamDesignations']) : [];

        foreach ($teamMembersArray as $index => $member) {
            // Assign designation if available, otherwise set as '-'
            $designation = isset($teamDesignationsArray[$index]) ? trim($teamDesignationsArray[$index]) : '-';

            $tbodyContent .= '<tr>
                                <td>' . $serialNumber . '</td>
                                <td>' . htmlspecialchars(trim($member)) . '</td>
                                <td>' . htmlspecialchars($designation) . '</td>
                            </tr>';
            $serialNumber++;
        }

        // Ensure placeholder replacement works even if formatting differs
        $htmlContent = preg_replace('/<tbody>.*?\[S.No\].*?\[Name\].*?\[Designation\].*?<\/tbody>/is', "<tbody>$tbodyContent</tbody>", $htmlContent);

        // Remove <style> and <script> tags (PHPWord doesn't support them)
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<script.*?<\/script>/is', '', $htmlContent);

        return $htmlContent;
    }

    private function loadintimationletter($scheduleId, $lang)
    {
        $loaddata = $this->loadAllValues($scheduleId, $lang);
        $htmlContent = $this->loadreportcontents(1, $lang);

        // Convert HTML entities (ensure Tamil text is preserved)
        $htmlContent = html_entity_decode($htmlContent, ENT_QUOTES, 'UTF-8');

        $htmlContent = preg_replace('/<tbody>.*?\[S.No\].*?\[Name\].*?\[Designation\].*?<\/tbody>/is', '<tbody>[Table Content]</tbody>', $htmlContent);

        $fromDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['fromdate'])));
        $toDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['todate'])));
        $entymeetdate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['entrymeetdate'])));

        // Generate table content first
        $serialNumber = 1;
        $tbodyContent = '';

        // Convert comma-separated values into an array
        $teamMembersArray = explode(',', $loaddata['TeamMembers']);

        foreach ($teamMembersArray as $member) {
            $parts = explode('(', $member);

            $name = trim($parts[0]);

            $designation = trim(str_replace(')', '', $parts[1]));

            $tbodyContent .= '<tr><td>' . $serialNumber . '</td><td>' . htmlspecialchars(trim($name)) . '</td><td>' . htmlspecialchars(trim($designation)) . '</td></tr>';
            $serialNumber++;
        }

        $audit_particulars = AuditManagementModel::Selected_CFR($scheduleId);
        $CallforRecords = array();

        $x = 1;

        $x = 0;  // Initialize a counter

        foreach ($audit_particulars as $record) {
            // Check the language and add the appropriate field to the result array
            if ($lang == 'ta') {
                // If the language is Tamil, use the Tamil field
                $CallforRecords[] = $record['callforrecordstname'];  // Access as an array, not as an object
            } else {
                // Otherwise, use the English field
                $CallforRecords[] = $record['callforrecordsename'];  // Access as an array, not as an object
            }

            // Optional: Break after processing 12 records (uncomment to stop after 12 iterations)
            if ($x == 12) {
                // break; // Stops the loop after 12 iterations
            }

            $x++;  // Increment the counter
        }

        $CallforRecords = implode(', ', $CallforRecords);
        $CallforRecords = str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $CallforRecords);
        $CallforRecords = htmlspecialchars($CallforRecords, ENT_XML1, 'UTF-8');

        // Store dynamic data
        $dynamicData = [
            'Team Head Details' => $loaddata['TeamHead'],
            'Institution Details' => $loaddata['InstituteName'],
            'Department Name' => $loaddata['DeptName'],
            'RC No' => $loaddata['rcno'],
            'Audit Year' => $loaddata['yearname'],
            'From Date' => $fromDate,
            'To Date' => $toDate,
            'Entry Meeting Date' => $entymeetdate,
            'Current Date' => Controller::ChangeDateFormat(date('d-m-Y')),
            'table content' => $tbodyContent,  // Store table content for replacement
            'CallforRecords' => $CallforRecords
        ];

        // Replace the table placeholder separately

        // Replace other placeholders
        foreach ($dynamicData as $key => $value) {
            if ($key !== 'tablecontent_replace11') {
                // Ensure the correct placeholder format
                $htmlContent = str_replace("[$key]", $value, $htmlContent);
            }
        }

        // Remove <style> and <script> tags (PHPWord doesn't support them)
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<script.*?<\/script>/is', '', $htmlContent);

        return $htmlContent;
    }

    private function loadentrymeeting($scheduleId, $lang, $fontName)
    {
        $loaddata = $this->loadAllValues($scheduleId, $lang);

        // print_r($loaddata);exit;
        $htmlContent = $this->loadreportcontents(2, $lang);

        // Convert HTML entities properly
        // $htmlContent = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');

        $fromDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['entrymeetdate'])));

        $todate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['todate'])));

        $nodalpersondetails = '<span style="font-family:Times New Roman;">' . $loaddata['nodalname'] . '<br>' . $loaddata['nodaldesignation'] . '</span>';

        $dynamicData = [
            'Enter Institute Name' => $loaddata['InstituteName'],
            'Enter Institute Category' => $loaddata['InstCategory'],
            'Enter Institute SubCategory' => $loaddata['InstSubcat'],
            'Enter Audit Year' => $loaddata['yearname'],
            'Enter Entry Meet Date' => Controller::ChangeDateFormat($loaddata['entrymeetdate']),
            'Enter Proposed End Date' => $todate,
            'Enter Man Days Allocated' => $loaddata['mandays'],
            'Enter Audit Team Details' => $loaddata['Teamdetails'],
            'Enter Nodal officer name and details' => $nodalpersondetails
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        // Remove <style> and <script> tags
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<script.*?<\/script>/is', '', $htmlContent);

        // $htmlContent = str_replace(['&', '<', '>'], ['&amp;', '&lt;', '&gt;'], $htmlContent);

        // / $htmlContent = htmlspecialchars($htmlContent, ENT_XML1, 'UTF-8');

        // Decode special characters for PHPWord compatibility
        // $htmlContent = htmlspecialchars_decode($htmlContent, ENT_QUOTES);

        return $htmlContent;
    }

    private function load_codeofethicscontents($scheduleId, $lang, $ifModel = '', $Name = '', $Desig = '')
    {
        $loaddata = $this->loadAllValues($scheduleId, $lang);
        $htmlContent = $this->loadreportcontents(3, $lang);

        if ($ifModel) {
            $Name = $Name;
            $Desig = $Desig;
        } else {
            $Name = $loaddata['UserName'];
            $Desig = $loaddata['designName'];
        }

        $dynamicData = [
            'Name' => $Name,
            'Designation' => $Desig,
            'Current Date' => Controller::ChangeDateFormat(date('d-m-Y'))
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);

        return $htmlContent;
    }

    private function generateWorkAllocationTable($section, $auditscheduleid, $fontName, $defaultsize, $reportype, $lang)
    {
        // Fetch Work Allocation Data
        $workAllocation = FormatModel::fetch_allocatedwork($auditscheduleid);

        $labels = $this->loadlabels();

        $nodata_avail = $labels[$lang]['nodata_avail'];

        if (!$workAllocation->isEmpty()) {
            $results = [];
            foreach ($workAllocation->all() as $item) {
                $results[] = [
                    'username' => $item->username,
                    'worktypes' => $item->worktypes,
                ];
            }

            if (!empty($results)) {
                $section->addText(
                    '5. WORK ALLOCATION',
                    ['name' => $fontName, 'size' => 14, 'bold' => true],
                    ['align' => 'center', 'lineHeight' => 1.5, 'spaceBefore' => 70]
                );

                $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80]);

                // Add table headers with bold text
                $table->addRow();
                $table->addCell(1000)->addText('S.No.', ['bold' => true, 'name' => $fontName, 'size' => $defaultsize]);
                $table->addCell(5000)->addText('Team Member Name', ['bold' => true, 'name' => $fontName, 'size' => $defaultsize]);
                $table->addCell(5000)->addText('Work Allocation', ['bold' => true, 'name' => $fontName, 'size' => $defaultsize]);

                $serialNumber = 1;

                foreach ($results as $entry) {
                    $table->addRow();
                    $table->addCell(1000)->addText($serialNumber++, ['name' => $fontName, 'size' => $defaultsize]);
                    $table->addCell(5000)->addText($entry['username'], ['name' => $fontName, 'size' => $defaultsize]);
                    $worktypes = htmlspecialchars($entry['worktypes'], ENT_QUOTES, 'UTF-8');
                    $table->addCell(5000)->addText($worktypes, ['name' => $fontName, 'size' => $defaultsize]);
                }
            }
        } else {
            if ($reportype == 'singlereport') {
                $section->addText(
                    $nodata_avail,
                    ['name' => $fontName, 'size' => $defaultsize, 'bold' => true],
                    ['alignment' => 'center']
                );
            }
        }
    }

    private function loadexitmeeting($scheduleId, $lang, $fontName)
    {
        $loaddata = $this->loadAllValues($scheduleId, $lang);
        $htmlContent = $this->loadreportcontents(5, $lang);

        // Convert HTML entities properly
        // $htmlContent = mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8');
        $nodalpersondetails = '<span style="font-family:Times New Roman;">' . $loaddata['nodalname'] . '<br>' . $loaddata['nodaldesignation'] . '</span>';

        $fromDate = Controller::ChangeDateFormat(date('d-m-Y', strtotime($loaddata['fromdate'])));
        $dynamicData = [
            'Enter Institute Name' => $loaddata['InstituteName'],
            'Enter Audit Year' => $loaddata['yearname'],
            'Enter Audit Start Date' => Controller::ChangeDateFormat($loaddata['fromdate']),
            'Enter Audit Team Details' => $loaddata['Teamdetails'],
            'Enter Proposed Exit Meeting Date' => Controller::ChangeDateFormat($loaddata['todate']),
            'Enter Exit Meeting Date' => Controller::ChangeDateFormat($loaddata['exitmeetdate']),
            'Enter Allocated Man Days' => $loaddata['mandays'],
            'Enter Exact Man Days' => $loaddata['mandays'],
            'Enter Conference Date' => '',
            'Enter officer Details' => $nodalpersondetails,
            'Enter Data' => ''
        ];

        // Replace placeholders with dynamic values
        foreach ($dynamicData as $key => $value) {
            // Replace [key] with actual values
            $htmlContent = str_replace('[' . $key . ']', $value, $htmlContent);
        }

        // Remove <style> and <script> tags
        $htmlContent = preg_replace('/<style.*?<\/style>/is', '', $htmlContent);
        $htmlContent = preg_replace('/<script.*?<\/script>/is', '', $htmlContent);

        // Decode special characters for PHPWord compatibility
        // $htmlContent = htmlspecialchars_decode($htmlContent, ENT_QUOTES);

        return $htmlContent;
    }

    private function loadlabels()
    {
        $jsonFilePath = public_path('json/layout.json');
        $jsonContent = file_get_contents($jsonFilePath);
        $labels = json_decode($jsonContent, true);
        $labels = mb_convert_encoding($labels, 'UTF-8', 'auto');
        return $labels;
    }

    private function AuditSlipLoad($section, $auditscheduleid, $fontName, $defaultsize, $GetauditSlips, $lang)
    {
        $tableContent = '';

        $labels = $this->loadlabels();

        $amountinvolved_label = $labels[$lang]['amount_involved'];
        $severity_label = $labels[$lang]['severity'];
        $liability_label = $labels[$lang]['liability'];
        $slip_details_label = $labels[$lang]['slip_details'];
        $slip_details_headinglabel = $labels[$lang]['slipdetails_label'];

        $auditordetails = $labels[$lang]['auditordetails_heading'];

        $severitylow = $labels[$lang]['severity_low'];
        $severitymedium = $labels[$lang]['severity_medium'];
        $severityhigh = $labels[$lang]['severity_high'];

        $SeverityArr = ['L' => $severitylow, 'M' => $severitymedium, 'H' => $severityhigh];

        $liabilityarr = ['Y' => $labels[$lang]['yes'], 'N' => $labels[$lang]['no']];

        // $GetauditSlips =json_decode($GetauditSlips,true);
        $liability = $GetauditSlips->getData()->liability;  // Get 'data' key from response
        $GetauditSlips = $GetauditSlips->getData()->data;  // Get 'data' key from response

        $groupedByMainslip = [];
        $groupedByLiability1 = [];
        $mainslipnumbers = [];

        // Iterate over each item and group by 'mainslipnumber'
        foreach ($GetauditSlips as $item) {
            $groupedByMainslip[$item->mainslipnumber][] = $item;
            $mainslipnumbers[] = $item->mainslipnumber;
        }
        $mainslipnumbers = array_unique($mainslipnumbers);

        foreach ($liability as $LiabilityItem) {
            $groupedByLiability1[$LiabilityItem->mainslipnumber][] = $LiabilityItem;
        }

        $groupedByLiability = [];

        foreach ($mainslipnumbers as $val) {
            if (array_key_exists($val, $groupedByLiability1)) {
                // If the mainslip number exists in the first grouped array, add it to the final grouped array
                $groupedByLiability[$val] = $groupedByLiability1[$val];
            } else {
                // If the mainslip number doesn't exist, assign an empty array
                $groupedByLiability[$val] = 'nodata';
            }
        }

        // $groupedByMainslip = $GetauditSlips->groupBy('mainslipnumber');

        foreach ($groupedByMainslip as $mainslipNumber => $items) {
            $textRun = $section->addTextRun(['align' => 'center']);

            if ($lang == 'ta') {
                $textRun->addText('#' . $mainslipNumber . '-' . $slip_details_headinglabel, ['name' => $fontName, 'bold' => true, 'size' => 14]);
            } else {
                $textRun->addText($slip_details_headinglabel . '#' . $mainslipNumber . '', ['name' => $fontName, 'bold' => true, 'size' => 14]);
            }

            $X = 1;
            foreach ($items as $auditSlip) {
                if ($lang == 'ta') {
                    $objectionname = $auditSlip->objectiontname;
                    $subobjectionname = $auditSlip->subobjectiontname;
                } else {
                    $objectionname = $auditSlip->objectionename;
                    $subobjectionname = $auditSlip->subobjectionename;
                }

                $textRun = $section->addTextRun(['align' => 'center']);
                $textRun->addText('' . $X . ') ' . $objectionname . ' => ', ['name' => $fontName, 'bold' => false, 'size' => 14]);
                $textRun->addText($subobjectionname, ['name' => $fontName, 'bold' => false, 'size' => $defaultsize]);

                // Add remaining content
                /*$textRun = $section->addTextRun(['align' => 'left']);
                $textRun->addText($amountinvolved_label.': ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                $textRun->addText($auditSlip->amtinvolved, ['name' => $fontName, 'size' => $defaultsize]);*/

                $textRun = $section->addTextRun(['align' => 'left']);
                $textRun->addText($severity_label . '   : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                $textRun->addText($SeverityArr[$auditSlip->severityid], ['name' => $fontName, 'size' => $defaultsize]);

                /*$textRun = $section->addTextRun(['align' => 'left']);
                $textRun->addText($liability_label .'               : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                $textRun->addText($liabilityarr[$auditSlip->liability], ['name' => $fontName, 'size' => $defaultsize]);

                if($auditSlip->liability == 'Y')
                {
                    $textRun = $section->addTextRun(['align' => 'left']);
                    $textRun->addText('Liability Name: ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                    $textRun->addText($auditSlip->liabilityname, ['name' => $fontName, 'size' => $defaultsize]);
                }*/

                $textRun = $section->addTextRun();
                $textRun->addText($slip_details_label . ': ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                $textRun->addText($auditSlip->slipdetails, ['name' => $fontName, 'size' => $defaultsize]);

                $textRun = $section->addTextRun(['align' => 'left']);
                $textRun->addText('Status : ', ['name' => $fontName, 'size' => $defaultsize, 'bold' => true]);
                $textRun->addText($auditSlip->processelname, ['name' => $fontName, 'size' => $defaultsize]);

                $textRun = $section->addTextRun(['align' => 'left']);

                $textRun->addText('' . $X . '.1.Remarks', ['name' => $fontName, 'bold' => true, 'size' => $defaultsize]);

                $textRun = $section->addTextRun(['align' => 'left']);

                if (!empty($auditSlip->remarks)) {
                    $auditorRemarks = json_decode($auditSlip->remarks);
                    $auditorContent = isset($auditorRemarks->content) ? $auditorRemarks->content : 'No Remarks Available';

                    // Decode HTML entities
                    $auditorContent = html_entity_decode($auditorContent, ENT_QUOTES, 'UTF-8');

                    // Convert double quotes to single quotes
                    $auditorContent = str_replace('"', "'", $auditorContent);

                    // Remove entire style="" attributes
                    $auditorContent = preg_replace("/font-family:'([^']+)'/i", 'font-family:$1', $auditorContent);

                    // Convert encoding to UTF-8 (ensure proper character handling)
                    $auditorContent = mb_convert_encoding($auditorContent, 'UTF-8', 'auto');

                    // Add clean HTML content to PHPWord
                    Html::addHtml($section, $auditorContent, false, false);
                } else {
                    $section->addText('No Remarks Available', ['size' => $defaultsize, 'name' => $fontName]);
                }

                // $section->addLine(['weight' => 1, 'width' => 430, 'height' => 0, 'color' => '000000']);
                // Add a line break at the end of each loop iteration
                $section->addText('', ['name' => $fontName, 'size' => $defaultsize]);  // Blank line after each loop iteration

                $X++;
            }
            $Liability = $groupedByLiability[$mainslipNumber];

            if ($Liability != 'nodata') {
                $textRun = $section->addTextRun(['align' => 'center']);
                $textRun->addText('Liability Details', ['name' => $fontName, 'bold' => true, 'size' => 14]);

                $tableStyle = array(
                    'borderSize' => 6,
                    'borderColor' => '000000',
                    'cellMargin' => 80,
                );

                // Add the table to the section
                $table = $section->addTable('tableStyle');

                // Add the header row
                $table->addRow();
                $table->addCell(2000)->addText('Liability Name', array('bold' => true, 'name' => $fontName, 'size' => 12));
                $table->addCell(2000)->addText('Details', array('bold' => true, 'name' => $fontName, 'size' => 12));
                $table->addCell(2000)->addText('Designation', array('bold' => true, 'name' => $fontName, 'size' => 12));
                $table->addCell(2000)->addText('Amount Involved', array('bold' => true, 'name' => $fontName, 'size' => 12));

                // Assuming $Liability is your array of objects
                foreach ($Liability as $LiabilityKey => $LiabilityVal) {
                    $table->addRow();
                    $table->addCell(2000)->addText($LiabilityVal->liabilityname, array('name' => $fontName, 'size' => 12));

                    // Conditional logic for Identification Number with prefix
                    $identificationNumber = $LiabilityVal->liabilitygpfno;
                    $prefix = '';  // Initialize prefix variable

                    if ($LiabilityVal->notype == 1) {
                        $prefix = 'GPF No: ';
                    } elseif ($LiabilityVal->notype == 2) {
                        $prefix = 'CF No: ';
                    } elseif ($LiabilityVal->notype == 3) {
                        $prefix = 'IHRMS No: ';
                    }

                    // Add the prefix and the identification number together
                    $table->addCell(2000)->addText($prefix . $identificationNumber, array('name' => $fontName, 'size' => 12));
                    $table->addCell(2000)->addText($LiabilityVal->liabilitydesignation, array('name' => $fontName, 'size' => 12));
                    $table->addCell(2000)->addText($LiabilityVal->liabilityamount, array('name' => $fontName, 'size' => 12));
                }
            }
        }

        // Html::addHtml($textRun, $htmlContent, false, false);
    }

    private function OverviewContentLoad($auditscheduleid)
    {
        $html = '<h3>OVERVIEW</h3>';

        $html .= 'This Report contains four chapters. The first and second chapters contain an Executive
                Summary and overview of Annual Accounts respectively. The third chapter contains details
                of audit procedure and Auditable Institutions. The fourth chapter contain the Introduction and
                audit observations pertaining to City Municipal Corporations, Municipalities and Town
                Panchayats in Urban Local Bodies respectively.1. Executive Summary:
                An Overview of Financial Position of Urban Local Bodies (viz., Municipal Corporations,
                Municipalities and Town Panchayats).IL Overview of Annual Accounts:
                Comparative analysis of Income and Expenditure under various sub-heads of Urban Local
                Bodies.
                III Audit procedure and Auditable Institutions:
                A short introduction on the Tamil Nadu Local Fund Audit Department and the Tamil Nadu
                Local Fund Audit Act, 2014. Audit procedure, Number of Auditable Institutions and the Gist
                of major Audit Observations are given in this chapter.
                IV. Introduction to Urban Local Bodies and Major Audit Observations:
                For the Year ended March 2022, 41 Major Audit Observations pertaining to Urban Local
                Bodies are discussed in this chapter.';

        return $html;
    }

    // private function currentparadetails($GetauditSlips, $lang)
    // {
    //     $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
    //     $label_layout = $labelsJson_layout[$lang];

    //     $GetauditSlips = $GetauditSlips->getData()->data;

    //     /*$groupedByMainslip = [];
    //     $groupedByLiability1 = [];
    //     $mainslipnumbers = [];

    //     foreach ($GetauditSlips as $item) {
    //         $groupedByMainslip[$item->mainslipnumber][] = $item;
    //         $mainslipnumbers[] = $item->mainslipnumber;
    //     }

    //     $mainslipnumbers = array_unique($mainslipnumbers);*/

    //     $currentparacontent = '<div class="page-break"><h3 style="text-align:center;"><u>' . $label_layout['currectyearpata'] . '</u></h3>';

    //     $currentparacontent .= ' <table style="width: 100%; border: none; border-collapse: collapse;">
    //     <tr>
    //         <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['s_no'] . '</th>
    //         <th style="width:25%; font-weight: bold;text-align:center;">' . $label_layout['reportpara'] . '</th>
    //         <th style="width:20%; font-weight: bold;text-align:center;">' . $label_layout['amount'] . '</th>
    //     </tr>';

    //     $counter = 1; // Initialize the counter for S.No

    //     // Loop through the $mainslipnumbers array
    //     /* foreach ($mainslipnumbers as $slipkey => $slipval) {
    //         $currentparacontent .= '<tr>';
    //         $currentparacontent .= '<td style="width:10%;">' . $counter . '</td>'; // Insert S.No
    //         $currentparacontent .= '<td style="width:25%;">' . $counter . '</td>';  // Insert Audit Report Para No
    //         $currentparacontent .= '<td style="width:25%;">' . $slipval . '</td>'; // Insert Audit Notes Para No
    //         $currentparacontent .= '<td style="width:20%;">' . (isset($slipval['amtinvolved']) ? $slipval['notes_amount'] : '6770') . '</td>'; // Insert Amount for Audit Notes
    //         $currentparacontent .= '</tr>';

    //         $counter++;
    //     }*/
    //     $totalAmount = 0;

    //     foreach ($GetauditSlips as $item) {

    //         $currentparacontent .= '<tr>';
    //         $currentparacontent .= '<td style="width:10%;text-align:center;">' . $counter . '</td>'; // Insert S.No
    //         $currentparacontent .= '<td style="width:25%;text-align:center;">' . str_pad($counter, 4, '0', STR_PAD_LEFT) . '</td>';  // Insert Audit Report Para No

    //         $currentparacontent .=  '<td style="text-align:right;">' . htmlspecialchars($this->formatIndianCurrency($item->amtinvolved)) . '</td>'; // Insert Amount for Audit Notes
    //         $currentparacontent .= '</tr>';
    //         $counter++;

    //         $totalAmount += $item->amtinvolved; // Add to total
    //     }

    //     // Add total row
    //     $currentparacontent .= '<tr>';
    //     $currentparacontent .= '<td colspan="2" style="text-align:right;font-weight:bold;">' . $label_layout['total_amt'] . '</td>';
    //     $currentparacontent .= '<td style="font-weight:bold;text-align:right;">' . htmlspecialchars($this->formatIndianCurrency($totalAmount)) . '</td>';
    //     $currentparacontent .= '</tr>';

    //     $currentparacontent .= '</tbody>';
    //     $currentparacontent .= '</table>';

    //     $currentparacontent .= '</table></div>';

    //     return $currentparacontent;
    // }
    private function currentparadetails($GetauditSlips, $lang)
    {
        $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
        $label_layout = $labelsJson_layout[$lang];

        // Decode JSON response
        $responseData = json_decode($GetauditSlips->getContent(), true);

        // Ensure indexed array to preserve order
        $GetauditSlips = array_values($responseData['data'] ?? []);

        $currentparacontent = '<div class="page-break">
        <h3 style="text-align:center;">
        <u>' . $label_layout['currectyearpata'] . '</u>
        </h3>';

        $currentparacontent .= '
    <table style="width: 100%; border: none; border-collapse: collapse;">
        <tr>
            <th style="width:10%; font-weight:bold;text-align:center;">' . $label_layout['s_no'] . '</th>
            <th style="width:25%; font-weight:bold;text-align:center;">' . $label_layout['reportpara'] . '</th>
            <th style="width:20%; font-weight:bold;text-align:center;">' . $label_layout['amount'] . '</th>
        </tr>';

        $counter = 1;
        $totalAmount = 0;

        foreach ($GetauditSlips as $item) {
            $amount = $item['amtinvolved'] ?? 0;

            $currentparacontent .= '<tr>';
            $currentparacontent .= '<td style="width:10%;text-align:center;">' . $counter . '</td>';
            $currentparacontent .= '<td style="width:25%;text-align:center;">' . str_pad($counter, 4, '0', STR_PAD_LEFT) . '</td>';
            $currentparacontent .= '<td style="text-align:right;">' . $this->formatIndianCurrency($amount) . '</td>';
            $currentparacontent .= '</tr>';

            $totalAmount += $amount;
            $counter++;
        }

        // Total row
        $currentparacontent .= '<tr>';
        $currentparacontent .= '<td colspan="2" style="text-align:right;font-weight:bold;">' . $label_layout['total_amt'] . '</td>';
        $currentparacontent .= '<td style="font-weight:bold;text-align:right;">' . $this->formatIndianCurrency($totalAmount) . '</td>';
        $currentparacontent .= '</tr>';

        $currentparacontent .= '</table></div>';

        return $currentparacontent;
    }

    private function generatePartAContent($scheduleId, $lang, $handlestatusflag, $spilloverflag, $instid, $financialyearcode)
    {
        $labelsJson = json_decode(file_get_contents(public_path('json/report.json')), true);
        $label = $labelsJson[$lang];

        $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
        $label_layout = $labelsJson_layout[$lang];

        $loaddata = $this->loadAllValues($scheduleId, $lang, $spilloverflag, $instid, $financialyearcode);

        // $AuditeeInstituteDetails = FormatModel::AuditeeUsers($scheduleId);
        $AuditeeInstituteDetails = FormatModel::AuditeeUsers($scheduleId, $spilloverflag, $instid, $financialyearcode);

        // b) Period of Audit Conducted
        $yearLabel = ($loaddata['deptcode'] == '01')
            ? 'Falsi Year'
            : 'Financial Year';
        if (($loaddata['deptcode'] == '01') && !empty($loaddata['annadhanamyearname'])) {
            $annadhanamyear = $loaddata['annadhanamyearname'];
            $annadhanam_content = $label['annadhanam_year_label'] . $annadhanamyear;
        } else {
            $annadhanam_content = '';
        }

        $yearName = $loaddata['yearname'];

        $entrydate = date('d-m-Y', strtotime($loaddata['entrymeetdate']));
        $exitmeet = date('d-m-Y', strtotime($loaddata['exitmeetdate']));

        if ($lang === 'en') {
            $intname = htmlspecialchars($loaddata['instename']);
            $yearLabel = ($loaddata['deptcode'] == '01')
                ? 'Fasli Year'
                : 'Financial Year';
            $extra = 'for';
        } else {
            $intname = htmlspecialchars($loaddata['insttname']);
            $yearLabel = ($loaddata['deptcode'] == '01')
                ? 'ஃபஸ்லி ஆண்டு'
                : 'நிதி ஆண்டு';
            $extra = '-';
        }

        $officialRows = '';
        $count = 1;

        foreach ($AuditeeInstituteDetails as $official) {
            $fromDate = !empty($official->service_fromdate) ? date('d-m-Y', strtotime($official->service_fromdate)) : '-';
            $toDate = !empty($official->service_todate) ? date('d-m-Y', strtotime($official->service_todate)) : 'Till Date';

            $officialRows .= '<tr>';
            $officialRows .= '<td>' . $count++ . '</td>';
            $officialRows .= '<td>' . htmlspecialchars($official->ofc_username) . ' , ' . htmlspecialchars($official->ofc_designation) . '</td>';
            $officialRows .= '<td>' . date('d-m-Y', strtotime($official->service_fromdate)) . ' to ' . date('d-m-Y', strtotime($official->service_todate)) . '</td>';
            $officialRows .= '</tr>';
        }

        // $parta_content = '
        // <div style="border: 2px solid black; padding: 20px; text-align: center; margin-bottom: 20px;">
        // <h3 style="margin: 0;">' . $label['audit_report_title'] . ' ' . $intname . ' ' . $extra . ' ' . $yearLabel . ' ' . htmlspecialchars($loaddata['yearname']) . '</h3>
        // </div>

        // <!-- a) Name of the Auditors -->
        // <div style="margin-bottom: 10px;">
        //     <table style="width: 100%; border: none; border-collapse: collapse;">
        //         <tr>
        //             <td style="width: 50%; font-weight: bold; border: none;">' . $label['name_of_auditors'] . '</td>
        //             <td style="width: 2%; border: none;"><b>:</b></td>
        //             <td style="width: 48%; border: none;"></td>
        //         </tr>';

        // $parta_content .= '</table></div>';
        // 🔹 Prepare team details HTML per language
        $teamDetailsHtml = '';
        $teamIndex = 0;

        // Count how many Teamdetails exist
        $teamCount = 0;
        while (isset($loaddata["Teamdetails{$teamCount}_en"])) {
            $teamCount++;
        }

        // Determine which key to use based on language
        $teamKeySuffix = ($lang === 'en') ? '_en' : '_ta';

        // Loop through all Teamdetails
        while (isset($loaddata["Teamdetails{$teamIndex}{$teamKeySuffix}"])) {
            $team = $loaddata["Teamdetails{$teamIndex}{$teamKeySuffix}"];
            $quarter = $loaddata["TeamQuarter{$teamIndex}"] ?? '-';

            $teamDetailsHtml .= '<div style="margin-bottom: 15px;">';

            // Only show "Team for Quarter X" if more than 1 team
            if ($teamCount > 1) {
                $teamDetailsHtml .= '<b>Team for ' . htmlspecialchars($quarter) . '</b><br>';
            }

            $teamDetailsHtml .= $team;
            $teamDetailsHtml .= '</div>';

            $teamIndex++;
        }

        // Bind $teamDetailsHtml into Part A
        $parta_content = '
            <div style="border: 2px solid black; padding: 20px; text-align: center; margin-bottom: 20px;">
 <h3 style="margin: 0;">' . $label['audit_report_title'] . ' ' . $intname . ' ' . $extra . ' ' . $yearLabel . ' ' . htmlspecialchars($loaddata['yearname']) . $annadhanam_content . '</h3>
            </div>

            <!-- a) Name of the Auditors -->
            <div style="margin-bottom: 10px;">
                <table style="width: 100%; border: none; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; font-weight: bold; border: none;">' . $label['name_of_auditors'] . '</td>
                        <td style="width: 2%; border: none;"><b>:</b></td>
                        <td style="width: 48%; border: none;">' . $teamDetailsHtml . '</td>
                    </tr>
                </table>
            </div>';

        $periodHtml = '';

        if ($teamCount > 1) {
            // 🔹 Multiple teams — show period per quarter
            for ($i = 0; $i < $teamCount; $i++) {
                $quarter = $loaddata["TeamQuarter{$i}"] ?? '-';
                $entrydate = $loaddata["TeamEntryMeet{$i}"] ?? '-';
                $exitmeet = $loaddata["TeamExitMeet{$i}"] ?? '-';

                // Format dates as d-m-Y
                if (!empty($entrydate) && $entrydate !== '-') {
                    $entrydate = date('d-m-Y', strtotime($entrydate));
                } else {
                    $entrydate = '-';
                }

                if (!empty($exitmeet) && $exitmeet !== '-') {
                    $exitmeet = date('d-m-Y', strtotime($exitmeet));
                } else {
                    $exitmeet = '-';
                }

                $periodHtml .= '<b>' . htmlspecialchars($quarter) . ':</b> ' . htmlspecialchars($entrydate) . ' to ' . htmlspecialchars($exitmeet) . '<br>';
            }
        } else {
            // 🔹 Single team — show single period
            $entrydate = $loaddata['TeamEntryMeet0'] ?? '-';
            $exitmeet = $loaddata['TeamExitMeet0'] ?? '-';

            if (!empty($entrydate) && $entrydate !== '-') {
                $entrydate = date('d-m-Y', strtotime($entrydate));
            } else {
                $entrydate = '-';
            }

            if (!empty($exitmeet) && $exitmeet !== '-') {
                $exitmeet = date('d-m-Y', strtotime($exitmeet));
            } else {
                $exitmeet = '-';
            }

            $periodHtml = htmlspecialchars($entrydate) . ' to ' . htmlspecialchars($exitmeet);
        }

        // Inject into parta_content
        $parta_content .= '
            <div style="margin-bottom: 10px;">
                <table style="width: 100%; border: none; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; font-weight: bold; border: none;">' . $label['period_of_audit'] . '</td>
                        <td style="width: 2%; border: none;"><b>:</b></td>
                        <td style="width: 48%; border: none;">' . $periodHtml . '</td>
                    </tr>
                </table>
            </div>';

        // $parta_content .= '
        // <div style="margin-bottom: 10px;">
        //     <p><strong>' . $label['officials_of_institution'] . '</strong></p>
        //     <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        //         <thead>
        //             <tr>
        //                 <th>' . $label['s_no'] . '</th>
        //                 <th>' . $label['name_desig'] . '</th>
        //                 <th>' . $label['service_period'] . '</th>
        //             </tr>
        //         </thead>
        //         <tbody>' . $officialRows . '</tbody>
        //     </table>
        // </div>';

        $parta_content .= '
<div style="margin-bottom: 10px;">
    <p><strong>' . $label['officials_of_institution'] . '</strong></p>';

        if ($AuditeeInstituteDetails->isNotEmpty()) {
            $parta_content .= '
    <table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr>
                <th>' . $label['s_no'] . '</th>
                <th>' . $label['name_desig'] . '</th>
                <th>' . $label['service_period'] . '</th>
            </tr>
        </thead>
        <tbody>' . $officialRows . '</tbody>
    </table>';
        } else {
            $parta_content .= '<p>No officials Details found</p>';
        }

        $parta_content .= '</div>';

        $Contentauthorityofaudit = DB::table('audit.map_authorityofaudit as auth')
            ->select(
                'auth.auth_content_en',
                DB::raw("
                    CASE
                        WHEN auth.deptcode IN ('02','03')
                        THEN auth.auth_content_ta
                        ELSE NULL
                    END AS auth_content_ta
                ")
            )
            ->where('auth.deptcode', $loaddata['deptcode'])
            ->where('auth.statusflag', 'Y')
            ->where(function ($q) use ($loaddata) {
                $q
                    ->where('auth.catcode', $loaddata['catcode'])
                    ->orWhere('auth.catcode', 'A');
            })
            ->where(function ($q) use ($loaddata) {
                $q
                    ->where('auth.subcatid', $loaddata['subcatcode'])
                    ->orWhere('auth.subcatid', 'A');
            })
            ->orderByRaw("
                CASE
                    WHEN auth.subcatid = ? THEN 1
                    WHEN auth.subcatid = 'A' THEN 2
                END
            ", [$loaddata['subcatcode']])
            ->first();

        $authorityofaudit_remarks = '';

        if ($Contentauthorityofaudit && !empty($Contentauthorityofaudit->auth_content_en)) {
            if ($lang === 'en') {
                $decoded = json_decode($Contentauthorityofaudit->auth_content_en);
            } else {
                $decoded = json_decode($Contentauthorityofaudit->auth_content_ta);
            }
            if (isset($decoded->content)) {
                $authorityofaudit_remarks = $decoded->content;
            }
        }

        $parta_content .= '<div class="page-break"></div><div style="margin-bottom: 10px;">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 100%; font-weight: bold; border: none;">' . $label['authority_of_audit'] . '</td>
                </tr>
                 <tr>
                    <td style="width: 100%; border: none;">' . $authorityofaudit_remarks . '</td>
                </tr>
            </table>
        </div>';

        $GenesisofAudit = DB::table('audit.report_insitutegenesis')
            ->where('scheduleid', $scheduleId)
            ->where('statusflag', $handlestatusflag)
            ->first();

        $genesis_remarks = json_decode($GenesisofAudit->genesis_remarks)->content;

        $parta_content .= '<div ></div><div style="margin-bottom: 10px;">
            <table style="width: 100%; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 100%; font-weight: bold; border: none;">' . $label['genesis_title'] . '</td>
                </tr>
                 <tr>
                    <td style="width: 100%; border: none;">' . $genesis_remarks . '</td>
                </tr>
            </table>
        </div>';

        $AccountDetails = DB::table('audit.report_accountdetails')
            ->where('auditscheduleid', $scheduleId)
            ->where('statusflag', $handlestatusflag)
            ->first();

        if ($AccountDetails->account_details == '[null]') {
            $parta_content .= '<div style="margin-bottom: 10px;">
        <br>
        <p>' . $label['accounts_section_title'] . '</p>
         <p><b>No account Details</b></p>';
        } else {
            $parta_content .= '<div style="margin-bottom: 10px;">
        <br>
        <p><b>' . $label['accounts_section_title'] . '</b></p>



        <p><b>' . $label['accounts_intro'] . '</b></p>

        <p>' . $label['accounts_para'] . '</p>

        <table style="width: 100%; border: none; border-collapse: collapse;">
            <tr>
                <th style="width:8%; font-weight: bold;text-align:center;">' . $label_layout['s_no'] . '</th>
                <th style="width:12%; font-weight: bold;text-align:center;">' . $label_layout['namescheme'] . '</th>
                <th style="width:12%; font-weight: bold;text-align:center;">' . $label_layout['bank'] . '</th>
                <th style="width:12%; font-weight: bold;text-align:center;">' . $label_layout['branch'] . '</th>
                <th style="width:12%; font-weight: bold;text-align:center;">' . $label_layout['bank_acc_no'] . "</th>
\t\t<th style=\"width:12%; font-weight: bold;text-align:center;\">" . $label_layout['account_type'] . '</th>
                <th style="width:5%; font-weight: bold;text-align:center;">' . $label_layout['cashbook'] . '<br>OB<br>(1)</th>
                <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['receipts'] . '<br>(2)</th>
                <th style="width:8%; font-weight: bold;text-align:center;">' . $label_layout['total'] . '<br>(3)<br>(1+2)</th>
                <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['expenditure'] . '<br>(4)</th>
                <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['cb_cashbook'] . '<br>(5)<br>(3 - 4)</th>
                <th style="width:8%; font-weight: bold;text-align:center;">' . $label_layout['cheq'] . '<br>' . $label_layout['Add'] . '<br>(6)</th>
                <th style="width:8%; font-weight: bold;text-align:center;">' . $label_layout['discheq'] . '<br>' . $label_layout['less'] . '<br>(7)</th>
                <th style="width:10%; font-weight: bold;text-align:center;">' . $label_layout['cb_passbook'] . '<br>(8)<br>(5+6-7)</th>
            </tr>';

            // Decode JSON fields
            $account_details = json_decode($AccountDetails->account_details ?? '{}', true);
            $bank_account_number = json_decode($AccountDetails->bank_account_number ?? '{}', true);
            $ob = json_decode($AccountDetails->ob ?? '{}', true);
            $account_type = json_decode($AccountDetails->accounttype ?? '{}', true);
            $receipts = json_decode($AccountDetails->receipts ?? '{}', true);
            $total = json_decode($AccountDetails->total ?? '{}', true);
            $expenditure = json_decode($AccountDetails->expenditure ?? '{}', true);
            $cb_cashbook = json_decode($AccountDetails->cb_cashbook ?? '{}', true);
            $add = json_decode($AccountDetails->add ?? '{}', true);
            $less = json_decode($AccountDetails->less ?? '{}', true);
            $cb_passbook = json_decode($AccountDetails->cb_passbook ?? '{}', true);
            $scheme = json_decode($AccountDetails->scheme ?? '{}', true);
            $branch = json_decode($AccountDetails->branch ?? '{}', true);

            // Determine keys
            $entryKeys = array_keys($account_details);
            if (empty($entryKeys)) {
                $entryKeys = [1];  // fallback default
            }
            $account_type = array_values($account_type);

            foreach ($entryKeys as $index => $key) {
                // Collect all values for the current row
                $values = [
                    $account_details[$key] ?? null,
                    $bank_account_number[$key] ?? null,
                    $ob[$key] ?? null,
                    $receipts[$key] ?? null,
                    $total[$key] ?? null,
                    $expenditure[$key] ?? null,
                    $cb_cashbook[$key] ?? null,
                    $add[$key] ?? null,
                    $less[$key] ?? null,
                    $cb_passbook[$key] ?? null,
                    $scheme[$key] ?? null,
                    $branch[$key] ?? null,
                ];

                // Check if all values are null or empty
                $allEmpty = true;
                foreach ($values as $value) {
                    if (!is_null($value) && $value !== '') {
                        $allEmpty = false;
                        break;
                    }
                }

                if ($allEmpty) {
                    continue;  // Skip this row
                }
                $accountTypeMap = [
                    '01' => 'Savings Account',
                    '02' => 'Current Account'
                ];

                // Add the row if not all values are empty
                $parta_content .= '
                <tr>
                    <td style="width:8%;">' . ($index + 1) . '</td>
                    <td style="width:12%;">' . ($scheme[$key] ?? '') . '</td>
                    <td style="width:12%;">' . ($account_details[$key] ?? '') . '</td>
                    <td style="width:12%;">' . ($branch[$key] ?? '') . '</td>
                    <td style="width:12%;">' . ($bank_account_number[$key] ?? '') . "</td>
\t\t    <td style=\"width:12%;\">" . ($accountTypeMap[$account_type[$key] ?? ''] ?? '') . '</td>
                    <td style="width:5%;">' . ($ob[$key] ?? '') . '</td>
                    <td style="width:10%;">' . ($receipts[$key] ?? '') . '</td>
                    <td style="width:8%;">' . ($total[$key] ?? '') . '</td>
                    <td style="width:10%;">' . ($expenditure[$key] ?? '') . '</td>
                    <td style="width:10%;">' . ($cb_cashbook[$key] ?? '') . '</td>
                    <td style="width:8%;">' . ($add[$key] ?? '') . '</td>
                    <td style="width:8%;">' . ($less[$key] ?? '') . '</td>
                    <td style="width:10%;">' . ($cb_passbook[$key] ?? '') . '</td>
                </tr>';
            }
        }

        $PANTAN_Details = DB::table('audit.report_pantan')
            ->where('auditscheduleid', $scheduleId)
            ->where('statusflag', $handlestatusflag)
            ->first();

        // $itfiling_remaks= json_decode($PANTAN_Details->itfiling_issue)->content;

        $itfiling_remaks = '';
        if (!empty($PANTAN_Details->itfiling_issue)) {
            $raw = $PANTAN_Details->itfiling_issue;
            $itfiling_remaks = json_decode($PANTAN_Details->itfiling_issue)->content;
            $decoded = json_decode($raw);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_object($decoded) && isset($decoded->content)) {
                    $itfiling_remaks = $decoded->content;
                } elseif (is_string($decoded)) {
                    // This means it was a quoted string in JSON, like: "<p>text</p>"
                    $itfiling_remaks = $decoded;
                }
            } else {
                // Fallback in case it's not even valid JSON
                $itfiling_remaks = $raw;
            }
        }

        $legalcomplaince_remaks = json_decode($PANTAN_Details->legal_complaince)->content;
        $financialreview_remaks = json_decode($PANTAN_Details->financial_review)->content;
        $parta_content .= '</table>

            <p><center><b >' . $label['filing_status_title'] . '</b></center></p>
             <p><b>' . $label_layout['tdsdetails'] . '</b></p>';
        $tdsFiledData = DB::table('audit.report_tds_filed_details')
            ->where('auditscheduleid', $scheduleId)
            ->where('statusflag', 'Y')
            ->get();
        if (!$tdsFiledData->isEmpty()) {
            $tableHtml = '
                    <table border="1" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="width:19%;text-align:center;" class="lang">' . $label_layout['financeyr'] . '</th>
                                <th style="width:19%;text-align:center;" class="lang">' . $label_layout['section'] . '</th>
                                <th style="width:19%;text-align:center;" class="lang">' . $label_layout['period_label'] . '</th>
                                <th style="width:19%;text-align:center;" class="lang1">' . $label_layout['remittance'] . '</th>
                                <th style="width:19%;text-align:center;" class="lang1">' . $label_layout['returnfiled'] . '</th>
                            </tr>
                        </thead>
                        <tbody>';

            foreach ($tdsFiledData as $row) {
                $tableHtml .= '
                        <tr>
                            <td style="text-align:center;">' . htmlspecialchars($row->audityear) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($row->filing_status) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($row->auditquarter) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($row->remit_on_time) . '</td>
                            <td style="text-align:center;">' . htmlspecialchars($row->returns_filed) . '</td>
                        </tr>';
            }

            $tableHtml .= '
                        </tbody>
                    </table>';
        } else {
            $tableHtml = '<p>' . $label['no_tds'] . '</p>';
        }

        $parta_content .= $tableHtml . '<p><b>' . $label_layout['itfill'] . '</b></p>
             <p>' . $itfiling_remaks . '</p>

             <p><b>' . $label_layout['gstreturn'] . '</b></p>';
        $Report_GST = DB::table('audit.report_gstreturn_details')
            ->where('auditscheduleid', $scheduleId)
            ->first();

        $gstdata = [
            'audit_year' => $Report_GST->audityear,
            'q1' => json_decode($Report_GST->det_q1, true),
            'q2' => json_decode($Report_GST->det_q2, true),
            'q3' => json_decode($Report_GST->det_q3, true),
            'q4' => json_decode($Report_GST->det_q4, true),
        ];

        $quarters = ['q1', 'q2', 'q3', 'q4'];
        $gst_audityear = $gstdata['audit_year'] ?? '2024 -2025';

        $tableHtml = '
 <b>' . $label_layout['financeyr'] . '- ' . $gst_audityear . '</b>
             <table border="1" style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="text-align:center;">' . $label_layout['period_label'] . '</th>
                        <th style="text-align:center;">' . $label_layout['remittance_ontime'] . '</th>
                        <th style="text-align:center;">' . $label_layout['duedatebefore'] . '</th>
                    </tr>
                </thead>
                <tbody>';
        $yesNoMap = [
            'en' => ['yes' => 'Yes', 'no' => 'No'],
            'ta' => ['yes' => 'ஆம்', 'no' => 'இல்லை']
        ];
        foreach ($quarters as $q) {
            $qUpper = strtoupper($q);
            // $remit = $gstdata[$q]['remit'] ?? '';
            // $filed = $gstdata[$q]['filed'] ?? '';

            // Normalize inputs
            $remitRaw = strtolower(trim($gstdata[$q]['remit'] ?? ''));
            $filedRaw = strtolower(trim($gstdata[$q]['filed'] ?? ''));

            // Fallback if value not recognized
            $translatedRemit = $yesNoMap[$lang][$remitRaw] ?? $gstdata[$q]['remit'] ?? '';
            $translatedFiled = $yesNoMap[$lang][$filedRaw] ?? $gstdata[$q]['filed'] ?? '';

            // print_r($translatedRemit);
            // exit;
            $tableHtml .= '
                    <tr>
                        <td style="text-align:center;">' . $qUpper . '</td>
                        <td style="text-align:center;">' . $translatedRemit . '</td>
                        <td style="text-align:center;">' . $translatedFiled . '</td>
                    </tr>';
        }

        $tableHtml .= '
                </tbody>
            </table>';

        $parta_content .= $tableHtml;

        $Report_lWF = DB::table('audit.report_labourwelfarefund')
            ->where('auditscheduleid', $scheduleId)
            ->first();

        if ($Report_lWF) {
            $tableHtml = '
                        <br><div style="margin-bottom: 10px;">
                            <b>' . $label_layout['labour_heading'] . '</b>
                        </div>
                        <table border="1" cellpadding="5" style="width:100%; border-collapse: collapse; font-size: 12px;">
                            <thead>
                                <tr style="background-color: #f2f2f2;">
                                    <th style="text-align:center;">' . $label_layout['s_no'] . '</th>
                                    <th style="text-align:center;">' . $label_layout['details'] . '</th>
                                    <th style="text-align:center;">' . $label_layout['remarks'] . '</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="text-align:center;">1</td>
                                    <td>' . $label_layout['estimate_deduct'] . '</td>
                                    <td style="text-align:center;">' . $Report_lWF->lwfq1_remarks . '</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">2</td>
                                    <td>' . $label_layout['nodeduct'] . '</td>
                                    <td>' . $Report_lWF->lwfq2_remarks . '</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">3</td>
                                    <td>' . $label_layout['lwf_collect'] . '</td>
                                    <td style="text-align:center;">' . $Report_lWF->lwfq3_remarks . '</td>
                                </tr>
                                <tr>
                                    <td style="text-align:center;">4</td>
                                    <td>' . $label_layout['shortfall'] . '</td>
                                    <td style="text-align:center;">' . $Report_lWF->lwfq4_remarks . '</td>
                                </tr>
                            </tbody>
                        </table>';

            $parta_content .= $tableHtml;
        }

        $parta_content .= '<p><b>' . $label_layout['legal'] . '</b></p>
             <p>' . $legalcomplaince_remaks . '</p>

             <p><b>' . $label_layout['financial_review'] . '</b></p>
             <p>' . $financialreview_remaks . '</p>



            ';

        return $parta_content;
    }

    private function generatePartCContent($scheduleId, $lang)
    {
        $partc_content = '<h1 style="text-align:center;">PART - III</h1>';

        $partc_content .= '<p><b>1) List of Annexures</b></p>';

        return $partc_content;
    }

    // private function GistAuditObjections($auditscheduleid, $GetauditSlips, $lang)
    // {
    //     $labels = $this->loadlabels();
    //     $GetauditSlips = $GetauditSlips->getData()->data;
    //     if ($lang === 'en') {
    //         $html = '<h3>GIST OF AUDIT OBJECTIONS</h3>';

    //         $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
    //         $html .= '<thead>';
    //         $html .= '<tr>';
    //         $html .= '<th>S.No</th>';
    //         $html .= '<th>Para No</th>';
    //         $html .= '<th>Details of Observation</th>';
    //         $html .= '<th>Amount</th>';
    //         // $html .= '<th>Page No</th>';
    //         $html .= '</tr>';
    //         $html .= '</thead>';
    //         $html .= '<tbody>';
    //     } else {
    //         $html = '<h3>தணிக்கை ஆட்சேபனைகளின் சுருக்கம்</h3>';

    //         $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
    //         $html .= '<thead>';
    //         $html .= '<tr>';
    //         $html .= '<th>வ.எண்</th>';
    //         $html .= '<th>பாரா எண்</th>';
    //         $html .= '<th>கவனிப்பு விவரங்கள்</th>';
    //         $html .= '<th>தொகை</th>';
    //         // $html .= '<th>பக்க எண்</th>';
    //         $html .= '</tr>';
    //         $html .= '</thead>';
    //         $html .= '<tbody>';
    //     }
    //     $count = 1;

    //     $OrderingSlips = DB::table('audit.report_storesliporder')
    //         ->where('auditscheduleid', $auditscheduleid)
    //         ->select('ser_ordered_slips', 'nonser_ordered_slips')
    //         ->first();

    //     // Step 1: Decode both ordered slip JSONs
    //     $seriousOrdered = isset($OrderingSlips->ser_ordered_slips)
    //         ? json_decode($OrderingSlips->ser_ordered_slips, true)
    //         : [];

    //     $nonSeriousOrdered = isset($OrderingSlips->nonser_ordered_slips)
    //         ? json_decode($OrderingSlips->nonser_ordered_slips, true)
    //         : [];

    //     // Step 2: Merge serious first, then non-serious
    //     $orderedArray = array_merge($seriousOrdered, $nonSeriousOrdered);

    //     // Step 2: Reorder the $GetauditSlips collection
    //     $orderedSlips = [];

    //     if (!empty($orderedArray)) {
    //         $lookup = collect($GetauditSlips)->keyBy('auditslipid');

    //         foreach ($orderedArray as $pos => $slipId) {
    //             if ($lookup->has($slipId)) {
    //                 $orderedSlips[] = $lookup[$slipId];
    //             }
    //         }

    //         // Optional: Append any slips not in the order
    //         $remaining = collect($GetauditSlips)->whereNotIn('auditslipid', $orderedArray);
    //         foreach ($remaining as $item) {
    //             $orderedSlips[] = $item;
    //         }
    //     } else {
    //         $orderedSlips = $GetauditSlips;
    //     }

    //     $totalAmount = 0;  // Initialize total

    //     // Step 3: Generate the HTML table rows
    //     foreach ($orderedSlips as $item) {
    //         $html .= '<tr>';
    //         $html .= '<td>' . $count . '</td>';
    //         $html .= '<td>' . str_pad($count, 4, '0', STR_PAD_LEFT) . '</td>';
    //         $html .= '<td>'
    //             . htmlspecialchars($lang === 'ta' ? $item->objectiontname : $item->objectionename)
    //             . ' - ' . htmlspecialchars($item->slipdetails)
    //             . '</td>';
    //         $html .= '<td style="text-align:right;">' . htmlspecialchars($this->formatIndianCurrency($item->amtinvolved)) . '</td>';
    //         $html .= '</tr>';

    //         $totalAmount += $item->amtinvolved;  // Add to total
    //         $count++;
    //     }

    //     // Add total row
    //     $html .= '<tr>';
    //     $html .= '<td colspan="3" style="text-align:right;font-weight:bold;">'
    //         . ($lang === 'ta' ? 'மொத்தத் தொகை' : 'Total Amount Involved')
    //         . '</td>';
    //     $html .= '<td style="font-weight:bold;text-align:right;">' . htmlspecialchars($this->formatIndianCurrency($totalAmount)) . '</td>';
    //     $html .= '</tr>';

    //     $html .= '</tbody>';
    //     $html .= '</table>';

    //     return $html;
    // }

    private function GistAuditObjections($auditscheduleid, $GetauditSlips, $lang)
    {
        $labels = $this->loadlabels();

        // Decode JSON response safely
        $response = json_decode($GetauditSlips->getContent(), true);
        $GetauditSlips = $response['data'] ?? [];

        if ($lang === 'en') {
            $html = '<h3>GIST OF AUDIT OBJECTIONS</h3>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            $html .= '<thead>
                    <tr>
                        <th>S.No</th>
                        <th>Para No</th>
                        <th>Details of Observation</th>
                        <th>Amount</th>
                    </tr>
                  </thead><tbody>';
        } else {
            $html = '<h3>தணிக்கை ஆட்சேபனைகளின் சுருக்கம்</h3>';
            $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse; width: 100%;">';
            $html .= '<thead>
                    <tr>
                        <th>வ.எண்</th>
                        <th>பாரா எண்</th>
                        <th>கவனிப்பு விவரங்கள்</th>
                        <th>தொகை</th>
                    </tr>
                  </thead><tbody>';
        }

        $count = 1;
        $totalAmount = 0;

        foreach ($GetauditSlips as $item) {
            $amount = $item['amtinvolved'] ?? 0;

            $html .= '<tr>';
            $html .= '<td>' . $count . '</td>';
            $html .= '<td>' . str_pad($count, 4, '0', STR_PAD_LEFT) . '</td>';

            $html .= '<td>'
                . htmlspecialchars($lang === 'ta' ? $item['objectiontname'] : $item['objectionename'])
                . ' - '
                . htmlspecialchars($item['slipdetails'])
                . '</td>';

            $html .= '<td style="text-align:right;">'
                . htmlspecialchars($this->formatIndianCurrency($amount))
                . '</td>';

            $html .= '</tr>';

            $totalAmount += $amount;
            $count++;
        }

        // Total row
        $html .= '<tr>';
        $html .= '<td colspan="3" style="text-align:right;font-weight:bold;">'
            . ($lang === 'ta' ? 'மொத்தத் தொகை' : 'Total Amount Involved')
            . '</td>';
        $html .= '<td style="font-weight:bold;text-align:right;">'
            . htmlspecialchars($this->formatIndianCurrency($totalAmount))
            . '</td>';
        $html .= '</tr>';

        $html .= '</tbody></table>';

        return $html;
    }

    private function formatIndianCurrency($amount)
    {
        $intPart = (int) round($amount);  // Remove decimals
        $lastThree = substr($intPart, -3);
        $restUnits = substr($intPart, 0, -3);

        if ($restUnits != '') {
            $restUnits = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $restUnits);
            return $restUnits . ',' . $lastThree;
        } else {
            return $lastThree;
        }
    }

    private function AuditSlipLoadPDF($auditscheduleid, $GetauditSlips, $lang, $mpdf, $seriousLastSlipNO, $sertype)
    {
        $labels = $this->loadlabels();
        // $slipPageMap = [];

        $amountinvolved_label = $labels[$lang]['amount_involved'];
        $severity_label = $labels[$lang]['severity'];
        $liability_label = $labels[$lang]['liability'];
        $slip_details_label = $labels[$lang]['slip_details'];
        $slip_details_headinglabel = $labels[$lang]['slipdetails_label'];
        $auditordetails = $labels[$lang]['auditordetails_heading'];

        $severitylow = $labels[$lang]['severity_low'];
        $severitymedium = $labels[$lang]['severity_medium'];
        $severityhigh = $labels[$lang]['severity_high'];

        $SeverityArr = ['L' => $severitylow, 'M' => $severitymedium, 'H' => $severityhigh];
        $liabilityarr = ['Y' => $labels[$lang]['yes'], 'N' => $labels[$lang]['no']];

        $liability = $GetauditSlips->getData()->liability;
        $GetauditSlips = $GetauditSlips->getData()->data;

        // Group slips by irregularity category and subcategory
        $groupedByIrregularity = [];
        $groupedByLiability = [];

        foreach ($GetauditSlips as $item) {
            $cat = $item->irregularitiescatelname;
            $subcat = $item->irregularitiessubcatelname;
            $groupedByIrregularity[$cat][$subcat][$item->mainslipnumber][] = $item;
        }

        // Group liabilities by mainslip
        foreach ($liability as $LiabilityItem) {
            $groupedByLiability[$LiabilityItem->mainslipnumber][] = $LiabilityItem;
        }

        $html = '';

        $X = 1;  // Start serial number outside all loops
        $currentSlip = 1;  // Track current slip number

        if ($sertype == 'nonser') {
            $X = $seriousLastSlipNO;
        }

        foreach ($groupedByIrregularity as $catCode => $subCats) {
            foreach ($subCats as $subCatCode => $slipGroups) {
                foreach ($slipGroups as $mainslipNumber => $items) {
                    $title = ($lang == 'ta')
                        ? '#' . $mainslipNumber . '-' . $slip_details_headinglabel
                        : $slip_details_headinglabel . ' #' . $mainslipNumber;

                    $html .= "<a name='slip-{$mainslipNumber}'></a>";  // Anchor for referencing later

                    // Capture the current page for TOC
                    // $slipPageMap[$mainslipNumber] = $mpdf->PageNo();

                    foreach ($items as $auditSlip) {
                        // Add page break *before* second slip onwards
                        if ($currentSlip > 1) {
                            $html .= '<div style="page-break-before:after;">';
                        }

                        $auditslipdetails = !empty($auditSlip->final_slipdetails) ? $auditSlip->final_slipdetails : $auditSlip->slipdetails;

                        $objectionname = ($lang == 'ta') ? $auditSlip->objectiontname : $auditSlip->objectionename;

                        $html .= "<a name='slip-{$mainslipNumber}'></a>";
                        $html .= "<p style='margin-bottom: 4px;'><strong>$X) $objectionname</strong></p>";
                        $html .= "<div style=\"font-family: 'nototamil', 'DejaVu Sans', sans-serif; border: 1px solid #222; padding: 8px; background-color: #e4e4e4; margin-bottom: 4px;\">{$auditslipdetails}</div>";

                        $auditslipremarks = !empty($auditSlip->final_remarks) ? $auditSlip->final_remarks : $auditSlip->sliphistory_remarks;

                        if (!empty($auditslipremarks)) {
                            $auditorRemarks = json_decode($auditslipremarks);

                            if (
                                json_last_error() === JSON_ERROR_NONE &&
                                is_object($auditorRemarks) &&
                                isset($auditorRemarks->content)
                            ) {
                                $auditorContent = $auditorRemarks->content;
                            } else {
                                $auditorContent = $auditslipremarks;
                            }

                            $auditorContent = is_string($auditorContent) ? $auditorContent : '';
                            $auditorContent = $this->cleanForMPDF($auditorContent);

                            if (trim($auditorContent) !== '') {
                                $html .= "<div style='margin-bottom: 6px; font-size: 12pt;'>$auditorContent</div>";
                            } else {
                                $html .= "<p style='margin-bottom: 6px;'>No Remarks Available</p>";
                            }
                        } else {
                            $html .= "<p style='margin-bottom: 6px;'>No Remarks Available</p>";
                        }

                        // ✅ Keep liability section as is, since it comes immediately after remarks
                        $Liability = $groupedByLiability[$mainslipNumber] ?? null;
                        if ($Liability) {
                            $html .= "<h4 style='text-align: center;'>Liability Details</h4>";
                            $html .= "<table border='1' cellpadding='5' cellspacing='0' width='100%' style='border-collapse: collapse;'>";
                            $html .= '<thead>
                                            <tr>
                                                <th>Liability Name</th>
                                                <th>GPF/CPS/Other Number</th>
                                                <th>Designation</th>
                                                <th>Amount Involved</th>
                                            </tr>
                                        </thead><tbody>';

                            foreach ($Liability as $LiabilityVal) {
                                $identificationNumber = $LiabilityVal->liabilitygpfno;
                                $prefix = match ($LiabilityVal->notype) {
                                    1 => 'GPF No: ',
                                    2 => 'CF No: ',
                                    3 => 'IHRMS No: ',
                                    default => '',
                                };

                                $html .= "<tr>
                                                <td>{$LiabilityVal->liabilityname}</td>
                                                <td>{$prefix}{$identificationNumber}</td>
                                                <td>{$LiabilityVal->liabilitydesignation}</td>
                                                <td>{$LiabilityVal->liabilityamount}</td>
                                            </tr>";
                            }

                            $html .= '</tbody></table>';
                        }

                        if ($currentSlip > 1) {
                            $html .= '</div>';
                        }

                        $currentSlip++;
                        $X++;  // Next serial number
                    }
                }
            }
        }

        return [
            'html' => $html,
            // 'slipPages' => $slipPageMap,
            'seriousno' => $X
        ];
    }

    public function previewWordFileTest()
    {
        try {
            $phpWord = new \PhpOffice\PhpWord\PhpWord();

            $phpWord->addFontStyle('TamilStyle', [
                'name' => 'Nirmala UI',  // Use a Tamil Unicode font
                'size' => 16,
                'bold' => true
            ]);

            // Add a new section
            $section = $phpWord->addSection();

            // Add Tamil text with a Unicode-compatible font
            $section->addText('???????, ??? ??? ??????????? ???????????!', 'TamilStyle');

            // Save the Word file
            // $fileName = 'TamilPreview.docx';
            $fileName = 'AuditReport_' . Carbon::now()->format('Y_m_d_H_i_s') . '.docx';

            $filePath = public_path('files/' . $fileName);
            $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($filePath);

            // Generate HTML Preview
            $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
            ob_start();
            $htmlWriter->save('php://output');
            $htmlContent = ob_get_clean();

            return response()->json([
                'res' => 'success',
                'html' => $htmlContent,  // Send the HTML content
                'filename' => $fileName  // Send the generated filename for download
            ]);
        } catch (\Exception $e) {
            // Log the error and return a response
            \Log::error('Error in previewWordFile: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function previewWordFile()
    {
        // echo 'hi';
        // echo  $_GET['instid'];
        // echo  $_GET['spilloverflag'];

        $lang = $_GET['lang'];

        $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
        $label_layout = $labelsJson_layout[$lang];
        ob_end_clean();
        ob_start();

        $scheduleId = $_GET['scheduleid'];
        $spilloverflag = $_GET['spilloverflag'];
        $instid = $_GET['instid'];
        $financialyearcode = $_GET['financialyearcode'];


        $whichpart = $_GET['whichpart'];

        $handlestatusflag = 'Y';



        if ($whichpart == 'all') {
            $handlestatusflag = 'F';
        }

        $chargeData = session('charge');
        $userData = session('user');

        $session_userid = $userData->userid;


        if (!$scheduleId) {
            return response()->json(['res' => 'nodata']);
        }





        $WorkingOfficeGet = FormatModel::GetSchedultedEventDetails($scheduleId, $spilloverflag, $instid, $financialyearcode);

        $WorkingOfficeGet = $WorkingOfficeGet[0];
        // auditquarter

        $auditeamid = $WorkingOfficeGet->auditteamid;

        // Language setup
        if ($lang == 'ta') {
            $DeptName = $WorkingOfficeGet->depttlname;
            $InstituteName = $WorkingOfficeGet->insttname;
            $TypeofAudit = $WorkingOfficeGet->typeofaudittname;
            $DistName = $WorkingOfficeGet->disttname . ' மாவட்டம்';
            $fontName = 'Latha';
            $defaultsize = 8;
            $AuditReport_Text = 'தணிக்கை அறிக்கை';
            $AuditReport_Year = 'ஆண்டு';
            $deptsize = '16';
            $InstNameSize = '22';
        } else {

            $DeptName = $WorkingOfficeGet->deptelname;
            $InstituteName = $WorkingOfficeGet->instename;
            $TypeofAudit = $WorkingOfficeGet->typeofauditename;
            $DistName = $WorkingOfficeGet->distename . ' District';
            $fontName = 'Latha';
            $defaultsize = 13;
            $AuditReport_Text = 'Audit Report';
            $AuditReport_Year = 'Year of';
            $deptsize = '24';
            $InstNameSize = '26';
        }

        $FinancialYear = $WorkingOfficeGet->yearname;

        $deptcode = $WorkingOfficeGet->deptcode;


        if ($deptcode == '01') {
            $auditEndDate = '30/06/2025';
        } else {
            // Split the years
            $years = explode(',', $FinancialYear);

            // Get the last year range
            $lastYearRange = trim(end($years)); // e.g. "2023-2024"

            // Split the range into start and end years
            $yearParts = explode('-', $lastYearRange);

            // Use the second part as the end year
            $endYear = isset($yearParts[1]) ? trim($yearParts[1]) : null;

            if ($endYear) {
                $auditEndDate = "31/03/$endYear";
                //echo "Audit End Date: $auditEndDate";
            }
        }



        if ($whichpart == 'part_a' || $whichpart == 'all') {

            $auditCertificate = DB::table('audit.report_auditcertificate')
                ->where('scheduleid', $scheduleId)
                ->where('statusflag', $handlestatusflag)
                ->first();



            $cer_type_code = $auditCertificate->cer_type_code;


            $MasterauditCertificate = DB::table('audit.mst_auditcertificatetype')
                ->where('cer_type_code', $cer_type_code)
                ->first();

            $Master_cer_content = json_decode($MasterauditCertificate->cer_content)->content;
            $Master_cer_content = str_replace('[audityear]', $auditEndDate, $Master_cer_content);
            $certypetext = $MasterauditCertificate->cer_ename;
        }



        if ($whichpart == 'all') {

            $imagePath = public_path('site/image/tn__logo.png');
            $imageData = base64_encode(file_get_contents($imagePath));
            $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
            $base64Image = 'data:image/' . $imageType . ';base64,' . $imageData;

            $annadhanamyearcontent = $this->getFirstPageAnnadhanamYearContent($WorkingOfficeGet->deptcode ?? '', $lang, $WorkingOfficeGet->annadhanamyearname ?? '');
            $htmlContent = $this->generateFirstPageHtml($deptsize, $DeptName, $base64Image, $DistName, $InstituteName, $FinancialYear, $AuditReport_Year, $AuditReport_Text, $annadhanamyearcontent);
            $mpdfContent = $htmlContent;



            $coveringletter = $this->coveringletter($scheduleId, $lang, $certypetext, $auditEndDate, $spilloverflag, $instid, $financialyearcode);
            $loadcoveringletter = '<div class="page-break"></div><div class="section-content">' . $coveringletter . '</div>';
            $mpdfContent .= $loadcoveringletter;
        } else {
            // Load CSS file
            $templatePath = resource_path('views/pdf/style.css');
            $stylesheet = File::get($templatePath);

            // Inline CSS in <style> tag
            $css = "<style>$stylesheet</style>";

            $mpdfContent  = $css;
        }




        if ($whichpart == 'part_a' || $whichpart == 'all') {
            $auditcertificate_remarks = json_decode($auditCertificate->cer_remarks)->content;

            $partacontents = '
                <div>
                    <h1 style="text-align:center; font-size: 36px;">PART - I</h1>
                </div>
                ';

            // Page break to move to next page
            $partacontents .= '<pagebreak />';

            // Page 2: Audit Certificate content (normal alignment)
            $partacontents .= '
                <div >
                    <h3 style="text-align:center;">AUDIT CERTIFICATE</h3><p>' . $Master_cer_content . '</p>
                </div> ';

            if ($cer_type_code !== '01') {
                //$partacontents .='<h3 style="text-align:center;">Remarks of Audit Certificate with '.$MasterauditCertificate->cer_ename.'</h3>';
                $partacontents .= $auditcertificate_remarks;
            }


            $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partacontents . '</div>';
        }



         $GetauditSlips = FormatModel::FetchAuditSlips($scheduleId, '01', $spilloverflag, $instid, $financialyearcode);
        $slipResultSerious = $this->AuditSlipLoadPDF($scheduleId, $GetauditSlips, $lang, '', '', 'ser');
        //$slipPageMapSerious = $slipResultSerious['slipPages'];

        $seriousLastSlipNO = $slipResultSerious['seriousno'];

        $GetauditSlips = FormatModel::FetchAuditSlips($scheduleId, '02', $spilloverflag, $instid, $financialyearcode);
        $slipResultNonSerious = $this->AuditSlipLoadPDF($scheduleId, $GetauditSlips, $lang, '', $seriousLastSlipNO, 'nonser');
        // $slipPageMapNonSerious = $slipResultNonSerious['slipPages'];


           $FetchGistObjections = FormatModel::FetchGistObjections($scheduleId, $spilloverflag, $instid, $financialyearcode);

        if ($whichpart == 'all') {

            $GistAuditObjections = $this->GistAuditObjections($scheduleId, $FetchGistObjections, $lang);
            $mpdfContent .= ' <div class="page-break"></div><div class="section-content">' . $GistAuditObjections . '</div>';
        }

        // $spilloverflag = $_GET['spilloverflag'];
        // $instid = $_GET['instid'];
        // $financialyearcode = $_GET['financialyearcode'];



        // $teamDetailsHtml = '';
        // $teamIndex = 0;

        // // Count how many Teamdetails exist
        // $teamCount = 0;
        // while (isset($loaddata["Teamdetails{$teamCount}_en"])) {
        //     $teamCount++;
        // }

        // // Determine which key to use based on language
        // $teamKeySuffix = ($lang === 'en') ? '_en' : '_ta';

        // // Loop through all Teamdetails
        // while (isset($loaddata["Teamdetails{$teamIndex}{$teamKeySuffix}"])) {
        //     $team = $loaddata["Teamdetails{$teamIndex}{$teamKeySuffix}"];
        //     $quarter = $loaddata["TeamQuarter{$teamIndex}"] ?? '-';

        //     $teamDetailsHtml .= '<div style="margin-bottom: 15px;">';

        //     // Only show "Team for Quarter X" if more than 1 team
        //     if ($teamCount > 1) {
        //         $teamDetailsHtml .= '<b>Team for ' . htmlspecialchars($quarter) . '</b><br>';
        //     }

        //     $teamDetailsHtml .= $team;
        //     $teamDetailsHtml .= '</div>';

        //     $teamIndex++;
        // }


        // // Bind $teamDetailsHtml into Part A
        // $parta_content = '
        //     <div style="border: 2px solid black; padding: 20px; text-align: center; margin-bottom: 20px;">
        //         <h3 style="margin: 0;">' . $label['audit_report_title'] . ' ' . $intname . ' ' . $extra . ' ' . $yearLabel . ' ' . htmlspecialchars($loaddata['yearname']) . '</h3>
        //     </div>

        //     <!-- a) Name of the Auditors -->
        //     <div style="margin-bottom: 10px;">
        //         <table style="width: 100%; border: none; border-collapse: collapse;">
        //             <tr>
        //                 <td style="width: 50%; font-weight: bold; border: none;">' . $label['name_of_auditors'] . '</td>
        //                 <td style="width: 2%; border: none;"><b>:</b></td>
        //                 <td style="width: 48%; border: none;">' . $teamDetailsHtml . '</td>
        //             </tr>
        //         </table>
        //     </div>';




        // $periodHtml = '';
        // if ($teamCount > 1) {
        //     // Multiple teams -> show each quarter
        //     for ($i = 0; $i < $teamCount; $i++) {
        //         $quarter   = $loaddata["TeamQuarter{$i}"] ?? '-';
        //         $entrydate = $loaddata["TeamEntryMeet{$i}"] ?? '-';
        //         $exitmeet  = $loaddata["TeamExitMeet{$i}"] ?? '-';

        //         $periodHtml .= '<b> ' . htmlspecialchars($quarter) . ':</b> ' . htmlspecialchars($entrydate) . ' to ' . htmlspecialchars($exitmeet) . '<br>';
        //     }
        // } else {
        //     // Single team -> show single period
        //     $entrydate = $loaddata["TeamEntryMeet0"] ?? '-';
        //     $exitmeet  = $loaddata["TeamExitMeet0"] ?? '-';
        //     $periodHtml = htmlspecialchars($entrydate) . ' to ' . htmlspecialchars($exitmeet);
        // }

        // // Inject into parta_content
        // $parta_content .= '
        //     <div style="margin-bottom: 10px;">
        //         <table style="width: 100%; border: none; border-collapse: collapse;">
        //             <tr>
        //                 <td style="width: 50%; font-weight: bold; border: none;">' . $label['period_of_audit'] . '</td>
        //                 <td style="width: 2%; border: none;"><b>:</b></td>
        //                 <td style="width: 48%; border: none;">' . $periodHtml . '</td>
        //             </tr>
        //         </table>
        //     </div>';


        if ($whichpart == 'part_a' || $whichpart == 'all') {

            $partacontents = $this->generatePartAContent($scheduleId, $lang, $handlestatusflag, $spilloverflag, $instid, $financialyearcode);

            // print_r($partacontents);


            $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partacontents . '</div>';
        }


        if ($whichpart == 'part_b_seriousirregularities' || $whichpart == 'all') {


            if ($slipResultSerious['html']) {
                // $SeriousSectionHeading = '<div>
                //                         <h1 style="text-align:center; font-size: 36px;">PART - II</h1>
                //                         <h2 style="text-align:center;">Serious Irregularities</h2>
                //                     </div>';
                $SeriousSectionHeading = '<div>
                                            <h1 style="text-align:center; font-size: 36px;">' . $label_layout['part_b_prefill'] . '</h1>
                                            <h2 style="text-align:center;">' . $label_layout['serious_reg'] . '</h2>
                                        </div>';
                //  $SeriousSectionHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                //                                             <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_b_prefill'] . '</h1>
                //                                             <h2 style="text-align:center;">' . $label_layout['serious_reg'] . '</h2>
                //                                         </div>';

                $mpdfContent .= '<div style="page-break-before: always;">' . $SeriousSectionHeading . '</div>';

                $SeriousSection = $slipResultSerious['html'];

                $mpdfContent .= '<div style="page-break-before: always;">' . $SeriousSection . '</div>';
            }
        }


        if ($whichpart == 'part_b_nonseriousirregularities' || $whichpart == 'all') {

            // Now for non-serious irregularities
            if ($slipResultNonSerious['html']) {
                // $nonSeriousSectionHeading = '<div>
                //                         <h1 style="text-align:center; font-size: 36px;">PART - II</h1>
                //                         <h2 style="text-align:center;">Non Serious Irregularities</h2>
                //                     </div>';
                $nonSeriousSectionHeading = '<div>
                                            <h1 style="text-align:center; font-size: 36px;">' . $label_layout['part_b_prefill'] . '</h1>
                                            <h2 style="text-align:center;">' . $label_layout['nonserious_reg'] . '</h2>
                                        </div>';
                // $nonSeriousSectionHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                //                         <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_b_prefill'] . '</h1>
                //                         <h2 style="text-align:center;">' . $label_layout['nonserious_reg'] . '</h2>
                //                     </div>';
                $mpdfContent .= '<div style="page-break-before: always;">' . $nonSeriousSectionHeading . '</div>';

                $nonSeriousSection = $slipResultNonSerious['html'];

                $mpdfContent .= '<div style="page-break-before: always;">' . $nonSeriousSection . '</div>';
            }
        }

        if ($whichpart == 'part_b_others' || $whichpart == 'all') {

            $auditfees_content = '<h3 style="text-align:center;">AUDIT FEES / AUDIT LEVY CERTIFICATE</h3>';


            $auditfeesDetails = DB::table('audit.report_auditlevycertificate')
                ->where('scheduleid', $scheduleId)
                ->where('statusflag', $handlestatusflag)
                ->first();

            $auditlevy_remarks = json_decode($auditfeesDetails->auditlevy_remarks)->content;

            $auditfees_content .= $auditlevy_remarks;
            $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $auditfees_content . '</div>';

            /* $conclusion_content ='<h3 style="text-align:center;">CONCLUSION OF AUDIT</h3>';
                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $conclusion_content . '</div>';*/

            $pendingparacontent = '<div><h3 style="text-align:center;"><u>Pending Paras Details</u></h3>';

            $PendingParaDetails = DB::table('audit.report_pendingparadetails')
                ->where('scheduleid', $scheduleId)
                ->where('statusflag', $handlestatusflag)
                ->first();

            $pendingpara_remarks = json_decode($PendingParaDetails->pendingpara_remarks)->content;

            $pendingparacontent .= $pendingpara_remarks;


            //$GetauditSlips = FormatModel::FetchGistObjections($scheduleId);
           // $GetauditSlips = FormatModel::Fetchparadetails($scheduleId);
		$GetauditSlips = FormatModel::Fetchparadetails($scheduleId, $spilloverflag, $instid, $financialyearcode);


            $currentparacontent = $this->currentparadetails($GetauditSlips, $lang);

            $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $pendingparacontent . ' ' . $currentparacontent . '</div>';
        }



        if ($whichpart == 'part_c' || $whichpart == 'all') {
            $partCHeading = '<div>
                                            <h1 style="text-align:center; font-size: 36px;">PART - III</h1>
                                        </div>';

            $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partCHeading . '</div>';



            $slipAnnexureIframes = '';

            $ParaDetails = DB::table('audit.slipfileupload as fileup')
                ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'fileup.auditslipid')
                ->join('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'fileup.fileuploadid')
                ->where('slip.auditscheduleid', $scheduleId)
                //->where('slip.processcode', 'X')
                ->whereIn('slip.processcode', ['X'])

                ->select('slip.auditslipid', 'slip.irregularitiescode', 'slip.slipdetails', 'fileup.fileuploadid', 'filedet.filepath', 'filedet.filename')
                ->get();


            $seriousCount = 0;

            $serSlipRow = DB::table('audit.report_storesliporder')
                ->where('auditscheduleid', $scheduleId)
                ->select('ser_ordered_slips')
                ->first();

            if ($serSlipRow && $serSlipRow->ser_ordered_slips) {
                $seriousArray = json_decode($serSlipRow->ser_ordered_slips, true);
                if (is_array($seriousArray)) {
                    $seriousCount = count($seriousArray);
                }
            }



            if (count($ParaDetails) > 0) {
                $partccontents = '<p ><b>1)List of Annexures</b></p>';

                $partccontents .= '<table style="width: 100%; border: none; border-collapse: collapse;">
                                    <tr>
                                        <th style="width:5%; font-weight: bold;text-align:center;">Annexure No</th>
                                        <th style="width:40%; font-weight: bold;text-align:center;">Subject</th>
                                        <th style="width:40%; font-weight: bold;text-align:center;">Para No</th>
                                        <th style="width:5%; font-weight: bold;text-align:center;">Attachment</th>
                                    </tr>';

                $groupedParas = [];

                $slipAnnextureFiles = [];

                foreach ($ParaDetails as $para) {
                    $slipId = $para->auditslipid;
                    $code = $para->irregularitiescode;

                    // Calculate para number and setup structure
                    if (!isset($groupedParas[$slipId])) {
                        $paraNo = '-';
                        $slipOrderColumn = ($code === '01') ? 'ser_ordered_slips' : 'nonser_ordered_slips';

                        $slipOrderRow = DB::table('audit.report_storesliporder')
                            ->where('auditscheduleid', $scheduleId)
                            ->select($slipOrderColumn)
                            ->first();

                        if ($slipOrderRow && $slipOrderRow->$slipOrderColumn) {
                            $orderedArray = json_decode($slipOrderRow->$slipOrderColumn, true);
                            if (is_array($orderedArray)) {
                                foreach ($orderedArray as $key => $id) {
                                    if ((int)$id === (int)$slipId) {
                                        $paraNo = ($code === '01') ? (int)$key : $seriousCount + (int)$key;
                                        break;
                                    }
                                }
                            }
                        }

                        $groupedParas[$slipId] = [
                            'slipdetails' => $para->slipdetails,
                            'parano' => str_pad($paraNo, 4, '0', STR_PAD_LEFT),
                            'attachments' => []
                        ];
                    }

                    // Add attachments
                    if (!empty($para->filename)) {
                        $files = explode(',', $para->filename);
                        $filepaths = explode(',', $para->filepath);

                        foreach ($files as $key => $file) {
                            $file = trim($file);
                            $filepath = isset($filepaths[$key]) ? trim($filepaths[$key]) : '';

                            $groupedParas[$slipId]['attachments'][] = [
                                'filename' => $file,
                                'filepath' => $filepath
                            ];
                            $paraNoLabel = 'Annexure of Para No: ' . str_pad($groupedParas[$slipId]['parano'], 4, '0', STR_PAD_LEFT);

                            // ✅ Collect for processing later
                            $slipAnnextureFiles[] = (object)[
                                'filename' => $file,
                                'filepath' => $filepath,
                                'annexture_type' => 'slip_related'
                            ];
                        }
                    }
                }

		 uasort($groupedParas, function ($a, $b) {
 		return strnatcmp($a['parano'], $b['parano']); // natural ascending sort
 		});

                $serial = 1;

                if (!empty($groupedParas)) {
                    foreach ($groupedParas as $slipId => $data) {
                        $attachmentLinks = '-';
                        if (!empty($data['attachments'])) {
                            $attachmentLinks = '';
                            foreach ($data['attachments'] as $i => $att) {
                                $attachmentLinks .= ($i + 1) . ') ' . basename($att['filename']) . '<br>';

                                // Optional: generate iframes
                                $filePath = storage_path('app/public/' . $att['filepath']);
                                if (File::exists($filePath)) {
                                    $publicPath = str_replace(storage_path('app/public/'), '', $filePath);
                                    $url = asset('/' . $publicPath);
                                    $slipAnnexureIframes .= '<p ><b>Annexture of Para No :' . $data['parano'] . '</b></p>';

                                    $slipAnnexureIframes .= "
                                            <div style='margin-top: 40px; page-break-before: always;'>
                                                <iframe
                                                    src='{$url}#toolbar=0&navpanes=0&scrollbar=0'
                                                    width='100%'
                                                    height='800px'
                                                    style='border:1px solid #ccc; overflow:hidden;'></iframe>
                                            </div>
                                        ";
                                }
                            }
                        }

                        $partccontents .= '<tr>
                                <td style="width:5%; text-align:center;">' . str_pad($serial++, 4, '0', STR_PAD_LEFT) . '</td>
                                <td style="width:40%; text-align:center;">' . ($data['slipdetails'] ?? '-') . '</td>
                                <td style="width:5%; text-align:center;">' . $data['parano'] . '</td>
                                <td style="width:40%; text-align:left;">' . $attachmentLinks . '</td>
                            </tr>';
                    }
                } else {
                    $partccontents .= '<tr>
                            <td colspan="4" style="text-align:center;">No annexure found</td>
                        </tr>';
                }
            } else {

                $partccontents = '<p ><b>1)List of Annexures : Nil</b></p>';
            }

            $partccontents .= '</table>';

            $annexturefiles = DB::table('audit.report_annextures as ann')
                ->join('audit.fileuploaddetail as fup', 'fup.fileuploadid', '=', 'ann.fileupload_id')
                ->select('fup.filepath', 'fup.filename', 'ann.annexture_type')
                ->where('ann.auditscheduleid', $scheduleId)
                ->where('ann.statusflag', '!=', 'N')
                ->orderby('ann.annexture_id', 'asc')
                ->get();
            $response = [];
            foreach ($annexturefiles as $file) {
                $response[$file->annexture_type] = [
                    'filename' => $file->filename,
                    'filepath' => $file->filepath,
                    'annexture_type' => $file->annexture_type,
                    'subject' => $file->subject ?? '', // fallback if 'subject' is missing
                ];
            }


            $annexureLabels = DB::table('audit.mst_accountparticulars_details')
                ->where('statusflag', 'Y')
                ->pluck('accpar_ename', 'accpar_key') // value, key
                ->toArray();



            $partccontents .= '<p><b>2) List of Accounts and Statements</b></p>';
            $partccontents .= '<table width="100%" border="1" cellspacing="0" cellpadding="5">
                                    <tr>
                                        <th width="20%" align="center">Annexure No</th>
                                        <th width="80%" align="center">Subject</th>
                                    </tr>';
            $annexureNo = 1;
            foreach ($response as $type => $data) {
                $subject = htmlspecialchars($annexureLabels[$data['annexture_type']] ?? 'Unknown');
                $partccontents .= '<tr>
                        <td align="center">' . $annexureNo++ . '</td>
                        <td>' . $subject . '</td>
                    </tr>';
            }
            $partccontents .= '</table>';

            $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partccontents . '</div>';

            $listofannextures = '';
            if (count($ParaDetails) > 0) {
                $listofannextures .= '<div>
                                                <h3 style="text-align:center;">List of Annexures</h3>
                                            </div>' . $slipAnnexureIframes;
            }

            $mpdfContent =  $mpdfContent . '' . $listofannextures;


            $annexurePaths = [];
            $GetAnnextureFiles = FormatModel::FetchAnnextures($scheduleId);
            $annexureIframes = '';
            if ($GetAnnextureFiles['pdfFiles']) {
                foreach ($GetAnnextureFiles['pdfFiles'] ?? [] as $pdfFile) {
                    $filePath = storage_path('app/public/' . $pdfFile->filepath);
                    if (File::exists($filePath)) {
                        $annexurePaths[] = [
                            'path' => $filePath,
                            'type' => $pdfFile->annexture_type ?? 'Annexure'
                        ];
                    }
                }

                $pdffiles = $annexurePaths;


                foreach ($pdffiles as $i => $annexure) {
                    $path = $annexure['path'];
                    $type = $annexure['type'];
                    // Convert full storage path to public asset path
                    $publicPath = str_replace(storage_path('app/public/'), '', $path);
                    $url = asset('/' . $publicPath); // assuming you're serving from storage:link
                    $annexureTitle = $annexureLabels[$type];
                    $annexureIframes .= "
                                                            <div style='margin-top: 40px;'>
                                                                <h4 style='font-weight:bold; color:#2c3e50;text-align:center;font-size:16px;'>{$annexureTitle}</h4>
                                                                <iframe
                                                                    src='{$url}#toolbar=0&navpanes=0&scrollbar=0'
                                                                    width='100%'
                                                                    height='800px'
                                                                    style='border:1px solid #ccc; overflow:hidden;'></iframe>
                                                            </div>
                                                        ";
                }


                // Combine the main content and annexure previews
                $mpdfContent = $mpdfContent . $annexureIframes;
            }




            if (!empty($GetAnnextureFiles['xlsxFiles'])) {
                foreach ($GetAnnextureFiles['xlsxFiles'] as $xlsxFile) {
                    // print_r($xlsxFile);
                    $excelPath = storage_path('app/public/' . $xlsxFile->filepath);

                    if (file_exists($excelPath)) {
                        try {
                            $spreadsheet = IOFactory::load($excelPath);
                            $sheets = $spreadsheet->getAllSheets(); // ✅ get all sheets
                            $fileTitle = $annexureLabels[$xlsxFile->annexture_type] ?? $xlsxFile->annexture_type ?? 'Excel Annexure';
                            // $fileTitle ='';
                            $excelHtml = "<div style='page-break-before: always; margin-top:20px; font-family:latha;'>"; // 👈 for Tamil support
                            $excelHtml .= "<h3 style='font-weight:bold; text-align:center; color:#2c3e50;'>{$fileTitle}</h3>";

                            foreach ($sheets as $sheetIndex => $sheet) {
                                $data = $sheet->toArray(false);
                                //$data = $sheet->toArray(null, false, false, true);
                                $sheetTitle = $sheet->getTitle();

                                $excelHtml .= "<h4 style='font-weight:bold; text-align:center; color:#2c3e50;'>{$sheetTitle}</h4>";
                                $excelHtml .= "<table border='1' cellpadding='5' cellspacing='0' width='100%' style='border-collapse:collapse; font-size:12px;'>";

                                foreach ($data as $row) {
                                    $excelHtml .= '<tr>';
                                    foreach ($row as $cell) {
                                        $excelHtml .= '<td style=\"font-family:latha;\">' . htmlspecialchars($cell) . '</td>'; // 👈 force font here too
                                    }
                                    $excelHtml .= '</tr>';
                                }

                                $excelHtml .= '</table>';

                                // Append each sheet's HTML to the final preview content
                            }

                            $excelHtml .= '</div>';
                            $mpdfContent .= $excelHtml;
                        } catch (\Exception $e) {
                            $mpdfContent .= "<p style='color:red;'>Failed to load Excel file: {$xlsxFile['filename']}</p>";
                        }
                    } else {
                        $mpdfContent .= "<p style='color:red;'>Excel file not found: {$xlsxFile['filename']}</p>";
                    }
                }
            }
        }

        // Return
        return response()->json([
            'res' => 'success',
            'html' => $mpdfContent
        ]);
    }

     public function DownloadAuditReport($scheduleId, $lang, $spilloverflag, $instid, $financialyearcode, $action = 'download')
    {
        @set_time_limit(0);
        @ini_set('max_execution_time', '0');
        $attachmentsOnly = $action === 'download_attachments';
        $reportOnly = !$attachmentsOnly;

        $labelsJson_layout = json_decode(file_get_contents(public_path('json/layout.json')), true);
        $label_layout = $labelsJson_layout[$lang];

        ob_end_clean();
        ob_start();

        $chargeData = session('charge');
        $userData = session('user');
        $session_userid = $userData->userid;

        $workAllocation = FormatModel::fetch_allocatedwork($scheduleId);
        $WorkingOfficeGet1 = FormatModel::GetSchedultedEventDetails($scheduleId, $spilloverflag, $instid, $financialyearcode);
        $WorkingOfficeGet   =   $WorkingOfficeGet1[0];
        // print_r($WorkingOfficeGet);



        //$auditeamid = $WorkingOfficeGet->auditteamid;

	        if (!$scheduleId) {
	            return response()->json(['res' => 'nodata']);
	        }

	        if ($attachmentsOnly) {
	            return $this->downloadOriginalAuditAttachments($scheduleId);
	        }



	        //$TeammemberGet = FormatModel::getTeamMembers($auditeamid);

        // Language setup
        if ($lang == 'ta') {
            $DeptName = $WorkingOfficeGet->depttlname;
            $InstituteName = $WorkingOfficeGet->insttname;
            $TypeofAudit = $WorkingOfficeGet->typeofaudittname;
            $DistName = $WorkingOfficeGet->disttname . ' மாவட்டம்';
            $fontName = 'Latha';
            $defaultsize = 8;
            $AuditReport_Text = 'தணிக்கை அறிக்கை';
            $AuditReport_Year = 'ஆண்டு';
            $deptsize = '16';
            $InstNameSize = '22';
        } else {

            $DeptName = $WorkingOfficeGet->deptelname;
            $InstituteName = $WorkingOfficeGet->instename;
            $TypeofAudit = $WorkingOfficeGet->typeofauditename;
            $DistName = $WorkingOfficeGet->distename . ' District';
            $fontName = 'Latha';
            $defaultsize = 13;
            $AuditReport_Text = 'Audit Report';
            $AuditReport_Year = 'Year of';
            $deptsize = '24';
            $InstNameSize = '26';
        }



        //print_r($WorkingOfficeGet);
        $FinancialYear = $WorkingOfficeGet->yearname;



        $deptcode = $WorkingOfficeGet->deptcode;


        if ($deptcode == '01') {
            $auditEndDate = '30/06/2025';
        } else {
            // Split the years
            $years = explode(',', $FinancialYear);

            // Get the last year range
            $lastYearRange = trim(end($years)); // e.g. "2023-2024"

            // Split the range into start and end years
            $yearParts = explode('-', $lastYearRange);

            // Use the second part as the end year
            $endYear = isset($yearParts[1]) ? trim($yearParts[1]) : null;

            if ($endYear) {
                $auditEndDate = "31/03/$endYear";
                //echo "Audit End Date: $auditEndDate";
            }
        }


        /* if($WorkingOfficeGet->deptcode == '01')
            {
                $FinancialYear = $WorkingOfficeGet->annadhanamyearname;


            }*/


        // mPDF instance
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'tempDir' => $this->getPdfTempDir(),
            'fontDir' => array_merge(
                (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
                [public_path('fonts/tamil')]
            ),
            'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
                'noto1' => [
                    'R' => 'Latha.ttf',
                    'useOTL' => 0xFF,
                    //'useKashida' => 75,
                ],
                'noto' => [
                    'R' => 'times.ttf',
                    'useOTL' => 0xFF,
                    // 'useKashida' => 75,
                ],
                'times' => [
                    'R' => 'times.ttf',
                    'useOTL' => 0xFF,
                    //'useKashida' => 75,
                ],
                'arial' => [
                    'R' => 'arial.ttf',
                    'useOTL' => 0xFF,
                    // 'useKashida' => 75,
                ],
            ],
            'default_font' => 'noto1',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_top' => 13,    // Top margin
            'margin_bottom' => 13, // Bottom margin
            'margin_left' => 10,   // Left margin
            'margin_right' => 10   // Right margin
        ]);

        // Border size: inside the margins (15mm all sides)
        // Use a div in header that covers whole page with border
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

        // Set the same border as header and footer for all pages
        $mpdf->SetHTMLHeader($borderHtml);
        $mpdf->SetHTMLFooter($borderHtml);


        $imagePath = public_path('site/image/tn__logo.png');
        $imageData = base64_encode(file_get_contents($imagePath));
        $imageType = pathinfo($imagePath, PATHINFO_EXTENSION);
        $base64Image = 'data:image/' . $imageType . ';base64,' . $imageData;

        $mpdfContent = '';
        $attachmentFirstPageContent = '';

        $annadhanamyearcontent = $this->getFirstPageAnnadhanamYearContent($WorkingOfficeGet->deptcode ?? '', $lang, $WorkingOfficeGet->annadhanamyearname ?? '');

        if ($attachmentsOnly) {
            $attachmentFirstPageContent = $this->generateFirstPageHtml($deptsize, $DeptName, $base64Image, $DistName, $InstituteName, $FinancialYear, $AuditReport_Year, $AuditReport_Text, $annadhanamyearcontent);

            $mpdfContent = $attachmentFirstPageContent;
        }

        if ($reportOnly) {
            $htmlContent = $this->generateFirstPageHtml($deptsize, $DeptName, $base64Image, $DistName, $InstituteName, $FinancialYear, $AuditReport_Year, $AuditReport_Text, $annadhanamyearcontent);
            $mpdfContent = $htmlContent;

        $auditCertificate = DB::table('audit.report_auditcertificate')
            ->where('scheduleid', $scheduleId)
            ->where('statusflag', 'F')
            ->first();

        $auditcertificate_remarks = json_decode($auditCertificate->cer_remarks)->content;

	        $cer_type_code = $auditCertificate->cer_type_code;



        $MasterauditCertificate = DB::table('audit.mst_auditcertificatetype')
            ->where('cer_type_code', $cer_type_code)
            ->first();


        $Master_cer_content = json_decode($MasterauditCertificate->cer_content)->content;



        $Master_cer_content = str_replace('[audityear]', $auditEndDate, $Master_cer_content);


        //$certypetext =$MasterauditCertificate->cer_ename;
        if ($lang === 'en') {
            $certypetext = $MasterauditCertificate->cer_ename;
        } else {
            $certypetext = $MasterauditCertificate->cer_tname;
        }


        $coveringletter = $this->coveringletter($scheduleId, $lang, $certypetext, $auditEndDate, $spilloverflag, $instid, $financialyearcode);

        // print_r($coveringletter);
        // print_r($coveringletter);
        //            exit;
        $loadcoveringletter = '<div class="page-break"></div><div class="section-content">' . $coveringletter . '</div>';


        $mpdfContent .= $loadcoveringletter;

        // Add other sections
        //$mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $this->loadintimationletter($scheduleId, $lang) . '</div>';
        //$mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $this->loadentrymeeting($scheduleId, $lang, $fontName) . '</div>';

        /*$overview_content = $this->OverviewContentLoad($scheduleId);
            $mpdfContent .= ' <div class="page-break"></div><div class="section-content">' . $overview_content . '</div>';*/



        //$partacontents = $this->GeneratePartAContentsLoad($scheduleId,$lang);

        // $mpdfContent .= $partacontents;


        if ($lang === 'en') {
            $part1 = 'PART - I';
            $certifiacte = 'AUDIT CERTIFICATE';
        } else {
            $part1 = 'பகுதி - I';
            $certifiacte = 'தணிக்கைச் சான்றிதழ்';
        }

        // Page 1: PART - I centered in the middle
        $partacontents = '
            <div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $part1 . '</h1>
            </div>
            ';

        // Page break to move to next page
        $partacontents .= '<pagebreak />';

        // Page 2: Audit Certificate content (normal alignment)
        $partacontents .= '
            <div >
                <h3 style="text-align:center;">' . $certifiacte . '</h3><p>' . $Master_cer_content . '</p>
            </div> ';

        if ($cer_type_code !== '01') {
            //$partacontents .='<h3 style="text-align:center;">Remarks of Audit Certificate with '.$MasterauditCertificate->cer_ename.'</h3>';
            $partacontents .= $auditcertificate_remarks;
        }


        $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partacontents . '</div>';

        $GetauditSlips = FormatModel::FetchAuditSlips($scheduleId, '01', $spilloverflag, $instid, $financialyearcode);
        $slipResultSerious = $this->AuditSlipLoadPDF($scheduleId, $GetauditSlips, $lang, $mpdf, '', 'ser');
        //$slipPageMapSerious = $slipResultSerious['slipPages'];

        $seriousLastSlipNO = $slipResultSerious['seriousno'];

         $GetauditSlips = FormatModel::FetchAuditSlips($scheduleId, '02', $spilloverflag, $instid, $financialyearcode);
        $slipResultNonSerious = $this->AuditSlipLoadPDF($scheduleId, $GetauditSlips, $lang, $mpdf, $seriousLastSlipNO, 'nonser');
        //$slipPageMapNonSerious = $slipResultNonSerious['slipPages'];

        $mpdf->TOC_Entry("Chapter 1", 0);

           $FetchGistObjections = FormatModel::FetchGistObjections($scheduleId, $spilloverflag, $instid, $financialyearcode);
        $GistAuditObjections = $this->GistAuditObjections($scheduleId, $FetchGistObjections, $lang);
        $mpdfContent .= ' <div class="page-break"></div><div class="section-content">' . $GistAuditObjections . '</div>';


        $partacontents = $this->generatePartAContent($scheduleId, $lang, 'F', $spilloverflag, $instid, $financialyearcode);



        $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partacontents . '</div>';

        /* foreach ($TeammemberGet as $Teammembers) {
                        $Name = $lang == 'ta' ? $Teammembers->usertamilname : $Teammembers->username;
                        $Desig = $lang == 'ta' ? $Teammembers->desigtlname : $Teammembers->desigelname;
                        $codeOfEthics = $this->load_codeofethicscontents($scheduleId, $lang, true, $Name, $Desig);
                        $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $codeOfEthics . '</div>';
                    }

                    $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $this->workallocationpdf($scheduleId, $lang) . '</div>';
                    $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $this->loadexitmeeting($scheduleId, $lang, $fontName) . '</div>';*/



        if ($slipResultSerious['html']) {
            $SeriousSectionHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                                            <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_b_prefill'] . '</h1>
                                            <h2 style="text-align:center;">' . $label_layout['serious_reg'] . '</h2>
                                        </div>';

            $mpdfContent .= '<div style="page-break-before: always;">' . $SeriousSectionHeading . '</div>';

            $SeriousSection = $slipResultSerious['html'];

            $mpdfContent .= '<div style="page-break-before: always;">' . $SeriousSection . '</div>';
        }



        // Now for non-serious irregularities
        if ($slipResultNonSerious['html']) {
            $nonSeriousSectionHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
                                            <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_b_prefill'] . '</h1>
                                            <h2 style="text-align:center;">' . $label_layout['nonserious_reg'] . '</h2>
                                        </div>';
            $mpdfContent .= '<div style="page-break-before: always;">' . $nonSeriousSectionHeading . '</div>';

            $nonSeriousSection = $slipResultNonSerious['html'];

            $mpdfContent .= '<div style="page-break-before: always;">' . $nonSeriousSection . '</div>';
        }

        //$slipPageMap = array_merge($slipPageMapSerious, $slipPageMapNonSerious);


        //print_r($slipPageMapSerious);




        $auditfees_content = '<h3 style="text-align:center;">' . $label_layout['auditefees'] . '</h3>';


        $auditfeesDetails = DB::table('audit.report_auditlevycertificate')
            ->where('scheduleid', $scheduleId)
            ->where('statusflag', 'F')
            ->first();

        $auditlevy_remarks = json_decode($auditfeesDetails->auditlevy_remarks)->content;

        $auditfees_content .= $auditlevy_remarks;
        $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $auditfees_content . '</div>';

        /* $conclusion_content ='<h3 style="text-align:center;">CONCLUSION OF AUDIT</h3>';
                $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $conclusion_content . '</div>';*/

        $pendingparacontent = '<div><h3 style="text-align:center;"><u>' . $label_layout['pendingpara'] . '</u></h3>';

        $PendingParaDetails = DB::table('audit.report_pendingparadetails')
            ->where('scheduleid', $scheduleId)
            ->where('statusflag', 'F')
            ->first();

        $pendingpara_remarks = json_decode($PendingParaDetails->pendingpara_remarks)->content;

        $pendingparacontent .= $pendingpara_remarks;


        //  $GetauditSlips = FormatModel::FetchGistObjections($scheduleId);

        //$GetauditSlips = FormatModel::Fetchparadetails($scheduleId);

	$GetauditSlips = FormatModel::Fetchparadetails($scheduleId, $spilloverflag, $instid, $financialyearcode);



        $currentparacontent = $this->currentparadetails($GetauditSlips, $lang);

            $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $pendingparacontent . ' ' . $currentparacontent . '</div>';
        }

        if ($reportOnly) {
            $footerHTML = '
                            <sethtmlpagefooter name="myFooter" value="on" />
                            <htmlpagefooter name="myFooter">
                                <div style="text-align:center; font-size: 15pt; font-family:Times New Roman;">
                                    {PAGENO}
                                </div>
                            </htmlpagefooter>';

            $mpdfContentFinal = $footerHTML . self::applyFontByLanguage($mpdfContent);
            $reportCacheKey = sha1($lang . '|' . $scheduleId . '|' . $mpdfContentFinal);
            $reportCachePath = $this->getDownloadCachePath('report', $scheduleId, $lang, $reportCacheKey);

            if ($this->isReusableDownloadCache($reportCachePath)) {
                return [
                    'fileName' => 'AuditReport_' . $scheduleId . '.pdf',
                    'filePath' => $reportCachePath,
                    'deleteAfterSend' => false,
                ];
            }

            @$mpdf->WriteHTML('<div style="padding: 20px; text-align: justify;">' . $mpdfContentFinal . '</div>');

            $tempReportPdfPath = $this->createTempPdfPath('reportpdf_');
            @$mpdf->Output($tempReportPdfPath, \Mpdf\Output\Destination::FILE);

            if (!file_exists($tempReportPdfPath) || filesize($tempReportPdfPath) < 1000) {
                throw new \Exception("Invalid mPDF output: $tempReportPdfPath");
            }

            try {
             if (!$this->mergePdfFilesWithPdfcpu(
    [$tempReportPdfPath],
    $reportCachePath,
    'compressed'
)) {
    throw new \RuntimeException(
        'pdfcpu merge failed. Check laravel.log'
    );
}

                return [
                    'fileName' => 'AuditReport_' . $scheduleId . '.pdf',
                    'filePath' => $reportCachePath,
                    'deleteAfterSend' => false,
                ];
            } finally {
                if (file_exists($tempReportPdfPath)) {
                    @unlink($tempReportPdfPath);
                }
            }
        }

        $partCHeading = '<div style="height: 100vh; display: flex; justify-content: center; align-items: center;">
	                                            <h1 style="text-align:center; font-size: 36px;margin-top: 380px;">' . $label_layout['part_c_prefill'] . '</h1>
	                                        </div>';

        $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partCHeading . '</div>';

        // Initialize the annexure page map

        /* $mpdfContentFinal = self::applyFontByLanguage($mpdfContent);

                $footerHTML = '
                        <sethtmlpagefooter name="myFooter" value="on" />
                        <htmlpagefooter name="myFooter">
                            <div style="text-align:center; font-size: 15pt; font-family:Times New Roman;">
                                {PAGENO}
                            </div>
                        </htmlpagefooter>';

                $mpdfContentFinal = $footerHTML . $mpdfContentFinal;

                $mpdf->WriteHTML('<div style="padding: 20px; text-align: justify;">' . $mpdfContentFinal . '</div>');*/


        /*$ParaDetails = DB::table('audit.report_paradetails as para')
                                ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'para.slip_id')
                                ->where('para.auditscheduleid', $scheduleId)
                                ->whereNotNull('para.slip_attachments') // ensure not null\
                                ->select('para.para_id', 'para.slip_id','para.orderid', 'slip.slipdetails', 'para.slip_attachments')
                                ->orderBy('para.orderid', 'asc')
                                ->get();

                foreach ($ParaDetails as $para) {

                    $fileIds = json_decode($para->slip_attachments ?? '[]', true); // decode attachment IDs

                    $files = DB::table('audit.fileuploaddetail')
                                ->whereIn('fileuploadid', $fileIds)
                                ->pluck('filepath') // get just the names
                                ->toArray();

                    $filesNames = DB::table('audit.fileuploaddetail')
                                    ->whereIn('fileuploadid', $fileIds)
                                    ->pluck('filename') // get just the names
                                    ->toArray();

                    $para->filepaths = implode(', ', $files); // attach comma-separated filenames
                    $para->filenames = implode(', ', $filesNames);
                }*/
        $partccontents = '<p ><b>' . $label_layout['list_annexure'] . '</b></p>';

        $ParaDetails = DB::table('audit.slipfileupload as fileup')
            ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'fileup.auditslipid')
            ->join('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'fileup.fileuploadid')
            ->where('slip.auditscheduleid', $scheduleId)
            //->where('slip.processcode', 'X')
            ->whereIn('slip.processcode', ['X'])

            ->select('slip.auditslipid', 'slip.irregularitiescode', 'slip.slipdetails', 'fileup.fileuploadid', 'filedet.filepath', 'filedet.filename')
            ->get();


        $seriousCount = 0;

        $serSlipRow = DB::table('audit.report_storesliporder')
            ->where('auditscheduleid', $scheduleId)
            ->select('ser_ordered_slips')
            ->first();

        if ($serSlipRow && $serSlipRow->ser_ordered_slips) {
            $seriousArray = json_decode($serSlipRow->ser_ordered_slips, true);
            if (is_array($seriousArray)) {
                $seriousCount = count($seriousArray);
            }
        }


        $partccontents .= '<table style="width: 100%; border: none; border-collapse: collapse;">
                                    <tr>
                                        <th style="width:5%; font-weight: bold;text-align:center;">' . $label_layout['annexureno'] . '</th>
                                        <th style="width:40%; font-weight: bold;text-align:center;">' . $label_layout['subject'] . '</th>
                                        <th style="width:40%; font-weight: bold;text-align:center;">' . $label_layout['parano'] . '</th>
                                        <th style="width:5%; font-weight: bold;text-align:center;">' . $label_layout['attachments'] . '</th>
                                    </tr>';
        $slipAnnextureFiles = [];

        if (count($ParaDetails) > 0) {
            $groupedParas = [];

            foreach ($ParaDetails as $para) {
                $slipId = $para->auditslipid;
                $code = $para->irregularitiescode;

                // Calculate para number and setup structure
                if (!isset($groupedParas[$slipId])) {
                    $paraNo = '-';
                    $slipOrderColumn = ($code === '01') ? 'ser_ordered_slips' : 'nonser_ordered_slips';

                    $slipOrderRow = DB::table('audit.report_storesliporder')
                        ->where('auditscheduleid', $scheduleId)
                        ->select($slipOrderColumn)
                        ->first();

                    if ($slipOrderRow && $slipOrderRow->$slipOrderColumn) {
                        $orderedArray = json_decode($slipOrderRow->$slipOrderColumn, true);
                        if (is_array($orderedArray)) {
                            foreach ($orderedArray as $key => $id) {
                                if ((int)$id === (int)$slipId) {
                                    $paraNo = ($code === '01') ? (int)$key : $seriousCount + (int)$key;
                                    break;
                                }
                            }
                        }
                    }

                    $groupedParas[$slipId] = [
                        'slipdetails' => $para->slipdetails,
                        'parano' => str_pad($paraNo, 4, '0', STR_PAD_LEFT),
                        'attachments' => []
                    ];
                }

                // Add attachments
                if (!empty($para->filename)) {
                    $files = explode(',', $para->filename);
                    $filepaths = explode(',', $para->filepath);

                    foreach ($files as $key => $file) {
                        $file = trim($file);
                        $filepath = isset($filepaths[$key]) ? trim($filepaths[$key]) : '';

                        $groupedParas[$slipId]['attachments'][] = [
                            'filename' => $file,
                            'filepath' => $filepath
                        ];

                        // ✅ Collect for processing later
                        $slipAnnextureFiles[] = (object)[
                            'filename' => $file,
                            'filepath' => $filepath,
                            'annexture_type' => 'slip_related',
                            'title' => $para->slipdetails ?: basename($file)
                        ];
                    }
                }
            }


            $serial = 1;

            if (!empty($groupedParas)) {
                foreach ($groupedParas as $slipId => $data) {
                    $attachmentLinks = '-';
                    if (!empty($data['attachments'])) {
                        $attachmentLinks = '';
                        foreach ($data['attachments'] as $i => $att) {
                            $attachmentLinks .= ($i + 1) . ') ' . basename($att['filename']) . '<br>';
                        }
                    }

                    $partccontents .= '<tr>
                                <td style="width:5%; text-align:center;">' . str_pad($serial++, 4, '0', STR_PAD_LEFT) . '</td>
                                <td style="width:40%; text-align:center;">' . ($data['slipdetails'] ?? '-') . '</td>
                                <td style="width:5%; text-align:center;">' . $data['parano'] . '</td>
                                <td style="width:40%; text-align:left;">' . $attachmentLinks . '</td>
                            </tr>';
                }
            } else {
                $partccontents .= '<tr>
                            <td colspan="4" style="text-align:center;">' . $label_layout['noannexure'] . '</td>
                        </tr>';
            }
        } else {
            // Show fallback message when no annexures exist
            $partccontents .= '<tr>
                                        <td colspan="4" style="text-align:center;">' . $label_layout['noannexure'] . '</td>
                                    </tr>';
        }

        $partccontents .= '</table>';

        $annexturefiles = DB::table('audit.report_annextures as ann')
            ->join('audit.fileuploaddetail as fup', 'fup.fileuploadid', '=', 'ann.fileupload_id')
            ->select('fup.filepath', 'fup.filename', 'ann.annexture_type')
            ->where('ann.auditscheduleid', $scheduleId)
            ->where('ann.statusflag', '!=', 'N')
            ->orderby('ann.annexture_id', 'asc')
            ->get();

        // print_r($annexturefiles);
        // exit;
        $response = [];
        foreach ($annexturefiles as $file) {
            $response[$file->annexture_type] = [
                'filename' => $file->filename,
                'filepath' => $file->filepath,
                'annexture_type' => $file->annexture_type,
                'subject' => $file->subject ?? '', // fallback if 'subject' is missing
            ];
        }


        // $annexureLabels = DB::table('audit.mst_accountparticulars_details')
        //         ->where('statusflag', 'Y')
        //         ->pluck('accpar_ename','accpar_tname', 'accpar_key') // value, key
        //         ->toArray();
        $annexureRaw = DB::table('audit.mst_accountparticulars_details')
            ->where('statusflag', 'Y')
            ->select('accpar_key', 'accpar_ename', 'accpar_tname')
            ->get();

        $annexureLabels = [];

        foreach ($annexureRaw as $row) {
            $annexureLabels[$row->accpar_key] = $lang === 'ta' ? $row->accpar_ename : $row->accpar_ename;
        }



        $annexureDisplayTitles = [];

        $partccontents .= '<p><b>' . $label_layout['list_acc'] . '</b></p>';
        $partccontents .= '<table width="100%" border="1" cellspacing="0" cellpadding="5">
                                    <tr>
                                        <th width="20%" align="center">' . $label_layout['annexureno'] . '</th>
                                        <th width="80%" align="center">' . $label_layout['subject'] . '</th>
                                    </tr>';
        $annexureNo = 1;
        foreach ($response as $type => $data) {
            $subject = htmlspecialchars($annexureLabels[$data['annexture_type']] ?? 'Unknown');
            $annexureDisplayTitles[$data['annexture_type']] = $annexureNo . '. ' . strip_tags($annexureLabels[$data['annexture_type']] ?? 'Unknown');

            $partccontents .= '<tr>
                            <td align="center">' . $annexureNo++ . '</td>
                            <td>' . $subject . '</td>
                        </tr>';
        }

        $partccontents .= '</table>';

        $mpdfContent .= '<div class="page-break"></div><div class="section-content">' . $partccontents . '</div>';


        $GetAnnextureFiles = FormatModel::FetchAnnextures($scheduleId);
        $attachmentCachePath = null;
        $attachmentSourceCount = count($slipAnnextureFiles)
            + count($GetAnnextureFiles['pdfFiles'] ?? [])
            + count($GetAnnextureFiles['xlsxFiles'] ?? []);
        $useFastAttachmentMode = $attachmentsOnly && $attachmentSourceCount >= 80;

        if ($attachmentsOnly) {
            $attachmentCacheKey = $this->buildAttachmentBundleCacheKey($scheduleId, $lang, $slipAnnextureFiles, $GetAnnextureFiles);
            $attachmentCachePath = $this->getDownloadCachePath('attachments', $scheduleId, $lang, $attachmentCacheKey);

            if ($this->isReusableDownloadCache($attachmentCachePath)) {
                return [
                    'fileName' => 'AuditAttachments_' . $scheduleId . '.pdf',
                    'filePath' => $attachmentCachePath,
                    'deleteAfterSend' => false,
                ];
            }
        }

        $tempMainPdfPath = null;

        if (!$useFastAttachmentMode) {
            $mpdfContentPdfContent = $mpdfContent;
            $mpdfContentFinal = self::applyFontByLanguage($mpdfContentPdfContent);

            $footerHTML = '
                            <sethtmlpagefooter name="myFooter" value="on" />
                            <htmlpagefooter name="myFooter">
                                <div style="text-align:center; font-size: 15pt; font-family:Times New Roman;">
                                    {PAGENO}
                                </div>
                            </htmlpagefooter>';

            $mpdfContentFinal = $footerHTML . $mpdfContentFinal;
            @$mpdf->WriteHTML('<div style="padding: 20px; text-align: justify;">' . $mpdfContentFinal . '</div>');

            $tempMainPdfPath = $this->createTempPdfPath('mainpdf_');
            @$mpdf->Output($tempMainPdfPath, \Mpdf\Output\Destination::FILE);

            if (!file_exists($tempMainPdfPath) || filesize($tempMainPdfPath) < 1000) {
                throw new \Exception("Invalid mPDF output: $tempMainPdfPath");
            }
        } elseif ($attachmentsOnly && $attachmentFirstPageContent !== '') {
            $mpdfContentPdfContent = $attachmentFirstPageContent;
            $mpdfContentFinal = self::applyFontByLanguage($mpdfContentPdfContent);

            $footerHTML = '
                            <sethtmlpagefooter name="myFooter" value="on" />
                            <htmlpagefooter name="myFooter">
                                <div style="text-align:center; font-size: 15pt; font-family:Times New Roman;">
                                    {PAGENO}
                                </div>
                            </htmlpagefooter>';

            $mpdfContentFinal = $footerHTML . $mpdfContentFinal;
            @$mpdf->WriteHTML('<div style="padding: 20px; text-align: justify;">' . $mpdfContentFinal . '</div>');

            $tempMainPdfPath = $this->createTempPdfPath('mainpdf_');
            @$mpdf->Output($tempMainPdfPath, \Mpdf\Output\Destination::FILE);

            if (!file_exists($tempMainPdfPath) || filesize($tempMainPdfPath) < 1000) {
                throw new \Exception("Invalid mPDF output: $tempMainPdfPath");
            }
        }

        $fileName = $attachmentsOnly
            ? 'AuditAttachments_' . $scheduleId . '.pdf'
            : 'AuditReport_' . $scheduleId . '.pdf';
        $finalFilePath = $attachmentsOnly && $attachmentCachePath
            ? $attachmentCachePath
            : public_path('files/' . $fileName);
        File::ensureDirectoryExists(dirname($finalFilePath));

        $pdfParts = $tempMainPdfPath ? [$tempMainPdfPath] : [];
        $tempPdfParts = $tempMainPdfPath ? [$tempMainPdfPath] : [];
        $includeAttachmentTitlePages = $attachmentsOnly ? true : !$useFastAttachmentMode;

        try {
            if (!empty($slipAnnextureFiles)) {
                $slipAnnexurePaths = $this->getConvertedAnnexurePaths($slipAnnextureFiles, ['slip_related' => 'Annexure']);
                $this->collectAnnexurePdfParts($pdfParts, $tempPdfParts, $slipAnnexurePaths, ['slip_related' => 'Annexure'], 'slipbased', $lang, $includeAttachmentTitlePages);
            }

            $converted = [];

            foreach ($GetAnnextureFiles['pdfFiles'] as $file) {
                $converted[] = [
                    'path' => $this->resolveStoredAttachmentPath($file->filepath),
                    'type' => $file->annexture_type,
                    'title' => $annexureDisplayTitles[$file->annexture_type] ?? ($annexureLabels[$file->annexture_type] ?? 'Annexure'),
                ];
            }

            foreach ($GetAnnextureFiles['xlsxFiles'] as $file) {
                $resolvedPath = $this->resolveStoredAttachmentPath($file->filepath);
                $convertedPdfPath = $this->convertExcelAttachmentToPdf(
                    $resolvedPath,
                    $annexureDisplayTitles[$file->annexture_type] ?? ($annexureLabels[$file->annexture_type] ?? 'Excel Annexure'),
                    $lang
                );

                if ($convertedPdfPath === null) {
                    continue;
                }

                $converted[] = [
                    'path' => $convertedPdfPath,
                    'type' => $file->annexture_type,
                    'title' => $annexureDisplayTitles[$file->annexture_type] ?? ($annexureLabels[$file->annexture_type] ?? 'Annexure'),
                ];
                if (!$this->isAttachmentAssetCachePath($convertedPdfPath)) {
                    $tempPdfParts[] = $convertedPdfPath;
                }
            }

            $this->collectAnnexurePdfParts($pdfParts, $tempPdfParts, $converted, $annexureLabels, 'fileupbased', $lang, $includeAttachmentTitlePages);

            $attachmentMergeProfile = $useFastAttachmentMode ? 'fast' : 'attachments_final';
          if (!$this->mergePdfFilesWithPdfcpu(
        $pdfParts,
        $finalFilePath,
        $attachmentMergeProfile
)) {
    throw new \RuntimeException(
        'pdfcpu failed to merge the audit report attachments.'
    );
}

            return [
                'fileName' => $fileName,
                'filePath' => $finalFilePath,
                'deleteAfterSend' => !$attachmentsOnly,
            ];
        } finally {
            foreach (array_unique($tempPdfParts) as $tempPdfPath) {
                if (is_string($tempPdfPath) && file_exists($tempPdfPath)) {
                    @unlink($tempPdfPath);
                }
            }
	        }
	    }


	    private function downloadOriginalAuditAttachments(int $scheduleId): array
	    {
	        $attachments = $this->getOriginalAuditAttachmentFiles($scheduleId);

	        if (count($attachments) === 0) {
	            throw new \RuntimeException('No attachments found for this report.');
	        }

	        if (count($attachments) === 1) {
	            $attachment = $attachments[0];

	            return [
	                'fileName' => $attachment['fileName'],
	                'filePath' => $attachment['path'],
	                'deleteAfterSend' => false,
	                'contentType' => $this->guessDownloadContentType($attachment['path']),
	            ];
	        }

	        if (!class_exists(\ZipArchive::class)) {
	            throw new \RuntimeException('ZIP extension not available. Cannot download multiple attachments.');
	        }

	        $zipPath = $this->createTempZipPath('audit_attachments_');
	        $zip = new \ZipArchive();

	        if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
	            throw new \RuntimeException('Unable to create attachments ZIP file.');
	        }

	        $usedNames = [];
	        foreach ($attachments as $attachment) {
	            $zipName = $this->makeUniqueZipEntryName($attachment['downloadName'], $usedNames);
	            $zip->addFile($attachment['path'], $zipName);
	        }

	        $zip->close();

	        return [
	            'fileName' => 'AuditAttachments_' . $scheduleId . '.zip',
	            'filePath' => $zipPath,
	            'deleteAfterSend' => true,
	            'contentType' => 'application/zip',
	        ];
	    }

	    private function createTempZipPath(string $prefix): string
	    {
	        $tempPath = tempnam($this->getPdfTempDir(), $prefix);
	        if ($tempPath === false) {
	            throw new \RuntimeException('Unable to create a temporary ZIP file.');
	        }

	        $zipTempPath = $tempPath . '.zip';
	        @rename($tempPath, $zipTempPath);

	        return $zipTempPath;
	    }

	    private function getOriginalAuditAttachmentFiles(int $scheduleId): array
	    {
	        $attachments = [];

	        $slipFiles = DB::table('audit.slipfileupload as fileup')
	            ->join('audit.trans_auditslip as slip', 'slip.auditslipid', '=', 'fileup.auditslipid')
	            ->join('audit.fileuploaddetail as filedet', 'filedet.fileuploadid', '=', 'fileup.fileuploadid')
	            ->where('slip.auditscheduleid', $scheduleId)
	            ->whereIn('slip.processcode', ['X'])
	            ->select('filedet.filepath', 'filedet.filename')
	            ->get();

	        foreach ($slipFiles as $file) {
	            $attachments[] = $this->formatOriginalAttachmentFile($file, 'Slip_Attachments');
	        }

	        $annexureFiles = DB::table('audit.report_annextures as ann')
	            ->join('audit.fileuploaddetail as fup', 'fup.fileuploadid', '=', 'ann.fileupload_id')
	            ->where('ann.auditscheduleid', $scheduleId)
	            ->where('ann.statusflag', '!=', 'N')
	            ->select('fup.filepath', 'fup.filename', 'ann.annexture_type')
	            ->orderBy('ann.annexture_id')
	            ->get();

	        foreach ($annexureFiles as $file) {
	            $folder = 'Report_Annexures';
	            if (!empty($file->annexture_type)) {
	                $folder .= '/' . $this->sanitizeZipPathPart((string) $file->annexture_type);
	            }
	            $attachments[] = $this->formatOriginalAttachmentFile($file, $folder);
	        }

	        return array_values(array_filter($attachments));
	    }

	    private function formatOriginalAttachmentFile($file, string $folder): ?array
	    {
	        $path = $this->resolveStoredAttachmentPath($file->filepath ?? '');

	        if (!is_string($path) || !File::exists($path) || filesize($path) === 0) {
	            \Log::warning('Skipping missing report attachment download file.', [
	                'filepath' => $file->filepath ?? null,
	                'filename' => $file->filename ?? null,
	            ]);

	            return null;
	        }

	        $filename = trim((string) ($file->filename ?? ''));
	        if ($filename === '') {
	            $filename = basename($path);
	        }

	        return [
	            'path' => $path,
	            'fileName' => $this->sanitizeZipPathPart($filename),
	            'downloadName' => $this->sanitizeZipPathPart($folder) . '/' . $this->sanitizeZipPathPart($filename),
	        ];
	    }

	    private function sanitizeZipPathPart(string $value): string
	    {
	        $value = trim(str_replace(['\\', '/'], '_', $value));
	        $value = preg_replace('/[^\w.\- ()]/u', '_', $value);
	        $value = preg_replace('/_+/', '_', (string) $value);

	        return trim((string) $value, '._ ') ?: 'attachment';
	    }

	    private function makeUniqueZipEntryName(string $name, array &$usedNames): string
	    {
	        $name = trim($name, '/');
	        $candidate = $name;
	        $index = 2;

	        while (isset($usedNames[strtolower($candidate)])) {
	            $pathInfo = pathinfo($name);
	            $directory = isset($pathInfo['dirname']) && $pathInfo['dirname'] !== '.' ? $pathInfo['dirname'] . '/' : '';
	            $filename = $pathInfo['filename'] ?? 'attachment';
	            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
	            $candidate = $directory . $filename . '_' . $index++ . $extension;
	        }

	        $usedNames[strtolower($candidate)] = true;

	        return $candidate;
	    }

	    private function guessDownloadContentType(string $path): string
	    {
	        $contentType = @mime_content_type($path);

	        return is_string($contentType) && $contentType !== '' ? $contentType : 'application/octet-stream';
	    }


	    private function getConvertedAnnexurePaths($annexureFiles, $labels)
	    {
	        $pdfPaths = [];
        $excelPaths = [];

        foreach ($annexureFiles as $file) {
            $path = $this->resolveStoredAttachmentPath($file->filepath ?? '');
            $type = $file->annexture_type ?? 'unknown';

            if (!File::exists($path)) continue;

            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($extension === 'pdf') {
                $pdfPaths[] = [
                    'path' => $path,
                    'type' => $type,
                    'title' => $file->title ?? $this->resolveAnnexureTitle($labels, $type, 'Annexure'),
                ];
            } elseif (in_array($extension, ['xlsx', 'xls'], true)) {
                $tempExcelPdfPath = $this->convertExcelAttachmentToPdf(
                    $path,
                    $file->title ?? $this->resolveAnnexureTitle($labels, $type, 'Excel Annexure'),
                    'en'
                );

                if ($tempExcelPdfPath === null) {
                    continue;
                }

                $excelPaths[] = [
                    'path' => $tempExcelPdfPath,
                    'type' => $type,
                    'title' => $file->title ?? $this->resolveAnnexureTitle($labels, $type, 'Excel Annexure'),
                ];
            }
        }

        return array_merge($pdfPaths, $excelPaths);
    }
private function collectAnnexurePdfParts(array &$pdfParts, array &$tempPdfParts, array $annexureFiles, $labels, $typeofann, $lang, bool $includeTitlePages = true)
    {
        foreach ($annexureFiles as $file) {
            $path = $file['path'] ?? '';
            $type = $file['type'] ?? 'unknown';
            $label = $file['title'] ?? $this->resolveAnnexureTitle($labels, $type, 'Annexure');

            if (!File::exists($path)) continue;
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            if ($includeTitlePages) {
                $titlePdfPath = $this->createAnnexureTitlePage($label);
                if ($titlePdfPath !== null) {
                    $pdfParts[] = $titlePdfPath;
                    if (!$this->isAttachmentAssetCachePath($titlePdfPath)) {
                        $tempPdfParts[] = $titlePdfPath;
                    }
                }
            }

            if ($extension === 'pdf') {
                $pdfParts[] = $path;
            } elseif (in_array($extension, ['xlsx', 'xls'], true)) {
                try {
                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                    $reader->setReadDataOnly(true);
                    $spreadsheet = $reader->load($path);
                    $sheets = $spreadsheet->getAllSheets();

                    $mpdf = new \Mpdf\Mpdf([
                        'mode' => 'utf-8',
                        'tempDir' => $this->getPdfTempDir(),
                        'fontDir' => array_merge(
                            (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
                            [public_path('fonts/tamil')]
                        ),
                        'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
                            'noto1' => ['R' => 'Latha.ttf', 'useOTL' => 0xFF],
                            'noto'  => ['R' => 'times.ttf', 'useOTL' => 0xFF],
                            'arial' => ['R' => 'arial.ttf', 'useOTL' => 0xFF],
                        ],
                        'default_font' => 'noto1',
                        'format' => 'A4',
                        'orientation' => 'P',
                        'margin_top' => 13,
                        'margin_bottom' => 13,
                        'margin_left' => 10,
                        'margin_right' => 10,
                    ]);

                    $style = '<style>table, td, th { border: 1px solid #000; border-collapse: collapse; font-size: 10pt; font-family: latha; }</style>';

                    foreach ($sheets as $sheetIndex => $sheet) {
                        $data = $sheet->toArray(null, false, false, true);
                        $isSheetEmpty = array_reduce($data, fn($carry, $row) =>
                        $carry && !array_filter($row, fn($cell) => trim((string)$cell) !== ''), true);

                        if ($isSheetEmpty) continue;
                        if ($sheetIndex > 0) $mpdf->AddPage();

                        $sheetTitle = $sheet->getTitle();
                        $nonEmptyColumns = [];
                        foreach ($data as $row) {
                            foreach ($row as $colIndex => $cell) {
                                if (trim((string)$cell) !== '') {
                                    $nonEmptyColumns[$colIndex] = true;
                                }
                            }
                        }

                        $excelHtml = $style;
                        $excelHtml .= "<h4 style='font-weight:bold; text-align:center; color:#2c3e50;'>{$sheetTitle}</h4>";
                        $excelHtml .= "<table width='100%' cellpadding='5'>";

                        foreach ($data as $row) {
                            if (!array_filter($row, fn($cell) => trim((string)$cell) !== '')) continue;

                            $excelHtml .= "<tr>";
                            foreach ($row as $colIndex => $cell) {
                                if (!isset($nonEmptyColumns[$colIndex])) continue;
                                $excelHtml .= "<td>" . htmlspecialchars($cell ?? '') . "</td>";
                            }
                            $excelHtml .= "</tr>";
                        }

                        $excelHtml .= "</table><br><br>";
                        $mpdf->WriteHTML(self::sanitizeMpdfInlineStyles($excelHtml));
                    }

                    $tempPdf = $this->createTempPdfPath('xlsx_');
                    $mpdf->Output($tempPdf, 'F');
                    $pdfParts[] = $tempPdf;
                    $tempPdfParts[] = $tempPdf;
                } catch (\Exception $e) {
                    \Log::warning("Failed to convert annexure Excel file '{$label}' to PDF: " . $e->getMessage());
                }
            }
        }

    }

    private function buildAttachmentBundleCacheKey(int $scheduleId, string $lang, array $slipAnnextureFiles, array $annexureFiles): string
    {
        $manifest = [
            'cache_version' => 'attachments_v2_first_page',
            'schedule_id' => $scheduleId,
            'lang' => $lang,
            'slip_files' => [],
            'pdf_files' => [],
            'xlsx_files' => [],
        ];

        foreach ($slipAnnextureFiles as $file) {
            $manifest['slip_files'][] = [
                'type' => $file->annexture_type ?? 'slip_related',
                'filepath' => $file->filepath ?? '',
                'signature' => $this->getAttachmentFileSignature($this->resolveStoredAttachmentPath($file->filepath ?? '')),
            ];
        }

        foreach ($annexureFiles['pdfFiles'] ?? [] as $file) {
            $manifest['pdf_files'][] = [
                'type' => $file->annexture_type ?? 'annexure',
                'filepath' => $file->filepath ?? '',
                'signature' => $this->getAttachmentFileSignature($this->resolveStoredAttachmentPath($file->filepath ?? '')),
            ];
        }

        foreach ($annexureFiles['xlsxFiles'] ?? [] as $file) {
            $manifest['xlsx_files'][] = [
                'type' => $file->annexture_type ?? 'annexure',
                'filepath' => $file->filepath ?? '',
                'signature' => $this->getAttachmentFileSignature($this->resolveStoredAttachmentPath($file->filepath ?? '')),
            ];
        }

        return sha1(json_encode($manifest));
    }

    private function getDownloadCachePath(string $type, int $scheduleId, string $lang, string $cacheKey, string $extension = 'pdf'): string
    {
        $cacheDir = $this->getPdfTempDir();
        File::ensureDirectoryExists($cacheDir);

        return $cacheDir . DIRECTORY_SEPARATOR . ucfirst($type) . "_{$scheduleId}_{$lang}_{$cacheKey}.{$extension}";
    }

    private function isReusableDownloadCache(string $path): bool
    {
        return File::exists($path) && filesize($path) > 0;
    }

    private function getAttachmentFileSignature(string $path): array
    {
        if (!File::exists($path)) {
            return ['missing'];
        }

        return [
            'size' => @filesize($path) ?: 0,
            'mtime' => @filemtime($path) ?: 0,
        ];
    }

    private function getAttachmentAssetCachePath(string $group, string $key, string $extension = 'pdf'): string
    {
        $cacheDir = $this->getPdfTempDir();
        File::ensureDirectoryExists($cacheDir);

        return $cacheDir . DIRECTORY_SEPARATOR . $group . '_' . $key . '.' . $extension;
    }

    private function isAttachmentAssetCachePath(string $path): bool
    {
        $cacheRoot = $this->getPdfTempDir();
        return strncmp($path, $cacheRoot, strlen($cacheRoot)) === 0;
    }

    private function convertExcelAttachmentToPdf(string $path, string $label, string $lang): ?string
    {
        if (!File::exists($path)) {
            return null;
        }

        try {
            $signature = $this->getAttachmentFileSignature($path);
            $cacheKey = sha1($path . '|' . json_encode($signature) . '|' . $lang);
            $cachedPdfPath = $this->getAttachmentAssetCachePath('excel-assets', $cacheKey);

            if (File::exists($cachedPdfPath) && filesize($cachedPdfPath) > 0) {
                return $cachedPdfPath;
            }

            $reader = IOFactory::createReaderForFile($path);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($path);
            $sheets = $spreadsheet->getAllSheets();

            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'tempDir' => $this->getPdfTempDir(),
                'fontDir' => array_merge(
                    (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
                    [public_path('fonts/tamil')]
                ),
                'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
                    'noto1' => ['R' => 'Latha.ttf', 'useOTL' => 0xFF],
                    'noto'  => ['R' => 'times.ttf', 'useOTL' => 0xFF],
                    'arial' => ['R' => 'arial.ttf', 'useOTL' => 0xFF],
                ],
                'default_font' => $lang === 'ta' ? 'noto1' : 'arial',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_left' => 8,
                'margin_right' => 8,
            ]);

            $style = '<style>table,td,th{border:1px solid #000;border-collapse:collapse;font-size:9pt;font-family:arial;}th,td{padding:3px;}</style>';
            $hasContent = false;

            foreach ($sheets as $sheetIndex => $sheet) {
                $data = $sheet->toArray(null, false, false, true);
                $isSheetEmpty = array_reduce($data, fn($carry, $row) => $carry && !array_filter($row, fn($cell) => trim((string) $cell) !== ''), true);

                if ($isSheetEmpty) {
                    continue;
                }

                if ($hasContent) {
                    $mpdf->AddPage();
                }

                $hasContent = true;
                $nonEmptyColumns = [];
                foreach ($data as $row) {
                    foreach ($row as $colIndex => $cell) {
                        if (trim((string) $cell) !== '') {
                            $nonEmptyColumns[$colIndex] = true;
                        }
                    }
                }

                $excelHtml = $style;
                $excelHtml .= "<h4 style='font-weight:bold; text-align:center; margin-bottom:8px;'>" . htmlspecialchars($sheet->getTitle()) . "</h4>";
                $excelHtml .= "<table width='100%' cellpadding='3'>";

                foreach ($data as $row) {
                    if (!array_filter($row, fn($cell) => trim((string) $cell) !== '')) {
                        continue;
                    }

                    $excelHtml .= '<tr>';
                    foreach ($row as $colIndex => $cell) {
                        if (!isset($nonEmptyColumns[$colIndex])) {
                            continue;
                        }
                        $excelHtml .= '<td>' . htmlspecialchars((string) ($cell ?? '')) . '</td>';
                    }
                    $excelHtml .= '</tr>';
                }

                $excelHtml .= '</table>';
                $mpdf->WriteHTML(self::sanitizeMpdfInlineStyles($excelHtml));
            }

            if (!$hasContent) {
                return null;
            }

            $mpdf->Output($cachedPdfPath, 'F');

            return $cachedPdfPath;
        } catch (\Exception $e) {
            \Log::warning("Failed to convert annexure Excel file '{$label}' to PDF: " . $e->getMessage());
            return null;
        }
    }

    private function resolveStoredAttachmentPath(string $filepath): string
    {
        $normalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, trim($filepath));
        if ($normalized === '') {
            return '';
        }

        $normalizedWithoutLeadingSlash = ltrim($normalized, DIRECTORY_SEPARATOR);
        $storagePublicPrefix = 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR;
        $publicUploadsPrefix = 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
        $uploadsPrefix = 'uploads' . DIRECTORY_SEPARATOR;

        if (preg_match('/^[A-Za-z]:\\\\/', $normalized)) {
            return $normalized;
        }

        if (strtolower(substr($normalizedWithoutLeadingSlash, 0, strlen($storagePublicPrefix))) === strtolower($storagePublicPrefix)) {
            return base_path($normalizedWithoutLeadingSlash);
        }

        if (strtolower(substr($normalizedWithoutLeadingSlash, 0, strlen($publicUploadsPrefix))) === strtolower($publicUploadsPrefix)) {
            return base_path($normalizedWithoutLeadingSlash);
        }

        if (strtolower(substr($normalizedWithoutLeadingSlash, 0, strlen($uploadsPrefix))) === strtolower($uploadsPrefix)) {
            return $this->resolveUploadsRelativePath($normalizedWithoutLeadingSlash);
        }

        if (substr($normalized, 0, 1) === DIRECTORY_SEPARATOR) {
            return $this->resolveStoredAttachmentPath($normalizedWithoutLeadingSlash);
        }

        return $this->buildStoragePublicFilePath($normalizedWithoutLeadingSlash, false);
    }

    private function resolveUploadsRelativePath(string $relativeUploadsPath): string
    {
        $trimmedPath = ltrim($relativeUploadsPath, DIRECTORY_SEPARATOR);
        $uploadsRelative = preg_replace('/^uploads[\\\\\/]?/i', '', $trimmedPath);
        $uploadsRelative = ltrim((string) $uploadsRelative, DIRECTORY_SEPARATOR);

        $candidates = [
            public_path('uploads' . DIRECTORY_SEPARATOR . $uploadsRelative),
            storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $uploadsRelative),
        ];

        foreach ($candidates as $candidate) {
            if (File::exists($candidate)) {
                return $candidate;
            }
        }

        return $candidates[0];
    }

    private function buildStoragePublicFilePath(string $relativePath, bool $ensureDirectory = false): string
    {
        $fullPath = storage_path('app' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $relativePath);

        if ($ensureDirectory) {
            File::ensureDirectoryExists(dirname($fullPath));
        }

        return $fullPath;
    }

    private function createAnnexureTitlePage(string $title): ?string
    {
        $title = trim($title);
        if ($title === '') {
            return null;
        }

        $cacheKey = sha1($title);
        $tempTitlePdf = $this->getAttachmentAssetCachePath('title-pages', $cacheKey);

        if (File::exists($tempTitlePdf) && filesize($tempTitlePdf) > 0) {
            return $tempTitlePdf;
        }

        if (preg_match('/[^\x00-\x7F]/', $title)) {
            $mpdfTitle = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'tempDir' => $this->getPdfTempDir(),
                'fontDir' => array_merge(
                    (new \Mpdf\Config\ConfigVariables())->getDefaults()['fontDir'],
                    [public_path('fonts/tamil')]
                ),
                'fontdata' => (new \Mpdf\Config\FontVariables())->getDefaults()['fontdata'] + [
                    'noto1' => ['R' => 'Latha.ttf', 'useOTL' => 0xFF],
                    'noto'  => ['R' => 'times.ttf', 'useOTL' => 0xFF],
                    'arial' => ['R' => 'arial.ttf', 'useOTL' => 0xFF],
                ],
                'default_font' => 'noto1',
                'format' => 'A4',
                'orientation' => 'P',
                'margin_top' => 13,
                'margin_bottom' => 13,
                'margin_left' => 10,
                'margin_right' => 10,
            ]);

            $titlePageHtml = '
                <div style="border:2px solid #000; height:270mm; width:100%;">
                    <table style="height:100%; width:100%; border-collapse:collapse;">
                        <tr>
                            <td style="text-align:center; vertical-align:middle; padding:130mm 10mm;">
                                <h1 style="font-weight:bold; text-align:center; color:#2c3e50; margin:0;">'
                                    . htmlspecialchars($title) .
                                '</h1>
                            </td>
                        </tr>
                    </table>
                </div>';

            $mpdfTitle->WriteHTML($titlePageHtml);
            $mpdfTitle->Output($tempTitlePdf, 'F');

            return $tempTitlePdf;
        }

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();
        $pdf->SetLineWidth(0.5);
        $pdf->Rect(10, 10, 190, 277);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetXY(20, 120);
        $pdf->MultiCell(170, 12, iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $title) ?: 'Annexure', 0, 'C');
        $pdf->Output('F', $tempTitlePdf);

        return $tempTitlePdf;
    }

    private function resolveAnnexureTitle($labels, string $type, string $default): string
    {
        if (is_array($labels)) {
            return $labels[$type] ?? $default;
        }

        return is_string($labels) && $labels !== '' ? $labels : $default;
    }

    private function getPdfTempDir(): string
    {
        $tempDir = base_path('mpdf_temp');

        File::ensureDirectoryExists($tempDir);

        return $tempDir;
    }

    private function createTempPdfPath(string $prefix): string
    {
        $tempPath = tempnam($this->getPdfTempDir(), $prefix);
        if ($tempPath === false) {
            throw new \RuntimeException('Unable to create a temporary PDF file.');
        }

        $pdfTempPath = $tempPath . '.pdf';
        @rename($tempPath, $pdfTempPath);

        return $pdfTempPath;
    }

// private function mergePdfFilesWithPdfcpu(
//     array $inputPaths,
//     string $outputPath,
//     string $profile = 'compressed'
// ): bool
// {
//     // $pdfcpu = env('PDFCPU_PATH');
// $pdfcpu = config('app.pdfcpu_path', env('PDFCPU_PATH'));
// //print_r($pdfcpu);
//     if (!$pdfcpu || !file_exists($pdfcpu)) {
//         throw new \RuntimeException('pdfcpu executable not found.');
//     }

//     $validPaths = array_values(array_filter(
//         $inputPaths,
//         fn ($path) => is_string($path) && File::exists($path)
//     ));

//     if (empty($validPaths)) {
//         return false;
//     }

//     return $this->runPdfcpuMerge(
//         $pdfcpu,
//         $validPaths,
//         $outputPath,
//         $profile
//     );
// }
private function mergePdfFilesWithPdfcpu(
    array $inputPaths,
    string $outputPath,
    string $profile = 'compressed'
): bool
{
    $pdfcpu = $this->getPdfcpuBinary();
    $validPaths = $this->getMergeablePdfPaths($inputPaths);

    if ($validPaths === []) {
        return false;
    }

    if ($pdfcpu === null) {
        \Log::warning('pdfcpu executable not found. Falling back to FPDI PDF merge.', [
            'outputPath' => $outputPath,
            'profile' => $profile,
        ]);

        return $this->mergePdfFilesWithFpdi($validPaths, $outputPath);
    }

    $merged = $this->runPdfcpuMerge(
        $pdfcpu,
        $validPaths,
        $outputPath,
        $profile
    );

    if ($merged) {
        return true;
    }

    \Log::warning('pdfcpu merge failed. Falling back to FPDI PDF merge.', [
        'outputPath' => $outputPath,
        'profile' => $profile,
    ]);

    return $this->mergePdfFilesWithFpdi($validPaths, $outputPath);
}

private function mergePdfFilesWithFpdi(array $inputPaths, string $outputPath): bool
{
    try {
        File::ensureDirectoryExists(dirname($outputPath));

        $pdf = new Fpdi();
        $hasPages = false;

        foreach ($inputPaths as $path) {
            $pageCount = $pdf->setSourceFile($path);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
                $hasPages = true;
            }
        }

        if (!$hasPages) {
            return false;
        }

        $pdf->Output('F', $outputPath);

        return File::exists($outputPath) && filesize($outputPath) > 0;
    } catch (\Throwable $exception) {
        \Log::error('FPDI PDF merge failed.', [
            'outputPath' => $outputPath,
            'error' => $exception->getMessage(),
        ]);

        return false;
    }
}


private function getMergeablePdfPaths(array $inputPaths): array
{
    $validPaths = [];

    foreach ($inputPaths as $path) {
        if (!is_string($path) || !File::exists($path)) {
            \Log::warning('Skipping missing PDF attachment before merge.', [
                'path' => $path,
            ]);
            continue;
        }

        if (!$this->isMergeablePdf($path)) {
            continue;
        }

        $validPaths[] = $path;
    }

    return $validPaths;
}

private function isMergeablePdf(string $path): bool
{
    clearstatcache(true, $path);

    if (!File::exists($path) || filesize($path) === 0) {
        \Log::warning('Skipping empty PDF attachment before merge.', [
            'path' => $path,
        ]);
        return false;
    }

    try {
        $fpdi = new Fpdi();
        $fpdi->setSourceFile($path);

        return true;
    } catch (\Throwable $exception) {
        \Log::warning('Skipping unreadable PDF attachment before merge.', [
            'path' => $path,
            'error' => $exception->getMessage(),
        ]);
        return false;
    }
}




private function runPdfcpuMerge(
    string $pdfcpu,
    array $inputPaths,
    string $outputPath,
    string $profile = 'compressed'
): bool
{
    $tempRoot = sys_get_temp_dir();
    $configRoot = $tempRoot . DIRECTORY_SEPARATOR . '.config';
    $pdfcpuConfigDir = $configRoot . DIRECTORY_SEPARATOR . 'pdfcpu';

    putenv('HOME=' . $tempRoot);
    putenv('XDG_CONFIG_HOME=' . $configRoot);

    if (!is_dir($pdfcpuConfigDir)) {
        @mkdir($pdfcpuConfigDir, 0777, true);
    }

    $command =
        escapeshellarg($pdfcpu)
        . ' merge '
        . escapeshellarg($outputPath)
        . ' '
        . implode(' ', array_map('escapeshellarg', $inputPaths))
        . ' 2>&1';

    exec($command, $output, $returnCode);

    \Log::info('PDFCPU Merge', [
        'command' => $command,
        'returnCode' => $returnCode,
        'output' => $output
    ]);

    return $returnCode === 0;
}





// private function runPdfcpuMerge(
//     string $pdfcpu,
//     array $inputPaths,
//     string $outputPath,
//     string $profile = 'compressed'
// ): bool
// {
//     putenv('HOME=/tmp');
//     putenv('XDG_CONFIG_HOME=/tmp/.config');

//     if (!is_dir('/tmp/.config/pdfcpu')) {
//         mkdir('/tmp/.config/pdfcpu', 0777, true);
//     }

//     $command =
//         escapeshellarg($pdfcpu)
//         . ' merge '
//         . escapeshellarg($outputPath)
//         . ' '
//         . implode(' ', array_map('escapeshellarg', $inputPaths))
//         . ' 2>&1';

//     exec($command, $output, $returnCode);

//     \Log::info('PDFCPU Merge', [
//         'command' => $command,
//         'returnCode' => $returnCode,
//         'output' => $output
//     ]);

//     return $returnCode === 0;
// }

 private function getPdfcpuBinary(): ?string
{
    $candidates = array_filter([
        env('PDFCPU_PATH'),
        env('PDFCPU_BIN'),
        '/usr/bin/pdfcpu',
        '/usr/local/bin/pdfcpu',
        '/bin/pdfcpu',
        'pdfcpu',
    ]);

    $nullDevice = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
        ? 'NUL'
        : '/dev/null';

    foreach ($candidates as $candidate) {

        $command =
            escapeshellarg($candidate)
            . " version 2>$nullDevice";

        exec($command, $output, $resultCode);

        if ($resultCode === 0) {
            return $candidate;
        }
    }

    return null;
}

//    private function getPdfcpuBinary(): ?string
// {
//     $candidates = array_filter([
//         env('PDFCPU_BIN'),
//        // '/usr/local/bin/pdfcpu',
//         '/bin/pdfcpu',
//     ]);

//     $nullDevice = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'
//         ? 'NUL'
//         : '/dev/null';

//     foreach ($candidates as $candidate) {

//         $command =
//             escapeshellarg($candidate)
//             . " version 2>$nullDevice";

//         exec($command, $output, $resultCode);

//         if ($resultCode === 0) {
//             return $candidate;
//         }
//     }

//     return null;
// }


private function optimizePdf(
    string $pdfcpu,
    string $pdfPath
): bool
{
    $command =
        escapeshellarg($pdfcpu)
        . ' optimize '
        . escapeshellarg($pdfPath)
        . ' 2>&1';

    exec($command, $output, $returnCode);

    \Log::info('PDFCPU Optimize', [
        'command' => $command,
        'returnCode' => $returnCode,
        'output' => $output
    ]);

    return $returnCode === 0;
}

    private function applyFontByLanguage(string $html, bool $pageBreak = true): string
    {

    $html = self::sanitizeMpdfInlineStyles($html);


        // Load and clean HTML
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        // Remove existing font-family styles
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//*[@style]') as $el) {
            $style = $el->getAttribute('style');
            $rules = array_filter(array_map('trim', explode(';', $style)), function ($rule) {
                return $rule !== '' && stripos($rule, 'font-family') !== 0;
            });
            if (count($rules)) {
                $el->setAttribute('style', implode('; ', $rules));
            } else {
                $el->removeAttribute('style');
            }
        }

        $body = $dom->getElementsByTagName('body')->item(0);
        $cleanHtml = '';
        foreach ($body->childNodes as $child) {
            $cleanHtml .= $dom->saveHTML($child);
        }

        // Regex to split by language/script (Tamil vs Non-Tamil)
        $segments = preg_split('/((?:[\x{0B80}-\x{0BFF}]+))/u', $cleanHtml, -1, PREG_SPLIT_DELIM_CAPTURE);

        // Initialize output
        $output = '';

        // No @page style or border here   just font styling

        // Optional page break div if needed
        if ($pageBreak && !empty(trim($cleanHtml))) {
            $output .= '<div ></div>';
        }

        // Process segments and apply font styles
        foreach ($segments as $segment) {
            if (preg_match('/[\x{0B80}-\x{0BFF}]/u', $segment)) {
                // Tamil font
                $output .= '<span style="font-family: noto1; font-size: 12pt;">' . $segment . '</span>';
            } elseif (trim(strip_tags($segment)) !== '') {
                // Non-empty English or others

                $output .= '<span style="font-family: times; font-size: 13pt;">' . $segment . '</span>';
            } else {
                // Keep spacing/markup if needed
                $output .= $segment;
            }
        }

        return $output;
    }

    private static function sanitizeMpdfInlineStyles(string $html): string
    {
        $html = preg_replace_callback('/\sstyle=(["\'])(.*?)\1/is', function ($matches) {
            $style = self::sanitizeMpdfStyleDeclarations($matches[2]);

            return $style !== '' ? ' style="' . $style . '"' : '';
        }, $html);

        $html = preg_replace_callback('/<style\b[^>]*>(.*?)<\/style>/is', function ($matches) {
            $css = preg_replace_callback('/([^{}]+)\{([^{}]*)\}/s', function ($rule) {
                $declarations = self::sanitizeMpdfStyleDeclarations($rule[2]);

                return $declarations !== '' ? $rule[1] . '{' . $declarations . '}' : '';
            }, $matches[1]);

            return '<style>' . $css . '</style>';
        }, $html);

        return preg_replace_callback('/\s(color|bgcolor|bordercolor)=(["\'])(.*?)\2/is', function ($matches) {
            $attribute = strtolower($matches[1]);
            $value = trim($matches[3]);

            if ($value === '') {
                return '';
            }

            $value = preg_replace('/\bwindowtext\b/i', '#000000', $value);
            $value = preg_replace('/\b(currentcolor|initial|unset|revert)\b/i', 'inherit', $value);

            if (self::mpdfStyleHasUnsupportedColor($attribute, $value)) {
                return '';
            }

            return ' ' . $attribute . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
        }, $html);
    }

    private static function sanitizeMpdfStyleDeclarations(string $styleString): string
    {
        $safeStyles = [];

        foreach (explode(';', $styleString) as $style) {
            $style = trim($style);

            if ($style === '' || strpos($style, ':') === false) {
                continue;
            }

            [$property, $value] = array_map('trim', explode(':', $style, 2));
            $property = strtolower($property);
            $value = trim($value);

            if ($value === '' || strpos($property, 'mso-') === 0 || strpos($property, '--') === 0) {
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

            if (self::mpdfStyleHasUnsupportedColor($property, $value)) {
                continue;
            }

            $safeStyles[] = $property . ': ' . $value;
        }

        return implode('; ', $safeStyles);
    }

    private static function mpdfStyleHasUnsupportedColor(string $property, string $value): bool
    {
        if (!preg_match('/(^|-)color$|^background$|^border($|-)|^bgcolor$|^bordercolor$/i', $property)) {
            return false;
        }

        if (stripos($value, 'var(') !== false || stripos($value, 'calc(') !== false || strpos($value, '/') !== false) {
            return true;
        }

        if (preg_match_all('/#[0-9a-f]{1,8}\b/i', $value, $matches)) {
            foreach ($matches[0] as $hex) {
                $length = strlen($hex) - 1;
                if (!in_array($length, [3, 6], true)) {
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



    private function GeneratePartAContentsLoad()
    {
        $templatePath = resource_path('views/pdf/first_page_template.html');
        $htmlTemplate = File::get($templatePath);

        return $htmlTemplate;
    }

    private function coveringletter($auditscheduleid, $lang, $certypetxt, $auditEndDate, $spilloverflag, $instid, $financialyearcode)
    {
        $loaddata = $this->loadAllValues($auditscheduleid, $lang, $spilloverflag, $instid, $financialyearcode);

        $financialYear = $loaddata['yearname'];

        if ($lang === 'en') {
            $yeartype = 'Financial Year';
        } else {
            $yeartype = 'நிதி ஆண்டு';
        }

        if ($loaddata['deptcode'] == '01') {
            if ($lang === 'en')
                $yeartype = 'Fasli Year';
            else
                $yeartype = 'ஃபஸ்லி ஆண்டு';
        }
        if ($loaddata['deptcode'] == '01' && !empty($loaddata['annadhanamyearname'])) {
            if ($lang === 'en')
                $annadhanamyearcontent = ' and Annadhanam Year ' . $loaddata['annadhanamyearname'];
            else
                $annadhanamyearcontent = ' மற்றும் அன்னதான ஆண்டு  ' . $loaddata['annadhanamyearname'];
        } else {
            $annadhanamyearcontent = '';
        }

        $certificate_opinion = $certypetxt . ' Opinion';

        // print_r($certypetxt);
        // exit;

        $AuditeeInstDet = DB::table('audit.auditee_dept_reporting')
            ->where('instid', $loaddata['instid'])
            ->select('designation')
            ->orderBy('auditeedesigid', 'asc')
            ->first();

        if (!$AuditeeInstDet) {
            $auditeedesig = 'to be filled';
        } else {
            $auditeedesig = $AuditeeInstDet->designation;
        }
        $templateJson = json_decode(file_get_contents(public_path('json/report.json')), true);

        $template = $templateJson[$lang]['coverletter_' . $lang];

        $htmlTemplate = $template;

        // $htmlTemplate = File::get($templatePath);
        //  print_r($templatePath);
        //         exit;
        // Break address by comma
        $auditaddress = explode(',', $loaddata['auditeeofficeaddress']);
        //   print_r($templatePath);
        // exit;
        // Convert to <br> separated string
        $formattedAddress = implode('<br>', array_map('trim', $auditaddress));

        // Prepare final HTML
        $auditeeofficeaddress = '<p style="font-weight:normal !important;">' . $auditeedesig . '<br>' . $formattedAddress . '</p>';

        // print_r($auditeeofficeaddress);
        // exit;

        if ($lang === 'en') {
            $teamHtml = '';
            $teamCount = 0;

            // Count total English teams
            foreach ($loaddata as $key => $value) {
                if (str_starts_with($key, 'Teamdetails') && str_ends_with($key, '_en')) {
                    $teamCount++;
                }
            }

            // $i = 0;
            // foreach ($loaddata as $key => $value) {
            //     if (str_starts_with($key, 'Teamdetails') && str_ends_with($key, '_en')) {
            //         $quarterKey = "quartercode{$i}";
            //         $quarterName = isset($loaddata[$quarterKey]) ? htmlspecialchars($loaddata[$quarterKey]) : ($i + 1);

            //         // If multiple teams exist, add "Team for Quarter X"
            //         if ($teamCount > 1) {
            //             $teamHtml .= '<div style="margin-top:10px; font-weight:bold;">Team for Quarter ' . $quarterName . '</div>';
            //         }

            //         // Append team details block
            //         $teamHtml .= '<div style="margin-bottom: 15px;">' . $value . '</div>';
            //         $i++;
            //     }
            // }
            $i = 0;
            foreach ($loaddata as $key => $value) {
                if (str_starts_with($key, 'Teamdetails') && str_ends_with($key, '_en')) {
                    $quarterKey = "TeamQuarter{$i}";
                    $quarterName = isset($loaddata[$quarterKey])
                        ? htmlspecialchars($loaddata[$quarterKey])
                        : ($i + 1);

                    if ($teamCount > 1) {
                        $teamHtml .= '<div style="margin-top:10px; font-weight:bold;">Team for ' . $quarterName . '</div>';
                    }

                    $teamHtml .= '<div style="margin-bottom: 15px;">' . $value . '</div>';
                    $i++;
                }
            }
            // Fallback if only Teamdetails (no _en versions)
            if ($teamCount === 0 && !empty($loaddata['Teamdetails'])) {
                $teamHtml = $loaddata['Teamdetails'];
            }

            $replacements = [
                '[audityear]' => $financialYear,
                '[AuditeeInstitution]' => $loaddata['instename'],
                '[TeamDet]' => $teamHtml,
                '[yeartype]' => $yeartype,
                '[certificateopinion]' => $certificate_opinion,
                '[auditeeInstDetails]' => $auditeeofficeaddress,
                '[endateofaudit]' => $auditEndDate,
                '[Date of Signing]' => date('d/m/Y'),
                '[annadhanamyearcontent]' => $annadhanamyearcontent,
            ];
        } else {
            // Tamil version
            $teamHtml = '';
            $teamCount = 0;

            // Count total Tamil teams
            foreach ($loaddata as $key => $value) {
                if (str_starts_with($key, 'Teamdetails') && str_ends_with($key, '_ta')) {
                    $teamCount++;
                }
            }

            $i = 0;
            foreach ($loaddata as $key => $value) {
                if (str_starts_with($key, 'Teamdetails') && str_ends_with($key, '_ta')) {
                    $quarterKey = "quartercode{$i}";
                    $quarterName = isset($loaddata[$quarterKey]) ? htmlspecialchars($loaddata[$quarterKey]) : ($i + 1);

                    // Tamil "Team for Quarter" text if multiple teams exist
                    if ($teamCount > 1) {
                        $teamHtml .= '<div style="margin-top:10px; font-weight:bold;">' . $quarterName . ' காலாண்டிற்கான குழு</div>';
                    }

                    // Append Tamil team details
                    $teamHtml .= '<div style="margin-bottom: 15px;">' . $value . '</div>';
                    $i++;
                }
            }

            if ($teamCount === 0 && !empty($loaddata['Teamdetails'])) {
                $teamHtml = $loaddata['Teamdetails'];
            }

            $replacements = [
                '[audityear]' => $financialYear,
                '[AuditeeInstitution]' => $loaddata['insttname'],
                '[TeamDet]' => $teamHtml,
                '[yeartype]' => $yeartype,
                '[certificateopinion]' => $certificate_opinion,
                '[auditeeInstDetails]' => $auditeeofficeaddress,
                '[endateofaudit]' => $auditEndDate,
                '[Date of Signing]' => date('d/m/Y'),
                '[annadhanamyearcontent]' => $annadhanamyearcontent,
            ];
        }

        $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlTemplate);

        return '<div class="section-content">' . $htmlContent . '</div>';

        // Replace placeholders
        $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlTemplate);

        return '<div class="section-content">' . $htmlContent . '</div>';

        // Replace placeholders
        $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlTemplate);

        return '<div class="section-content">' . $htmlContent . '</div>';
    }


     private function generateFirstPageHtml($deptsize, $DeptName, $base64Image, $DistName, $InstituteName, $FinancialYear, $AuditReport_Year, $AuditReport_Text, $annadhanamyearcontent = '')
    {
        // Load the HTML template
        $templatePath = resource_path('views/pdf/first_page_template.html');
        $htmlTemplate = File::get($templatePath);

        // Define replacements
        $replacements = [
            '{{deptsize}}'         => $deptsize,
            '{{DeptName}}'         => htmlspecialchars($DeptName),
            '{{base64Image}}'      => $base64Image,
            '{{DistName}}'         => htmlspecialchars($DistName),
            '{{InstituteName}}'    => htmlspecialchars($InstituteName),
            '{{FinancialYear}}'    => htmlspecialchars($FinancialYear),
            '{{annadhanamyearcontent}}' => htmlspecialchars($annadhanamyearcontent),
            '{{AuditReport_Year}}' => htmlspecialchars($AuditReport_Year),
            '{{AuditReport_Text}}' => htmlspecialchars($AuditReport_Text),
        ];

        // Replace all placeholders
        $htmlContent = str_replace(array_keys($replacements), array_values($replacements), $htmlTemplate);

        return '<div class="section-content">' . $htmlContent . '</div>';
    }

    private function getFirstPageAnnadhanamYearContent($deptcode, string $lang, $annadhanamyearname): string
    {
        if ((string) $deptcode !== '01' || empty($annadhanamyearname)) {
            return '';
        }

        if ($lang === 'en') {
            return ' and Annadhanam Year ' . $annadhanamyearname;
        }

        return ' மற்றும் அன்னதான ஆண்டு ' . $annadhanamyearname;
    }


    private function workallocationpdf($auditscheduleid, $lang)
    {
        $workAllocation = FormatModel::fetch_allocatedwork($auditscheduleid);
        $labels = $this->loadlabels();
        $nodata_avail = $labels[$lang]['nodata_avail'];

        $html = '';

        if (!$workAllocation->isEmpty()) {
            $results = [];
            foreach ($workAllocation->all() as $item) {
                $results[] = [
                    'username' => $item->username,
                    'worktypes_first' => $item->worktypes_first,
                ];
            }

            if (!empty($results)) {
                $html .= '<h3 style="text-align: center;">5. WORK ALLOCATION</h3>';
                $html .= '<div style="page-break-inside: avoid;">';  // Prevent breaking inside table
                $html .= '<table border="1" cellpadding="8" cellspacing="0" width="100%" style="border-collapse: collapse;">';
                $html .= '<thead>';
                $html .= '<tr>';
                $html .= '<th style="width: 10%; font-weight: bold;">S.No.</th>';
                $html .= '<th style="width: 45%; font-weight: bold;">Team Member Name</th>';
                $html .= '<th style="width: 45%; font-weight: bold;">Work Allocation</th>';
                $html .= '</tr>';
                $html .= '</thead><tbody>';

                $serialNumber = 1;
                foreach ($results as $entry) {
                    $html .= '<tr>';
                    $html .= '<td>' . $serialNumber++ . '</td>';
                    $html .= '<td>' . htmlspecialchars($entry['username']) . '</td>';
                    $html .= '<td>' . $entry['worktypes_first'] . '</td>';
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
                $html .= '</div>';
            }
        }

        return $html;
    }

    /**
     * Make the HTML content editable by adding contenteditable="true" to all text elements,
     * but exclude images from being editable.
     */
    private function makeEditablenew($htmlContent)
    {
        // Use DOMDocument to manipulate the HTML and make text fields editable
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);  // Disable warnings for invalid HTML structure

        // Load the HTML content
        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Create an XPath object to search for elements
        $xpath = new \DOMXPath($dom);

        // Find all text nodes (excluding images) and set them to editable
        foreach ($xpath->query('//body') as $element) {
            // Check if the element is not an image
            if ($element->tagName !== 'img') {
                $element->setAttribute('contenteditable', 'true');
            }
        }

        // Return the modified HTML content
        return $dom->saveHTML();
    }

    /**
     * Make the HTML content editable by adding contenteditable="true" to all elements.
     */
    private function makeEditable($htmlContent)
    {
        // Use DOMDocument to manipulate the HTML and make text fields editable
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);  // Disable warnings for invalid HTML structure

        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Find all text nodes and set them to editable
        $xpath = new \DOMXPath($dom);
        // Make only the .content-wrapper div editable
        foreach ($xpath->query('//div[contains(@class, "content-wrapper")]|//body') as $contentWrapper) {
            $contentWrapper->setAttribute('contenteditable', 'true');
        }

        // Ensure individual child elements inside .content-wrapper are NOT editable
        foreach ($xpath->query('//div[contains(@class, "content-wrapper")]//*') as $childElement) {
            $childElement->removeAttribute('contenteditable');
        }

        // Return the modified HTML content
        return $dom->saveHTML();
    }

    private function addEmptySpaceForPreview($htmlContent)
    {
        // Calculate the total height of content (you can use JS to dynamically get the height in the front-end, but for simplicity, let's assume a fixed height here)
        $contentHeight = 5000;  // For example, assume content height is 5000px (adjust dynamically as needed)
        $pageHeight = 8000;  // Assume page height is 8000px

        // Calculate the empty space to be added
        $emptySpaceHeight = $pageHeight - $contentHeight;

        // If content is smaller than the page, add empty space
        if ($emptySpaceHeight > 0) {
            $htmlContent .= '<div style="height:' . $emptySpaceHeight . 'px;"></div>';
        }

        return $htmlContent;
    }

    private function addBordersToHtml($htmlContent)
    {
        // Add a style for the border of the content and page breaks
        $htmlContent = '
        <style>

            .content-wrapper {
                border: 2px solid #000;
                padding: 20px;
                margin: 20px auto;
                width: 90%;
                box-sizing: border-box;
            }
            .page-content {
                padding: 10px;
            }
            .highlight {
                background-color: yellow;
            }
            table {
                border-collapse: collapse;  /* Ensures single line borders for the table */
                width: 100%;
            }
            th, td {
                border: 1px solid black;  /* Single line border for table cells */
                padding: 5px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }




            img
            {
               width:200px !important;
               height:200px !important;
            }




        </style>
        <div class="content-wrapper">
            <div class="page-content">
                ' . $htmlContent . '
            </div>
        </div>';

        return $htmlContent;
    }

    private function parseHtmlToWord($section, $htmlContent, $lang)
    {
        // Set default font and size based on language
        $fontSettings = $this->getFontSettings($lang);
        $fontName = $fontSettings['name'];
        $defaultSize = $fontSettings['size'];

        // Keep only allowed HTML tags
        $htmlContent = strip_tags($htmlContent, '<b><h1><h2><h4><h5><h6><p><ul><ol><li><table><tr><td><pre>');

        $paragraphStyle = [
            'lineHeight' => 0.5,  // 1.5x line spacing
            'spaceAfter' => 200,  // Adds spacing after paragraphs
        ];

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body)
            return;

        foreach ($body->childNodes as $node) {
            $this->processNode($section, $node, $fontName, $defaultSize, $lang, null);
        }
    }

    // Function to get font settings based on language
    private function getFontSettings($lang)
    {
        $fonts = [
            'ta' => ['name' => 'Latha', 'size' => 10],  // Tamil
            'en' => ['name' => 'Times New Roman', 'size' => 13],  // English
        ];

        return $fonts[$lang] ?? $fonts['en'];  // Default to English settings
    }

    // Function to get default line height based on language
    private function getLineHeightSettings($lang)
    {
        $lineHeights = [
            'ta' => 0.8,  // Tamil (example: higher line height for readability)
            'en' => 1.4,  // English (default)
        ];

        return $lineHeights[$lang] ?? $lineHeights['en'];  // Default to English if not found
    }

    private function processNode($section, $node, $fontName, $defaultSize, $lang, $tableCell = null)
    {
        $text = trim($node->textContent);
        if (empty($text))
            return;

        $styles = ['name' => $fontName, 'size' => $defaultSize];
        $alignment = [];

        // Get base line height for language
        $baseLineHeight = $this->getLineHeightSettings($lang);
        $lineHeight = $baseLineHeight;  // Default line height for general text

        switch ($node->nodeName) {
            case 'h1':
                $styles['bold'] = true;
                $styles['size'] = 16;
                $alignment = ['alignment' => 'center'];
                $lineHeight = $baseLineHeight * 1.5;
                break;

            case 'h2':
                $styles['bold'] = true;
                $styles['size'] = 14;
                $alignment = ['alignment' => 'center'];
                $lineHeight = $baseLineHeight * 1.4;
                break;

            case 'h4':
                $styles['bold'] = true;
                $styles['size'] = 13;
                $alignment = ['alignment' => 'center'];
                $lineHeight = $baseLineHeight * 1.3;
                break;

            case 'h5':
            case 'h6':
                $styles['bold'] = true;
                $lineHeight = $baseLineHeight * 1.2;
                break;

            case 'b':
                $styles['bold'] = true;
                break;

            case 'p':
                $alignment = ['lineHeight' => $baseLineHeight];  // Adjust paragraph spacing
                $lineHeight = $baseLineHeight;
                break;

            case 'pre':
                $styles['underline'] = true;
                $lineHeight = $baseLineHeight * 1.6;
                break;

            case 'ul':
            case 'ol':
                $isOrdered = ($node->nodeName === 'ol');
                foreach ($node->getElementsByTagName('li') as $li) {
                    $section->addListItem(
                        trim($li->textContent),
                        0,
                        $styles,
                        $isOrdered
                            ? \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER
                            : \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET
                    );
                }
                return;

            case 'table':
                $table = $section->addTable(['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 100]);
                foreach ($node->getElementsByTagName('tr') as $tr) {
                    $tableRow = $table->addRow();
                    foreach ($tr->getElementsByTagName('td') as $td) {
                        $colspan = $td->getAttribute('colspan') ? intval($td->getAttribute('colspan')) : 1;
                        $cellWidth = 3000 * $colspan;

                        $tableCell = $tableRow->addCell($cellWidth, ['gridSpan' => $colspan, 'borderSize' => 6]);

                        foreach ($td->childNodes as $childNode) {
                            $this->processNode($section, $childNode, $fontName, $defaultSize, $lang, $tableCell);
                        }
                    }
                }
                return;
        }

        // Apply dynamic line height based on language
        $alignment['lineHeight'] = $lineHeight;

        if ($tableCell) {
            $tableCell->addText($text, $styles, $alignment);
        } else {
            $section->addText($text, $styles, $alignment);
        }
    }

    // Function to download the generated Word file
    public function downloadWordFile($fileName)
    {
        try {
            // Define the file path for download
            $filePath = public_path('files/' . $fileName);

            // Check if the file exists
            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            // Ensure the content type is correctly set for Word files
            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
        } catch (\Exception $e) {
            // Log the error and return a response
            \Log::error('Error in downloadWordFile: ' . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function deleteFile(Request $request)
    {
        $fileName = $request->input('fileName');  // Get the file name from the request

        // Define the file path for deletion
        $filePath = public_path('files/' . $fileName);

        // Check if the file exists and delete it
        if (file_exists($filePath)) {
            unlink($filePath);  // Delete the file
            return response()->json(['success' => 'File deleted successfully']);
        }

        return response()->json(['error' => 'File not found'], 404);
    }

    private function cleanForMPDF($html)
    {
        if (!is_string($html) || empty($html)) {
            return '';
        }

        // 1. Remove unsupported HTML5 semantic tags
        $html = preg_replace('/<\/?(section|article|nav|aside|header|footer)[^>]*>/i', '', $html);

        // 2. Remove scripts, styles, iframes, etc.
        $html = preg_replace('/<(script|style|iframe|object|embed)[^>]*>.*?<\/\1>/is', '', $html);

        // 3. Remove class and id attributes
        $html = preg_replace('/\s(class|id)="[^"]*"/i', '', $html);

        // 4. Sanitize inline styles
         $html = self::sanitizeMpdfInlineStyles($html);
        // $html = preg_replace_callback('/style="([^"]*)"/i', function ($matches) {
        //     $styles = explode(';', $matches[1]);
        //     $safeStyles = [];

        //     foreach ($styles as $style) {
        //         $style = trim($style);

        //         // Skip empty or malformed
        //         if (strpos($style, ':') === false)
        //             continue;

        //         // Skip Word-generated styles like windowtext, pt, etc.
        //         if (stripos($style, 'windowtext') !== false || stripos($style, 'pt') !== false) {
        //             continue;
        //         }

        //         $safeStyles[] = $style;
        //     }

        //     return count($safeStyles) ? 'style="' . implode('; ', $safeStyles) . '"' : '';
        // }, $html);

        return $html;
    }

    public function sendAuditeeMail(Request $request)
    {
        try {
            $scheduleId = Crypt::decryptString($request->schedule_id);
            if (empty($scheduleId)) {
                throw new \Exception('schedule Details not found');
            }

            // print_r($scheduleId);
            // exit;

            $user = session('user');
            $userId = $user->userid ?? null;
            $username = $user->username ?? null;

            // return $request->all();

            $auditeedetails = FormatModel::getauditeedetails($scheduleId);

            if (!$auditeedetails) {
                return response()->json(['error' => 'Auditee not found for this schedule.'], 404);
            }

            $instid = $auditeedetails[0]->instid;

            $data = [
                'sendername' => $username,
                'userid' => $userId,
                'email' => $auditeedetails[0]->auditeeemail,
                // 'email' => 'nijisa18@gmail.com',
                'auditeeusername' => $auditeedetails[0]->auditeeusername,
                'issuedby' => $username,
                'issuedon' => View::shared('get_nowtime'),
                // 'ccEmails' => $ccEmails
            ];

            $Lang = 'en';
            $auditModel = new SmsmailModel(new SmsService(), new PHPMailerService());
            $sentsms = $auditModel->sendauditeerportmail($data, $Lang, $scheduleId, $instid);

            //  return $sentsms;

            if ($sentsms) {
                return response()->json(['success' => true, 'message' => 'Audit Report issued successfully']);
            } else {
                return response()->json(['error' => 'Failed to sent Mail'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
            //  return response()->json(['error' => 'Failed to sent Mail'], 500);
        }
    }

    public static function reportdeptfetch()
    {
        $dept = FormatModel::commondeptfetch();
        $quarter = FormatModel::getQuarter();
        $financialyear = FormatModel::getDFinancialyear();

        $selectedQuarters = 'Q2';

        return view('audit.reportrevoke', compact('dept', 'quarter', 'selectedQuarters', 'financialyear'));

        // return view('audit.reportrevoke', compact('dept'));
    }

    public function getregionbasedondeptforreportdept(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'financialyear' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');
        $financialyear = $request->input('financialyear');

        $regions = FormatModel::getRegionsByDept($deptcode);
        $quarter = FormatModel::getquarteryDeptandfin($deptcode, $financialyear);

        return response()->json([
            'success' => true,
            'data' => $regions,
            'quarter' => $quarter
        ]);
    }

    public function getdistrictbasedonregionreport(Request $request)
    {
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

        $district = FormatModel::getdistrictByregion($regioncode, $deptcode);

        if ($district->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $district]);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function getinstitutionbasedondistreport(Request $request)
    {
        // Validate the input
        $request->validate([
            'region' => ['required', 'string', 'regex:/^\d+$/'],
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'district' => ['required', 'string', 'regex:/^\d+$/'],
            'auditquartercode' => ['required', 'string'],
        ], [
            'region.required' => 'The :attribute field is required.',
            'region.regex' => 'The :attribute field must be a valid number.',
            'deptcode.required' => 'The deptcode field is required.',
            'deptcode.regex' => 'The deptcode field must be a valid number.',
            'district.required' => 'The district field is required.',
            'district.regex' => 'The district field must be a valid number.',
        ]);

        $regioncode = $request->input('region');
        $deptcode = $request->input('deptcode');
        $district = $request->input('district');
        $auditquartercode = $request->input('auditquartercode');

        $institution = FormatModel::getinstitutionBydistrictchange($district, $regioncode, $deptcode, $auditquartercode);

        if ($institution->isNotEmpty()) {
            return response()->json(['success' => true, 'data' => $institution]);
        } else {
            return response()->json(['success' => false, 'message' => 'No Institutions found'], 200);
        }
    }

    public function report_fetchData(Request $request)
    {
        $auditscheduleid = $request->has('auditscheduleid') ? Crypt::decryptString($request->auditscheduleid) : null;
        $auditschedule = FormatModel::reportfetchData('audit.inst_schteammember');
        if (is_iterable($auditschedule)) {
            foreach ($auditschedule as $all) {
                $all->encrypted_sauditscheduleid = Crypt::encryptString($all->auditscheduleid);
                unset($all->auditscheduleid);
            }
        }

        return response()->json([
            'success' => true,
            'message' => empty($auditschedule) ? 'No Details found' : '',
            'data' => $auditschedule ?? []
        ], 200);
    }

    public function report_insertupdate(Request $request)
    {
        // print_r($_REQUEST);

        try {
            $rules = [
                'deptcode' => 'required|string|regex:/^\d+$/',
                'regioncode' => 'required|string|regex:/^\d+$/',
                'distcode' => 'required|string|regex:/^\d+$/',
                'instmappingcode' => 'required|string|regex:/^\d+$/',
                // 'usernamefield' => 'integer',
                'revoke' => 'integer'
            ];

            $auditplan = session('user');
            if (!$auditplan || !isset($auditplan->userid)) {
                return response()->json(['success' => false, 'message' => 'charge session not found or invalid.'], 400);
            }
            $userchargeid = $auditplan->userid;

            $data = [
                'auditscheduleid' => $request->auditscheduleid ?? null,
                'instmappingcode' => $request->instmappingcode ?? null,
                'revoke' => $request->revoke ?? null,
            ];

            $result = FormatModel::auditreportrevoke_insertupdate($data, 'audit.inst_schteammember', $userchargeid);
            return response()->json(['success' => true, 'message' => 'auditdiaryupdated']);
        } catch (ValidationException $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 401], 401);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], $e->getMessage() === 'Record already exists with the provided conditions.' ? 422 : 500);
        }
    }

    private function convertPdfTo14($inputPath, $outputPath)
    {
        $inputPath = trim($inputPath);
        $outputPath = trim($outputPath);

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $gsExe = '"C:\Program Files\gs\gs9.18\bin\gswin64c.exe"';
        } else {
            $gsExe = 'gs';  // Linux AWS
        }

        $command = $gsExe
            . ' -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dBATCH -dSAFER'
            . ' -sOutputFile=' . escapeshellarg($outputPath)
            . ' ' . escapeshellarg($inputPath)
            . ' 2>&1';

        exec($command, $outputLines, $resultCode);

        return ($resultCode === 0 && file_exists($outputPath) && filesize($outputPath) > 100);
    }

    public function callcodecheck(Request $request)
    {
        // Ensure all inputs are arrays
        $deptcode = (array) $request->input('deptcode', []);
        $category = (array) $request->input('category', []);
        $subcategory = (array) $request->input('subcategory', []);
        $instmappingcode = (array) $request->input('instmappingcode', []);

        // Fetch data using filters
        $data = FormatModel::fetchQuarterData($deptcode, $category, $subcategory, $instmappingcode);

        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function finalizedinstitutesforreport()
    {
        $userData = session('user');
        $userChargeData = session('charge');
        $sessiondeptcode =  $userChargeData->deptcode;
        $page = 'reportdownload';
        $session_userid = $userData->userid;
        $quatdetails = FormatModel::fetch_quarterdetails($sessiondeptcode,$page);

        return view('audit.reportdownload', compact('quatdetails'));
    }

    /* Template Audit Reprt***************************************** */


    private function renderFirstPage($data)
    {
        return $this->renderTemplate('first_page_template.html', $data);
    }

    private function renderTemplate($templateFile, $dynamicData = [])
    {
        $templatePath = resource_path('views/pdf/intropage.html');

        if (!file_exists($templatePath)) {
            return "<p>Template $templateFile not found</p>";
        }

        $htmlContent = file_get_contents($templatePath);

        foreach ($dynamicData as $key => $value) {
            if (is_array($value)) {
                $value = implode('<br>', $value);  // convert arrays to HTML
            }
            $htmlContent = str_replace('{{' . $key . '}}', $value, $htmlContent);
        }

        return $htmlContent;
    }

    private function renderEntrySheet($data)
    {
        return $this->renderTemplate('entry_sheet.html', $data);
    }

 public function downloadTemplateReport(Request $request)
{
    try {
        $instidEnc    = $request->input('instid');
        $formcodeEnc  = $request->input('formcode');
        $tempplanidEnc = $request->input('tempplanid');
        $catcode      = $request->input('catcode');
        $language     = $request->input('lang', 'en');

        if (!$instidEnc || !$formcodeEnc) {
            return response()->json(['error' => 'Institution ID and Form code are required'], 400);
        }

        try {
            $instid    = Crypt::decryptString($instidEnc);
            $formcode  = Crypt::decryptString($formcodeEnc);
            $tempplanid = $tempplanidEnc ? Crypt::decryptString($tempplanidEnc) : null;
        } catch (DecryptException $e) {
            return response()->json(['error' => 'Invalid or tampered encrypted data'], 400);
        }

        $cacheKey = 'template_report_' . md5(json_encode([
            $instid,
            $formcode,
            $tempplanid,
            $catcode,
            $language,
        ]));

        $fileName = 'TemplateAuditReport_' . now()->format('Ymd_His') . '.pdf';


        $cachedContent = Cache::get($cacheKey);
        if ($cachedContent !== null) {
            return response()->streamDownload(function () use ($cachedContent) {
                echo $cachedContent;
            }, $fileName);
        }

        $firstInst = FormatModel::instfetchforreportgeneration('audit.templateauditplan', $tempplanid)
            ->where('mi.instid', $instid)
            ->where('deptaudit.formcode', $formcode)
            ->first();

        if (!$firstInst) {
            return response()->json(['error' => 'Institution data not found'], 404);
        }

        $remarksRows = FormatModel::remarkssheet(
            $firstInst->instid,
            'audit.templateauditplan',
            $formcode,
            $tempplanid
        );

        $sharedData = [
            'remarksRows' => $remarksRows,
            'logoBase64'  => $this->getLogoBase64(),
        ];

        $dynamicData      = $this->prepareDynamicData($firstInst, $language, $sharedData['logoBase64']);
        $entrySheetHtml   = self::sanitizeMpdfInlineStyles($this->EntrySheetdata($firstInst, $language, $formcode, $tempplanid, $catcode));
        $remarksSheetHtml = self::sanitizeMpdfInlineStyles($this->buildRemarksHtml($sharedData['remarksRows']));
        $auditcertificate = self::sanitizeMpdfInlineStyles($this->auditcertificatefortemplate($sharedData['remarksRows']));

        $font = $language === 'ta' ? 'latha' : 'times';

        $mpdf = new \Mpdf\Mpdf([
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'orientation'   => 'P',
            'margin_top'    => 20,
            'margin_bottom' => 20,
            'margin_left'   => 15,
            'margin_right'  => 15,
            'fontDir'       => array_merge(
                (new \Mpdf\Config\ConfigVariables)->getDefaults()['fontDir'],
                [public_path('fonts/tamil')]
            ),
            'fontdata'      => (new \Mpdf\Config\FontVariables)->getDefaults()['fontdata'] + [
                'latha' => ['R' => 'Latha.ttf', 'B' => '', 'I' => '', 'BI' => '', 'useOTL' => 0xFF],
                'times' => ['R' => 'times', 'useOTL' => 0xFF],
            ],
            'default_font'  => $font,
        ]);

        $mpdf->SetHTMLFooter('<div style="text-align:center;font-size:10pt;color:#555;">{PAGENO}</div>');

        $sections = [
            2 => ['en' => 'Audit Details',      'ta' => 'தணிக்கை விவரங்கள்'],
            3 => ['en' => 'Remarks',             'ta' => 'குறிப்புகள்'],
            4 => ['en' => 'Audit Certificate',   'ta' => 'கணக்கு சான்றிதழ்'],
           // 5 => ['en' => 'Attachments',         'ta' => 'இணைப்புகள்'],
        ];

        $stampBorder = function () use ($mpdf) {
            $mpdf->Rect(5, 5, $mpdf->w - 10, $mpdf->h - 10);
        };

        $mpdf->WriteHTML($this->renderFirstPage($dynamicData));
        $stampBorder();

        $title = $language === 'ta' ? $sections[2]['ta'] : $sections[2]['en'];
        $mpdf->AddPage();
        $mpdf->WriteHTML(
            '<div style="position:absolute;top:50%;left:0;transform:translate(50%,50%);'
            . 'text-align:center;width:100%;font-family:' . $font . ';">'
            . '<h2 style="font-size:35px;margin:0;">2. ' . $title . '</h2></div>'
        );
        $stampBorder();

        if ($formcode === 'HRIA01') {
            $mpdf->AddPage();
            $mpdf->WriteHTML(
                '<div style="position:absolute;top:35%;text-align:center;'
                . 'transform:translate(50%,50%);width:85%;font-family:' . $font . ';">'
                . $entrySheetHtml . '</div>'
            );
        } else {
            $mpdf->WriteHTML('<div style="page-break-after:always;"></div>');
            $mpdf->WriteHTML('<div style="font-family:' . $font . ';">' . $entrySheetHtml . '</div>');
        }
        $stampBorder();

        $title = $language === 'ta' ? $sections[3]['ta'] : $sections[3]['en'];
        $mpdf->AddPage();
        $mpdf->WriteHTML(
            '<h2 style="text-align:center;margin-top:50px;font-size:35px;font-family:' . $font . ';">'
            . '3. ' . $title . '</h2>'
            . '<div style="font-family:latha;unicode-bidi:plaintext;">' . $remarksSheetHtml . '</div>'
        );
        $stampBorder();

        $title = $language === 'ta' ? $sections[4]['ta'] : $sections[4]['en'];
        $mpdf->WriteHTML(
            '<h2 style="text-align:center;margin-top:50px;font-size:35px;font-family:' . $font . ';">'
            . '4. ' . $title . '</h2>'
            . '<div style="font-family:' . $font . ';">' . $auditcertificate . '</div>'
        );
        $stampBorder();

        $entryData = $remarksRows->first();
      //  $title     = $language === 'ta' ? $sections[5]['ta'] : $sections[5]['en'];
        $filePath  = null;

        //dd($entryData);

        if ($entryData && !empty($entryData->filepath)) {

        $title = $language === 'ta'
                ? 'இணைப்புகள்'
                : 'Attachments';


            $filePath = storage_path(
                'app/public/' . str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $entryData->filepath)
            );

            $mpdf->AddPage();
            $mpdf->WriteHTML(
                '<h2 style="position:absolute;top:50%;left:0;transform:translate(50%,50%);'
                . 'text-align:center;width:100%;font-size:35px;font-family:' . $font . ';">'
                . '5. ' . $title . '</h2>'
            );
            $stampBorder();
        }

        $reportPdf = storage_path('app/report_' . uniqid() . '.pdf');
        $mpdf->Output($reportPdf, \Mpdf\Output\Destination::FILE);

        $endMpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
        ]);

        $endMpdf->AddPage();
        $endMpdf->WriteHTML(
            '<h2 style="position:absolute;top:50%;left:0;transform:translate(50%,50%);'
            . 'text-align:center;width:100%;font-size:30px;font-family:' . $font . ';">'
            . '**End of the Report**</h2>'
        );
        $endMpdf->Rect(5, 5, $endMpdf->w - 10, $endMpdf->h - 10);

        $endPdf = storage_path('app/end_' . uniqid() . '.pdf');
        $endMpdf->Output($endPdf, \Mpdf\Output\Destination::FILE);

        $finalPdf = storage_path('app/final_' . uniqid() . '.pdf');


        $filesToMerge = [$reportPdf];
        $attachmentIncluded = false;

        if ($filePath && file_exists($filePath)) {
            $filesToMerge[] = $filePath;
            $attachmentIncluded = true;
        }

        $filesToMerge[] = $endPdf;

        try {
            $merged = $this->mergePdfFilesWithPdfcpu($filesToMerge, $finalPdf, 'compressed');

            if (!$merged && $attachmentIncluded) {


                $filesToMerge = [$reportPdf, $endPdf];
                $attachmentIncluded = false;

                $merged = $this->mergePdfFilesWithPdfcpu($filesToMerge, $finalPdf, 'compressed');
            }

            if (!$merged || !file_exists($finalPdf)) {
                throw new \Exception('PDF merge failed via pdfcpu.');
            }

          //  $fileName   = 'TemplateAuditReport_' . now()->format('Ymd_His') . '.pdf';
            $pdfContent = file_get_contents($finalPdf);

             Cache::put($cacheKey, $pdfContent, now()->addMinutes(30));

            return response()->streamDownload(function () use ($pdfContent) {
                echo $pdfContent;
            }, $fileName);

        } finally {
            @unlink($reportPdf);
            @unlink($endPdf);
            @unlink($finalPdf);
        }

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public function generateReport(Request $request)
{
    $language   = $request->lang === 'ta' ? 'ta' : 'en';
    $instid     = $request->input('instid');
    $formcode   = $request->input('formcode');
    $tempplanid = $request->input('tempplanid');
    $catcode    = $request->input('catcode');

    if (!$instid) {
        return response()->json(['error' => 'Institution ID is required'], 400);
    }

    $firstInst = FormatModel::instfetchforreportgeneration('audit.templateauditplan', $tempplanid)
        ->where('mi.instid', $instid)
        ->where('deptaudit.formcode', $formcode)
        ->first();

    if (!$firstInst) {
        return response()->json(['error' => 'Institution data not found'], 404);
    }

    // ── OPTIMIZATION: single remarkssheet query shared across helpers ──
    $remarksRows = FormatModel::remarkssheet(
        $firstInst->instid,
        'audit.templateauditplan',
        $formcode,
        $tempplanid
    );

    $encryptedData = [
        'instid'     => Crypt::encryptString($instid),
        'formcode'   => Crypt::encryptString($formcode),
        'tempplanid' => Crypt::encryptString($tempplanid ?? ''),
    ];

    $logoBase64       = $this->getLogoBase64();
    $dynamicData      = $this->prepareDynamicData($firstInst, $language, $logoBase64);
    $entrySheetHtml   = $this->EntrySheetdata($firstInst, $language, $formcode, $tempplanid, $catcode);
    $remarksSheetHtml = $this->buildRemarksHtml($remarksRows);   // no extra query
    $attachments      = $this->buildAttachmentsHtml($remarksRows->first()); // no extra query

    $auditcertificate = $this->auditcertificatefortemplate($remarksRows);

    $sections = [
        2 => ['en' => 'Audit Details',    'ta' => 'தணிக்கை விவரங்கள்'],
        3 => ['en' => 'Remarks',          'ta' => 'குறிப்புகள்'],
        4 => ['en' => 'Audit Certificate','ta' => 'கணக்கு சான்றிதழ்'],
        5 => ['en' => 'Attachments',      'ta' => 'இணைப்புகள்'],
    ];

    $reportHtml  = $this->renderFirstPage($dynamicData);

    $sectionContent = [
        2 => $entrySheetHtml,
        3 => $remarksSheetHtml,
        4 => $auditcertificate,
        5 => $attachments,
    ];

    foreach ([2, 3, 4, 5] as $secNum) {
        if ($secNum === 5 && empty($attachments)) {
            continue;
        }

        $title = ($language === 'ta') ? $sections[$secNum]['ta'] : $sections[$secNum]['en'];

        $reportHtml .= '<h2 style="text-align:center;margin-top:100px;font-size:35px;">'
            . $secNum . '. ' . htmlspecialchars($title) . '</h2>';

        $reportHtml .= $sectionContent[$secNum];
        $reportHtml .= '<div style="page-break-before:always;"></div>';
    }

    return response()->json(['html' => $reportHtml, 'encrypted' => $encryptedData]);
}


  private function auditcertificatefortemplate($remarksRows)
    {
        $entryData = $remarksRows->first();

        if (!$remarksRows) {
            return 'No certificate data available.';
        }

        $cerContent = json_decode($entryData->cer_content, true);
        $htmlContent = $cerContent['content'] ?? 'No content available.';

        if (!empty($entryData->audit_end_date)) {
            $auditDate = \Carbon\Carbon::parse($entryData->audit_end_date);
            $formattedDate = $auditDate->format('d-m-Y');
            $htmlContent = str_replace('[audityear]', $formattedDate, $htmlContent);
        }

        $html = '<div class="certificate">';
        $html .= '<div class="certificate-content">' . $htmlContent . '</div>';
        $html .= '</div>';

        return $html;
    }


 private function buildAttachmentsHtml($entryData): string
{
    if (!$entryData || empty($entryData->filepath)) {
        return '';
    }

    $filePath = str_replace('\\', '/', $entryData->filepath);
    $fullPath = storage_path('app/public/' . ltrim($filePath, '/'));


    if (!file_exists($fullPath)) {
        return '<div class="attachment-block" style="color:red;">Attachment file not found.</div>';
    }

    if (mime_content_type($fullPath) !== 'application/pdf') {
        return '<div class="attachment-block" style="color:red;">Invalid file type. Only PDF files are allowed.</div>';
    }

    if (filesize($fullPath) > 2 * 1024 * 1024) {
        return '<div class="attachment-block" style="color:red;">File size exceeds 2 MB limit.</div>';
    }

    $assetPath = asset(ltrim($filePath, '/'));

    // $assetPath = asset('storage/' . ltrim($filePath, '/'));

    return <<<HTML
        <div class="attachments-section">
            <div class="attachment-block" style="margin-bottom:20px;">
                <div id="pdf-viewer" style="border:1px solid #ccc;"></div>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.10.111/pdf.min.js"></script>
                <script>
                    (function () {
                        const pdfjsLib = window['pdfjsLib'];
                        pdfjsLib.GlobalWorkerOptions.workerSrc =
                            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.10.111/pdf.worker.min.js';
                        const container = document.getElementById('pdf-viewer');
                        pdfjsLib.getDocument("{$assetPath}").promise.then(pdf => {
                            for (let p = 1; p <= pdf.numPages; p++) {
                                pdf.getPage(p).then(page => {
                                    const vp     = page.getViewport({ scale: 1 });
                                    const canvas = document.createElement('canvas');
                                    canvas.style.cssText = 'display:block;margin-bottom:10px;';
                                    canvas.height = vp.height;
                                    canvas.width  = vp.width;
                                    container.appendChild(canvas);
                                    page.render({ canvasContext: canvas.getContext('2d'), viewport: vp });
                                });
                            }
                        });
                    })();
                </script>
            </div>
        </div>
    HTML;
}
private static ?string $cachedLogoBase64 = null;

private function getLogoBase64(): string
{
    if (self::$cachedLogoBase64 === null) {
        self::$cachedLogoBase64 = 'data:image/png;base64,'
            . base64_encode(file_get_contents(public_path('site/image/tn__logo.png')));
    }

    return self::$cachedLogoBase64;
}



private function buildRemarksHtml($remarksRows): string
{


    $html = '<div class="remarks-section">';

    foreach ($remarksRows as $remark) {
        $type        = $remark->type        ?? '';
        $remark_text = $remark->remark_text ?? '';
        $label       = !empty($type) ? $type . ' Irregularities' : '';

        $html .= '<div class="remark-block" style="margin-bottom:20px;">';
        if (!empty($label)) {
            $html .= '<label style="font-weight:bold;display:block;margin-bottom:5px;">' . $label . '</label>';
        }
        $html .= '<div class="remark-content" style="border:1px solid #ccc;padding:10px;min-height:82px;">'
            . $remark_text . '</div>';
        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}


    public function milkstructuretemplate($templatesData, $milkData = null, $language = 'en')
    {
        $htmlOutput = '';

        foreach ($templatesData as &$templateData) {
            $structure = $templateData['structure'];
            $headers = $structure->headers ?? [];

            // dd( $milkData);

            $pairs = [];
            for ($i = 0; $i < count($headers); $i++) {
                if ($headers[$i]['inputtype'] === 'C') {
                    $pair = [
                        'category' => $headers[$i],
                        'amount_headers' => [],
                    ];
                    for ($j = $i + 1; $j < count($headers); $j++) {
                        if ($headers[$j]['inputtype'] === 'N') {
                            $pair['amount_headers'][] = $headers[$j];
                        } else {
                            break;
                        }
                    }
                    $pairs[] = $pair;
                }
            }

            $tableData = [];
            foreach ($pairs as $pairIndex => $pair) {
                $tableData[$pairIndex] = [];

                foreach ($pair['category']['maintypes'] ?? [] as $maintype) {
                    if (!empty($maintype['subtypes'])) {
                        // Maintype has subtypes → show header row
                        $tableData[$pairIndex][] = [
                            'type' => 'maintype_header',
                            'maintypeid' => $maintype['maintypeid'],
                            'maintypeename' => $maintype['maintypeename'],
                            'maintypetname' => $maintype['maintypetname'],
                            'maintypetotalflag' => $maintype['maintypetotalflag'],
                            'temptypeid' => $maintype['temptypeid'],
                            'subtypeid' => null,
                            'isTotal' => false,
                        ];

                        // Add all subtypes
                        foreach ($maintype['subtypes'] as $subtype) {
                            $tableData[$pairIndex][] = [
                                'type' => 'subtype',
                                'maintypeid' => $maintype['maintypeid'],
                                'maintypeename' => $maintype['maintypeename'],
                                'maintypetname' => $maintype['maintypetname'],
                                'maintypetotalflag' => $maintype['maintypetotalflag'],
                                'temptypeid' => $maintype['temptypeid'],
                                'subtypeid' => $subtype['subtypeid'],
                                'stypeename' => $subtype['stypeename'],
                                'stypetname' => $subtype['stypetname'],
                                'orderno' => $subtype['orderno'],
                                'subtemptypeid' => $subtype['subtemptypeid'],
                                'isTotal' =>
                                    stripos($subtype['stypeename'] ?? '', 'total') !==
                                    false,
                            ];
                        }
                    } else {
                        // No subtypes → show maintype as data row only (no header)
                        $tableData[$pairIndex][] = [
                            'type' => 'maintype_data',
                            'maintypeid' => $maintype['maintypeid'],
                            'maintypeename' => $maintype['maintypeename'],
                            'maintypetname' => $maintype['maintypetname'],
                            'maintypetotalflag' => $maintype['maintypetotalflag'],
                            'temptypeid' => $maintype['temptypeid'],
                            'isTotal' => !empty($maintype['maintypetotalflag']),
                        ];
                    }
                }
            }

            $leftData = $tableData[0] ?? [];
            $rightData = $tableData[1] ?? [];

            $amountValues = [];
            if ($milkData) {
                foreach ($milkData as $amount) {
                    $value = match ($amount->temptypeid) {
                        1 => $amount->value_numeric !== null ? (float) $amount->value_numeric : '',
                        2 => $amount->value_char ?? '',
                        3 => $amount->value_date ?? '',
                        4 => $amount->value_radio ?? '',
                        default => '',
                    };

                    $key = "header_{$amount->tmpauditheaderid}_maintype_{$amount->maintypeid}";
                    if ($amount->subtypeid) {
                        $key .= "_subtype_{$amount->subtypeid}";
                    }
                    $amountValues[$key] = $value;
                }
            }

            $alignmentMap = [
                'R' => 'E',
                'O' => 'C',
                'V' => 'X',
            ];

            $leftRegular = [];
            $leftFlagged = [];
            $rightRegular = [];
            $rightFlagged = [];

            foreach ($leftData as $row) {
                $flag = $row['maintypetotalflag'] ?? null;
                if ($flag && array_key_exists($flag, $alignmentMap)) {
                    $leftFlagged[$flag] = $row;
                } else {
                    $leftRegular[] = $row;
                }
            }

            foreach ($rightData as $row) {
                $flag = $row['maintypetotalflag'] ?? null;
                if ($flag && in_array($flag, $alignmentMap)) {
                    $rightFlagged[$flag] = $row;
                } else {
                    $rightRegular[] = $row;
                }
            }

            $maxRegular = max(count($leftRegular), count($rightRegular));
            $alignedRows = [];

            for ($i = 0; $i < $maxRegular; $i++) {
                $alignedRows[] = [
                    'left' => $leftRegular[$i] ?? null,
                    'right' => $rightRegular[$i] ?? null,
                ];
            }

            foreach ($alignmentMap as $leftFlag => $rightFlag) {
                $alignedRows[] = [
                    'left' => $leftFlagged[$leftFlag] ?? null,
                    'right' => $rightFlagged[$rightFlag] ?? null,
                ];
            }

            $html = '<table class="table table-bordered table-sm" style="border:1px solid #000;border-collapse:collapse;width:100%">';
            $html .= '<thead style="background:#f8f9fa;"><tr>';

            foreach ($headers as $header) {
                if (isset($header['lblename'], $header['lbltname'])) {
                    $label = ($language === 'en') ? $header['lblename'] : $header['lbltname'];
                    $html .= "<th style='border:1px solid #000;'>" . e($label) . '</th>';
                }
            }

            $html .= '</tr></thead><tbody>';

            // $maxRegular = max(count($leftRegular), count($rightRegular));
            // for ($i = 0; $i < $maxRegular; $i++) {
            //     $html .= $this->milkrenderRow($pairs, $leftRegular[$i] ?? null, $rightRegular[$i] ?? null, $amountValues, $language);
            // }

            foreach ($alignedRows as $pairRow) {
                $leftIsTotal = !empty($pairRow['left']['isTotal']);
                $rightIsTotal = !empty($pairRow['right']['isTotal']);
                $isTotalRow = $leftIsTotal || $rightIsTotal;

                $html .= $this->milkrenderRow(
                    $pairs,
                    $pairRow['left'] ?? null,
                    $pairRow['right'] ?? null,
                    $amountValues,
                    $language,
                    $isTotalRow
                );
            }

            $html .= '</tbody></table>';
            $htmlOutput .= $html;
        }

        return $htmlOutput;
    }

    private function milkrenderRow($pairs, $leftRow, $rightRow, $amountValues, $language = 'en', $isTotalRow = false)
    {
        $html = '<tr>';

        foreach ($pairs as $pairIndex => $pair) {
            $currentRowData = ($pairIndex === 0) ? $leftRow : $rightRow;
            if (!$currentRowData) {
                $html .= "<td style='border:1px solid #000;'>&nbsp;</td>";
                foreach ($pair['amount_headers'] as $amountHeader) {
                    $html .= "<td style='border:1px solid #000;'>&nbsp;</td>";
                }

                continue;
            }

            $isHeader = $currentRowData['type'] === 'maintype_header';
            $isSubtype = $currentRowData['type'] === 'subtype';
            $isDataRow = in_array($currentRowData['type'], ['subtype', 'maintype_data']);

            $label = $isSubtype
                ? (($language === 'en') ? ($currentRowData['stypeename'] ?? '') : ($currentRowData['stypetname'] ?? ''))
                : (($language === 'en') ? ($currentRowData['maintypeename'] ?? '') : ($currentRowData['maintypetname'] ?? ''));

            $style = 'border:1px solid #000; padding:6px;';
            if ($isHeader) {
                $style .= 'font-weight:bold;background:#f1f1f1;';
            }
            if ($isTotalRow) {
                $style .= 'font-weight:bold;background:#e9ecef;';
            }
            if ($isSubtype) {
                $style .= 'padding-left:24px;';
            }

            $html .= "<td style='{$style}'>" . e($label ?: '-') . '</td>';

            foreach ($pair['amount_headers'] as $amountHeader) {
                $value = '';
                if ($isDataRow || $isTotalRow) {
                    $lookupKey = "header_{$amountHeader['tmpauditheaderid']}_maintype_{$currentRowData['maintypeid']}";
                    if (!empty($currentRowData['subtypeid'])) {
                        $lookupKey .= "_subtype_{$currentRowData['subtypeid']}";
                    }
                    $value = $amountValues[$lookupKey] ?? '';
                }

                if (!empty($currentRowData['temptypeid']) && $currentRowData['temptypeid'] == 4 && $value !== '') {
                    $value = ucfirst($value);
                    if ($language !== 'en') {
                        $value = ($value === 'Yes') ? 'ஆம்' : (($value === 'No') ? 'இல்லை' : $value);
                    }
                }

                $html .= "<td style='border:1px solid #000; text-align:right; padding:6px; font-weight:" . ($isTotalRow ? 'bold' : 'normal') . ";'>" . e($value ?: '-') . '</td>';
            }
        }

        $html .= '</tr>';

        return $html;
    }

    public function structureHriaTemplate($templatesData, $hriaData = null, $language = 'en')
    {
        $htmlOutput = '';

        foreach ($templatesData as &$templateData) {
            $structure = $templateData['structure'];
            $headers = $structure->headers ?? [];

            // 1️⃣ Pair category + amount headers
            $pairs = [];
            for ($i = 0; $i < count($headers); $i++) {
                if (
                    $headers[$i]['inputtype'] === 'C' &&
                    isset($headers[$i + 1]) &&
                    $headers[$i + 1]['inputtype'] === 'N'
                ) {
                    $pairs[] = [
                        'category' => $headers[$i],
                        'amount' => $headers[$i + 1],
                    ];
                    $i++;
                }
            }

            // 2️⃣ Calculate max rows based on maintypes count
            $max = 0;
            foreach ($pairs as &$pair) {
                $pair['maintypes'] = array_values($pair['category']['maintypes'] ?? []);
                $max = max($max, count($pair['maintypes']));
            }
            unset($pair);

            // 3️⃣ Build amount values dictionary
            $amountValues = [];
            if ($hriaData && isset($hriaData)) {
                foreach ($hriaData as $amount) {
                    $value = '';
                    switch ($amount->temptypeid) {
                        case 1:
                            $value = $amount->value_numeric;
                            break;
                        case 2:
                            $value = $amount->value_char;
                            break;
                        case 3:
                            $value = $amount->value_date;
                            break;
                        case 4:
                            $value = $amount->value_radio;
                            break;
                    }
                    $amountValues[$amount->maintypeid] = $value;
                }
            }

            // 4️⃣ Start HTML table
            $html = '<table class="table table-bordered table-sm" style="border:1px solid #000; border-collapse:collapse; width:100%">';
            $html .= '<thead style="background:#f8f9fa;"><tr>';

            // Headers row (Label + Amount for each pair)
            foreach ($pairs as $pair) {
                $headerLabel = ($language === 'en') ? $pair['category']['lblename'] : $pair['category']['lbltname'];
                $amountLabel = ($language === 'en') ? $pair['amount']['lblename'] : $pair['amount']['lbltname'];

                $html .= "<th style='border:1px solid #000;'>" . e($headerLabel) . '</th>';
                $html .= "<th style='border:1px solid #000;'>" . e($amountLabel) . '</th>';
            }
            $html .= '</tr></thead><tbody>';

            // 5️⃣ Generate rows
            for ($row = 0; $row < $max; $row++) {
                $html .= '<tr>';
                foreach ($pairs as $pairIndex => $pair) {
                    $item = $pair['maintypes'][$row] ?? null;

                    if (!$item) {
                        $html .= "<td style='border:1px solid #000;'>&nbsp;</td>";
                        $html .= "<td style='border:1px solid #000;'>&nbsp;</td>";

                        continue;
                    }

                    // ✅ Choose language label
                    $label = ($language === 'en') ? ($item['maintypeename'] ?? '') : ($item['maintypetname'] ?? '');

                    // ✅ Label Cell
                    $isTotalRow = !empty($item['maintypetotalflag']);
                    $labelStyle = 'border:1px solid #000; padding:6px;';
                    if ($isTotalRow) {
                        $labelStyle .= 'font-weight:bold;background:#e9ecef;';
                    }

                    $html .= "<td style='{$labelStyle}'>" . e($label) . '</td>';

                    // ✅ Value Cell (report-only)
                    $value = $amountValues[$item['maintypeid']] ?? '';
                    if ($item['temptypeid'] == 1 && $value !== '') {
                        $value = number_format((float) $value, 2, '.', '');
                    }

                    // Radio values - convert Yes/No based on language if needed
                    if ($item['temptypeid'] == 4 && $value !== '') {
                        $value = ucfirst($value);
                        if ($language !== 'en') {
                            $value = ($value === 'Yes') ? 'ஆம்' : (($value === 'No') ? 'இல்லை' : $value);
                        }
                    }

                    $valueStyle = 'border:1px solid #000; padding:6px; text-align:right;';
                    if ($isTotalRow) {
                        $valueStyle .= 'font-weight:bold;background:#e9ecef;';
                    }

                    $displayValue = $value !== '' ? e($value) : '-';

                    $html .= "<td style='{$valueStyle}'>{$displayValue}</td>";
                }
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            $htmlOutput .= $html;
        }

        return $htmlOutput;
    }

    public function structureHriaFullReport($templatesData, $hriaData = null, $language = 'en')
    {
        $htmlOutput = '';

        foreach ($templatesData as &$templateData) {
            $structure = $templateData['structure'] ?? null;
            if (!$structure || !isset($structure->headers)) {
                continue;
            }

            $headers = $structure->headers;

            // ✅ Split headers into subtables & main details
            $subTables = [];
            $mainHeaders = [];
            foreach ($headers as $header) {
                if (!empty($header['subtableflag'])) {
                    $subTables[$header['subtableflag']][] = $header;
                } else {
                    $mainHeaders[] = $header;
                }
            }

            // ✅ Collect values
            $amountValues = [];
            if ($hriaData && isset($hriaData)) {
                foreach ($hriaData as $amount) {
                    $value = match ($amount->temptypeid) {
                        1 => $amount->value_numeric,
                        2 => $amount->value_char,
                        3 => $amount->value_date,
                        4 => $amount->value_radio,
                        default => '',
                    };
                    $amountValues[$amount->maintypeid] = $value;
                }
            }

            // ✅ 1️⃣ Render SUBTABLE sections first
            $titles = [
                'closing_balance' => [
                    'en' => 'Capital Accounts Balance Details at the End of the Audit',
                    'ta' => 'தணிக்கையின் முடிவில் மூலதன கணக்குகள் இருப்பு விபரம்',
                ],
                'jewel_valuation' => [
                    'en' => 'Value of Jewelry According to Appraisal Report',
                    'ta' => 'மதிப்பீடு அறிக்கையின் படி நகைகளின் மதிப்பு',
                ],
            ];

            foreach ($subTables as $flag => $headers) {
                $title = $titles[$flag][$language === 'en' ? 'en' : 'ta'] ?? ucfirst(str_replace('_', ' ', $flag));
                $htmlOutput .= "<h4 style='margin-top:25px;margin-bottom:10px;font-weight:bold;text-decoration:underline;'>" . e($title) . '</h4>';
                $htmlOutput .= $this->renderHriaTable($headers, $amountValues, $language);
            }

            // ✅ 2️⃣ Render MAIN DETAILS TABLE second
            if (!empty($mainHeaders)) {
                $htmlOutput .= "<h4 style='margin-top:35px;margin-bottom:10px;font-weight:bold;text-decoration:underline;'>" . e($language === 'en' ? 'Detailed Report' : 'விவரமான அறிக்கை') . '</h4>';
                $htmlOutput .= $this->renderHriaTable($mainHeaders, $amountValues, $language, true);
            }
        }

        return $htmlOutput;
    }

    private function renderHriaTable($headers, $amountValues, $language = 'en', $alignTotals = false)
    {
        // 1️⃣ Build pairs
        $pairs = [];
        for ($i = 0; $i < count($headers); $i++) {
            if ($headers[$i]['inputtype'] === 'C' && isset($headers[$i + 1]) && $headers[$i + 1]['inputtype'] === 'N') {
                $pairs[] = [
                    'category' => $headers[$i],
                    'amount' => $headers[$i + 1],
                ];
                $i++;
            }
        }

        // Split into left & right pairs
        $leftPairs = [];
        $rightPairs = [];
        foreach ($pairs as $i => $pair) {
            if ($i % 2 === 0) {
                $leftPairs[] = $pair;
            } else {
                $rightPairs[] = $pair;
            }
        }

        foreach ($leftPairs as &$pair) {
            $pair['maintypes'] = array_values($pair['category']['maintypes'] ?? []);
        }
        unset($pair);
        foreach ($rightPairs as &$pair) {
            $pair['maintypes'] = array_values($pair['category']['maintypes'] ?? []);
        }
        unset($pair);

        if ($alignTotals) {
            $alignmentMap = [
                'I' => 'G',
                'R' => 'E',
                'O' => 'C',
                'V' => 'X',
            ];
            foreach ($alignmentMap as $leftFlag => $rightFlag) {
                $lIndex = null;
                $rIndex = null;
                foreach ($leftPairs as $lpIndex => $pair) {
                    $idx = collect($pair['maintypes'])->search(fn($item) => ($item['maintypetotalflag'] ?? '') === $leftFlag);
                    if ($idx !== false) {
                        $lIndex = [$lpIndex, $idx];
                    }
                }
                foreach ($rightPairs as $rpIndex => $pair) {
                    $idx = collect($pair['maintypes'])->search(fn($item) => ($item['maintypetotalflag'] ?? '') === $rightFlag);
                    if ($idx !== false) {
                        $rIndex = [$rpIndex, $idx];
                    }
                }
                if ($lIndex && $rIndex) {
                    $diff = $rIndex[1] - $lIndex[1];
                    if ($diff > 0) {
                        array_splice($leftPairs[$lIndex[0]]['maintypes'], $lIndex[1], 0, array_fill(0, $diff, null));
                    } elseif ($diff < 0) {
                        array_splice($rightPairs[$rIndex[0]]['maintypes'], $rIndex[1], 0, array_fill(0, abs($diff), null));
                    }
                }
            }
        }

        $maxLeft = max(array_map(fn($p) => count($p['maintypes']), $leftPairs) ?: [0]);
        $maxRight = max(array_map(fn($p) => count($p['maintypes']), $rightPairs) ?: [0]);
        $totalRows = max($maxLeft, $maxRight);

        $html = '<table class="table table-bordered table-sm" style="border:1px solid #000;border-collapse:collapse;width:100%">';
        $html .= '<thead style="background:#f8f9fa;"><tr>';

        foreach ($leftPairs as $pair) {
            $html .= "<th style='border:1px solid #000;'>" . e($language === 'en' ? ($pair['category']['lblename'] ?? '') : ($pair['category']['lbltname'] ?? '')) . '</th>';
            $html .= "<th style='border:1px solid #000;'>" . e($language === 'en' ? ($pair['amount']['lblename'] ?? '') : ($pair['amount']['lbltname'] ?? '')) . '</th>';
        }
        foreach ($rightPairs as $pair) {
            $html .= "<th style='border:1px solid #000;'>" . e($language === 'en' ? ($pair['category']['lblename'] ?? '') : ($pair['category']['lbltname'] ?? '')) . '</th>';
            $html .= "<th style='border:1px solid #000;'>" . e($language === 'en' ? ($pair['amount']['lblename'] ?? '') : ($pair['amount']['lbltname'] ?? '')) . '</th>';
        }

        $html .= '</tr></thead><tbody>';

        for ($row = 0; $row < $totalRows; $row++) {
            $html .= '<tr>';

            foreach ($leftPairs as $pair) {
                $html .= $this->renderHriaCell($pair['maintypes'][$row] ?? null, $amountValues, $language);
            }
            foreach ($rightPairs as $pair) {
                $html .= $this->renderHriaCell($pair['maintypes'][$row] ?? null, $amountValues, $language);
            }

            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return $html;
    }

    private function renderHriaCell($item, $amountValues, $language)
    {
        if (!$item) {
            return "<td style='border:1px solid #000;'>&nbsp;</td><td style='border:1px solid #000;'>&nbsp;</td>";
        }

        $label = $language === 'en' ? ($item['maintypeename'] ?? '') : ($item['maintypetname'] ?? '');
        $value = $amountValues[$item['maintypeid']] ?? '';
        if (($item['temptypeid'] ?? 0) == 1 && $value !== '') {
            $value = number_format($value, 2, '.', '');
        }

        $rowStyle = !empty($item['maintypetotalflag']) ? 'background:#e9ecef;font-weight:bold;' : '';

        return "<td style='border:1px solid #000;{$rowStyle}'>" . e($label) . "</td>
            <td style='border:1px solid #000;text-align:right;{$rowStyle}'>" . e($value) . '</td>';
    }

    public function structureDcaReport($templatesData, $dcaData = null, $language = 'en')
    {
        $htmlOutput = '';

        foreach ($templatesData as &$templateData) {
            $structure = $templateData['structure'] ?? null;
            if (!$structure || !isset($structure->headers)) {
                continue;
            }

            $headers = $structure->headers;

            $pairs = [];
            for ($i = 0; $i < count($headers); $i++) {
                if ($headers[$i]['inputtype'] === 'C') {
                    $pair = [
                        'category' => $headers[$i],
                        'amount_headers' => [],
                    ];
                    for ($j = $i + 1; $j < count($headers); $j++) {
                        if ($headers[$j]['inputtype'] === 'N') {
                            $pair['amount_headers'][] = $headers[$j];
                        } else {
                            break;
                        }
                    }
                    $pairs[] = $pair;
                }
            }

            $tableData = [];
            foreach ($pairs as $pairIndex => $pair) {
                $tableData[$pairIndex] = [];
                foreach ($pair['category']['maintypes'] ?? [] as $maintype) {
                    $tableData[$pairIndex][] = [
                        'type' => 'maintype_header',
                        'maintypeid' => $maintype['maintypeid'],
                        'maintypeename' => $maintype['maintypeename'],
                        'maintypetname' => $maintype['maintypetname'],
                        'maintypetotalflag' => $maintype['maintypetotalflag'],
                        'temptypeid' => $maintype['temptypeid'],
                        'isTotal' => false,
                    ];

                    foreach ($maintype['subtypes'] ?? [] as $subtype) {
                        $tableData[$pairIndex][] = [
                            'type' => 'subtype',
                            'maintypeid' => $maintype['maintypeid'],
                            'maintypeename' => $maintype['maintypeename'],
                            'maintypetname' => $maintype['maintypetname'],
                            'maintypetotalflag' => $maintype['maintypetotalflag'],
                            'temptypeid' => $maintype['temptypeid'],
                            'subtypeid' => $subtype['subtypeid'],
                            'stypeename' => $subtype['stypeename'],
                            'stypetname' => $subtype['stypetname'],
                            'orderno' => $subtype['orderno'],
                            'subtemptypeid' => $subtype['subtemptypeid'],
                            'isTotal' => stripos($subtype['stypeename'] ?? '', 'total') !== false,
                        ];
                    }
                }
            }

            $maxRows = 0;
            foreach ($tableData as $pairData) {
                $maxRows = max($maxRows, count($pairData));
            }

            $amountValues = [];
            if ($dcaData && isset($dcaData)) {
                foreach ($dcaData as $amount) {
                    $value = match ($amount->temptypeid) {
                        1 => $amount->value_numeric,
                        2 => $amount->value_char,
                        3 => $amount->value_date,
                        4 => $amount->value_radio,
                        default => '',
                    };

                    $key = "header_{$amount->tmpauditheaderid}_maintype_{$amount->maintypeid}";
                    if ($amount->subtypeid) {
                        $key .= "_subtype_{$amount->subtypeid}";
                    }
                    $amountValues[$key] = $value;
                }
            }

            $html = '<table class="table table-bordered table-sm financial-table" style="border:1px solid #000; border-collapse:collapse; width:100%;">';
            $html .= '<thead style="background:#f8f9fa;"><tr>';
            foreach ($headers as $header) {
                if (!isset($header['lblename'], $header['lbltname'])) {
                    continue;
                }
                $label = ($language === 'en') ? $header['lblename'] : $header['lbltname'];
                $bold = ($header['inputtype'] === 'N') ? 'font-weight:bold;background:#e9ecef;' : '';
                $html .= "<th style='border:1px solid #000;{$bold}'>" . e($label) . '</th>';
            }
            $html .= '</tr></thead><tbody>';

            for ($row = 0; $row < $maxRows; $row++) {
                $html .= '<tr>';
                foreach ($pairs as $pairIndex => $pair) {
                    $currentRowData = $tableData[$pairIndex][$row] ?? null;

                    $isMaintypeHeader = $currentRowData && $currentRowData['type'] === 'maintype_header';
                    $isSubtype = $currentRowData && $currentRowData['type'] === 'subtype';
                    $isTotal = $currentRowData['isTotal'] ?? false;

                    $catLabel = '';
                    if ($currentRowData) {
                        if ($isMaintypeHeader) {
                            $catLabel = ($language === 'en')
                                ? ($currentRowData['maintypeename'] ?? '')
                                : ($currentRowData['maintypetname'] ?? '');
                        } elseif ($isSubtype) {
                            $catLabel = ($language === 'en')
                                ? ($currentRowData['stypeename'] ?? '')
                                : ($currentRowData['stypetname'] ?? '');
                        }
                    }

                    $catStyle = 'border:1px solid #000; padding:6px;';
                    if ($isMaintypeHeader) {
                        $catStyle .= 'font-weight:bold;background:#f1f1f1;';
                    }
                    if ($isTotal) {
                        $catStyle .= 'font-weight:bold;background:#e9ecef;';
                    }

                    $html .= "<td style='{$catStyle}'>" . e($catLabel ?: '-') . '</td>';

                    foreach ($pair['amount_headers'] as $amountHeader) {
                        $headerId = $amountHeader['tmpauditheaderid'] ?? 0;
                        $key = "header_{$headerId}_maintype_" . ($currentRowData['maintypeid'] ?? 0);
                        if (!empty($currentRowData['subtypeid'])) {
                            $key .= "_subtype_{$currentRowData['subtypeid']}";
                        }
                        $value = $amountValues[$key] ?? '';

                        // Handle formatting
                        $subtypeField = $currentRowData['subtemptypeid'] ?? 1;
                        if ($subtypeField == 1 && $value !== '') {
                            $value = number_format((float) $value, 2, '.', '');
                        } elseif ($subtypeField == 4 && $value !== '') {
                            $value = ucfirst($value);
                            if ($language !== 'en') {
                                $value = ($value === 'Yes') ? 'ஆம்' : (($value === 'No') ? 'இல்லை' : $value);
                            }
                        }

                        $amountStyle = 'border:1px solid #000; text-align:right; padding:6px;';
                        if ($isTotal) {
                            $amountStyle .= 'font-weight:bold;background:#e9ecef;';
                        }
                        if ($isMaintypeHeader) {
                            $amountStyle .= 'background:#f9f9f9;';
                        }

                        $html .= "<td style='{$amountStyle}'>" . e($value ?: '') . '</td>';
                    }
                }
                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            $htmlOutput .= $html;
        }

        return $htmlOutput;
    }

    public function structureLfaReport($templatesData, $lfaData = null, $language = 'en')
    {
        $htmlOutput = '';

        foreach ($templatesData as &$templateData) {
            $structure = $templateData['structure'] ?? null;
            if (!$structure || !isset($structure->headers)) {
                continue;
            }

            $headers = $structure->headers;

            $pairs = [];
            for ($i = 0; $i < count($headers); $i++) {
                if ($headers[$i]['inputtype'] === 'C') {
                    $pair = [
                        'category' => $headers[$i],
                        'amount_headers' => [],
                    ];

                    for ($j = $i + 1; $j < count($headers); $j++) {
                        if ($headers[$j]['inputtype'] === 'N') {
                            $pair['amount_headers'][] = $headers[$j];
                        } else {
                            break;
                        }
                    }
                    $pairs[] = $pair;
                }
            }

            $max = 0;
            foreach ($pairs as &$pair) {
                $pair['maintypes'] = array_values($pair['category']['maintypes'] ?? []);
                $max = max($max, count($pair['maintypes']));
            }
            unset($pair);

            $alignmentMap = [
                'R' => 'E',
                'L' => 'A',
            ];

            $maintypesArrays = [];
            foreach ($pairs as $pair) {
                $maintypesArrays[] = $pair['maintypes'];
            }

            foreach ($alignmentMap as $leftFlag => $rightFlag) {
                $indices = [];

                foreach ($maintypesArrays as $arrayIndex => $maintypes) {
                    $leftIndex = collect($maintypes)->search(fn($item) => ($item['maintypetotalflag'] ?? '') === $leftFlag);
                    $rightIndex = collect($maintypes)->search(fn($item) => ($item['maintypetotalflag'] ?? '') === $rightFlag);

                    if ($leftIndex !== false) {
                        $indices[] = ['pair' => $arrayIndex, 'index' => $leftIndex];
                    }
                    if ($rightIndex !== false) {
                        $indices[] = ['pair' => $arrayIndex, 'index' => $rightIndex];
                    }
                }

                if (count($indices) >= 2) {
                    $maxIndex = max(array_column($indices, 'index'));
                    foreach ($indices as $item) {
                        $diff = $maxIndex - $item['index'];
                        if ($diff > 0) {
                            array_splice(
                                $maintypesArrays[$item['pair']],
                                $item['index'],
                                0,
                                array_fill(0, $diff, null)
                            );
                        }
                    }
                }
            }

            foreach ($pairs as $index => &$pair) {
                $pair['maintypes'] = $maintypesArrays[$index];
            }
            unset($pair);

            $maxRows = max(array_map(fn($pair) => count($pair['maintypes']), $pairs));

            $amountValues = [];
            if ($lfaData && isset($lfaData)) {
                foreach ($lfaData as $amount) {
                    $value = match ($amount->temptypeid) {
                        1 => $amount->value_numeric,
                        2 => $amount->value_char,
                        3 => $amount->value_date,
                        4 => $amount->value_radio,
                        default => '',
                    };
                    $amountValues[$amount->maintypeid][$amount->tmpauditheaderid] = $value;
                }
            }

            $html = '<table class="table table-bordered table-sm financial-table" style="border:1px solid #000;border-collapse:collapse;width:100%">';
            $html .= '<thead style="background:#f8f9fa;"><tr>';
            foreach ($headers as $header) {
                $label = $language === 'en' ? ($header['lblename'] ?? '') : ($header['lbltname'] ?? '');
                $bold = ($header['inputtype'] === 'N') ? 'font-weight:bold;background:#e9ecef;' : '';
                $html .= "<th style='border:1px solid #000;{$bold}'>" . e($label) . '</th>';
            }
            $html .= '</tr></thead><tbody>';

            for ($row = 0; $row < $maxRows; $row++) {
                $html .= '<tr>';

                foreach ($pairs as $pairIndex => $pair) {
                    $item = $pair['maintypes'][$row] ?? null;
                    $isHeading = ($item['maintypetotalflag'] ?? '') === 'HEADING';
                    $isTotal = in_array(($item['maintypetotalflag'] ?? ''), ['RTOTAL', 'ETOTAL', 'LTOTAL', 'ATOTAL']);
                    $isEmpty = !$item;

                    // Category name
                    $catLabel = '';
                    if ($item) {
                        $catLabel = ($language === 'en') ? ($item['maintypeename'] ?? '') : ($item['maintypetname'] ?? '');
                    }

                    $catStyle = 'border:1px solid #000; padding:6px;';
                    if ($isHeading) {
                        $catStyle .= 'font-weight:bold;background:#f1f1f1;';
                    }
                    if ($isTotal) {
                        $catStyle .= 'font-weight:bold;background:#e9ecef;';
                    }

                    $html .= "<td style='{$catStyle}'>" . e($catLabel ?: '-') . '</td>';

                    // Amount columns
                    foreach ($pair['amount_headers'] as $amountHeader) {
                        $headerId = $amountHeader['tmpauditheaderid'] ?? 0;
                        $value = ($item && isset($item['maintypeid']))
                            ? ($amountValues[$item['maintypeid']][$headerId] ?? '')
                            : '';

                        if ($item && ($item['temptypeid'] ?? 0) == 1 && $value !== '') {
                            $value = number_format((float) $value, 2, '.', '');
                        } elseif (($item['temptypeid'] ?? 0) == 4 && $value !== '') {
                            $value = ucfirst($value);
                            if ($language !== 'en') {
                                $value = ($value === 'Yes') ? 'ஆம்' : (($value === 'No') ? 'இல்லை' : $value);
                            }
                        }

                        // For HEADING or empty rows → show dash
                        if ($isHeading || $isEmpty) {
                            $value = '-';
                        }

                        $amountStyle = 'border:1px solid #000; text-align:right; padding:6px;';
                        if ($isTotal) {
                            $amountStyle .= 'font-weight:bold;background:#e9ecef;';
                        }
                        if ($isHeading) {
                            $amountStyle .= 'background:#f9f9f9;';
                        }

                        $html .= "<td style='{$amountStyle}'>" . e($value ?: '') . '</td>';
                    }
                }

                $html .= '</tr>';
            }

            $html .= '</tbody></table>';
            $htmlOutput .= $html;
        }

        return $htmlOutput;
    }

    // ---------------milk---------------------------------------

   private function EntrySheetdata($inst, $language, $formcode, $tempplanid, $catcode)
{
     $sessiondetails = session('charge');
     $deptcode = $sessiondetails->deptcode;


    switch ($formcode) {
        case 'HRIA01':
            [$templatesData, $amounts] = $this->fetchTemplateData($deptcode, $formcode, $inst->instid, $tempplanid);
            return self::structureHriaTemplate($templatesData, $amounts, $language);

        case 'HRIA02':
            [$templatesData, $amounts] = $this->fetchTemplateData($deptcode, $formcode, $inst->instid, $tempplanid);
            return self::structureHriaFullReport($templatesData, $amounts, $language);

        case 'MILK01':
            [$templatesData, $amounts] = $this->fetchTemplateData($deptcode, $formcode, $inst->instid, $tempplanid);
            return self::milkstructuretemplate($templatesData, $amounts);

        case 'DCA01':
        case 'DCA02':
            [$templatesData, $amounts] = $this->fetchTemplateData($deptcode, $formcode, $inst->instid, $tempplanid, $catcode);
            return self::structureDcaReport($templatesData, $amounts, $language);

        case 'LFA01':
            [$templatesData, $amounts] = $this->fetchTemplateData($deptcode, $formcode, $inst->instid, $tempplanid);
            return self::structureLfaReport($templatesData, $amounts, $language);

        default:
            return '<p>Not applicable for this form</p>';
    }
}




private function fetchTemplateData(string $deptcode, string $formcode, $instid, $tempplanid, $catcode = null): array
{

    $auditTemplates = FormatModel::getTemplates($deptcode, $formcode, $instid, $tempplanid, $catcode);
    $amounts        = FormatModel::getdetails($instid, $formcode);


    $templatesData = [];
    foreach ($auditTemplates as $temp) {
        $templatesData[] = [
            'structure' => FormatModel::getTemplateStructure($temp->tmpaudittypeid, $instid),
        ];
    }



    return [$templatesData, $amounts];
}

private function prepareDynamicData($inst, $language, string $logoBase64)
{
    if ($language === 'ta') {
        $deptName        = $inst->depttlname  ?? 'தணிக்கை துறை';
        $distName        = ($inst->disttname  ?? 'தெரியாதது') . ' மாவட்டம்';
        $instituteName   = $inst->insttname   ?? 'தெரியாத நிறுவனம்';
        $auditReportYear = 'ஆண்டு அறிக்கை';
        $auditReportText = 'டெம்ப்ளேட் தணிக்கை அறிக்கை சுருக்கம்';
    } else {
        $deptName        = $inst->deptelname  ?? 'Audit Department';
        $distName        = ($inst->distename  ?? 'Unknown') . ' District';
        $instituteName   = $inst->instename   ?? 'Unknown Institution';
        $auditReportYear = 'Annual Report';
        $auditReportText = 'Template Audit Report Summary';
    }

    return [
        'DeptName'           => $deptName,
        'DistName'           => $distName,
        'InstituteName'      => $instituteName,
        'FinancialYear'      => $inst->audit_period ?? ($language === 'ta' ? 'தெரியாத கணக்கு ஆண்டு' : 'Unknown Audit Period'),
        'AuditReport_Year'   => $auditReportYear,
        'AuditReport_Text'   => $auditReportText,
        'AuditorName'        => $inst->auditor_name    ?? '-',
        'InstitutionDetails' => $inst->inst_details    ?? '-',
        'EntrySheetHtml'     => $inst->entry_sheet     ?? '-',
        'Remarks'            => $inst->remarks         ?? [],
        'Attachments'        => $inst->attachments     ?? [],
        'AuditCertificate'   => $inst->audit_certificate ?? '-',
        'base64Image'        => $logoBase64,
        'deptsize'           => 24,
    ];
}


    public static function liabilitiesdeptfetch()
    {
        $dept = FormatModel::commondeptfetch();
        $category = FormatModel::commongetcategory();
        $subcategory = FormatModel::commongetSubcategory();
        $institution = FormatModel::commongetinstitution();

        return view('audit.Liability_details', compact('dept', 'category', 'subcategory', 'institution'));
    }

    public function commongetCategoriesBasedOnDept(Request $request)
    {
        // Validate the input
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $deptcode = $request->input('deptcode');

        $category = FormatModel::commongetcategoryByDept($deptcode);

        if ($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No Category found'], 404);
        }
    }

    public function subcatbasedoncategoryforliabilities(Request $request)
    {
        $request->validate([
            'category' => ['required', 'array'],
            'category.*' => ['required', 'string', 'regex:/^(\d+|A)$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('category');

        $subcategory = FormatModel::commongetSubcategoryByCategory($category);

        return response()->json($subcategory);
    }

    public function getinstituionbasedonsubcategory(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'category' => ['required', 'array'],
            'category.*' => ['required', 'string', 'regex:/^(\d+|A)$/'],
            'subcatcode' => ['nullable', 'array'],
            'subcatcode.*' => ['nullable', 'regex:/^(\d+|A)$/'],
        ], [
            'required' => 'The :attribute field is required.',
            'regex' => 'The :attribute field must be a valid number.',
        ]);

        $category = $request->input('catcode');
        $deptcode = $request->input('deptcode');
        $subcatcode = $request->input('subcatcode');

        $institution = FormatModel::getinstituionbasedonsubcategory($deptcode, $category, $subcatcode);

        return response()->json($institution);
    }

    /* Template audit report end**************************** */

    private function convertPdfTo14fortmp($inputPath)
    {
        $tempPath = storage_path('app/temp_' . uniqid() . '.pdf');

        $input = escapeshellarg($inputPath);
        $output = escapeshellarg($tempPath);

        // Detect OS
        // if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        //     $gsPath = '"C:\Program Files\gs\gs10.06.0\bin\gswin64c.exe"';
        // } else {
        //     $gsPath = 'gs';  // Linux / production
        // }

        // $command = "$gsPath -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$output $input";

        exec($command, $outputLines, $resultCode);

        if ($resultCode === 0 && file_exists($tempPath)) {
            return $tempPath;
        }

        return false;
    }

 public static function reportpreview_dropdown(Request $request)
    {

        $viewName   = $request->route('viewName');
        $charge     = session('charge');
        $deptcode   = $charge->deptcode ?? NULL;

        $dept       = FormatModel::getdeptData($deptcode);
        $region   = FormatModel::getRegion();
        $district   = FormatModel::getDistrict();

        return view($viewName, compact('dept', 'region', 'district'));
    }

    function fetch_deptbaseddata(Request $request)
    {
        $validatedData = $request->validate([
            'deptcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
            'regioncode'    => ['nullable', 'string', 'regex:/^\d+$/'],
            'distcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
            'valuefor'      => ['required', 'string', 'in:region,district,plan'], // Include "region"

        ], [
            'required' => 'The :attribute field is required.',
            'regex'    => 'The :attribute field must be a valid number.',
            'in'       => 'The :attribute field must be one of: region, district, institution.',
        ]);

        $deptcode = $validatedData['deptcode'];
        $regioncode = $validatedData['regioncode'] ?? null;
        $distcode = $validatedData['distcode'] ?? null;
        $valuefor = $validatedData['valuefor'];

        if (($valuefor === 'region' && !$deptcode)) {
            return response()->json(['success' => false, 'message' => 'Department code is required for Region.'], 422);
        }
        if (($valuefor === 'district' && !$regioncode)) {
            return response()->json(['success' => false, 'message' => 'Region code is required for district.'], 422);
        }
        if ($valuefor === 'district' && !$deptcode) {
            return response()->json(['success' => false, 'message' => 'Department code is required for district.'], 422);
        }


        try {

            $getdata =  FormatModel::fetch_deptbaseddata(
                $deptcode,
                $regioncode,
                $distcode,
                $valuefor,

            );
            if ($getdata) {
                return response()->json(['success' => true, 'data' => $getdata['data'], 'planmapData' => $getdata['planmapData'],]);
            }

            return response()->json(['success' => false, 'message' => 'Data not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function fetch_listinstitutes(Request $request)
    {

        try {



            $validatedData = $request->validate([
                'deptcode'      => ['nullable', 'string', 'regex:/^\d+$/'],
                'regioncode'    => ['nullable', 'string', 'regex:/^\d+$/'],
                'distcode'      => ['required', 'string', 'regex:/^\d+$/'],
                'planmappingid' => ['required'],

            ], [
                'required' => 'The :attribute field is required.',
                'regex'    => 'The :attribute field must be a valid number.',
            ]);

            $session = session('charge');
            $deptcode = $request->deptcode ?: ($session->deptcode ?? null);
            $regioncode = $request->regioncode ?: ($session->regioncode ?? null);
            $distcode = $request->distcode ?: ($session->distcode ?? null);
            $planmappingid = $request->planmappingid ?: null;

            $data = [
                'deptcode' => $deptcode,
                'regioncode' => $regioncode,
                'distcode' => $distcode,
                'planmappingid' => $planmappingid
            ];


            $session = session('charge');
            $teamHead =  $session->auditteamhead;
            $results  =   FormatModel::fetch_previewlistinstitutes($data);

            if ($results->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Institution Details not found',
                    'data' => null
                ], 404);
            }
            $resultsNew = [];
            foreach ($results as $all) {
                if ($all->exitmeetdate) {
                    // Convert exitmeetdate to timestamp and add 6 days
                    $exitmeetdate = strtotime($all->exitmeetdate);
                    $dateAfter6Days = strtotime('+6 days', $exitmeetdate);
                    $currdate = strtotime(date('Y-m-d'));

                    // Check if current date is more than 6 days after exitmeetdate
                    if ($currdate > $dateAfter6Days) {
                        $all->formatted_fromdate = Controller::ChangeDateFormat($all->fromdate);
                        $all->formatted_todate = Controller::ChangeDateFormat($all->todate);
                        $all->formatted_entrydate = Controller::ChangeDateFormat($all->entrymeetdate);
                        $all->formatted_exitdate = Controller::ChangeDateFormat($all->exitmeetdate);
                        $resultsNew[] = $all;
                    }
                }
            }



            return response()->json([
                'success' => true,
                'message' => '',
                'data' => $resultsNew
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
            ], 500);
        }
    }
}
