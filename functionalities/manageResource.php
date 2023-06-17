<?php
    session_start();

    $descrizione = $_POST["descrizione"];
    $username = $_SESSION["user"];
    $link = $_POST["link"];
    $codiceTutorial = $_POST["codiceTutorial"];
    $resourceAddOpt = $_POST["resourceAddOpt"];

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
        if($resourceAddOpt == "add") {
            $query = 'call confvirtual.AggiungiSpeakerTutorialRel(:lab1, :lab2, :lab3, :lab4);';

            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $username);
            $res -> bindValue(":lab2", $codiceTutorial);
            $res -> bindValue(":lab3", $link);
            $res -> bindValue(":lab4", $descrizione);
            
            $res -> execute();

            //MongoDB
            // $DATA = array("UsernameSpeaker"=>$username, "Link"=>$link, "Descrizione"=>$descrizione, 
            //     "CodiceTutorial"=>$codiceTutorial);
            // $insertOneResult = $collection->insertOne([
            //     'TimeStamp' 		=> time(),
            //     'User'				=> $_SESSION['user'],
            //     'OperationType'		=> 'INSERT',
            //     'InvolvedTable'	    => 'RISORSA',
            //     'Input'				=> $DATA
            // ]);

            $_SESSION["opSuccesfull"] = 0;
            
            header('Location: speaker_presenter.php');
        
        } else if($resourceAddOpt == "modify") {
            $query = 'UPDATE RISORSA SET Link = :lab1, Descrizione = :lab2 WHERE UsernameSpeaker = :lab3 AND CodiceTutorial = :lab4';

            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $link);
            $res -> bindValue(":lab2", $descrizione);
            $res -> bindValue(":lab3", $username);
            $res -> bindValue(":lab4", $codiceTutorial);
            
            $res -> execute();

            //MongoDB
            // $DATA = array("Link"=>$link, "Descrizione"=>$descrizione);
            // $insertOneResult = $collection->insertOne([
            //     'TimeStamp' 		=> time(),
            //     'User'				=> $_SESSION['user'],
            //     'OperationType'		=> 'UPDATE',
            //     'InvolvedTable'	    => 'RISORSA',
            //     'Input'				=> $DATA
            // ]);

            $_SESSION["opSuccesfull"] = 0;
            
            header('Location: speaker_presenter.php');
        }
        
    
    } catch (PDOException $e) {
        // Errore
        $_SESSION["error"] = 1;
        header('Location:speaker_presenter.php');
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
?>