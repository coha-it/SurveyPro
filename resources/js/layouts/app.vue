<template>
  <div>
    <v-app>
      <Navigation />
      <v-content>
        <v-container fluid>
          <div class="main-layout coha-app--view">
            <child />
          </div>
        </v-container>
      </v-content>
    </v-app>
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
    max-width: 1080px;
  }
}
</style>