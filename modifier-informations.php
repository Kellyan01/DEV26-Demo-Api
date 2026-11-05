<?php
//La route pour modifier un utilisateur via son ancien email
//Elle sera en méthode PUT

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
header("Access-Control-Allow-Methods: PUT, OPTIONS");

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

//Vérifier la méthode PUT
if($_SERVER["REQUEST_METHOD"] === "PUT"){
    //Récupération des données
    $json = file_get_contents('php://input');

    //Decondage du json
    $put = json_decode($json);

    //Normalisation des données
    $oldEmail = normalize($put->oldEmail);
    $pseudo = normalize($put->pseudo);
    $email = normalize($put->email);
    $prenom = normalize($put->prenom);
    $nom = normalize($put->nom);
    $dob = normalize($put->dob);

    //Vérifier les champs vides
    if(!empty($oldEmail) && !empty($pseudo) && !empty($email) && !empty($prenom) && !empty($nom) && !empty($dob)){
        //Vérifier le format des emails
        if(filter_var($oldEmail,FILTER_VALIDATE_EMAIL) && filter_var($email,FILTER_VALIDATE_EMAIL)){
            //Vérifier si oldEmail correspond à un compte existant
            try{
                //Mise en place de l'objet PDO
                $pdo = connect($_ENV['dbhost'],$_ENV['dbname'],$_ENV['dblogin'],$_ENV['dbpassword']);

                //Appeler le modèle pour récupérer les infos du comptes
                $data = findOneByMail($pdo, $oldEmail);

                if(!empty($data)){
                    //Vérifier si le nouvel email est disponible. 2 cas de figures :
                        // - le nouvelle email correspond à l'ancien email
                        // - aucun compte ne correspond au nouvelle email
                    //Récupération d'un potentiel compte utilisant le nouvel email
                    $data = findOneByMail($pdo, $oldEmail);

                    if($oldEmail === $email || empty($data)){
                        //Je lance la procédure de mise à jour
                        update($pdo, $oldEmail, $pseudo, $email, $nom, $prenom, $dob);

                        //J'envoie le message de confirmation
                        http_response_code(200);
                        echo(json_encode(["code"=>200, "message"=>"Modifications effectuées avec succès"]));
                        exit;

                    }else{
                        //Cas d'erreur : Conflit (409)
                        http_response_code(409);
                        echo(json_encode(["code"=>409, "message"=>"Le nouvel email n'est pas disponible."]));
                        exit;
                    }

                }else{
                    //Cas d'erreur : l'utilisateur n'existe pas (404 NOT FOUND)
                    http_response_code(404);
                    echo(json_encode(["code"=>404, "message"=>"Aucune n'utilisateur ne correspond à l'ancien email."]));
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
            echo(json_encode(["code"=>400, "message"=>"Les emails ne sont pas au bon format"]));
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
