<template>
  <div class="coha-app--view">
    <q-layout view="hHh LpR lFf">

      <Navigation ref="childRef" @navigationStateChanged="navigationStateChanged"  />

      <q-page-container>
        <q-page padding>
          <div class="container" fluid>
            <div class="main-layout">
              <child />
            </div>
          </div>
        </q-page>
      </q-page-container>

      <NavigationBottomBar @toggle-navigation="toggleNavigation" />

    </q-layout>
  </div>
</template>

<script>
import Navigation from '~/components/Navigation'
import NavigationBottomBar from '~/components/NavigationBottomBar'

export default {
  middlware: 'auth',
  name: 'AppLayout',

  components: {
    Navigation,
    NavigationBottomBar
  },

  data () {
    return {
      left: false
    }
  },

  methods: {
    toggleNavigation(state) {
      this.left = state;
      this.callChild();
    },

    callChild() {
      this.$refs.childRef.setNavigation(this.left);
    },

    navigationStateChanged(state) {
      this.left = state;
    }
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
