<?php

namespace App\Console\Commands;

use App\Services\PHPMailerService;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class Auditeependingstatus extends Command
{
    protected $signature = 'send:auditeependingstatus';
    protected $description = 'Fetch records from PostgreSQL function and send summary mails';
    protected $smsService;
    protected $mailService;

    public function __construct(PHPMailerService $mailService, SmsService $smsService)
    {
        parent::__construct();
        $this->mailService = $mailService;
        $this->smsService = $smsService;
    }

    public function handle()
    {
        $this->info('Starting daily PostgreSQL mail process...');
        $mailCount = 0;

        try {
            // Call PostgreSQL function
            $records = DB::select('SELECT * FROM audit.cronjob_auditeependingstatus() AS result');
            $get_nowtime = View::shared('get_nowtime');

            if (empty($records)) {
                $this->warn('No data returned.');
                return 0;
            }

            $jsonData = $records[0]->result ?? null;
            if (!$jsonData) {
                $this->warn('No JSON found.');
                return 0;
            }

            $data = json_decode($jsonData, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON.');
                return 0;
            }

            foreach ($data as $rec) {
                $email = $rec['email'] ?? null;
                if (!$email) {
                    $this->warn('Skipped record: No email');
                    continue;
                }

                $instename = $rec['instename'] ?? '';
                $todaypending = $rec['todaypending'] ?? 0;
                $totalpending = $rec['totalpending'] ?? 0;
                $entrymeetdate = date('d-m-Y', strtotime($rec['entrymeetdate']));
                $exitmeetdate = date('d-m-Y', strtotime($rec['exitmeetdate']));
                $yearcode_mapping = $rec['yearcode_mapping'] ?? '';
                $exitmeetlabel = $rec['exitmeetdateisdone'] ? 'Exitmeeting Date' : 'Proposed Exitmeeting Date';

                $today = date('d-m-Y h:i A', strtotime($get_nowtime));
                $senton = date('d-m-Y h:i A', strtotime($get_nowtime));

                // 📌 Inline HTML Email Template
                $html = '
                    <p class="greeting">Dear Mam/Sir,</p>



                     <p style="text-align:center;color:#110552;font-size:15px;">
            <b>Comprehensive Audit Management System (CAMS)</b><br>
            Daily Audit Summary as on <b>' . $today . '</b>
        </p>

                    <h3 style="text-align:center;">
                        Audit Slip status for the ' . $instename . '

                    </h3>

                    <table border="1" cellpadding="6" cellspacing="0" width="80%" align="center" style="border-collapse:collapse;">
                        <tbody>
                            <tr>
                                <th>Number of slips received</th>
                                <td>' . $todaypending . '</td>
                            </tr>
                            <tr>
                                <th>Number of slips pending to reply</th>
                                <td>' . $totalpending . '</td>
                            </tr>
                            <tr>
                                <th>Entrymeeting Date</th>
                                <td>' . $entrymeetdate . '</td>
                            </tr>
                            <tr>
                                <th>' . $exitmeetlabel . '</th>
                                <td>' . $exitmeetdate . '</td>
                            </tr>
                            <tr>
                                <th>Audit Period</th>
                                <td>' . $yearcode_mapping . '</td>
                            </tr>
                        </tbody>
                    </table>




                    <div style="margin-top:20px;">
                        With regards,<br />
                        <b>CAMS – IT Team</b>
                    </div>

                     <table width="80%" cellpadding="6" cellspacing="0" width="80%"  style="border-collapse:collapse;">
                        <tr>
                            <td style="text-align:left; border:none;">
                                Sent on ' . $senton . '
                            </td>
                            <td style="text-align:right; border:none;">
                                NIC
                            </td>
                        </tr>
                    </table>
                ';

                // 📩 Send Email
                $subject = 'Audit Slip Status - CAMS';

                $status = $this->mailService->sendEmail($email, $subject, $html, '');

                if ($status) {
                    $mailCount++;
                    $this->info("✔ Mail sent to: {$email}");
                } else {
                    $this->warn("⚠ Failed mail: {$email}");
                }
            }

            // Save success history
            DB::table('audit.history_cronjob')->insert([
                'processdate' => $get_nowtime,
                'process' => 'AuditeePendingStatus_Mail',
                'details' => json_encode(['mailcount' => $mailCount]),
                'statusflag' => 'S',
            ]);

            $this->info("🎯 Completed. Total mails: {$mailCount}");
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            Log::error('MailCronError: ' . $e->getMessage());

            // Save failure history
            DB::table('audit.history_cronjob')->insert([
                'processdate' => View::shared('get_nowtime'),
                'process' => 'AuditeePendingStatus_Mail',
                'details' => json_encode(['mailcount' => $mailCount]),
                'statusflag' => 'F',
            ]);
        }

        return 0;
    }
}
