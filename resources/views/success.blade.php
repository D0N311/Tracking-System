<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verification Success</title>
    <style>
        body {
            background-color: #1F2937; /* Slate background color */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0; /* Remove default margin */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; /* Set a common font-family */
        }

        .message {
            background-color: #2E3A47; /* Slightly darker background color */
            padding: 20px;
            color: #ffffff; /* White text color */
            text-align: center;
            border-radius: 10px; /* Add border-radius for a rounded look */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); /* Add a subtle box shadow */
        }

        h2 {
            margin-top: 0; /* Remove top margin for h2 */
            color: #61dafb; /* Bright blue color for heading */
        }

        p {
            margin-bottom: 15px; /* Add some bottom margin to paragraph */
        }

        button {
            background-color: #27b5dc; /* Bright blue button color */
            color: #ffffff; /* White text color */
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease; /* Add a smooth transition effect */
        }

        button:hover {
            background-color: #2e5163; /* Darker blue on hover */
        }
    </style>
</head>
<body>
    <div class="message">
        <h2>You are now verified.</h2>
        <p>Thank you for verifying your account. You can now login to your account.</p>
        <button onclick="window.location.href = 'http://localhost:5173/Login/'">Go to Login</button>
    </div>
</body>
</html>
