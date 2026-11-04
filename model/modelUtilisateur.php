<?php
//Récupère la liste des utilisateurs en BDD
function findAll($bdd){
    //ATTENTION : on ne sélectionne JAMAIS le mot de passe pour une lecture publique.
    //Diffuser les hashs par l'API, c'est les offrir au bruteforce hors ligne.
    $req = $bdd->prepare('SELECT u.id, u.nom, u.prenom, u.pseudo, u.email, u.dob FROM utilisateurs u');

    $req->execute();

    $data = $req->fetchAll(PDO::FETCH_OBJ);

    return $data;
}
