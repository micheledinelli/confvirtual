<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <?php
        $username = $_POST["username"];
        $password = $_POST["pw"];

        // Connection to db to save data
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'Pinaccio00!');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
        }
        catch(PDOException $e) {
            echo("[ERRORE] Query SQL (Insert) non riuscita. Errore primo catch: ".$e->getMessage());
            exit();
        }

        try {
            //check if the username and the password are in the Database
            $sql='SELECT COUNT(*) AS counter FROM UTENTE  WHERE (Username=:lab1) AND (Password=:lab2)';
            $res=$pdo->prepare($sql);
            $res->bindValue(":lab1",$username);
            $res->bindValue(":lab2",$password);
            $res->execute();

            $res=$pdo->query($sql);
          }
         catch(PDOException $e) {
           echo("[ERRORE] Query SQL (Insert) non riuscita. Errore secondo catch: ".$e->getMessage());
           exit();
         }
        $row=$res->fetch();
        if ($row['counter']>0) {
            echo("<b> Login effettuato con successo, ".$username."</b>"); 
           } else {
            echo("<b>Login non autorizzato! </b>");  
        }
        
       
    ?>
</body>
</html>