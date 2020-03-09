<template>
  <div>
    <h2>Overview - Question List</h2>
    <ul>
      <li v-for="question in oSurvey.questions" v-bind:key="question.id">
        <router-link :to="getQuestionHash(question)">
          {{ question.title }}
        </router-link>
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: [
    'hashes',
    'oSurvey',
    'getQuestionHash'
  ]
}
</script>
