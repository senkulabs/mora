<?php

namespace SenkuLabs\Mora\Providers;

use Illuminate\Console\Application as Artisan;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as OriginalMigrateMakeCommand;
use Illuminate\Support\ServiceProvider;
use SenkuLabs\Mora\Commands\Laravel as LaravelCommands;
use SenkuLabs\Mora\Commands\MakeModuleCommand;

class ModularizedCommandsServiceProvider extends ServiceProvider
{
    /**
     * Laravel command aliases mapped to our replacement classes.
     */
    protected array $overrides = [
        'command.cast.make' => LaravelCommands\CastMakeCommand::class,
        'command.controller.make' => LaravelCommands\ControllerMakeCommand::class,
        'command.console.make' => LaravelCommands\CommandMakeCommand::class,
        'command.channel.make' => LaravelCommands\ChannelMakeCommand::class,
        'command.event.make' => LaravelCommands\EventMakeCommand::class,
        'command.exception.make' => LaravelCommands\ExceptionMakeCommand::class,
        'command.factory.make' => LaravelCommands\FactoryMakeCommand::class,
        'command.job.make' => LaravelCommands\JobMakeCommand::class,
        'command.listener.make' => LaravelCommands\ListenerMakeCommand::class,
        'command.mail.make' => LaravelCommands\MailMakeCommand::class,
        'command.middleware.make' => LaravelCommands\MiddlewareMakeCommand::class,
        'command.model.make' => LaravelCommands\ModelMakeCommand::class,
        'command.notification.make' => LaravelCommands\NotificationMakeCommand::class,
        'command.observer.make' => LaravelCommands\ObserverMakeCommand::class,
        'command.policy.make' => LaravelCommands\PolicyMakeCommand::class,
        'command.provider.make' => LaravelCommands\ProviderMakeCommand::class,
        'command.request.make' => LaravelCommands\RequestMakeCommand::class,
        'command.resource.make' => LaravelCommands\ResourceMakeCommand::class,
        'command.rule.make' => LaravelCommands\RuleMakeCommand::class,
        'command.seeder.make' => LaravelCommands\SeederMakeCommand::class,
        'command.test.make' => LaravelCommands\TestMakeCommand::class,
        'command.component.make' => LaravelCommands\ComponentMakeCommand::class,
        'command.view.make' => LaravelCommands\ViewMakeCommand::class,
        'command.scope.make' => LaravelCommands\ScopeMakeCommand::class,
        'command.enum.make' => LaravelCommands\EnumMakeCommand::class,
        'command.class.make' => LaravelCommands\ClassMakeCommand::class,
        'command.trait.make' => LaravelCommands\TraitMakeCommand::class,
        'command.interface.make' => LaravelCommands\InterfaceMakeCommand::class,
    ];

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->booted(function () {
            Artisan::starting(function ($artisan) {
                $artisan->add(new MakeModuleCommand());
                $this->registerMakeCommandOverrides();
                $this->registerMigrationCommandOverrides();
            });
        });
    }

    /**
     * Register the Make command overrides.
     */
    protected function registerMakeCommandOverrides(): void
    {
        foreach ($this->overrides as $alias => $className) {
            // Skip if the parent class doesn't exist (for Laravel version compatibility)
            $parentClass = get_parent_class($className);
            if ($parentClass && ! class_exists($parentClass)) {
                continue;
            }

            $this->app->singleton($alias, $className);

            if ($parentClass) {
                $this->app->singleton($parentClass, $className);
            }
        }
    }

    /**
     * Register the Migration command overrides.
     */
    protected function registerMigrationCommandOverrides(): void
    {
        $this->app->singleton('command.migrate.make', function ($app) {
            return new LaravelCommands\MigrationMakeCommand(
                $app['migration.creator'],
                $app['composer']
            );
        });

        $this->app->singleton(OriginalMigrateMakeCommand::class, function ($app) {
            return new LaravelCommands\MigrationMakeCommand(
                $app['migration.creator'],
                $app['composer']
            );
        });
    }
}
