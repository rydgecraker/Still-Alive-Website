<?php
/**
 * Rydge Craker
 * 7/21/2018 : 9:22 AM
 * Still-Alive-Website
 * PlayersTableFunctions.php
 */

function usernameExists($pdo, $username) {
    $query = $pdo->prepare('SELECT * FROM Players WHERE username = ?');
    $query->execute([$username]);
    while($row = $query->fetch()) {
        return true;
    }
    return false;
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