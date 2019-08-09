<template>
  <div>
    <div>
        <v-container grid-list-md fluid style="padding: 0; height: 100vh;">
            <v-layout wrap class="v-application" fill-height>
                <v-flex md6 xs12 class="hidden-sm-and-down">
                    <div class="coha--banner-wrapper">
                        <div class="image" style="background-image: url(/storage/analysis-analytics-analyze-590022-test2.jpg)"></div>
                        <div class="text"></div>
                    </div>
                </v-flex>
                <v-flex md6 xs12>
                  <child />
                  <Footer />
                </v-flex>
            </v-layout>
        </v-container>
    </div>
  </div>
</template>

<script>

import Footer from "~/components/FooterGuest";

export default {

  name: 'RightSidedLayout',

  components: {
    Footer
  }
}
</script>

<style lang="scss" scoped>
.container.grid-list-md .layout .flex {
  position: relative;
    max-height: 100vh;
    overflow: scroll;
    padding: 0;
}

.coha--banner-wrapper {
    height: 100%;
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;

    .image {
        background-image: url('/storage/student-849825-1.jpg');
        position: absolute;
        left: 0;
        right: 0;
        margin: auto;
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
</style>
