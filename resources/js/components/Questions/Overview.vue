<template>
  <q-layout view="hHr lpr fFr">
    <q-header class="bg-white text-primary">
      <q-toolbar>
        <div class="q-mx-sm q-my-md">
          <h4 style="margin: 0;">
            <div class="text-caption text-gray">
              {{ getAuthor() }}
            </div>
            <span>{{ oSurvey.title }}</span>
          </h4>
        </div>
      </q-toolbar>
    </q-header>
    <q-page-container class="bg-white">
      <q-page>
        <div class="q-px-lg q-pb-lg">
          <div
            v-if="oSurvey.use_html && oSurvey.desc_long"
            v-html="oSurvey.desc_long"
          />
          <div v-else>
            {{ oSurvey.desc_long || oSurvey.desc_short }}
          </div>
        </div>

        <q-list class="q-px-sm">
          <template v-for="question in oSurvey.questions">
            <div :key="question.id">
              <q-item
                :to="getQuestionHash(question)"
                :class="getQuestionClasses(question)"
                :disable="questionIsUnselectable(question)"
              >
                <q-item-section>
                  <q-item-label>{{ question.title }}</q-item-label>
                  <q-item-label caption lines="2">{{ question.subtitle || question.description }}</q-item-label>
                </q-item-section>

                <q-item-section side top>
                  <template v-if="question.users_awnser">
                    <q-item-label caption>
                      {{ questionIsSkipped(question) ? 'Übersprungen' : 'Beantwortet' }}
                    </q-item-label>
                    <q-icon
                      :name="questionIsSkipped(question) ? 'redo' : 'check'"
                      :color="questionIsSkipped(question) ? 'yellow-9' : 'green'"
                    />
                  </template>
                </q-item-section>
              </q-item>
              <q-separator spaced inset />
            </div>
          </template>
        </q-list>
      </q-page>
    </q-page-container>
    <q-footer bordered class="bg-white text-primary">
      <q-toolbar>
        <q-btn flat icon="keyboard_arrow_left" @click="$router.back()" />

        <q-btn
          v-if="!allQuestionsAwnsered()"
          :label="noQuestionsAwnsered() ? 'Umfrage Beginnen' : 'Umfrage fortsetzen'"
          color="primary"
          class="full-width"
          :to="getSelectableQuestionHash()"
          @click="getSelectableQuestion()"
        />

        <q-btn
          v-else-if="!oSurvey.user_finished"
          label="Umfrage abschließen"
          color="primary"
          class="full-width"
          icon="check_circle"
          @click="bTryFinishDialog = true"
        />

        <q-dialog v-model="bTryFinishDialog" persistent>
          <q-card>
            <q-card-section class="row items-center">
              <q-avatar icon="warning" color="white" text-color="green" />
              <span class="q-ml-sm">Nach dem Abschließen ist die Umfrage nichtmehr bearbeitbar</span>
            </q-card-section>

            <q-card-actions align="right">
              <q-btn v-close-popup flat label="Abbruch" color="primary" />
              <q-space />
              <q-btn v-close-popup icon="check" unelevated label="Umfrage abschließen" color="primary" @click="finishSurvey" />
            </q-card-actions>
          </q-card>
        </q-dialog>

        <q-dialog
          v-model="bFinishedSurveyDialog"
          content-class="naked centered finished_survey_dialog"
          persistent
        >
          <q-card>
            <q-card-section class="text-center full-height">
              <div class="justify-center full-height full-width text-center items-center flex column">
                <q-icon name="check_circle" class="text-green" style="font-size: 8rem;" />
                <br>
                <h1 class="q-mb-xs">Vielen Dank!</h1>
                <br>
                <h6 class="q-ma-xs">Ihre Umfrageergebnisse wurden erfolgreich ausgefüllt</h6>
              </div>
            </q-card-section>

            <q-card-actions align="right">
              <q-space />
              <q-btn v-close-popup unelevated label="Ok" color="primary" @click="$router.push('/')" />
            </q-card-actions>
          </q-card>
        </q-dialog>

        <q-btn flat icon="keyboard_arrow_down" :to="'/'" />
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
    }
  },

  data () {
    return {
      bTryFinishDialog: false,
      bFinishedSurveyDialog: false
    }
  },

  methods: {

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
    noQuestionsAwnsered: function () {
      return this.countAwnseredQuestions() === 0
    },
    questionIsSkipped (q) {
      return q.users_awnser && q.users_awnser.skipped
    },
    getQuestionClasses (q) {
      if (this.questionIsSkipped(q)) {
        return 'bg-yellow-1'
      }

      if (q.users_awnser) {
        return 'bg-green-1'
      }

      return 'f'
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
