<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css" integrity="sha384-zCbKRCUGaJDkqS1kPbPd7TveP5iyJE0EjAuZQTgFLD2ylzuqKfdKlfG/eSrtxUkn" crossorigin="anonymous">
    <title>Chat</title>
</head>

<body>

    <?php
        session_start();
        
        if(time() > $_SESSION['expire']) {
            session_unset();
            session_destroy();
            header('Location:/DBProject2021/landingPage/index.php');
        }

        try {
            
            $pdo = new PDO('mysql:host=localhost;dbname=CONFVIRTUAL', $user ='root', $pass='root');
            $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo -> exec('SET NAMES "utf8"');
            
            //Connection to MongoDB
            require '../vendor/autoload.php';
            $conn = new MongoDB\Client("mongodb://localhost:27017");
            $collection = $conn -> CONFVIRTUAL_log -> log;	

            $queryMessages = ('SELECT *
                            FROM REGISTRAZIONE AS R, SESSIONE AS S
                            WHERE R.AcronimoConferenza = S.AcronimoConferenza AND Username = :lab1');
        
            $res = $pdo -> prepare($queryMessages);
            $res -> bindValue(":lab1", $_SESSION['user']);
            $res -> execute();

            $tables = array("REGISTRAZIONE", "SESSIONE");
            $insertOneResult = $collection->insertOne([
                'TimeStamp' 		=> time(),
                'User'				=> $_SESSION['user'],
                'OperationType'		=> 'SELECT',
                'InvolvedTable'	    => $tables
            ]);

            $sessionsPermitted = array();
            while($row = $res -> fetch()) {
                $sessione = new stdClass();
                $sessione -> acronimoConferenza = $row["AcronimoConferenza"];
                $sessione -> codiceSessione = $row["Codice"];
                $sessione -> titoloSessione = $row["Titolo"];
                $sessione -> data = $row["Data"];
                $sessione -> oraInizio = $row["OraInizio"];
                $sessione -> oraFine = $row["OraFine"];
                array_push($sessionsPermitted, $sessione);
            }

            $messaggiSessioniPermesse = array();
            foreach($sessionsPermitted as $i => $i_value) {
                $messaggi = array();
                $curCodice =  $i_value -> codiceSessione;
                $query = 'SELECT * FROM MESSAGGIO WHERE ChatID = :lab1 ORDER BY Ts';
                $res = $pdo -> prepare($query);
                $res -> bindValue(":lab1", $curCodice);
                $res -> execute();
                
                while($row = $res -> fetch()) {
                    $messaggio = new stdClass();
                    $messaggio -> testo = $row["Testo"];
                    $messaggio -> ts = $row["Ts"];
                    $messaggio -> mittente = $row["UsernameMittente"];
                    array_push($messaggi, $messaggio);
                }
                $messaggiSessioniPermesse[$curCodice] = $messaggi;            
            }

        } catch( PDOException $e ) {
            echo("[ERRORE]".$e->getMessage());
            exit();
        }
        
    ?>

    <?php
        if(isset($_SESSION["chatError"])) {
            if($_SESSION["chatError"] == 1) {
                print'
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Si è verificato un errore!</strong>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>';
            } 
        }
        unset($_SESSION["chatError"]);
    ?>

    <div class="container">
        <a class="btn btn-primary my-3" href="../functionalities/base.php" role="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-house" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
            </svg>
        </a>
    </div>
    
    <div class="container py-3 px-4">    
        <div class="row rounded-lg overflow-hidden shadow my-3">
            <!-- Users box-->
            <div class="col-5 px-0">
                <div class="bg-white">
                    <div class="bg-gray px-4 py-2 bg-light">
                         <p class="h5 mb-0 py-1">Chat a cui hai accesso</p>
                    </div>

                    <div class="messages-box">
                        <?php
                            foreach($sessionsPermitted as $i => $i_value) {
                                $currentConference = $i_value -> acronimoConferenza;
                                $codiceSessione = $i_value -> codiceSessione;
                                $titoloSessione = $i_value -> titoloSessione;
                                $data = $i_value -> data;
                                $oraInizio = $i_value -> oraInizio;
                                $oraFine = $i_value -> oraFine;

                                $currentDate = date("Y-m-d");
                                if(time() > strtotime($oraFine) || $currentDate != $data) {
                                    $stato = "Chiusa";
                                } else { 
                                    $stato = "Aperta";
                                }

                                echo"
                                <div class='list-group rounded-0'>
                                    <a id='{$codiceSessione}' class='list-group-item list-group-item-action rounded-0' onclick='changeChat(this.id)'>
                                        <div class='media'><img src='https://bootstrapious.com/i/snippets/sn-chat/avatar.svg' alt='user' width='50' class='rounded-circle'>
                                            <div class='media-body ml-4'>
                                                <div class='d-flex align-items-center justify-content-between mb-1'>
                                                    <h6 class='mb-0'> {$currentConference} <span>({$stato})</span></h6><small class='small font-weight-bold'>{$data}</small>
                                                </div>
                                                <p class='font-italic mb-0 text-small'>Titolo della sessione: {$titoloSessione}</p>
                                                <p class='font-italic mb-0 text-small'>Durata: {$oraInizio} - {$oraFine}</p>
                                            </div>
                                        </div>
                                    </a>    
                                </div>";
                            }
                        ?>
                    </div>

                </div>
            </div>
              
            <!-- Chat Box-->
            <div class="col-7 px-0">
                <div class="px-4 py-5 chat-box bg-white" id="chat-box">
                    
                </div>

                <!-- Typing area -->
                <iframe name="votar" style="display:none;"></iframe>
                <form id="send-form" action="insertMessage.php" method="post" class="bg-light" target="votar">
                    <div class="input-group">
                        <input id="send-input" name="msg" type="text" placeholder="Type a message" aria-describedby="send-btn" autocomplete="off" class="form-control rounded-0 border-0 py-4 bg-light" >
                        <input id="chat-id-input" name="chatId" type="hidden" aria-describedby="send-btn" autocomplete="off" class="form-control rounded-0 border-0 py-4 bg-light" >
                        <div class="input-group-append">
                            <button id="send-btn" type="submit" class="btn btn-link">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                                    <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>

    <style>
       
        body {
            background-color: #b8c6db;
            background-image: linear-gradient(315deg, #b8c6db 0%, #f5f7fa 74%);
            min-height: 100vh;
        }

        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            width: 5px;
            background: #f5f5f5;
        }

        ::-webkit-scrollbar-thumb {
            width: 1em;
            background-color: #ddd;
            outline: 1px solid slategrey;
            border-radius: 1rem;
        }

        .text-small {
            font-size: 0.9rem;
        }

        .messages-box,
        .chat-box {
            height: 510px;
            overflow-y: scroll;
        }

        .rounded-lg {
            border-radius: 0.5rem;
        }

        input::placeholder {
            font-size: 0.9rem;
            color: #999;
        }
        
    </style>
</body>

<script>

</script>

<script>
    
    const sendBtn = document.getElementById("send-btn");
    const sendInput = document.getElementById("send-input");
    const chatIdInput = document.getElementById("chat-id-input");
    const chatBox = document.getElementById("chat-box");
    const messagesBox = document.getElementById("messages-box");

    var sessioniPermesse = <?php echo json_encode($sessionsPermitted); ?>;
    var messaggiSessioniPermesse = <?php echo json_encode($messaggiSessioniPermesse); ?>;
    var usernameAttuale = <?php echo json_encode($_SESSION['user']); ?>;

    var currentChatId;

    function getCurrentChatId() {
        return currentChatId;
    }
    
    function changeChat(clickedId) {
        
        currentChatId = clickedId;

        // Check to disable input if the session is closed        
        /*let currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
        currentDate = currentDate.slice(0,10);
        
        if(sessioniPermesse[clickedId-1]["data"] !== currentDate) {
            sendInput.readOnly = true;
        } else {
            sendInput.readOnly = false;
        }*/

        var dynamicContent = '';
        for(let i = 0; i < messaggiSessioniPermesse[clickedId].length; i++) {
            if(messaggiSessioniPermesse[clickedId] !== undefined) {
                var messaggio = messaggiSessioniPermesse[clickedId][i]["testo"];
                var timeStamp = messaggiSessioniPermesse[clickedId][i]["ts"];
                var mittente = messaggiSessioniPermesse[clickedId][i]["mittente"];
                
                if(mittente === usernameAttuale) {
                    // Blue right
                    dynamicContent += `
                    <div class="media w-50 ml-auto mb-3">
                        <div class="media-body">
                            <div class="bg-primary rounded py-2 px-3 mb-2">
                                <p class="text-small mb-0 text-white">${messaggio}</p>
                            </div>
                            <p class="small text-muted">${timeStamp}</p>
                        </div>
                    </div>`;
                } else {
                    // White left
                    dynamicContent += `
                    <span style="border-radius:50%;"class="text-small bg-light">${mittente}</span>
                    <div class="media w-50 mb-3">
                        <div class="media-body ml-3">
                            <div class="bg-light rounded py-2 px-3 mb-2">
                                <p class="text-small mb-0 text-muted">${messaggio}</p>
                            </div>
                            <p class="small text-muted">${timeStamp}</p>
                        </div>
                        </div>`;
                    }
                }
            }
            
            chatBox.innerHTML = dynamicContent;
        }

            
        sendBtn.addEventListener("click", function(){

            let date = new Date().toISOString().slice(0, 19).replace('T', ' ');
            if(sendInput.value !== '') {
                const newMessage = `
                <div class="media w-50 ml-auto mb-3">
                    <div class="media-body">
                        <div class="bg-primary rounded py-2 px-3 mb-2">
                            <p class="text-small mb-0 text-white">${sendInput.value}</p>
                        </div>
                        <p class="small text-muted">${date}</p>
                    </div>
                </div>`;
                
                chatBox.insertAdjacentHTML('beforeend', newMessage);
                chatIdInput.value = getCurrentChatId();
            }  
        });
</script>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.min.js" integrity="sha384-VHvPCCyXqtD5DqJeNxl2dtTyhF78xXNXdkwX1CZeRusQfRKp+tA7hAShOK/B/fQ2" crossorigin="anonymous"></script>

</html>