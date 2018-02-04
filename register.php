

<?php
require_once("utils.php");

print "<title>Register Best space shooter game ever</title>";

/*handles user registration, checks inputs and updates users to database*/

if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["password-repeat"])) {
    /*if two password fields match*/
    if($_POST["password"] === $_POST["password-repeat"]) {
        
        /*connect to database*/
        try {
            $db = new PDO("mysql:host=127.0.0.1;dbname=c9;charset=utf8;port:3306", "odango", "");
            $password = $_POST["password"];
    
            $stmt = $db->prepare("SELECT username FROM users WHERE username=:username");
            $stmt->execute(array(":username" => $_POST["username"]));
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        catch(Exception $e) {
            ?>
            <div class="redirect">
                <p>Failed to connect to database!</p>
            </div>
        <?php
        }
        
        /*search all usernames so it's possible to check if username exists*/
        if(count($rows) === 0) {

            if(strlen($password) < 8) {
                print "<p>Password needs to be 8 characters long!</p>";
                registerForm();
            }
            else if(strlen($password) >= 255) {
                print "<p>Password is too long</p>";
                registerForm();
            }

            /*if password fills all the requirements*/
            else if(preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $password)) {
                /*if username has only allowed characters*/
                if (preg_match('#^[a-zA-Z0-9äöüÄÖÜ]+$#', $_POST["username"])) {
                    if(strlen($_POST["username"]) >= 16) {
                        print "<p>Username is too long</p>";
                        registerForm();
                    }
                    
                    else if(strlen($_POST["username"]) == 0) {
                        print "<p>Username field is empty!</p>";
                        registerForm();
                    }
                    
                    else { /*if everything is okay!*/
                        /*hash the password*/
                        try {
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT); 
                            $stmt = $db->prepare("INSERT INTO users(username, password) VALUES(:f1, :f2)");
                            $stmt->execute(array(":f1" => $_POST["username"], ":f2" => $hashed_password));
                        }
                        
                        catch(Exception $e){
                            ?>
                            <div class="redirect">
                                <p>Failed to connect to database!</p>
                                <button type="button" class="button cancel" name="frontpage" onclick="location.href='index.php?=frontpage'">Frontpage</button>
                            </div>
                        <?php
                        }
                        
                        ?>
                        <div class="redirect">
                            <p>User created!</p>
                            <button type="button" class="button cancel" name="frontpage" onclick="location.href='index.php?=frontpage'">Frontpage</button>
                            <button type="button" class="button" name="login" onclick="location.href='index.php?p=login'">Login</button>
                        </div>
                        <?php
                    }
                }

                else {
                    print "<p>Username can only include numbers and letters!</p>";
                    registerForm();
                }

            }

            else {
                print "<p>Password must include numbers, and upper and lowercase letters!</p>";
                registerForm();
            }

        }

        else {
            print "<p>Username is taken!</p>";
            registerForm();
        }

    }

}

else {
    registerForm();
}
?>
