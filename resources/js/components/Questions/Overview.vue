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
      <q-page class="q-mb-xl">
        <div>
          {{ oSurvey.desc_long || oSurvey.desc_short }}
        </div>
        <ul>
          <li v-for="question in oSurvey.questions" v-bind:key="question.id">
            <router-link :to="getQuestionHash(question)">
              {{ question.title }}
            </router-link>
          </li>
        </ul>
      </q-page>
    </q-page-container>
    <q-footer bordered class="bg-white text-primary">
      <q-toolbar>
        <q-btn flat icon="keyboard_arrow_left" :to="'/'" />
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
  }
}
</script>
