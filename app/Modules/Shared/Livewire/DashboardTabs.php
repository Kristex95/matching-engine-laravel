<?php

declare(strict_types=1);

namespace App\Modules\Shared\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DashboardTabs extends Component
{
    // Default active tab
    public string $activeTab = 'open-orders';

    public function selectTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render(): View
    {
        return view('livewire.trading.dashboard-tabs');
    }
}
