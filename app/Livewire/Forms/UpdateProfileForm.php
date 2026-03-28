<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class UpdateProfileForm extends Form
{
    #[Validate]
    public string $username = '';

    #[Validate]
    public string $email = '';

    public function rules(): array
    {
        $userId = auth()->id();

        return [
            'username' => "required|string|min:3|max:20|unique:users,username,{$userId}",
            'email' => "required|email|unique:users,email,{$userId}",
        ];
    }
}
