<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php
        
        // Start or resume the session
        session_start();

        $username = $_POST["username"];
        $password = $_POST["pw"];
        
        // Connection to db to save data
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user ='root', $pass='Squidy.77');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            $query = ("SELECT COUNT(*) AS Counter 
                        FROM CONFVIRTUAL.UTENTE 
                        WHERE Username = :lab1 AND Password = :lab2");

            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $username);
            $res -> bindValue(":lab2", $password);

            $res -> execute();
        
            $row = $res -> fetch();

            if($row["Counter"] > 0) {
                echo "OK";
                $_SESSION['user'] = $username;
                header('Location:index.php');
            } else {
                echo "ERROR";
            }

        } catch( PDOException $e ) {
            echo("[ERRORE]".$e->getMessage());
            exit();
        }

    ?>
</body>
</html>