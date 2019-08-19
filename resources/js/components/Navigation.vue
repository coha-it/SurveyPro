<template>
  <div>
    <v-navigation-drawer app left absolute light resize v-model="drawer">
      <v-list-item>

          <v-list-item-avatar>
            <v-img :src="'/storage/corporate-happiness-gmbh.svg'"></v-img>
          </v-list-item-avatar>

          <v-list-item-content>
            <v-list-item-title class="title">
              {{ title }}
            </v-list-item-title>
            <v-list-item-subtitle>
              {{ user.pan ? '#' + user.pan : user.email }}
            </v-list-item-subtitle>
          </v-list-item-content>

      </v-list-item>

      <v-divider></v-divider>

      <template>
        <div v-for="(cat, key) in sidenav" v-bind:key="key">
          <v-subheader v-if="cat.title">{{ $t(cat.title) }}</v-subheader>
          <v-list>
              <v-list-item
                v-for="item in cat.pages"
                :key="item.title"
                link
                :to="item.route"
                color="accent"
              >
                <v-list-item-icon>
                  <v-icon>{{ item.icon }}</v-icon>
                </v-list-item-icon>

                <v-list-item-content>
                  <v-list-item-title>{{ $t(item.title) }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
          </v-list>
        </div>
      </template>

    </v-navigation-drawer>

    <!-- Bottom Bar -->
    <template>
      <v-bottom-navigation shift fixed bottom color="black accent-4" class="d-none d-md-flex d-sm-flex d-flex d-lg-none">
        <v-btn :input-value="drawer" @click.stop="drawer = !drawer">
          <span>{{ $t('bottombar_menu') }}</span>
          <v-icon>menu</v-icon>
        </v-btn>

        <v-btn v-for="item in bottomnav" :key="item.title" :to="item.route">
          <span>{{ $t(item.title) }}</span>
          <v-icon>{{ item.icon }}</v-icon>
        </v-btn>

      </v-bottom-navigation>
    </template>

  </div>
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
      sidenav: [
        {
          pages: [
            { title: 'sidenav_home', icon: 'home', route: {name:'home'} },
            { title: 'sidenav_profile', icon: 'person', route: {name:'profile'} },
            { title: 'sidebar_surveys', icon: 'poll', route: {name: 'surveys'} },
            { title: 'sidebar_faq', icon: 'help', route: {name: 'faq'} }
          ]
        },

        {
          title: 'sidenav_backend',
          pages: [
            { title: 'sidebar_backend_users', icon: 'person_add', route: {name: 'backend.users'} },
            { title: 'sidebar_backend_groups', icon: 'group_add', route: {name: 'backend.groups'} },
            { title: 'sidebar_backend_surveys', icon: 'forum', route: {name: 'backend.surveys'} },
            { title: 'sidebar_backend_statistics', icon: 'pie_chart', route: {name: 'backend.statistics'} }
          ]
        }
      ],

      drawer: null,
      bottomnav: [
        { title: 'bottombar_profile', icon: 'person', route: {name: 'profile'} },
        { title: 'bottombar_home', icon: 'home', route: {name: 'home'} },
        { title: 'bottombar_surveys', icon: 'poll', route: {name: 'surveys'} },
        { title: 'bottombar_faq', icon: 'help', route: {name: 'faq'} },
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

<style lang="scss" scoped>
.v-item-group.v-bottom-navigation {

  .v-btn {
      min-width: 60px;
      width: 20%;
      max-width: 125px;
      letter-spacing: inherit;
  }
}
</style>