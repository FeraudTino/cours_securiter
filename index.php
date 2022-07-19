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
    <title>Document</title>
</head>

<body>
    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="login" placeholder="login">
        <input type="password" name="password" placeholder="password">
        <input type="email" name="email" placeholder="email">
        <input type="file" name="photo" value="" />
        <br>
        <label for="inscription">Inscription</label>
        <input type="submit" name="inscription" id="inscription">
    </form>

    <a href="info.php">Voir les informations</a>
</body>

</html>
<?php
if (isset($_POST['inscription'])) {

    $login = $_POST['login'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    $loginCrypte = openssl_encrypt($login, "AES-128-ECB", $cle);
    $emailCrypte = openssl_encrypt($email, "AES-128-ECB", $cle);

    $password = hash('sha256', $password);
    $photo = $_FILES['photo']['name'];
    define('TARGET', 'images/');    //Repertoire cible
    define('MAX_SIZE', 100000000);  //Taille max en octets du fichier
    define('WIDTH_MAX', 100000);    //Largeur max de l'image en pixels
    define('HEIGHT_MAX', 8000000);  //Hauteur max de l'image en pixels
    $tabExt = array('jpg', 'gif', 'png', 'jpeg');    // Extensions autorisees
    $infosImg = array();

    // On verifie l'extension du fichier
    $extension = strtolower(substr(strrchr($_FILES['photo']['name'], '.'), 1));
    if (!in_array($extension, $tabExt)) {
        $erreur = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg, txt ou doc...';
    } else {
        // On verifie la taille du fichier
        if ($_FILES['photo']['size'] > MAX_SIZE) {
            $erreur = 'Le fichier est trop gros...';
        } else {
            // On verifie que le type MIME est bien image/png, image/gif, image/jpeg
            if (!in_array($_FILES['photo']['type'], array('image/png', 'image/gif', 'image/jpeg'))) {
                $erreur = 'Le fichier doit être au format jpg, gif ou png...';
            } else {
                $date = date('Y-m-d_H-i-s');
                $nom = $date . '_' . $_FILES['photo']['name'];
                $nomImage = hash('sha256', $nom);
                $nomImage = $nomImage . '.' . $extension;
                // On copie le fichier dans le dossier de destination
                if (move_uploaded_file($_FILES['photo']['tmp_name'], TARGET . $nomImage)) {
                    $infosImg = getimagesize(TARGET . $nomImage);

                    $db = getDataBase();
                    $sql = $db->prepare("INSERT INTO user (login, email, password, photo) VALUES (:login, :email, :password, :photo)");
                    $sql->execute(array(
                        'login' => $loginCrypte,
                        'email' => $emailCrypte,
                        'password' => $password,
                        'photo' => $nomImage
                    ));
                    $erreur = 'Votre compte a bien été créé !';
                } else {
                    $erreur = 'Impossible de copier le fichier...';
                }
            }
        }
    }
}
?>