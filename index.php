<?php
    session_start();
    require 'php/DBConnect.php';
    require 'php/Inputs.php';
    require 'php/DateTimeFunctions.php';
    require 'php/security/PlayersTableFunctions.php';

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(isPosted("username") && isPosted("password")){
            if(usernameExists(getPDO(), sanitizePostString("username"))){
                $username = sanitizePostString("username");
                $password = sanitizePostString("password");
                $_SESSION["username"] = $username;
                $_SESSION["password"] = $password;
                $usernameExists = true;
            } else {
                $usernameExists = false;
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Still Alive Post-Apocalyptic Larp</title>
        <meta charset="utf-8">
        <link href="css/still_alive.css" rel="stylesheet">
    </head>
    <body>
        <header>
            <div id="loginFormDiv">
                <form id="loginForm" method="post">
                    <p class="loginFormPiece">Username: <input type="text" name="username"> <br>
                    <p class="loginFormPiece">Password: <input type="password" name="password"> <br></p>
                    <div id="loginSubmitButton" class="loginFormPiece">
                        <input type="submit" value="Create Account"> <input type="submit" value="Log In">
                    </div>
                </form>
            </div>
            <div id="loginInfo">
                <div id="loginPlaceholderDiv"></div>
                <p id="logInBtn">Log In</p>
            </div>
        </header>
        <div id ="columns">
            <div id="leftColumn">
                <div id="navigation">
                    <nav>
                        <ul>
                            <li id="currentLink"><a href="index.php">Home</a></li>
                            <li><a href="events.php">Events</a></li>
                            <li><a href="story.php">The Story</a></li>
                            <li><a href="about.php">About the Creators</a></li>
                            <li><a href="contact.php">Contact Us</a></li>
                        </ul>
                    </nav>
                </div>
                <div id="carrotFormatter">
                    <div id="openCloseCarrot">
                        <p><</p>
                    </div>
                </div>

            </div>
            <div id="rightColumn">
                <img id="stillAliveTitleImage" src="images/PageAssets/stillAliveTitleRed.png">
                <main>
                    <div id="homepageIntro">
                        <h1 id="underConstruction">Welcome new recruits! Unfortunately, this website is still under construction. Check back later!</h1>
                    </div>
                </main>
                <foot>
                    <footer>
                        <small><br>Still Alive Larp &copy; 2018 | <a href="https://www.facebook.com/groups/1694035254163754/">Facebook</a><br><br></small>
                    </footer>
                </foot>
            </div>
        </div>
    </body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
</html>