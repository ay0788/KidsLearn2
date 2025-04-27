<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">
                    <img src="assets/images/logo.svg" alt="KidsLearn">
                    <span class="logo-text">KidsLearn</span>
                </a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php" <?php if(basename($_SERVER['PHP_SELF']) == 'index.php') echo 'class="active"'; ?>>Accueil</a></li>
                    <li><a href="categories.php" <?php if(basename($_SERVER['PHP_SELF']) == 'categories.php') echo 'class="active"'; ?>>Catégories</a></li>
                    <li><a href="games.php" <?php if(basename($_SERVER['PHP_SELF']) == 'games.php') echo 'class="active"'; ?>>Jeux</a></li>
                    <li><a href="parents.php" <?php if(basename($_SERVER['PHP_SELF']) == 'parents.php') echo 'class="active"'; ?>>Parents</a></li>
                </ul>
            </nav>
        </div>
    </header>