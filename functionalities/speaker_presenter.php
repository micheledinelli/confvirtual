<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <link rel="stylesheet" href="/DBProject2021/css/base.css">
    <title>S-P</title>
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

    ?>

    <div class="wrapper">
        <nav id="sidebar" class="vh-100 bg-primary">
            <div class="sidebar-header">
                <a class="btn btn-primary" href="base.php">
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
            
                <li> <a href="#" onclick="insertPhoto()">Inserisci Foto</a> </li>

                <li> <a href="#" onclick="modifyPhoto()">Modifica Foto</a> </li>
                
                <!-- Funzionalità aggiuntive per gli speaker -->
                <?php
                    session_start();
                    if($_SESSION['userType'] == "SPEAKER") {
                       print'
                            <li> <a href="#" onclick="">Inserisci Risorsa</a> </li>

                            <li> <a href="#" onclick="">Modifica Risorsa</a> </li>';
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
        
        const content = document.getElementById("main-content");
        
        function insertCV() {
            content.innerHTML = `
                <h2>Inserisci il tuo CV</h2>
                <hr class="my-4">
                <div class="container text-center">
                    <form action="insertCV.php" method="post">
                        <input type="file" name="fileCV">
                        <div class="container text-center my-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            `;
        }

        function insertPhoto() {
            content.innerHTML = `
                <h2>Inserisci la tua foto</h2>
                <hr class="my-4">
                <div class="mb-3">
                    <form action="insertPhoto.php" method="post">
                        <input type="file" name="photo">
                        <div class="container text-center my-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            `;
        }

        function moodifyCV() {
            content.innerHTML = `
                <h2>Modifica il tuo CV</h2>
                <hr class="my-4">
                <div class="container text-center">
                    <form action="insertCV.php" method="post">
                        <input type="file" name="fileCV">
                        <div class="container text-center my-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            `;
        }

        function modifyPhoto() {
            content.innerHTML = `
                <h2>Modfica la tua foto</h2>
                <hr class="my-4">
                <div class="mb-3">
                    <form action="insertPhoto.php" method="post">
                        <input type="file" name="photo">
                        <div class="container text-center my-5">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            `;
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