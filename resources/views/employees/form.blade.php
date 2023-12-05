@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Add new employee') }}</div>

                
                
                <div class="card-body">
                    <form method="POST" action="{{ Request::url() }}">
                        @csrf
                        @isset($employee)
                            <input type="hidden" name="id" value="{{ $employee->id }}" />
                        @endisset
                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', isset($employee)? $employee->name: '') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="telegram" class="col-md-4 col-form-label text-md-end">{{ __('Telegram') }}</label>

                            <div class="col-md-6">
                                <input id="telegram" type="text" class="form-control @error('telegram') is-invalid @enderror" name="telegram" value="{{ old('telegram', isset($employee)? $employee->telegram: '') }}" required autocomplete="telegram">

                                @error('telegram')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="suspended" class="col-md-4 col-form-label text-md-end">{{ __('Suspended') }}</label>

                            <div class="col-md-6">
                                <input id="suspended" type="checkbox" value=1 class="form-control @error('suspended') is-invalid @enderror" name="suspended" @if (isset($employee) && $employee->suspended) checked @endif autocomplete="suspended">

                                @error('suspended')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __(isset($employee)? 'Update': 'Create') }}
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
