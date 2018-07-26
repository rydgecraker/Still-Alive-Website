<?php
/**
 * Rydge Craker
 * 7/20/2018 : 4:39 PM
 * Still-Alive-Website
 * Inputs.php
 */

function sanitizeString($input){
    return filter_input(INPUT_GET, $input, FILTER_SANITIZE_STRING);
}

function sanitizeInt($input) {
    return filter_input(INPUT_GET, $input, FILTER_SANITIZE_NUMBER_INT);
}

function isGiven($input) {
    return isset($_GET[$input]);
}