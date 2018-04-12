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
        $usernameExists = false;
        while($row = $query->fetch()) {
            $usernameExists = true;
            break;
        }
        
        if(isset($_GET['create'])) {
            $createEntryInTable = filter_input(INPUT_GET, "create", FILTER_SANITIZE_STRING);
            
            $response = "";
            switch($createEntryInTable) {
                case "Players":
                    if($usernameExists) {
                        $response = "ERROR: PLAYER USERNAME ALREADY EXISTS";
                    } else {
                        if(isset($_GET['name']) && isset($_GET['experience']) && isset($_GET['freeSkills'])) {

                           //request should look like:
                           //username=someusername&create=Players&name=somename&experience=0&freeSkills=0

                           $name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING);
                           $xp = filter_input(INPUT_GET, "experience", FILTER_SANITIZE_NUMBER_INT);
                           $freeSkills = filter_input(INPUT_GET, "freeSkills", FILTER_SANITIZE_NUMBER_INT);

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
                        if(isset($_GET['name'])) {

                            //request should look like:
                            //username=someusername&create=Characters&name=somename&bio=someBio
                            //bio is optional
                            
                            $name = filter_input(INPUT_GET, "name", FILTER_SANITIZE_STRING);
                            $bio = "-None Given-";
                            if(isset($_GET['bio'])) {
                                $bio = filter_input(INPUT_GET, 'bio', FILTER_SANITIZE_STRING);
                            }
                            
                            $playerID;
                            $query = $pdo->prepare('SELECT * FROM Players WHERE username = ?');
                            $query->execute([$username]);
                            while($row = $query->fetch()) {
                                $playerID = $row['playerID'];
                                break;
                            }
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO Characters (playerID, name, startDate, isAlive, numSkills, spentXp, freeSkillsSpent, infection, primaryWeaponID, bullets, megas, accus, millitaries, rockets, bio, bulletCasings, megaCasings, accuCasings, millitaryCasings, rocketCasings, techParts, mechParts, stone, wood, metal, cloth) " .
                                         "VALUES (?, ?, ?, 1, 4, 0, 0, 0, 1, 0, 0, 0, 0, 0, ?, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0)");
                                 $query->execute([$playerID, $name, date('Y-m-d'), $bio]);
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