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
use Carbon\Carbon;
use App\Models\Timetrack;
use App\Models\UserLeave;

class LeaveController extends BaseController {

    public function __construct() {
	
	
	}
	
	public function index()   {
	
		$params = array();
		$params['title'] = "Starlfinx - Leaves";
		$params['leaves'] = UserLeave::where('user_id',auth()->id())->orderBy('start_date', 'desc')->get();
		return view('leave.index',$params);
	}
	
	public function store(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'start_date'  => 'required|date',
			'end_date'    => 'required|date',
			'leave_type'  => 'required|string|max:50',
			'description' => 'nullable|string|max:100',
		]);

		if ($validator->fails()) {
			//return redirect()->back()->with('ERROR',$validator->errors()->first());
			return response()->json([
				'status'  => false,
				'message' => $validator->errors()->first(),
			]);			
		}

		$startDate = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();
		$endDate   = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
		/*
		|--------------------------------------------------------------------------
		| Check overlapping leave
		|--------------------------------------------------------------------------
		*/

		$leaveExists = UserLeave::where('user_id', auth()->id())
			->where(function ($query) use ($startDate, $endDate) {
				$query->whereBetween('start_date', [$startDate, $endDate])
					  ->orWhereBetween('end_date', [$startDate, $endDate])
					  ->orWhere(function ($q) use ($startDate, $endDate) {
						  $q->where('start_date', '<=', $startDate)
							->where('end_date', '>=', $endDate);
					  });
			})
			->exists();

		if ($leaveExists) {
			//return redirect()->back()->with('ERROR','A leave already exists for the selected period');
			return response()->json([
				'status'  => false,
				'message' => 'A leave already exists for the selected period',
			]);			
		}

		/*
		|--------------------------------------------------------------------------
		| Check existing timetracks
		|--------------------------------------------------------------------------
		*/

		$dates = TimeTrack::where('user_id', auth()->id())
			->whereDate('start_time', '>=', $startDate)
			->whereDate('start_time', '<=', $endDate)
			->selectRaw('DATE(start_time) as work_date')
			->distinct()
			->orderBy('work_date')
			->pluck('work_date')
			->map(fn($date) => Carbon::parse($date)->format('d M Y'))
			->implode(', ');
		
		if ($dates) {
			//return redirect()->back()->with('ERROR',"Task entries already exist on {$dates}. Please remove those entries before applying for leave.");
			return response()->json([
				'status'  => false,
				'message' => "Task entries already exist on {$dates}. Please remove those entries before applying for leave.",
			]);
		}

		/*
		|--------------------------------------------------------------------------
		| Save
		|--------------------------------------------------------------------------
		*/

		UserLeave::create([
			'user_id'     => auth()->id(),
			'start_date'  => $startDate->toDateString(),
			'end_date'    => $endDate->toDateString(),
			'leave_type'  => $request->leave_type,
			'description' => $request->description,
		]);

		//return redirect()->back()->with('SUCC','Leave applied successfully.');
		return response()->json([
			'status' => true,
			'message' => "Leave applied successfully"
		]);		
	}
	
	public function delete($id)	{
		try {
			$item = UserLeave::find($id);

			if (!$item) {
				return redirect()->back()->with('ERROR', 'Record not found.');
			}

			$item->delete();

			return redirect()->back()->with('SUCC', 'Applied leave deleted successfully.');

		} catch (\Exception $e) {
			return redirect()->back()->with('ERROR', 'Error deleting record.');
		}
	}
	
}