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
        
        session_start();

        // Retrieve data from signup.html
        $uname = $_POST["uname"];
        $password = $_POST["psw"];
        $name = $_POST["name"];
        $surname = $_POST["surname"];
        $bday = $_POST["bday"];

        // Connection to db and logic to store data
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'Squidy.77');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');

            // Check if the username does not exist in the DB
            $query = ("SELECT Username FROM CONFVIRTUAL.UTENTE WHERE Username = :lab1");
            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $name);
            $res -> execute();
           
            while($row = $res -> fetch() ){
                if($row["Nome"] == $uname) {
                    $checked = true;
                } 
            }

            if($checked == true ){
                // TO DO: handle error 
            }  else {
                // User added to the database
                $query = ('INSERT INTO UTENTE(Username, Password, Nome, Cognome, DataNascita) VALUES(:lab1, :lab2, :lab3, :lab4, :lab5)');
                $res = $pdo -> prepare($query);
                $res -> bindValue(":lab1", $uname);
                $res -> bindValue(":lab2", $password);
                $res -> bindValue(":lab3", $name);
                $res -> bindValue(":lab4", $surname);
                $res -> bindValue(":lab5", $bday);

                $res -> execute();
                echo 'User inserted into table UTENTE';
            
            }
        
            $pdo = null;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            // equivalent to exit();
            die();
        }
        
    
    
    ?>
</body>
</html>