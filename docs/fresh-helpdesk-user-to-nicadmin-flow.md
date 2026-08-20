# Fresh Helpdesk Ticket Flow: User to NIC Admin

This guide explains how a normal department user ticket reaches NIC Admin. In the current system, a user-created ticket does not go directly to NIC Admin. It first reaches State Admin, and State Admin forwards it to NIC Admin when NIC action is required.

## When To Use This Flow

Use this flow when a department user raises a support request, bug, data correction, or feature request that needs NIC Admin review or technical coordination.

## Roles Involved

| Role | Responsibility |
| --- | --- |
| User | Creates the ticket and provides issue details. |
| State Admin | Reviews the request, asks user for clarification if needed, or forwards it to NIC Admin. |
| NIC Admin | Reviews the ticket, updates/coordinates the technical action, and returns the ticket to State Admin. |

## Flow Summary

```text
User creates ticket
    -> State Admin reviews
        -> State Admin forwards to NIC Admin
            -> NIC Admin reviews / acts
                -> NIC Admin returns to State Admin
                    -> State Admin resolves and returns to User
```

## Step By Step

### 1. User Creates Ticket

The user opens Helpdesk and selects Create Ticket.

Required fields:

| Field | Guidance |
| --- | --- |
| Department | Auto-filled for normal users. Admin users can select a department. |
| Financial Year | Select the correct financial year for the issue. |
| Audit Quarter | Select the related audit quarter. |
| Type | Select Support, New Feature, Bug / Issue, or Data Correction. |
| Ticket Scope | Select Specified or All, based on whether the issue is limited or common. |
| Priority | Use Critical only for urgent blocking issues. |
| Category | Select the matching module/category. |
| Subject | Keep it short and clear. |
| Description | Mention screen name, exact issue, user ID, and steps to reproduce. |
| Attachments | Optional. JPEG, PNG, or PDF only, maximum 500 KB per file. |

After submission:

| System Field | Value |
| --- | --- |
| Status | In Progress |
| Currently With | State Admin |
| Internal Note | Ticket created and auto forwarded to State Admin. |

### 2. State Admin Reviews Ticket

State Admin checks the ticket in Ticket Details.

State Admin can:

| Action | Use When | Result |
| --- | --- | --- |
| Return to User | Required information is missing. | Ticket goes back to the user for clarification. |
| Forward to NIC Admin | NIC Admin action is required. | Ticket moves to NIC Admin. |
| Mark Important | Ticket needs NIC Admin attention. | Important flag is saved and NIC Admin is notified. |

When State Admin forwards to NIC Admin:

| System Field | Value |
| --- | --- |
| Status | In Progress |
| Stored Status | pending_nic_admin |
| Currently With | NIC Admin |
| Internal Note | Forwarded to NIC Admin. |

### 3. NIC Admin Reviews Ticket

NIC Admin checks the ticket in Ticket Details using the Assigned / Forwarded filter if needed.

NIC Admin can:

| Action | Use When | Result |
| --- | --- | --- |
| Return to State Admin | NIC review is complete, or State Admin has to close/communicate with user. | Ticket goes back to State Admin. |
| Forward to Senior Developer | Technical work needs developer review path. | Ticket enters developer flow. |
| Assign Directly to Developer | NIC Admin wants to assign directly without senior review first. | Ticket enters direct developer flow. |
| Update Status | NIC Admin needs to update ticket status while it is with NIC. | Status changes on the ticket. |

When NIC Admin returns to State Admin:

| System Field | Value |
| --- | --- |
| Status | Need Clarification |
| Stored Status | returned_state_admin |
| Currently With | State Admin |
| Internal Note | Returned to State Admin. |

### 4. State Admin Resolves And Returns To User

After NIC Admin returns the ticket, State Admin verifies the response.

State Admin can:

| Action | Use When | Result |
| --- | --- | --- |
| Resolve and Return to User | Issue is solved or final response is ready. | Ticket goes back to user as resolved. |
| Forward to NIC Admin again | More NIC action is required. | Ticket goes back to NIC Admin. |

When State Admin resolves:

| System Field | Value |
| --- | --- |
| Status | Resolved |
| Currently With | User |
| Internal Note | Resolved and returned to User. |

## User Guidance

Before creating a ticket, the user should:

- Write the exact screen name where the problem happened.
- Mention the user ID or department affected.
- Add the exact error message if available.
- Add screenshots or PDF proof when useful.
- Choose the correct priority. Do not use Critical for normal clarification or minor issues.

## State Admin Guidance

Before forwarding to NIC Admin, State Admin should:

- Confirm the department, financial year, and audit quarter are correct.
- Check whether the issue is understandable without calling the user again.
- Return to User if screenshots, IDs, or steps are missing.
- Mark Important only when NIC Admin should treat the ticket as urgent or high visibility.

## NIC Admin Guidance

Before returning to State Admin, NIC Admin should:

- Add clear remarks about what was checked or completed.
- If developer work is needed, forward to Senior Developer or assign directly to Developer instead of returning.
- Return to State Admin only when State Admin can take the next action.

## Quick Verification

Use these values to verify the grid:

| Stage | Status Shown | Currently With |
| --- | --- | --- |
| User submitted | In Progress | State Admin |
| State forwarded | In Progress | NIC Admin |
| NIC returned | Need Clarification | State Admin |
| State resolved | Resolved | User |
