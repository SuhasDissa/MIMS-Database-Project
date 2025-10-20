<?php

use App\Enums\EmployeePosition;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component {
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public string $phone = '';
    public string $position = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $employee = Auth::user();
        $this->first_name = $employee->first_name;
        $this->last_name = $employee->last_name;
        $this->email = $employee->email;
        $this->phone = $employee->phone;
        $this->position = $employee->position->value;
    }

    /**
     * Update the profile information for the currently authenticated employee.
     */
    public function updateProfileInformation(): void
    {
        $employee = Auth::user();

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:50'],
            'last_name' => ['required', 'string', 'max:50'],
            'phone' => ['required', 'string', 'max:15'],
            'position' => ['required', Rule::enum(EmployeePosition::class)],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:100',
                Rule::unique('employee', 'email')->ignore($employee->id)
            ],
        ]);

        $employee->fill($validated);

        if ($employee->isDirty('email')) {
            $employee->email_verified_at = null;
        }

        $employee->save();

        $this->dispatch('profile-updated', name: $employee->name);
    }

    /**
     * Send an email verification notification to the current employee.
     */
    public function resendVerificationNotification(): void
    {
        $employee = Auth::user();

        if ($employee->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $employee->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profile')" :subheading="__('Update your personal information')">
        <x-mary-form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <x-mary-input wire:model="first_name" :label="__('First Name')" type="text" required autofocus autocomplete="given-name" />

            <x-mary-input wire:model="last_name" :label="__('Last Name')" type="text" required autocomplete="family-name" />

            <x-mary-input wire:model="phone" :label="__('Phone')" type="text" required autocomplete="tel" />

            <x-mary-select
                wire:model="position"
                :label="__('Position')"
                :options="App\Enums\EmployeePosition::options()"
                required
            />

            <div>
                <x-mary-input wire:model="email" :label="__('Email')" type="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div class="mt-4">
                        <div class="text-sm text-warning">
                            {{ __('Your email address is unverified.') }}

                            <a class="link link-primary cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('Click here to re-send the verification email.') }}
                            </a>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="mt-2 text-sm font-medium text-success">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <x-slot:actions>
                <div class="flex items-center gap-4">
                    <x-mary-button type="submit" class="btn-primary" spinner="updateProfileInformation">
                        {{ __('Save') }}
                    </x-mary-button>

                    <x-action-message class="me-3" on="profile-updated">
                        {{ __('Saved.') }}
                    </x-action-message>
                </div>
            </x-slot:actions>
        </x-mary-form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
