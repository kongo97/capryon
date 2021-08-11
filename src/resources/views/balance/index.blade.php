<!DOCTYPE html>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
</head>
<body>
<style>
  #balance {
    font-size: 10em;
    text-align: center;
  }
</style>

  <div id="app">
    <v-app>
        <v-main>
            <!-- NAVBAR -->
            <v-app-bar color="yellow">Capryon</v-app-bar>
            
            <!-- PAGE CONTAINER -->
            <v-container>
              <div class="row">
                  <div class="col-md-12">
                  <h1 id="balance">@{{balance}} $</h1>
                  </div>
              </div>
            </v-container>
        </v-main>
    </v-app>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/vue@2.x/dist/vue.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.js"></script>
  <!-- axios -->
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script>
    var app = new Vue({
      el: '#app',
      vuetify: new Vuetify(),
      data: {
        balance: 0,
      },
      methods: {
        getBalance: function() 
        {
          axios.get('/_balance')
          .then(function (response) 
          {
              // case error
              if (response.data.error === true) {
                event.preventDefault();
                return;
              } 

              app.balance = response.data.balance;
              console.log("balance", app.balance);
          });
        },
      },
      mounted () {
        this.read_balance = this.getBalance();
      },
    })
  </script>
</body>
</html>