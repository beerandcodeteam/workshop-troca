<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class UpdatePasswordForm extends Form
{
    #[Validate('required|string|current_password')]
    public string $current_password = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    public string $password_confirmation = '';
}
