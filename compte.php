<?php
//La route pour récupérer les données d'un utilisateur grâce à son id
//Elle sera en méthode GET

//inclure les ressources
include './env.php';
include './utils/functions.php';
include './model/modelUtilisateur.php';

//I. Mise en place des entêtes
//Controle de l'origin : Qui a le droit de demander service : ici TOUT LE MONDE
header("Access-Control-Allow-Origin: *");

// Format des données envoyées : Dans quelle langue je parle : ici en JSON, avec caractères encodés en UTF-8
header("Content-Type: application/json; charset=UTF-8");

// Méthodes autorisées : Quel est le but de la route : ici lire des données
header("Access-Control-Allow-Methods: GET");

// Entêtes autorisées pour le Client : qu'est-ce que le client a le droit de m'envoyer
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//II. Vérifier la méthode de la requête HTTP
if($_SERVER["REQUEST_METHOD"] === "GET"){
    //On reçoit une donnée de la part du client. On doit donc controller l'$id
    //Normaliser l'id : on le fait avant de vérifier si l'id est vide, car ainsi, si l'id n'est composé que d'espace, il en sera nettoyé, deviendra vide, et sera alors bloqué par la prochaine vérification
    $id = trim($_GET['id']);

    //Vérifier si l'id est vide ou qu'il n'est pas un entier naturel
    if($id !== '' && filter_var($id,FILTER_VALIDATE_INT)){
        //Lancer la récupération des données
        try{
            //Créer l'objet de connexion PDO
            $pdo = connect($_ENV['dbhost'], $_ENV['dbname'],$_ENV['dblogin'],$_ENV['dbpassword']);

            //Lance la requête depuis le model
            $data = findOne($pdo,$id);

            //Vérifier si un utilisateur existe avec cet id
            if(!empty($data)){
                //Envoyer la réponse (200)
                http_response_code(200);
                echo(json_encode(["code"=>200,"user"=>$data]));
                exit;

            }else{
                //Erreur : Aucune donnée trouvée (404)
                http_response_code(404);
                echo(json_encode(["code"=>404, "message"=>"Aucun utilisateur trouvé."]));
                exit;
            }

        }catch(Exceptio $error){
            //Erreur : Problème Server (500)
            http_response_code(500);
            echo(json_encode(["code"=>500, "message"=>$error->getMessage()]));
            exit;
        }

    }else{
        //Erreur : Mauvais Requête (400)
        http_response_code(400);
        echo(json_encode(["code"=>400, "message"=>"Mauvaise Requête. Envoyez un id valide"]));
        exit;
    }


}else{
    //Cas d'erreur : Méthode non autorisée 405
    http_response_code(405);
    echo(json_encode(["code"=>405,"message"=>"Méthode non autorisée"]));
    exit;
}