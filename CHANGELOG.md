# Changelog
All notable changes to "XO Functions" will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com),
and this project adheres to [Semantic Versioning](https://semver.org).

## [2.1.8]
### Added
- `extend/wp-core/search-url-slug.php`: redirects `?s=` search URLs to a clean `/search/{term}/` slug, with its own rewrite rule (self-flushed once via an option-flag guard, no manual Permalinks visit needed).
### Fixed
- The search URL redirect (previously pasted inline into `extend/wp-core/index.php`, sourced from a WPBeginner tutorial) had no matching rewrite rule at all, so every search 404'd after redirecting — the same bare snippet had been copy-pasted across several past one-off client projects (now archived in `.wip/`, replaced with pointer comments to this module) without ever being fixed. Adding the missing rewrite rule introduces a new failure mode if the redirect isn't scoped correctly (the clean URL itself also satisfies `is_search()`, so a naive check redirects it to itself in a loop) — fixed by keying the redirect specifically on `isset( $_GET['s'] )` rather than `is_search()` alone.
### Changed
- Renamed `wpb_change_search_url()` → `xo_redirect_search_url()`, matching this project's naming convention instead of the tutorial source's.
- `urlencode()` → `rawurlencode()` for the redirect target — this is a URL path segment, not a query string.

## [2.1.7]
### Changed
- No entry recorded prior to this file's split from ROADMAP.md — see plugin header `Version:` in
  `xo-functions.php` for the current released version; backfill this entry if you have the
  original notes.

## [2.1.3]
### Optimized
- Stripped out the redundant `XO_VERSION` global PHP constant.
### Fixed
- Resolved the WordPress 6.7+ `_load_textdomain_just_in_time` architectural warning by moving
  version data exclusively to the core DocBlock plugin header.

## [2.1.2]
### Added
- Infrastructure: initialized the automated Kinsta cloud deployment pipeline using local Git hook
  archives and SSH/SCP secure tunnels.
- Framework: injected dynamic `plugin-update-checker` (PUC) framework linking child endpoints to
  the MainWP template provisioning vault.

## [2.0.0]
### Changed
- Architectural shift: rebuilt the plugin with an isolated module loader loop checking saved
  configuration toggles before loading extended components.
