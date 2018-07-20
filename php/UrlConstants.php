<?php
/**
 * Rydge Craker
 * 7/20/2018 : 4:42 PM
 * Still-Alive-Website
 * UrlConstants.php
 */

function getParentDirectory() {
    return "../../";
}

function getAppSecuritySubDirectory() {
    return getParentDirectory() . "AppSecurity/";
}
function getOtherSubDirectory() {
    return getParentDirectory() . "Other/";
}
function getEventSubDirectory() {
    return getParentDirectory() . "EventRelatedStuff/";
}
function getAdminSubDirectory() {
    return getParentDirectory() . "AdminStuff/";
}
function getDatabaseSubDirectory() {
    return getParentDirectory() . "databaseCred/";
}

function getDbStatusUrl() {
    return getOtherSubDirectory() . "dbstatus.txt";
}
function getAppVersionUrl() {
    return getOtherSubDirectory() . "appVersion.txt";
}
function getPlayerIntrigueUrl() {
    return getOtherSubDirectory() . "playerIntrigue.txt";
}
function getMessagesDirectoryUrl() {
    return getOtherSubDirectory() . "messages/";
}
function getReadMessagesDirectoryUrl() {
    return getOtherSubDirectory() . "readMessages.txt";
}

function getAppPasswordUrl() {
    return getAppSecuritySubDirectory() . "appPassword.txt";
}

function getEventPasswordUrl() {
    return getEventSubDirectory() . "eventPassword.txt";
}

function getVatnapciagrUrl(){
    return getAdminSubDirectory() . "vatnapciagr.txt";
}