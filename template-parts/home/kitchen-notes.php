<?php
/**
 * Homepage Kitchen Notes section.
 *
 * @package Larder
 */

$notes_category = get_category_by_slug( 'baking-guides' );
$notes_args     = array(
	'post_type'           => 'post',
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => true,
);

if ( $notes_category ) {
	$notes_args['cat'] = $notes_category->term_id;
}

$notes = new WP_Query( $notes_args );

if ( ! $notes->have_posts() ) {
	return;
}
?>
<section class="home-section home-notes" aria-labelledby="home-notes-title">
	<div class="container">
		<header class="section-heading section-heading--split">
			<div>
				<p class="eyebrow"><?php esc_html_e( 'Learn at the table', 'larder' ); ?></p>
				<h2 id="home-notes-title"><?php esc_html_e( 'Kitchen Notes', 'larder' ); ?></h2>
			</div>
			<a class="text-link" href="<?php echo esc_url( home_url( '/kitchen-notes/' ) ); ?>"><?php esc_html_e( 'Explore all notes', 'larder' ); ?></a>
		</header>

		<div class="home-notes__grid">
			<?php while ( $notes->have_posts() ) : $notes->the_post(); ?>
				<article <?php post_class( 'home-note-card' ); ?>>
					<a href="<?php the_permalink(); ?>" class="home-note-card__link">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="home-note-card__media"><?php the_post_thumbnail( 'larder-card', array( 'loading' => 'lazy' ) ); ?></div>
						<?php endif; ?>
						<div class="home-note-card__body">
							<p class="home-note-card__meta"><?php echo esc_html( get_the_date() ); ?></p>
							<h3><?php the_title(); ?></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
							<span class="home-note-card__cta"><?php esc_html_e( 'Read the note', 'larder' ); ?> →</span>
						</div>
					</a>
				</article>
			<?php endwhile; ?>
		</div>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
