<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Capryon &bull; {{ $title }}</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma.min.css">
        <link rel="stylesheet" href="{{ URL::asset('css/style.css') }}">
        <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
        <!-- axios -->
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    </head>
    <body> 
        @include('navbar')

        <div id="app" class="columns">
            <div class="columns is-multiline column is-12">
                @include($page)
            </div>
        </div>

        @include('scripts')  
    </body>
</html>