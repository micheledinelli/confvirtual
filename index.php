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
                        <li><a href="#" id="login">Login</a></li>
                        <li><a href="signup.html" id="register">Register</a></li>
                    </ul>
                </div>
            </li>
            <li><a href="news.asp">News</a></li>
            <li><a href="contact.asp">Contact</a></li>
            <li><a href="about.html">About</a></li>
        </ul>
    </div>

    <div class="pop-up">
        <div class="pop-up-content">
            <form action="authentication.php" method="post">
                <label for="uname"><b>Username</b></label>
                <input type="text" placeholder="Enter Username" name="uname" required autocomplete="off">
                <label for="psw"><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>
                <button type="submit" class="login-btn"><strong>Login</strong></button>
            </form>
            <div id="close">x</div>
        </div>
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