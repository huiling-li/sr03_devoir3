<?php


/*
INJECTION SQL
	http://172.17.0.2/unsafe/myController.php?action=authenticate&login=johnd&mdp='or '1'='1
	Connexion avec le login 'johnd' sans mot de passe

	FIX : Utiliser `$mysql->escape_string` pour chaque paramètre

XSS
    http://172.17.0.2/unsafe/myController.php?action=sendmsg&to=1&sujet=XSS&corps=<script>alert(document.cookie);</script>
    Affichage du cookie de session (pourrait etre un envoie sur un autre serveur aussi)

    FIX : Utiliser `htmlspecialchars` pour éviter l'évaluation des scripts dans le navigateur

VIOLATION CONTROLE ACCÈS
    http://172.17.0.2/unsafe/myController.php?action=msglist&userid=1
    Affiche les messages d'un utilisateur sans meme vérifier que l'utilisateur connecté
    est bien le destinataire des messages consultés

    FIX : Vérifier la présence et la bonne valeur de `$_SESSION["connected_user"]` à chaque opération sensible

VIOLATION GESTION SESSION
    On récupère le cookie PHPSESSID (via XSS par exemple)
    La session n'est pas détruite lors d'un déconnexion, ainsi lorsque l'utilisateur se reconnecte,
    la session est de nouveau valide

    RÉDUCTION DE LA FAILLE : Supprimer la session à chaque déconnexion avec `session_destroy()`
    RÉDUCTION DE LA FAILLE : Réduire la durée de validité des session (configuration serveur)
    RÉDUCTION DE LA FAILLE : Configuer le cookie de session en httpOnly (configuration serveur)

FALSIFICATION REQUETES (CSRF)
    http://172.17.0.2/unsafe/myController.php?action=transfert&destination=1337&montant=-1000
    Vole 1000€ au compte numéro 1337 puisqu'un décompte négatif revient à un ajout de valeur

    FIX : Vérifier que le montant est bien positif

VULNERABILITÉ COMPOSANT
    Mot de passe et utilisateurs par défaut pour la base de données

    RÉDUCTION DE LA FAILLE : Utiliser un utilisateur dédié avec le minumum d'accès au SGBD pour le serveur, avec
    un mot de passe généré aléatoirement et très long

CHIFFREMENT DE DONNÉES SENSIBLES
    Pas de HTTPS : une interception du traffic réseau donne accès aux identifiants lors de la connexion et
    au cookie de session
    Les mots de passe sont stokés en clairs dans la base de données, n'importe quel accès à la base révèle
    les mots de passe de tous les utilisateurs

    FIX : Utiliser HTTPS partout
    FIX : Utiliser une fonction de hachage comme SHA-256 pour les mots de passe

ACCÈS AU RÉPARTOIRE PAR HTTP
    http://172.17.0.2/unsafe/config/config.ini
    Affiche la configuration de la base de données (adresse IP, nom et mot de passe de l'utilisateur)

    FIX 1 : Mettre un fichier .htaccess avec `deny from all` pour les serveurs Apache
    FIX 2 (mieux) : Déplacer le fichier `config/config.ini` hors du dossier servi par le serveur HTTP

SCRIPT DE REDIRECTION
    http://172.17.0.2/unsafe/myController.php?action=disconnect&loginPage=http://example.com
    Affiche le site 'example.com' alors que l'URL indique '172.17.0.2'

    FIX : Ne jamais passer une valeur entrée par l'utilisateur à l'en-tete `Location`