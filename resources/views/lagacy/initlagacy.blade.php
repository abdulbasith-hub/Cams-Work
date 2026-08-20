@section('content')

    @extends('index2')
    @include('common.alert')
    @php
        $sessionchargedel = session('charge');
        // print_r($sessionchargedel);
        $deptcode = $sessionchargedel->deptcode;

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
    <link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">

    <div class="row">
        <div class="col-12">
            <div class="card card_border">
                <div class="card-header card_header_color lang" key="">Lagacy Entry</div>
                <div class="card-body">
                    <form id="initfollowupform" name="initfollowupform">
                        <!-- <input type="text" name="workallocation" id="workallocation"> -->
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="form-label lang required" key="department"
                                    for="validationDefault01">Department</label>
                                <input type="hidden" id="" name="" value="">
                                <select class="form-select mr-sm-2 lang-dropdown select2" id="deptcode" name="deptcode"
                                    <?php echo $make_dept_disable; ?> onchange="getCategoriesBasedOnDept('')">
                                    <option value="" data-name-en="---Select Department---"
                                        data-name-ta="--- துறையைத் தேர்ந்தெடுக்கவும்---">---Select Department---</option>

                                    @if (!empty($dept) && count($dept) > 0)
                                        @foreach ($dept as $department)
                                            <option value="{{ $department->deptcode }}"
                                                @if (old('dept', $deptcode) == $department->deptcode) selected @endif
                                                data-name-en="{{ $department->deptelname }}"
                                                data-name-ta="{{ $department->depttlname }}">
                                                {{ $department->deptelname }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled data-name-en="No Department Available"
                                            data-name-ta="துறைகள் எதுவும் இல்லை">No Departments Available</option>
                                    @endif
                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="Region" for="region">Region</label>
                                <select class="form-select mr-sm-2 select2 " <?php echo $make_region_disable; ?> id="regioncode"
                                    name="regioncode" onchange="getDistrictBasedOnRegion('','','','')">
                                    <option value="" data-name-en="Select a Region"
                                        data-name-ta="??????? ?????????????????">Select Region</option>

                                    @if (!empty($region) && count($region) > 0)
                                        @foreach ($region as $reg)
                                            <option value="{{ $reg->regioncode }}"
                                                @if (old('dept', $regioncode) == $reg->regioncode) selected @endif
                                                data-name-en="{{ $reg->regionename }}"
                                                data-name-ta="{{ $reg->regiontname }}">
                                                {{ $reg->regionename }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled data-name-en="No Regions Available"
                                            data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Regions
                                            Available
                                        </option>
                                    @endif

                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="district" for="district">District</label>
                                <select class="form-select mr-sm-2 select2 " <?php echo $make_dist_disable; ?> id="distcode"
                                    name="distcode" onchange="getcategoryBasedOndistrict('','','','')">
                                    <option value="" data-name-en="Select a District"
                                        data-name-ta="மாவட்டத்தைத் தேர்ந்தெடுக்கவும்">Select District</option>

                                    @if (!empty($district) && count($district) > 0)
                                        @foreach ($district as $dist)
                                            <option value="{{ $dist->distcode }}"
                                                @if (old('dept', $distcode) == $dist->distcode) selected @endif
                                                data-name-en="{{ $dist->distename }}"
                                                data-name-ta="{{ $dist->disttname }}">
                                                {{ $dist->distename }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option disabled data-name-en="No Department Available"
                                            data-name-ta="எந்த துறையும் கிடைக்கவில்லை">No Departments
                                            Available
                                        </option>
                                    @endif

                                </select>
                            </div>




                            <div class="col-md-4 mb-2">
                                <label class="form-label lang required" key="category"
                                    for="validationDefault01">Category</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="category" name="category"
                                    onchange="onchange_category('','','')">
                                    <option value="" data-name-en="---Select Category---"
                                        data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">---Select Category---</option>

                                    <option value="" disabled id="" data-name-en="No Category Available"
                                        data-name-ta="வகை கிடைக்கவில்லை">No Category Available</option>

                                </select>
                            </div>


                            <div class="col-md-4 mb-2 subcatdiv ">
                                <label class="form-label lang required" key="if_subcategory"
                                    for="subcategory">SubCategory</label>

                                <select class="form-select mr-sm-2 lang-dropdown select2" id="subcategory"
                                    onchange="onchange_subcategory('','','','','','')" name="subcategory">
                                    <option value="" data-name-en="---Select SubCategory---"
                                        data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">---Select SubCategory---
                                    </option>

                                    <option value="" disabled data-name-en="No SubCategory Available"
                                        data-name-ta="துணை வகை கிடைக்கவில்லை">No SubCategory Available</option>


                                </select>
                            </div>


                            <div class="col-md-4 mb-3">
                                <label class="form-label required lang" key="institution" for="institution">Auditable
                                    Institution</label>
                                <select class="form-select mr-sm-2 select2 " id="instid" name="instid">
                                    <option value="" data-name-en="Select Auditable Institution"
                                        data-name-ta="???????????? ?????????????????">Select Auditable Institution</option>




                                </select>
                            </div>




                            <div class="row ">
                                <div class="col-md-3 mx-auto text-center">
                                    <!-- Adding text-center to center the content inside -->
                                    <input type="hidden" name="action" id="action" value="insert" />

                                    <input type="hidden" name="auditeeschemeid" id="auditeeschemeid" value="" />

                                    <button class="btn btn-primary mt-3 lang" key="" type="submit"
                                        action="insert" id="buttonaction" name="buttonaction">Proceed</button>


                                </div>
                            </div>

                    </form>
                </div>
            </div>



        </div>
    </div>
    <!-- Include jQuery and Bootstrap -->


    <script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <!-- Download Button Start -->

    <script src="../assets/js/download-button/buttons.min.js"></script>
    <script src="../assets/js/download-button/jszip.min.js"></script>
    <script src="../assets/js/download-button/buttons.print.min.js"></script>
    <script src="../assets/js/download-button/buttons.html5.min.js"></script>
    <script src="../assets/js/download-button/custom.xl.min.js"></script>



    <!-- select2 -->
    <script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
    <script src="../assets/libs/select2/dist/js/select2.min.js"></script>
    <script src="../assets/js/forms/select2.init.js"></script>

    <!-- Download Button End -->

    <script>
        let data = "";






        function onchange_subcategory(deptcode, region, district, category, subcategory, selecteinstitutioncode = null) {

            const institutionDropdown = $('#instid');

            institutionDropdown.html('<option value="">Select Audit Office</option>');
            const lang = getLanguage();

            // institutionDropdown.empty();


            var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
            var region = '<?php echo $regioncode; ?>' || $('#regioncode').val();
            var district = '<?php echo $distcode; ?>' || $('#distcode').val();

            var category = $("#category").val();

            if (subcategory == "") {
                var subcategory = $("#subcategory").val();
            }




            if (deptcode && region && district && category) {

                $.ajax({
                    url: "/getinstbasedonsubcatfollowup",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        district: district,
                        catcode: category,
                        subcatcode: subcategory,

                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        if (response.success && response.data.length > 0) {
                            response.data.forEach(institution => {
                                institutionDropdown.append(
                                    `<option value="${institution.instid}"
                                     data-name-en="${institution.instename}"
                                     data-name-ta="${institution.insttname}">${institution.instename}</option>`
                                );
                            });
                        } else {

                            institutionDropdown.append(`
                                <option disabled data-name-en="No Institution Available" data-name-ta="">
                                    ${lang === 'ta' ? '' : 'No Institution Available'}
                                </option>
                            `);
                        }

                    },
                    error: function() {
                        alert('Error fetching category. Please try again.');
                    }
                });
            }
        }




        function getcategoryBasedOndistrict(deptcode, region, district, selectedCatcode = null) {
            // alert('te');
            const catcodeDropdown = $('#category');
            const subcategoryDropdown = $('#subcategory');
            const lang = getLanguage();

            $('#category').empty();



            catcodeDropdown.html(`
            <option value="" data-name-en="---Select Category---" data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}
            </option>
            `);

            subcategoryDropdown.html(`
        <option value="" data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
        </option>
    `);





            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
            }
            if (region == "") {
                var region = $("#regioncode").val();
            }
            if (district == "") {
                var district = $("#distcode").val();
            }

            if (!deptcode && !region && !district) {


                catcodeDropdown.append(`
                <option value="" disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                    ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                </option>


                `);
                subcategoryDropdown.append(`
                            <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                                ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                            </option>
                        `);
            }



            if (deptcode && region && district) {
                $.ajax({
                    url: "/getcategorybasedondistfollowup",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        district: district,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {

                        data = response;


                        if (response.success && response.category.length > 0) {
                            response.category.forEach(category => {
                                catcodeDropdown.append(`
                                    <option value="${category.catcode}"
                                        data-name-en="${category.catename}"
                                        subcategory="${category.if_subcategory}"
                                        data-name-ta="${category.cattname}"
                                        ${category.catcode === selectedCatcode ? 'selected' : ''}>
                                        ${lang === 'ta' ? category.cattname : category.catename}
                                    </option>

                            `);

                            });

                        } else {

                            catcodeDropdown.append(`
                    <option disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
                    </option>
                `);
                        }
                        if (response.category[0].if_subcategory == 'N') {
                            $('.subcatdiv').hide()
                        }

                    },
                    error: function() {
                        alert('Error fetching category. Please try again.');
                    }
                });
            }
        }


        function getDistrictBasedOnRegion(deptcode, region, selecteDistrictcode = null) {
            // alert('te');
            const districtDropdown = $('#distcode');
            const institutionDropdown = $('#instid');

            districtDropdown.html('<option value="">Select District</option>');
            institutionDropdown.html('<option value="">Select Audit Office</option>');

            if (deptcode == "") {
                var deptcode = $("#deptcode").val();
                // alert(deptcode);
            }
            if (region == "") {
                var region = $("#regioncode").val();
                // alert(deptcode);
            }

            if (!region) {
                districtDropdown.append(`
                    <option value="" disabled id=""
                            data-name-en="No District Available"
                            data-name-ta="???????? ?????????????">
                            ${lang === 'ta' ? '???????? ?????????????' : 'No District Available'}
                    </option>
                `);


            }
            // institutionDropdown.append('<option value="" disabled>No Institution Available</option>');

            if (deptcode && region) {
                $.ajax({
                    url: "/getdistrictbasedonregionfollowup",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        region: region,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success && response.data.length > 0) {
                            response.data.forEach(district => {
                                districtDropdown.append(
                                    `<option value="${district.distcode}"
                                     data-name-en="${district.distename}"
                                   data-name-ta="${district.disttname}" ${
                                    district.distcode === selecteDistrictcode ? 'selected' : ''
                            }>${district.distename}</option>`
                                );
                            });
                        } else {
                            districtDropdown.append('<option disabled>No District Available</option>');
                        }
                    },
                    error: function() {
                        alert('Error fetching district. Please try again.');
                    }
                });
            }
        }













        function getCategoriesBasedOnDept(deptcode) {
            const catcodeDropdown = $('#category');
            const subcategoryDropdown = $('#subcategory');
            const regionDropdown = $('#regioncode')

            const lang = getLanguage();

            $('#category').empty();



            catcodeDropdown.html(`
            <option value="" data-name-en="---Select Category---" data-name-ta="---வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select Category---'}
            </option>
            `);

            subcategoryDropdown.html(`
        <option value="" data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
            ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
        </option>
    `);


            if (!deptcode) {
                deptcode = $("#deptcode").val();
            }

            if (!deptcode) {


                catcodeDropdown.append(`
        <option value="" disabled data-name-en="No Category Available" data-name-ta="வகை கிடைக்கவில்லை">
            ${lang === 'ta' ? 'வகை கிடைக்கவில்லை' : 'No Category Available'}
        </option>


`);
                subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                    </option>
                `);
            }

            if (deptcode) {
                $.ajax({
                    url: "/getcategoriesbasednndeptforfollowup",
                    type: "POST",
                    data: {
                        deptcode: deptcode,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {



                        if (response.success && response.regions.length > 0) {
                            response.regions.forEach(region => {
                                regionDropdown.append(
                                    `<option value="${region.regioncode}"
                                    data-name-en="${region.regionename}"
                                    data-name-ta="${region.regiontname}">${region.regionename}</option>`
                                );
                            });
                        } else {
                            regionDropdown.append('<option disabled>No Region Available</option>');
                        }





                    },
                    error: function() {
                        alert('Error fetching categories. Please try again.');
                    }
                });
            }
        }




        function onchange_category(catcode, selectedsubCatcode = null, subcategory) {
            var catcode = catcode || $('#category').val();
            var selectedOption = $('#category').find(':selected');
            var subcategory = subcategory || selectedOption.attr('subcategory');
            let lang = getLanguage();



            const subcategoryDropdown = $('#subcategory');
            subcategoryDropdown.empty();

            subcategoryDropdown.append(`
            <option value="" data-name-en="---Select SubCategory---" data-name-ta="---துணை வகையைத் தேர்ந்தெடுக்கவும்---">
                ${lang === 'ta' ? '---துணை வகையைத் தேர்ந்தெடுக்கவும்---' : '---Select SubCategory---'}
            </option>
        `);


            if (!catcode) {

                subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="துணை வகை கிடைக்கவில்லை">
                        ${lang === 'ta' ? 'துணை வகை கிடைக்கவில்லை' : 'No SubCategory Available'}
                    </option>
                `);

            }

            var deptcode = '<?php echo $deptcode; ?>' || $('#deptcode').val();
            var regioncode = '<?php echo $regioncode; ?>' || $('#regioncode').val();
            var distcode = '<?php echo $distcode; ?>' || $('#distcode').val();
            if (category == "") {
                var category = $("#category").val();
            }

            if (subcategory == 'N') {

                onchange_subcategory(deptcode, region, district, category, '', selecteinstitutioncode = null)

            }
            if (subcategory == 'Y') {
                $.ajax({
                    url: '/getsubcategoriesbasedondeptforfollowup', // Your API route to get user details
                    method: 'POST',
                    data: {
                        category: catcode
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token for security
                    },


                    success: function(response) {
                        if (subcategory === 'Y') {


                            if (response && response.length > 0) {

                                response.forEach(subcategory => {
                                    subcategoryDropdown.append(`
                        <option value="${subcategory.auditeeins_subcategoryid}"
                            data-name-en="${subcategory.subcatename}"
                            data-name-ta="${subcategory.subcattname}"
                            ${subcategory.auditeeins_subcategoryid === selectedsubCatcode ? 'selected' : ''}>
                            ${lang === 'ta' ? subcategory.subcattname : subcategory.subcatename}
                        </option>
                    `);

                                });

                            } else {
                                subcategoryDropdown.append(`
                    <option disabled data-name-en="No SubCategory Available" data-name-ta="???? ??? ?????????????">
                        ${lang === 'ta' ? '???? ??? ?????????????' : 'No SubCategory Available'}
                    </option>
                `);
                            }
                        } else {

                            $.each(data, function(i, subcategory) {
                                // alert("else");

                                if (subcategory.catcode === catcode) {
                                    $('#subcategory').append(
                                        `<option value="" data-name-en="${subcategory.catename}" data-name-ta="${subcategory.cattname}" selected>
                                  ${lang === "ta" ? subcategory.cattname : subcategory.catename}
                                 </option>`
                                    );
                                }
                            });


                        }

                    },
                    error: function(xhr, status, error) {
                        // alert('enter')


                    }
                });
            }
        }





        let table;
        let dataFromServer = [];

        var sessiondeptcode = ' <?php echo $deptcode; ?>';
        var sessiondistcode = ' <?php echo $distcode; ?>';
        var sessionregioncode = ' <?php echo $regioncode; ?>';

        $(document).ready(function() {
            $('#initfollowupform')[0].reset();

            var errorMessage = @json(session('errorMessage') ?? ($errorMessage ?? ''));
            var pageName = @json(session('pageName') ?? ($pageName ?? ''));



            if (errorMessage) {

                $('#close_button').hide();
                passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                    'alert_header', 'alert_body', 'confirmation_alert');

                $('#ok_button').off('click').on('click', function(event) {
                    event.preventDefault();
                    if (pageName == 'initlagacy') {
                        window.location.href = '/dashboard';
                    }


                    // If validation passes, manually close the modal

                });
            }

            updateSelectColorByValue(document.querySelectorAll(".form-select"));

            var lang = getLanguage();
            //  initializeDataTable(lang);

            if (sessiondeptcode && sessiondeptcode.trim() !== '') {

                getCategoriesBasedOnDept(sessiondeptcode);
                getcategoryBasedOndistrict(sessiondeptcode, sessionregioncode, sessiondistcode, '');

            }


        });


        $('#translate').change(function() {
            var lang = getLanguage('Y');
            updateTableLanguage(lang);
            changeButtonText('action', 'buttonaction', 'reset_button', @json($savebtn),
                @json($updatebtn), @json($clearbtn));
            updateValidationMessages(getLanguage('Y'), 'initfollowupform');
        });





        jsonLoadedPromise.then(() => {
            const language = window.localStorage.getItem('lang') || 'en';
            var validator = $("#initfollowupform").validate({

                rules: {
                    deptcode: {
                        required: true,
                    },
                    category: {
                        required: true
                    },
                    subcategory: {
                        required: true
                    },
                    regioncode: {
                        required: true
                    },
                    distcode: {
                        required: true
                    },
                    instid: {
                        required: true
                    },

                },
                errorPlacement: function(error, element) {
                    if (element.hasClass('select2')) {
                        error.insertAfter(element.next('.select2-container'));
                    } else {
                        error.insertAfter(element);
                    }
                },

            });


            $("#buttonaction").on("click", function(event) {
                event.preventDefault();

                if ($("#initfollowupform").valid()) {
                    const selectedCategoryVal = $("#category").val();
                    const selectedCategoryText = $("#category option:selected").text().trim();

                    const selectedSubcategoryVal = $("#subcategory").val();
                    const selectedSubcategoryText = $("#subcategory option:selected").text().trim();

                    const selectedinstitutionVal = $("#instid").val();
                    // const  = $("#subcategory option:selected").text().trim();

                    if (selectedCategoryVal) {
                        const url =
                            `/followup?inst=${encodeURIComponent(selectedinstitutionVal)}`;

                        // const url =
                        //     `/followup?cat=${encodeURIComponent(selectedCategoryVal)}&catname=${encodeURIComponent(selectedCategoryText)}&subcat=${encodeURIComponent(selectedSubcategoryVal)}&subcatname=${encodeURIComponent(selectedSubcategoryText)}`;

                        window.location.href = url;
                    } else {
                        alert("Please select both category and subcategory.");
                    }
                } else {
                    console.log("Form validation failed.");
                }
            });




        });
    </script>


@endsection
