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
        $employees = Employee::orderBy('telegram')->get();
        
        return view('employees.index', ['employees' => $employees]);
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
        $employee = Employee::find($id);
        
        $employee->fill($request->all());        
        $employee->suspended = (bool) $request->get('suspended');
        $employee->save();
        
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
                    $contents .= "{$days};{$from};{$to};";
                    $contents .= ($i == 1)? $employee->suspended: '';
                    $contents .= ";\n";
                }
            } else {
                $contents .= ";;;{$employee->suspended};";
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
                $from = Schedule::convertStringToTimestamp($request->from);
                $to = Schedule::convertStringToTimestamp($request->to);
                
                if ($from < $to) {
                    $data[$key]['schedule'][] = [
                        'day' => array_search(trim($day), WeekDay::DAYS),
                        'from' => $from,
                        'to' => $to,
                    ];
                } else {
                    $day = array_search(trim($day), WeekDay::DAYS);
                    $data[$key]['schedule'][] = [
                        'day' => $day,
                        'from' => $from,
                        'to' => '24:00',
                    ];
                    
                    $data[$key]['schedule'][] = [
                        'day' => ($day + 1)%7,
                        'from' => 0,
                        'to' => $to,
                    ];      
                }
                
            }
            $data[$key]['employee']['suspended'] = empty($line[5])? 0: 1;
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

        $getRequestedEmployeesList = function(Request $request) {
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
            return $employees;
        };
        $employees = $getRequestedEmployeesList($request);
        
        $suspendedEmployees = Employee::whereIn('name', $employees)->where('suspended', true)->get();

        $_employees = array_diff($employees, $suspendedEmployees->pluck('name')->toArray());
        
        
        $employeesAtWorkInTime = Employee::whereIn('name', $_employees)
            ->whereHas('schedules', function($q) use($day, $time) {
                $q->where('day', $day)->where('from', '<', $time)->where('to', '>', $time);
            })
            ->get();
        
        $_employees = array_diff($employees, $employeesAtWorkInTime->pluck('name')->toArray());
        
        
        $employeesAtWorkNotInTime = Employee::whereIn('name', $_employees)->where('suspended', false)->get();
        
        $notFoundEmployees = array_diff($_employees, $employeesAtWorkNotInTime->pluck('name')->toArray(), $suspendedEmployees->pluck('name')->toArray());
        
        
        $absentEmployees = Employee::where('suspended', false)
            ->where('suspended', false)
            ->whereNotIn('name', $employees)
            ->whereHas('schedules', function($q) use($day, $time) {
                $q->where('day', $day)->where('from', '<', $time)->where('to', '>', $time);
            })
            ->get();
        
        return view('employees.report', [
            'initialList' => $employees, 
            'employeesAtWorkInTime' => $employeesAtWorkInTime,
            'employeesAtWorkNotInTime' => $employeesAtWorkNotInTime,
            'suspendedEmployees' => $suspendedEmployees,
            'notFoundEmployees' => $notFoundEmployees, 
            'absentEmployees' => $absentEmployees,
            'request' => $request,
        ]);
    }
}
