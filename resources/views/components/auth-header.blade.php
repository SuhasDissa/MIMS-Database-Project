@props([
    'heading' => '',
    'description' => '',
])

<div class="flex w-full flex-col text-center">
    <flux:heading size="xl">{{ $heading }}</flux:heading>
    <flux:subheading>{{ $description }}</flux:subheading>
</div>
