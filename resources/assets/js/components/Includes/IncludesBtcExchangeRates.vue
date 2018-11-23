<template>
  <div>
    <div class="ui raised inverted segment">
      <div class="ui grid container">
        <div class="row">
          <div class="eight wide column">
            <h3>Exchange Rates</h3>
          </div>
          <div class="eight wide right aligned column">
            <button class="ui mini inverted icon button" @click.prevent="getBtcExchangeRates"><i class="redo icon"></i> Refresh</button>
          </div>
        </div>
      </div>
      
      <table class="ui striped small table tableBodyScroll">
        <thead>
          <tr>
            <th>Currency</th>
            <th>Last</th>
            <th>Buy</th>
            <th>Sell</th>
            <th>Symbol</th>
          </tr>
        </thead>
        
        <tbody>
          <tr v-for="(rate, index) in rates" :key="index">
            <td>{{ index }}</td>
            <td>{{ rate.last }}</td>
            <td>{{ rate.buy }}</td>
            <td>{{ rate.sell }}</td>
            <td>{{ rate.symbol }}</td>
          </tr>
        </tbody>
      
      </table>
    </div>
  </div>
</template>
<script>

  export default {
    name: 'includes-btc-exchange-rates',
    data: () => ({
      rates: []
    }),
    created() {
      this.getBtcExchangeRates();
    },
    methods: {
      getBtcExchangeRates() {
        axios.get('/api/blockchain/btc-exchange-rates').then((resp) => {
          console.log(resp);
          this.rates = resp.data;
        }).catch((err) => {
          console.log(err);
        })
      }
    },
  }
</script>
<style scoped>
.tableBodyScroll tbody {
display:block;
max-height:300px;
overflow-y:scroll;
}
.tableBodyScroll thead, tbody tr {
display:table;
width:100%;
table-layout:fixed;
}
</style>
