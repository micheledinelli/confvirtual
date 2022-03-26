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
        $usernameAdmin = $_SESSION['user'];
        $nomeConferenza = $_POST['nomeConferenza'];
        $annoEdizione = $_POST['annoEdizione'];
        $acronimo = $_POST['acronimo'];
        $dataInizio = $_POST['dataInizio'];
        $dataFine = $_POST['dataFine'];
        $logo = $_POST['logo'];

        //$mysqldate = date( 'Y-m-d', $dataInizio);
        echo $dataInizio;

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
            $sql = 'call creaConferenzaAdmin(:lab1, :lab2, :lab3, :lab4, :lab5)';

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':lab1', $nomeConferenza);
            $stmt->bindValue(':lab2', $usernameAdmin);
            $stmt->bindValue(':lab3', $acronimo);
            $stmt->bindValue(':lab4', $annoEdizione);
            $stmt -> bindValue(":lab5", $logo, PDO::PARAM_LOB);

            $stmt->execute();

            //MongoDB
            $DATA = array("Nome"=>$nomeConferenza, "Acronimo"=>$acronimo, "AnnoEdizione"=>$annoEdizione,
                "Svolgimento"=>"*ENUM*");
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'INSERT',
                'InvolvedTable'	    => 'CONFERENZA',
                'Input'				=> $DATA
            ]);

            $DATA = array("Username"=>$usernameAdmin, "AcronimoConferenza"=>$acronimo,
                "AnnoEdizione"=>$annoEdizione);
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'INSERT',
                'InvolvedTable'	    => 'REGISTRAZIONE',
                'Input'				=> $DATA
            ]);

            $DATA = array("UsernameAdmin"=>$usernameAdmin, "AcronimoConferenza"=>$acronimo,
                "AnnoEdizione"=>$annoEdizione);
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'INSERT',
                'InvolvedTable'	    => 'CREAZIONE',
                'Input'				=> $DATA
            ]);

            //MySQL
            $sql = 'call InserisciDateSvoglimento(:lab1, :lab2, :lab3, :lab4)';
            
            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':lab1', $acronimo);
            $stmt->bindValue(':lab2', $annoEdizione);
            $stmt->bindValue(':lab3', $dataInizio);
            $stmt->bindValue(':lab4', $dataFine);

            $stmt->execute();

            //MongoDB
            $DATA = array("AcronimoConferenza"=>$acronimo, "AnnoEdizione"=>$annoEdizione, 
                "DataInizio"=>$dataInizio, "DataFine"=>$dataFine);
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'INSERT',
                'InvolvedTable'	    => 'DATESVOLGIMENTO',
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
            //header('Location:admin.php');

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

    ?>
</body>
</html>