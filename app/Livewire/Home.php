<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public function login()
    {
        // TODO .env 'https://utasfoglalo.hu/' . 'user'
        return redirect('https://utasfoglalo.hu/user');
    }

    public function render()
    {
        return view('livewire.home')->title(env('APP_NAME'));
    }
}
