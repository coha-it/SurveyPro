<template>
    <q-drawer show-if-above v-model="bLeft" side="left" bordered @input="callToggleNavigation">
      <!-- drawer content -->
      <q-scroll-area class="fit nav-flex">
            <div style="flex: auto">
              <q-item>
                <q-item-section top thumbnail class="q-ml-none">
                    <img :src="'/storage/corporate-happiness-gmbh.svg'" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    {{ title }}
                  </q-item-label>
                  <q-item-label caption>
                    {{ $t('sidenav.subtitle') }}
                  </q-item-label>
                </q-item-section>

                <q-item-section side top>
                  <q-item-label caption>

                  </q-item-label>
                </q-item-section>
              </q-item>

              <br>

              <q-list v-for="(cat, key) in sidenav" v-bind:key="key">

                <template v-if="cat.title">
                  <q-separator spaced />
                  <q-item-label header>List Header</q-item-label>
                </template>

                <q-item color="red" exact :to="item.route" v-for="item in cat.pages" :key="item.title" clickable v-ripple>
                  <q-item-section avatar>
                    <q-icon :name="item.icon" />
                  </q-item-section>
                  <q-item-section>
                    {{ $t( item.title) }}
                  </q-item-section>
                </q-item>

              </q-list>
            </div>

            <!-- Bottom of Sidenav -->
            <div style="position: sticky; bottom: 0;" class="q-pa-sm bg-white">
            <!-- <q-footer class="bg-white"> -->
              <q-btn block outline depressed :label="$t('logout.btn')" color="grey" @click="logoutDialog = true" class="full-width" />
            <!-- </q-footer> -->
            </div>
      </q-scroll-area>
      <q-dialog v-model="logoutDialog">
          <q-card>
            <q-card-section class="row items-center">
              <q-avatar icon="logout" color="primary" text-color="white" />
              <span class="q-ml-sm">
                <p>{{ $t('logout.title') }}<br>{{ $t('logout.desc') }}</p>
              </span>
            </q-card-section>

            <q-card-actions align="right">
              <q-btn outline :label="$t('logout.btn_stay_here')" @click="logoutDialog = false" color="primary" v-close-popup />
              <q-btn outline :label="$t('logout.btn')" @click="logout" color="warning" v-close-popup />
            </q-card-actions>
          </q-card>
      </q-dialog>
    </q-drawer>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {

  computed: mapGetters({
    user: 'auth/user'
  }),

  mounted() {
  },

  // created: function() {
  //   this.$parent.$on('updateNavigation', this.toggleNavigation);
  // },

  data () {
    return {
      bLeft: false,
      title: window.config.appName,
      sidenav: [
        {
          pages: [
            { title: 'sidenav.home', icon: 'home', route: {name:'home'} },
            { title: 'sidenav.surveys', icon: 'poll', route: {name: 'surveys'} },
            { title: 'sidenav.profile', icon: 'person', route: {name:'profile'} },
            { title: 'sidenav.faq', icon: 'help', route: {name: 'faq'} }
          ]
        },
        {
          title: 'sidenav.backend.title',
          hide_for_pan: true,
          pages: [
            { title: 'sidenav.backend.groups', icon: 'group_add', route: {name: 'backend.groups'},            sPermission: 'create_groups',                },
            { title: 'sidenav.backend.pan', icon: 'person_add', route: {name: 'backend.users'},             sPermission: 'create_users',               },
            { title: 'sidenav.backend.surveys', icon: 'forum', route: {name: 'backend.surveys'},              sPermission: 'create_surveys',              },
            { title: 'sidenav.backend.statistics', icon: 'pie_chart', route: {name: 'backend.statistics'},}
          ]
        }
      ],

      logoutDialog: false,
    }
  },

  methods: {
    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'welcome' })
    },

    callToggleNavigation: function() {
      this.$emit('navigationStateChanged', this.bLeft);
    },

    setNavigation(state) {
      this.bLeft = state;
    },

    userIsAdmin() {
      return this.user && this.user.right && this.user.right.admin ? true : false;
    },

    checkRights (item) {
      var perm = item.sPermission;

      if(this.userIsAdmin() || typeof perm === "undefined" || perm === null) {
        return true;
      }

      if(perm && this.user && this.user.right && this.user.right[perm] != 1) {
        return false;
      }

      return true;
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
