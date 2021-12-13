<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="signup.css">
    <title>Sign-Up</title>
</head>
<body>
    <div class="container">
        <form action="registration.php" method="post">
            
            <label for="uname"><b>Username</b></label>
            <span id="check-username"></span>
            <input type="text" placeholder="Enter Username" id="username" name="username" required autocomplete="off" onInput="checkUsername()">    
            
            <label for="psw"><b>Password</b></label>
            <input type="password" placeholder="Enter Password" name="psw" required>
            
            <label for="name"><b>Enter yuor name</b></label>
            <input type="text" placeholder="Mario" name="name" required>
            
            <label for="surname"><b>Enter your surname</b></label>
            <input type="text" placeholder="Rossi" name="surname" required>
            
            <label for="bday"><b>Select your birthday</b></label>
            <input type="date" name="bday" required>
            
            <button type="submit" class="signup-btn"><strong>Sign Up</strong></button>
        </form>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function checkUsername() {
    
    jQuery.ajax({
    url: "check_availability.php",
    data:'username='+$("#username").val(),
    type: "POST",
    success:function(data){
        $("#check-username").html(data);
    },
    error:function (){}
    });
}
</script> 

</body>
</html>