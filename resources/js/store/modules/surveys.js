import axios from 'axios'
import * as types from '../mutation-types'

// state
export const state = {
  surveysCreated: null,
  surveyCreated: null
}

// getters
export const getters = {
  surveysCreated: state => state.surveysCreated,
  surveyCreated: state => state.surveyCreated
}

// mutations
export const mutations = {
  [types.FETCH_SURVEYS_CREATED_SUCCESS] (state, { surveysCreated }) {
    state.surveysCreated = surveysCreated
  },
  [types.FETCH_SURVEY_CREATED_SUCCESS] (state, { surveyCreated }) {
    state.surveyCreated = surveyCreated
  }
}

// actions
export const actions = {
  async fetchSurveysCreated ({ commit }) {
    try {
      const { data } = await axios.get('/api/backend/surveys-created')
      commit(types.FETCH_SURVEYS_CREATED_SUCCESS, { surveysCreated: data })
    } catch (e) {
      commit(types.FETCH_SURVEYS_CREATED_FAILURE)
    }
  },

  async fetchSurveyCreated ({ commit }, id) {
    try {
      const { data } = await axios.get('/api/backend/survey-created', { params: { id: id } } )
      commit(types.FETCH_SURVEY_CREATED_SUCCESS, { surveyCreated: data })
      return 'jo'
    } catch (e) {
      commit(types.FETCH_SURVEY_CREATED_FAILURE)
      return 'jo'
    }
  },

  addCreatedSurveyToGroup ({ commit }, payload) {
    axios.post('/api/backend/add-survey-to-group', payload)
  },

  removeCreatedSurveyFromGroup ({ commit }, payload) {
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
      return axios.patch('/api/backend/update-created-survey', payload)
    } catch (e) {
      return e
    }
  },

  deleteSurvey ({ commit }, payload) {
    try {
      return axios.patch('/api/backend/delete-created-survey', payload)
    } catch (e) {
      return e
    }
  }

}
