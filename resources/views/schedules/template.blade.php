<div id="schedule-template" class="schedule-container" style="display:none">
    <div class="row mb-3">
        <label for="day" class="col-md-4 col-form-label text-md-end">{{ __('Day') }}</label>

        <div class="col-md-6">
            <select id="day" class="toggle-require required form-control @error('day') is-invalid @enderror" name="days[]" autocomplete="day" autofocus multiple >
                @foreach (\App\Enum\WeekDay::DAYS as $key => $day)
                    <option value="{{ $key }}" >{{ $day }}</option>
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
        <div class="time-dd-container">
            <label for="from-hours" class="col-md-4 col-form-label text-md-end">{{ __('From') }}</label>

            <div class="col-md-6">

                <input id="from" type="hidden" class="time required toggle-require form-control @error('from') required is-invalid @enderror" name="from" autocomplete="from">

                <select class="hour toggle-require required form-control @error('to') is-invalid @enderror">
                    <option disabled selected value></option>
                    @foreach (range(0, 24, 1) as $hour)
                        <option value="{{ sprintf("%02s", $hour) }}" >{{ sprintf("%02s", $hour) }}</option>
                    @endforeach
                </select>
            <label class="col-md-4 col-form-label text-md-end">{{ __(':') }}</label>

                <select class="minute toggle-require required form-control @error('to') is-invalid @enderror">
                    <option value="00" >00</option>
                    <option value="30" >30</option>
                </select>

                @error('from')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="time-dd-container">
            <label class="col-md-4 col-form-label text-md-end">{{ __('To') }}</label>

            <div class="col-md-6">

                <input id="to" type="hidden" class="time required toggle-require form-control @error('to') required is-invalid @enderror" name="to" autocomplete="to">

                <select class="hour toggle-require required form-control @error('to') is-invalid @enderror">
                    <option disabled selected value></option>
                    @foreach (range(0, 24, 1) as $hour)
                        <option value="{{ sprintf("%02s", $hour) }}" >{{ sprintf("%02s", $hour) }}</option>
                    @endforeach
                </select>
            <label class="col-md-4 col-form-label text-md-end">{{ __(':') }}</label>

                <select class="minute toggle-require required form-control @error('to') is-invalid @enderror">
                    <option value="00" >00</option>
                    <option value="30" >30</option>
                </select>

                @error('to')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="row mb-0">
        <div class="col-md-8 offset-md-4">
            <button style="display:none" class="remove btn btn-primary">
                {{ __('Remove') }}
            </button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        
        
        (function() {
            const toggleRequired = function($container, force) {
                if (force !== undefined) {
                    $container
                        .find('.required').prop('required', force);

                    return;
                }
                const day = $container.find('[name^=day').val().length;
                const from = $container.find('[name^=from').val();
                const to = $container.find('[name^=to').val();            
                
                const required = !!(day || from || to); //@todo :D

                if (required) {
                    $container.find('button.remove').show();
                }
                
                $container.find('.required').prop('required', required);

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
                if ($scheduleContainer.data('multiple')) {
                    const $schedule = $(e.target)
                        .closest('.schedule-container');
                    toggleRequired($schedule);
                    if ($schedule.is('#schedule-' + num)) {
                        addSchedule();
                    }
                }
            };

            $($scheduleContainer).on('change', '.toggle-require', formChanged);

            addSchedule($scheduleContainer.data('required'));
            
            (function() {
                const data_day = $scheduleContainer.data('day');
                if (data_day !== undefined) {
                    const days = Number.isInteger(data_day)? data_day: data_day.split(',');
                    const from = $scheduleContainer.data('from');
                    const to = $scheduleContainer.data('to');
                    $scheduleContainer.find('select[name^=day\\\[]:first').val(days);
                    $scheduleContainer.find('input[name^=from\\\[]:first').val(from);
                    $scheduleContainer.find('input[name^=to\\\[]:first').val(to).change();
                }
            }());
            

            $(document).on('click', 'button.remove', function(e) {
                e.preventDefault();
                if ($('.schedule-container:visible').length > 1) {
                    $(e.target).closest('.schedule-container').remove();
                }
                return false;
            });
        }());
        
        (function() {
            const $froms = $('[id^=from][type=hidden][name^=from\\\[]');
            
            const $tos = $('[id^=to][type=hidden][name^=to\\\[]');
            
            
            const fillDd = function(index, element) {
                const $element = $(element);
                const value = $element.val();

                if (value) {
                    const $container = $element.closest('.time-dd-container');

                    $container.find('.hour').val(value.substring(0, 2));
                    $container.find('.minute').val(value.substring(3, 5));
                }
                
            };
            
            $froms.each(fillDd);
            $tos.each(fillDd);
            
            $(document).on('change', '.time-dd-container .hour, .time-dd-container .minute', function(e) {
                    const $container = $(e.target).closest('.time-dd-container');
                    
                    const value = $container.find('.hour').val() + ':' + $container.find('.minute').val();
                    
                    $container.find('input[type=hidden].time').val(value);
            });
            
        }())
    });

</script>