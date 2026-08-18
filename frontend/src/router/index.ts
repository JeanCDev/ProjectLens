import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      component: () => import('@/layouts/DefaultLayout.vue'),
      children: [
        {
          path: '',
          name: 'dashboard',
          component: () => import('@/pages/DashboardView.vue'),
          meta: { title: 'Dashboard' },
        },
        {
          path: 'projects',
          name: 'projects',
          component: () => import('@/pages/ProjectsView.vue'),
          meta: { title: 'Projects' },
        },
        {
          path: 'projects/:id',
          name: 'project-details',
          component: () => import('@/pages/ProjectDetailsView.vue'),
          meta: { title: 'Project Details' },
        },
        {
          path: 'endpoints',
          name: 'endpoints',
          component: () => import('@/pages/EndpointsView.vue'),
          meta: { title: 'Endpoints' },
        },
        {
          path: 'endpoints/:id',
          name: 'endpoint-details',
          component: () => import('@/pages/EndpointDetailsView.vue'),
          meta: { title: 'Endpoint Details' },
        },
        {
          path: 'releases',
          name: 'releases',
          component: () => import('@/pages/ReleasesView.vue'),
          meta: { title: 'Releases' },
        },
        {
          path: 'environments',
          name: 'environments',
          component: () => import('@/pages/EnvironmentsView.vue'),
          meta: { title: 'Environments' },
        },
        {
          path: 'settings',
          name: 'settings',
          component: () => import('@/pages/SettingsView.vue'),
          meta: { title: 'Settings' },
        },
      ],
    },
    {
      path: '/:pathMatch(.*)*',
      redirect: { name: 'dashboard' },
    },
  ],
})

router.afterEach((to) => {
  const title = typeof to.meta.title === 'string' ? to.meta.title : 'Dashboard'
  document.title = `${title} · ProjectLens`
})

export default router