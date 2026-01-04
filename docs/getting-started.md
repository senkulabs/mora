---
outline: deep
---

# Getting Started

Mora package works in Laravel version 12. Install Mora package by run the command below.

```sh
composer require senkulabs/mora --dev
```

## Your First Modular Laravel

Let's create a first module in your Laravel project which is a Blog.

```sh
php artisan make:module Blog
```

This will create a `Blog` module inside of `Modules` directory in the Laravel project.

```tree
.
└── Modules/
    └── Blog/
        ├── app/
        │   └── Providers/
        │       └── BlogServiceProvider.php
        ├── database
        ├── lang
        ├── resources
        ├── routes/
        │   └── web.php
        ├── tests
        ├── .gitignore
        ├── composer.json
        └── package.json
```

Next, it will register `modules/blog` into composer.json.

```json
{
    "require": {
        "modules/blog": "*"
    },
    "repositories": {
        "modules/blog": {
            "type": "path",
            "url": "Modules/Blog"
        }
    }
}
```

To enable the `Blog` module, you need to manually run `composer update modules/blog` in order to make the `Blog` module to be autoloaded in your Laravel project.

Try to run `php artisan route:list`. You will see that the routes from `Blog` module has been added. See the `Modules/Blog/app/Providers/BlogServiceProvider.php` to make you more curious.

To see the `blog` route, run `composer run dev` or `php artisan serve` then access `http://localhost:8000/blog`. :)

## Vite for Modular Laravel

If you see in the `modules/blog`, there's no `vite.config.js` there like nWidart/laravel-modules did. This is intended because if we put `vite.config.js` in each module then it contradicts modular Laravel's design where modules **require** the core Laravel app to function. So, we tweak configuration in `package.json` and `vite.config.js` in the root project by run `mora:vite` command.

```sh
php artisan mora:vite
```

This command will:

- Make Vite detect `Modules` directory. Each time certain directories inside the `Modules` change then Vite will refresh the Laravel app in dev mode. Vite also include `Modules` directory in build mode.
- Add `workspaces` option in `package.json`. This will be useful when we want to install certain npm dependencies in a module instead of polluting the `package.json` in Laravel app. This will be discuss in the next section.

After that, we need to re-run `npm install` to "activate" workspace configuration.

> [!NOTE]
> Actually, the meaning of word "activate" is shared dependencies across workspaces get hoisted to the root `node_modules` to avoid duplication.

> [!TIP]
> If you're making significant changes to workspaces (adding/removing packages), sometimes it's cleaner to run command `rm -rf node_modules package-lock.json && npm install`. This ensures a fresh install without any stale symlinks or cached dependency trees.

Let's test it by update `app.js` and `app.css` in `Modules/Blog/resources`.

::: code-group

```js [Modules/Blog/resources/js/app.js]
console.log('foobar')
```

```css [Modules/Blog/resources/css/app.css]
.border-red {
    border: 1px solid red;
}
```

:::

Create a `<div>` with `border-red` inside the `index.blade.php` in `Modules/Blog/resources/views`.

```html{7}
<x-blog::layouts.master>
    <h1>Hello World</h1>

    <p>Module: Blog</p>
    <p>{{ __('greeting') }}</p>
    <p>{{ __('blog::messages.tagline') }}</p>
    <div class="border-red">I'm a red border</div>
</x-blog::layouts.master>
```

If we open the `https://localhost:8000/blog` again, we will see log `foobar` in dev tools and the div with border red in blog page.

### Bundle Module Assets in Vite

To bundle the module assets, you just need to run `npm run build` to let Vite build the assets. Then, let's open again the page by run `php artisan serve` to see blog page.

> [!NOTE]
> I choose only run `php artisan serve` instead of `composer run dev` to test that Vite has bundling the module assets.

## Livewire for Modular Laravel

Mora package support the command to generate Livewire component out-of-the-box. Just add `--module` flag in `make:livewire` or `livewire:make`.

> [!NOTE]
> Make sure you already install Livewire. Visit the [Livewire's documentation](https://livewire.laravel.com/docs/3.x/quickstart) to install Livewire.

Let's create classic `Counter` component in `Modules/Blog`.

```sh
php artisan make:livewire Counter --module=Blog
```

This will create `Counter` Livewire component inside `Modules/Blog/app/Livewire` and register `Counter` component inside the `BlogServiceProvider.php`. Let's update the code for the counter.

::: code-group

```php [Modules/Blog/app/Livewire/Counter.php]
<?php

namespace Modules\Blog\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public $count = 0;

    public function increase()
    {
        $this->count++;
    }

    public function decrease()
    {
        $this->count--;
    }

    public function render()
    {
        return view('blog::livewire.counter', [
            'count' => $this->count
        ]);
    }
}
```

```html [Modules/Blog/resources/views/livewire/counter.blade.php]
<div>
    <button wire:click="increase">+</button>
    <button wire:click="decrease">-</button>
    <span>{{ $count }}</span>
</div>
```

:::

Call `<livewire:blog::counter />` component inside `index.blade.php` in `Modules/Blog`. Enjoy play the counter.

```html{8}
<x-blog::layouts.master>
    <h1>Hello World</h1>

    <p>Module: Blog</p>
    <p>{{ __('greeting') }}</p>
    <p>{{ __('blog::messages.tagline') }}</p>

    <livewire:blog::counter />
</x-blog::layouts.master>
```

## Volt for Modular Laravel

Mora package support the command to generate Volt component out-of-the-box. Just add `--module` flag in `make:volt`.

> [!NOTE]
> Make sure you already install Livewire first then Volt. Visit the [Livewire's documentation](https://livewire.laravel.com/docs/3.x/quickstart) to install Livewire then visit [Volt's documentation](https://livewire.laravel.com/docs/3.x/volt).

Let's create `random-quote` component in `Modules/Blog` with class-based Volt component.

```sh
php artisan make:volt random-quote --class --module=Blog
```

This will create `random-quote` Volt component inside `Modules/Blog/resources/views/livewire` and register `random-quote` component inside the `BlogServiceProvider.php` in `registerVoltComponents()`.

> [!NOTE]
> Take a bit of time to see the code inside the `registerVoltComponents()` method in `BlogServiceProvider.php` to learn how to call the Livewire Volt component into a module.


Let's update the code for the `random-quote` component.

```php
<?php

use Illuminate\Foundation\Inspiring;
use Livewire\Volt\Component;

new class extends Component {
    public string $quote = '';

    public function mount(): void
    {
        $this->refreshQuote();
    }

    public function refreshQuote(): void
    {
        $this->quote = Inspiring::quote();
    }
}; ?>

<div class="p-6 bg-white dark:bg-zinc-800 rounded-lg shadow-md">
    <div class="flex flex-col items-center space-y-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-zinc-400" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/>
        </svg>
        
        <blockquote class="text-center text-lg text-zinc-700 dark:text-zinc-300 italic">
            {{ $quote }}
        </blockquote>
        
        <button 
            wire:click="refreshQuote"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 px-4 py-2 bg-zinc-100 dark:bg-zinc-700 hover:bg-zinc-200 dark:hover:bg-zinc-600 text-zinc-700 dark:text-zinc-300 rounded-md transition-colors disabled:opacity-50"
        >
            <svg 
                wire:loading.class="animate-spin" 
                xmlns="http://www.w3.org/2000/svg" 
                class="h-4 w-4" 
                fill="none" 
                viewBox="0 0 24 24" 
                stroke="currentColor"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span wire:loading.remove>New Quote</span>
            <span wire:loading>Loading...</span>
        </button>
    </div>
</div>
```

Call `<livewire:blog::counter />` component inside `index.blade.php` in `Modules/Blog`. Enjoy play the counter.

```html{9}
<x-blog::layouts.master>
    <h1>Hello World</h1>

    <p>Module: Blog</p>
    <p>{{ __('greeting') }}</p>
    <p>{{ __('blog::messages.tagline') }}</p>

    <livewire:blog::counter />
    <livewire:blog:random-quote />
</x-blog::layouts.master>
```


## Add Dependencies in Modular Laravel

The previous example seems trivial. Let's make it look like real.

First, let's make a migration table for Blog module.

```sh
php artisan make:migration create_posts_table --module=Blog
```

Update the posts migration schema in `Modules/Blog/database/migrations`.

```php{3,4,5}
Schema::create('posts', function (Blueprint $table) {
    $table->id();
    $table->string('slug');
    $table->string('title');
    $table->text('content');
    $table->timestamps();
});
```

Run the post migration schema with command `artisan migrate`.

```sh
php artisan migrate
```

Create a Post model in `Modules/Blog`.

```sh
php artisan make:model Post --module=Blog
```

Add `$fillable` property in Post model.


```php{10}
// Modules/Blog/app/Models/Post.php
<?php

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'slug', 'content'];
}
```

### Composer Dependency

Since Laravel version 12, there's a `Str::slug()` helper to generate slug title. But, in this section, we use `spatie/laravel-sluggable` in order to show you how to add dependencies in modular Laravel. To install `spatie/laravel-sluggable`, you need to use `mora:composer-require` command

```sh
php artisan mora:composer-require spatie/laravel-sluggable --module=Blog
```

This will add `spatie/laravel-sluggable` package to `Modules/Blog` but it's not installed into our Laravel app. To install it, we run command `composer update modules/blog`.

```sh
composer update modules/blog
```

Then, we use `Spatie\Sluggable\HasSlug` trait and the `Spatie\Sluggable\SlugOptions` class in Post model.

```php{6,7,11,15-20}
<?php

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model
{
    use HasSlug;

    protected $fillable = ['title', 'slug', 'content'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}
```

Create factory for Post model called `PostFactory` then use the following code below.

```sh
php artisan make:factory PostFactory --module=Blog --model=Post
```

```php
<?php

namespace Modules\Blog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Modules\Blog\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'content' => fake()->realText()
        ];
    }
}
```

Then, attach the PostFactory class to Post model.

```php{5-6,8,12,15}
<?php

namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Blog\Database\Factories\PostFactory;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[UseFactory(PostFactory::class)]
class Post extends Model
{
    use HasFactory, HasSlug;

    protected $fillable = ['title', 'slug', 'content'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }
}
```

Create a PostSeeder class inside `Modules/Blog` then use the following code below.

```sh
php artisan make:seed PostSeeder --module=Blog
```

```php
<?php

namespace Modules\Blog\Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Blog\Models\Post;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Post::factory()->count(10)->create();
    }
}
```

Then, run the PostSeeder to seed data to posts table.

```sh
php artisan db:seed --module=Blog --class=PostSeeder
```

> [!NOTE]
> Run `php artisan tinker` and use `Modules\Blog\Models\Post::all();` to see list of posts data.

### NPM Dependency

> [!WARNING]
> Make sure you already setup the [Vite for Modular Laravel](#vite-for-modular-laravel)!

Let's install [dayjs](https://day.js.org) library inside the `Blog` module to show relative time for posts data. Mora have command `mora:npm-install` to add dependencies inside `Blog` module.

```sh
php artisan mora:npm-install dayjs --module=Blog
```

This will add dayjs to `package.json` and update `package-lock.json` inside `Blog` module.

We just add this library but we don't install it. To install it, we need to run `npm install`.

```sh
npm install
```

In `Modules/Blog/resources/js/app.js`, we register dayjs library globally in order to be accessed by `PostList` Livewire component.

```js
import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

dayjs.extend(relativeTime);

window.dayjs = dayjs;
```

Create `PostList` Livewire component inside `Modules/Blog`.

```sh
php artisan make:livewire PostList --module=Blog
```

Update the PostList Livewire component code.

::: code-group

```php [Modules/Blog/app/Livewire/PostList.php]
<?php

namespace Modules\Blog\Livewire;

use Livewire\Component;
use Modules\Blog\Models\Post;

class PostList extends Component
{
    public function render()
    {
        $posts = Post::get();

        return view('blog::livewire.post-list', [
            'posts' => $posts,
        ]);
    }
}
```

```html [Modules/Blog/resources/views/livewire/post-list.blade.php]
<div>
    @foreach($posts as $post)
        <div>
            <h3>{{ $post->title }}</h3>
            <span x-data="{ time: '' }"
                data-created-at="{{ $post->created_at->toISOString() }}"
                x-init="
                    const update = () => time = dayjs($el.dataset.createdAt).fromNow();
                    update();
                    setInterval(update, 60_000);
                "
                x-text="time">
            </span>
        </div>
    @endforeach
</div>
```

:::

Then, put `<livewire:blog::post-list />` tag into `index.blade.php` in `Modules/Blog`.

## Testing

Mora has two commands to setup testing for modular Laravel: `mora:test-setup` and `mora:test-namespace`.

The `mora:test-setup` will update the `phpunit.xml` and `Pest.php` in the root project.

```sh
php artisan mora:test-setup
```

The `mora:test-namespace` will add certain module into a `autoload-dev` section in `composer.json` in root Laravel project.

```sh
php artisan mora:test-namespace --module=Blog
```

To be able run test for `Blog` module, you need to run `composer dump-autoload`.

```sh
composer dump-autoload
```

Let's add trivial tests in `Modules/Blog`.

First, create unit and feature test files.

```sh
php artisan make:test PostTest --module=Blog --pest --unit
```

```sh
php artisan make:test PostTest --module=Blog --pest
```

Update code for unit and feature test files.

::: code-group

```php [Modules/Blog/tests/Unit/PostTest.php]
<?php

use Modules\Blog\Models\Post;

it('generates a slug from the title', function () {
    $post = new Post(['title' => 'Hello World']);

    expect($post->title)->toBe('Hello World');
});
```

```php [Modules/Blog/tests/Feature/PostTest.php]
<?php

use Modules\Blog\Models\Post;

use function Pest\Laravel\get;

test('can list posts on the blog index page', function () {
    Post::factory(3)->create();

    $response = get('/blog');

    $response->assertStatus(200);
});
```

:::

Then, run `php artisan test` for `Blog` module.

```sh
php artisan test Modules/Blog/tests
```