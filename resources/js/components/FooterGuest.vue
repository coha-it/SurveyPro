<template>
    <v-footer>
      <v-layout wrap align-center>
        <v-flex xs12 sm6>
          <LocaleSwitcher />
        </v-flex>
        <v-flex xs12 sm6 text-right>
          <span>Corporate Happiness &copy; {{ new Date().getFullYear() }}</span>
        </v-flex>
      </v-layout>
    </v-footer>
</template>

<script>
import LocaleSwitcher from "~/components/LocaleSwitcher";
export default {
  name: 'FooterGuest',
  components: {
    LocaleSwitcher
  },
  props: [

  ],
  created: function() {
  }
}
</script>
