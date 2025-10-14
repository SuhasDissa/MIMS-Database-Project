<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class=" bg-base antialiased m-0 p-0">
                    {{ $slot }}

                    <x-mary-toast />
    </body>
</html>
