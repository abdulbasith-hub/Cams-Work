<? ?>
@section('content')
@extends('index2')
@include('common.alert')
@section('title', 'Spillover Schedulex')

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

	table.dataTable td,
	table.dataTable th {
		word-wrap: break-word;
		white-space: normal;
	}
</style>
@php
$spilloverdetails=json_decode($getplandetails,true);
$scheduledflag =$spilloverdetails[0]['scheduledflag'];
@endphp
<link rel="stylesheet" href="../assets/libs/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="../assets/libs/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css">
<div class="col-12">
	<div class="card">
		<div class="card-header card_header_color lang" key="">Spillover Institution Details</div>
		<div class="card-body">
			<div class="row">
				<input type="hidden" name="instid" id="instid" value="{{ $getplandetails->first()->encrypted_instid ?? '' }}">

				<div class="col-md-3 mb-1 mt-1">
					<label class="form-label lang " key="inst">Institution</label>

					<input class="form-control lang_value" data-en="{{ $getplandetails->first()->instename ?? '' }}" data-ta="{{ $getplandetails->first()->insttname ?? '' }}" value="{{ $getplandetails->first()->instename ?? '' }}" disabled>
				</div>

				<div class="col-md-3 mb-1  mt-1">
					<label class="form-label lang lang_value" key="auditquarter">Audit Quarter</label>

					<input class="form-control lang_value" data-en="{{ $getplandetails->first()->auditquarter ?? '' }}" data-ta="{{ $getplandetails->first()->auditquarter ?? '' }}" value="{{ $getplandetails->first()->auditquarter ?? '' }}" disabled>
				</div>

				<div class="col-md-3 mb-1  mt-1">
					<label class="form-label lang " key="fromdate">From Date</label>

					<input class="form-control " value="{{ $getplandetails->first()->fromdate ?? '' }}" disabled>
				</div>

				<div class="col-md-3 mb-1  mt-1">
					<label class="form-label lang " key="todate">To Date</label>

					<input class="form-control " value="{{ $getplandetails->first()->todate ?? '' }}" disabled>
				</div>

				<div class="col-md-3 mb-1  mt-1">
					<label class="form-label lang " key="teamsize">Teamsize</label>

					<input class="form-control " value="{{ $getplandetails->first()->team_member_count ?? '' }}" disabled>
				</div>

				<!-- <div class="col-md-3 mb-1  mt-1">
                    <label class="form-label lang " key="mandays">Mandays</label>

                    <input class="form-control " value="{{ $getplandetails->first()->mandays ?? '' }}" disabled>
                </div> -->

				<div class="col-md-3 mb-1  mt-1">
					<label class="form-label lang " key="teamHead">Team Head</label>

					<input class="form-control lang_value" data-en="{{ $getplandetails->first()->team_head_en ?? '' }}" data-ta="{{ $getplandetails->first()->team_head_ta ?? '' }}" value="{{ $getplandetails->first()->team_head_en ?? '' }}" disabled>
				</div>

				<div class="col-md-3 mb-1  mt-1">
					<label class="form-label lang " key="members">Members</label>

					<textarea class="form-control lang_value" data-en="{{ $getplandetails->first()->team_head_en ?? '' }}" data-ta="{{ $getplandetails->first()->team_members_en ?? '' }}" value="{{ $getplandetails->first()->team_members_ta ?? '' }}" disabled>{{ $getplandetails->first()->team_members_en ?? '' }}</textarea>
				</div>
			</div>

			<div id="statusmessage" class="row hide_this">
				<div class="col-md-8 ms-4 mt-4"><span class="required"></span>
					<span class="lang" key="spillover_success">Institution has been scheduled successfully</span>
				</div>
			</div>

			<div class="row justify-content-center hide_this" id="buttonset">
				<div class="col-md-3 mx-auto">
					<input type="hidden" name="action" id="action" value="insert" />
					<button class="btn button_save mt-3 lang" key="Schedule" type="submit"
						action="insert" id="schedule_btn" name="schedule_btn">Schedule</button>
				</div>
			</div>

		</div>
	</div>
</div>

<script src="../assets/js/vendor.min.js"></script>
<script src="../assets/js/jquery.js"></script>
<script src="../assets/libs/jquery-validation/dist/jquery.validate.min.js"></script>


<!-- solar icons -->
<script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>

{{-- data table --}}
<script src="../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>


<script src="../assets/js/datatable/datatable-advanced.init.js"></script>
<script>
	$(document).ready(function() {
		var schdeuledflag = '<?php echo $scheduledflag; ?>';
		if (schdeuledflag == 'Y') {
			$('#statusmessage').show();
			$('#buttonset').hide();
		} else {
			$('#statusmessage').hide();
			$('#buttonset').show();
		}

	});

	$('#translate').change(function() {
		const lang = getLanguage('Y');

		switchChecklistLanguage(lang)
	});

	function switchChecklistLanguage(lang) {

		$('.lang_value').each(function() {
			const ta = $(this).attr('data-ta');
			const en = $(this).attr('data-en');
			$(this).val(lang === 'ta' ? ta : en);
		});
	}

	$(document).on('click', '#schedule_btn', function() {
		event.preventDefault();

		$('#schedule_btn').attr('disabled', true);
		$('#process_button').off('click').on('click', function(event) {
			event.preventDefault();
			$('#confirmation_alert').modal('hide');
			spilloverschedule()
		});

		passing_alert_value('Confirmation', 'Are you sure to transfer charge?', 'confirmation_alert',
			'alert_header',
			'alert_body', 'forward_alert');
	})

	function spilloverschedule() {
		var instid = $('#instid').val();
		$.ajax({
			url: "/spillover/chargetakingover",
			type: "POST",
			data: {
				instid: instid,
			},
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			success: function(response) {
				if (response.success) {
					$('#statusmessage').show();
					$('#buttonset').hide();

					$('#ok_button').off('click').on('click', function(event) {
						event.preventDefault();
						$('#confirmation_alert').modal('hide');

						window.location.href = '/init_fieldaudit';
					});
					passing_alert_value('Confirmation', 'Charge Transover is successfull ', 'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');


				} else {
					passing_alert_value('Confirmation', response.message, 'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');

				}

			},
			complete: function() {
				$('#process_button').removeAttr('disabled');
				$('#schedule_btn').removeAttr('disabled');
			},
			error: function(xhr, status, error) {
				var response = JSON.parse(xhr.responseText);

				passing_alert_value('Alert', response.message,
					'confirmation_alert', 'alert_header', 'alert_body', 'confirmation_alert');
			}
		});
	}
</script>
@endsection