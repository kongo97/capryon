<!DOCTYPE html>
<html>
<head>
  <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css" rel="stylesheet">
  <!-- chart js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.min.js" integrity="sha512-Wt1bJGtlnMtGP0dqNFH1xlkLBNpEodaiQ8ZN5JLA5wpc1sUlk/O5uuOMNgvzddzkpvZ9GLyYNa8w2s7rqiTk5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
                  <v-card
                      elevation="4"
                  >
                      <v-card-title>
                          Last hour (every 5 minutes)
                      </v-card-title>
                      <v-card-text>
                          <canvas id="myChart" height="600px"></canvas>
                      </v-card-text>
                      <v-card-text>
                        <div class="row">
                          <div class="col-md-1">
                            <v-btn href="/charts/COMPUSDT">COMPUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/SUSHIUSDT">SUSHIUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/SANDUSDT">SANDUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/UNIUSDT">UNIUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/YFIUSDT">YFIUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/SNXUSDT">SNXUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/AAVEUSDT">AAVEUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/KNCUSDT">KNCUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/MKRUSDT">MKRUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/ZRXUSDT">ZRXUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/BALUSDT">BALUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/UMAUSDT">UMAUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/CRVUSDT">CRVUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/ALPHAUSDT">ALPHAUSDT</v-btn>
                          </div>
                          <div class="col-md-1">
                            <v-btn href="/charts/RENUSDT">RENUSDT</v-btn>
                          </div>
                        </div>
                      </v-card-text>

                  </v-card>
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
        chart: {
            type: "line",
            data: {},
            options: {},
        }
      },
      methods: {
        getChart: function() 
        {
            axios.get("/chart/{{$crypto}}")
            .then(function (response) 
            {
                // case error
                if (response.data.error === true) {
                    event.preventDefault();
                    return;
                } 

                chart_data = JSON.parse(response.data.data);
                console.log("chart", chart_data);

                // app.chart.type = chart_data.type,
                app.chart.data = {
                    labels: chart_data.close_time, //["gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio"],                                     // chart labels
                    datasets: 
                    [
                        {
                            label: chart_data.name, // 'COMP/USDT',                         // legend name
                            data: chart_data.close, // [65, 59, 80, 81, 56, 55, 40],         // points
                            fill: true,                                 // fill chart (under line)
                            borderColor: chart_data.end > chart_data.start ? '#00c853' : '#d50000', //chart_data.up_count > 3 ? '#00c853' : '#d50000',                     // line color
                            backgroundColor: chart_data.end > chart_data.start ? "rgba(0, 200, 83, 0.35)" : "rgba(215, 59, 30, 0.35)",                     // line color
                        }
                    ]
                },
                app.chart.options = {
                    scales: {
                        y: {
                            stacked: true
                        }
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                }

                // CHARTS

                // get context
                var ctx = document.getElementById('myChart').getContext('2d');

                // draw chart
                var stackedLine = new Chart(ctx, {
                    type: app.chart.type,
                    data: app.chart.data,
                    options: app.chart.options
                });
            });
        },
      },
      mounted () {
        this.read_balance = this.getChart();
      },
    })

  </script>
</body>
</html>