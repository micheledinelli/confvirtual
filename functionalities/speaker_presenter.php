<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/base.css">
    <link rel="stylesheet" href="../css/form.css">

    <title>S-P</title>
</head>
<body>

    <?php
        session_start();
        if(time() > $_SESSION['expire']) {
            session_unset();
            session_destroy();
            header('Location:../landingPage/index.php');
        } 
        
        try{
            // Connection to db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');

            if($_SESSION["userType"] == "SPEAKER") {
                $query = 'SELECT CurriculumVitae FROM SPEAKER WHERE Username = :lab1';
            
            } elseif($_SESSION["userType"] == "PRESENTER") {
                $query = 'SELECT CurriculumVitae FROM PRESENTER WHERE Username = :lab1';
            }
            
            $res = $pdo -> prepare($query);
            $res -> bindValue(":lab1", $_SESSION["user"]);
            $res -> execute();

            while($row = $res -> fetch()) {
                $userCurriculum = $row["CurriculumVitae"];
            }

            $query = 'SELECT * FROM UNIVERSITA';
            $res = $pdo -> prepare($query);            
            $res -> execute();

            $università = array(); 
            while($row = $res -> fetch()) {
               $uni = new StdClass();
               $uni -> nome = $row["NomeUniversità"];
               $uni -> dipartimento = $row["NomeDipartimento"];
               array_push($università, $uni);
            }

            if($_SESSION['userType'] == "SPEAKER") {
                $queryUniAttuale = 'SELECT * FROM SPEAKER AS S WHERE S.Username = :lab1';
            } else {
                $queryUniAttuale = 'SELECT * FROM  PRESENTER AS P WHERE P.Username = :lab1';
            }
            
            $res = $pdo -> prepare($queryUniAttuale);
            $res -> bindValue(":lab1", $_SESSION["user"]);
            $res -> execute();
            while($row = $res -> fetch()) {
                $uniAttuale = $row["NomeUniversità"];
                $dipAttuale = $row["NomeDipartimento"];
            }
            
            $queryTutorialPermessi = 'SELECT * 
                                    FROM SPEAKER_TUTORIAL AS T, P_TUTORIAL AS PT
                                    WHERE UsernameSpeaker = :lab1 AND T.CodiceTutorial = PT.CodicePresentazione;';
            $res = $pdo -> prepare($queryTutorialPermessi);
            $res -> bindValue(":lab1", $_SESSION["user"]);
            $res -> execute();

            $tutorialsPermitted = array();
            while($row = $res -> fetch()) {
                $tutorial = new StdClass();
                $tutorial -> titolo = $row["Titolo"];
                $tutorial -> codice = $row["CodiceTutorial"];
                array_push($tutorialsPermitted, $tutorial);
            }

            $queyRisorse = 'SELECT * 
                        FROM RISORSA, P_TUTORIAL AS PT 
                        WHERE UsernameSpeaker=:lab1 AND RISORSA.CodiceTutorial = PT.CodicePresentazione;';
            
            $res = $pdo -> prepare($queyRisorse);
            $res -> bindValue(":lab1", $_SESSION["user"]);
            $res -> execute();

            $risorse = array();
            while($row = $res -> fetch()) {
                $risorsa = new StdClass();
                $risorsa -> link = $row["Link"];
                $risorsa -> descrizione = $row["Descrizione"];
                $risorsa -> titoloTutorial = $row["Titolo"];
                $risorsa -> codiceTutorial = $row["CodiceTutorial"];
                array_push($risorse, $risorsa);
            }

        }catch(PDOException $e) {
            // Errore
            $_SESSION["error"] = 1;

            print "Error!: " . $e->getMessage() . "<br/>";
            die();
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
                
                <li> <a href="#" onclick="insertCV()">Inserisci CV</a></li>
                
                <li> <a href="#" onclick="moodifyCV()">Modifica CV</a> </li>
            
                <li> <a href="#" onclick="insertPhoto()">Inserisci/Modifica Foto</a> </li>

                <li> <a href="#" onclick="manageUni()">Affilizazione universitaria</a> </li>
                
                <!-- Funzionalità aggiuntive per gli speaker -->
                <?php
                    session_start();
                    if($_SESSION['userType'] == "SPEAKER") {
                       print'
                            <li> <a href="#" onclick="insertResource()">Inserisci Risorsa</a> </li>

                            <li> <a href="#" onclick="modifyResource()">Modifica Risorsa</a> </li>';
                    }
                ?>
                        
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
                <strong>Ottimo!</strong> Operazione andata buon fine!
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
    <script>
        
        const userName = <?php echo json_encode($_SESSION["user"]); ?>

        const content = document.getElementById("main-content");
        const userCurriculum = <?php echo json_encode($userCurriculum); ?>;
        const università = <?php echo json_encode($università); ?>;
        const uniAttuale = <?php echo json_encode($uniAttuale); ?>;
        const dipAttuale = <?php echo json_encode($dipAttuale); ?>;
        const tutorialPermessi = <?php echo json_encode($tutorialsPermitted); ?>;
        const risorse = <?php echo json_encode($risorse); ?>;

        function insertCV() {
            content.innerHTML = `
                <div class="container text-center w-50">
                    <h2>Inserisci il tuo CV</h2>
                    <hr class="my-4">
                    <p id="charNum">0</p>
                    <form action="insertCV.php" method="post">
                        <div class="form-floating">
                            <textarea name="cv" class="form-control" placeholder="Scrivi il tuo cv" style="height: 300px;" onkeyup="countChars(this);"></textarea>
                        </div>
                        <div class="container text-center my-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            `;
        }

        function insertPhoto() {
            content.innerHTML = `
                <div class="container w-50">
                    <h2>Inserisci la tua foto</h2>
                    <hr class="my-4">
                    <div class="mb-3">
                        <form action="insertPhoto.php" method="post">

                            <div class="mb-3 form-group floating">
                                <input type="button" class="btn btn-primary" id="photo" value="carica la tua foto" onclick="document.getElementById('hidden-photo-input').click();" />
                                <input type="file" style="display:none;" id="hidden-photo-input" name="photo"/>                    
                            </div>

                            <div class="container text-center my-5">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;

            const hiddenInput = document.getElementById('hidden-photo-input');
            hiddenInput.addEventListener("change", function() {
                alert("Foto aggiunta con successo");
             });
        }

        function moodifyCV() { 
            if(userCurriculum) {
                content.innerHTML = `
                <div class="container text-center w-50">
                    <h2>Modifica il tuo CV</h2>
                    <p>Abbiamo caricato il tuo cv dall'ultimo edit</p>
                    <hr class="my-4">
                    <p id="charNum"></p>
                    <form action="insertCV.php" method="post">
                        <div class="form-floating">
                        <textarea name="cv" class="form-control" placeholder="Scrivi il tuo cv" style="height: 300px;" onkeyup="countChars(this);">${userCurriculum}</textarea>
                        </div>
                        <div class="container text-center my-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>`;
            } else {
                content.innerHTML = `
                <div class="container text-center w-50">
                    <h2>Modifica il tuo CV</h2>
                    <hr class="my-4">
                    <p id="charNum">0</p>
                    <form action="insertCV.php" method="post">
                        <div class="form-floating">
                            <textarea name="cv" class="form-control" placeholder="Scrivi il tuo cv" style="height: 100px" onkeyup="countChars(this);"></textarea>
                        </div>
                        <div class="container text-center my-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            `;
            }
        }

        function manageUni() {
            content.innerHTML = `
            <div class = "container w-50">
                <h2>Università</h2>
                <p>Abbiamo caricato le università ed i relativi dipartimenti nel nostro database,
                se non appartieni ad una di queste <a class="link-primary" href="../landingPage/index.php#contact-us">informaci</a>!</p>
                <input class="form-control" id="demo" type="text" placeholder="Search here...">
                <br>
                <ul class="list-group" id="uniList">
                    
                </ul>
            </div>`;
            var list = '';
            var ul = document.getElementById("uniList");

            for(let i = 0; i < università.length; i++) {
                nomeUni = università[i]["nome"];
                dipartimento = università[i]["dipartimento"];
                if((uniAttuale && nomeUni === uniAttuale) && (dipAttuale && dipAttuale == dipartimento)) {
                    attuale = ` 
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-check-lg" viewBox="0 0 16 16">
                        <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                    </svg>`
                } else {
                    attuale = '';
                }
                list += `<li id="${nomeUni} | ${dipartimento}" class="list-group-item"><a href="#" data-toggle="modal" data-target="#${nomeUni}${dipartimento}">${nomeUni} | ${dipartimento} ${attuale}</a></li>
                    <div id="${nomeUni}${dipartimento}" class="modal fade">
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
                                    <p>Stai dichiarando di essere un membro di ${nomeUni}.<p>
                                    <p>Puoi modificare la tua affiliazione in qualsiasi momento</p>
                                </div>
                                <div class="modal-footer">
                                    <form method="post" action="insertUniAffiliation.php">
                                        <input type="hidden" class="form-control floating" name="uni" value=${nomeUni} autocomplete="off">
                                        <input type="hidden" class="form-control floating" name="dip" value=${dipartimento} autocomplete="off">
                                        <button class="btn btn-primary" type="submit">Procedi</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>`;
            }

            ul.innerHTML = list;

                $(document).ready(function(){
                    $("#demo").on("keyup", function() {
                        var value = $(this).val().toLowerCase();
                        $("#uniList li").filter(function() {
                            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
            });
        }
        
        function countChars(obj){
            document.getElementById("charNum").innerHTML = obj.value.length + ' / 300';
        }

        function insertResource() {
            var currentSelection;

            var dynamicContent = '';
            dynamicContent = `
                <p class="lead">Seleziona il tutorial per il quale inserirai la risorsa</p>
                <select id="tutorial-select" class="form-select form-select-lg mb-3" aria-label="select" onchange=produceForm(this.value)>
                <option value="-1" selected>Tutorial ai quali hai accesso</option>`;

            for(let i = 0; i < tutorialPermessi.length; i++) {
                titolo = tutorialPermessi[i]["titolo"];
                codice = tutorialPermessi[i]["codice"];
                dynamicContent += `<option value=${codice}>${titolo}</option>`;
            }

            dynamicContent  += `</select>`;
            content.innerHTML = dynamicContent;

        }

        function produceForm(codiceTutorial) {
            if(document.getElementById("dynamic-div")) {
                document.getElementById("dynamic-div").remove();
            }

            var mySelect = document.getElementById("tutorial-select");
            var titolo = mySelect.options[mySelect.selectedIndex].text;
            const div = document.createElement("div");
            div.classList.add("container");
            div.classList.add("my-3");
            div.setAttribute("id", "dynamic-div")

            // Si evita il caso della selzione default
            if(codiceTutorial !== "-1") {
                div.innerHTML = `
                <div class="container-fluid text-center w-50">
                    <p>Risorsa per ${titolo}</p>
                    <form action="manageResource.php" method="post" class="container my-5" id="resource-form">
                        <div class="mb-3 form-group floating">
                            <input type="text" class="form-control floating" name="link" required autocomplete="off" required>
                            <label for="nomeConferenza">Link della risorsa</label>          
                        </div>
                        <div class="form-floating">
                            <textarea name="descrizione" form="resource-form" class="form-control" placeholder="Inserisci una breve descrizione" style="height: 200px; required"></textarea>
                        </div>
                        <input type="hidden" name="username" value="${userName}">
                        <input type="hidden" name="codiceTutorial" value="${codiceTutorial}">
                        <input type="hidden" name="resourceAddOpt" value="add">
                        <div class="container text-center my-5">
                            <button type="submit" class="btn btn-primary">Inserisci</button>
                        </div>
                    </form>
                </div>
                `;
                content.append(div);
            }
        }

        function modifyResource() {
            content.innerHTML = '';
            const div = document.createElement('div');
            div.classList.add('row');
            var cardContent = "";
            
            for(let i = 0; i < risorse.length; i++) {
                title = risorse[i]["titoloTutorial"];
                link = risorse[i]["link"];
                descrizione = risorse[i]["descrizione"];
                codiceTutorial = risorse[i]["codiceTutorial"];
                var artificialId = "";
                artificialId = title.split(" ").join("") + codiceTutorial;

                cardContent += `
                <div class="card" style="width: 18rem; margin: 5px 5px">
                    <div class="card-body">
                        <h5 class="card-title">${title}</h5>
                        <p class="card-text">${link}</p>
                        <p class="small">${descrizione}</p>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#${artificialId}">Modifica</button>
                    </div>
                </div>
                <div id="${artificialId}" class="modal fade">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Modifica ${title}</h5>
                                    <button type="button" class="btn" data-dismiss="modal" aria-label="Close">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
                                            <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form action="manageResource.php" method="post" class="container">
                                        <div class="mb-3 form-group floating">
                                            <input type="hidden" class="form-control floating" name="username" value=${userName} readonly>
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="hidden" class="form-control floating" name="codiceTutorial" value=${codiceTutorial} readonly>
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <input type="text" class="form-control floating" name="link" placeholder="${link}" required>
                                        </div>
                                        <div class="mb-3 form-group floating">
                                            <textarea name="descrizione" class="form-control" placeholder="Edita la descrizione: ${descrizione}" style="height: 200px;" required></textarea>
                                        </div>
                                        <input type="hidden" name="resourceAddOpt" value="modify">
                                        <div class="container text-center">
                                            <button type="submit" class="btn btn-primary">Modifica</button>
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </form>
                                    <form method="post" action="deleteResource.php">
                                        <div class="container my-3 text-center">
                                            <input type="hidden" name="codiceTutorial" value=${codiceTutorial}>
                                            <input type="hidden" name="link" value=${link}>
                                            <input type="hidden" name="username" value=${userName}>
                                            <button type="submit" class="btn btn-danger">Elimina</button>
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

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous"></script>

</body>
</html>