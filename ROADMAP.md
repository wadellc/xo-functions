# XO Functions Framework — Operations Roadmap

This document tracks active development goals and feature backlog for the XO Functions utility
stack. Release history lives in [CHANGELOG.md](CHANGELOG.md).

---

## 🎯 Active Milestone: v2.2.0 (Operations Dashboard)
*Target: Q3 2026*

The primary focus of this milestone is visibility and quick diagnostics for managed fleet environments directly from the WP Admin dashboard.

- [ ] **Dashboard Logging Widget:** Add a live system/error event logger box to the main WordPress dashboard using `wp_add_dashboard_widget()`.
- [ ] **Log Pop-out UI:** Add a toggle inside the widget wrapper that launches an isolated, minimal terminal window (`target="_blank"`) for real-time monitoring.
- [ ] **Log Purge System:** Implement an automated cron or manual button to clear log database rows/files older than 30 days.

---

## 🗃️ Future Backlog (Ideas & Sandbox)
*Features prioritized for evaluation in upcoming versions.*

- [ ] **Remote Log Aggregation:** Sync critical site alerts back to the central MainWP Hub repository.
- [ ] **Third-Party Integrations:** Add Slack/Discord webhook alerts for severe, unhandled plugin drops.
- [ ] **Extended Topologies:** Add custom toggles for advanced environment caching layers (Redis/Memcached sniffers).
- [ ] **Mega Menu Framework Support:** Generalize the dynamic WooCommerce mega-menu engine being built
  in `wagners-site-extensions` (`includes/nav-menus.php` — see that plugin's ROADMAP.md Phase 0) into
  a reusable XO Functions module, so other managed-fleet sites can opt into category/Smart-Item-driven
  mega menus without a site-specific plugin. Not scoped — evaluate once the Wagner's implementation is
  proven out; it currently has hard dependencies (WooCommerce, a specific theme's `primary-menu`
  location) that would need to be abstracted first.
- [ ] **Duplicate Menu Item:** Add a "Duplicate" action to each item row on Appearance > Menus (any
  menu, any theme). Surfaced while building the Wagner's mega-menu (`wagners-site-extensions`) — not
  WooCommerce- or Wagner's-specific, so it belongs here rather than in that client plugin. Not scoped.
- [ ] **Evaluate archived per-client forks (`.wip/`):** `.wip/` holds ~11 pre-consolidation,
  per-client XO Functions forks (e.g. `lakemoor-extensions`, `utrf-2022q3-extensions`,
  `wadellc-exo-functions`) from before this plugin became one shared codebase. Needs a real
  evaluation pass, not a quick skim — most functions in there were written for one site and won't
  generalize safely, but some may be worth promoting into a proper module here (see "Modules"
  pattern in AGENTS.md), and the rest can likely be dropped once reviewed. Not scoped as a task
  yet; flagging so this archive isn't silently forgotten.

---

Release history lives in [CHANGELOG.md](CHANGELOG.md), not here — this file tracks what's next,
not what shipped. Move an item's notes to CHANGELOG.md and remove it from this file when it's
done, don't leave completed work listed above.