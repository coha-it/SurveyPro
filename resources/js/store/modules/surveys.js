import axios from 'axios'
import * as types from '../mutation-types'

// state
export const state = {
  surveysMembering: null,
  surveyFillable: null,
  backendSurveysAllowed: null,
  backendSurveyAllowed: null
}

// getters
export const getters = {
  surveysMembering: state => state.surveysMembering,
  surveyFillable: state => state.surveyFillable,
  backendSurveysAllowed: state => state.backendSurveysAllowed,
  backendSurveyAllowed: state => state.backendSurveyAllowed
}

// mutations
export const mutations = {

  [types.FETCH_SURVEYS_MEMBERING_SUCCESS] (state, { surveysMembering }) {
    state.surveysMembering = surveysMembering
  },

  [types.FETCH_SURVEY_FILLABLE_SUCCESS] (state, { surveyFillable }) {
    state.surveyFillable = surveyFillable
  },

  [types.FETCH_BACKEND_SURVEYS_ALLOWED_SUCCESS] (state, { backendSurveysAllowed }) {
    state.backendSurveysAllowed = backendSurveysAllowed
  },

  [types.FETCH_BACKEND_SURVEY_ALLOWED_SUCCESS] (state, { backendSurveyAllowed }) {
    state.backendSurveyAllowed = backendSurveyAllowed
  }
}

// actions
export const actions = {

  // Frontend Surveys
  async fetchSurveysMembering ({ commit }) {
    try {
      const { data } = await axios.get('/api/surveys-membering')
      commit(types.FETCH_SURVEYS_MEMBERING_SUCCESS, { surveysMembering: data })
    } catch (e) {
      commit(types.FETCH_SURVEYS_MEMBERING_FAILURE)
    }
  },

  async fetchSurveyFillable ({ commit }, id) {
    try {
      const { data } = await axios.get('/api/survey-fillable', { params: { id: id } })
      commit(types.FETCH_SURVEY_FILLABLE_SUCCESS, { surveyFillable: data })
    } catch (e) {
      commit(types.FETCH_SURVEY_FILLABLE_FAILURE)
    }
  },

  // Backend Surveys
  async fetchBackendSurveysAllowed ({ commit }) {
    try {
      const { data } = await axios.get('/api/backend/surveys-allowed')
      commit(types.FETCH_BACKEND_SURVEYS_ALLOWED_SUCCESS, { backendSurveysAllowed: data })
    } catch (e) {
      commit(types.FETCH_BACKEND_SURVEYS_ALLOWED_FAILURE)
    }
  },

  async fetchBackendSurveyAllowed ({ commit }, id) {
    try {
      const { data } = await axios.get('/api/backend/survey-allowed', { params: { id: id } })
      commit(types.FETCH_BACKEND_SURVEY_ALLOWED_SUCCESS, { backendSurveyAllowed: data })
    } catch (e) {
      commit(types.FETCH_BACKEND_SURVEY_ALLOWED_FAILURE)
    }
  },

  addAllowedSurveyToGroup ({ commit }, payload) {
    axios.post('/api/backend/add-survey-to-group', payload)
  },

  removeAllowedSurveyFromGroup ({ commit }, payload) {
    axios.post('/api/backend/remove-survey-from-group', payload)
  },

  updateOrCreateAwnser ({ commit }, payload) {
    try {
      return axios.post('/api/update-or-create-awnser', payload)
    } catch (e) {
      return e
    }
  },

  finishSurvey ({ commit }, payload) {
    try {
      return axios.post('/api/finish-survey', payload)
    } catch (e) {
      return e
    }
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
