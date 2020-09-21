<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Get Good - Email Verification</title>
        <link href='//fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>
        <style>
            body {
                margin: 50px 0 0 0;
                padding: 0;
                width: 100%;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                text-align: center;
                color: #aaa;
                font-size: 18px;
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
        </style>
    </head>
    <?php 
        $code = $_GET['code'];
    ?>
    <body>
        
        <h1>Get Good</h1>

        <form action="verify_email" method="post">
            <input type="hidden" name="code" value="<?php echo $code;?>">
            <input class="verify_btn" type="submit" value="Verify Email">
        </form>

    </body>
</html>
