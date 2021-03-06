export default {
    'portfolio': require('./controllers/portfolio'),
    'cmv-jobs': require('./controllers/cmv-jobs'),

    'admin/project-modal': require('./controllers/admin/project-modal'),

    'project/briefs': require('./controllers/project/briefs'),
    'project/brief-view': require('./controllers/project/brief-view'),
    'project/brief-edit': require('./controllers/project/brief-edit'),
    'project/dashboard': require('./controllers/project/dashboard'),
    'project/files': require('./controllers/project/files'),
    'project/invoices': require('./controllers/project/invoices'),
    'project/invoice-edit': require('./controllers/project/invoice-edit'),
    'project/invoice': require('./controllers/project/invoice'),
    'project/todos': require('./controllers/project/todos'),
    'project/todo': require('./controllers/project/todo'),
    'project/new': require('./controllers/project/new'),
    'project/team': require('./controllers/project/team'),
    'project/news': require('./controllers/project/news'),

    'misc/uploadcare': require('./controllers/misc/uploadcare'),
    'misc/invitations': require('./controllers/misc/invitations'),
    'register/invitation': require('./controllers/register/invitation')
};