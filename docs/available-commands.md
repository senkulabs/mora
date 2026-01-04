---
outline: deep
---

# Available Commands

## Core

The command `make:module` is the core of Mora to create modular Laravel. Just give the module name after that.

```sh
php artisan make:module ModuleName
```

The default rule is it will create a module inside `Modules` directory with Uppercase format.

- If user put module name like "guitar" then the module name will be "Guitar".
- If user put module name like "guitar-hero" then the module name will be "GuitarHero".
- If user put module name like "Guitar" then the module name will be "Guitar".
- if user put module name like "Guitar-Hero" then the module name will be "Guitar-Hero".

## Laravel

Mora utilise the Laravel's artisan command to create file like controller, model, and views with `--module` flag. Thanks to [InterNACHI/modular](https://github.com/InterNACHI/modular) for this idea. Here are the list of Laravel's artisan command that support in Mora:

- `make:cast`
- `make:channel`
- `make:class`,
- `make:command`
- `make:component`
- `make:controller`
- `make:enum`
- `make:event`
- `make:exception`
- `make:factory`
- `make:interface`
- `make:job`
- `make:listener`
- `make:mail`
- `make:middleware`
- `make:migration`
- `make:model`
- `make:notification`
- `make:observer`
- `make:policy`
- `make:provider`
- `make:resource`
- `make:rule`
- `make:scope`
- `make:seeder`
- `make:test`
- `make:trait`
- `make:view`

## Livewire and Volt

Mora also support the command to generate Livewire and Volt components by using `--module` flag. You just need to run `make:livewire`, `livewire:make`, or `make:volt` with `--module` flag.

## Mora

This package has several commands to support the modular Laravel.

- `mora:composer-require` for require composer package in a module
- `mora:npm-install` for install npm packages in a module
- `mora:test-namespace` for add module test namespace to root composer.json autoload-dev
- `mora:test-setup` for setup module tests in phpunit.xml and Pest.php (root Laravel project)
- `mora:vite` for configure Vite and set `"workspaces"` in package.json for modular Laravel architecture
