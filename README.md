# PG Body Tracker (Production SaaS Engine)
### A complete, high-performance physical assessment and body composition SaaS tailored for fitness and health professionals. 

💻 **Live Production URL:** [www.pgbodytracker.com.br](https://pgbodytracker.com.br)

---

## 📐 Architecture & Product Overview
PG Body Tracker is a multi-tenant Software-as-a-Service (SaaS) platform built to automate physical evaluations, generate complex graphical evolution charts, and handle dynamic client reporting. 

The architecture is driven by a robust **Laravel 8** core engine, serving responsive server-side views leveraging Laravel Blade optimized with lightweight jQuery pipelines. The application processes high-density biometric data (bioimpedance, metrics, skinfolds) and aggregates it into interactive analytical dashboards and dynamic downloadable reports.

---

## 🚀 Key Engineering & Integration Patterns

- **Subscription & Automated Billing Pipeline:** Architected the entire multi-tiered membership workflow integrated directly with the **Mercado Pago API** (`mercadopago/dx-php`). Handled automated recurring billing, checkout redirection, and programmatic subscription status updates via background webhooks.
- **Dynamic Charting & Visual Analytics:** Built an automated charting pipeline mapping time-series body composition metrics into responsive progress graphs, utilizing `QuickChart` API integrations.
- **Asynchronous & High-Fidelity PDF Generation:** Engineered a high-performance document rendering matrix combining `wkhtmltopdf` (packaged natively for both AMD64 Linux containers and local Windows development compatibility) and `dompdf` to instantly generate customized, graphic-rich client evaluation PDFs.
- **Image Processing Optimization:** Scaled upload handling pipelines through dynamic image mutation and compression vectors (`intervention/image`), ensuring optimal storage profiles for high-resolution client physical assessment history tracking.
- **Test-Driven Reliability:** Secured operational critical paths (auth, assessment calculators, membership gating) via a robust **PHPUnit automated test suite**, guaranteeing zero-downtime structural modifications.
- **SEO & Traffic Automation:** Automated dynamic web crawling map building utilizing continuous sitemap generations (`spatie/laravel-sitemap`) and responsive client device signature mapping (`jenssegers/agent`).

---

## 🛠️ Production Tech Stack & Key Dependencies

- **Core Framework:** PHP (^7.3 | ^8.0) & Laravel Framework (^8.75)
- **Frontend Layer:** Laravel Blade, Javascript, Custom jQuery Components
- **Payment & SaaS Infrastructure:** Mercado Pago SDK (DX-PHP), Symfony Filesystem Components
- **Reporting & Engines:** wkhtmltopdf (Linux/Windows variants), Laravel Snappy, DomPDF, QuickChart
- **Data Layers:** PostgreSQL / MySQL relational layout strategies
- **Testing Suite:** PHPUnit Automated Integration & Unit Tests
