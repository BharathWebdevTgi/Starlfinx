@extends('layouts.theme')
@section('styles')
	<link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
	<!-- Row Group CSS -->
	<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/form-validation.css') }}" />




@endsection
@section('content')
	<div class="container-xxl flex-grow-1 container-p-y">
	     <div class="d-flex justify-content-between align-item-center bd-container">
			<h5 class="card-title mb-0 text-md-start text-center pb-md-0 pb-6 d-flex"><span class="proicons--bank icon"></span> Leave History</h5> 
			
			<ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="breadcrumb-item active">Leave</li>
            </ul>
		    </div>

		    <div class="d-flex justify-content-between align-item-center">

        					    

		    </div>
		<!-- DataTable with Buttons -->
		<div class="card">
		    

			<div class="card-datatable table-responsive pt-0">
				<table class="datatables-basi table table-hover" id="simpleTable">
					<thead>
						<tr>
							<th>Leave Type</th>
							<th>Reason for Leave</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($leaves as $leave)
						<tr>
							<td>{{ $leave->leave_type }}</td>
							<td>{{ $leave->description }}</td>
							<td>{{ date("d M, Y",strtotime($leave->start_date)) }}</td>
							<td>{{ date("d M, Y",strtotime($leave->end_date)) }}</td>
							<td>
								<a type="button" class="btn delete-btn " data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" 
								class="btn btn-icon item-delete" data-url="{{ route('leave.delete', $leave->id) }}"><i class="icon-base ti tabler-trash"></i></a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		</div>


  <hr class="my-12" />
	</div>
	<!-- Add User Modal -->
	<div class="modal fade" id="addLeave" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-xs modal-simple modal-edit-user">
			<div class="modal-content">
			    	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					<div class="">
					  <h4 class="">Apply Leave</h4>
					</div>
				<div class="modal-body">
				
					<form id="add_leave" data-ajax="true" class="row g-6" method="post" action="{{ route('leave.store') }}">
						{{ csrf_field() }}
						<input type="hidden" id="institution_type" name="institution_type" value="1">
						<div class="col-6">
							<label class="form-label" for="modalEditUserName">Start Date</label>
							<input type="text" id="start_date" name="start_date" class="form-control"  required />
						</div>
						<div class="col-6">
							<label class="form-label" for="modalEditUserName">End Date</label>
							<input type="text" id="end_date" name="end_date" class="form-control"  required />
						</div>						
						<div class="col-12" >
							<label class="form-label" for="modalEditUserEmail">Sequence</label>
							<select class="form-control" id="leave_type" name="leave_type" required="">
								<option value="" selected="">Select Leave Type</option>
								<option value="Casual">Casual</option>
								<option value="Sick">Sick</option>
								<option value="Earned">Earned</option>
								<option value="Loss of Pay">Loss of Pay</option>
							</select>
						</div>
						<div class="col-12">
							<label class="form-label">Reason (If any)</label>
							<textarea class="form-control" id="description" name="description" rows="3" maxlength="100"></textarea>
							<small class="text-muted">
								<span id="descriptionCount">0</span>/100 characters
							</small>							
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
	<!-- Add Client Code Modal -->
	
	<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog delete-modal modal-simple modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body text-center">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

        <div class="mb-4">
          <h5 class="mb-2 text-danger">Confirm Deletion</h5>
          <p>Are you sure you want to delete this item? This action cannot be undone.</p>
        </div>

        <div class="d-flex justify-content-center gap-3">
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
</div>
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection
@section('scripts')
 <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
 <script src="{{ asset('assets/vendor/libs/moment/moment.js')}}"></script>
 <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
 <script src="{{ asset('assets/vendor/libs/@form-validation/popular.js')}}"></script>
 <script src="{{ asset('assets/vendor/libs/@form-validation/bootstrap5.js')}}"></script>
 <script src="{{ asset('assets/vendor/libs/@form-validation/auto-focus.js')}}"></script>
 <script src="{{ asset('assets/js/tables-datatables-basic.js')}}"></script>
 
<script>
	window.Leave = {
		csrf_token: "{{ csrf_token() }}",
		routes: {
			storeUrl: "{{ route('leave.store') }}"
		}
	};
</script>
@vite('resources/js/custom/leave.js')

@endsection