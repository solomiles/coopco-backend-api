@component('mail::message')
# {{ $data['title']}}

The body of your message.
{{ $data['body']}}
@component('mail::button', ['url' => ''])
Button Text
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
