<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    public function create($employeeId)
    {
        return view('schedules.form', ['employeeId' => $employeeId]);
    }

    public function insert(Request $request)
    {
        $days = $request->days;

        foreach($days as $day) {
            $schedule = Schedule::make($request->all());
            $schedule->day = $day;
            $schedule->from = Schedule::convertStringToTimestamp($request->from);
            $schedule->to = Schedule::convertStringToTimestamp($request->to);
            $schedule->save();            
        }

        return redirect()->route('employees');
    }

    public function edit($ids)
    {
        $schedules = Schedule::whereIn('id', explode(',', $ids))->get();
        return view('schedules.form', ['schedules' => $schedules]);
    }

    public function patch(Request $request, $ids)
    {
        $schedules = Schedule::whereIn('id', explode(',', $ids))->delete();
        $days = $request->days;
        foreach($days as $day) {
            $schedule = Schedule::make($request->all());
            $schedule->day = $day;
            $schedule->from = Schedule::convertStringToTimestamp($request->from);
            $schedule->to = Schedule::convertStringToTimestamp($request->to);
            $schedule->save();            
        }

        return redirect()->route('employees');
    }

    public function delete(Request $request)
    {
        $ids = explode(',', $request->get('ids'));
        $schedule = Schedule::whereIn('id', $ids)->delete();
        return redirect()->route('employees');
    }
}
