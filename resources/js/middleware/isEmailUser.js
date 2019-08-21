import store from '~/store'

export default (to, from, next) => {
  if (true | store.getters['auth/user'].role !== 'admin') {
    next({ name: 'home' })
  } else {
    next()
  }
}
