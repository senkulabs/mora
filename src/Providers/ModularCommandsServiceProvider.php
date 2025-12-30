<?php

namespace SenkuLabs\Mora\Providers;

use Illuminate\Console\Application as Artisan;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as OriginalMigrateMakeCommand;
use Illuminate\Support\ServiceProvider;
use Livewire\Features\SupportConsoleCommands\Commands\MakeCommand as MakeLivewireCommand;
use SenkuLabs\Mora\Console\Commands\Make\MakeLivewire;
use SenkuLabs\Mora\Console\Commands\Make\MakeModule;
use SenkuLabs\Mora\Console\Commands\Database\SeedCommand;
use SenkuLabs\Mora\Console\Commands\Make\MakeCast;
use SenkuLabs\Mora\Console\Commands\Make\MakeChannel;
use SenkuLabs\Mora\Console\Commands\Make\MakeClass;
use SenkuLabs\Mora\Console\Commands\Make\MakeCommand;
use SenkuLabs\Mora\Console\Commands\Make\MakeComponent;
use SenkuLabs\Mora\Console\Commands\Make\MakeController;
use SenkuLabs\Mora\Console\Commands\Make\MakeEnum;
use SenkuLabs\Mora\Console\Commands\Make\MakeEvent;
use SenkuLabs\Mora\Console\Commands\Make\MakeException;
use SenkuLabs\Mora\Console\Commands\Make\MakeFactory;
use SenkuLabs\Mora\Console\Commands\Make\MakeInterface;
use SenkuLabs\Mora\Console\Commands\Make\MakeJob;
use SenkuLabs\Mora\Console\Commands\Make\MakeListener;
use SenkuLabs\Mora\Console\Commands\Make\MakeMail;
use SenkuLabs\Mora\Console\Commands\Make\MakeMiddleware;
use SenkuLabs\Mora\Console\Commands\Make\MakeMigration;
use SenkuLabs\Mora\Console\Commands\Make\MakeModel;
use SenkuLabs\Mora\Console\Commands\Make\MakeNotification;
use SenkuLabs\Mora\Console\Commands\Make\MakeObserver;
use SenkuLabs\Mora\Console\Commands\Make\MakePolicy;
use SenkuLabs\Mora\Console\Commands\Make\MakeProvider;
use SenkuLabs\Mora\Console\Commands\Make\MakeRequest;
use SenkuLabs\Mora\Console\Commands\Make\MakeResource;
use SenkuLabs\Mora\Console\Commands\Make\MakeRule;
use SenkuLabs\Mora\Console\Commands\Make\MakeScope;
use SenkuLabs\Mora\Console\Commands\Make\MakeSeeder;
use SenkuLabs\Mora\Console\Commands\Make\MakeTest;
use SenkuLabs\Mora\Console\Commands\Make\MakeTrait;
use SenkuLabs\Mora\Console\Commands\Make\MakeView;

class ModularCommandsServiceProvider extends ServiceProvider
{
    /**
     * Laravel command aliases mapped to our replacement classes.
     */
    protected array $overrides = [
        'command.cast.make' => MakeCast::class,
        'command.controller.make' => MakeController::class,
        'command.console.make' => MakeCommand::class,
        'command.channel.make' => MakeChannel::class,
        'command.event.make' => MakeEvent::class,
        'command.exception.make' => MakeException::class,
        'command.factory.make' => MakeFactory::class,
        'command.job.make' => MakeJob::class,
        'command.listener.make' => MakeListener::class,
        'command.mail.make' => MakeMail::class,
        'command.middleware.make' => MakeMiddleware::class,
        'command.model.make' => MakeModel::class,
        'command.notification.make' => MakeNotification::class,
        'command.observer.make' => MakeObserver::class,
        'command.policy.make' => MakePolicy::class,
        'command.provider.make' => MakeProvider::class,
        'command.request.make' => MakeRequest::class,
        'command.resource.make' => MakeResource::class,
        'command.rule.make' => MakeRule::class,
        'command.seeder.make' => MakeSeeder::class,
        'command.test.make' => MakeTest::class,
        'command.component.make' => MakeComponent::class,
        'command.view.make' => MakeView::class,
        'command.scope.make' => MakeScope::class,
        'command.enum.make' => MakeEnum::class,
        'command.class.make' => MakeClass::class,
        'command.trait.make' => MakeTrait::class,
        'command.interface.make' => MakeInterface::class,
        'command.seed' => SeedCommand::class,
    ];

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->booted(function () {
            Artisan::starting(function ($artisan) {
                $artisan->add(new MakeModule());
                $this->registerMakeCommandOverrides();
                $this->registerMigrationCommandOverrides();
                $this->registerLivewireOverrides($artisan);
            });
        });
    }

    /**
     * Register the Make command overrides.
     */
    protected function registerMakeCommandOverrides(): void
    {
        foreach ($this->overrides as $alias => $className) {
            $this->app->singleton($alias, $className);
            $this->app->singleton(get_parent_class($className), $className);
        }
    }

    /**
     * Register the Migration command overrides.
     */
    protected function registerMigrationCommandOverrides(): void
    {
        // Laravel 8
        $this->app->singleton('command.migrate.make', function($app) {
            return new MakeMigration($app['migration.creator'], $app['composer']);
        });

        // Laravel 9
        $this->app->singleton(OriginalMigrateMakeCommand::class, function($app) {
            return new MakeMigration($app['migration.creator'], $app['composer']);
        });
    }

    protected function registerLivewireOverrides(Artisan $artisan)
    {
        // Don't register commands if Livewire isn't installed
        if (! class_exists(MakeLivewireCommand::class)) {
            return;
        }

        // Replace the resolved command with our subclass
        $artisan->resolveCommands(MakeLivewire::class);

        // Ensure that if 'make:livewire' or 'livewire:make' is resolved from the container
        // in the future, our subclass is used instead
        $this->app->extend(MakeLivewireCommand::class, function () {
            return new MakeLivewire();
        });
    }
}
