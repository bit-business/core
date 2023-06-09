
**Before submitting your pull request** make sure the following requirements are fulfilled:

To do : complete this section

## Contribution Prerequisites

- You are familiar with Git.

## Development Workflow

Before develop Skijasi, please get SKIJASI_LICENSE_KEY by  register on <a href="https://skijasi.nadzorserveraweb.hr/" target="_blank">Skijasi</a> or contact skijasi core team. This key must be included in the laravel project's .env.
Steps for registering and getting a license on Skijasi Dashboard can be found on <a href="https://skijasi-docs.nadzorserveraweb.hr/docs/en/getting-started/installation/" target="_blank">Skijasi Docs</a>.

### Installation step

After getting the license, you can proceed to Skijasi installation.

1, Clone skijasi into Laravel project. Sample:
- Root Laravel Project
  - /packages // new folder
    - /skijasi // new folder
      - core // clone here

cd into skijasi directory, then run
```
git clone https://github.com/nadzorservera-croatia/skijasi.git
```

2. Add the following Skijasi provider and JWT provider to ```/config/app.php```.

```
'providers' => [
  ...,
  NadzorServera\Skijasi\Providers\SkijasiServiceProvider::class,
  PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider::class,
]
```

3. Add the following aliases to ```config/app.php```.
```
'aliases' => [
    ...,
    'JWTAuth' => PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::class,
    'JWTFactory' => PHPOpenSourceSaver\JWTAuth\Facades\JWTFactory::class,
]
```

4. Add skijasi providers to autoload

```
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "NadzorServera\\Skijasi\\": "packages/nadzorservera-croatia/skijasi/src/"
    },
    ...
}
```

5. Copy required library from ```packages/skijasi/core/composer.json``` to ```/composer.json``` then ```composer install```

6. Run the following commands to update dependencies in package.json and webpack.
```
php artisan skijasi:setup
```

7. Run the following commands in sequence.
```
composer dump-autoload
php artisan migrate
php artisan db:seed --class=SkijasiSeeder
```

8. Open the ```env``` file then add the following lines.
```
#Set a key as secret key for generating JWT token
JWT_SECRET=

#Set JWT Token lifetime, default 60 minutes
SKIJASI_AUTH_TOKEN_LIFETIME=


#Set default menu to generate menu in dashboard. 
#By default Skijasi provide `admin` as default menu
MIX_DEFAULT_MENU=admin

#Set Route prefix for your dashboard. 
#Access dashboard via {HOST}/{MIX_ADMIN_PANEL_ROUTE_PREFIX}
MIX_ADMIN_PANEL_ROUTE_PREFIX=dashboard

#Set prefix for api that skijasi provide. By default 
#Skijasi provide `skijasi-api` as prefix. 
MIX_API_ROUTE_PREFIX=admin

#Skijasi provide Log Viewer feature. please set a route to access this feature
MIX_LOG_VIEWER_ROUTE="log-viewer"
```
:::important
MIX_ADMIN_PANEL_ROUTE_PREFIX, MIX_API_ROUTE_PREFIX & MIX_LOG_VIEWER_ROUTE should be different
:::

9. Add the following Skijasi guard and auth provider in ```config/auth.php```. Make sure to use Skijasi guard as auth default in ```config/auth.php```.
<!--DOCUSAURUS_CODE_TABS-->
<!--PHP-->
```php
return [
  'defaults' => [
    'guard' => 'skijasi_guard',
    'passwords' => 'users',
  ],

  'guards' => [
    ...,
    'skijasi_guard' => [
        'driver' => 'jwt',
        'provider' => 'skijasi_users',
    ],
  ],

  'providers' => [
    ...,
    'skijasi_users' => [
        'driver' => 'eloquent',
        'model' => \NadzorServera\Skijasi\Models\User::class,
    ],
  ],

  ...,
]
```
<!--END_DOCUSAURUS_CODE_TABS-->

10. Create an admin account by typing the following command.
```
php artisan skijasi:admin your@email.com --create
```

11. Run the command ```npm install``` to install all of dependencies
12. Update webpack.mix.js from
```
// Skijasi
mix.js(
        "vendor/skijasi/core/src/resources/js/app.js",
        "public/js/skijasi.js"
    )
```
into
```
// Skijasi
mix.js(
        "packages/skijasi/core/src/resources/js/app.js",
        "public/js/skijasi.js"
    )
```
13. Run laravel with the command ```npm run watch``` if it is under development environment or ```npm run prod``` for production environment.

## Running the tests

To do : complete this section

---

### Reporting an issue

Before submitting an issue you need to make sure:

- You are experiencing a concrete technical issue with Skijasi.
- You have already searched for related [issues](https://github.com/nadzorservera-croatia/skijasi/issues), and found none open (if you found a related _closed_ issue, please link to it from your post).
- You are not asking a question about how to use Skijasi or about whether or not Skijasi has a certain feature. For general help using Skijasi, you may:
  - Refer to [the official Skijasi documentation](https://skijasi-docs.nadzorserveraweb.hr).
  - Ask a question on [github discussion](https://github.com/nadzorservera-croatia/skijasi/discussions).
- Your issue title is concise, on-topic and polite.
- You can and do provide steps to reproduce your issue.
- You have tried all the following (if relevant) and your issue remains:
  - Make sure you have the right application started.
  - Make sure the [issue template](.github/ISSUE_TEMPLATE) is respected.
  - Make sure your issue body is readable and [well formatted](https://guides.github.com/features/mastering-markdown).
  - Make sure the application you are using to reproduce the issue has a clean `node_modules` or `vendor` directory, meaning:
    - that you haven't made any inline changes to files in the `node_modules` or `vendor` folder
    - that you don't have any weird global dependency loops. The easiest way to double-check any of the above, if you aren't sure, is to run: `$ rm -rf node_modules && npm cache clear && npm install`.
