<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
    <?php
        // Retrieve data from html form
        $username = $_POST["username"];
        $password = $_POST["pw"];
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $date = $_POST["date"];
        
        try {
            echo $username;
            // Connection to db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'Squidy.77');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            // Check if the username already exists, if not is inserted into UTENTE
            $query = ('INSERT INTO UTENTE(Username, Password, Nome, Cognome, DataNascita) 
                VALUES(:lab1, :lab2, :lab3, :lab4, :lab5)');
            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $username);
            $res -> bindValue(":lab2", $password);
            $res -> bindValue(":lab3", $name);
            $res -> bindValue(":lab4", $surname);
            $res -> bindValue(":lab5", $date);
            $res -> execute();
            echo 'User inserted into table UTENTE';
            // Redirect
            header('Location:index.html');
            
            $pdo = null;

        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

    ?>      
    
</body>
</html>