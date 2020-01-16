<template>
  <div v-if="oSurvey">
    <!-- Overview -->
    <template v-if="overviewIsViewed()">
      <h2>Overview - Question List</h2>
      <ul>
        <li v-for="question in oSurvey.questions" v-bind:key="question.id">
          <router-link :to="getQuestionHash(question)">
            {{ question.title }}
          </router-link>
        </li>
      </ul>
    </template>

    <!-- Questions -->
    <template v-else>
      <q-layout view="hHr lpr fFr">
        <q-header class="bg-white text-primary">
          <q-toolbar>
            <!-- Progress -->
            <div class="survey-progress-wrapper">
              <router-link
                v-for="question in oSurvey.questions"
                v-bind:key="question.id"
                :to="getQuestionHash(question)"
                :class="getProgressClasses(oSurvey, question)"
              />
              <!-- :to="getQuestionHash(question)" -->
              <!-- question.users_awnser -->
            </div>
          </q-toolbar>
        </q-header>
        <q-page-container>
          <q-page class="q-pa-md">
            <!-- The Question -->
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
                    <!-- Multiple -->
                    <q-checkbox
                      v-if="question.max_options > 1"
                      v-on:click.native="toggleAwnserOption(question, option)"
                      :value="findSelectedOption(question, option) ? true : false"
                    />
                    <!-- Single -->
                    <q-radio
                      v-else
                      :value="findSelectedOptionId(question, option)"
                      :val="option.id"
                      selected
                      v-on:click.native="toggleAwnserOptionSingle(question, option)"
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
              </template>

              <!-- If Else Slider -->
              <template v-else-if="question.format == 'slider'">
                <q-slider
                  :value="getSelectedSliderOptionOrder(question)"
                  :min="getFirstQuestionOption(question)"
                  :max="getLastQuestionOption(question)"
                  :step="1"
                  label
                  :label-value="getSliderLabel(question)"
                  :style="'color:'+getSliderColor(question)"
                  :label-text-color="getSliderTextColor(question)"
                  class="coha--rating-slider"
                  label-always
                  markers
                  @change="sliderChange"
                  @mousedown.native="sliderInput"
                />
                <template v-if="questionHasAwnsers(question)">
                  {{ firstAwnser(question).title }}
                  {{ firstAwnser(question).subtitle }}
                  {{ firstAwnser(question).description }}
                  <!-- {{ firstAwnser(question) }} -->
                </template>
              </template>

              <!-- Debug -->
              <div v-if="false" class="code">{{ question }}</div>

              <div class="comment-wrapper" style="text-align: center">
                <template v-if="question.users_awnser">
                  <q-btn
                    :label="question.users_awnser.comment == '' ? 'Kommentar hinzufügen' : 'Kommentar bearbeiten'"
                    icon="chat"
                    size="md"
                    flat
                    rounded
                    color="grey"
                    @click.native="question_dialog = true"
                  />
                 <q-dialog
                    v-model="question_dialog"
                    persistent
                  >
                    <q-card style="min-width: 350px">
                      <q-card-section>
                        <div class="text-h6">Kommentar:</div>
                      </q-card-section>

                      <q-card-section class="q-pt-none">
                        <q-input dense v-model="question.users_awnser.comment" autofocus @keyup.enter="question_dialog = false" />
                      </q-card-section>

                      <q-card-actions align="right" class="text-primary">
                        <q-btn flat label="Übernehmen" v-close-popup />
                      </q-card-actions>
                    </q-card>
                  </q-dialog>
                </template>
                <template v-else>
                  <q-btn
                    label="Kommentar hinzufügen"
                    icon="chat"
                    size="md"
                    flat
                    rounded
                    color="grey"
                    @click="findOrCreateAwnser(question); question_dialog = true"
                  />
                </template>
              </div>

              <q-input v-if="question.users_awnser" v-model="question.users_awnser.comment" label="Kommentar" />
              <q-input v-else label="Kommentar" @click="findOrCreateAwnser(question)" />

              <!-- Before question Button -->
              <q-btn
                :disable="!beforeQuestionRoute(question)"
                :to="beforeQuestionRoute(question)"
                icon="arrow_left"
              />

              <!-- Next question Button -->
              <q-btn
                :disable="!nextQuestionRoute(question)"
                :to="nextQuestionRoute(question)"
                icon="arrow_right"
              />
            </div>
          </q-page>
        </q-page-container>
        <q-footer bordered class="bg-white text-primary">
          <q-toolbar>
            <q-btn flat icon="keyboard_arrow_left" :to="hashes.overview" />
            <template v-if="question">
              <q-btn
                :label="getSubmitLabel(question)"
                :disable="questionIsNotSubmittable(question) ? true : false"
                color="primary"
                class="full-width"
                @click="updateOrCreateAwnser(question)"
              />
            </template>
            <q-btn flat icon="keyboard_arrow_down" :to="{ path: $store.state.route.from.fullPath }" />
          </q-toolbar>
        </q-footer>
      </q-layout>
    </template>
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
      oSurvey: null,
      question: null,
      question_dialog: false
    }
  },
  methods: {
    questionHasAwnsers (question) {
      return this.firstAwnser(question) ? true : false
    },
    firstAwnser (question) {
      if (
        question &&
        question.users_awnser &&
        question.users_awnser.awnser_options)
      {
        return question.users_awnser.awnser_options[0]
      }
      return 0
    },
    getSelectedSliderOptionOrder (question) {
      var o = this.firstAwnser(question)
      return (o.order) ? o.order : null
    },
    getFirstQuestionOption (question) {
      return Math.min.apply(Math, question.options.map(option => option.order))
    },
    getLastQuestionOption (question) {
      return Math.max.apply(Math, question.options.map(option => option.order))
    },
    sliderInput (e) {
      console.log(e)
    },
    sliderChange (order) {
      var question = this.getViewedQuestion(this.oSurvey)
      var option = this.findByKey(question.options, order, 'order')

      this.toggleAwnserOptionSingle(question, option)
    },
    getSliderLabel (question) {
      var o = this.firstAwnser(question)
      return (o.title) ? o.title : null
    },
    getSliderColor (question) {
      var o = this.firstAwnser(question)
      return (o.color) ? o.color : ''
    },
    getSliderTextColor (question) {
      var o = this.firstAwnser(question)
      return (o.color && this.isLight(o.color)) ? 'black' : 'white'
    },
    getProgressClasses (survey, question) {
      var r = ' progress '
      var viewedQ = this.getViewedQuestion(survey)

      // Get Viewed Question
      if      (question.order < viewedQ.order) r += ' passed '
      else if (question.order > viewedQ.order) r += ' away '
      else r += ' current '

      // If Question is Awnsered
      if (question.users_awnser) r += ' awnsered '

      return r
    },
    updateOrCreateAwnser (question) {
      const _t = this

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

            // Next Question
            _t.$router.push(
              _t.nextQuestionRoute(question)
            )
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
    getSubmitLabel (question) {
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
    toggleAwnserOptionSingle (question, option) {
      // Delete all Awnser Options
      if (question &&
          question.users_awnser &&
          question.users_awnser.awnser_options)
      {
            question.users_awnser.awnser_options = []
      }

      // Select
      this.toggleAwnserOption(question, option)
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
    findSelectedOptionId (question, option) {
      var option = this.findSelectedOption(question, option)
      if (option && option.id) return option.id
    },
    questionRoute (q, dir) {
      var nq = this.getQuestion(q, dir)
      if (nq) return this.getQuestionHash(nq)
    },
    beforeQuestionRoute (q) {
      return this.questionRoute(q, -1)
    },
    nextQuestionRoute (q) {
      return this.questionRoute(q, +1)
    },
    getQuestion (q, dir) {
      var qs = this.oSurvey.questions
      var iCurPos = this.getPositionById(q, qs)
      var iNewPos = iCurPos + dir
      var eNew = qs[iNewPos]
      if (eNew) return eNew
    },
    nextQuestion (q) {
      return this.getQuestion(q, +1)
    },
    beforeQuestion (q) {
      return this.getQuestion(q, -1)
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
    getViewedQuestion (oSurvey) {
      var _t = this;
      return oSurvey.questions.find(q => _t.questionIsViewed(q))
    },
    overviewIsViewed () {
      return this.$route.hash == this.hashes.overview
    },



    getNegativeColor (color) {
      if (this.isDark(color)) {
        return 'text-white'
      }
      return 'text-black'
    },

    isLight (color) {
      return this.lightOrDark(color) === 'light'
    },

    isDark (color) {
      return this.lightOrDark(color) === 'dark'
    },

    lightOrDark (color) {
      if (!color) return

      // Variables for red, green, blue values
      var r, g, b, hsp

      // Check the format of the color, HEX or RGB?
      if (color.match(/^rgb/)) {
        // If HEX --> store the red, green, blue values in separate variables
        color = color.match(/^rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*(\d+(?:\.\d+)?))?\)$/)

        r = color[1]
        g = color[2]
        b = color[3]
      } else {
        // If RGB --> Convert it to HEX: http://gist.github.com/983661
        color = +('0x' + color.slice(1).replace(color.length < 5 && /./g, '$&$&'))

        r = color >> 16
        g = color >> 8 & 255
        b = color & 255
      }

      // HSP (Highly Sensitive Poo) equation from http://alienryderflex.com/hsp.html
      hsp = Math.sqrt(
        0.299 * (r * r) +
        0.587 * (g * g) +
        0.114 * (b * b)
      )

      // Using the HSP value, determine whether the color is light or dark
      // if (hsp > 127.5) {
      if (hsp > 197.5) {
        return 'light'
      } else {
        return 'dark'
      }
    },
  },

  watch: {
    surveyFillable: function (promise) {
      this.oSurvey = this.copyObject(promise)
      this.question = this.getViewedQuestion(this.oSurvey)
    },
    '$route.hash': function (current, before) {
      this.changedHash(current)
      if(this.oSurvey) {
        this.question = this.getViewedQuestion(this.oSurvey)
      }
    },
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

  .progress {
    height: 9px;
    background: #6f6f6f;
    display: inline-block;
    width: 100%;
    border-radius: 10px;
    margin: 3px;
    &.awnsered {
      background-color: var(--q-color-positive);
    }
    &.away { opacity: .3; }
    &.current { opacity: 1; }
    &.passed { opacity: 1; }
  }

}
</style>
