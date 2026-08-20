<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .container {
            text-align: center;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
        }
        h1 {
            color: #dc3545;
        }
        p {
            font-size: 18px;
        }
        .button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            margin-top: 20px;
            text-decoration: none;
            display: inline-block;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404 Not Found</h1>
        <p>Sorry, the page you are looking for does not exist.</p>
        <button class="button" onclick="goToHome()">Go to Home</button>
    </div>

    <script>
        // Function to redirect to the home page when the button is clicked
        function goToHome() {
            window.location.href = "{{ url('/') }}"; // Redirect to home page
        }

        // Disable back button and prevent navigation
        (function disableBackNavigation() {
            // Replace the current page in the browser's history stack
            if (window.history && window.history.pushState) {
                history.pushState(null, null, location.href); // Push the 404 page to history
                window.onpopstate = function() {
                    history.pushState(null, null, location.href); // Push the 404 page again to block back
                };
            }
        })();
    </script>
</body>
</html>