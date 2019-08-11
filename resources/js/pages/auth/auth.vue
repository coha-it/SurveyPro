<template>
    <div style="padding: 25px;">
        <h1>Willkommen</h1>
        <p style="max-width: 400px;">Um sich in ihr SurveyPro-Konto anzumelden, klicken Sie auf eine der drei unterstehenden Optionen</p>

        <router-link :to="{ name: 'login' }">
          {{ $t('login') }}
        </router-link>

        <div style="max-width: 500px;">
          <v-card color="#E8D03E" dark ripple :to="{ name: 'login' }">
              <v-list-item three-line>

                <v-list-item-content class="align-self-center">
                  <v-list-item-title class="headline mb-2" v-text="'PAN & PIN'" ></v-list-item-title>
                  <v-list-item-subtitle v-text="'jo'"></v-list-item-subtitle>
                </v-list-item-content>

                <!-- <v-list-item-avatar size="125" tile>
                  <v-img src="'item.src'"></v-img>
                </v-list-item-avatar> -->

                <v-list-item-icon class="align-self-center">
                  <v-icon x-large v-text="'mdi-dialpad'"></v-icon>
                </v-list-item-icon>

              </v-list-item>
            </v-card>
        </div>

    </div>
</template>

<script>
export default {
  
  middleware: 'guest',

  layout: 'rightsided',

  metaInfo () {
    return { title: this.$t('authentificate') }
  },

}
</script>

<style lang="scss">

</style>
