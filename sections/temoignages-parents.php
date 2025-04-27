<?php
// sections/temoignages-parents.php
?>
<section class="testimonials">
    <div class="container">
        <h2>Que disent les parents ?</h2>
        <div class="testimonials-slider">
            <?php
            $testimonials = get_testimonials(3);
            foreach ($testimonials as $testimonial) {
                ?>
                <div class="testimonial-card">
                    <div class="testimonial-content">
                        <p>"<?php echo $testimonial['contenu']; ?>"</p>
                    </div>
                    <div class="testimonial-author">
                        <img src="<?php echo $testimonial['avatar']; ?>" alt="<?php echo $testimonial['nom']; ?>">
                        <div>
                            <h4><?php echo $testimonial['nom']; ?></h4>
                            <p>Parent de <?php echo $testimonial['enfant']; ?>, <?php echo $testimonial['age']; ?> ans</p>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="testimonial-dots">
            <span class="dot active"></span>
            <span class="dot"></span>
            <span class="dot"></span>
        </div>
    </div>
</section>