@php
    use Illuminate\Support\Facades\URL;
@endphp
<x-mail::message>
# Dear {{ $user->name}},

We are pleased to welcome you to our platform. To ensure the security and authenticity of your account, we kindly request you to verify your account.

{{-- <x-mail::button :url="route('verification.verify', ['id' => $user->id])"> --}}
<x-mail::button :url="URL::signedRoute('verification.verify', ['id' => $user->id])">

Click Here to Verify Your Account
</x-mail::button>

Thank you for your attention to this matter.

Best Regards,<br>
Fligno Software Philippines, Inc.
</x-mail::message>