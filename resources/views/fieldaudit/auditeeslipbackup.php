@section('content')
@extends('index2')
@include('common.alert')
<?php
$instdel = json_decode($inst_details, true);

if (count($instdel)) {
    $auditscheduleid = $instdel[0]['auditscheduleid'];
    $schteammemberid = $instdel[0]['schteammemberid'];
    $auditplanid = $instdel[0]['auditplanid'];
    $instid = $instdel[0]['instid'];
    $teamheaduserid = $teamheadid;
} else {
    $auditscheduleid = '';
    $schteammemberid = '';
    $auditplanid = '';
    $instid = '';
    $teamheaduserid = '';
}

?>
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/daterangepicker/daterangepicker.css">
<style>
    .ck-editor__editable {
        max-height: 300px;
        /* Set the max height as per your requirement */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }


    .ck-powered-by-balloon {
        display: none !important;
    }

    #container {
        width: 1000px;
        margin: 20px auto;
    }

    .ck-editor__editable[role="textbox"] {
        min-height: 200px;
    }

    .ck-editor__editable {
        font-family: 'Marutham', sans-serif;
    }

    .content-cell {
        display: -webkit-box;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
        /* Show only 2 lines */
        overflow: hidden;
        text-overflow: ellipsis;
        height: 40px;
        /* Adjust this based on your line height */
        line-height: 20px;
        /* Set this to match your text height */
        white-space: normal;
        /* Allow wrapping */
    }

    /* @font-face {
                   font-family: 'Marutham';
                   src: url('path/to/marutham.ttf') format('truetype');
                   } */
    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    .card-body {
        padding: 15px 10px;
    }

    .card {
        margin-bottom: 10px;
    }

    /* Step Circle Style */
    .step-circle {
        display: inline-block;
        width: 25px;
        height: 25px;
        line-height: 20px;
        text-align: center;
        border-radius: 50%;
        background-color: #fff;
        color: #0d6efd;
        font-weight: bold;
        /* position: absolute; */
        top: -10px;
        left: 10px;
        font-size: 14px;
        border: 2px solid #0d6efd;
    }

    /* Mobile View Adjustments */
    @media (max-width: 768px) {

        /* Make the navigation stack vertically on smaller screens */
        .nav-pills .nav-item {
            width: 100%;
            text-align: left;
            margin-bottom: 10px;
        }

        /* Adjust the .nav-link to display block on mobile */
        .nav-pills .nav-link {
            display: block;
            padding-left: 40px;
            /* Ensure the text doesn't overlap with the circle */
        }

        /* Adjust the circle position and size */
        .step-circle {
            position: relative;
            top: 0;
            left: 0;
            margin-right: 10px;
            font-size: 16px;
            display: inline-block;
        }

        /* Adjust the tab content for smaller screens */
        .tab-content {
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Make the 3rd and 4th steps appear in separate rows */
        .tab-content .row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .tab-pane .col-md-6 {
            width: 100%;
        }
    }

    /* Small screens, stack elements even more */
    @media (max-width: 576px) {
        .nav-pills .nav-item {
            width: 100%;
            text-align: left;
        }

        .nav-pills .nav-link {
            display: flex;
            align-items: center;
            padding-left: 40px;
            /* Keeps the circle alignment */
        }

        .step-circle {
            margin-right: 10px;
            font-size: 18px;
            top: 0;
            left: 0;
            display: inline-block;
        }

        /* Adjust the tab content padding for small screens */
        .tab-content {
            padding-left: 15px;
            padding-right: 15px;
        }

        /* Adjust rows in tab content to display properly on mobile */
        .tab-pane .row {
            display: flex;
            flex-direction: column;
        }

        .tab-pane .col-md-6 {
            width: 100%;
            /* Ensure full width for each column */
        }
    }

    /* For larger screens, keep the default horizontal nav-pills layout */
    @media (min-width: 992px) {
        .nav-pills .nav-item {
            width: auto;
            /* Revert the width to auto for large screens */
        }

        .nav-pills .nav-link {
            display: inline-block;
            /* Horizontal layout */
        }

        .step-circle {
            margin-right: 10px;
            font-size: 16px;
            top: -10px;
            left: 10px;
        }
    }

    .wizard .nav-link {
        font-weight: bold;
        border: 1px solid #dee2e6;
        margin: 0 5px;
        border-radius: 5px;
    }

    .active_div {
        background-color: rgb(164, 205, 248) !important;
    }

    .drop_div {
        background-color: #cef0be !important
    }

    .contopara_div {
        background-color: rgb(238, 164, 164) !important;

    }

    .wizard .nav-link.active {
        background-color: #0d6efd;
        color: #fff;
    }
</style>
<?php
if (count($instdel)) { ?>
    <div class="row">
        <div class="col-12">
            <div class="card" style="border-color: #7198b9">
                <div class="card-body">
                    <div class="card card_border">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3"></div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label required" for="validationDefault01">Plan Period</label>
                                    <input type="text" class="form-control" id="total_mandays" name="total_mandays"
                                        value="<?php echo $instdel[0]['typeofauditename']; ?>" disabled>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label required" for="validationDefault01">Year</label>
                                    <input type="text" class="form-control" id="total_mandays" name="total_mandays"
                                        value="<?php echo $instdel[0]['yearname']; ?>" disabled>
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label required" for="validationDefault01">Team Head Name</label>
                                    <input type="text" class="form-control" id="total_mandays" name="total_mandays"
                                        value="<?php echo $instdel[0]['username']; ?>" disabled>
                                </div>
                                <div class="col-md-3 mb-3"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="hstack mb-2">
                            <div class="div ms-2">
                                <span class="badge active_div">
                                </span>
                                <span class="">Selected slip</span>
                            </div>
                            <div class="div ms-2 ">
                                <span class="badge drop_div">
                                </span>
                                <span class="">Dropped</span>
                            </div>
                            <div class="div ms-2">
                                <span class="badge contopara_div">
                                </span>
                                <span class="">Converted to para</span>
                            </div>
                        </div>
                        <div class="col-md-1 d-none d-lg-block border-end user-chat-box">
                            <div class="position-relative mb-2">
                                <!-- <div class="dropdown">
                                            <a class="text-light fs-5 nav-icon-hover btn btn-sm bg-info" href="javascript:void(0)"
                                                role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Filter <i class="ti ti-adjustments"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 border-bottom"
                                                        href="javascript:void(0)">
                                                        <span>
                                                            <i class="ti ti-settings fs-4"></i>
                                                        </span>All
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 border-bottom"
                                                        href="javascript:void(0)">
                                                        <span>
                                                            <i class="ti ti-arrow-forward fs-4"></i>
                                                        </span>Forwarded
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2"
                                                        href="javascript:void(0)">
                                                        <span>
                                                            <i class="ti ti-clock fs-4"></i>
                                                        </span>Pending
                                                    </a>
                                                </li>
                                            </ul>
                                        </div> -->

                                <div class="dropdown">
                                    <a class="text-light fs-4 nav-icon-hover btn btn-sm bg-info" href="javascript:void(0)"
                                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Filter <i class="ti ti-adjustments"></i>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 border-bottom"
                                                href="javascript:void(0)" onclick="getSlipBasedOnfilter('A')">
                                                <span>
                                                    <i class="ti ti-settings fs-4"></i>
                                                </span>All
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2 border-bottom"
                                                href="javascript:void(0)" onclick="getSlipBasedOnfilter('F')">
                                                <span>
                                                    <i class="ti ti-arrow-forward fs-4"></i>
                                                </span>Forwarded
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                href="javascript:void(0)" onclick="getSlipBasedOnfilter('P')">
                                                <span>
                                                    <i class="ti ti-clock fs-4"></i>
                                                </span>Pending
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center gap-2"
                                                href="javascript:void(0)" onclick="getSlipBasedOnfilter('C')">
                                                <span>
                                                    <i class="ti ti-checkbox fs-4"></i>
                                                </span>Completed
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="pt-1">
                                <div class="position-relative mb-4">
                                    <input type="text" class="form-control search-chat py-2 " id="text-srh"
                                        placeholder="Search" />
                                </div>
                            </div>
                            <div class="app-chat">
                                <div class="overflow-auto card mb-0 shadow-none border h-150">
                                    <ul class="chat-users mb-0 mh-n100" data-simplebar>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-11">
                            <!-- <B><span id="forwardedby" class="text-end"></span></B> -->
                            <div class="row">
                                <div class="col-md-4">
                                    <b><span id="forwardedby" class="text-end"></span></b>
                                </div>
                                <div class="col-md-4">
                                    <!-- Optional: You can place another item here if needed -->
                                    <b><span id="mainslipnumber" class="text-end"></span></b>
                                </div>
                                <div class="col-md-4 mb-1 d-flex justify-content-end ">
                                    <span class="badge fs-2 bg-danger text-light rounded-pill blinking-badge"
                                        id="processname"></span>
                                </div>
                            </div>
                            <div class="card" style="border-color: #7198b9">
                                <div class="card-header card_header_color">Auditor Observation</div>
                                <div class="card-body">
                                    <div id="auditslipcard">
                                    </div>
                                    <div id="viewauditslipcard" class="hide_this">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label required lang" for="validationDefaultUsername"
                                                    key="major_obj">Title/Heading</label>
                                                <select class="select2 form-control custom-select"
                                                    id="view_majorobjectioncode" name="view_majorobjectioncode"
                                                    onchange="getminorobjection()" disabled>
                                                    <option value="">Select Major Objection
                                                    </option>
                                                    @foreach ($get_majorobjection as $ob)
                                                    <option value="{{ $ob->mainobjectionid }}">
                                                        {{ $ob->objectionename }}
                                                        <!-- Display any field you need -->
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-4 ">
                                                <label class="form-label required"
                                                    for="validationDefaultUsername">Categorization of paras</label>
                                                <input type="text" id="view_minorobjectioncode"
                                                    name="view_minorobjectioncode" class="form-control" disabled>
                                            </div>
                                            <div class="col-md-4 ">
                                                <label class="form-label " for="validationDefaultUsername">Amount
                                                    Involved</label>
                                                <input type="text" class="form-control" id="view_amount_involved"
                                                    name="view_amount_involved" placeholder="50,000" disabled>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-md-4 ">
                                                <label class="form-label required"
                                                    for="validationDefaultUsername">Severity</label>
                                                <select class="select2 form-control custom-select" id="view_severityid"
                                                    name="view_severityid" disabled>
                                                    <option value='' data-name-en="Select Severity"
                                                        data-name-ta="தெரிவு கடைசியாக"></option>
                                                    @if (!empty($severitydel) && count($severitydel) > 0)
                                                    @foreach ($severitydel as $s)
                                                    <option value="{{ $s->severitycode }}"
                                                        data-name-en="{{ $s->severityelname }}"
                                                        data-name-ta="{{ $s->severitytlname }}">
                                                        {{ $s->severityelname }}
                                                    </option>
                                                    @endforeach
                                                    @endif
                                                </select>

                                            </div>
                                            <div class="col-md-4 ">
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-6">
                                                        <label class="form-label required"
                                                            for="validationDefaultUsername">
                                                            Liablility</label> <br>
                                                        <div class="d-flex align-items-center">
                                                            <div class="form-check form-check-inline">
                                                                <input class="view_form-check-input success"
                                                                    type="radio" name="view_liability" id="Y"
                                                                    value="Y" disabled>
                                                                <label class="form-check-label" for="all">Yes</label>
                                                            </div>
                                                            <div class="form-check form-check-inline">
                                                                <input class="view_form-check-input success"
                                                                    type="radio" name="view_liability" id="N"
                                                                    value="N" checked disabled>
                                                                <label class="form-check-label" for="district">No</label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <label class="form-label required" for="validationDefaultUsername">Slip
                                                    Details</label>
                                                <textarea id="view_slipdetails" name="view_slipdetails" class="form-control" placeholder="Enter remarks" disabled></textarea>
                                            </div>
                                        </div>
                                        <div id="dynamicRowsContainer"></div>


                                        <hr>
                                        <!-- <div class="row">
                                                    <div class="col-md-8">
                                                        <label class="form-label required" for="validationDefaultUsername">Auditor
                                                            Observation/Remarks</label>
                                                        <textarea id="view_auditorremarks" class="form-control" placeholder="Enter remarks" name="view_auditorremarks"></textarea>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label required" for="validationDefaultUsername">Auditor
                                                            Attachments</label>
                                                        <div class="container my-1" id="view_file-list-container">
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div> -->
                                        <div id="auditorAccordionsContainer"></div>
                                    </div>
                                </div>
                                <!-- <div class="row"> -->

                                <!-- </div> -->
                            </div>

                            <div class="card hide_this" style="border-color: #7198b9" id="auditeereplaycard">

                                <div class="card-header card_header_color">Auditee Reply</div>
                                <div class="card-body">
                                    <form id="auditslip" class="mt-2" name="auditslip">
                                        <input type="hidden" name="filter" id="filter" value='A'>

                                        <input type="hidden" name="seriesno" id="seriesno" value='1'>
                                        <input type="hidden" name="deactive_fileid" id="deactive_fileid">
                                        <input type="hidden" name="active_fileid" id="active_fileid">
                                        <input type="hidden" name="auditslipid" id="auditslipid">
                                        <input type="hidden" name="auditscheduleid" id="auditscheduleid"
                                            value="<?php echo $auditscheduleid; ?>">
                                        <div id="form_auditeereply" name="form_auditeereply">
                                            <input type="hidden" id="rejoinderstatus" name="rejoinderstatus">
                                            <input type="hidden" id="rejoindercycle" name="rejoindercycle">
                                            <div class="row">
                                                <div class="col-md-12" id="remarksedit_div">
                                                    <label class="form-label required"
                                                        for="validationDefaultUsername">Auditee
                                                        Reply</label>
                                                    <textarea id="auditeeremarks" class="form-control " placeholder="Enter remarks" name="auditeeremarks"></textarea>
                                                </div>
                                                <div class="col-md-12 " id="uploadfile_div">
                                                    <label class="form-label " for="validationDefaultUsername"> Upload
                                                        File</label>
                                                    <div class="row mb-2">
                                                        <!-- File Input Previews and Add Button Together -->
                                                        <div class="col-md-12 d-flex align-items-center">
                                                            <!-- File Previews Container -->
                                                            <div class="file-input-container" id="file-input-container">
                                                                <div id="file-list" class="d-flex flex-wrap gap-2"></div>
                                                                <template id="file-preview">
                                                                    <div class="position-relative text-center file-preview"
                                                                        style="width: 120px;">
                                                                        <div class="img-thumbnail">
                                                                            <img src="" height="100" />
                                                                            <div class="position-absolute text-white fs-1 text-wrap bg-dark p-1 small w-100"
                                                                                style="bottom: 0; left: 0; opacity: 0.8;">
                                                                                <span class="file-name">File Name</span> |
                                                                                <span class="file-size">0 KB</span>
                                                                            </div>
                                                                            <button style="top: 5px; right: 5px;"
                                                                                class="btn btn-sm btn-danger position-absolute"
                                                                                onclick="removeFilePreview(this)">
                                                                                <span aria-hidden="true">&times;</span>
                                                                            </button>
                                                                        </div>
                                                                        <input class="d-none" multiple="multiple"
                                                                            type="file" name="fileupload[]"
                                                                            accept=".pdf, .docx, .xlsx, .jpeg, .jpg, .png">
                                                                    </div>
                                                                </template>
                                                            </div>

                                                            <!-- Add File Button -->
                                                            <label for="upload_input" id="add-file-btn"
                                                                class="btn btn-success btn-sm ms-2">
                                                                <i class="fs-6 ti ti-plus"></i>
                                                            </label>
                                                            <input id="upload_input" type="file" name="fileupload[]"
                                                                class="d-none" multiple="multiple"
                                                                onchange="window.breakIntoSeparateFiles(this, '#file-list', '#file-preview')" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 hide_this" id="uploadedfile_div">
                                                    <label class="form-label required" for="validationDefaultUsername">
                                                        Attachment</label>
                                                    <div class="position-relative d-flex flex-row"
                                                        id="file-list-container">
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="row mt-4" id="showbtn">
                                                <div class="col-md-6" style="margin-left: 39% !important;">
                                                    <div class="d-flex align-items-center gap-6">
                                                        <input type="hidden" id="action" name="action"
                                                            value="insert">
                                                        <button class="btn button_save" id="buttonaction"
                                                            name="buttonaction">Save
                                                            / Draft</button>
                                                        <button class="btn button_finalise"
                                                            id="forwardbtn">Forward</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
} else { ?>
    <div class="card " style="border-color: #7198b9">
        <div class="card-header card_header_color">Slip Details</div>
        <div class="card-body">
            <br>
            <center>No Data Available</center>
        </div>
    </div>
    </div>
<?php
} ?>
<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/apps/chat.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/super-build/ckeditor.js"></script>
<script>
    /*************************************************  Audit Tab Functions *********************************************/

    var fileCount = 0;

    function removeFilePreview(button) {

        fileCount--;

        // Remove the file preview element
        $(button).closest('.file-preview').remove();
        if (fileCount >= 3) {
            $('#add-file-btn').hide();
        } else {
            $('#add-file-btn').show();
        }

    }
    (function(window, $) {
        var FILE_ICON_URL = 'https://icons.iconarchive.com/icons/zhoolego/material/512/Filetype-Docs-icon.png';
        var ALLOWED_TYPES = ['application/pdf', 'image/jpeg', 'image/png', 'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        var MAX_FILE_SIZE_MB = 3;
        var MAX_FILE_COUNT = 3;

        function addFileToNewInput(file, newInput) {
            if (!newInput) {
                return;
            }

            var dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            newInput.files = dataTransfer.files;
        }

        function setFileDetails(file, $previewElement) {
            // Populate file name
            $previewElement.find('.file-name').text(file.name);
            // Populate file size in KB with 2 decimal precision
            var fileSizeInKB = (file.size / 1024).toFixed(2) + ' KB';
            $previewElement.find('.file-size').text(fileSizeInKB);
        }

        function addSrcToPreview(file, preview) {
            if (!preview) {
                return;
            }

            if (file.type.match(/image/)) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
                $('.file-preview').show();
            } else {
                preview.src = FILE_ICON_URL;
                $('.file-preview').show();
            }

        }

        function showError(message, $previewElement) {
            var $errorElement = $previewElement.find('.file-error');
            if ($errorElement.length === 0) {
                $errorElement = $('<div class="file-error" style="color: red; font-size: 12px;"></div>').appendTo(
                    $previewElement);
            }
            $errorElement.text(message);
        }

        // function breakIntoSeparateFiles(input, targetSelector, templateSelector) {

        //     $('#file-input-container').show();
        //     var $input = $(input);

        //     var templateHtml = $(templateSelector).html();

        //     if (!input.files) {
        //         return;
        //     }

        //     var existingFileCount = $(targetSelector).children().length; // Existing file previews
        //     var newFileCount = input.files.length; // New files being added
        //     fileCount += newFileCount;

        //     if (fileCount > MAX_FILE_COUNT) {
        //         alert('Only ' + MAX_FILE_COUNT + ' files are allowed.');
        //         return;
        //     }

        //     for (var file of input.files) {
        //         var $newFile = $(templateHtml).appendTo(targetSelector);
        //         var errorMessage = '';

        //         // Validate file type
        //         if (!ALLOWED_TYPES.includes(file.type)) {
        //             errorMessage = 'Invalid type';
        //         }

        //         // Validate file size
        //         if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
        //             alert('File Size Exceeds');
        //             // return;
        //         }

        //         // Ensure total files do not exceed the limit
        //         if (fileCount > MAX_FILE_COUNT) {
        //             errorMessage = 'Max files limit';
        //         }

        //         if (errorMessage) {
        //             setFileDetails(file, $newFile);
        //             showError(errorMessage, $newFile);
        //             $newFile.find('img').remove(); // Remove preview image since the file is invalid
        //             continue;
        //         }
        //         if (fileCount >= 3) {
        //             $('#add-file-btn').hide();
        //         } else {
        //             $('#add-file-btn').show();
        //         }
        //         processFile(file, $newFile);
        //             // fileCount++;
        //         }
        //         async function processFile(file, $newFile) {
        //             // Ensure this runs first
        //             await addFileToNewInput(file, $newFile.find("input")[0]);
        //             await addSrcToPreview(file, $newFile.find("img")[0]);
        //             await setFileDetails(file, $newFile);
        //         }
        //     $input.val([]);
        // }

        function breakIntoSeparateFiles(input, targetSelector, templateSelector) {
            $('#file-input-container').show();
            var $input = $(input);
            var templateHtml = $(templateSelector).html();

            if (!input.files) {
                return;
            }

            var existingFileCount = $(targetSelector).children().length; // Existing previews

            var newFiles = Array.from(input.files); // Convert FileList to array for easier processing
            var validFiles = [];

            // Validate files before appending
            newFiles.forEach(file => {
                // Check file type
                if (!ALLOWED_TYPES.includes(file.type)) {
                    alert('Invalid file type. Please upload PDF, Excel, JPG, JPEG, or PNG files only.');
                    return; // Don't increment fileCount here
                }

                // Check file size
                if (file.size > MAX_FILE_SIZE_MB * 1024 * 1024) {
                    alert('File size exceeds the 3MB limit.');
                    return; // Don't increment fileCount here
                }

                // alert(fileCount);
                // alert(existingFileCount);
                // alert(validFiles.length);
                // alert(existingFileCount + validFiles.length + fileCount);
                // Ensure total files do not exceed the limit
                if (validFiles.length + fileCount >= MAX_FILE_COUNT) {
                    alert('Max file limit reached.');
                    return; // Don't increment fileCount here
                }

                validFiles.push(file); // Only add to validFiles if passed validation
            });

            // If no valid files, exit
            if (validFiles.length === 0) {
                return;
            }

            // Append valid files and process them
            validFiles.forEach(file => {
                var $newFile = $(templateHtml).appendTo(targetSelector);

                // Process the file (async handling)
                processFile(file, $newFile).then(() => {
                    existingFileCount++; // Update count only after successful processing

                    // Hide or show the "Add File" button based on the limit
                    if (validFiles.length + fileCount >= MAX_FILE_COUNT) {
                        $('#add-file-btn').hide();
                    } else {
                        $('#add-file-btn').show();
                    }

                    // Increment fileCount only after successful processing
                    fileCount++;
                    //alert('addfile');
                    //alert(fileCount);
                });
            });
            //processFile(file, $newFile)
            ; // Clear input to allow re-selection of the same files
            $input.val([]);
        }


        async function processFile(file, $newFile) {
            await addFileToNewInput(file, $newFile.find("input")[0]);
            await addSrcToPreview(file, $newFile.find("img")[0]);
            await setFileDetails(file, $newFile);
        }

        window.breakIntoSeparateFiles = breakIntoSeparateFiles;
    })(window, jQuery);

    function appendFilePreview() {
        // Check if #file-list exists, if not, create it


        $('#file-input-container').append('<div id="file-list" class="mb-2 d-flex"></div>');

        $('#file-input-container').append(`
            <template id="file-preview">
                <div class="position-relative mr-3 text-center file-preview" style="width: min-content;">
                    <div class="img-thumbnail">
                        <img src="" height="70" />
                        <div class="position-absolute text-white fs-40 text-wrap bg-dark p-1 small w-100" style="bottom: 0; left: 0; opacity: 0.8;">
                            <span class="file-name fs-40">File Name</span> |
                            <span class="file-size fs-40">0 KB</span>
                        </div>
                        <button style="top: 1px; right: 1px;" class="btn btn-sm btn-danger position-absolute" onclick="removeFilePreview(this)">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <input class="d-none" multiple="multiple" type="file" name="fileupload[]">
                </div>
            </template>
        `);


    }

    /*************************************************  Ckeditor  *********************************************/

    let auditeeremarks;

    CKEDITOR.ClassicEditor.create(document.getElementById("auditeeremarks"), {
            toolbar: {
                items: [
                    'findAndReplace', 'selectAll', '|',
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'numberedList', '|',
                    'outdent', 'indent', '|',
                    'undo', 'redo',
                    'fontSize', 'fontFamily', '|',
                    'alignment', '|',
                    'uploadImage', 'insertTable',
                    '|',
                ],
                shouldNotGroupWhenFull: true
            },
            placeholder: 'Welcome to CAMS...',
            fontFamily: {
                options: [
                    'default', 'Marutham', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace',
                    'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif', 'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            fontSize: {
                options: [10, 12, 14, 'default', 18, 20, 22],
                supportAllValues: true
            },
            htmlSupport: {
                allow: [{
                    name: /.*/,
                    attributes: true,
                    classes: true,
                    styles: true
                }]
            },
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            removePlugins: [
                'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter', 'MultiLevelList',
                'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                'RealTimeCollaborativeRevisionHistory', 'PresenceList', 'Comments', 'TrackChanges',
                'TrackChangesData', 'RevisionHistory', 'Pagination', 'WProofreader',
                'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                'TableOfContents', 'PasteFromOfficeEnhanced', 'CaseChange'
            ]
        })
        .then(editor => {
            auditeeremarks = editor;
            // auditeeremarks.enableReadOnlyMode('initial');

            // Disable editing (make read-only)
            // view_editor.enableReadOnlyMode();
        })
        .catch(error => {
            console.error(error);
        });






    /*************************************************  Ckeditor  *********************************************/


    /***************************************************** Upload File - Preview *********************************/

    function importData() {
        // Dynamically create a file input element
        let input = document.createElement('input');
        input.type = 'file';

        // Handle file selection
        input.onchange = () => {
            let files = Array.from(input.files);

            // Synchronize the files with the original input
            document.getElementById('auditee_upload').files = input.files;

            // Pass the selected file to the previewAttachment function
            previewAttachment(input, 'upload_preview');
        };

        // Trigger the file dialog
        input.click();
    }

    function previewAttachment(input, previewDivId) {
        // Ensure a file is selected
        if (!input.files || input.files.length === 0) return;

        const file = input.files[0];
        const previewDiv = document.getElementById(previewDivId);

        // Clear the preview area
        previewDiv.innerHTML = "";

        if (file) {
            const fileType = file.type;
            $('#upload_preview').show();

            // Check if the uploaded file is an image
            if (fileType.startsWith("image/")) {
                const img = document.createElement("img");
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = "100%";
                img.style.maxHeight = "400px";

                previewDiv.appendChild(img);
            }
            // Check if the uploaded file is a PDF
            else if (fileType === "application/pdf") {
                const iframe = document.createElement("iframe");
                iframe.src = URL.createObjectURL(file);
                iframe.style.width = "100%";
                iframe.style.height = "400px";
                iframe.setAttribute("frameborder", "0");
                previewDiv.appendChild(iframe);
            }
            // Handle unsupported file types
            else {
                const message = document.createElement("p");
                message.textContent = "Unsupported file type. Please upload an image or a PDF.";
                previewDiv.appendChild(message);
            }
        }
    }

    /***************************************************** Upload File - Preview *********************************/



    ////////////////////////////////////// Search Audit Slip Number //////////////////////////////////////

    $(".search-chat").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".chat-users li").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    ////////////////////////////////////// Search Audit Slip Number //////////////////////////////////////



    function getSlipBasedOnfilter(filtervalue) {
        $('#filter').val(filtervalue);
        getauditslip('', 'fetch', '', filtervalue);
    }



    $(document).ready(function() {

        getauditslip('', 'fetch', '', $('#filter').val())
    });

    /*********************************************** Jqury Form Validation *******************************************/


    $("#auditslip").validate({
        rules: {
            auditeeremarks: {
                required: function() {
                    // Get content from CKEditor
                    let content = auditeeremarks.getData().trim();
                    return content === '' || content === '<p>&nbsp;</p>';
                }
            }
        },
        messages: {
            auditeeremarks: {
                required: "Please enter your remarks. This field cannot be empty."
            }
        }
    });

    /*********************************************** Jqury Form Validation *******************************************/


    /*********************************************** Insert,update,finalise,reset *******************************************/

    function removeAllEventListeners(element) {
        var newElement = element.cloneNode(true); // Clone the element (deep clone)
        element.parentNode.replaceChild(newElement, element); // Replace old element with new cloned element
    }



    // Event listener for the button to add a new slip number
    $("#forwardbtn").on("click", function() {
        event.preventDefault();
        // Trigger the form validation
        if ($("#auditslip").valid()) {
            if (!(auditeeremarks.getData())) {
                passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');
                return;
            }
            removeAllEventListeners(document.getElementById("process_button"));
            document.getElementById("process_button").onclick = function() {
                createslip('Y')
            };
            // Show confirmation alert
            passing_alert_value('Confirmation', 'Are you Sure to forward?', 'confirmation_alert',
                'alert_header',
                'alert_body', 'forward_alert');
        } else {}
    });

    $("#buttonaction").on("click", function(event) {
        // Prevent form submission (this stops the page from refreshing)
        event.preventDefault();

        if (!(auditeeremarks.getData())) {
            passing_alert_value('Alert', 'Enter the Remarks', 'confirmation_alert',
                'alert_header', 'alert_body', 'confirmation_alert');
            return;
        }
        removeAllEventListeners(document.getElementById("process_button"));

        //Trigger the form validation
        if ($("#auditslip").valid()) {
            createslip('N')
        } else {

        }
    });


    function createslip(finalise) {
        var formData = new FormData($('#auditslip')[0]); // Serialize form data, including files
        formData.append('finaliseflag', finalise);
        formData.append('teamheadid', <?php echo $teamheadid; ?>);
        // formData.append('fileuploadstatus', $('#fileuploadstatus').val());
        // formData.append('rejoinderstatus', $('#rejoinderstatus').val());

        formData.append('auditeeremarks_append', auditeeremarks.getData());



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                // 'Content-Type': 'application/x-www-form-urlencoded',
            }
        });


        $.ajax({
            url: '/auditeereply', // URL where the form data will be posted
            type: 'POST',
            data: formData,
            processData: false, // Disable automatic data processing
            contentType: false, // Let jQuery handle the content type for FormData
            success: function(response) {
                if (response.success) {
                    reset_form(); // Reset the form after successful submission
                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');

                    // table.ajax.reload(); // Reload the table with the new data
                    getauditslip(response.data['slid'], 'fetchwithdata', response.data['auditslipnumber'],
                        $('#filter').val())

                    // getauditslip(response.data['slid'], response.data['auditslipnumber'],
                    //             'fetchwithdata', 'Y', $('#filter').val());


                } else if (response.error) {
                    // Handle errors if needed
                    console.log(response.error);
                }
            },
            error: function(xhr, status, error) {

                var response = JSON.parse(xhr.responseText);

                var errorMessage = response.error ||
                    'An unknown error occurred';

                // // Extracting error details
                // var errorMessage = 'An error occurred.';

                // Optionally, you can check for the type of error
                // if (xhr.status === 400) {
                //     // Example of handling 400 Bad Request error
                //     errorMessage =  xhr.responseText;
                // } else if (xhr.status === 500) {
                //     // Example of handling 500 Internal Server Error
                //     errorMessage = 'Server error: ' + xhr.responseText;
                // } else {
                //     // You can include the status or other relevant details
                //     errorMessage = 'Error ' + xhr.status + ': ' + error;
                // }

                // Displaying the error message
                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');


                // Optionally, log the error to console for debugging
                console.error('Error details:', xhr, status, error);
            }
        });

    }



    // function changebuttonasinsert() {
    //     // change_button_as_insert('form_auditeereply', 'action', 'buttonaction', 'display_error', '', '');
    //     $('#action').val('insert');
    //     $("#buttonaction").html("Save Draft");
    //     document.getElementById('buttonaction').style.backgroundColor = "#b71362";
    // }

    // function changebuttonasupdate() {
    //     $('#action').val('update');
    //     $("#buttonaction").html("Update");
    //     document.getElementById('buttonaction').style.backgroundColor = "#0262af";

    //     // change_button_as_insert('auditslip', 'action', 'buttonaction', 'display_error', '', '');
    // }




    /*********************************************** Insert,update,finalise,reset *******************************************/




    /*********************************************** Fetch Data *******************************************/

    function getauditslip(slipid, action, fixid, filter) {
        $.ajax({
            url: '/getauditslip', // Your API route to get user details
            method: 'POST',
            data: {
                auditslipid: slipid,
                auditscheduleid: '<?php echo $auditscheduleid; ?>',
                action: action,
                filter: filter
            }, // Pass deptuserid in the data object
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            success: function(response) {
                if (response.success) {

                    $('#upload_preview').hide();
                    if (response.data && response.data.auditDetails.length > 0) {
                        // alert('if');
                        let firstItem = '';
                        let historydel = '';
                        let seriesnumber = Number($('#seriesno').val());
                        // Handle action fetch
                        if ((action === 'fetch') || (action === 'fetchwithdata')) {
                            $('#seriesno').val(1);
                            document.querySelector(".chat-users").innerHTML = '';

                            response.data.auditDetails.forEach((item) => {
                                addSlipNumber(item.tempslipnumber, item.encrypted_auditslipid, item
                                    .processcode);

                                // alert(fixid);
                                // alert(item.tempslipnumber)

                                if (fixid && fixid === item.tempslipnumber) {
                                    fixarrow = $('#seriesno').val() - 1;
                                    firstItem = item;

                                }
                            });


                            if (!fixid) {
                                firstItem = response.data.auditDetails[0];
                                fixarrow = seriesnumber;

                            }
                            // alert(fixarrow);
                            $('#arrow_' + fixarrow).css("visibility", "visible");
                            $('#' + fixarrow).addClass("active_div");



                        } else if (action == 'edit') {
                            firstItem = response.data.auditDetails[0];
                        }

                        // Display the selected audit slip details
                        if (firstItem) {
                            // alert('iffffffff');
                            $('#auditslipid').val(firstItem.encrypted_auditslipid);
                            show_view_card(firstItem);
                        }

                        console.log(firstItem);


                        showremarks(response.data.historydel);
                        set_auditeereplyform(firstItem)
                        appendFilePreview();

                    } else {
                        const chatUsersList = document.querySelector(".chat-users");
                        chatUsersList.innerHTML = '';
                        $('#auditorAccordionsContainer').empty();
                        $('#view_majorobjectioncode').val('');
                        $('#view_minorobjectioncode').val('');
                        $('#view_amount_involved').val('');
                        $('#view_severityid').val('');
                        $('#view_slipdetails').val('');
                        $('#dynamicRowsContainer').empty();
                        $('input[name="view_liability"][value="N"]').prop('checked', true);
                        $('#processname').html('');
                        $('#mainslipnumber').html('');
                    }

                } else {
                    alert('User not found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });
    }




    function showremarks(responseData) {
        $('#auditorAccordionsContainer').show()
        let container = document.getElementById("auditorAccordionsContainer");
        container.innerHTML = ""; // Clear existing content

        let editorIds = []; // Store CKEditor IDs
        let lastIndex = responseData.length - 1; // Get the last index

        responseData.forEach((data, index) => {
            let accordionId = `auditorAccordion${index}`;
            let collapseId = `collapse${index}`;
            let headerId = `heading${index}`;
            let editorId = `editor${index}`;
            editorIds.push(editorId); // Store editor ID for CKEditor initialization

            // Extract all file keys dynamically
            let fileList = "<li>No attachments</li>"; // Default message if no files

            if (data.file_details) {
                fileList = Object.keys(data)
                    .filter(key => key.startsWith("file_details")) // Filter only file keys
                    .map(key => {
                        return data[key]
                            .split(",") // Split multiple files
                            .map(file => {
                                let parts = file.split('-');
                                if (parts.length < 2) return ""; // Skip invalid entries

                                let [name, path] = parts; // Extract only necessary parts
                                return `<li><a href="/storage/${path}" target="_blank" style="color:black">${name}</a></li>`;
                            })
                            .join(""); // Join all file links
                    })
                    .join("") || "<li>No attachments</li>"; // Default message if no files
            }


            var values = {
                A: @json($A),
                I: @json($I)
            };

            // Dynamically get the value based on forwardedBy
            var forwardedby = values[data.forwardedbyusertypecode];
            var rejoinderstatus = data.rejoinderstatus;
            var rejoindercycle = data.rejoindercycle;

            let isLast = index === lastIndex; // Check if it's the last item


            // Create accordion element
            let accordion = document.createElement("div");
            accordion.classList.add("accordion", "my-2");
            accordion.id = accordionId;
            accordion.innerHTML = `
                 <div class="accordion-item">
                <h2 class="accordion-header" id="${headerId}">
                    <button class="hrem accordion-button ${isLast ? "" : "collapsed"}
                     ${forwardedby == 'Auditee'?"bg-success-subtle":"bg-primary-subtle"}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#${collapseId}"
                            aria-expanded="${isLast}"
                            aria-controls="${collapseId}">
                        ${forwardedby}
                                                ${ data.forwardedbyusertypecode === 'A' ? `Observation` : "Reply"}

                        ${rejoinderstatus === 'Y' ? `# Rejoinder ${rejoindercycle}` : ""}
                    </button>
                </h2>
                <div id="${collapseId}" class="accordion-collapse collapse ${isLast ? "show" : ""}" aria-labelledby="${headerId}">
                    <div class="accordion-body">
                        <div class="row">
                            <div class="col-md-8">
                                <label><strong>Remarks:</strong></label>
                                <div class="editor-container">
                                    <textarea id="${editorId}">${data.remarks || "No remarks provided"}</textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label><strong>Attachments:</strong></label>
                                <ul class="file-list" >${fileList}</ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;

            container.appendChild(accordion); // Append dynamically created accordion
        });

        // Initialize CKEditor after elements are created
        initializeEditors(editorIds);
    }



    function initializeEditors(editorIds) {
        if (!window.CKEDITOR || !window.CKEDITOR.ClassicEditor) {
            setTimeout(() => initializeEditors(editorIds), 100); // Retry if CKEditor is not yet loaded
            return;
        }

        editorIds.forEach(id => {
            let textarea = document.getElementById(id);
            if (textarea) {
                CKEDITOR.ClassicEditor.create(textarea, {
                        toolbar: {
                            items: [

                                'selectAll', '|',
                                // 'findAndReplace',
                                // 'heading', '|',
                                // 'bold', 'italic', 'underline', '|',
                                // 'numberedList', '|',
                                // 'outdent', 'indent', '|',
                                // 'undo', 'redo',
                                // 'fontSize', 'fontFamily', '|',
                                // 'alignment', '|',
                                // 'uploadImage', 'insertTable',
                                // '|',
                            ],
                            shouldNotGroupWhenFull: true
                        },
                        placeholder: 'Write Your Audit Observation here',
                        fontFamily: {
                            options: [
                                'default', 'Marutham', 'Arial, Helvetica, sans-serif',
                                'Courier New, Courier, monospace',
                                'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                                'Tahoma, Geneva, sans-serif',
                                'Times New Roman, Times, serif', 'Trebuchet MS, Helvetica, sans-serif',
                                'Verdana, Geneva, sans-serif'
                            ],
                            supportAllValues: true
                        },
                        fontSize: {
                            options: [10, 12, 14, 'default', 18, 20, 22],
                            supportAllValues: true
                        },
                        htmlSupport: {
                            allow: [{
                                name: /.*/,
                                attributes: true,
                                classes: true,
                                styles: true
                            }]
                        },
                        link: {
                            decorators: {
                                addTargetToExternalLinks: true,
                                defaultProtocol: 'https://',
                                toggleDownloadable: {
                                    mode: 'manual',
                                    label: 'Downloadable',
                                    attributes: {
                                        download: 'file'
                                    }
                                }
                            }
                        },
                        removePlugins: [
                            'AIAssistant', 'CKBox', 'CKFinder', 'EasyImage', 'Base64UploadAdapter',
                            'MultiLevelList',
                            'RealTimeCollaborativeComments', 'RealTimeCollaborativeTrackChanges',
                            'RealTimeCollaborativeRevisionHistory',
                            'PresenceList', 'Comments', 'TrackChanges', 'TrackChangesData',
                            'RevisionHistory', 'Pagination',
                            'WProofreader',
                            'MathType', 'SlashCommand', 'Template', 'DocumentOutline', 'FormatPainter',
                            'TableOfContents',
                            'PasteFromOfficeEnhanced', 'CaseChange'
                        ]
                    })
                    .then(editor => {
                        editor.enableReadOnlyMode('initial'); // Read-only mode
                    })
                    .catch(error => console.error(`Error initializing CKEditor 5 for ${id}:`, error));
            }
        });
    }






    function reset_form() {
        //$('#auditeereplaycard').hide();
        auditeeremarks.setData('');
        $('#file-input-container').empty();
        fileCount = 0;
        appendFilePreview();
    }



    function set_auditeereplyform(firstItem) {
        reset_form();



        if ((firstItem.processcode === 'F') || (firstItem.processcode === 'U')) {
            $('#auditeereplaycard').show();
            if (firstItem.processcode === 'F') {
                //changeButtonAction('auditslip','action','buttonaction','forwardbtn','display_error',@json($savedraftbtn), @json($forwardbtn),'insert')
                change_button_as_insert('auditslip', 'action', 'buttonaction', 'display_error', '');

            } else if (firstItem.processcode === 'U') {
                //changeButtonAction('auditslip','action','buttonaction','forwardbtn','display_error',@json($updatebtn), @json($forwardbtn),'update')
                change_button_as_update('auditslip', 'action', 'buttonaction', 'display_error', '');

                auditeeremarks.setData(firstItem.remarks);

                if (firstItem.auditeefileupload) {
                    const files = getfile(firstItem.auditeefileupload);
                    UploadedFileList(files, 'edit', 'file-list-container', 'Y', 'fileuploadid', 'uploadfile_div',
                        'uploadedfile_div');
                    $('#uploadedfile_div').show();
                }







                // if (files) {
                //     // alert('files');
                //     $('#uploadedfile_div').show();
                //     UploadedFileList(files, 'edit', 'file-list-container', 'Y', 'fileuploadid', 'uploadfile_div',
                //         'uploadedfile_div');
                // }

            }
        } else
            $('#auditeereplaycard').hide();
    }

    $('#translate').change(function() {
        //changeButtonText('action','buttonaction', 'forwardbtn',@json($savedraftbtn),@json($updatebtn),@json($forwardbtn));
    });

















    function hide_rejoinderfields() {
        $('#rejoinder_uploadfile_div').hide();
        $('#viewrejoinderfile_div').hide();
        $('#rejoinderremarksdiv').hide();
        $('#rejoinderdiv').hide();
        $('#rejoinder_auditorremarksdiv').hide();
        $('#rejoinderstatus').val('');
    }



    function set_auditeereply(action, files, remarks) {
        // alert(action);
        $('#file-input-container').empty();
        fileCount = 0;
        // alert(remarks);
        if (action == 'new') {
            if (editor) editor.disableReadOnlyMode('customLock');
            $('#uploadfile_div').show();
            $('#showbtn').show();



            files = '';
        } else if (action == 'edit') {
            // alert('ekf');
            $('#showbtn').show();

            if (files) {
                // alert('files');
                $('#uploadedfile_div').show();
                UploadedFileList(files, 'edit', 'file-list-container', 'Y', 'fileuploadid', 'uploadfile_div',
                    'uploadedfile_div');
            } else {
                $('#uploadfile_div').show();
            }
            editor.setData(remarks);
            if (editor) editor.disableReadOnlyMode('customLock');
        } else {
            editor.setData(remarks);
            if (editor) editor.enableReadOnlyMode('customLock');

            $('#showbtn').hide();
            if (files) {
                UploadedFileList(files, 'view', 'file-list-container', 'N', '', 'uploadfile_div', 'uploadedfile_div');
            }
            $('#uploadfile_div').hide();
            $('#uploadedfile_div').show();
        }
    }



    // function set_auditeereplyform(firstItem)
    // {
    //     reset_form();

    //     if (firstItem.auditeeremarks) {
    //         // alert('if');
    //         // alert(firstItem.processcode);
    //         if (firstItem.processcode === 'F' && firstItem.rejoinderstatus !== 'Y') //
    //         {
    //             changebuttonasupdate()
    //             hide_rejoinderfields()

    //             if (firstItem.auditeefileupload) {
    //                 // alert('jo');

    //                 const files = getfile(firstItem.auditeefileupload);

    //             } else {
    //                 $('#file-input-container').empty();
    //                 fileCount = 0;
    //                 files = '';

    //             }

    //             set_auditeereply('edit', files, firstItem.auditeeremarks)
    //         } else if (['M', 'R', 'X', 'A'].includes(firstItem.processcode) ||
    //             (firstItem.processcode === 'F' && firstItem.rejoinderstatus === 'Y') ||
    //             (firstItem.rejoinderstatus === 'Y')
    //         ) {
    //             // alert('elseif');
    //             if(firstItem.auditeefileupload)
    //             {
    //                 const files = getfile(firstItem.auditeefileupload);
    //             }
    //             else files  =   '';
    //             set_auditeereply('view', files, firstItem.auditeeremarks)

    //             if ((firstItem.processcode === 'F' && firstItem.rejoinderstatus === 'Y') || (firstItem
    //                     .rejoinderstatus === 'Y')) {

    //                 if (firstItem.auditee_rejoinderfileupload) {
    //                     uploadedfile = firstItem.auditee_rejoinderfileupload;
    //                 } else {
    //                     $('.rejoinder_inputfile_container').empty();
    //                     uploadedfile = '';

    //                     fileCount = 0;
    //                 }

    //                 $('#rejoinder_auditorremarks').val(firstItem.rejoinder_auditorremarks)

    //                 console.log(uploadedfile);


    //                 setRejoinderForm(firstItem.rejoinder_auditeerremarks, firstItem.rejoinderstatus, uploadedfile,
    //                     firstItem.processcode);
    //             } else {
    //                 hide_rejoinderfields()
    //             }
    //         }
    //     } else {
    //         changebuttonasinsert()
    //         hide_rejoinderfields()
    //         set_auditeereply('new', '', '');
    //     }
    // }

    function show_rejoinderfields() {
        $('#rejoinder_uploadfile_div').show();
        $('#viewrejoinderfile_div').hide();
        $('#rejoinderremarksdiv').show();

    }

    function setRejoinderForm(rejoinderremarks, rejoinderstatus, fileuploadlist, processcode) {

        $('#rejoinderdiv').show();
        $('#rejoinder_auditorremarksdiv').show();
        $('#rejoinderremarksdiv').show();
        $('#rejoinderstatus').val(rejoinderstatus);

        $('#rejoinder_inputfile_container').empty();
        fileCount = 0;

        if (rejoinderremarks) {
            changebuttonasupdate()
            auditeeremarks.setData(rejoinderremarks);


            if (processcode == 'F') {
                $('#showbtn').show();
                if (fileuploadlist) {
                    $('#viewrejoinderfile_div').show();
                    UploadedFileList(getfile(fileuploadlist), 'edit', 'RejoinderFileListContainer', 'Y', 'fileuploadid',
                        'rejoinder_uploadfile_div', 'viewrejoinderfile_div');
                } else {
                    $('#viewrejoinderfile_div').hide();
                    $('#rejoinder_uploadfile_div').show()
                }

            } else {
                $('#showbtn').hide();
                $('#viewrejoinderfile_div').show();
                $('#rejoinder_uploadfile_div').hide();
                if (fileuploadlist) {
                    UploadedFileList(getfile(fileuploadlist), 'view', 'RejoinderFileListContainer', 'N', '',
                        'rejoinder_uploadfile_div', 'viewrejoinderfile_div');
                }

            }
        } else {
            $('#showbtn').show();
            changebuttonasupdate()
            auditeeremarks.setData('')
            $('#rejoinder_uploadfile_div').show();
        }
    }


    var rowCount = 0;
    let maxRow = 5;

    function checkRowLimit(isLast) {
        if (isLast) {
            // Add any logic here to check row limit or handle specific cases.
        }
    }


    function show_view_card(firstItem) {
        rowCount = 0;
        $('#dynamicRowsContainer').empty();
        // Show the card
        $('#viewauditslipcard').show();

        $('#rejoinderstatus').val(firstItem.rejoinderstatus);
        $('#rejoindercycle').val(firstItem.rejoindercycle);

        // $('#processname').html('Status :'+firstItem.processelname	)
        $('#processname').html('Status :' + firstItem.processelname)

        $('#mainslipnumber').html('#' + firstItem.mainslipnumber);


        //Set values directly
        const viewFields = {
            '#view_majorobjectioncode': firstItem.mainobjectionid,
            '#view_amount_involved': firstItem.amtinvolved,
            '#view_slipdetails': firstItem.slipdetails,
            '#view_auditorremarks': firstItem.auditorremarks,
            '#view_severityid': firstItem.severitycode,
            // '#view_minorobjectioncode': firstItem.subobjectionename ?: 'No Objection Available',
            '#view_minorobjectioncode': firstItem.subobjectionename ?? 'No Objection Available',

        };

        // $('#view_majorobjectioncode').val(firstItem.mainobjectionid);
        // $('#view_minorobjectioncode').val(firstItem.subobjectionename);
        // $('#view_amount_involved').val(firstItem.amtinvolved);
        // $('#view_severityid').val(firstItem.severity);
        // $('#view_slipdetails').val(firstItem.slipdetails);
        // $('#dynamicRowsContainer').empty();

        for (const [field, value] of Object.entries(viewFields)) {
            $(field).val(value);
        }

        // Handle liability
        const liability = (firstItem.liability === 'Y') ? 'Yes' : 'No';


        // $('#view_liabilityname').val(firstItem.liability === 'Y' ? firstItem.liabilityname : '');
        // $('#liabilitygpfno').val(firstItem.liability === 'Y' ? firstItem.liabilitygpfno : '');
        // $('#liabilitydesig').val(firstItem.liability === 'Y' ? firstItem.liabilitydesig : '');

        // $('input[name="view_liability"][value="' + firstItem.liability + '"]').prop('checked', true);

        //enable_liability(firstItem.liability, 'liabilityname_div', 'liabilitygpfno_div')
        $('input[name="view_liability"][value="' + firstItem.liability + '"]').prop('checked', true);
        if (firstItem.liability === 'Y') {



            $liabilityaction = 'view';



            // $liabilityaction   =   'edit';
            // $('#liabilityname_div').show()
            // $('#liabilitygpfno_div').show()
            // $('#liabilityname').val(firstItem.liabilityname);
            // $('#liabilitygpfno').val(firstItem.liabilitygpfno);
            // $('#liabilitydesig').val(firstItem.liabilitydesig);


            // Assuming you have this string (you can replace this with dynamic data from your backend or input)
            liabilitydel = firstItem.liabilitydel;

            if (liabilitydel) {
                // Step 1: Split the liabilitydel string by commas
                var liablityparts = liabilitydel.split(",");
                liabilityid = '';
                // Step 2: Iterate over the parts and split each by the dash (-)
                for (var i = 0; i < liablityparts.length; i++) {
                    var liablity = liablityparts[i].split("-"); // Split by the dash (-)

                    let isLast = (i === liablityparts.length - 1);


                    addNewWorkRow(event, $liabilityaction, liablity[0], liablity[2], liablity[1], liablity[3], liablity[
                        4], liablity[5], isLast, liablity[6]);

                    console.log(liablity);
                    liabilityid += liablity[5] + ',';

                }
                if (liabilityid.endsWith(',')) {
                    liabilityid = liabilityid.slice(0, -1); // Remove the last character (the comma)
                }
                $('#liabilityid').val(liabilityid);
            }








        } else {
            $('#dynamicRowsContainer').hide()
        }

        // Handle file list
        $('#view_file-list-container').show();
        if (firstItem.auditorfileupload) {
            const files = getfile(firstItem.auditorfileupload);
            UploadedFileList(files, 'view', 'view_file-list-container', 'N', '', '', '');

        }


        // // Set editor content
        // if (view_editor) {
        //     view_editor.setData(firstItem.auditorremarks || '');
        // }
    }


    /*********************************************** Fetch Data *******************************************/





    /*********************************************** Automatic Slip Add *******************************************/


    function addSlipNumber(slipNumber, id, processcode) {
        // Check if slipNumber is not provided
        if (!slipNumber) {
            var stringValue = $('#autoslipnumber').val(); // Get value of the slip number input
            var intValue = Number(stringValue); // Convert string to number using Number()
            if (isNaN(intValue)) intValue = 0;
            slipNumber = intValue;
        }

        // Ensure id is not null or undefined (set to empty string by default)
        if (!id) id = '';

        // Get the 'ul' element where the slip numbers are listed
        const chatUsersList = document.querySelector(".chat-users");

        // Create a new 'li' element for the new slip number
        const newListItem = document.createElement("li");

        seriesno = $('#seriesno').val();

        // Add the HTML content for the new 'li'
        newListItem.innerHTML = `
   <div class="hstack ${processcode =='A' ? 'drop_div' :processcode =='X' ? 'contopara_div':''}   p-2 bg-hover-light-black position-relative border-bottom" id="${seriesno}" onclick="handleSlipClick('${seriesno}')">
       <input type="hidden" id="slipid_${seriesno}" name="slipid" value="${id}">
       <input type="hidden" id="slipnumber_${seriesno}" name="slipnumber_${seriesno}" value='${slipNumber}'>

       <a style="color:black;" href="javascript:void(0)" class="stretched-link"></a>
       <div class="ms-2">
           <a style="color:black;" href="javascript:void(0)">
               <i class="text-primary ri ri-clipboard-text fs-5"></i>
           </a>
       </div>
       <div class="ms-auto">
           <h6 class="mb-0 fs-2">${slipNumber}</h6>
       </div>
       <div class="ms-auto">
           <a style="color:black;" href="javascript:void(0)">
               <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="currentColor"
                   class="icon icon-tabler icons-tabler-filled icon-tabler-arrow-big-right-lines slip-arrow" style="visibility:hidden" id="arrow_${seriesno}">
                   <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                   <path d="M12.089 3.634a2 2 0 0 0 -1.089 1.78l-.001 2.585l-1.999 .001a1 1 0 0 0 -1 1v6l.007 .117a1 1 0 0 0 .993 .883l1.999 -.001l.001 2.587a2 2 0 0 0 3.414 1.414l6.586 -6.586a2 2 0 0 0 0 -2.828l-6.586 -6.586a2 2 0 0 0 -2.18 -.434l-.145 .068z" />
                   <path d="M3 8a1 1 0 0 1 .993 .883l.007 .117v6a1 1 0 0 1 -1.993 .117l-.007 -.117v-6a1 1 0 0 1 1 -1z" />
                   <path d="M6 8a1 1 0 0 1 .993 .883l.007 .117v6a1 1 0 0 1 -1.993 .117l-.007 -.117v-6a1 1 0 0 1 1 -1z" />
               </svg>
           </a>
       </div>
   </div>`;

        // Append the new 'li' to the list
        chatUsersList.appendChild(newListItem);

        // Increment the slip number and series number
        slipNumber = slipNumber + 1;
        seriesno = Number($('#seriesno').val()) + 1;

        // Update the value of the autoslipnumber input field
        $('#autoslipnumber').val(slipNumber);
        $('#seriesno').val(seriesno);

        // Flag to check if the click handler has been triggered before
        let clickHandled = false;
    }

    function handleSlipClick(seriesno) {


        // alert(seriesno);
        $('#upload_file').show();
        $('#fileuploadstatus').val('Y');
        $('#fileuploadid').val('');
        // is
        $('#auditee_upload').val('');
        const fileListContainer = $('#file-list-container');
        fileListContainer.empty(); // Clear previous file cards
        $('#file-list-container').hide();

        const clickedId = seriesno; // Get the ID of the clicked element
        currentslipnumber = $('#slipnumber_' + clickedId).val();
        currentslipid = $('#slipid_' + clickedId).val();
        $('#upload_preview').hide();
        if (currentslipid) {
            getauditslip(currentslipid, 'edit', '', $('#filter').val());
        } else {
            $('#forwardedby').html('Initiated By Self')
            reset_form();
            $('#auditslipcard').show();
            $('#viewauditslipcard').hide();
        }



        $('#currentslipnumber').val(currentslipnumber);
        $('#auditslipid').val(currentslipid);

        $('.hstack').removeClass("active_div");

        $(".slip-arrow").css("visibility", "hidden");

        $('#arrow_' + clickedId).css("visibility", "visible");
        $('#' + clickedId).addClass("active_div");




    }

    // When the 'Add Slip Number' button is clicked (directly, no modal)
    $("#add-button").click(function() {
        addSlipNumber(); // Add a new slip number when the button is clicked
    });

    /*********************************************** Automatic Slip Add *******************************************/



    /**************************************** Fit the upload files, delete upload file in s **********************/
    function view_files(files) {

        const fileListContainer = $('#view_file-list-container');
        fileListContainer.empty(); // Clear previous file cards

        files.forEach(file => {

            const fileCard = `
       <div class=" overflow-hidden mb-3" id="viewfile-card-${file.id}">
           <div class="d-flex flex-row">
               <div class="p-2 align-items-center">
                   <h3 class="text-danger box mb-0 round-56 p-2">
                       <i class="ti ti-file-text"></i>
                   </h3>
               </div>
               <div class="p-3">
                   <h3 class="text-dark mb-0 fs-4">
                       <!-- Add an anchor tag to open the file in a new tab -->
                       <a style="color:black;" href="/storage/${file.path}" target="_blank">${file.name}</a>                        </h3>
               </div>


           </div>
       </div>
   `;

            fileListContainer.append(fileCard); // Add the file card to the container
        });
    }


    /**************************************** Fit the upload files, delete upload file in edit **********************/


    /*************************************************  Audit Tab Functions *********************************************/


    // UploadedFileList(files, UploadedFileList_withaction, 'file-list-container', 'Y', 'fileuploadid')

    function UploadedFileList(files, action, containerid, uploadidstatus, fileuploadhiddenid, fileuploaddiv,
        viewfilediv) {
        $('.' + containerid).empty();
        fileCount = 0;

        const $container = $('#' + containerid).empty();

        files.forEach(file => {

            if (uploadidstatus == 'Y') $('#' + fileuploadhiddenid).val(file.fileuploadid);

            const fileCard = `
                   <div class=" position-relative  align-items-stretch ms-2"  ${action === 'edit' ? `id="file-card-${file.fileuploadid}"` : ''}>
  <div class="card  ms-2">
                                                                        <div class="card-body">

                                                                    <div class="d-flex align-items-center justify-content-between  ms-2">
      ${action === 'edit' ? `<input type="hidden" id="fileuploadid_${file.fileuploadid}" name="fileuploadid_${file.fileuploadid}" value="${file.fileuploadid}">` : ''}
    <div class="d-flex">
       <div class="p-1 bg-primary-subtle rounded me-6 d-flex align-items-center justify-content-center">
          <i class="ti ti-file-text text-primary fs-6"></i>
       </div>
       <div>
          <a class="fs-3 fw-semibold" style="color:black;" href="/storage/${file.path}" target="_blank">${file.name}</a>
       </div>
    </div>
     ${action === 'edit' ? `
                                                                                                                                                     <div class="bg-danger-subtle badge ms-2">
                                                                                                                                                                                                                                                                                                                                   <span class="fs-5  text-danger fw-semibold mb-0"><i class="ti ti-trash"  onclick="deleteFile(${file.fileuploadid}, event)"></i></span>
                                                                                                                                                                                                                                                                                                                                </div>  ` : ''}

                                                                      </div>  </div></div>`;

            $container.append(fileCard);
            fileCount++;
        });
        // alert(uploadidstatus);
        if (uploadidstatus == 'Y') {
            if (fileCount < 3) {
                $('#add-file-btn').show()
                $('.' + containerid).show()
                $('#' + fileuploaddiv).show();
            } else {
                $('#add-file-btn').hide()
                $('.' + containerid).hide()
                $('#' + fileuploaddiv).hide();
            }
        }
    }

    function getfile(filearray) {
        return files = filearray.split(',').map((fileDetail, index) => {
            const [name, path, size, fileuploadid] = fileDetail.split('-');
            return {
                id: index + 1,
                name,
                path,
                size,
                fileuploadid
            };
        });
    }



    // Function to delete a file
    function deleteFile(fileId, event) {
        event.preventDefault(); // Prevents page refresh

        removeAllEventListeners(document.getElementById("process_button"));

        // Set up the confirmation process
        document.getElementById("process_button").onclick = function() {
            deletefilefromview(fileId);
        };

        // Show confirmation alert
        passing_alert_value('Confirmation', "Are you sure you want to delete this file?", 'confirmation_alert',
            'alert_header', 'alert_body', 'forward_alert');
    }

    function deletefilefromview(fileId, fileuploadiv, viewdiv) {
        alert(fileCount);
        $('#file-card-' + fileId).hide();

        // Optionally, remove the file ID from activefileid (if necessary)
        var activeFileIds = $('#active_fileid').val().split(',');
        activeFileIds = activeFileIds.filter(function(id) {
            return id != fileId;
        });
        $('#active_fileid').val(activeFileIds.join(','));


        // Get the current deactivefileid value and ensure it is an array
        var deactiveFileIds = $('#deactive_fileid').val().split(',').filter(function(id) {
            return id !== ''; // Remove empty values (in case there's a leading comma)
        });

        // Add the file ID to deactivefileid if not already present
        if (!deactiveFileIds.includes(fileId.toString())) {
            deactiveFileIds.push(fileId);
        }

        if (fileId !== 1) { // Only allow removal of second and third file inputs
            $(`#fileupload_${fileId}`).remove();
            fileCount--;
        }

        // alert(fileCount);

        if ((fileCount > 0) && (fileCount < 3)) {
            $('#uploadfile_div').show()
            $('#add-file-btn').show()

        }

        // Join the array with commas and update the deactive_fileid hidden input field
        $('#deactive_fileid').val(deactiveFileIds.join(','));
        // $('#upload_file').show();





        // $('#fileuploadstatus').val('Y');

    }



    // Start with one file input



    // Handle Add File Button Click
    // $('#add-file-btn').click(function() {
    //     add_uploadfile('file-input-container')
    // });

    $('#add_rejoinderfile').click(function() {
        add_uploadfile('rejoinder_inputfile_container')
    });


    // Handle Remove Button Click
    $(document).on('click', '.remove-file', function() {
        const fileId = $(this).data('id');
        // if (fileId !== 1) {  // Only allow removal of second and third file inputs
        $(`#fileupload_${fileId}`).remove();
        fileCount--;
        //}
    });



    function addNewWorkRow(event, action, notype, name, gpfno, designation, amount, liabilityid, isLast, statusflag) {
        event.preventDefault();
        let isChecked = (statusflag === 'Y') ? 'checked' : '';

        // isdisabled  =   '';

        let isdisabled = (action === 'view') ? 'disabled' : '';


        if (rowCount >= maxRow) {
            alert("Maximum row limit reached!");
            return;
        }

        let selectedOption = "";
        if (notype === "01") {
            selectedOption = "01"; // GPF No
        } else if (notype === "02") {
            selectedOption = "02"; // CPF No
        } else if (notype === "03") {
            selectedOption = "03"; // IFHRMS No
        }

        // Create the new row with proper string interpolation for dynamic values
        let newWorkRow = `
        <div class="d-flex mt-2 work-row" id="row${rowCount}">
            <input type="hidden" id="liabilityid${rowCount}" name="liabilityid${rowCount}" value="${liabilityid}" >
            <div class="col-md-2">`;
        if (rowCount == 0) {
            newWorkRow +=
                `
              <label class="form-label  lang" for="validationDefaultUsername" key="">Type</label>`;
        }
        newWorkRow += `

                <select class="form-select" name="notype[]" value="${name}"  ${isdisabled} >
                    <option value="">---Select Type---</option>
                    <option value="01" ${selectedOption === "01" ? "selected" : ""}>GPF No</option>
                    <option value="02" ${selectedOption === "02" ? "selected" : ""}>CPF No</option>
                    <option value="03" ${selectedOption === "03" ? "selected" : ""}>IFHRMS No</option>
                </select>
            </div>
            <div class="col-md-2 ms-2">`;
        if (rowCount == 0) {
            newWorkRow +=
                `
              <label class="form-label  lang" for="validationDefaultUsername" key="name">Name</label>`;
        }
        newWorkRow += `
                <input type="text" class="form-control" name="name[]" value="${name}" placeholder="Name" ${isdisabled} >
            </div>
            <div class="col-md-1 ms-2">`;
        if (rowCount == 0) {
            newWorkRow +=
                `
              <label class="form-label  lang" for="validationDefaultUsername" key="">GPF Number</label>`;
        }
        newWorkRow += `
                <input type="text" class="form-control" name="gpfno[]" value="${gpfno}" placeholder="GPF Number" ${isdisabled}>
            </div>
            <div class="col-md-2 ms-2">`;
        if (rowCount == 0) {
            newWorkRow +=
                `
              <label class="form-label  lang" for="validationDefaultUsername" key="">Desgnation</label>`;
        }
        newWorkRow += `
                <input type="text" class="form-control" name="designation[]" value="${designation}" placeholder="Designation" ${isdisabled}>
            </div>
            <div class="col-md-2 ms-2">`;
        if (rowCount == 0) {
            newWorkRow +=
                `
              <label class="form-label  lang" for="validationDefaultUsername" key="">Amount</label>`;
        }
        newWorkRow += `
                <input type="text" class="form-control numberswithdecimal"  maxlength="10" name="amount[]" value="${amount}" placeholder="Role" ${isdisabled}>
            </div>`;
        if ((rowCount == 0) && (action === 'edit')) {
            newWorkRow +=
                ` <div class="col-md-1 ">
              <label class="form-label  lang" for="validationDefaultUsername" key="name">Action</label>`;
        }


        if ((action === 'edit')) {

            let isdisabled = (action === 'view') ? 'disabled' : '';
            newWorkRow += `

                <div class="form-check form-check-inline ms-2 ">
                    <input type="checkbox" class="form-check-input warning" name="activestatus[]"   ${isChecked} ${isdisabled}>
                    <label class="form-check-label" for="active_status_${rowCount}" id="label_${rowCount}">Active</label>
                </div>
           `;
        } else if (action === 'entry') {
            if (rowCount > 0) {
                newWorkRow += `
                <button type="button" class="btn btn-danger fw-medium ms-2 deleteRowBtn" onclick="deleteRow(${rowCount})">
                    <i class="ti ti-trash"></i>
                </button></div> </div>`;
            }
        }



        // Add the plus button only for the last row


        // Append the new row
        rowCount++;
        $("#dynamicRowsContainer").append(newWorkRow);

        checkRowLimit(isLast);
    }
</script>
@endsection
