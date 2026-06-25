<?php

declare(strict_types=1);

namespace App\Modules\Shared\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class TerminalAuthCta extends Component
{
    public string $title = 'Account Allocation Locked';
    public string $description = 'Please sign in to establish a session verification context and track your live application data parameters.';

    public function mount(?string $title = null, ?string $description = null): void
    {
        if ($title) {
            $this->title = $title;
        }
        if ($description) {
            $this->description = $description;
        }
    }

    public function render(): View
    {
        return view('livewire.terminal-auth-cta');
    }
}
