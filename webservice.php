<?php
    ini_set('display_errors', 1); 
    error_reporting(-1);
        /* require the user as the parameter */
    
    function sanitizeString($input){
        return filter_input(INPUT_GET, $input, FILTER_SANITIZE_STRING);
    }
    
    function sanitizeInt($input) {
        return filter_input(INPUT_GET, $input, FILTER_SANITIZE_NUMBER_INT);
    }
    
    function getPlayerID($pdo, $username){
        $playerID;
        $query = $pdo->prepare('SELECT * FROM Players WHERE username = ?');
        $query->execute([$username]);
            while($row = $query->fetch()) {
                $playerID = $row['playerID'];
                break;
            }
        return $playerID;
    }
    
    function getCurrentDate(){
        return date('Y-m-d');
    }
    
    function getTomorrowDate(){
        $date = new DateTime('tomorrow');
        return $date->format('Y-m-d');
    }
    
    function getCurrentTime(){
        return date('H:i:s', time());
    }
    

    
    function isGiven($input) {
        return isset($_GET[$input]);
    }
    
    if(isGiven('username')) {
        
        $username = sanitizeString('username');
        
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
        $usernameExists = false;
        while($row = $query->fetch()) {
            $usernameExists = true;
            break;
        }
        
        if(isGiven('create')) {
            $createEntryInTable = sanitizeString('create');
            
            $response = "";
            switch($createEntryInTable) {
                case "Players":
                    if($usernameExists) {
                        $response = "ERROR: PLAYER USERNAME ALREADY EXISTS";
                    } else {
                        if(isGiven('name') && isGiven('experience') && isGiven('freeSkills')) {

                           //request should look like:
                           //username=someusername&create=Players&name=somename&experience=0&freeSkills=0

                           $name = sanitizeString('name');
                           $xp = sanitizeInt('experience');
                           $freeSkills = sanitizeInt('freeSkills');

                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO Players (username, name, startDate, experience, numEventsAttended, numNpcEvents, numPcEvents, isCheckedIn, freeSkills) VALUES (?, ?, ?, ?, 0, 0, 0, 0, ?)");
                                 $query->execute([$username, $name, date('Y-m-d'), $xp, $freeSkills]);
                                 $pdo->commit();
                                 $response = "Sucessfully added a new player to the Players Database";
                            }catch (Exception $e){
                                $pdo->rollback();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }

                    break;
                case "Characters":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('name')) {

                            //request should look like:
                            //username=someusername&create=Characters&name=somename&bio=someBio
                            //bio is optional
                            
                            $name = sanitizeString('name');
                            
                            $bio = "-None Given-";
                            if(isGiven('bio')) {
                                $bio = sanitizeString('bio');
                            }
                            
                            $playerID = getPlayerID($pdo, $username);
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO Characters (playerID, name, startDate, isAlive, numSkills, spentXp, freeSkillsSpent, infection, primaryWeaponID, bullets, megas, accus, millitaries, rockets, bio, bulletCasings, megaCasings, accuCasings, millitaryCasings, rocketCasings, techParts, mechParts, stone, wood, metal, cloth) " .
                                         "VALUES (?, ?, ?, 1, 4, 0, 0, 0, 1, 0, 0, 0, 0, 0, ?, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)");
                                 $query->execute([$playerID, $name, getCurrentDate(), $bio]);
                                 $pdo->commit();
                                 $response = "Sucessfully added a new character to the Characters Database with the playerID $playerID ($username)";
                            }catch (Exception $e){
                                $pdo->rollback();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "CharacterSkills":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: UNIMPLEMENTED UNTIL SKILLS ARE IN DATABASE";
                    }
                    break;
                case "EventAttendees":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('event')) {

                            //request should look like:
                            //username=someusername&create=EventAttendees&event=eventIDnum&character=someCharacterID
                            //character is optional
                            
                            $eventID = sanitizeInt('event');
                            $playerID = getPlayerID($pdo, $username);
                            $characterID = null;
                            if(isGiven('character')) {
                                $characterID = sanitizeInt('character');
                            }
                            
                            
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO EventAttendees (eventID, playerID, characterID, checkinTime) " .
                                         "VALUES (?, ?, ?, ?)");
                                 $query->execute([$eventID, $playerID, $characterID, getCurrentTime()]);
                                 $pdo->commit();
                                 $response = "Sucessfully added a new EventAttendee to the EventAttendees Database with the playerID $playerID ($username)";
                            }catch (Exception $e){
                                $pdo->rollback();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "Events":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('startTime') && isGiven('endTime') && isGiven('name') && isGiven('desc')) {

                            //request should look like:
                            //username=someusername&create=Events&startTime=someStartTime&endTime=someEndTime&name=someEventName&desc=someDescription
                            //times are in armyTime 00:00:00 - 23:59:59
                            //character is optional
                            
                            $start = date('H:i:s', strtotime(sanitizeString('startTime')));
                            $end = date('H:i:s', strtotime(sanitizeString('endTime')));
                            $name = sanitizeString('name');
                            $desc = sanitizeString('desc');
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO Events (startDate, endDate, startTime, endTime, eventRunning, name, description) " .
                                         "VALUES (?, ?, ?, ?, 0, ?, ?)");
                                 $query->execute([getCurrentDate(), getTomorrowDate(), $start, $end, $name, $desc]);
                                 $pdo->commit();
                                 $response = "Sucessfully added a new Event to the Events Database.";
                            }catch (Exception $e){
                                $pdo->rollback();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "HistoricalEvents":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('name') && isGiven('desc')) {

                            //request should look like:
                            //username=someusername&create=HistoricalEvents&character=someCharID&event=someEventID&name=someName&desc=someDescription
                            //character and event are optional
                            
                            $name = sanitizeString('name');
                            $desc = sanitizeString('desc');
                            $playerID = getPlayerID($pdo, $username);
                            
                            $characterID = null;
                            if(isGiven('character')) {
                                $characterID = sanitizeInt('character');
                            }
                            
                            $eventID = null;
                            if(isGiven('event')) {
                                $eventID = sanitizeInt('event');
                            }
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO HistoricalEvents (playerID, characterID, eventID, name, description, date) " .
                                         "VALUES (?, ?, ?, ?, ?, ?)");
                                 $query->execute([$playerID, $characterID, $eventID, $name, $desc, getCurrentDate()]);
                                 $result = $pdo->commit();
                                 
                                 if($result) {
                                     $response = "Failed to add a new HistoricalEvent to the HistoricalEvents Database.";
                                 } else {
                                     $response = "Sucessfully added a new HistoricalEvent to the HistoricalEvents Database.";
                                 }
                                 
                            }catch (Exception $e){
                                $pdo->rollback();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
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
            
        } else if (isset($_GET['table'])){
            $table = filter_input(INPUT_GET, "table", FILTER_SANITIZE_STRING);

            if($usernameExists){
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
    } else {
        echo "ERROR: NO USERNAME SPECIFIED";
    }
?> 