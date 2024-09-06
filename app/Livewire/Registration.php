<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;

class Registration extends Component
{
    public $name;

    public $email;

    public $phone;

    public $password;

    public $password_confirmation;

    public $registered = false;

    protected $rules = [
        'name' => 'required|min:6',
        'email' => 'required|email|unique:users',
        'phone' => 'required',
        'password' => 'required|confirmed|min:6',
    ];

    public function registration()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'password' => $this->password,
        ]);

        $user->assignRole('vásárló');

        $this->registered = true;
    }

    public function render()
    {
        return view('livewire.registration');
    }
}
