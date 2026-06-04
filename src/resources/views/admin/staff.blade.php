@extends('layouts.admin.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/staff.css') }}">
@endsection

@section('content')
<div class="staff-form__content">
 <x-page-header title="スタッフ一覧" />
 <x-attendance-table :headers="['名前','メールアドレス','月次勤怠']">
    @foreach($users as $user)
     <tr class="table-common__row">
         <td class="table-common__item">{{ $user->name}}</td>
         <td class="table-common__item">{{  $user->email}}</td>
         <td class="table-common__item">
             <a href="/admin/attendance/staff/{{ $user->id }}">
             詳細
             </a>
         </td>
     </tr>
    @endforeach
 </x-attendance-table>
</div>
@endsection