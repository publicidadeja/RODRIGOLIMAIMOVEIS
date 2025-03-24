# Laravel Project Guidelines for Claude

## Commands
- **Serve**: `php artisan serve`
- **Test**: `./vendor/bin/phpunit`
- **Single Test**: `./vendor/bin/phpunit --filter=TestClassName::testMethodName`
- **Lint PHP**: `./vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.php`
- **Compile Assets**: `npm run dev` or `npm run production`
- **Package Assets**: `php artisan cms:publish:assets`

## Code Style
- Follow **PSR-2** standards
- Use **type hints** where possible (PHP 7.3+ supported)
- **Naming**: camelCase for methods/variables, StudlyCase for classes
- **Imports**: Group by type (PHP core, vendors, app namespaces)
- **Indentation**: 4 spaces, no tabs
- **Error handling**: Use try/catch blocks with specific exceptions
- **Comments**: DocBlocks for classes and public methods
- Prefer **dependency injection** over facades when possible
- Use **Laravel conventions** for models, controllers, and migrations
- Follow **RESTful** patterns for API endpoints
- Use **artisan commands** to generate boilerplate code

## Architecture
- SiteRapido theme is the primary frontend
- Follows core/plugins/packages/themes structure
- Uses custom "srapid" packages