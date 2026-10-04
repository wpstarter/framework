# Working with WpStarter

## Project context

This repository is the `wpstarter/framework` Composer package, not the application skeleton. WpStarter is a Laravel port for WordPress: 1.x is based on Laravel 8.x, and 2.x on Laravel 12.x. This checkout targets PHP 8.2 or later; consult `composer.json` for dependency constraints.

Use the installed source as the authority for APIs and behavior. Laravel documentation is useful background, but WordPress integration differs from a standalone Laravel application.

When this package is installed under `vendor/wpstarter/framework`, treat the paths below as relative to that package directory. For application work, put customizations in the application's controllers, providers, middleware, and configuration. Change framework source when the task is explicitly about the framework itself.

## Namespaces and helpers

- Use `WpStarter\` in place of `Illuminate\` for ported framework classes, including imports, type hints, contracts, and facade references.
- Laravel-style global helpers use `ws_`, such as `ws_app()`, `ws_config()`, `ws_request()`, `ws_view()`, and `ws_route()`. The translation helper corresponding to `__()` is `ws___()`.
- WordPress integration has additional helpers with distinct names: `is_wp()`, `wp_view()`, `content_view()`, and `shortcode_view()`. Verify helper names and signatures in the relevant helper file instead of applying a prefix mechanically.
- Do not assume a third-party Laravel package works unchanged if it depends on `Illuminate` classes or a standalone Laravel bootstrap.

## Routing and WordPress lifecycle

There are three separate routers. Choose imports deliberately; their facades are all named `Route`.

| Container binding | Facade | Purpose |
| --- | --- | --- |
| `router` | `WpStarter\Support\Facades\Route` | HTTP URL routing |
| `wp.router` | `WpStarter\Wordpress\Facades\Route` | Routes matched by shortcode tags in singular post content |
| `wp.admin.router` | `WpStarter\Wordpress\Admin\Facades\Route` | Admin menus and screen routing |

- Content routes use the URI as a shortcode tag. `ShortcodeValidator` checks the current singular post; the WordPress `RouteCollection` extracts shortcode attributes into route parameters.
- The WordPress HTTP kernel falls back to content routing when the HTTP router does not match. Deferred content handling uses `template_redirect` at priority 1 by default; preserve hook timing when changing bootstrap or dispatch.
- The admin kernel registers menus on `admin_menu` and dispatches on `current_screen`. Menu registration populates `hookSuffix`; `ScreenIdValidator` compares it with the current screen ID. Keep these operations at runtime.
- The current `route:cache` command only caches `router`. Load content and admin route definitions even when HTTP routes are cached. The HTTP compiled URL matcher does not replace shortcode or admin screen matching.
- WordPress routing collections preserve custom matching and request-time state. Do not replace them with the base compiled collection without accounting for those behaviors.
- Responses have different rendering targets: `wp_view()` for a full page, `content_view()` for post content, `shortcode_view()` for shortcode output, and `ws_pass()` to continue WordPress handling. Follow the response handler when changing rendering or termination.
- Some provider boot paths are guarded by `is_wp()`. CLI boot and WordPress request boot are different contexts; inspect both when changing providers.

## Source map

Read the entries relevant to the task rather than loading the whole framework.

| Area | Entry points |
| --- | --- |
| Autoload and requirements | `composer.json` |
| Framework services | `src/WpStarter/` by component; `src/WpStarter/Contracts/` for interfaces |
| Core helpers | `src/WpStarter/Foundation/helpers.php`, `src/WpStarter/Support/helpers.php`; other helper files are listed in Composer's `autoload.files` |
| WordPress helpers and services | `src/WpStarter/Wordpress/helpers.php`, `src/WpStarter/Wordpress/WordpressServiceProvider.php` |
| Bootstrap and HTTP lifecycle | `src/WpStarter/Wordpress/Application.php`, `src/WpStarter/Wordpress/Kernel.php`, `src/WpStarter/Foundation/Configuration/ApplicationBuilder.php` |
| HTTP routing and cache | `src/WpStarter/Routing/`, `src/WpStarter/Foundation/Support/Providers/RouteServiceProvider.php`, `src/WpStarter/Foundation/Console/RouteCacheCommand.php` |
| Content routing | `src/WpStarter/Wordpress/Routing/` |
| Admin menus and dispatch | `src/WpStarter/Wordpress/Admin/AdminServiceProvider.php`, `src/WpStarter/Wordpress/Admin/Kernel.php`, `src/WpStarter/Wordpress/Admin/Routing/` |
| WordPress rendering | `src/WpStarter/Wordpress/Http/Response/` |
| Default configuration | `config/`, `config-stubs/` |
| Bundled libraries | `lib/` |
| Tests and static-analysis fixtures | `tests/`, `types/` |

## Changes and verification

- Follow surrounding code and `.editorconfig`: UTF-8, LF, four spaces for PHP, and a final newline. `pint.json` describes formatting rules; avoid unrelated formatting changes.
- Preserve public API compatibility unless the requested change calls for a break. Update related contracts, facade PHPDoc, and relevant `types/` fixtures when signatures change.
- Use existing tests as examples. Add focused regression coverage for behavior changes, particularly lifecycle, matching, and response handling. Documentation-only changes need link and diff checks rather than PHP tests.
- With development dependencies installed, run a focused test using `php vendor/bin/phpunit tests/Routing/RouteCollectionTest.php` or `php vendor/bin/phpunit --filter TestName`. Select the path or filter relevant to the change.
- PHPUnit configuration is in `phpunit.xml.dist`; CI commands and service requirements are in `.github/workflows/tests.yml` and `.github/workflows/databases.yml`. Integration tests may require databases or other services. Report missing prerequisites rather than treating unrun tests as passing.
- For changes needing static analysis, use `php vendor/bin/phpstan analyse -c phpstan.src.neon.dist` for source or `php vendor/bin/phpstan analyse -c phpstan.types.neon.dist` for type fixtures.
- Before finishing, run `git diff --check` and summarize the change, verification, and any unresolved limitations.
