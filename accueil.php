<!--开头必须要写这个 不然拿不到session变量-->
<?php
session_start();

?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Accueil</title>
    <link rel="stylesheet" type="text/css" media="all"  href="css/mystyle.css" />
</head>
<body>
<header>
    <h1>Accueil</h1><br>
    <form method="POST" action="myController.php">
        <input type="hidden" name="action" value="disconnect">
        <input type="hidden" name="loginPage" value="vw_login.php?disconnect">
        <button class="btn-logout form-btn">Déconnexion</button>
    </form>
    <!--怎么获取到用户信息：数据全都存在session或者request里面 在控制台在session变量里存储了connected_user(数据库查询得到utlisateur那一整条记录的所有表项信息)-->
<!--    在php里echo html语句可能复杂一点 需要'bbb'.xxx.'aaa'
但在html里插php就很简单 直接插进去就行-->
<!--    <ul>-->
<!--        id_user:<li>--><?php //echo $_SESSION["connected_user"]["id_user"];?><!--</li>-->
<!--        login:<li>--><?php //echo $_SESSION["connected_user"]["login"];?><!--</li>-->
<!--        nom:<li>--><?php //echo $_SESSION["connected_user"]["nom"];?><!--</li>-->
<!--        prenom:<li>--><?php //echo $_SESSION["connected_user"]["prenom"];?><!--</li>-->
<!--        profil_user:<li>--><?php //echo $_SESSION["connected_user"]["profil_user"];?><!--</li>-->
<!--        numero_compte:<li>--><?php //echo $_SESSION["connected_user"]["numero_compte"];?><!--</li>-->
<!--    </ul>-->
</header>



<article>
    <div class="fieldset">
        <div class="fieldset_label">
            <span>Vos informations personnelles</span>
        </div>
        <div class="field">
            <label>Login : </label><span><?php echo $_SESSION["connected_user"]["login"];?></span>
        </div>
        <div class="field">
            <label>Id_user : </label><span><?php echo $_SESSION["connected_user"]["id_user"];?></span>
        </div>
        <div class="field">
            <label>Nom : </label><span><?php echo $_SESSION["connected_user"]["nom"];?></span>
        </div>
        <div class="field">
            <label>Prenom : </label><span><?php echo $_SESSION["connected_user"]["prenom"];?></span>
        </div>
        <div class="field">
            <label>Profil : </label><span><?php echo $_SESSION["connected_user"]["profil_user"];?></span>
        </div>
        <div class="field">
            <label>Numero_compte : </label><span><?php echo $_SESSION["connected_user"]["numero_compte"];?></span>
        </div>
    </div>
</article>


<!--限制不同用户不同的链接 全部都先去controller 然后回再给你返回回来的 这样就都是没经过controller的-->
<p><a href="myController.php?action=msglist&userid=<?php echo $_SESSION["connected_user"]["id_user"];?>" target="_blank">Messagerie</a></p>
<p><a href="vw_moncompte.php?userid=<?php echo $_SESSION["connected_user"]["id_user"];?>" target="_blank">effectuer un virement</a></p>
<?php if ($_SESSION["connected_user"]["profil_user"]=='EMPLOYE')
    echo '<p><a href="myController.php?action=afficheclient&userid='.$_SESSION["connected_user"]["id_user"].'" target="_blank">Fiche client</a></p>';
  ?>


</body>
</html>
