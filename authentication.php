<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Script.php</title>
</head>
<body>
    <?php
        // Session has an ID and session variable
        session_start();
        
        $_SESSION['name'] = $_POST["uname"];
        $_SESSION['psw'] = $_POST["psw"] ;

        $name = $_POST["uname"];
        $password = $_POST["psw"];

        // Connection to db to save data
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=AUTHENTICATION', $user = 'root', $pass = 'Squidy.77');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');

            // Check if the user exists in the DB
            $query = ("SELECT Nome FROM AUTHENTICATION.UTENTE WHERE Nome = :lab1");
            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $name);
            $res -> execute();
           
            while($row = $res -> fetch() ){
                if($row["Nome"] == $name) {
                    $checked = true;
                } 
            }
            
            if( $checked ){
                // Check if the password is ok for that user
                $query = ("SELECT Password FROM AUTHENTICATION.UTENTE WHERE Nome = :lab1");
                $res = $pdo -> prepare($query);
                $res -> bindValue(":lab1", $name);
                $res -> execute();
                
                while($row = $res -> fetch() ){
                    if($row["Password"] == $password) {
                        $_SESSION['status'] = "Login successfull";
                        sleep(0.5);
                        // Redirect
                        header('Location:index.php');
                    } else {
                        echo 'Wrong Password';
                    }
                } 
            } else {
                echo 'Username does not exists';
            } 
            

            $pdo = null;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            // equivalent to exit();
            die();
        }
        
    ?>

    <br>
    <a href="index.php">GOBACK</a>

</body>
</html>