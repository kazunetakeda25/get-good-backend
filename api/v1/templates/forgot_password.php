<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Get Good - Reset password</title>
        <link href='//fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>
        <style>
            body {
                margin: 50px 0 0 0;
                padding: 0;
                width: 100%;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                /*text-align: center;*/
                color: #aaa;
                font-size: 18px;
                margin-left: 20px;
            }

            h1 {
                color: #719e40;
                letter-spacing: -3px;
                font-family: 'Lato', sans-serif;
                font-size: 50px;
                font-weight: 200;
                margin-bottom: 0;
            }

            .verify_btn
            {
                color: #719e40;
                letter-spacing: -3px;
                font-family: 'Lato', sans-serif;
                font-size: 50px;
                font-weight: 200;
                margin-bottom: 0;
            }

            p {
                margin-top: 5px !important;
                margin-bottom: 5px !important;
                font-size: 12px;
            }
        </style>
    </head>
    <?php 
        $code = $_GET['code'];
    ?>
    <body>
        
        <h1>Get Good</h1>

        <form action="reset_password" method="post">
            
            <input type="hidden" name="code" value="<?php echo $code;?>">
            <p>Password:</p>
            <input name="password" required="required" type="password" id="password" />
            <p>Confirm Password:</p>
            <input name="password_confirm" required="required" type="password" id="password_confirm" oninput="check(this)" />
            <script language='javascript' type='text/javascript'>
                function check(input) {
                    if (input.value.length < 6) {
                        input.setCustomValidity('Password Must be longer than 5.');
                    }
                    else if (input.value != document.getElementById('password').value) {
                        input.setCustomValidity('Password Must be Matching.');
                    } else {
                        // input is valid -- reset the error message
                        input.setCustomValidity('');
                    }
                }
            </script>
            <br /><br />
            <input class="verify_btn" type="submit" value="Reset Password">
        </form>

    </body>
</html>
