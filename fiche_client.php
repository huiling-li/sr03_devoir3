<?php
session_start();
?>
<!--怎么一下插入好多个dom-->
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Fiche_client</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/mystyle.css"/>
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
    $t2="<script type='text/javascript'>document.write(t1)</script>";
//    echo "$t2";
    $keyword="<script>document.writeln(keyword);</script>";//php获取js的变量！！
//    echo "$keyword";
    ?>

    function affiche_client(id) {
        var div = document.getElementById("affiche");
        var p = document.createElement("p");
        var id1=id;
        <?php $id1= "\"+id+\"";
//                echo $id1;
        ?>
        var t2 = Number("<?php echo $_SESSION["connected_user"]["id_user"];?>");
        var data= "<?php echo $_SESSION["listeUsers"]['1']['prenom'];?>";

        console.log(data,t2,typeof t2,typeof data);

        var txt = document.createTextNode(
           " id_user:<?php echo $_SESSION["connected_user"]["id_user"];?>
        //login:<?php //echo $_SESSION["listeUsers"][$id]["login"];?>
        //login:<?php //echo $t2;?>
            nom:<?php echo $_SESSION["connected_user"]["nom"];?>
                prenom:<?php echo $_SESSION["connected_user"]["prenom"];?>
                    profil_user:<?php echo $_SESSION["connected_user"]["profil_user"];?>
                        numero_compte:<?php echo $_SESSION["connected_user"]["numero_compte"];?>");
                            //好像不加""就直接找不到函数了？
        p.appendChild(txt);
        div.appendChild(p);
    }
</script>


<section>
    <div id="affiche">
        <div class="form">
            liste de clients:
            <!--foreach (iterable_expression as $key => $value) 所以id是键 user是值-->
            <!--或者只有值：foreach (iterable_expression as $value)-->
            <!--需要在php的循环/判断里的html就必须用echo拼了:字符串用''包裹 php变量用..包裹即可:其实就要插的地方'.xxx.'即可-->
            <?php
            foreach ($_SESSION['listeUsers'] as $id => $user) {
                echo '<button  onclick="affiche_client('.$id.')" value="' . $id . '">' . $user['nom'] . ' ' . $user['prenom'] . '</button>';
            }
            ?>

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
