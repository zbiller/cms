<?php

/**
 | ---------------------------------------------------------------------------------------------------------------------
 | Dashboard
 | ---------------------------------------------------------------------------------------------------------------------
 */
/* Home (Dashboard) */
Breadcrumbs::register('admin', function($breadcrumbs) {
    $breadcrumbs->push('Home', route('admin'));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Uploads
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Layouts */
Breadcrumbs::register('admin.uploads.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Uploads', route('admin.uploads.index'));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
 | ---------------------------------------------------------------------------------------------------------------------
 | Pages
 | ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Pages */
Breadcrumbs::register('admin.pages.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Pages', route('admin.pages.index'));
});

/* Home > Pages > Add */
Breadcrumbs::register('admin.pages.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.pages.index');
    $breadcrumbs->push('Add', route('admin.pages.create'));
});

/* Home > Pages > Edit */
Breadcrumbs::register('admin.pages.edit', function($breadcrumbs, $page) {
    $breadcrumbs->parent('admin.pages.index');
    $breadcrumbs->push('Edit', route('admin.pages.edit', $page));
});

/* Home > Pages > Edit > Draft */
Breadcrumbs::register('admin.pages.draft', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.pages.edit', $draft->draftable);
    $breadcrumbs->push('Draft', route('admin.pages.draft', $draft));
});

/* Home > Pages > Edit > Revision */
Breadcrumbs::register('admin.pages.revision', function($breadcrumbs, $revision) {
    $breadcrumbs->parent('admin.pages.edit', $revision->revisionable);
    $breadcrumbs->push('Revision', route('admin.pages.revision', $revision));
});

/* Home > Pages > Deleted */
Breadcrumbs::register('admin.pages.deleted', function($breadcrumbs) {
    $breadcrumbs->parent('admin.pages.index');
    $breadcrumbs->push('Deleted', route('admin.pages.deleted'));
});

/* Home > Pages > Drafts */
Breadcrumbs::register('admin.pages.drafts', function($breadcrumbs) {
    $breadcrumbs->parent('admin.pages.index');
    $breadcrumbs->push('Drafts', route('admin.pages.drafts'));
});

/* Home > Pages > Drafts > Draft */
Breadcrumbs::register('admin.pages.limbo', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.pages.drafts');
    $breadcrumbs->push('Draft', route('admin.pages.draft', $draft));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Menus
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Locations */
Breadcrumbs::register('admin.menus.locations', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Locations', route('admin.menus.locations'));
});

/* Home > Menus */
Breadcrumbs::register('admin.menus.index', function($breadcrumbs, $location) {
    $breadcrumbs->parent('admin.menus.locations');
    $breadcrumbs->push('Menus', route('admin.menus.index', $location));
});

/* Home > Menus > Add */
Breadcrumbs::register('admin.menus.create', function($breadcrumbs, $location) {
    $breadcrumbs->parent('admin.menus.index', $location);
    $breadcrumbs->push('Add', route('admin.menus.create', $location));
});

/* Home > Menus > Edit */
Breadcrumbs::register('admin.menus.edit', function($breadcrumbs, $location, $menu) {
    $breadcrumbs->parent('admin.menus.index', $location);
    $breadcrumbs->push('Edit', route('admin.menus.edit', ['location' => $location, 'menu' => $menu]));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Blocks
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Blocks */
Breadcrumbs::register('admin.blocks.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Blocks', route('admin.blocks.index'));
});

/* Home > Blocks > Add */
Breadcrumbs::register('admin.blocks.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.blocks.index');
    $breadcrumbs->push('Add', route('admin.blocks.create'));
});

/* Home > Blocks > Edit */
Breadcrumbs::register('admin.blocks.edit', function($breadcrumbs, $block) {
    $breadcrumbs->parent('admin.blocks.index');
    $breadcrumbs->push('Edit', route('admin.blocks.edit', $block));
});

/* Home > Blocks > Edit > Draft */
Breadcrumbs::register('admin.blocks.draft', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.blocks.edit', $draft->draftable);
    $breadcrumbs->push('Draft', route('admin.blocks.draft', $draft));
});

/* Home > Blocks > Edit > Revision */
Breadcrumbs::register('admin.blocks.revision', function($breadcrumbs, $revision) {
    $breadcrumbs->parent('admin.blocks.edit', $revision->revisionable);
    $breadcrumbs->push('Revision', route('admin.blocks.revision', $revision));
});

/* Home > Blocks > Deleted */
Breadcrumbs::register('admin.blocks.deleted', function($breadcrumbs) {
    $breadcrumbs->parent('admin.blocks.index');
    $breadcrumbs->push('Deleted', route('admin.blocks.deleted'));
});

/* Home > Blocks > Drafts */
Breadcrumbs::register('admin.blocks.drafts', function($breadcrumbs) {
    $breadcrumbs->parent('admin.blocks.index');
    $breadcrumbs->push('Drafts', route('admin.blocks.drafts'));
});

/* Home > Blocks > Drafts > Draft */
Breadcrumbs::register('admin.blocks.limbo', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.blocks.drafts');
    $breadcrumbs->push('Draft', route('admin.blocks.draft', $draft));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Layouts
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Layouts */
Breadcrumbs::register('admin.layouts.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Layouts', route('admin.layouts.index'));
});

/* Home > Layouts > Add */
Breadcrumbs::register('admin.layouts.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.layouts.index');
    $breadcrumbs->push('Add', route('admin.layouts.create'));
});

/* Home > Layouts > Edit */
Breadcrumbs::register('admin.layouts.edit', function($breadcrumbs, $layout) {
    $breadcrumbs->parent('admin.layouts.index');
    $breadcrumbs->push('Edit', route('admin.layouts.edit', $layout));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Emails
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Emails */
Breadcrumbs::register('admin.emails.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Emails', route('admin.emails.index'));
});

/* Home > Emails > Add */
Breadcrumbs::register('admin.emails.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.emails.index');
    $breadcrumbs->push('Add', route('admin.emails.create'));
});

/* Home > Emails > Edit */
Breadcrumbs::register('admin.emails.edit', function($breadcrumbs, $email) {
    $breadcrumbs->parent('admin.emails.index');
    $breadcrumbs->push('Edit', route('admin.emails.edit', $email));
});

/* Home > Emails > Edit > Draft */
Breadcrumbs::register('admin.emails.draft', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.emails.edit', $draft->draftable);
    $breadcrumbs->push('Draft', route('admin.emails.draft', $draft));
});

/* Home > Emails > Edit > Revision */
Breadcrumbs::register('admin.emails.revision', function($breadcrumbs, $revision) {
    $breadcrumbs->parent('admin.emails.edit', $revision->revisionable);
    $breadcrumbs->push('Revision', route('admin.emails.revision', $revision));
});

/* Home > Emails > Deleted */
Breadcrumbs::register('admin.emails.deleted', function($breadcrumbs) {
    $breadcrumbs->parent('admin.emails.index');
    $breadcrumbs->push('Deleted', route('admin.emails.deleted'));
});

/* Home > Emails > Drafts */
Breadcrumbs::register('admin.emails.drafts', function($breadcrumbs) {
    $breadcrumbs->parent('admin.emails.index');
    $breadcrumbs->push('Drafts', route('admin.emails.drafts'));
});

/* Home > Emails > Drafts > Draft */
Breadcrumbs::register('admin.emails.limbo', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.emails.drafts');
    $breadcrumbs->push('Draft', route('admin.emails.draft', $draft));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Users
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Users */
Breadcrumbs::register('admin.users.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Users', route('admin.users.index'));
});

/* Home > Users > Add */
Breadcrumbs::register('admin.users.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.users.index');
    $breadcrumbs->push('Add', route('admin.users.create'));
});

/* Home > Users > Edit */
Breadcrumbs::register('admin.users.edit', function($breadcrumbs, $user) {
    $breadcrumbs->parent('admin.users.index');
    $breadcrumbs->push('Edit', route('admin.users.edit', $user));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Admins
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Admins */
Breadcrumbs::register('admin.admins.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Admins', route('admin.admins.index'));
});

/* Home > Admins > Add */
Breadcrumbs::register('admin.admins.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.admins.index');
    $breadcrumbs->push('Add', route('admin.admins.create'));
});

/* Home > Admins > Edit */
Breadcrumbs::register('admin.admins.edit', function($breadcrumbs, $admin) {
    $breadcrumbs->parent('admin.admins.index');
    $breadcrumbs->push('Edit', route('admin.admins.edit', $admin));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Roles
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Roles */
Breadcrumbs::register('admin.roles.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Roles', route('admin.roles.index'));
});

/* Home > Roles > Add */
Breadcrumbs::register('admin.roles.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.roles.index');
    $breadcrumbs->push('Add', route('admin.roles.create'));
});

/* Home > Roles > Edit */
Breadcrumbs::register('admin.roles.edit', function($breadcrumbs, $admin) {
    $breadcrumbs->parent('admin.roles.index');
    $breadcrumbs->push('Edit', route('admin.roles.edit', $admin));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Activity
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Roles */
Breadcrumbs::register('admin.activity.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Activity', route('admin.activity.index'));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Countries
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Countries */
Breadcrumbs::register('admin.countries.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Countries', route('admin.countries.index'));
});

/* Home > Countries > Add */
Breadcrumbs::register('admin.countries.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.countries.index');
    $breadcrumbs->push('Add', route('admin.countries.create'));
});

/* Home > Countries > Edit */
Breadcrumbs::register('admin.countries.edit', function($breadcrumbs, $admin) {
    $breadcrumbs->parent('admin.countries.index');
    $breadcrumbs->push('Edit', route('admin.countries.edit', $admin));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| States
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > States */
Breadcrumbs::register('admin.states.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('States', route('admin.states.index'));
});

/* Home > States > Add */
Breadcrumbs::register('admin.states.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.states.index');
    $breadcrumbs->push('Add', route('admin.states.create'));
});

/* Home > States > Edit */
Breadcrumbs::register('admin.states.edit', function($breadcrumbs, $admin) {
    $breadcrumbs->parent('admin.states.index');
    $breadcrumbs->push('Edit', route('admin.states.edit', $admin));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Cities
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Cities */
Breadcrumbs::register('admin.cities.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Cities', route('admin.cities.index'));
});

/* Home > Cities > Add */
Breadcrumbs::register('admin.cities.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.cities.index');
    $breadcrumbs->push('Add', route('admin.cities.create'));
});

/* Home > Cities > Edit */
Breadcrumbs::register('admin.cities.edit', function($breadcrumbs, $admin) {
    $breadcrumbs->parent('admin.cities.index');
    $breadcrumbs->push('Edit', route('admin.cities.edit', $admin));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Settings
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Settings > General */
Breadcrumbs::register('admin.settings.general', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Settings');
    $breadcrumbs->push('General', route('admin.settings.general'));
});

/* Home > Settings > Analytics */
Breadcrumbs::register('admin.settings.analytics', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Settings');
    $breadcrumbs->push('Analytics', route('admin.settings.analytics'));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */