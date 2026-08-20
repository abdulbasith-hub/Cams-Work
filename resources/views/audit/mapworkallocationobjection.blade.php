@section('content')
@section('title', 'Callfor Records Report')
@extends('index2')
@include('common.alert')
@php

    $sessionchargedel = session('charge');
    $deptcode = $sessionchargedel->deptcode;
    $make_dept_disable = $deptcode ? 'disabled' : '';

@endphp

<link rel="stylesheet" href="{{ asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}">
<style>
    table tbody tr td
    {
        padding:15px !important;
    }
</style>


<div class="row">
        <div class="col-12">
            <div class="card" style="border-color: #7198b9">
            <div class="card-header card_header_color">
                   Mapping 
                </div>
                <div class="card-body">
                    <div>
                    <div>
                        <div>
                            <br>
                            <table id="file_export" class="table w-100 table-bordered datatables-basic">
                                <tbody>
                                    <tr>
                                        <td style="width:100%;" colspan="7">   
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label required lang" for="deptcode">Department</label>
                                                    <select class="form-select mr-sm-2 select2 lang-dropdown" id="deptcode" name="deptcode">
                                                        <option value="">-- Select Department --</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label required lang">Category</label>
                                                    <select class="form-select mr-sm-2 select2 lang-dropdown" id="category" name="category">
                                                        <option value="">-- Select Category --</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label required lang">Sub Category</label>
                                                    <select class="form-select mr-sm-2 select2 lang-dropdown" id="sub_category" name="sub_category">
                                                        <option value="">-- Select Sub Category --</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label required lang">Group</label>
                                                    <select class="form-select mr-sm-2 select2 lang-dropdown" id="group" name="group">
                                                        <option value="">-- Select Group --</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>   
                                    <tr class="callforrecords-row">
                                        <td class="callforrecords" rowspan="3"> 
                                            Call For Records 
                                            <br><br> 
                                            <select class="form-select mr-sm-2 select2 lang-dropdown" id="call_for_records" name="call_for_records">
                                                <option value="">-- Select Call For Records --</option>
                                            </select>
                                        </td>
                                        <td colspan="2">Work Allocation</td>
                                        <td colspan="2">Objection</td>
                                        <td></td>
                                        <td style="width:11%;vertical-align:middle;" class="callforrecords" rowspan="3"> 
                                            <button onclick="Callforrecordsaddnew();" type="button" data-repeater-create="" class="btn btn-success hstack gap-6">
                                                Add  New
                                                <i class="ti ti-circle-plus ms-1 fs-5"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Main Work Allocation</td>
                                        <td>Sub Work Allocation</td>
                                        <td>Main Objection</td>
                                        <td>Sub Objection</td>
                                        <td></td>
                                    </tr>
                                    <tr class="work-row">
                                        <td>
                                            <select class="form-select" name="main_work_allocation">
                                                <option value="">-- Select Main Work Allocation --</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select" name="sub_work_allocation">
                                                <option value="">-- Select Sub Work Allocation --</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select" name="main_objection">
                                                <option value="">-- Select Main Objection --</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-select" name="sub_objection">
                                                <option value="">-- Select Sub Objection --</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button onclick="addNewRow();" class="btn btn-success fw-medium" type="button">
                                                <i class="ti ti-circle-plus fs-5 d-flex"></i>
                                            </button>
                                        
                                        </td>
                                    </tr>                               
                                </tbody>
                            </table>
                        </div>
                    </div>

                                        
                    </div>



                                                
                        </div>
                    </div>
                </div>
            </div>
        </div>







<style>
    /* Change the border color of the search box */
    /* Remove border from Select2 search field */
    .select2-container .select2-search__field {
        border: none !important;
        outline: none !important;
    }

    /* Ensure focus does not cause the border to appear */
    .select2-container .select2-search__field:focus {
        border: none !important;
        outline: none !important;
    }


    .select2-container .select2-selection--single {
        font-size: 12px;
    }

    .select2-container .select2-results__option {
        font-size: 12px !important;
    }


    .select2-container .select2-selection--single .select2-selection__arrow {
        top: 50%;
        transform: translateY(-50%);


    }

    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        margin-top: 20px !important;
    }

    body .select2-results__option {
        padding: 4px 10px;
    }

    .select2-selection--single .select2-selection__rendered {
        height: 30px !important;
        line-height: 38px !important;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        padding-right: 30px;
        position: relative;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute;
        right: 5px;
    }
</style>

<script src="{{ asset('assets/libs/jquery-validation/dist/jquery.validate.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>


<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>



<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->
<script src="../assets/js/forms/select2.init.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<script>
 
    
 function addNewRow() {
    var tableBody = document.querySelector("#file_export tbody");

    // Get all existing work rows
    var workRows = document.querySelectorAll(".work-row");

    // Change last row's "+" button to "-"
    if (workRows.length > 0) {
        var lastRow = workRows[workRows.length - 1];
        var lastTd = lastRow.querySelector("td:last-child");
        lastTd.innerHTML = `<button onclick="removeRow(this);" class="btn btn-danger fw-medium" type="button">
                                <i class="ti ti-circle-minus fs-5 d-flex"></i>
                            </button>`;
    }

    // Clone the first row to create a new one
    var firstRow = workRows[0];
    if (!firstRow) return; // If no row exists, do nothing

    var newRow = firstRow.cloneNode(true);

    // Reset dropdown values
    newRow.querySelectorAll("select").forEach(select => select.value = "");

    // Set last column to have "+" button
    newRow.querySelector("td:last-child").innerHTML = `<button onclick="addNewRow();" class="btn btn-success fw-medium" type="button">
                                                            <i class="ti ti-circle-plus fs-5 d-flex"></i>
                                                        </button>`;

    // Append new row
    tableBody.appendChild(newRow);

    // Update rowspan
    updateRowspan(1);
}

function removeRow(button) {
    var rowToRemove = button.closest("tr");
    var workRows = document.querySelectorAll(".work-row");

    if (workRows.length > 1) {
        rowToRemove.remove();
        updateRowspan(-1);

        // Ensure last row always has "+"
        var remainingRows = document.querySelectorAll(".work-row");
        if (remainingRows.length > 0) {
            var lastRow = remainingRows[remainingRows.length - 1];
            lastRow.querySelector("td:last-child").innerHTML = `<button onclick="addNewRow();" class="btn btn-success fw-medium" type="button">
                                                                    <i class="ti ti-circle-plus fs-5 d-flex"></i>
                                                                </button>`;
        }
    } else {
        alert("At least one row must remain!");
    }
}

function updateRowspan(change) {
    var callForRecordsCell = document.querySelector(".callforrecords");
    var currentRowspan = parseInt(callForRecordsCell.getAttribute("rowspan")) || 3;
    callForRecordsCell.setAttribute("rowspan", currentRowspan + change);
}


function Callforrecordsaddnew() {
    // Select the last "callforrecords-row" (which represents a group of 3 rows)
    let lastRowGroup = document.querySelector(".callforrecords-row");

    if (lastRowGroup) {
        // Create a new table section to hold the cloned rows
        let tbody = lastRowGroup.parentElement;

        // Select the three rows to clone together
        let newRows = lastRowGroup.nextElementSibling.nextElementSibling; // The third row (work-row)
        let middleRow = lastRowGroup.nextElementSibling; // The second row (header row)

        // Clone all three rows
        let clonedCallForRecordsRow = lastRowGroup.cloneNode(true);
        let clonedMiddleRow = middleRow.cloneNode(true);
        let clonedWorkRow = newRows.cloneNode(true);

        // Clear select values in the cloned rows
        clonedCallForRecordsRow.querySelectorAll("select").forEach(select => select.value = "");
        clonedMiddleRow.querySelectorAll("select").forEach(select => select.value = "");
        clonedWorkRow.querySelectorAll("select").forEach(select => select.value = "");

        // Append all cloned rows to the table
        tbody.appendChild(clonedCallForRecordsRow);
        tbody.appendChild(clonedMiddleRow);
        tbody.appendChild(clonedWorkRow);
    }
}
</script>


</script>
</script>
@endsection
