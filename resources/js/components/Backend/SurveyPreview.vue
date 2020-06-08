<template lang="pug">
span
  q-btn(label='Vorschau', icon='preview', color='primary', outline, @click='showDialog', unelevated)
  q-dialog(v-model='bDialog', persistent, :maximized='max', transition-show='slide-up', transition-hide='slide-down')
    q-card
      q-bar
        q-space
        q-btn(dense='', flat='', icon='minimize', @click='max = false', :disable='!max')
          q-tooltip(v-if='max', content-class='bg-white text-primary') Minimize
        q-btn(dense='', flat='', icon='crop_square', @click='max = true', :disable='max')
          q-tooltip(v-if='!max', content-class='bg-white text-primary') Maximize
        q-btn(dense='', flat='', icon='close', v-close-popup, @click="closeDialog")
          q-tooltip(content-class='bg-white text-primary') Close
      q-card-section
        .text-h6 Vorschaumodus
      q-card-section.q-pt-none
        Preview(:o-survey='oSurvey', :b-preview='true', :key="key")
</template>
<script>
import Preview from '~/components/SurveyDetails'

export default {
  components: {
    Preview
  },

  props: {
    oSurvey: {
      type: Object,
      required: true
    }
  },

  data () {
    return {
      max: true,
      bDialog: false,
      key: 1
    }
  },

  mounted () {
    window.addEventListener('mouseup', this.refresh)
  },

  methods: {
    showDialog () {
      this.removeHash()
      this.bDialog = true
    },
    closeDialog () {
      this.bDialog = false
      this.removeHash()
    },
    removeHash () {
      window.location.hash = ''
      // if (window.history.pushState) {
      //   window.history.pushState('', '/', window.location.pathname)
      // } else {
      //   window.location.hash = ''
      // }
    },
    refresh () {
      setTimeout(() => {
        this.key += 1
      }, 1)
    },
    copyObject (obj) {
      if (typeof obj !== 'undefined') {
        var copy = JSON.parse(JSON.stringify(obj))
        if (copy) return copy
      }
    }
  }
}
</script>
