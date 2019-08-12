<template>
    <v-footer absolute>
      <v-spacer></v-spacer>
      <div>
        <Languages />
        Corporate Happiness &copy; {{ new Date().getFullYear() }}
      </div>
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