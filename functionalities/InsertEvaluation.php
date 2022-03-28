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
        $voto = $_POST['voto'];
        $commento = $_POST['commento'];
        $codicePresentazione = $_POST['codice'];
        
        try {
            
            // Connection to db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            $sql = 'call inserisciValutazione(:lab1, :lab2, :lab3, :lab4)';

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':lab1', $usernameAdmin);
            $stmt->bindValue(':lab2', $voto);
            $stmt->bindValue(':lab3', $commento);
            $stmt->bindValue(':lab4', $codicePresentazione);
            
            $stmt->execute();

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