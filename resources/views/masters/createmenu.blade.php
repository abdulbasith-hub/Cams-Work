@section('content')
@extends('index2')
@include('common.alert')
@php
$sessionmenudel = session('charge');
$deptcode = $sessionmenudel->deptcode;
// $make_dept_disable = $deptcode ? 'disabled' : '';
$make_deptdiv_show = $deptcode ? '' : 'hide_this';
@endphp
<link rel="stylesheet" href="{{asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css')}}">
<style>
    .custom-size {
        font-size: 18px;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color">Create Menu </div>
            <div class="card-body">
                <form id="menuform" name="menuform">
                    <input type="hidden" name="menuid" id="menuid">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3 ">
                            <label class="form-label required lang" key="levelid" for="levelid">Level Menu Name</label>
                            <select class="form-select mr-sm-2" id="levelid" onchange="gettyplevelecode(this.value)" name="levelid">
                                <option disabled>Select Level Menu Name</option>
                                <option value="1">Main Menu</option>
                                <option value="2">Sub Menu</option>
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3 " id="parentid_div" style="display: none;">
                            <label class="form-label required lang" key="desig_dept" for="dept">Parent Menu Name</label>
                            <select class="form-select mr-sm-2" id="parentid" name="parentid">
                                <option value="">Select Parent Menu Name</option>
                                @if (!empty($menus) && count($menus) > 0)
                                @foreach ($menus as $menu)
                                <option value="{{ $menu->menuid }}">
                                    {{ $menu->menuename }}
                                </option>
                                @endforeach
                                @else
                                <option disabled>No Parent Menu Name Available</option>
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="desig_short" for="menuename">Menu English Name</label>
                            <input type="text" class="form-control" id="menuename" name="menuename"
                                placeholder="Menu English Name" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="menutname" for="menutname">Menu Tamil Name</label>
                            <input type="text" class="form-control" id="menutname" name="menutname"
                                placeholder="Menu Tamil Name" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="iconname" for="iconname">Menu Icon Name</label>
                            <input type="text" class="form-control" id="iconname" name="iconname"
                                placeholder="Menu Icon Name" required />
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label required lang" key="menuurl" for="menuurl">Menu Url</label>
                            <input type="text" class="form-control" id="menuurl" name="menuurl"
                                placeholder="Menu Url" required />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mx-auto">
                            <input type="hidden" name="action" id="action" value="insert" />
                            <input type="hidden" name="menucode" id="menucode" value="" />
                            <button class="btn button_save mt-3" type="submit" action="insert" id="buttonaction"
                                name="buttonaction">Save Draft </button>
                            <button type="button" class="btn btn-danger mt-3" id="reset_button"
                                onclick="reset_form()">Clear</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color">menu Details</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="menutable"
                            class="table w-100 table-striped table-bordered display text-nowrap datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang" key="s_no">S.No</th>
                                    <th>Menu Name</th>
                                    <th> Menu Url</th>
                                    <th>Parent Menue Name</th>
                                    <th> Order Id</th>
                                    <th>Parent Order Id</th>
                                    <th class="all">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div id='no_data' class='hide_this'>
                    <center>No Data Available</center>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="{{asset('assets/libs/jquery-validation/dist/jquery.validate.min.js')}}"></script>
<script src="{{asset('assets/libs/datatables.net/js/jquery.dataTables.min.js')}}"></script>
<script>
    var table = $('#menutable').DataTable({
        colId: 'orderid',
        processing: true,
        serverSide: false,
        lengthChange: false,
        ajax: {
            url: "{{ route('menu.menu_fetchData') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: function(json) {
                if (json.data && json.data.length > 0) {
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    return json.data;
                } else {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                    return [];
                }
            },
        },
        columns: [{
                data: null,
                render: (_, __, ___, meta) => meta.row + 1,
                className: 'text-end'
            },
            {
                data: "menuename"
            },

            {
                data: "menuurl"
            },
            {
                data: 'parent_menuename',
                render: function(data, type, row) {
                    if (data == null || data === '') {
                        return '<div class="text-center"><button type="button" class="btn btn-primary btn-sm">--Main Menu--</button></div>';
                    } else {
                        return `<div class="text-center"><button type="button" class="btn btn-sm" style="background-color: rgb(183, 19, 98); color: rgb(255, 255, 255);">${data}</button></div>`;
                    }
                }
            },
            {
                data: "orderid",
                render: function(data, type, row) {
                    if (!data) {
                        return '<div class="d-flex justify-content-center"><button type="button" class="btn btn-primary btn-sm">-- Null --</button></div>';
                    } else {
                        return OrderChangeFunction(data, row, 'orderid', row.menuid);
                    }
                }
            },
            {
                data: "parentorderid",
                render: function(data, type, row) {
                    if (!data) {
                        return '<div class="d-flex justify-content-center"><button type="button" class="btn btn-primary btn-sm">-- Null --</button></div>';
                    } else {
                        return OrderChangeFunction(data, row, 'parentorderid', row.menuid);
                    }
                }
            },
            {
                data: "encrypted_menuid",
                render: (data) =>
                    `<center>
                    <a class="btn editicon editmenudel" id="${data}">
                        <i class="ti ti-edit fs-4"></i>
                    </a>
                </center>`
            }
        ]
    });

    function OrderChangeFunction(data, row, type, menuid) {
        return `
        <div class="d-flex justify-content-center">
            <input type="text" class="form-control form-control-sm text-center" 
                value="${data}" 
                data-menuid="${menuid}" 
                data-${type}="${row[type]}" 
                disabled id="${type}-${row[type]}" style="width: 100px;">
            <i class="fas fa-edit text-primary mx-2 custom-size" style="cursor: pointer;" 
                onclick="enableEdit('${type}', ${row[type]})" id="edit-${type}-${row[type]}"></i>
            <i class="fas fa-save text-success mx-2 d-none custom-size" style="cursor: pointer;" 
                onclick="saveOrderId('${type}', ${row[type]}, ${menuid})" id="save-${type}-${row[type]}"></i>
            <i class="fas fa-times text-danger mx-2 d-none custom-size" style="cursor: pointer;" 
                onclick="cancelEdit('${type}', ${row[type]})" id="cancel-${type}-${row[type]}"></i>
        </div>`;
    }


    window.enableEdit = function(type, id) {
        $(`#${type}-${id}`).prop('disabled', false);
        $(`#save-${type}-${id}`).removeClass('d-none');
        $(`#cancel-${type}-${id}`).removeClass('d-none');
        $(`#edit-${type}-${id}`).addClass('d-none');
    };

    window.saveOrderId = function(type, id) {
        var newValue = $(`#${type}-${id}`).val();
        var menuid = $(`#${type}-${id}`).attr('data-menuid');
        $.ajax({
            url: "{{ route('menu.saveOrderId') }}",
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                type: type,
                menuid: menuid,
                value: newValue
            },
            success: function(response) {
                if (response.success) {
                    passing_alert_value('Confirmation', response.message,
                        'confirmation_alert', 'alert_header', 'alert_body',
                        'confirmation_alert');
                    table.ajax.reload();
                } else {
                    alert("Error saving value.");
                }
            }
        });
        $(`#${type}-${id}`).prop('disabled', true);
        $(`#save-${type}-${id}`).addClass('d-none');
        $(`#cancel-${type}-${id}`).addClass('d-none');
        $(`#edit-${type}-${id}`).removeClass('d-none');
    };

    window.cancelEdit = function(type, id) {
        var originalValue = table.row(function(idx, data, node) {
            return data[type] == id;
        }).data()[type];
        $(`#${type}-${id}`).val(originalValue);
        $(`#${type}-${id}`).prop('disabled', true);
        $(`#save-${type}-${id}`).addClass('d-none');
        $(`#cancel-${type}-${id}`).addClass('d-none');
        $(`#edit-${type}-${id}`).removeClass('d-none');
    };

    $("#menuform").validate({
        rules: {
            menuename: {
                required: true
            },
            menutname: {
                required: true
            },

        },
        messages: {

            menuename: {
                required: "Enter a Objection English name",
            },
            menutname: {
                required: "Enter a Objection Tamil name",
            },
        }
    });
    $("#buttonaction").on("click", function(event) {
        event.preventDefault();
        if ($("#menuform").valid()) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = $('#menuform').serializeArray();
            $.ajax({
                url: "{{ route('menuhome.menu_insertupdate') }}",
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        reset_form();
                        passing_alert_value('Confirmation', response.message,
                            'confirmation_alert', 'alert_header', 'alert_body',
                            'confirmation_alert');
                        table.ajax.reload();

                    } else if (response.error) {
                        console.log(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    var errorMessage = response.message ||
                        'An unknown error occurred';
                    passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                    console.error('Error details:', xhr, status, error);
                }
            });
        } else {}
    });

    $(document).on('click', '.editmenudel', function() {
        const id = $(this).attr('id');
        if (id) {
            $('#menuid').val(id);
            $.ajax({
                url: "{{ route('menu.menu_fetchData') }}",
                method: 'POST',
                data: {
                    menuid: id
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data && response.data.length > 0) {
                            populatemenuForm(response.data[0]);
                        } else {
                            alert('menu data is empty');
                        }
                    } else {
                        alert('menu not found');
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr.responseText || 'Unknown error');
                }
            });
        }
    });

    function gettyplevelecode(key) {
        if (key == '2') {
            $('#parentid_div').css('display', 'block');
        } else {
            $('#parentid_div').css('display', 'none');
        }
    }



    function populatemenuForm(menu) {
        $('#display_error').hide();
        change_button_as_update('menuform', 'action', 'buttonaction', 'display_error', '', '');
        $('#menuename').val(menu.menuename);
        $('#menutname').val(menu.menutname);
        $('#iconname').val(menu.iconname);
        $('#menuurl').val(menu.menuurl);
        if (menu.levelid == '2' && menu.parentid !== null && menu.parentid !== '') {
            gettyplevelecode(menu.levelid);
            $('#levelid').val(menu.levelid).change();
            $('#parentid').val(menu.parentid).change();
        } else {
            $('#levelid').val(menu.levelid).change();
        }
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }

    function populateStatusFlag(statusflag) {
        if (statusflag === "Y") {
            document.getElementById('statusYes').checked = true;
        } else if (statusflag === "N") {
            document.getElementById('statusNo').checked = true;
        }
    }

    function reset_form() {
        $('#menuform')[0].reset();
        $('#menuform').validate().resetForm();
        change_button_as_insert('menuform', 'action', 'buttonaction', 'display_error', '', '');
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>
@endsection