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

        echo $usernameAdmin . " "  . $annoEdizione .  " " . $nomeConferenza . " " . $acronimo;

        try {
            
            // Connection to db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'Squidy.77');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            $sql = 'call creaConferenzaAdmin(:lab1, :lab2, :lab3, :lab4)';

            $stmt = $pdo->prepare($sql);

            $stmt->bindValue(':lab1', $nomeConferenza);
            $stmt->bindValue(':lab2', $usernameAdmin);
            $stmt->bindValue(':lab3', $acronimo);
            $stmt->bindValue(':lab4', $annoEdizione);

            $stmt->execute();

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