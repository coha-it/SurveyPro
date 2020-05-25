<template>
  <transition name="fade" mode="out-in">
    <div class="survey wrapper">
      <div class="inner">
        <div :key="overviewIsViewed()">
          <div v-if="oSurvey">
            <!-- Overview -->
            <Overview
              v-if="overviewIsViewed()"
              :hashes="hashes"
              :o-survey="oSurvey"
              :get-question-hash="getQuestionHash"
              :get-overview-hash="getOverviewHash"
            />

            <!-- Questions -->
            <Question
              v-else
              :hashes="hashes"
              :o-survey="oSurvey"
              :question="question"
              :get-viewed-question="getViewedQuestion"
              :get-question-hash="getQuestionHash"
              :get-overview-hash="getOverviewHash"
              :question-is-viewed="questionIsViewed"
              :get-position-by-id="getPositionById"
              :find-by-key="findByKey"
              :copy-object="copyObject"
              :is-light="isLight"
              :is-dark="isDark"
              :light-or-dark="lightOrDark"
              :question_transition="question_transition"
            />
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import { mapGetters } from 'vuex'
import Overview from '~/components/Questions/Overview'
import Question from '~/components/Questions/Question'

export default {
  layout: 'NakedLayout',
  middleware: 'auth',

  components: {
    Overview,
    Question
  },

  data () {
    return {
      hashes: {
        question: '#q-',
        overview: '#overview'
      },
      oSurvey: null,
      question_transition: 'fade-left'
    }
  },

  computed: {
    ...mapGetters({
      user: 'auth/user',
      surveyFillable: 'surveys/surveyFillable'
    })
  },

  watch: {
    surveyFillable: function (promise) {
      this.oSurvey = this.copyObject(promise)
      this.question = this.getViewedQuestion(this.oSurvey)
    },
    '$route.hash': function (current, before) {
      this.changedHash(current)
      if (this.oSurvey) {
        // Set Transition
        this.setQuestionTransition()

        // Set New Question
        this.question = this.getViewedQuestion(this.oSurvey)
      }
    }
  },

  created: function () {
    this.$store.dispatch(
      'surveys/fetchSurveyFillable',
      parseInt(this.$route.params.id)
    )

    this.changedHash()
  },

  methods: {

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
    getQuestionHash (q) {
      return this.hashes.question + (q && q.id ? q.id : null)
    },
    questionIsViewed (q) {
      return this.getQuestionHash(q) === this.$route.hash
    },
    getViewedQuestion (oSurvey) {
      var _t = this
      return oSurvey.questions.find(q => _t.questionIsViewed(q))
    },
    overviewIsViewed () {
      const hash = this.$route.hash
      return hash === this.hashes.overview || hash === ''
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

    setQuestionTransition: function () {
      var oQuestionOld = this.question
      var oQuestionNew = this.getViewedQuestion(this.oSurvey)

      if (oQuestionOld && oQuestionNew) {
        var a = oQuestionOld.order
        var b = oQuestionNew.order

        this.question_transition = a < b ? 'fade-left' : 'fade-right'
      }
    }
  }

}
</script>

<style lang="scss">

h1 {
  font-size: 24px;
  font-weight: 500;
  line-height: 1;
  letter-spacing: .02em;
  color: #000;
}

h2 {

}

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
    height: 100%;
    display: inline-flex;
    width: 100%;
    align-items: center;
    justify-content: center;
    align-self: center;
    .inner {
      height: 9px;
      background: #6f6f6f;
      display: inline-block;
      width: 100%;
      border-radius: 10px;
      margin: 3px;
      transition: 200ms;
    }

    &.awnsered {
      .inner {
        background-color: var(--q-color-positive);
      }
    }
    &.skipped {
      .inner {
        background: #bbbd5e;
      }
    }
    &.away {
      .inner {
        opacity: .3;
      }
      &.unawnsered {
        pointer-events: none;
      }
    }
    &.curr {
      .inner {
        opacity: 1;
      }
      // &.awnsered {
      //   & + .progress {
      //     pointer-events: all;
      //   }
      // }
    }
    &.passed {
      .inner {
        opacity: 1;
      }
    }

  }

}
</style>
