<?php  
    $host = "sql307.infinityfree.com";
    $dbname = "if0_35483179_adatok";
    $user = "if0_35483179";
    $password_db = "N1xWCbRKNV";

    $backgroundColor = "cornflowerblue";
    $wrongPassword = false;
    $usernameExists = true;

    $conn = new mysqli($host, $user, $password_db, $dbname);

    function decrypt($encryptedData) {
        $keys = array( 5, -14, 31, -9, 3 );

        $encyptedDataArr = explode(PHP_EOL, $encryptedData);
        $decryptedText = array();

        for($z = 0; $z < 6; $z++) {
            $currEncryptedDataLength = strlen($encyptedDataArr[$z]);
            $currDecryptingData = $encyptedDataArr[$z];

            for ($i = 0; $i < $currEncryptedDataLength; $i++) {
                $character = $currDecryptingData[$i];
                $j = $i % 5;
                $decryptedText[$z] .= chr((ord($character) - $keys[$j] - 32 + 95) % 95 + 32);
            }
           }
        return $decryptedText;
    }

    function authenticateUser($username, $password, $passwordFilePath) {
        global $wrongPassword, $usernameExists;
        $encryptedPasswords = file_get_contents($passwordFilePath);

        $decryptedPasswords = decrypt($encryptedPasswords);

        foreach ($decryptedPasswords as $decryptedPassword) {
            list($storedUsername, $storedPassword) = explode('*', $decryptedPassword, 2);

            if ($username === $storedUsername) {
                if ($password === $storedPassword) {
                    return true; // Successful authentication
                } else {
                    $wrongPassword = true;
                    return false; // Wrong password
                }
            }
        }

    $usernameExists = false;
    return false; // Username not found
}


    function getFavoriteColor($username, $conn) {
        $query = "SELECT Titkos FROM tabla WHERE Username = '$username'";
        $result = $conn->query($query);

        if (!$result) {
            echo "Error: " . $conn->error;
            return null;
        }

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['Titkos'];
        }
        return null;
    }   

    if (isset($_POST['submit'])) {
        if (isset($_POST['name']) && isset($_POST['password'])) {
            $username = $_POST['name'];
            $password = $_POST['password'];

            if (authenticateUser($username, $password, 'password.txt')) {
                $favoriteColor = getFavoriteColor($username, $conn);

                if ($favoriteColor == "piros") {
                    $backgroundColor = "red";
                } else if ($favoriteColor == "zold") {
                    $backgroundColor = "green";
                } else if ($favoriteColor == "sarga") {
                    $backgroundColor = "yellow";
                } else if ($favoriteColor == "kek") {
                    $backgroundColor = "blue";
                } else if ($favoriteColor == "fekete") {
                    $backgroundColor = "black";
                } else if ($favoriteColor == "feher") {
                    $backgroundColor = "white";
                }
            } else if ($wrongPassword) {
                echo '<script>alert("Hibás jelsó.");</script>';
                echo '<script>
                        setTimeout(function(){
                            window.location.href = "https://www.police.hu/";
                        }, 3000);
                    </script>';
            } else if (!$usernameExists) {
                echo '<script>alert("Nincs ilyen felhasználó");</script>';
                echo '<script>
                        setTimeout(function(){
                            window.location.href = "https://www.police.hu/";
                        }, 3000);
                    </script>';
            }
        } else {
            echo "Invalid form data.";
        }
    }
?>

<!DOCTYPE html>

<html lang="hu">
    <head>
        <title>Bejelentkezés</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="index.css">
        <style>
            body {
                background-color: <?php echo $backgroundColor; ?>;
            }
        </style>
    </head>
    <body>
        <div class="login_center">
            <div class="login_body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
                    <label for="name">Email:</label><br>
                    <input type="text" id="name" name="name" class="username" placeholder="Adja meg az email címét"><br><br>
                    <label for="name">Jelszó:</label><br>
                    <input type="password" id="password" name="password" class="password" placeholder="Adja meg a jelszavát"><br><br>
                    <input type="checkbox" onclick="showPassword()"><a class="check_button">Mutasd a jelszót</a>
                    <input type="submit" name="submit" value="Bejelentkezés" class="login_button">
                </form>
            </div>
        </div>

        <script>
            function showPassword() {
                let x = document.getElementById("password");
                if (x.type === "password") {
                    x.type = "text";
                } else {
                    x.type = "password";
                }
            }
        </script>
    </body>
</html>