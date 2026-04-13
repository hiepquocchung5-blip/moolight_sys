/moonlight_root
│
├── .env                    <-- Secret Environment Variables (NEVER commit to Git)
├── index.php               <-- Master Router (Traffic Cop)
│
├── /config                 <-- Configurations (Global & Portal Specific)
│   ├── database.php        (PDO Global Connection Instance)
│   ├── api_config.php      (JWT Secrets, Rate Limits)
│   └── app_config.php      (Session limits, Gateway toggles)
│
├── /functions              <-- Core Logic Engines
│   ├── global_functions.php(env parser, text sanitization, route helpers)
│   ├── currency_engine.php (MMK -> USD/EUR maths via DB rates)
│   ├── webhook_engine.php  (Telegram messaging functions)
│   └── api_engine.php      (cURL wrappers for internal microservice calls)
│
├── /landing                <-- PORTAL 1: Storefront
│   ├── index.php           (Homepage, Categories, Products)
│   ├── /assets             (CSS, JS, Images for landing only)
│
├── /app                    <-- PORTAL 2 & 3: Moon Account & Auth
│   ├── index.php           (User Dashboard, Invoices, Settings)
│   ├── /auth               (login.php, register.php, process_*.php)
│   └── /shop               (checkout.php, blindbox.php)
│
├── /moon-admin             <-- PORTAL 4: God-Mode
│   ├── index.php           (Admin Dashboard, CMS, Order Approval)
│
└── /api                    <-- PORTAL 5: Headless & Webhooks
    ├── index.php           (API Master Router & JSON output)
    └── /webhooks           (Telegram callbacks, payment callbacks)
