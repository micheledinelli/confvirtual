<?php
    session_start();

    $username = $_SESSION["user"];
    $link = $_POST["link"];
    $codiceTutorial = $_POST["codiceTutorial"];

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
        echo $link;
        $query = 'DELETE FROM RISORSA WHERE UsernameSpeaker = :lab1 AND CodiceTutorial = :lab2 AND Link = :lab3';

        $res = $pdo -> prepare($query);
        $res -> bindValue(":lab1", $username);
        $res -> bindValue(":lab2", $codiceTutorial);
        $res -> bindValue(":lab3", $link);
        
        $res -> execute();

        //MongoDB
        // $insertOneResult = $collection->insertOne([
        //     'TimeStamp' 		=> time(),
        //     'User'				=> $_SESSION['user'],
        //     'OperationType'		=> 'DELETE',
        //     'InvolvedTable'	    => 'RISORSA'
        // ]);

        $_SESSION["opSuccesfull"] = 0;
        
        header('Location: speaker_presenter.php');

    } catch (PDOException $e) {
        // Errore
        $_SESSION["error"] = 1;
        header('Location:speaker_presenter.php');
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
?>