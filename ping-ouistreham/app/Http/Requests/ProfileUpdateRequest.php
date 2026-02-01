<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            
            // Tes ajouts pour le SaaS Ping
            'license_number' => ['required', 'string', 'max:20'], 
            'points' => ['required', 'integer', 'min:500', 'max:4000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'club' => ['nullable', 'string', 'max:255'],
        ];
    }
}