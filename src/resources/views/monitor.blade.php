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
    <canvas id="priceLineZoom" style="width: 100%; height: 400px;"></canvas>
  </div>
</div>

<div class="column is-one-fifth">
  <div class="card" id="cardTradeListTotal">
    <canvas id="tradeListTotal" style="width: 100%; height: 100px;"></canvas>
  </div>
</div>

<div class="column is-one-third">
  <div class="card">
    <canvas id="tradeListBuyers" style="width: 100%; height: 400px;"></canvas>
  </div>
</div>

<div class="column is-one-third">
  <div class="card">
    <canvas id="tradeListSellers" style="width: 100%; height: 400px;"></canvas>
  </div>
</div>

<div class="column is-one-third">
  <div class="card">
    <canvas id="tradeListScatter" style="width: 100%; height: 400px;"></canvas>
  </div>
</div>

<div class="column is-three-fifths">
  <div class="card bars-container" style="height: 200px !important">
    <div 
      v-for="history in history_15m.history"
      class="vertical-bar" 
      v-bind:class="{ green: history.delta > 0, 'red': history.delta <= 0 }"
      v-bind:style="{ '--height': Math.abs((history.close - history.open) * 200 / (history_1h.info.max - history_1h.info.min)) + 'px', '--margin': Math.abs((history.max - history.close) * 200 / (history_1h.info.max - history_1h.info.min)) + 'px'}"></div>
    </div>
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
      // data
      data: {
        labels: [
          'Buyers',
          'Sellers'
        ],
        datasets: [{
          label: 'Trade List',
          data: [{{ $buyers }}, {{ $sellers }}],
          backgroundColor: [
            'rgb(255, 99, 132)',
            'rgba(75, 192, 192)',
          ],
          hoverOffset: 4
        }]
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
      // chart
      chart: null,
      chart_buyers: null,
      chart_sellers: null,
      chart_tradeList_scatter: null,
      chart_priceLine: null,
      chart_priceLineZoom: null,
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

            app.price = response.data.price;

            app.chart_priceLine.update();
            app.chart_priceLineZoom.update();
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
    },
    mounted () {
      // this.read_balance = this.getBalance();
      this.updateTradeListPie();
      this.updateTradeListBuyer();
      this.updateTradeListSeller();
      this.updateTradeListScatter();
      this.updatePriceLine();

      this.$nextTick(function () {
        window.setInterval(() => {
          app.getTradeList();
          app.updateHistory_15m();
          app.updateHistory_1h();
          app.updatePrice();
          app.updateBalance();
          app.updateBalanceUSDT();
          app.updateBalanceCrypto();
        },5000);
      })
    },
  })
  

</script>

@endsection