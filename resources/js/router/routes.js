function page (path) {
  return () => import(/* webpackChunkName: '' */ `~/pages/${path}`).then(m => m.default || m)
}

export default [

  // Unauthentificated
  { path: '/', name: 'welcome', component: page('_guest/welcome.vue') },
  { path: '/auth', name: 'auth', component: page('_guest/auth/auth.vue') },
  { path: '/pan', name: 'pan', component: page('_guest/auth/pan.vue') },
  { path: '/pan*', component: page('_guest/auth/pan.vue') },
  { path: '/p/:pan', redirect: { path: '/pan/:pan' } },
  { path: '/login', name: 'login', component: page('_guest/auth/login.vue') },
  { path: '/register', name: 'register', component: page('_guest/auth/register.vue') },
  { path: '/password/reset', name: 'password.request', component: page('_guest/auth/password/email.vue'), meta: { title: 'reset_password' } },
  { path: '/password/reset/:token', name: 'password.reset', component: page('_guest/auth/password/reset.vue') },
  { path: '/email/verify/:id', name: 'verification.verify', component: page('_guest/auth/verification/verify.vue'), meta: { title: 'verify_email' } },
  { path: '/email/resend', name: 'verification.resend', component: page('_guest/auth/verification/resend.vue'), meta: { title: 'verify_email' } },

  // Authentificated
  { path: '/',
    component: page('index.vue'),
    meta: {
      middleware: 'auth'
    },
    children: [
      // Landing Pages
      { path: '/home', name: 'home', component: page('home.vue'), meta: { title: 'home' } },
      { path: '/faq', name: 'faq', component: page('faq.vue'), meta: { title: 'FAQ' } },

      // Surveys
      { path: '/surveys', name: 'surveys', component: page('surveys/surveys.vue') },

      // Account Settings
      { path: '/profile', name: 'profile', component: page('profile/profile.vue'), meta: { title: 'profile.title' } }

      // Old Settings
      // { path: '/settings',
      //   component: page('settings/index.vue'),
      //   children: [
      //     { path: '', redirect: { name: 'settings.profile' } },
      //     { path: 'profile', name: 'settings.profile', component: page('settings/profile.vue') },
      //     { path: 'password', name: 'settings.password', component: page('settings/password.vue'), meta: {title: 'settings'} }
      //   ]
      // }

    ]
  },

  {
    path: '/survey/:id',
    name: 'survey',
    component: page('surveys/survey.vue'),
    meta: {
      middleware: 'auth'
    }
  },

  // Managment / Verwaltung
  { path: '/backend',
    component: page('backend/index.vue'),
    meta: {
      middleware: 'auth'
    },
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
        component: page('backend/surveys/backend_surveys.vue')
      }, {
        path: 'survey/:id',
        name: 'backend.survey',
        component: page('backend/surveys/backend_survey.vue')
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
