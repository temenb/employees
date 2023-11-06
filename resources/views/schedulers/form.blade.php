@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add new scheduler') }}</div>

                
                
                <div class="card-body">
                    <form method="POST" action="{{ Request::url() }}">
                        @csrf
                        @isset($scheduler)
                            <input type="hidden" name="id" value="{{ $scheduler->id }}" />
                        @endisset
                        <input type="hidden" name="employee_id" value="{{ isset($scheduler)? $scheduler->employee_id: $employeeId }}" />
                        
                        <div class="row mb-3">
                            <label for="day" class="col-md-4 col-form-label text-md-end">{{ __('Day') }}</label>

                            <div class="col-md-6">
                                <select id="day" type="text" class="form-control @error('day') is-invalid @enderror" name="days[]"required autocomplete="day" autofocus @if(!isset($scheduler))multiple @endif >
                                    @foreach (\App\Enum\WeekDay::DAYS as $key => $day)
                                        <option value="{{ $key }}" @if (isset($scheduler) && $key == $scheduler->day) selected="selected" @endif>{{ $day }}</option>
                                    @endforeach
                                </select>

                                @error('day')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        

                        <div class="row mb-3">
                            <label for="from" class="col-md-4 col-form-label text-md-end">{{ __('From') }}</label>

                            <div class="col-md-6">
                                <input id="from" type="text" pattern="([0-1]\d|2[0-3]):[0-3]\d}" class="form-control @error('from') is-invalid @enderror" name="from" value="{{ old('from', isset($scheduler)? $scheduler->from: '') }}" required autocomplete="from">

                                @error('from')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="to" class="col-md-4 col-form-label text-md-end">{{ __('To') }}</label>

                            <div class="col-md-6">
                                <input id="to" type="text" pattern="([0-1]\d|2[0-3]):[0-3]\d}" class="form-control @error('to') is-invalid @enderror" name="to" value="{{ old('to', isset($scheduler)? $scheduler->to: '') }}" required autocomplete="to">

                                @error('to')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __(isset($scheduler)? 'Update': 'Create') }}
                                </button>
                            </div>
                        </div>
                        
                        <a href="{{ url()->previous() }}">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
