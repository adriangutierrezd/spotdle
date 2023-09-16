<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spotdle</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/node_modules/sweetalert2/dist/sweetalert2.css">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/footer.css">
    <script type="module" src="js/game.js"></script>
</head>
<body>
    

    <header>
        <h1>Hola, <?= $_SESSION['loggedUser']['user']['display_name'] ?></h1>
    </header>

    <main>

        <form id="game-start-form">
            <select name="game_type" id="game_type" required>
                <option value="" selected disabled>Selecciona un modo de juego:</option>
                <option value="FIND_ARTIST">Adivina el/la artista</option>
            </select>
            <button id="game-start-btn" class="main-button" type="submit">Comenzar</button>
        </form>

        <section id="game-board">
            
        </section>
    
    </main>

    <footer>
        <form action="http://localhost:8888/public/logout" method="POST">
            <input type="submit" value="Cerrar sesión">
        </form>
    </footer>
    
</body>
</html>