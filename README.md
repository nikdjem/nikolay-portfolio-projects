# Nikolay Portfolio Projects

Companion WordPress plugin for the [NIKWEB.EU](https://nikweb.eu) portfolio theme ([nikdjem/nikolay-portfolio](https://github.com/nikdjem/nikolay-portfolio)).

Registers the portfolio **project** content model: custom post type, taxonomy, metadata fields, REST exposure, and admin UI. **Project content lives in the WordPress database** — not in this repository.

**Version:** 1.0.2
**Requires WordPress:** 6.0+
**Requires PHP:** 7.4+

## Architecture

| Component | Details |
|-----------|---------|
| **CPT** | `project` — rewrite slug `work` (`/work/{slug}/`) |
| **Taxonomy** | `project_category` — default terms: Intelligence, Commerce, Interface |
| **Post meta** | `_np_project_status`, `_np_project_github_url`, `_np_project_live_url` |
| **REST** | CPT, taxonomy, and meta exposed via REST API |
| **Admin UI** | Project Metadata meta box on project edit screen |
| **Hooks** | Activation/deactivation flush rewrite rules; default terms on init |

### Helper functions (theme consumption)

- `np_projects_get_status_label()`
- `np_projects_should_render_live_link()`
- `np_projects_should_render_github_link()`

The theme uses optional `function_exists()` guards — the plugin does not call theme code.

## Dependencies

Self-contained. **No** ACF, WooCommerce, or external service requirements.

| Dependency | Required? |
|------------|-----------|
| WordPress 6.0+ | Yes |
| PHP 7.4+ | Yes |
| Theme | No (portfolio presentation is theme-owned) |

## Repository Structure

```
nikolay-portfolio-projects/
├── nikolay-portfolio-projects.php   Bootstrap, CPT, taxonomy, activation hooks
├── includes/
│   └── project-meta.php             Meta registration, sanitization, admin UI
└── .cpanel.yml                      cPanel Git deployment configuration
```

## Local Development

1. Clone into `wp-content/plugins/nikolay-portfolio-projects/`.
2. Activate **Nikolay Portfolio Projects** in WordPress Admin.
3. Create or import `project` posts, assign categories, and set metadata in the editor.

Project posts, excerpts, case-study bodies, featured images, and uploads are **WordPress database and media assets** — maintained in LocalWP for development, not in Git.

## Production Deployment

| Item | Value |
|------|-------|
| **Production URL** | [https://nikweb.eu](https://nikweb.eu) |
| **cPanel Git clone** | `/home/nikwebeu/git/nikolay-portfolio-projects` |
| **Deploy target** | `/home/nikwebeu/public_html/wp-content/plugins/nikolay-portfolio-projects/` |
| **Deploy config commit** | `8283411` (`.cpanel.yml`) |
| **Deploy method** | cPanel Git — copies `nikolay-portfolio-projects.php` and `includes/` only |

Activation is performed separately in WordPress Admin after deployment.

### Production status

| Area | Status |
|------|--------|
| Plugin code deployment | **COMPLETE** |
| Plugin activation | **COMPLETE** |
| Project CPT in Admin | **VERIFIED** |
| Project entries on production | **0** (content migration **PENDING**) |
| Database migration | **NOT STARTED** |
| Uploads / media migration | **NOT STARTED** |
| Final production QA | **NOT STARTED** |

**Do not assume** deploying this plugin migrates the six LocalWP portfolio projects. Content migration is a separate WordPress database and media operation.

## Related Repository

Theme (presentation layer): [nikdjem/nikolay-portfolio](https://github.com/nikdjem/nikolay-portfolio)

## License

GNU General Public License v2 or later.
