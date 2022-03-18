<?php
        // Start or resume the session
        session_start();

        $username = $_POST["username"];
        $codice = $_POST["codice"];
        
        try {

            // Connection to db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            $query = ('DELETE FROM FAVORITE WHERE Username = :lab1 AND CodicePresentazione = :lab2');

            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $username);
            $res -> bindValue(":lab2", $codice);
    
            $res -> execute();

            // L'ultima operazione è andata a buon fine
            $_SESSION["opSuccesfull"] = 0;

            // Redirect
            header('Location:base.php');
            
            $pdo = null;

        } catch (PDOException $e) {
            
            // Errore
            $_SESSION["error"] = 1;
            header('Location:base.php');

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    ?>