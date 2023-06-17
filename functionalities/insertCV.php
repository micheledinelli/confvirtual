<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
    
    session_start();

    if(!empty($_POST["cv"])) {
        $cv = $_POST["cv"];
    }

    try {

        // Connection to MySQL db
        $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
        $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo -> exec('SET NAMES "utf8"');
        
        //MongoDB
        // require '../vendor/autoload.php';
        // $conn = new MongoDB\Client("mongodb://localhost:27017");
        // $collection = $conn -> CONFVIRTUAL_log -> log;

        //MySQL
        if($_SESSION["userType"] == "SPEAKER") {
            $query = 'UPDATE CONFVIRTUAL.SPEAKER SET CurriculumVitae = :lab1  WHERE Username = :lab2';

            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $cv);
            $res -> bindValue(":lab2", $_SESSION["user"]);
            
            $res -> execute();
    
            //MongoDB
            // $insertOneResult = $collection->insertOne([
            //     'TimeStamp' 		=> time(),
            //     'User'				=> $_SESSION['user'],
            //     'OperationType'		=> 'UPDATE',
            //     'InvolvedTable'	    => 'SPEAKER',
            //     'Input'				=> $cv
            // ]);

            $_SESSION["opSuccesfull"] = 0;
        
        } elseif($_SESSION["userType"] == "PRESENTER") {
            $query = 'UPDATE CONFVIRTUAL.PRESENTER SET CurriculumVitae = :lab1  WHERE Username = :lab2';
            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $cv);
            $res -> bindValue(":lab2", $_SESSION["user"]);
            
            $res -> execute();
    
            //MongoDB
            // $insertOneResult = $collection->insertOne([
            //     'TimeStamp' 		=> time(),
            //     'User'				=> $_SESSION['user'],
            //     'OperationType'		=> 'UPDATE',
            //     'InvolvedTable'	    => 'PRESENTER',
            //     'Input'				=> $cv
            // ]);

            $_SESSION["opSuccesfull"] = 0;
        } 
        
        header('Location: speaker_presenter.php');
    
    } catch (PDOException $e) {
        // Errore
        $_SESSION["error"] = 1;
        header('Location:speaker_presenter.php');
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }


    
    ?>
</body>
</html>