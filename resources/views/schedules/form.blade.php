@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __(isset($schedules)? 'Update schedule': 'Add new schedule') }}</div>

                <div class="card-body">
                    <form method="POST" class="schedules-form" action="{{ Request::url() }}">
                        @csrf
                        @isset($schedules)
                            <input type="hidden" name="id" value="{{ implode(',', $schedules->pluck('id')->toArray()) }}" />                        
                        @endisset
                        <input type="hidden" name="employee_id" value="{{ isset($schedules)? $schedules->first()->employee_id: $employeeId }}" />

                        <div id="schedule-container" 
                            data-required="true"
                            data-multiple="{{ !isset($schedules) }}"
                            @isset($schedules)
                                data-day="{{ $schedules->pluck('day')->implode(',') }}"
                                data-from="{{ \App\Models\Schedule::convertTimestampToString($schedules->first()->from) }}"
                                data-to="{{ \App\Models\Schedule::convertTimestampToString($schedules->first()->to) }}"
                            @endisset
                        ></div>
                        
                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __(isset($schedules)? 'Update': 'Create') }}
                                </button>
                            </div>
                        </div>
                        
                        <a href="{{ url()->previous() }}">{{ __('Cancel') }}</a>
                    </form>
                    
                    
                                        
                    @include('schedules.template')
                        
                    
                </div>
            </div>
        </div>
    </div>
</div>

                        
@endsection
