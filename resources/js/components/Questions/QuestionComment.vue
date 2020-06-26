<template lang="pug">
.comment_wrapper.q-pa-md(v-if='isCommentable(question)')
  template(v-if='questionHasComment()')
    .user_comment_wrapper
      .user_comment
        textarea.input(
          ref='user_comment'
          v-model='question.users_awnser.comment'
          :type="question.comment_is_number ? 'number' : ''"
          :required="question.comment_is_required"
          :autofocus='true'
          placeholder='Ihr Kommentar'
          tabindex='1'
          @focus='textFocus'
          @blur='textBlur'
        )
    q-btn(label='Kommentar entfernen' size='sm' unelevated='' flat='' rounded='' tabindex='5' @click='tryDeleteComment')
  template(v-else='')
    q-btn(label='Kommentar hinzufügen' icon='chat' size='md' flat='' rounded='' color='grey' @click='createComment(question)')
</template>
<script>
export default {

  props: {
    question: {
      type: Object,
      required: true
    },
    isNoInfoblock: {
      type: Function,
      required: true
    },
    findOrCreateAwnser: {
      type: Function,
      required: true
    },
    textFocus: {
      type: Function,
      required: true
    },
    textBlur: {
      type: Function,
      required: true
    }
  },

  methods: {
    isCommentable (question = this.question) {
      return question.is_commentable && this.isNoInfoblock(question)
    },

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

    focusCommentInput () {
      this.$refs.user_comment.focus()
    }

  }
}
</script>
