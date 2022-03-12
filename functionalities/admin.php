<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <link rel="stylesheet" href="/DBProject2021/css/form.css">
    <link rel="stylesheet" href="/DBProject2021/css/base.css">
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

        //creating query for active conferences
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

        //creating quey for sessions
        $querySessions = ('SELECT * FROM SESSIONE');
        
        $res = $pdo -> prepare($querySessions);
        $res -> execute();
        $sessioni = array();

        while($row = $res -> fetch()) {
            $sessione = new stdClass();
            $sessione -> acronimoConferenza = $row["AcronimoConferenza"];
            $sessione -> titoloSessione = $row["Titolo"];
            $sessione -> codiceSessione = $row["Codice"];
            $sessione -> numPresentazioni = $row["NumeroPresentazioni"];
            array_push($sessioni, $sessione);
        }

    ?>

    <div class="wrapper">
        <nav id="sidebar" class="vh-100 bg-primary">
            <div class="sidebar-header">
                <a class="btn btn-primary" href="/DBProject2021/functionalities/base.php">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-bar-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M12.5 15a.5.5 0 0 1-.5-.5v-13a.5.5 0 0 1 1 0v13a.5.5 0 0 1-.5.5zM10 8a.5.5 0 0 1-.5.5H3.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L3.707 7.5H9.5a.5.5 0 0 1 .5.5z"/>
                    </svg>
                    Go back
                </a>
                <hr>
            </div>
            <ul class="list-unstyled components">
                <li> 
                    <a href="#" onclick="createConference()">Crea Conferenza</a>
                </li>
                <li> 
                    <a href="#" onclick="createSession()">Crea Sessione</a>
                </li>
            
                <li> <a href="#" onclick="CreatePresentation()">Inserisci presentazioni</a> </li>

                <li> <a href="#" onclick="">Associa speaker</a> </li>
                
                <li> <a href="#" onclick="">Associa presenter</a> </li>

                <li> <a href="#" onclick="addSponsor()">Inserisci Sponsor</a> </li>
                
                <li> <a href="/DBProject2021/clustering/cluster.php">Viusalizza cluster utenti</a> </li>
                                
                <li> <a href="#pageSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Valutazioni</a>
                    <ul class="collapse list-unstyled" id="pageSubmenu">
                        <li><a href="#">Inserisci valutazione</a></li>
                        <li><a href="#">Visualizza valutazioni</a></li>
                    </ul>
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

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous"></script>
    
     <!-- JAVASCRIPT -->
    <script>

        const content = document.getElementById("main-content");

        // Array di conferenze e sessioni presi tradotti da php
        var conferenze = <?php echo json_encode($conferenze); ?>;
        var sessioni = <?php echo json_encode($sessioni); ?>;
        const userName = <?php echo json_encode($_SESSION["user"]); ?>

        function createConference() {
            content.innerHTML = `
            <div class="container-fluid text-center w-50">
                <h2>Registrati</h2>
                <hr class="my-4">
                <form action="createConferenceAdmin.php" method="post" class="container my-5">
                    <div class="mb-3 form-group floating">
                        <input type="text" class="form-control floating" name="nomeConferenza" required autocomplete="off">
                        <label for="nomeConferenza">Nome della Conferenza</label>          
                    </div>
                    <div class="mb-3 form-group floating">
                        <input type="text" class="form-control floating" name="acronimo" required autocomplete="off">
                        <label for="acronimo">Acronimo della Conferenza</label> 
                    </div>
                    <div class="mb-3 form-group floating">
                        <input type="number" class="form-control floating" name="annoEdizione" required autocomplete="off">
                        <label for="annoEdizione">Anno Edizione</label>          
                    </div>
                    <div class="container text-center my-5">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
            `;
        }

        //creazione sessione
        function createSession() {
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
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#${acr}">Crea sessione</button>
                        </div>
                    </div>

                    <div id="${acr}" class="modal fade">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Sessione</h5>
                                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Sei sicuro di voler aggiungere una sessione a ${title}? 
                                </div>
                                <div class="modal-footer">
                                    <form action="createSession.php" method="post" class="container my-5">
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="acronimo" required autocomplete="off" readonly value=${acr}>
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="" class="form-control floating" name="annoEdizione" required autocomplete="off" readonly value=${anno}>        
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="titoloSessione" required autocomplete="off">
                                            <label style="margin:0" for="titoloSessione">Titolo della sessione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="date" class="form-control floating" name="dataSessione" required autocomplete="off">
                                            <label style="margin:0" for="dataSessione">Data della sessione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="time" class="form-control floating" name="oraInizio" required autocomplete="off">
                                            <label style="margin:0" for="oraInizio">Ora di inizio sessione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="time" class="form-control floating" name="oraFine" required autocomplete="off">
                                            <label style="margin:0" for="oraFine">Ora di fine sessione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="linkSessione" required autocomplete="off">
                                            <label style="margin:0" for="linkSessione">Link della sessione</label>          
                                        </div>
                                        <div class="container text-center my-5">
                                            <button type="submit" class="btn btn-primary">Crea sessione</button>
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

        function CreatePresentation() {
            
            content.textContent = '';
            var dynamicContent = `                
            <table class="table table-striped">
                <thead>
                <p> Sessioni disponibili <p> 
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Conferenza</th>
                        <th scope="col">Titolo Sessione</th>
                        <th scope="col">Numero di Presentazioni</th>
                        <th scope="col">Aggiungi Articolo</th>
                        <th scope="col">Aggiungi Tutorial</th>
                    </tr>
                </thead>
                <tbody>`;
            
            
            for(let i = 0; i < sessioni.length; i++) {
                acr = sessioni[i]["acronimoConferenza"];
                titleSession = sessioni[i]["titoloSessione"];
                codiceSessione = sessioni[i]["codiceSessione"];
                numPresentazioni = sessioni[i]["numPresentazioni"];
                dynamicContent += `
                <tr>
                    <th scope="row">${i+1}</th>                    
                        <td>${acr}</td>
                        <td>${titleSession}</td>
                        <td>${numPresentazioni}</td>
                        <td><button type="text" class="btn btn-primary" data-toggle="modal" data-target="#${acr}${titleSession}" >+ </button></td> 
                        <td><button type="text" class="btn btn-primary" data-toggle="modal" data-target="#${titleSession}${acr}" >+ </button></td> 
                    
                    <!-- creazione articolo -->
                    <div id="${acr}${titleSession}" class="modal fade">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Presentazione</h5>
                                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Sei sicuro di voler aggiungere la presentazione di un articolo a ${titleSession}? 
                                    <form action="createPresentation.php" method="post" class="container my-5">
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="codiceSessione" required autocomplete="off" readonly value=${codiceSessione}>
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="titoloSessione" required autocomplete="off" readonly value=${titleSession}>
                                            <label for="titoloSessione">Titolo della sessione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="titolo" required autocomplete="off">
                                            <label for="titolo">Titolo dell'articolo</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="time" class="form-control floating" name="oraInizio" required autocomplete="off">
                                            <label for="oraInizio">Ora inizio presentazione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="time" class="form-control floating" name="oraFine" required autocomplete="off">
                                            <label for="oraFine">Ora fine presentazione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="number" class="form-control floating" name="numeroPagine" required autocomplete="off">
                                            <label for="numeroPagine">numero pagine</label>          
                                        </div>
                                        <div class="container text-center my-5">
                                            <button type="submit" class="btn btn-primary">Crea articolo</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- creazione presentazione -->
                    <div id="${titleSession}${acr}" class="modal fade">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Presentazione</h5>
                                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    Sei sicuro di voler aggiungere la presentazione di un tutorial a ${titleSession}? 
                                    <form action="createPresentation.php" method="post" class="container my-5">
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="codiceSessione" required autocomplete="off" readonly value=${codiceSessione}>
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="titoloSessione" required autocomplete="off" readonly value=${titleSession}>
                                            <label for="titoloSessione">Titolo della sessione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="titolo" required autocomplete="off">
                                            <label for="titolo">Titolo della presentazione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="time" class="form-control floating" name="oraInizio" required autocomplete="off">
                                            <label for="oraInizio">Ora inizio presentazione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="time" class="form-control floating" name="oraFine" required autocomplete="off">
                                            <label for="oraFine">Ora fine presentazione</label>          
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <textarea type="text" style="height:100px;" placeholder="inserisci il tuo abstract" class="form-control" name="abstract" required autocomplete="off"></textarea>
                                                    
                                        </div>
                                        <div class="container text-center my-5">
                                            <button type="submit" class="btn btn-primary">Crea tutorial</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="modal-footer">
                                </div>
                            </div>
                        </div>
                    </div>
                </tr>`;
            }
            
            //dynamicContent += `</tbody></table>`;
            content.innerHTML = dynamicContent;

        }

        function addSponsor() {
            content.innerHTML = `
            <div class="container-fluid text-center w-50">
                <h2>Inserisci uno sponsor</h2>
                <hr class="my-4">
                <form id="sponsor-form" action="insertSponsor.php" method="post" class="container my-3">
                    
                    <!-- Form content -->
                    <div class="container" id="form-content">
                        <div class="mb-3 form-group floating">
                            <input type="text" class="form-control floating" name="nomeSponsor" required autocomplete="off">
                            <label for="nomeSponsor">Nome dello sponsor</label>          
                        </div>
                    </div>
                    <div class="mb-3 form-group floating">
                        <input type="button" class="btn btn-primary" id="logo-input" value="carica il logo" onclick="document.getElementById('hidden-logo-input').click();" />
                        <input type="file" style="display:none;" id="hidden-logo-input" name="logo"/>                    
                    </div>
                    
                    <!-- Submit -->
                    <div class="container text-center my-5">
                        <div class="row">
                            <div class="col">
                                <a id="expand-btn" role="btn" class="btn btn-primary" data-toggle="tooltip" data-placement="bottom" title="Espandi per aggiungere anche la sponsorizzazione">espandi</a>
                            </div>
                            <div class="col">
                                <button type="submit" id="my-btn" class="btn btn-primary">Inserisci</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            `;

            const formContent = document.getElementById("form-content");
            const expandBtn =  document.getElementById("expand-btn");
            const hiddenInput = document.getElementById('hidden-logo-input');

            var tooltip = new bootstrap.Tooltip(expandBtn);
            
            hiddenInput.addEventListener("change", function() {
                alert("Logo aggiunto con successo");
            });

            var radio = 1;
            expandBtn.addEventListener("click", function() {
                
                if(radio === 1) {
                    formContent.innerHTML += `
                        <div class="mb-3 form-group floating">
                            <input type="text" class="form-control floating" name="acronimoConferenza" autocomplete="off">
                            <label for="acronimoConferenza">Acronimo della conferenza</label>          
                        </div>
                        <div class="mb-3 form-group floating">
                            <input type="number" min="1990" class="form-control floating" name="annoEdizione" autocomplete="off">
                            <label for="annoEdizione">Anno Edizione</label>          
                        </div>
                        <div class="mb-3 form-group floating">
                            <input type="number" min="0" max="99999999" class="form-control floating" name="importo" autocomplete="off">
                            <label for="importo">Importo</label>          
                        </div>`;
                        expandBtn.textContent = "riduci";
                        expandBtn.setAttribute("data-original-title", "Riduci");
                        
                } else {
                    formContent.innerHTML = `
                    <div class="mb-3 form-group floating">
                        <input type="text" class="form-control floating" name="nomeSponsor" autocomplete="off">
                        <label for="nomeSponsor">Nome dello sponsor</label>          
                    </div>`;
                    expandBtn.textContent = "espandi";
                    expandBtn.setAttribute("data-original-title", "Espandi per aggiungere anche la sponsorizzazione");
                } 

                radio = -radio;

            })
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

</body>
</html>