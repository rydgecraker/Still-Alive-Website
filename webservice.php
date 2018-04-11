<?php
    ini_set('display_errors', 1); 
    error_reporting(-1);
        /* require the user as the parameter */
    if(isset($_GET['user']) && intval($_GET['user'])) {

                    /*** mysql hostname ***/
         $hostname = 'localhost';

         /*** mysql username ***/
         $username = 'root';

         /*** mysql password ***/
         $password = 'stillalive';

         try {
             $dbh = new PDO("mysql:host=$hostname;dbname=StillAlive", $username, $password);
             /*** echo a message saying we have connected ***/
             echo 'Connected to database';
             }
         catch(PDOException $e)
             {
             echo $e->getMessage();
             }
    }
?> 