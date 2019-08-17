<template>
  <v-app>
    <v-content>
        <v-container fluid style="padding: 0; height: 100vh;">
            <v-layout wrap class="v-application" fill-height>
              <v-flex md6 xs12 class="hidden-sm-and-down coha--banner-wrapper">
                  <div class="coha--banner">
                      <div class="image" v-bind:style="{ backgroundImage: backgroundImage }"></div>
                      <div class="text"></div>
                  </div>
              </v-flex>
              <v-flex md6 xs12 style="display: grid; grid-template-rows: 1fr auto; grid-template-columns: 100%;">
                <child />
                <Footer />
              </v-flex>
            </v-layout>
        </v-container>
    </v-content>
  </v-app>
</template>

<script>

import Footer from "~/components/FooterGuest";

export default {

  name: 'RightSidedLayout',

  components: {
    Footer
  },

  data() {
    return {
        backgroundImage: 'url(/storage/student-2.jpg)'
    }
  },

  methods: {
  }

}
</script>

<style lang="scss" scoped>
.container.grid-list-md .layout .flex {
  padding: 0;
}

.coha--banner-wrapper {
  position: relative;
  max-height: 100vh;
  overflow: scroll;

  .coha--banner {
      overflow: hidden;
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;

      position: fixed;
      left: 0;
      top: 0;
      height: 100%;
      width: 50vw;

      .image {
          // background-image: url('/storage/student-849825-1.jpg');
          position: absolute;
          left: 0;
          right: 0;
          margin: 0;
          top: 0;
          bottom: 0;
          background-position: 60% 70%;
          width: 100%;
          min-height: 31vw;
          height: 100vh;
          background-size: auto 110%;
          z-index: -1;
      }

      .text {
          text-align: center;
          color: #fff;
          font-size: 3rem;
      }
  }
}

.v-footer {
  transition: 0ms;
}
</style>
