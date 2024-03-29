<?php

function getMySqliConnection()
{
    $db_connection_array = parse_ini_file("config/config.ini");
    return new mysqli($db_connection_array['DB_HOST'], $db_connection_array['DB_USER'], $db_connection_array['DB_PASSWD'], $db_connection_array['DB_NAME']);
}

function ipIsBanned($ip)
{//ip地址自动获取
    $mysqli = getMySqliConnection();

    if ($mysqli->connect_error) {
        trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error, E_USER_ERROR);
        return false;
    } else {
        $stmt = $mysqli->prepare("select count(*) as nb_tentatives from connection_errors where ip=?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        if ($count > 4) {
            return true; // cette IP a atteint le nombre maxi de 5 tentatives infructueuses
        } else {
            return false;
        }
        $mysqli->close();
    }
}

function findUserByLoginPwd($login, $pwd, $ip)
{
    $mysqli = getMySqliConnection();

    if ($mysqli->connect_error) {
        trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error, E_USER_ERROR);
        $utilisateur = false;
    } else {
        // Pour faire vraiment propre, on devrait tester si le prepare et le execute se passent bien
        $stmt = $mysqli->prepare("select nom,prenom,login,id_user,numero_compte,profil_user,solde_compte from users where login=? and mot_de_passe=?");
        $stmt->bind_param("ss", $login, $pwd); // on lie les paramètres de la requête préparée avec les variables
        $stmt->execute();
        $stmt->bind_result($nom, $prenom, $username, $id_user, $numero_compte, $profil_user, $solde_compte); // on prépare les variables qui recevront le résultat
        if ($stmt->fetch()) {
            // les identifiants sont corrects => on renvoie les infos de l'utilisateur
            $utilisateur = array("nom" => $nom,
                "prenom" => $prenom,
                "login" => $username,
                "id_user" => $id_user,
                "numero_compte" => $numero_compte,
                "profil_user" => $profil_user,
                "solde_compte" => $solde_compte);
        } else {
            // les identifiants sont incorrects
            $utilisateur = false;

            // on log l'IP ayant généré l'erreur
            $stmt_insert = $mysqli->prepare("insert into connection_errors(ip,error_date) values(?,CURTIME())");
            $stmt_insert->bind_param("s", $ip); // Eventuellement, gérer le cas où l'utilisateur on est derrière un proxy en utilisant $_SERVER['HTTP_X_FORWARDED_FOR']
            $stmt_insert->execute();
            $stmt_insert->close();
        }
        $stmt->close();

        $mysqli->close();
    }

    return $utilisateur;
}

//
//function findUserByLoginPwd($login, $pwd) {
//  $mysqli = getMySqliConnection();
//
//  if ($mysqli->connect_error) {//基本错误处理 不用管
//      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
//      $utilisateur = false;
//  } else {
//      $req="select nom,prenom,login,id_user,numero_compte,profil_user,solde_compte from users where login='$login' and mot_de_passe='$pwd'";
//      if (!$result = $mysqli->query($req)) {//query执行sql语句 然后错误判断
//          echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
//          $utilisateur = false;
//      } else {
//          if ($result->num_rows === 0) {
//            $utilisateur = false;
//          } else {
//            $utilisateur = $result->fetch_assoc();
//          }
//          $result->free();
//      }
//      $mysqli->close();
//  }
//
//  return $utilisateur;//拿到用户 里面有各种键值对信息
//}


function findAllUsers()
{
//    返回的listeUsers结构是：id_user:user(也是个array/dict所有用户键值信息)
    $mysqli = getMySqliConnection();

    $listeUsers = array();//类似dict

    if ($mysqli->connect_error) {
        echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error;
    } else {
        $req = "select * from users";//这里没选要profil_user的信息肯定就获取不到呀
        if (!$result = $mysqli->query($req)) {
            echo 'Erreur requête BDD [' . $req . '] (' . $mysqli->errno . ') ' . $mysqli->error;
        } else {
            while ($unUser = $result->fetch_assoc()) {
                $listeUsers[$unUser['id_user']] = $unUser;
            }
            $result->free();
        }
        $mysqli->close();
    }

    return $listeUsers;
}


//function transfert($dest, $src, $mt) {
//    $mysqli = getMySqliConnection();
//
//    if ($mysqli->connect_error) {
//        trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error, E_USER_ERROR);
//        $utilisateur = false;
//    } else {
//        // Pour faire vraiment propre, on devrait tester si le execute et le prepare se passent bien
//        $stmt = $mysqli->prepare("update users set solde_compte=solde_compte+? where numero_compte=?");
//        $stmt->bind_param("ds", $mt, $dest); // on lie les paramètres de la requête préparée avec les variables
//        $stmt->execute();
//        $stmt->close();
//
//        $stmt = $mysqli->prepare("update users set solde_compte=solde_compte-? where numero_compte=?");
//        $stmt->bind_param("ds", $mt, $src); // on lie les paramètres de la requête préparée avec les variables
//        $stmt->execute();
//        $stmt->close();
//
//        $mysqli->close();
//    }
//
//    return $utilisateur;
//}
//
function transfert($dest, $src, $mt)
{
    $mysqli = getMySqliConnection();
    echo 'transfert了';
    if ($mysqli->connect_error) {
        echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error;
        $utilisateur = false;
    } else {
        $req = "update users set solde_compte=solde_compte+$mt where numero_compte='$dest'";
        if (!$result = $mysqli->query($req)) {
            echo 'Erreur requête BDD [' . $req . '] (' . $mysqli->errno . ') ' . $mysqli->error;
        }
        $req = "update users set solde_compte=solde_compte-$mt where numero_compte='$src'";
        if (!$result = $mysqli->query($req)) {
            echo 'Erreur requête BDD [' . $req . '] (' . $mysqli->errno . ') ' . $mysqli->error;
        }
        $mysqli->close();
    }

    return $utilisateur;
}


function findMessagesInbox($userid)
{
    $mysqli = getMySqliConnection();

    $listeMessages = array();

    if ($mysqli->connect_error) {
        trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error, E_USER_ERROR);
    } else {
        // Pour faire vraiment propre, on devrait tester si le prepare et le execute se passen bien
        $stmt = $mysqli->prepare("select id_msg,sujet_msg,corps_msg,u.nom,u.prenom from messages m, users u where m.id_user_from=u.id_user and id_user_to=?");
        $stmt->bind_param("i", $userid); // on lie les paramètres de la requête préparée avec les variables
        $stmt->execute();
        $stmt->bind_result($id_msg, $sujet_msg, $corps_msg, $nom, $prenom); // on prépare les variables qui recevront le résultat
        while ($stmt->fetch()) {
            $unMessage = array("id_msg" => $id_msg, "sujet_msg" => $sujet_msg, "corps_msg" => $corps_msg, "nom" => $nom, "prenom" => $prenom);
            $listeMessages[$id_msg] = $unMessage;
        }
        $stmt->close();

        $mysqli->close();
    }

    return $listeMessages;
}

//function findMessagesInbox($userid) {
//  $mysqli = getMySqliConnection();
//
//  $listeMessages = array();
//
//  if ($mysqli->connect_error) {
//      echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error;
//  } else {
//      $req="select id_msg,sujet_msg,corps_msg,u.nom,u.prenom from messages m, users u where m.id_user_from=u.id_user and id_user_to=".$userid;
//      if (!$result = $mysqli->query($req)) {
//          echo 'Erreur requête BDD ['.$req.'] (' . $mysqli->errno . ') '. $mysqli->error;
//      } else {
//          while ($unMessage = $result->fetch_assoc()) {
//            $listeMessages[$unMessage['id_msg']] = $unMessage;//id：信息集合 的字典形式存储数据
//          }
//          $result->free();
//      }
//      $mysqli->close();
//  }
//
//  return $listeMessages;
//}


function addMessage($to, $from, $subject, $body)
{
    $mysqli = getMySqliConnection();

    if ($mysqli->connect_error) {
        echo 'Erreur connection BDD (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error;
    } else {
        $req = "insert into messages(id_user_to,id_user_from,sujet_msg,corps_msg) values($to,$from,'$subject','$body')";
        if (!$result = $mysqli->query($req)) {
            echo 'Erreur requête BDD [' . $req . '] (' . $mysqli->errno . ') ' . $mysqli->error;
        }
        $mysqli->close();
    }

}

//function addMessage($to,$from,$subject,$body) {
//    $mysqli = getMySqliConnection();
//
//    if ($mysqli->connect_error) {
//        trigger_error('Erreur connection BDD (' . $mysqli->connect_errno . ') '. $mysqli->connect_error, E_USER_ERROR);
//    } else {
//        // Pour faire vraiment propre, on devrait tester si le execute et le prepare se passent bien
//        $stmt = $mysqli->prepare("insert into messages(id_user_to,id_user_from,sujet_msg,corps_msge) values(?,?,?,?)");
//        $stmt->bind_param("iiss", $to,$from,$subject,$body); // on lie les paramètres de la requête préparée avec les variables
//        $stmt->execute();
//        $stmt->close();
//
//        $mysqli->close();
//    }
//
//}

?>
