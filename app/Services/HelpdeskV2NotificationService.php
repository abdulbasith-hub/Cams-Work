<?php

namespace App\Services;

use App\Models\HelpdeskV2Ticket;
use App\Services\PHPMailerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class HelpdeskV2NotificationService
{
    public function __construct(private PHPMailerService $mailService)
    {
    }

    public function ticketCreated(HelpdeskV2Ticket $ticket): void
    {
        $this->send($ticket, 'Helpdesk V2 Ticket Created', 'A new Helpdesk V2 ticket has been submitted.');
    }

    public function ticketTransitioned(HelpdeskV2Ticket $ticket, string $action, ?string $remarks = null): void
    {
        $this->send($ticket, 'Helpdesk V2 Ticket Updated', 'A Helpdesk V2 workflow action was completed.', [
            'Action' => str_replace('_', ' ', ucfirst($action)),
            'Remarks' => $remarks ?: '-',
        ]);
    }

    private function send(HelpdeskV2Ticket $ticket, string $subject, string $message, array $extra = []): void
    {
        // Temporarily disabled while Helpdesk V2 workflow is being tested.
        return;

        $recipients = $this->recipientsForTicket($ticket);

        if (!$recipients) {
            $this->logNotification($ticket, $subject, [], 'skipped', 'No recipient found.');
            return;
        }

        $primary = array_shift($recipients);
        $body = $this->body($ticket, $message, $extra);

        try {
            $result = $this->mailService->sendEmail($primary, 'CAMS '.$subject.' - '.$ticket->ticket_number, $body, $recipients);
            $status = str_contains((string) $result, 'Message has been sent') ? 'sent' : 'failed';
            $this->logNotification($ticket, $subject, array_merge([$primary], $recipients), $status, (string) $result);
        } catch (Throwable $exception) {
            Log::error('Helpdesk V2 notification failed.', [
                'ticket_id' => $ticket->id,
                'error' => $exception->getMessage(),
            ]);
            $this->logNotification($ticket, $subject, array_merge([$primary], $recipients), 'failed', $exception->getMessage());
        }
    }

    private function recipientsForTicket(HelpdeskV2Ticket $ticket): array
    {
        $emails = [];

        if (in_array($ticket->forwarded_to_role, HelpdeskV2Session::tableRoleAliases(HelpdeskV2Session::ROLE_STATE_ADMIN), true)) {
            $emails = $this->deptUserEmails(fn ($query) => $query->where('uc.chargeid', (string) (view()->shared('Stateadminchargeid') ?? '1')));
        } elseif (in_array($ticket->forwarded_to_role, HelpdeskV2Session::tableRoleAliases(HelpdeskV2Session::ROLE_NIC_ADMIN), true)) {
            $emails = $this->deptUserEmails(function ($query) {
                $query->where('uc.chargeid', (string) (view()->shared('NICAdminchargeid') ?? '907'))
                    ->orWhere('rm.roleactioncode', (string) (view()->shared('Admin_roleactioncode') ?? '01'));
            });
        } elseif ($ticket->assigned_to_userid) {
            $emails[] = DB::table('audit.dev_userdetails')
                ->where('devuserid', (string) $ticket->assigned_to_userid)
                ->where('statusflag', 'Y')
                ->value('email');
        }

        $emails[] = $ticket->created_by_email;
        $current = strtolower((string) HelpdeskV2Session::email());

        return collect($emails)
            ->filter()
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->reject(fn ($email) => strtolower($email) === $current)
            ->unique(fn ($email) => strtolower($email))
            ->values()
            ->all();
    }

    private function deptUserEmails(callable $scope): array
    {
        return DB::table('audit.userchargedetails as uc')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->where('uc.statusflag', 'Y')
            ->where('du.statusflag', 'Y')
            ->where($scope)
            ->pluck('du.email')
            ->all();
    }

    private function body(HelpdeskV2Ticket $ticket, string $message, array $extra): string
    {
        $rows = array_merge([
            'Ticket No' => $ticket->ticket_number,
            'Subject' => $ticket->subject,
            'Status' => $ticket->status_label,
            'Pending With' => $ticket->pendingWithLabel(),
            'Priority' => $ticket->priority,
        ], $extra);

        $rowHtml = '';
        foreach ($rows as $label => $value) {
            $rowHtml .= '<tr><td style="padding:8px;border:1px solid #e5e7eb;font-weight:700;">'.e($label).'</td>'
                .'<td style="padding:8px;border:1px solid #e5e7eb;">'.nl2br(e((string) $value)).'</td></tr>';
        }

        return '<div style="font-family:Arial,sans-serif;color:#111827;">'
            .'<h2 style="margin-bottom:8px;">'.e($message).'</h2>'
            .'<table style="border-collapse:collapse;width:100%;font-size:14px;">'.$rowHtml.'</table>'
            .'</div>';
    }

    private function logNotification(HelpdeskV2Ticket $ticket, string $subject, array $recipients, string $status, ?string $response): void
    {
        try {
            DB::table('audit.helpdesk_ticket_comments')->insert([
                'ticket_id' => $ticket->id,
                'cams_userid' => HelpdeskV2Session::userId() ?: 'system',
                'user_name' => HelpdeskV2Session::userName(),
                'user_role' => 'notification',
                'comment' => 'Notification '.$status.': '.$subject.'. Recipients: '.implode(', ', $recipients).'. Response: '.($response ?: '-'),
                'is_internal' => true,
                'created_at' => now('Asia/Kolkata'),
                'updated_at' => now('Asia/Kolkata'),
            ]);
        } catch (Throwable $exception) {
            Log::warning('Helpdesk V2 notification log failed.', ['error' => $exception->getMessage()]);
        }
    }
}
