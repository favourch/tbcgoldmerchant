<template>
  <div>
    <el-carousel :interval="4000" type="card" :height="height">
      <el-carousel-item v-for="(item, index) in items" :key="index">
        
          <img :src="item.imgLnk" class="ui centered large rounded bordered image" />
          <!-- <i :class="item.icon"></i> -->
       
      </el-carousel-item>
    </el-carousel>
  </div>
</template>
<script>
  import { Carousel, CarouselItem } from 'element-ui';
  export default {
    name: 'includes-carousel',
    components: {
      'el-carousel': Carousel,
      'el-carousel-item': CarouselItem,
    },
    props: {
      height: {
        type: String,
        required: true
      }
    },
    data: () => ({
      items: [
        { icon: 'ui balance scale massive icon', imgLnk: '/images/grocery3.png' },
        { icon: 'ui bullhorn scale massive icon', imgLnk: '/images/house-gift1.png' },
        { icon: 'ui certificate scale massive icon', imgLnk: '/images/car2.jpg' },
        { icon: 'ui chart line scale massive icon', imgLnk: '/images/free-gas2.png' },
      ]
    }),
    methods: {

    },
    created() {

    }
  }
</script>
<style scoped>
  .el-carousel__item h3 {
    color: #475669;
    font-size: 14px;
    opacity: 0.75;
    line-height: 200px;
    margin: 0;
  }

  .el-carousel__item:nth-child(2n) {
   
  }

  .el-carousel__item:nth-child(2n+1) {
    
  }
</style>