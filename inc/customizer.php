<?php
/**
 * Theme Customizer settings.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register theme settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 * @return void
 */
function larder_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'larder_homepage',
		array(
			'title'       => __( "Nigel's Kitchen Table Homepage", 'larder' ),
			'description' => __( 'Set the homepage wording, images and the recipes shown in each featured section. The portrait is deliberately displayed at a modest size.', 'larder' ),
			'priority'    => 30,
		)
	);

	$homepage_settings = array(
		'larder_hero_title' => array(
			'label'   => __( 'Hero title', 'larder' ),
			'default' => __( 'Seasonal recipes, made to be shared.', 'larder' ),
		),
		'larder_hero_copy' => array(
			'label'   => __( 'Hero introduction', 'larder' ),
			'default' => __( 'Beautiful bakes, comforting food and practical kitchen knowledge—tested carefully and written for real life.', 'larder' ),
		),
		'larder_about_title' => array(
			'label'   => __( 'About title', 'larder' ),
			'default' => __( 'From my kitchen table', 'larder' ),
		),
		'larder_about_copy' => array(
			'label'   => __( 'About introduction', 'larder' ),
			'default' => __( "I'm Nigel. I develop and test reliable recipes for beautiful bakes, comforting food and the everyday kitchen.", 'larder' ),
		),
	);

	foreach ( $homepage_settings as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => 'sanitize_textarea_field',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $args['label'],
				'section' => 'larder_homepage',
				'type'    => 'textarea',
			)
		);
	}

	foreach ( array( 'hero' => __( 'Hero image', 'larder' ), 'portrait' => __( 'Small about portrait', 'larder' ) ) as $key => $label ) {
		$setting_id = 'larder_' . $key . '_image';
		$wp_customize->add_setting(
			$setting_id,
			array(
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Media_Control(
				$wp_customize,
				$setting_id,
				array(
					'label'     => $label,
					'section'   => 'larder_homepage',
					'mime_type' => 'image',
				)
			)
		);
	}

	$recipe_options = nkt_homepage_recipe_options();

	$wp_customize->add_setting(
		'larder_home_hero_recipe_id',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'larder_home_hero_recipe_id',
		array(
			'label'       => __( 'Hero featured recipe', 'larder' ),
			'description' => __( 'This controls the recipe named in the small card over the hero image. Leave it unselected to show no specific recipe.', 'larder' ),
			'section'     => 'larder_homepage',
			'type'        => 'select',
			'choices'     => $recipe_options,
		)
	);

	foreach ( nkt_homepage_collection_definitions() as $definition ) {
		$category   = nkt_homepage_collection_category( $definition );
		$setting_id = 'larder_home_collection_' . $definition['key'] . '_recipe_id';

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'       => sprintf(
					/* translators: %s: collection name. */
					__( 'Collection cover: %s', 'larder' ),
					$definition['label']
				),
				'description' => __( 'Choose the recipe whose featured image appears on this collection card. The card will still open the collection, not the recipe.', 'larder' ),
				'section'     => 'larder_homepage',
				'type'        => 'select',
				'choices'     => nkt_homepage_recipe_options( $category ? $category->term_id : 0 ),
			)
		);
	}

	for ( $index = 1; $index <= 6; $index++ ) {
		$setting_id = 'larder_home_latest_recipe_' . $index;

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'       => sprintf(
					/* translators: %d: recipe position. */
					__( 'Latest recipes: position %d', 'larder' ),
					$index
				),
				'description' => 1 === $index ? __( 'Choose the recipes and their order for the Latest recipes section. When all six are empty, the section uses the newest published recipes automatically.', 'larder' ) : '',
				'section'     => 'larder_homepage',
				'type'        => 'select',
				'choices'     => $recipe_options,
			)
		);
	}

	for ( $index = 1; $index <= 4; $index++ ) {
		$setting_id = 'larder_home_favourite_recipe_' . $index;

		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'       => sprintf(
					/* translators: %d: recipe position. */
					__( "Everyone's cooking: position %d", 'larder' ),
					$index
				),
				'description' => 1 === $index ? __( "Choose the four recipes shown in the “This is what everyone’s cooking” section. When all four are empty, the theme uses its automatic favourites.", 'larder' ) : '',
				'section'     => 'larder_homepage',
				'type'        => 'select',
				'choices'     => $recipe_options,
			)
		);
	}

	$wp_customize->add_section(
		'larder_newsletter',
		array(
			'title'       => __( 'Newsletter & Welcome Gift', 'larder' ),
			'description' => __( 'Connect Mailchimp for WordPress, refine the invitation and optionally add a free guide or other welcome gift.', 'larder' ),
			'priority'    => 31,
		)
	);

	$wp_customize->add_setting(
		'larder_mailchimp_form_id',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
		)
	);
	$wp_customize->add_control(
		'larder_mailchimp_form_id',
		array(
			'label'       => __( 'Mailchimp for WordPress form ID', 'larder' ),
			'description' => __( 'Numeric only, for example: 123. Mailchimp audience or account identifiers will not work here.', 'larder' ),
			'section'     => 'larder_newsletter',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'step' => 1 ),
		)
	);

	$newsletter_text_settings = array(
		'larder_newsletter_title' => array(
			'label'    => __( 'Newsletter title', 'larder' ),
			'default'  => __( 'Join the Kitchen Table', 'larder' ),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		),
		'larder_newsletter_copy' => array(
			'label'    => __( 'Newsletter introduction', 'larder' ),
			'default'  => __( 'Seasonal recipes, practical kitchen notes and thoughtful inspiration—delivered occasionally.', 'larder' ),
			'type'     => 'textarea',
			'sanitize' => 'sanitize_textarea_field',
		),
		'larder_newsletter_promise' => array(
			'label'    => __( 'Newsletter promise', 'larder' ),
			'default'  => __( 'No clutter. Just something worth cooking.', 'larder' ),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		),
		'larder_lead_magnet_title' => array(
			'label'    => __( 'Welcome gift title', 'larder' ),
			'default'  => __( 'A useful guide for your kitchen', 'larder' ),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		),
		'larder_lead_magnet_copy' => array(
			'label'    => __( 'Welcome gift description', 'larder' ),
			'default'  => __( 'Offer a practical seasonal guide, checklist or recipe collection as a thoughtful welcome gift.', 'larder' ),
			'type'     => 'textarea',
			'sanitize' => 'sanitize_textarea_field',
		),
		'larder_lead_magnet_button' => array(
			'label'    => __( 'Welcome gift button label', 'larder' ),
			'default'  => __( 'Get the free guide', 'larder' ),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		),
	);

	foreach ( $newsletter_text_settings as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => $args['sanitize'],
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $args['label'],
				'section' => 'larder_newsletter',
				'type'    => $args['type'],
			)
		);
	}

	$wp_customize->add_setting(
		'larder_lead_magnet_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'larder_lead_magnet_url',
		array(
			'label'       => __( 'Welcome gift URL', 'larder' ),
			'description' => __( 'The welcome gift panel only appears when this URL is entered.', 'larder' ),
			'section'     => 'larder_newsletter',
			'type'        => 'url',
		)
	);

	$wp_customize->add_section(
		'larder_growth',
		array(
			'title'       => __( 'Business & Promotion', 'larder' ),
			'description' => __( 'Add one optional, clearly presented homepage promotion. Leave the URL empty to hide it.', 'larder' ),
			'priority'    => 32,
		)
	);

	$promotion_settings = array(
		'larder_promotion_eyebrow' => array(
			'label'    => __( 'Promotion eyebrow', 'larder' ),
			'default'  => __( 'From the Kitchen Table', 'larder' ),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		),
		'larder_promotion_title' => array(
			'label'    => __( 'Promotion title', 'larder' ),
			'default'  => __( 'Something useful for your kitchen', 'larder' ),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		),
		'larder_promotion_copy' => array(
			'label'    => __( 'Promotion description', 'larder' ),
			'default'  => __( 'Use this space for a seasonal collection, digital guide, course, event or trusted partner.', 'larder' ),
			'type'     => 'textarea',
			'sanitize' => 'sanitize_textarea_field',
		),
		'larder_promotion_button' => array(
			'label'    => __( 'Promotion button label', 'larder' ),
			'default'  => __( 'Discover more', 'larder' ),
			'type'     => 'text',
			'sanitize' => 'sanitize_text_field',
		),
	);

	foreach ( $promotion_settings as $setting_id => $args ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $args['default'],
				'sanitize_callback' => $args['sanitize'],
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $args['label'],
				'section' => 'larder_growth',
				'type'    => $args['type'],
			)
		);
	}

	$wp_customize->add_setting(
		'larder_promotion_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		'larder_promotion_url',
		array(
			'label'       => __( 'Promotion URL', 'larder' ),
			'description' => __( 'The promotion appears only when this URL is entered. The Homepage Promotion widget area takes priority when it contains a widget.', 'larder' ),
			'section'     => 'larder_growth',
			'type'        => 'url',
		)
	);

	$wp_customize->add_section(
		'larder_social',
		array(
			'title'    => __( 'Social Links', 'larder' ),
			'priority' => 33,
		)
	);

	$social_defaults = array(
		'instagram' => 'https://www.instagram.com/thegourmetlarder/',
		'pinterest' => 'https://hu.pinterest.com/thegourmetlarder/',
		'facebook'  => 'https://www.facebook.com/thegourmetlarder/',
	);

	foreach ( $social_defaults as $network => $default_url ) {
		$setting_id = 'larder_' . $network . '_url';
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => $default_url,
				'sanitize_callback' => 'esc_url_raw',
			)
		);
		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => ucfirst( $network ) . ' URL',
				'section' => 'larder_social',
				'type'    => 'url',
			)
		);
	}
}
add_action( 'customize_register', 'larder_customize_register' );
