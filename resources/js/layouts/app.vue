<template>
  <div class="coha-app--view">
      <Navigation />
      <v-content>
        <v-container fluid>
          <div class="main-layout">
            <child />
          </div>
        </v-container>
      </v-content>
  </div>
</template>

<script>
import Navigation from '~/components/Navigation'

export default {
  middlware: 'auth',
  name: 'AppLayout',

  components: {
    Navigation
  }
}
</script>

<style lang="scss" scoped>
.container {
  padding: 25px;

  .main-layout {
    max-width: 1580px;
  }
}
</style>