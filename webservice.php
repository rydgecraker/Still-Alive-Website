<?php
    ini_set('display_errors', 1); 
    error_reporting(-1);

    require 'php/Inputs.php';
    require 'php/DateTimeFunctions.php';
    require 'php/DBConnect.php';
    
    function createHistoricalEvent($username, $title, $desc, $charID, $eventID){
        $pdo = setUpPDO();
        if($username != null){
            $playerID = getPlayerID($pdo, $username);
        }
        try {
            $pdo->beginTransaction();
            $query = $pdo->prepare("INSERT INTO HistoricalEvents (playerID, characterID, eventID, name, description, date) " .
                    "VALUES (?, ?, ?, ?, ?, ?)");
            $query->execute([$playerID, $charID, $eventID, $title, $desc, getCurrentDate()]);
            $pdo->commit();

        }catch (Exception $e){
            $pdo->rollBack();
            throw $e;
        }
    }
    
    function getPlayerID($pdo, $username){
        $playerID = 1;
        $query = $pdo->prepare('SELECT * FROM Players WHERE username = ?');
        $query->execute([$username]);
            while($row = $query->fetch()) {
                $playerID = $row['playerID'];
                break;
            }
        return $playerID;
    }
    
    function getDbUpdateStatusNum(){
        header('Content-Type: text/plain');
        $updateNum = 0;
        $fp = fopen(getDbStatusUrl(), "r");
        while(!feof ($fp)) {
            $line = rtrim(fgets($fp));
            if($line != ""){
                $updateNum = doubleval($line);
            }
        }
        fclose($fp);
        return $updateNum;
    }
    
    function writeDbUpdateStatusNum($updateNum) {
        $myfile = fopen(getDbStatusUrl(), "w") or die("ERROR: unable to open file!");
        fwrite($myfile, $updateNum."");
        fclose($myfile);
        header('Content-Type: text/plain');
    }
    
    if(isGiven('username')) {
        
        $updateStatusNum = getDbUpdateStatusNum();
        
        $username = sanitizeString('username');
        
        $pdo = setUpPDO();

        $JSON;

        $query = $pdo->prepare('SELECT * FROM Players WHERE username = ?');
        $query->execute([$username]);
        $usernameExists = false;
        while($row = $query->fetch()) {
            $usernameExists = true;
            break;
        }
        
        if(isGiven('supidasy')){
            $playerID = getPlayerID($pdo, $username);
            $pidasy = sanitizeString('supidasy');
            try {
                $pdo->beginTransaction();
                $query = $pdo->prepare("INSERT INTO Tnaptyg (playerID, tnapyg) " .
                        "VALUES (?, ?)");
                $query->execute([$playerID, $pidasy]);
                $pdo->commit();

                header('Content-Type: text/plain');
                echo "SUCCESS";
           }catch (Exception $e){
               $pdo->rollBack();
               throw $e;
           } 
            
        } else if(isGiven('cpidasy')){
            $playerID = getPlayerID($pdo, $username);
            $pidasy = sanitizeString('cpidasy');
            
            $result = callQuery($pdo, "SELECT tnapyg FROM Tnaptyg WHERE playerID = $playerID");
            
            $value = "";
            
            while($row = $result->fetch()) {
                $value = $row['tnapyg'];
            }
            
            if($value == $pidasy){
                header('Content-Type: text/plain');
                echo "ACCESS GRANTED";
            } else {
                header('Content-Type: text/plain');
                echo "ERROR: WITH PIDASY";
            }
            
        } else if(isGiven('rpidasy')){
            $playerID = getPlayerID($pdo, $username);
            $pidasy = sanitizeString('rpidasy');
            
            try {
                $pdo->beginTransaction();
                $query = $pdo->prepare("UPDATE Tnaptyg SET tnapyg = ? WHERE playerID = ?");
                $query->execute([$pidasy, $playerID]);
                
                $pdo->commit();
                header('Content-Type: text/plain');
                echo "SUCCESS";
                createHistoricalEvent($username, "Player changed password", "Player changed password to new value", null, null);
           }catch (Exception $e){
               $pdo->rollBack();
               throw $e;
           } 
     
        } else if(isGiven('create')) {
            $createEntryInTable = sanitizeString('create');
            
            $response = "";
            switch($createEntryInTable) {
                case "AwardWinners":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('eventID') && isGiven('awardID') && isGiven('notes')){
                            //request should look like:
                            //username=asdf&create=Awards&name=asdf&description=asdf
                            
                            $eventID = sanitizeInt('eventID');
                            $awardID = sanitizeInt('awardID');
                            $notes = sanitizeString('notes');
                            $playerID = getPlayerID($pdo, $username);
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO AwardWinners (eventID, playerID, awardID, notes) " .
                                         "VALUES (?, ?, ?, ?)");
                                 $query->execute([$eventID, $playerID, $awardID, $notes]);
                                 $pdo->commit();
                                 
                                 $response = "Sucessfully added a new Award Winner.";
                                 createHistoricalEvent($username, "Award winner created", "Player is an award winner", null, $eventID);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            }
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                case "Awards":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('name') && isGiven('description')){
                            //request should look like:
                            //username=asdf&create=Awards&name=asdf&description=asdf
                            
                            $name = sanitizeString('name');
                            $desc = sanitizeString('description');
                            
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO Awards (name, description) " .
                                         "VALUES (?, ?)");
                                 $query->execute([$name, $desc]);
                                 $pdo->commit();
                                 
                                 $response = "Sucessfully added a new Award Type.";
                                 createHistoricalEvent($username, "Award Created", "An award type was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                case "CraftableObjectMaterials":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('objectID') && isGiven('materialID') && isGiven('amount')){
                            //request should look like:
                            //username=asdf&create=CraftableObjectMaterials&objectID=#&materialID=#&amount=#
                            
                            $objectID = sanitizeInt('objectID');
                            $materialID = sanitizeInt('materialID');
                            $amount = sanitizeInt('amount');
                            
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO CraftableObjectMaterials (objectID, materialID, amount) " .
                                         "VALUES (?, ?, ?)");
                                 $query->execute([$objectID, $materialID, $amount]);
                                 $pdo->commit();
                                 
                                 $response = "Sucessfully added a new Craftable Object Material.";
                                 createHistoricalEvent($username, "Craftable Object Material Created", "A craftable object material was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                case "CraftableObjectRequiredSkills":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('objectID') && isGiven('skillID')){
                            //request should look like:
                            //username=asdf&create=CraftableObjectRequiredSkills&objectID=#&skillID=#
                            
                            $objectID = sanitizeInt('objectID');
                            $skillID = sanitizeInt('skillID');
                            
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO CraftableObjectRequiredSkills (objectID, skillID) " .
                                         "VALUES (?, ?, ?)");
                                 $query->execute([$objectID, $skillID]);
                                 $pdo->commit();
                                 
                                 $response = "Sucessfully added a new Craftable Object SkillID.";
                                 createHistoricalEvent($username, "Craftable Object Required Skill Created", "A craftable object required skill entry was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                case "CraftableObjects":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('name') && isGiven('description') && isGiven('clothAmount') && isGiven('metalAmount') && isGiven('woodAmount') && isGiven('stoneAmount') && isGiven('techPartsAmount') && isGiven('mechPartsAmount') && isGiven('buildTimeMinutes') && isGiven('fortificationLevel')){
                            //request should look like:
                            //username=asdf&create=CraftableObjects&name=asdf&description=asdf&clothAmount=#&metalAmount=#&woodAmount=#&stoneAmount=#&techPartsAmount=#&mechPartsAmount=#&buildTimeMinutes=#&fortificationLevel=#
                            
                            $name = sanitizeString('name');
                            $desc = sanitizeString('description');
                            $cloth = sanitizeInt('clothAmount');
                            $metal = sanitizeInt('metalAmount');
                            $wood = sanitizeInt('woodAmount');
                            $stone = sanitizeInt('stoneAmount');
                            $tech = sanitizeInt('techPartsAmount');
                            $mech = sanitizeInt('mechPartsAmount');
                            $buildTime = sanitizeInt('buildTimeMinutes');
                            $fortLvl = sanitizeInt('fortificationLevel');
                            
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO CraftableObjects (name, description, clothAmount, metalAmount, woodAmount, stoneAmount, techPartsAmount, mechPartsAmount, buildTimeMinutes, fortificationLevel) " .
                                         "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                 $query->execute([$name, $desc, $cloth, $metal, $wood, $stone, $tech, $mech, $buildTime, $fortLvl]);
                                 $pdo->commit();
                                 
                                 $response = "Sucessfully added a new Craftable Object.";
                                 createHistoricalEvent($username, "Craftable Object Created", "A craftable object entry was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                case "CraftableObjectsAsMaterials":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('objectToBeCrafted') && isGiven('objectMaterial') && isGiven('amount')){
                            //request should look like:
                            //username=asdf&create=CraftableObjectsAsMaterials&objectToBeCrafted=#&objectMaterial=#&amount=#
                            
                            $objToBeCrafted = sanitizeInt('objectToBeCrafted');
                            $objMat = sanitizeInt('objectMaterial');
                            $amount = sanitizeInt('amount');
                            
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO CraftableObjectsAsMaterials (objectToBeCrafted, objectMaterial, amount) " .
                                         "VALUES (?, ?, ?)");
                                 $query->execute([$objToBeCrafted, $objMat, $amount]);
                                 $pdo->commit();
                                 
                                 $response = "Sucessfully added a new Craftable Object as a Material.";
                                 createHistoricalEvent($username, "Craftable Object as Material Created", "A craftable object as material entry was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                case "Materials":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('name') && isGiven('description')){
                            //request should look like:
                            //username=asdf&create=Materials&name=asdf&description=asdf
                            
                            $name = sanitizeString('name');
                            $desc = sanitizeString('description');
                            
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO Materials (name, description) " .
                                         "VALUES (?, ?)");
                                 $query->execute([$name, $desc]);
                                 $pdo->commit();
                                 
                                 $response = "Sucessfully added a new Craftable Object as a Material.";
                                 createHistoricalEvent($username, "Material Created", "A Material type was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                case "HandbookEntry":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT ADD TO THE HANDBOOK ENTRY TABLE";
                    }
                case "SkillCategories":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('name')) {

                            //request should look like:
                            //username=asdf&create=SkillCategories&name=#
                            
                            $name = sanitizeString('name');
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO SkillCategories (name) " .
                                         "VALUES (?)");
                                 $query->execute([$name]);
                                 $pdo->commit();
                                 
                                 $response = "Sucessfully added a new Skill Category to the Skill Categories Table with the name: $name";
                                 createHistoricalEvent($username, "Skill Category Created", "A skill category type was created", null, null);
                                 
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "Players":
                    if($usernameExists) {
                        $response = "ERROR: PLAYER USERNAME ALREADY EXISTS";
                    } else {
                        if(isGiven('name') && isGiven('experience') && isGiven('freeSkills')) {

                           //request should look like:
                           //username=asdf&create=Players&name=asdf&experience=#&freeSkills=#

                           $name = sanitizeString('name');
                           $xp = sanitizeInt('experience');
                           $freeSkills = sanitizeInt('freeSkills');

                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO Players (username, name, startDate, experience, numEventsAttended, numNpcEvents, numPcEvents, isCheckedIn, freeSkills) VALUES (?, ?, ?, ?, 0, 0, 0, 0, ?)");
                                 $query->execute([$username, $name, getCurrentDate(), $xp, $freeSkills]);
                                 $playerID = $pdo->lastInsertId();
                                 $pdo->commit();
                                 $response = "Sucessfully added a new player to the Players Table with ID=$playerID";
                                 createHistoricalEvent($username, "Player Created", "The player was created on this date", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
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
                            //username=asdf&create=Characters&name=asdf&bio=asdf
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
                                 $charID = $pdo->lastInsertId();
                                 $pdo->commit();
                                 $response = "Sucessfully added a new character to the Characters Table with the playerID $playerID ($username) at the ID=$charID";
                                 createHistoricalEvent($username, "Character Created", "The character was created on this date", $charID, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
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
                        if(isGiven('charID') && isGiven('skillID')) {

                            //request should look like:
                            //username=asdf&create=CharacterSkills&charID=#&skillID=#
                            
                            $characterID = sanitizeInt('charID');
                            $skillID = sanitizeInt('skillID');
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("INSERT INTO CharacterSkills (characterID, skillID, dateAdded) " .
                                         "VALUES (?, ?, ?)");
                                 $query->execute([$characterID, $skillID, getCurrentDate()]);
                                 $pdo->commit();
                                 $response = "Sucessfully added a new Character Skill to the Character Skills Table with the characterID: $characterID";
                                 createHistoricalEvent($username, "Character added a new skill.", "The character added a new skill with the ID of $skillID.", $characterID, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "EventAttendees":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('event')) {

                            //request should look like:
                            //username=asdf&create=EventAttendees&event=#&character=#
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
                                 $response = "Sucessfully added a new EventAttendee to the EventAttendees Table with the playerID $playerID ($username)";
                            }catch (Exception $e){
                                $pdo->rollBack();
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
                            //username=asdf&create=Events&startTime=asdf&endTime=asdf&name=asdf&desc=asdf
                            //times are in armyTime 00:00:00 - 23:59:59
                            
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
                                 $response = "Sucessfully added a new Event to the Events Table.";
                                 createHistoricalEvent($username, "Event Created", "An event was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
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
                            //username=asdf&create=HistoricalEvents&character=#&event=#&name=asdf&desc=asdf
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
                                $pdo->commit();
                                $response = "Sucessfully added a new HistoricalEvent to the HistoricalEvents Table.";
                                 
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "Items":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('character') && isGiven('name') && isGiven('desc')) {

                            //request should look like:
                            //username=asdf&create=Items&character=#&name=asdf&desc=asdf
                            
                            $name = sanitizeString('name');
                            $desc = sanitizeString('desc');
                            $characterID = sanitizeInt('character');
                                                 
                            try {
                                $pdo->beginTransaction();
                                $query = $pdo->prepare("INSERT INTO Items (characterID, name, description, date) " .
                                        "VALUES (?, ?, ?, ?)");
                                $query->execute([$characterID, $name, $desc, getCurrentDate()]);
                                $pdo->commit();
                                $response = "Sucessfully added a new Item to the Items Table.";
                                createHistoricalEvent($username, "Item Created", "An item type was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "PrimaryWeapons":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('desc')) {

                            //request should look like:
                            //username=asdf&create=PrimaryWeapons&desc=asdf
                            $desc = sanitizeString('desc');
                                                 
                            try {
                                $pdo->beginTransaction();
                                $query = $pdo->prepare("INSERT INTO PrimaryWeapons (description) " .
                                        "VALUES (?)");
                                $query->execute([$desc]);
                                $pdo->commit();
                                $response = "Sucessfully added a new Primary Weapon to the PrimaryWeapons Table.";
                                createHistoricalEvent($username, "Primary Weapon Created", "A primary weapon type was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "Skills":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('xp') && isGiven('skillType') && isGiven('name') && isGiven('desc') && isGiven('flavor') && isGiven('minInfect') && isGiven('skillCategory')) {

                            //request should look like:
                            //username=asdf&create=Skills&xp=#&skillType=#&name=asdf&desc=asdf&flavor=asdf&minInfect=#&skillCategory=#
                            $xp = sanitizeInt('xp');
                            $skillTypeID = sanitizeInt('skillType');
                            $name = sanitizeString('name');                            
                            $desc = sanitizeString('desc');
                            $flav = sanitizeString('flavor');
                            $minInfect = sanitizeInt('minInfect');
                            $skillCategory = sanitizeInt('skillCateogry');
                                                 
                            try {
                                $pdo->beginTransaction();
                                $query = $pdo->prepare("INSERT INTO Skills (xpCost, skillTypeID, name, description, flavor, minInfect, skillCategoryID) " .
                                        "VALUES (?, ?, ?, ?, ?, ?, ?)");
                                $query->execute([$xp, $skillTypeID, $name, $desc, $flav, $minInfect, $skillCategory]);
                                $pdo->commit();
                                $response = "Sucessfully added a new Skill to the Skills Table.";
                                createHistoricalEvent($username, "Skill Created", "A skill entry was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "SkillPrerequisites":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('base') && isGiven('prereq')) {

                            //request should look like:
                            //username=asdf&create=SkillPrerequisites&base=#&prereq=#
                            $base = sanitizeInt('base');
                            $prereq = sanitizeInt('prereq');
                                                 
                            try {
                                $pdo->beginTransaction();
                                $query = $pdo->prepare("INSERT INTO SkillPrerequisites (baseSkillID, prereqSkillID) " .
                                        "VALUES (?, ?)");
                                $query->execute([$base, $prereq]);
                                $pdo->commit();
                                $response = "Sucessfully added a new Skill Prereq to the SkillPrerequisites Table.";
                                createHistoricalEvent($username, "Skill Prerequisite Created", "A skill prerequisite entry was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                case "SkillTypes":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('name')) {

                            //request should look like:
                            //username=asdf&create=SkillTypes&name=asdf
                            $name = sanitizeString('name');
                                                 
                            try {
                                $pdo->beginTransaction();
                                $query = $pdo->prepare("INSERT INTO SkillTypes (name) " .
                                        "VALUES (?)");
                                $query->execute([$name]);
                                $pdo->commit();
                                $response = "Sucessfully added a new Skill Type to the SkillTypes Table.";
                                createHistoricalEvent($username, "Skill Type Created", "A skill type entry was created", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $createEntryInTable table";
                        }
                    }
                    break;
                default:
                    $response = "ERROR: COULD NOT FIND SPECIFIED TABLE";
                    break;
            }
            header('Content-Type: text/plain');
            $updateStatusNum += 1;
            writeDbUpdateStatusNum($updateStatusNum);
            echo $response;
            
        } else if (isGiven('fetch')){
            $table = sanitizeString('fetch');
            $JSON;
            if($usernameExists){
                switch ($table) {
                    case "AwardWinners":
                        $query = $pdo->query('SELECT * FROM AwardWinners');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Awards":
                        $query = $pdo->query('SELECT * FROM Awards');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "CraftableObjectMaterials":
                        $query = $pdo->query('SELECT * FROM CraftableObjectMaterials');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "CraftableObjectRequiredSkills":
                        $query = $pdo->query('SELECT * FROM CraftableObjectRequiredSkills');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "CraftableObjects":
                        $query = $pdo->query('SELECT * FROM CraftableObjects');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "CraftableObjectsAsMaterials":
                        $query = $pdo->query('SELECT * FROM CraftableObjectsAsMaterials');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Materials":
                        $query = $pdo->query('SELECT * FROM Materials');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "HandbookEntry":
                        $query = $pdo->query('SELECT * FROM HandbookEntry');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Tnaptyg":
                        $query = $pdo->query('SELECT * FROM Tnaptyg');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "SkillCategories":
                        $query = $pdo->query('SELECT * FROM SkillCategories');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Players":
                        $query = $pdo->query('SELECT * FROM Players');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Characters":
                        $query = $pdo->query('SELECT * FROM Characters');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "CharacterSkills":
                        $query = $pdo->query('SELECT * FROM CharacterSkills');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "EventAttendees":
                        $query = $pdo->query('SELECT * FROM EventAttendees');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Events":
                        $query = $pdo->query('SELECT * FROM Events');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "HistoricalEvents":
                        $query = $pdo->query('SELECT * FROM HistoricalEvents');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Items":
                        $query = $pdo->query('SELECT * FROM Items');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "PrimaryWeapons":
                        $query = $pdo->query('SELECT * FROM PrimaryWeapons');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "Skills":
                        $query = $pdo->query('SELECT * FROM Skills');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "SkillPrerequisites":
                        $query = $pdo->query('SELECT * FROM SkillPrerequisites');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    case "SkillTypes":
                        $query = $pdo->query('SELECT * FROM SkillTypes');
                        header('Content-Type: application/json');
                        $JSON = json_encode($query->fetchAll(PDO::FETCH_ASSOC));
                        break;
                    default:
                        header('Content-Type: text/plain');
                        $JSON = "ERROR: COULD NOT FIND SPECIFIED TABLE";
                        break;
                }
                 echo $JSON;
            } else {
                header('Content-Type: text/plain');
                echo "ERROR: PLAYER DOES NOT EXIST";
            }
        } else if(isGiven('update')){
            
            $updateEntryInTable = sanitizeString('update');
  
            $response = "";
            switch($updateEntryInTable) {
                case "AwardWinners":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE $updateEntryInTable TABLE";
                    }
                    break;
                case "Awards":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE $updateEntryInTable TABLE";
                    }
                    break;
                case "CraftableObjectMaterials":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE $updateEntryInTable TABLE";
                    }
                    break;
                case "CraftableObjectRequiredSkills":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE $updateEntryInTable TABLE";
                    }
                    break;
                case "CraftableObjects":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE $updateEntryInTable TABLE";
                    }
                    break;
                case "CraftableObjectsAsMaterials":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE $updateEntryInTable TABLE";
                    }
                    break;
                case "Materials":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE $updateEntryInTable TABLE";
                    }
                    break;
                case "HandbookEntry":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE HANDBOOK ENTRY TABLE";
                    }
                    break;
                case "SkillCategories":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE SKILL CATEGORIES TABLE";
                    }
                    break;
                case "Players":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('name') && isGiven('experience') && isGiven('numEvents') && isGiven('numNpc') && isGiven('numPc') && isGiven('checkedIn') && isGiven('freeSkills')) {

                           //request should look like:
                           //username=asdf&update=Players&name=asdf&experience=#&numEvents=#&numNpc=#&numPc=#&checkedIn=#&freeSkills=#
                           $playerID = getPlayerID($pdo, $username);
                           $name = sanitizeString('name');
                           $xp = sanitizeInt('experience');
                           $numEvents = sanitizeInt('numEvents');
                           $numNpcEvents = sanitizeInt('numNpc');
                           $numPcEvents = sanitizeInt('numPc');
                           $checkedIn = (sanitizeInt('checkedIn') == 1);
                           $freeSkills = sanitizeInt('freeSkills');
                           

                            try {
                                 $pdo->beginTransaction();
                                 if($checkedIn) {
                                    $query = $pdo->prepare("UPDATE Players SET name = ?, experience = ?, numEventsAttended = ?, numNpcEvents = ?, numPcEvents = ?, isCheckedIn = 1, lastCheckIn = ?, freeSkills = ? WHERE playerID = ?");
                                    $query->execute([$name, $xp, $numEvents, $numNpcEvents, $numPcEvents, getCurrentDate(), $freeSkills, $playerID]);
                                 } else {
                                    $query = $pdo->prepare("UPDATE Players SET name = ?, experience = ?, numEventsAttended = ?, numNpcEvents = ?, numPcEvents = ?, isCheckedIn = 0, freeSkills = ? WHERE playerID = ?");
                                    $query->execute([$name, $xp, $numEvents, $numNpcEvents, $numPcEvents, $freeSkills, $playerID]);
                                 }
                                 $pdo->commit();
                                 $response = "Sucessfully updated $username's entry in the Players table";
                                 createHistoricalEvent($username, "Player was modified", "The player's stats were modified", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            } 
                        } else {
                            $response = "Please Supply all of the necessary data for the $updateEntryInTable table";
                        }
                    }

                    break;
                case "Characters":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('charID') && isGiven('name') && isGiven('isAlive') && isGiven('skills') && isGiven('spentXp') && isGiven('spentFS') && isGiven('infect') && isGiven('primWeap') && isGiven('bull') && isGiven('mega') && isGiven('accu') && isGiven('milli') && isGiven('rocket') && isGiven('bio') && isGiven('bullCas') && isGiven('megaCas') && isGiven('accuCas') && isGiven('rocketCas') && isGiven('milliCas') && isGiven('tech') && isGiven('mech') && isGiven('stone') && isGiven('wood') && isGiven('metal') && isGiven('cloth')) {

                            //request should look like:
                            //username=asdf&update=Characters&charID=#&name=asdf&isAlive=#&skills=#&spentXp=#&spentFS=#&infect=#&primWeap=#&bull=#&mega=#&accu=&milli=&rocket=#&bio=asdf&bullCas=#&megaCas=#&accuCas=#&rocketCas=#&milliCas=#&tech=#&mech=#&stone=#&wood=#&metal=#&cloth=#
                            $characterID = sanitizeInt('charID');
                            $name = sanitizeString('name');
                            $isAlive = (sanitizeInt('isAlive') == 1);
                            $numSkills = sanitizeInt('skills');
                            $spentXp = sanitizeInt('spentXp');
                            $spentFreeSkills = sanitizeInt('spentFS');
                            $infection = sanitizeInt('infect');
                            $primaryWeaponID = sanitizeInt('primWeap');
                            
                            $bullets = sanitizeInt('bull');
                            $megas = sanitizeInt('mega');
                            $rockets = sanitizeInt('rocket');
                            $accus = sanitizeInt('accu');
                            $millis = sanitizeInt('milli');
                            
                            $bio = sanitizeString('bio');
                            
                            $bulletCasings = sanitizeInt('bullCas');
                            $megaCasings = sanitizeInt('megaCas');
                            $rocketCasings = sanitizeInt('rocketCas');
                            $accuCasings = sanitizeInt('accuCas');
                            $milliCasings = sanitizeInt('milliCas');
                            
                            $tech = sanitizeInt('tech');
                            $mech = sanitizeInt('mech');
                            $stone = sanitizeInt('stone');
                            $wood = sanitizeInt('wood');
                            $metal = sanitizeInt('metal');
                            $cloth = sanitizeInt('cloth');
                            
                            
                            try {
                                 $pdo->beginTransaction();
                                 if($isAlive) {
                                    $query = $pdo->prepare("UPDATE Characters SET name = ?, isAlive = 1, numSkills = ?, spentXp = ?, freeSkillsSpent = ?, infection = ?, primaryWeaponID = ?, bullets = ?, megas = ?, accus = ?, millitaries = ?, rockets = ?, bio = ?, bulletCasings = ?,  megaCasings = ?,  accuCasings = ?, millitaryCasings = ?, rocketCasings = ?, techParts = ?, mechParts = ?, stone = ?, wood = ?, metal = ?, cloth = ? WHERE characterID = ?");
                                    $query->execute([$name, $numSkills, $spentXp, $spentFreeSkills, $infection, $primaryWeaponID, $bullets, $megas, $accus, $millis, $rockets, $bio, $bulletCasings, $megaCasings, $accuCasings, $milliCasings, $rocketCasings, $tech, $mech, $stone, $wood, $metal, $cloth, $characterID]);
                                 } else {
                                    $query = $pdo->prepare("UPDATE Characters SET name = ?, isAlive = 0, deathDate = ?, numSkills = ?, spentXp = ?, freeSkillsSpent = ?, infection = ?, primaryWeaponID = ?, bullets = ?, megas = ?, accus = ?, millitaries = ?, rockets = ?, bio = ?, bulletCasings = ?,  megaCasings = ?,  accuCasings = ?, millitaryCasings = ?, rocketCasings = ?, techParts = ?, mechParts = ?, stone = ?, wood = ?, metal = ?, cloth = ? WHERE characterID = ?");
                                    $query->execute([$name, getCurrentDate(), $numSkills, $spentXp, $spentFreeSkills, $infection, $primaryWeaponID, $bullets, $megas, $accus, $millis, $rockets, $bio, $bulletCasings, $megaCasings, $accuCasings, $milliCasings, $rocketCasings, $tech, $mech, $stone, $wood, $metal, $cloth, $characterID]);
                                 }
                                 $pdo->commit();
                                 $response = "Sucessfully updated $name's entry in the Characters table";
                                 createHistoricalEvent($username, "Character Modified", "The character's stats were modified", $characterID, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            }
                        } else {
                            $response = "Please Supply all of the necessary data for the $updateEntryInTable table";
                        }
                    }
                    break;
                case "CharacterSkills":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE CHARACTER SKILLS TABLE";
                    }
                    break;
                case "EventAttendees":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE EVENT ATTENDEES TABLE";
                    }
                    break;
                case "Events":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('eventID') && isGiven('eventRunning') && isGiven('name') && isGiven('desc')) {

                            //request should look like:
                            //username=asdf&update=Events&eventID=#&eventRunning=#&name=asdf&desc=asdf
                            
                            $eventID = sanitizeInt('eventID');
                            $eventRunning = (sanitizeInt('eventRunning') == 1);
                            $name = sanitizeString('name');
                            $desc = sanitizeString('desc');
                            
                            try {
                                 $pdo->beginTransaction();
                                 if($eventRunning) {
                                    $query = $pdo->prepare("UPDATE Events SET startDate = ?, startTime = ?, eventRunning = 1, name = ?, description = ? WHERE eventID = ?");
                                 } else {
                                    $query = $pdo->prepare("UPDATE Events SET endDate = ?, endTime = ?, eventRunning = 0, name = ?, description = ? WHERE eventID = ?");
                                 }
                                 $query->execute([getCurrentDate(), getCurrentTime(), $name, $desc, $eventID]);
                                 $pdo->commit();
                                 $response = "Sucessfully updated $name's entry in the Characters table";
                                 createHistoricalEvent($username, "Event Modified", "This event was modified", null, $eventID);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            }
                        } else {
                            $response = "Please Supply all of the necessary data for the $updateEntryInTable table";
                        }
                    }
                    break;
                case "HistoricalEvents":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE HISTORICAL EVENTS TABLE";
                    }
                    break;
                case "Items":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE ITEMS TABLE";
                    }
                    break;
                case "PrimaryWeapons":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE PRIMARY WEAPONS TABLE";
                    }
                    break;
                case "Skills":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('skillID') && isGiven('xp') && isGiven('skillType') && isGiven('name') && isGiven('desc') && isGiven('flav') && isGiven('minInfect') && isGiven('skillCategory')) {

                            //request should look like:
                            //username=asdf&update=Skills&skillID=#&xp=#&skillType=#&name=asdf&desc=asdf&flav=asdf&minInfect=#&skillCategory=#
                            
                            $skillID = sanitizeInt('skillID');
                            $xp = sanitizeInt('xp');
                            $skillType = sanitizeInt('skillType');
                            $name = sanitizeString('name');
                            $desc = sanitizeString('desc');
                            $flav = sanitizeString('flav');
                            $infect = sanitizeInt('minInfect');
                            $skillCategory = sanitizeInt('skillCategory');
                            
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("UPDATE Skills SET xpCost = ?, skillTypeID = ?, name = ?, desc = ?, flavor = ?, minInfect = ?, skillCategoryID = ? WHERE skillID = ?");
                                 $query->execute([$xp, $skillType, $name, $desc, $flav, $infect, $skillCategory, $skillID]);
                                 $pdo->commit();
                                 $response = "Sucessfully updated $name's entry in the Skills table";
                                 createHistoricalEvent($username, "Skill Entry Modified", "A skill entry was modified (ID of $skillID)", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            }
                        } else {
                            $response = "Please Supply all of the necessary data for the $updateEntryInTable table";
                        }
                    }
                    break;
                case "SkillPrerequisites":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        if(isGiven('base') && isGiven('prereq')) {

                            //request should look like:
                            //username=asdf&update=SkillPrerequisites&oldBase=#&oldPrereq=#&base=#&prereq=#
                            $oldBase = sanitizeInt('oldBase');
                            $oldPrereq = sanitizeInt('oldPrereq');
                            $base = sanitizeInt('base');
                            $prereq = sanitizeInt('prereq');
                                                 
                            try {
                                 $pdo->beginTransaction();
                                 $query = $pdo->prepare("UPDATE SkillPrerequisites SET baseSkillID = ?, prereqSkillID = ? WHERE baseSkillID = ? AND prereqSkillID = ?");
                                 $query->execute([$base, $prereq, $oldBase, $oldPrereq]);
                                 $pdo->commit();
                                 $response = "Sucessfully updated skill ID: $oldBase's entry in the SkillPrerequisites table";
                                 createHistoricalEvent($username, "Skill Prereqs Modified", "A skill's prerequisites were modified. Old base=$oldBase new base=$base oldPrereq=$oldPrereq new prereq=$prereq", null, null);
                            }catch (Exception $e){
                                $pdo->rollBack();
                                throw $e;
                            }
                        } else {
                            $response = "Please Supply all of the necessary data for the $updateEntryInTable table";
                        }
                    }
                    break;
                case "SkillTypes":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT UPDATE THE SKILL TYPES TABLE";
                    }
                    break;
                default:
                    $JSON = "ERROR: COULD NOT FIND SPECIFIED TABLE";
                    break;
            }
            header('Content-Type: text/plain');
            $updateStatusNum += 1;
            writeDbUpdateStatusNum($updateStatusNum);
            echo $response;
            
        } else if(isGiven('delete')) {
            $table = sanitizeString('delete');
            $response = "";
            switch ($table) {
                case "AwardWinners":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE $table TABLE";
                    }
                    break;
                case "Awards":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE $table TABLE";
                    }
                    break;
                case "CraftableObjectMaterials":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE $table TABLE";
                    }
                    break;
                case "CraftableObjectRequiredSkills":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE $table TABLE";
                    }
                    break;
                case "CraftableObjects":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE $table TABLE";
                    }
                    break;
                case "CraftableObjectsAsMaterials":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE $table TABLE";
                    }
                    break;
                case "Materials":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE $table TABLE";
                    }
                    break;
                case "HandbookENtry":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE HANDBOOK ENTRY TABLE";
                    }
                    break;
                case "SkillCategories":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE SKILL CATEGORIES TABLE";
                    }
                    break;
                case "Players":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE PLAYERS TABLE";
                    }
                    break;
                case "Characters":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE CHARACTERS TABLE";
                    }
                    break;
                case "CharacterSkills":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE CHARACTER SKILLS TABLE";
                    }
                    break;
                case "EventAttendees":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE EVENT ATTENDEES TABLE";
                    }
                    break;
                case "Events":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE EVENTS TABLE";
                    }
                    break;
                case "HistoricalEvents":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE HISTORICAL EVENTS TABLE";
                    }
                    break;
                case "Items":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE ITEMS TABLE";
                    }
                    break;
                case "PrimaryWeapons":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE PRIMARY WEAPONS TABLE";
                    }
                    break;
                case "Skills":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE SKILLS TABLE";
                    }
                    break;
                case "SkillPrerequisites":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE SKILL PREREQUISITES TABLE";
                    }
                    break;
                case "SkillTypes":
                    if(!$usernameExists) {
                        $response = "ERROR: PLAYER USERNAME DOES NOT EXIST";
                    } else {
                        $response = "ERROR: YOU MAY NOT DELETE FROM THE SKILL TYPES TABLE";
                    }
                    break;
                default:
                    $JSON = "ERROR: COULD NOT FIND SPECIFIED TABLE";
                    break;
            }
            header('Content-Type: text/plain');
            $updateStatusNum += 1;
            writeDbUpdateStatusNum($updateStatusNum);
            echo $response;
        } else {
            header('Content-Type: text/plain');
            echo "ERROR: NO COMMAND GIVEN";
        } 
    } else if(isGiven('vatnapciagr')){
        $vatnapciagr = sanitizeString('vatnapciagr');
        $fp = fopen(getVatnapciagrUrl(), "r");
        while(!feof ($fp)) {
            $line = rtrim(fgets($fp));
            if($line != ""){
               
                if($line == $vatnapciagr){
                    header('Content-Type: text/plain');
                    echo "ACCESS GRANTED";
                    createHistoricalEvent(null, "Admin Panel Login", "A player logged into the admin panel. Id isn't given.", null, null);
                    break;
                } else {
                    header('Content-Type: text/plain');
                    echo "ERROR: INCORRECT VATNAPCIAGR";
                    break;
                }
                
            }
        }
        fclose($fp);
    }else if(isGiven('sepidkwtct')){
        $sepidkwtct = sanitizeString('sepidkwtct');
        $myfile = fopen(getEventPasswordUrl(), "w") or die("ERROR: unable to open file!");
        fwrite($myfile, $sepidkwtct);
        fclose($myfile);
        header('Content-Type: text/plain');
        echo "SUCCESS!";
        createHistoricalEvent(null, "Event Password Set", "Event password successfully set to $sepidkwtct", null, null);
    } else if(isGiven('cepidkwtct')){
        $cepidkwtct = sanitizeString('cepidkwtct');
        $fp = fopen(getEventPasswordUrl(), "r");
        while(!feof ($fp)) {
            $line = rtrim(fgets($fp));
            if($line != ""){
                
                if($line == $cepidkwtct){
                    header('Content-Type: text/plain');
                    echo "ACCESS GRANTED";
                    break;
                } else {
                    header('Content-Type: text/plain');
                    echo "ERROR: INCORRECT CEPIDKWTCT";
                    break;
                }
            }
        }
        fclose($fp);
    } else if(isGiven('setIntrigue')){
        $intrigue = sanitizeString('setIntrigue');
        list($il1, $il2, $il3) = explode("~~~", $intrigue);
        $myfile = fopen(getPlayerIntrigueUrl(), "w") or die("ERROR: unable to open file!");
        fwrite($myfile, $il1."\n");
        fwrite($myfile, $il2."\n");
        fwrite($myfile, $il3);
        fclose($myfile);
        header('Content-Type: text/plain');
        echo "SUCCESS!";
        createHistoricalEvent(null, "Intrigue Messages Set", "Intrigue Messages set to 1) $il1 | 2) $il2 | 3) $il3", null, null);
    } else if(isGiven('fetchIntrigue')){
        header('Content-Type: text/plain');
        $fp = fopen(getPlayerIntrigueUrl(), "r");
        while(!feof ($fp)) {
            $line = rtrim(fgets($fp));
            if($line != ""){
                echo $line . "\n";
            }
        }
        fclose($fp);
        
    } else if(isGiven('fetchUpdateStatusNum')) {
        echo getDbUpdateStatusNum();
        
    } else if(isGiven('createPlayerPass')) {
        $cpp = sanitizeString('createPlayerPass');
        $fp = fopen(getAppPasswordUrl(), "r");
        while(!feof ($fp)) {
            $line = rtrim(fgets($fp));
            if($line != ""){
                
                if($line == $cpp){
                    header('Content-Type: text/plain');
                    echo "ACCESS GRANTED";
                    createHistoricalEvent(null, "Create Account Login", "The create account password was sucessfully typed in.", null, null);
                    break;
                } else {
                    header('Content-Type: text/plain');
                    echo "ERROR: INCORRECT createPlayerPass";
                    break;
                }
            }
        }
        fclose($fp);
    } else if(isGiven('fetchAppVersion')) {
        header('Content-Type: text/plain');
        $fp = fopen(getAppVersionUrl(), "r");
        while(!feof ($fp)) {
            $line = rtrim(fgets($fp));
            if($line != ""){
                echo $line;
            }
        }
        fclose($fp);
    } else if (isGiven('setAppVersion')) {
        $version = sanitizeString('setAppVersion');
        $myfile = fopen(getAppVersionUrl(), "w") or die("ERROR: unable to open file!");
        fwrite($myfile, $version);
        fclose($myfile);
        header('Content-Type: text/plain');
        echo "SUCCESS!";
        createHistoricalEvent(null, "App Version Set", "The checkable app version was set to $version", null, null);
    } else if (isGiven('sendContactMessage') && isGiven('name') && isGiven('email')){
        $name = sanitizeString('name');
        $email = sanitizeString('email');
        $message = sanitizeString('sendContactMessage');
        $date = getCurrentDate();
        $filename = $date . " - " . $name . " - " . getCurrentTime();
        $myfile = fopen(getMessagesDirectoryUrl() . $filename, "w") or die("ERROR: unable to open file!");
        fwrite($myfile, $message . "\n" . $email);
        fclose($myfile);
        header('Content-Type: text/plain');
        echo "SUCCESS!";
        createHistoricalEvent(null, "Contact Message Sent", "A contact message was sent from $name. email=$email. Their message was: $message", null, null);
    } else if (isGiven('getContactTitles')) {
        foreach (new DirectoryIterator(getMessagesDirectoryUrl()) as $file) {
            if ($file->isFile()) {
               echo $file->getFilename() . "\n";
            }
        }
    } else if (isGiven('getContactMessage')) {
        $filename = sanitizeString('getContactMessage');
        header('Content-Type: text/plain');
        $fp = fopen(getMessagesDirectoryUrl() . $filename, "r");
        while(!feof ($fp)) {
            $line = rtrim(fgets($fp));
            if($line != ""){
                echo $line;
            }
        }
        fclose($fp);
    } else {
        header('Content-Type: text/plain');
        echo "ERROR: NO USERNAME SPECIFIED";
    }