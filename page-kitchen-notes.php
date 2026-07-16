<?php
/**
 * Kitchen Notes landing page.
 *
 * Automatically used by a page with the slug kitchen-notes.
 *
 * @package Larder
 */

get_header();

$guide_category = get_category_by_slug( 'baking-guides' );
$query_args     = array(
	'post_type'           => 'post',
	'posts_per_page'      => 12,
	'ignore_sticky_posts' => true,
);

if ( $guide_category ) {
	$query_args['cat'] = $guide_category->term_id;
}

$notes = new WP_Query( $query_args );
?>
<main id="primary" class="kitchen-notes-page">
	<header class="archive-hero kitchen-notes-hero">
		<div class="container archive-hero__inner">
			<p class="eyebrow"><?php esc_html_e( "Nigel's Kitchen Table", 'larder' ); ?></p>
			<h1><?php the_title(); ?></h1>
			<p><?php esc_html_e( 'Practical baking guides, ingredient know-how and techniques to help you cook with confidence.', 'larder' ); ?></p>
		</div>
	</header>

	<section class="archive-content">
		<div class="container">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<div class="kitchen-notes-intro"><?php the_content(); ?></div>
			<?php endwhile; endif; ?>

			<?php if ( $notes->have_posts() ) : ?>
				<div class="kitchen-notes-grid">
					<?php while ( $notes->have_posts() ) : $notes->the_post(); ?>
						<?php get_template_part( 'template-parts/content', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<p><?php esc_html_e( 'Kitchen notes will appear here as they are published.', 'larder' ); ?></p>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
