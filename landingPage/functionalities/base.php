<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <link rel="stylesheet" href="/DBProject2021/landingPage/css/form.css">
    <link rel="stylesheet" href="/DBProject2021/landingPage/css/base.css">
    <title>Document</title>
</head>
<body>

    <?php
        // Connection to db
        $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'Squidy.77');
        $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo -> exec('SET NAMES "utf8"');

        $query = ('SELECT * FROM CONFERENZA WHERE Svolgimento = "ATTIVA"');

        $res = $pdo -> prepare($query);
        $res -> execute();
        $conferenze = array();

        while($row = $res -> fetch()) {
            $conferenza = new stdClass();
            $conferenza -> acronimo = $row["Acronimo"];
            $conferenza -> annoEdizione = $row["AnnoEdizione"];
            array_push($conferenze, $conferenza);
        }    
    ?>

    <div class="wrapper">
        <nav id="sidebar" class="vh-100 bg-primary">
            <div class="sidebar-header">
                <h3><a href="/DBProject2021/landingPage/">Home</a></h3>
                <hr>
            </div>
            <ul class="list-unstyled components">
                <li> 
                    <a href="#" onclick="visualize()">Visualizza conferenze</a>
                </li>
                <li> <a href="#" onclick="registerToConference()">Registrati ad una conferenza</a> </li>
            
                <li> <a href="#" onclick="">Inserisci messaggio</a> </li>

                <li> <a href="#" onclick="">Visualizza chat di sessione</a> </li>
                
                <li> <a href="#pageSubmenu" data-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Favorites</a>
                    <ul class="collapse list-unstyled" id="pageSubmenu">
                        <li> <a href="#">New favorite</a> </li>
                        <li> <a href="#">My favorties</a> </li>
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
                <strong>Ottimo!</strong> Sei registrato!
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

    <script>
        
        const content = document.getElementById("main-content");
        const userName = <?php echo json_encode($_SESSION["user"]); ?>
        
        // Array di conferenze preso dalla query in php
        var conferenze = <?php echo json_encode($conferenze); ?>
        
        function visualize() {
            content.textContent = '';
            const ul = document.createElement("ul");
            var text = `<h2 class="my-4">Lista delle conferenze</h2>`;
            for(let i = 0; i < conferenze.length; i++) {
                conf = conferenze[i]["acronimo"]
                text += `<li class="list-group-item"> Acronimo: ${conf} </li> <br>`;
            }
            
            ul.innerHTML = text;
            ul.classList.add("list-group-flush");
            content.append(ul);
        }

        function registerToConference() {
            content.innerHTML = `
            <div class="container-fluid text-center">
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