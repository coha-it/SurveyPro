<template>
  <card :title="$t('home')">
    {{ $t('you_are_logged_in') }}
    <a href="#" class="dropdown-item pl-3" @click.prevent="logout">
      <fa icon="sign-out-alt" fixed-width />
      {{ $t('logout') }}
    </a>
  </card>
</template>

<script>
export default {
  middleware: 'auth',
  layout: 'user',

  metaInfo () {
    return { title: this.$t('home') }
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
