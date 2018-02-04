
<?php
require_once("utils.php");
print "<title>Login Best space shooter game ever</title>";

/*handles user login*/

if(isset($_POST["username"]) && isset($_POST["password"])) { /*if fields are set*/
    
    try {
        $db = new PDO("mysql:host=127.0.0.1;dbname=c9;charset=utf8;port:3306", "odango", "");
    
        $stmt = $db->prepare("SELECT * FROM users WHERE username=:username");
        $stmt->execute(array(":username" => $_POST["username"]));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    catch(Exception $e) {
        ?>
        <div class="redirect">
            <p>Failed to connect to database!</p>
            <button type="button" class="button cancel" name="frontpage" onclick="location.href='index.php?=frontpage'">Frontpage</button>
        </div>
    <?php
    }

    if(count($rows) === 1) { /*if username is found*/
        $password_hashed = $rows[0]["password"];
        $password = $_POST["password"];
        if(password_verify($password, $password_hashed)) { /*if password is correct*/

            $_SESSION["userId"] = $rows[0]["id"];
            $_SESSION["username"] = $rows[0]["username"];
            ?>
            <div class="redirect">
                <p>Login was succesful.</p>
                <button type="button" class="button cancel" name="frontpage" onclick="location.href='index.php?=frontpage'">Frontpage</button>
                <button type="button" class="button" name="game" onclick="location.href='index.php?p=game'">Play!</button>
            </div>
            <?php
        }

        else {
            print "<p>Login failed! Check username and password.</p>";
            loginForm();
        }
    }

    else {
        print "<p>Login failed!</p>";
        loginForm();
    }
}

else {
loginForm();
}
?>
