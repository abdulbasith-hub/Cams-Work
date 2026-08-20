<?php

declare(strict_types=1);

$baseDir = __DIR__;
$screenshotDir = $baseDir.DIRECTORY_SEPARATOR.'screenshots';
$docxPath = $baseDir.DIRECTORY_SEPARATOR.'Fresh_Helpdesk_Ticket_User_Manual_Final.docx';

if (!is_dir($screenshotDir)) {
    mkdir($screenshotDir, 0777, true);
}

function img_text($image, string $text, int $x, int $y, int $size = 3, ?int $color = null): void
{
    if ($color === null) {
        $color = imagecolorallocate($image, 20, 35, 58);
    }
    imagestring($image, $size, $x, $y, $text, $color);
}

function img_rect($image, int $x1, int $y1, int $x2, int $y2, int $fill, ?int $border = null): void
{
    imagefilledrectangle($image, $x1, $y1, $x2, $y2, $fill);
    if ($border !== null) {
        imagerectangle($image, $x1, $y1, $x2, $y2, $border);
    }
}

function img_badge($image, string $label, int $x, int $y, int $fill, int $textColor): void
{
    img_rect($image, $x, $y, $x + 126, $y + 28, $fill, null);
    img_text($image, $label, $x + 12, $y + 8, 3, $textColor);
}

function base_canvas(int $w, int $h)
{
    $image = imagecreatetruecolor($w, $h);
    imageantialias($image, true);
    $bg = imagecolorallocate($image, 246, 249, 252);
    imagefilledrectangle($image, 0, 0, $w, $h, $bg);
    return $image;
}

function save_png($image, string $path): void
{
    imagepng($image, $path);
    imagedestroy($image);
}

function create_ticket_screen(string $path): void
{
    $img = base_canvas(1200, 760);
    $blue = imagecolorallocate($img, 76, 111, 232);
    $teal = imagecolorallocate($img, 47, 143, 157);
    $dark = imagecolorallocate($img, 7, 19, 48);
    $muted = imagecolorallocate($img, 86, 105, 130);
    $white = imagecolorallocate($img, 255, 255, 255);
    $line = imagecolorallocate($img, 213, 224, 238);
    $field = imagecolorallocate($img, 255, 255, 255);
    $light = imagecolorallocate($img, 239, 246, 255);

    img_rect($img, 40, 36, 1160, 724, $white, $line);
    img_rect($img, 40, 36, 1160, 92, $blue, null);
    img_text($img, 'Create Ticket', 64, 54, 5, $white);
    img_text($img, 'Fresh Helpdesk - sample screen', 64, 76, 2, $white);

    $x = 72;
    $y = 124;
    $w = 690;
    $h = 44;
    $fields = [
        ['Department', 'Directorate / Department'],
        ['Financial Year', '2026-2027'],
        ['Audit Quarter', 'Quarter 1'],
        ['Type', 'Bug / Issue'],
        ['Ticket Scope', 'Specified'],
        ['Priority', 'Medium'],
        ['Category', 'Audit Report'],
        ['Institution', 'District - Institution'],
        ['Subject', 'Report submit button not working'],
    ];

    foreach ($fields as $index => $fieldInfo) {
        $col = $index % 2;
        $row = intdiv($index, 2);
        $fx = $x + ($col * 360);
        $fy = $y + ($row * 76);
        if ($index === 8) {
            $fx = $x;
            $fy = $y + 4 * 76;
            $fw = $w;
        } else {
            $fw = 330;
        }
        img_text($img, $fieldInfo[0], $fx, $fy, 3, $dark);
        img_rect($img, $fx, $fy + 22, $fx + $fw, $fy + 22 + $h, $field, $line);
        img_text($img, $fieldInfo[1], $fx + 12, $fy + 37, 3, $muted);
    }

    img_text($img, 'Description', $x, 526, 3, $dark);
    img_rect($img, $x, 550, $x + $w, 634, $field, $line);
    img_text($img, 'Explain screen name, error message, expected result, and steps.', $x + 12, 570, 3, $muted);
    img_text($img, 'Attachments', $x, 648, 3, $dark);
    img_rect($img, $x, 672, $x + 420, 714, $field, $line);
    img_text($img, 'Choose JPEG / PNG / PDF file', $x + 12, 687, 3, $muted);
    img_rect($img, 630, 672, 762, 714, $blue, null);
    img_text($img, 'Create Ticket', 650, 687, 3, $white);

    img_rect($img, 805, 124, 1126, 214, $light, $line);
    img_text($img, 'Watchlist', 828, 146, 5, $dark);
    img_text($img, '[ ] Important: Notify NIC Admin', 828, 180, 3, $muted);

    img_rect($img, 805, 242, 1126, 524, $white, $line);
    img_rect($img, 805, 242, 1126, 292, $teal, null);
    img_text($img, 'Tips', 828, 260, 5, $white);
    $tips = [
        'Be specific about the issue.',
        'Select department first.',
        'Use Critical only for blockers.',
        'Attach screenshots when useful.',
        'Mention steps to reproduce.',
    ];
    foreach ($tips as $i => $tip) {
        img_text($img, '- '.$tip, 828, 320 + ($i * 32), 3, $muted);
    }

    save_png($img, $path);
}

function ticket_grid_screen(string $path): void
{
    $img = base_canvas(1260, 720);
    $blue = imagecolorallocate($img, 86, 119, 235);
    $darkHeader = imagecolorallocate($img, 98, 98, 98);
    $white = imagecolorallocate($img, 255, 255, 255);
    $line = imagecolorallocate($img, 213, 224, 238);
    $muted = imagecolorallocate($img, 82, 99, 123);
    $dark = imagecolorallocate($img, 7, 19, 48);
    $green = imagecolorallocate($img, 220, 252, 231);
    $greenText = imagecolorallocate($img, 22, 101, 52);
    $blueSoft = imagecolorallocate($img, 219, 234, 254);
    $blueText = imagecolorallocate($img, 30, 64, 175);
    $orange = imagecolorallocate($img, 255, 237, 213);
    $orangeText = imagecolorallocate($img, 154, 52, 18);
    $red = imagecolorallocate($img, 254, 226, 226);
    $redText = imagecolorallocate($img, 153, 27, 27);

    img_rect($img, 0, 0, 1260, 70, $blue, null);
    img_text($img, 'Ticket Details', 30, 20, 5, $white);
    img_text($img, 'Filters, ticket list, current owner, status and action', 30, 46, 2, $white);

    img_rect($img, 30, 92, 1230, 178, $white, $line);
    $filters = ['Search ticket / subject / user', 'All priorities', 'All statuses', 'Assigned / Forwarded', 'All'];
    $fx = 54;
    foreach ($filters as $i => $filter) {
        $fw = $i === 0 ? 320 : 190;
        img_rect($img, $fx, 118, $fx + $fw, 154, $white, $line);
        img_text($img, $filter, $fx + 12, 131, 3, $muted);
        $fx += $fw + 14;
    }
    img_rect($img, 1086, 118, 1198, 154, $blue, null);
    img_text($img, 'Filter', 1122, 131, 3, $white);

    img_rect($img, 30, 205, 1230, 650, $white, $line);
    $headers = ['S.No', 'Ticket / Created By', 'Ticket Details', 'Priority', 'Type', 'Subject', 'Status', 'Currently With', 'Created On', 'Dev Status', 'Updated On', 'Action'];
    $widths = [50, 130, 160, 85, 65, 115, 135, 112, 85, 120, 80, 63];
    $x = 30;
    foreach ($headers as $i => $header) {
        img_rect($img, $x, 205, $x + $widths[$i], 255, $darkHeader, $line);
        img_text($img, $header, $x + 8, 225, 2, $white);
        $x += $widths[$i];
    }

    $rows = [
        ['1', 'TKT00034 / Kumar', 'Login captcha error', 'High', 'Bug', 'Login issue', 'In Progress', 'NIC Admin', '19-08-2026', 'In Process', '19-08-2026', 'View'],
        ['2', 'TKT00035 / Priya', 'Audit report total mismatch', 'Critical', 'Data', 'Report issue', 'Need Clar.', 'State Admin', '18-08-2026', 'Need Clar.', '19-08-2026', 'View'],
        ['3', 'TKT00036 / Ravi', 'New export request', 'Medium', 'Feature', 'Export Excel', 'Resolved', 'User', '17-08-2026', 'Completed', '18-08-2026', 'View'],
    ];
    $y = 255;
    foreach ($rows as $r => $row) {
        $x = 30;
        foreach ($row as $c => $cell) {
            img_rect($img, $x, $y, $x + $widths[$c], $y + 78, $white, $line);
            if ($c === 3) {
                $fill = $cell === 'Critical' ? $red : ($cell === 'High' ? $orange : $blueSoft);
                $text = $cell === 'Critical' ? $redText : ($cell === 'High' ? $orangeText : $blueText);
                img_badge($img, $cell, $x + 8, $y + 24, $fill, $text);
            } elseif ($c === 6 || $c === 9) {
                $fill = $cell === 'Resolved' || $cell === 'Completed' ? $green : ($cell === 'Need Clar.' ? $orange : $blueSoft);
                $text = $cell === 'Resolved' || $cell === 'Completed' ? $greenText : ($cell === 'Need Clar.' ? $orangeText : $blueText);
                img_badge($img, $cell, $x + 8, $y + 24, $fill, $text);
            } else {
                img_text($img, $cell, $x + 8, $y + 30, 2, $dark);
            }
            $x += $widths[$c];
        }
        $y += 78;
    }

    save_png($img, $path);
}

function actions_screen(string $path): void
{
    $img = base_canvas(1100, 720);
    $blue = imagecolorallocate($img, 86, 119, 235);
    $green = imagecolorallocate($img, 34, 197, 94);
    $white = imagecolorallocate($img, 255, 255, 255);
    $line = imagecolorallocate($img, 213, 224, 238);
    $muted = imagecolorallocate($img, 82, 99, 123);
    $dark = imagecolorallocate($img, 7, 19, 48);
    $soft = imagecolorallocate($img, 239, 246, 255);

    img_text($img, 'Ticket Action Panels by Role', 42, 32, 5, $dark);

    $cards = [
        ['State Admin', ['Resolve and Return to User', 'Return to User', 'Forward to NIC Admin'], 40],
        ['NIC Admin', ['Return to State Admin', 'Forward to Senior Developer', 'Assign Directly to Developer'], 385],
        ['Developer', ['Update Developer Status', 'Return to Senior Developer'], 730],
    ];

    foreach ($cards as [$title, $actions, $x]) {
        img_rect($img, $x, 92, $x + 320, 620, $white, $line);
        img_rect($img, $x, 92, $x + 320, 142, $blue, null);
        img_text($img, $title, $x + 20, 110, 5, $white);
        img_text($img, 'Remarks', $x + 22, 170, 3, $dark);
        img_rect($img, $x + 22, 194, $x + 298, 274, $soft, $line);
        img_text($img, 'Enter action remarks', $x + 36, 226, 3, $muted);
        $y = 314;
        foreach ($actions as $i => $action) {
            $fill = $i === 0 ? $green : $blue;
            img_rect($img, $x + 22, $y, $x + 298, $y + 42, $fill, null);
            img_text($img, $action, $x + 42, $y + 14, 3, $white);
            $y += 70;
        }
    }

    save_png($img, $path);
}

function flow_screen(string $path): void
{
    $img = base_canvas(1260, 560);
    $blue = imagecolorallocate($img, 219, 234, 254);
    $blueText = imagecolorallocate($img, 30, 64, 175);
    $green = imagecolorallocate($img, 220, 252, 231);
    $greenText = imagecolorallocate($img, 22, 101, 52);
    $orange = imagecolorallocate($img, 255, 237, 213);
    $orangeText = imagecolorallocate($img, 154, 52, 18);
    $line = imagecolorallocate($img, 148, 163, 184);
    $dark = imagecolorallocate($img, 7, 19, 48);
    $white = imagecolorallocate($img, 255, 255, 255);

    img_text($img, 'Ticket Movement and Status Change', 40, 32, 5, $dark);

    $steps = [
        ['User', 'Create Ticket', 'In Progress'],
        ['State Admin', 'Review / Forward', 'In Progress'],
        ['NIC Admin', 'Forward / Assign', 'In Progress'],
        ['Senior Developer', 'Assign Developer', 'In Progress'],
        ['Developer', 'Work / Complete', 'In Progress'],
        ['State Admin', 'Resolve', 'Resolved'],
        ['User', 'Ticket Closed View', 'Resolved'],
    ];

    $x = 40;
    foreach ($steps as $i => $step) {
        $fill = $step[2] === 'Resolved' ? $green : $blue;
        $txt = $step[2] === 'Resolved' ? $greenText : $blueText;
        img_rect($img, $x, 126, $x + 150, 230, $fill, $line);
        img_text($img, $step[0], $x + 14, 146, 3, $dark);
        img_text($img, $step[1], $x + 14, 172, 2, $dark);
        img_badge($img, $step[2], $x + 12, 196, $white, $txt);
        if ($i < count($steps) - 1) {
            imageline($img, $x + 150, 178, $x + 178, 178, $line);
            imageline($img, $x + 178, 178, $x + 168, 170, $line);
            imageline($img, $x + 178, 178, $x + 168, 186, $line);
        }
        $x += 178;
    }

    img_rect($img, 40, 300, 1220, 470, $orange, $line);
    img_text($img, 'Need Clarification stages', 62, 326, 5, $orangeText);
    img_text($img, 'Returned to User, Returned to Senior Developer, Returned to NIC Admin, and Returned to State Admin', 62, 360, 4, $dark);
    img_text($img, 'are shown to users as Need Clarification. The Currently With column tells who must act next.', 62, 394, 4, $dark);

    save_png($img, $path);
}

function filters_screen(string $path): void
{
    $img = base_canvas(1180, 520);
    $blue = imagecolorallocate($img, 86, 119, 235);
    $white = imagecolorallocate($img, 255, 255, 255);
    $line = imagecolorallocate($img, 213, 224, 238);
    $muted = imagecolorallocate($img, 82, 99, 123);
    $dark = imagecolorallocate($img, 7, 19, 48);
    $soft = imagecolorallocate($img, 241, 245, 249);

    img_text($img, 'Ticket Details Filters', 40, 32, 5, $dark);
    img_rect($img, 40, 92, 1140, 190, $white, $line);
    $filters = [
        ['Search', 'Ticket no, subject, module, user'],
        ['Priority', 'Low / Medium / High / Critical'],
        ['Status', 'In Progress / Need Clarification / Resolved'],
        ['Scope', 'All / Assigned / Returned From NIC / Important'],
        ['Created By', 'State Admin can filter user-created tickets'],
        ['Developer', 'NIC Admin can filter assigned developer'],
        ['Dev Status', 'In Process / Need Clarification / Completed'],
    ];

    $x = 60;
    $y = 220;
    foreach ($filters as $i => $filter) {
        $col = $i % 2;
        $row = intdiv($i, 2);
        $fx = $x + ($col * 540);
        $fy = $y + ($row * 70);
        img_rect($img, $fx, $fy, $fx + 500, $fy + 50, $soft, $line);
        img_text($img, $filter[0], $fx + 14, $fy + 10, 3, $dark);
        img_text($img, $filter[1], $fx + 126, $fy + 10, 3, $muted);
    }
    img_rect($img, 890, 120, 990, 158, $blue, null);
    img_text($img, 'Filter', 920, 134, 3, $white);
    img_rect($img, 1002, 120, 1102, 158, $white, $line);
    img_text($img, 'Reset', 1036, 134, 3, $muted);

    save_png($img, $path);
}

$screenshots = [
    'create-ticket.png' => 'create_ticket_screen',
    'ticket-details-grid.png' => 'ticket_grid_screen',
    'ticket-actions.png' => 'actions_screen',
    'ticket-flow.png' => 'flow_screen',
    'ticket-filters.png' => 'filters_screen',
];

foreach ($screenshots as $file => $callback) {
    $callback($screenshotDir.DIRECTORY_SEPARATOR.$file);
}

function x(string $text): string
{
    return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function p(string $text, string $style = 'Body'): string
{
    $styleXml = $style === 'Body' ? '' : '<w:pPr><w:pStyle w:val="'.x($style).'"/></w:pPr>';
    return '<w:p>'.$styleXml.'<w:r><w:t xml:space="preserve">'.x($text).'</w:t></w:r></w:p>';
}

function bullet(string $text): string
{
    return '<w:p><w:pPr><w:pStyle w:val="ListBullet"/></w:pPr><w:r><w:t xml:space="preserve">'.x($text).'</w:t></w:r></w:p>';
}

function page_break(): string
{
    return '<w:p><w:r><w:br w:type="page"/></w:r></w:p>';
}

function image_paragraph(int $rid, string $name, int $cx = 5486400, int $cy = 3291840): string
{
    $id = 100 + $rid;
    return '<w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:drawing><wp:inline distT="0" distB="0" distL="0" distR="0"><wp:extent cx="'.$cx.'" cy="'.$cy.'"/><wp:docPr id="'.$id.'" name="'.x($name).'"/><wp:cNvGraphicFramePr><a:graphicFrameLocks xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" noChangeAspect="1"/></wp:cNvGraphicFramePr><a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"><a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"><pic:nvPicPr><pic:cNvPr id="'.$id.'" name="'.x($name).'"/><pic:cNvPicPr/></pic:nvPicPr><pic:blipFill><a:blip r:embed="rId'.$rid.'"/><a:stretch><a:fillRect/></a:stretch></pic:blipFill><pic:spPr><a:xfrm><a:off x="0" y="0"/><a:ext cx="'.$cx.'" cy="'.$cy.'"/></a:xfrm><a:prstGeom prst="rect"><a:avLst/></a:prstGeom></pic:spPr></pic:pic></a:graphicData></a:graphic></wp:inline></w:drawing></w:r></w:p>';
}

function table(array $headers, array $rows): string
{
    $xml = '<w:tbl><w:tblPr><w:tblStyle w:val="TableGrid"/><w:tblW w:w="9360" w:type="dxa"/><w:tblBorders><w:top w:val="single" w:sz="4" w:color="DADCE0"/><w:left w:val="single" w:sz="4" w:color="DADCE0"/><w:bottom w:val="single" w:sz="4" w:color="DADCE0"/><w:right w:val="single" w:sz="4" w:color="DADCE0"/><w:insideH w:val="single" w:sz="4" w:color="DADCE0"/><w:insideV w:val="single" w:sz="4" w:color="DADCE0"/></w:tblBorders><w:tblCellMar><w:top w:w="80" w:type="dxa"/><w:left w:w="120" w:type="dxa"/><w:bottom w:w="80" w:type="dxa"/><w:right w:w="120" w:type="dxa"/></w:tblCellMar></w:tblPr>';
    $xml .= '<w:tr>';
    foreach ($headers as $header) {
        $xml .= '<w:tc><w:tcPr><w:shd w:fill="E8EEF5"/></w:tcPr>'.p($header, 'TableHeader').'</w:tc>';
    }
    $xml .= '</w:tr>';
    foreach ($rows as $row) {
        $xml .= '<w:tr>';
        foreach ($row as $cell) {
            $xml .= '<w:tc>'.p((string) $cell, 'TableBody').'</w:tc>';
        }
        $xml .= '</w:tr>';
    }
    $xml .= '</w:tbl>'.p('');
    return $xml;
}

$body = '';
$body .= p('Fresh Helpdesk Ticket User Manual', 'Title');
$body .= p('Create ticket, status movement, details screen, filters, and role-wise actions', 'Subtitle');
$body .= p('This document explains how Fresh Helpdesk tickets are created, how each status changes, what each status means, what details are shown in the Ticket Details page, and how filters work.');
$body .= p('Screenshot note: Images in this document are sample screenshots created from the current application screens and labels. They do not contain live ticket/user data.');

$body .= p('1. Who Can Create Tickets', 'Heading1');
$body .= p('Tickets can be created by User, State Admin, and NIC Admin roles. A normal user-created ticket is first auto-forwarded to State Admin. State Admin can then forward it to NIC Admin when NIC action is required.');
$body .= table(['Role', 'Can Create Ticket', 'Initial Currently With'], [
    ['User', 'Yes', 'State Admin'],
    ['State Admin', 'Yes', 'NIC Admin'],
    ['NIC Admin', 'Yes', 'NIC Admin'],
    ['Senior Developer / Developer', 'No', 'Not applicable'],
]);

$body .= p('2. Create Ticket Screen', 'Heading1');
$body .= image_paragraph(1, 'Create Ticket Screen', 5486400, 3474720);
$body .= p('Required fields in the Create Ticket page are Department, Financial Year, Audit Quarter, Type, Ticket Scope, Priority, Category, Subject, and Description. Attachments are optional and support JPEG, PNG, and PDF files up to 500 KB per file.');
$body .= table(['Field', 'Meaning / Guidance'], [
    ['Department', 'Department related to the ticket. For normal users it is taken from the login session.'],
    ['Financial Year', 'Financial year linked with the ticket.'],
    ['Audit Quarter', 'Audit quarter or plan mapping related to the issue.'],
    ['Type', 'Support, New Feature, Bug / Issue, or Data Correction.'],
    ['Ticket Scope', 'Specified means limited to selected context. All means common/all-department scope.'],
    ['Priority', 'Low, Medium, High, or Critical. Use Critical only for blocking/urgent issues.'],
    ['Category', 'Module or issue category.'],
    ['Subject', 'Short searchable title for the ticket.'],
    ['Description', 'Clear details: screen name, exact issue, error, expected result, actual result, steps.'],
    ['Watchlist', 'Marks ticket as Important and notifies NIC Admin.'],
]);

$body .= page_break();
$body .= p('3. Ticket Movement Flow', 'Heading1');
$body .= image_paragraph(4, 'Ticket Movement Flow', 5486400, 2438400);
$body .= p('Normal user tickets move through State Admin first. NIC Admin can then return to State Admin, forward to Senior Developer, or assign directly to Developer.');
$body .= table(['Path', 'Movement'], [
    ['User to NIC Admin', 'User creates ticket -> State Admin reviews -> State Admin forwards to NIC Admin -> NIC Admin returns to State Admin -> State Admin resolves and returns to User.'],
    ['User to Developer through Senior', 'User -> State Admin -> NIC Admin -> Senior Developer -> Developer -> Senior Developer -> NIC Admin -> State Admin -> User.'],
    ['User to Developer direct', 'User -> State Admin -> NIC Admin -> Developer -> Senior Developer if available, otherwise NIC Admin -> State Admin -> User.'],
]);

$body .= p('4. Status Meaning', 'Heading1');
$body .= table(['Status Shown', 'Meaning', 'Typical Stored Status Values'], [
    ['In Progress', 'Ticket is active and currently with one role for action.', 'in_progress, pending_nic_admin, pending_senior_dev, pending_developer'],
    ['Need Clarification', 'Ticket was returned to a previous role/user for clarification, review, or next action.', 'returned_user, returned_state_admin, returned_nic_admin, returned_senior_dev, need_clarification'],
    ['Resolved', 'Ticket is completed/closed from workflow side and returned to user.', 'resolved, closed'],
]);

$body .= p('5. Ticket Details Grid', 'Heading1');
$body .= image_paragraph(2, 'Ticket Details Grid', 5486400, 3133440);
$body .= table(['Column', 'What It Shows'], [
    ['S.No', 'Serial number in the grid.'],
    ['Ticket / Created By', 'Ticket number and created user name.'],
    ['Ticket Details', 'Description/summary of the ticket.'],
    ['Priority', 'Priority badge: Low, Medium, High, or Critical.'],
    ['Type', 'Support, Bug / Issue, New Feature, or Data Correction.'],
    ['Subject', 'Ticket subject.'],
    ['Status', 'User-friendly status badge.'],
    ['Currently With', 'Role/person who must act next. This is the most important workflow column.'],
    ['Created On', 'Ticket created date/time.'],
    ['Dev Status', 'Developer work status if developer tracking is enabled.'],
    ['Updated On', 'Last update date/time.'],
    ['Action', 'View/open ticket detail action.'],
]);

$body .= page_break();
$body .= p('6. Filters In Ticket Details', 'Heading1');
$body .= image_paragraph(5, 'Ticket Details Filters', 5486400, 2415540);
$body .= table(['Filter', 'How It Works'], [
    ['Search', 'Searches ticket number, subject, module/details, or user text.'],
    ['Priority', 'Shows only tickets with selected priority.'],
    ['Status', 'Shows In Progress, Need Clarification, or Resolved tickets.'],
    ['Assigned / Forwarded', 'Shows tickets currently assigned or forwarded to the logged-in role/user.'],
    ['Returned From NIC', 'State Admin-only filter. Shows tickets returned by NIC Admin to State Admin.'],
    ['Important', 'State Admin/NIC Admin filter. Shows tickets marked as important/watchlist.'],
    ['Created By', 'State Admin can filter by ticket creator/user.'],
    ['Developer', 'NIC Admin can filter tickets assigned to a selected developer.'],
    ['Developer Status', 'Filters by In Process, Need Clarification, or Completed developer status.'],
    ['Filter Button', 'Applies selected filters and refreshes the grid.'],
    ['Reset / Clear', 'Clears filter values and returns to default ticket list.'],
]);

$body .= p('7. Ticket Actions By Role', 'Heading1');
$body .= image_paragraph(3, 'Ticket Action Panels', 5486400, 3596640);
$body .= table(['Role', 'Available Actions', 'Guidance'], [
    ['State Admin', 'Return to User, Forward to NIC Admin, Resolve and Return to User', 'State Admin screens user tickets and closes tickets after NIC/developer response.'],
    ['NIC Admin', 'Return to State Admin, Forward to Senior Developer, Assign Directly to Developer, Update Status', 'NIC Admin controls technical routing.'],
    ['Senior Developer', 'Assign Developer, Return to NIC Admin', 'Senior Developer reviews and assigns work to a developer.'],
    ['Developer', 'Update Developer Status, Return to Senior Developer', 'Developer must mark Completed before returning to Senior Developer.'],
    ['User', 'Return to State Admin, Reopen after resolved', 'User can return or reopen when the ticket is back with user/resolved.'],
]);

$body .= p('8. Developer Status Meaning', 'Heading1');
$body .= table(['Developer Status', 'Meaning', 'Forward Rule'], [
    ['In Process', 'Developer is working on the ticket.', 'Cannot return to Senior Developer yet.'],
    ['Need Clarification', 'Developer needs more details.', 'Cannot return to Senior Developer from this status.'],
    ['Completed', 'Developer work is finished.', 'Can return to Senior Developer.'],
]);

$body .= p('9. Practical User Guidance', 'Heading1');
$body .= bullet('Always mention the screen name and exact error message.');
$body .= bullet('Add steps to reproduce for bugs.');
$body .= bullet('Attach screenshots or PDF proof when possible.');
$body .= bullet('Use Critical priority only for urgent blocking issues.');
$body .= bullet('Check the Currently With column to know who must act next.');
$body .= bullet('Use Need Clarification status to identify tickets returned for more input.');

$styles = <<<'XML'
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:style w:type="paragraph" w:default="1" w:styleId="Normal"><w:name w:val="Normal"/><w:pPr><w:spacing w:after="120" w:line="264" w:lineRule="auto"/></w:pPr><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="22"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="Title"><w:name w:val="Title"/><w:pPr><w:spacing w:after="120"/></w:pPr><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:b/><w:color w:val="0B2545"/><w:sz w:val="40"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="Subtitle"><w:name w:val="Subtitle"/><w:pPr><w:spacing w:after="240"/></w:pPr><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:color w:val="52637B"/><w:sz w:val="24"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="Heading 1"/><w:pPr><w:keepNext/><w:spacing w:before="320" w:after="160"/></w:pPr><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:b/><w:color w:val="2E74B5"/><w:sz w:val="32"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="TableHeader"><w:name w:val="Table Header"/><w:pPr><w:spacing w:after="40"/></w:pPr><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:b/><w:sz w:val="19"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="TableBody"><w:name w:val="Table Body"/><w:pPr><w:spacing w:after="40" w:line="252" w:lineRule="auto"/></w:pPr><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="19"/></w:rPr></w:style>
  <w:style w:type="paragraph" w:styleId="ListBullet"><w:name w:val="List Bullet"/><w:pPr><w:spacing w:after="80"/><w:ind w:left="360" w:hanging="180"/></w:pPr><w:rPr><w:rFonts w:ascii="Calibri" w:hAnsi="Calibri"/><w:sz w:val="22"/></w:rPr></w:style>
</w:styles>
XML;

$documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
    .'<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas" xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:wp14="http://schemas.microsoft.com/office/word/2010/wordprocessingDrawing" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml" xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup" xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk" xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml" xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape" mc:Ignorable="w14 wp14">'
    .'<w:body>'.$body.'<w:sectPr><w:pgSz w:w="12240" w:h="15840"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="708" w:footer="708" w:gutter="0"/></w:sectPr></w:body></w:document>';

$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/create-ticket.png"/><Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/ticket-details-grid.png"/><Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/ticket-actions.png"/><Relationship Id="rId4" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/ticket-flow.png"/><Relationship Id="rId5" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/ticket-filters.png"/></Relationships>';

$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Default Extension="png" ContentType="image/png"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/><Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/></Types>';
$packageRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>';

if (is_file($docxPath)) {
    unlink($docxPath);
}

$zip = new ZipArchive();
$zip->open($docxPath, ZipArchive::CREATE);
$zip->addFromString('[Content_Types].xml', $contentTypes);
$zip->addFromString('_rels/.rels', $packageRels);
$zip->addFromString('word/document.xml', $documentXml);
$zip->addFromString('word/styles.xml', $styles);
$zip->addFromString('word/_rels/document.xml.rels', $rels);
foreach (array_keys($screenshots) as $file) {
    $zip->addFile($screenshotDir.DIRECTORY_SEPARATOR.$file, 'word/media/'.$file);
}
$zip->close();

echo "Created: {$docxPath}\n";
echo "Screenshots: {$screenshotDir}\n";
