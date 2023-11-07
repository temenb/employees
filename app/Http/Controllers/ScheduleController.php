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
        
        $makeSchedule = function($request, $day) {
            $schedule = Schedule::make($request->all());
            $schedule->day = $day;
            $schedule->from = Schedule::convertStringToTimestamp($request->from);
            $schedule->to = Schedule::convertStringToTimestamp($request->to);
            $schedule->save();            
        }; 

        $makeSchedule($request, array_shift($days));

        foreach($days as $day) {
            $makeSchedule($request, $days);
        }
        return redirect()->route('employees');
    }

    public function edit($id)
    {
        $schedule = Schedule::find($id);
        return view('schedules.form', ['schedule' => $schedule]);
    }

    public function patch(Request $request, $id)
    {
        $schedule = Schedule::find($id);
        $schedule->fill($request->all());
        $schedule->from = Schedule::convertStringToTimestamp($request->from);
        $schedule->to = Schedule::convertStringToTimestamp($request->to);
        $days = $request->days;
        $schedule->day = array_shift($days);
        $schedule->save();
        return redirect()->route('employees');
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();
        return redirect()->route('employees');
    }
}
