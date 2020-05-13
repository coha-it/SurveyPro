<template>
  <div>
    <div style="max-width: 400px">
      <div class="row q-gutter-md">
        <q-card
          v-for="oSurvey in aSurveys"
          :id="'survey-'+oSurvey.id"
          :key="oSurvey.id"
          :class="'col col-12 col-xs-12 col-xl-12 ' + (isEnabled(oSurvey) ? 'pointer' : null)"
          clickable
          flat
          bordered
          ripple
          :disabled="isDisabled(oSurvey)"
          @click="isEnabled(oSurvey) ? link('survey/'+oSurvey.id + '#overview') : null"
        >
          <q-card-section>
            <div class="text-subtitle2">
              <template v-if="oSurvey.author">
                Von {{ oSurvey.author }}
              </template>
              <q-badge
                outline
                :color="getBadgeColor(oSurvey)"
                :label="getBadgeLabel(oSurvey)"
                style="float:right"
              />
            </div>
            <div class="text-h6 text-black">{{ oSurvey.title || 'Umfrage #'+oSurvey.id }}</div>

            <div class="row justify-center full-height full-width text-center  items-center">
              <div class="col col-4">
                <q-linear-progress rounded size="8px" :value="0" color="green" />
              </div>
              <div class="col col-8 text-left q-pl-sm">
                <small class="text-caption">
                  {{ '0 von '+ oSurvey.question_count +' Fragen beantwortet' }}
                </small>
              </div>
            </div>
          </q-card-section>

          <q-card-section>
            <div>{{ oSurvey.desc_short }}</div>
            <div class="text-right text-grey text-caption">
              Startet am {{ fhd(oSurvey.start_datetime) }} um {{ fht(oSurvey.start_datetime) }}<br>
              Endet um {{ fhd(oSurvey.end_datetime) }} um {{ fht(oSurvey.end_datetime) }}<br>
            </div>
          </q-card-section>
        </q-card>
      </div>
    </div>
  </div>
</template>

<script>
// import axios from 'axios'
import { mapGetters } from 'vuex'
import dayjs from 'dayjs'
import _ from 'lodash'

export default {
  data () {
    return {
      sSearch: '',
      aSurveys: []
    }
  },
  computed: {
    ...mapGetters({
      user: 'auth/user',
      surveysMembering: 'surveys/surveysMembering'
    })
  },
  watch: {
    surveysMembering: function (promise) {
      // Order
      promise = _.orderBy(promise, [
        'is_fillable',    // 1 .. 0
        'is_finished',    // 0 .. 1
        'has_ended',      // 0 .. 1
        'has_started',    // 1 .. 0
        'start_datetime', // 1 .. 0
      ], [
        'desc',           // 1 .. 0
        'asc',            // 0 .. 1
        'asc',            // 0 .. 1
        'desc',           // 1 .. 0
        'desc',           // 1 .. 0
      ]);

      // Set
      this.aSurveys = promise
    }
  },
  created: function () {
    this.$store.dispatch('surveys/fetchSurveysMembering')
  },
  methods: {
    link (url) {
      this.$router.push(url)
    },
    isDisabled (survey) {
      return !this.isEnabled(survey)
    },
    isEnabled (survey) {
      return survey.is_fillable
    },
    fhd (d) {
      return dayjs(d).format('DD.MM.YYYY')
    },
    fht (d) {
      return dayjs(d).format('hh:mm')
    },
    getBadgeColor (oSurvey) {
      if (oSurvey.is_fillable) return 'green'
      if (oSurvey.is_expired) return 'red'
      return 'primary'
    },
    getBadgeLabel (oSurvey) {
      if (oSurvey.is_fillable) return 'Jetzt Verfügbar'
      if (oSurvey.is_expired) return 'Abgelaufen'
      return 'Bald verfügbar'
    }
  }
}
</script>
