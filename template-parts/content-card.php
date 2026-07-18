<?php
/**
 * Recipe/post card.
 *
 * @package Larder
 */

$is_kitchen_note = has_category( array( 'kitchen-notes', 'baking-guides' ) );
$cta_label       = $is_kitchen_note ? __( 'Read the note', 'larder' ) : __( 'View recipe', 'larder' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'recipe-card' ); ?> data-content-type="<?php echo esc_attr( $is_kitchen_note ? 'note' : 'recipe' ); ?>">
	<a class="recipe-card__link" href="<?php the_permalink(); ?>">
		<div class="recipe-card__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'larder-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 620px) 92vw, (max-width: 900px) 46vw, 31vw' ) ); ?>
			<?php else : ?>
				<div class="recipe-card__placeholder" aria-hidden="true"></div>
			<?php endif; ?>
			<span class="recipe-card__type"><?php echo esc_html( $is_kitchen_note ? __( 'Kitchen note', 'larder' ) : __( 'Recipe', 'larder' ) ); ?></span>
		</div>

		<div class="recipe-card__content">
			<?php nkt_post_meta(); ?>
			<h3 class="recipe-card__title"><?php the_title(); ?></h3>
			<p class="recipe-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
			<span class="recipe-card__cta"><?php echo esc_html( $cta_label ); ?> <span aria-hidden="true">→</span></span>
		</div>
	</a>
</article>
