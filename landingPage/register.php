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

        // Start or resume the session
        session_start();

        $_SESSION['start'] = time(); 
        $inactive = 2400;
        $_SESSION['expire'] = time() + $inactive;

        // Retrieve data from html form
        $username = $_POST["username"];
        $password = $_POST["pw"];
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $date = $_POST["date"];
        $birthplace = $_POST["place"];
        
        try {
            
            // Connection to MySQL db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
<<<<<<< HEAD
=======
            //MongoDB
            require '../vendor/autoload.php';
            $conn = new MongoDB\Client("mongodb://localhost:27017");
            $collection = $conn -> CONFVIRTUAL_log -> log;	

            // Check if the username already exists, if not is inserted into UTENTE
            // Eventually possible to use stored procedure defined in the db
>>>>>>> d518ce0880685e5fff294f481cc6740be0c9a873
            $query = ('INSERT INTO UTENTE(Username, Password, Nome, Cognome, DataNascita, Luogo) 
                VALUES(:lab1, :lab2, :lab3, :lab4, :lab5, :lab6)');
            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $username);
            $res -> bindValue(":lab2", $password);
            $res -> bindValue(":lab3", $name);
            $res -> bindValue(":lab4", $surname);
            $res -> bindValue(":lab5", $date);
            $res -> bindValue(":lab6", $birthplace);
            $res -> execute();
            echo 'User inserted into table UTENTE';
            $_SESSION['user'] = $username;
            $_SESSION['userType'] = "BASE";

            //MongoDB
            $DATA = array("Username"=>$username, "Password"=>$password, "Nome"=>$name, "Cognome"=>$surname,
                "DataNascita"=>$date, "Luogo"=>$birthplace);
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'INSERT',
                'InvolvedTable'	    => 'UTENTE',
                'Input'				=> $DATA
            ]);
            
            // Redirect
            header('Location:index.php');
            
            $pdo = null;

        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            header('Location:register.html');
            die();
        }

    ?>      
    
</body>
</html>