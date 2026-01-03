# Mora

Modular Laravel with native artisan conventions.

For documentation, please visit [mora.senkulabs.net](https://mora.senkulabs.net).

## Features

### Intuitive Command

Mora has intuitive command to create controller, model, and view like InterNACHI/Modular.

### Livewire and Volt opt-in

Mora also has intuitive command for create Livewire and Volt components. It also register the Livewire and Volt components each time created.

### Decoupled

You only need to install Mora in the `require-dev` section of your `composer.json`, and your modules don't need depend on Mora itself.

## Credits

This package includes code derived from [internachi/modular](https://github.com/InterNACHI/modular) by InterNACHI, Inc. The following components were adapted from their work:

- Module registry and configuration system
- Modularized artisan make commands
- Module-aware seeder command

We thank the InterNACHI team for their excellent work on Laravel modular architecture.
