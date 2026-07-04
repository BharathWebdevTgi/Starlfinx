@forelse($groupedRecords as $date => $items)
    @php
        $dateSeconds = 0;
        foreach($items as $row){
            if($row->start_time && $row->end_time){
                $dateSeconds +=
                    Carbon\Carbon::parse($row->start_time)
                    ->diffInSeconds(
                        Carbon\Carbon::parse($row->end_time)
                    );
            }
        }
        $dateTotal = gmdate('H:i:s', $dateSeconds);
    @endphp
		<div class="card {{ !$loop->first ? 'mt-2' : '' }}" data-date="{{ $date }}">
			<div class="table-responsive">
				<table class="table table-bordered">
					<thead>
						<tr class="date-header">
							<th style="padding-block: 0.5rem;text-align: center;">
								<div class="date-header-cell">
									<div>{{ Carbon\Carbon::parse($date)->format('M jS Y') }}</div>
									<small style="color: #685dd8;font-size: 12px;">
										{{ Carbon\Carbon::parse($date)->format('l') }}
									</small>
								</div>
							</th>							
							<th colspan="6"></th>
							<th class="date-total" data-date="{{ $date }}">{{ $dateTotal }}</th>
						</tr>
					</thead>
					<tbody style="text-align:center">
						@foreach($items as $timetrack)
						<tr class="track-row"
							data-id="{{ $timetrack->id }}"
							data-date="{{ Carbon\Carbon::parse($timetrack->start_time)->format('Y-m-d') }}">
							<td style="width:15%">{{ $timetrack->description }}</td>
							<td>{{ $timetrack->project_text }}</td>		
							<td >{{ date('H:i', strtotime($timetrack->start_time)) }}</td>
							<td >{{ date('H:i', strtotime($timetrack->end_time)) }}</td>
							<td >{{ date('d-m-Y', strtotime($timetrack->start_time)) }}</td>
							<td class="duration-cell" width="120" colspan="2">
								{{ $timetrack->duration_text }}
							</td>
							
							<td >
								<a href="javascript:;" class="btn btn-icon delete-track" style="color:#ea5455;" 
								data-bs-toggle="modal" data-bs-target="#deleteConfirmModal" 
								data-id="{{ $timetrack->id }}">
								<i class="icon-base ti tabler-trash"></i>
								</a>
							</td>
						</tr>
						@endforeach
					</tbody>
				</table>
				
			</div>
			
		</div>
    </div>
@empty

    <div class="card mt-3">
        <div class="card-body text-center py-5">
            <h5 class="text-muted mb-0">No records found</h5>
        </div>
    </div>

@endforelse