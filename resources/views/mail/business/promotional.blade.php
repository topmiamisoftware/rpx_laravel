@component('mail::message')

    {{ $emailBody  }}

@component('mail::button', ['url' => $businessLink])
Check out {{ $businessName  }} at SpotBie!
@endcomponent

Thanks, {{ $firstName }}!
@endcomponent
