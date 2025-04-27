<?php
// sections/jeux-educatifs.php
?>
<section class="educational-games">
    <div class="container">
        <h2>Découvrez nos jeux éducatifs</h2>
        <div class="games-grid">
            <?php
            $games = get_popular_games(4);
            foreach ($games as $game) {
                ?>
                <div class="game-card">
                    <img src="<?php echo $game['image']; ?>" alt="<?php echo $game['titre']; ?>">
                    <h3><?php echo $game['titre']; ?></h3>
                    <p><?php echo $game['description']; ?></p>
                    <div class="game-info">
                        <span class="age-range"><?php echo $game['age']; ?> ans</span>
                        <span class="category"><?php echo $game['categorie']; ?></span>
                    </div>
                    <a href="index.php?page=jeu&id=<?php echo $game['id']; ?>" class="play-button">Jouer</a>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="see-all">
            <a href="index.php?page=jeux" class="see-all-button">Voir tous les jeux <i class="icon-arrow-right"></i></a>
        </div>
    </div>
</section>