<?php

// app/Http/Controllers/TemplateAuditController.php

namespace App\Http\Controllers;

use App\Models\AuditManagementModel;
use App\Models\AuditPeriodModel;
use App\Models\TemplateAudit;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Models\CommonModel;


class TemplateAuditController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function index()
    {
        try {
            $sessioncharge = session('charge');
            $sessionuser = session('user');
            if (empty($sessionuser)) {
                throw new \Exception('No session Data');
            } else {
                $sessionuserid = $sessionuser->userid;
                $formsessionuserid = Crypt::encryptString($sessionuserid);
            }

            $deptcode = $sessioncharge->deptcode;
            $quarter_det = TemplateAudit::getquarterdetails($deptcode);
            $templates = TemplateAudit::getTemplates($deptcode);

            return view('templateaudit.templateplan', compact('formsessionuserid', 'quarter_det', 'templates'));
        } catch (\Exception $e) {
            return view('templateaudit.templateplan', [
                'quarter_det' => $quarter_det ?? null,
                'errorMessage' => $e->getMessage(),
                'pageName' => 'index',
            ]);
        }
    }

    public function temp_auditplandetails(Request $request)
    {
        try {
            $quartercode = $request->quartercode;

            $sessionuser = session('user');
            $charge = session('charge');

            if (empty($sessionuser)) {
                throw new \Exception('No session Data');
            } else {
                $userid = $sessionuser->userid;
            }

            $deptTypeMapping = [
                '01' => 'okp',    // HRIA/OKP Department
                '02' => 'lfa',    // LFA Department
                '04' => 'dca',    // DCA Department
                '05' => 'milk',    // Milk Department
            ];

            $auditType = $deptTypeMapping[$charge->deptcode] ?? 'okp';

            $audit_plandetail = TemplateAudit::fetch_temp_auditplandetails($userid, $quartercode, $auditType, $charge->deptcode) ?? [];
            foreach ($audit_plandetail as $item) {
                 $item->encrypted_prioritycode = Crypt::encryptString($item->prioritycode);
                $item->encrypted_tempplanid = Crypt::encryptString($item->tempplanid);
                $item->encrypted_instid = Crypt::encryptString($item->instid);
                $item->encrypted_deptcode = Crypt::encryptString($item->deptcode);
                $item->encrypted_formcode = Crypt::encryptString($item->formcode);
                $item->encrypted_catcode = Crypt::encryptString($item->catcode);
		$item->encrypted_subcatcode = Crypt::encryptString($item->subcatcode);

            }

            return response()->json(['data' => $audit_plandetail]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage(), 'error' => 409], 409);
        }
    }

    public function startTewmplateAudit()
    {
        return view('templateaudit.forms.lfa01');
    }

    public function startTemplateAudit($deptcode, $formcode, $instid, $tempplanid, $catcode,$subcatcode,$auditquartercode,$prioritycode)
    {
        try {
            $deptcode = Crypt::decryptString($deptcode);
            $formcode = Crypt::decryptString($formcode);
            $instid = Crypt::decryptString($instid);
            $tempplanid = Crypt::decryptString($tempplanid);
            $catcode = Crypt::decryptString($catcode);
$subcatcode = Crypt::decryptString($subcatcode);
 $prioritycode = Crypt::decryptString($prioritycode);
            // dd($tempplanid);
        } catch (\Exception $e) {
            return redirect()->route('templateaudit.index')
                ->with('errorMessage', $e->getMessage());
        }

        $auditTemplates = TemplateAudit::getTemplates($deptcode, $formcode, $instid, $tempplanid, $catcode,$subcatcode,$auditquartercode);

        if ($auditTemplates->isEmpty()) {
            return redirect()->route('templateaudit.index')
                ->with('errorMessage', 'No audit templates found for the selected criteria.');
        }

        $basicinformation = $auditTemplates->first();
        if ($basicinformation) {
            $basicinformation->audit_date = now()->format('Y-m-d');
            $basicinformation->audit_year = now()->year;
            $basicinformation->auditor_name_en = $basicinformation->username ?? 'Not Assigned';
            $basicinformation->auditor_name_ta = $basicinformation->usertamilname ?? 'ஒதுக்கப்படவில்லை';
            $basicinformation->instid = $instid;
            session(['basicinformation' => $basicinformation]);

        }

        $hriaData = null;
        $selectedYears = [];
        $existingRemarks = [];
        $maxRows = $basicinformation->max_audit_remarks_rows ?? 5;
        $currentRows = 1;

        $existingAudit = DB::table('audit.hria_details')
            ->where('tempplanid', $tempplanid)
            ->whereIn('statusflag', ['E', 'F'])
            ->first();

        $basicinformation->audit_end_date = $existingAudit ? date('Y-m-d', strtotime($existingAudit->audit_end_date)) : null;
        if ($existingAudit) {
            $hriaData = $existingAudit;
            if ($hriaData->file_id) {
                $fileDetails = DB::table('audit.fileuploaddetail')
                    ->where('fileuploadid', $hriaData->file_id)
                    ->first();
                $hriaData->fileDetails = $fileDetails;
            }

            $amounts = DB::table('audit.template_hria as tho')
                ->join('audit.mst_templatemaintype as mtm', 'mtm.maintypeid', '=', 'tho.maintypeid')
                ->join('audit.mst_templatetype as tt', 'tt.temptypeid', '=', 'tho.temptypeid')
                ->where('tho.okpid', $existingAudit->okpsdetails_id)
                ->select(
                    'tho.*',
                    'mtm.maintypeename',
                    'mtm.maintypetname',
                    'tt.temptypename'
                )
                ->get();

            $hriaData->amounts = $amounts;

            $selectedYears = $existingAudit->audityearid ? json_decode($existingAudit->audityearid, true) : [];
            if (! is_array($selectedYears)) {
                $selectedYears = [];
            }

            // Get remarks from the new template_hria_remarks table via remark_ids
            if (isset($hriaData->remark_ids) && ! empty($hriaData->remark_ids)) {
                $remarkIds = json_decode($hriaData->remark_ids, true);
                if (is_array($remarkIds) && ! empty($remarkIds)) {
                    $existingRemarks = DB::table('audit.template_hria_remarks')
                        ->whereIn('remark_id', $remarkIds)
                        ->where('statusflag', '!=', 'D')
                        ->orderBy('remark_id')
                        ->get()
                        ->toArray();
                }
            }

            // Fallback to old remarks field if no data in new table
            if (empty($existingRemarks) && isset($hriaData->remarks) && is_array(json_decode($hriaData->remarks, true))) {
                $existingRemarks = json_decode($hriaData->remarks, true);
            }

            $currentRows = max(count($existingRemarks), 1);
        }

        $templatesData = [];
        foreach ($auditTemplates as $temp) {
            $templatesData[] = [
                'structure' => TemplateAudit::getTemplateStructure( $temp->tmpaudittypeid, $instid, $auditquartercode, $prioritycode),
            ];
        }

        $auditEndDate = now()->format('d/m/Y');

        $Master_Auditcertificate = DB::table('audit.mst_auditcertificatetype')
            ->select('cer_type_code', 'cer_content')
            ->get()
            ->map(function ($cert) use ($auditEndDate) {
                $decodedContent = json_decode($cert->cer_content);
                if ($decodedContent && isset($decodedContent->content)) {
                    $cert->cer_content = str_replace('[audityear]', $auditEndDate, $decodedContent->content);
                }

                return $cert;
            });

        $viewName = match ($formcode) {
            'HRIA01' => 'templateaudit.forms.hria01',
            'HRIA02' => 'templateaudit.forms.hria02',
            'LFA01' => 'templateaudit.forms.lfa01',
            'SGA01' => 'templateaudit.forms.sga01',
            'DCA01' => 'templateaudit.forms.dca01',
            'MILK01' => 'templateaudit.forms.milk01',
            default => 'templateaudit.entry',
        };

        $banks = DB::table('audit.mst_bank_master')
            ->where('statusflag', 'Y')
            ->orderBy('bankid', 'asc')
            ->select('bankid','bank_name', 'ifsc_code')
            ->get();

        $auditperiod = AuditPeriodModel::select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->whereIn('lagacyyear', ['N', 'B'])
            ->orderBy('fromyear', 'desc')
            ->get();

        $isEditMode = $existingAudit !== null;
        $isFinalMode = $isEditMode && $existingAudit->statusflag === 'F';

        // dd($hriaData);
        return view($viewName, [
            'templatesData' => $templatesData,
            'basicinformation' => $basicinformation,
    	    'banksGrouped' => $banks,
            'hriaData' => $hriaData,
            'auditperiod' => $auditperiod,
            'selectedYears' => $selectedYears,
            'isEditMode' => $isEditMode,
            'isFinalMode' => $isFinalMode,
            'existingRemarks' => $existingRemarks,
            'maxRows' => $maxRows,
            'currentRows' => $currentRows,
            'Master_Auditcertificate' => $Master_Auditcertificate,
        ]);
    }

    public function startTemplateAuditLfa($deptcode, $formcode, $instid, $tempplanid, $catcode,$subcatcode,$auditquartercode,$prioritycode)
    {
        try {
            $deptcode = Crypt::decryptString($deptcode);
            $formcode = Crypt::decryptString($formcode);
            $instid = Crypt::decryptString($instid);
            $tempplanid = Crypt::decryptString($tempplanid);
            $catcode = Crypt::decryptString($catcode);
$subcatcode = Crypt::decryptString($subcatcode);
 $prioritycode = Crypt::decryptString($prioritycode);
        } catch (\Exception $e) {
            return redirect()->route('templateaudit.index')
                ->with('errorMessage', $e->getMessage());
        }

        $auditTemplates = TemplateAudit::getTemplates($deptcode, $formcode, $instid, $tempplanid, $catcode,$subcatcode,$auditquartercode,$prioritycode);

        if ($auditTemplates->isEmpty()) {
            return redirect()->route('templateaudit.index')
                ->with('errorMessage', 'No audit templates found for the selected criteria.');
        }

        $basicinformation = $auditTemplates->first();
        if ($basicinformation) {
            $basicinformation->audit_date = now()->format('Y-m-d');
            $basicinformation->audit_year = now()->year;
            $basicinformation->auditor_name_en = $basicinformation->username ?? 'Not Assigned';
            $basicinformation->auditor_name_ta = $basicinformation->usertamilname ?? 'ஒதுக்கப்படவில்லை';
            $basicinformation->instid = $instid;
            session(['basicinformation' => $basicinformation]);

        }

        $lfaData = null;
        $selectedYears = [];
        $existingRemarks = [];
        $maxRows = $basicinformation->max_audit_remarks_rows ?? 5;
        $currentRows = 1;

        $existingAudit = DB::table('audit.lfa_details')
            ->where('tempplanid', $tempplanid)
            ->whereIn('statusflag', ['E', 'F'])
            ->first();

        $basicinformation->audit_end_date = $existingAudit ? date('Y-m-d', strtotime($existingAudit->audit_end_date)) : null;

        if ($existingAudit) {
            $lfaData = $existingAudit;

            if ($lfaData->file_id) {
                $fileDetails = DB::table('audit.fileuploaddetail')
                    ->where('fileuploadid', $lfaData->file_id)
                    ->first();
                $lfaData->fileDetails = $fileDetails;
            }

            // Update amounts query for new structure
            $amounts = DB::table('audit.template_lfa as tlfa')
                ->join('audit.mst_templatemaintype as mtm', 'mtm.maintypeid', '=', 'tlfa.maintypeid')
                ->leftjoin('audit.mst_templatesubtype as st', 'st.subtypeid', '=', 'tlfa.subtypeid')
                ->join('audit.mst_templatetype as tt', 'tt.temptypeid', '=', 'tlfa.temptypeid')
                ->join('audit.mst_templateheader as tah', 'tah.tmpauditheaderid', '=', 'tlfa.tmpauditheaderid')
                ->where('tlfa.lfaid', $existingAudit->lfa_details_id)
                ->select(
                    'tlfa.*',
                    'mtm.maintypeename',
                    'mtm.maintypetname',
                    'tt.temptypename',
                    'st.temptypeid as subtemptypeid',
                    'st.stypeename',
                    'st.stypetname',
                )
                ->get();

            $lfaData->amounts = $amounts;

            $selectedYears = $existingAudit->audityearid ? json_decode($existingAudit->audityearid, true) : [];
            if (! is_array($selectedYears)) {
                $selectedYears = [];
            }

            if (isset($lfaData->remark_ids) && ! empty($lfaData->remark_ids)) {
                $remarkIds = json_decode($lfaData->remark_ids, true);
                if (is_array($remarkIds) && ! empty($remarkIds)) {
                    $existingRemarks = DB::table('audit.template_lfa_remarks')
                        ->whereIn('remark_id', $remarkIds)
                        ->where('statusflag', '!=', 'D')
                        ->orderBy('remark_id')
                        ->get()
                        ->toArray();
                }
            }

            // Fallback to old remarks field if no data in new table
            if (empty($existingRemarks) && isset($lfaData->remarks) && is_array(json_decode($lfaData->remarks, true))) {
                $existingRemarks = json_decode($lfaData->remarks, true);
            }

            $currentRows = max(count($existingRemarks), 1);
        }
        $templatesData = [];
        foreach ($auditTemplates as $temp) {
            $templatesData[] = [
                'structure' => TemplateAudit::getTemplateStructure($temp->tmpaudittypeid, $instid, $auditquartercode, $prioritycode),
            ];
        }

        $viewName = match ($formcode) {
            'LFA01' => 'templateaudit.forms.lfa01',
            default => 'templateaudit.entry',
        };

        $banks = DB::table('audit.mst_bank_master')
            ->where('statusflag', 'Y')
            ->orderBy('bankid', 'asc')
            ->select('bank_name', 'ifsc_code')
            ->get();

        $banksGrouped = [];
        foreach ($banks as $b) {
            $banksGrouped[$b->bank_name][] = $b->ifsc_code;
        }

        $auditEndDate = now()->format('d/m/Y');

        $Master_Auditcertificate = DB::table('audit.mst_auditcertificatetype')
            ->select('cer_type_code', 'cer_content')
            ->get()
            ->map(function ($cert) use ($auditEndDate) {
                $decodedContent = json_decode($cert->cer_content);
                if ($decodedContent && isset($decodedContent->content)) {
                    $cert->cer_content = str_replace('[audityear]', $auditEndDate, $decodedContent->content);
                }

                return $cert;
            });

        $auditperiod = AuditPeriodModel::select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->whereIn('lagacyyear', ['N', 'B'])
            ->orderBy('fromyear', 'desc')
            ->get();

        $isEditMode = $existingAudit !== null;
        $isFinalMode = $isEditMode && $existingAudit->statusflag === 'F';

        return view($viewName, [
            'templatesData' => $templatesData,
            'basicinformation' => $basicinformation,
            'banksGrouped' => $banksGrouped,
            'lfaData' => $lfaData,
            'auditperiod' => $auditperiod,
            'selectedYears' => $selectedYears,
            'isEditMode' => $isEditMode,
            'isFinalMode' => $isFinalMode,
            'existingRemarks' => $existingRemarks,
            'maxRows' => $maxRows,
            'currentRows' => $currentRows,
            'Master_Auditcertificate' => $Master_Auditcertificate,

        ]);
    }

    public function startTemplateAuditDca($deptcode, $formcode, $instid, $tempplanid, $catcode,$subcatcode,$auditquartercode,$prioritycode)
    {

        try {
            $deptcode = Crypt::decryptString($deptcode);
            $formcode = Crypt::decryptString($formcode);
            $instid = Crypt::decryptString($instid);
            $tempplanid = Crypt::decryptString($tempplanid);
            $catcode = Crypt::decryptString($catcode);
$subcatcode = Crypt::decryptString($subcatcode);
 $prioritycode = Crypt::decryptString($prioritycode);
        } catch (\Exception $e) {
            return redirect()->route('templateaudit.index')
                ->with('errorMessage', $e->getMessage());
        }

        $auditTemplates = TemplateAudit::getTemplates($deptcode, $formcode, $instid, $tempplanid, $catcode,$subcatcode,$auditquartercode,$prioritycode);

        if ($auditTemplates->isEmpty()) {
            return redirect()->route('templateaudit.index')
                ->with('errorMessage', 'No audit templates found for the selected criteria.');
        }

        $basicinformation = $auditTemplates->first();
        if ($basicinformation) {
            $basicinformation->audit_date = now()->format('Y-m-d');
            $basicinformation->audit_year = now()->year;
            $basicinformation->auditor_name_en = $basicinformation->username ?? 'Not Assigned';
            $basicinformation->auditor_name_ta = $basicinformation->usertamilname ?? 'ஒதுக்கப்படவில்லை';
            $basicinformation->instid = $instid;
            session(['basicinformation' => $basicinformation]);

        }

        $dcaData = null;
        $selectedYears = [];
        $existingRemarks = [];
        $maxRows = $basicinformation->max_audit_remarks_rows ?? 5;
        $currentRows = 1;

        $existingAudit = DB::table('audit.dca_details')
            ->where('tempplanid', $tempplanid)
            ->whereIn('statusflag', ['E', 'F'])
            ->first();
        $basicinformation->audit_end_date = $existingAudit ? date('Y-m-d', strtotime($existingAudit->audit_end_date)) : null;

        if ($existingAudit) {
            $dcaData = $existingAudit;

            if ($dcaData->file_id) {
                $fileDetails = DB::table('audit.fileuploaddetail')
                    ->where('fileuploadid', $dcaData->file_id)
                    ->first();
                $dcaData->fileDetails = $fileDetails;
            }

            // Update amounts query for new structure
            $amounts = DB::table('audit.template_dca as tdca')
                ->join('audit.mst_templatemaintype as mtm', 'mtm.maintypeid', '=', 'tdca.maintypeid')
                ->join('audit.mst_templatesubtype as st', 'st.subtypeid', '=', 'tdca.subtypeid')
                ->join('audit.mst_templatetype as tt', 'tt.temptypeid', '=', 'tdca.temptypeid')
                ->join('audit.mst_templateheader as tah', 'tah.tmpauditheaderid', '=', 'tdca.tmpauditheaderid')
                ->where('tdca.dcaid', $existingAudit->dca_details_id)
                ->select(
                    'tdca.*',
                    'mtm.maintypeename',
                    'mtm.maintypetname',
                    'tt.temptypename',
                    'st.temptypeid as subtemptypeid',
                    'st.stypeename',
                    'st.stypetname',
                )
                ->get();

            $dcaData->amounts = $amounts;

            $selectedYears = $existingAudit->audityearid ? json_decode($existingAudit->audityearid, true) : [];
            if (! is_array($selectedYears)) {
                $selectedYears = [];
            }

            if (isset($dcaData->remark_ids) && ! empty($dcaData->remark_ids)) {
                $remarkIds = json_decode($dcaData->remark_ids, true);
                if (is_array($remarkIds) && ! empty($remarkIds)) {
                    $existingRemarks = DB::table('audit.template_dca_remarks')
                        ->whereIn('remark_id', $remarkIds)
                        ->where('statusflag', '!=', 'D')
                        ->orderBy('remark_id')
                        ->get()
                        ->toArray();
                }
            }

            if (empty($existingRemarks) && isset($dcaData->remarks) && is_array(json_decode($dcaData->remarks, true))) {
                $existingRemarks = json_decode($dcaData->remarks, true);
            }

            $currentRows = max(count($existingRemarks), 1);
        }
        $templatesData = [];
        foreach ($auditTemplates as $temp) {
            $templatesData[] = [
                'structure' => TemplateAudit::getTemplateStructure( $temp->tmpaudittypeid, $instid, $auditquartercode, $prioritycode),
            ];
        }

        $auditEndDate = now()->format('d/m/Y');

        $Master_Auditcertificate = DB::table('audit.mst_auditcertificatetype')
            ->select('cer_type_code', 'cer_content')
            ->get()
            ->map(function ($cert) use ($auditEndDate) {
                $decodedContent = json_decode($cert->cer_content);
                if ($decodedContent && isset($decodedContent->content)) {
                    $cert->cer_content = str_replace('[audityear]', $auditEndDate, $decodedContent->content);
                }

                return $cert;
            });
        $viewName = match ($formcode) {
            'HRIA01' => 'templateaudit.forms.hria01',
            'HRIA02' => 'templateaudit.forms.hria02',
            'LFA01' => 'templateaudit.forms.lfa01',
            'SGA01' => 'templateaudit.forms.sga01',
            'DCA01' => 'templateaudit.forms.dca01',
            'DCA02' => 'templateaudit.forms.dca02',
            'MILK01' => 'templateaudit.forms.milk01',
            default => 'templateaudit.entry',
        };

        $banks = DB::table('audit.mst_bank_master')
            ->where('statusflag', 'Y')
            ->orderBy('bankid', 'asc')
            ->select('bank_name', 'ifsc_code')
            ->get();

        $banksGrouped = [];
        foreach ($banks as $b) {
            $banksGrouped[$b->bank_name][] = $b->ifsc_code;
        }

        $auditperiod = AuditPeriodModel::select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->whereIn('lagacyyear', ['N', 'B'])
            ->orderBy('fromyear', 'desc')
            ->get();

        $isEditMode = $existingAudit !== null;
        $isFinalMode = $isEditMode && $existingAudit->statusflag === 'F';

        return view($viewName, [
            'templatesData' => $templatesData,
            'basicinformation' => $basicinformation,
            'banksGrouped' => $banksGrouped,
            'dcaData' => $dcaData,
            'auditperiod' => $auditperiod,
            'selectedYears' => $selectedYears,
            'isEditMode' => $isEditMode,
            'isFinalMode' => $isFinalMode,
            'existingRemarks' => $existingRemarks,
            'maxRows' => $maxRows,
            'currentRows' => $currentRows,
            'Master_Auditcertificate' => $Master_Auditcertificate,

        ]);
    }

    public function startTemplateAuditMilk($deptcode, $formcode, $instid, $tempplanid, $catcode,$subcatcode,$auditquartercode,$prioritycode)
    {
        try {
            $deptcode = Crypt::decryptString($deptcode);
            $formcode = Crypt::decryptString($formcode);
            $instid = Crypt::decryptString($instid);
            $tempplanid = Crypt::decryptString($tempplanid);
            $catcode = Crypt::decryptString($catcode);
$subcatcode = Crypt::decryptString($subcatcode);
 $prioritycode = Crypt::decryptString($prioritycode);
        } catch (\Exception $e) {
            return redirect()->route('templateaudit.index')
                ->with('errorMessage', $e->getMessage());
        }

        $auditTemplates = TemplateAudit::getTemplates($deptcode, $formcode, $instid, $tempplanid, $catcode,$subcatcode,$auditquartercode,$prioritycode);
        if ($auditTemplates->isEmpty()) {
            return redirect()->route('templateaudit.index')
                ->with('errorMessage', 'No audit templates found for the selected criteria.');
        }

        $basicinformation = $auditTemplates->first();

        if ($basicinformation) {
            $basicinformation->audit_date = now()->format('Y-m-d');
            $basicinformation->audit_year = now()->year;
            $basicinformation->auditor_name_en = $basicinformation->username ?? 'Not Assigned';
            $basicinformation->auditor_name_ta = $basicinformation->usertamilname ?? 'ஒதுக்கப்படவில்லை';
            $basicinformation->instid = $instid;
            session(['basicinformation' => $basicinformation]);

        }

        $milkData = null;
        $selectedYears = [];
        $existingRemarks = [];
        $maxRows = $basicinformation->max_audit_remarks_rows ?? 5;
        $currentRows = 1;

        $existingAudit = DB::table('audit.milk_details')
            ->where('tempplanid', $tempplanid)
            ->whereIn('statusflag', ['E', 'F'])
            ->first();

        $basicinformation->audit_end_date = $existingAudit ? date('Y-m-d', strtotime($existingAudit->audit_end_date)) : null;
        if ($existingAudit) {
            $milkData = $existingAudit;

            if ($milkData->file_id) {
                $fileDetails = DB::table('audit.fileuploaddetail')
                    ->where('fileuploadid', $milkData->file_id)
                    ->first();
                $milkData->fileDetails = $fileDetails;
            }

            $amounts = DB::table('audit.template_milk as tmilk')
                ->join('audit.mst_templatemaintype as mtm', 'mtm.maintypeid', '=', 'tmilk.maintypeid')
                ->leftjoin('audit.mst_templatesubtype as st', 'st.subtypeid', '=', 'tmilk.subtypeid')
                ->join('audit.mst_templatetype as tt', 'tt.temptypeid', '=', 'tmilk.temptypeid')
                ->join('audit.mst_templateheader as tah', 'tah.tmpauditheaderid', '=', 'tmilk.tmpauditheaderid')
                ->where('tmilk.milkid', $existingAudit->milk_details_id)
                ->select(
                    'tmilk.*',
                    'mtm.maintypeename',
                    'mtm.maintypetname',
                    'tt.temptypename',
                    'st.temptypeid as subtemptypeid',
                    'st.stypeename',
                    'st.stypetname',
                )
                ->get();

            $milkData->amounts = $amounts;

            $selectedYears = $existingAudit->audityearid ? json_decode($existingAudit->audityearid, true) : [];
            if (! is_array($selectedYears)) {
                $selectedYears = [];
            }
            if (isset($milkData->remark_ids) && ! empty($milkData->remark_ids)) {
                $remarkIds = json_decode($milkData->remark_ids, true);
                if (is_array($remarkIds) && ! empty($remarkIds)) {
                    $existingRemarks = DB::table('audit.template_milk_remarks')
                        ->whereIn('remark_id', $remarkIds)
                        ->where('statusflag', '!=', 'D')
                        ->orderBy('remark_id')
                        ->get()
                        ->toArray();
                }
            }

            // Fallback to old remarks field if no data in new table
            if (empty($existingRemarks) && isset($milkData->remarks) && is_array(json_decode($milkData->remarks, true))) {
                $existingRemarks = json_decode($milkData->remarks, true);
            }

            $currentRows = max(count($existingRemarks), 1);
        }
        $templatesData = [];
        foreach ($auditTemplates as $temp) {
            $templatesData[] = [
                'structure' => TemplateAudit::getTemplateStructure($temp->tmpaudittypeid, $instid, $auditquartercode, $prioritycode),
            ];
        }

        $auditEndDate = now()->format('d/m/Y');

        $Master_Auditcertificate = DB::table('audit.mst_auditcertificatetype')
            ->select('cer_type_code', 'cer_content')
            ->get()
            ->map(function ($cert) use ($auditEndDate) {
                $decodedContent = json_decode($cert->cer_content);
                if ($decodedContent && isset($decodedContent->content)) {
                    $cert->cer_content = str_replace('[audityear]', $auditEndDate, $decodedContent->content);
                }

                return $cert;
            });

        $viewName = match ($formcode) {
            'HRIA01' => 'templateaudit.forms.hria01',
            'HRIA02' => 'templateaudit.forms.hria02',
            'LFA01' => 'templateaudit.forms.lfa01',
            'SGA01' => 'templateaudit.forms.sga01',
            'DCA01' => 'templateaudit.forms.dca01',
            'MILK01' => 'templateaudit.forms.milk01',
            default => 'templateaudit.entry',
        };

        $banks = DB::table('audit.mst_bank_master')
            ->where('statusflag', 'Y')
            ->orderBy('bankid', 'asc')
            ->select('bank_name', 'ifsc_code')
            ->get();

        $banksGrouped = [];
        foreach ($banks as $b) {
            $banksGrouped[$b->bank_name][] = $b->ifsc_code;
        }

        $auditperiod = AuditPeriodModel::select('auditperiodid', DB::raw("CONCAT(fromyear, ' - ', toyear) AS audit_period"))
            ->where('deptcode', $deptcode)
            ->where('statusflag', 'Y')
            ->where('financestatus', 'N')
            ->whereIn('lagacyyear', ['N', 'B'])
            ->orderBy('fromyear', 'desc')
            ->get();

        $isEditMode = $existingAudit !== null;
        $isFinalMode = $isEditMode && $existingAudit->statusflag === 'F';

        return view($viewName, [
            'templatesData' => $templatesData,
            'basicinformation' => $basicinformation,
            'banksGrouped' => $banksGrouped,
            'milkData' => $milkData,
            'auditperiod' => $auditperiod,
            'selectedYears' => $selectedYears,
            'isEditMode' => $isEditMode,
            'isFinalMode' => $isFinalMode,
            'existingRemarks' => $existingRemarks,
            'maxRows' => $maxRows,
            'currentRows' => $currentRows,
            'Master_Auditcertificate' => $Master_Auditcertificate,

        ]);
    }

    public function insertUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'priest_name' => 'required|string|max:80',
            'phone_number' => 'required|digits:10|regex:/^[6-9][0-9]{9}$/',
            'bank_name' => 'required|string|max:100',
            'branch_name' => 'required|string|max:100',
            'account_number' => 'required|digits_between:9,18',
           // 'ifsc_code' => 'required|string|max:20',
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'uploadid' => 'nullable|string',
            'amount' => 'required|array',
            'action' => 'required|in:insert,update',
            'finalize' => 'sometimes|boolean',
            'yearselected' => 'required|array',
            'yearselected.*' => 'integer',
            'remarks' => 'required|array',
            'remarks.*.type' => 'required|in:serious,non-serious',
            'remarks.*.text' => 'required|string',

        ]);

        $sessionUser = session('user');
        $sessionCharge = session('charge');
        if (! $sessionCharge || ! isset($sessionUser->userid)) {
            return response()->json(['success' => false, 'message' => 'Charge session not found.'], 400);
        }

        $userId = $sessionUser->userid;
        $basicinformation = session('basicinformation');

        $instid = $basicinformation->instid ?? null;
        if (! $instid) {
            return response()->json(['success' => false, 'message' => 'Institution ID not found.'], 400);
        }


        $tempplanid = null;
        if ($request->filled('tempplanid')) {
            try {
                $tempplanid = Crypt::decryptString($request->input('tempplanid'));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Invalid tempplanid'], 400);
            }
        }

        if (! $tempplanid) {
            return response()->json(['success' => false, 'message' => 'Tempplanid missing from request'], 400);
        }

        $okpid = $request->input('action') === 'update'
            ? Crypt::decryptString($request->input('okpid'))
            : null;

        $basicinformation = session('basicinformation');

        $data = [
            'tempplanid' => $tempplanid,
            'poosari_name' => $request->priest_name,
            'phone_number' => $request->phone_number,
            'bankname' => $request->bankname,
            'bankid' => $request->bank_name,
            'branch_name' => $request->branch_name,
            'account_no' => $request->account_number,
            'ifsc_code' => $request->ifsc_code,
            'statusflag' => $request->has('finalize') && $request->finalize ? 'F' : 'E',
            'cer_type_code' => $request->cer_typecode,
        ];

        $uploadId = $request->input('uploadid');

        if ($request->hasFile('file')) {
            $today = now();
            $destinationarray = [
                $sessionCharge->deptcode,
                $sessionCharge->regioncode,
                $sessionCharge->distcode,
                $today->format('Y'),
                $today->format('m'),
                $today->format('d'),
                $tempplanid,
                View::shared('templateaudit'),
            ];

            $uploadResult = $this->fileUploadService->uploadFile(
                $request->file('file'),
                'uploads/templateaudit',
                $uploadId ?? '',
                $destinationarray
            );

            $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
            if (! $fileuploadId) {
                return response()->json(['success' => false, 'message' => 'File upload failed.'], 500);
            }

            $data['file_id'] = $fileuploadId;
        } elseif (empty($uploadId) && $request->input('action') === 'update') {
            $data['file_id'] = null;
        }

        $data['created_by'] = $userId;
        $data['created_on'] = now();
        $remarksArray = [];
        try {
            $result = TemplateAudit::insertorUpdate(
                $data,
                $request->amount,
                $request->remarks,
                $okpid,
                $userId,
                $request->action,
                $request->yearselected,
                $request->header_mapping
            );

            return response()->json([
                'success' => true,
                'message' => $request->has('finalize') && $request->finalize
                    ? 'Data has been finalized successfully'
                    : 'Data has been saved successfully',
                'okpid' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function lfainsertUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'district_officer_name' => 'required|string|max:80',
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'uploadid' => 'nullable|string',
            'amounts' => 'required|array',
            'action' => 'required|in:insert,update',
            'finalize' => 'sometimes|boolean',
            'yearselected' => 'required|array',
            'yearselected.*' => 'integer',
            'remarks' => 'required|array',
            'remarks.*.type' => 'required|in:serious,non-serious',
            'remarks.*.text' => 'required|string',
        ]);

        $sessionUser = session('user');
        $sessionCharge = session('charge');
        if (! $sessionCharge || ! isset($sessionUser->userid)) {
            return response()->json(['success' => false, 'message' => 'Charge session not found.'], 400);
        }

        $userId = $sessionUser->userid;
        $basicinformation = session('basicinformation');
        $instid = $basicinformation->instid ?? null;

        if (! $instid) {
            return response()->json(['success' => false, 'message' => 'Institution ID not found.'], 400);
        }

        $tempplanid = null;
        if ($request->filled('tempplanid')) {
            try {
                $tempplanid = Crypt::decryptString($request->input('tempplanid'));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Invalid tempplanid'], 400);
            }
        }

        if (! $tempplanid) {
            return response()->json(['success' => false, 'message' => 'Tempplanid missing from request'], 400);
        }

        $lfaid = $request->input('action') === 'update'
            ? Crypt::decryptString($request->input('lfaid'))
            : null;

        $data = [
            'tempplanid' => $tempplanid,
            'remarks' => $request->remarks,
            'statusflag' => $request->has('finalize') && $request->finalize ? 'F' : 'E',
            'audityearid' => json_encode($request->yearselected),
            'district_officer_name' => $request->district_officer_name,
            'cer_type_code' => $request->cer_typecode,

        ];

        $uploadId = $request->input('uploadid');

        if ($request->hasFile('file')) {
            $today = now();
            $destinationarray = [
                $sessionCharge->deptcode,
                $sessionCharge->regioncode,
                $sessionCharge->distcode,
                $today->format('Y'),
                $today->format('m'),
                $today->format('d'),
                $tempplanid,
                View::shared('templateaudit'),
            ];

            $uploadResult = $this->fileUploadService->uploadFile(
                $request->file('file'),
                'uploads/templateaudit',
                $uploadId ?? '',
                $destinationarray
            );

            $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
            if (! $fileuploadId) {
                return response()->json(['success' => false, 'message' => 'File upload failed.'], 500);
            }

            $data['file_id'] = $fileuploadId;
        } elseif (empty($uploadId) && $request->input('action') === 'update') {
            $data['file_id'] = null;
        }

        try {
            $result = TemplateAudit::lfainsertorUpdate(
                $data,
                $request->amounts,
                $request->remarks,
                $lfaid,
                $userId,
                $request->action,
                $request->yearselected
            );

            return response()->json([
                'success' => true,
                'message' => $request->has('finalize') && $request->finalize
                    ? 'Data has been finalized successfully'
                    : 'Data has been saved successfully',

                'lfaid' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function dcainsertUpdate(Request $request)
    {
        $validatedData = $request->validate([
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'uploadid' => 'nullable|string',
            'amount' => 'required|array',
            'action' => 'required|in:insert,update',
            'finalize' => 'sometimes|boolean',
            'yearselected' => 'required|array',
            'yearselected.*' => 'integer',
            'remarks' => 'required|array',
            'remarks.*.type' => 'required|in:serious,non-serious',
            'remarks.*.text' => 'required|string',
        ]);

        $sessionUser = session('user');
        $sessionCharge = session('charge');
        if (! $sessionCharge || ! isset($sessionUser->userid)) {
            return response()->json(['success' => false, 'message' => 'Charge session not found.'], 400);
        }

        $userId = $sessionUser->userid;
        $basicinformation = session('basicinformation');
        $instid = $basicinformation->instid ?? null;

        if (! $instid) {
            return response()->json(['success' => false, 'message' => 'Institution ID not found.'], 400);
        }

        $tempplanid = null;
        if ($request->filled('tempplanid')) {
            try {
                $tempplanid = Crypt::decryptString($request->input('tempplanid'));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Invalid tempplanid'], 400);
            }
        }

        if (! $tempplanid) {
            return response()->json(['success' => false, 'message' => 'Tempplanid missing from request'], 400);
        }

        $dcaid = $request->input('action') === 'update'
            ? Crypt::decryptString($request->input('dcaid'))
            : null;

        $data = [
            'tempplanid' => $tempplanid,
            'remarks' => $request->remarks,
            'statusflag' => $request->has('finalize') && $request->finalize ? 'F' : 'E',
            'audityearid' => json_encode($request->yearselected),
            'cer_type_code' => $request->cer_typecode,

        ];

        $uploadId = $request->input('uploadid');

        if ($request->hasFile('file')) {
            $today = now();
            $destinationarray = [
                $sessionCharge->deptcode,
                $sessionCharge->regioncode,
                $sessionCharge->distcode,
                $today->format('Y'),
                $today->format('m'),
                $today->format('d'),
                $tempplanid,
                View::shared('templateaudit'),
            ];

            $uploadResult = $this->fileUploadService->uploadFile(
                $request->file('file'),
                'uploads/templateaudit',
                $uploadId ?? '',
                $destinationarray
            );

            $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
            if (! $fileuploadId) {
                return response()->json(['success' => false, 'message' => 'File upload failed.'], 500);
            }

            $data['file_id'] = $fileuploadId;
        } elseif (empty($uploadId) && $request->input('action') === 'update') {
            $data['file_id'] = null;
        }

        try {
            $result = TemplateAudit::dcainsertorUpdate(
                $data,
                $request->amount,
                $request->remarks,
                $dcaid,
                $userId,
                $request->action,
                $request->yearselected
            );

            return response()->json([
                'success' => true,
                'message' => $request->has('finalize') && $request->finalize
                    ? 'Data has been finalized successfully'
                    : 'Data has been saved successfully',

                'dcaid' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function milkinsertUpdate(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'file' => 'nullable|file|mimes:pdf|max:2048',
            'uploadid' => 'nullable|string',
            'amount' => 'required|array',
            'action' => 'required|in:insert,update',
            'finalize' => 'sometimes|boolean',
            'yearselected' => 'required|array',
            'yearselected.*' => 'integer',
            'remarks' => 'required|array',
            'remarks.*.type' => 'required|in:serious,non-serious',
            'remarks.*.text' => 'required|string',
        ]);

        $sessionUser = session('user');
        $sessionCharge = session('charge');
        if (! $sessionCharge || ! isset($sessionUser->userid)) {
            return response()->json(['success' => false, 'message' => 'Charge session not found.'], 400);
        }

        $userId = $sessionUser->userid;
        $basicinformation = session('basicinformation');
        $instid = $basicinformation->instid ?? null;

        if (! $instid) {
            return response()->json(['success' => false, 'message' => 'Institution ID not found.'], 400);
        }

        $tempplanid = null;
        if ($request->filled('tempplanid')) {
            try {
                $tempplanid = Crypt::decryptString($request->input('tempplanid'));
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Invalid tempplanid'], 400);
            }
        }

        if (! $tempplanid) {
            return response()->json(['success' => false, 'message' => 'Tempplanid missing from request'], 400);
        }

        $milkid = $request->input('action') === 'update'
            ? Crypt::decryptString($request->input('milkid'))
            : null;

        $data = [
            'tempplanid' => $tempplanid,
            'remarks' => $request->remarks,
            'statusflag' => $request->has('finalize') && $request->finalize ? 'F' : 'E',
            'audityearid' => json_encode($request->yearselected),
            'cer_type_code' => $request->cer_typecode,

        ];

        $uploadId = $request->input('uploadid');

        if ($request->hasFile('file')) {
            $today = now();
            $destinationarray = [
                $sessionCharge->deptcode,
                $sessionCharge->regioncode,
                $sessionCharge->distcode,
                $today->format('Y'),
                $today->format('m'),
                $today->format('d'),
                $tempplanid,
                View::shared('templateaudit'),
            ];

            $uploadResult = $this->fileUploadService->uploadFile(
                $request->file('file'),
                'uploads/templateaudit',
                $uploadId ?? '',
                $destinationarray
            );

            $fileuploadId = $uploadResult->getData()->fileupload_id ?? null;
            if (! $fileuploadId) {
                return response()->json(['success' => false, 'message' => 'File upload failed.'], 500);
            }

            $data['file_id'] = $fileuploadId;
        } elseif (empty($uploadId) && $request->input('action') === 'update') {
            $data['file_id'] = null;
        }

        try {
            $result = TemplateAudit::milkinsertorUpdate(
                $data,
                $request->amount,
                $request->remarks,
                $milkid,
                $userId,
                $request->action,
                $request->yearselected
            );

            return response()->json([
                'success' => true,
                'message' => $request->has('finalize') && $request->finalize
                    ? 'Data has been finalized successfully'
                    : 'Data has been saved successfully',
                'milkid' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }






//----------------------------- Fetching Departments for Manual Template Plan ----------------------------------



    public static function fetch_dept()
    {

     $sessionuser = session('user');
            $sessioncharge = session('charge');
            // if (!$sessioncharge || !isset($sessionuser->userid)) {
            //     return response()->json(['success' => false, 'message' => 'Charge session not found.'], 400);
            // }
    //  dd($sessioncharge);
            $deptcode = $sessioncharge->deptcode;

        $dept = TemplateAudit::deptfetch();
        // $quarter = collect(CommonModel::getplandetailsforreport($deptcode))
        //         ->filter(function ($item) {
        //             return in_array($item->statusflag ?? null, ['Y'], true);
        //         })
        //         ->values();
        // $quarter = collect(CommonModel::getplandetails($deptcode))->values();
        // dd($quarter);
        return view('templateaudit.manualtemplateplan', compact('dept'));
    }

    public function getRegionsByDept(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ]);

        $regions = TemplateAudit::getRegionsByDept($request->input('deptcode'));

        return response()->json([
            'success' => $regions->isNotEmpty(),
            'data' => $regions,
        ], $regions->isNotEmpty() ? 200 : 404);
    }

    public function getQuarterByDept(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
        ]);

        $quarterDetails = collect(CommonModel::getplandetails($request->input('deptcode')))->values();
        $currentQuarter = $quarterDetails->first(function ($detail) {
            return (($detail->statusflag ?? null) === 'Y');
        }) ?? $quarterDetails->first();

        $quarterLabel = $currentQuarter->planname ?? $currentQuarter->auditquarter ?? $currentQuarter->auditquartercode ?? '';
        $quarterCode = $currentQuarter->auditquartercode ?? null;
        $planmappingid = $currentQuarter->planmappingid ?? null;
        $currentQuarterToDate = TemplateAudit::getDeptCurrentQuarterToDate($request->input('deptcode'));

        $hasQuarterData = !empty($quarterLabel) || !empty($currentQuarterToDate);

        return response()->json([
            'success' => $hasQuarterData,
            'quarter' => $quarterLabel,
            'quartercode' => $quarterCode,
            'planmappingid' => $planmappingid,
            'currentquartertodate' => $currentQuarterToDate,
        ], $hasQuarterData ? 200 : 404);
    }

    public function getDistrictsByRegion(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
        ]);

        $districts = TemplateAudit::getDistrictsByRegion(
            $request->input('deptcode'),
            $request->input('regioncode')
        );

        return response()->json([
            'success' => $districts->isNotEmpty(),
            'data' => $districts,
        ], $districts->isNotEmpty() ? 200 : 404);
    }

    public function getCategoriesByLocation(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'districtcode' => ['nullable', 'string', 'regex:/^\d+$/'],
        ]);

        $categories = TemplateAudit::getCategoriesByLocation(
            $request->input('deptcode'),
            $request->input('regioncode'),
            $request->input('districtcode')
        );

        return response()->json([
            'success' => $categories->isNotEmpty(),
            'data' => $categories,
        ], $categories->isNotEmpty() ? 200 : 404);
    }

    public function getInstitutionsByFilters(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'category' => ['required', 'string', 'regex:/^(\d+|A)$/'],
            'regioncode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'districtcode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'subcatcode' => ['nullable', 'regex:/^(\d+|A)$/'],
        ]);

        $institutions = TemplateAudit::getInstitutionsByFilters(
            $request->input('deptcode'),
            $request->input('category'),
            $request->input('subcatcode'),
            $request->input('regioncode'),
            $request->input('districtcode')
        );

        return response()->json([
            'success' => $institutions->isNotEmpty(),
            'data' => $institutions,
        ], $institutions->isNotEmpty() ? 200 : 404);
    }

    public function getAuditorsByDeptAndDistrict(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['required', 'string', 'regex:/^\d+$/'],
            'auditdate' => ['nullable', 'date'],
        ]);

        $auditors = TemplateAudit::getAuditorsByDeptAndRegion(
            $request->input('deptcode'),
            $request->input('regioncode'),
            $request->input('auditdate')
        );

        return response()->json([
            'success' => $auditors->isNotEmpty(),
            'data' => $auditors,
        ], $auditors->isNotEmpty() ? 200 : 404);
    }

    public function saveTempTemplateAuditPlan(Request $request)
    {
        $validated = $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'regioncode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'districtcode' => ['nullable', 'string', 'regex:/^\d+$/'],
            'category' => ['required', 'string', 'regex:/^(\d+|A)$/'],
            'subcategory' => ['nullable', 'regex:/^(\d+|A)$/'],
            'instid' => ['required', 'array', 'min:1'],
            'instid.*' => ['required', 'string', 'regex:/^\d+$/'],
            'auditorid' => ['nullable', 'string', 'regex:/^\d+$/'],
            'audit_date' => [
                'required',
                'date',
                'after_or_equal:today',
                function ($attribute, $value, $fail) use ($request) {
                    $currentQuarterToDate = TemplateAudit::getDeptCurrentQuarterToDate($request->input('deptcode'));
                    if (empty($currentQuarterToDate)) {
                        $currentQuarterToDate = null;
                    }

                    try {
                        $selectedDate = \Illuminate\Support\Carbon::parse($value);
                    } catch (\Exception $e) {
                        $fail('The audit date is invalid.');
                        return;
                    }

                    if ($currentQuarterToDate) {
                        $maxDate = \Illuminate\Support\Carbon::parse($currentQuarterToDate)->toDateString();
                        if ($selectedDate->toDateString() > $maxDate) {
                            $fail('The audit date must be on or before the current quarter end date.');
                            return;
                        }
                    }

                    if (in_array($selectedDate->dayOfWeek, [0, 6], true)) {
                        $fail('The audit date cannot be a weekend.');
                        return;
                    }

                    $isHoliday = DB::table('audit.mst_holiday')
                        ->whereDate('holiday_date', $selectedDate->toDateString())
                        ->where('statusflag', 'Y')
                        ->exists();

                    if ($isHoliday) {
                        $fail('The audit date cannot be a holiday.');
                    }
                },
            ],
            'planmappingid' => ['nullable', 'string', 'max:50'],
            'verifiedflag' => ['nullable', 'in:Y,N'],
            'manualplanid' => ['nullable', 'string'],
            'finalize' => ['nullable', 'in:Y'],
        ]);

        $sessionUser = session('user');
        if (!$sessionUser || !isset($sessionUser->userid)) {
            return response()->json(['success' => false, 'message' => 'User session not found'], 400);
        }

        $recordId = null;
        if ($request->filled('manualplanid')) {
            $recordId = (int) $request->input('manualplanid');
        }

        $now = now();
        $selectedAuditDate = \Illuminate\Support\Carbon::parse($validated['audit_date'])->toDateString();
        $normalizedInstIds = collect($request->input('instid', []))
            ->map(fn ($instid) => (string) ((int) $instid))
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->all();
        $statusflag = $request->input('finalize') === 'Y' ? 'F' : 'Y';
        $verifiedflag = $statusflag === 'F' ? $request->input('verifiedflag', 'N') : 'N';
        $planmappingid = $request->input('planmappingid');
        $planMappingDetail = null;
        $auditorId = $request->input('auditorid') ?: null;
        $maxInstitutionCount = TemplateAudit::getMaxInstitutionCountForCategory(
            $request->input('deptcode'),
            $request->input('category')
        );

        if ($auditorId) {
            $existingAssignedInstitutionCount = DB::table(DB::raw("audit.temp_templateauditplan as t, jsonb_array_elements_text(COALESCE(t.instid, '[]'::jsonb)) as assigned_inst(instid)"))
                ->selectRaw("COUNT(*) as total_count")
                ->where('t.userid', $auditorId)
                ->whereDate('t.auditdate', $selectedAuditDate)
                ->where(function ($query) {
                    $query->whereNull('t.statusflag')
                        ->orWhere('t.statusflag', '!=', 'D');
                })
                ->when($recordId, function ($query) use ($recordId) {
                    $query->where('t.temp_templateauditplanid', '!=', $recordId);
                })
                ->value('total_count') ?? 0;

            $requestedInstitutionCount = count($normalizedInstIds);

            if ($existingAssignedInstitutionCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'This auditor is already allotted for the selected audit date. Please choose another auditor or another date.',
                ], 422);
            }

            // if ($maxInstitutionCount > 0 && ($existingAssignedInstitutionCount + $requestedInstitutionCount) > $maxInstitutionCount) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'manual_template_auditor_limit_reached',
            //     ], 422);
            // }
        }

        $duplicateTempPlan = DB::table('audit.temp_templateauditplan as t')
            ->select('t.temp_templateauditplanid')
            ->where('t.deptcode', $request->input('deptcode'))
            ->where(function ($query) use ($request) {
                $regioncode = $request->input('regioncode');
                if ($regioncode === null || $regioncode === '') {
                    $query->whereNull('t.regioncode');
                } else {
                    $query->where('t.regioncode', $regioncode);
                }
            })
            ->where(function ($query) use ($request) {
                $districtcode = $request->input('districtcode');
                if ($districtcode === null || $districtcode === '') {
                    $query->whereNull('t.distcode');
                } else {
                    $query->where('t.distcode', $districtcode);
                }
            })
            ->where('t.catcode', $request->input('category'))
            ->where(function ($query) use ($request) {
                $subcategory = $request->input('subcategory');
                if ($subcategory === null || $subcategory === '') {
                    $query->whereNull('t.subcatid');
                } else {
                    $query->where('t.subcatid', $subcategory);
                }
            })
            ->where(function ($query) use ($planmappingid) {
                if ($planmappingid === null || $planmappingid === '') {
                    $query->whereNull('t.planmappingid');
                } else {
                    $query->where('t.planmappingid', $planmappingid);
                }
            })
            ->whereExists(function ($query) use ($normalizedInstIds) {
                $query->select(DB::raw(1))
                    ->from(DB::raw("jsonb_array_elements_text(COALESCE(t.instid, '[]'::jsonb)) as existing_inst(instid)"))
                    ->whereIn('existing_inst.instid', $normalizedInstIds);
            })
            ->when($recordId, function ($query) use ($recordId) {
                $query->where('t.temp_templateauditplanid', '!=', $recordId);
            })
            ->first();

        if ($duplicateTempPlan) {
            return response()->json([
                'success' => false,
                'message' => 'manual_template_institution_allocated',
            ], 422);
        }

        if ($statusflag === 'F') {
            $effectiveAuditDate = $selectedAuditDate;

            if ($recordId) {
                $existingDraft = DB::table('audit.temp_templateauditplan')
                    ->select('auditdate')
                    ->where('temp_templateauditplanid', $recordId)
                    ->first();

                if ($existingDraft && !empty($existingDraft->auditdate)) {
                    $effectiveAuditDate = \Illuminate\Support\Carbon::parse($existingDraft->auditdate)->toDateString();
                }
            }

            // $finalizeDeadline = \Illuminate\Support\Carbon::parse($effectiveAuditDate)->setTime(12, 0, 0);
            // if ($now->greaterThan($finalizeDeadline)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'manual_template_finalize_time_over',
            //     ], 422);
            // }
        }

        if ($statusflag === 'F' && !empty($planmappingid)) {
            $planMappingDetail = DB::table('audit.auditplanmapping')
                ->select('planmappingid', 'auditquartercode', 'prioritycode')
                ->where('planmappingid', $planmappingid)
                ->first();
        }

        $data = [
            'deptcode' => $request->input('deptcode'),
            'regioncode' => $request->input('regioncode'),
            'distcode' => $request->input('districtcode'),
            'catcode' => $request->input('category'),
            'subcatid' => $request->input('subcategory') ?: null,
            'instid' => json_encode($normalizedInstIds),
            'userid' => $request->input('auditorid') ?: null,
            'planmappingid' => $planmappingid ?: null,
            'verifiedflag' => $verifiedflag,
            'auditdate' => $selectedAuditDate,
            'statusflag' => $statusflag,
            'updatedon' => $now,
            'updatedby' => $sessionUser->userid,
        ];

        if (!$recordId) {
            $data['createdon'] = $now;
            $data['createdby'] = $sessionUser->userid;
        }

        $savedId = DB::transaction(function () use ($data, $recordId, $statusflag, $planMappingDetail, $request, $sessionUser, $now, $normalizedInstIds, $selectedAuditDate) {
            $savedId = TemplateAudit::insertOrUpdateTempTemplateAuditPlan($data, $recordId);

            if ($statusflag === 'F' && $planMappingDetail) {
                $finalizedRows = collect($normalizedInstIds)
                    ->map(function ($instid) use ($request, $sessionUser, $now, $planMappingDetail, $selectedAuditDate) {
                        return [
                            'deptuserid' => $request->input('auditorid') ?: null,
                            'instid' => (int) $instid,
                            'auditquartercode' => $planMappingDetail->auditquartercode,
                            'statusflag' => 'F',
                            'createdby' => $sessionUser->userid,
                            'createdon' => $now,
                            'updatedby' => $sessionUser->userid,
                            'updatedon' => $now,
                            'mandays' => 1,
                            'fromdate' => $selectedAuditDate,
                            'todate' => $selectedAuditDate,
                            'startdate' => null,
                            'enddate' => null,
                            'prioritycode' => $planMappingDetail->prioritycode ?? null,
                            'planmappingid' => $planMappingDetail->planmappingid ?? null,
                        ];
                    })
                    ->values()
                    ->all();

                TemplateAudit::insertFinalizedTemplateAuditPlans($finalizedRows);
            }

            return $savedId;
        });

        return response()->json([
            'success' => true,
            'message' => $statusflag === 'F' ? 'Manual plan finalized successfully' : 'Manual plan saved successfully',
            'manualplanid' => $savedId,
            'statusflag' => $statusflag,
        ]);
    }

    public function fetchTempTemplateAuditPlans(Request $request)
    {
        $recordId = $request->filled('manualplanid') ? (int) $request->input('manualplanid') : null;
        $rows = TemplateAudit::fetchTempTemplateAuditPlans($recordId);

        foreach ($rows as $row) {
            $row->encrypted_manualplanid = Crypt::encryptString((string) $row->temp_templateauditplanid);
        }

        return response()->json([
            'success' => !$rows->isEmpty(),
            'data' => $rows,
        ], $rows->isEmpty() ? 404 : 200);
    }

    public function fetchTempTemplateAuditPlanDetail(Request $request)
    {
        try {
            $recordId = (int) Crypt::decryptString((string) $request->input('manualplanid'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid record id',
            ], 400);
        }

        $row = TemplateAudit::fetchTempTemplateAuditPlanDetail($recordId);

        if (! $row) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found',
            ], 404);
        }

        $regions = TemplateAudit::getRegionsByDept($row->deptcode);
        $districts = $row->regioncode ? TemplateAudit::getDistrictsByRegion($row->deptcode, $row->regioncode) : collect();
        $categories = TemplateAudit::getCategoriesByLocation($row->deptcode, $row->regioncode, $row->distcode);
        $subcategories = ($row->catcode && ($row->if_subcategory ?? '') === 'Y')
            ? TemplateAudit::getSubcategoryByCategory($row->catcode)
            : collect();
        $institutions = TemplateAudit::getInstitutionsByFilters(
            $row->deptcode,
            $row->catcode,
            $row->subcatid,
            $row->regioncode,
            $row->distcode
        );
        $auditors = $row->regioncode
            ? TemplateAudit::getAuditorsByDeptAndRegion($row->deptcode, $row->regioncode, $row->auditdate)
            : collect();
        $currentQuarterToDate = TemplateAudit::getDeptCurrentQuarterToDate($row->deptcode);

        return response()->json([
            'success' => true,
            'data' => $row,
            'options' => [
                'regions' => $regions->values(),
                'districts' => $districts->values(),
                'categories' => $categories->values(),
                'subcategories' => $subcategories->values(),
                'institutions' => $institutions->values(),
                'auditors' => $auditors->values(),
                'currentquartertodate' => $currentQuarterToDate,
            ],
        ]);
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


        $category = TemplateAudit::getcategoryByDept($deptcode);
        if($category->isNotEmpty()) {
            return response()->json($category);
        } else {
            return response()->json(['success' => false, 'message' => 'No regions found'], 404);
        }
    }

    public function getCategoryInstitutionLimit(Request $request)
    {
        $request->validate([
            'deptcode' => ['required', 'string', 'regex:/^\d+$/'],
            'catcode' => ['required', 'string', 'regex:/^(\d+|A)$/'],
        ]);

        $limit = TemplateAudit::getMaxInstitutionCountForCategory(
            $request->input('deptcode'),
            $request->input('catcode')
        );

        return response()->json([
            'success' => true,
            'noofcolumn' => $limit,
        ]);
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

        $subcategoryData = TemplateAudit::getSubcategoryByCategory($catcode);

        if($subcategoryData->isNotEmpty()) {
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

    //------------------------------------------Manual Template Audit End -----------------------------------------------------------
}
