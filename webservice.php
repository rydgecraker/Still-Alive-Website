<?php
    ini_set('display_errors', 1); 
    error_reporting(-1);
        /* require the user as the parameter */
    if(isset($_GET['username'])) {
        
        $username = filter_input(INPUT_GET, "username", FILTER_SANITIZE_STRING);
        echo "'" . $username . "'";
        
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
        
        $query = $pdo->prepare('SELECT * FROM Players WHERE username = ?');
        $query->execute([$username]);
        while ($row = $query->fetch())
        {
            foreach ($row as $key => $value) {
                echo $key . ":" . $row[$key] . "\n";
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