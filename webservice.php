<?php
    ini_set('display_errors', 1); 
    error_reporting(-1);
        /* require the user as the parameter */
    
    if(isset($_GET['username'])) {
        
        $username = filter_input(INPUT_GET, "username", FILTER_SANITIZE_STRING);
        
                $host = 'localhost';
        $db   = 'StillAlive';
        $user = 'root';
        $pass = 'stillalive';
        $charset = 'utf8mb4';


        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, $user, $pass, $opt);

        $JSON;

        $query = $pdo->prepare('SELECT * FROM Players WHERE username = ?');
        $query->execute([$username]);
        $exists = false;
        while($row = $query->fetch()) {
            $exists = true;
            break;
        }
        
        if(isset($_GET['create'])) {
            $createEntryInTable = filter_input(INPUT_GET, "create", FILTER_SANITIZE_STRING);
            
            if(!$exists){
                $response = "";
                switch($createEntryInTable) {
                    case "Players":
                        if(isset($_GET['name']) && issset($_GET['experience']) && isset($_GET['freeSkills'])) {
                            
                            $name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING);
                            $xp = filter_input(INPUT_GET, "experience", FILTER_SANITIZE_NUMBER_INT);
                            $freeSkills = filter_input(INPUT_GET, "freeSkills", FILTER_SANITIZE_NUMBER_INT);
                            
                           try {
                                $query = $pdo->prepare("INSERT INTO Players (username, name, startDate, experience, numEventsAttended, numNpcEvents, numPcEvents, isCheckedIn, freeSkills) VALUES (?, ?, ?, ?, 0, 0, 0, 0, ?)");
                                $query->execute([$username, $name, date('Y-m-d'), $xp, $freeSkills]);
                                $pdo->commit();
                            }catch (Exception $e){
                                $pdo->rollback();
                                throw $e;
                            } 
                            
                            $response = "Sucessfully added a new player to the Players Database";
                            
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                        break;
                    case "Characters":
                        
                        break;
                    case "CharacterSkills":
                        
                        break;
                    case "EventAttendees":
                        
                        break;
                    case "Events":
                        
                        break;
                    case "HistoricalEvents":
                        
                        break;
                    case "Items":
                        
                        break;
                    case "PrimaryWeapons":
                        
                        break;
                    case "Skills":
                        
                        break;
                    case "SkillPrerequisites":
                        
                        break;
                    case "SkillTypes":
                        
                        break;
                    default:
                        $JSON = "ERROR: COULD NOT FIND SPECIFIED TABLE";
                        break;
                }
                
                echo $response;
                
            } else {
                echo "ERROR: USERNAME ALREADY EXISTS";
            }
            
        } else if (isset($_GET['table'])){
            $table = filter_input(INPUT_GET, "table", FILTER_SANITIZE_STRING);

            if($exists){
                switch ($table) {
                    case "Players":
                        $query = $pdo->query('SELECT * FROM Players');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Characters":
                        $query = $pdo->query('SELECT * FROM Characters');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "CharacterSkills":
                        $query = $pdo->query('SELECT * FROM CharacterSkills');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "EventAttendees":
                        $query = $pdo->query('SELECT * FROM EventAttendees');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Events":
                        $query = $pdo->query('SELECT * FROM Events');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "HistoricalEvents":
                        $query = $pdo->query('SELECT * FROM HistoricalEvents');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Items":
                        $query = $pdo->query('SELECT * FROM Items');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "PrimaryWeapons":
                        $query = $pdo->query('SELECT * FROM PrimaryWeapons');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Skills":
                        $query = $pdo->query('SELECT * FROM SkillPrerequisites');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "SkillPrerequisites":
                        $query = $pdo->query('SELECT * FROM Skills');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "SkillTypes":
                        $query = $pdo->query('SELECT * FROM SkillTypes');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    default:
                        $JSON = "ERROR: COULD NOT FIND SPECIFIED TABLE";
                        break;
                }

                 echo $JSON;
            } else {
                echo "ERROR: PLAYER DOES NOT EXIST";
            }
        }
        
        
        
        
        
        
        

        
        
                    /* soak in the passed variable or set our own */
//            $number_of_posts = isset($_GET['num']) ? intval($_GET['num']) : 10; //10 is the default
//            $format = strtolower($_GET['format']) == 'json' ? 'json' : 'xml'; //xml is the default
//            $user_id = intval($_GET['user']); //no default
//
//            /* connect to the db */
//            $link = mysql_connect('localhost/phpmyadmin','root','stillalive', "StillAlive") or die('Cannot connect to the DB');
//
//            /* grab the posts from the db */
//            $query = "SELECT * FROM Players WHERE playerID = $user_id";
//            $result = mysql_query($query,$link) or die('Errant query:  '.$query);
//
//            /* create one master array of the records */
//            $posts = array();
//            if(mysql_num_rows($result)) {
//                    while($post = mysql_fetch_assoc($result)) {
//                            $posts[] = array('post'=>$post);
//                    }
//            }
//
//            /* output in necessary format */
//            if($format === 'json') {
//                    header('Content-type: application/json');
//                    echo json_encode(array('posts'=>$posts));
//            }
//            else {
//                    header('Content-type: text/xml');
//                    echo '<posts>';
//                    foreach($posts as $index => $post) {
//                            if(is_array($post)) {
//                                    foreach($post as $key => $value) {
//                                            echo '<',$key,'>';
//                                            if(is_array($value)) {
//                                                    foreach($value as $tag => $val) {
//                                                            echo '<',$tag,'>',htmlentities($val),'</',$tag,'>';
//                                                    }
//                                            }
//                                            echo '</',$key,'>';
//                                    }
//                            }
//                    }
//                    echo '</posts>';
//            }
//
//            /* disconnect from the db */
//            mysql_close($link);
            
    }
?> 