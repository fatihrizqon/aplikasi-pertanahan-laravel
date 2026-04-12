<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PreserveQuery extends Component
{
    public $except;

    public function __construct($except = [])
    {
        $this->except = $except;
    }

    public function render()
    {
        return view('components.preserve-query');
    }
}

