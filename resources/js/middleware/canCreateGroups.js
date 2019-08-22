import store from '~/store'

export default async (to, from, next) => {
  var u = store.getters['auth/user']
  console.log(u)
  if (u && u.right && u.right.create_groups === 1) {
    next()
  } else {
    next({ name: 'home' })
  }
}
