@section('content')
@extends('index2')
@include('common.alert')
<style>
	.card_seperator {
		height: 10px;
		border: 0;
		box-shadow: 0 10px 10px -10px #8c8b8b inset;
	}

	.card-title {
		font-size: 15px;
	}

	.title-part-padding {
		background-color: #e3efff;
	}

	.card-body {
		padding: 15px 10px;
	}

	.card {
		margin-bottom: 10px;
	}

	.dataTables_info {
		margin-bottom: 1rem !important;
	}

	.modal.fullscreen-modal .modal-dialog {
		max-width: 100%;
		height: 100%;
		margin: 0;
	}

	.modal.fullscreen-modal .modal-content {
		height: 100%;
		border: none;
		border-radius: 0;
	}
</style>
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">

<?php $i = 1; ?>

<div class="card mb-3 card_border" style="max-width: 100%; font-size: 14px;">
	<div class="card-header card_header_color ">
		AuditSchedule Request Details
	</div>
	<div class="card-body py-2">
		<div class="row">
			<!-- Left: User Details -->
			<div class="col-md-6 border-end">
				<p class="mb-1">Institution Name:<strong> <?php echo $schedulrequestdel[0]->instename; ?></strong></p>
				<p class="mb-1">Teamsize: <strong> <?php echo $schedulrequestdel[0]->teamsize; ?></strong></p>
				<p class="mb-1">Head Name:<strong> <?php echo $schedulrequestdel[0]->teamhead; ?></strong></p>
				<p class="mb-1">Members Name:<strong> <?php echo $schedulrequestdel[0]->teammember; ?></strong></p>
				<p class="mb-1">EntryMeeting Date: <strong><?php echo date('d-m-Y', strtotime($schedulrequestdel[0]->entrymeetdate)); ?></strong></p>
				<p class="mb-1">Proposed ExitMeeting Date: <strong><?php echo date('d-m-Y', strtotime($schedulrequestdel[0]->oldpurposedexitmeetdate)); ?></strong></p>
			</div>

			<!-- Right: Transaction Details -->
			<?php
			$transactionType = $schedulrequestdel[0]->transactiontypelname;
			$transactionTypecode =  $schedulrequestdel[0]->transactiontypecode;
			?>
			<div class="col-md-6">
				<p class="mb-1">Transaction Name:<strong> <?php echo $transactionType; ?></strong></p>

				<p class="mb-1">Leave Details:<strong>

						<?php if ($schedulrequestdel[0]->transactiontypecode == '09') { ?>
						<?php
							$usercount = 0;
							$workingdayscount = 0;
							$mandayscount = 0;

							$html = '
							<table style="width:100%; border-collapse:collapse; font-size:14px;">
								<thead>
									<tr style="background:#f5f5f5;">
										<th style="padding:6px; border:1px solid #ddd; text-align:left;">Username (Designation)</th>
										<th style="padding:6px; border:1px solid #ddd; text-align:center;">Leave Period</th>
										<th style="padding:6px; border:1px solid #ddd; text-align:right;">Working Days</th>
									</tr>
								</thead>
								<tbody>
							';

							foreach ($schedulrequestdel as $item) {
								$fromDate = date('d-m-Y', strtotime($item->fromdate));
								$toDate = date('d-m-Y', strtotime($item->todate));

								$html .= '
									<tr>
										<td style="padding:6px; border:1px solid #ddd;">
											<strong>' . htmlspecialchars($item->username) . '</strong> (' . htmlspecialchars($item->desigesname) . ')
										</td>
										<td style="padding:6px; border:1px solid #ddd; text-align:center;">
											' . $fromDate . ' to ' . $toDate . '
										</td>
										<td style="padding:6px; border:1px solid #ddd; text-align:right;">
											<strong>' . intval($item->working_days) . '</strong>
										</td>
									</tr>';

								$usercount++;
								$workingdayscount += intval($item->working_days);
							}

							$mandayscount	=	$workingdayscount / $usercount;

							// Footer with totals
							$html .= '
								</tbody>
								<tfoot>
									<tr style="background:#f9f9f9; font-weight:bold;">
										<td style="padding:6px; border:1px solid #ddd;">Total Users: ' . $usercount . '</td>
										<td style="padding:6px; border:1px solid #ddd; text-align:center;"></td>
										<td style="padding:6px; border:1px solid #ddd; text-align:right;">' . $workingdayscount . ' Days</td>
									</tr>
								</tfoot>
							</table>
							';

							echo $html;
						} else {
							echo '<p>No data available.</p>';
						}
						?>
			</div>

		</div>
	</div>
</div>



<div class="row">
	<div class="col-12">
		<div class="card card_border">
			<!-- <div class="card-header card_header_color lang" key=""> Data Transfer</div> -->
			<div class="card-body collapse show">
				<form id="schedulerequest_form" name="schedulerequest_form">
					<?php
					if ($schedulrequestdel[0]->transactiontypecode == '09') { ?>
						<input type="hidden" id="mandaysextension" name="mandaysextension"
							value="<?php echo $schedulrequestdel[0]->mandaysextensionid; ?>"><?php
																							} else { ?>
						<input type="hidden" id="othertransid" name="othertransid"><?php } ?>

					<input type="hidden" id="transactiontypecode" name="transactiontypecode" value="<?php echo $schedulrequestdel[0]->transactiontypecode; ?>">
					@csrf
					<div class="card mb-4 card_border">
						<div class="card-header card_header_color lang" key="">
							Request for <?php echo $transactionType ?>
						</div>
						<div class="card-body p-0">
							<!-- <h5 class="p-3 mb-0">Schedule Pendings</h5> -->
							<div class="table-responsive">
								<table class="table table-bordered mb-0 text-center align-middle">
									<colgroup>
										<col style="width: 5%;">
										<col style="width: 25%;">
										<col style="width: 10%;">
										<col style="width: 15%;">

										<col style="width: 15%;">
									</colgroup>
									<thead>
										<tr style="vertical-align:middle">
											<th>S.No</th>
											<th>Institute Name</th>
											<th>Alloted Work</th>
											<th>Total No. of Slip</th>
											<th class="required">Extra Manday(s) to be Alloted </th>
											<th class="required">Remarks</th>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($schedulrequestdel as $item) { ?>
											<tr>
												<td><?= $i ?></td>
												<input type="hidden" class="auditscheduleid" name="auditscheduleid[]"
													value="<?php echo $item->auditscheduleid; ?>">
												<td>
													<span class="badge bg-success text-light px-2 py-1"
														style="cursor: pointer;"
														onclick="institutiondel(<?= $item->auditscheduleid ?>, event)">
														<?php echo htmlspecialchars($item->instename); ?>
													</span>
												</td>

												<td>
													<?php if ($item->workallocationflag == 'Y') { ?>
														<span class="badge bg-success text-light px-2 py-1"
															style="cursor: pointer;"
															onclick="getworkallocationdel(<?= $item->auditscheduleid ?>,'', event)">
															View Alloted Work
														</span>
													<?php } else { ?>
														<span class="badge bg-secondary">No Work</span>
													<?php } ?>
												</td>



												<td>
													<?php if ($item->slipcount > 0) { ?>
														<button type="button" class="btn btn-sm btn-outline-success"
															onclick="getslipallocationdel(<?= $item->auditscheduleid ?>, '', event)">
															<?= $item->slipcount ?>
														</button>
													<?php } else { ?>
														<span class="badge bg-secondary">No Slip</span>
													<?php } ?>
												</td>

												<td>
													<?php if ($schedulrequestdel[0]->transactiontypecode == '09') { ?>
														<input type="text" class="form-control required only_numbers" name="extramandays" id="extramandays"
															value="" maxlength="2">
													<?php } else { ?>
														<span class="badge bg-secondary">No Slip</span>
													<?php } ?>
												</td>
												<td>
													<?php if ($schedulrequestdel[0]->transactiontypecode == '09') { ?>
														<textarea id="remarks" class="form-control required alpha_numeric" name="remarks"></textarea>
													<?php }  ?>
												</td>



											</tr>
										<?php $i++;
										} ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<div class="row justify-content-center">
						<div class="col-md-2 mx-auto">


							<input type="hidden" name="action" id="action" value="insert" />
							<button class="btn button_save mb-3" type="submit" action="insert" id="buttonaction"
								name="buttonaction">Submit</button>


							<button type="button" class="btn btn-danger mb-3" id="reset_button">Clear</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>




<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.full.min.js"></script>
<script src="../assets/libs/select2/dist/js/select2.min.js"></script>
<script src="../assets/js/forms/select2.init.js"></script>

<!-- solar icons -->
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="../assets/js/datatable/datatable-advanced.init.js"></script>

<script>
	var oldMandays = '<?php echo $mandayscount ?>';

	$(document).ready(function() {

		// Add custom method: value must be > 0
		$.validator.addMethod("greaterThanZero", function(value, element) {
			return this.optional(element) || parseInt(value) > 0;
		}, "Value must be greater than 0.");

		// Add custom method: value must be <= oldMandays
		$.validator.addMethod("notMoreThanOldMandays", function(value, element) {
			return this.optional(element) || parseInt(value) <= oldMandays;
		}, "Cannot exceed old mandays.");

		$.validator.addMethod("alphanumericWithSpaces", function(value, element) {
			return this.optional(element) || /^[\u0B80-\u0BFFa-zA-Z0-9 ]+$/.test(value);
		}, "Only Tamil, English letters, numbers, and spaces are allowed.");


		$("#schedulerequest_form").validate({
			rules: {
				'extramandays': {
					required: function() {
						return $('#transactiontypecode').val() === '09';
					},
					digits: true,
					greaterThanZero: true,
					notMoreThanOldMandays: true
				},
				remarks: {
					required: true,
					alphanumericWithSpaces: true
				}
			},
			messages: {
				'extramandays': {
					required: "Please enter extra mandays.",
					digits: "Only numeric values are allowed.",
					greaterThanZero: "Must be greater than 0.",
					notMoreThanOldMandays: "Cannot exceed the request mandays (" + oldMandays + ")"

				}
			},
			errorClass: "is-invalid",
			// validClass: "is-valid",
			errorElement: "div",
			errorPlacement: function(error, element) {
				error.addClass("invalid-feedback");
				element.closest("td").append(error);
			},
			submitHandler: function(form) {


				mandays = '<?= $schedulrequestdel[0]->oldmandays ?>'
				extramandays = $('#extramandays').val();

				totalmandys = parseInt(extramandays);


				// teamsize = '<?php echo $schedulrequestdel[0]->entrymeetdate ?>';
				// entrymeetdate = $('#entrymeetingdate').val();
				workingdays = totalmandys / '<?= $schedulrequestdel[0]->teamsize ?>';


				newpurposedexitmeetdate = calculateToDate('<?php echo $schedulrequestdel[0]->oldpurposedexitmeetdate ?>', workingdays, 'next');




				let data = 'Are you sure to approve extra  <b>' +
					$('#extramandays').val() +
					'</b> manday(s) with new proposed Exitmeeting date <b>' +
					newpurposedexitmeetdate +
					'</b>?';


				$('#process_button').off('click').on('click', function(event) {
					event.preventDefault();
					// If validation passes, manually close the modal
					$('#confirmation_alert').modal('hide');
					approverequest(newpurposedexitmeetdate)
				});


				passing_alert_value('Confirmation', data, 'confirmation_alert',
					'alert_header', 'alert_body',
					'forward_alert');





				// var formData = $('#schedulerequest_form').serializeArray();
				// $.ajax({
				// 	url: '/schedulerequestchanges',
				// 	type: 'POST',
				// 	data: formData,
				// 	success: function(response) {
				// 		if (response.success) {
				// 			reset_form();

				// 			getLabels_jsonlayout([{
				// 				id: response.success,
				// 				key: response.success
				// 			}], 'N').then((text) => {
				// 				passing_alert_value('Confirmation', Object
				// 					.values(text)[0], 'confirmation_alert',
				// 					'alert_header', 'alert_body',
				// 					'confirmation_alert');
				// 			});


				// 			initializeDataTable(window.localStorage.getItem('lang'));
				// 		} else if (response.error) {


				// 		}
				// 	},
				// 	error: function(xhr, status, error) {
				// 		var response = JSON.parse(xhr.responseText);
				// 		if (response.error == 401) {
				// 			alert('error on insert')
				// 			//    handleUnauthorizedError();
				// 		} else {

				// 			getLabels_jsonlayout([{
				// 				id: response.message,
				// 				key: response.message
				// 			}], 'N').then((text) => {
				// 				let alertMessage = Object.values(text)[0] ||
				// 					"Error Occured";
				// 				passing_alert_value('Confirmation',
				// 					alertMessage, 'confirmation_alert',
				// 					'alert_header', 'alert_body',
				// 					'confirmation_alert');
				// 			});
				// 		}



				// 	}
				// });
			}
		});
	});

	const holidayList = @json($holidays);

	function calculateToDate(fromDateStr, totalWorkingDays, datetakenfrom) {
		const holidaySet = new Set(holidayList);
		let fromDate = new Date(fromDateStr);
		let startDate = new Date(fromDate); // Clone the date

		const isWorkingDay = (date) => {
			const day = date.getDay();
			const formatted = date.toISOString().split('T')[0];
			return day !== 0 && day !== 6 && !holidaySet.has(formatted);
		};

		// Adjust the start date based on datetakenfrom value
		if (datetakenfrom === 'next') {
			// Move to the next working day
			do {
				startDate.setDate(startDate.getDate() + 1);
			} while (!isWorkingDay(startDate));
		} else if (datetakenfrom === 'today') {
			// If today is not a working day, move to the next working day
			if (!isWorkingDay(startDate)) {
				do {
					startDate.setDate(startDate.getDate() + 1);
				} while (!isWorkingDay(startDate));
			}
		}

		// Now calculate the final date after totalWorkingDays
		let count = 0;
		let currentDate = new Date(startDate); // Start from the adjusted startDate

		while (count < totalWorkingDays) {
			if (isWorkingDay(currentDate)) {
				count++;
			}
			if (count < totalWorkingDays) {
				currentDate.setDate(currentDate.getDate() + 1);
			}
		}

		// Format the final date as dd-mm-yyyy
		const day = String(currentDate.getDate()).padStart(2, '0');
		const month = String(currentDate.getMonth() + 1).padStart(2, '0'); // Months are 0-based
		const year = currentDate.getFullYear();

		return `${day}-${month}-${year}`;
	}

	function approverequest(newpurposedexitmeetdate) {

		var formData = $('#schedulerequest_form').serializeArray();

		formData.push({
			name: 'newpurposedexitmeetdate',
			value: newpurposedexitmeetdate
		});


		$.ajax({
			url: '/schedulerequest_approve',
			type: 'POST',
			data: formData,
			success: function(response) {
				if (response.success) {
					// resetForm();


					$('#close_button').hide();

					$('#ok_button').off('click').on('click', function(event) {
						// Prevent the default behavior if necessary (optional)
						window.location.href = '/transactionflow';
					});

					passing_alert_value('Confirmation', response.message,
						'confirmation_alert', 'alert_header',
						'alert_body', 'confirmation_alert');
				} else if (response.error) {


				}
			},
			error: function(xhr, status, error) {
				var response = JSON.parse(xhr.responseText);
				if (response.error == 401) {
					handleUnauthorizedError();
				} else {

					getLabels_jsonlayout([{
						id: response.message,
						key: response.message
					}], 'N').then((text) => {
						let alertMessage = Object.values(text)[0] ||
							"Error Occured";
						passing_alert_value('Confirmation',
							alertMessage, 'confirmation_alert',
							'alert_header', 'alert_body',
							'confirmation_alert');
					});
				}
			}
		});



	}


	function institutiondel(auditscheduleid, event) {
		if (event) event.preventDefault(); //
		$.ajax({
			url: '/getinstitutiondel',
			method: 'POST',
			data: {
				auditscheduleid: auditscheduleid
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response) {


				function formatDate(inputDate) {
					if (inputDate) {
						const date = new Date(inputDate);
						const day = String(date.getDate()).padStart(2, '0');
						const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-indexed
						const year = String(date.getFullYear()).slice(-2); // Get last two digits
						return `${day}-${month}-${year}`;
					}
					return ' - ';
				}


				if (response.success && response.data.length > 0) {
					let tableHTML = `
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Institution Name</th>
                    <th>Team Head</th>
                    <th>Team Members</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>ManDays</th>
                    <th>Entry Date</th>
                    <th>Exit Date</th>
                </tr>
            </thead>
            <tbody>
`;

					response.data.forEach((item, index) => {
						tableHTML += `
        <tr>
            <td>${index + 1}</td>
            <td>${item.instename}</td>
            <td>${item.teamhead}</td>
            <td>${item.memberdel}</td>
            <td>${formatDate(item.fromdate)}</td>
            <td>${formatDate(item.todate)}</td>
            <td>${item.mandays}</td>
          <td>${formatDate(item.entrymeetdate)}</td>
        <td>${formatDate(item.exitmeetdate)}</td>
        </tr>
    `;
					});

					tableHTML += `
            </tbody>
        </table>
    </div>
`;


					// Pass it to your custom alert/modal system
					passing_large_alert(
						'Institution Details',
						tableHTML, // this becomes the body
						'large_confirmation_alert',
						'large_alert_header',
						'large_alert_body',
						'forward_alert'
					);

					$("#large_modal_process_button").html("Ok");
					$("#large_modal_process_button").removeAttr("button_finalize");
					$('#large_modal_process_button').addClass('data-bs-dismiss');
				} else {
					alert('No objections found.');
				}
			},

			error: function(xhr) {
				console.error('Error:', xhr.responseText || 'Unknown error');
			}
		});
	}




	function getworkallocationdel(auditscheduleid, schememberid, event) {
		if (event) event.preventDefault();

		$.ajax({
			url: '/getworkalloactionbasedonSchedulemember',
			method: 'POST',
			data: {
				auditscheduleid: auditscheduleid,
				schememberid: schememberid
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response) {
				if (response.success && response.data.length > 0) {
					const data = response.data;

					const language = getLanguage();


					const groupCol = language === 'ta' ? 'grouptname' : 'groupename';
					const workCol = language === 'ta' ? 'majorworkallocationtypeename' :
						'majorworkallocationtypeename';
					const groupLabel = language === 'ta' ? '????' : 'Group';
					const workLabel = language === 'ta' ? '??????? ???' : 'Work Allocation';

					// Group the data
					const grouped = {};
					data.forEach(item => {
						const group = item[groupCol] || '-';
						const work = item[workCol] || '-';
						if (!grouped[group]) grouped[group] = new Set();
						grouped[group].add(work);
					});

					// Generate table HTML as a string
					let tableHTML = `
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>S.No</th>
									<th>${groupLabel}</th>
									<th>${workLabel}</th>
								</tr>
							</thead>
							<tbody>
					`;

					let index = 1;
					for (const [group, works] of Object.entries(grouped)) {
						const workList = Array.from(works).map(w => `<span>${w}</span>`).join('<br>');
						tableHTML += `
							<tr>
								<td>${index++}</td>
								<td>${group}</td>
								<td>${workList}</td>
							</tr>
						`;
					}

					tableHTML += `
							</tbody>
						</table>
					`;

					// Pass it to your custom alert/modal system
					passing_large_alert(
						'Alloted Work',
						tableHTML, // this becomes the body
						'large_confirmation_alert',
						'large_alert_header',
						'large_alert_body',
						'forward_alert'
					);


					$("#large_modal_process_button").html("Ok");
					$("#large_modal_process_button").addClass("data-bs-dismiss");
					$('#large_modal_process_button').removeAttr('button_finalize');

				}
			},
			error: function(xhr) {
				console.error('Error:', xhr.responseText);
				$('#workallocation_container').html(
					`<p class="text-danger">${language === 'ta' ? '???? ?????????.' : 'An error occurred.'}</p>`
				);
			}
		});
	}


	function getslipallocationdel(auditscheduleid, schememberid, event) {
		if (event) event.preventDefault();

		$.ajax({
			url: '/getslipdetailsbasedon_schedulemember',
			method: 'POST',
			data: {
				auditscheduleid: auditscheduleid,
				schememberid: schememberid
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response) {
				if (response.success) {
					if (response.success && response.data.length > 0) {
						let tableHTML = `
						<table class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>S.No</th>
									<th>Main Objection (English)</th>
									<th>Sub Objection </th>
									<th>MainSlip Number </th>
										<th>Severity </th>
										<th>Status </th>
								</tr>
							</thead>
							<tbody>
					`;

						response.data.forEach((item, index) => {
							tableHTML += `
							<tr>
								<td>${index + 1}</td>
								<td>${item.objectionename}</td>
								<td>${item.subobjectionename}</td>
								<td>${item.mainslipnumber}</td>
								<td>${item.severityelname}</td>
								<td>${item.processelname}</td>


							</tr>
						`;
						});

						tableHTML += `
							</tbody>
						</table>
					`;

						// Pass it to your custom alert/modal system
						passing_large_alert(
							'Slip Details',
							tableHTML, // this becomes the body
							'large_confirmation_alert',
							'large_alert_header',
							'large_alert_body',
							'forward_alert'
						);

						$("#large_modal_process_button").html("Ok");
						$("#large_modal_process_button").addClass("button_finalize");
						$('#large_modal_process_button').removeAttr('data-bs-dismiss');
					} else {
						alert('No objections found.');
					}
				} else {
					alert('Charge not found');
				}
			},
			error: function(xhr) {
				console.error('Error:', xhr.responseText || 'Unknown error');
			}
		});
	}
</script>




@endsection