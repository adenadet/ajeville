const ArchiveBackups                       = () => import('../../archives/Backups.vue');
const ArchiveCategories                    = () => import('../../archives/Categories.vue');
const ArchiveCategory                      = () => import('../../archives/Category.vue');
const ArchiveDashboard                     = () => import('../../archives/Dashboard.vue');
const ArchiveDocuments                     = () => import('../../archives/Documents.vue');
const ArchiveDocument                      = () => import('../../archives/Document.vue');

    const ArchiveDetailBackupList          = () => import('../../archives/details/BackupList.vue');
    const ArchiveDetailCategoryList        = () => import('../../archives/details/CategoryList.vue');
    const ArchiveDetailDocumentList        = () => import('../../archives/details/DocumentList.vue');

    const ArchiveFormBackup                = () => import('../../archives/forms/Backup.vue');
    const ArchiveFormCategory              = () => import('../../archives/forms/Category.vue');
    const ArchiveFormDocument              = () => import('../../archives/forms/Document.vue');
    const ArchiveFormDocumentSearch        = () => import('../../archives/forms/DocumentSearch.vue');


export default[
    {path: '/archives',                                         component: ArchiveDashboard},
    {path: '/archives/dashboard',                               component: ArchiveDashboard},
    {path: '/archives/categories',                              component: ArchiveCategories},
    {path: '/archives/categories/:id',                          component: ArchiveCategory},
    {path: '/archives/documents',                               component: ArchiveDocuments},
    {path: '/archives/documents/:id',                           component: ArchiveDocument},
];