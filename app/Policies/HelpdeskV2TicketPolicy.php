<?php

namespace App\Policies;

use App\Models\HelpdeskV2Ticket;
use App\Services\HelpdeskV2Session;

class HelpdeskV2TicketPolicy
{
    public function view(?object $user, HelpdeskV2Ticket $ticket): bool
    {
        $userId = HelpdeskV2Session::userId();

        return HelpdeskV2Session::isStateAdmin()
            || HelpdeskV2Session::isNicAdmin()
            || (string) $ticket->created_by_userid === (string) $userId
            || (string) $ticket->layer_lead_userid === (string) $userId
            || (string) $ticket->developer_userid === (string) $userId
            || (string) $ticket->tester_userid === (string) $userId;
    }
}
