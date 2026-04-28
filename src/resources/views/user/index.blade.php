@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/index.css') }}">
@endsection

@section('content')
<div class="show-form__content">
 <div class="show-form__header">
     <h1>勤怠一覧</h1>
 </div>
 @php
    $prev = $month->copy()->subMonth()->format('Y-m');
    $next = $month->copy()->addMonth()->format('Y-m');
 @endphp
 <div class="show-form__link">
     <a href="{{ url('/attendance/list?month=' . $prev) }}">← 前月</a>
     <span><i class="fa-regular fa-calendar"></i>{{ $month->format('Y/m') }}</span>
     <a href="{{ url('/attendance/list?month=' . $next) }}">次月 →</a>
 </div>
 <div class="show-table">
     <table class="show-table__inner">
         <tr class="show-table__row">
             <th class="show-table__header">日付</th>
             <th class="show-table__header">出勤</th>
             <th class="show-table__header">退勤</th> 
             <th class="show-table__header">休憩</th>
             <th class="show-table__header">合計</th>  
             <th class="show-table__header">詳細</th>
         </tr>
         @foreach($dates as $date)
         @php
             $attendance = $attendances[$date->format('Y-m-d')] ?? null;
         @endphp
             @php
                 $days = ['日', '月', '火', '水', '木', '金', '土'];
             @endphp   
             <tr class="show-table__row">
                 <td class="show-table__item">{{ $date->format('m/d') }}({{ $days[$date->dayOfWeek] }})</td>
                 <td class="show-table__item">{{ $attendance?->clock_in?->format('H:i') ?? '-' }}</td>
                 <td class="show-table__item">{{ $attendance?->clock_out?->format('H:i') ?? '-' }}</td>
                 <td class="show-table__item">{{ $attendance?->total_break_time ?? '-' }}</td>
                 <td class="show-table__item"> {{ $attendance?->working_time ?? '-' }}</td>
                 <td class="show-table__item">
                    @if($attendance)
                     <a href="/attendance/detail/{{$attendance->id}}"> 
                         詳細
                     </a>
                     @endif
                 </td>
             </tr>
         @endforeach
     </table>
 </div>
</div>
@endsection