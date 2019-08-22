<template>
  <router-view />
</template>

<script>
export default {
  middleware: ['auth', 'isEmailUser'],
}
</script>
