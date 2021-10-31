const menuItems = [
    {
        label: "General",
        children: [
            {
                label : "Dashboard",
                icon : "mdi mdi-desktop-mac-dashboard",
                to : "dashboard"
            }
        ]
    },
    {
        label: "Product",
        children: [
            {
                label : "Manufacturers",
                icon : "mdi mdi-domain",
                to : "manufacturers"
            },
            {
                label : "Products",
                icon : "mdi mdi-database",
                to : "products"
            },
            {
                label : "Product Types",
                icon : "mdi mdi-database-search",
                to : "product-types"
            },
            {
                label : "Taxes",
                icon : "mdi mdi-credit-card-plus",
                to : "taxes"
            },
        ]
    },
    {
        label: "Sale",
        children: [
            {
                label : "Sales",
                icon : "mdi mdi-receipt",
                to : "sales"
            },
            {
                label : "Customers",
                icon : "mdi mdi-account-multiple",
                to : "customers"
            },
            {
                label : "Credits",
                icon : "mdi mdi-credit-card-plus",
                to : "credits"
            },
            {
                label : "Receipts",
                icon : "mdi mdi-receipt",
                to : "receipts"
            },
        ]
    },
    {
        label: "Purchase",
        children: [
            {
                label : "Purchases",
                icon : "mdi mdi-receipt",
                to : "purchases"
            },
            {
                label : "Vendors",
                icon : "mdi mdi-account-multiple",
                to : "vendors"
            },
            {
                label : "Debits",
                icon : "mdi mdi-credit-card-plus",
                to : "debits"
            },
            {
                label : "Vouchers",
                icon : "mdi mdi-currency-inr",
                to : "vouchers"
            },
        ]
    },
    {
        label: "Account",
        children: [
            {
                label: "Accounts",
                icon: "mdi mdi-cash-100",
                to: "accounts"
            },
            {
                label: "Incomes",
                icon: "mdi mdi-cash-plus",
                to: "incomes"
            },
            {
                label: "Expenses",
                icon: "mdi mdi-cash-minus",
                to: "expenses"
            }
        ]
    },
    {
        label: "Help",
        children: [
            {
                label : "Support",
                icon : "mdi mdi-help-box",
                to : "support"
            },
        ]
    },
    {
        label: "Application",
        children: [
            {
                label : "Settings",
                icon : "mdi mdi-application-settings",
                to : "settings"
            },
            {
                label : "Logout",
                icon : "mdi mdi-logout",
                to : "logout"
            }
        ]
    }
];

export default menuItems;
