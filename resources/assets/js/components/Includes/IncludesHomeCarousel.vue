<template>
  <div>
    <el-carousel :interval="2000" height="300px" arrow="never">
      <el-carousel-item v-for="(item, index) in items" :key="index">
        
          <img :src="item.imgLnk" class="ui centered image" style="border-radius: 7px;" />
        
      </el-carousel-item>
    </el-carousel>
  </div>
</template>
<script>
  import { Carousel, CarouselItem } from 'element-ui';
  export default {
    name: 'includes-home-carousel',
    components: {
      'el-carousel': Carousel,
      'el-carousel-item': CarouselItem,
    },
    data: () => ({
      items: [
        { icon: 'ui balance scale massive icon', imgLnk: '/images/home-1.jpg' },
        { icon: 'ui bullhorn scale massive icon', imgLnk: '/images/home-2.jpg' },
        { icon: 'ui certificate scale massive icon', imgLnk: '/images/home-3.jpg' },
      ]
    }),
    methods: {

    },
    created() {

    }
  }
</script>
<style>

</style>