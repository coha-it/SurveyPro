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
      Questions hier ey
      <q-btn label="back" :to="hashes.overview" />
      <template v-for="question in oSurvey.questions">
        <!-- Single Question here -->
        <div v-if="questionIsViewed(question)" v-bind:key="question.id">
        {{ question }}
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
      oSurvey: null,
    }
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
  },

  methods: {
    copyObject (obj) {
      if (typeof obj != "undefined") {
        var copy = JSON.parse(JSON.stringify(obj));
        if (copy) return copy;
      }
    },
    findById (arr, id) {
      return arr.find(x => x.id === id)
    },
    changedHash (hash) {
      var h = hash || window.location.hash
      var id = h.split('#')[1]

      switch (h) {
        case h === '':
        case h === '#':
          window.location.hash = this.hashes.overview
          break;
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
    overviewIsViewed () {
      return this.$route.hash == this.hashes.overview
    }
  }

}
</script>

<style lang="scss">
.progress {
  display: none;
}
</style>
