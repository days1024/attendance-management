@extends('layouts.admin.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance.css') }}">
@endsection

@section('content')
<div class="attendance-form__content">
 <x-page-header :title="$user->name . 'さんの勤怠'" />
 <x-date-nav :date="$month" mode="month" :route="'/admin/attendance/staff/' . $user->id" />
 <x-attendance-table :headers="['日付','出勤','退勤','休憩','合計','詳細']" :dates="$dates" :attendances="$attendances" >
 @php
     $days = ['日','月','火','水','木','金','土'];
 @endphp
 @foreach($dates as $date)
     @php
         $attendance = $attendances[$date->format('Y-m-d')] ?? null;
     @endphp
     <tr class="table-common__row">
         <td class="table-common__item">{{ $date->format('m/d') }}({{ $days[$date->dayOfWeek] }})</td>
         <td class="table-common__item">{{ $attendance?->clock_in?->format('H:i') ?? '' }}</td>
         <td class="table-common__item">{{ $attendance?->clock_out?->format('H:i') ?? '' }}</td>
         <td class="table-common__item">{{ $attendance?->total_break_time ?? '' }}</td>
         <td class="table-common__item">{{ $attendance?->working_time ?? '' }}</td>
         <td class="table-common__item">
         @if($attendance)
         <a href="/admin/attendance/{{ $attendance->id }}">
            詳細
         </a>
         @endif
         </td>
     </tr>
 @endforeach
 </x-attendance-table>
 <form action="/admin/attendance/staff/{{$user->id}}/csv" method="GET" class="csv-button">
       <input type="hidden" name="month" value="{{ $month }}">
     <button type="submit">
        CSV出力
    </button>
</form>

</div>

@endsection