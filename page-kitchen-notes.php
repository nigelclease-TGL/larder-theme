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

$sort  = nkt_get_requested_discovery_sort( 'newest' );
$paged = max( 1, get_query_var( 'paged' ), get_query_var( 'page' ) );
$notes = null;

if ( $guide_category ) {
	$notes_args = array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 12,
		'paged'               => $paged,
		'ignore_sticky_posts' => true,
		'cat'                 => $guide_category->term_id,
	);
	$notes      = new WP_Query( array_merge( $notes_args, nkt_get_discovery_order_args( $sort ) ) );
}
?>
<main id="primary" class="kitchen-notes-page nkt-discovery-page">
	<header class="archive-hero kitchen-notes-hero nkt-discovery-hero nkt-discovery-hero--notes">
		<div class="container nkt-discovery-hero__grid">
			<div class="nkt-discovery-hero__copy">
				<p class="eyebrow"><?php esc_html_e( "Nigel's Kitchen Table", 'larder' ); ?></p>
				<h1><?php the_title(); ?></h1>
				<p><?php esc_html_e( 'Practical guides, ingredient know-how and techniques to help you cook and bake with confidence.', 'larder' ); ?></p>
				<?php if ( $notes ) : ?>
					<p class="nkt-results-count"><?php echo esc_html( sprintf( _n( '%s kitchen note', '%s kitchen notes', $notes->found_posts, 'larder' ), number_format_i18n( $notes->found_posts ) ) ); ?></p>
				<?php endif; ?>
			</div>
			<div class="nkt-discovery-search-card">
				<p class="nkt-discovery-search-card__eyebrow"><?php esc_html_e( 'Search the table', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'Find a recipe, ingredient or technique.', 'larder' ); ?></h2>
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>

	<section class="archive-content nkt-discovery-results">
		<div class="container">
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
				<?php if ( trim( get_the_content() ) ) : ?><div class="kitchen-notes-intro"><?php the_content(); ?></div><?php endif; ?>
			<?php endwhile; endif; ?>

			<header class="nkt-results-header nkt-results-header--small">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Learn at the table', 'larder' ); ?></p>
					<h2><?php esc_html_e( 'All Kitchen Notes', 'larder' ); ?></h2>
				</div>
				<form class="nkt-discovery-toolbar nkt-discovery-toolbar--sort" method="get">
					<label>
						<span><?php esc_html_e( 'Sort by', 'larder' ); ?></span>
						<select name="sort">
							<?php foreach ( nkt_get_discovery_sort_options() as $value => $label ) : ?>
								<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $sort, $value ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
					<button class="button button-primary" type="submit"><?php esc_html_e( 'Apply', 'larder' ); ?></button>
				</form>
			</header>

			<?php if ( $notes && $notes->have_posts() ) : ?>
				<div class="kitchen-notes-grid nkt-discovery-grid">
					<?php while ( $notes->have_posts() ) : $notes->the_post(); ?>
						<?php get_template_part( 'template-parts/content', 'card' ); ?>
					<?php endwhile; ?>
				</div>
				<?php if ( $notes->max_num_pages > 1 ) : ?>
					<nav class="pagination" aria-label="<?php esc_attr_e( 'Kitchen Notes pagination', 'larder' ); ?>">
						<?php echo wp_kses_post( paginate_links( array( 'total' => $notes->max_num_pages, 'current' => $paged, 'mid_size' => 1, 'prev_text' => __( 'Previous', 'larder' ), 'next_text' => __( 'Next', 'larder' ), 'add_args' => 'newest' !== $sort ? array( 'sort' => $sort ) : array() ) ) ); ?>
					</nav>
				<?php endif; ?>
				<?php wp_reset_postdata(); ?>
			<?php else : ?>
				<div class="nkt-empty-state">
					<h2><?php esc_html_e( 'Kitchen Notes are being prepared', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'Practical guides and useful techniques will appear here as they are published.', 'larder' ); ?></p>
					<a class="button button-primary" href="<?php echo esc_url( nkt_page_url( array( 'recipes' ), '/recipes/' ) ); ?>"><?php esc_html_e( 'Browse recipes', 'larder' ); ?></a>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer(); ?>
