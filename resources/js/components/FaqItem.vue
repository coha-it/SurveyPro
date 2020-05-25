<template>
  <q-expansion-item
    expand-separator
    :disable="disable"
    :icon="item.icon"
    :label="item.title"
    :caption="'Frage'+ ': ' +item.headline + 'Antwort' + ': '+ item.subtitle"
    :header-style="{ backgroundColor: '#ffffff' }"
  >
    <q-separator />
    <q-card>
      <q-card-section>
        {{ item.text }}
      </q-card-section>
    </q-card>
  </q-expansion-item>
</template>

<script>
export default {
  props: {
    'item': {
      type: Object,
      required: true
    },
    'disable': {
      type: Boolean,
      default: false
    }
  }
}
</script>
