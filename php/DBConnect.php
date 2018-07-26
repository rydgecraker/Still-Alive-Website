<?php
/**
 * Rydge Craker
 * 7/20/2018 : 4:39 PM
 * Still-Alive-Website
 * DBConnect.php
 */

require 'UrlConstants.php';

function readFromDbCredsFolder($filename) {
    $fp = fopen(getDatabaseSubDirectory() . $filename, "r");
    $response = "";
    while(!feof ($fp)) {
        $line = rtrim(fgets($fp));
        if($line != ""){
            $response .= $line;
        }
    }
    fclose($fp);
    return $response;
}

function setUpPDO(){
    $host = readFromDbCredsFolder("host.txt");
    $db   = readFromDbCredsFolder("db.txt");
    $user = readFromDbCredsFolder("user.txt");
    $pass = readFromDbCredsFolder("pass.txt");
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, $user, $pass, $opt);
}

function callQuery($pdo, $query) {
    try {
        return $pdo->query($query);
    } catch (Exception $ex) {
        throw $ex;
    }
}