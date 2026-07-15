# Installation

- [Requirements](#requirements)
- [Install With Composer](#composer)
- [Publish Assets And Config](#assets)
- [Administrator Config](#administrator-config)
- [Model Config](#model-config)
- [Settings Config](#settings-config)
- [Laravel Octane](#octane)
- [Update Workflow](#updates)

<a name="requirements"></a>
## Requirements

Laravel Administrator 13.0 targets Laravel `^13.0` and PHP `^8.3`. Keep the host Laravel application and PHP runtime patched within those supported release lines.

The package ships built Vite assets for normal Composer installation. You only need Node.js when you are developing the package assets or building the VitePress documentation site.

<a name="composer"></a>
## Install With Composer

Install the package in your Laravel application:

```bash
composer require "saaksin/laravel-administrator:^13.0"
```

Laravel package auto-discovery registers the service provider automatically. If auto-discovery is disabled, register it manually:

```php
'providers' => array(
    SaAkSin\Administrator\AdministratorServiceProvider::class,
),
```

<a name="assets"></a>
## Publish Assets And Config

Publish the configuration file and package assets:

```bash
php artisan vendor:publish --tag=laravel-administrator --force
```

This publishes `config/administrator.php` and the package assets used by the administrator UI. The UI uses Vite-built JavaScript and CSS, Alpine.js, Tailwind CSS, Quill for `wysiwyg2`, and the bundled CKEditor 4 files for the legacy `wysiwyg` field.

<a name="administrator-config"></a>
## Administrator Config

The main configuration file is `config/administrator.php`. At minimum, configure the menu and the directories that contain model and settings configuration files:

```php
return array(
    'title' => 'Administrator',
    'uri' => 'admin',
    'middleware' => array('web', 'auth'),
    'model_config_path' => base_path('administrator'),
    'settings_config_path' => base_path('administrator/settings'),
    'menu' => array(
        'users',
        'Settings' => array('settings.site'),
    ),
);
```

Create the configuration directories if they do not exist:

```bash
mkdir -p administrator/settings
```

For all available options, see the [configuration docs](./configuration.md).

<a name="model-config"></a>
## Model Config

Model configuration files describe Eloquent resources that should appear in the administrator UI. Their file names map to entries in the `menu` option.

For example, `administrator/users.php` can describe `App\Models\User`:

```php
return array(
    'title' => 'Users',
    'single' => 'user',
    'model' => App\Models\User::class,
    'columns' => array(
        'id',
        'name',
        'email',
    ),
    'edit_fields' => array(
        'name' => array(
            'title' => 'Name',
            'type' => 'text',
        ),
        'email' => array(
            'title' => 'Email',
            'type' => 'text',
        ),
    ),
);
```

For details, see the [model configuration docs](./model-configuration.md).

<a name="settings-config"></a>
## Settings Config

Settings configuration files manage administrative values that are not best represented by an Eloquent model. Their file names map to `settings.*` entries in the `menu` option.

For details, see the [settings configuration docs](./settings-configuration.md).

<a name="octane"></a>
## Laravel Octane

Administrator isolates request-specific configuration, fields, columns, actions, permissions, pagination state, and locale when a Laravel 13 host application runs under Laravel Octane with RoadRunner, Swoole, or FrankenPHP. Octane remains an optional host runtime: this package does not add `laravel/octane` as a production dependency or install a server driver for the application.

Install and configure a Laravel 13-compatible Octane 2.x release in the host application when you choose this runtime. The `administrator.ready` event is dispatched once while the application or worker boots; do not use it to reset request-specific state.

After deploying package code or configuration changes to a running Octane environment, reload the workers so that the new code is booted:

```bash
php artisan octane:reload
```

<a name="updates"></a>
## Update Workflow

After updating the package, publish the latest assets again:

```bash
composer update saaksin/laravel-administrator
php artisan vendor:publish --tag=laravel-administrator --force
```

If the host application uses Octane, run `php artisan octane:reload` after this update workflow.

If you automate publishing in the host application's `composer.json`, keep the command scoped to the package tag:

```json
{
  "scripts": {
    "post-update-cmd": [
      "php artisan vendor:publish --tag=laravel-administrator --force"
    ]
  }
}
```
