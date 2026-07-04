@extends('layouts.theme')
@section('styles')
    <style>
        .time-tracker-card .v-details {
            background: #B3446C;
            color: #fff;
        }

        .time-tracker-card .bottom-text {
            color: #fff;
        }

        .time-tracker-card .mynaui--arrow-up-right {
            color: #fff;
        }

        .dashboard-ui .row>div:nth-child(7) .v-details {
            background: #951a6b;
        }
    </style>
@endsection
@section('content')
    <!-- Content wrapper -->
    <div class="content-wrapper dashboard-ui">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="d-flex justify-content-between">
                <h5 class="dashbopard-h">Dashboard</h5>
            </div>
            <div class="row g-9 mb-6">
                <!-- Client List -->
                <div class="col-sm-6 col-xl-4">
                    <a href="{{ route('timetracks.index') }}">
                        <div class="card time-tracker-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="content-left">
                                        <div class="d-flex justify-content-start flex-column my-1">
                                            <h4 class="mb-0 me-2">Time Tracker</h4>
                                            <p class="mb-0">
                                                Track daily tasks, monitor work hours, manage projects, and generate
                                                productivity reports.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="avatar">
                                        <span class="avatar-initial rounded bg-label-primary">
                                            <img src="{{ asset('assets/img/anime-icons/timer.gif') }}" />
                                        </span>
                                    </div>
                                </div>
                                <div class="v-details d-flex align-items-center justify-content-between">
                                    <p class="bottom-text mb-0">View Details</p>
                                    <span class="mynaui--arrow-up-right"></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
				<!-- Leave History -->
				<div class="col-sm-6 col-xl-4">
					<a href="{{ route('leave.index') }}">
						<div class="card">
							<div class="card-body">
								<div class="d-flex align-items-center justify-content-between">
									<div class="content-left">
										<div class="d-flex justify-content-start flex-column my-1">
											<h4 class="mb-0 me-2">Leave History</h4>
											<p class="mb-0">
												View your leave records, track approvals, and monitor your leave balance with ease.
											</p>
										</div>
									</div>
									<div class="avatar">
										<span class="avatar-initial rounded bg-label-primary">
											<img src="{{ asset('assets/img/anime-icons/leave-history.gif') }}" />
										</span>
									</div>
								</div>
								<div class="v-details d-flex align-items-center justify-content-between">
									<p class="bottom-text mb-0">View Details</p>
									<span class="mynaui--arrow-up-right"></span>
								</div>
							</div>
						</div>
					</a>
				</div>				
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
@endsection
