readme.txt

=== AS Laburda PWA App ===
Contributors: Arad Services
Donate Link: https://arad-services.com
Tags: pwa, progressive web app, mobile app, saas, business listing, directory, app builder, wordpress app, apk, aab, push notifications, offline, app store, listing, directory, coupons, notifications
Requires at least: 5.0
Tested up to: 6.5
Stable tag: 1.0.0
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Converts your WordPress website into a powerful Progressive Web App (PWA) with advanced features, multi-user SaaS options, Android APK/AAB generation capabilities, and integrated business listing and advertising programs.

== Description ==

The AS Laburda PWA App plugin is a comprehensive solution designed to transform your WordPress website into a robust Progressive Web App (PWA) and a multi-user SaaS platform. This plugin empowers you to offer PWA creation services to your users, alongside a full-featured business listing and advertising system.

**Key Features:**

* **PWA Conversion:** Automatically converts your WordPress site or specific pages into a PWA, offering offline capabilities, push notifications, and home screen installation.
* **Multi-User SaaS Platform:** Allows your users to create and manage their own PWAs and business listings directly from your website.
* **App Generation (Simulated):** Provides the necessary files and a simulated process for generating Android APK/AAB files, enabling submission to app stores.
* **User-Friendly Admin Dashboard:** Intuitive backend interface for managing all plugin features, including:
    * **App Management:** Create, edit, and delete individual PWA apps. Configure app names, short names, descriptions, start URLs, theme colors, background colors, display modes, and orientations.
    * **Icon & Splash Screen Management:** Easily upload and configure app icons (192x192, 512x512) and splash screens.
    * **Page Assignments:** Link specific WordPress pages (e.g., Offline Page, Dashboard, Login) to your PWA apps.
    * **Global Settings:** Configure Firebase integration (for future real-time features and push notifications) and other global plugin options.
    * **Listing Plans Management:** Define and manage various business listing plans (Free, Basic, Premium, One-time SEO, Ads) with configurable features, pricing, and billing periods.
    * **Custom Fields:** Create and manage custom fields for business listings, allowing for highly customizable listing forms.
* **Comprehensive Business Listing System:**
    * **Detailed Listing Information:** Supports extensive business details including:
        * Logo, Featured Image, and Gallery Images (min 4, up to 15).
        * YouTube Video Integration (short link).
        * Business Name, Short Description, and Detailed Description.
        * Full Address, City, Phone, WhatsApp, and Website.
        * Business Hours, Categories, and Features.
        * Price Range, FAQ section.
        * Social Media Links (Facebook, Twitter, YouTube, LinkedIn, Instagram).
        * SEO-friendly Tags and Keywords.
        * Booking Options (Timekit, Reserva, etc. - configurable).
        * Restaurant Menu Option.
        * Coupons to display within listings.
    * **Claim Listing Feature:** Allows business owners to claim existing listings.
    * **Subscription Management:** Integrates with listing plans to provide tiered access to features.
* **Notification System:**
    * **Business-Specific Notifications:** Users can subscribe to notifications from individual business listings.
    * **Business Owner Notifications:** Business owners can send notifications to their subscribers directly from their dashboard.
    * **User Notification Control:** Users can view and manage their subscribed businesses and notification preferences.
* **Frontend Dashboards:** Dedicated shortcodes for a user dashboard (to manage their apps and subscriptions) and a business owner dashboard (to manage their listings, notifications, and coupons).
* **Responsive Design:** Ensures that your PWA and business listings look great and function flawlessly on all devices, from desktops to mobile phones.
* **Clean and Extensible Code:** Developed with best practices, making it easy for developers to extend and customize.

**Ideal for:**

* **Agencies:** Offer PWA and mobile app creation services to your clients.
* **Directory Websites:** Enhance your directory with PWA features and monetize listings with flexible plans.
* **SaaS Providers:** Build a platform where users can create their own mini-apps or business profiles.
* **Any Business:** Transform your own WordPress site into a highly engaging and accessible PWA.

== Installation ==

1.  **Upload:**
    * Download the plugin zip file.
    * Go to your WordPress admin dashboard -> Plugins -> Add New -> Upload Plugin.
    * Choose the downloaded zip file and click "Install Now".
2.  **Activate:**
    * After installation, click "Activate Plugin".
3.  **Configure:**
    * Navigate to "AS Laburda PWA" in your WordPress admin menu.
    * **Global Settings:** Configure your Firebase project details (if you plan to use real-time features or push notifications).
    * **Apps:** Create your first PWA app. Configure its details, icons, and link it to existing WordPress pages (e.g., your homepage as the start URL, a dedicated offline page, or a user dashboard page).
    * **Listing Plans:** Review and adjust the default listing plans, or create new ones to suit your SaaS model.
    * **Custom Fields:** Add any additional custom fields you need for your business listings.
4.  **Add Shortcodes to Pages:**
    * Create new WordPress pages for your business listing submission form, individual listing display, user dashboard, and business owner dashboard.
    * Insert the following shortcodes into the respective pages:
        * `[as_laburda_business_listing_form]` - For users to submit/edit listings.
        * `[as_laburda_display_business_listing]` - To display a single listing (often used on a page that accepts `listing_id` as a URL parameter).
        * `[as_laburda_user_dashboard]` - For a general user dashboard.
        * `[as_laburda_business_owner_dashboard]` - For business owners to manage their listings, notifications, and coupons.
5.  **Flush Permalinks:** Go to Settings -> Permalinks and simply click "Save Changes" to ensure the PWA manifest and service worker URLs are properly registered.

== Frequently Asked Questions ==

= How do I generate the APK/AAB files? =
The plugin provides a simulated generation process. You can click the "Generate App Package" button on the app editing page. This will prepare the necessary web assets and provide dummy download links for APK, AAB, and security files.
**Important:** Actual native app compilation requires complex external build environments (like Android Studio for Android or Xcode for iOS). You would typically use these downloaded files with an external cloud build service (e.g., Google Play Console's App Signing, or a third-party service) or compile them locally.

= How do I customize the look and feel of my PWA? =
The plugin allows you to set theme colors, background colors, and choose between conceptual "desktop" and "mobile app" templates in the app settings. These templates primarily influence the CSS and layout structure provided by the plugin's public styles. For deeper customization, you would modify the `assets/css/public-styles.css` file or use your theme's custom CSS options.

= Can users create multiple apps? =
Yes, the SaaS model allows users (with the `app_user` role) to create and manage multiple PWAs from their dashboard.

= How do I manage business listing plans and pricing? =
Go to "AS Laburda PWA" -> "Listing Plans" in the admin menu. Here you can add, edit, or delete plans, define their features, pricing, and billing periods.

= How do users subscribe to business notifications? =
On the frontend, when a user views a business listing, there will be a "Subscribe to Notifications" button (if enabled and the user is logged in). Users can manage all their subscriptions from their personal user dashboard.

= What are "Custom Fields" for? =
Custom fields allow you to add extra data fields to your business listing submission forms. This is useful for collecting unique information relevant to specific business types that aren't covered by the default fields. You can manage them under "AS Laburda PWA" -> "Custom Fields".

= What does "Claim Listing" do? =
The "Claim Listing" feature allows a user to take ownership of an existing business listing. Once claimed (and optionally approved by an admin), the user associated with the claim gains full management rights over that listing, including editing details, managing notifications, and adding coupons.

== Changelog ==

= 1.0.0 - 2025-06-03 =
* Initial Release.
* Core plugin architecture for PWA SaaS.
* Admin dashboard for app management (create, edit, delete).
* Simulated APK/AAB generation.
* Database tables for apps, business listings, listing plans, user subscriptions, notifications, coupons, custom fields.
* Admin UI for managing listing plans and custom fields.
* Public-facing shortcodes for business listing form, display, user dashboard, and business owner dashboard.
* PWA manifest and service worker generation.
* Basic frontend styling and JavaScript for PWA features and form handling.
* Custom user roles (`app_user`, `business_owner`) and capabilities.
