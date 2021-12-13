<?php

    
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=AUTHENTICATION', $user = 'root', $pass = 'Squidy.77');
        $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo -> exec('SET NAMES "utf8"');

        if(!empty($_POST["username"])){
            $query = ("SELECT Nome FROM AUTHENTICATION.UTENTE WHERE Nome = :lab1");
            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $name);
            $res -> execute();
    
             while($row = $res -> fetch() ){
                if($row["Nome"] == $name) {
                    $checked = true;
                } 
            }
    
            if($checked==true){
                echo "<span style='color:red'>Sorry username already exists</span>"
            } else {
                echo "<span style='color:red'>Sorry username already exists</span>"
            }
        }
      
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        // equivalent to exit();
        die();
    }

    

?>