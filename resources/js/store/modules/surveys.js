import axios from 'axios'
import * as types from '../mutation-types'

// state
export const state = {
  surveysAllowed: null,
  surveyAllowed: null
}

// getters
export const getters = {
  surveysAllowed: state => state.surveysAllowed,
  surveyAllowed: state => state.surveyAllowed
}

// mutations
export const mutations = {
  [types.FETCH_SURVEYS_ALLOWED_SUCCESS] (state, { surveysAllowed }) {
    state.surveysAllowed = surveysAllowed
  },

  [types.FETCH_SURVEY_ALLOWED_SUCCESS] (state, { surveyAllowed }) {
    state.surveyAllowed = surveyAllowed
  }
}

// actions
export const actions = {
  async fetchSurveysAllowed ({ commit }) {
    try {
      const { data } = await axios.get('/api/backend/surveys-allowed')
      commit(types.FETCH_SURVEYS_ALLOWED_SUCCESS, { surveysAllowed: data })
    } catch (e) {
      commit(types.FETCH_SURVEYS_ALLOWED_FAILURE)
    }
  },

  async fetchSurveyAllowed ({ commit }, id) {
    try {
      const { data } = await axios.get('/api/backend/survey-allowed', { params: { id: id } } )
      commit(types.FETCH_SURVEY_ALLOWED_SUCCESS, { surveyAllowed: data })
      return 'jo'
    } catch (e) {
      commit(types.FETCH_SURVEY_ALLOWED_FAILURE)
      return 'jo'
    }
  },

  addAllowedSurveyToGroup ({ commit }, payload) {
    axios.post('/api/backend/add-survey-to-group', payload)
  },

  removeAllowedSurveyFromGroup ({ commit }, payload) {
    axios.post('/api/backend/remove-survey-from-group', payload)
  },

  createSurvey ({ commit }, payload) {
    try {
      return axios.post('/api/backend/create-survey', payload)
    } catch (e) {
      return e
    }
  },

  updateSurvey ({ commit }, payload) {
    try {
      return axios.patch('/api/backend/update-allowed-survey', payload)
    } catch (e) {
      return e
    }
  },

  deleteSurvey ({ commit }, payload) {
    try {
      return axios.patch('/api/backend/delete-allowed-survey', payload)
    } catch (e) {
      return e
    }
  },

  deleteQuestions ({ commit }, payload) {
    try {
      return axios.patch('/api/backend/delete-questions', payload )
    } catch (e) {
      return e
    }
  }

}
