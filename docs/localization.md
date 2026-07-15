# Localization

- [Introduction](#introduction)
- [Default Locale](#default-locale)
- [Administrator Locale Menu](#administrator-locales)
- [Translations In Config Files](#config-translation)
- [Available Languages](#available-languages)
- [Adding A Language](#contributing)

<a name="introduction"></a>
## Introduction

Administrator uses Laravel's localization system. Package translations live in `src/lang/{locale}/administrator.php` and `src/lang/{locale}/frontend.php`. The application locale and the Administrator `locales` option determine which language appears in the UI.

<a name="default-locale"></a>
## Default Locale

Set the application's default locale in `config/app.php`:

```php
return array(
    'locale' => 'en',
);
```

In Laravel 13 applications, you can also wire this value to an environment variable in your application config:

```dotenv
APP_LOCALE=en
```

<a name="administrator-locales"></a>
## Administrator Locale Menu

To show a language selector in the administrator header, add the supported locales to `config/administrator.php`:

```php
return array(
    'locales' => array('en', 'ko', 'ja'),
);
```

If `locales` is empty, Administrator does not render a separate locale menu.

<a name="config-translation"></a>
## Translations In Config Files

Global configuration, model configuration, and settings configuration files may use Laravel translation helpers:

```php
return array(
    'title' => __('admin.users.title'),
    'single' => __('admin.users.single'),
    'model' => App\Models\User::class,
    'columns' => array(
        'name' => array(
            'title' => __('admin.users.name'),
        ),
    ),
    'edit_fields' => array(
        'name' => array(
            'title' => __('admin.users.name'),
            'type' => 'text',
        ),
    ),
);
```

Application translation files should follow Laravel's standard `lang/{locale}` or `resources/lang/{locale}` layout, depending on the host application structure.

<a name="available-languages"></a>
## Available Languages

Administrator currently includes the following locale directories:

```text
ar az bg ca da de en es eu fi fr hr hu it ja nb nl pl pt pt-BR ro ru se si sk sr tr uk vi zh-CN zh-TW
```

<a name="contributing"></a>
## Adding A Language

Add both translation files for the new locale:

```text
src/lang/en/
  administrator.php
  frontend.php
```

Use the existing `src/lang/en` files as the base structure, then submit a pull request or open an issue on [GitHub](https://github.com/SaAkSin/Laravel-Administrator/issues).
