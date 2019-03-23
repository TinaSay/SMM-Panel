<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>
</head>
    <body class="save-profile">
        <header>
            <div class="logo_check">
                <img src="{{ asset('images/landing/logo.png') }}" alt="Picstar">
            </div>
        </header>

        <h3>Защита вашего профиля</h3>
        <h4>В социальных сетях существуют лимиты, <br/> мы учитываем ваши действия и вычисляем время до следующего задания.</h4>
        <div class="task_loader_cover">
            <img src="{{ asset('images/loader_rocket.gif') }}" />
        </div>

        <script>
            setTimeout(function(){
                window.location = "{!! $link !!}"
            }, 1500);
        </script>
    </body>
</html>

<style type="text/css">
    body {
        font-family: "Nunito", sans-serif;
    }
    .save-profile .logo_check {
        text-align: center;
        background: linear-gradient(to right, rgba(196,89,201,1) 2%, rgba(82,92,235,1) 100%);
    }
    .save-profile .logo_check img {
        width: 200px;
    }
    .save-profile h3 {
        text-align: center;
        font-size: 30px;
        margin-top: 25px;
        color: #9B9B9B;
        margin-bottom: 0px;
    }
    .save-profile h4 {
        text-align: center;
        font-size: 20px;
        line-height: 25px;
        color: #9B9B9B;
        margin-top: 20px;
    }

    .task_loader_cover {
        text-align: center;
    }
</style>