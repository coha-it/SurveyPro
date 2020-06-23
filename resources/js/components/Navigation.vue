<template>
  <q-drawer show-if-above v-model="bLeft" side="left" bordered @input="callToggleNavigation">
    <!-- drawer content -->
    <q-scroll-area class="fit nav-flex">
      <div style="flex: auto; min-height: 100%;">
        <q-item class="pointer">
          <q-item-section top class="q-ml-none">
            <router-link :to="{path: '/'}">
              <img
                class="logo"
                src="https://dreamteam-survey.s3.eu-central-1.amazonaws.com/app/logo/dts-app-logo.svg"
              >
            </router-link>
          </q-item-section>
        </q-item>

        <template v-if="user">
          <q-list
            v-for="(cat, key) in sidenav"
            :key="key"
          >
            <template v-if="!(cat.hide_for_pan && user.is_panuser) && !(cat.hide_for_pan && !user.right)">
              <template v-if="cat.title">
                <q-separator spaced />
                <q-item-label header>Verwaltung</q-item-label>
              </template>

              <q-item v-for="item in cat.pages" :key="item.title" v-ripple color="red" exact :to="item.route" clickable>
                <q-item-section avatar>
                  <q-icon :name="item.icon" />
                </q-item-section>
                <q-item-section>
                  {{ $t( item.title) }}
                </q-item-section>
              </q-item>
            </template>
          </q-list>
        </template>
      </div>

      <!-- Bottom of Sidenav -->
      <div style="position: sticky; bottom: 0;" class="q-pa-sm bg-white">
        <!-- <q-footer class="bg-white"> -->
        <q-btn
          block
          outline
          depressed
          :label="$t('logout.btn')"
          color="grey"
          class="full-width"
          @click="tryLogout"
        />
        <!-- </q-footer> -->
      </div>
    </q-scroll-area>
  </q-drawer>
</template>

<script>
import { mapGetters } from 'vuex'

export default {

  data () {
    return {
      bLeft: false,
      title: window.config.appName,
      sidenav: [
        {
          pages: [
            { title: 'sidenav.home', icon: 'home', route: { name: 'home' } },
            { title: 'sidenav.surveys', icon: 'poll', route: { name: 'surveys' } },
            { title: 'sidenav.settings', icon: 'settings', route: { name: 'settings' } },
            { title: 'sidenav.faq', icon: 'help', route: { name: 'faq' } }
          ]
        },
        {
          title: 'sidenav.backend.title',
          hide_for_pan: true,
          pages: [
            { title: 'sidenav.backend.surveys', icon: 'analytics', route: { name: 'backend.surveys' }, sPermission: 'create_surveys' },
            { title: 'sidenav.backend.groups', icon: 'group_add', route: { name: 'backend.groups' }, sPermission: 'create_groups' },
            { title: 'sidenav.backend.pan', icon: 'person_add', route: { name: 'backend.users' }, sPermission: 'create_users' },
            { title: 'sidenav.backend.statistics', icon: 'pie_chart', route: { name: 'backend.statistics' } }
          ]
        }
      ],

      logoutDialog: false
    }
  },

  computed: mapGetters({
    user: 'auth/user'
  }),

  mounted () {
    //
  },

  // created: function() {
  //   this.$parent.$on('updateNavigation', this.toggleNavigation);
  // },

  methods: {
    tryLogout () {
      this.$q.dialog({
        title: this.$t('logout.title'),
        message: this.$t('logout.desc'),
        ok: {
          label: this.$t('logout.btn'),
          unelevated: true,
          color: 'negative'
        },
        cancel: {
          label: this.$t('logout.btn_stay_here'),
          unelevated: true
        },
        persistent: true
      }).onOk(() => {
        this.logout()
      }).onCancel(() => {
        //
      }).onDismiss(() => {
        //
      })
    },

    async logout () {
      // Log out the user.
      await this.$store.dispatch('auth/logout')

      // Redirect to login.
      this.$router.push({ name: 'welcome' })
    },

    callToggleNavigation: function () {
      this.$emit('navigationStateChanged', this.bLeft)
    },

    setNavigation (state) {
      this.bLeft = state
    },

    userIsAdmin () {
      return this.user && this.user.right && this.user.right.admin ? true : false
    },

    checkRights (item) {
      var perm = item.sPermission

      if (this.userIsAdmin() || typeof perm === 'undefined' || perm === null) {
        return true
      }

      if (perm && this.user && this.user.right && this.user.right[perm] != 1) {
        return false
      }

      return true
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

.nav-flex.q-scrollarea .scroll .absolute.full-width {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
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
.logo {
    width: 90%;
    margin: 7px 0 7px 0px;
}
</style>
