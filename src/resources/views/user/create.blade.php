@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/create.css') }}">
@endsection

@section('content')
<div class="create-form__content">
    <div class="create-form__wrapper">
         <div class="create-form__label">
             勤務外
         </div>
         <div class="create-form__date-group">
             <div class="create-form__day">
                 @php
                     $days = ['日', '月', '火', '水', '木', '金', '土'];
                 @endphp
                 {{ now()->format('Y年m月d日') }}({{ $days[now()->dayOfWeek] }})
             </div>
             <div class="create-form__time">
                {{ now()->format('H:i') }}
             </div>
         </div>
         <div class="create-form__bottun">
             @php
                 $attendance = $attendance ?? null;
             @endphp
             @if(!$attendance || $attendance->status === 'not_started')
                 <form method="POST" class="create-form__bottun-clock" action="/attendance">
                     @csrf
                     <input type="hidden" name="status" value="clock_in">
                     <button type="submit">出勤</button>
                 </form>
             @endif
             @if($attendance && $attendance->status === 'working')
                 <form method="POST" class="create-form__bottun-clock" action="/attendance">
                     @csrf
                     <input type="hidden" name="status" value="clock_out">
                     <button type="submit">退勤</button>
                 </form>
                 <form method="POST" class="create-form__bottun-break" action="/attendance">
                     @csrf
                     <input type="hidden" name="status" value="break_start">
                     <button type="submit">休憩入</button>
                 </form>
             @endif
             @if($attendance && $attendance->status === 'on_break')
             <form method="POST" class="create-form__bottun-break" action="/attendance">
                     @csrf
                     <input type="hidden" name="status" value="break_end">
                     <button type="submit">休憩戻</button>
             </form>
            @endif
            @if($attendance && $attendance->status === 'finished')
             <div class="create-form__comment">
                お疲れ様でした。
             </div>
            @endif
         </div>
    </div>
</div>
@endsection