const routes =  [
    {
        path: '/',
        name: 'home',
        component: './views/dashboard/Dashboard'
    },
    {
        path: '/login',
        name: 'login',
        component: './views/Login'
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: './views/dashboard/Dashboard'
    },

    /* Manufacturers */
    {
        path: '/manufacturers',
        name: 'manufacturers',
        component: './views/product/Manufacturers'
    },
    {
        path: '/manufacturers/new',
        name: 'manufacturers-new',
        component: './views/forms/NewManufacturer'
    },
    {
        path: '/manufacturers/:id',
        name: 'manufacturer',
        component: './views/product/Manufacturer'
    },

    /* Products */
    {
        path: '/products',
        name: 'products',
        component: './views/product/Products'
    },
    {
        path: '/products/new',
        name: 'products-new',
        component: './views/forms/NewProduct'
    },
    {
        path: '/products/:id/stock',
        name: 'product-stock',
        component: './views/product/Stock'
    },
    {
        path: '/products/:id',
        name: 'product',
        component: './views/product/Product'
    },

    /* Product Stock */
    {
        path: '/stock',
        name: 'stock',
        component: './views/product/Stocks'
    },

    /* Product Types */
    {
        path: '/product-types',
        name: 'product-types',
        component: './views/product/ProductTypes'
    },

    /* Tax */
    {
        path: '/taxes',
        name: 'taxes',
        component: './views/product/Taxes'
    },

    /* Customers */
    {
        path: '/customers',
        name: 'customers',
        component: './views/customers/Customers'
    },
    {
        path: '/customers/new',
        name: 'customers-new',
        component: './views/forms/NewCustomer'
    },
    {
        path: '/customers/:id/edit',
        name: 'customer-edit',
        component: './views/forms/EditCustomer'
    },
    {
        path: '/customers/:id',
        name: 'customer',
        component: './views/customers/Customer'
    },

    /* Vendors */
    {
        path: '/vendors',
        name: 'vendors',
        component: './views/vendors/Vendors'
    },
    {
        path: '/vendors/new',
        name: 'vendors-new',
        component: './views/forms/NewVendor'
    },
    {
        path: '/vendors/:id/edit',
        name: 'vendor-edit',
        component: './views/forms/EditVendor'
    },
    {
        path: '/vendors/:id',
        name: 'vendor',
        component: './views/vendors/Vendor'
    },

    /* Sales */
    {
        path: '/sales',
        name: 'sales',
        component: './views/sale/Sales'
    },
    {
        path: '/sales/new',
        name: 'sales-new',
        component: './views/forms/NewSale'
    },
    {
        path: '/sales/:id',
        name: 'sale',
        component: './views/sale/Sale'
    },

    /* Credit Notes */
    {
        path: '/credit-notes',
        name: 'credit-notes',
        component: './views/sale/CreditNotes'
    },
    {
        path: '/credit-notes/:id',
        name: 'credit-note',
        component: './views/sale/CreditNote'
    },
    {
        path: '/credit-notes/new',
        name: 'credit-notes-new',
        component: './views/forms/NewCreditNote'
    },

    /* Purchases */
    {
        path: '/purchases',
        name: 'purchases',
        component: './views/purchase/Purchases'
    },
    {
        path: '/purchases/new',
        name: 'purchases-new',
        component: './views/forms/NewPurchase'
    },
    {
        path: '/purchases/:id',
        name: 'purchase',
        component: './views/purchase/Purchase'
    },

    /* Debit Notes */
    {
        path: '/debit-notes',
        name: 'debit-notes',
        component: './views/purchase/DebitNotes'
    },
    {
        path: '/debit-notes/new',
        name: 'debit-notes-new',
        component: './views/forms/NewDebitNote'
    },
    {
        path: '/debit-notes/:id',
        name: 'debit-note',
        component: './views/purchase/DebitNote'
    },

    /* Receipts */
    {
        path: '/receipts',
        name: 'receipts',
        component: './views/accounting/Receipts'
    },
    {
        path: '/receipts/new',
        name: 'receipts-new',
        component: './views/forms/NewReceipt'
    },
    {
        path: '/receipts/:id',
        name: 'receipt',
        component: './views/accounting/Receipt'
    },

    /* Expenses */
    {
        path: '/expenses',
        name: 'expenses',
        component: './views/accounting/Expenses'
    },
    {
        path: '/expenses/new',
        name: 'expenses-new',
        component: './views/forms/NewExpense'
    },
    {
        path: '/expenses/:id',
        name: 'expense',
        component: './views/accounting/Expense'
    },

    /* Support */
    {
        path: '/support',
        name: 'support',
        component: './views/help/Support'
    },

    /* About */
    {
        path: '/about',
        name: 'about',
        component: './views/help/About'
    },

    /* All other pages to be redirected to 404*/
    {
        path: '*',
        name: '404',
        component: './components/PageNotFound'
    }
]

export default routes
