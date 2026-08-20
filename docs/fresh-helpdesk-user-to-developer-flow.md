# Fresh Helpdesk Ticket Flow: User to Developer

This guide explains how a user-created helpdesk ticket reaches a developer. The current system routes user tickets through State Admin and NIC Admin before a developer receives the work.

## When To Use This Flow

Use this flow when the ticket needs code changes, data correction, bug fixing, deployment support, or developer investigation.

## Roles Involved

| Role | Responsibility |
| --- | --- |
| User | Creates the ticket and confirms whether the final response is acceptable. |
| State Admin | Screens the ticket and forwards valid technical requests to NIC Admin. |
| NIC Admin | Decides whether to route through Senior Developer or assign directly to Developer. |
| Senior Developer | Reviews the ticket and assigns it to a Developer. |
| Developer | Works on the ticket and updates developer status. |

## Flow Summary

```text
User creates ticket
    -> State Admin reviews
        -> State Admin forwards to NIC Admin
            -> NIC Admin forwards to Senior Developer
                -> Senior Developer assigns Developer
                    -> Developer completes work
                        -> Developer returns to Senior Developer
                            -> Senior Developer returns to NIC Admin
                                -> NIC Admin returns to State Admin
                                    -> State Admin resolves and returns to User
```

NIC Admin can also skip the Senior Developer assignment step and assign directly to a Developer:

```text
User -> State Admin -> NIC Admin -> Developer
```

## Step By Step

### 1. User Creates Ticket

The user opens Helpdesk and selects Create Ticket.

Required fields:

| Field | Guidance |
| --- | --- |
| Department | Auto-filled for normal users. |
| Financial Year | Select the year linked to the issue. |
| Audit Quarter | Select the related audit quarter. |
| Type | Choose Support, New Feature, Bug / Issue, or Data Correction. |
| Ticket Scope | Select Specified or All. |
| Priority | Choose Low, Medium, High, or Critical. |
| Category | Choose the affected module. |
| Subject | Keep it short and searchable. |
| Description | Explain the issue, expected result, actual result, and steps to reproduce. |
| Attachments | Optional supporting screenshot/PDF, maximum 500 KB per file. |

After submission:

| System Field | Value |
| --- | --- |
| Status | In Progress |
| Currently With | State Admin |

### 2. State Admin Sends Ticket To NIC Admin

State Admin reviews whether the ticket has enough information.

If the ticket is incomplete, State Admin returns it to the user.

If the ticket needs technical action, State Admin uses Forward to NIC Admin.

After forwarding:

| System Field | Value |
| --- | --- |
| Status | In Progress |
| Stored Status | pending_nic_admin |
| Currently With | NIC Admin |

### 3. NIC Admin Chooses Developer Route

NIC Admin has two developer routing options.

| Option | Button | Best For |
| --- | --- | --- |
| Senior Developer route | Forward to Senior Developer | Work that needs review, coordination, or senior assignment decision. |
| Direct Developer route | Assign Directly to Developer | Clear work where NIC Admin already knows the correct developer. |

### 4A. Senior Developer Route

NIC Admin selects a senior developer and clicks Forward to Senior Developer.

After forwarding:

| System Field | Value |
| --- | --- |
| Status | In Progress |
| Stored Status | pending_senior_dev |
| Currently With | Selected Senior Developer |

Senior Developer reviews the ticket and selects Assign Developer.

After assignment:

| System Field | Value |
| --- | --- |
| Status | In Progress |
| Stored Status | pending_developer |
| Currently With | Selected Developer |

### 4B. Direct Developer Route

NIC Admin selects a developer and clicks Assign Directly to Developer.

After assignment:

| System Field | Value |
| --- | --- |
| Status | In Progress |
| Stored Status | pending_developer |
| Currently With | Selected Developer |

If the developer later returns the ticket and no previous senior developer exists, the system tries to route it to an available senior developer. If no senior developer is available, the ticket returns to NIC Admin.

### 5. Developer Works On Ticket

Developer opens Ticket Details and updates Developer Status.

Available developer statuses:

| Developer Status | Meaning |
| --- | --- |
| In Process | Work has started or is ongoing. |
| Need Clarification | Developer needs more information. |
| Completed | Developer work is complete and ready to return. |

Important rule:

- Developer must update Developer Status to Completed before using Return to Senior Developer.
- If the status is In Process or Need Clarification, the return button is disabled.

When Developer returns the ticket:

| System Field | Value |
| --- | --- |
| Status | Need Clarification |
| Stored Status | returned_senior_dev, or returned_nic_admin if no senior is available |
| Currently With | Senior Developer or NIC Admin |

### 6. Senior Developer Returns To NIC Admin

Senior Developer verifies the developer response.

Senior Developer can:

| Action | Use When | Result |
| --- | --- | --- |
| Assign Developer | More developer work is needed. | Ticket goes back to selected Developer. |
| Return to NIC Admin | Developer work is complete or NIC decision is needed. | Ticket goes to NIC Admin. |

When Senior Developer returns to NIC Admin:

| System Field | Value |
| --- | --- |
| Status | Need Clarification |
| Stored Status | returned_nic_admin |
| Currently With | NIC Admin |

### 7. NIC Admin Returns To State Admin

NIC Admin reviews the technical response and clicks Return to State Admin.

After return:

| System Field | Value |
| --- | --- |
| Status | Need Clarification |
| Stored Status | returned_state_admin |
| Currently With | State Admin |

### 8. State Admin Resolves And Returns To User

State Admin verifies the final response and clicks Resolve and Return to User.

After resolving:

| System Field | Value |
| --- | --- |
| Status | Resolved |
| Currently With | User |

## User Guidance

To help developers resolve faster, users should include:

- Exact module and screen name.
- Steps to reproduce the problem.
- Expected result and actual result.
- Error message text.
- User ID, department, institution, financial year, and audit quarter if relevant.
- Screenshot or PDF proof if available.

## NIC Admin Guidance

Use Senior Developer route when:

- The correct developer is not clear.
- Work needs senior review.
- Multiple modules or dependencies are involved.
- The ticket may need staging/production coordination.

Use Direct Developer route when:

- The issue is clear.
- The correct developer is already known.
- No senior review is needed before starting.

## Senior Developer Guidance

Before assigning to Developer:

- Confirm the ticket has enough detail for development work.
- Choose a non-senior developer.
- Add clear assignment remarks.
- Return to NIC Admin if the ticket is not a developer task.

## Developer Guidance

While working:

- Keep Developer Status updated.
- Use Need Clarification only when more information is required.
- Use Completed only when the work is finished and ready for review.
- Add completion remarks before returning.

## Quick Verification

Use these values to verify the grid:

| Stage | Status Shown | Currently With |
| --- | --- | --- |
| User submitted | In Progress | State Admin |
| State forwarded | In Progress | NIC Admin |
| NIC forwarded to Senior | In Progress | Senior Developer |
| Senior assigned Developer | In Progress | Developer |
| Developer completed/returned | Need Clarification | Senior Developer or NIC Admin |
| Senior returned | Need Clarification | NIC Admin |
| NIC returned | Need Clarification | State Admin |
| State resolved | Resolved | User |
