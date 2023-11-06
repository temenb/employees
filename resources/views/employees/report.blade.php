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
                <div class="card-header">{{ __('Initial Employees List') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Telegram') }}</td>
                    </tr>
                @foreach ($employeesList as $employee)
                    <tr>
                        <td>{{ $employee }}</td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Not Found Employees List') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Telegram') }}</td>
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
                <div class="card-header">{{ __('Employees At Workplace List') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Telegram') }}</td>
                    </tr>
                @foreach ($employeesAtWork as $employee)
                    <tr>
                        <td>{{ $employee }}</td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Absent Employees List') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Telegram') }}</td>
                    </tr>
                @foreach ($employeesNotAtWork as $employee)
                    <tr>
                        <td>{{ $employee }}</td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Employees Has To Be At Workplace List') }}</div>
                <table>
                    <tr>
                        <td>{{ __('Telegram') }}</td>
                    </tr>
                @foreach ($employeesHasToBeAtWork as $employee)
                    <tr>
                        <td>{{ $employee }}</td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
