@extends('layouts.theme')
@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
    <!-- Row Group CSS -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    @vite('resources/css/custom/timetracker.css')
@endsection
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center bd-container pr-0">
			<div class="d-flex justify-content-between   flex-column ">
				<h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6 d-flex">
					<span class="proicons--bank icon"></span> Time Tracks
				</h5>
				<ul class="breadcrumb mt-1">
					<li class="breadcrumb-item">
						<a href="{{ route('dashboard') }}">Dashboard</a>
					</li>
					<li class="breadcrumb-item active">Time Tracks</li>
				</ul>
			</div>
			<a href="javascript:;" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addTimeTrack">
				+ Add Task
			</a>

		</div>		

		<div id="timetrackContainer"></div>

		<div id="paginationContainer" style="margin-top: 20px;"></div>
        <hr class="my-12" />
    </div>
	<!-- Add User Modal -->
	<div class="modal fade" id="addTimeTrack" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-xs modal-simple modal-edit-user">
			<div class="modal-content">
			    	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					<div class="">
					  <h4 class="">Add Task</h4>
					</div>
				<div class="modal-body">
				
					<form id="add_time_track" data-ajax="true" class="row g-6" method="post" action="{{ route('timetrack.store') }}">
						@csrf
						<div class="col-6">
							<label class="form-label" for="modalEditUserName">Date</label>
							<input type="text" id="time_track_date" name="time_track_date" class="form-control" required />
						</div>
						<div class="col-6">
							<label class="form-label">Project</label>
							<select id="projectFilter" class="form-select" name="project_id" required>
							</select>						
						</div>						
						
						<div class="col-12">
							<label class="form-label">Task Description:</label>
							<textarea class="form-control" id="description" name="description" rows="3" required maxlength="100"></textarea>
							<small class="text-muted">
								<span id="descriptionCount">0</span>/100 characters
							</small>							
						</div>	
						<div class="col-md-6">
							<label class="form-label">Start Time (HH:MM)</label>
							<input type="text" id="start_time" name="start_time" class="form-control tt-time-input" placeholder="Start Time" required>
							<span class="error-text" id="start_time_error"></span>
						</div>
						<div class="col-md-6">
							<label class="form-label">End Time (HH:MM)</label>
							<input type="text" id="end_time" name="end_time" class="form-control tt-time-input" placeholder="End Time" required>
							<span class="error-text" id="end_time_error"></span>
						</div>
						<div class="col-12 text-center">
							<button type="submit" class="btn btn-primary me-3">Submit</button>
							<button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>	
    <div class="modal fade" id="deleteTimeTrackConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog delete-modal modal-simple modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="mb-4">
                        <h5 class="mb-2 text-danger">Delete Time Track</h5>
                        <p>Are you sure you want to cancel this timetrack info?</p>
                    </div>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-danger" id="confirmDeleteTimeTrackBtn">Yes, I am sure</button>
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>	
	
@endsection
@section('scripts')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('assets/js/tables-datatables-basic.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/js/forms-selects.js') }}"></script>
	<script>
		window.TimeTrack = {
			csrf_token: "{{ csrf_token() }}",
			routes: {
				groupedUrl: "{{ route('timetrack.grouped_ajax') }}",
				storeUrl: "{{ route('timetrack.store') }}",
				deleteUrl: "{{ route('timetrack.delete') }}",
				projectSearchUrl: "{{ route('time_tracker.project_search') }}"
			}
		};
	</script>
	@vite('resources/js/custom/timetracker.js')
	
@endsection