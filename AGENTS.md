# AGENTS.md

This file provides guidance to AI coding agents (Claude Code, Gemini CLI, or others) when working
with code in this repository.

## Deploy role: active development source (deployable, fleet-shared)

This plugin is actively developed and is a deploy source — but unlike a single-site plugin, it
runs across **every** Wade LLC managed WordPress site, not just one. Two consequences:

- Changes here are commonly made *while working inside a specific client project* (its folder is
  included in most client `.code-workspace` files precisely so this is convenient) — with the
  understanding that the change ships to the whole fleet, not just that one site.
- This repo's `.deploy/config.json` has a default staging validation target, but that default is
  **not permanent** — it's just whichever site is currently convenient to test against. Override
  it per-run for a different site: `deploy-staging.sh xo-functions --remote <other-site-stage>
  --dry-run` (see `D:/Development/_scripts/deploy/README.md`). Don't read the configured default
  as "this plugin belongs to that site."
- The deploy tool here is for **validating a WIP change against one real site** before cutting a
  release — it is not how the plugin reaches the fleet. That's the GitHub-backed Plugin Update
  Checker wired in `xo-functions.php` (see below); sites update from a tagged release, not from
  this deploy tool.

## What this is

A shared WordPress "utility belt" plugin (`XO Functions`) — environment cues, admin dashboard
helpers, and small optimizations for WP core, Gravity Forms, and WooCommerce, toggled per-site.
Each site enables its own subset of modules via saved settings; not every site runs every module.
PHP 7.4+ (lower floor than the 8.2+ used in newer single-site plugins — keep compatibility in
mind when adding syntax here, since a fleet-wide break has a much bigger blast radius than a
single-site plugin bug).

## Boot sequence & module loader

`xo-functions.php` is the sole entry point:

1. Defines `XO_PLUGIN_DIR`/`XO_PLUGIN_URL`.
2. On `admin_head`, paints a colored top border in wp-admin keyed to
   `wp_get_environment_type()` (local/development/staging/production) — a visual cue so it's
   obvious which environment you're in; add new environment types to the `$colors` map if
   `WP_ENVIRONMENT_TYPE` grows new values.
3. If `is_admin()`, requires `xo-wp-settings.php` — the settings UI (see below).
4. On `plugins_loaded`, `xo_functions_core_module_loader()` reads saved toggles
   (`xo_functions_settings`, multisite-aware via `get_site_option`/`get_option`) and
   conditionally `require_once`s each module's `extend/<module>/index.php`. Two modules also
   gate on a dependency class existing (`gravity-forms` → `GFCommon`, `woo-commerce` →
   `WooCommerce`) in addition to the saved toggle.
5. `xo_get_default_toggles()` centralizes the "never configured yet" default (all four modules
   on) — this used to be duplicated across call sites; keep it centralized when touching
   settings logic.
6. Plugin Update Checker (PUC, vendored at `includes/plugin-update-checker-5.6/`) is wired
   against `https://github.com/wadellc/xo-functions` for update checks, wrapped in try/catch
   logging to `error_log()` on failure — do not let PUC init errors fatal any site.

## Modules (`extend/`)

Each module is a folder with its own `index.php` loader (defensive `file_exists()` checks,
matching the top-level pattern) plus one or more implementation files:

- **`wp-core/`** — admin-side taxonomy tooling (`subcategories-add.php`: "Add Sub-[Taxonomy]"
  deep links on hierarchical taxonomy list tables) and `search-url-slug.php` (redirects `?s=`
  search URLs to a clean `/search/{term}/` slug, backed by its own rewrite rule + a one-time
  `flush_rewrite_rules()` guarded by an option flag; fixes a bare-redirect-with-no-rewrite-rule
  bug that had been copy-pasted across several past one-off client projects — see CHANGELOG.md).
  Both files are unconditionally bundled under the single `wp-core` toggle, matching this
  module's existing pattern — no separate opt-in, so any site with `wp-core` already enabled
  picks up new wp-core features on update. The prototype dynamic-taxonomy-driven nav menu files
  formerly here now live in `.wip/` (not required by any loader) — see ROADMAP.md's mega-menu
  generalization item before wiring those up.
- **`wp-frontend/`** — `wp-frontend.php` (body-class additions, theme-level filters) +
  `shortcodes.php` (shortcode registry, e.g. `[todays_date]`, guarded with `shortcode_exists()`
  so a theme/other plugin registering the same tag wins).
- **`gravity-forms/`** — `xo-gravity-forms.php`: admin dashboard tweaks (defaults the form list
  to "Active" forms), gated on `GFForms`/`GFCommon` existing.
- **`woo-commerce/`** — `xo-woo-commerce.php`: admin product-list column additions (e.g.
  shipping class column), gated on `WooCommerce` existing.

New modules follow this same shape: a folder under `extend/`, its own `index.php` entry point,
added to the `$module_map` in `xo_functions_core_module_loader()`, and a toggle key added to
`xo_get_default_toggles()`. Because a change here reaches every site, prefer additive, toggled
behavior over changing what an existing enabled module does — a site relying on current behavior
shouldn't silently get something different after an update.

## Settings UI (`xo-wp-settings.php`)

Registers a Tools submenu page (`manage_options`, or `manage_network_options` under multisite —
hooked to `network_admin_menu` vs `admin_menu` based on `is_multisite()`). Saving is hybrid:
single sites use the native Settings API (`register_setting`/`options.php`); multisite
intercepts the POST directly (nonce-verified: `xo_multisite_nonce` / `xo_save_network_settings`)
since network options aren't covered by the single-site Settings API flow. When adding a new
toggle, it needs to appear in both the single-site sanitize callback and the multisite POST
interceptor — they are not unified.

## Versioning & changelog discipline

Keep a Changelog + strict SemVer, in **separate files** — `CHANGELOG.md` for shipped release
history, `ROADMAP.md` for active/future work only. When an item ships, move its notes into
CHANGELOG.md and remove it from ROADMAP.md; don't let completed work linger in the roadmap. The
header `Version:` in `xo-functions.php` must stay in sync with the latest `CHANGELOG.md` entry —
and since that version is what every site's Plugin Update Checker compares against, a bump here
is a fleet-wide release, treat it with matching care.

## Deployment workflow

Validate a WIP change against one real site with:
`bash D:/Development/_scripts/deploy/deploy-staging.sh xo-functions` (dry-run first, then a
confirmed live sync — see `D:/Development/_scripts/deploy/README.md`). The default target
configured in `.deploy/config.json` is just whatever site is currently convenient to test
against — override with `--remote <name>` to validate against a different managed site instead.

This is validation only, never how the plugin reaches the fleet — that's the GitHub-backed
Plugin Update Checker above. There is also an existing, separate git-hook pipeline in
`D:/Development/_scripts/` (`build-plugin-zip.sh` + `.git-hooks/post-commit`) that zips this
plugin into `D:/Development/_releases/` for the MainWP template vault — unrelated to staging
validation, left as-is by this deploy tool.

## Conventions to follow when extending

- Every file starts with the `ABSPATH` guard and a phpDocumentor header (`@package XO_Functions`,
  `@subpackage <Module>`, `@category <Type>`) — match existing files' style.
- All module loaders use defensive `file_exists()` checks before `require_once` and never fatal
  on a missing file.
- Escape all output (`esc_html()`, `esc_attr()`, `esc_html__()`) under the `xo-functions` text
  domain, matching existing modules.
- Gate any WooCommerce/Gravity Forms-specific code on the relevant `class_exists()` check, both
  in the module's own code and in its `$module_map` entry — not every site in the fleet has
  either installed.
