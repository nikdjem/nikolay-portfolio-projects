# Changelog

All notable changes to **Nikolay Portfolio Projects** are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

### Phase 12 — Production Deployment

**Status: IN PROGRESS — plugin code deployed and active; content migration pending**

#### Completed (verified)

- cPanel deployment configuration added (`.cpanel.yml`, commit `8283411`)
- Plugin deployed to production via cPanel Git → `/home/nikwebeu/public_html/wp-content/plugins/nikolay-portfolio-projects/`
- Plugin **ACTIVE** on [https://nikweb.eu](https://nikweb.eu)
- Project CPT **VERIFIED** in WordPress Admin (zero project entries on production)

#### Pending

- Database / content migration from LocalWP (six project posts)
- Project metadata, taxonomy terms, and relationships
- `menu_order`, post content, excerpts
- Featured-image and attachment posts/meta
- Project media and uploads (including inline case-study screenshots)
- URL search-replace (`nikolay-portfolio.local` → `https://nikweb.eu`)
- Thumbnail regeneration if necessary
- Permalink validation
- Final production portfolio QA

*Phase 12 is not complete until content migration and production QA pass.*

---

## [1.0.2] — Project metadata and single-project support

**Commit:** `125ee15`

### Added

- Project metadata fields: `_np_project_status`, `_np_project_github_url`, `_np_project_live_url`
- REST registration and sanitization for project meta
- Admin Project Metadata meta box
- Helper functions for claim-safe Live and GitHub CTA rendering
- Single-project architecture support (status labels, meta auth callbacks)

---

## [1.0.0] — Initial project content model

**Commit:** `54c163e`

### Added

- `project` custom post type (rewrite slug `work`)
- `project_category` taxonomy
- Default taxonomy terms: Intelligence, Commerce, Interface
- Activation/deactivation rewrite flush hooks
