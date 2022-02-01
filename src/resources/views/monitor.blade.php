<div class="column is-one-fifth">
  <canvas id="tradeListTotal" style="width: 100%; height: 100px;"></canvas>
</div>

<div class="column is-two-fifths">
  <canvas id="tradeListBuyers" style="width: 100%; height: 400px;"></canvas>
</div>

<div class="column is-two-fifths">
  <canvas id="tradeListSellers" style="width: 100%; height: 400px;"></canvas>
</div>

<div class="column is-three-fifths">
  <div class="bars-container" style="height: 200px !important">
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
      balance: 0,
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
            "{{$trade['price']}}",
          @endforeach
        ],
        datasets: [{
          label: 'Buyers',
          data: [
            @foreach($trade_list['buyers'] as $trade)
              {{$trade['quoteQty']}},
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
            "{{$trade['price']}}",
          @endforeach
        ],
        datasets: [{
          label: 'Sellers',
          data: [
            @foreach($trade_list['sellers'] as $trade)
              {{$trade['quoteQty']}},
            @endforeach
          ],
          backgroundColor: 'rgba(75, 192, 192, 0.3)',
          borderColor: 'rgba(75, 192, 192, 0.3)',
          borderWidth: 1
        }]
      },
      // chart
      chart: null,
      chart_buyers: null,
      chart_sellers: null,
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
            app.buyers.datasets.data = [];

            for(buyer in _buyers)
            {
              app.buyers.labels.push(_buyers[buyer].price+"");
              app.buyers.datasets[0].data.push(_buyers[buyer].quoteQty);
            }

            app.sellers.labels = [];
            app.sellers.datasets.data = [];

            for(seller in _sellers)
            {
              app.sellers.labels.push(_sellers[seller].price+"");
              app.sellers.datasets[0].data.push(_sellers[seller].quoteQty);
            }

            app.chart.update();
            app.chart_buyers.update();
            app.chart_sellers.update();
        });
      },
      updateTradeListPie: function()
      { 
        html = this.html;

        config = {
          type: 'doughnut',
          data: this.data,
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
    },
    mounted () {
      // this.read_balance = this.getBalance();
      this.updateTradeListPie();
      this.updateTradeListBuyer();
      this.updateTradeListSeller();

      this.$nextTick(function () {
        window.setInterval(() => {
          app.getTradeList();
          app.updateHistory_15m();
          app.updateHistory_1h();
        },5000);
      })
    },
  })
  

</script>

@endsection