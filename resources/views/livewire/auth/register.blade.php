<?php

use App\Enums\EmployeePosition;
use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $position = '';
    public string $nic_num = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:100', 'unique:employee,email'],
            'phone' => ['required', 'string', 'max:15'],
            'position' => ['required', Rule::enum(EmployeePosition::class)],
            'nic_num' => ['required', 'string', 'max:12', 'unique:employee,nic_num'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['branch_id'] = Branch::first()->id; // Assign to first branch by default
        $validated['is_active'] = true;

        event(new Registered(($employee = Employee::create($validated))));

        Auth::login($employee);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :heading="__('Register')" :description="__('Please enter your details to create an account.')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <x-mary-form wire:submit="register" class="space-y-6">
        <!-- First Name -->
        <x-mary-input
            wire:model="first_name"
            :label="__('First Name')"
            type="text"
            required
            autofocus
            autocomplete="given-name"
            :placeholder="__('First name')"
        />

        <!-- Last Name -->
        <x-mary-input
            wire:model="last_name"
            :label="__('Last Name')"
            type="text"
            required
            autocomplete="family-name"
            :placeholder="__('Last name')"
        />

        <!-- Email Address -->
        <x-mary-input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Phone -->
        <x-mary-input
            wire:model="phone"
            :label="__('Phone')"
            type="text"
            required
            autocomplete="tel"
            :placeholder="__('Phone number')"
        />

        <!-- Position -->
        <x-mary-select
            wire:model="position"
            :label="__('Position')"
            :options="App\Enums\EmployeePosition::options()"
            option-label="label"
            option-value="value"
            required  
        />


        <!-- NIC Number -->
        <x-mary-input
            wire:model="nic_num"
            :label="__('NIC Number')"
            type="text"
            required
            :placeholder="__('National ID number')"
        />

        <!-- Password -->
        <x-mary-input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
        />

        <!-- Confirm Password -->
        <x-mary-input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
        />

        <x-slot:actions>
            <x-mary-button type="submit" class="btn-primary w-full" spinner="register">
                {{ __('Create account') }}
            </x-mary-button>
        </x-slot:actions>
    </x-mary-form>

    <div class="text-center text-sm">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ route('login') }}" wire:navigate class="link link-primary">{{ __('Log in') }}</a>
    </div>
</div>
