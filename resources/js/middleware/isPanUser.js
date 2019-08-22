import store from '~/store'

export default async (to, from, next) => {
  var u = store.getters['auth/user']
  if (u && u.pan && u.pan.is_pan_user === 1) {
    next()
  } else {
    next({ name: 'home' })
  }
}
