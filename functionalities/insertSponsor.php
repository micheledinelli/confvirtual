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

        $nomeSponsor = $_POST["nomeSponsor"]; 

        if(!empty($_POST["logo"])) {
            echo 'im here';
            $logo = $_POST["logo"];  
            $insertingLogo = true;
        }

        $bothInsert = false;

        if(!empty($_POST["acronimoConferenza"]) && !empty($_POST["annoEdizione"]) && !empty($_POST["importo"])) {
            
            $acronimoConferenza = $_POST["acronimoConferenza"];
            $annoEdizione = $_POST["annoEdizione"];
            $importo = $_POST["importo"];
            
            $bothInsert = true;
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
            if( $bothInsert ) {

                // La procedura inserisce automaticamente anche lo sponsor 
                // nella tabella sponsor se non lo trova nel db
                $query = 'call confvirtual.AggiungiSponsorizzazione(:lab1, :lab2, :lab3, :lab4);';

                $res = $pdo -> prepare($query);
                $res -> bindValue(":lab1", $nomeSponsor);
                $res -> bindValue(":lab2", $acronimoConferenza);
                $res -> bindValue(":lab3", $importo);
                $res -> bindValue(":lab4", $annoEdizione);

                $res -> execute();

                //MongoDB
                // $DATA = array("NomeSponsor"=>$nomeSponsor, "AnnoEdizione"=>$annoEdizione, 
                //     "AcronimoConferenza"=>$acronimoConferenza, "Importo"=>$importo);
                // $insertOneResult = $collection->insertOne([
                //     'TimeStamp' 		=> time(),
                //     'User'				=> $_SESSION['user'],
                //     'OperationType'		=> 'INSERT',
                //     'InvolvedTable'	    => 'SPONSORIZZAZIONE',
                //     'Input'				=> $DATA
                // ]);

                $_SESSION["opSuccesfull"] = 0;

                header('Location: admin.php');

            } else if($insertingLogo && !$bothInsert) {

                $query = 'INSERT INTO SPONSOR(Nome, Logo) VALUES(:lab1, :lab2)';
                $res = $pdo -> prepare($query);
                $res -> bindValue(":lab1", $nomeSponsor);
                $res -> bindValue(":lab2", $logo, PDO::PARAM_LOB);

                $res -> execute();

                //MongoDB
                // $DATA = array("Nome"=>$nomeSponsor, "Logo"=>$logo);
                // $insertOneResult = $collection->insertOne([
                //     'TimeStamp' 		=> time(),
                //     'User'				=> $_SESSION['user'],
                //     'OperationType'		=> 'INSERT',
                //     'InvolvedTable'	    => 'SPONSOR',
                //     'Input'				=> $DATA
                // ]);
                
                $_SESSION["opSuccesfull"] = 0;

                header('Location: admin.php');

            } else {
                $query = 'INSERT INTO SPONSOR(Nome) VALUES(:lab1)';
                $res = $pdo -> prepare($query);
                $res -> bindValue(":lab1", $nomeSponsor);

                $res -> execute();

                //MongoDB
                // $insertOneResult = $collection->insertOne([
                //     'TimeStamp' 		=> time(),
                //     'User'				=> $_SESSION['user'],
                //     'OperationType'		=> 'INSERT',
                //     'InvolvedTable'	    => 'SPONSOR',
                //     'Input'				=> $nomeSponsor
                // ]);

                $_SESSION["opSuccesfull"] = 0;

                header('Location: admin.php');
            }

        }catch(PDOException $e) {
            // Errore
            $_SESSION["error"] = 1;
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    ?>
</body>
</html>