<?php
require_once('myModel.php');

session_start();

// URL de redirection par défaut (si pas d'action ou action non reconnue)
$url_redirect = "index.php";

if (isset($_REQUEST['action'])) {

//    if (ipIsBanned($_SERVER['REMOTE_ADDR'])){//remote_addr这个不用自己传 只要喊他工作就行获取ip地址？
//        // cette IP est bloquée
//        $url_redirect = "vw_login.php?ipbanned";}
    if ($_REQUEST['action'] == 'authenticate') {
        /* ======== AUTHENT ======== */
        if (!isset($_REQUEST['login']) || !isset($_REQUEST['mdp']) || $_REQUEST['login'] == "" || $_REQUEST['mdp'] == "") {
            // manque login ou mot de passe
            $url_redirect = "vw_login.php?nullvalue";

        } else {

            //把login里特殊字符去掉
            $car_interdits = array("'", "\"", ";", "%"); // une liste de caractères que je choisis dinterdire
            $utilisateur = findUserByLoginPwd(str_replace($car_interdits, "", $_REQUEST['login']), str_replace($car_interdits, "", $_REQUEST['mdp']), $_SERVER['REMOTE_ADDR']);

            if ($utilisateur == false) {
                // echec authentification
                $url_redirect = "vw_login.php?badvalue";

            } else {
                // authentification réussie
                $_SESSION["connected_user"] = $utilisateur;//就是在这里把user所有信息存入session变量里
                $_SESSION["listeUsers"] = findAllUsers();
                $url_redirect = "accueil.php";
            }
        }

    } else if ($_REQUEST['action'] == 'disconnect') {
        /* ======== DISCONNECT ======== */
//          unset($_SESSION["connected_user"]);
//        session_unset();
        session_destroy();
        //session里所有键值清空 而不是只清除connected_user
        $url_redirect = 'vw_login.php';//faut corriger

    } else if ($_REQUEST['action'] == 'transfert') {
        /* ======== TRANSFERT ======== */
        if (!isset($_REQUEST['mytoken']) || $_REQUEST['mytoken'] != $_SESSION['mytoken']) {
            // echec vérification du token (ex : attaque CSRF) 因为是自己设置的 别人如果没有这个值或者不是这个值 就是黑客咯
            $url_redirect = "vw_moncompte.php?err_token";
        } else if (is_numeric($_REQUEST['montant'])) {
            if ($_REQUEST['montant'] > $_SESSION["connected_user"]["solde_compte"])
                $url_redirect = "vw_moncompte.php?not_enough=" . $_REQUEST['montant'];//控制了不能超过你有的钱
            elseif ($_REQUEST['montant'] < 0)
                $url_redirect = "vw_moncompte.php?negative=" . $_REQUEST['montant'];//控制了不能超过你有的钱
            else {
                transfert($_REQUEST['destination'], $_SESSION["connected_user"]["numero_compte"], $_REQUEST['montant']);
                $_SESSION["connected_user"]["solde_compte"] = $_SESSION["connected_user"]["solde_compte"] - $_REQUEST['montant'];
                $url_redirect = "vw_moncompte.php?trf_ok";
            }
        } else {
            $url_redirect = "vw_moncompte.php?bad_mt=" . $_REQUEST['montant'];
        }
    } else if ($_REQUEST['action'] == 'sendmsg') {
        /* ======== MESSAGE ======== */
        addMessage($_REQUEST['to'], $_SESSION["connected_user"]["id_user"], $_REQUEST['sujet'], $_REQUEST['corps']);
        $url_redirect = "vw_messagerie.php?msg_ok";

    } else if ($_REQUEST['action'] == 'msglist') {
        /* ======== MESSAGE ======== */
        $_SESSION['messagesRecus'] = findMessagesInbox($_REQUEST["userid"]);
        $url_redirect = "vw_messagerie.php";


    } else if ($_REQUEST['action'] == 'afficheclient') {
        $url_redirect = "fiche_client.php";
    }
    else if ($_REQUEST['action'] == 'transfert_depuis_fiche_client') {
        $url_redirect = "virement_depuis_fiche_client.php?id=".$_REQUEST["id"];
    }


}

header("Location: $url_redirect");

?>
