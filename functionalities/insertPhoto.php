<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Photo</title>
</head>
<body>
    <?php
        session_start();

        if(!empty($_POST["photo"])) {
            $file = $_POST["photo"];
        }

        try {

            // Connection to db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            if($_SESSION["userType"] == "SPEAKER") {
                $query = 'UPDATE CONFVIRTUAL.SPEAKER SET Foto = :lab1  WHERE Username = :lab2';
            
            } elseif($_SESSION["userType"] == "PRESENTER") {
                $query = 'UPDATE CONFVIRTUAL.PRESENTER SET Foto = :lab1  WHERE Username = :lab2';
            } 

            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $file, PDO::PARAM_LOB);
            $res -> bindValue(":lab2", $_SESSION["user"]);
            
            $res -> execute();

            $_SESSION["opSuccesfull"] = 0;

            header('Location: speaker_presenter.php');
        
        } catch (PDOException $e) {
            
            // Errore
            $_SESSION["error"] = 1;

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }

    ?>
</body>
</html>