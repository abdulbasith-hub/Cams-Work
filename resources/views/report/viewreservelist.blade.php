@section('content')
@extends('index2')
@include('common.alert')
@section('title', 'Reservelist')

@php
    $sessionmainobjectiondel = session('charge');
   
    $sessionchargedel = session('charge');
   
    $sessionroletypecode = $sessionchargedel->roletypecode;
    $dga_roletypecode = $DGA_roletypecode;
    $Dist_roletypecode = $Dist_roletypecode;
    $Re_roletypecode = $Re_roletypecode;
    $Ho_roletypecode = $Ho_roletypecode;
    $Admin_roletypecode = $Admin_roletypecode;
    $roleTypeCode = $sessionchargedel->roletypecode;

    $deptcode = $sessionchargedel->deptcode;
    $regioncode = $sessionchargedel->regioncode;
    $distcode = $sessionchargedel->distcode;

    $make_dept_disable = $deptcode ? 'disabled' : '';
    $make_region_disable = $regioncode ? 'disabled' : '';
    $make_dist_disable = $distcode ? 'disabled' : '';

@endphp

<style>

html, body {
  overflow-x: hidden;
}



</style>

<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">


<div class="row">
    <div class="col-12">
        <div class="card card_border">
            <div class="card-header card_header_color lang" key="checklist_head">Reservelist</div>
            <div class="card-body">
                <form id="checklistform" name="checklistform">
                    @csrf
                    <div class="row">
                       
                        <div class="col-md-4 mb-2" id="deptdiv">
                            <label class="form-label required  lang" key="department" for="dept">Department</label>

                            <select class=" mr-sm-2 select2  lang-dropdown" multiple="multiple" <?php echo $make_dept_disable; ?>  id="deptcode"  name="deptcode[]"
                                onchange="getRegionBasedOnDept('');">

                                <option value="" data-name-en="Select Department" data-name-ta="துறையைத் தேர்ந்தெடுக்கவும்">Select Department</option>

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                              
                                   @if (!empty($dept) && count($dept) > 0)
                                    @foreach ($dept as $department)
                                        <option value="{{ $department->deptcode }}"
                                        @if (
                                    (is_array(old('dept', $deptcode)) && in_array($department->deptcode, old('dept', $deptcode)))
                                    || (old('dept', $deptcode) == $department->deptcode)
                                )
                                    selected
                                @endif   
                                

                                                             
                                data-name-en="{{ $department->deptelname }}"
                                            data-name-ta="{{ $department->depttlname }}">
                                            {{ $department->deptelname }}
                                        </option>
                                    @endforeach
                                @else
                                    <option disabled>No Departments Available</option>
                                @endif

                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="region" for="region">Region</label>
                                <select class="  mr-sm-2 select2 lang-dropdown" multiple="multiple" <?php echo $make_region_disable; ?>   id="regioncode" name="regioncode[]"
                                    onchange="getDistrictBasedOnRegion('','')">

                                    <option value="" data-name-en="Select Region" data-name-ta="மண்டலத்தைத் தேர்ந்தெடுக்கவும்">Select Region</option>

                                    <option value="A" data-name-en="All" data-name-ta="அனைத்து" >All</option>

                                  

                                    @if ($regioncode)
                                    @foreach ($region as $reg)
                                        <option value="{{ $reg->regioncode }}"
                                            @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                                            data-name-en="{{ $reg->regionename }}" data-name-ta="{{ $reg->regiontname }}">
                                            {{ $reg->regionename }}
                                        </option>
                                        @endforeach
                                    @else
                                  
                                @endif

                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <select class=" mr-sm-2 select2 lang-dropdown" multiple="multiple" <?php echo $make_dist_disable; ?> id="distcode" name="distcode[]"  >

                                <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">Select District</option>

                                <option value="A" data-name-en="All" data-name-ta="அனைத்து"> All</option>

                                 

                                    @if ($distcode)
                                    @foreach ($district as $dist)
                                        <option value="{{ $dist->distcode }}"
                                            @if (old('dept', $distcode) == $dist->distcode) selected @endif
                                            data-name-en="{{ $dist->distename }}" data-name-ta="{{ $dist->disttname }}">
                                            {{ $dist->distename }}
                                        </option>
                                    @endforeach
                                @else
                                 
                                @endif

                                </select>
                            </div>


                    </div>

                    <div class="row">
                    <div class="col-md-3 mx-auto text-center">
                    <button class="btn button_save mt-3" type="submit" id="buttonaction" name="buttonaction" data-action="insert">Submit</button>

                        <button type="button" class="btn btn-danger mt-3" id="reset_button"
                            onclick="reset_form()">Clear</button>
                    </div>
                </div>



                </form>
            </div>
        </div>

        <div class="card card_border">
            <div class="card-header card_header_color lang" key="checklist_table">List of Auditors in the Reservelist</div>
            <div class="card-body"><br>
                <div class="datatables">
                    <div class="table-responsive hide_this" id="tableshow">
                        <table id="checklisttable"
                            class="table w-100 table-striped table-bordered display  align-middle datatables-basic">
                            <thead>
                                <tr>
                                    <th class="lang align-middle text-center" key="s_no">S.No</th>
                                    <th class="lang align-middle text-center" key="department">Department</th> 
                                     <th class="lang align-middle text-center" key="region">Region</th>
                                <th class="lang align-middle text-center" key="district">District</th>
                                    <th class="lang align-middle text-center" key="username">Username</th> 
                                    <th class="lang align-middle text-center" key="designation">Desgnation</th> 

                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
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




<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<!-- Download Button Start -->

<script src="../assets/js/download-button/buttons.min.js"></script>
<script src="../assets/js/download-button/jszip.min.js"></script>
<script src="../assets/js/download-button/buttons.print.min.js"></script>
<script src="../assets/js/download-button/buttons.html5.min.js"></script>

<!-- Download Button End -->

<!-- Select2 -->
<script src="../assets/libs/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>

<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>



<script>






jsonLoadedPromise.then(() => {
    const language = window.localStorage.getItem('lang') || 'en';

    $.validator.addMethod("selectRequired", function(value, element) {
        let selected = $(element).val();
        return selected && selected.length > 0 && !selected.includes('');
    }, "Please select at least one option.");

    var validator = $("#checklistform").validate({
        ignore: [],
        rules: {
            "deptcode[]": { selectRequired: true },
            "regioncode[]": { selectRequired: true },
            "distcode[]": { selectRequired: true },

        },
        messages: {
            "deptcode[]": "Select a Department",
            "regioncode[]": "Select a Region",
            "distcode[]": "Select a District",    

        },
        errorPlacement: function(error, element) {
                if (element.hasClass('select2')) {
                    error.insertAfter(element.next('.select2-container'));
                } else {
                    error.insertAfter(element);
                }
            },

        });

       

    $('.select2').on('change', function() {
        $(this).valid(); 
    });

    $('#buttonaction').on('click', function (e) {
        e.preventDefault();


        deptcode = $("#deptcode").val();
        region = $("#regioncode").val();
        district = $("#distcode").val() ;
    


        if ($("#checklistform").valid()) {
            initializeDataTable(language,deptcode,region,district)     
           } else {
            validator.focusInvalid();
        }
    });

    // reset_form(); // maybe remove if causing issues
}).catch(error => {
    console.error("Failed to load JSON data:", error);
});






$(document).ready(function () {

    function updateSelect($select) {
        let selectedValues = $select.val() || [];

        if (selectedValues.includes('A')) {
            // If "All" is selected, keep only A
            $select.val(['A']).trigger('change.select2');
            $select.find('option').not('[value="A"]').prop('disabled', true);

        } else {
            if (selectedValues.includes('')) {
                selectedValues = selectedValues.filter(v => v !== '');
                $select.val(selectedValues).select2();
            }

            $select.find('option').prop('disabled', false);
        }
    }

    function handleAllOption($select) {
        $select.on('change', function () {
            updateSelect($select);
        });

        if (!$select.val() || $select.val().length === 0) {
            $select.val(['']).select2();
        }

        updateSelect($select);
    }

    ['#deptcode','#distcode','#regioncode']
        .forEach(function (selector) {
            handleAllOption($(selector));
        });
});



let table;
    let dataFromServer = [];


    $(document).ready(function() {
        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();

        $('#checklistform')[0].reset();
        updateSelectColorByValue(document.querySelectorAll(".form-select"));
        getRegionBasedOnDept('');

        var lang = getLanguage();

    });


    $('#translate').change(function() {
        var lang = getLanguage('Y');
        // change_lang_for_page(lang);
        updateTableLanguage(lang);
       
        updateValidationMessages(getLanguage('Y'), 'checklistform');
    });

    function initializeDataTable(language,deptcode,region,district,data = []) {


        $.ajax({
            url: "{{ route('report.checklist_fetchData') }}",
            type: "POST",
            data: {    
                 deptcode: deptcode,
                 region: region,
                 district: district,
                    },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataSrc: "json",
            success: function(json) {
               // console.log("Success Response:", json);
                if (json.data && json.data.length > 0) {
                    $('#tableshow').show();
                    $('#usertable_wrapper').show();
                    $('#no_data').hide();
                    dataFromServer = json.data;
                    renderTable(language);
                } else {
                    $('#tableshow').hide();
                    $('#usertable_wrapper').hide();
                    $('#no_data').show();
                }
            },
            error: function() {
                $('#tableshow').hide();
                $('#no_data').show();
            }
        });
    }




    function renderTable(language) {
        const departmentColumn = language === 'ta' ? 'deptesname' : 'deptesname';
        const regionColumn = language === 'ta' ? 'regionename' : 'regionename';
                const districtColumn = language === 'ta' ? 'distename' : 'distename';

         const usernameColumn = language === 'ta' ? 'usertamilname' : 'username';
         const designationColumn = language === 'ta' ? 'desigtlname' : 'desigelname';

        if ($.fn.DataTable.isDataTable('#checklisttable')) {
            $('#checklisttable').DataTable().clear().destroy();
        }

        table = $('#checklisttable').DataTable({
            "processing": true,
            "serverSide": false,
            "lengthChange": false,
            "data": dataFromServer,
            columns: [{
                    data: null,
                    render: function(data, type, row, meta) {
                        return `<div >
                            <button class="toggle-row d-md-none" data-row='${JSON.stringify(row)}'>▶</button>${meta.row + 1}
                        </div>`;
                    },
                    className: 'text-end',
                    type: "num"
                },
                {
                    data: departmentColumn,
                    title: columnLabels?.[departmentColumn]?.[language],
                    render: function(data, type, row) {
                        return row[departmentColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                 {
                    data: regionColumn,
                    title: columnLabels?.[regionColumn]?.[language],
                    render: function(data, type, row) {
                        return row[regionColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                 {
                    data: districtColumn,
                    title: columnLabels?.[districtColumn]?.[language],
                    render: function(data, type, row) {
                        return row[districtColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                {
                    data: usernameColumn,
                    title: columnLabels?.[usernameColumn]?.[language],
                    render: function(data, type, row) {
                        return row[usernameColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
                
                {
                    data: designationColumn,
                    title: columnLabels?.[designationColumn]?.[language],
                    render: function(data, type, row) {
                        return row[designationColumn] || '-';
                    },
                    className: 'text-wrap text-start' // Removed col-1
                },
              
            ],

            "initComplete": function(settings, json) {
                $("#checklisttable").wrap(
                    "<div style='overflow:auto; width:100%;position:relative;'></div>");
            },

        });

        const mobileColumns = ["username"];
        setupMobileRowToggle(mobileColumns);

        updatedatatable(language, "checklisttable"); // Title: "Call for Records"
    }

    function updateTableLanguage(language) {
        if ($.fn.DataTable.isDataTable('#checklisttable')) {
            $('#checklisttable').DataTable().clear().destroy();
        }
        renderTable(language);
    }




    function onchangedistrict(deptcode, region,district) {
        const lang=getLanguage();


      
        if (!deptcode) {
        deptcode = $("#deptcode").val();
        }
        if (!region) {
            region = $("#regioncode").val();
        }
        district = $("#distcode").val() || [];
        if (!Array.isArray(district)) {
            district = [district];
        }

        district = district.filter(v => v && v !== 'undefined');

        


            if (deptcode && region) {
                $.ajax({
                    url: "/getdistrictofchecklist",
                    type: "POST",
                    data: {
                        deptcode : deptcode,
                        region: region,    
                        district: district,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        if (response.success && response.data.length > 0) {
                         initializeDataTable(lang, deptcode, region, district, response.data);

                    } else {
                        initializeDataTable(lang, deptcode, region,district, []);

                    }

                    },
                    error: function() {
                        alert('Error fetching district. Please try again.');
                    }
                });
            }
        }





        function getDistrictBasedOnRegion(deptcode, region) {
            const lang=getLanguage();
            const districtDropdown = $('#distcode');

       
            districtDropdown.empty();


          
            districtDropdown.empty().append(`
                <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">
                    ${lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select District'}
                </option>
                <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                    ${lang === 'ta' ? 'அனைத்து' : 'All'}
                </option>
            `);

            $('#distcode').val(['']).select2();



            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
            }
            if (region == "") {
                var region = $("#regioncode").val();
            }

           
            if (deptcode) {
                $.ajax({
                    url: "/getdistrictbasedonregionchecklist",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region || null,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(district => {

                                if (district.distcode && district.distcode !== 'undefined') {
                                districtDropdown.append(`
                                    <option value="${district.distcode}" 
                                    data-name-en="${district.distename || ''}"
                                    data-name-ta="${district.disttname || ''}">
                                    ${district.distename || ''}
                                    </option>
                                `);
                            }
                            });

                            $('#distcode').val(['']).select2();


                        } else{

                        }

                       // initializeDataTable(lang,deptcode,region);

                    },
                    error: function() {
                        alert('Error fetching district. Please try again.');
                    }
                });
            }
        }


        var sessiondeptcode = ' <?php echo $deptcode; ?>';

var sessionregioncode = '<?php echo $regioncode; ?>';
var sessiondistcode = '<?php echo $distcode; ?>';




if (
    (!sessiondeptcode || sessiondeptcode.trim() === '') &&
    (!sessionregioncode || sessionregioncode.trim() === '') &&
    (!sessiondistcode || sessiondistcode.trim() === '')
) {
    $('#regioncode').val(['']).select2();
    $('#distcode').val(['']).select2();

}



        function getRegionBasedOnDept(deptcode) {
            const lang=getLanguage();

                    const districtDropdown = $('#distcode');
                    const regionDropdown = $('#regioncode');




                
                    if (
                    (!sessiondeptcode || sessiondeptcode.trim() === '') &&
                    (!sessionregioncode || sessionregioncode.trim() === '') &&
                    (!sessiondistcode || sessiondistcode.trim() === '')
                ) {

                    regionDropdown.empty();

                    
                    // Empty and append defaults for region
                    regionDropdown.empty().append(`
                        <option value="" data-name-en="Select Region" data-name-ta="மண்டலத்தைத் தேர்ந்தெடுக்கவும்">
                            ${lang === 'ta' ? 'மண்டலத்தைத் தேர்ந்தெடுக்கவும்' : 'Select Region'}
                        </option>
                        <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                            ${lang === 'ta' ? 'அனைத்து' : 'All'}
                        </option>
                    `);

                    // Empty and append defaults for district
                    districtDropdown.empty().append(`
                        <option value="" data-name-en="Select District" data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">
                            ${lang === 'ta' ? 'மாவட்டத்தைத் தேர்ந்தெடுக்கவும்' : 'Select District'}
                        </option>
                        <option value="A" data-name-en="All" data-name-ta="அனைத்து">
                            ${lang === 'ta' ? 'அனைத்து' : 'All'}
                        </option>
                    `);

                    $('#regioncode').val(['']).select2();
                    $('#distcode').val(['']).select2();


                }
                        var deptcode = $("#deptcode").val();

                    if (deptcode) {
                        $.ajax({
                            url: "/getregionbasedondeptforchecklist",
                            type: "POST",
                            data: {
                                deptcode: deptcode,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {

                                if (response.success && response.data.length > 0) {
                                    response.data.forEach(region => {
                                        regionDropdown.append(
                                            `<option value="${region.regioncode}"
                                            data-name-en="${region.regionename}"
                                            data-name-ta="${region.regiontname}">${region.regionename}</option>`
                                        );

                                    });

                                if (
                                    (!sessiondeptcode || sessiondeptcode.trim() === '') &&
                                    (!sessionregioncode || sessionregioncode.trim() === '') &&
                                    (!sessiondistcode || sessiondistcode.trim() === '')
                                ) {
                                    $('#regioncode').val(['']).select2();
                                    $('#distcode').val(['']).select2();


                                }

                              //  initializeDataTable(lang,deptcode);



                                } 

                            },
                            error: function() {
                                alert('Error fetching region. Please try again.');
                            }
                        });
                    }
                }



        
         


        $(document).ready(function () {
            var sessionrole = '<?php echo $sessionroletypecode; ?>';
            var dgarole = '<?php echo $dga_roletypecode; ?>';
            var distrole = '<?php echo $Dist_roletypecode; ?>';
            var regionrole = '<?php echo $Re_roletypecode; ?>';
            var headofficerole = '<?php echo $Ho_roletypecode; ?>';
            var adminrole = '<?php echo $Admin_roletypecode; ?>';

            var deptcode = $("#deptcode").val();
            var region = $("#regioncode").val();
            var district = $("#distcode").val();

            if (!deptcode || !region || !district) {
                deptcode = deptcode || '';
                region = region || '';     
                district = district || '';
            }

           
            if (sessionrole == distrole ) {
                //onchangedistrict(deptcode,region,district)

            }

            else if (sessionrole == dgarole ) {
                $('#distcode').val('').select2('');
                $('#regioncode').val('').select2('');
                $('#deptcode').val('').select2('');
            }
          
            else if(sessionrole == regionrole){

                getDistrictBasedOnRegion(deptcode,region)
            }
            else if(sessionrole == headofficerole)
            {
                getRegionBasedOnDept(deptcode)
            }
            
     });

     var sessionrole = '<?php echo $sessionroletypecode; ?>'
     var regionrole = '<?php echo $Re_roletypecode; ?>'
     var headofficerole = '<?php echo $Ho_roletypecode; ?>'


     $(document).ready(function () {

    if (sessionrole === headofficerole) {
        $('#regioncode').val(['']).select2();
        $('#distcode').val('').select2('');
    }
});









    function reset_form() {
        // $('#checklistform')[0].reset();
        // $('#checklistform').validate().resetForm();
        // $('#deptcode').val(null).select2();

        // getRegionBasedOnDept(null);

        // getDistrictBasedOnRegion(null);

        var sessionrole = '<?php echo $sessionroletypecode; ?>'
        var dgarole = '<?php echo $dga_roletypecode; ?>'
        var distrole = '<?php echo $Dist_roletypecode; ?>'
        var regionrole = '<?php echo $Re_roletypecode; ?>'
        var headofficerole = '<?php echo $Ho_roletypecode; ?>'
        var adminrole = '<?php echo $Admin_roletypecode; ?>'

         
        $('#tableshow').hide();
        $('#usertable_wrapper').show();
        $('#no_data').show();

        $('#checklistform')[0].reset();

        var form = $('#checklistform');

            form[0].reset();    


            // Reset validation messages
            if (form.data('validator')) {
            form.validate().resetForm();
            form.find('.error').removeClass('error');
            }


        if (sessionrole == distrole) {
            
           
        } else if (sessionrole == regionrole) {
            $('#distcode').select2('destroy');
            $('#distcode').select2(null);
            $('#distcode').select2();
            $('#distcode').val('').select2();

        } else if (sessionrole == headofficerole) {

            $('#distcode,#regioncode').select2('destroy');
            $('#distcode,#regioncode').select2(null);
            $('#distcode,#regioncode').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();
           // onchangedistrict('deptcode','regioncode')

        } else if (sessionrole == dgarole) {
            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();
            $('#regioncode').val('').select2();
            $('#distcode').val('').select2();
            $('#deptcode').val('').select2();


             } else if (sessionrole == adminrole) {
            $('#deptcode,#distcode,#regioncode').select2('destroy');
            $('#deptcode,#distcode,#regioncode').select2(null);
            $('#deptcode,#distcode,#regioncode').select2();
            
          //  getInstData(lang);
        }


       // changeButtonAction('checklistform', 'action', 'buttonaction', 'reset_button', 'display_error', @json($clearbtn))

       // updateSelectColorByValue(document.querySelectorAll(".form-select"));
    }
</script>


@endsection



