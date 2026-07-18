<?php
/**
 * Posts page template.
 *
 * @package Larder
 */

get_header();

global $wp_query;

$sort  = nkt_get_requested_discovery_sort( 'newest' );
$count = isset( $wp_query->found_posts ) ? (int) $wp_query->found_posts : 0;
?>
<main id="primary" class="archive-page nkt-discovery-page">
	<header class="archive-hero nkt-discovery-hero nkt-discovery-hero--compact">
		<div class="container nkt-discovery-hero__grid">
			<div class="nkt-discovery-hero__copy">
				<p class="eyebrow"><?php esc_html_e( 'Fresh from the kitchen', 'larder' ); ?></p>
				<h1><?php single_post_title(); ?></h1>
				<p><?php esc_html_e( 'New recipes, practical guides and seasonal inspiration from Nigel’s kitchen table.', 'larder' ); ?></p>
				<p class="nkt-results-count"><?php echo esc_html( nkt_get_recipe_count_label( $count ) ); ?></p>
			</div>
			<div class="nkt-discovery-search-card">
				<p class="nkt-discovery-search-card__eyebrow"><?php esc_html_e( 'Find something useful', 'larder' ); ?></p>
				<h2><?php esc_html_e( 'Search recipes and kitchen notes.', 'larder' ); ?></h2>
				<?php get_search_form(); ?>
			</div>
		</div>
	</header>

	<section class="archive-content nkt-discovery-results">
		<div class="container">
			<header class="nkt-results-header nkt-results-header--small">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Latest stories', 'larder' ); ?></p>
					<h2><?php esc_html_e( 'From the kitchen table', 'larder' ); ?></h2>
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

			<?php if ( have_posts() ) : ?>
				<div class="recipe-grid nkt-discovery-grid">
					<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content', 'card' ); endwhile; ?>
				</div>
				<div class="pagination"><?php the_posts_pagination( array( 'add_args' => 'newest' !== $sort ? array( 'sort' => $sort ) : array() ) ); ?></div>
			<?php else : ?>
				<div class="nkt-empty-state">
					<h2><?php esc_html_e( 'Nothing has been published yet.', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'New recipes and notes will appear here as they are added.', 'larder' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>
<?php get_footer();
