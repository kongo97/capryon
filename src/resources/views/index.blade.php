<!-- PAGE CONTAINER -->
<div class="row">
    <div class="col-md-12">
    <h1 id="balance">@{{balance}} $</h1>
    </div>
</div>

<!-- VUE -->
@section("vue")
<style>
  #balance {
    font-size: 10em;
    text-align: center;
  }
</style>

<script>
  var app = new Vue({
    el: '#app',
    vuetify: new Vuetify(),
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