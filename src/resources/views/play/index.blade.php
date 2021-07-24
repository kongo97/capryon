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
  #crypto_bar {
    transition: border 2s;
  }
</style>

  <div id="app">
    <v-app>
      <v-main>
        <v-container>
          Capryon

          <div class="row">
            <div class="col-md-3">
              <span>COMPUSDT</span><br>
              <span>@{{earn}} $</span>
              <br>
              <button style="background-color: #77b977; line-height: 35px; width: 100px; height: 35px; margin: auto;" @click="play('COMPUSDT')">PLAY</button>
              <button style="background-color: #da4040; line-height: 35px; width: 100px; height: 35px; margin: auto;" @click="stop()">STOP</button>
            </div>
            
            <div class="col-md-9" style="position: relative;">
              <div id="crypto_bar" style="
                position: absolute;
                top: calc(50% - 2.5px); 
                height: 5px; 
                width: 5px; 
                border-radius: 15px; 
                background-color: #da4040; 
                margin: auto;">
              </div>
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
        price: 0,
        start: 0,
        amount: 0,
        earn: 0,
        closed: true,
        play_check: true,
      },
      methods: {
        getPrice: function(name) 
        {
          self = this;

          axios.post('/price', {
              name: name
          })
          .then(function (response) 
          {
              // case error
              if (response.data.error === true) {
                  event.preventDefault();
                  return;
              } 

              app.price = response.data.data.price;
              if(app.start == -1)
              {
                app.start = response.data.data.price;
                app.amount = 300 / app.start;
              }

              self.earn = self.amount * self.price - 300;

              console.log("earn", self.earn);

              //document.getElementById("crypto_bar").style.marginTop = (-1*self.earn*200)+'px';
              document.getElementById("crypto_bar").style.borderBottom = self.earn < 0 ? (-1*self.earn*50)+'px solid #da4040' : 'none';
              document.getElementById("crypto_bar").style.borderTop = self.earn >= 0 ? (self.earn*50)+'px solid #77b977' : 'none';
              document.getElementById("crypto_bar").style.backgroundColor = self.earn < 0 ? '#da4040' : '#77b977';
              document.getElementById("crypto_bar").style.bottom = self.earn < 0 ? 'unset' : 'calc(50% - 2.5px)';
              document.getElementById("crypto_bar").style.top = self.earn < 0 ? 'calc(50% - 2.5px)' : 'unset';

              event.preventDefault();
          });
        },
        updatePrice: function(name)
        {
          this.getPrice(name);
        },
        play: function(name)
        {
          if(this.closed === true)
          {
            this.play_check = true;
            this.closed = false;
          }

          self = this;
          this.updatePrice(name);

          setTimeout(function() {
            if(self.play_check === true)
            {
              return self.play(name);
            }
          }, 3000);

          return;   
        },
        stop: function() {
          this.play_check = false;
          this.closed = true;
        }
      },
      mounted () {
        this.start = -1;
        this.getPrice("COMPUSDT");
      },
    })
  </script>
</body>
</html>