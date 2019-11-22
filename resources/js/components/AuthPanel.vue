<template>
  <router-link :to="{ name: route }" tag="div" style="cursor: pointer;">
    <q-card color="primary" dark v-ripple :class="'auth-panel bg-'+ color">
      <q-card-section>
        <div class="row items-center no-wrap">
          <div class="col">
            <div class="text-h6 headline">{{ title }}</div>
            <div class="text-subtitle2 subtitle">{{ text }}</div>
          </div>

          <div class="col-auto">
            <q-icon x-large :name="icon" class="icon"></q-icon>
          </div>
        </div>
      </q-card-section>
    </q-card>
  </router-link>
</template>

<script>
export default {
    props: [
        'title',
        'text',
        'color',
        'route',
        'icon'
    ]
}
</script>
