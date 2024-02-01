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
            foreach($days as $day) {
                $schedule = Schedule::make($request->all());
                $schedule->day = $day;
                $from = Schedule::convertStringToTimestamp($request->from);
                $to = Schedule::convertStringToTimestamp($request->to);
                $schedule->from = $from;
                if ($from < $to) {
                    $schedule->to = $to;
                    $schedule->save();            
                } else {
                    $schedule->to = Schedule::convertStringToTimestamp('24:00');
                    $schedule->save();            

                    $schedule2 = Schedule::make($request->all());
                    $schedule2->day = ($day + 1)%7;
                    $schedule2->from = 0;
                    $schedule2->to = $to;
                    $schedule2->save();            
                }
            }
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
        Schedule::whereIn('id', explode(',', $ids))->delete();
        $days = $request->days;
        foreach($days as $day) {
            $schedule = Schedule::make($request->all());
            $schedule->day = $day;
            $from = Schedule::convertStringToTimestamp($request->from);
            $to = Schedule::convertStringToTimestamp($request->to);
            $schedule->from = $from;
            if ($from < $to) {
                $schedule->to = $to;
                $schedule->save();            
            } else {
                $schedule->to = Schedule::convertStringToTimestamp('24:00');
                $schedule->save();            

                $schedule2 = Schedule::make($request->all());
                $schedule2->day = ($day + 1)%7;
                $schedule2->from = 0;
                $schedule2->to = $to;
                $schedule2->save();            
            }
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
