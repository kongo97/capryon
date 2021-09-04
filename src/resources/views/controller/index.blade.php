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
    <v-app style="background-color: rgba(255, 235, 59, 0.16)">
        <v-main>
            <!-- NAVBAR -->
            <v-app-bar color="yellow">Capryon</v-app-bar>
            
            <!-- PAGE CONTAINER -->
            <v-container>
                <div class="row">
                    <div class="col-md-6">

                        <div class="col-md-12">
                            <v-card
                                elevation="4"
                            >
                                <v-card-title>
                                    Crypto status (last hour)
                                </v-card-title>
                                    <v-list-item>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <span
                                                            :style="{fontSize: '0.8em', fontWeight: '700'}"
                                                        >
                                                            Name
                                                        </span>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <span
                                                            :style="{fontSize: '0.8em', fontWeight: '700'}"
                                                        >
                                                            Earn
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="col-md-2">
                                                        <span
                                                            :style="{fontSize: '0.8em', fontWeight: '700'}"
                                                        >
                                                            Max price
                                                        </span>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <span
                                                            :style="{fontSize: '0.8em', fontWeight: '700'}"
                                                        >
                                                            Min price
                                                        </span>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <span
                                                            :style="{fontSize: '0.8em', fontWeight: '700'}"
                                                        >
                                                            Percent change
                                                        </span>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <span
                                                            :style="{fontSize: '0.8em', fontWeight: '700'}"
                                                        >
                                                            Chart link
                                                        </span>
                                                    </div>
                                                </div>
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <template v-for="(crypto, i) in api_data">
                                        <v-list-item>
                                            <v-list-item-content>
                                                <v-list-item-title>
                                                    <div class="row">
                                                        <div class="col-md-2">
                                                            <span
                                                                :style="{fontSize: '0.9em', fontWeight: '600'}"
                                                            >
                                                                @{{crypto.name}}
                                                            </span>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <span
                                                                :style="{color: crypto.earn_tot > 0 ? 'rgba(0, 200, 83, 0.55)' : 'rgba(215, 59, 30, 0.55)', fontSize: '0.8em', fontWeight: '700'}"
                                                            >
                                                                @{{crypto.earn_tot}} $
                                                            </span>
                                                        </div>
                                                        
                                                        <div class="col-md-2">
                                                            <span
                                                                :style="{color: 'rgba(0, 200, 83, 0.55)', fontSize: '0.8em', fontWeight: '700'}"
                                                            >
                                                                @{{crypto.max}}
                                                            </span>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <span
                                                                :style="{color: 'rgba(215, 59, 30, 0.55)', fontSize: '0.8em', fontWeight: '700'}"
                                                            >
                                                                @{{crypto.min}} 
                                                            </span>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <span
                                                                :style="{color: crypto.earn_tot > 0 ? 'rgba(0, 200, 83, 0.55)' : 'rgba(215, 59, 30, 0.55)', fontSize: '0.8em', fontWeight: '700'}"
                                                            >
                                                                @{{crypto.percent_change}} %
                                                            </span>
                                                        </div>

                                                        <div class="col-md-2">
                                                            <a
                                                                :href="'/charts/' + crypto.name"
                                                                target="_blank"
                                                                :style="{fontSize: '0.8em', fontWeight: '700'}"
                                                            >
                                                                Show
                                                            </span>
                                                        </div>
                                                    </div>
                                                </v-list-item-title>
                                            </v-list-item-content>
                                        </v-list-item>
                                    </template>
                            </v-card>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-12">
                            <v-card
                                elevation="4"
                            >
                                <v-card-title>
                                    Balance
                                </v-card-title>
                                <v-card-text>
                                <span
                                    :style="{fontSize: '2em', fontWeight: '700'}"
                                >
                                    @{{balance}} $
                                </span>
                                </v-card-text>
                            </v-card>
                        </div>

                        <div class="col-md-12">
                            <v-card
                                elevation="4"
                            >
                                <v-card-title>
                                    Last hour
                                </v-card-title>
                                <v-card-text>
                                    <canvas id="line_chart" height="600px"></canvas>
                                </v-card-text>
                            </v-card>
                        </div>
                    </div>
              </div>

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
        api_data: [],
        chart: {
            type: "line",
            data: {},
            options: {},
        },
        bar: {
            type: "bar",
            data: {},
            options: {},
        },
        good_crypto: [],
        all_crypto: [
            "COMPUSDT",
            "SUSHIUSDT",
            "SANDUSDT",
            "UNIUSDT",
            "YFIUSDT",
            "SNXUSDT",
            "AAVEUSDT",
            "KNCUSDT",
            "MKRUSDT",
            "ZRXUSDT",
            "BALUSDT",
            "UMAUSDT",
            "CRVUSDT",
            "ALPHAUSDT",
            "RENUSDT"
        ]
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
        getChart: function() 
        {
            axios.get("/control")
            .then(function (response) 
            {
                // case error
                if (response.data.error === true) {
                    event.preventDefault();
                    return;
                } 

                chart_data = JSON.parse(response.data.data);
                console.log("chart", chart_data);

                app.api_data = chart_data;

                // set datasets
                datasets = [];

                // get context
                var ctx = document.getElementById('myChart').getContext('2d');

                const skipped = (ctx, value) => ctx.p0.skip || ctx.p1.skip ? value : undefined;
                const down = (ctx, value) => ctx.p0.parsed.y > ctx.p1.parsed.y ? value : undefined;

                for(var index = 0; index < chart_data.length; index++)
                {
                    datasets[index] = {
                        label: chart_data[index].name, // 'COMP/USDT',                         // legend name
                        data: chart_data[index].earn, // [65, 59, 80, 81, 56, 55, 40],         // points
                        fill: true,                                 // fill chart (under line)
                        borderColor: "rgba(0, 200, 83, 0.55)", //chart_data.up_count > 3 ? '#00c853' : '#d50000',                     // line color
                        //backgroundColor: chart_data[index].end >= chart_data[index].start ? "rgba(0, 200, 83, 0.35)" : "rgba(215, 59, 30, 0.35)",                     // line color
                        backgroundColor: 'rgba(0, 0, 0, 0.0)',
                        segment: {
                            //borderColor: chart_data[index].close[chart_data[index].close.length + index] >= chart_data[index].close[chart_data[index].close.length + index + 1] ? '#00c853' : '#d50000',
                            borderColor: ctx => down(ctx, 'rgba(215, 59, 30, 0.55)'),
                        },
                        pointRadius: 5,
                        pointBackgroundColor: chart_data[index].close[chart_data[index].close.length - 1] >= chart_data[index].open[chart_data[index].open.length - 1] ? "rgba(0, 200, 83, 0.25)" : "rgba(215, 59, 30, 0.25)",
                        pointBorderColor: chart_data[index].close[chart_data[index].close.length - 1] >= chart_data[index].open[chart_data[index].open.length - 1] ? "rgba(0, 200, 83, 0.25)" : "rgba(215, 59, 30, 0.25)",
                    };
                }

                // app.chart.type = chart_data.type,
                app.chart.data = {
                    labels: chart_data[0].close_time, //["gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio"],                                     // chart labels
                    datasets: datasets
                },
                app.chart.options = {
                    scales: {
                        y: {
                            stacked: true
                        }
                    },
                    // indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                }

                // CHARTS

                // draw line chart
                var stackedBar = new Chart(ctx, {
                    type: app.chart.type,
                    data: app.chart.data,
                    options: app.chart.options
                });


                /* BAR CHART */ 
                // set datasets
                datasets_line = [];

                // get context
                var ctx2 = document.getElementById('line_chart').getContext('2d');

                for(var index = 0; index < chart_data.length; index++)
                {
                    datasets_line[index] = {
                        label: chart_data[index].name, // 'COMP/USDT',                         // legend name
                        data: [chart_data[index].earn_tot], // [65, 59, 80, 81, 56, 55, 40],         // points
                        fill: true,                                 // fill chart (under line)
                        borderColor: chart_data[index].earn_tot > 0 ? '#00c853' : '#d50000', //chart_data.up_count > 3 ? '#00c853' : '#d50000',                     // line color
                        backgroundColor: chart_data[index].earn_tot > 0 ? "rgba(0, 200, 83, 0.35)" : "rgba(215, 59, 30, 0.35)",                     // line color
                    };

                    if(chart_data[index].earn_tot > 0)
                    {
                        app.good_crypto[app.good_crypto.length] = chart_data[index].name;
                    }
                }

                // app.bar.type = chart_data.type,
                app.bar.data = {
                    labels: [chart_data[0].close_time[chart_data[0].close_time.length -1]], //["gennaio", "febbraio", "marzo", "aprile", "maggio", "giugno", "luglio"],                                     // chart labels
                    datasets: datasets_line
                },
                app.bar.options = {
                    scales: {
                        y: {
                            stacked: false
                        }
                    },
                    // indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                }

                // draw bar chart
                var stackedLine = new Chart(ctx2, {
                    type: "bar",
                    data: app.bar.data,
                    options: app.bar.options
                });
            });
        },
      },
      mounted () {
        this.read_balance = this.getChart();

        this.balance = this.getBalance();
      },
    })

  </script>
</body>
</html>