@extends('layouts.user.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/index.css') }}">
@endsection

@section('content')
<div class="show-form__content">
 <x-page-header title="勤怠一覧" />
 <x-date-nav :date="$month" mode="month" route="/attendance/list" />
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
         <td class="table-common__item">{{ $attendance?->clock_in?->format('H:i') ?? '-' }}</td>
         <td class="table-common__item">{{ $attendance?->clock_out?->format('H:i') ?? '-' }}</td>
         <td class="table-common__item">{{ $attendance?->total_break_time ?? '-' }}</td>
         <td class="table-common__item">{{ $attendance?->working_time ?? '-' }}</td>
         <td class="table-common__item">
         @if($attendance)
         <a href="/attendance/detail/{{ $attendance->id }}">
            詳細
         </a>
         @endif
         </td>
     </tr>
 @endforeach
 </x-attendance-table>
</div>
@endsection