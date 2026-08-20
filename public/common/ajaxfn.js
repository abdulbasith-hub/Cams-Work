function getDesignationBasedonDept(deptcode,editdesigcode,page,deptTexboxid,desigTextboxid)
{
    if(getLanguage() == 'en')
    {
        firstoption = 'Select Designation';
    }
    else  firstoption = 'பதவியை தேர்ந்தெடுக்கவும்';

    const defaultOption = "<option value='' data-name-en='Select Designation'  data-name-ta='பதவியை தேர்ந்தெடுக்கவும்' >"+firstoption+" </option>";

        const $dropdown = $("#"+desigTextboxid);

        // Get department code from DOM if not passed
        if (!deptcode) deptcode = $('#'+deptTexboxid).val();


        if (deptcode) {
            // Clear the dropdown and set the default option
            $dropdown.html(defaultOption);

            $.ajax({
                url: '/getDesignationBasedonDept',
                type: 'POST',
                data: {
                    deptcode: deptcode,
                    'page': page,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success && Array.isArray(response.data)) {
                        let options = defaultOption;

                        // Iterate through the roles and build options
                        // response.data.forEach(({
                        //     desigcode: code,
                        //     desigelname: name
                        // }) => {
                        //     if (code && name) {
                        //         const isSelected = (code === editdesigcode) ? "selected" : "";
                        //         options += `<option value="${code}" ${isSelected}>${name}</option>`;
                        //         desigtsname
                        //     }
                        // });

                        response.data.forEach(({ desigcode: code, desigelname: nameEn, desigtlname: nameTa }) => {
                            const isSelected = (code === editdesigcode) ? "selected" : "";
                            language    =   getLanguage()
                            const displayName = language === "ta" ? nameTa : nameEn; // Choose name based on selected language
                            options += `<option value="${code}" ${isSelected}
                                            data-name-en="${nameEn}"
                                            data-name-ta="${nameTa}">
                                            ${displayName}
                                        </option>`;
                        });

                        // Append the options to the dropdown
                        $dropdown.html(options);
                    } else {
                        console.error("Invalid response or data format:", response);
                    }
                },
                error: function(xhr) {
                    console.log(xhr);
                    let errorMessage = response.error;

                    if (xhr.responseText) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            console.error("Error parsing error response:", e);
                        }
                    }

                    passing_alert_value('Alert', errorMessage, 'confirmation_alert',
                        'alert_header', 'alert_body', 'confirmation_alert');
                }
            });

        } else {
            // Reset to default option if no department code is provided
            $dropdown.html(defaultOption);
        }
}
