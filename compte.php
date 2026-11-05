<?php
//La route pour récupérer la liste d'un utilisateur via son id
//Elle sera en méthode GET

//inclure les ressources
include './env.php';
include './utils/functions.php';
include './model/modelUtilisateur.php';

// I. Mise en place des entêtes
//CORS origin
header("Access-Control-Allow-Origin: *");

//Format de la réponse au client
header("Content-Type: application/json; charset=UTF-8");

// Méthodes autorisées : Quel est le but de la route : ici lire des données
header("Access-Control-Allow-Methods: GET");

// Entêtes autorisées pour le Client : qu'est-ce que le client a le droit de m'envoyer
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// II. Vérifier la methode HTTP utilisé par la requête cliente
if($_SERVER['REQUEST_METHOD'] === 'GET'){
    //Normaliser la donnée (id) envoyée : supprimer les espaces
    //On le fait avant de vérifier les champs, car ainsi, si le champ n'est composé que d'espace, trim va en faire un champ vide, et la vérification suivante pourra bloquer la donnée envoyée. Alors que l'inverse laissera le champ vide passer.
    $id = normalize($_GET['id']);

    //Vérifier si l'id est vide
    //Comme en SQL les id ne sont pas égaux à 0, on n'a pas besoin de prévoir ce cas là
    if($id != '' && filter_var($id,FILTER_VALIDATE_INT)){
        try{
            //RECUPERATION DES DONNEES DE L'UITILSATEUR
            //Créer l'objet PDO
            $pdo = connect($_ENV['dbhost'],$_ENV['dbname'],$_ENV['dblogin'],$_ENV['dbpassword']);

            //Appel au model pour trouver l'utilisateur
            $data = findOne($pdo, $id);

            //Envoyer la réponse
            http_response_code(200);
            echo(json_encode(["code"=>200, "user"=>$data]));
            exit;
        }catch(Exception $error){
            //Erreur du Serveur 
            http_response_code(500);
            echo(json_encode(["code"=>500,"message"=>$error->getMessage()]));
            exit;
        }
    }else{
        //Cas d'erreur
        http_response_code(400);
        echo(json_encode(["code"=>400, "message"=>"Vous devez envoyer un identifiant valide."]));
        exit;
    }

}else{
    //Envoie du message d'erreur
    http_response_code(405);
    echo(json_encode(["code"=>405, "message"=>"Méthode non autorisée"]));
    exit;
}