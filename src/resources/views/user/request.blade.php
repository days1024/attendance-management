@extends('layouts.user.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/request.css') }}">
@endsection

@section('content')
<div class="request-form__content">
     <x-page-header title="申請一覧" />
     <div class="request-form__tab">
          <a href="/stamp_correction_request/list/?tab=pending">承認待ち</a>
          <a href="/stamp_correction_request/list/?tab=approved">承認済み</a>
     </div>
     <x-request-table :requests="$requests" route="attendance.detail" idField="attendance_id"/>
 
</div>

@endsection

