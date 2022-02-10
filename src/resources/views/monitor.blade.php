<div class="column is-full columns is-multiline">
  <div class="column is-2">
    <div class="card has-background-dark has-text-light">
      <span class="tag is-warning corner-left">crypto</span>
      <h1 id="price-in-card">{{ strtoupper($crypto->name) }}<sub><sub>&nbsp;</sub></sub></h1>
    </div>
  </div>  

  <div class="column is-2">
    <div class="card">
      <span class="tag is-warning corner-left">max</span>
      <h1 id="price-in-card">{{rtrim($crypto->max, 0)}}<sub><sub>$</sub></sub></h1>
    </div>
  </div>

  <div class="column is-2">
    <div class="card">
      <span class="tag is-warning corner-left">min</span>
      <h1 id="price-in-card">{{rtrim($crypto->min, 0)}}<sub><sub>$</sub></sub></h1>
    </div>
  </div>

  <div class="column is-2">
    <div class="card">
      <span class="tag is-warning corner-left">price</span>
      <h1 id="price-in-card">@{{price}}<sub><sub>$</sub></sub></h1>
    </div>
  </div>

  <div class="column is-2">
    <div class="card">
      <span class="tag is-warning corner-left">balance</span>
      <h1 id="price-in-card">@{{balance_usdt}}<sub><sub>USDT</sub></sub></h1>
    </div>
  </div>

  <div class="column is-2">
    <div class="card">
      <span class="tag is-warning corner-left">balance</span>
      <h1 id="price-in-card">@{{Math.round(balance_crypto * price * 100) / 100}}<sub><sub>{{strtoupper($crypto->name)}}</sub></sub></h1>
    </div>
  </div>

  <div class="column is-12">
    <ul class="columns" style="overflow: scroll;">
      <li v-for="follow in follows" class="column">
        <a :href="'/crypto/'+follow.name">
          <div class="card follow-link">@{{follow.name}}<sub><sub>(@{{follow.last_diff_perc}}%)</sub></sub></div>
        </a>
      </li>
    </ul>
  </div>

  <div class="column is-2">
    <div class="card">
      <span class="tag is-warning corner-left">bid</span>
      <div class="buttons-in-card">
        <button class="button is-success" v-on:click="buy">BUY</button>
        <button class="button is-danger" v-on:click="sell">SELL</button>
      </div>
    </div>
  </div>
</div>

<div class="column is-two-fifths">
  <div class="card">
    <canvas id="priceLine" style="width: 100%; height: 400px;"></canvas>
  </div>
</div>

<div class="column is-two-fifths">
  <div class="card">
    <div class="info-card">
      max: @{{zoomMax}}<sub><sub>$</sub></sub>
      <br>
      min: @{{zoomMin}}<sub><sub>$</sub></sub>
      <br>
      price: @{{price}}<sub><sub>$</sub></sub>
    </div>
    <canvas id="priceLineZoom" style="width: 100%; height: 400px;"></canvas>
  </div>
</div>

<div class="column is-one-fifth">
  <div class="card" id="cardTradeListTotal">
    <canvas id="tradeListTotal" style="width: 100%; height: 100px;"></canvas>
  </div>
</div>

<div class="column is-one-third">
  <div class="">
    <canvas id="tradeListBuyers" style="width: 100%; height: 200px;"></canvas>
  </div>

  <div class="">
    <canvas id="tradeListSellers" style="width: 100%; height: 200px;"></canvas>
  </div>
</div>

<div class="column is-two-thirds">
  <div class="card">
    <canvas id="bars_1h" style="width: 100%; height: 400px;"></canvas>
  </div>
</div>

<div class="column is-two-thirds">
  <div class="card">
    <canvas id="tradeListScatter" style="width: 100%; height: 400px;"></canvas>
  </div>
</div>

<!-- VUE -->
@section("vue")

<script>
  var app = new Vue({
    el: '#app',
    data: {
      price: 0,
      balance: 0,
      balance_crypto: {{$balance_crypto != '' ? $balance_crypto : '0'}},
      balance_usdt: {{$balance_usdt}},
      history_15m: JSON.parse('{!! json_encode($history_15m) !!}'),
      history_1h: JSON.parse('{!! json_encode($history_1h) !!}'),
      from_max: 50,
      from_min: 50,
      zoomMax: null,
      zoomMin: null,
      follows: [],
      // data
      data: {
        labels: [
          'Buyers',
          'Sellers'
        ],
        datasets: [
          {
            label: 'Trade List',
            data: [{{ $buyers }}, {{ $sellers }}],
            backgroundColor: [
              'rgb(255, 99, 132)',
              'rgba(75, 192, 192)',
            ],
            hoverOffset: 4
          },
          {
            label: 'Tip',
            data: [50, 50],
            backgroundColor: [
              'rgba(54, 54, 54)',
              'rgba(255, 224, 138)',
            ],
            hoverOffset: 4
          }
        ]
      },
      // buyers
      buyers: {
        labels: [
          @foreach($trade_list['buyers'] as $trade)
            {{$trade['quoteQty']}},
          @endforeach
        ],
        datasets: [{
          label: 'Buyers',
          data: [
            @foreach($trade_list['buyers'] as $trade)
              "{{$trade['price']}}",
            @endforeach
          ],
          backgroundColor: 'rgba(255, 99, 132, 0.3)',
          borderColor: 'rgba(255, 99, 132, 0.3)',
          borderWidth: 1
        }]
      },
      // sellers
      sellers: {
        labels: [
          @foreach($trade_list['sellers'] as $trade)
            {{$trade['quoteQty']}},
          @endforeach
        ],
        datasets: [{
          label: 'Sellers',
          data: [
            @foreach($trade_list['sellers'] as $trade)
              "{{$trade['price']}}",
            @endforeach
          ],
          backgroundColor: 'rgba(75, 192, 192, 0.3)',
          borderColor: 'rgba(75, 192, 192, 0.3)',
          borderWidth: 1
        }]
      },
      // trade list scatter
      traderListScatter: {
        datasets: [
          {
            label: 'Buyers',
            data: [
              @foreach($trade_list['buyers'] as $trade)
                {
                  x: {{$trade['quoteQty']}},
                  y: {{$trade['price']}}
                },
              @endforeach
            ],
            backgroundColor: 'rgb(255, 99, 132)'
          },
          {
            label: 'Sellers',
            data: [
              @foreach($trade_list['sellers'] as $trade)
                {
                  x: {{$trade['quoteQty']}},
                  y: {{$trade['price']}}
                },
              @endforeach
            ],
            backgroundColor: 'rgba(75, 192, 192)'
          }
        ],
      },
      priceLine: 
      {
        labels: [],
        datasets: [
          {
            label: 'Price line',
            data: [],
            fill: false,
            borderColor: 'rgb(75, 192, 192)',
            tension: 0.1
          }
        ]
      },
      bars_1h: {
        labels: [
          @foreach($history_1h['history'] as $history)
            "{{$history['open_time']}}",
          @endforeach
        ],
        datasets: [
          {
            label: 'Price 1h',
            data: [
              @foreach($history_1h['history'] as $history)
                [{{$history['open']}}, {{$history['close']}}],
              @endforeach
            ],
            backgroundColor: "rgb(75, 192, 192)",
          }
        ]
      },
      // chart
      chart: null,
      chart_buyers: null,
      chart_sellers: null,
      chart_tradeList_scatter: null,
      chart_priceLine: null,
      chart_priceLineZoom: null,
      chart_bars_1h: null,
    },
    methods: {
      getTradeList: function() 
      {
        axios.get('/api/tradeList/{{$crypto->name}}')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 

            app.data.datasets[0].data[0] = response.data.count['buyers'];
            app.data.datasets[0].data[1] = response.data.count['sellers'];

            _buyers = response.data.buyers;
            _sellers = response.data.sellers;

            app.buyers.labels = [];
            app.buyers.datasets[0].data = [];
            app.traderListScatter.datasets[0].data = []

            for(buyer in _buyers)
            {
              app.buyers.labels.push(_buyers[buyer].quoteQty);
              app.buyers.datasets[0].data.push(_buyers[buyer].price+"");
              app.traderListScatter.datasets[0].data.push({x: _buyers[buyer].quoteQty, y: _buyers[buyer].price});
            }

            app.sellers.labels = [];
            app.sellers.datasets[0].data = [];
            app.traderListScatter.datasets[1].data = []

            for(seller in _sellers)
            {
              app.sellers.labels.push(_sellers[seller].quoteQty);
              app.sellers.datasets[0].data.push(_sellers[seller].price+"");
              app.traderListScatter.datasets[1].data.push({x: _sellers[seller].quoteQty, y: _sellers[seller].price});
            }

            app.chart.update();
            app.chart_buyers.update();
            app.chart_sellers.update();
            app.chart_tradeList_scatter.update();
        });
      },
      updateTradeListPie: function()
      { 
        html = this.html;

        config = {
          type: 'doughnut',
          data: this.data,
          rotation: 180,
        },

        this.chart = new Chart(
          document.getElementById('tradeListTotal'),
          config
        );
      },
      updateTradeListBuyer: function()
      { 
        config = {
          type: 'bar',
          data: this.buyers,
          options: {
            indexAxis: 'y',
            scales: {
              y: {
                beginAtZero: true
              }
            }
          },
        },

        this.chart_buyers = new Chart(
          document.getElementById('tradeListBuyers'),
          config
        );
      },
      updateTradeListSeller: function()
      { 
        config = {
          type: 'bar',
          data: this.sellers,
          options: {
            indexAxis: 'y',
            scales: {
              y: {
                beginAtZero: true
              }
            }
          },
        },

        this.chart_sellers = new Chart(
          document.getElementById('tradeListSellers'),
          config
        );
      },
      updateTradeListScatter: function()
      { 
        config = {
          type: 'scatter',
          data: this.traderListScatter,
          options: {
            scales: {
              x: {
                type: 'linear',
                position: 'bottom'
              }
            }
          }
        };

        this.chart_tradeList_scatter = new Chart(
          document.getElementById('tradeListScatter'),
          config
        );
      },
      updatePriceLine: function()
      { 

        config = {
          type: 'line',
          options: {
            scales: {
              y: {
                min: {{$crypto->min}},
                max: {{$crypto->max}},
              }
            }
          },
          data: this.priceLine,
        };

        this.chart_priceLine = new Chart(
          document.getElementById('priceLine'),
          config
        );

        config = {
          type: 'line',
          data: this.priceLine,
        };

        this.chart_priceLineZoom = new Chart(
          document.getElementById('priceLineZoom'),
          config
        );
      },
      updateBars1h: function()
      { 
        config = {
          type: 'bar',
          data: this.bars_1h,
          options: {
            scales: {
              y: {
                min: this.history_1h["info"]['min'],
                max: this.history_1h["info"]['max'],
              }
            }
          },
        };

        this.chart_bars_1h = new Chart(
          document.getElementById('bars_1h'),
          config
        );
      },
      updateHistory_15m: function()
      {
        axios.get('/api/updateHistory_15m/{{$crypto->name}}')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 

            app.history_15m = response.data;
        });
      },
      updateHistory_1h: function()
      {
        axios.get('/api/updateHistory_1h/{{$crypto->name}}')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 

            app.history_1h = response.data;

            app.bars_1h.labels = [];
            app.bars_1h.datasets[0].data = [];

            for(var x=0; x<app.history_1h['history'].length; x++)
            {
              app.bars_1h.labels.push(app.history_1h['history'][x].open_time);
              app.bars_1h.datasets[0].data.push([app.history_1h['history'][x].open, app.history_1h['history'][x].close]);
            }

            app.chart_bars_1h.update();
        });
      },
      updatePrice: function()
      {
        axios.get('/api/price/{{$crypto->name}}')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 

            app.priceLine.labels.push(response.data.time);
            app.priceLine.datasets[0].data.push(response.data.price);

            app.chart_priceLine.update();
            app.chart_priceLineZoom.update();

            max = null;
            min = null;

            for(data in app.priceLine.datasets[0].data)
            {
              _data = app.priceLine.datasets[0].data[data]

              max = (max < _data || max == null) ? _data : max;
              min = (min > _data || min == null) ? _data : min;
            }

            // update max min
            app.zoomMax = max;
            app.zoomMin = min;

            from_max = ((max - min) - (response.data.price - min)) * 100 / (max - min);
            from_min = 100 - from_max;
          
            app.data.datasets[1].data[0] = from_min;
            //app.data.datasets[1].data[1] = 0;
            app.data.datasets[1].data[1] = from_max;

            app.price = response.data.price;

            app.chart.update();
        });
      },
      updateBalance: function()
      {
        axios.get('/api/balance')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 
            app.balance = response.data.balance;
        });
      },
      updateBalanceUSDT: function()
      {
        axios.get('/api/amount/usdt')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 
            app.balance_usdt = response.data.amount;
        });
      },
      updateBalanceCrypto: function()
      {
        axios.get('/api/amount/{{$crypto->name}}')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 
            app.balance_crypto = response.data.amount != '' ? response.data.amount : 0;
        });
      },
      buy: function()
      {
        axios.get('/buy/{{$crypto->name}}')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 
            console.log(response.data);
        });
      },
      sell: function()
      {
        axios.get('/sell/{{$crypto->name}}')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 
            console.log(response.data);
        });
      },
      follow: function()
      {
        axios.get('/follow')
        .then(function (response) 
        {
            // case error
            if (response.data.error === true) {
              event.preventDefault();
              return;
            } 

            app.follows = response.data;
        });
      },
    },
    mounted () {
      // this.read_balance = this.getBalance();
      this.updateTradeListPie();
      this.updateTradeListBuyer();
      this.updateTradeListSeller();
      this.updateTradeListScatter();
      this.updatePriceLine();
      this.updateBars1h();

      this.$nextTick(function () {
        window.setInterval(() => {
          app.getTradeList();
          app.updateHistory_15m();
          app.updateHistory_1h();
          app.updatePrice();
          app.updateBalance();
          app.updateBalanceUSDT();
          app.updateBalanceCrypto();
          app.follow();
        },5000);
      })
    },
  })
  

</script>

@endsection