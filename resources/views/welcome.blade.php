<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Blog</title>
        <link rel="stylesheet" href="{{mix('/css/all.css')}}">
        <!-- <link rel="stylesheet" href="{{ URL::asset('css/iview.css') }}"> -->
        <script>
            (function () {
                window.Laravel = {
                    csrfToken: '{{ csrf_token() }}'
                };
            })();
        </script>
        <!-- <link rel="stylesheet" type="text/css" href="http://unpkg.com/view-design/dist/styles/iview.css"> -->
        <!-- <script type="text/javascript" src="http://unpkg.com/view-design/dist/iview.min.js"></script> -->
    </head>
    <body>
        <div id="app">
            @if(Auth::check())
                <mainapp :user="{{ Auth::user() }}" :permission="{{Auth::user()->role->permission}}"></mainapp>
            @else
                <mainapp :user="false"></mainapp>
            @endif
        </div>
        <script src="{{mix('/js/app.js')}}"></script>
        <!-- <script src="{{ URL::asset('js/iview.min.js') }}"></script> -->
    </body>
</html>
