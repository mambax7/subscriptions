# Changelog

All notable changes to the **Subscriptions** module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-02-23

### Added
- Initial release of the Subscriptions — Paid Memberships & Service Subscriptions module for XOOPS 2.5.11+
- Subscription plan management (CRUD) with support for monthly, annual, and custom billing cycles
- Plan features system with per-plan feature lists and descriptions
- Subscription lifecycle management: active, cancelled, expired, trial, and grace-period states
- Payment processing with pluggable gateway architecture (PayPal, Stripe, Manual/Offline)
- Invoice generation with line items, tax calculation, and printable invoice view
- Coupon and discount code system with percentage/fixed-amount discounts and usage limits
- Coupon usage tracking per user and per coupon
- Refund management linked to payments and invoices
- Webhook support for payment gateway event notifications
- Connected Modules system: gate access to other XOOPS modules based on active subscription
- Module Access Rules for fine-grained per-module permission control
- Usage log for tracking subscription feature consumption
- Gateway configuration storage (API keys, credentials) per gateway
- Checkout flow with plan selection, coupon application, and payment redirect
- Payment return handler for gateway callbacks (IPN/webhook)
- User dashboard showing active subscription, payment history, and invoice list
- Admin dashboard with subscription statistics and quick-action panel
- Admin management pages: Plans, Subscriptions, Payments, Coupons, Gateways, Connected Modules, Webhooks, Reports
- Admin clone utility for duplicating plans
- Cron job support (`cron.php`) for expiry checks and renewal reminders
- Seed script (`seed.php`) for populating sample plans and test data
- Featured Plans block with configurable item count and layout toggle
- 14 database tables: plans, plan\_features, subscriptions, payments, invoices, invoice\_items, coupons, coupon\_usage, connected\_modules, module\_access\_rules, usage\_logs, webhooks, refunds, gateway\_configs
- 21 Smarty templates covering all frontend and admin views
- PSR-4 autoloading via `preloads/autoloader.php` for `XoopsModules\Subscriptions` namespace
- `SubscriptionsSDK.php` for third-party module integration
- Gateway abstraction layer: `AbstractGateway`, `GatewayInterface`, `GatewayFactory`
- Common helper classes: `Helper`, `Utility`, `AccessControl`, `Cloner`
- Shared admin utilities: `Blocksadmin`, `FilesManagement`, `ModuleStats`, `ServerStats`, `SysUtility`, `TestdataButtons`, `VersionChecks`
- YAML test data fixtures for all 14 tables
- PHPStan static analysis configuration (level 6, PHP 7.4 target)
- PHP-CS-Fixer code style configuration (PSR-12, PHP 7.4 migration rules)
- GitHub Actions CI/CD workflows: tests, static analysis, code style, changelog, Dependabot auto-merge
- Dependabot configuration for automated dependency updates
- GitHub issue templates (bug report, feature request) and pull request template
- `FUNDING.yml` for GitHub Sponsors support
- 11 module configuration options: currency, currency symbol, tax rate, trial days, invoice prefix, email notifications, active gateway, multiple subscriptions, grace period, sample button, developer tools

### Technical Notes
- Compatible with PHP 8.2, 8.3, 8.4, and 8.5
- Requires XOOPS 2.5.12 or later
- Uses XMF library (Xmf\Request, Xmf\Module\Helper, Xmf\Module\Admin)
- Database: 14 InnoDB tables with proper foreign-key relationships
- No dependency on the PHP intl extension
- No use of PHP 8.0+ features (match, named args, enums, readonly, attributes)
- CSRF protection on all forms using XOOPS security tokens
- Prepared statements for all database queries (no raw SQL concatenation)
- XSS prevention using MyTextSanitizer and htmlspecialchars throughout
