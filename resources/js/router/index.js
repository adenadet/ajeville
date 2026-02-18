import { createRouter, createWebHistory } from 'vue-router'

// Auto-load all route modules
const modules = import.meta.glob('./modules/*.js', { eager: true })

let routes = []

Object.values(modules).forEach(module => {
    routes = routes.concat(module.default)
})

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
