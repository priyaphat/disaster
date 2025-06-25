<x-mail::layout>
    {{-- Header --}}
    <x-slot:header>
        <x-mail::header :url="config('app.url')">
            {{ config('app.name') }}
        </x-mail::header>
    </x-slot:header>

    {{-- Body --}}
    {{!! $slot !!}}

    {{-- Subcopy --}}
    <x-slot:subcopy>
        <x-mail::subcopy>
            หากคุณต้องการความช่วยเหลืออื่นๆ สามารถติดต่อได้ที่ <br>
            สำนักงาน เวลาทำการ 8.00 - 16.00 น.
        </x-mail::subcopy>
    </x-slot:subcopy>

    {{-- Footer --}}
    <x-slot:footer>
        <x-mail::footer>
            <!-- © {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.') -->
        </x-mail::footer>
    </x-slot:footer>
</x-mail::layout>
