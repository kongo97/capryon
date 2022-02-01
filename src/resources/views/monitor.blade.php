<div class="column is-one-fifth">
  <canvas id="myChart"></canvas>
</div>

<!-- VUE -->
@section("vue")

<script>
  var app = new Vue({
    el: '#app',
    data: {
      balance: 0,
      buyers: {{ $buyers }},
      sellers: {{ $sellers }},
      // data
      data: {
        labels: [
          'Buyers',
          'Sellers'
        ],
        datasets: [{
          label: 'Trade List',
          data: [buyers, sellers],
          backgroundColor: [
            'red',
            'green',
          ],
          hoverOffset: 4
        }]
      },
      // config
      config: {
        type: 'doughnut',
        data: data,
      },
      // chart
      chart: null,
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
      updateTradeListPie: function()
      {
        app.chart = new Chart(
          document.getElementById('myChart'),
          config
        );
      }
    },
    mounted () {
      // this.read_balance = this.getBalance();
    },
  })
</script>

@endsection

 

<script>
  var myChart = new Chart(
    document.getElementById('myChart'),
    app.config
  );
</script>

 

 