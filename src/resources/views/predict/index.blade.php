<!doctype html>
<html lang="en">
    <head>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.3/css/bulma-rtl.min.css">
    </head>
    <body> 
        <nav class="navbar" role="navigation" aria-label="main navigation">
            <div class="navbar-brand">
                <a class="navbar-item" href="https://bulma.io">
                    <img src="https://bulma.io/images/bulma-logo.png" alt="Bulma: Free, open source, and modern CSS framework based on Flexbox" width="112" height="28">
                </a>
            </div>
        </nav>

        <div class="contianer is-fluid columns is-multiline">
            <div class="column">
                <div class="control has-icons-left">
                    <div class="select">
                        <select>
                            <option selected disabled>Currency</option>
                            @foreach($currencies as $currency)
                                <option>{{$currency['symbol']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <span class="icon is-left">
                        <i class="fas fa-globe"></i>
                    </span>
                </div>
            </div>

            <div class="column">
                <div class="control has-icons-left">
                    <div class="select">
                        <select>
                            <option selected>15 m</option>
                            <option>30 m</option>
                            <option>1 h</option>
                            <option>2 h</option>
                            <option>3 h</option>
                            <option>4 h</option>
                            <option>5 h</option>
                            <option>6 h</option>
                            <option>12 h</option>
                            <option>1 D</option>
                            <option>3 D</option>
                            <option>1 W</option>
                            <option>2 W</option>/
                            <option>1 M</option>/
                        </select>
                    </div>
                    <span class="icon is-left">
                        <i class="fas fa-globe"></i>
                    </span>
                </div>
            </div>

            <div class="block"></div>

            <table class="table is-fullwidth card">
                <thead>
                    <tr>
                    <th><abbr title="Symbol">Symbol</abbr></th>
                    <th><abbr title="Min">Min</abbr></th>
                    <th><abbr title="Max">Max</abbr></th>
                    <th><abbr title="Start">Start</abbr></th>
                    <th><abbr title="End">End</abbr></th>
                    <th><abbr title="Started at">Started at</abbr></th>
                    <th><abbr title="Ended at">Ended at</abbr></th>
                    <th><abbr title="Started at">Delta</abbr></th>
                    <th><abbr title="Ended at">% Change</abbr></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $history)
                        <tr>
                            <td>{{$history['name']}}</td>
                            <td>{{number_format($history['min'], 2, ',', '.')}}</td>
                            <td>{{number_format($history['max'], 2, ',', '.')}}</td>
                            <td>{{number_format($history['open'], 2, ',', '.')}}</td>
                            <td>{{number_format($history['close'], 2, ',', '.')}}</td>
                            <td>{{$history['open_time']}}</td>
                            <td>{{$history['close_time']}}</td>
                            <td>{{number_format($history['directions'][0]['delta'], 2, ',', '.')}}</td>
                            <td>{{number_format($history['percent_change'], 2, ',', '.')}}</td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
        </div>
    </body>
</html>