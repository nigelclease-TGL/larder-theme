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
				<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<div class="recipe-card__placeholder" aria-hidden="true"></div>
			<?php endif; ?>
		</div>

		<div class="recipe-card__content">
			<p class="recipe-card__meta"><?php echo esc_html( get_the_date() ); ?></p>
			<h3 class="recipe-card__title"><?php the_title(); ?></h3>
			<p class="recipe-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
		</div>
	</a>
</article>
