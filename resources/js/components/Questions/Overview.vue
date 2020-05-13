<template>
  <q-layout view="hHr lpr fFr">
    <q-header class="bg-white text-primary">
      <q-toolbar>
        <div class="q-mx-sm q-my-md">
          <h4 style="margin: 0;">
            <div class="text-caption text-gray">{{ $t('from') + ' ' + oSurvey.author }}</div>
            <span>{{ oSurvey.title }}</span>
          </h4>
        </div>
      </q-toolbar>
    </q-header>
    <q-page-container class="bg-white">
      <q-page>
        <div class="q-px-lg q-pb-lg">
          {{ oSurvey.desc_long || oSurvey.desc_short }}
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
          v-if="allQuestionsAwnsered()"
          label="Umfrage abschließen"
          color="primary"
          class="full-width"
        />
        <q-btn
          v-else
          :label="noQuestionsAwnsered() ? 'Umfrage Beginnen' : 'Umfrage fortsetzen'"
          color="primary"
          class="full-width"
          :to="getSelectableQuestionHash()"
          @click="getSelectableQuestion()"
        />
        <q-btn flat icon="keyboard_arrow_down" :to="'/'" />
      </q-toolbar>
    </q-footer>
  </q-layout>
</template>

<script>
export default {
  props: {
    hashes: Object,
    oSurvey: Object,
    getQuestionHash: Function,
    getOverviewHash: Function,
    getProgressClasses: Function,
  },

  methods: {

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
    }
  }
}
</script>
