<template>
    <v-footer absolute>
      <v-layout wrap align-center>
        <v-flex xs12 sm6>
          <Languages />
        </v-flex>
        <v-flex xs12 sm6 text-right>
          <span>Corporate Happiness &copy; {{ new Date().getFullYear() }}</span>
        </v-flex>
      </v-layout>
    </v-footer>
</template>

<script>
import Languages from "~/components/Languages";
export default {
  name: 'FooterGuest',
  components: {
    Languages
  },
}
</script>