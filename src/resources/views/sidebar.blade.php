<div class="column is-one-fifth"> 
  <nav class="panel is-warning">
    <p class="panel-heading">
      Market
    </p>
    <div class="panel-block">
      <p class="control has-icons-left">
        <input class="input" type="text" placeholder="Search">
        <span class="icon is-left">
          <i class="fas fa-search" aria-hidden="true"></i>
        </span>
      </p>
    </div>
    
    @foreach($dailyUp as $crypto)
      <div class="panel-block is-active">
        <div class="column is-12 level">
          <div class="level-left" style="float:left;">
            <span class="panel-icon">
              <img src="{{ URL::asset('exchange.png') }}">
            </span>

            <button class="button" style="margin-left: 5px;">
              {{ strtoupper($crypto->name) }} 
            </button>

            <a href="/crypto/{{$crypto->name}}" class="button" style="margin-left: 5px;">
              <span style="color: {{$crypto->delta_percent > 0 ? 'green;' : 'red;'}}">{{ $crypto->numberFormat('price') }}<sub><sub>$</sub></sub></span>
            </a>
          </div>

          <div class="level-right">
            <a href="/crypto/{{$crypto->name}}" class="button" style="margin-right: 5px;">
              <span class="icon is-small">
                <img src="{{ URL::asset('eye.png') }}">
              </span>
            </a>
          
            <a href="https://www.binance.com/en/trade/{{strtoupper($crypto->name)}}_USDT?layout=pro" class="button is-dark" style="margin-right: 5px;">
              <span class="icon is-small">
                <img src="{{ URL::asset('binance.webp') }}">
              </span>
            </a>
          </div>

        </div>
      </div>
    @endforeach
  </nav>
</div>