<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scheduler;

class SchedulerController extends Controller
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
        return view('schedulers.form', ['employeeId' => $employeeId]);
    }

    public function insert(Request $request)
    {
        $days = $request->days;
        $scheduler = Scheduler::make($request->all());
        $scheduler->day = array_shift($days);
        $scheduler->save();
        foreach($days as $day) {
            $scheduler = Scheduler::make($request->all());
            $scheduler->day = $day;
            $scheduler->save();
        }
        return redirect()->route('employees');
    }

    public function edit($id)
    {
        $scheduler = Scheduler::find($id);
        return view('schedulers.form', ['scheduler' => $scheduler]);
    }

    public function patch(Request $request, $id)
    {
        $scheduler = Scheduler::find($id);
        $scheduler->fill($request->all());
        $days = $request->days;
        $scheduler->day = array_shift($days);
        $scheduler->save();
        return redirect()->route('employees');
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        $scheduler = Scheduler::findOrFail($id);
        $scheduler->delete();
        return redirect()->route('employees');
    }
}
