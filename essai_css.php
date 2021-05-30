<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Connexion</title>
    <link rel="stylesheet" type="text/css" media="all" href="css/mystyle.css"/>
</head>
<body>
<header>
    <h1>Connexion</h1>
</header>

<section>
    <div class="login-page">
        <div class="form">
            <form method="POST" action="myController.php">
                <input type="hidden" name="action" value="authenticate">
                <input type="text" name="login" placeholder="login"/>
                <input type="password" name="mdp" placeholder="mot de passe"/>
                <button>login</button>
            </form>
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
<style type="text/css">
    body {

    }
</style>

<!--js调用php-->


<script>
    var user = 100;
    userId = document.getElementByIdx_x("userId").value;
    alert(userId);
</script>

<script type="text/javascript">
    var t1 = 123;
    <?php
    $t2 = "<script type='text/javascript'>document.write(t1)</script>";
    ?>

</script>

</body>
</html>
