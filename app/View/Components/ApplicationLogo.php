<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Closure;

class ApplicationLogo extends Component
{
    /**
     * Create a new component instance.
     */
    public ?string $logoPath;

    public ?string $siteName;

    public function __construct(?string $logoPath = null, ?string $siteName = null)
    {
        $this->logoPath = $logoPath;
        $this->siteName = $siteName;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.application-logo');
    }
}
