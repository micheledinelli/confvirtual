
<?php
        session_start();
        if(time() > $_SESSION['expire']) {
            session_unset();
            session_destroy();
            header('Location:../index.php');
        } 

        $uni = $_POST['uni'];
        $dip = $_POST['dip'];
        
        try{
            // Connection to db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');

            $query = 'call confvirtual.AffiliazioneUni(:lab1, :lab2, :lab3)';
            
            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $uni);
            $res -> bindValue(":lab2", $dip);
            $res -> bindValue(":lab3", $_SESSION["user"]);
            $res -> execute();
           
            header('Location: speaker_presenter.php');

        }catch(PDOException $e) {
            // Errore
            $_SESSION["error"] = 1;
            //header('Location: speaker_presenter.php');

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
?>