@extends('layouts.app')

@section('content')
<style type="text/css">
    table, th, td {
        border: 1px solid black;
    }
</style>
<div class="container">
    <div class="row justify-content-center">
        <br />
        <br />
        <div class="col-md-8">
            <div class="card">
                <a href="{{ route('employees.export') }}">{{ __('Export') }}</a>
            </div>
        </div>
        <br />
        <br />
        <div class="col-md-8">
            <form method="post" enctype="multipart/form-data" action="{{ route('employees.import') }}">
                @csrf
                <div class="card">
                    <label for="import_file">Choose file to upload</label>
                </div>
                <div class="card">
                    <input type="file" id="import_file" name="import_file" required/>
                </div>
                @csrf
                <div class="card">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Import') }}
                    </button>
                </div>
            </form>
        </div>
        <br />
        <br />
        <div class="col-md-8">
            <form method="post" action="{{ route('employees.compare') }}">
                @csrf
                <div class="card">
                    <label for="day" class="col-md-4 col-form-label text-md-end">{{ __('Day') }}</label>

                    <div class="col-md-6">
                        <select id="day" type="text" class="form-control @error('day') is-invalid @enderror" name="day"required autocomplete="day" autofocus >
                            @foreach (\App\Enum\WeekDay::DAYS as $key => $day)
                            <option value="{{ $key }}" @if (now()->format('w') == $key) selected="selected" @endif>{{ $day }}</option>
                            @endforeach
                        </select>

                        @error('day')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                
                <div class="card">
                    <label for="time" class="col-md-4 col-form-label text-md-end">{{ __('Time') }}</label>

                    <div class="col-md-6">
                        <input id="time" type="text" pattern="([0-1]\d|2[0-3]):[0-3]\d}" class="form-control @error('time') is-invalid @enderror" name="time" value="{{ now()->format('H:i') }}" required autocomplete="from">

                        @error('from')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="card">
                    <label for="employees" class="col-md-4 col-form-label text-md-end">{{ __('Planned employees list') }}</label>
                    <div class="col-md-6">
                        <textarea name="employees"></textarea>
                    </div>
                </div>

                <div class="card">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Compare') }}
                    </button>
                </div>
            </form>
        </div>
        <br />
        <br />
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Employees List') }}</div>

                
                <a href="{{ route('employees.create') }}">{{ __('Add new Employee') }}</a>
                <table>
                    <tr>
                        <td>{{ __('Name') }}</td>
                        <td>{{ __('Telegram') }}</td>
                        <td>{{ __('Working hours') }}</td>
                        <td>{{ __('Suspended') }}</td>
                        <td>{{ __('Action') }}</td>
                    </tr>
                @foreach ($employees as $employee)
                    <tr>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->telegram }}</td>
                        <td>
                            <a href="{{ route('schedules.create', ['employeeId' => $employee->id]) }}">{{ __('Add') }}</a>
                            <table>
                                <tr>
                                    <td>{{ __('Day') }}</td>
                                    <td>{{ __('From') }}</td>
                                    <td>{{ __('To') }}</td>
                                    <td>{{ __('Action') }}</td>
                                </tr>
                                @if (!empty($employee->schedules))
                                    @foreach ($employee->schedulesAgregatedByDays() as $schedule)
                                        <tr>
                                            <td>
                                                @foreach ($schedule['days'] as $day)
                                                    {{ \App\Enum\WeekDay::DAYS[$day] }}@if(!$loop->last),
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>{{ \App\Models\Schedule::convertTimestampToString($schedule['from']) }}</td>
                                            <td>{{ \App\Models\Schedule::convertTimestampToString($schedule['to']) }}</td>
                                            <td>
                                                <a href="{{ route('schedules.edit', ['ids' => implode(',', $schedule['ids'])]) }}">{{ __('Edit') }}</a>
                                                <form method="post" action="{{ route('schedules.delete') }}">
                                                    @csrf
                                                    <input name="ids" value="{{ implode(',', $schedule['ids']) }}" type="hidden" />

                                                    <button type="submit" class="btn btn-primary">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                            
                        </td>
                        <td>
                            <!--{{var_export($employee->toArray())}}-->
                            <form action="{{ route('employees.edit', ['id' => $employee->id]) }}" type="POST"> 
                                @csrf
                                <input type="checkbox" value=1 @if ($employee->suspended) checked @endif name="suspended" class="suspended-{{ $employee->id }}" />
                            </form>
                        </td>
                        <td>
                            <a href="{{ route('employees.edit', ['id' => $employee->id]) }}">{{ __('Edit') }}</a>
                            <form method="post" action="{{ route('employees.delete') }}">
                                @csrf
                                <input name="id" value="{{ $employee->id }}" type="hidden" />
                                
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </table>
            </div>
        </div>
        
    </div>
</div>

<script> 
    $(document).ready(function(){
        $(document).on('click', 'input[name=suspended]', function(e) {
            const $form = $(e.target).closest('form');
            
            $.ajax({
              type: $form.attr('type'),
              url: $form.attr('action'),
              data: $form.serialize(),
              dataType: 'json'
            });
            
        });
    });
    
</script>
@endsection
