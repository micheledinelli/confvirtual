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
        echo "im here";
        $usernameAdmin = $_SESSION['user'];
        $codiceSessione = $_POST['codiceSessione'];
        $titoloSessione = $_POST['titoloSessione'];
        $titolo = $_POST['titolo'];
        $dataSessione = $_POST['dataSessione'];
        $oraInizio = $_POST['oraInizio'];
        $oraFine = $_POST['oraFine'];
        //campo solo della presentazione
        if(isset ($_POST['abstract'])){
            $abstract = $_POST['abstract'];
            $articolo=false;
        }
        //campo solo dell'articolo
        if(isset ($_POST['numeroPagine'])){
            $numeroPagine = $_POST['numeroPagine'];
            $articolo=true;
        }
        
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
            if($articolo){
                $sql = 'call inserisciArticolo(:lab1, :lab2, :lab3, :lab4, :lab5, "")';
                $stmt = $pdo->prepare($sql);

                $stmt->bindValue(':lab1', $codiceSessione);
                $stmt->bindValue(':lab2', $oraInizio);
                $stmt->bindValue(':lab3', $oraFine);
                $stmt->bindValue(':lab4', $titolo);
                $stmt->bindValue(':lab5', $numeroPagine);
                $stmt->execute();

                //MongoDB
                $DATA = array("Titolo"=>$titolo, "NumeroPagine"=>$numeroPagine);
                $insertOneResult = $collection->insertOne([
                    'TimeStamp' 		=> time(),
                    'User'				=> $_SESSION['user'],
                    'OperationType'		=> 'INSERT',
                    'InvolvedTable'	    => 'P_ARTICOLO',
                    'Input'				=> $DATA
                ]);

            }else{
                $sql = 'call inserisciTutorial(:lab1, :lab2, :lab3, :lab4, :lab5)';
                $stmt = $pdo->prepare($sql);

                $stmt->bindValue(':lab1', $codiceSessione);
                $stmt->bindValue(':lab2', $oraInizio);
                $stmt->bindValue(':lab3', $oraFine);
                $stmt->bindValue(':lab4', $titolo);
                $stmt->bindValue(':lab5', $abstract);
                $stmt->execute();

                //MongoDB
                $DATA = array("Titolo"=>$titolo, "Abstract"=>$abstract);
                $insertOneResult = $collection->insertOne([
                    'TimeStamp' 		=> time(),
                    'User'				=> $_SESSION['user'],
                    'OperationType'		=> 'INSERT',
                    'InvolvedTable'	    => 'P_TUTORIAL',
                    'Input'				=> $DATA
                ]);
            }
            //MongoDB
            $DATA = array("CodiceSessione"=>$codiceSessione, "OraInizio"=>$oraInizio, 
                    "OraFine"=>$oraFine);
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'INSERT',
                'InvolvedTable'	    => 'PRESENTAZIONE',
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