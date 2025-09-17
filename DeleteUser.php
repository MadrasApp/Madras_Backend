<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <style>
        /* Reset margin and padding for cleaner layout */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Center the form in the middle of the page */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
        }

        /* Form container styling */
        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        /* Input field styling */
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }

        /* Hide the token input field */
        input[name="token"] {
            display: none;
        }

        /* Button styling */
        .btn-primary {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
            text-align: center;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
        ::placeholder{
            text-align:right;
            direction:rtl;
        }

        /* Responsive behavior */
        @media (max-width: 400px) {
            form {
                padding: 15px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

    <form action="api/v2/DeleteUser" method="post" target="_blank">
        <h1>حذف اکانت کاربری</h1>

        <input name="mac" type="text" placeholder="شماره ی خود را وارد کنید" required>

        <input name="token" type="text" value="C36ZKdE02Nf89MIylUpbgL5VDnjArHmX">

        <p>
            <input type="submit" value="حذف اکانت" class="btn btn-primary">
        </p>
    </form>

</body>
</html>
