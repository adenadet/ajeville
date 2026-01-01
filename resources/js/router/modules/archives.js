export default[
    {path: '/archives',                                         component: () => import ('@/archives/Dashboard.vue')}, //ArchiveDashboard},
    {path: '/archives/dashboard',                               component: () => import ('@/archives/Dashboard.vue')}, //ArchiveDashboard},
    {path: '/archives/categories',                              component: () => import ('@/archives/Categories.vue')}, //ArchiveCategories},
    {path: '/archives/categories/:id',                          component: () => import ('@/archives/Category.vue')}, //ArchiveCategory},
    {path: '/archives/documents',                               component: () => import ('@/archives/Documents.vue')}, //ArchiveDocuments},
    {path: '/archives/documents/:id',                           component: () => import ('@/archives/Document.vue')}, //ArchiveDocument},
];