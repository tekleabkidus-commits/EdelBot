<?php

declare(strict_types=1);

use App\Models\MenuItem;
use App\Services\SettingsService;
use Database\Seeders\MenuSeeder;
use Database\Seeders\SettingsSeeder;

it('uses the Edel Bot name and never seeds links to another operator', function () {
    $this->seed(SettingsSeeder::class);
    $this->seed(MenuSeeder::class);

    $settings = app(SettingsService::class);
    $welcome = (array) $settings->get('welcome.message');

    expect($welcome['en'])->toContain('Edel Bot')
        ->and($settings->get('webapp.url'))->toBeNull()
        ->and(MenuItem::query()->whereNotNull('url')->count())->toBe(0)
        ->and(MenuItem::query()->whereHas('translations', fn ($q) => $q->where('label', 'ℹ️ About Edel Bot'))->exists())->toBeTrue();
});

it('ships the distinct violet Edel Bot theme', function () {
    expect(file_exists(public_path('css/edelbot-theme.css')))->toBeTrue()
        ->and(file_get_contents(public_path('css/edelbot-theme.css')))->toContain('#6d28d9')
        ->and(file_exists(public_path('edelbot-legacy-mark.svg')))->toBeTrue();
});
