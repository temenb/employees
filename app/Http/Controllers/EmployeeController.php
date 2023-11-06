<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Enum\WeekDay;
use App\Models\Scheduler;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
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

    public function index()
    {
        $employees = Employee::all();
        
        
        $employeesHasToBeAtWork = Employee::whereHas('schedulers', function($q) {
            $day = now()->format('w');
            $time = now()->format('H:i');
            $q->where('day', $day)->where('from', '<', $time)->where('to', '>', $time);
        })->get();
        
        $employeesHasToBeAtWork->pluck('telegram');
        return view('employees.index', ['employees' => $employees, 'employeesHasToBeAtWork' => $employeesHasToBeAtWork]);
    }

    public function create()
    {
        return view('employees.form');
    }

    public function insert(Request $request)
    {
        Employee::create($request->all());
        return redirect()->route('employees');
    }

    public function edit($id)
    {
        $employee = Employee::find($id);
        return view('employees.form', ['employee' => $employee]);
    }

    public function patch(Request $request, $id)
    {
        $employees = Employee::find($id);
        $employees->fill($request->all());
        $employees->save();
        return redirect()->route('employees');
    }

    public function delete(Request $request)
    {
        $id = $request->get('id');
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return redirect()->route('employees');
    }

    public function export()
    {
        $employees = Employee::all();
        $contents = '';
        foreach($employees as $employee) {
            $contents .= "{$employee->name};{$employee->telegram};";
            if ($employee->schedulers) {
                $schedulers = $employee->schedulers;
                $scheduler = $schedulers->shift();
                $contents .= WeekDay::DAYS[$scheduler->day] . ";{$scheduler->from};{$scheduler->to};\n";
                foreach($schedulers as $scheduler) {
                    $contents .= ";;" . WeekDay::DAYS[$scheduler->day] . ";{$scheduler->from};{$scheduler->to};\n";
                }
            } else {
                $contents .= ';;;';
            }
        }
                
                
        $filename = 'export.csv';
        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, $filename);
    }

    public function import(Request $request)
    {
        $content = File::get($request->file('import_file')->getRealPath());
        Scheduler::truncate();
        Employee::truncate();
        $_content = explode("\n", trim($content));
        
        $data = [];
        $key = -1;
        foreach ($_content as $_line) {
            $line = explode(';', $_line);
            if ($line[1]) {
                $key++;
                $data[$key]['employee']['name'] = $line[0];
                $data[$key]['employee']['telegram'] = $line[1];
            }
            $data[$key]['scheduler'][] = [
                'day' => array_search($line[2], WeekDay::DAYS),
                'from' => $line[3],
                'to' => $line[4],
            ];
        }
        foreach ($data as $piece) {
            $employee = Employee::create($piece['employee']);
            foreach ($piece['scheduler'] as $scheduler) {
                $employee->schedulers()->save(Scheduler::make($scheduler));
            }
        }
        return redirect()->route('employees');
    }

    public function compare(Request $request)
    {
        $day = $request->day;
        $time = $request->time;
        $employees = array_map('trim', explode("\n", $request->employees));
        
        $employeesAtWork = Employee::whereIn('telegram', $employees)->get();
        
        $notFoundEmployees = array_diff($employees, $employeesAtWork->pluck('telegram')->toArray());
        
        $employeesHasToBeAtWorkCollection = Employee::whereHas('schedulers', function($q) use($day, $time) {
            $q->where('day', $day)->where('from', '<', $time)->where('to', '>', $time);
        })->get();
        
        $employeesHasToBeAtWork = $employeesHasToBeAtWorkCollection->pluck('telegram')->toArray();
        
        $employeesNotAtWork = array_diff($employeesHasToBeAtWork, $employeesAtWork->pluck('telegram')->toArray());
        
        return view('employees.report', [
            'employeesList' => $employees, 
            'notFoundEmployees' => $notFoundEmployees, 
            'employeesAtWork' => $employeesAtWork->pluck('telegram')->toArray(),
            'employeesHasToBeAtWork' => $employeesHasToBeAtWork,
            'employeesNotAtWork' => $employeesNotAtWork
        ]);
    }
}
