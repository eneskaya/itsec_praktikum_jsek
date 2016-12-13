Hello,
<br/>
Reset your email here please:
<a href="{{ env('APP_URL') }}/password/reset/{{ $token }}">{{ env('APP_URL') }}/password/reset/{{ $token }}</a>.
<br/>
This link is valid for {{$minutes}} minutes.
<br/>
Mfg
