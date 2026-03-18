# EduMetric_Dashboard_Plugin
# EduRank Mega Dashboard

A WordPress plugin that provides role-based dashboards, a leaderboard, and an impact stories page for the EduRank platform. Integrates with **myCred** and **GiveWP** when available, with safe fallbacks if neither is installed.

## Features

- **School Dashboard** — Shows points, badges, student count, attendance rate, performance score, and recent activity posts for logged-in school users.
- **Donor Dashboard** — Displays donation history, recommended schools, myCred points balance, and mentorship actions for logged-in donors.
- **Admin Dashboard** — Restricted to users with `manage_options` capability. Shows platform-wide stats (total schools, donors, donations, pending stories) and a leaderboard.
- **Leaderboard** — Renders a ranked list of schools by myCred points, with a fallback to `school_profile` posts if myCred is not installed.
- **Impact Stories** — Displays a grid of published `impact_story` posts with thumbnail, excerpt, and date.

## Installation

1. Download or clone this repository.
2. Upload the `edurank-mega-dashboard` folder to `/wp-content/plugins/`.
3. Activate the plugin from **WordPress Admin → Plugins**.
4. On activation, the plugin automatically creates five pages (if they don't already exist):

| Page Title        | Shortcode                    |
|-------------------|------------------------------|
| School Dashboard  | `[edurank_school_dashboard]` |
| Donor Dashboard   | `[edurank_donor_dashboard]`  |
| Admin Dashboard   | `[edurank_admin_dashboard]`  |
| Leaderboard       | `[edurank_leaderboard]`      |
| Impact Stories    | `[edurank_impact_stories]`   |


## Shortcodes

You can also place any shortcode manually on any page or post.

### `[edurank_school_dashboard]`
Displays the school user's dashboard. Requires the user to be logged in. Pulls data from user meta fields:
- `total_students`
- `attendance_rate`
- `performance_score`

### `[edurank_donor_dashboard]`
Displays the donor's dashboard. Requires the user to be logged in. Shows donation history via GiveWP and recommended schools via myCred leaderboard (or a `school_profile` post list as fallback).

### `[edurank_admin_dashboard]`
Displays the admin dashboard. Restricted to users with the `manage_options` capability (i.e. WordPress administrators).

### `[edurank_leaderboard]`
Renders a ranked leaderboard of schools. Uses myCred's leaderboard shortcode if available; otherwise lists `school_profile` posts.

### `[edurank_impact_stories]`
Renders a responsive grid of published `impact_story` posts, showing thumbnail, title, date, and excerpt.


## Optional Integrations

The plugin works out of the box but enhances automatically when these plugins are present:

| Plugin | What it adds |
|--------|--------------|
| **myCred** | Points balance, badges, and leaderboard data across all dashboards |
| **GiveWP** | Donation history and platform-wide donation totals in the Donor and Admin dashboards |

No configuration is required — the plugin detects these plugins and uses them automatically.

## User Meta Fields (School Dashboard)

The School Dashboard reads the following WordPress user meta keys. Populate these via your registration flow, Ultimate Member profile fields, or a custom form:

| Meta Key           | Description                     |
|--------------------|---------------------------------|
| `total_students`   | Number of students at the school |
| `attendance_rate`  | Numeric attendance percentage   |
| `performance_score`| School performance score        |


## Custom Post Types

The plugin queries these post types, which should be registered by your theme or another plugin:

- `school_profile` — Used in the leaderboard fallback and donor recommended schools.
- `impact_story` — Used in the Impact Stories grid and Admin Dashboard moderation queue.
- `attendance`, `activities`, `performance` — Queried in the School Dashboard recent updates feed.

---

## Styles

Basic responsive CSS is injected into `wp_head` automatically — no separate stylesheet needed. The layout uses CSS Grid and is mobile-responsive at a 700px breakpoint. You can override styles in your theme using the `.edurank-*` class namespace.


## Uninstall

Uninstalling the plugin intentionally **leaves all pages and user data intact**. If you want the plugin to delete the pages it created on uninstall, uncomment the relevant lines in the `edurank_uninstall_cleanup()` function inside the plugin file.

## Requirements

- WordPress 5.8+
- PHP 7.4+
- myCred *(optional)*
- GiveWP *(optional)*
