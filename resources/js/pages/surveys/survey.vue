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
        <router-link
            v-for="question in oSurvey.questions"
            v-bind:key="question.id"
            :to="getQuestionHash(question)"
            :class="getProgressClasses(oSurvey, question)"
        >
        </router-link>
      </div>

      <q-btn label="overview" :to="hashes.overview" />

      <!-- All Questions -->
      <template v-for="question in oSurvey.questions">
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
          <div class="code">{{ question }}</div>

          <q-input v-if="question.users_awnser" v-model="question.users_awnser.comment" label="Kommentar" />
          <q-input v-else label="Kommentar" @click="findOrCreateAwnser(question)" />

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
