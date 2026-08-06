<?php
/**
 * Kitchen Products archive and product taxonomy template.
 *
 * @package Larder
 */

get_header();

$is_taxonomy  = is_tax( array( NKT_PRODUCT_CATEGORY_TAX, NKT_PRODUCT_BRAND_TAX ) );
$queried_term = $is_taxonomy ? get_queried_object() : null;
$title        = $is_taxonomy && $queried_term instanceof WP_Term ? $queried_term->name : __( 'Shop My Kitchen', 'larder' );
$intro        = $is_taxonomy && $queried_term instanceof WP_Term && $queried_term->description
	? $queried_term->description
	: __( 'The cookware, baking equipment and kitchen tools I genuinely use, recommend or have carefully researched.', 'larder' );
$categories   = get_terms(
	array(
		'taxonomy'   => NKT_PRODUCT_CATEGORY_TAX,
		'hide_empty' => true,
		'parent'     => 0,
	)
);
$brands       = get_terms(
	array(
		'taxonomy'   => NKT_PRODUCT_BRAND_TAX,
		'hide_empty' => true,
		'number'     => 12,
		'orderby'    => 'count',
		'order'      => 'DESC',
	)
);
?>

<main id="primary" class="nkt-shop-archive">
	<header class="nkt-shop-hero">
		<div class="container nkt-shop-hero__inner">
			<p class="eyebrow"><?php esc_html_e( 'Nigel recommends', 'larder' ); ?></p>
			<h1><?php echo esc_html( $title ); ?></h1>
			<p class="nkt-shop-hero__intro"><?php echo esc_html( wp_strip_all_tags( $intro ) ); ?></p>
		</div>
	</header>

	<?php if ( ( ! is_wp_error( $categories ) && $categories ) || ( ! is_wp_error( $brands ) && $brands ) ) : ?>
		<nav class="nkt-shop-directory" aria-label="<?php esc_attr_e( 'Browse kitchen products', 'larder' ); ?>">
			<div class="container nkt-shop-directory__inner">
				<a class="nkt-shop-directory__all<?php echo $is_taxonomy ? '' : ' is-active'; ?>" href="<?php echo esc_url( nkt_get_product_archive_url() ); ?>"><?php esc_html_e( 'All products', 'larder' ); ?></a>
				<?php if ( ! is_wp_error( $categories ) ) : ?>
					<?php foreach ( $categories as $category ) : ?>
						<a class="<?php echo $is_taxonomy && $queried_term instanceof WP_Term && (int) $queried_term->term_id === (int) $category->term_id ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a>
					<?php endforeach; ?>
				<?php endif; ?>
				<?php if ( ! is_wp_error( $brands ) ) : ?>
					<?php foreach ( $brands as $brand ) : ?>
						<a class="nkt-shop-directory__brand<?php echo $is_taxonomy && $queried_term instanceof WP_Term && (int) $queried_term->term_id === (int) $brand->term_id ? ' is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $brand ) ); ?>"><?php echo esc_html( $brand->name ); ?></a>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
		</nav>
	<?php endif; ?>

	<section class="nkt-shop-results" aria-labelledby="nkt-shop-results-title">
		<div class="container">
			<header class="nkt-shop-results__header">
				<div>
					<p class="eyebrow"><?php esc_html_e( 'Kitchen essentials', 'larder' ); ?></p>
					<h2 id="nkt-shop-results-title">
						<?php echo $is_taxonomy ? esc_html( $title ) : esc_html__( 'Browse all products', 'larder' ); ?>
					</h2>
				</div>
				<p><?php esc_html_e( 'Retailer links may be affiliate links. Recommendations remain editorially independent.', 'larder' ); ?></p>
			</header>

			<?php if ( have_posts() ) : ?>
				<div class="nkt-product-grid">
					<?php
					while ( have_posts() ) :
						the_post();
						get_template_part( 'template-parts/content', 'product-card' );
					endwhile;
					?>
				</div>
				<?php the_posts_pagination( array( 'prev_text' => __( 'Previous', 'larder' ), 'next_text' => __( 'Next', 'larder' ) ) ); ?>
			<?php else : ?>
				<div class="nkt-shop-empty">
					<h2><?php esc_html_e( 'No products have been added here yet.', 'larder' ); ?></h2>
					<p><?php esc_html_e( 'Please check back as the kitchen collection grows.', 'larder' ); ?></p>
					<?php if ( $is_taxonomy ) : ?>
						<a class="button button-primary" href="<?php echo esc_url( nkt_get_product_archive_url() ); ?>"><?php esc_html_e( 'Browse all products', 'larder' ); ?></a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>
</main>

<?php get_footer(); ?>
