<?php

/*This is the place where all the needed functions for creating a page is*/

function siteHeader() {
    
    /*site header contains header information and page title and is in charge
    of displaying page loader*/
    
    ?>
    <!doctype html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="This is the best 8-bit space shooter ever. It's a part of Webbed Application course. 
        Goal is to understand the basic principles of making own web pages using HTML, JavaScript, PHP and frameworks such as Phaser.io.
        Hope you enjoy my colorful and fun game!">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
        <link href="https://fonts.googleapis.com/css?family=Faster+One" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Press+Start+2P" rel="stylesheet">
        <!-- Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <!-- Optional theme -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
        <!-- Latest compiled and minified JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
        <script type="text/javascript" src="js/phaser.min.js"></script>
        <script type="text/javascript" src="comment.js"></script>
        <link rel="stylesheet" href="style.css" type="text/css">
    </head>
    
    <body>
        <div id="loader">
            <!-- page loader -->
        	<div id="squaresWaveG_1" class="squaresWaveG"></div>
        	<div id="squaresWaveG_2" class="squaresWaveG"></div>
        	<div id="squaresWaveG_3" class="squaresWaveG"></div>
        	<div id="squaresWaveG_4" class="squaresWaveG"></div>
        	<div id="squaresWaveG_5" class="squaresWaveG"></div>
        	<div id="squaresWaveG_6" class="squaresWaveG"></div>
        	<div id="squaresWaveG_7" class="squaresWaveG"></div>
        	<div id="squaresWaveG_8" class="squaresWaveG"></div>
        	<script type="text/javascript">
                $(window).load(function() {
                    /*will fade out the loader when page has fully loaded*/
            		$("#loader").delay(500).fadeOut('slow');
            		$(".mainpage").delay(500).fadeIn('slow');
            	});
            </script>
        </div>
        <div class="mainpage" style="display:none">
        <div id="header" class="borders sitewidth">
            <h1>The best space shooter ever</h1>
        </div>
    <?php
}

function headerButtons() {
    
    /*menu buttons for site, changes a bit depending on if user has logged in or not*/
    
    ?>
    <div class="buttons sitewidth">
    <?php if(isset($_SESSION["username"])) { /*if user has logged in*/
        print "<p>Signed in: <strong>{$_SESSION['username']}</strong></p>";?>
        <div class="topbuttons">
            <button type="button" class="button" onclick="location.href='index.php?p=logout'">Log Out</button>
            <button type="button" class="button" name="game" onclick="location.href='index.php?p=game'">Play!</button>
        </div>
        <?php
    }

    else { /*if user has on logged in*/
        print "<p>Register or login!</p>";?>
        <div class="topbuttons">
            <button type="button" class="button" name="login" onclick="location.href='index.php?p=login'">Login</button>
            <button type="button" class="button" name="register" onclick="location.href='index.php?p=register'">Sign Up</button>
            <button type="button" class="button" name="game" onclick="location.href='index.php?p=game'">Play!</button>
        </div>
        <?php
    }
    ?>
    </div>
    <?php
}

function commentBox() {
    
    /*displays and handles comment box on front page*/
    
    ?>
    <div class="borders sitewidth" style="margin-bottom: 5px; margin-top: 5px;"><p>Shoot the asteroids and became the greatest pilot in the Universe!
    Submit your scores and compare them with other players! Register today so you get the best nick and
    possibility to comment with a name!</p></div>
    <div id="commentbox">
        <?php
        /*connect to database and fetch comments*/
        try {
            $db = new PDO("mysql:host=127.0.0.1;dbname=c9;charset=utf8;port:3306", "odango", ""); //connect to database
            $stmt = $db->prepare("SELECT * FROM comments");
            $stmt->execute();
            while ($comment = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $user = $comment['username'];
                $date = $comment['date'];
                echo '<div class="comment borders"><b class="username">' . $user . ': </b>' . $comment['comment'];
                echo '<p class="under">' . date("F j, Y, g:i a", strtotime($date));
                echo '</p></div>';
            }
        }
        
        catch(Exception $e) { /*error handling*/
            echo '<div class="comment borders"><p> Error has occured in database!</p></div>';
        }
        
        ?>
        
    </div>
    <script>scrollBottom();</script>
    <form autocomplete="off" class="forms sitewidth" id="commentform">
        <?php 
        if(isset($_SESSION["username"])) { /*if user has logged in*/
            print "Commenting as <strong>{$_SESSION['username']}</strong>:<br>";
        } 
        else { /*commenting as anonymous if user has not logged in*/
            print "Comment as Anonymous:<br>";
        }
        ?>
            <input type="text" class="inputField" id="commentinput" name="comment" value="" placeholder="Comment...">
            <input type="button" class="button" id="commentbtn" style="float:left; margin-top:5px;" value="Comment">
    </form>
    <script>/*listeners*/submitComment(); enterListener();</script>
    <?php
}

function buttonsGame() {
    
    /*menu buttons for game site, works the same way as buttonsHeader() only one
    button directs to different page (frontpage instead of gamepage)*/
    
    ?>
    <div class="buttons sitewidth">
    <?php if(isset($_SESSION["username"])) {
        print "<p>Signed in: <strong>{$_SESSION['username']}</strong></p>";?>
        <div class="topbuttons">
            <button type="button" class="button" onclick="location.href='index.php?p=logout'">Log Out</button>
            <button type="button" class="button" name="frontpage" onclick="location.href='index.php?p=frontpage'">Frontpage</button>
        </div>
        <?php
    }

    else {
        print "<p>Register or login!</p>";?>
        <div class="topbuttons">
            <button type="button" class="button" name="login" onclick="location.href='index.php?p=login'">Login</button>
            <button type="button" class="button" name="register" onclick="location.href='index.php?p=register'">Sign Up</button>
            <button type="button" class="button" name="frontpage" onclick="location.href='index.php?=frontpage'">Frontpage</button>
        </div>
        <?php
    }
    ?>
    </div>
    <?php
}

function registerForm() {
    
    /*register form used in user register*/
    
    print <<<REGISTERFORM
        <form autocomplete="off" class="forms" id="signupform" action="index.php?p=register" method="post">
            <div class="container">
                <label><b>Username</b></label><br>
                <input type="text" class="formfield" placeholder="Enter Username" name="username" required>
                <br>
                <label><b>Password</b></label><br>
                <input type="password" class="formfield" placeholder="Enter Password" name="password" required>
                <br>
                <label><b>Repeat Password</b></label><br>
                <input type="password" class="formfield" placeholder="Repeat Password" name="password-repeat" required>
            </div>
            <div class="container">
                <button type="button" class="button cancel" name="cancel" onclick="location.href='index.php?=frontpage'">Cancel</button>
                <button type="submit" class="button" name="register" id="signup">Sign Up</button>
            </div>
        </form>
REGISTERFORM;
}

function loginForm() {
    
    /*login form used in user login*/
    
    print <<<LOGINFORM
    <form autocomplete="off" class="forms" id="loginform" action="index.php?p=login" method="post">
        <div class="container">
            <label><b>Username</b></label><br>
            <input type="text" placeholder="Enter Username" name="username" required>
            <br>
            <label><b>Password</b></label><br>
            <input type="password" placeholder="Enter Password" name="password" required>
            <br>
        </div>
        <div class="container">
            <button type="button" class="button cancel" name="cancel" onclick="location.href='index.php?=frontpage'">Cancel</button>
            <button type="submit" name="login" class="button">Login</button>
        </div>
    </form>
LOGINFORM;
}

function gameArea() {
    
    /*creates a game area or if the device does not support it a notification
    about not supporting the game*/
    
    ?>
        <div id="wrapper" class="sitewidth">
            <script type="text/javascript" src="js/game.js"></script>
            <div class="hidden-xs borders" id="gamearea">
                <div id="dialog" class="borders" title="Submit Score!" style="display:none;">
                    <form autocomplete="off" class="forms" id="scoreform">
                    	<p class="tips">Input your name</p>
                    	Name:<br>
                    	<input class="inputField" type="text" name="player" value="">
                    	<br><br>
                        <p id="score"></p>
                    	<input class="button" id="submitbtn" type="button" value="OK" style="float: right">
                	</form>
                </div>
            </div>
            <div id="gameutils">
                <!-- for small devices -->
                <div class="visible-xs borders">
                    <p class="mobileinfo">Game works only on desktop!
                    You can still leave comment on frontpage and the fun on desktop!</p>
                </div>
                <div id="controls" class="borders">
                    <p>Move: Arrow keys<br>
                    Primary Weapon: X<br>
                    Special Weapon: Z
                    Pause: ESC</p>
                </div>

    <?php
}

function highScores() {
    ?>
            <div id="highscores">
                <p>Highscores:</p>
                <table class="borders">
                    <?php
                    print '<tr class="titles"><th>RANK</th><th>USER</th><th>SCORE</th></tr>';
                    try {
                        $db = new PDO("mysql:host=127.0.0.1;dbname=c9;charset=utf8;port:3306", "odango", ""); //connect to database
                        $stmt = $db->prepare("SELECT username, score, date FROM highscores ORDER BY score DESC LIMIT 10");
                        $stmt->execute();
                        $rank = 1;
                        while ($scores = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $name = $scores['username'];
                            $score = $scores['score'];
                            print '<tr><th>' . $rank . '</th><th>' . $name . '</th><th>' . $score . '</th></tr>';
                            $rank = $rank+1;
                        }
                    }
                    
                    catch(Exception $e) { /*error handling*/
                        echo '<tr><th><p> Error has occured in database!</th></tr>';
                    }
                    
                    ?>
                </table>
            </div>
        </div>
    </div>

<?php
}

function footer() {
    
    /*for displaying footer*/
    
    ?>
        <div id="sharebuttons" class="borders sitewidth">
            <!-- sosial media share buttons -->
                <ul class="share-buttons">
                    <li>Share: </li>
                    <li><a href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fspacegame-odango.c9users.io%2Fpeli%2Findex.php&t=" title="Share on Facebook" target="_blank" onclick="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(document.URL) + '&t=' + encodeURIComponent(document.URL)); return false;"><img alt="Share on Facebook" src="icons/social_flat_rounded_rects_svg/Facebook.svg" /></a></li>
                    <li><a href="https://twitter.com/intent/tweet?source=https%3A%2F%2Fspacegame-odango.c9users.io%2Fpeli%2Findex.php&text=:%20https%3A%2F%2Fspacegame-odango.c9users.io%2Fpeli%2Findex.php" target="_blank" title="Tweet" onclick="window.open('https://twitter.com/intent/tweet?text=' + encodeURIComponent(document.title) + ':%20'  + encodeURIComponent(document.URL)); return false;"><img alt="Tweet" src="icons/social_flat_rounded_rects_svg/Twitter.svg" /></a></li>
                    <li><a href="https://plus.google.com/share?url=https%3A%2F%2Fspacegame-odango.c9users.io%2Fpeli%2Findex.php" target="_blank" title="Share on Google+" onclick="window.open('https://plus.google.com/share?url=' + encodeURIComponent(document.URL)); return false;"><img alt="Share on Google+" src="icons/social_flat_rounded_rects_svg/Google+.svg" /></a></li>
                    <li><a href="http://www.tumblr.com/share?v=3&u=https%3A%2F%2Fspacegame-odango.c9users.io%2Fpeli%2Findex.php&t=&s=" target="_blank" title="Post to Tumblr" onclick="window.open('http://www.tumblr.com/share?v=3&u=' + encodeURIComponent(document.URL) + '&t=' +  encodeURIComponent(document.title)); return false;"><img alt="Post to Tumblr" src="icons/social_flat_rounded_rects_svg/Tumblr.svg" /></a></li>
                    <li><a href="http://www.reddit.com/submit?url=https%3A%2F%2Fspacegame-odango.c9users.io%2Fpeli%2Findex.php&title=" target="_blank" title="Submit to Reddit" onclick="window.open('http://www.reddit.com/submit?url=' + encodeURIComponent(document.URL) + '&title=' +  encodeURIComponent(document.title)); return false;"><img alt="Submit to Reddit" src="icons/social_flat_rounded_rects_svg/Reddit.svg" /></a></li>
                    <li><a href="mailto:?subject=&body=:%20https%3A%2F%2Fspacegame-odango.c9users.io%2Fpeli%2Findex.php" target="_blank" title="Send email" onclick="window.open('mailto:?subject=' + encodeURIComponent(document.title) + '&body=' +  encodeURIComponent(document.URL)); return false;"><img alt="Send email" src="icons/social_flat_rounded_rects_svg/Email.svg" /></a></li>
                </ul>
        </div>
    </div>
</body>
</html>
    
<?php
}
?>
