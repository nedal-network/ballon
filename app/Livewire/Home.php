<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public function login()
    {
        return redirect('https://utasfoglalo.hu');
    }

    public function render()
    {
        return view('livewire.home')->title(env('APP_NAME'));
    }
}
