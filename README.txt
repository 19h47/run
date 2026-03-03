=== Run ===
Contributors: 19h47
Donate link: https://www.19h47.fr
Tags: running, tracking, health, dashboard, custom-post-type
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 2.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A lightweight plugin to track your runs in WordPress (custom post type + metadatas) and get a native‑looking dashboard widget summarising your activity.

== Description ==

Run adds a simple, focused **"run" custom post type** to WordPress so you can log your sessions (date, duration, steps, weight, etc.) and review your progress over time.

The plugin ships with a native‑feeling **dashboard widget** that summarises your recent activity without any JavaScript:

* Sessions, steps, duration and weight over several periods (7 days, 1 month, 6 months, 1 year)
* One small bar chart per metric (sessions, steps, duration, weight)
* Accessible tooltips and labels that respect your WordPress date formats
* Colours and buttons that follow the admin colour scheme
* Per‑user persistence of the selected period

Two filters are available if you need to extend the widget:

* `run_dashboard_allowed_ranges` – change the list of available periods (in days).
* `run_dashboard_series` – add/remove series or adjust labels/behaviour.

== Installation ==

1. Upload the `run` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start creating **Run** posts from the `Runs` menu in the admin.

== Screenshots ==

1. Dashboard widget showing recent sessions, steps, duration and weight.

== Changelog ==

= 2.1.0 =
* Add a native dashboard widget with activity graphs for sessions, steps, duration and weight.
* Support period selection (7 days, 1 month, 6 months, 1 year) with per‑user persistence.
* Add derived metrics (average steps and duration per session, weight delta over the period).
* Integrate with the admin colour scheme and improve accessibility (ARIA labels, tooltips).
* Allow customisation via `run_dashboard_allowed_ranges` and `run_dashboard_series` filters.

= 2.0.0 =
* Initial public release of the new Run CPT and admin UI.

== Upgrade Notice ==

= 2.1.0 =
This release adds a powerful, no‑JS dashboard widget to visualise your running activity (sessions, steps, duration, weight) and exposes filters for customisation.
