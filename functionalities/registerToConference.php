<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register to conferenze</title>
</head>
<body>
    <?php
        
        // Start or resume the session
        session_start();

        // Retrieve data from html form
        $username = $_POST["username"];
        $acronimo = $_POST["acronimo"];
        $annoEdizione = $_POST["annoEdizione"];
        
        try {

            // Connection to MySQL db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');

            //Connection to MongoDB
            // require '../vendor/autoload.php';
            // $conn = new MongoDB\Client("mongodb://localhost:27017");
            // $collection = $conn -> CONFVIRTUAL_log -> log;	
            
            //MySQL
            $query = ('INSERT INTO REGISTRAZIONE(Username, AcronimoConferenza, AnnoEdizione) 
                VALUES(:lab1, :lab2, :lab3)');

            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $username);
            $res -> bindValue(":lab2", $acronimo);
            $res -> bindValue(":lab3", $annoEdizione);
    
            $res -> execute();

            //MongoDB
            // $DATA = array("Username"=>$username, "AcronimoConferenza"=>$acronimo, "AnnoEdizione"=>$annoEdizione); 
            // $insertOneResult = $collection->insertOne([
            //     'TimeStamp' 		=> time(),
            //     'User'				=> $_SESSION['user'],
            //     'OperationType'		=> 'INSERT',
            //     'InvolvedTable'	    => 'RESGISTRAZIONE',
            //     'Input'				=> $DATA
            // ]);

            // L'ultima operazione è andata a buon fine
            $_SESSION["opSuccesfull"] = 0;

            // Redirect
            header('Location:base.php');
            
            $pdo = null;

        } catch (PDOException $e) {
            
            // Errore
            $_SESSION["error"] = 1;
            header('Location:base.php');

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    ?>
</body>
</html>