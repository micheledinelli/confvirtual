<?php
        // Start or resume the session
        session_start();

        $username = $_POST["username"];
        $codice = $_POST["codice"];

        if($_POST['manageOpt'] == "true") {
            $mode = "insert";
        } else {
            $mode = "delete";
        }
        
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
            if($mode == "delete") {
                $query = ('DELETE FROM FAVORITE WHERE Username = :lab1 AND CodicePresentazione = :lab2');

                $res = $pdo -> prepare($query);
                $res -> bindValue(":lab1", $username);
                $res -> bindValue(":lab2", $codice);
                $res -> execute();

                //MongoDB
                // $insertOneResult = $collection->insertOne([
                //     'TimeStamp' 		=> time(),
                //     'User'				=> $_SESSION['user'],
                //     'OperationType'		=> 'DELETE',
                //     'InvolvedTable'	    => 'FAVORITE'
                // ]);

                // L'ultima operazione è andata a buon fine
                $_SESSION["opSuccesfull"] = 0;

                $pdo = null;

                header('Location:base.php');

            } else if($mode == "insert") {
                $query = ('INSERT INTO FAVORITE(Username, CodicePresentazione) VALUES(:lab1, :lab2)');

                $res = $pdo -> prepare($query);
                $res -> bindValue(":lab1", $username);
                $res -> bindValue(":lab2", $codice);
                $res -> execute();

                // $DATA = array("Username"=>$username, "CodicePresentazione"=>$codice);
                // $insertOneResult = $collection->insertOne([
                //     'TimeStamp' 		=> time(),
                //     'User'				=> $_SESSION['user'],
                //     'OperationType'		=> 'INSERT',
                //     'InvolvedTable'	    => 'FAVORITE',
                //     'Input'				=> $DATA
                // ]);

                $_SESSION["opSuccesfull"] = 0;
                
                $pdo = null;
            
                header('Location:base.php');

            }

        } catch (PDOException $e) {
            
            // Errore
            $_SESSION["error"] = 1;
            header('Location:base.php');

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    ?>