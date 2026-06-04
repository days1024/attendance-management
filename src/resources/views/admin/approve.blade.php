@extends('layouts.admin.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/approve.css') }}">
@endsection

@section('content')
<div class="detail-form__content">
 <form action="/stamp_correction_request/approve/{{ $attendanceRequest->id }}" method="POST">
 @csrf
     <x-page-header title="勤怠詳細" />
 <div class="detail-form__wrapper">
     <div class="detail-form__group">
         <label class="detail-form__label">名前</label>
         <span class="detail-form__value">{{ $attendance->user->name }}</span>
     </div>
     <div class="detail-form__group">
         <label class="detail-form__label">日付</label>
         <span class="detail-form__value">{{ $attendance->work_date->format('Y') }}年</span>
         <span class="detail-form__time-separator"></span>
         <span class="detail-form__value">{{ $attendance->work_date->format('n月j日') }}</span>
     </div>
     <div class="detail-form__group">
         <label class="detail-form__label">出勤・退勤</label>
         <span class="detail-form__value">
             {{ \Carbon\Carbon::parse($attendanceRequest->request_clock_in)->format('H:i') }}
         </span>
         <span class="detail-form__time-separator">~</span>
         <span class="detail-form__value">
             {{ \Carbon\Carbon::parse($attendanceRequest->request_clock_out)->format('H:i') }}
         </span>
     </div>
     @foreach($attendanceRequest->requestBreakTimes as $index => $break)
     <div class="detail-form__group">
         <label class="detail-form__label">{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</label>
         <span class="detail-form__value">
             {{ \Carbon\Carbon::parse($break->request_break_start)->format('H:i') }}
         </span>
         <span class="detail-form__time-separator">~</span>
         <span class="detail-form__value">
             {{ \Carbon\Carbon::parse($break->request_break_end)->format('H:i') }}
         </span>
     </div>
     @endforeach
     <div class="detail-form__gorup-text">
         <label class="detail-form__label">備考</label>
         <span class="detail-form__value-text">
             {{ $reason }}
         </span>
     </div>
    </div>
    <div class="detail-form__button">
        @if($hasRequest)
         <p class="detail-form__approved">承認済み</p>
        @else
         <button type="submit">承認</button>
        @endif
    </div>
 </form>
</div>


@endsection