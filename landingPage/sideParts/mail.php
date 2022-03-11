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
        // Sendmail
        $arr_mail = array(
            "email"             => "dinellimichele00@gmail.com", // user email
            "name"              => "Michele",
            "system_email"      => "michele.dinelli5@studio.unibo.it", // your email
            "system_from_name"  => "Michele Dinelli", // your name
            "subject"           => "prova", 
            "message"           => "Ciao come stai ?",
            "message_template"  => "",
            "attachment"        => ""
        );
            
            try {
                $sendmail->mail($arr_mail);
            } catch(Exception $e) {
                print "Error!: " . $e->getMessage() . "<br/>";
            }

    ?>
</body>
</html>