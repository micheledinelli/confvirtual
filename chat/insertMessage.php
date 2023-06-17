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
        
        ini_set('display_errors', 1); 
        error_reporting(-1);
        session_start();
        $username = $_SESSION["user"]; 
        $text = $_POST["msg"];
        $chatId = $_POST["chatId"];

        date_default_timezone_set('Europe/Rome');
        $date = new DateTime();
        $currentDate = $date -> format('Y-m-d H:i:s');
        try {
            //Connection to MySQL db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user ='root', $pass='root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            //Connection to MongoDB
            // require '../vendor/autoload.php';
            // $conn = new MongoDB\Client("mongodb://localhost:27017");
            // $collection = $conn -> CONFVIRTUAL_log -> log;	

            //MySQL
            $query = 'call confvirtual.InserisiciMessaggio(:lab1, :lab2, :lab3, :lab4)';
            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $username);
            $res -> bindValue(":lab2", $text);
            $res -> bindValue(":lab3", $chatId);
            $res -> bindValue(":lab4", $currentDate);
            
            $res -> execute();

            //MongoDB
            // $DATA = array("UsernameMittente"=>$username, "Testo"=>$text, "Ts"=>$currentDate, "ChatId"=>$currentDate);
            // $insertOneResult = $collection->insertOne([
            //     'TimeStamp' 		=> time(),
            //     'User'				=> $_SESSION['user'],
            //     'OperationType'		=> 'INSERT',
            //     'InvolvedTable'	    => 'MESSAGGIO',
            //     'Input'				=> $DATA
            // ]);

            header('Location:chat.php');

            $_SESSION["chatError"] = 0;
        } catch( PDOException $e ) {
            
            $_SESSION["chatError"] = 1;
            header('Location:chat.php');
            echo("[ERRORE]".$e->getMessage());
            exit();
        }
    ?>
</body>
</html>