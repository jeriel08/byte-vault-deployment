import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/account-manager-style.css",
                "resources/css/app.css",
                "resources/css/dashboard-style.css",
                "resources/css/inventory-report-style.css",
                "resources/css/login-style.css",
                "resources/css/orders-style.css",
                "resources/css/pos-sales-styling.css",
                "resources/css/pos-styling.css",
                "resources/css/product-style.css",
                "resources/js/app.js",
                "resources/js/bootstrap.js",
            ],
            refresh: true,
        }),
    ],
});
