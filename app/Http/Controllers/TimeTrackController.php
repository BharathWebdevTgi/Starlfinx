<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use Illuminate\Routing\Controller as BaseController;
use Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Cookie;
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use App\Models\Client\Assessee;
use App\Models\Timetrack;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use App\Models\UserLeave;

class TimeTrackController extends BaseController {

    public function __construct() {
	
	
	}
	
	public function index()   {
	
		$params = array();
		$params['title'] = "Starlfinx - Timetracks";
			
		return view('timetracks.index',$params);
	}
	
	public function groupedAjax(Request $request)
	{

		$query = Timetrack::where('user_id', auth()->id())->with(['project:id,title']);
		

		/*
		|--------------------------------------------------------------------------
		| Get all matching records
		|--------------------------------------------------------------------------
		*/

		$dateQuery = clone $query;

		$datePaginator = $dateQuery
			->selectRaw('DATE(start_time) as work_date')
			->groupByRaw('DATE(start_time)')
			->orderBy('work_date', 'DESC')
			->paginate(5);
			
		$dateList = $datePaginator
			->pluck('work_date')
			->toArray();
		
		$records = $query
			->where(function ($q) use ($dateList) {

				foreach ($dateList as $date) {
					$q->orWhereDate('start_time', $date);
				}

			})
			->orderBy('start_time', 'DESC')
			->get();		
		/*
		|--------------------------------------------------------------------------
		| Prepare extra fields
		|--------------------------------------------------------------------------
		*/

		$records->transform(function ($timetrack) {

			$duration = '';

			if ($timetrack->start_time && $timetrack->end_time) {

				$startTime = Carbon::parse($timetrack->start_time);
				$endTime   = Carbon::parse($timetrack->end_time);

				$duration = $startTime
					->diff($endTime)
					->format('%H:%I:%S');
			}

			$projectTitle = $timetrack->project?->title ?? '';

			$timetrack->project_text = $projectTitle;

			$timetrack->duration_text = $duration;

			return $timetrack;
		});

		/*
		|--------------------------------------------------------------------------
		| Group by date
		|--------------------------------------------------------------------------
		*/

		$groupedRecords = $records->groupBy(function ($item) {

			return Carbon::parse($item->start_time)->format('Y-m-d');

		});

		/*
		|--------------------------------------------------------------------------
		| Render HTML
		|--------------------------------------------------------------------------
		*/

		$html = view('timetracks.partials.grouped_rows',compact('groupedRecords'))->render();

		return response()->json([
			'html' => $html,
			'pagination' => $datePaginator
				->appends($request->all())
				->links('pagination::bootstrap-5')
				->render()
		]);
	}	
	
	public function deleteTrack(Request $request)
	{

		$timetrack = Timetrack::find($request->id);

		if (!$timetrack) {
			return response()->json([
				'status' => false,
				'message' => 'Timetrack not found'
			]);
		}
		
		$date = Carbon::parse($timetrack->start_time)->toDateString();
		
		$timetrack->delete();
		
		/*
		|--------------------------------------------------------------------------
		| Calculate existing timetracks total hours for the particular date
		|--------------------------------------------------------------------------
		*/

		$rows = Timetrack::where('user_id',auth()->id())->whereDate('start_time', $date)->get();

		$totalSeconds = $rows->sum(function ($row) {

			if (!$row->start_time || !$row->end_time) {
				return 0;
			}

			return Carbon::parse($row->start_time)
				->diffInSeconds(Carbon::parse($row->end_time));
		});

		return response()->json([
			'status'      => true,
			'message'     => 'Timetrack deleted',
			'deleted_id'  => $request->id,
			'date'        => $date,
			'date_total'  => gmdate('H:i:s', $totalSeconds),
			'row_count'   => $rows->count()
		]);
	}	
	
	public function projectSearch(Request $request)
	{
		$search = $request->get('term');
		$page = $request->get('page', 1);

		$query = Project::query();

		if (!empty($search)) {
			$query->where('title', 'like', "%{$search}%");
		}

		$projects = $query
			->select('id', 'title')
			->orderBy('title')
			->paginate(20, ['*'], 'page', $page);

		return response()->json([
			'results' => $projects->map(function ($project) {
				return [
					'id' => $project->id,
					'text' => $project->title
				];
			}),
			'pagination' => [
				'more' => $projects->hasMorePages()
			]
		]);
	}
	
	public function validateTimeTrack(Request $request)	{	
		
			
	}

	public function store (Request $request) {
		
		$validator = Validator::make(
			$request->all(),
			[
				'description' => 'required|string|max:100',
				'project_id'  => 'required|exists:projects,id',
				'start_time'  => 'required|date',
				'end_time'    => 'required|date',
			],
			[
				'project_id.required' => 'Please select a project.',
				'project_id.exists'   => 'Selected project is invalid.',
				'description.required'=> 'Task description is required.',
				'description.max'     => 'Task description cannot exceed 100 characters.',
				'start_time.required' => 'Start time is required.',
				'end_time.required'   => 'End time is required.',
			]
		);		
		
		if ($validator->fails()) {
			return response()->json([
				'status'  => false,
				'message' => $validator->errors()->first(),
			]);
		}
		
		$date = Carbon::parse($request->start_time)->toDateString();
		
		/*
		|--------------------------------------------------------------------------
		| Checking Leaves exists or not
		|--------------------------------------------------------------------------
		*/		
		
		$leave = UserLeave::where('user_id', auth()->id())
			->whereDate('start_date', '<=', $date)
			->whereDate('end_date', '>=', $date)
			->first();		
					
		if ($leave) {
			return response()->json([
				'status' => false,
				'message' => 'You have already applied ' . $leave->leave_type .
							 ' leave on ' . Carbon::parse($date)->format('d M Y') .
							 '. Time entries cannot be added for leave dates.'
			]);
		}			
		
		$maxMinutes = 600;
		
		$start = Carbon::parse($request->start_time);
		$end   = Carbon::parse($request->end_time);	


		/*
		|--------------------------------------------------------------------------
		| Checking single time entry cannot exceed 10 hours
		|--------------------------------------------------------------------------
		*/	

		$currentMinutes = $start->diffInMinutes($end);
		
		if ($currentMinutes > 600) {
			return response()->json([
				'status' => false,
				'message' => 'A single time entry cannot exceed 10 hours.'
			]);
		}	
		
		/*
		|--------------------------------------------------------------------------
		| Checking remaining task Hour and Minutes for the selected Date
		|--------------------------------------------------------------------------
		*/			
		
		$existingMinutes = TimeTrack::where('user_id', auth()->id())
			->whereDate('start_time', $date)
			->get()
			->sum(function ($track) {
				return Carbon::parse($track->start_time)
					->diffInMinutes(Carbon::parse($track->end_time));
			});		
			
		$remainingMinutes = $maxMinutes - $existingMinutes;

		if (($existingMinutes + $currentMinutes) > $maxMinutes) {
			
			$remainingMinutes = max(0, $maxMinutes - $existingMinutes);

			if ($remainingMinutes === 0) {
				return response()->json([
					'status' => false,
					'message' => 'You have already submitted the maximum allowed 10 hours for the selected date.'
				]);
			}			

			$hours = intdiv($remainingMinutes, 60);
			$minutes = $remainingMinutes % 60;
			
			return response()->json([
				'status' => false,
				'message' => "You can submit only {$hours} hour(s) {$minutes} minute(s) for the selected date."
			]);
		}		
		
		/*
		|--------------------------------------------------------------------------
		| Store task timetracks
		|--------------------------------------------------------------------------
		*/
		
		$data = [
			'description'     => $request->description,
			'project_id'      => $request->project_id,
			'user_id'         => auth()->id(),
			'start_time'      => $request->start_time,
			'end_time'        => $request->end_time,
		];

		$timetrack = Timetrack::create($data);
		
		return response()->json([
			'status' => true,
			'message' => "Task Added successfully"
		]);
	}
}