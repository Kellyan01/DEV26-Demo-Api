<?php
//LISTE LES FONCTIONS UTILITAIRES

//Fonction pour créer l'objet de connexion PDO
function connect($host,$dbname,$login,$password){
    return new PDO('mysql:host='.$host.';dbname='.$dbname.'',$login,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
}


//fonction pour normaliser une donnée texte
//ATTENTION : ceci n'est PAS un échappement. On ne "nettoie" pas une donnée avant de la
//stocker : la BDD doit contenir la donnée VRAIE. Un utilisateur nommé O'Brien doit être
//stocké O'Brien, et pas O&#039;Brien, sinon la recherche, l'export et le JSON sont faussés.
//L'échappement se fait à la SORTIE, selon le contexte de destination :
//   - vers du HTML  -> htmlspecialchars()
//   - vers du JSON  -> json_encode() s'en charge tout seul
//   - vers du SQL   -> rien à faire, la requête préparée isole la donnée
//Ici on se contente de retirer les espaces de bordure : c'est de la NORMALISATION.
function normalize($data){
    return trim($data);
}
