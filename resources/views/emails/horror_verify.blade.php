<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Verify Your Existence</title>
<style>
/* Reset some basic styles for email compatibility */
body, table, td {
margin: 0; 
padding: 0;
font-family: Arial, sans-serif;
}
/* Force the entire background to be dark red */
.outer {
background-color: #220000; 
width: 100%; 
min-height: 100vh;
}
.container {
width: 600px; 
background-color: #110000; 
margin: 30px auto; 
border-radius: 10px;
box-shadow: 0 0 20px #ff0000;
overflow: hidden;
}
.logo {
display: block; 
margin: 20px auto; 
filter: drop-shadow(0 0 15px red);
}
.title {
color: #ff4444; 
text-shadow: 0 0 10px #ff0000; 
text-align: center; 
margin: 20px 0; 
font-size: 28px;
}
.content {
padding: 20px; 
color: #ffcccc; 
font-size: 16px;
text-align: center;
}
.verify-button {
background: linear-gradient(to right, #550000, #ff0000);
color: #fff;
padding: 15px 30px;
text-decoration: none;
font-size: 18px;
font-weight: bold;
border-radius: 8px;
box-shadow: 0 0 10px #ff0000;
display: inline-block;
margin-top: 20px;
}
.footer {
color: #ff4444; 
text-align: center; 
margin: 20px 0;
}
</style>
</head>
<body>
<table class="outer" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td align="center" valign="top">
<table class="container" border="0" cellspacing="0" cellpadding="0">
<tr>
<td align="center">
<!-- LOGO -->
<img src="{{ asset('images/logo.png') }}" alt="Knull Void" width="200" class="logo">
</td>
</tr>
<tr>
<td>
<h1 class="title">Welcome to the Abyss</h1>
</td>
</tr>
<tr>
<td class="content">
<p>{{ $content ?? 'You have been chosen. Click below to verify your existence...' }}</p>
@if(!empty($actionUrl))
<a href="{{ $actionUrl }}" class="verify-button">
{{ $actionText ?? 'Verify Email Address' }}
</a>
@endif
<p style="margin-top: 20px;">
If you did not create an account, no further action is required... for now.
</p>
</td>
</tr>
<tr>
<td class="footer">
<p>May the Abyss Embrace You,<br>
<strong>{{ config('app.name') }}</strong></p>
</td>
</tr>
<!-- Extra spacing at bottom -->
<tr>
<td style="height: 40px;"></td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
