@extends('layouts.admin.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/index.css') }}">
@endsection

@section('content')
<div class="show-form__content">
 <x-page-header title="勤怠一覧" />
 <x-date-nav :date="$day" mode="day" route="/admin/attendance/list" />
 <x-attendance-table :headers="['名前','出勤','退勤','休憩','合計','詳細']">
 @php
    $days = ['日','月','火','水','木','金','土'];
    @endphp
    @foreach($users as $user)

 

    <tr class="table-common__row">
    <td class="table-common__item">{{ $user->name}}</td>
    <td class="table-common__item">{{  $user->attendance?->clock_in?->format('H:i') ?? '' }}</td>
    <td class="table-common__item">{{ $user->attendance?->clock_out?->format('H:i') ?? '' }}</td>
    <td class="table-common__item">{{ $user->attendance?->total_break_time ?? '' }}</td>
    <td class="table-common__item">{{ $user->attendance?->working_time ?? '' }}</td>
    <td class="table-common__item">
        @if($user->attendance)
        <a href="/attendance/detail/{{ $user->attendance->id }}">
            詳細
        </a>
        @endif
    </td>
 </tr>

 @endforeach
 </x-attendance-table>
</div>
@endsection