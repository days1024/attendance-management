@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/user/email.css') }}">
@endsection

@section('content')
<div class="email-form__content">
 <div class="email-form__group">
     <div class="email-form__message">
         <p>登録していただいたメールアドレスに認証メールを送付しました。</p>
         <p>メール認証を完了してください</p>
     </div>
     <div class="email-form__button">
         <a href="{{ route('verification.notice') }}">
            認証はこちらから
         </a>
     </div>
     <div class="email-form__link">
         <form method="POST" action="{{ route('verification.send') }}">
         @csrf
             <button type="submit"  class="email-link-button">
                 認証メールを再送する
             </button>
         </form>
     </div>
 </div>
</div>
@endsection