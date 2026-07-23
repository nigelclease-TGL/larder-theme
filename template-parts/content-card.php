<?php
/**
 * Recipe/post card.
 *
 * @package Larder
 */

$is_kitchen_note = has_category( array( 'kitchen-notes', 'baking-guides' ) );
$cta_label       = $is_kitchen_note ? __( 'Read the note', 'larder' ) : __( 'View recipe', 'larder' );
$post_url        = get_permalink();
$post_title      = get_the_title();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'recipe-card' ); ?> data-content-type="<?php echo esc_attr( $is_kitchen_note ? 'note' : 'recipe' ); ?>">
	<a class="recipe-card__link recipe-card__media-link" href="<?php echo esc_url( $post_url ); ?>" aria-label="<?php echo esc_attr( $post_title ); ?>">
		<div class="recipe-card__media">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php the_post_thumbnail( 'larder-card', array( 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 620px) 92vw, (max-width: 900px) 46vw, 31vw' ) ); ?>
			<?php else : ?>
				<div class="recipe-card__placeholder" aria-hidden="true"></div>
			<?php endif; ?>
			<span class="recipe-card__type"><?php echo esc_html( $is_kitchen_note ? __( 'Kitchen note', 'larder' ) : __( 'Recipe', 'larder' ) ); ?></span>
		</div>
	</a>

	<div class="recipe-card__content">
		<?php nkt_post_meta(); ?>
		<h3 class="recipe-card__title"><a class="recipe-card__link recipe-card__title-link" href="<?php echo esc_url( $post_url ); ?>"><?php echo esc_html( $post_title ); ?></a></h3>
		<p class="recipe-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
		<a class="recipe-card__link recipe-card__cta" href="<?php echo esc_url( $post_url ); ?>"><?php echo esc_html( $cta_label ); ?> <span aria-hidden="true">→</span></a>
	</div>
</article>
