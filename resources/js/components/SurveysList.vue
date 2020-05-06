<template>
  <div>
    <div style="max-width: 400px">
      <div class="row q-gutter-md">
        <q-card
          v-for="oSurvey in aSurveys"
          :id="'survey-'+oSurvey.id"
          v-bind:key="oSurvey.id"
          class="col col-12 col-xs-12 col-xl-12"
          clickable
          flat
          bordered
          ripple
          :disabled="!oSurvey.is_fillable"
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
            <router-link :to="'survey/'+oSurvey.id + '#overview'">
              <div class="text-h6 text-black">{{ oSurvey.title || 'Umfrage #'+oSurvey.id }}</div>
            </router-link>

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
import VueMoment from 'vue-moment'
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
    fhd: function (d) {
      return VueMoment(d).format('DD.MM.YYYY')
    },
    fht: function (d) {
      return VueMoment(d).format('hh:mm')
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
