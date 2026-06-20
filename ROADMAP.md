# XO Functions Framework — Operations Roadmap & Changelog

This document tracks the active development goals, feature backlog, and milestone history for the XO Functions utility stack.

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

---

## 📜 Changelog History

### v2.1.3
- **Optimized:** Stripped out the redundant `XO_VERSION` global PHP constant.
- **Fixed:** Resolved the WordPress 6.7+ `_load_textdomain_just_in_time` architectural warning by moving version data exclusively to the core DocBlock plugin header.

### v2.1.2
- **Infrastructure:** Initialized the automated Kinsta cloud deployment pipeline using local Git hook archives and SSH/SCP secure tunnels.
- **Framework:** Injected dynamic `plugin-update-checker` (PUC) framework linking child endpoints to the MainWP template provisioning vault.

### v2.0.0
- **Architectural Shift:** Rebuilt the plugin with an isolated module loader loop checking saved configuration toggles before loading extended components.