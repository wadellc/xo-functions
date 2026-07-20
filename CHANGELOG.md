# Changelog
All notable changes to "XO Functions" will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com),
and this project adheres to [Semantic Versioning](https://semver.org).

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
