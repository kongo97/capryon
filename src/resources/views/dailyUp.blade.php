@include('sidebar')

<div class="column is-four-fifths">
  <div class="card table-container">
    <table class="table is-fullwidth">
      <thead>
        <tr class="has-background-warning">
          <th><abbr title="Id">#</abbr></th>
          <th>Name</th>
          <th>Symbol</th>
          <th>Start<sub>($)</sub></th>
          <th>Price<sub>($)</sub></th>
          <th>Delta<sub>(%)</sub></th>
          <th>Min<sub>($)</sub></th>
          <th>Max<sub>($)</sub></th>
          <th>Earn<sub>($)</sub></th>
          <th>History<sub>(24h)</sub></th>
          <th>History<sub>(15m)</sub></th>
        </tr>
      </thead>
      <tbody>
        @foreach($dailyUp as $crypto)
          <tr>
              <th>{{$crypto->id}}</th>
              <td>{{$crypto->link()}}</td>
              <td>{{$crypto->symbol}}</td>
              <td>{{$crypto->numberFormat('start')}}</td>
              <td>{{$crypto->numberFormat('price')}}</td>
              <td>{{$crypto->numberFormat('delta_percent')}}</td>
              <td>{{$crypto->numberFormat('min')}}</td>
              <td>{{$crypto->numberFormat('max')}}</td>
              <td>{{$crypto->_dailyEarn()}}</td>
              <td>
                @if($crypto->history_24h != null)
                  <div class="bars-container">
                    @foreach((json_decode($crypto->history_24h, true))['history'] as $history)
                      <div class="vertical-bar @if($history['delta'] > 0) green @else red @endif" style="--height: {{ abs(($history['close'] - $history['open']) * 50 / ($crypto->max - $crypto->min))}}px; --margin: {{ abs(($crypto->max - $history['close']) * 50 / ($crypto->max - $crypto->min))}}px"></div>
                    @endforeach
                  </div>
                @endif
              </td>
              <td>
                @if($crypto->history_15m != null)
                  <div class="bars-container">
                    @foreach((json_decode($crypto->history_15m, true))['history'] as $history)
                      @if( (json_decode($crypto->history_15m, true))['info']['max'] - (json_decode($crypto->history_15m, true))['info']['min'] > 0)
                        <div class="vertical-bar @if($history['delta'] > 0) green @else red @endif" style="--height: {{ abs(($history['close'] - $history['open']) * 50 / ((json_decode($crypto->history_15m, true))['info']['max'] - (json_decode($crypto->history_15m, true))['info']['min']))}}px; --margin: {{ abs(($history['max'] - $history['close']) * 50 / ((json_decode($crypto->history_15m, true))['info']['max'] - (json_decode($crypto->history_15m, true))['info']['min']))}}px"></div>
                      @endif
                    @endforeach
                  </div>
                @endif
              </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- VUE -->
@section("vue")

<script>
  var app = new Vue({
    el: '#app',
    data: {
      balance: 0,

      // !!! navbar !!!
      drawer: false,
      group: null,
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
      // this.read_balance = this.getBalance();
    },
  })

  setTimeout(function() {
    location.reload();
  }, 1000 * 60 * 5);
</script>

@endsection