<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Government of India | Secure Redirect</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f6f8;
            color: #1a1a1a;
        }
        .header {
            background: #0b3c5d;
            color: #fff;
            padding: 12px 20px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
        }
        .subheader {
            background: #e6eef3;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #333;
        }
        .container {
            max-width: 520px;
            margin: 60px auto;
            background: #fff;
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            padding: 30px;
            text-align: center;
        }
        .title {
            font-size: 20px;
            margin-bottom: 12px;
            color: #0b3c5d;
            font-weight: 600;
        }
        .desc {
            font-size: 14px;
            color: #555;
            margin-bottom: 25px;
        }
        .loader {
            width: 42px;
            height: 42px;
            border: 4px solid #d6d6d6;
            border-top: 4px solid #0b3c5d;
            border-radius: 50%;
            margin: 0 auto 20px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
        .footer {
            margin-top: 60px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer span {
            display: block;
            margin-top: 4px;
        }
    </style>
</head>
<body onload="document.forms[0].submit()">
    <div class="container">
        <div class="title">Redirecting to ePACS Portal</div>
        <div class="desc">
            Please wait while we securely authenticate and redirect you.
        </div>
        <div class="loader"></div>

        <!-- The baseUrl already contains ?encryData=, so just append encryption value -->
        <form method="POST" action="{{ $baseUrl }}{{ $encryData }}">
	 @csrf
            <!-- No need for hidden field since it's already in the URL -->
        </form>
    </div>

    <div class="footer">
        © National Informatics Centre (NIC)
        <span>Ministry of Electronics & Information Technology, Government of India</span>
    </div>
</body>
</html>
