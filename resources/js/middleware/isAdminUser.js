import store from '~/store'

export default async (to, from, next) => {
  var u = store.getters['auth/user']
  if (u && u.right && u.right.admin === 1) {
    next()
  } else {
    next({ name: 'home' })
  }
}
