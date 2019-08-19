function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [

  // Unauthentificated
  { path: '/', name: 'welcome', component: page('guest/welcome.vue') },
  { path: '/auth', name: 'auth', component: page('guest/auth/auth.vue') },
  { path: '/pan', name: 'pan', component: page('guest/auth/pan.vue') },
  { path: '/login', name: 'login', component: page('guest/auth/login.vue') },
  { path: '/register', name: 'register', component: page('guest/auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('guest/auth/password/email.vue') },
  { path: '/password/reset/:token', name: 'password.reset', component: page('guest/auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('guest/auth/verification/verify.vue') },
  { path: '/email/resend', name: 'verification.resend', component: page('guest/auth/verification/resend.vue') },

  // Authentificated
  { path: '/',
    component: page('user/index.vue'),
    children: [
      // Landing Pages
      { path: '/home', name: 'home', component: page('user/home.vue') },
      { path: '/faq', name: 'faq', component: page('user/faq.vue') },

      // Account Settings
      { path: '/surveys', name: 'surveys', component: page('user/surveys/surveys.vue') },
      { path: '/profile', name: 'profile', component: page('user/profile/profile.vue') }

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
    component: page('user/backend/index.vue'),
    children: [
      { path: 'users', name: 'backend.users', component: page('user/backend/users.vue') },
      { path: 'groups', name: 'backend.groups', component: page('user/backend/groups.vue') },
      { path: 'surveys', name: 'backend.surveys', component: page('user/backend/surveys.vue') },
      { path: 'statistics', name: 'backend.statistics', component: page('user/backend/statistics.vue') }
    ]
  },

  // All
  { path: '*', component: page('all/errors/404.vue') }
]
