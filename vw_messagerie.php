<!--希望写动态页面 点一下这个用户就会出现他的信息：js代码-->
<?php
session_start();
if (!isset($_SESSION["connected_user"]) || $_SESSION["connected_user"] == "") {
    // utilisateur non connecté
    header('Location: vw_login.php');
    exit();//要先登录！！
}
if (isset($_SESSION["lastConnectionTimeStamp"])) {
    if (strtotime("now") - $_SESSION["lastConnectionTimeStamp"] > 300) {
        unset($_SESSION["connected_user"]);
        $url_redirect = 'vw_login.php';
    }
}
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Messages</title>
    <meta http-equiv="Content-Security-Policy" content="default-src 'self';">
    <link rel="stylesheet" type="text/css" media="all" href="css/mystyle.css"/>
</head>
<body>
<header>
    <!--怎么放在同一行里？-->
    <form method="POST" action="myController.php">
        <input type="hidden" name="action" value="disconnect">
        <input type="hidden" name="loginPage" value="vw_login.php?disconnect">
        <button class="btn-logout form-btn">Déconnexion</button>
    </form>
    <br/>
    <br/>
    <form method="POST" action="accueil.php">
        <input type="hidden" name="action" value="retour">
        <button class="btn-logout form-btn">Retourner</button>
    </form>

    <h2><?php echo htmlspecialchars($_SESSION["connected_user"]["prenom"]); ?> <?php echo htmlspecialchars($_SESSION["connected_user"]["nom"]); ?>
        - Messages reçus</h2>

</header>

<section>
    <article>

        <div class="liste">
            <table>
                <tr>
                    <th>Expéditeur</th>
                    <th>Sujet</th>
                    <th>Message</th>
                </tr>
                <?php foreach ($_SESSION['messagesRecus'] as $cle => $message) {
//                htmlentities把php变量转换成字符实体 ?>
                    <tr>
                        <td><?= $message['nom'] ?> <?= $message['prenom'] ?></td>
                        <td><?= htmlentities($message['sujet_msg'], ENT_QUOTES) ?></td>
                        <td><?= htmlentities($message['corps_msg'], ENT_QUOTES) ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>

    </article>
    <article>
        <form method="POST" action="myController.php">
            <input type="hidden" name="action" value="sendmsg">
            <div class="fieldset">
                <div class="fieldset_label">
                    <span>Envoyer un message</span>
                </div>
                <div class="field">
                    <label>Destinataire : </label>
                    <select name="to">
                        <!--listeUsers结构是：id_user:user(也是个array/dict所有用户键值信息)-->
                        <?php
                        if ($_SESSION["connected_user"]['profil_user'] == 'EMPLOYE')
                            $visible = 'CLIENT';
                        elseif ($_SESSION["connected_user"]['profil_user'] == 'CLIENT')
                            $visible = 'EMPLOYE';
                        foreach ($_SESSION['listeUsers'] as $id => $user) {
                            if ($user["profil_user"] == $visible) { ?>
                                <option value="<?= $id ?>"><?= $user['nom'] ?> <?= $user['prenom'] ?></option>
                            <?php }
                        }
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label>Sujet : </label><input type="text" size="20" name="sujet">
                </div>
                <div class="field">
                    <label>Message : </label><textarea name="corps" cols="25" rows="3""></textarea>
                </div>
                <button class="form-btn">Envoyer</button>
                <?php
                if (isset($_REQUEST["msg_ok"])) {
                    echo '<p>Message envoyé avec succès.</p>';
                }
                ?>
            </div>
        </form>
    </article>
</section>
</body>
</html>
