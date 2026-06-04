<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class RequestTable extends Component
{
    public $requests;
    public $route;

    public function __construct($requests, $route)
    {
        $this->requests = $requests;
         $this->route = $route;
    }

    public function render(): View
    {
        return view('components.request-table');
    }
}