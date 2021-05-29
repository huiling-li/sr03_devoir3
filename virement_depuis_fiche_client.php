<?php
session_start();
if(!isset($_SESSION["connected_user"]) || $_SESSION["connected_user"] == "") {
    // utilisateur non connecté
    header('Location: vw_login.php');
    exit();
}

$mytoken = bin2hex(random_bytes(128)); // token va servir à prévenir des attaques CSRF
$_SESSION["mytoken"] = $mytoken;
?>

<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Mon Compte</title>
    <link rel="stylesheet" type="text/css" media="all"  href="css/mystyle.css" />
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
    <h2><?php echo $_SESSION["listeUsers"][$_REQUEST["id"]]["prenom"];?> <?php echo $_SESSION["listeUsers"][$_REQUEST["id"]]["nom"];?> - Mon compte</h2>
</header>

<section>

    <article>
        <div class="fieldset">
            <div class="fieldset_label">
                <span>Vos informations personnelles</span>
            </div>
            <div class="field">
                <label>Login : </label><span><?php echo $_SESSION["listeUsers"][$_REQUEST["id"]]["login"];?></span>
            </div>
            <div class="field">
                <label>Profil : </label><span><?php echo $_SESSION["listeUsers"][$_REQUEST["id"]]["profil_user"];?></span>
            </div>
        </div>
    </article>

    <article>
        <div class="fieldset">
            <div class="fieldset_label">
                <span>Votre compte</span>
            </div>
            <div class="field">
                <label>N° compte : </label><span><?php echo$_SESSION["listeUsers"][$_REQUEST["id"]]["numero_compte"];?></span>
            </div>
            <div class="field">
                <label>Solde : </label><span><?php echo $_SESSION["listeUsers"][$_REQUEST["id"]]["solde_compte"];?> &euro;</span>
            </div>
        </div>
    </article>

    <article>
        <form method="POST" action="myController.php">
            <input type="hidden" name="action" value="transfert_depuis_fiche_client">
            <input type="hidden" name="id" value="<?=$_SESSION["listeUsers"][$_REQUEST["id"]]?>">
            <input type="hidden" name="mytoken" value="<?php echo $mytoken; ?>">
            <div class="fieldset">
                <div class="fieldset_label">
                    <span>Transférer de l'argent</span>
                </div>
                <div class="field">
                    <label>N° compte destinataire : </label>
                    <!--                  <input type="text" size="20" name="destination">-->
                    <select name="destination">
                        <!--listeUsers结构是：id_user:user(也是个array/dict所有用户键值信息)-->
                        <?php
                        foreach ($_SESSION['listeUsers'] as $id => $user) {
                            if($user['numero_compte']!=$_SESSION["listeUsers"][$_REQUEST["id"]]['numero_compte'])
                                echo '<option name="destination" value="'.$user['numero_compte'].'">'.$user['numero_compte'].'</option>';
//                              控制了转账对象！！
                        }
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label>Montant à transférer : </label><input type="text" size="10" name="montant">
                </div>
                <button class="form-btn">Transférer</button>
                <?php
                if (isset($_REQUEST["err_token"])) {
                    echo '<p>Echec virement : le contrôle d\'intégrité a échoué.</p>';
                }
                if (isset($_REQUEST["trf_ok"])) {
                    echo '<p>Virement effectué avec succès.</p>';
                }
                if (isset($_REQUEST["not_enough"])) {
                    echo "<p>Votre solde n'est pas assez, veuillez saisir un montant inférieur à ".$_SESSION["connected_user"]["solde_compte"].".</p>";
                }
                if (isset($_REQUEST["negative"])) {
                    echo "<p>Veuillez saisir un montant positive!</p>";
                }
                //              if (isset($_REQUEST["bad_mt"])) {
                //                echo '<p>Le montant saisi est incorrect : '.$_REQUEST["bad_mt"].'</p>';
                //              }
                if (isset($_REQUEST["bad_mt"])) {
                    echo '<p>Le montant saisi est incorrect : '.htmlentities($_REQUEST["bad_mt"], ENT_QUOTES).' 
                    veuillez saisir un numéro!
                  </p>';
//               跟直接$_REQUEST["bad_mt"]没啥区别 只不过加了个 ENT_QUOTES标志 - 可以编码双引号和单引号。
                }
                ?>
            </div>
        </form>
    </article>

</section>

</body>
</html>
