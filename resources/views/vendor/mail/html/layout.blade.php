<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        body, table, td, th {
            margin: 0;
            padding: 0;
        }
        /* Force a dark red background for the entire email */
        body {
            background-color: #220000 !important;
            color: #ffcccc;
        }
        .wrapper {
            background-color: #220000;
            width: 100%;
            -premailer-width: 100%;
        }
        .content {
            width: 100%;
        }
        /* The inner-body is our main container */
        .inner-body {
            background-color: #110000;
            width: 570px;
            margin: 0 auto;
            border-radius: 5px;
            -premailer-width: 570px;
        }
        /* Responsive adjustments */
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }
            .footer {
                width: 100% !important;
            }
        }
        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
    {{ $head ?? '' }}
</head>
<body style="background-color: #220000; color: #ffcccc;">
    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" bgcolor="#220000">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    {{ $header ?? '' }}
                    
                    <!-- Email Body -->
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: none;">
                            <table class="inner-body" align="center" cellpadding="0" cellspacing="0" role="presentation" bgcolor="#110000">
                                <!-- Body content -->
                                <tr>
                                    <td class="content-cell" style="padding: 35px;">
                                        {{ Illuminate\Mail\Markdown::parse($slot) }}
                                        {{ $subcopy ?? '' }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    {{ $footer ?? '' }}
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
