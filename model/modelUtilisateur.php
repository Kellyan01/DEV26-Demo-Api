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

//Récupère un utilisateur grâce à son id
function findOne($bdd, $id){
    $req = $bdd->prepare('SELECT u.id, u.nom, u.prenom, u.pseudo, u.email, u.dob FROM utilisateurs u WHERE u.id = ?');

    $req->bindParam(1,$id,PDO::PARAM_INT);

    $req->execute();

    $data = $req->fetch(PDO::FETCH_OBJ);

    return $data;
}

//Récupère un utilisateur grâce à son pseudo
function findOneByPseudo($bdd, $pseudo){
    $req = $bdd->prepare('SELECT u.id, u.nom, u.prenom, u.pseudo, u.email, u.dob FROM utilisateurs u WHERE u.pseudo = ?');

    $req->bindParam(1,$pseudo,PDO::PARAM_STR);

    $req->execute();

    $data = $req->fetch(PDO::FETCH_OBJ);

    return $data;
}

//Récupère un utilisateur grâce à son email
function findOneByMail($bdd, $mail){
    //Ici on garde le mdp : cette fonction servira AUSSI à la connexion (password_verify).
    //C'est un bon exemple : une même table se lit différemment selon l'usage.
    $req = $bdd->prepare('SELECT u.id, u.nom, u.prenom, u.pseudo, u.email, u.dob FROM utilisateurs u WHERE u.email = ?');

    $req->bindParam(1,$mail,PDO::PARAM_STR);

    $req->execute();

    $data = $req->fetch(PDO::FETCH_OBJ);

    return $data;
}

//Récupère le mot de passe grâce à son pseudo
function findPasswordByPseudo($bdd, $pseudo){
    //Ici on garde le mdp : cette fonction servira AUSSI à la connexion (password_verify).
    //C'est un bon exemple : une même table se lit différemment selon l'usage.
    $req = $bdd->prepare('SELECT u.pseudo, u.mdp FROM utilisateurs u WHERE u.pseudo = ?');

    $req->bindParam(1,$pseudo,PDO::PARAM_STR);

    $req->execute();

    $data = $req->fetch(PDO::FETCH_OBJ);

    return $data;
}

//Enregistrer un nouvel utilisateur
function add($bdd, $pseudo, $email, $mdp, $nom = NULL, $prenom = NULL, $dob = NULL){
    $req = $bdd->prepare('INSERT INTO utilisateurs (nom, prenom, dob, pseudo, email, mdp) VALUES (?,?,?,?,?,?)');
    
    //bindParam accepte NULL : PARAM_STR suffit, pas besoin de PDO::PARAM_NULL
    $req->bindParam(1,$nom,PDO::PARAM_STR);
    $req->bindParam(2,$prenom,PDO::PARAM_STR);
    $req->bindParam(3,$dob,PDO::PARAM_STR);
    //On bind le reste des champs obligatoires
    $req->bindParam(4,$pseudo,PDO::PARAM_STR);
    $req->bindParam(5,$email,PDO::PARAM_STR);
    $req->bindParam(6,$mdp,PDO::PARAM_STR);

    //Exécution de la requête.
    //ATTENTION : connect() configure PDO en ERRMODE_EXCEPTION. Dans ce mode, execute()
    //ne renvoie JAMAIS false : en cas d'erreur il LEVE une exception. C'est donc le
    //try/catch de la route qui la traitera, et pas un if sur la valeur de retour.
    $req->execute();
}

//Modifier un utilisateur à partir de son ancien email
function update($bdd, $oldEmail, $pseudo, $email, $nom, $prenom, $dob){
    $req = $bdd->prepare('UPDATE utilisateurs SET nom = ?, prenom = ?, dob = ?, pseudo = ?, email = ? WHERE email = ?');
    
    $req->bindParam(1,$nom,PDO::PARAM_STR);
    $req->bindParam(2,$prenom,PDO::PARAM_STR);
    $req->bindParam(3,$dob,PDO::PARAM_STR);
    $req->bindParam(4,$pseudo,PDO::PARAM_STR);
    $req->bindParam(5,$email,PDO::PARAM_STR);
    $req->bindParam(6,$oldEmail,PDO::PARAM_STR);

    //Exécution de la requête.
    $req->execute();
}

//Supprimer un utilisateur à partir de son pseudo
function delete($bdd, $pseudo){
    $req = $bdd->prepare('DELETE FROM utilisateurs WHERE pseudo = ?');
    
    $req->bindParam(1,$pseudo,PDO::PARAM_STR);

    //Exécution de la requête.
    $req->execute();
}
