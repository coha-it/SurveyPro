<template>
    <v-navigation-drawer app permanent left absolute light>
      <v-list-item>

          <v-list-item-avatar>
            <v-img :src="'storage/corporate-happiness-gmbh.svg'"></v-img>
          </v-list-item-avatar>

        <v-list-item-content>
          <v-list-item-title class="title">
            {{ user.pan ? '#' + user.pan : user.email }}
          </v-list-item-title>
          <v-list-item-subtitle>
            subtext
          </v-list-item-subtitle>
        </v-list-item-content>
      </v-list-item>

      <v-divider></v-divider>

      <v-list>
        <v-list-item
          v-for="item in items"
          :key="item.title"
          link
          :to="item.route"
          color="accent"
        >
          <v-list-item-icon>
            <v-icon>{{ item.icon }}</v-icon>
          </v-list-item-icon>

          <v-list-item-content>
            <v-list-item-title>{{ item.title }}</v-list-item-title>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-subheader>Verwaltung</v-subheader>

    </v-navigation-drawer>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {

computed: mapGetters({
    user: 'auth/user'
  }),

  data () {
    return {
      title: window.config.appName,
      items: [
        { title: 'Home', icon: 'mdi-view-dashboard', route: {name:'home'} },
        { title: 'User', icon: 'mdi-image', route: {name:'user'} },
      ],
      right: null,
    }
  },


}
</script>

<style lang="scss">
.v-avatar {
  .v-image__image.v-image__image--cover {
    background-size: 75%;
  }
}
</style>