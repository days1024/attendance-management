@extends('layouts.user.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/show.css') }}">
@endsection

@section('content')
<div class="detail-form__content">
 <form action="/attendance/detail/{{$attendance->id}}" method="POST">
 @csrf
     <x-page-header title="勤怠詳細" />
 <div class="detail-form__wrapper">
     <div class="detail-form__section">
         <div class="detail-form__group">
             <label class="detail-form__label">名前</label>
             <span class="detail-form__value">{{ $attendance->user->name }}</span>
         </div>
     </div>
     <div class="detail-form__section">
         <div class="detail-form__group">
             <label class="detail-form__label">日付</label>
             <span class="detail-form__value">{{ $attendance->work_date->format('Y') }}年</span>
             <span class="detail-form__time-separator"></span>
             <span class="detail-form__value">{{ $attendance->work_date->format('n月j日') }}</span>
         </div>
     </div>
     <div class="detail-form__section">
         <div class="detail-form__group">
             <label class="detail-form__label">出勤・退勤</label>
             <div class="detail-form__input">
                 <input name="request_clock_in"  value="{{old('request_clock_in',$attendance?->clock_in?->format('H:i') ?? '') }}"/>
             </div>
             <span class="detail-form__time-separator">~</span>
             <div class="detail-form__input">
                 <input name="request_clock_out"  value="{{old('request_clock_out',$attendance?->clock_out?->format('H:i') ?? '') }}"/>
             </div>
         </div>
         <div class="form__error-detail">
             @error('request_clock_in')
                 {{ $message }}
                 @enderror
                 @error('request_clock_out')
                     {{ $message }}
                 @enderror
         </div>
     </div>
      @foreach($attendance->breakTimes as $index => $break)
     <div class="detail-form__section">
         <div class="detail-form__group">
             <label class="detail-form__label">{{ $index === 0 ? '休憩' : '休憩' . ($index + 1) }}</label>
             <div class="detail-form__input">
                 <input name="request_break_start[]"  value="{{ old('request_break_start.' . $index, $break->break_start?->format('H:i')) }}"/>
             </div>
             <span class="detail-form__time-separator">~</span>
             <div class="detail-form__input">
                 <input name="request_break_end[]" value="{{ old('request_break_end.' . $index, $break->break_end?->format('H:i')) }}"/>
             </div>
         </div>
         <div class="form__error-detail">
             @error('request_break_start.' . $index)
                 {{ $message }}
             @enderror
             @error('request_break_end.' . $index)
                 {{ $message }}
             @enderror
         </div>
     </div>
     @endforeach
     <div class="detail-form__section">
         <div class="detail-form__group">
             @php
                 $newIndex = $attendance->breakTimes->count();
             @endphp
             <label class="detail-form__label">
                 休憩{{ $attendance->breakTimes->count() + 1 }}
             </label>
             <div class="detail-form__input">
                 <input name="request_break_start[{{ $newIndex }}]"
                 value="{{ old('request_break_start')[$newIndex] ?? '' }}" />
             </div>
             <span class="detail-form__time-separator">~</span>
             <div class="detail-form__input">
                 <input name="request_break_end[{{ $newIndex }}]"
                 value="{{ old('request_break_end')[$newIndex] ?? '' }}" />
             </div>
         </div>
         <div class="form__error-detail">
             @error('request_break_start.' . $newIndex)
                 {{ $message }}
             @enderror
             @error('request_break_end.' . $newIndex)
                 {{ $message }}
             @enderror
         </div>
     </div>
     <div class="detail-form__section">
         <div class="detail-form__gorup-text">
             <label class="detail-form__label">備考</label>
             <div class="detail-form__text">
                 @if($reason)
                     <textarea readonly>{{ $reason }}</textarea>
                 @else
                 <textarea type="text" name="reason">{{ old('reason') }}</textarea>
                 @endif
             </div>
         </div>
         <div class="form__error-detail">
             @error('reason')
                 {{ $message }}
             @enderror
         </div>
     </div>
    </div>
    <div class="detail-form__button">
        @if($hasRequest)
         <p class="detail-form__cannot">※承認待ちのため修正できません</p>
        @else
         <button type="submit">修正</button>
        @endif
    </div>
 </form>
</div>


@endsection