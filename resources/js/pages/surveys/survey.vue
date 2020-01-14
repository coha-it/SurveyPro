<template>
  <div>
    <!-- Overview -->
    <div v-if="overviewIsViewed()">
      Overview<br>

      Question List

      <ul v-if="oSurvey">
        <li v-for="question in oSurvey.questions" v-bind:key="question.id">
          <router-link :to="getQuestionHash(question)">
            {{ question.title }}
          </router-link>
        </li>
      </ul>
    </div>

    <!-- Questions -->
    <div v-else-if="oSurvey">
      <!-- Progress -->
      <div class="survey-progress-wrapper">
        <span
          v-for="question in oSurvey.questions"
          v-bind:key="question.id"
          :class="getProgressClasses(oSurvey, question)"
        ></span>
      </div>

      <q-btn label="overview" :to="hashes.overview" />
      <template v-for="(question, i) in oSurvey.questions">
        <!-- Single Question here -->
        <div v-if="questionIsViewed(question)" v-bind:key="question.id">
          <div>{{ question.title }}</div>
          <div>{{ question.subtitle }}</div>
          <div>{{ question.description }}</div>

          <!-- if checkboxes -->
          <template v-if="question.format == 'checkboxes'">
            <q-item
              tag="label"
              v-ripple
              v-for="option in question.options"
              v-bind:key="option.id"
              :value="option.id"
            >
              <q-item-section avatar top>
                <q-checkbox
                  :value="findSelectedOption(question, option) ? true : false"
                  v-on:click.native="toggleAwnserOption(question, option)"
                />
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ option.title }}</q-item-label>
                <q-item-label caption>
                  {{ option.subtitle }}
                  {{ option.description }}
                </q-item-label>
              </q-item-section>

            </q-item>
            <!-- question.users_awnser.awnser_options -->
          </template>

          <!-- If Else Slider -->
          <template v-else-if="question.format == 'slider'">
            slider
          </template>

          <!-- Debug -->
          <div class="code">{{ question }}</div>

          <q-input v-if="question.users_awnser" v-model="question.users_awnser.comment" label="Kommentar" />

          <!-- Before question Button -->
          <q-btn
            :disable="!beforeQuestionRoute(oSurvey.questions, question)"
            :to="beforeQuestionRoute(oSurvey.questions, question)"
            icon="arrow_left"
          />

          <q-btn
            :label="getSubmitLabel(oSurvey, question)"
            :disable="questionIsNotSubmittable(question) ? true : false"
            color="primary"
            @click="updateOrCreateAwnser(oSurvey, question)"
          />

          <!-- Next question Button -->
          <q-btn
            :disable="!nextQuestionRoute(oSurvey.questions, question)"
            :to="nextQuestionRoute(oSurvey.questions, question)"
            icon="arrow_right"
          />
        </div>
      </template>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  layout: 'naked',
  middleware: 'auth',

  data () {
    return {
      hashes: {
        question: '#q-',
        overview: '#overview'
      },
      oSurvey: null
    }
  },

  methods: {
    getProgressClasses (survey, question) {
      var r = ''

      // If Question is Awnsered
      if (question.users_awnser) r += ' awnsered '

      return r
    },
    updateOrCreateAwnser (survey, question) {
      const _t = this
      console.log(question)

      // Update Users
      this.$store
        .dispatch('surveys/updateOrCreateAwnser', {
          survey_id: question.survey_id,
          question_id: question.id,
          awnser: question.users_awnser
        })
        .then(function (e) {
          // Success
          if (!e || !e.response || !e.response.data || !e.response.data.error) {
            // _t.showSnackbarSuccess(_t.$t('data_saved'))
            console.log('data saved')

            // Update in Model
            question.users_awnser = _t.copyObject(e.data)
          }
        })
        .catch(function (e) {
          console.log(e)
          // Error
          if (e.reponse && e.reponse.data && e.response.data.error) {
            var errText = ''
            var err = e.response.data.error
            for (var i in err) {
              errText += ': ' + err[i]
            }
            console.log('data UNsaved')
            // _t.showSnackbarError(_t.$t('data_unsaved') + '<br />' + errText)
          }
        })
    },

    questionIsSubmittable (q) {
      // Skippable
      const skp = q.is_skippable
      if (skp) return true

      // Options Available
      if (q && q.users_awnser && q.users_awnser.awnser_options) {
        // Not Skippable
        const len = q.users_awnser.awnser_options.length
        const min = q.min_options <= len
        const max = q.max_options >= len

        return min && max
      }

      return false
    },
    questionIsNotSubmittable (q) {
      return !this.questionIsSubmittable(q)
    },
    getSubmitLabel (oSurvey, question) {
      // If is not Skippable
      if (question.is_skippable === 0) {
        // If Min Options is smaller or equal the current options
        return 'übernehmen'
      } else {
        // Is Skippable
        return 'Überspringen'
      }
    },
    toggleAwnserOption (oQuestion, oOption) {
      console.log('jo toggle!')
      var oAwnser = this.findOrCreateAwnser(oQuestion)
      var aAwOpts = oAwnser.awnser_options
      var oSelOpt = this.findSelectedOption(oQuestion, oOption)

      // Already Selected
      if (oSelOpt) {
        // So remove!
        var iPos = this.getPositionById(oSelOpt, aAwOpts)
        if (aAwOpts[iPos]) {
          // Remove
          aAwOpts.splice(iPos, 1)
        }
      } else {
        // Not Selected
        // So Add!
        var obj = this.copyObject(oOption)

        aAwOpts.push(obj)
      }
    },
    findOrCreateAwnser (oQuestion) {
      if (!oQuestion.users_awnser) {
        oQuestion.users_awnser = {
          question_id: oQuestion.id,
          awnser_options: []
        }
      }
      return oQuestion.users_awnser
    },
    findSelectedOption (question, option) {
      if (question && question.users_awnser && question.users_awnser.awnser_options) {
        return this.findByKey(
          question.users_awnser.awnser_options,
          option.id,
          'id'
        )
      }
    },
    questionRoute (qs, q, dir) {
      var iCurPos = this.getPositionById(q, qs)
      var iNewPos = iCurPos + dir
      var eNew = qs[iNewPos]
      if (eNew) return this.getQuestionHash(eNew)
    },
    beforeQuestionRoute (qs, q) {
      return this.questionRoute(qs, q, -1)
    },
    nextQuestionRoute (qs, q) {
      return this.questionRoute(qs, q, +1)
    },
    copyObject (obj) {
      if (typeof obj !== 'undefined') {
        var copy = JSON.parse(JSON.stringify(obj))
        if (copy) return copy
      }
    },
    findByKey (arr, id, key) {
      return arr.find(x => x[key] === id)
    },
    findById (arr, id) {
      return arr.find(x => x.id === id)
    },
    getPositionById (item, arr) {
      return arr
        .map(function (x) {
          return x.id
        })
        .indexOf(item.id)
    },
    getPositionByKey (oItem, oObject, sKey) {
      return oObject
        .map(function (x) {
          return x[sKey]
        })
        .indexOf(oItem[sKey])
    },
    changedHash (hash) {
      var h = hash || window.location.hash
      // var id = h.split('#')[1]

      switch (h) {
        case h === '':
        case h === '#':
          window.location.hash = this.hashes.overview
          break
      }
    },
    getOverviewHash () {
      return this.hashes.overview
    },
    getQuestionHash (question) {
      return this.hashes.question + question.id
    },
    questionIsViewed (q) {
      return this.getQuestionHash(q) === this.$route.hash
    },
    overviewIsViewed () {
      return this.$route.hash == this.hashes.overview
    }
  },

  watch: {
    surveyFillable: function (promise) {
      this.oSurvey = this.copyObject(promise)
    },
    '$route.hash': function (current, before) {
      this.changedHash(current)
    }
  },

  created: function () {
    this.$store.dispatch(
      'surveys/fetchSurveyFillable',
      parseInt(this.$route.params.id)
    )

    this.changedHash()
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      surveyFillable: 'surveys/surveyFillable'
    })
  }

}
</script>

<style lang="scss">
.progress {
  display: none;
}

.survey-progress-wrapper {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-evenly;
  align-self: normal;
  span {
    height: 9px;
    background: grey;
    display: inline-block;
    width: 100%;
    border-radius: 10px;
    margin: 3px;
  }
  .awnsered {
    background-color: var(--q-color-positive);
  }
}
</style>
