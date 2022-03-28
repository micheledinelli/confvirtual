<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCA</title>
</head>
<body>
    <?php
        session_start();
        $usernamePresenter = $_POST['username'];
        $codicePresentazione = $_POST['codicePresentazione'];
       
        echo $usernamePresenter;
        echo $codicePresentazione;
        
        try {
            
            // Connection to MySQL db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');

            //Connection to MongoDB
            require '../vendor/autoload.php';
            $conn = new MongoDB\Client("mongodb://localhost:27017");
            $collection = $conn -> CONFVIRTUAL_log -> log;	
            
            //MySQL
            $sql = 'call AssociaPresenter(:lab1, :lab2)';

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':lab1', $usernamePresenter);
            $stmt->bindValue(':lab2', $codicePresentazione);
            
            $stmt->execute();

            //MongoDB
            $DATA = array("UsernamePresenter"=>$usernamePresenter);
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'UPDATE',
                'InvolvedTable'	    => 'P_ARTICOLO',
                'Input'				=> $DATA
            ]);

            // L'ultima operazione è andata a buon fine
            $_SESSION["opSuccesfull"] = 0;

            // Redirect
            header('Location:admin.php');
            
            $pdo = null;

        } catch (PDOException $e) {
            
            // Errore
            $_SESSION["error"] = 1;
            header('Location:admin.php');

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

    ?>
</body>
</html>