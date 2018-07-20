<?php
/**
 * Rydge Craker
 * 7/20/2018 : 4:38 PM
 * Still-Alive-Website
 * DateTimeFunctions.php
 */

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