<?php

// list of accessible routes of your application, add every new route here
// key : route to match
// values : 1. controller name
//          2. method name
//          3. (optional) array of query string keys to send as parameter to the method
// e.g route '/item/edit?id=1' will execute $itemController->edit(1)
return [

    // ADMIN OK //

    '' => ['HomeController', 'index',],
    'home' => ['HomeController', 'index',],

    'user/show' => ['UserController', 'show', ['id']],
    'register' => ['UserController', 'add',],
    'user/edit' => ['UserController', 'edit', ['id']],

    'products' => ['ProductController', 'index',],
    'products/show' => ['ProductController', 'show', ['id']],
    'products/category' => ['ProductController', 'indexByCategory', ['cat_id']],
    'products/color' => ['ProductController', 'showByColor', ['id']],
    'products/filters' => ['ProductController', 'showByColByCat', ['col', 'cat_id']],
    'products/search' => ['ProductController', 'search', ['keyWord']],

    'cart' => ['CartController', 'cart',],
    'order' => ['HomeController', 'order',],
    'confirmation' => ['HomeController', 'orderConfirmation'],

    'invoices' => ['InvoiceController', 'index',],
    'invoices/show' => ['InvoiceController', 'show', ['id']],
    'invoices/user-invoices' => ['InvoiceController', 'userInvoices', ['id']],

    'admin' => ['AdminProductController', 'index',],
    'admin/add-product' => ['AdminProductController', 'addProduct',],
    'admin/edit-product' => ['AdminProductController', 'editProduct', ['id']],
    'admin/delete-product' => ['AdminProductController', 'deleteProduct',],
    'admin/search-product' => ['AdminProductController', 'search', ['keyWord']],

    'admin/filters' => ['FilterController', 'index',],
    'admin/filters/add-color' => ['AdminColorController', 'addColor',],
    'admin/filters/color' => ['AdminColorController', 'editColor', ['id']],
    'admin/filters/delete-color' => ['AdminColorController', 'deleteColor',],
    'admin/filters/add-category' => ['AdminCategoryController', 'addCategory',],
    'admin/filters/category' => ['AdminCategoryController', 'editCategory', ['id']],
    'admin/filters/delete-category' => ['AdminCategoryController', 'deleteCategory',],

    'admin/orders' => ['InvoiceController', 'index',],
    'admin/orders/treated' => ['InvoiceController', 'treatedInvoices',],
    'admin/orders/not-treated' => ['InvoiceController', 'notTreatedInvoices',],
    'admin/orders/delete' => ['InvoiceController', 'delete',],

    'admin/users' => ['AdminUserController', 'index',],
    'admin/search-user' => ['AdminUserController', 'search', ['keyWord']],
    'admin/users/edit' => ['AdminUserController', 'edit', ['id']],
    'admin/users/delete' => ['AdminUserController', 'delete',],

    'login' => ['SecurityController', 'login',],
    'logout' => ['SecurityController', 'logout',],
];
