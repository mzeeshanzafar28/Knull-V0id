<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>@yield('title', 'Knull Void')</title>
<style>
body, table, td {
margin: 0; padding: 0;
font-family: Arial, sans-serif;
}
.outer {
background-color: #220000; /* Dark red background */
width: 100%;
min-height: 100vh;
}
.container {
width: 600px;
background-color: #110000;
margin: 30px auto; /* This centers the card horizontally */
border-radius: 10px;
box-shadow: 0 0 20px #ff0000;
overflow: hidden;
}
.logo {
display: block;
margin: 20px auto;
filter: drop-shadow(0 0 15px red);
}
</style>
</head>
<body style="background-color: #220000;">
<table class="outer" width="100%" border="0" cellspacing="0" cellpadding="0">
<tr>
<td align="center" valign="top">
<table class="container" border="0" cellspacing="0" cellpadding="0">
<tr>
<td align="center" style="padding: 20px;">
<!-- LOGO -->
<img src="{{ asset('images/logo.png') }}" alt="Knull Void" width="200" class="logo">
</td>
</tr>
<tr>
<td style="padding: 20px;">
@yield('content')
</td>
</tr>
<tr>
<td style="height: 40px;"></td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
