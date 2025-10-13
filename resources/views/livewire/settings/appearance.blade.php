<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <div class="space-y-4" x-data="{ theme: localStorage.getItem('theme') || 'system' }">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <label class="cursor-pointer">
                    <input type="radio" name="theme" value="light" class="hidden" x-model="theme" @change="localStorage.setItem('theme', 'light'); document.documentElement.classList.remove('dark')">
                    <div class="card bg-base-100 border-2 transition-all" :class="theme === 'light' ? 'border-primary' : 'border-base-300'">
                        <div class="card-body items-center text-center">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <h3 class="font-semibold">{{ __('Light') }}</h3>
                            <p class="text-sm text-base-content/70">{{ __('Light theme') }}</p>
                        </div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="theme" value="dark" class="hidden" x-model="theme" @change="localStorage.setItem('theme', 'dark'); document.documentElement.classList.add('dark')">
                    <div class="card bg-base-100 border-2 transition-all" :class="theme === 'dark' ? 'border-primary' : 'border-base-300'">
                        <div class="card-body items-center text-center">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            <h3 class="font-semibold">{{ __('Dark') }}</h3>
                            <p class="text-sm text-base-content/70">{{ __('Dark theme') }}</p>
                        </div>
                    </div>
                </label>

                <label class="cursor-pointer">
                    <input type="radio" name="theme" value="system" class="hidden" x-model="theme" @change="localStorage.setItem('theme', 'system'); window.matchMedia('(prefers-color-scheme: dark)').matches ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')">
                    <div class="card bg-base-100 border-2 transition-all" :class="theme === 'system' ? 'border-primary' : 'border-base-300'">
                        <div class="card-body items-center text-center">
                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <h3 class="font-semibold">{{ __('System') }}</h3>
                            <p class="text-sm text-base-content/70">{{ __('Use system preference') }}</p>
                        </div>
                    </div>
                </label>
            </div>
        </div>
    </x-settings.layout>
</section>
