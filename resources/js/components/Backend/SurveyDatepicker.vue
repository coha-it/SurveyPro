<template lang="pug">
  div.q-pb-md
    q-input(:value='sValue', :disable='disable', :label='sLabel', placeholder='Bitte auswählen', readonly, disabled, color='teal', standout)
      template(v-slot:prepend)
        q-icon(:name='sIcon')
      q-popup-proxy(ref='qDateProxy', transition-show='scale', transition-hide='scale')
        q-time(v-if="time", v-model='oSurvey[sModel]', :color='sColor', :header-color='sHeaderColor', :events='events', :disable='disable', required, :options='options', :mask='mask', format24h)
          Inner(:o-survey='oSurvey', :s-model='sModel')
        q-date(v-else, v-model='oSurvey[sModel]', :color='sColor', :range="bRange" :header-color='sHeaderColor', :events='events', :event-color='colors', :disable='disable', required, :options='options', :mask='mask', :selected-items-text="getDatesDiffDays() + ' Tage Zeit'")
          Inner(:o-survey='oSurvey', :s-model='sModel')
</template>

<script>
import Inner from '~/components/Backend/SurveyDatepickerInner'

export default {

  components: {
    Inner
  },

  props: {
    sModel: {
      type: String,
      required: true
    },
    sLabel: {
      type: String,
      default: 'Not Set'
    },
    sColor: {
      type: String,
      required: true
    },
    sHeaderColor: {
      type: String,
      required: true
    },
    sIcon: {
      type: String,
      default: 'event'
    },
    events: {
      type: Function,
      default: () => {
        return []
      }
    },
    colors: {
      type: Function,
      default: () => {
        return []
      }
    },
    getDatesDiffDays: {
      type: Function,
      default: () => {
        return []
      }
    },
    bRange: {
      type: Boolean,
      default: false
    },

    // Defaults
    time: {
      type: Boolean,
      default: false
    },
    oSurvey: {
      type: Object,
      required: true
    },
    sValue: {
      type: String,
      default: 'Bitte ausfüllen'
    },
    disable: {
      type: Boolean,
      default: true
    },
    options: {
      type: Function,
      required: true
    },
    mask: {
      type: String,
      required: true
    }
  }
}
</script>
