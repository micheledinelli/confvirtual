<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <link rel="stylesheet" href="/DBProject2021/css/form.css">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <title>Base</title>
</head>
<body>

    <?php
        session_start();
        if(time() > $_SESSION['expire']) {
            session_unset();
            session_destroy();
            header('Location:/DBProject2021/landingPage/index.php');
        } 

        // Connection to db
        $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
        $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo -> exec('SET NAMES "utf8"');

        //Connection to MongoDB
        require '../vendor/autoload.php';
        $conn = new MongoDB\Client("mongodb://localhost:27017");
        $collection = $conn -> CONFVIRTUAL_log -> log;	

        //MySQL
        // CONFERENCES
        $query = ('SELECT * FROM CONFERENZA WHERE Svolgimento = "ATTIVA"');

        $res = $pdo -> prepare($query);
        $res -> execute();
        $conferenze = array(); 

        while($row = $res -> fetch()) {
            $conferenza = new stdClass();
            $conferenza -> acronimo = $row["Acronimo"];
            $conferenza -> nome = $row["Nome"];
            $conferenza -> annoEdizione = $row["AnnoEdizione"];
            array_push($conferenze, $conferenza);
        }

        //MongoDB
        $insertOneResult = $collection->insertOne([
            'TimeStamp' 		=> time(),
            'User'				=> $_SESSION['user'],
            'OperationType'		=> 'SELECT',
            'InvolvedTable'	    => 'CONFERENZA'
        ]);
        
        //MySQL
        // SESSIONS
        $querySessions = ('SELECT * FROM SESSIONE');
        
        $res = $pdo -> prepare($querySessions);
        $res -> execute();
        $sessioni = array();

        while($row = $res -> fetch()) {
            $sessione = new stdClass();
            $sessione -> codice = $row["Codice"];
            $sessione -> acronimoConferenza = $row["AcronimoConferenza"];
            $sessione -> titoloSessione = $row["Titolo"];
            $sessione -> numPresentazioni = $row["NumeroPresentazioni"];
            $sessione -> oraInizio = $row["OraInizio"];
            $sessione -> oraFine = $row["OraFine"];
            $sessione -> data = $row["Data"];
            array_push($sessioni, $sessione);
        }

        //MongoDB
        $insertOneResult = $collection->insertOne([
            'TimeStamp' 		=> time(),
            'User'				=> $_SESSION['user'],
            'OperationType'		=> 'SELECT',
            'InvolvedTable'	    => 'SESSIONE'
        ]);

        //MySQL
        $querySessionsPermitted = ('SELECT *
                            FROM REGISTRAZIONE AS R, SESSIONE AS S
                            WHERE R.AcronimoConferenza = S.AcronimoConferenza AND Username = :lab1');
        
        $res = $pdo -> prepare($querySessionsPermitted);
        $res -> bindValue(":lab1", $_SESSION['user']);
        $res -> execute();

        $sessionsPermitted = array();
        while($row = $res -> fetch()) {
            $sessione = new stdClass();
            $sessione -> acronimoConferenza = $row["AcronimoConferenza"];
            $sessione -> titoloSessione = $row["Titolo"];
            $sessione -> data = $row["Data"];
            $sessione -> oraInizio = $row["OraInizio"];
            $sessione -> oraFine = $row["OraFine"];
            array_push($sessionsPermitted, $sessione);
        }

        //MongoDB
        $DATA = array("REGISTRAZIONE", "SESSIONE");
        $insertOneResult = $collection->insertOne([
            'TimeStamp' 		=> time(),
            'User'				=> $_SESSION['user'],
            'OperationType'		=> 'SELECT',
            'InvolvedTable'	    => $DATA
        ]);

        //MySQL
        // PRESENTATIONS
        // Considerare l'operazione UNION ma si perderebbe specificità
        // alcuni campi non sono comuni nei tutorial e negli articoli
        // e.g abstract...
        $queryPresArticoli = 'SELECT * 
                            FROM PRESENTAZIONE AS P, P_ARTICOLO AS PA
                            WHERE P.Codice = PA.CodicePresentazione;';
        $res = $pdo -> prepare($queryPresArticoli);
        $res -> execute();

        $articles = array();
        while($row = $res -> fetch()) {
            $article = new stdClass();
            $article -> codicePresentazione = $row["CodicePresentazione"];
            $article -> oraInizio = $row["OraInizio"];
            $article -> oraFine = $row["OraFine"];
            $article -> tipologia = $row["Tipologia"];
            $article -> titolo = $row["Titolo"];
            $article -> presenter = $row["UsernamePresenter"];
            $article -> stato = $row["StatoSvolgimento"];
            array_push($articles, $article);
        }

        //MongoDB
        $DATA = array("PRESENTAZIONE", "P_ARTICOLO");
        $insertOneResult = $collection->insertOne([
            'TimeStamp' 		=> time(),
            'User'				=> $_SESSION['user'],
            'OperationType'		=> 'SELECT',
            'InvolvedTable'	    => $DATA
        ]);

        //MySQL
        $queryPresTutorial = 'SELECT * 
                            FROM PRESENTAZIONE AS P, P_TUTORIAL AS PT
                            WHERE P.Codice = PT.CodicePresentazione;
                            ';
        $res = $pdo -> prepare($queryPresTutorial);
        $res -> execute();

        $tutorials = array();
        while($row = $res -> fetch()) {
            $tutorial = new stdClass();
            $tutorial -> codicePresentazione = $row["CodicePresentazione"];
            $tutorial -> oraInizio = $row["OraInizio"];
            $tutorial -> oraFine = $row["OraFine"];
            $tutorial -> tipologia = $row["Tipologia"];
            $tutorial -> titolo = $row["Titolo"];
            array_push($tutorials, $tutorial);
        }

        //MongoDB
        $DATA = array("PRESENTAZIONE", "P_TUTORIAL");
        $insertOneResult = $collection->insertOne([
            'TimeStamp' 		=> time(),
            'User'				=> $_SESSION['user'],
            'OperationType'		=> 'SELECT',
            'InvolvedTable'	    => $DATA
        ]);

        //MySQL
        // FAVORITES
        $query = ('SELECT * 
                FROM FAVORITE AS F, P_ARTICOLO AS PA, PRESENTAZIONE AS P 
                WHERE Username = :lab1 AND F.CodicePresentazione = PA.CodicePresentazione AND PA.CodicePresentazione = P.Codice');
        
        $res = $pdo -> prepare($query);
        $res -> bindValue(":lab1", $_SESSION['user']);
        $res -> execute();
        
        $favoritesArticoli = array();
        while($row = $res -> fetch()) {
            $favoriteArticolo = new stdClass();
            $favoriteArticolo -> codicePresentazione = $row["CodicePresentazione"];
            $favoriteArticolo -> oraInizio = $row["OraInizio"];
            $favoriteArticolo -> oraFine = $row["OraFine"];
            $favoriteArticolo -> tipologia = $row["Tipologia"];
            $favoriteArticolo -> titolo = $row["Titolo"];
            array_push($favoritesArticoli, $favoriteArticolo);
        }

        //MongoDB
        $DATA = array("FAVORITE", "P_ARTICOLO", "PRESENTAZIONE");
        $insertOneResult = $collection->insertOne([
            'TimeStamp' 		=> time(),
            'User'				=> $_SESSION['user'],
            'OperationType'		=> 'SELECT',
            'InvolvedTable'	    => $DATA
        ]);

        //MySQL
        $query = ('SELECT * 
                FROM FAVORITE AS F, P_TUTORIAL AS PT, PRESENTAZIONE AS P 
                WHERE Username = :lab1 AND F.CodicePresentazione = PT.CodicePresentazione AND PT.CodicePresentazione = P.Codice');
        
        $res = $pdo -> prepare($query);
        $res -> bindValue(":lab1", $_SESSION['user']);
        $res -> execute();

        $favoritesTutorial = array();
        while($row = $res -> fetch()) {
            $favoriteTutorial = new stdClass();
            $favoriteTutorial -> codicePresentazione = $row["CodicePresentazione"];
            $favoriteTutorial -> oraInizio = $row["OraInizio"];
            $favoriteTutorial -> oraFine = $row["OraFine"];
            $favoriteTutorial -> tipologia = $row["Tipologia"];
            $favoriteTutorial -> titolo = $row["Titolo"];
            array_push($favoritesTutorial, $favoriteTutorial);
        }

        //MongoDB
        $DATA = array("FAVORITE", "P_TUTORIAL", "PRESENTAZIONE");
        $insertOneResult = $collection->insertOne([
            'TimeStamp' 		=> time(),
            'User'				=> $_SESSION['user'],
            'OperationType'		=> 'SELECT',
            'InvolvedTable'	    => $DATA
        ]);

    ?>
    
    <div class="wrapper">
        <nav id="sidebar" class="vh-100 bg-primary">
            <div class="sidebar-header">
                <a class="btn btn-primary" href="../landingPage/index.php" role="button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                        <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
                    </svg>
                </a>
                <hr>
            </div>
            <ul class="list-unstyled components">
                <li> <a href="#" onclick="visualizeConferences()">Visualizza conferenze</a> </li>
                <li> <a href="#" onclick="registerToConference()">Registrati ad una conferenza</a> </li>
                <li> <a href="#" onclick="visualizeSessions()">Visualizza sessioni</a> </li>
                <li> <a href="#" onclick="insertMessage()">Inserisci messaggio</a> </li>
                <li> <a href="/DBProject2021/chat/chat.php" onclick="">Visualizza chat di sessione</a> </li>
                <li> <a href="#pageSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Favorites</a>
                    <ul class="collapse list-unstyled" id="pageSubmenu">
                        <li> <a href="#" onclick="showPresentations()">Add favorite</a> </li>
                        <li> <a href="#" onclick="showFavorites()">My favorites</a> </li>
                    </ul>
                </li>
                
                <li> 
                    <?php
                        session_start();
                        
                        if (isset($_SESSION['userType']) && $_SESSION['userType'] == "ADMIN") {
                            print'  
                                    <a href="/DBProject2021/functionalities/admin.php" class="container me-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                        </svg>
                                        Admin area
                                    </a';
                        
                        } elseif(isset($_SESSION['userType']) && $_SESSION['userType'] == "PRESENTER") {
                            print'
                                    <a href="/DBProject2021/functionalities/speaker_presenter.php" class="container me-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                        </svg>
                                        Presenter area
                                    </a';
                        
                        } elseif(isset($_SESSION['userType']) && $_SESSION['userType'] == "SPEAKER") {
                            print'
                                    <a href="/DBProject2021/functionalities/speaker_presenter.php" class="container me-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                                        </svg>
                                        Speaker area
                                    </a';
                        }
                    ?>
                </li>
            </ul>
        </nav>

        <div class="content container-fluid">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
                 <button type="button" id="sidebarCollapse" class="btn navbar-toggler-icon"> </button> 
            </nav>
            
        <?php
            session_start();
            if (isset($_SESSION["opSuccesfull"])) {
        ?>
            
        <?php print'
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Ottimo!</strong> Operazione andata a buon fine!
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
              </div>
            ';
            unset($_SESSION["opSuccesfull"]);
        ?>
    
        <?php
            } elseif(isset($_SESSION["error"])) {
            unset($_SESSION["error"]);
        ?>
           <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Si è verificato un errore!</strong> Riprova controllando che i campi inseriti siano corretti.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php
            }
        ?>
                <div id="main-content" class="content-wrapper text-center">
                    
                </div>            
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    
    <script>
        
        const content = document.getElementById("main-content");
        const userName = <?php echo json_encode($_SESSION["user"]); ?>
        
        var conferenze = <?php echo json_encode($conferenze); ?>;

        var sessioni = <?php echo json_encode($sessioni); ?>;
        var sessioniPermesse = <?php echo json_encode($sessionsPermitted); ?>;

        var articles = <?php echo json_encode($articles); ?>;
        var tutorials = <?php echo json_encode($tutorials); ?>;

        var favoritesArticoli = <?php echo json_encode($favoritesArticoli); ?>;
        var favoritesTutorial = <?php echo json_encode($favoritesTutorial); ?>;

        function visualizeConferences() {
            content.textContent = '';
            let div = document.createElement('div');
            div.classList.add('row');
            var cardContent = "";

            for(let i = 0; i < conferenze.length; i++) {
                acr = conferenze[i]["acronimo"];
                anno = conferenze[i]["annoEdizione"];
                title = conferenze[i]["nome"];
                cardContent += ` 
                    <div class="card" style="width: 18rem; margin: 5px 5px">
                        <div class="card-body">
                            <h5 class="card-title">${title}</h5>
                            <p class="card-text">Acronimo: ${acr} <br> Anno edizione: ${anno}</p>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#${acr}">Register</button>
                        </div>
                    </div>

                    <div id="${acr}" class="modal fade">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Sei sicuro di volerti registrare a ${title}? 
                                </div>
                                <div class="modal-footer">
                                    <form action="registerToConference.php" method="post" class="container">
                                        <div class="mb-3 form-group floating">
                                            <input type="hidden" class="form-control floating" name="username" value=${userName} readonly>
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="hidden" class="form-control floating" name="acronimo" value=${acr} autocomplete="off">
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="hidden" class="form-control floating" name="annoEdizione" value=${anno} autocomplete="off">
                                        </div>
                                        <div class="container text-center">
                                            <button type="submit" class="btn btn-primary">Register</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>                                    
                                </div>
                            </div>
                        </div>
                    </div>`;
            }
            
            div.innerHTML = cardContent;
            content.append(div);
        }

        function registerToConference() {
            content.textContent = '';
            content.innerHTML = `
            <div class="container-fluid text-center w-50">
                <h2>Registrati</h2>
                <hr class="my-4">
                <form action="registerToConference.php" method="post" class="container my-5">
                    <div class="mb-3 form-group floating">
                        <input type="text" class="form-control floating" name="username" required value=${userName} readonly>
                        <label for="username">Username</label>          
                    </div>
                    <div class="mb-3 form-group floating">
                        <input type="text" class="form-control floating" name="acronimo" required autocomplete="off">
                        <label for="acronimo">Acronimo Conferenza</label> 
                    </div>
                    <div class="mb-3 form-group floating">
                        <input type="number" min="1900" max="2099" class="form-control floating" name="annoEdizione" required autocomplete="off">
                        <label for="annoEdizione">Anno</label>          
                    </div>
                    <div class="container text-center my-5">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>`;   
        }

        function visualizeSessions() {
            content.textContent = '';
            var dynamicContent = '';
            dynamicContent = `
            <div class="container text-center">
            <p>Sessioni della conferenza<p> 
            <p class="small">Clicca sul titolo per visualizzare le presentazioni di una sessione</p>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Acronimo conferenza</th>
                            <th scope="col">Titolo sessione</th>
                            <th scope="col">Data</th>
                            <th scope="col">Ora inizio</th>
                            <th scope="col">Ora Fine</th>
                            <th scope="col">Numero presentazioni</th>
                        </tr>
                    </thead>
                    <tbody>` ;
            
            for(let i = 0; i < sessioni.length; i++) {
                codice = sessioni[i]["codice"];
                acr = sessioni[i]["acronimoConferenza"];
                titleSession = sessioni[i]["titoloSessione"];
                numPresentazioni = sessioni[i]["numPresentazioni"];
                oraFine = sessioni[i]["oraFine"];
                oraInizio = sessioni[i]["oraInizio"];
                data = sessioni[i]["data"];

                dynamicContent += `
                <tr>
                    <td>${acr}</td>
                    <td><button type="button" id="${codice}" class="btn btn-primary" onclick="showPresentations(this.id)">${titleSession}</button></td> 
                    <td>${data}</td>
                    <td>${oraInizio}</td>
                    <td>${oraFine}</td>
                    <td>${numPresentazioni}</td>
                </tr>`;
            }
            
            dynamicContent += `</tbody></table>`;
            content.innerHTML = dynamicContent;
 
        }

        function showPresentations(id) {

            content.textContent = '';
            var dynamicContent = '';

            // Se l'id (il codice della presentazione) 
            // viene specificato si mostrano solo le presentazioni di una data sessione
            // altrimenti si mostrano tutte
            if(!id) {
                // Id non specificato 
                // Qui è possibile aggiungere una presentazione alla lista delle preferite

                if(tutorials.length > 0) {
                    dynamicContent = `
                    <div class="container text-center">
                    <p>Tutorial<p> 
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Codice</th>
                                    <th scope="col">Titolo</th>
                                    <th scope="col">Ora inizio</th>
                                    <th scope="col">Ora Fine</th>
                                    <th scope="col">Favorite</th>
                                </tr>
                            </thead>
                            <tbody> ` ;
                    
                    for(let i = 0; i < tutorials.length; i++) {
                        codicePresentazione = tutorials[i]["codicePresentazione"];
                        oraInizio = tutorials[i]["oraInizio"];
                        oraFine = tutorials[i]["oraFine"];
                        titolo = tutorials[i]["titolo"];
                        
                        var abilitato = "";
                        var btnColor = "btn-primary";
                        for(let i = 0; i < favoritesTutorial.length; i++) {
                            if(favoritesTutorial[i]["codicePresentazione"] === codicePresentazione) {
                                abilitato = "disabled";
                                btnColor = "btn-success";
                            }
                        }

                        dynamicContent += `
                        <tr>
                            <td>${codicePresentazione}</td>
                            <td>${titolo}</td>
                            <td>${oraInizio}</td>
                            <td>${oraFine}</td>
                            <td>
                                <form action="manageFavorites.php" method="post">
                                    <input type="hidden" name="username" value="${userName}">
                                    <input type="hidden" name="codice" value="${codicePresentazione}">
                                    <input type="hidden" name="manageOpt" value="true">
                                    <button type="submit" ${abilitato} class="btn ${btnColor}"><i class="bi bi-star"></i></button>
                                </form>
                            </td>
                        </tr>`;
                        
                        
                    }
                    
                    dynamicContent += `</tbody></table>`;
                }
                
                if(articles.length > 0) {
                    dynamicContent += `
                    <div class="container text-center">
                    <p>Articoli<p> 
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Codice</th>
                                    <th scope="col">Titolo</th>
                                    <th scope="col">Ora inizio</th>
                                    <th scope="col">Ora Fine</th>
                                    <th scope="col">Presenter</th>
                                    <th scope="col">Stato</th>
                                    <th scope="col">Favorite</th>
                                </tr>
                            </thead>
                            <tbody> ` ;
                    
                    for(let i = 0; i < articles.length; i++) {
                        codicePresentazione = articles[i]["codicePresentazione"];
                        oraInizio = articles[i]["oraInizio"];
                        oraFine = articles[i]["oraFine"];
                        titolo = articles[i]["titolo"];
                        stato = articles[i]["stato"];
                        presenter = articles[i]["presenter"];
                        
                        var abilitato = "";
                        var btnColor = "btn-primary";
                        for(let i = 0; i < favoritesArticoli.length; i++) {
                            if(favoritesArticoli[i]["codicePresentazione"] === codicePresentazione) {
                                abilitato = "disabled";
                                btnColor = "btn-success";
                            }
                        }

                        dynamicContent += `
                        <tr>
                            <td>${codicePresentazione}</td>
                            <td>${titolo}</td>
                            <td>${oraInizio}</td>
                            <td>${oraFine}</td>
                            <td>${presenter}</td>
                            <td>${stato}</td>
                            <td>
                                <form action="manageFavorites.php" method="post">
                                    <input type="hidden" name="username" value="${userName}">
                                    <input type="hidden" name="codice" value="${codicePresentazione}">
                                    <input type="hidden" name="manageOpt" value="true">
                                    <button type="submit" ${abilitato} class="btn ${btnColor}"><i class="bi bi-star"></i></button>
                                </form>
                            </td>
                        </tr>`;
                    }
                    
                    dynamicContent += `</tbody></table>`;
                    content.innerHTML = dynamicContent;
                }

            } else {
                // Id specificato
                if(tutorials.length !== 0) {
                    dynamicContent = `
                    <div class="container text-center">
                    <p>Tutorial<p> 
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Codice</th>
                                    <th scope="col">Titolo</th>
                                    <th scope="col">Data</th>
                                    <th scope="col">Ora inizio</th>
                                    <th scope="col">Ora Fine</th>
                                </tr>
                            </thead>
                            <tbody> ` ;
                    
                    for(let i = 0; i < tutorials.length; i++) {
                        codicePresentazione = tutorials[i]["codicePresentazione"];
                        oraInizio = tutorials[i]["oraInizio"];
                        oraFine = tutorials[i]["oraFine"];
                        titolo = tutorials[i]["titolo"];
                        
                        if(codicePresentazione === id) {
                            dynamicContent += `
                            <tr>
                                <td>${codicePresentazione}</td>
                                <td>${titolo}</td>
                                <td>${data}</td>
                                <td>${oraInizio}</td>
                                <td>${oraFine}</td>
                            </tr>`;
                        }
                    }
                    dynamicContent += `</tbody></table>`;
                }

                if(articles.length > 0) {
                    dynamicContent += `
                    <div class="container text-center">
                    <p>Articoli<p> 
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Codice</th>
                                    <th scope="col">Titolo</th>
                                    <th scope="col">Ora inizio</th>
                                    <th scope="col">Ora Fine</th>
                                    <th scope="col">Presenter</th>
                                    <th scope="col">Stato</th>
                                </tr>
                            </thead>
                            <tbody> ` ;
                    
                    for(let i = 0; i < articles.length; i++) {
                        codicePresentazione = articles[i]["codicePresentazione"];
                        oraInizio = articles[i]["oraInizio"];
                        oraFine = articles[i]["oraFine"];
                        titolo = articles[i]["titolo"];
                        stato = articles[i]["stato"];
                        presenter = articles[i]["presenter"];
                        if(codicePresentazione === id) {
                            dynamicContent += `
                            <tr>
                                <td>${codicePresentazione}</td>
                                <td>${titolo}</td>
                                <td>${oraInizio}</td>
                                <td>${oraFine}</td>
                                <td>${presenter}</td>
                                <td>${stato}</td>
                            </tr>`;
                        }
                    }
                    
                    dynamicContent += `</tbody></table>`;
                }
                content.innerHTML = dynamicContent;
            }
        }

        function insertMessage() {
            content.textContent = '';
            var dynamicContent = '';
            dynamicContent = `
            <div class="container text-center">
            <p> Chat a cui hai accesso <p> 
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">Acronimo conferenza</th>
                            <th scope="col">Titolo sessione</th>
                            <th scope="col">Data</th>
                            <th scope="col">Ora inizio</th>
                            <th scope="col">Ora Fine</th>
                        </tr>
                    </thead>
                    <tbody> ` ;

            for(let i = 0; i < sessioniPermesse.length; i++) {
                acronimo = sessioniPermesse[i]["acronimoConferenza"];
                titoloSessione = sessioniPermesse[i]["titoloSessione"];
                oraInizio = sessioniPermesse[i]["oraInizio"];
                oraFine = sessioniPermesse[i]["oraFine"];
                data = sessioniPermesse[i]["data"];
                dynamicContent += `
                <tr>
                    <td>${acronimo}</td>
                    <td><form action="/DBProject2021/chat/chat.php" method="post" class="container">
                        <div class="mb-3 form-group floating">
                            <input type="hidden" class="form-control floating" name="acronimo" value=${acronimo} autocomplete="off">
                        </div>
                        <div class="mb-3 form-group floating">
                            <input type="hidden" class="form-control floating" name="titoloSessione" value=${titoloSessione} autocomplete="off">
                        </div>
                        <div class="container text-center">
                            <button class="btn btn-secondary" type="submit">${titoloSessione}</button></td>
                        </div>
                    </form> 
                    </td>   
                    <td>${data}</td>
                    <td>${oraInizio}</td>
                    <td>${oraFine}</td>
                </tr>`;
            }

            dynamicContent += `
                    </tbody>
                </table>
            </div>
            <p>I messaggi possono essere inseriti solo nell'orario indicato</p>`;

            content.innerHTML = dynamicContent;
        }

        function showFavorites() {
            content.innerHTML = '';
            var dynamicContent = `<p class="lead">Le tue presentazioni preferite</p><hr>
                                    <div class="container w-50">`;
            const ol = document.createElement("ol");
            ol.classList.add("list-group");
            ol.classList.add("list-group-numbered");

            if(favoritesArticoli.length === 0 && favoritesTutorial.length === 0) {
                dynamicContent += `<p>Lista vuota</p></div>`;
            }

            for(let i = 0; i < favoritesArticoli.length; i++) {
                var title = favoritesArticoli[i]["titolo"];
                var oraInizio = favoritesArticoli[i]["oraInizio"];
                var oraFine = favoritesArticoli[i]["oraFine"];
                var tipologia = favoritesArticoli[i]["tipologia"];
                var codice =  favoritesArticoli[i]["codicePresentazione"];

                // Si eliminano gli spazi per problemi se assegnato come id di un elemento
                var titleCompact = title.split(' ').join('');

                dynamicContent += ` 
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="container d-flex justify-content-between" >
                        <span>${title}</span>
                        <span><button class="btn btn-primary" data-toggle="modal" data-target="#${titleCompact}"><i class="bi bi-star-fill"></i></button></span>
                    </div>
                </li>
                <div id="${titleCompact}" class="modal fade">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                                <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                Sei sicuro di voler rimuovere <b>${title}</b> dai tuoi preferiti? 
                            </div>
                            <div class="modal-footer">
                                <form action="manageFavorites.php" method="post" class="container">
                                    <div class="mb-3 form-group floating">
                                        <input type="hidden" class="form-control floating" name="username" value=${userName} readonly>
                                    </div>
                                    <div class="mb-3 form-group floating">
                                        <input type="hidden" class="form-control floating" name="codice" value=${codice} autocomplete="off">
                                    </div>
                                    <div class="mb-3 form-group floating">
                                        <input type="hidden" class="form-control floating" name="manageOpt" value="false" autocomplete="off">
                                    </div>
                                    <div class="container text-center">
                                        <button type="submit" class="btn btn-danger">Rimuovi</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </form>                                    
                            </div>
                        </div>
                    </div>
                </div>`;
            }

            for(let i = 0; i < favoritesTutorial.length; i++) {
                var title = favoritesTutorial[i]["titolo"];
                var oraInizio = favoritesTutorial[i]["oraInizio"];
                var oraFine = favoritesTutorial[i]["oraFine"];
                var tipologia = favoritesTutorial[i]["tipologia"];
                var codice =  favoritesTutorial[i]["codicePresentazione"];

                // Si eliminano gli spazi per problemi se assegnato come id di un elemento
                var titleCompact = title.split(' ').join('');

                dynamicContent += ` 
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div class="container d-flex justify-content-between">
                        <span>${title}</span>
                        <span><button class="btn btn-primary" data-toggle="modal" data-target="#${titleCompact}"><i class="bi bi-star-fill"></i></button></span>
                    </div>  
                </li>
                <div id="${titleCompact}" class="modal fade">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                                <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="modal-body">
                                Sei sicuro di voler rimuovere <b>${title}</b> dai tuoi preferiti? 
                            </div>
                            <div class="modal-footer">
                                <form action="manageFavorites.php" method="post" class="container">
                                    <div class="mb-3 form-group floating">
                                        <input type="hidden" class="form-control floating" name="username" value=${userName} readonly>
                                    </div>
                                    <div class="mb-3 form-group floating">
                                        <input type="hidden" class="form-control floating" name="codice" value=${codice} autocomplete="off">
                                    </div>    
                                    <div class="container text-center">
                                        <button type="submit" class="btn btn-danger">Remove</button>
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </form>                                    
                            </div>
                        </div>
                    </div>
                </div>`;
            }
            
            dynamicContent += `</div>`;
            ol.innerHTML = dynamicContent;
            content.append(ol);
            
        }

        // switch per il menu
        var radio = 0;
        document.getElementById("sidebarCollapse").addEventListener("click", () => {
            if(radio === 0) {
                document.getElementById("sidebar").classList.add("active");
                radio = 1;
            } else {
                document.getElementById("sidebar").classList.remove("active");
                radio = 0;
            }
        });

    </script>

    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous"></script>

</body>
</html>