<?php
/**
 * Recipe/post card.
 *
 * @package Larder
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'recipe-card' ); ?>>
	<a class="recipe-card__link" href="<?php the_permalink(); ?>">
		<div class="recipe-card__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'larder-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 620px) 92vw, (max-width: 900px) 46vw, 31vw' ) ); ?>
			<?php else : ?>
				<div class="recipe-card__placeholder" aria-hidden="true"></div>
			<?php endif; ?>
		</div>

		<div class="recipe-card__content">
			<?php nkt_post_meta(); ?>
			<h3 class="recipe-card__title"><?php the_title(); ?></h3>
			<p class="recipe-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
			<span class="recipe-card__cta"><?php esc_html_e( 'View recipe', 'larder' ); ?> <span aria-hidden="true">→</span></span>
		</div>
	</a>
</article>
