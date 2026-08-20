<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class TemplateAudit extends BaseModel
{
    protected static $templateAuditTypeTable = BaseModel::TEMPLATE_AUDIT_TYPE_TABLE;

    protected static $templateAuditHeaderTable = BaseModel::TEMPLATE_AUDIT_HEADER_TABLE;

    protected static $templateAuditMainTypeTable = BaseModel::TEMPLATE_AUDIT_MAIN_TYPE_TABLE;

    protected static $templateAuditSubTypeTable = BaseModel::TEMPLATE_AUDIT_SUB_TYPE_TABLE;

    protected static $templateAuditPlanTable = BaseModel::TEMPLATE_AUDIT_PLAN_TABLE;

    protected static $templateFormMappingTable = BaseModel::TMPAUDIT_FORM_MAPPING_TABLE;

    protected static $hriaZoneTable = BaseModel::MST_HRIAZONE_TABLE;

    protected static $hriaCircleTable = BaseModel::MST_HRIACIRCLE_TABLE;

    protected static $HriaOkpDetailsTable = BaseModel::HRIA_DETAILS_TABLE;

    protected static $deptUserDetailsTable = BaseModel::USERDETAIL_TABLE;

    protected static $mstDeptTable = BaseModel::DEPT_TABLE;

    protected static $mstInstitutionTable = BaseModel::INSTITUTION_TABLE;

    protected static $TempHriaOkpTable = BaseModel::TEMP_HRIA_TABLE;

    protected static $TempHriaRemarksTable = BaseModel::TEMP_HRIA_REMARKS_TABLE;

    protected static $LFADetailsTable = BaseModel::LFA_DETAILS_TABLE;

    protected static $DCADetailsTable = BaseModel::DCA_DETAILS_TABLE;

    protected static $MILKDetailsTable = BaseModel::MILK_DETAILS_TABLE;

    protected static $TempLFATable = BaseModel::TEMP_LFA_TABLE;

    protected static $TempDCATable = BaseModel::TEMP_DCA_TABLE;

    protected static $TempMILKTable = BaseModel::TEMP_MILK_TABLE;

    protected static $TempLFARemarksTable = BaseModel::TEMP_LFA_REMARKS_TABLE;

    protected static $TempDCARemarksTable = BaseModel::TEMP_DCA_REMARKS_TABLE;

    protected static $TempMILKRemarksTable = BaseModel::TEMP_MILK_REMARKS_TABLE;

    protected static $DesignationTable = BaseModel::DESIGNATION_TABLE;

    protected static $CategoryTable = BaseModel::MSTAUDITEEINSCATEGORY_TABLE;

    protected static $SubCategoryTable = BaseModel::SUBCATEGORY_TABLE;

    protected static $AuditQuarterTable = BaseModel::AUDITQUARTER_TABLE;

    protected static $template_audit_mapping = BaseModel::TEMP_AUDIT_MAPPING;

    protected $table = 'audit.mst_depttemplatetype';

    protected $primaryKey = 'tmpaudittypeid';

public static function getTemplates($deptcode, $formcode = null, $instid = null, $tempplanid = null, $catcode = null,$subcatcode = null, $auditquartercode = null, $prioritycode = null)
    {

        $userId = session('user')->userid ?? null;
        // dd($deptcode, $formcode, $instid, $tempplanid, $catcode);
       $query = DB::table(self::$templateAuditPlanTable.' as ap')
            ->select(
                DB::raw('DISTINCT ON (mi.instid) ap.templateauditplanid as tempplanid'),
                'ap.instid',
                'ap.deptuserid',
                'ap.statusflag',
                'ap.createdby',
                'ap.createdon',
                'ap.auditquartercode',
                'mi.instid',
                'mi.instename',
                'mi.insttname',
                'mi.erpno',
                'dud.username',
                'dud.usertamilname',
                't.*',
                'tfm.tmpaudittypeid',
                'tfm.deptcode',
                'tfm.catcode',
                'tfm.subcatcode',
                'mhz.zonecode',
                'mhz.zoneename',
                'mhz.zonetname',
                'mhc.*'
            )
            ->join(self::$mstInstitutionTable.' as mi', 'mi.instid', '=', 'ap.instid')
            ->join(self::$deptUserDetailsTable.' as dud', 'dud.deptuserid', '=', 'ap.deptuserid')
            ->join(self::$mstDeptTable.' as msd', 'msd.deptcode', '=', 'mi.deptcode')
            ->join(self::$templateAuditTypeTable.' as tfm', function ($join) use ($deptcode) {
                $join->on('tfm.deptcode', '=', 'mi.deptcode')
                    ->where('tfm.deptcode', $deptcode)
                    ->where('tfm.statusflag', 'Y');
            })
            ->join(self::$templateAuditTypeTable.' as t', 't.tmpaudittypeid', '=', 'tfm.tmpaudittypeid')
            ->leftjoin('audit.mst_nonaudit_hub as non_hub', function ($join) {
                $join->on('non_hub.deptcode', '=', 'mi.deptcode')
                    ->on('non_hub.desigcode', '=', 'mi.hubdesigcode');
            })
            ->leftJoin(self::$hriaZoneTable.' as mhz', function ($join) {
                $join->on('mhz.zonecode', '=', 'non_hub.zonecode');
            })
            ->leftJoin(self::$hriaCircleTable.' as mhc', 'mhc.circleid', '=', 'non_hub.circleid')
            ->where('ap.statusflag', 'F')
            ->where('mi.statusflag', 'Y')
            ->where('dud.statusflag', 'Y');

        if ($formcode) {
            $query->where('t.formcode', $formcode);
        }

        if ($instid) {
            $query->where('mi.instid', $instid);
        }

        if ($tempplanid) {
            $query->where('ap.templateauditplanid', $tempplanid);
        }

        if ($catcode) {
            $query->where('tfm.catcode', $catcode);
        }
 if ($subcatcode) {
                $query->where('tfm.subcatcode', $subcatcode);
            }

        if ($auditquartercode) {
            $query->where('ap.auditquartercode', $auditquartercode);
        }

        if ($userId) {
            $query->where('ap.deptuserid', $userId);
        }   
        if ($prioritycode) {
            $query->where('ap.prioritycode', $prioritycode);
        } 

	return $query->get();
    }


    
     public static function getTemplateStructure($templateId, $instid , $auditquartercode, $prioritycode)
    {
        $userid = session('user')->userid ?? null;
    $template = DB::table(self::$templateAuditTypeTable.' as tfm')
            ->join(self::$mstInstitutionTable.' as mi', function ($join) {
                $join->on('tfm.deptcode', '=', 'mi.deptcode')
                    ->whereRaw('(tfm.catcode = mi.catcode OR tfm.catcode IS NULL)')
                    ->whereRaw('(tfm.subcatcode = mi.subcatid OR tfm.subcatcode IS NULL)');
            })
            ->join(self::$templateAuditPlanTable.' as tap', 'tap.instid', '=', 'mi.instid')
            ->where('tfm.tmpaudittypeid', $templateId)
            ->where('tap.instid', $instid)
            ->where('tfm.statusflag', 'Y')
	    ->where('tap.auditquartercode', $auditquartercode)
        ->where('tap.prioritycode', $prioritycode)
            ->where('tap.statusflag', 'F')
            ->where('tap.deptuserid', $userid)
	    ->select(
                'tfm.*',
                'mi.*',
                'tap.templateauditplanid as tempplanid',
                'tap.*',
            )
            ->first();

        // dd($template);
        if (! $template) {
            return null;
        }

        $headers = DB::table(self::$templateAuditHeaderTable)
            ->where('tmpaudittypeid', $templateId)
            ->where('statusflag', 'Y')
            ->orderBy('columnno')
            ->get();


        $mainTypes = DB::table(self::$template_audit_mapping. ' as tmpmap')
            ->where('tmpmap.tmpaudittypeid', $templateId)
            ->join(self::$templateAuditMainTypeTable.' as m', 'tmpmap.maintypeid', '=', 'm.maintypeid')
            ->leftJoin(self::$templateAuditSubTypeTable.' as s', function ($join) {
                $join->on('tmpmap.subtypeid', '=', 's.subtypeid')
                    ->where('s.statusflag', 'Y');
            })
            ->select(
                'm.*',
                's.subtypeid',
                's.stypeename',
                's.stypetname',
                's.orderno',
                's.temptypeid as subtemptypeid',
                'tmpmap.totalflag',
                'tmpmap.headerid'
            )
            ->where('m.statusflag', 'Y')
            ->where('tmpmap.statusflag','Y')
            ->orderBy('tmpmap.headerid')
            ->orderBy('tmpmap.tmp_mappingid')
            ->orderBy('s.orderno')
            ->get();

        $groupedHeaders = [];

        foreach ($headers as $header) {
            $groupedHeaders[$header->tmpauditheaderid] = [
                'tmpauditheaderid' => $header->tmpauditheaderid,
                'lblename' => $header->lblename,
                'lbltname' => $header->lbltname,
                'inputtype' => $header->inputtype,
                'subtableflag' => $header->subtableflag ?? null,
                'maintypes' => [],
            ];
        }

        foreach ($mainTypes as $item) {
            $headerId = $item->headerid;
            $mainTypeId = $item->maintypeid;

            if (isset($groupedHeaders[$headerId])) {
                if (! isset($groupedHeaders[$headerId]['maintypes'][$mainTypeId])) {
                    $groupedHeaders[$headerId]['maintypes'][$mainTypeId] = [
                        'maintypeid' => $item->maintypeid,
                        'maintypeename' => $item->maintypeename,
                        'maintypetname' => $item->maintypetname,
                        'maintypetotalflag' => $item->totalflag,
                        'temptypeid' => $item->temptypeid,
                        'subtypes' => [],
                    ];
                }

                if ($item->subtypeid) {
                    $groupedHeaders[$headerId]['maintypes'][$mainTypeId]['subtypes'][] = [
                        'subtypeid' => $item->subtypeid,
                        'stypeename' => $item->stypeename,
                        'stypetname' => $item->stypetname,
                        'orderno' => $item->orderno,
                        'subtemptypeid' => $item->subtemptypeid,
                    ];
                }
            }
        }

        $orderedHeaders = [];
        foreach ($headers as $header) {
            $headerId = $header->tmpauditheaderid;
            if (isset($groupedHeaders[$headerId])) {
                $groupedHeaders[$headerId]['maintypes'] = array_values($groupedHeaders[$headerId]['maintypes']);
                $orderedHeaders[] = $groupedHeaders[$headerId];
            }
        }

        $template->headers = $orderedHeaders;

        // dd($template);
        return $template;
    }

    public static function insertorUpdate(
        array $detailsData,
        array $amounts,
        array $remarks,
        $okpid,
        $userId,
        $action = 'insert',
        array $yearselected = []
    ) {
        DB::beginTransaction();

        try {
            $detailsStatus = $detailsData['statusflag'] ?? 'E';
            $amountsStatus = $detailsStatus === 'F' ? 'F' : 'E';
            $auditEndDate = $detailsStatus === 'F' ? now() : null;
            $detailsData['audit_end_date'] = now()->format('Y-m-d');
            $detailsData['cer_type_code'] = $detailsData['cer_type_code'] ?? null;

            $tempplanid = $detailsData['tempplanid'] ?? null;

            $startdate = now(); // Same as audit_start_date
            $enddate = $auditEndDate; // Same as audit_end_date

            $normalizedAmounts = [];

            foreach ($amounts as $firstLevelKey => $firstLevelData) {
                if (str_starts_with($firstLevelKey, 'pair') && is_array($firstLevelData)) {
                    foreach ($firstLevelData as $headerId => $headerData) {
                        if (is_array($headerData)) {
                            foreach ($headerData as $maintypeid => $value) {
                                if ($value !== null && $value !== '') {
                                    $normalizedAmounts[$headerId][$maintypeid] = $value;
                                }
                            }
                        }
                    }
                } elseif (in_array($firstLevelKey, ['left', 'right']) && is_array($firstLevelData)) {
                    foreach ($firstLevelData as $pairKey => $pairData) {
                        if (is_array($pairData)) {
                            $headerId = self::getHeaderIdForHria02($firstLevelKey, $pairKey);

                            foreach ($pairData as $maintypeid => $value) {
                                if ($value !== null && $value !== '') {
                                    $normalizedAmounts[$headerId][$maintypeid] = $value;
                                }
                            }
                        }
                    }
                } elseif (is_array($firstLevelData)) {
                    $headerId = self::getHeaderIdForSubTable($firstLevelKey);

                    foreach ($firstLevelData as $maintypeid => $value) {
                        if ($value !== null && $value !== '') {
                            $normalizedAmounts[$headerId][$maintypeid] = $value;
                        }
                    }
                }
            }

            $amounts = $normalizedAmounts;

            $remarkIds = [];

            if ($action === 'insert') {
                $detailsData['created_by'] = $userId;
                $detailsData['created_on'] = now();

                $okpid = DB::table(self::$HriaOkpDetailsTable)->insertGetId($detailsData, 'okpsdetails_id');

                if ($tempplanid) {
                    $updateData = [
                        'startdate' => $startdate,
                        'enddate' => $enddate,
                    ];

                    DB::table(self::$templateAuditPlanTable)
                        ->where('templateauditplanid', $tempplanid)
                        ->update($updateData);
                }

                foreach ($amounts as $headerId => $headerData) {
                    foreach ($headerData as $maintypeid => $value) {
                        if (empty($maintypeid) || $value === null || $value === '') {
                            continue;
                        }

                        $maintype = DB::table(self::$templateAuditMainTypeTable)
                            ->where('maintypeid', $maintypeid)
                            ->first();

                        $tmpMapping = DB::table(self::$template_audit_mapping)
                            ->where('headerid', $headerId)
                            ->where('maintypeid', $maintypeid)
                            ->first();

                        $tmpMappingId = $tmpMapping->tmp_mappingid ?? null;
                        // dd($tmpMappingId);

                        if (! $maintype) {
                            continue;
                        }

                        $temptypeid = $maintype->temptypeid;
                        $insertData = [
                            'okpid' => $okpid,
                            'maintypeid' => $maintypeid,
                            'headerid' => $headerId,
                            'temptypeid' => $temptypeid,
                            'tempplanid' => $tempplanid,
                            'audit_start_date' => now(),
                            'audit_end_date' => $auditEndDate,
                            'statusflag' => $amountsStatus,
                            'created_by' => $userId,
                            'created_on' => now(),
                            'tmp_mappingid' => $tmpMappingId,
                        ];

                        switch ($temptypeid) {
                            case 1: // Numeric
                                $insertData['value_numeric'] = $value;
                                break;
                            case 2: // Character
                                $insertData['value_char'] = $value;
                                break;
                            case 3: // Date
                                $insertData['value_date'] = $value;
                                break;
                            case 4: // Radio
                                $insertData['value_radio'] = $value;
                                break;
                        }

                        DB::table(self::$TempHriaOkpTable)->insert($insertData);
                    }
                }

                $remarkIds = self::insertRemarks($okpid, $remarks, $userId, $amountsStatus, $auditEndDate);

                DB::table(self::$HriaOkpDetailsTable)
                    ->where('okpsdetails_id', $okpid)
                    ->update([
                        'audityearid' => json_encode($yearselected),
                        'remark_ids' => json_encode($remarkIds),
                        'statusflag' => $amountsStatus,
                        'updated_by' => $userId,
                        'updated_on' => now(),
                    ]);
            } elseif ($action === 'update' && $okpid) {
                $detailsData['updated_by'] = $userId;
                $detailsData['updated_on'] = now();

                DB::table(self::$HriaOkpDetailsTable)
                    ->where('okpsdetails_id', $okpid)
                    ->update($detailsData);

                if ($tempplanid) {
                    $updateData = [
                        'enddate' => $enddate,
                    ];

                    DB::table(self::$templateAuditPlanTable)
                        ->where('templateauditplanid', $tempplanid)
                        ->update($updateData);
                }

                if ($detailsStatus === 'F') {
                    DB::table(self::$TempHriaOkpTable)
                        ->where('okpid', $okpid)
                        ->where('statusflag', '!=', 'F')
                        ->update([
                            'statusflag' => 'F',
                            'audit_end_date' => $auditEndDate,
                            'updated_by' => $userId,
                            'updated_on' => now(),
                        ]);
                }

                foreach ($amounts as $headerId => $headerData) {
                    foreach ($headerData as $maintypeid => $value) {
                        if (empty($maintypeid) || $value === null || $value === '') {
                            continue;
                        }

                        $maintype = DB::table(self::$templateAuditMainTypeTable)
                            ->where('maintypeid', $maintypeid)
                            ->first();

                        if (! $maintype) {
                            continue;
                        }

                        $tmpMapping = DB::table(self::$template_audit_mapping)
                            ->where('headerid', $headerId)
                            ->where('maintypeid', $maintypeid)
                            ->first();

                        $tmpMappingId = $tmpMapping->tmp_mappingid ?? null;

                        $temptypeid = $maintype->temptypeid;
                        $updateData = [
                            'tempplanid' => $tempplanid,
                            'updated_by' => $userId,
                            'updated_on' => now(),
                            'tmp_mappingid' => $tmpMappingId,
                        ];

                        if ($detailsStatus === 'F') {
                            $updateData['statusflag'] = 'F';
                            $updateData['audit_end_date'] = $auditEndDate;
                        }

                        switch ($temptypeid) {
                            case 1: // Numeric
                                $updateData['value_numeric'] = $value;
                                break;
                            case 2: // Character
                                $updateData['value_char'] = $value;
                                break;
                            case 3: // Date
                                $updateData['value_date'] = $value;
                                break;
                            case 4: // Radio
                                $updateData['value_radio'] = $value;
                                break;
                        }

                        $exists = DB::table(self::$TempHriaOkpTable)
                            ->where('okpid', $okpid)
                            ->where('maintypeid', $maintypeid)
                            ->where('headerid', $headerId)
                            ->exists();

                        if ($exists) {
                            DB::table(self::$TempHriaOkpTable)
                                ->where('okpid', $okpid)
                                ->where('maintypeid', $maintypeid)
                                ->where('headerid', $headerId)
                                ->update($updateData);
                        } else {
                            $insertData = array_merge([
                                'okpid' => $okpid,
                                'maintypeid' => $maintypeid,
                                'headerid' => $headerId,
                                'temptypeid' => $temptypeid,
                                'audit_start_date' => now(),
                                'statusflag' => $amountsStatus,
                                'audit_end_date' => $auditEndDate,
                                'created_by' => $userId,
                                'created_on' => now(),
                            ], $updateData);

                            DB::table(self::$TempHriaOkpTable)->insert($insertData);
                        }
                    }
                }

                $remarkIds = self::updateRemarks($okpid, $remarks, $userId, $amountsStatus, $auditEndDate);

                DB::table(self::$HriaOkpDetailsTable)
                    ->where('okpsdetails_id', $okpid)
                    ->update([
                        'audityearid' => json_encode($yearselected),
                        'remark_ids' => json_encode($remarkIds),
                        'updated_by' => $userId,
                        'updated_on' => now(),
                    ]);
            }

            DB::commit();

            return $okpid;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    private static function getHeaderIdForHria02($side, $pairKey)
    {
        $mapping = DB::table('audit.mst_templateheader as h')
            ->join('audit.mst_depttemplatetype as t', 'h.tmpaudittypeid', '=', 't.tmpaudittypeid')
            ->where('t.formcode', 'HRIA02')
            ->where('h.statusflag', 'Y')
            ->select('h.tmpauditheaderid', 'h.tmpaudittypeid', 'h.columnno', 'h.subtableflag')
            ->get()
            ->groupBy('tmpaudittypeid')
            ->toArray();

        if ($side === 'left') {
            if ($pairKey === 'pair0') {
                $header = collect($mapping[2] ?? [])->firstWhere('columnno', 1);

                return $header->tmpauditheaderid ?? 0;
            } elseif ($pairKey === 'pair1') {
                $header = collect($mapping[3] ?? [])->firstWhere('columnno', 1);

                return $header->tmpauditheaderid ?? 0;
            }
        } elseif ($side === 'right') {
            if ($pairKey === 'pair0') {
                $header = collect($mapping[2] ?? [])->firstWhere('columnno', 3);

                return $header->tmpauditheaderid ?? 0;
            } elseif ($pairKey === 'pair1') {
                $header = collect($mapping[3] ?? [])->firstWhere('columnno', 2);

                return $header->tmpauditheaderid ?? 0;
            }
        }

        return 0;
    }

    private static function getHeaderIdForSubTable($subTableFlag)
    {
        $header = DB::table('audit.mst_templateheader')
            ->where('subtableflag', $subTableFlag)
            ->where('statusflag', 'Y')
            ->where('columnno', 1) // Get the first column (usually the label column)
            ->first();

        return $header->tmpauditheaderid ?? 0;
    }


    private static function insertRemarks($okpid, $remarks, $userId, $statusflag, $auditEndDate)
    {
        $remarkIds = [];

        foreach ($remarks as $index => $remark) {
            if (! empty($remark['type']) && ! empty(trim($remark['text'] ?? ''))) {
                $remarkId = DB::table(self::$TempHriaRemarksTable)->insertGetId([
                    'okpid' => $okpid,
                    'type' => $remark['type'],
                    'remark_text' => $remark['text'],
                    'statusflag' => $statusflag,
                    'audit_start_date' => now(),
                    'audit_end_date' => $auditEndDate,
                    'created_by' => $userId,
                    'created_on' => now(),
                ], 'remark_id');

                $remarkIds[] = $remarkId;
            }
        }

        return $remarkIds;
    }

    private static function updateRemarks($okpid, $remarks, $userId, $statusflag, $auditEndDate)
    {
        $remarkIds = [];

        $existingRemarks = DB::table(self::$TempHriaRemarksTable)
            ->where('okpid', $okpid)
            ->where('statusflag', 'E') // Only consider active remarks
            ->orderBy('remark_id')
            ->get();

        foreach ($remarks as $index => $remark) {
            if (! empty($remark['type']) && ! empty($remark['text'])) {
                if (isset($existingRemarks[$index])) {
                    $remarkId = $existingRemarks[$index]->remark_id;

                    DB::table(self::$TempHriaRemarksTable)
                        ->where('remark_id', $remarkId)
                        ->update([
                            'type' => $remark['type'],
                            'remark_text' => $remark['text'],
                            'statusflag' => $statusflag,
                            'audit_end_date' => $auditEndDate,
                            'updated_by' => $userId,
                            'updated_on' => now(),
                        ]);

                    $remarkIds[] = $remarkId;
                } else {
                    $remarkId = DB::table(self::$TempHriaRemarksTable)->insertGetId([
                        'okpid' => $okpid,
                        'type' => $remark['type'],
                        'remark_text' => $remark['text'],
                        'statusflag' => $statusflag, // 'E' or 'F'
                        'audit_start_date' => now(),
                        'audit_end_date' => $auditEndDate,
                        'created_by' => $userId,
                        'created_on' => now(),
                    ], 'remark_id');

                    $remarkIds[] = $remarkId;
                }
            }
        }

        if (count($existingRemarks) > count($remarks)) {
            $remainingIds = array_slice($existingRemarks->pluck('remark_id')->toArray(), count($remarks));

            // APPROACH 1: Soft delete (recommended - keeps history)
            DB::table(self::$TempHriaRemarksTable)
                ->whereIn('remark_id', $remainingIds)
                ->update([
                    'statusflag' => 'D', // 'D' for Deleted
                    'audit_end_date' => now(), // Set end date to now
                    'updated_by' => $userId,
                    'updated_on' => now(),
                ]);

            // APPROACH 2: Hard delete (completely remove from database)
            // DB::table(self::$TempHriaRemarksTable)
            //     ->whereIn('remark_id', $remainingIds)
            //     ->delete();

            // If using soft delete, you might not want to include deleted IDs in the returned array
            // $remarkIds = array_merge($remarkIds, $remainingIds);
            // Uncomment if you want to keep track of deleted IDs
        }

        return $remarkIds;
    }

    public static function lfainsertorUpdate(
        array $detailsData,
        array $amounts,
        array $remarks,
        $lfaid,
        $userId,
        $action = 'insert',
        array $yearselected = []
    ) {
        DB::beginTransaction();

        try {
            $detailsStatus = $detailsData['statusflag'] ?? 'E';
            $amountsStatus = $detailsStatus === 'F' ? 'F' : 'E';
            $auditEndDate = $detailsStatus === 'F' ? now() : null;
            $tempplanid = $detailsData['tempplanid'] ?? null;
            $detailsData['audit_end_date'] = now()->format('Y-m-d');
            $detailsData['cer_type_code'] = $detailsData['cer_type_code'] ?? null;

            $startdate = now(); // Same as audit_start_date
            $enddate = $auditEndDate; // Same as audit_end_date

            if ($action === 'insert') {
                $detailsData['created_by'] = $userId;
                $detailsData['created_on'] = now();

                if (isset($detailsData['remarks'])) {
                    unset($detailsData['remarks']);
                }
                // dd($detailsData);
                if (! empty($detailsData['audityearid'])) {
                    if (is_array($detailsData['audityearid'])) {
                        $detailsData['audityearid'] = json_encode($detailsData['audityearid']);
                    } else {
                        $decoded = json_decode($detailsData['audityearid'], true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $detailsData['audityearid'] = json_encode($decoded);
                        }
                    }
                }

                $lfaid = DB::table(self::$LFADetailsTable)
                    ->insertGetId($detailsData, 'lfa_details_id');

                if ($tempplanid) {
                    $updateData = [
                        'startdate' => $startdate,
                        'enddate' => $enddate,
                    ];

                    DB::table(self::$templateAuditPlanTable)
                        ->where('templateauditplanid', $tempplanid)
                        ->update($updateData);
                }

                foreach ($amounts as $maintypeId => $headers) {
                    if (is_numeric($maintypeId)) {
                        $maintype = DB::table(self::$templateAuditMainTypeTable)
                            ->where('maintypeid', intval($maintypeId))
                            ->first();

                        if (! $maintype) {
                            continue;
                        }
                    } else {
                        continue;
                    }

                    $temptypeid = $maintype->temptypeid ?? 1;



                    foreach ($headers as $headerId => $value) {

                        if (empty($value) || $value === null || $value === '') {
                            continue;
                        }

                    $tmpMapping = DB::table(self::$template_audit_mapping)
                            ->where('headerid', $headerId)
                            ->where('maintypeid', $maintypeId)
                            // ->where('subtypeid', $subtypeId)
                            ->first();

                    $tmpMappingId = $tmpMapping->tmp_mappingid ?? null;

                        $insertData = [
                            'lfaid' => $lfaid,
                            'tempplanid' => $tempplanid,
                            'tmpauditheaderid' => $headerId,
                            'maintypeid' => $maintypeId,
                            'subtypeid' => null,
                            'temptypeid' => $temptypeid,
                            'audit_start_date' => now(),
                            'audit_end_date' => $auditEndDate,
                            'statusflag' => $amountsStatus,
                            'created_by' => $userId,
                            'created_on' => now(),
                            'tmp_mappingid' => $tmpMappingId,
                        ];

                        switch ($temptypeid) {
                            case 1: // Numeric
                                $insertData['value_numeric'] = $value;
                                break;
                            case 2: // Character
                                $insertData['value_char'] = $value;
                                break;
                            case 3: // Date
                                $insertData['value_date'] = $value;
                                break;
                            case 4: // Radio
                                $insertData['value_radio'] = $value;
                                break;
                        }

                        DB::table(self::$TempLFATable)->insert($insertData);
                    }
                }

                $remarkIds = self::insertRemarkslfa($lfaid, $remarks, $userId, $amountsStatus, $auditEndDate);

                DB::table(self::$LFADetailsTable)
                    ->where('lfa_details_id', $lfaid)
                    ->update([
                        'audityearid' => json_encode($yearselected),
                        'remark_ids' => json_encode($remarkIds),
                        'statusflag' => $amountsStatus,
                        'updated_by' => $userId,
                        'updated_on' => now(),
                    ]);
            } elseif ($action === 'update' && $lfaid) {
                $detailsData['updated_by'] = $userId;
                $detailsData['updated_on'] = now();

                if (isset($detailsData['remarks'])) {
                    unset($detailsData['remarks']);
                }

                DB::table(self::$LFADetailsTable)
                    ->where('lfa_details_id', $lfaid)
                    ->update($detailsData);

                if ($tempplanid) {
                    $updateData = [
                        'enddate' => $enddate,
                    ];
                    DB::table(self::$templateAuditPlanTable)
                        ->where('templateauditplanid', $tempplanid)
                        ->update($updateData);
                }

                foreach ($amounts as $maintypeId => $headers) {

                    if (is_numeric($maintypeId)) {
                        $maintype = DB::table(self::$templateAuditMainTypeTable)
                            ->where('maintypeid', intval($maintypeId))
                            ->first();

                        if (! $maintype) {
                            continue;
                        }
                    } else {
                        continue;
                    }

                    $temptypeid = $maintype->temptypeid ?? 1;

                    foreach ($headers as $headerId => $value) {

                        if (empty($value) || $value === null || $value === '') {
                            DB::table(self::$TempLFATable)
                                ->where('lfaid', $lfaid)
                                ->where('tmpauditheaderid', $headerId)
                                ->where('maintypeid', $maintypeId)
                                ->delete();

                            continue;
                        }

                         $tmpMapping = DB::table(self::$template_audit_mapping)
                            ->where('headerid', $headerId)
                            ->where('maintypeid', $maintypeId)
                            // ->where('subtypeid', $subtypeId)
                            ->first();

                    $tmpMappingId = $tmpMapping->tmp_mappingid ?? null;

                        $updateData = [
                            'tempplanid' => $tempplanid,
                            'statusflag' => $amountsStatus,
                            'audit_end_date' => $auditEndDate,
                            'updated_by' => $userId,
                            'updated_on' => now(),
                            'tmp_mappingid' => $tmpMappingId,
                        ];

                        switch ($temptypeid) {
                            case 1:
                                $updateData['value_numeric'] = $value;
                                break;
                            case 2:
                                $updateData['value_char'] = $value;
                                break;
                            case 3:
                                $updateData['value_date'] = $value;
                                break;
                            case 4:
                                $updateData['value_radio'] = $value;
                                break;
                        }

                        $exists = DB::table(self::$TempLFATable)
                            ->where('lfaid', $lfaid)
                            ->where('tmpauditheaderid', $headerId)
                            ->where('maintypeid', $maintypeId)
                            ->exists();

                        if ($exists) {
                            DB::table(self::$TempLFATable)
                                ->where('lfaid', $lfaid)
                                ->where('tmpauditheaderid', $headerId)
                                ->where('maintypeid', $maintypeId)
                                ->update($updateData);
                        } else {
                            $insertData = array_merge([
                                'lfaid' => $lfaid,
                                'tmpauditheaderid' => $headerId,
                                'maintypeid' => $maintypeId,
                                'subtypeid' => null,
                                'temptypeid' => $temptypeid,
                                'audit_start_date' => now(),
                                'created_by' => $userId,
                                'created_on' => now(),
                            ], $updateData);

                            DB::table(self::$TempLFATable)->insert($insertData);
                        }
                    }
                }

                $remarkIds = self::updateRemarkslfa($lfaid, $remarks, $userId, $amountsStatus, $auditEndDate);

                DB::table(self::$LFADetailsTable)
                    ->where('lfa_details_id', $lfaid)
                    ->update([
                        'audityearid' => json_encode($yearselected),
                        'remark_ids' => json_encode($remarkIds),
                        'updated_by' => $userId,
                        'updated_on' => now(),
                    ]);
            }

            DB::commit();

            return $lfaid;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    private static function insertRemarkslfa($lfaid, $remarks, $userId, $statusflag, $auditEndDate)
    {
        $remarkIds = [];

        foreach ($remarks as $index => $remark) {
            // Check if remark has both type and text and text is not empty
            if (! empty($remark['type']) && ! empty(trim($remark['text'] ?? ''))) {
                $remarkId = DB::table(self::$TempLFARemarksTable)->insertGetId([
                    'lfaid' => $lfaid,
                    'type' => $remark['type'],
                    'remark_text' => $remark['text'],
                    'statusflag' => $statusflag,
                    'audit_start_date' => now(),
                    'audit_end_date' => $auditEndDate,
                    'created_by' => $userId,
                    'created_on' => now(),
                ], 'remark_id');

                $remarkIds[] = $remarkId;
            }
        }

        return $remarkIds;
    }

    private static function updateRemarkslfa($lfaid, $remarks, $userId, $statusflag, $auditEndDate)
    {
        $remarkIds = [];

        $existingRemarks = DB::table(self::$TempLFARemarksTable)
            ->where('lfaid', $lfaid)
            ->where('statusflag', 'E') // Only consider active remarks
            ->orderBy('remark_id')
            ->get();

        foreach ($remarks as $index => $remark) {
            if (! empty($remark['type']) && ! empty(trim($remark['text'] ?? ''))) {
                if (isset($existingRemarks[$index])) {
                    $remarkId = $existingRemarks[$index]->remark_id;

                    DB::table(self::$TempLFARemarksTable)
                        ->where('remark_id', $remarkId)
                        ->update([
                            'type' => $remark['type'],
                            'remark_text' => trim($remark['text']),
                            'statusflag' => $statusflag,
                            'audit_end_date' => $auditEndDate,
                            'updated_by' => $userId,
                            'updated_on' => now(),
                        ]);

                    $remarkIds[] = $remarkId;
                } else {
                    $remarkId = DB::table(self::$TempLFARemarksTable)->insertGetId([
                        'lfaid' => $lfaid,
                        'type' => $remark['type'],
                        'remark_text' => trim($remark['text']),
                        'statusflag' => $statusflag, // 'E' or 'F'
                        'audit_start_date' => now(),
                        'audit_end_date' => $auditEndDate,
                        'created_by' => $userId,
                        'created_on' => now(),
                    ], 'remark_id');

                    $remarkIds[] = $remarkId;
                }
            }
        }

        if (count($existingRemarks) > count($remarks)) {
            $remainingIds = array_slice($existingRemarks->pluck('remark_id')->toArray(), count($remarks));

            // APPROACH 1: Soft delete (recommended - keeps history)
            DB::table(self::$TempLFARemarksTable)
                ->whereIn('remark_id', $remainingIds)
                ->update([
                    'statusflag' => 'D', // 'D' for Deleted
                    'audit_end_date' => now(), // Set end date to now
                    'updated_by' => $userId,
                    'updated_on' => now(),
                ]);

            // APPROACH 2: Hard delete (completely remove from database)
            // DB::table(self::$TempHriaRemarksTable)
            //     ->whereIn('remark_id', $remainingIds)
            //     ->delete();

            // If using soft delete, you might not want to include deleted IDs in the returned array
            // $remarkIds = array_merge($remarkIds, $remainingIds);
            // Uncomment if you want to keep track of deleted IDs
        }

        return $remarkIds;
    }

    public static function dcainsertorUpdate(
        array $detailsData,
        array $amounts,
        array $remarks,
        $dcaid,
        $userId,
        $action = 'insert',
        array $yearselected = []
    ) {
        DB::beginTransaction();

        try {
            $detailsStatus = $detailsData['statusflag'] ?? 'E';
            $amountsStatus = $detailsStatus === 'F' ? 'F' : 'E';
            $auditEndDate = $detailsStatus === 'F' ? now() : null;
            $tempplanid = $detailsData['tempplanid'] ?? null;
            $detailsData['audit_end_date'] = now()->format('Y-m-d');
            $detailsData['cer_type_code'] = $detailsData['cer_type_code'] ?? null;

            $startdate = now(); // Same as audit_start_date
            $enddate = $auditEndDate;

            if ($action === 'insert') {
                $detailsData['created_by'] = $userId;
                $detailsData['created_on'] = now();

                if (isset($detailsData['remarks'])) {
                    unset($detailsData['remarks']);
                }

                if (! empty($detailsData['audityearid'])) {
                    if (is_array($detailsData['audityearid'])) {
                        $detailsData['audityearid'] = json_encode($detailsData['audityearid']);
                    } else {
                        $decoded = json_decode($detailsData['audityearid'], true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $detailsData['audityearid'] = json_encode($decoded);
                        }
                    }
                }

                $dcaid = DB::table(self::$DCADetailsTable)
                    ->insertGetId($detailsData, 'dca_details_id');

                if ($tempplanid) {
                    $updateData = [
                        'startdate' => $startdate,
                        'enddate' => $enddate,
                    ];

                    DB::table(self::$templateAuditPlanTable)
                        ->where('templateauditplanid', $tempplanid)
                        ->update($updateData);
                }

                foreach ($amounts as $headerKey => $maintypes) {
                    $headerId = str_replace('header_', '', $headerKey);

                    foreach ($maintypes as $maintypeKey => $subtypes) {
                        $maintypeId = str_replace('maintype_', '', $maintypeKey);

                        foreach ($subtypes as $subtypeKey => $value) {
                            $subtypeId = str_replace('subtype_', '', $subtypeKey);

                            if (empty($value) || $value === null || $value === '') {
                                continue;
                            }

                            $subtype = DB::table(self::$templateAuditSubTypeTable)
                                ->where('subtypeid', $subtypeId)
                                ->first();

                            if (! $subtype) {
                                continue;
                            }

                            $temptypeid = $subtype->temptypeid ?? 1; // Default to numeric

                             $tmpMapping = DB::table(self::$template_audit_mapping)
                            ->where('headerid', $headerId)
                            ->where('maintypeid', $maintypeId)
                            ->where('subtypeid', $subtypeId)
                            ->first();

                            $tmpMappingId = $tmpMapping->tmp_mappingid ?? null;

                            $insertData = [
                                'dcaid' => $dcaid,
                                'tempplanid' => $tempplanid,
                                'tmpauditheaderid' => $headerId,
                                'maintypeid' => $maintypeId,
                                'subtypeid' => $subtypeId,
                                'temptypeid' => $temptypeid,
                                'audit_start_date' => now(),
                                'audit_end_date' => $auditEndDate,
                                'statusflag' => $amountsStatus,
                                'created_by' => $userId,
                                'created_on' => now(),
                                'tmp_mappingid' => $tmpMappingId,

                            ];

                            switch ($temptypeid) {
                                case 1: // Numeric
                                    $insertData['value_numeric'] = $value;
                                    break;
                                case 2: // Character
                                    $insertData['value_char'] = $value;
                                    break;
                                case 3: // Date
                                    $insertData['value_date'] = $value;
                                    break;
                                case 4: // Radio
                                    $insertData['value_radio'] = $value;
                                    break;
                            }

                            DB::table(self::$TempDCATable)->insert($insertData);
                        }
                    }
                }

                // Insert remarks into new table and get IDs
                $remarkIds = self::insertRemarksdca($dcaid, $remarks, $userId, $amountsStatus, $auditEndDate);

                // Update same row with audityearid and remark IDs
                DB::table(self::$DCADetailsTable)
                    ->where('dca_details_id', $dcaid)
                    ->update([
                        'audityearid' => json_encode($yearselected),
                        'remark_ids' => json_encode($remarkIds), // Store remark IDs as JSON
                        'statusflag' => $amountsStatus,
                        'updated_by' => $userId,
                        'updated_on' => now(),
                    ]);
            } elseif ($action === 'update' && $dcaid) {
                $detailsData['updated_by'] = $userId;
                $detailsData['updated_on'] = now();

                if (isset($detailsData['remarks'])) {
                    unset($detailsData['remarks']);
                }

                DB::table(self::$DCADetailsTable)
                    ->where('dca_details_id', $dcaid)
                    ->update($detailsData);

                if ($tempplanid) {
                    $updateData = [
                        'enddate' => $enddate,
                    ];

                    DB::table(self::$templateAuditPlanTable)
                        ->where('templateauditplanid', $tempplanid)
                        ->update($updateData);
                }

                foreach ($amounts as $headerKey => $maintypes) {
                    $headerId = str_replace('header_', '', $headerKey);

                    foreach ($maintypes as $maintypeKey => $subtypes) {
                        $maintypeId = str_replace('maintype_', '', $maintypeKey);

                        foreach ($subtypes as $subtypeKey => $value) {
                            $subtypeId = str_replace('subtype_', '', $subtypeKey);

                            if (empty($value) || $value === null || $value === '') {
                                // Delete if value is empty
                                DB::table(self::$TempDCATable)
                                    ->where('dcaid', $dcaid)
                                    ->where('tmpauditheaderid', $headerId)
                                    ->where('maintypeid', $maintypeId)
                                    ->where('subtypeid', $subtypeId)
                                    ->delete();

                                continue;
                            }

                            // Get the subtype details
                            $subtype = DB::table(self::$templateAuditSubTypeTable)
                                ->where('subtypeid', $subtypeId)
                                ->first();

                            if (! $subtype) {
                                continue;
                            }

                            $temptypeid = $subtype->temptypeid ?? 1;

                            $tmpMapping = DB::table(self::$template_audit_mapping)
                            ->where('headerid', $headerId)
                            ->where('maintypeid', $maintypeId)
                            ->where('subtypeid', $subtypeId)
                            ->first();

                            $tmpMappingId = $tmpMapping->tmp_mappingid ?? null;

                            $updateData = [
                                'tempplanid' => $tempplanid,
                                'statusflag' => $amountsStatus,
                                'audit_end_date' => $auditEndDate,
                                'updated_by' => $userId,
                                'updated_on' => now(),
                                'tmp_mappingid' => $tmpMappingId,
                            ];

                            // Set value based on temptypeid
                            switch ($temptypeid) {
                                case 1:
                                    $updateData['value_numeric'] = $value;
                                    break;
                                case 2:
                                    $updateData['value_char'] = $value;
                                    break;
                                case 3:
                                    $updateData['value_date'] = $value;
                                    break;
                                case 4:
                                    $updateData['value_radio'] = $value;
                                    break;
                            }

                            $exists = DB::table(self::$TempDCATable)
                                ->where('dcaid', $dcaid)
                                ->where('tmpauditheaderid', $headerId)
                                ->where('maintypeid', $maintypeId)
                                ->where('subtypeid', $subtypeId)
                                ->exists();

                            if ($exists) {
                                DB::table(self::$TempDCATable)
                                    ->where('dcaid', $dcaid)
                                    ->where('tmpauditheaderid', $headerId)
                                    ->where('maintypeid', $maintypeId)
                                    ->where('subtypeid', $subtypeId)
                                    ->update($updateData);
                            } else {
                                $insertData = array_merge([
                                    'dcaid' => $dcaid,
                                    'tmpauditheaderid' => $headerId,
                                    'maintypeid' => $maintypeId,
                                    'subtypeid' => $subtypeId,
                                    'temptypeid' => $temptypeid,
                                    'audit_start_date' => now(),
                                    'created_by' => $userId,
                                    'created_on' => now(),
                                ], $updateData);

                                DB::table(self::$TempDCATable)->insert($insertData);
                            }
                        }
                    }
                }
                $remarkIds = self::updateRemarksdca($dcaid, $remarks, $userId, $amountsStatus, $auditEndDate);

                DB::table(self::$DCADetailsTable)
                    ->where('dca_details_id', $dcaid)
                    ->update([
                        'audityearid' => json_encode($yearselected),
                        'remark_ids' => json_encode($remarkIds), // Store remark IDs as JSON
                        'updated_by' => $userId,
                        'updated_on' => now(),
                    ]);
            }

            DB::commit();

            return $dcaid;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    private static function insertRemarksdca($dcaid, $remarks, $userId, $statusflag, $auditEndDate)
    {
        $remarkIds = [];

        foreach ($remarks as $index => $remark) {
            if (! empty($remark['type']) && ! empty(trim($remark['text'] ?? ''))) {
                $remarkId = DB::table(self::$TempDCARemarksTable)->insertGetId([
                    'dcaid' => $dcaid,
                    'type' => $remark['type'],
                    'remark_text' => $remark['text'],
                    'statusflag' => $statusflag,
                    'audit_start_date' => now(),
                    'audit_end_date' => $auditEndDate,
                    'created_by' => $userId,
                    'created_on' => now(),
                ], 'remark_id');

                $remarkIds[] = $remarkId;
            }
        }

        return $remarkIds;
    }

    private static function updateRemarksdca($dcaid, $remarks, $userId, $statusflag, $auditEndDate)
    {
        $remarkIds = [];

        $existingRemarks = DB::table(self::$TempDCARemarksTable)
            ->where('dcaid', $dcaid)
            ->where('statusflag', 'E') // Only consider active remarks
            ->orderBy('remark_id')
            ->get();

        foreach ($remarks as $index => $remark) {
            if (! empty($remark['type']) && ! empty(trim($remark['text'] ?? ''))) {
                if (isset($existingRemarks[$index])) {
                    $remarkId = $existingRemarks[$index]->remark_id;

                    DB::table(self::$TempDCARemarksTable)
                        ->where('remark_id', $remarkId)
                        ->update([
                            'type' => $remark['type'],
                            'remark_text' => trim($remark['text']),
                            'statusflag' => $statusflag,
                            'audit_end_date' => $auditEndDate,
                            'updated_by' => $userId,
                            'updated_on' => now(),
                        ]);

                    $remarkIds[] = $remarkId;
                } else {
                    $remarkId = DB::table(self::$TempDCARemarksTable)->insertGetId([
                        'dcaid' => $dcaid,
                        'type' => $remark['type'],
                        'remark_text' => trim($remark['text']),
                        'statusflag' => $statusflag, // 'E' or 'F'
                        'audit_start_date' => now(),
                        'audit_end_date' => $auditEndDate,
                        'created_by' => $userId,
                        'created_on' => now(),
                    ], 'remark_id');

                    $remarkIds[] = $remarkId;
                }
            }
        }

        if (count($existingRemarks) > count($remarks)) {
            $remainingIds = array_slice($existingRemarks->pluck('remark_id')->toArray(), count($remarks));

            // APPROACH 1: Soft delete (recommended - keeps history)
            DB::table(self::$TempDCARemarksTable)
                ->whereIn('remark_id', $remainingIds)
                ->update([
                    'statusflag' => 'D', // 'D' for Deleted
                    'audit_end_date' => now(), // Set end date to now
                    'updated_by' => $userId,
                    'updated_on' => now(),
                ]);

            // APPROACH 2: Hard delete (completely remove from database)
            // DB::table(self::$TempHriaRemarksTable)
            //     ->whereIn('remark_id', $remainingIds)
            //     ->delete();

            // If using soft delete, you might not want to include deleted IDs in the returned array
            // $remarkIds = array_merge($remarkIds, $remainingIds);
            // Uncomment if you want to keep track of deleted IDs
        }

        return $remarkIds;
    }

    public static function milkinsertorUpdate(
        array $detailsData,
        array $amounts,
        array $remarks,
        $milkid,
        $userId,
        $action = 'insert',
        array $yearselected = []
    ) {
        DB::beginTransaction();

        try {
            $detailsStatus = $detailsData['statusflag'] ?? 'E';
            $amountsStatus = $detailsStatus === 'F' ? 'F' : 'E';
            $auditEndDate = $detailsStatus === 'F' ? now() : null;
            $tempplanid = $detailsData['tempplanid'] ?? null;
            // $tmpaudittypeid = $detailsData['tmpaudittypeid'] ?? null;
            $detailsData['audit_end_date'] = now()->format('Y-m-d');
            $detailsData['cer_type_code'] = $detailsData['cer_type_code'] ?? null;

            $startdate = now(); // Same as audit_start_date
            $enddate = $auditEndDate;


            if ($action === 'insert') {
                $detailsData['created_by'] = $userId;
                $detailsData['created_on'] = now();

                if (isset($detailsData['remarks'])) {
                    unset($detailsData['remarks']);
                }

                if (! empty($detailsData['audityearid'])) {
                    if (is_array($detailsData['audityearid'])) {
                        $detailsData['audityearid'] = json_encode($detailsData['audityearid']);
                    } else {
                        $decoded = json_decode($detailsData['audityearid'], true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $detailsData['audityearid'] = json_encode($decoded);
                        }
                    }
                }

                $milkid = DB::table(self::$MILKDetailsTable)->insertGetId($detailsData, 'milk_details_id');

                if ($tempplanid) {
                    $updateData = [
                        'startdate' => $startdate,
                        'enddate' => $enddate,
                    ];

                    DB::table(self::$templateAuditPlanTable)
                        ->where('templateauditplanid', $tempplanid)
                        ->update($updateData);
                }

                foreach ($amounts as $compoundKey => $value) {
                    if ($value === null || $value === '') {
                        continue;
                    }

                    $keyParts = self::parseAmountKey($compoundKey);

                    if (! $keyParts) {
                        continue;
                    }

                    $headerId = $keyParts['header_id'];
                    $maintypeId = $keyParts['maintype_id'];
                    $subtypeId = $keyParts['subtype_id'] ?? null;

                    $temptypeid = 1;
                    if ($subtypeId) {
                        $subtype = DB::table(self::$templateAuditSubTypeTable)
                            ->where('subtypeid', $subtypeId)
                            ->first();
                        $temptypeid = $subtype->temptypeid ?? 1;
                    }

                    $tmpMapping = DB::table(self::$template_audit_mapping)
                            ->where('headerid', $headerId)
                            ->where('maintypeid', $maintypeId)
                            ->where('subtypeid', $subtypeId)
                            ->first();

                    $tmpMappingId = $tmpMapping->tmp_mappingid ?? null;



                    $insertData = [
                        'milkid' => $milkid,
                        'tempplanid' => $tempplanid,
                        'tmpauditheaderid' => $headerId,
                        'maintypeid' => $maintypeId,
                        'subtypeid' => $subtypeId,
                        'temptypeid' => $temptypeid,
                        'audit_start_date' => now(),
                        'audit_end_date' => $auditEndDate,
                        'statusflag' => $amountsStatus,
                        'created_by' => $userId,
                        'created_on' => now(),
                        'tmp_mappingid' => $tmpMappingId,
                    ];

                    switch ($temptypeid) {
                        case 1:
                            $insertData['value_numeric'] = $value;
                            break;
                        case 2:
                            $insertData['value_char'] = $value;
                            break;
                        case 3:
                            $insertData['value_date'] = $value;
                            break;
                        case 4:
                            $insertData['value_radio'] = $value;
                            break;
                        default:
                            $insertData['value_numeric'] = $value;
                            break;
                    }

                    DB::table(self::$TempMILKTable)->insert($insertData);
                }

                $remarkIds = self::insertRemarksmilk($milkid, $remarks, $userId, $amountsStatus, $auditEndDate);

                DB::table(self::$MILKDetailsTable)
                    ->where('milk_details_id', $milkid)
                    ->update([
                        'audityearid' => json_encode($yearselected),
                        'remark_ids' => json_encode($remarkIds),
                        'statusflag' => $amountsStatus,
                        'updated_by' => $userId,
                        'updated_on' => now(),

                    ]);
            } elseif ($action === 'update' && $milkid) {
                $detailsData['updated_by'] = $userId;
                $detailsData['updated_on'] = now();

                if (isset($detailsData['remarks'])) {
                    unset($detailsData['remarks']);
                }

                DB::table(self::$MILKDetailsTable)
                    ->where('milk_details_id', $milkid)
                    ->update($detailsData);

                if ($tempplanid) {
                    $updateData = [
                        'enddate' => $enddate,
                    ];
                    DB::table(self::$templateAuditPlanTable)
                        ->where('templateauditplanid', $tempplanid)
                        ->update($updateData);
                }

                foreach ($amounts as $compoundKey => $value) {
                    $keyParts = self::parseAmountKey($compoundKey);

                    if (! $keyParts) {
                        continue;
                    }

                    $headerId = $keyParts['header_id'];
                    $maintypeId = $keyParts['maintype_id'];
                    $subtypeId = $keyParts['subtype_id'] ?? null;

                    // Determine the input type for this mapping: prefer subtype's temptypeid, fallback to maintype
                    $temptypeid = 1;
                    if ($subtypeId) {
                        $subtype = DB::table(self::$templateAuditSubTypeTable)
                            ->where('subtypeid', $subtypeId)
                            ->first();
                        $temptypeid = $subtype->temptypeid ?? $temptypeid;
                    } else {
                        $maintype = DB::table(self::$templateAuditMainTypeTable)
                            ->where('maintypeid', $maintypeId)
                            ->first();
                        $temptypeid = $maintype->temptypeid ?? $temptypeid;
                    }



                    if ($value === null || $value === '') {
                        $query = DB::table(self::$TempMILKTable)
                            ->where('milkid', $milkid)
                            ->where('tmpauditheaderid', $headerId)
                            ->where('maintypeid', $maintypeId);

                        if ($subtypeId) {
                            $query->where('subtypeid', $subtypeId);
                        } else {
                            $query->whereNull('subtypeid');
                        }

                        $query->delete();

                        continue;
                    }

                    $tmpMapping = DB::table(self::$template_audit_mapping)
                            ->where('headerid', $headerId)
                            ->where('maintypeid', $maintypeId)
                            ->where('subtypeid', $subtypeId)
                            ->first();

                    $tmpMappingId = $tmpMapping->tmp_mappingid ?? null;

                    $updateData = [
                        'tempplanid' => $tempplanid,
                        'statusflag' => $amountsStatus,
                        'audit_end_date' => $auditEndDate,
                        'updated_by' => $userId,
                        'updated_on' => now(),
                        'tmp_mappingid' => $tmpMappingId,
                    ];

                    switch ($temptypeid) {
                        case 1:
                            $updateData['value_numeric'] = $value;
                            break;
                        case 2:
                            $updateData['value_char'] = $value;
                            break;
                        case 3:
                            $updateData['value_date'] = $value;
                            break;
                        case 4:
                            $updateData['value_radio'] = $value;
                            break;
                        default:
                            $updateData['value_numeric'] = $value;
                            break;
                    }

                    $query = DB::table(self::$TempMILKTable)
                        ->where('milkid', $milkid)
                        ->where('tmpauditheaderid', $headerId)
                        ->where('maintypeid', $maintypeId);

                    if ($subtypeId) {
                        $query->where('subtypeid', $subtypeId);
                    } else {
                        $query->whereNull('subtypeid');
                    }

                    $exists = $query->exists();

                    if ($exists) {
                        $query->update($updateData);
                    } else {
                        $insertData = array_merge([
                            'milkid' => $milkid,
                            'tmpauditheaderid' => $headerId,
                            'maintypeid' => $maintypeId,
                            'subtypeid' => $subtypeId,
                            'temptypeid' => $temptypeid,
                            'audit_start_date' => now(),
                            'created_by' => $userId,
                            'created_on' => now(),
                        ], $updateData);

                        DB::table(self::$TempMILKTable)->insert($insertData);
                    }
                }

                $remarkIds = self::updateRemarksmilk($milkid, $remarks, $userId, $amountsStatus, $auditEndDate);

                DB::table(self::$MILKDetailsTable)
                    ->where('milk_details_id', $milkid)
                    ->update([
                        'audityearid' => json_encode($yearselected),
                        'remark_ids' => json_encode($remarkIds),
                        'updated_by' => $userId,
                        'updated_on' => now(),
                    ]);
            }

            DB::commit();

            return $milkid;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }


    private static function parseAmountKey($compoundKey)
    {
        $pattern = '/^header_(\d+)_maintype_(\d+)(?:_subtype_(\d+))?$/';

        if (preg_match($pattern, $compoundKey, $matches)) {
            return [
                'header_id' => $matches[1],
                'maintype_id' => $matches[2],
                'subtype_id' => $matches[3] ?? null,
            ];
        }

        return null;
    }

    private static function insertRemarksmilk($milkid, $remarks, $userId, $statusflag, $auditEndDate)
    {
        $remarkIds = [];

        foreach ($remarks as $index => $remark) {
            // Check if remark has both type and text and text is not empty
            if (! empty($remark['type']) && ! empty(trim($remark['text'] ?? ''))) {
                $remarkId = DB::table(self::$TempMILKRemarksTable)->insertGetId([
                    'milkid' => $milkid,
                    'type' => $remark['type'],
                    'remark_text' => $remark['text'],
                    'statusflag' => $statusflag,
                    'audit_start_date' => now(),
                    'audit_end_date' => $auditEndDate,
                    'created_by' => $userId,
                    'created_on' => now(),
                ], 'remark_id');

                $remarkIds[] = $remarkId;
            }
        }

        return $remarkIds;
    }

    private static function updateRemarksmilk($milkid, $remarks, $userId, $statusflag, $auditEndDate)
    {
        $remarkIds = [];

        $existingRemarks = DB::table(self::$TempMILKRemarksTable)
            ->where('milkid', $milkid)
            ->where('statusflag', 'E') // Only consider active remarks
            ->orderBy('remark_id')
            ->get();

        foreach ($remarks as $index => $remark) {
            if (! empty($remark['type']) && ! empty(trim($remark['text'] ?? ''))) {
                if (isset($existingRemarks[$index])) {
                    $remarkId = $existingRemarks[$index]->remark_id;

                    DB::table(self::$TempMILKRemarksTable)
                        ->where('remark_id', $remarkId)
                        ->update([
                            'type' => $remark['type'],
                            'remark_text' => trim($remark['text']),
                            'statusflag' => $statusflag,
                            'audit_end_date' => $auditEndDate,
                            'updated_by' => $userId,
                            'updated_on' => now(),
                        ]);

                    $remarkIds[] = $remarkId;
                } else {
                    $remarkId = DB::table(self::$TempMILKRemarksTable)->insertGetId([
                        'milkid' => $milkid,
                        'type' => $remark['type'],
                        'remark_text' => trim($remark['text']),
                        'statusflag' => $statusflag, // 'E' or 'F'
                        'audit_start_date' => now(),
                        'audit_end_date' => $auditEndDate,
                        'created_by' => $userId,
                        'created_on' => now(),
                    ], 'remark_id');

                    $remarkIds[] = $remarkId;
                }
            }
        }

        if (count($existingRemarks) > count($remarks)) {
            $remainingIds = array_slice($existingRemarks->pluck('remark_id')->toArray(), count($remarks));

            // APPROACH 1: Soft delete (recommended - keeps history)
            DB::table(self::$TempMILKRemarksTable)
                ->whereIn('remark_id', $remainingIds)
                ->update([
                    'statusflag' => 'D', // 'D' for Deleted
                    'audit_end_date' => now(), // Set end date to now
                    'updated_by' => $userId,
                    'updated_on' => now(),
                ]);

            // APPROACH 2: Hard delete (completely remove from database)
            // DB::table(self::$TempHriaRemarksTable)
            //     ->whereIn('remark_id', $remainingIds)
            //     ->delete();

            // If using soft delete, you might not want to include deleted IDs in the returned array
            // $remarkIds = array_merge($remarkIds, $remainingIds);
            // Uncomment if you want to keep track of deleted IDs
        }

        return $remarkIds;
    }

    //  public static function fetch_temp_auditplandetails($userid, $quartercode, $type = 'okp', $deptcode)
    // {

    //     $currentQuarter = DB::table(self::$mstDeptTable)
    //         ->where('deptcode', $deptcode)
    //         ->value('currentquarter');

    //     // dd($userid, $quartercode, $type);
    //     $configs = [
    //         'okp' => [
    //             'details_table' => self::$HriaOkpDetailsTable,
    //             'details_alias' => 'okp_details',
    //             'temp_table' => self::$TempHriaOkpTable,
    //             'temp_alias' => 'okp_temp',
    //             'join_column' => 'okpid',
    //             'details_join_column' => 'okpsdetails_id',
    //             'status_alias' => 'okp_status',
    //         ],
    //         'dca' => [
    //             'details_table' => self::$DCADetailsTable,
    //             'details_alias' => 'dca_details',
    //             'temp_table' => self::$TempDCATable,
    //             'temp_alias' => 'dca_temp',
    //             'join_column' => 'dcaid',
    //             'details_join_column' => 'dca_details_id',
    //             'status_alias' => 'dca_status',
    //         ],
    //         'milk' => [
    //             'details_table' => self::$MILKDetailsTable,
    //             'details_alias' => 'milk_details',
    //             'temp_table' => self::$TempMILKTable,
    //             'temp_alias' => 'milk_temp',
    //             'join_column' => 'milkid',
    //             'details_join_column' => 'milk_details_id',
    //             'status_alias' => 'milk_status',
    //         ],
    //         'lfa' => [
    //             'details_table' => self::$LFADetailsTable,
    //             'details_alias' => 'lfa_details',
    //             'temp_table' => self::$TempLFATable,
    //             'temp_alias' => 'lfa_temp',
    //             'join_column' => 'lfaid',
    //             'details_join_column' => 'lfa_details_id',
    //             'status_alias' => 'lfa_status',
    //         ],
    //     ];

    //     $config = $configs[$type] ?? $configs['okp'];

    //     $query = DB::table(self::$templateAuditPlanTable.' as tap')
    //         ->join(self::$mstInstitutionTable.' as mi', 'mi.instid', '=', 'tap.instid')
    //         ->join(self::$deptUserDetailsTable.' as dud', 'dud.deptuserid', '=', 'tap.deptuserid')
    //         ->join(self::$mstDeptTable.' as msd', 'msd.deptcode', '=', 'mi.deptcode')
    //         ->join(self::$CategoryTable.' as mc', 'mc.catcode', '=', 'mi.catcode')
    //         ->leftJoin(self::$SubCategoryTable.' as msc', 'msc.auditeeins_subcategoryid', '=', 'mi.subcatid')
    //         ->leftJoin("{$config['details_table']} as {$config['details_alias']}", "{$config['details_alias']}.tempplanid", '=', 'tap.templateauditplanid')
    //         ->leftJoin("{$config['temp_table']} as {$config['temp_alias']}", "{$config['temp_alias']}.{$config['join_column']}", '=', "{$config['details_alias']}.{$config['details_join_column']}")
    //         ->join(self::$templateAuditTypeTable.' as tfm', function ($join) {
    //             $join->on('tfm.deptcode', '=', 'mi.deptcode')
    //                 ->whereRaw('(tfm.catcode = mi.catcode OR tfm.catcode IS NULL)')
    //                 ->whereRaw('(tfm.subcatcode = mi.subcatid OR tfm.subcatcode IS NULL)');
    //         })
    //         ->leftJoin(self::$templateAuditMainTypeTable.' as mtm', 'mtm.maintypeid', '=', "{$config['temp_alias']}.maintypeid")
    //         // ->leftJoin(self::$templateAuditHeaderTable.' as mth', 'mth.tmpauditheaderid', '=', 'mtm.tmpauditheaderid')
    //         ->join(self::$DesignationTable.' as d', 'd.desigcode', '=', 'dud.desigcode')
    //         ->join(
    //             DB::raw('(SELECT DISTINCT ON (auditquartercode) *
    //               FROM '.self::$AuditQuarterTable.'
    //               WHERE statusflag = \'Y\') AS maq'),
    //             'maq.auditquartercode',
    //             '=',
    //             'tap.auditquartercode'
    //         )
    //         ->select([
    //             'mi.instid',
    //             'mi.instename',
    //             'mi.insttname',
    //             'mi.erpno',
    //             'tap.templateauditplanid as tempplanid',
    //             'dud.username',
    //             'dud.usertamilname',
    //             'd.desigelname',
    //             'd.desigtlname',
    //             'maq.auditquartercode',
    //             'maq.auditquarter',
    //             'msd.deptcode',
    //             'msd.deptesname',
    //             'msd.deptelname',
    //             'mc.catcode',
    //             'mc.catename',
    //             'mc.cattname',
    //             'msc.auditeeins_subcategoryid as subcatcode',
    //             'msc.subcatename',
    //             'msc.subcattname',
    //             'tfm.tmpaudittypeid',
    //             'tfm.formcode',
    //             'tap.prioritycode',
    //             "{$config['details_alias']}.statusflag as {$config['status_alias']}",
    //         ])
    //         ->where('tap.deptuserid', $userid)
    //         ->where('tap.auditquartercode', $quartercode)
    //         ->where('tap.statusflag', 'F')
    //         ->where('mi.statusflag', 'Y')
    //         ->where('dud.statusflag', 'Y')
    //         ->where('msd.statusflag', 'Y')
    //         ->where('tap.prioritycode','02')
    //         ->when($quartercode != $currentQuarter, function ($query) {
    //             $query->whereNotNull('tap.startdate');
    //         })
    //         ->groupBy([
    //             'mi.instid',
    //             'mi.instename',
    //             'mi.insttname',
    //             'mi.erpno',
    //             'tap.templateauditplanid',
    //             'dud.username',
    //             'dud.usertamilname',
    //             'd.desigelname',
    //             'd.desigtlname',
    //             'maq.auditquartercode',
    //             'maq.auditquarter',
    //             'msd.deptcode',
    //             'msd.deptesname',
    //             'msd.deptelname',
    //             'mc.catcode',
    //             'mc.catename',
    //             'mc.cattname',
    //             'msc.auditeeins_subcategoryid',
    //             'msc.subcatename',
    //             'msc.subcattname',
    //             "{$config['details_alias']}.statusflag",
    //             'tfm.tmpaudittypeid',
    //             'tfm.formcode',
    //         ])
    //         ->orderBy('tap.templateauditplanid');

    //     return $query->get();
    // }

    public static function fetch_temp_auditplandetails($userid, $quartercode, $type = 'okp', $deptcode)
    {

        // dd($userid, $quartercode, $type);
        $configs = [
            'okp' => [
                'details_table' => self::$HriaOkpDetailsTable,
                'details_alias' => 'okp_details',
                'temp_table' => self::$TempHriaOkpTable,
                'temp_alias' => 'okp_temp',
                'join_column' => 'okpid',
                'details_join_column' => 'okpsdetails_id',
                'status_alias' => 'okp_status',
            ],
            'dca' => [
                'details_table' => self::$DCADetailsTable,
                'details_alias' => 'dca_details',
                'temp_table' => self::$TempDCATable,
                'temp_alias' => 'dca_temp',
                'join_column' => 'dcaid',
                'details_join_column' => 'dca_details_id',
                'status_alias' => 'dca_status',
            ],
            'milk' => [
                'details_table' => self::$MILKDetailsTable,
                'details_alias' => 'milk_details',
                'temp_table' => self::$TempMILKTable,
                'temp_alias' => 'milk_temp',
                'join_column' => 'milkid',
                'details_join_column' => 'milk_details_id',
                'status_alias' => 'milk_status',
            ],
            'lfa' => [
                'details_table' => self::$LFADetailsTable,
                'details_alias' => 'lfa_details',
                'temp_table' => self::$TempLFATable,
                'temp_alias' => 'lfa_temp',
                'join_column' => 'lfaid',
                'details_join_column' => 'lfa_details_id',
                'status_alias' => 'lfa_status',
            ],
            // 'sga' => [
            //     'details_table' => self::$SGADetailsTable,
            //     'details_alias' => 'sga_details',
            //     'temp_table' => self::$TempSGATable,
            //     'temp_alias' => 'sga_temp',
            //     'join_column' => 'sgaid',
            //     'details_join_column' => 'sga_details_id',
            //     'status_alias' => 'sga_status',
            // ],

        ];

        $config = $configs[$type] ?? $configs['okp'];

        $query = DB::table(self::$templateAuditPlanTable.' as tap')
            ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'tap.planmappingid')
            ->join(self::$mstInstitutionTable.' as mi', 'mi.instid', '=', 'tap.instid')
            ->join(self::$deptUserDetailsTable.' as dud', 'dud.deptuserid', '=', 'tap.deptuserid')
            ->join(self::$mstDeptTable.' as msd', 'msd.deptcode', '=', 'mi.deptcode')
            ->join(self::$CategoryTable.' as mc', 'mc.catcode', '=', 'mi.catcode')
            ->leftJoin(self::$SubCategoryTable.' as msc', 'msc.auditeeins_subcategoryid', '=', 'mi.subcatid')
            ->leftJoin("{$config['details_table']} as {$config['details_alias']}", "{$config['details_alias']}.tempplanid", '=', 'tap.templateauditplanid')
            ->leftJoin("{$config['temp_table']} as {$config['temp_alias']}", "{$config['temp_alias']}.{$config['join_column']}", '=', "{$config['details_alias']}.{$config['details_join_column']}")
            ->join(self::$templateAuditTypeTable.' as tfm', function ($join) {
                $join->on('tfm.deptcode', '=', 'mi.deptcode')
                    ->whereRaw('(tfm.catcode = mi.catcode OR tfm.catcode IS NULL)')
                    ->whereRaw('(tfm.subcatcode = mi.subcatid OR tfm.subcatcode IS NULL)');
            })
            // ->leftJoin(self::$templateAuditMainTypeTable.' as mtm', 'mtm.maintypeid', '=', "{$config['temp_alias']}.maintypeid")
            // ->leftJoin(self::$templateAuditHeaderTable.' as mth', 'mth.tmpauditheaderid', '=', 'mtm.tmpauditheaderid')
            ->join(self::$DesignationTable.' as d', 'd.desigcode', '=', 'dud.desigcode')
            ->join(
                DB::raw('(SELECT DISTINCT ON (auditquartercode) *
                  FROM '.self::$AuditQuarterTable.'
                  WHERE statusflag = \'Y\') AS maq'),
                'maq.auditquartercode',
                '=',
                'tap.auditquartercode'
            )
            ->select([
                'mi.instid',
                'mi.instename',
                'mi.insttname',
                'mi.erpno',
                'tap.templateauditplanid as tempplanid',
                'tap.prioritycode',
                'dud.username',
                'dud.usertamilname',
                'd.desigelname',
                'd.desigtlname',
                'maq.auditquartercode',
                'maq.auditquarter',
                'msd.deptcode',
                'msd.deptesname',
                'msd.deptelname',
                'mc.catcode',
                'mc.catename',
                'mc.cattname',
                'msc.auditeeins_subcategoryid as subcatcode',
                'msc.subcatename',
                'msc.subcattname',
                'tfm.tmpaudittypeid',
                'tfm.formcode',
                "{$config['details_alias']}.statusflag as {$config['status_alias']}",
            ])
            ->where('tap.deptuserid', $userid)
            ->where('apm.planmappingid', $quartercode)
            ->where('tap.statusflag', 'F')
            ->where('mi.statusflag', 'Y')
            ->where('dud.statusflag', 'Y')
            ->where('msd.statusflag', 'Y')
            ->where(function ($q) {
                $q->where('apm.statusflag', 'Y')
                ->orWhere(function ($q) {
                    $q->whereNotNull('tap.startdate')
                        ->whereNotNull('tap.enddate');
                });
            })
            ->groupBy([
                'mi.instid',
                'mi.instename',
                'mi.insttname',
                'mi.erpno',
                'tap.templateauditplanid',
                'dud.username',
                'dud.usertamilname',
                'd.desigelname',
                'd.desigtlname',
                'maq.auditquartercode',
                'maq.auditquarter',
                'msd.deptcode',
                'msd.deptesname',
                'msd.deptelname',
                'mc.catcode',
                'mc.catename',
                'mc.cattname',
                'msc.auditeeins_subcategoryid',
                'msc.subcatename',
                'msc.subcattname',
                "{$config['details_alias']}.statusflag",
                'tfm.tmpaudittypeid',
                'tfm.formcode',
            ])
            ->orderBy('tap.templateauditplanid');

        // dd($query->toSql(), $query->getBindings());
        // dd($query->get());
        return $query->get();
    }

// public static function getquarterdetails($deptcode)
//     {
//         try {
//             if (empty($deptcode)) {
//                 throw new Exception("Deptcode is not available");
//             }

//             $deptData = DB::table(self::$mstDeptTable . ' as dept')
//                 ->select(
//                     'dept.nextquarter',
//                     'dept.nextquarterfromdate',
//                     'dept.nextquartertodate',
//                     'dept.currentquarter',
//                     'dept.currentquarterfromdate',
//                     'dept.currentquartertodate',
//                     'dept.autoplandate',
//                     'dept.previousquarter'
//                 )
//                 ->where('dept.deptcode', $deptcode)
//                 ->first();

//             if (!$deptData || is_null($deptData->autoplandate)) {
//                 throw new Exception('Department data or autoplandate not found for ' . $deptcode);
//             }


//             $currentDate = Carbon::today();

//             $quartersToFetch = [];

//             if ($currentDate > $deptData->autoplandate && $currentDate < $deptData->nextquarterfromdate) {
//                 $quartersToFetch = [$deptData->currentquarter, $deptData->nextquarter];
//             } else {
//                 $quartersToFetch = [$deptData->currentquarter, $deptData->previousquarter];
//             }

//             $quarterDets = DB::table(self::$AuditQuarterTable . ' as aq')
//                 ->join('audit.templateauditplan as ta', 'ta.auditquartercode', '=', 'aq.auditquartercode')
//                 ->whereIn('aq.auditquartercode', $quartersToFetch)
//                 ->select('aq.auditquarter', 'aq.auditquartercode')
//                 ->distinct()
//                 ->orderBy('aq.auditquartercode', 'desc')
//                 ->get();

//             return $quarterDets;

//         } catch (\Illuminate\Database\QueryException $e) {
//             throw new \Exception('Database error occurred. Please contact the administrator.', 500);
//         } catch (\Exception $e) {
//             throw new \Exception($e->getMessage(), 409);
//         }
//     }

public static function getquarterdetails($deptcode)
    {
        try {
            if (empty($deptcode)) {
                throw new Exception("Deptcode is not available");
            }

            // $deptData = DB::table(self::$mstDeptTable . ' as dept')
            //     ->select(
            //         'dept.nextquarter',
            //         'dept.nextquarterfromdate',
            //         'dept.nextquartertodate',
            //         'dept.currentquarter',
            //         'dept.currentquarterfromdate',
            //         'dept.currentquartertodate',
            //         'dept.autoplandate',
            //         'dept.previousquarter'
            //     )
            //     ->where('dept.deptcode', $deptcode)
            //     ->first();

            // if (!$deptData || is_null($deptData->autoplandate)) {
            //     throw new Exception('Department data or autoplandate not found for ' . $deptcode);
            // }


            // $currentDate = Carbon::today();

            // $quartersToFetch = [];

            // if ($currentDate > $deptData->autoplandate && $currentDate < $deptData->nextquarterfromdate) {
            //     $quartersToFetch = [$deptData->currentquarter, $deptData->nextquarter];
            // } else {
            //     $quartersToFetch = [$deptData->currentquarter, $deptData->previousquarter];
            // }

            $quarterDets = DB::table('audit.templateauditplan as ta')
                ->join('audit.auditplanmapping as apm', 'apm.planmappingid', '=', 'ta.planmappingid')
                ->join('audit.mst_financialyear as mf', 'mf.financialyearcode', '=', 'apm.financialyearcode')
                ->where('apm.deptcode', $deptcode)
                ->select(
                    'apm.planname',
                    'apm.auditquartercode',
                    'mf.financialyear',
                    'apm.planmappingid'
                )
                ->distinct()
                ->orderBy('apm.planmappingid', 'desc')
                ->get();

            return $quarterDets;

        } catch (\Illuminate\Database\QueryException $e) {
            throw new \Exception('Database error occurred. Please contact the administrator.', 500);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage(), 409);
        }
    }
}
