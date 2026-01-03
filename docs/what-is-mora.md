---
outline: deep
---

# What is Mora?

Mora stands for **Modular Laravel**. It's a tool to make your Laravel project to be modular with native artisan conventions.

Mora was born because of uncomfortable Developer Experience (DX) in nWidart/laravel-modules and bit jealous with InterNACHI/modular artisan commands.

Here are the list of the uncomfortable DX in nWidart/laravel-modules:

- The command to create controller, model, view, and so on are not intuitive. To create a BlogController, you need to run `php artisan module:make-controller BlogController Blog`. Why not `php artisan make:controller BlogController --module=Blog` like InterNACHI/modular approach?
- To make it work with Livewire and Volt, you need to install additional composer package like [mhmiton/laravel-modules-livewire](https://github.com/mhmiton/laravel-modules-livewire).
- It has `modules_statuses.json` to enable and disable module.
- The module scaffolding generate unnecessary file like `vite.config.js` inside each module directory. You also need to put `vite-module-loader.js` into the root project then import it on `vite.config.js` in the root project. I think the we only need one `vite.config.js` in the root project to rule all of it.
- The nWidart/laravel-modules package is coupled. It means, you must install it on `require` section of your `composer.json`, and your modules depend on nWidart/laravel-modules.

In InterNACHI/Modular, we have some features that I think it’s better than nWidart/laravel-modules. Here are the list:

- The command to create controller, model, view, and so on are intuitive. You just jun `php artisan make:controller BlogController` and append `--module` after that.
- It doesn’t need to have complicated things in nWidart/laravel-modules like `module_statuses.json`, `vite-module-loader.js`.
- It follows Laravel conventions.
- The module itself treat as composer package.

Although InterNACHI/modular is better than nWidart/laravel-modules, there are things that I’m not comfortable with it:

- It doesn’t have support for Livewire Volt.
- The route output in InterNACHI/modular generate like this `app-modules/blog/routes/blog-routes.php` instead of `app-modules/blog/routes/web.php`.
- It doesn't handle how to make Vite can run in modular Laravel.

Some programmers have their own way to organize the code when facing with modular Laravel. They organize the code into `Modules`, `modules`, `src`, or `app-modules`. But, 60 to 80% use case they use `Modules` directory.

In short, Mora is a combination of the great things in nWidart/Laravel-Modules and interNACHI/Modular.

> Hold on! Why don’t you contribute into those packages to make it better instead of create the new packages?

I was thinking about that before. I can make PR(s) to make those packages better. But, it takes (quite long) time for the owner to decide if it’s worth. If I make a new package, I can deliver fast improvements and have own control on my package. 🙂

Back to the topic, Mora has features like:

- Intuitive command to create controller, model, view, and so on like InterNACHI/modular.
- It has support for Livewire Volt.
- It has better approach to make Vite run in modular Laravel by run `mora:vite` command.
- It has better approach to install npm dependencies and composer package dependencies in a  module with command `mora:npm-install` and `mora:composer-require`.
- It’s decoupled. You only need to install Mora in the `require-dev` section of your `composer.json`, and your modules don't need depend on Mora itself.

Think Mora as a modular Laravel scaffolding. 🙂

Let's get started.