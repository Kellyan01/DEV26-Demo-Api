<?php
//La route pour supprimer un utilisateur via son pseudo
//On demandera aussi le mot de passe du compte pour s'assurer que l'utilisateur souhaite effectivement supprimer son compte et qu'il a le droit de le faire
//Elle sera en méthode DELETE

//inclure les ressources
include './env.php';
include './utils/functions.php';
include './model/modelUtilisateur.php';

//I. Mise en place des Entêtes
//CORS origin
header("Access-Control-Allow-Origin: *");

//Format de la réponse au client
header("Content-Type: application/json; charset=UTF-8");

// Méthodes autorisées : Quel est le but de la route : ici lire des données
header("Access-Control-Allow-Methods: DELETE, OPTIONS");

//Durée de validité de l'autorisation preflight
header("Access-Control-Max-Age: 3600");

// Entêtes autorisées pour le Client : qu'est-ce que le client a le droit de m'envoyer
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

//II.Vérifier les méthodes
//Vérifier OPTIONS : réponse au preflight pour donner l'autorisation
if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        http_response_code(204);
        exit;
}

//Vérifier la méthode DELETE
if($_SERVER["REQUEST_METHOD"] === "DELETE"){
    //Récupération des données
    $json = file_get_contents('php://input');

    //Decodage du json
    $put = json_decode($json);

    //Normalisation des données
    $pseudo = normalize($put->pseudo);
    //On normalise pas le mot de passe, car il ne l'est pas lors de l'enregistrement
    $password = $put->password;

    //Vérifier les champs vides
    if(!empty($pseudo) && !empty($password)){
            //Vérifier si pseudo correspond à un compte existant
            try{
                //Mise en place de l'objet PDO
                $pdo = connect($_ENV['dbhost'],$_ENV['dbname'],$_ENV['dblogin'],$_ENV['dbpassword']);

                //Appeler le modèle pour récupérer les infos du comptes
                $data = findPasswordByPseudo($pdo, $pseudo);

                if(!empty($data)){
                    //Vérifier la correspondance des mots de passe
                    if(password_verify($password,$data->mdp)){
                        //Je lance la procédure de mise à jour
                        delete($pdo, $pseudo);

                        //J'envoie le message de confirmation
                        http_response_code(200);
                        echo(json_encode(["code"=>200, "message"=>"Le compte a été supprimé."]));
                        exit;

                    }else{
                        //Cas d'erreur : Forbidden (403)
                        http_response_code(403);
                        echo(json_encode(["code"=>403, "message"=>"Mauvais mot de passe. Suppression du compte interdite."]));
                        exit;
                    }

                }else{
                    //Cas d'erreur : l'utilisateur n'existe pas (404 NOT FOUND)
                    http_response_code(404);
                    echo(json_encode(["code"=>404, "message"=>"Aucune n'utilisateur ne correspond à ce pseudo."]));
                    exit;
                }
            }catch(Exception $error){
                //Erreur de Server 500
                http_response_code(500);
                echo(json_encode(["code"=>500, "message"=>$error->getMessage()]));
                exit;
            }

    }else{
        //Erreur : BAD REQUEST
        http_response_code(400);
        echo(json_encode(["code"=>400, "message"=>"Toutes les données doivent être complétée."]));
        exit;
    }
}else{
    //Cas d'Erreur
    http_response_code(405);
    echo(json_encode(["code"=>405, "message"=>"Methode non autorisée."]));
    exit;
}
