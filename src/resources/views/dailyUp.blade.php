<div class="column is-12">
  <div class="table-container">
    <table class="table card is-fullwidth transparent">
      <thead>
        <tr>
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
          <th>History<sub>(24h compressed)</sub></th>
          <th>History<sub>(1h)</sub></th>
          <th>History<sub>(1h compressed)</sub></th>
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
                <div class="bars-container">
                  @foreach((json_decode($crypto->history_24h, true))['history'] as $history)
                    <div class="vertical-bar @if($history['delta'] > 0) green @else red @endif" style="--height: {{ abs(($history['close'] - $history['open']) * 50 / ($crypto->max - $crypto->min))}}px; --margin: {{ abs(($crypto->max - $history['close']) * 50 / ($crypto->max - $crypto->min))}}px"></div>
                  @endforeach
                </div>
              </td>
              <td>
                <div class="bars-container">
                @foreach((json_decode($crypto->history_24h, true))['compressed'] as $history)
                    <div class="vertical-bar @if($history['delta'] > 0) green @else red @endif" style="--height: {{ abs(($history['close'] - $history['open']) * 50 / ($crypto->max - $crypto->min))}}px; --margin: {{ abs(($crypto->max - $history['close']) * 50 / ($crypto->max - $crypto->min))}}px"></div>
                  @endforeach
                </div>
              </td>
              <td>
                <div class="bars-container">
                  @foreach((json_decode($crypto->history_1h, true))['history'] as $history)
                    @if( (json_decode($crypto->history_1h, true))['info']['max'] - (json_decode($crypto->history_1h, true))['info']['min'] > 0)
                      <div class="vertical-bar @if($history['delta'] > 0) green @else red @endif" style="--height: {{ abs(($history['close'] - $history['open']) * 50 / ((json_decode($crypto->history_1h, true))['info']['max'] - (json_decode($crypto->history_1h, true))['info']['min']))}}px; --margin: {{ abs(($history['max'] - $history['close']) * 50 / ((json_decode($crypto->history_1h, true))['info']['max'] - (json_decode($crypto->history_1h, true))['info']['min']))}}px"></div>
                    @endif
                  @endforeach
                </div>
              </td>
              <td>
                <div class="bars-container">
                  @foreach((json_decode($crypto->history_1h, true))['compressed'] as $history)
                    @if( (json_decode($crypto->history_1h, true))['info']['max'] - (json_decode($crypto->history_1h, true))['info']['min'] > 0)
                      <div class="vertical-bar @if($history['delta'] > 0) green @else red @endif" style="--height: {{ abs(($history['close'] - $history['open']) * 50 / ((json_decode($crypto->history_1h, true))['info']['max'] - (json_decode($crypto->history_1h, true))['info']['min']))}}px; --margin: {{ abs(($history['max'] - $history['close']) * 50 / ((json_decode($crypto->history_1h, true))['info']['max'] - (json_decode($crypto->history_1h, true))['info']['min']))}}px"></div>
                    @endif
                  @endforeach
                </div>
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
  export default {
    data:() => ({
      active: 'guide'
    })
  }
</script>

<script>
  var app = new Vue({
    el: '#app',
    active: 'guide',
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
      this.read_balance = this.getBalance();
    },
  })
</script>
@endsection