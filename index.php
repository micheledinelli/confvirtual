<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Demo</title>
</head>
<body>

    <?php session_start(); ?>

    <div class="menu-bar">
        <ul>
            <li><a>Start</a>
                <div class="sub-menu-1">
                    <ul>
                        <li><a href="login.html" id="login">Login</a></li>
                        <li><a href="signup.html" id="register">Register</a></li>
                    </ul>
                </div>
            </li>
            <li><a href="news.asp">News</a></li>
            <li><a href="contact.asp">Contact</a></li>
            <li><a href="about.html">About</a></li>
        </ul>
    </div>

    <?php
        if(isset($_SESSION['status'])){
    ?>        
    
    <div class="popup-succesfull-login">
        <div class="successfull-login"><?php echo $_SESSION['status']?></div>
        <div id="close-succesfull-login">x</div>
    </div>
        
    <?php
        }    
        unset($_SESSION['status']);
    ?>

<script src="app.js"></script>
    
</body>
</html>