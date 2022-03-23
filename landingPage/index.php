<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <title>CONFVIRTUAL</title>
</head>
<body>
    
    <nav class="navbar py-4 navbar-expand-lg navbar-light bg-light sticky-top">
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
                    <a class="nav-link" href="#ourSponsors">I nostri sponsor</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#classifica">Classifica migliori speaker/utenti</a>
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

        <a class="btn btn-primary me-3" href="../functionalities/base.php" role="button">
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
            //MySQL
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user ='root', $pass='root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            //MongoDB
            require '../vendor/autoload.php';
            $conn = new MongoDB\Client("mongodb://localhost:27017");
            $collection = $conn -> CONFVIRTUAL_log -> log;

            //MySQL
            $query1 = ("SELECT COUNT(*) AS Counter FROM CONFVIRTUAL.CONFERENZA WHERE Svolgimento = 'COMPLETATA'");
            $res = $pdo -> prepare($query1);
            $res -> execute();
            $row = $res -> fetch();
            $conf = $row["Counter"];

            //MongoDB
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'SELECT',
                'InvolvedTable'	=> 'CONFERENZA'
            ]);

            //MySQL
            $query2 = ("SELECT COUNT(*) AS Counter FROM CONFVIRTUAL.CONFERENZA WHERE Svolgimento = 'ATTIVA'");
            $res = $pdo -> prepare($query2);
            $res -> execute();
            $row = $res -> fetch();
            $confAttive = $row["Counter"];

            //MongoDB
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'SELECT',
                'InvolvedTable'	=> 'CONFERENZA'
            ]);

            //MySQL
            $query3 = ("SELECT COUNT(*) AS Counter FROM CONFVIRTUAL.UTENTE");
            $res = $pdo -> prepare($query3);
            $res -> execute();
            $row = $res -> fetch();
            $numUtenti = $row["Counter"];

            //MongoDB
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'SELECT',
                'InvolvedTable'	=> 'CONFERENZA'
            ]);

            $queryClassifica = '
            SELECT ROUND(AVG(Voto),1) AS MediaVoto, Username, Tipologia
            FROM CLASSIFICA
            GROUP BY Username, Tipologia
            order by ROUND(AVG(Voto),1) DESC';

            $res = $pdo -> prepare($queryClassifica);
            $res -> execute();

            //MongoDB
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'SELECT',
                'InvolvedTable'	=> 'VIEW CLASSIFICA'
            ]);

            $classificati = array();
            while($row = $res -> fetch()) {
                $utente = new StdClass();
                $utente -> username = $row['Username'];
                $utente -> tipologia = $row['Tipologia'];
                $utente -> votoMedio = $row['MediaVoto'];
                array_push($classificati, $utente);
            }

        } catch( PDOException $e ) {
            echo("[ERRORE]".$e->getMessage());
            exit();
        }
    ?>

    <div class="container-fluid bg-light" id="stats">
        <div class="container jumbotron my-5 text-center" style="padding-top:30px;">
            <h1 class="display-5">Confvirtual è pronta ad ospitare anche la tua conferenza!</h1>
            <p class="lead my-3">Unisciti alla nostra community <a href="register.html">Sign Up</a></p>
        </div>

        <div class="counter container-fluid my-5">
            <div class="row">
                <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                    <div class="counter">
                        <p class="counter-count bg-primary"><?php echo "{$conf}" ?></p>
                        <p class="p-counter">Conferenze registrate</p>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                    <div class="counter">
                        <p class="counter-count bg-primary"><?php echo "{$confAttive}"?></p>
                        <p class="p-counter">Conferenze attive</p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-3 col-sm-3 col-xs-12">
                    <div class="counter">
                        <p class="counter-count bg-primary"><?php echo "{$numUtenti}"?></p>
                        <p class="p-counter">Utenti</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid sponsors text-center" id="ourSponsors">
        <div class="container jumbotron my-5 text-center">
            <h1 class="display-4">Alcuni dei nostri sponsor</h1>
            <p class="lead my-3">Gli sponsor di confvirtual permettono che il nostro servizio sia il migliore possibile</p>
            <p class="lead text-small">Diventa anche tu un nostro partner!</p>
        </div>
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6">
                    <img src="../logos/dinelli-logo.png">
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <img src="../logos/brajucha-logo.png">
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <img src="../logos/pinazza-logo.png">
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <img src="../logos/leob-logo.png">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-6">
                    <img src="../logos/bepo-logo.png">
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <img src="../logos/friuj-logo.png">
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <img src="../logos/sayonara-logo.png">
                </div>
                <div class="col-lg-3 col-md-4 col-6">
                    <img src="../logos/maich-logo.png">
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid bg-light mt-5 h-75vh" id="classifica">
        <div class="container jumbotron text-center pt-5">
            <h1 class="display-4">I migliori speaker e presentatori</h1>
            <p class="lead my-3">Ecco il podio dei nostri speaker e presenter
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-list-ol ml-3" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"/>
                        <path d="M1.713 11.865v-.474H2c.217 0 .363-.137.363-.317 0-.185-.158-.31-.361-.31-.223 0-.367.152-.373.31h-.59c.016-.467.373-.787.986-.787.588-.002.954.291.957.703a.595.595 0 0 1-.492.594v.033a.615.615 0 0 1 .569.631c.003.533-.502.8-1.051.8-.656 0-1-.37-1.008-.794h.582c.008.178.186.306.422.309.254 0 .424-.145.422-.35-.002-.195-.155-.348-.414-.348h-.3zm-.004-4.699h-.604v-.035c0-.408.295-.844.958-.844.583 0 .96.326.96.756 0 .389-.257.617-.476.848l-.537.572v.03h1.054V9H1.143v-.395l.957-.99c.138-.142.293-.304.293-.508 0-.18-.147-.32-.342-.32a.33.33 0 0 0-.342.338v.041zM2.564 5h-.635V2.924h-.031l-.598.42v-.567l.629-.443h.635V5z"/>
                </svg>
            </p>
            <p class="lead small">I voti sono inseriti dagli admin della piattafroma</p>
        </div>

        <div id="my-carousel" data-interval="5000" class="container carousel slide carousel-fade w-75 pb-5" data-ride="carousel" style="margin-top:50px;">
            <ol class="carousel-indicators">
                <li data-target="#my-carousel" data-slide-to="0" class="active"></li>
                <li data-target="#my-carousel" data-slide-to="1"></li>
                <li data-target="#my-carousel" data-slide-to="2"></li>
            </ol>
            
            <a class="carousel-control-prev" style='text-decoration:none;' href="#my-carousel" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only visually-hidden">Previous</span>
            </a>
            <a class="carousel-control-next" style='text-decoration:none;' href="#my-carousel" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only visually-hidden">Next</span>
            </a>

        </div>
    </div>

    <div class="relative-bottom">
        <!-- style="background-color: #3f51b5" cool color to eventually use... -->
        <footer class="text-center bg-primary text-white">
            <div class="container">
                <section class="mt-5">
                    <div class="row text-center d-flex justify-content-center pt-5">
                        <div class="col-md-2">
                            <h6 class="text-uppercase font-weight-bold">
                                <a href="sideParts/aboutUs.html" class="text-white">About us</a>
                            </h6>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-uppercase font-weight-bold">
                                <a href="#!" class="text-white">Other projects</a>
                            </h6>
                        </div>
                        <div class="col-md-2">
                            <h6 class="text-uppercase font-weight-bold">
                                <a href="sideParts/contactUs.html" class="text-white" id="contact-us">Contact us</a>
                            </h6>
                        </div>
                    </div>
                </section>
                <hr class="my-5" />

                <section class="mb-5">
                    <div class="row d-flex justify-content-center">
                        <div class="col-lg-8">
                            <p>
                                Questo progetto è stato svolto da Brajucha Filippo, Youssef Hanna e Michele Dinelli.
                                Riguarda l'esame di Basi di Dati del nostro corso di laurea ed è stato molto divertente.
                                Ah giusto per la normativa vi informiamo che abbiamo un DataBase con tutti i vostri dati e password... 
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Section: Social -->
                <section class="text-center mb-5">
                    <a href="https://github.com/micheledinelli/DBProject2021" class="btn btn-secondary me-4" role="button" data-bs-toggle="button">
                        Github
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-github" viewBox="0 0 16 16">
                            <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27.68 0 1.36.09 2 .27 1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.012 8.012 0 0 0 16 8c0-4.42-3.58-8-8-8z"/>
                        </svg>
                    </a>
                    <a href="login.html" class="btn btn-secondary me-4" role="button" data-bs-toggle="button">
                        Sign-in
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                            <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                        </svg>
                    </a>
                    <a href="register.html" class="btn btn-secondary me-4" role="button" data-bs-toggle="button">
                        Sign-up
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-at" viewBox="0 0 16 16">
                            <path d="M13.106 7.222c0-2.967-2.249-5.032-5.482-5.032-3.35 0-5.646 2.318-5.646 5.702 0 3.493 2.235 5.708 5.762 5.708.862 0 1.689-.123 2.304-.335v-.862c-.43.199-1.354.328-2.29.328-2.926 0-4.813-1.88-4.813-4.798 0-2.844 1.921-4.881 4.594-4.881 2.735 0 4.608 1.688 4.608 4.156 0 1.682-.554 2.769-1.416 2.769-.492 0-.772-.28-.772-.76V5.206H8.923v.834h-.11c-.266-.595-.881-.964-1.6-.964-1.4 0-2.378 1.162-2.378 2.823 0 1.737.957 2.906 2.379 2.906.8 0 1.415-.39 1.709-1.087h.11c.081.67.703 1.148 1.503 1.148 1.572 0 2.57-1.415 2.57-3.643zm-7.177.704c0-1.197.54-1.907 1.456-1.907.93 0 1.524.738 1.524 1.907S8.308 9.84 7.371 9.84c-.895 0-1.442-.725-1.442-1.914z"/>
                        </svg>
                    </a>
                    <a href="#" class="btn btn-secondary me-4" role="button" data-bs-toggle="button">
                        Bring me up
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                            <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
                        </svg>
                    </a>
                    
                </section>
            </div>

            <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2)">
                © 2020 Copyright: Sayonara
            </div>
        </footer>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous"></script>
    <script>
        const classificati = <?php echo json_encode($classificati); ?>;
        const myCarousel = document.getElementById("my-carousel");    
        const divInner = document.createElement("div");
        divInner.classList.add("carousel-inner");
        var dynamicContent = '';

        
        for(let i = 0; i < classificati.length; i++) {
            votoMedio = classificati[i]["votoMedio"];
            username = classificati[i]["username"];
            tipologia = classificati[i]["tipologia"];
            stars = calcStarRating(votoMedio);
            // Per definire il primo attivo
            if(i === 0) {
                dynamicContent +=  `
                <div class="carousel-item text-center active">
                    <div class="container card border-dark mb-3" style="max-width: 18rem;">
                        <div class="card-body text-dark">
                            <h5 class="card-text">Posizione: ${i+1}</h5>
                            <h5 class="card-title">${username}</h5>
                            <p class="card-text">${tipologia}</p>
                            <p class="card-text">Voto Medio: ${votoMedio}</p>
                            ${stars}
                        </div>
                    </div>
                </div>`;
            } else {
                dynamicContent +=  `
                <div class="carousel-item text-center">
                    <div class="container card border-dark mb-3" style="max-width: 18rem;">
                        <div class="card-body text-dark">
                            <h5 class="card-text">Posizione: ${i+1}</h5>
                            <h5 class="card-title">${username}</h5>
                            <p class="card-text">${tipologia}</p>
                            <p class="card-text">Voto Medio: ${votoMedio}</p>
                            ${stars}
                        </div>
                    </div>
                </div>`;
            }
               
        }

        divInner.innerHTML = dynamicContent;
        myCarousel.append(divInner);

        function calcStarRating(votoMedio) {
            starRating = Math.round(votoMedio * 0.5);
            stars = '';
            if(starRating === 5) {
                stars +=`     
                <p>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                </p>`
                return stars;

            } else if(starRating === 4) {
                stars +=`     
                <p>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star'></span>
                </p>`
                return stars;

            } else if(starRating === 3) {
                stars +=`     
                <p>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star'></span>
                    <span class='bi bi-star'></span>
                </p>`
                return stars;

            } else if(starRating === 2) {
                stars +=`     
                <p>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star'></span>
                    <span class='bi bi-star'></span>
                    <span class='bi bi-star'></span>
                </p>`
                return stars;

            } else if(starRating === 1) {
                stars +=`     
                <p>
                    <span class='bi bi-star-fill' style='color: #f3da35'></span>
                    <span class='bi bi-star'></span>
                    <span class='bi bi-star'></span>
                    <span class='bi bi-star'></span>
                    <span class='bi bi-star'></span>
                </p>`
                return stars;

            }
        }
    </script>
</body>
</body>
</html>