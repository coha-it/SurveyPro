<template>
  <transition name="fade" mode="out-in">
    <div :class="'survey wrapper ' + (bPreview ? 'preview' : null)">
      <div v-if="bPreview">
        <q-btn
          label="CHANGE"
          icon="refresh"
          unelevated
          @click="oSurvey = copyObject(oSurvey)"
        />
      </div>
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
              :b-preview="bPreview"
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
              :question-transition="questionTransition"
              :b-preview="bPreview"
            />
          </div>
        </div>
      </div>
    </div>
  </transition>
</template>

<script>
import Overview from '~/components/Questions/Overview'
import Question from '~/components/Questions/Question'

export default {
  layout: 'NakedLayout',
  middleware: 'auth',

  components: {
    Overview,
    Question
  },

  props: {
    oSurvey: {
      type: Object,
      required: true
    },
    bPreview: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      hashes: {
        question: '#q-',
        overview: '#overview'
      },
      question: null,
      questionTransition: 'fade-left'
    }
  },

  watch: {
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
    // Changed Hash
    this.changedHash()

    // Set Question
    this.question = this.getViewedQuestion(this.oSurvey)
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

        this.questionTransition = a < b ? 'fade-left' : 'fade-right'
      }
    }
  }

}
</script>
