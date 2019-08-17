<template>
  <div>
    <v-app>
      <Navigation />
      <v-content>
        <v-container fluid>
          <div class="main-layout">
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
  name: 'UserLayout',

  components: {
    Navigation
  }
}
</script>
