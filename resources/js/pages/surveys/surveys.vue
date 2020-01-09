<template>
  <div>
    <h1>Surveys</h1>
    <div class="q-pa-sm row q-gutter-sm">
          <q-card
            v-for="oSurvey in surveysAllowed"
            v-bind:key="oSurvey.id"
            class="my-card bg-grey-1"
            clickable
            flat
            bordered
          >
            <q-card-section>
              <div class="row items-center no-wrap">
                <div class="col">
                  <div class="text-h6">{{ oSurvey.title }}</div>
                  <div class="text-subtitle2">by John Doe</div>
                </div>

                <div class="col-auto">
                  <q-btn color="grey-7" round flat icon="more_vert">
                    <q-menu cover auto-close>
                      <q-list>
                        <q-item clickable>
                          <q-item-section>Remove Card</q-item-section>
                        </q-item>
                        <q-item clickable>
                          <q-item-section>Send Feedback</q-item-section>
                        </q-item>
                        <q-item clickable>
                          <q-item-section>Share</q-item-section>
                        </q-item>
                      </q-list>
                    </q-menu>
                  </q-btn>
                </div>
              </div>
            </q-card-section>

            <q-card-section>
              {{ lorem }}
            </q-card-section>
          </q-card>

    </div>
  </div>
</template>

<script>
// import axios from 'axios'
import { mapGetters } from 'vuex'

export default {
  data () {
    return {
      sSearch: ''
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      surveysAllowed: 'surveys/surveysAllowed'
    })
  },

  created: function () {
    this.$store.dispatch('surveys/fetchSurveysAllowed')
  }
}
</script>
