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
      this.read_balance = this.getBalance();
    },
  })
</script>
@endsection