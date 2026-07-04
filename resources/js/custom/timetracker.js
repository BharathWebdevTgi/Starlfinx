
	const csrf_token = window.TimeTrack.csrf_token;

	const groupedUrl = window.TimeTrack.routes.groupedUrl;
	const storeUrl = window.TimeTrack.routes.storeUrl;
	const deleteUrl = window.TimeTrack.routes.deleteUrl;
	const projectSearchUrl = window.TimeTrack.routes.projectSearchUrl;	
	
	// Current time in HH:mm format
	let now = moment().format('HH:mm');

	$('#start_time, #end_time').val(now);

	let currentPage = 1;
		
	let deleteTrackId = '';
	
	var pagingClicked = false;
	
	$(document).ready(function() {
		
		function loadGroupedTracks(page = 1)
		{
			
			blockUI('Loading timetracks...');
			
			currentPage = page;
			
			$.ajax({
				url: groupedUrl,
				type: "GET",
				data: {
					page: page
				},
				success: function(res) {

					$('#timetrackContainer').html(res.html);

					if(res.pagination){
						$('#paginationContainer').html(res.pagination).show();
					} else {
						$('#paginationContainer').hide();
					}
					
					unblockUI();

					pagingClicked = false;
				},
				error: function(){

					unblockUI();

					pagingClicked = false;
				}
			});
		}			
		
		loadGroupedTracks(1);
		
		$(document).on('click','#paginationContainer .pagination a',function(e){

				e.preventDefault();

				let page = $(this).attr('href').split('page=')[1];

				blockUI('Loading timetracks...');

				pagingClicked = true;

				$('html, body').animate({
					scrollTop: $('#timetrackContainer').offset().top - 100
				}, 300);

				loadGroupedTracks(page);
			}
		);
		
	
		$(document).on('click', '.delete-track', function () {

			deleteTrackId = $(this).data('id');

			$('#deleteTimeTrackConfirmModal').modal('show');
		});
		
		$(document).on('click', '#confirmDeleteTimeTrackBtn', function(){

			if(!deleteTrackId){
				return;
			}

			//blockUI('Deleting timetrack...');

			$.ajax({

				url: deleteUrl,
				type: "POST",

				data: {
					_token: csrf_token,
					id: deleteTrackId
				},

				success: function(response){
					
					$('#deleteTimeTrackConfirmModal').modal('hide');
					
					if(!response.status){
						p.error(response.message);
						return;
					}

					p.success(response.message);

					// Remove deleted row
					$('.track-row[data-id="'+response.deleted_id+'"]').remove();

					// Update date total
					$('.date-total[data-date="'+response.date+'"]')
						.text(response.date_total);

					// No rows left for this date
					if(response.row_count === 0){

						$('.card[data-date="'+response.date+'"]').remove();

					}
					
				},

				error: function(){

					//unblockUI();

					p.error('Failed to delete timetrack');

				}
				
			});

		});			
		
		
		$('#projectFilter').select2({
			dropdownParent: $('#addTimeTrack'),
			width: '250px',
			placeholder: 'All Projects',
			allowClear: true,
			ajax: {
				url: projectSearchUrl,
				dataType: 'json',
				delay: 300,

				data: function (params) {
					return {
						term: params.term || '',
						page: params.page || 1
					};
				},

				processResults: function (data) {
					return {
						results: data.results,
						pagination: {
							more: data.pagination.more
						}
					};
				},

				cache: true
			}
		});			
		
		let time_track_date = flatpickr("#time_track_date", {
			dateFormat: "d/m/Y",
			allowInput: false,
			defaultDate: "today",
			maxDate: "today"
		});			

		$(document).on('input', '.tt-time-input', function() {
			let value = $(this).val();
			// Allow only digits and :
			value = value.replace(/[^0-9:]/g, '');
			// Auto insert :
			if (value.length === 2 && value.indexOf(':') === -1) {
				value += ':';
			}
			value = value.substring(0, 5);
			$(this).val(value);
		});
		
	
		function isValidTime(time) {
			return /^([01]\d|2[0-3]):([0-5]\d)$/.test(time);
		}			
		
		$("#add_time_track").on("submit", function(e) {

			e.preventDefault();
			
			$('#start_time_error, #end_time_error').text('');
			
			let form = $(this);

			let trackDate   = $('#time_track_date').val();

			let startInput = $('#start_time');
			let endInput   = $('#end_time');

			let startTime = startInput.val().trim();
			let endTime   = endInput.val().trim();

			let description = $('#description').val().trim();
			let projectId   = $('#projectFilter').val();

			if (!isValidTime(startTime)) {
				//$('#start_time_error').text("Start time must be in HH:MM format");
				p.error("Start time must be in HH:MM format");
				return false;
			}

			if (!isValidTime(endTime)) {
				//$('#end_time_error').text("End time must be in HH:MM format");
				p.error("End time must be in HH:MM format");
				return false;
			}

			if (startTime >= endTime) {
				//$('#start_time_error').text("Start time must be less than End time");
				p.error("Start time must be less than End time");
				return false;
			}
			
			let start = moment(startTime, "HH:mm");
			let end = moment(endTime, "HH:mm");				
			let diffHours = moment.duration(end.diff(start)).asHours();

			if (diffHours > 10) {
				//$('#start_time_error').text("Individual task should not exceeds 10 hours");
				p.error("Individual task should not exceeds 10 hours");
				return false;
			}				
			
			$.ajax({
				url: storeUrl,
				type: "POST",
				data: {
					_token: csrf_token,
					description: description,
					project_id: projectId,
					start_time: moment(
						trackDate + ' ' + startTime,
						'DD/MM/YYYY HH:mm'
					).format('YYYY-MM-DD HH:mm:ss'),

					end_time: moment(
						trackDate + ' ' + endTime,
						'DD/MM/YYYY HH:mm'
					).format('YYYY-MM-DD HH:mm:ss')
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
					
					$('#addTimeTrack').modal('hide');
					p.success(response.message);
					loadGroupedTracks(1);
				},

				error: function (xhr) {
					$('#addTimeTrack').modal('hide');
					p.error('Something went wrong');
				}
			});						
		});
		$('#description').on('input', function () {
			$('#descriptionCount').text($(this).val().length);
		});		
		$('#addTimeTrack').on('hidden.bs.modal', function () {
			$('#add_time_track').trigger("reset");
			$('#projectFilter').val(null).trigger('change');
			$('#add_time_track').find('button[type="submit"]').prop('disabled', false);
			$('#start_time, #end_time').val(moment().format('HH:mm'));
			$('#start_time_error, #end_time_error').text('');
			$('#descriptionCount').text(0);
		});		
		$('#description').on('input', function () {
			$('#descriptionCount').text($(this).val().length);
		});			
	});