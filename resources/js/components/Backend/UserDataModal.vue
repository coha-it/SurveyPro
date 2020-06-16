/* eslint-disable vue/order-in-components */
<template>
  <span>
    <q-btn
      :label="sEditText"
      color="primary"
      unelevated
      tile
      class="mt-2 mr-2"
      :outline="bOutline"
      :size="sSize"
      :icon-right="sIcon"
      @click="editDialog = true"
    />
    <q-dialog
      v-model="editDialog"
      max-width="900px"
      max-height="1000px"
      min-height="500px"
      height="90%"
      persistent
      scrollable
      transition="dialog-bottom-transition"
      :maximized="maximizedToggle"
    >
      <q-card>
        <q-toolbar class="bg-primary text-white" dark color="primary">
          <q-toolbar-title>{{ sEditText }}</q-toolbar-title>

          <q-space />

          <q-btn dense flat icon="minimize" :disable="!maximizedToggle" @click="maximizedToggle = false">
            <q-tooltip v-if="maximizedToggle" content-class="bg-white text-primary">Minimieren</q-tooltip>
          </q-btn>
          <q-btn dense flat icon="crop_square" :disable="maximizedToggle" @click="maximizedToggle = true">
            <q-tooltip v-if="!maximizedToggle" content-class="bg-white text-primary">Maximieren</q-tooltip>
          </q-btn>
          <q-btn v-close-popup dense flat icon="close">
            <q-tooltip content-class="bg-white text-primary">Schließen</q-tooltip>
          </q-btn>

        </q-toolbar>
        <q-card-section>
          <br>
          <UserDataModalContent
            :s-edit-text="sEditText"
            :s-create-text="sCreateText"
            :s-input-label="sInputLabel"
            :s-input-label2="sInputLabel2"
            :s-input-label3="sInputLabel3"
            :s-parent-model="sParentModel"
            :a-parent-models="aParentModels"
            :a-parent-headers="aParentHeaders"
          />
        </q-card-section>
      </q-card>
    </q-dialog>
  </span>
</template>

<script>
import UserDataModalContent from '~/components/Backend/UserDataModalContent'

export default {

  components: {
    UserDataModalContent
  },

  props: {

    sSize: {
      type: String,
      default: 'md'
    },

    bOutline: {
      type: Boolean,
      default: true
    },

    sIcon: {
      type: String,
      default: null
    },

    sParentModel: {
      type: String,
      default: null
    },
    aParentModels: {
      type: Array,
      default: null
    },
    aParentHeaders: {
      type: Array,
      default: null
    },

    sCreateText: {
      type: String,
      default: null
    },
    sEditText: {
      type: String,
      default: null
    },

    sInputLabel: {
      type: String,
      default: null
    },
    sInputLabel2: {
      type: String,
      default: null
    },
    sInputLabel3: {
      type: String,
      default: null
    }
  },

  data () {
    return {
      maximizedToggle: false,
      editDialog: false
    }
  }
}
</script>

<style>
</style>
