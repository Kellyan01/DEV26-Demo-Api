//Mise en place de variable js
let email; // une varibale qui peut être modifié. Elle n'a pas besoin d'être initialisée.
const PSEUDO = "Kellyan" // une variable qui ne peut pas être modifié. Elle doit être initialisée.

//Tableau associatif et objet sont le même concept = OBJET
const BODY = {
    email: "yoyo@gmail.fr",
    password: "123456"
}

//appeller un attribur ou une méthode d'un objet se fait avec le point . , et non la double flèche =>
// Afficher un message dans la console du navigateur (console.log)
console.log("Hello World !");

//Utiliser Fetch pour interroger une API et récupérer des données 
fetch('https://localhost/API/utilisateurs.php')
    .then(response => response.json())
    .then(data => console.log(data));

//Utiliser Fetch pour interroger une API et modifier des données en PUT, avec un body en JSON
//Mise en place d'un objet JS qui sera converti en JSON pour le body de la requête
const ACCOUNT = {
    oldEmail: "griffith@gmail.com",
    email: "griffith@gmail.com",
    pseudo: "Griffith",
    prenom: "Ceska",
    nom: "Guts",
    dob : "1999-12-12"
}
//Envoie de la requête PUT à l'API avec le body en JSON via Fetch, puis récupération de la réponse et affichage dans la console du navigateur
fetch('https://localhost/API/modifier-informations.php', {
    method : 'PUT',
    body : JSON.stringify(ACCOUNT) // JSON.stringify() permet de convertir un objet JS en JSON
})
.then(response => response.json()) //json() permet de convertir un JSON en objet JS
.then(data => console.log(data)); // Afficher le résultat dans la console du navigateur