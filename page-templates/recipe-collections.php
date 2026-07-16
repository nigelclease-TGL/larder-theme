<?php
/**
 * Template Name: Recipe Collections
 * Template Post Type: page
 *
 * @package Larder
 */

get_header();
$collections = get_terms(
	array(
		'taxonomy'   => 'recipe_collection',
		'hide_empty' => false,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
?>
<main id="primary" class="collections-index">
	<header class="collection-hero collections-index__hero">
		<div class="container collection-hero__inner">
			<p class="eyebrow"><?php esc_html_e( "Nigel's Kitchen Table", 'larder' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<?php while ( have_posts() ) : the_post(); ?>
				<div class="collection-description"><?php the_content(); ?></div>
			<?php endwhile; ?>
		</div>
	</header>

	<section class="collection-content">
		<div class="container">
			<?php if ( ! is_wp_error( $collections ) && $collections ) : ?>
				<div class="collections-grid">
					<?php foreach ( $collections as $collection ) : ?>
						<a class="collection-card" href="<?php echo esc_url( get_term_link( $collection ) ); ?>">
							<span class="collection-card__number" aria-hidden="true"><?php echo esc_html( str_pad( (string) ( array_search( $collection, $collections, true ) + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
							<div>
								<h2><?php echo esc_html( $collection->name ); ?></h2>
								<?php if ( $collection->description ) : ?>
									<p><?php echo esc_html( wp_trim_words( $collection->description, 24 ) ); ?></p>
								<?php endif; ?>
								<span class="collection-card__count"><?php echo esc_html( sprintf( _n( '%s recipe', '%s recipes', $collection->count, 'larder' ), number_format_i18n( $collection->count ) ) ); ?></span>
							</div>
							<span class="collection-card__arrow" aria-hidden="true">→</span>
						</a>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="collection-empty">
					<h2><?php esc_html_e( 'Create your first collection', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'In WordPress, open Posts → Recipe Collections, add a collection, then assign recipes to it.', 'larder' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
