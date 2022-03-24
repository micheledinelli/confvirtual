<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.plot.ly/plotly-2.8.3.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
    <title>Clustering</title>
</head>
<body>
    <?php
        session_start();
        if(time() > $_SESSION['expire']) {
            session_unset();
            session_destroy();
            header('Location:/DBProject2021/landingPage/index.php');
        } 

        try{
            // Connection to db
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user = 'root', $pass = 'root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');

            $query = '  SELECT COUNT(*) AS NumeroIscrizioni, U.Username AS Username, Tipologia, TIMESTAMPDIFF(YEAR,DataNascita,CURDATE()) AS Eta
                        FROM REGISTRAZIONE AS R, UTENTE AS U
                        WHERE U.Username = R.Username AND Tipologia <> "ADMIN"
                        GROUP BY U.Username';
             
            $res = $pdo -> prepare($query);
            $res -> execute();
            $dataset = array(); 

            while($row = $res -> fetch())  {
                $user = new StdClass();
                $user -> numeroIscrizioni = $row["NumeroIscrizioni"];
                $user -> username = $row["Username"];
                $user -> tipologia = $row["Tipologia"];
                $user -> eta = $row["Eta"];
                array_push($dataset, $user);
            }
           
        } catch( PDOException $e ) {
            header('Location:index.php');
            echo("[ERRORE]".$e->getMessage());
            exit();
        }
    ?>
    <div class="container p-3">
        <div class="container my-2">
            <a class="btn btn-primary" href="../functionalities/admin.php" role="button">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                    <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
                </svg>
            </a>
        </div>

        <div class="container my-5 text-center">
            <h1 class="display-6">Clustering w/ KMeans Algorithm</h1>
            <hr class="my-4">
            <div class="btn-group">
                <button class="btn-secondary btn" id="2d">2D</button>
                <button class="btn-secondary btn" id="3d">3D</button>
                <select class="form-select" id="select-clusters" aria-label="Default select example">
                    <option selected>Clusters</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                    <option value="7">7</option>
                    <option value="8">8</option>
                    <option value="9">9</option>
                </select>    
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-around">
            <div class="col-2">
               <div id="stats">

               </div>
            </div>
            <div class="col-8" id="root";>
            
            </div>
        </div>
    </div>

    <div id="clusters" class="container text-center my-5">
        
    </div>

    <script src="kmeansV1.js"></script>
    <script src="http://d3js.org/d3.v3.min.js" charset="utf-8"></script>
    <script>

        const statsDiv = document.getElementById("stats");
        const clusters = document.getElementById("select-clusters");
        const rootContent = document.getElementById("root");
        const clustersDiv = document.getElementById("clusters");

        const dataFromSystem = <?php echo json_encode($dataset); ?>
        
        var stats = '';
        var numberOfClusters = 2; // deafault
        
        document.addEventListener("change", () => {
            numberOfClusters = clusters.value;
        })

        document.getElementById("2d").addEventListener("click", () => {
            //var dataSet = preProcessData(dataFromSystem);
            stats = start(2, numberOfClusters);
            produceStats(stats);
            //printClusters();
        });

        document.getElementById("3d").addEventListener("click", () => {
            //var dataSet = preProcessData(dataFromSystem);
            stats = start(3, numberOfClusters);
            produceStats(stats);
            //printClusters();
        });


        function produceStats(stats) {
            
            var dynamicContent = `
                <h3>Stats</h3>
                <hr>`;
            
            /*dynamicContent += `<p class="text-left">Coordinate dei centroidi:</p>`;

            for(let i = 0; i < stats.clusters.length; i++) {
                centroid = stats.clusters[i].
                centroid = centroid.map(function(elem){
                    return Number(elem).toFixed(2);
                });

                dynamicContent += `<p class="text-left">[${centroid}]</p>`;
            }*/

            dynamicContent += `<p class="text-left text-muted">k: ${stats.k}</p>`;
            dynamicContent += `<p class="text-left text-muted">Convergenza: ${stats.converged}</p>`;
            dynamicContent += `<p class="text-left text-muted">Iterazioni: ${stats.iterations}</p>`;
            dynamicContent += `<p class="text-left text-muted">Errore Soglia: ${stats.threshold}</p>`;
            dynamicContent += `<p class="text-left text-muted">MSE: ${stats.mse}</p>`; 

            statsDiv.innerHTML = dynamicContent;
        }

        /*
        function printClusters() {
            var dynamicContent = '';
            dynamicContent += `
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Visualizza Clusters Utenti
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                            <div class="accordion-body">`;
            for(let i = 0; i < dataFromSystem.length; i++) {
                username = dataFromSystem[i]["username"];
                centroid = mapOfUsers.get(username);
                dynamicContent += `<p class="lead">${username} è stato asseganto al cluster con centoride in ${centroid}</p>`;
                
            }
            
            dynamicContent += ` 
                        </div>
                    </div>
                </div>
            </div>`;
           
            clustersDiv.innerHTML = dynamicContent;
        }*/

    </script>
</body>
</html>