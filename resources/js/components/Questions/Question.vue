<template>
  <q-layout view="hHr lpr fFr">
    <q-header class="bg-white text-primary">
      <q-toolbar>
        <!-- Progress -->
        <div class="survey-progress-wrapper">
          <template v-for="question in oSurvey.questions">
            <router-link
              :key="question.id"
              :to="getQuestionHash(question)"
              :class="getProgressClasses(oSurvey, question)"
            >
              <span class="inner">&nbsp;</span>
            </router-link>
            <!-- :to="getQuestionHash(question)" -->
            <!-- question.users_awnser -->
          </template>
        </div>
      </q-toolbar>
    </q-header>
    <q-page-container>
      <q-page class="q-mb-xl">
        <transition :name="question_transition" mode="out-in">
          <!-- The Question -->
          <!-- Single Question here -->
          <div v-if="questionIsViewed(question)" :key="question.id">
            <div class="q-mb-md q-pa-md">
              <div class="text-overline">{{ $t('Question') }} {{ getQuestionPosition() }} / {{ oSurvey.question_count }}</div>
              <h1 class="text-black">{{ question.title }}</h1>
              <div class="text-subtitle1 text-black">{{ question.subtitle }}</div>
              <p class="text-body2">{{ question.description }}</p>
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
                    @click.native="toggleAwnserOption(question, option)"
                  />
                  <!-- Single -->
                  <q-radio
                    v-else
                    :value="findSelectedOptionId(question, option)"
                    :val="option.id"
                    selected
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
            <template v-else-if="question.format == 'slider'">
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
                @change="sliderChange"
                @mousedown.native="sliderInput"
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
              <div class="comment_wrapper">
                <template v-if="question.users_awnser">
                  <div v-if="question.users_awnser.comment" class="comment_inner">
                    <p class="user_comment">
                      {{ question.users_awnser.comment }}
                    </p>
                  </div>

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
                        <q-input v-model="question.users_awnser.comment" dense autofocus @keyup.enter="question_dialog = false" />
                      </q-card-section>

                      <q-card-actions align="right" class="text-primary">
                        <q-btn v-close-popup flat label="Übernehmen" />
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

              <div v-if="question.is_skippable" class="skippable-wrapper">
                <br><br>
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
        <q-btn flat icon="keyboard_arrow_left" :to="getOverviewHash()" />
        <template v-if="question">
          <template v-if="question.is_skippable && !hasAwnser(question)">
            <q-btn
              label="Frage überspringen"
              color="primary"
              class="full-width"
              @click="skipQuestion(question)"
            />
          </template>

          <template v-else>
            <q-btn
              label="Antwort speichern"
              :disable="!questionSubmittable(question)"
              color="primary"
              class="full-width"
              @click="updateOrCreateAwnser(question)"
            />
          </template>
        </template>
        <q-btn flat icon="keyboard_arrow_down" :to="hashes.overview" />
      </q-toolbar>
    </q-footer>
  </q-layout>
</template>

<script>
/* eslint-disable vue/require-default-prop */
export default {
  props: {
    hashes: Object,
    oSurvey: Object,
    question: Object,
    getViewedQuestion: Function,
    getQuestionHash: Function,
    getOverviewHash: Function,
    questionIsViewed: Function,
    getPositionById: Function,
    findByKey: Function,
    copyObject: Function,
    isLight: Function,
    isDark: Function,
    lightOrDark: Function,
    question_transition: String
  },

  data () {
    return {
      question_dialog: false
    }
  },

  methods: {

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
    getProgressClasses (survey, question) {
      var r = ['progress']
      var viewedQ = this.getViewedQuestion(survey)

      // Get Viewed Question
      if      (question.order < viewedQ.order) r.push('passed')
      else if (question.order > viewedQ.order) r.push('away')
      else r.push('curr')

      // If Question is Awnsered
      if (
        question.users_awnser &&
        question.users_awnser.skipped != 1 &&
        this.questionSubmittable(question)
      ) {
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
      return this.firstAwnser(question) ? true : false
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
    sliderChange (order) {
      var question = this.getViewedQuestion(this.oSurvey)
      var option = this.findByKey(question.options, order, 'order')

      this.toggleAwnserOptionSingle(question, option)
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
      this.sendAwnser(q)
    },

    updateOrCreateAwnser (q) {
      q.users_awnser.skipped = 0
      this.sendAwnser(q)
    },

    sendAwnser (question) {
      const _t = this

      console.log('jo', question.users_awnser)

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
    }
  }
}
</script>
