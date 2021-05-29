<?php
session_start();
if (!isset($_SESSION["connected_user"]) || $_SESSION["connected_user"] == "") {
    // utilisateur non connecté
    header('Location: vw_login.php');
    exit();
}
if (isset($_SESSION["lastConnectionTimeStamp"])) {
    if ((strtotime("now") - $_SESSION["lastConnectionTimeStamp"]) > 300) {
        unset($_SESSION["connected_user"]);
        $url_redirect = 'vw_login.php';
    }
}
?>
<!--怎么一下插入好多个dom-->
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche_client</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/mystyle.css"/>
    <style>
        .user .hidden {
            display: none;
        }

        .user:hover .hidden {
            display: block !important;
        }
    </style>
</head>
<body>
<header>
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
    <h1>Fiche_client</h1>
</header>

<script type="text/javascript">
    var keyword = "123";
    <?php
    $t2 = "<script type='text/javascript'>document.write(t1)</script>";
    //    echo "$t2";
    $keyword = "<script>document.writeln(keyword);</script>";//php获取js的变量！！
    //    echo "$keyword";
    ?>
    function toggle(id) {
        const infos = document.getElementById('user-' + id);
        console.log([...infos.classList]);
        if (infos.classList.contains('hidden'))
            infos.classList.remove('hidden');
        else
            infos.classList.add('hidden');
    }
</script>


<section>
    <div id="affiche">
        <div class="form">
            liste de clients:
            <!--foreach (iterable_expression as $key => $value) 所以id是键 user是值-->
            <!--或者只有值：foreach (iterable_expression as $value)-->
            <!--需要在php的循环/判断里的html就必须用echo拼了:字符串用''包裹 php变量用..包裹即可:其实就要插的地方'.xxx.'即可-->
            <?php //                echo '<button  onclick="affiche_client('.$id.')" value="' . $id . '">' . $user['nom'] . ' ' . $user['prenom'] . '</button>';
            ?>
            <?php foreach ($_SESSION['listeUsers'] as $id => $user) { ?>
                <div class="user">
                    <button onclick="toggle.bind(null, '<?= $id ?>')"
                            value="<?= $id ?>"><?= $user['nom'] ?> <?= $user['prenom'] ?></button>
                    <table id="user-<?= $id ?>" class="hidden">
                        <tr><td>id_user:<?= $user["id_user"]; ?></td></tr>
                        <tr><td>nom:<?= $user["nom"]; ?></td></tr>
                        <tr><td>prenom:<?= $user["prenom"]; ?></td></tr>
                        <tr><td>profil_user:<?= $user["profil_user"]; ?></td></tr>
                        <tr><td>numero_compte:<?= $user["numero_compte"]; ?></td></tr>
                        <tr><td><a href="virement_depuis_fiche_client.php?id=<?=$id?>">effectuer un virement du compte de <?= $user['nom'] ?> <?= $user['prenom'] ?></a></td></tr><!--ajouté-->

                    </table>
                </div>
            <?php } ?>
        </div>
    </div>

    <?php
    if (isset($_REQUEST["nullvalue"])) {
        echo '<p class="errmsg">Merci de saisir votre login et votre mot de passe</p>';
    } else if (isset($_REQUEST["badvalue"])) {
        echo '<p class="errmsg">Votre login/mot de passe est incorrect</p>';
    } else if (isset($_REQUEST["disconnect"])) {
        echo '<p>Vous avez bien été déconnecté.</p>';
    }
    ?>
</section>

</body>
</html>
