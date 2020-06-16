<template>
  <div>
    <h1>{{ $t('Groups') }}</h1>
    <UserGroups />
  </div>
</template>

<script>
import UserGroups from '~/components/Backend/UserGroups'

export default {
  middleware: 'canCreateGroups',

  components: {
    UserGroups
  }
}
</script>
