<div id="schedule-template" class="schedule-container" style="display:none">
    <div class="row mb-3">
        <label for="day" class="col-md-4 col-form-label text-md-end">{{ __('Day') }}</label>

        <div class="col-md-6">
            <select id="day" type="text" class="toggle-require required form-control @error('day') is-invalid @enderror" name="days[]" autocomplete="day" autofocus multiple >
                @foreach (\App\Enum\WeekDay::DAYS as $key => $day)
                    @php
                        $selected = false;
                        if (isset($schedules)) {
                            foreach ($schedules as $schedule) {
                                if ($key == $schedule->day) {
                                    $selected = true;
                                    break;
                                }
                            }
                        }
                    @endphp

                    <option value="{{ $key }}" @if ($selected) selected="selected" @endif>{{ $day }}</option>
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
            <input id="from" type="text" pattern="([0-1]\d|2[0-3]):[0-3]\d}" class="required toggle-require form-control @error('from') is-invalid @enderror" name="from" value="{{ old('from', isset($schedules)? \App\Models\Schedule::convertTimestampToString($schedules->first()->from): '') }}" autocomplete="from">

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
            <input id="to" type="text" pattern="([0-1]\d|2[0-3]):[0-3]\d}" class="required toggle-require form-control @error('to') is-invalid @enderror" name="to" value="{{ old('to', isset($schedules)? \App\Models\Schedule::convertTimestampToString($schedules->first()->to): '') }}" autocomplete="to">

            @error('to')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>
    <div class="row mb-0">
        <div class="col-md-8 offset-md-4">
            <button class="remove btn btn-primary">
                {{ __('Remove') }}
            </button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        
        
        const toggleRequired = function($container, force) {
            if (force !== undefined) {
                $container
                    .find('.required').prop('required', force);

                return;
            }
            const day = $container.find('[name^=day').val().length;
            const from = $container.find('[name^=from').val();
            const to = $container.find('[name^=to').val();            

            $container
                .find('.required').prop('required', !!(day || from || to)); //@todo :D
            
        }
        
        let num = 0;
        const $scheduleContainer = $('#schedule-container'); 
        
        const addSchedule = function (required) {
            num++;
            
            const $schedule = $('#schedule-template').clone();
            $schedule.attr('id', 'schedule-' + num);
            $schedule.data('num', num);
            $schedule.removeAttr('style');

            $schedule.find('label[for=day]').attr('for', 'day' + num);
            $schedule.find('#day').attr('id', 'day' + num).attr('name', 'day[' + num + ']');

            $schedule.find('label[for=from]').attr('for', 'from' + num);
            $schedule.find('#from').attr('id', 'from' + num).attr('name', 'from[' + num + ']');

            $schedule.find('label[for=to]').attr('for', 'to' + num);
            $schedule.find('#to').attr('id', 'to' + num).attr('name', 'to[' + num + ']');

            $scheduleContainer.append($schedule);
            if (required === undefined) {
                toggleRequired($schedule);
            } else {
                toggleRequired($schedule, required);
            }            
        };
        
        const formChanged = function(e) {
            const $schedule = $(e.target)
                .closest('.schedule-container');
            toggleRequired($schedule);
            if ($schedule.is('#schedule-' + num)) {
                addSchedule();
            }
        };
        
        $($scheduleContainer).on('change', '.toggle-require', formChanged);
        
        addSchedule(true);
        
    });
        
    $(document).on('click', 'button.remove', function(e){
        e.preventDefault();
        if ($('.schedule-container:visible').length > 1) {
            $(e.target).closest('.schedule-container').remove();
        }
        return false;
    });

</script>
