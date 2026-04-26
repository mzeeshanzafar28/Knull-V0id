@extends('emails.layouts.horror')

@section('title', $subject)

@section('content')
<h1 style="color: #ff4444; text-shadow: 0 0 10px #ff0000; text-align: center;">
Welcome to the Abyss
</h1>

<p style="color: #ffcccc; font-size: 16px; text-align: center;">
{{ $content }}
</p>

@if(!empty($actionUrl))
<p style="text-align: center; margin-top: 20px;">
<a href="{{ $actionUrl }}" style="
background: linear-gradient(to right, #550000, #ff0000);
color: white;
padding: 15px 30px;
text-decoration: none;
font-size: 18px;
font-weight: bold;
border-radius: 8px;
box-shadow: 0 0 10px #ff0000;
display: inline-block;">
{{ $actionText ?? 'Verify Email Address' }}
</a>
</p>
@endif

<p style="color: #ffcccc; font-size: 14px; text-align: center; margin-top: 20px;">
If you did not create an account, no further action is required... for now.
</p>

<p style="color: #ff4444; text-align: center; margin-top: 20px;">
May the Abyss Embrace You,<br>
<strong style="color: #ff0000;">{{ config('app.name') }}</strong>
</p>
@endsection
