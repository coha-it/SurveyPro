<template>
    <v-card :color="color" dark ripple :to="{ name: route }" class="auth-panel">
      <v-list-item three-line>

        <v-list-item-content class="align-self-center">
          <v-list-item-title class="headline mb-2" v-text="title" ></v-list-item-title>
          <v-list-item-subtitle v-text="text"></v-list-item-subtitle>
        </v-list-item-content>

        <v-list-item-icon class="align-self-center">
          <v-icon x-large v-text="icon"></v-icon>
        </v-list-item-icon>

      </v-list-item>
    </v-card>
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