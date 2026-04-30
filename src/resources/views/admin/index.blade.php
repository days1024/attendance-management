@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/user/index.css') }}">
@endsection

@section('content')
<div class="show-form__content">
     <x-page-header title="勤怠一覧" />
     <x-date-nav :date="$day" mode="day" route="/admin/attendance/list" />
</div>
@endsection