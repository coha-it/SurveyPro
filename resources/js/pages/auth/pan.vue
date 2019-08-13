<template>
    <div class="rightsided-content coha--pan-wrapper">
        <Back />
    </div>
</template>

<script>
import Back from '~/components/AuthBack.vue'

export default {
  middleware: 'guest',
  layout: 'rightsided',
  components: {
      Back
  }
}
</script>

<style lang="scss" scoped>
.coha--pan-wrapper {
    background-color: #E8D03E;
    a {
        color: #000;
    }
}
</style>