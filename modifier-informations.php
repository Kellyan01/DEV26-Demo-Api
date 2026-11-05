<?php
//La route pour modifier un utilisateur via son ancien email
//Elle sera en méthode PUT

//inclure les ressources
include './env.php';
include './utils/functions.php';
include './model/modelUtilisateur.php';


// I. Mise en place des header()
//Controle de l'origin : Qui a le droit de demander service : ici TOUT LE MONDE
header("Access-Control-Allow-Origin: *");

// Format des données envoyées : Dans quelle langue je parle : ici en JSON, avec caractères encodés en UTF-8
header("Content-Type: application/json; charset=UTF-8");

// Méthodes autorisées : Quel est le but de la route : ici lire des données
header("Access-Control-Allow-Methods: PUT, OPTIONS");

// Durée de vie de l'autorisation du preflight
header("Access-Control-Max-Age: 3600");

// Entêtes autorisées pour le Client : qu'est-ce que le client a le droit de m'envoyer
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//I. Tester les Méthodes
//Tester OPTIONS pour l'envoie de l'autorisation (réponse au preflight)
if($_SERVER["REQUEST_METHOD"] === "OPTIONS"){
    //Envoie de l'autorisation (204)
    http_response_code(204);
    exit;
}

//Tester la methode PUT
if($_SERVER["REQUEST_METHOD"] === "PUT"){
    //Vérifier les données envoyées

    //Récupérer les données entrante
    $json = file_get_contents("php://input");

    //Décodage du json
    $put = json_decode($json);

    //Normaliser les données
    $nom = trim($put->nom);
    $prenom = trim($put->prenom);
    $pseudo = trim($put->pseudo);
    $dob = trim($put->dob);
    $email = trim($put->email);
    $oldEmail = trim($put->oldEmail);

    //Vérifier les champs vide
    if(!empty($nom) && !empty($prenom) && !empty($pseudo) && !empty($dob) && !empty($email) && !empty($oldEmail) ){
        //Vérifier le format des emails
        if(filter_var($email,FILTER_VALIDATE_EMAIL) && filter_var($oldEmail,FILTER_VALIDATE_EMAIL) ){
            try{
                //Créer l'objet PDO
                $pdo = connect($_ENV['dbhost'], $_ENV['dbname'],$_ENV['dblogin'],$_ENV['dbpassword']);

                //1. Vérifier si un compte existe pour l'ancien email
                $data = findOneByEmail($pdo,$email);
                //2. Vérifier si le nouvel email est disponible :
                    // - soit il correspond à l'ancien email
                    // - soit il ne correspond à aucun compte existant
                if($email === $oldEmail || empty($data)){
                    //3. Vérifier si le nouveau pseudo est disponible :
                        // - il ne correspond à aucunn compte existant
                        // - il est identite à celui du compte existant
                    $dataPseudo = findOneByPseudo($pdo,$pseudo);
                    if($pseudo === $data->pseudo || empty($dataPseudo) || $oldEmail === $dataPseudo->pseudo){
                        //Lancement de la mise à jour
                        update($pdo,$oldEmail, $nom, $prenom, $pseudo, $email, $dob);

                        //Envoyer le message
                        http_response_code(200);
                        echo(json_encode(["code"=>200,"message"=>"Mise à jour effectué avec succès"]));
                        exit;

                    }else{
                        //Erreur : Conflict (409)
                        http_response_code(409);
                        echo(json_encode(["code"=>409,"message"=>"Ce pseudo est indisponible"]));
                        exit;
                    }
                }else{
                    //Erreur : Conflict (409)
                    http_response_code(409);
                    echo(json_encode(["code"=>409,"message"=>"Email indisponible"]));
                    exit;
                }

            }catch(Exception $error){
                //Erreur : Problème Server (500)
                http_response_code(500);
                echo(json_encode(["code"=>500, "message"=>$error->getMessage()]));
                exit;
            }

        }else{
            //Erreur : Mauvais Requête (400)
            http_response_code(400);
            echo(json_encode(["code"=>400, "message"=>"Mauvaise Requête. Les emails ne sont pas au bon format."]));
            exit;
    }

    }else{
        //Erreur : Mauvais Requête (400)
        http_response_code(400);
        echo(json_encode(["code"=>400, "message"=>"Mauvaise Requête. Aucune donnée ne doit être vide"]));
        exit;
    }

}else{
    //Cas d'erreur : Méthode non autorisée 405
    http_response_code(405);
    echo(json_encode(["code"=>405,"message"=>"Méthode non autorisée"]));
    exit;
}
