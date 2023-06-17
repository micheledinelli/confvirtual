<?php
    session_start();

    $acronimo = $_POST['acronimo'];
    $annoEdizione = $_POST['annoEdizione'];
    $titoloSessione = $_POST['titoloSessione'];
    $dataSessione = $_POST['dataSessione'];
    $oraInizio = $_POST['oraInizio'];
    $oraFine = $_POST['oraFine'];
    $linkSessione = $_POST['linkSessione'];

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
        $sql = 'call confvirtual.CreaSessione(:lab1, :lab2, :lab3, :lab4, :lab5, :lab6, :lab7)';

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':lab1', $acronimo);
        $stmt->bindValue(':lab2', $titoloSessione);
        $stmt->bindValue(':lab3', $annoEdizione);
        $stmt->bindValue(':lab4', $dataSessione);
        $stmt->bindValue(':lab5', $oraInizio);
        $stmt->bindValue(':lab6', $oraFine);
        $stmt->bindValue(':lab7', $linkSessione);

        $stmt->execute();

        //MongoDB
        // $DATA = array("AcronimoConferenza"=>$acronimo, "Titolo"=>$titoloSessione, "Data"=>$dataSessione, 
        //     "Anno"=>$annoEdizione, "OraInizio"=>$oraInizio, "OraFine"=>$oraFine, "Link"=>$linkSessione);
        // $insertOneResult = $collection->insertOne([
        //     'TimeStamp' 		=> time(),
        //     'User'				=> $_SESSION['user'],
        //     'OperationType'		=> 'INSERT',
        //     'InvolvedTable'	    => 'SESSIONE',
        //     'Input'				=> $DATA
        // ]);

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
