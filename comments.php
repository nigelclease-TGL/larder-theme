<?php
/**
 * Comments template.
 *
 * @package Larder
 */

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2><?php esc_html_e( 'Baker comments', 'larder' ); ?></h2>
		<ol class="comment-list">
			<?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 56 ) ); ?>
		</ol>
		<?php the_comments_pagination(); ?>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'          => __( 'Share how your bake turned out', 'larder' ),
			'label_submit'         => __( 'Post comment', 'larder' ),
			'comment_notes_before' => '<p>' . esc_html__( 'Your email address will not be published.', 'larder' ) . '</p>',
		)
	);
	?>
</section>
