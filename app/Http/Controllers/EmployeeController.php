<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Enum\WeekDay;
use App\Models\Schedule;
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
        $employeesHasToBeAtWork = Employee::whereHas('schedules', function($q) {
            $day = now()->format('w');
            $time = 60*60*now()->format('H') + 60*now()->format('i');
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
            if ($employee->schedules) {
                $schedules = $employee->schedulesAgregatedByDays();
                $i = 0;
                foreach($schedules as $data) {
                    $contents .= ($i++ > 0)? ";;": '';
                    $from = Schedule::convertTimestampToString($data['from']);
                    $to = Schedule::convertTimestampToString($data['to']); 
                    $days = WeekDay::convertIntsToString($data['days']); 
                    $contents .= "{$days};{$from};{$to};\n";    
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

    public function customExport(Request $request)
    {
        $ids = $request->ids;
        $employees = Employee::whereIn('id', $ids)->get();
        $contents = '';
        foreach($employees as $employee) {
            $contents .= "{$employee->name};{$employee->telegram};\n";
        }
                
        $filename = 'export.csv';
        return response()->streamDownload(function () use ($contents) {
            echo $contents;
        }, $filename);
    }

    public function import(Request $request)
    {        
        $content = File::get($request->file('import_file')->getRealPath());
        Schedule::truncate();
        Employee::truncate();
        $_content = explode("\n", trim($content));
        
        $data = [];
        $key = -1;
        foreach ($_content as $_line) {
            $line = preg_split('(;|\t)', $_line);
            if ($line[1]) {
                $key++;
                $data[$key]['employee']['name'] = $line[0];
                $data[$key]['employee']['telegram'] = $line[1];
            }
            
            $days = empty($line[2])? $days: explode(',', $line[2]);

            foreach ($days as $day) {
                $data[$key]['schedule'][] = [
                    'day' => array_search(trim($day), WeekDay::DAYS),
                    'from' => Schedule::convertStringToTimestamp($line[3]),
                    'to' => Schedule::convertStringToTimestamp($line[4]),
                ];    
            }
        }
        
        foreach ($data as $piece) {
            $employee = Employee::create(array_map('utf8_encode', $piece['employee']));
            foreach ($piece['schedule'] as $schedule) {
                $employee->schedules()->save(Schedule::make($schedule));
            }
        }
        
        return redirect()->route('employees');
    }

    public function compare(Request $request)
    {
        $day = $request->day;
        $time = Schedule::convertStringToTimestamp($request->time);

        if (0 === strpos($request->employees, '<ul')) {
            preg_match_all('/(?<=\\>)[^\\<]+(?=\\<\\/li\\>)/', $request->employees, $matches);
            $employees = array_map(function($item) {return trim($item);}, $matches[0]);
            unset($employees[0]);
            unset($employees[1]);
            unset($employees[2]);
            unset($employees[3]);
            unset($employees[4]);
        } else {
            $employees = array_map('trim', preg_split("/[\\n\\s\\t]/", $request->employees));
        }

        $employeesAtWorkInTime = Employee::whereIn('name', $employees)->whereHas('schedules', function($q) use($day, $time) {
            $q->where('day', $day)->where('from', '<', $time)->where('to', '>', $time);
        })->get();
        
        $_employees = array_diff($employees, $employeesAtWorkInTime->pluck('name')->toArray());
        
        
        $employeesAtWorkNotInTime = Employee::whereIn('name', $_employees)->get();
        
        
        $notFoundEmployees = array_diff($_employees, $employeesAtWorkNotInTime->pluck('name')->toArray());
        
        
        $absentEmployees = Employee::whereHas('schedules', function($q) use($day, $time) {
            $q->where('day', $day)->where('from', '<', $time)->where('to', '>', $time);
        })->whereNotIn('name', $employees)->get();
        
        return view('employees.report', [
            'initialList' => $employees, 
            'employeesAtWorkInTime' => $employeesAtWorkInTime,
            'employeesAtWorkNotInTime' => $employeesAtWorkNotInTime,
            'notFoundEmployees' => $notFoundEmployees, 
            'absentEmployees' => $absentEmployees,
            'request' => $request,
        ]);
    }
}
