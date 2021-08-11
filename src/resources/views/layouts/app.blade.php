<!doctype html>
<html lang="en">
    <head>
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
        <title>Capryon</title>

        <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
        <!-- axios -->
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    </head>
    <body style="font-family: 'Black Ops one' !important"> 
        <style>
            button {
                font-family: 'Black Ops one';
            }
            label {
                font-family: 'Black Ops one';
            }
            input {
                font-family: 'Black Ops one';
            }
        </style>
        <div id="app">
            <v-app>
                <v-main>

                @include('navbar')

                </v-main>
            </v-app>
        </div>
        @yield('vue')

        @include('footer')

        @include('scripts')  
    </body>
</html>