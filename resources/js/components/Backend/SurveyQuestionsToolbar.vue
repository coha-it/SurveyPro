<template lang="pug">
div
  q-toolbar.coha--toolbar(v-if='selected.length <= 0' :flat="pagination.search == ''" floating='' style='min-height:100px' height='auto')
    .row(style="width: 100%")
      q-input(v-model='pagination.search' style='max-width: 400px;' :label="$t('Search')" autocomplete='off' append-icon='search' hide-details='' outlined='')
      .col
      .row.items-end
        span
          q-pagination(v-model='pagination.page' :max='getPages()' :max-pages='pagination.max_pages' :direction-links='true' :boundary-links='true' :boundary-numbers='false')
        q-input.ml-5(v-model='pagination.rowsPerPage' number='' type='number' hide-details='' style='max-width: 150px;' label='Fragen pro Seite' min='1' size='sm' outlined='')
  q-toolbar.coha--toolbar.bg-primary.text-white(v-else :flat="pagination.search == ''" color='primary' dark='' floating='' style='min-height:100px' height='auto')
    q-btn(:label="selected.length + ' ' + $t('edit')" unelevated='' rounded='' icon='mdi-pencil')
      q-menu(right='' offset-y='')
        q-list(style='min-width: 100px')
          q-item(v-close-popup='' clickable='' @click='duplicateSelectedQuestions()')
            q-item-section(side='')
              q-icon(name='control_point_duplicate')
            q-item-section
              | Duplizieren
          q-item(v-close-popup='' clickable='' @click='tryDeleteQuestions(selected)')
            q-item-section(side='')
              q-icon(name='delete')
            q-item-section
              | L&ouml;schen
          q-item(clickable='' @click='moveSelectedUp()')
            q-item-section(side='')
              q-icon(name='arrow_drop_up')
            q-item-section
              | Position hoch
          q-item(clickable='' @click='moveSelectedDown()')
            q-item-section(side='')
              q-icon(name='arrow_drop_down')
            q-item-section
              | Position runter
    .flex-grow-1

</template>

<script>
export default {
  props: {
    oSurvey: {
      type: Object,
      required: true
    },
    selected: {
      type: Array,
      required: true
    },
    pagination: {
      type: Object,
      required: true
    },
    tryDeleteQuestions: {
      type: Function,
      required: true
    },
    duplicateSelectedQuestions: {
      type: Function,
      required: true
    },
    moveSelectedUp: {
      type: Function,
      required: true
    },
    moveSelectedDown: {
      type: Function,
      required: true
    }
  },

  created () {
    // Go to last Page
    this.toLastPage()
  },

  methods: {

    toLastPage: function () {
      this.pagination.page = this.getPages()
    },

    getPages: function (pagination = this.pagination, oSurvey = this.oSurvey) {
      // Rows Per Page
      let rpp = pagination.rowsPerPage
      rpp = rpp > 0 ? rpp : 1

      // Len of questions
      const len = oSurvey.questions.length

      // Return rounded
      return Math.round(len / rpp)
    }
  }
}
</script>
