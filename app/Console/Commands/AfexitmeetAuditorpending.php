<?php

namespace App\Console\Commands;

use App\Services\PHPMailerService;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class AfexitmeetAuditorpending extends Command
{
    protected $signature = 'send:afexitmeet_auditorpending';
    protected $description = 'Send Pending Auditee Reply Slip Status emails (members & team heads)';
    protected $mailService;
    protected $smsService;

    public function __construct(PHPMailerService $mailService, SmsService $smsService)
    {
        parent::__construct();
        $this->mailService = $mailService;
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $this->info('Starting Pending Slip Email Process...');

        $mailCount = 0;

        try {
            $records = DB::select('SELECT * FROM audit.cronjob_afexitmeetingauditorpending() AS result');
            if (empty($records))
                return 0;

            $data = json_decode($records[0]->result, true);
            if (!$data)
                return 0;

            // MEMBER EMAILS
            $memberData = array_filter($data, fn($r) => $r['auditteamhead'] == 'N');
            $uniqueMemberEmails = array_unique(array_column($memberData, 'email'));

            foreach ($uniqueMemberEmails as $email) {
                $rows = array_filter($memberData, fn($r) => $r['email'] == $email);
                if (empty($rows))
                    continue;

                $username = $rows[array_key_first($rows)]['username'];
                $html = $this->buildEmailHtml($username, $rows);
                $this->mailService->sendEmail($email, 'Daily Audit Summary - CAMS', $html, '');
                $mailCount++;
            }

            // TEAM HEAD EMAILS
            $headData = array_filter($data, fn($r) => $r['auditteamhead'] == 'Y');
            $uniqueTeamHeadEmails = array_unique(array_column($headData, 'teamheademail'));

            foreach ($uniqueTeamHeadEmails as $email) {
                $rows = array_filter($data, fn($r) => $r['teamheademail'] == $email);
                if (empty($rows))
                    continue;

                $username = $rows[array_key_first($rows)]['username'];
                $html = $this->buildEmailHtml($username, $rows);
                $this->mailService->sendEmail($email, 'Daily Audit Summary - CAMS', $html, '');
                $mailCount++;
            }

            DB::table('audit.history_cronjob')->insert([
                'processdate' => now(),
                'process' => 'Afexitmeet_auditorpending',
                'details' => json_encode(['mailcount' => $mailCount]),
                'statusflag' => 'S',
            ]);

            $this->info("Completed. Total emails sent: {$mailCount}");
        } catch (\Exception $e) {
            Log::error('Cron Error: ' . $e->getMessage());
            $this->error($e->getMessage());

            DB::table('audit.history_cronjob')->insert([
                'processdate' => now(),
                'process' => 'Afexitmeet_auditorpending',
                'details' => json_encode(['mailcount' => $mailCount]),
                'statusflag' => 'F',
            ]);
        }
    }

    private function buildEmailHtml($username, $rows)
    {
        $today = date('d-m-Y h:i A', strtotime(View::shared('get_nowtime')));

        $html = "
        <div style='font-family:Arial;font-size:14px'>
            <p>Dear {$username},</p>
            <p style='text-align:center;color:#110552;font-size:15px;'>
                <b>Comprehensive Audit Management System (CAMS)</b><br>
                Daily Audit Summary as on <b>{$today}</b>
            </p>

            <p>The following Institutions have replied for the slip numbers mentioned below which are pending for Process.</p>

            <table border='1' width='100%' cellpadding='6' cellspacing='0' style='border-collapse:collapse;'>
                <tr style='background:#e6e6e6;'>
                    <th>Institution Name</th>
                    <th>Auditor Name</th>
                    <th>Slip Numbers</th>
                </tr>";

        foreach ($rows as $r) {
            $html .= "
                <tr>
                    <td>{$r['instename']}</td>
                    <td>{$r['username']}</td>
                    <td>{$r['slip_numbers']}</td>
                </tr>";
        }

        $html .= "
            </table><br><br>

            <p>With Regards,<br><b>CAMS IT Team</b></p>
            <table cellpadding='6' cellspacing='0' width='80%' style='border-collapse:collapse;'>
                <tr>
                    <td style='text-align:left; border:none;'>Sent on {$today}</td>
                    <td style='text-align:right; border:none;'>NIC</td>
                </tr>
            </table>
        </div>";

        return $html;
    }
}
