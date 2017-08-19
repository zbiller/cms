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
| Orders
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Orders */
Breadcrumbs::register('admin.orders.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Orders', route('admin.orders.index'));
});

/* Home > Orders > Add */
Breadcrumbs::register('admin.orders.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.orders.index');
    $breadcrumbs->push('Add', route('admin.orders.create'));
});

/* Home > Orders > Edit */
Breadcrumbs::register('admin.orders.edit', function($breadcrumbs, $order) {
    $breadcrumbs->parent('admin.orders.index');
    $breadcrumbs->push('Edit', route('admin.orders.edit', $order));
});

/* Home > Orders > View */
Breadcrumbs::register('admin.orders.view', function($breadcrumbs, $order) {
    $breadcrumbs->parent('admin.orders.index');
    $breadcrumbs->push('View', route('admin.orders.view', $order));
});

/* Home > Orders > Deleted */
Breadcrumbs::register('admin.orders.deleted', function($breadcrumbs) {
    $breadcrumbs->parent('admin.orders.index');
    $breadcrumbs->push('Deleted', route('admin.orders.deleted'));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Carts
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Carts */
Breadcrumbs::register('admin.carts.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Carts', route('admin.carts.index'));
});

/* Home > Carts > View */
Breadcrumbs::register('admin.carts.view', function($breadcrumbs, $cart) {
    $breadcrumbs->parent('admin.carts.index');
    $breadcrumbs->push('View', route('admin.carts.view', $cart));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Products
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Products */
Breadcrumbs::register('admin.products.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Products', route('admin.products.index'));
});

/* Home > Products > Add */
Breadcrumbs::register('admin.products.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.products.index');
    $breadcrumbs->push('Add', route('admin.products.create'));
});

/* Home > Products > Edit */
Breadcrumbs::register('admin.products.edit', function($breadcrumbs, $product) {
    $breadcrumbs->parent('admin.products.index');
    $breadcrumbs->push('Edit', route('admin.products.edit', $product));
});

/* Home > Products > Edit > Draft */
Breadcrumbs::register('admin.products.draft', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.products.edit', $draft->draftable);
    $breadcrumbs->push('Draft', route('admin.products.draft', $draft));
});

/* Home > Products > Edit > Revision */
Breadcrumbs::register('admin.products.revision', function($breadcrumbs, $revision) {
    $breadcrumbs->parent('admin.products.edit', $revision->revisionable);
    $breadcrumbs->push('Revision', route('admin.products.revision', $revision));
});

/* Home > Products > Deleted */
Breadcrumbs::register('admin.products.deleted', function($breadcrumbs) {
    $breadcrumbs->parent('admin.products.index');
    $breadcrumbs->push('Deleted', route('admin.products.deleted'));
});

/* Home > Products > Drafts */
Breadcrumbs::register('admin.products.drafts', function($breadcrumbs) {
    $breadcrumbs->parent('admin.products.index');
    $breadcrumbs->push('Drafts', route('admin.products.drafts'));
});

/* Home > Products > Drafts > Draft */
Breadcrumbs::register('admin.products.limbo', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.products.drafts');
    $breadcrumbs->push('Draft', route('admin.products.draft', $draft));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Categories
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Categories */
Breadcrumbs::register('admin.product_categories.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Categories', route('admin.product_categories.index'));
});

/* Home > Categories > Add */
Breadcrumbs::register('admin.product_categories.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.product_categories.index');
    $breadcrumbs->push('Add', route('admin.product_categories.create'));
});

/* Home > Categories > Edit */
Breadcrumbs::register('admin.product_categories.edit', function($breadcrumbs, $category) {
    $breadcrumbs->parent('admin.product_categories.index');
    $breadcrumbs->push('Edit', route('admin.product_categories.edit', $category));
});

/* Home > Categories > Edit > Draft */
Breadcrumbs::register('admin.product_categories.draft', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.product_categories.edit', $draft->draftable);
    $breadcrumbs->push('Draft', route('admin.product_categories.draft', $draft));
});

/* Home > Categories > Edit > Revision */
Breadcrumbs::register('admin.product_categories.revision', function($breadcrumbs, $revision) {
    $breadcrumbs->parent('admin.product_categories.edit', $revision->revisionable);
    $breadcrumbs->push('Revision', route('admin.product_categories.revision', $revision));
});

/* Home > Categories > Deleted */
Breadcrumbs::register('admin.product_categories.deleted', function($breadcrumbs) {
    $breadcrumbs->parent('admin.product_categories.index');
    $breadcrumbs->push('Deleted', route('admin.product_categories.deleted'));
});

/* Home > Categories > Drafts */
Breadcrumbs::register('admin.product_categories.drafts', function($breadcrumbs) {
    $breadcrumbs->parent('admin.product_categories.index');
    $breadcrumbs->push('Drafts', route('admin.product_categories.drafts'));
});

/* Home > Categories > Drafts > Draft */
Breadcrumbs::register('admin.product_categories.limbo', function($breadcrumbs, $draft) {
    $breadcrumbs->parent('admin.product_categories.drafts');
    $breadcrumbs->push('Draft', route('admin.product_categories.draft', $draft));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Sets
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Sets */
Breadcrumbs::register('admin.attribute_sets.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Sets', route('admin.attribute_sets.index'));
});

/* Home > Sets > Add */
Breadcrumbs::register('admin.attribute_sets.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.attribute_sets.index');
    $breadcrumbs->push('Add', route('admin.attribute_sets.create'));
});

/* Home > Sets > Edit */
Breadcrumbs::register('admin.attribute_sets.edit', function($breadcrumbs, $set) {
    $breadcrumbs->parent('admin.attribute_sets.index');
    $breadcrumbs->push('Edit', route('admin.attribute_sets.edit', $set));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Attributes
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Attributes */
Breadcrumbs::register('admin.attributes.index', function($breadcrumbs, $set) {
    $breadcrumbs->parent('admin.attribute_sets.edit', $set);
    $breadcrumbs->push('Attributes', route('admin.attributes.index', $set));
});

/* Home > Attributes > Add */
Breadcrumbs::register('admin.attributes.create', function($breadcrumbs, $set) {
    $breadcrumbs->parent('admin.attributes.index', $set);
    $breadcrumbs->push('Add', route('admin.attributes.create', $set));
});

/* Home > Attributes > Edit */
Breadcrumbs::register('admin.attributes.edit', function($breadcrumbs, $set, $attribute) {
    $breadcrumbs->parent('admin.attributes.index', $set);
    $breadcrumbs->push('Edit', route('admin.attributes.edit', ['set' => $set, 'attribute' => $attribute]));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Values
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Values */
Breadcrumbs::register('admin.values.index', function($breadcrumbs, $set, $attribute) {
    $breadcrumbs->parent('admin.attributes.edit', $set, $attribute);
    $breadcrumbs->push('Values', route('admin.values.index', ['set' => $set, 'attribute' => $attribute]));
});

/* Home > Values > Add */
Breadcrumbs::register('admin.values.create', function($breadcrumbs, $set, $attribute) {
    $breadcrumbs->parent('admin.values.index', $set, $attribute);
    $breadcrumbs->push('Add', route('admin.values.create', ['set' => $set, 'attribute' => $attribute]));
});

/* Home > Values > Edit */
Breadcrumbs::register('admin.values.edit', function($breadcrumbs, $set, $attribute, $value) {
    $breadcrumbs->parent('admin.values.index', $set, $attribute);
    $breadcrumbs->push('Edit', route('admin.values.edit', ['set' => $set, 'attribute' => $attribute, 'value' => $value]));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Discounts
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Discounts */
Breadcrumbs::register('admin.discounts.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Discounts', route('admin.discounts.index'));
});

/* Home > Discounts > Add */
Breadcrumbs::register('admin.discounts.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.discounts.index');
    $breadcrumbs->push('Add', route('admin.discounts.create'));
});

/* Home > Discounts > Edit */
Breadcrumbs::register('admin.discounts.edit', function($breadcrumbs, $discount) {
    $breadcrumbs->parent('admin.discounts.index');
    $breadcrumbs->push('Edit', route('admin.discounts.edit', $discount));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Taxes
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Taxes */
Breadcrumbs::register('admin.taxes.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Taxes', route('admin.taxes.index'));
});

/* Home > Taxes > Add */
Breadcrumbs::register('admin.taxes.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.taxes.index');
    $breadcrumbs->push('Add', route('admin.taxes.create'));
});

/* Home > Taxes > Edit */
Breadcrumbs::register('admin.taxes.edit', function($breadcrumbs, $tax) {
    $breadcrumbs->parent('admin.taxes.index');
    $breadcrumbs->push('Edit', route('admin.taxes.edit', $tax));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */



/**
| ---------------------------------------------------------------------------------------------------------------------
| Currencies
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Currencies */
Breadcrumbs::register('admin.currencies.index', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Currencies', route('admin.currencies.index'));
});

/* Home > Currencies > Add */
Breadcrumbs::register('admin.currencies.create', function($breadcrumbs) {
    $breadcrumbs->parent('admin.currencies.index');
    $breadcrumbs->push('Add', route('admin.currencies.create'));
});

/* Home > Currencies > Edit */
Breadcrumbs::register('admin.currencies.edit', function($breadcrumbs, $currency) {
    $breadcrumbs->parent('admin.currencies.index');
    $breadcrumbs->push('Edit', route('admin.currencies.edit', $currency));
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
| Addresses
| ---------------------------------------------------------------------------------------------------------------------
 */
/* Home > Addresses */
Breadcrumbs::register('admin.addresses.index', function($breadcrumbs, $user) {
    $breadcrumbs->parent('admin.users.edit', $user);
    $breadcrumbs->push('Addresses', route('admin.addresses.index', $user));
});

/* Home > Addresses > Add */
Breadcrumbs::register('admin.addresses.create', function($breadcrumbs, $user) {
    $breadcrumbs->parent('admin.addresses.index', $user);
    $breadcrumbs->push('Add', route('admin.addresses.create', $user));
});

/* Home > Addresses > Edit */
Breadcrumbs::register('admin.addresses.edit', function($breadcrumbs, $user, $address) {
    $breadcrumbs->parent('admin.addresses.index', $user);
    $breadcrumbs->push('Edit', route('admin.addresses.edit', ['user' => $user, 'address' => $address]));
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
Breadcrumbs::register('admin.roles.edit', function($breadcrumbs, $role) {
    $breadcrumbs->parent('admin.roles.index');
    $breadcrumbs->push('Edit', route('admin.roles.edit', $role));
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
Breadcrumbs::register('admin.countries.edit', function($breadcrumbs, $country) {
    $breadcrumbs->parent('admin.countries.index');
    $breadcrumbs->push('Edit', route('admin.countries.edit', $country));
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
Breadcrumbs::register('admin.states.edit', function($breadcrumbs, $state) {
    $breadcrumbs->parent('admin.states.index');
    $breadcrumbs->push('Edit', route('admin.states.edit', $state));
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
Breadcrumbs::register('admin.cities.edit', function($breadcrumbs, $city) {
    $breadcrumbs->parent('admin.cities.index');
    $breadcrumbs->push('Edit', route('admin.cities.edit', $city));
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

/* Home > Settings > Courier */
Breadcrumbs::register('admin.settings.courier', function($breadcrumbs) {
    $breadcrumbs->parent('admin');
    $breadcrumbs->push('Settings');
    $breadcrumbs->push('Courier', route('admin.settings.courier'));
});
/**
| ---------------------------------------------------------------------------------------------------------------------
 */