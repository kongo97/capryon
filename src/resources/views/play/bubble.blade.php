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
    transition: width 2s, height 2s;
  }
</style>

<script>

    pointer = {
       "x": 0,
       "y": 0,  
    };

    function getRandomInt(min, max) 
    {
        min = Math.ceil(min);
        max = Math.floor(max);
        return Math.floor(Math.random() * (max - min) + min); //The maximum is exclusive and the minimum is inclusive
    }

    function moveBubble(id)
    {
        current_top = document.getElementById(id).getBoundingClientRect().top;
        current_left = document.getElementById(id).getBoundingClientRect().left;
        document.getElementById(id).style.top = current_top + getRandomInt(-1, 1) + 'px';
        document.getElementById(id).style.left = current_left + getRandomInt(-1, 1) + 'px';
    }
</script>
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
          </div>

          <div id="crypto_bar" style="
            position: absolute;
            top: calc(50% - 2.5px);
            left: calc(50% - 2.5px); 
            height: 5px; 
            width: 5px; 
            border-radius: 100%; 
            background-color: #da4040; 
            margin: auto;">
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

              document.getElementById("crypto_bar").style.backgroundColor = self.earn < 0 ? '#da4040' : '#77b977';
              document.getElementById("crypto_bar").style.width = self.earn >= 0 ? (self.earn*50)+'px' : (-1*self.earn*50)+'px';
              document.getElementById("crypto_bar").style.height = self.earn >= 0 ? (self.earn*50)+'px' : (-1*self.earn*50)+'px';

              // event.preventDefault();
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