Hello,
<br/>
Please confirm your signup here:
<a href="{{ env('APP_URL') }}/email/confirm?id={{ $id }}&confirmation={{ $token }}">{{ env('APP_URL') }}/email/confirm?id={{ $id }}&confirmation={{ $token }}</a>.
<br/>
This link is valid for {{$minutes}} minutes.
<br/>
Mfg
