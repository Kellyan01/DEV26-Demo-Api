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

//Récupère les données d'un utilisateur via son id
function findOne($bdd,$id){
    //ATTENTION : on ne sélectionne JAMAIS le mot de passe pour une lecture publique.
    //Diffuser les hashs par l'API, c'est les offrir au bruteforce hors ligne.
    $req = $bdd->prepare('SELECT u.id, u.nom, u.prenom, u.pseudo, u.email, u.dob FROM utilisateurs u WHERE u.id = ?');

    $req->bindParam(1,$id,PDO::PARAM_INT);

    $req->execute();

    $data = $req->fetch(PDO::FETCH_OBJ);

    return $data;
}

//Récupère les données d'un utilisateur via son email
function findOneByEmail($bdd,$email){
    //ATTENTION : on ne sélectionne JAMAIS le mot de passe pour une lecture publique.
    //Diffuser les hashs par l'API, c'est les offrir au bruteforce hors ligne.
    $req = $bdd->prepare('SELECT u.id, u.nom, u.prenom, u.pseudo, u.email, u.dob FROM utilisateurs u WHERE u.email = ?');

    $req->bindParam(1,$email,PDO::PARAM_STR);

    $req->execute();

    $data = $req->fetch(PDO::FETCH_OBJ);

    return $data;
}

//Récupère les données d'un utilisateur via son pseudo
function findOneByPseudo($bdd,$pseudo){
    //ATTENTION : on ne sélectionne JAMAIS le mot de passe pour une lecture publique.
    //Diffuser les hashs par l'API, c'est les offrir au bruteforce hors ligne.
    $req = $bdd->prepare('SELECT u.id, u.nom, u.prenom, u.pseudo, u.email, u.dob FROM utilisateurs u WHERE u.pseudo = ?');

    $req->bindParam(1,$pseudo,PDO::PARAM_STR);

    $req->execute();

    $data = $req->fetch(PDO::FETCH_OBJ);

    return $data;
}

//Mettre à jour les données d'un utilisateur via son ancien email
function update($bdd,$oldEmail, $nom, $prenom, $pseudo, $email, $dob){
    //ATTENTION : on ne sélectionne JAMAIS le mot de passe pour une lecture publique.
    //Diffuser les hashs par l'API, c'est les offrir au bruteforce hors ligne.
    $req = $bdd->prepare('UPDATE utilisateurs SET nom = ?, prenom = ?, pseudo = ?, email = ?, dob = ? WHERE email = ?');

    $req->bindParam(1,$nom,PDO::PARAM_STR);
    $req->bindParam(2,$prenom,PDO::PARAM_STR);
    $req->bindParam(3,$pseudo,PDO::PARAM_STR);
    $req->bindParam(4,$email,PDO::PARAM_STR);
    $req->bindParam(5,$dob,PDO::PARAM_STR);
    $req->bindParam(6,$oldEmail,PDO::PARAM_STR);

    $req->execute();

    $data = $req->fetch(PDO::FETCH_OBJ);

    return $data;
}
