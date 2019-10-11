import axios from 'axios'
import * as types from '../mutation-types'

// state
export const state = {
  usersCreated: null,
  groupsModerating: null
}

// getters
export const getters = {
  usersCreated: state => state.usersCreated,
  groupsModerating: state => state.groupsModerating
}

// mutations
export const mutations = {
  [types.FETCH_USERS_CREATED_SUCCESS] (state, { usersCreated }) {
    state.usersCreated = usersCreated
  },

  [types.FETCH_GROUPS_MODERATING_SUCCESS] (state, { groupsModerating }) {
    state.groupsModerating = groupsModerating
  }
}

// actions
export const actions = {
  async fetchUsersCreated ({ commit }) {
    try {
      const { data } = await axios.get('/api/users-created')
      commit(types.FETCH_USERS_CREATED_SUCCESS, { usersCreated: data })
    } catch (e) {
      commit(types.FETCH_USERS_CREATED_FAILURE)
    }
  },
  async fetchGroupsModerating ({ commit }) {
    try {
      const { data } = await axios.get('/api/groups-moderating')
      commit(types.FETCH_GROUPS_MODERATING_SUCCESS, { groupsModerating: data })
    } catch (e) {
      commit(types.FETCH_GROUPS_MODERATING_FAILURE)
    }
  },

  addCreatedUserToGroup ({ commit }, payload) {
    axios.post('/api/add-user-to-group', payload)
  },

  removeCreatedUserFromGroup ({ commit }, payload) {
    axios.post('/api/remove-user-from-group', payload)
  },

  updateUsers ({ commit }, payload) {
    try {
      return axios.patch('/api/update-created-users', payload )
    } catch (e) {
      return e
    }
  },

  createUsers ({ commit }, payload) {
    try {
      return axios.post('/api/create-users', payload )
    } catch (e) {
      return e
    }
  },

  deleteUsers ({ commit }, payload) {
    try {
      return axios.patch('/api/delete-created-user', payload )
    } catch (e) {
      return e
    }
  }

}
