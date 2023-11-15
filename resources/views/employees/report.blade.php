@extends('layouts.app')

@section('content')
<style type="text/css">
    table, th, td {
        border: 1px solid black;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
                        <a href="{{ url()->previous() }}">{{ __('Back') }}</a>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ \App\Enum\WeekDay::DAYS[$request->day] }}</div>
                <div class="card-header">{{ $request->time }}</div>
                <div class="card-header">{{ __('Initial employees list') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Name') }}</td>
                    </tr>
                @foreach ($initialList as $employee)
                    <tr>
                        <td>{{ $employee }}</td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Not found employees list') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Name') }}</td>
                    </tr>
                @foreach ($notFoundEmployees as $employee)
                    <tr>
                        <td>{{ $employee }}</td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Employees at work in time') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Name') }}</td>
                        <td>{{ __('Telegram') }}</td>
                    </tr>
                @foreach ($employeesAtWorkInTime as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->telegram }}</td>
                    </tr>
                @endforeach
                </table>
                <form method="get" action="{{ route('employees.custom_export') }}">
                @foreach ($employeesAtWorkInTime as $employee)
                    <input type="hidden" name="ids[]" value="{{ $employee->id }}" />
                @endforeach
                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Export') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Absent employees') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Name') }}</td>
                        <td>{{ __('Telegram') }}</td>
                    </tr>
                @foreach ($absentEmployees as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->telegram }}</td>
                    </tr>
                @endforeach
                </table>
                <form method="get" action="{{ route('employees.custom_export') }}">
                @foreach ($absentEmployees as $employee)
                    <input type="hidden" name="ids[]" value="{{ $employee->id }}" />
                @endforeach
                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Export') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Employees at work not in time') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Name') }}</td>
                        <td>{{ __('Telegram') }}</td>
                    </tr>
                @foreach ($employeesAtWorkNotInTime as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->telegram }}</td>
                    </tr>
                @endforeach
                </table>
                <form method="get" action="{{ route('employees.custom_export') }}">
                @foreach ($employeesAtWorkNotInTime as $employee)
                    <input type="hidden" name="ids[]" value="{{ $employee->id }}" />
                @endforeach
                    <div class="row mb-0">
                        <div class="col-md-8 offset-md-4">
                            <button type="submit" class="btn btn-primary">
                                {{ __('Export') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
