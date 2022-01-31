<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>Capryon &bull; {{ $title }}</title>
        <link href="https://unpkg.com/vuesax@4.0.1-alpha.16/dist/vuesax.min.css" rel="stylesheet">
        <!--<link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">-->
    </head>
    <body> 
        @include('navbar')

        <div class="block"></div>

        <div id="app" class="columns">
            <div class="columns is-multiline column is-12">
                @include($page)
            </div>
        </div>

        @include('scripts')  
        <script src="https://unpkg.com/vue/dist/vue.js"></script>
        <script src="https://unpkg.com/vuesax@4.0.1-alpha.16/dist/vuesax.min.js"></script>
        <!-- axios -->
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    </body>
</html>