<?php

declare(strict_types=1);

namespace App\Modules\Shared\Providers;

use App\Modules\Shared\Livewire\DashboardTabs;
use App\Modules\Shared\Livewire\TerminalAuthCta;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class SharedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'shared');
        Livewire::component('shared::dashboard', DashboardTabs::class);
        Livewire::component('shared::auth-cta', TerminalAuthCta::class);
    }

    public function boot(): void {}
}
