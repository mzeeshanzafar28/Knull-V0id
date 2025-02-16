<x-mail::message>
<!-- Dark-Themed Header with Blood Red Horror Background -->
<table width="100%" bgcolor="#110000" cellpadding="0" cellspacing="0" border="0" style="max-width: 800px; margin: auto; background: radial-gradient(circle, #220000, #000000); padding-bottom: 50px;">
<tr>
<td align="center" style="padding: 40px;">
<img src="https://drive.google.com/thumbnail?id=1mTxOZXAbx90HqBDp3Xav_GdcAoibXAni" alt="Knull Void" width="250"
style="filter: drop-shadow(0 0 25px red); margin-top: 20px; border: 2px solid #ff0000; padding: 10px;">
</td>
</tr>
<tr>
<td align="center">
<h1 style="color: #ff0000; text-shadow: 0 0 15px #ff0000; margin: 20px 0; font-family: 'Creepster', sans-serif; font-size: 36px;">
Welcome to the Abyss
</h1>
</td>
</tr>
</table>

<!-- Dark & Red Introductory Text -->
<table width="100%" bgcolor="#220000" cellpadding="30" cellspacing="0" border="0">
<tr>
<td align="center">
<p style="color: #ff6666; font-size: 18px; margin: 0; font-weight: bold; font-family: 'Creepster', sans-serif;">
You have been chosen. Click the button below to verify your existence... if you dare.
</p>
</td>
</tr>
</table>

<!-- Blood Red Action Button -->
@isset($actionText)
<table width="100%" bgcolor="#110000" cellpadding="0" cellspacing="0" border="0">
<tr>
<td align="center" style="padding: 30px;">
<a href="{{ $actionUrl }}"
style="
background: linear-gradient(to right, #550000, #ff0000);
color: white;
padding: 20px 40px;
text-decoration: none;
font-size: 20px;
font-weight: bold;
border-radius: 8px;
box-shadow: 0 0 20px #ff0000;
text-transform: uppercase;
font-family: 'Creepster', sans-serif;
">
{{ $actionText }}
</a>
</td>
</tr>
</table>
@endisset

<!-- Outro Lines -->
<table width="100%" bgcolor="#220000" cellpadding="30" cellspacing="0" border="0">
<tr>
<td align="center">
<p style="color: #ffcccc; font-size: 16px; font-weight: bold; font-family: 'Creepster', sans-serif;">
If you did not summon this, no further action is required... yet.
</p>
</td>
</tr>
</table>

<!-- Subcopy with Glitchy Horror Effect -->
@isset($actionText)
<table width="100%" bgcolor="#110000" cellpadding="10" cellspacing="0" border="0">
<tr>
<td align="center">
<p style="color: #ff3333; text-shadow: 0 0 10px red; font-size: 14px;">
If the <b>"{{ $actionText }}"</b> button fails, copy and paste this cursed URL into your browser:
</p>
<p style="color: #ff1111; text-shadow: 0 0 10px red; font-size: 14px;">
<a href="{{ $actionUrl }}" style="color: #ff4444; text-decoration: underline;">
{{ $displayableActionUrl }}
</a>
</p>
</td>
</tr>
</table>
@endisset

<!-- Horror-Themed Footer -->
<table width="100%" bgcolor="#110000" cellpadding="20" cellspacing="0" border="0">
<tr>
<td align="center">
<p style="color: #ff4444; font-size: 18px; font-family: 'Creepster', sans-serif; text-shadow: 0 0 10px red;">May the Abyss Embrace You,</p>
<p style="font-weight: bold; color: #ff0000; font-size: 22px; text-shadow: 0 0 20px red;">{{ config('app.name') }}</p>
</td>
</tr>
<tr>
<td style="height: 80px;"></td> <!-- Spacer for extra height -->
</tr>
</table>
</x-mail::message>
