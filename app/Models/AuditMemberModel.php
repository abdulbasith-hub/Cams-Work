<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;  // Add this line

class AuditMemberModel extends Model
{
    protected $connection = 'pgsql'; // Use 'pgsql' for PostgreSQL
    protected $table = 'audit.auditplanteammember';
    public $incrementing = false; // No auto-increment

    protected $primaryKey = 'planteammemberid'; // No primary key
    public $timestamps = true;
    const CREATED_AT = 'createdon'; // Custom column name for `created_at`
    const UPDATED_AT = 'updatedon';

    // Define the fillable fields
    protected $fillable = [
        'teamhead',
        'userid',
        'statusflag',
        'createdon',
        'updatedon',
        'auditplanteamid'
    ];

    /**
     * Create or update the team head and members based on the provided data.
     *
     * @param array $data
     * @return bool
     */
    public static function updateOrCreate(array $data)
    {


        // Check if necessary fields are provided in $data
        if (!isset($data['teamid'], $data['teamheadid'], $data['teammembersid']) || !is_array($data['teammembersid'])) {
            // Handle missing fields or invalid data
            return false;
        }


        // Extract values from $data
        $auditteamid = $data['teamid'];
        $teamheadid = $data['teamheadid'];
        $teammembersid = $data['teammembersid'];

        // Start transaction to ensure all or none of the operations are applied
       // DB::beginTransaction();

        try {
            // Handle team head update or creation
            self::updateOrCreateTeamHead($auditteamid, $teamheadid);


            // Handle team members update or creation
            self::updateOrCreateTeamMembers($auditteamid, $teammembersid);

            // Commit the transaction if everything is successful
            //DB::commit();
            return true;
        } catch (\Exception $e) {
            // Rollback the transaction if any error occurs
            DB::rollback();
            \Log::error('Error during updateOrCreate for auditteamid ' . $auditteamid . ': ' . $e->getMessage());
            throw $e; // Re-throw the exception for further handling
        }
    }

    /**
     * Update or create the team head based on the audit team ID.
     *
     * @param int $auditteamid
     * @param int $teamheadid
     * @return void
     */
    protected static function updateOrCreateTeamHead($auditteamid, $teamheadid)
    {

        try {


            //Try to find existing team head
            $existingTeamHead = self::where('auditplanteamid', $auditteamid)
                                    ->where('teamhead', 'Y')
                                    ->first();


            if ($existingTeamHead) {
                //print_r($existingTeamHead);
                // Update existing team head
                $existingTeamHead->update([
                    'userid' => $teamheadid,
                    'statusflag' => 'Y',
                    'updatedon' => now(),
                ]);
            } else {
                // Create new team head if not found
                self::create([
                    'auditplanteamid' => $auditteamid,
                    'userid' => $teamheadid,
                    'teamhead' => 'Y',
                    'statusflag' => 'Y',
                    'createdon' => now(),
                    'updatedon' => now(),
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error during team head update or creation for auditteamid ' . $auditteamid . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update or create team members based on the audit team ID and the provided member IDs.
     *
     * @param int $auditteamid
     * @param array $teammembersid
     * @return void
     */
    protected static function updateOrCreateTeamMembers($auditteamid, $teammembersid)
    {
        // Get existing team members (not the team head)
        $existingUsers = self::where('auditplanteamid', $auditteamid)
                             ->where('teamhead', 'N')
                             ->get();

        $teamMemberCount = count($teammembersid);
        $teamMemberIds  =   $teammembersid;

        try {

                if($existingUsers->count() > 0)
                {
                    if ($existingUsers->count() === $teamMemberCount)
                    {
                        $i=0;
                        foreach($existingUsers as $value)
                        {
                            if ($value->planteammemberid)
                            {
                                AuditMemberModel::where('planteammemberid', $value->planteammemberid)
                                ->update([
                                    'userid' => $teamMemberIds[$i], // Update the teammember_userid
                                    'statusflag' => 'Y',                         // Set statusflag to 'Y'
                                    'updatedon' => now(),                        // Set the updated timestamp
                                ]);

                                $i++;
                            }
                        }
                    }
                    else if($existingUsers->count() < $teamMemberCount)
                    {
                        $i=0;
                        foreach($existingUsers as $value)
                        {
                            if ($value->planteammemberid)
                            {
                                // echo $value->teammemberid;
                                AuditMemberModel::where('planteammemberid', $value->planteammemberid)
                                ->update([
                                    'userid' => $teamMemberIds[$i], // Update the teammember_userid
                                    'statusflag' => 'Y',                         // Set statusflag to 'Y'
                                    'updatedon' => now(),                        // Set the updated timestamp
                                ]);

                                $i++;
                            }
                        }

                        for($l=$i;$l<$teamMemberCount;$l++)
                        {
                            AuditMemberModel::create([
                                'auditplanteamid' => $auditteamid,
                                'userid' => $teamMemberIds[$i],
                                'teamhead' => 'N',
                                'statusflag' => 'Y',
                                'createdon' => now(),
                                'updatedon' => now(),
                            ]);
                        }

                    }
                    else if($existingUsers->count() > $teamMemberCount)
                    {
                        $i=0;
                        foreach($existingUsers as $value)
                        {
                            if ($value->planteammemberid)
                            {
                                if (isset($teamMemberIds[$i]))
                                {
                                    AuditMemberModel::where('planteammemberid', $value->planteammemberid)
                                    ->update([
                                        'userid' => $teamMemberIds[$i], // Update the teammember_userid
                                        'statusflag' => 'Y',                         // Set statusflag to 'Y'
                                        'updatedon' => now(),                        // Set the updated timestamp
                                    ]);
                                }
                                else
                                {
                                    AuditMemberModel::where('planteammemberid', $value->planteammemberid)
                                    ->update([
                                        'statusflag' => 'N',                         // Set statusflag to 'Y'
                                        'updatedon' => now(),                        // Set the updated timestamp
                                    ]);
                                }


                                $i++;
                            }
                        }
                    }
                }
                else
                {
                    foreach ($teamMemberIds as $memberId) {

                        AuditMemberModel::create([
                            'auditplanteamid' => $auditteamid,
                            'userid' => $memberId,
                            'teamhead' => 'N',
                            'statusflag' => 'Y',
                            'createdon' => now(),
                            'updatedon' => now(),
                        ]);
                    }
                }
        } catch (\Exception $e) {
            \Log::error('Error during team members update or creation for auditteamid ' . $auditteamid . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Fetch a user by their ID.
     *
     * @param int $userId
     * @return AuditMemberModel|null
     */
    public static function fetchUserById($userId)
    {
        return self::find($userId);
    }
}
