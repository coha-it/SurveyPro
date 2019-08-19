<template>
  <div>
    <!-- SideNavigation -->
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
              {{ $t('sidebar_subtitle') }}
            </v-list-item-subtitle>
          </v-list-item-content>
      </v-list-item>
      <v-divider></v-divider>
      <!-- SideNavigation Categories / Pages -->
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
                  <v-list-item-title style="white-space: pre;">{{ $t(item.title) }}</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
          </v-list>
        </div>
      </template>
      <!-- Bottom of Sidenav -->
     <template v-slot:append fixed bottom>
        <div class="pa-2">
          <v-btn block outlined depressed color="grey" @click.stop="logoutDialog = true">Logout</v-btn>
        </div>
      </template>
    </v-navigation-drawer>

    <!-- BottomBar -->
    <template>
      <v-bottom-navigation shift color="black accent-4" class="d-none d-md-flex d-sm-flex d-flex d-lg-none">
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

    <!-- Logout Dialog -->
    <v-dialog v-model="logoutDialog" max-width="290" dark content-class="naked dark centered">
        <h2 class="display-2">{{ $t('logout_title') }}</h2>
        <p>{{ $t('logout_desc') }}</p>
        <v-container fluid>
          <v-row align="center">
            <v-col class="text-center" cols="12" sm="12">
              <v-btn depressed @click="logoutDialog = false" outlined>{{ $t('logout_btn_stay_here') }}</v-btn>
              <v-btn depressed @click.prevent="logout" color="error">{{ $t('logout_btn_logout') }}</v-btn>
            </v-col>
          </v-row>
        </v-container>
    </v-dialog>

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
            { title: 'sidebar_surveys', icon: 'poll', route: {name: 'surveys'} },
            { title: 'sidenav_profile', icon: 'person', route: {name:'profile'} },
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

      logoutDialog: false,
      
    }
  },

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'login' })
    }
  }


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