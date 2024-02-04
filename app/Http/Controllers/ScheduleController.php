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

        $employeeId = $request->get('employee_id');
        $days = $request->get('day');
        $froms = $request->get('from');
        $tos = $request->get('to');
        foreach(array_keys($days) as $key) {
            foreach($days[$key] as $day) {
                Schedule::createCustom($employeeId, $day, $froms[$key], $tos[$key]);
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
        $employeeId = $request->get('employee_id');
        $days = $request->get('day');
        $froms = $request->get('from');
        $tos = $request->get('to');
        foreach(array_keys($days) as $key) {
            foreach($days[$key] as $day) {
                Schedule::createCustom($employeeId, $day, $froms[$key], $tos[$key]);
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
