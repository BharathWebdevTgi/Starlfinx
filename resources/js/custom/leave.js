
const csrf_token = window.Leave.csrf_token;

const storeUrl = window.Leave.routes.storeUrl;

$(document).ready(function () {
	
	let deleteUrl = "";

	var table = $('#simpleTable').DataTable({
		order: [],
		pageLength: 25,
		ordering: false
	});
			
			
	$("div.dt-search").append('<button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addLeave" style="margin-left:10px">Apply Leave</button>');		
	
	let leaveStartDate = null, leaveEndDate = null

	// initialize flatpickr instances
	leaveStartDate = flatpickr("#start_date", {
		altInput: true,
		altFormat: "d/m/Y",
		dateFormat: "Y-m-d",
		allowInput: false,
		
		
	});
	leaveEndDate = flatpickr("#end_date", {
		altInput: true,
		altFormat: "d/m/Y",
		dateFormat: "Y-m-d",
		allowInput: false,
	});		
	
	
	
	$('#addLeave').on('hidden.bs.modal', function () {
		
		$('#add_leave')[0].reset();
		flatpickr("#start_date").clear();
		flatpickr("#end_date").clear();
		$('#descriptionCount').text(0);
		
	});	
	
	$("#add_leave").on('submit', function(e){
		e.preventDefault();
		let form = $(this);
		
		const from = leaveStartDate?.selectedDates[0] ?? null;
		const to   = leaveEndDate?.selectedDates[0] ?? null;


		if (to !== null && from !== null && to < from) {
			p.error('End date must be the same or after Start date');
			return false;
		}

		$.ajax({
			url: storeUrl,
			type: "POST",
			data: {
				_token: csrf_token,
				description: $('#description').val().trim(),
				leave_type: $('#leave_type').val(),
				start_date: $('#start_date').val(),
				end_date: $('#end_date').val()
			},
			beforeSend: function () {
				form.find('button[type="submit"]').prop('disabled', true);
			},
			success: function (response) {
				if(!response.status) {
					p.error(response.message);
					form.find('button[type="submit"]').prop('disabled', false);
					return false;
				}
				
				$('#addLeave').modal('hide');
				p.success(response.message);
				setTimeout(function () {
					location.reload();
				}, 800);
			},

			error: function (xhr) {
				$('#addLeave').modal('hide');
				p.error('Something went wrong');
			}
		});
	});

	

	$(document).on("click", ".delete-btn", function () {
		deleteUrl = $(this).data("url");
	});

	$("#confirmDeleteBtn").click(function () {
		if (deleteUrl) {
			let form = document.getElementById('deleteForm');
			form.action = deleteUrl;
			blockUI("Processing — please wait...");
			form.submit();
		}
	});		
	
	$('#description').on('input', function () {
		$('#descriptionCount').text($(this).val().length);
	});		
});