<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class=" bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900 m-0 p-0">
                    {{ $slot }}

    </body>
</html>
