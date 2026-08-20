<?php

namespace App\Services;

use App\Models\DeveloperTask;
use App\Models\HelpdeskTicket;
use App\Models\TaskManagementTask;
use App\Support\HelpdeskSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class HelpdeskMailNotificationService
{
    public function __construct(private PHPMailerService $mailService)
    {
    }

    public function ticketCreated(HelpdeskTicket $ticket): void
    {
        $this->sendTicketNotification(
            $ticket,
            'New Helpdesk Ticket',
            'A new helpdesk ticket has been created and forwarded.',
            $this->roleEmails((string) $ticket->forwarded_to_role),
            [
                'Forwarded To' => $this->roleLabel((string) $ticket->forwarded_to_role),
                'Remarks' => $ticket->forward_notes,
            ]
        );
    }

    public function ticketForwarded(HelpdeskTicket $ticket, string $forwardTo, ?object $developer = null, ?string $notes = null): void
    {
        $recipients = $forwardTo === 'developer'
            ? [$developer->email ?? null]
            : $this->roleEmails($forwardTo);

        $this->sendTicketNotification(
            $ticket,
            $forwardTo === 'developer' ? 'Helpdesk Ticket Assigned' : 'Helpdesk Ticket Forwarded',
            $forwardTo === 'developer'
                ? 'A helpdesk ticket has been assigned to you.'
                : 'A helpdesk ticket has been forwarded to your queue.',
            $recipients,
            [
                'Forwarded To' => $forwardTo === 'developer'
                    ? ($developer->devename ?? 'Tech Team')
                    : $this->roleLabel($forwardTo),
                'Remarks' => $notes,
            ]
        );
    }

    public function ticketCommentAdded(HelpdeskTicket $ticket, string $visibility, string $comment): void
    {
        $recipients = match ($visibility) {
            'developer_internal' => HelpdeskSession::isDeveloper()
                ? $this->roleEmails('nicadmin')
                : [$this->developerEmail($ticket->assigned_to_userid)],
            'dg_internal' => HelpdeskSession::isNicAdmin()
                ? $this->roleEmails('stateadmin')
                : $this->roleEmails('nicadmin'),
            default => $this->publicCommentRecipients($ticket),
        };

        $this->sendTicketNotification(
            $ticket,
            'Helpdesk Ticket Comment',
            'A new comment has been added to the helpdesk ticket.',
            $recipients,
            [
                'Comment Type' => Str::headline(str_replace('_', ' ', $visibility)),
                'Comment' => $comment,
            ]
        );
    }

    public function ticketSentBack(HelpdeskTicket $ticket, ?string $message = null): void
    {
        $this->sendTicketNotification(
            $ticket,
            'Helpdesk Ticket Sent Back',
            'A helpdesk ticket has been sent back to NIC Admin.',
            $this->roleEmails('nicadmin'),
            [
                'Sent Back By' => HelpdeskSession::userName(),
                'Message' => $message,
            ]
        );
    }

    public function developerTaskAssigned(DeveloperTask|TaskManagementTask $task, ?string $developerEmail = null, array $ccEmails = []): void
    {
        $recipients = $this->normalizeEmails(array_merge([$developerEmail], $ccEmails));

        if (empty($recipients)) {
            Log::info('Helpdesk task notification skipped because no valid developer email was found.', [
                'task_id' => $task->id,
            ]);
            return;
        }

        $subject = 'CAMS Helpdesk - Task Assigned';
        $body = $this->buildTaskBody($task);

        $this->sendToRecipients($recipients, $subject, $body, [
            'task_id' => $task->id,
        ]);
    }

    public function taskTestingSentToNic(TaskManagementTask $task): void
    {
        $recipients = $this->normalizeEmails($this->roleEmails('nicadmin'));

        if (empty($recipients)) {
            Log::info('Task testing notification skipped because no valid NIC Admin email was found.', [
                'task_id' => $task->id,
            ]);
            return;
        }

        $rows = [
            'Assigned To' => $task->developer_name,
            'Verified By' => $task->verified_by,
            'Task Type' => Str::headline((string) $task->task_type),
            'Module / Task' => $task->process_assigned,
            'Developer Note' => $task->remarks_by_developer,
            'Testing Note' => $task->remarks_by_verifier,
            'Verified On' => optional($task->verified_on)->format('d/m/Y h:i A') ?: '-',
        ];

        $body = $this->buildHtmlMail(
            'Task Testing Completed',
            'A Senior Developer has completed testing and sent the task to NIC Admin.',
            array_filter($rows, fn ($value) => $value !== null && $value !== ''),
            $this->taskUrl($task),
            'View Task'
        );

        $this->sendToRecipients($recipients, 'CAMS Helpdesk - Task Testing Completed', $body, [
            'task_id' => $task->id,
        ]);
    }

    private function sendTicketNotification(HelpdeskTicket $ticket, string $subjectAction, string $message, array $recipients, array $details = []): void
    {
        $recipients = $this->normalizeEmails($recipients);

        if (empty($recipients)) {
            Log::info('Helpdesk ticket notification skipped because no valid recipient email was found.', [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'subject_action' => $subjectAction,
            ]);
            return;
        }

        $ticketNumber = $ticket->ticket_number ?: 'Ticket #'.$ticket->id;
        $subject = 'CAMS Helpdesk - '.$subjectAction.' - '.$ticketNumber;
        $body = $this->buildTicketBody($ticket, $subjectAction, $message, $details);

        $this->sendToRecipients($recipients, $subject, $body, [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
        ]);
    }

    private function sendToRecipients(array $recipients, string $subject, string $body, array $context = []): void
    {
        $primaryEmail = array_shift($recipients);

        if (!$primaryEmail) {
            return;
        }

        try {
            $result = $this->mailService->sendEmail($primaryEmail, $subject, $body, $recipients);

            if (!str_contains((string) $result, 'Message has been sent')) {
                Log::warning('Helpdesk notification mail returned an unexpected response.', array_merge($context, [
                    'email' => $primaryEmail,
                    'cc' => $recipients,
                    'result' => $result,
                ]));
            }
        } catch (Throwable $exception) {
            Log::error('Helpdesk notification mail failed.', array_merge($context, [
                'email' => $primaryEmail,
                'cc' => $recipients,
                'error' => $exception->getMessage(),
            ]));
        }
    }

    private function publicCommentRecipients(HelpdeskTicket $ticket): array
    {
        $recipients = [$ticket->user_email];

        if ($ticket->isDeveloperStage()) {
            $recipients[] = $this->developerEmail($ticket->assigned_to_userid);
        } else {
            $recipients = array_merge($recipients, $this->roleEmails((string) $ticket->normalizedForwardedRole()));
        }

        return $recipients;
    }

    private function roleEmails(string $role): array
    {
        $role = strtolower(trim($role));

        if (!in_array($role, ['stateadmin', 'superadmin', 'nicadmin'], true)) {
            return [];
        }

        return DB::table('audit.userchargedetails as uc')
            ->join('audit.deptuserdetails as du', 'uc.userid', '=', 'du.deptuserid')
            ->join('audit.chargedetails as c', 'c.chargeid', '=', 'uc.chargeid')
            ->join('audit.rolemapping as rm', 'rm.rolemappingid', '=', 'c.rolemappingid')
            ->where('uc.statusflag', 'Y')
            ->where('du.statusflag', 'Y')
            ->where(function ($query) use ($role) {
                if (in_array($role, ['stateadmin', 'superadmin'], true)) {
                    $query->where('uc.chargeid', '1');
                    return;
                }

                $query->where('uc.chargeid', '907')
                    ->orWhere('rm.roleactioncode', '01');
            })
            ->pluck('du.email')
            ->all();
    }

    private function developerEmail($developerUserId): ?string
    {
        if (empty($developerUserId)) {
            return null;
        }

        return DB::table('audit.dev_userdetails')
            ->where('devuserid', trim((string) $developerUserId))
            ->where('statusflag', 'Y')
            ->value('email');
    }

    private function normalizeEmails(array $emails): array
    {
        $currentEmail = strtolower(trim((string) HelpdeskSession::email()));

        return collect($emails)
            ->flatten()
            ->filter()
            ->map(fn ($email) => trim((string) $email))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->reject(fn ($email) => strtolower($email) === $currentEmail)
            ->unique(fn ($email) => strtolower($email))
            ->values()
            ->all();
    }

    private function buildTicketBody(HelpdeskTicket $ticket, string $heading, string $message, array $details): string
    {
        $rows = [
            'Ticket No' => $ticket->ticket_number ?: 'Ticket #'.$ticket->id,
            'Subject' => $ticket->subject,
            'Department' => $ticket->department_name,
            'Category' => $ticket->category,
            'Priority' => Str::headline((string) $ticket->priority),
            'Status' => Str::headline(str_replace('_', ' ', (string) $ticket->status)),
            'Action By' => HelpdeskSession::userName(),
            'Action Role' => HelpdeskSession::roleLabel(),
        ];

        $rows = array_merge($rows, array_filter($details, fn ($value) => $value !== null && $value !== ''));
        $ticketUrl = $this->ticketUrl($ticket);

        return $this->buildHtmlMail($heading, $message, $rows, $ticketUrl, 'View Ticket');
    }

    private function buildTaskBody(DeveloperTask|TaskManagementTask $task): string
    {
        if ($task instanceof TaskManagementTask) {
            $rows = [
                'Assigned To' => $task->developer_name,
                'Assigned By' => $task->assigned_by_name,
                'Task Type' => Str::headline((string) $task->task_type),
                'Module / Task' => $task->process_assigned,
                'Description' => $task->testing_task_description,
                'Assigned On' => optional($task->assigned_on)->format('d/m/Y h:i A') ?: '-',
                'Expected On' => optional($task->expected_date_to_complete)->format('d/m/Y h:i A') ?: '-',
            ];

            return $this->buildHtmlMail(
                'Task Assigned',
                'A new task has been assigned to you.',
                array_filter($rows, fn ($value) => $value !== null && $value !== ''),
                $this->taskUrl($task),
                'View Task'
            );
        }

        $rows = [
            'Assigned To' => $task->developer_name,
            'Assigned By' => $task->assigned_by_name,
            'Task Type' => Str::headline((string) $task->task_type),
            'Process Assigned' => $task->process_assigned,
            'Expected Date' => optional($task->expected_date_to_complete)->format('d/m/Y h:i A') ?: '-',
            'Testing Task' => $task->is_testing_task ? 'Yes' : 'No',
            'Testing Description' => $task->testing_task_description,
        ];

        return $this->buildHtmlMail(
            'Task Assigned',
            'A new helpdesk task has been assigned to you.',
            array_filter($rows, fn ($value) => $value !== null && $value !== ''),
            $this->taskUrl($task),
            'View Task'
        );
    }

    private function buildHtmlMail(string $heading, string $message, array $rows, string $url, string $buttonLabel): string
    {
        $rowHtml = '';

        foreach ($rows as $label => $value) {
            $rowHtml .= '<tr>'
                .'<td style="padding:10px;border:1px solid #e5e7eb;font-weight:600;width:180px;">'.e((string) $label).'</td>'
                .'<td style="padding:10px;border:1px solid #e5e7eb;">'.nl2br(e((string) $value)).'</td>'
                .'</tr>';
        }

        return '<!doctype html><html><body style="margin:0;background:#f3f4f6;font-family:Arial,sans-serif;color:#111827;">'
            .'<div style="max-width:720px;margin:0 auto;padding:24px;">'
            .'<div style="background:#ffffff;border:1px solid #e5e7eb;border-radius:6px;padding:24px;">'
            .'<h2 style="margin:0 0 12px;font-size:20px;color:#111827;">'.e($heading).'</h2>'
            .'<p style="margin:0 0 18px;font-size:14px;line-height:1.6;">'.e($message).'</p>'
            .'<table style="width:100%;border-collapse:collapse;font-size:14px;margin-bottom:20px;">'.$rowHtml.'</table>'
            .'<a href="'.e($url).'" style="display:inline-block;background:#0d6efd;color:#ffffff;text-decoration:none;padding:10px 16px;border-radius:4px;font-weight:600;">'.e($buttonLabel).'</a>'
            .'<p style="margin:20px 0 0;font-size:12px;color:#e5e7eb;">This is an automated CAMS helpdesk notification.</p>'
            .'</div></div></body></html>';
    }

    private function ticketUrl(HelpdeskTicket $ticket): string
    {
        try {
            return route('helpdesk.tickets.show', $ticket);
        } catch (Throwable) {
            return url('/helpdesk/tickets/'.$ticket->id);
        }
    }

    private function taskUrl(DeveloperTask|TaskManagementTask $task): string
    {
        if ($task instanceof TaskManagementTask) {
            try {
                return route('task-management.show', $task);
            } catch (Throwable) {
                return url('/task-management/'.$task->id);
            }
        }

        try {
            return route('helpdesk.tasks.show', $task);
        } catch (Throwable) {
            return url('/helpdesk/tasks/'.$task->id);
        }
    }

    private function roleLabel(string $role): string
    {
        return match (strtolower(trim($role))) {
            'stateadmin', 'superadmin' => 'StateAdmin',
            'nicadmin' => 'NIC Admin',
            'developer' => 'Tech Team',
            default => Str::headline(str_replace('_', ' ', $role)),
        };
    }
}
