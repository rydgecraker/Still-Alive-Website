<?php
    ini_set('display_errors', 1); 
    error_reporting(-1);
        /* require the user as the parameter */
    if(isset($_GET['username']) &&isset($_GET['table'])) {
        
        $username = filter_input(INPUT_GET, "username", FILTER_SANITIZE_STRING);
        $table = filter_input(INPUT_GET, "table", FILTER_SANITIZE_STRING);
        
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