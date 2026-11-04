fetch('https://localhost/API/utilisateurs.php')
    .then(response => response.json())
    .then(data => console.log(data));