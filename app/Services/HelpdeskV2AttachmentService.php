<?php

namespace App\Services;

use App\Models\HelpdeskV2Ticket;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HelpdeskV2AttachmentService
{
    public function storeMany(HelpdeskV2Ticket $ticket, array $files): void
    {
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $this->store($ticket, $file);
            }
        }
    }

    public function store(HelpdeskV2Ticket $ticket, UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $storedName = Str::uuid()->toString().($extension ? '.'.$extension : '');
        $path = $file->storeAs('helpdesk-v2/'.$ticket->ticket_number, $storedName, 'public');

        $attachment = [
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $storedName,
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size_bytes' => $file->getSize(),
            'sha256_hash' => hash_file('sha256', $file->getRealPath()),
            'uploaded_by_userid' => HelpdeskV2Session::userId(),
            'uploaded_by_name' => HelpdeskV2Session::userName(),
            'uploaded_at' => now('Asia/Kolkata')->toDateTimeString(),
        ];

        $attachments = $ticket->attachments ?? [];
        $attachments[] = $attachment;
        $ticket->forceFill(['attachments' => $attachments])->save();

        return $attachment;
    }

    public function download(HelpdeskV2Ticket $ticket, int $index)
    {
        $attachment = ($ticket->attachments ?? [])[$index] ?? null;
        abort_unless($attachment && Storage::disk('public')->exists($attachment['path'] ?? ''), 404);

        return Storage::disk('public')->download($attachment['path'], $attachment['original_name'] ?? basename($attachment['path']));
    }
}
