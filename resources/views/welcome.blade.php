<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')
        <title>{{ config('app.name') }}</title>
    </head>
    <body>
        <div class="container mx-auto flex flex-col gap-6">
            <nav class="py-2">
                <ul class="flex justify-end items-center">
                    <li><a href="{{ route('filament.admin.pages.dashboard') }}">Bejelentkezés</a></li>
                </ul>
            </nav>
            <h1 align="center">Üdvözlünk rendszerünkön!</h1>
            <p>Ők már velünk teszik könnyebbé a foglalásaikat:</p>
            <a class="w-min" href="{{ route('ballonozz.home') }}">ballonozz.hu</a>
        </div>
    </body>
</html>
