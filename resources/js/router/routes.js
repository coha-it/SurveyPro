function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [

  // Unauthentificated
  { path: '/', name: 'welcome', component: page('_guest/welcome.vue') },
  { path: '/auth', name: 'auth', component: page('_guest/auth/auth.vue') },
  { path: '/pan', name: 'pan', component: page('_guest/auth/pan.vue') },
  { path: '/login', name: 'login', component: page('_guest/auth/login.vue') },
  { path: '/register', name: 'register', component: page('_guest/auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('_guest/auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('_guest/auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('_guest/auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('_guest/auth/verification/resend.vue') },

  // Authentificated
  { path: '/',
    component: page('index.vue'),
    meta: {
      middleware: 'auth'
    },
    children: [
      // Landing Pages
      { path: '/home', name: 'home', component: page('home.vue') },
      { path: '/faq', name: 'faq', component: page('faq.vue') },

      // Account Settings
      { path: '/surveys', name: 'surveys', component: page('surveys/surveys.vue') },
      { path: '/profile', name: 'profile', component: page('profile/profile.vue') }

      // Old Settings
      // { path: '/settings',
      //   component: page('settings/index.vue'),
      //   children: [
      //     { path: '', redirect: { name: 'settings.profile' } },
      //     { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      //     { path: 'password', name: 'settings.password', component: page('settings/password.vue') }
      //   ]
      // }

    ]
  },

  // Managment
  { path: '/backend',
    component: page('backend/index.vue'),
    children: [
      {
        path: 'users',
        name: 'backend.users',
        component: page('backend/users.vue')
      }, {
        path: 'groups',
        name: 'backend.groups',
        component: page('backend/groups.vue')
      }, {
        path: 'surveys',
        name: 'backend.surveys',
        component: page('backend/surveys.vue')
      }, {
        path: 'statistics',
        name: 'backend.statistics',
        component: page('backend/statistics.vue')
      }
    ]
  },

  // All
  { path: '*', component: page('_all/errors/404.vue') }
]
