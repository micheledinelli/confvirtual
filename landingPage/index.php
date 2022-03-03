<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="/DBProject2021/css/style.css">    
    <title>CONFVIRTUAL</title>
</head>
<body>
    
    <nav class="navbar py-3 navbar-expand-lg navbar-light bg-light sticky-top">
        <a class="navbar-brand ms-5" href="#">CONFVIRTUAL</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-4">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#sme">Some cool effects</a>
                </li>
            </ul>
        </div>
        
        <?php
            session_start();
            if (isset($_SESSION['user'])) {
        ?>
            
        <?php print"
                <ul class='navbar-nav me-3'>
                    <li class='nav-item active'>
                        <a class='nav-link'>Welcome back, {$_SESSION['user']}</a>
                    </li>
                    <li class='nav-item'>
                        <a class='nav-link'>Role: {$_SESSION['userType']}</a>
                    </li>
                </ul>";
        ?>

        <a class="btn btn-primary me-3" href="/DBProject2021/functionalities/base.php" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-grid-fill" viewBox="0 0 16 16">
                <path d="M1 2.5A1.5 1.5 0 0 1 2.5 1h3A1.5 1.5 0 0 1 7 2.5v3A1.5 1.5 0 0 1 5.5 7h-3A1.5 1.5 0 0 1 1 5.5v-3zm8 0A1.5 1.5 0 0 1 10.5 1h3A1.5 1.5 0 0 1 15 2.5v3A1.5 1.5 0 0 1 13.5 7h-3A1.5 1.5 0 0 1 9 5.5v-3zm-8 8A1.5 1.5 0 0 1 2.5 9h3A1.5 1.5 0 0 1 7 10.5v3A1.5 1.5 0 0 1 5.5 15h-3A1.5 1.5 0 0 1 1 13.5v-3zm8 0A1.5 1.5 0 0 1 10.5 9h3a1.5 1.5 0 0 1 1.5 1.5v3a1.5 1.5 0 0 1-1.5 1.5h-3A1.5 1.5 0 0 1 9 13.5v-3z"/>
            </svg>
        </a>
        <a class="btn btn-danger me-5" href="logut.php" role="button" aria-expanded="false">Log-out</a>
            
        <?php
            } else {
        ?>
            <ul class="navbar-nav me-5">
                <div class="dropdown nav-item">
                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-expanded="false">
                        Get in touch
                    </a>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="login.html">Login</a>
                        <a class="dropdown-item" href="register.html">Register</a>
                    </div>
                </div>
            </ul>
        <?php
            }
        ?>

    </nav>

    <div class="container jumbotron my-5 text-center">
        <h1 class="display-4">Hello, world!</h1>
        <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
        <hr class="my-4">
        <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
        <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
    </div>
    <div class="container jumbotron my-5 text-center">
        <h1 class="display-4">Hello, world!</h1>
        <p class="lead">This is a simple hero unit, a simple jumbotron-style component for calling extra attention to featured content or information.</p>
        <hr class="my-4">
        <p>It uses utility classes for typography and spacing to space content out within the larger container.</p>
        <a class="btn btn-primary btn-lg" href="#" role="button">Learn more</a>
    </div>
    
    <?php
        
        // Start or resume the session
        session_start();
        if(isset($_SESSION['expire'])) {
            if(time() > $_SESSION['expire']) {
                session_unset();
                session_destroy();
                header('Location:/DBProject2021/landingPage/index.php');
            } 
        }
         
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user ='root', $pass='root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            $query1 = ("SELECT COUNT(*) AS Counter FROM CONFVIRTUAL.CONFERENZA WHERE Svolgimento = 'COMPLETATA'");
            $res = $pdo -> prepare($query1);
            $res -> execute();
            $row = $res -> fetch();
            $conf = $row["Counter"];

            $query2 = ("SELECT COUNT(*) AS Counter FROM CONFVIRTUAL.CONFERENZA WHERE Svolgimento = 'ATTIVA'");
            $res = $pdo -> prepare($query2);
            $res -> execute();
            $row = $res -> fetch();
            $confAttive = $row["Counter"];

            $query3 = ("SELECT COUNT(*) AS Counter FROM CONFVIRTUAL.UTENTE");
            $res = $pdo -> prepare($query3);
            $res -> execute();
            $row = $res -> fetch();
            $numUtenti = $row["Counter"];

            /**
             * TO DO : Classifica per il voto medio
             */

        } catch( PDOException $e ) {
            echo("[ERRORE]".$e->getMessage());
            exit();
        }
    ?>
    
    <div class="counter container-fluid my-5 bg-light">
        <div class="row">
            <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                <div class="employees">
                    <p class="counter-count"><?php echo "{$conf}" ?></p>
                    <p class="employee-p">Conferenze Registrate</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                <div class="customer">
                    <p class="counter-count"><?php echo "{$confAttive}" ?></p>
                    <p class="customer-p">Conferenze Attive</p>
                </div>
            </div>

            <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                <div class="design">
                    <p class="counter-count"><?php echo "{$numUtenti}"?></p>
                    <p class="design-p">Utenti</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container text-center">
        <img id="sme" height="200px" src="https://zindex99.com/front/assets/img/effetti-speciali.gif" alt="ciao">
    </div>

    <footer class="my-5 page-footer font-small cyan darken-3 text-center">

        <div class="container">
  
            <div class="row">
        
                <div class="col-3">
                    <a href="login.html" class="btn btn-secondary" role="button" data-bs-toggle="button">
                        Sign-in
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                            <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>
                    </a>
                </div>

                <div class="col-3">
                    <a href="register.html" class="btn btn-secondary" role="button" data-bs-toggle="button">
                        Sign-up
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-at" viewBox="0 0 16 16">
                            <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"/>
                        </svg>
                    </a>
                </div>

                <div class="col-3">
                    <a href="#" class="btn btn-secondary" role="button" data-bs-toggle="button">
                        Bring me up
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                            <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
                        </svg>
                    </a>
                </div>

                <div class="col-3">
                    <a href="https://github.com/micheledinelli/DBProject2021" class="btn btn-secondary" role="button" data-bs-toggle="button">
                        Github
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-github" viewBox="0 0 16 16">
                            <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
                        </svg>
                    </a>
                </div>

            </div>
        </div> 
        
        <div class=" my-5 footer-copyright text-center py-3">©Sayonara 2022</div>
  
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous"></script>
</body>
</body>
</html>