<?php
include_once('function.php');
$cle = "toto";
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Information</title>
</head>

<body>
    <a href="index.php">Accueil</a>
    <table>
        <tr>
            <th>Login</th>
            <th>Password</th>
        </tr>
        <?php
        $db = getDataBase();
        $sql = $db->query("SELECT * FROM user");
        $users = $sql->fetchAll();
        foreach ($users as $user) {

            $login = openssl_decrypt($user['login'], "AES-128-ECB", $cle);
            $email = openssl_decrypt($user['email'], "AES-128-ECB", $cle);

            echo "<tr>";
            echo "<td>" . $login . "</td>";
            echo "<td>" . $email . "</td>";
            echo "<td>" . $user['password'] . "</td>";
            echo "<td><img src=\"images/" . $user['photo'] . "\"</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>

</html>