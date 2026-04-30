@php

switch ($mode) {

    case 'day':
        $prev = $date->copy()->subDay();
        $next = $date->copy()->addDay();
        $label = $date->format('Y/m/d');
        $prevText = '前日';
        $nextText = '次日';
        $query = 'date';
        break;

    case 'month':
        $prev = $date->copy()->subMonth();
        $next = $date->copy()->addMonth();
        $label = $date->format('Y/m');
        $prevText = '前月';
        $nextText = '次月';
        $query = 'month';
        break;
}

@endphp

<div class="show-form__link">

<a href="{{ url($route . '?' . $query . '=' . $prev->format('Y-m-d')) }}">
← {{ $prevText }}
</a>

<span>{{ $label }}</span>

<a href="{{ url($route . '?' . $query . '=' . $next->format('Y-m-d')) }}">
{{ $nextText }} →
</a>

</div>