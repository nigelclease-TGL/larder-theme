<?php
/**
 * Kitchen Notes landing page.
 *
 * Automatically used by pages with the kitchen-notes or baking-guides slugs.
 *
 * @package Larder
 */

get_header();

$guide_category = get_category_by_slug( 'kitchen-notes' );
if ( ! $guide_category ) {
	$guide_category = get_category_by_slug( 'baking-guides' );
}

$paged = max( 1, get_query_var( 'paged' ) );
$notes = null;

if ( $guide_category ) {
	$notes = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 12,
			'paged'               => $paged,
			'ignore_sticky_posts' => true,
			'cat'                 => $guide_category->term_id,
		)
	);
}
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
				<?php if ( trim( get_the_content() ) ) : ?><div class="kitchen-notes-intro"><?php the_content(); ?></div><?php endif; ?>
			<?php endwhile; endif; ?>

			<?php if ( $notes && $notes->have_posts() ) : ?>
				<div class="kitchen-notes-grid">
					<?php while ( $notes->have_posts() ) : $notes->the_post(); ?>
						<?php get_template_part( 'template-parts/content', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<?php if ( $notes->max_num_pages > 1 ) : ?>
					<nav class="pagination" aria-label="<?php esc_attr_e( 'Kitchen Notes pagination', 'larder' ); ?>">
						<?php echo wp_kses_post( paginate_links( array( 'total' => $notes->max_num_pages, 'current' => $paged, 'mid_size' => 1, 'prev_text' => __( 'Previous', 'larder' ), 'next_text' => __( 'Next', 'larder' ) ) ) ); ?>
					</nav>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="collection-empty">
					<h2><?php esc_html_e( 'Kitchen Notes are being prepared', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'Baking guides and practical techniques will appear here when posts are assigned to the Baking Guides category.', 'larder' ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( nkt_page_url( array( 'recipes' ), '/recipes/' ) ); ?>"><?php esc_html_e( 'Browse recipes', 'larder' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer(); ?>
