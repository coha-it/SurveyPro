<template lang="pug">
q-layout(view='hHr lpr fFr')
  q-header.bg-white.text-primary
    q-toolbar
      .q-mx-sm.q-mt-md
        h4(style='margin: 0;')
          .text-caption.text-gray
            | {{ getAuthor() }}
          span {{ oSurvey.title }}
  q-page-container.bg-white
    q-page
      .q-px-lg.q-pb-lg.q-mt-md
        template(v-if='oSurvey.use_html')
          div(v-html='getDescription(oSurvey)')
        template(v-else)
          div
            | {{ getDescription(oSurvey) }}
      q-list.q-px-sm
        template(v-for='(question, key) in oSurvey.questions')
          div(v-if='listIsUnlimited(key)' :key='question.id')
            q-item(:to='getQuestionHash(question)' :class='getQuestionClasses(question)' :disable='questionIsUnselectable(question)')
              q-item-section
                q-item-label {{ question.title }}
                q-item-label(caption lines='2') {{ question.subtitle || question.description }}
                q-item-label.users_awnser(v-if="true || allQuestionsAwnsered()" caption)
                  | {{ getUsersAwnser(question) }}
              q-item-section(side='' top='')
                template(v-if='question.users_awnser')
                  template(v-if='!questionIsSkipped(question)')
                    q-item-label(caption) Beantwortet
                    q-icon(name="check" color="green")
                  template(v-else-if='isInfoblock(question)')
                    q-item-label(caption) Gelesen
                    q-icon(name="check" color="green")
                  template(v-else)
                    q-item-label(caption) &Uuml;bersprungen
                    q-icon(name="redo" color="yellow-9")
            q-separator(spaced='' inset='')
        .text-center
          q-btn(v-if="listIsLimited()", :label="extendQuestionListLabel()", size="md", icon-right="keyboard_arrow_down" unelevated, rounded, outline, color="grey-6", @click="bLimited = false")
  q-footer.bg-white.text-primary(bordered='')
    q-toolbar.row.justify-between.q-pa-xs
      q-btn.q-px-sm.my-stack-btn(label='Zurück' stack flat='' icon='keyboard_arrow_down' :to="'/'")
      q-btn.full-width(v-if='notAllQuestionsAwnsered()' :label="noQuestionsAwnsered() ? 'Umfrage Beginnen' : 'Umfrage fortsetzen'" color='primary' :to='getSelectableQuestionHash()' @click='getSelectableQuestion()')
      q-btn.full-width(v-else-if='!oSurvey.user_finished' label='Umfrage abschließen' color='primary' @click='bTryFinishDialog = true')
      q-btn.q-px-sm(flat='' icon='help_outline' disable _click='$router.back()')
      q-dialog(v-model='bTryFinishDialog')
        q-card
          q-card-section.row.items-center
            q-avatar(icon='warning' color='white' text-color='primary')
            span.q-ml-sm
              div
                b Hinweis!
              |                 Nach dem Abschlie&szlig;en ist die Umfrage nichtmehr bearbeitbar
          q-card-actions(align='right')
            q-btn(v-close-popup='' flat='' label='Zurück' color='primary')
            q-space
            q-btn(v-close-popup='' icon='check' unelevated='' label='Umfrage abschließen' color='primary' @click='finishSurvey')
      q-dialog(v-model='bFinishedSurveyDialog' content-class='naked centered finished_survey_dialog' persistent='')
        q-card
          q-card-section.text-center.full-height
            .justify-center.full-height.full-width.text-center.items-center.flex.column
              q-icon.text-green(name='check_circle' style='font-size: 8rem;')
              br
              template(v-if='oSurvey.desc_after_submit')
                // eslint-disable-next-line vue/no-v-html
                div(v-if='oSurvey.use_html' v-html='oSurvey.desc_after_submit')
                div(v-else) {{ oSurvey.desc_after_submit }}
              template(v-else)
                h1.q-mb-xs
                  | Vielen Dank!
                br
                h6.q-ma-xs
                  | Ihre Umfrage wurden erfolgreich ausgef&uuml;llt.
          q-card-actions(align='right')
            q-space
            q-btn(v-close-popup='' unelevated='' label='Ok' color='primary' @click="$router.push('/')")
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
    getQuestionHash: {
      type: Function,
      required: true
    },
    getOverviewHash: {
      type: Function,
      required: true
    },
    bPreview: {
      type: Boolean,
      default: false
    },
    isInfoblock: {
      type: Function,
      required: true
    },
    isNoInfoblock: {
      type: Function,
      required: true
    }
  },

  data () {
    return {
      bTryFinishDialog: false,
      bFinishedSurveyDialog: false,

      // Limit list
      iListLimit: 4,
      bLimited: true
    }
  },

  created () {
    // Limit List
    this.bLimited = true
  },

  methods: {

    getUsersAwnser (question) {
      // Check if User has awnsered
      if (!question.users_awnser) return

      // Constants
      const sep = ' | '
      const awnser = question.users_awnser
      const comment = awnser.comment
      const options = awnser.awnser_options
      const sOption = options.map(e =>
        (e.title ? e.title : '') +
        (e.subtitle ? sep + e.subtitle : '') +
        (e.description ? sep + e.description : '')
      )[0]

      // Variables
      let s = ''
      s += comment ? 'Kommentar: ' + comment + '\n' : ''
      s += sOption ? 'Ausgewählt: ' + sOption + '\n' : ''

      // Returning Variable
      return s
    },

    listIsLimited (i = this.oSurvey.questions.length - 1) {
      return (i >= this.iListLimit) && (this.bLimited)
    },

    listIsUnlimited (i = this.oSurvey.questions.length - 1) {
      return !this.listIsLimited(i)
    },

    extendQuestionListLabel () {
      const max = this.oSurvey.questions.length
      const lim = this.iListLimit

      // 'Alle Fragen anzeigen'
      return (max - lim) + ' weitere Fragen anzeigen'
    },

    getDescription (survey = this.oSurvey) {
      switch (true) {
        case this.allQuestionsAwnsered() && !!survey.desc_before_submit && survey.desc_before_submit !== '<br>':
          return survey.desc_before_submit

        case !!survey.desc_long:
          return survey.desc_long

        case !!survey.desc_short:
          return survey.desc_short

        default:
          return ''
      }
    },

    getAuthor () {
      if (this.oSurvey.author) {
        return this.$t('From') + ' ' + (this.oSurvey.author)
      }
    },

    getSelectableQuestionHash: function () {
      return this.getQuestionHash(this.getSelectableQuestion())
    },

    getSelectableQuestion: function () {
      var order = this.getLastAwnseredQuestion().order + 1
      var selectableQ = this.oSurvey.questions.find(e => e.order === order)
      if (this.noQuestionsAwnsered()) {
        return this.getFirstQuestion()
      }
      return selectableQ
    },

    getFirstQuestion: function () {
      return this.oSurvey.questions[0]
    },

    getLastAwnseredQuestion: function () {
      var arr = this.oSurvey.questions.filter(e => e.users_awnser)
      if (arr.length) {
        return arr[arr.length - 1]
      }
      return this.getFirstQuestion()
    },
    questionIsSelectable: function (question) {
      if (this.noQuestionsAwnsered()) {
        return this.getFirstQuestion().order === question.order
      }
      return question.order <= this.getLastAwnseredQuestion().order + 1
    },
    questionIsUnselectable: function (question) {
      return !this.questionIsSelectable(question)
    },
    countAwnseredQuestions: function () {
      return this.oSurvey.questions.filter(e => e.users_awnser).length
    },
    allQuestionsAwnsered: function () {
      return this.countAwnseredQuestions() === this.oSurvey.question_count
    },
    notAllQuestionsAwnsered: function () {
      return !this.allQuestionsAwnsered()
    },
    noQuestionsAwnsered: function () {
      return this.countAwnseredQuestions() === 0
    },
    questionIsSkipped (q) {
      return q.users_awnser && q.users_awnser.skipped
    },
    getQuestionClasses (q) {
      const yellow = 'bg-yellow-1'
      const green = 'bg-green-1'
      let sClass = ''
      switch (true) {
        case this.isInfoblock(q):
          sClass = green
          break
        case this.questionIsSkipped(q) === 1:
          sClass = yellow
          break
        case q.users_awnser !== null:
          sClass = green
          break
      }
      return sClass
    },
    finishSurvey () {
      this.$store
        .dispatch('surveys/finishSurvey', {
          survey_id: this.oSurvey.id,
          navigator: this.recur(navigator),
          json_data: {
            screen: this.recur(window.screen),
            client_info: this.recur(window.clientInformation),
            window: {
              width: {
                inner: window.innerWidth ?? null,
                outer: window.outerWidth ?? null
              },
              height: {
                inner: window.innerHeight ?? null,
                outer: window.outerHeight ?? null
              }
            }
          }
        })
        .then((e) => {
          this.$q.notify({
            message: this.$t('Umfrage erfolgreich gespeichert'),
            color: 'green',
            position: 'top-right',
            actions: [{ icon: 'close', color: 'white' }],
            timeout: 6000
          })

          setTimeout(() => {
            this.bFinishedSurveyDialog = true
          }, 250)
        })
        .catch((e) => {
          if (e && e.response && e.response.data && e.response.data.message) {
            let message = e.response.data.message ?? ''
            let messages = e.response.data.messages ?? ''

            this.$q.notify({
              message: this.$t(message + '. ' + messages),
              color: 'red',
              position: 'top-right',
              actions: [{ icon: 'close', color: 'white' }],
              timeout: 6000
            })
          }
        })
    },
    recur (obj) {
      let result = {}
      let _tmp
      for (var i in obj) {
        // enabledPlugin is too nested, also skip functions
        if (i === 'enabledPlugin' || typeof obj[i] === 'function') {
          continue
        } else if (typeof obj[i] === 'object') {
          // get props recursively
          _tmp = this.recur(obj[i])
          // if object is not {}
          if (Object.keys(_tmp).length) {
            result[i] = _tmp
          }
        } else {
          // string, number or boolean
          result[i] = obj[i]
        }
      }
      return result
    }
  }
}
</script>
<style lang="sass" scoped>
.users_awnser
  white-space: pre-line
  line-height: 1.5 !important
  padding-left: 7px
  border-left: 3px solid #4eb052
</style>
