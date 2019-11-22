<template>
    <div>
      <q-btn rounded icon="mdi-chevron-left" :to="to" flat color="blue-grey" style="color: #FF0080; padding-left: 0;" :label="$t('back')" />
    </div>
</template>

<style lang="scss" scoped>
.v-btn {margin-left: -10px;}
</style>

<script>
export default {
    props: [
        'to'
    ]
}
</script>
