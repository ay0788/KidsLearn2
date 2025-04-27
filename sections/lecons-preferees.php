<?php
// sections/lecons-preferees.php
?>
<section class="favorite-lessons">
    <div class="container">
        <h2>Nos leçons préférées</h2>
        <div class="lessons-grid">
            <?php
            $lecons = get_lecons_preferees(3);
            foreach ($lecons as $lecon) {
                ?>
                <div class="lesson-card">
                    <img src="<?php echo $lecon['image']; ?>" alt="<?php echo $lecon['titre']; ?>">
                    <div class="lesson-content">
                        <h3><?php echo $lecon['titre']; ?></h3>
                        <p><?php echo $lecon['description']; ?></p>
                        <span class="lesson-category"><?php echo $lecon['categorie']; ?></span>
                        <a href="index.php?page=lecon&id=<?php echo $lecon['id']; ?>" class="lesson-button">Explorer</a>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="see-all">
            <a href="index.php?page=lecons" class="see-all-button">Toutes les leçons <i class="icon-arrow-right"></i></a>
        </div>
    </div>
</section>