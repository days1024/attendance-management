<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>勤怠管理</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
  <link rel="stylesheet" href="{{ asset('css/common.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components.css') }}">
  <link rel="stylesheet"
   href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @yield('css')
</head>

<body>
  <header class="header">
    <div class="header__inner">
      <img class="header__logo" src="{{ asset('img/COACHTECHヘッダーロゴ.png') }}" alt="coachtech">
     <a href="/attendance" class="form__button-attndance">
        <button type="submit">勤怠</button>
     </a>
     <a href="/attendance/list" class="form__button-show">
        <button type="submit">勤怠一覧</button>
     </a>
     <a href="/stamp_correction_request/list" class="form__button-request">
        <button type="submit">申請</button>
     </a>
     <form  class="form__button-logout" action="/logout" method="post">
       @csrf
       <button type="submit">ログアウト</button>
     </form>
    </div>
  </header>

  <main>
    @yield('content')
  </main>
</body>

</html>