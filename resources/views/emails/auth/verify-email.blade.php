<x-mail::layout>
    @slot('header')
        <x-mail::header :url="config('app.url')">
            <img src="{{ asset('images/logo.png') }}" alt="Knull Void" style="height: 50px;">
        </x-mail::header>
    @endslot

    <x-mail::panel style="background: #1a1a1a; color: #ffcccc; border-color: #4a0000;">
        # Welcome, Lost Soul! 👻

        You have been chosen. Click the button below to verify your existence... if you dare.
    </x-mail::panel>

    <x-mail::button :url="$actionUrl" color="blood-red" style="margin: 2rem 0;">
        {{ $actionText }}
    </x-mail::button>

    <x-mail::panel style="background: #1a1a1a; color: #ffcccc; border-color: #4a0000;">
        If you did not create an account, no further action is required... for now.
    </x-mail::panel>

    @slot('subcopy')
        <x-mail::subcopy style="color: #ff6666;">
            If the "{{ $actionText }}" button fails, copy and paste this cursed URL into your web browser:
            <span style="color: #ff4444; word-break: break-all;">{{ $displayableActionUrl }}</span>
        </x-mail::subcopy>
    @endslot

    @slot('footer')
        <x-mail::footer style="color: #ff4444;">
            © {{ date('Y') }} {{ config('app.name') }}. May the Abyss Embrace You.
        </x-mail::footer>
    @endslot
</x-mail::layout>
