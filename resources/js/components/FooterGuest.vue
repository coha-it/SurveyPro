<template>
  <div class="footer">
    <div class="fit row wrap justify-between items-center content-center">
        <div class="q-pa-md">
          <LocaleSwitcher />
        </div>
        <div class="q-pa-md text-right">
          <span>Corporate Happiness &copy; {{ new Date().getFullYear() }}</span>
        </div>
    </div>
  </div>
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
