<template>
  <q-layout view="hHr lpr fFr">
    <q-header class="bg-white text-primary">
      <q-toolbar>
        <!-- Progress -->
        <div :class="'survey-progress-wrapper ' + (oSurvey.questions.length > 15 ? 'thin' : null)">
          <template v-for="q in oSurvey.questions">
            <span
              :key="q.id"
              :class="getProgressClasses(oSurvey, q)"
              @click="changeQuestion(q)"
            >
              <span class="inner">&nbsp;</span>
            </span>
            <!-- :to="getQuestionHash(q)" -->
            <!-- q.users_awnser -->
          </template>
        </div>
      </q-toolbar>
    </q-header>
    <q-page-container>
      <q-page class="q-pb-xl">
        <transition :name="questionTransition" mode="out-in">
          <!-- The Question -->
          <!-- Single Question here -->
          <div v-if="questionIsViewed(question)" :key="question.id">
            <div class="q-mb-md q-px-md q-pb-md q-pt-sm">
              <div class="text-overline">{{ $t('Question') }} {{ getQuestionPosition() }} / {{ oSurvey.question_count }}</div>
              <h1 class="text-black">{{ question.title }}</h1>
              <div class="text-subtitle1 text-black">{{ question.subtitle }}</div>
              <div class="text-body2">
                <div
                  v-if="oSurvey.use_html"
                  v-html="question.description"
                />
                <p v-else>
                  {{ question.description }}
                </p>
              </div>
            </div>

            <!-- if checkboxes -->
            <template v-if="question.format == 'checkboxes'">
              <q-item
                v-for="option in question.options"
                :key="option.id"
                v-ripple
                tag="label"
                :value="option.id"
              >
                <q-item-section avatar top>
                  <!-- Multiple -->
                  <q-checkbox
                    v-if="question.max_options > 1"
                    :value="findSelectedOption(question, option) ? true : false"
                    :style="'--option-color: '+ option.color + ';'"
                    @click.native="toggleAwnserOption(question, option)"
                  />
                  <!-- Single -->
                  <q-radio
                    v-else
                    :value="findSelectedOptionId(question, option)"
                    :val="option.id"
                    selected
                    :style="'--option-color: '+ option.color + ';'"
                    @click.native="toggleAwnserOptionSingle(question, option)"
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
            <template v-else-if="question.format.indexOf('slider') !== -1">
              <q-slider
                :value="getSelectedSliderOptionOrder(question)"
                :min="getFirstQuestionOption(question)"
                :max="getLastQuestionOption(question)"
                :step="1"
                label
                :label-value="getSliderLabel(question)"
                :style="'color:'+getSliderColor()"
                :label-text-color="getSliderTextColor(question)"
                class="coha--rating-slider"
                label-always
                markers
                :vertical="question.format === 'slider_vert'"
                :reverse="question.format === 'slider_vert'"
                @change="sliderChange"
                @input="sliderChange"
              />
              <template v-if="questionHasAwnsers(question)">
                <transition name="fade" mode="out-in">
                  <div
                    :key="firstAwnser(question).subtitle"
                    class="selected-option coha"
                    :style="'color:'+getSliderColor()"
                  >
                    <div class="subtitle">{{ firstAwnser(question).subtitle }}</div>
                    <div class="description">{{ firstAwnser(question).description }}</div>
                  <!-- {{ firstAwnser(question) }} -->
                  </div>
                </transition>
              </template>
            </template>

            <div style="text-align: center">
              <div v-if="question.is_commentable" class="comment_wrapper q-pa-md">
                <template v-if="questionHasComment()">
                  <div class="user_comment_wrapper">
                    <div class="user_comment">
                      <textarea
                        ref="user_comment"
                        v-model="question.users_awnser.comment"
                        :autofocus="true"
                        placeholder="Ihr Kommentar"
                        class="input"
                        tabindex="1"
                      />
                    </div>
                  </div>
                  <q-btn
                    label="Kommentar entfernen"
                    size="sm"
                    unelevated
                    flat
                    rounded
                    tabindex="5"
                    @click="tryDeleteComment"
                  />
                </template>

                <template v-else>
                  <q-btn
                    label="Kommentar hinzufügen"
                    icon="chat"
                    size="md"
                    flat
                    rounded
                    color="grey"
                    @click="createComment(question)"
                  />
                </template>
              </div>

              <div v-if="question.is_skippable" class="skippable-wrapper q-pa-md">
                <q-btn
                  label="Frage überspringen"
                  icon="skip_next"
                  size="md"
                  flat
                  rounded
                  color="grey"
                  @click="skipQuestion(question)"
                />
              </div>
            </div>
            <!-- <pre class="debug">{{ question }}</pre> -->
          </div>
        </transition>
      </q-page>
    </q-page-container>
    <q-footer bordered class="bg-white text-primary">
      <q-toolbar>
        <!-- <q-btn flat icon="keyboard_arrow_left" :to="beforeQuestionRoute(question)" /> -->
        <q-btn flat icon="keyboard_arrow_left" @click="$router.back()" />
        <template v-if="question">
          <template v-if="question.is_skippable && !hasAwnser(question)">
            <q-btn
              label="Frage überspringen"
              color="primary"
              class="full-width"
              tabindex="2"
              @click="skipQuestion(question)"
            />
          </template>

          <template v-else>
            <q-btn
              label="Antwort speichern"
              :disable="!questionSubmittable(question)"
              color="primary"
              class="full-width"
              tabindex="2"
              @click="updateOrCreateAwnser(question, true)"
            />
          </template>
        </template>
        <q-btn flat icon="keyboard_arrow_down" :to="hashes.overview" />
      </q-toolbar>
    </q-footer>
  </q-layout>
</template>

<script>
export default {
  props: {
    hashes: {
      type: Object,
      required: true
    },
    oSurvey: {
      type: Object,
      required: true
    },
    question: {
      type: Object,
      default: null
    },
    getViewedQuestion: {
      type: Function,
      required: true
    },
    getQuestionHash: {
      type: Function,
      required: true
    },
    getOverviewHash: {
      type: Function,
      required: true
    },
    questionIsViewed: {
      type: Function,
      required: true
    },
    getPositionById: {
      type: Function,
      required: true
    },
    findByKey: {
      type: Function,
      required: true
    },
    copyObject: {
      type: Function,
      required: true
    },
    isLight: {
      type: Function,
      required: true
    },
    isDark: {
      type: Function,
      required: true
    },
    lightOrDark: {
      type: Function,
      required: true
    },
    questionTransition: {
      type: String,
      required: true
    },
    bPreview: {
      type: Boolean,
      default: false
    }
  },

  data () {
    return {
      //
    }
  },

  methods: {

    questionHasComment () {
      const q = this.question
      return q.users_awnser && typeof q.users_awnser.comment === 'string'
    },

    createComment (question) {
      let awnser = this.findOrCreateAwnser(question)

      // If Comment isnt a string
      if (typeof awnser.comment !== 'string') {
        // Set as a string
        awnser.comment = ''
      }

      if (this.$refs.user_comment) this.focusCommentInput()

      this.$nextTick(() => {
        if (this.$refs.user_comment) this.focusCommentInput()
      })

      setTimeout(() => {
        this.focusCommentInput()
      }, 5)
    },

    getAwnser () {
      const question = this.question
      return question && question.users_awnser ? question.users_awnser : {}
    },

    getComment () {
      const awnser = this.getAwnser()
      return awnser && awnser.comment ? awnser.comment : ''
    },

    tryDeleteComment () {
      this.$q.dialog({
        title: 'Kommentar Löschen?',
        message: 'Möchten Sie wirklich Ihren Kommentar löschen? ' +
                  (this.getComment() ? 'Ihr Kommentar: "' + this.getComment() + '"' : ''),
        ok: {
          label: 'Löschen',
          unelevated: true
        },
        cancel: {
          label: 'Zurück',
          unelevated: true
        }
      }).onOk(() => {
        this.question.users_awnser.comment = null
      }).onCancel(() => {
        // console.log('>>>> Cancel')
      }).onDismiss(() => {
        // console.log('I am triggered on both OK and Cancel')
      })
    },

    changeQuestion (clickedQuestion) {
      let currQ = this.question
      // If its another question
      if (currQ.id === clickedQuestion.id) {
        return true
      } else {
        // Update Current Question
        if (currQ.users_awnser) {
          this.updateOrCreateAwnser(this.question, false)
        }

        // Route to new clicked Question
        let to = this.getQuestionHash(clickedQuestion)
        this.$router.push(to)
      }
    },

    isSkipped (q) {
      return q.users_awnser && q.users_awnser.skipped
    },

    hasAwnser (q) {
      return (
        q &&
        q.users_awnser &&
        (
          q.users_awnser.awnser_options.length > 0 || q.users_awnser.comment
        )
      )
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
    getQuestionPosition () {
      return this.getPositionById(this.question, this.oSurvey.questions) + 1
    },
    questionSubmittable (q) {
      // Skippable
      if (q.is_skippable) return true

      // If Text only
      if (q.format === 'comment_only' && q.users_awnser && q.users_awnser.comment) return true

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
    getQuestion (q, dir) {
      var qs = this.oSurvey.questions
      var iCurPos = this.getPositionById(q, qs)
      var iNewPos = iCurPos + dir
      var eNew = qs[iNewPos]
      if (eNew) return eNew
    },
    firstAwnser (question) {
      if (
        question &&
        question.users_awnser &&
        question.users_awnser.awnser_options
      ) {
        return question.users_awnser.awnser_options[0]
      }
      return {}
    },
    questionRoute (q, dir) {
      var nq = this.getQuestion(q, dir)
      if (nq) return this.getQuestionHash(nq)
      else return this.getOverviewHash()
    },
    beforeQuestionRoute (q) {
      return this.questionRoute(q, -1)
    },
    nextQuestionRoute (q) {
      return this.questionRoute(q, +1)
    },
    goTo (a) {
      this.$router.push(a)
    },
    goToQuestion (q) {
      this.goTo(this.questionRoute(q, 0))
    },
    goToOverview () {
      this.goTo(this.getOverviewHash())
    },
    nextUnawnseredQuestion (q) {
      // Get next Unawnsered Questions
      const nextUaQuestions = this.oSurvey.questions.filter(e =>
        (!e.users_awnser || e.users_awnser.skipped || !this.questionSubmittable(e)) && // Where already Awnsered And
        e.order > q.order // Where order is bigger
      )

      if (nextUaQuestions.length) {
        this.goToQuestion(nextUaQuestions[0])
      } else {
        this.goToOverview()
      }
    },
    getProgressClasses (survey, question) {
      var r = ['progress']
      var viewedQ = this.getViewedQuestion(survey)

      // Get Viewed Question
      if (question.order < viewedQ.order) r.push('passed')
      else if (question.order > viewedQ.order) r.push('away')
      else r.push('curr')

      if (this.isSkipped(question)) {
        // If Question is Skipped
        r.push('skipped')
      } else if (
        question.users_awnser &&
        this.questionSubmittable(question)
      ) {
        // If Question is Awnsered
        r.push('awnsered')
      } else {
        r.push('unawnsered')
      }

      return r.join(' ')
    },
    getSelectedSliderOptionOrder (question) {
      let o = this.firstAwnser(question)
      return (o && o.order) ? o.order : null
    },
    getFirstQuestionOption (question) {
      return Math.min.apply(Math, question.options.map(option => option.order))
    },
    getLastQuestionOption (question) {
      return Math.max.apply(Math, question.options.map(option => option.order))
    },
    questionHasAwnsers (question) {
      return !!this.firstAwnser(question)
    },
    getSliderLabel (question) {
      let o = this.firstAwnser(question)
      return (o && o.title) ? o.title : null
    },
    getSliderColor () {
      let o = this.firstAwnser(this.question)
      return (o && o.color) ? o.color : ''
    },
    getSliderTextColor (question) {
      let o = this.firstAwnser(question)
      return (o && o.color && this.isLight(o.color)) ? 'black' : 'white'
    },
    sliderInput (e) {
      console.log(e)
    },
    sliderMouseMove (e) {
      console.log('slidermousemove', e)
    },
    sliderChange (order) {
      var question = this.getViewedQuestion(this.oSurvey)
      var option = this.findByKey(question.options, order, 'order')

      this.toggleAwnserOptionSingle(question, option)
    },
    toggleAwnserOptionSingle (question, option) {
      // Delete all Awnser Options
      if (
        question &&
        question.users_awnser &&
        question.users_awnser.awnser_options
      ) {
        question.users_awnser.awnser_options = []
      }

      // Select
      this.toggleAwnserOption(question, option)
    },

    focusCommentInput () {
      this.$refs.user_comment.focus()
    },

    findOrCreateAwnser (oQuestion) {
      if (!oQuestion.users_awnser) {
        oQuestion.users_awnser = {
          question_id: oQuestion.id,
          awnser_options: [],
          comment: null
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
      let opt = this.findSelectedOption(question, option)
      if (opt && opt.id) return opt.id
    },

    skipQuestion (q) {
      if (!q.users_awnser) {
        q.users_awnser = {}
      }
      q.users_awnser.awnser_options = [{}]
      q.users_awnser.comment = null
      q.users_awnser.skipped = 1
      this.sendAwnser(q, true)
    },

    updateOrCreateAwnser (q, bNextQ) {
      q.users_awnser.skipped = 0
      this.sendAwnser(q, bNextQ)
    },

    sendAwnser (question, bNextQ) {
      if (this.bPreview) {
        // Next Question
        if (bNextQ) {
          this.nextUnawnseredQuestion(question)
        }
      } else {
        this.$store
          .dispatch('surveys/updateOrCreateAwnser', {
            survey_id: question.survey_id,
            question_id: question.id,
            awnser: question.users_awnser
          })
          .then((e) => {
            // Success
            if (!e || !e.response || !e.response.data || !e.response.data.error) {
              // _t.showSnackbarSuccess(_t.$t('data_saved'))
              console.log('data saved')

              // Update in Model
              question.users_awnser = this.copyObject(e.data)

              // Next Question
              if (bNextQ) {
                this.nextUnawnseredQuestion(question)
              }
            }
          })
          .catch((e) => {
            // Error
            console.log(e)
            this.showSnackbarError(this.$t('data_unsaved') + '<br />' + e)
          })
      }
    }
  }
}
</script>

<style lang="scss" scoped>

h1 {
  font-size: 24px;
  font-weight: 500;
  line-height: 1;
  letter-spacing: .02em;
  color: #000;
}

</style>
