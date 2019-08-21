<template>
  <div class="welcome-layout">
    <div class="top-right links">
      <template v-if="authenticated">
        <router-link :to="{ name: 'home' }">
          {{ $t('home') }}
        </router-link>
      </template>
      <template v-else>
        <router-link :to="{ name: 'auth' }">
          {{ $t('entry') }}
        </router-link>
      </template>
    </div>

    <div class="text-center">
      <div class="title">
        {{ title }}
      </div>

      <div class="links">
        <a>Mitarbeiter-Befragungen Online</a>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  layout: 'naked',

  middleware: 'guest',

  metaInfo () {
    return { title: this.$t('home') }
  },

  data: () => ({
    title: window.config.appName
  }),

  computed: mapGetters({
    authenticated: 'auth/check'
  })
}
</script>

<style lang="scss" scoped>

.welcome-layout {
  color: #636b6f;
  height: 100vh;
  font-weight: 100;
  position: relative;

  .links > a {
    color: #636b6f;
    padding: 0 25px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .1rem;
    text-decoration: none;
    text-transform: uppercase;
  }
}

.top-right {
  position: absolute;
  right: 10px;
  top: 18px;
}

.title {
  font-size: 85px;
}

.text-center {
    margin: auto;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    text-align: center;
    position: absolute;
    display: inline-flex;
    width: auto;
    align-items: center;
    justify-content: center;
    flex-flow: column;
    pointer-events: none;
}

</style>
