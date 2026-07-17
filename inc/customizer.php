<?php
/**
 * Theme Customizer settings.
 *
 * @package Larder
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function larder_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'larder_homepage',
		array(
			'title'       => __( "Nigel's Kitchen Table Homepage", 'larder' ),
			'description' => __( 'Set the homepage wording and images. The portrait is deliberately displayed at a modest size.', 'larder' ),
			'priority'    => 30,
		)
	);

	$settings = array(
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

	foreach ( $settings as $setting_id => $args ) {
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

	$wp_customize->add_section(
		'larder_newsletter',
		array(
			'title'       => __( 'Mailchimp Newsletter', 'larder' ),
			'description' => __( 'Install and connect the Mailchimp for WordPress plugin, create a form, then enter the numeric WordPress form ID shown by the plugin. Mailchimp audience or account identifiers will not work in this field.', 'larder' ),
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
			'description' => __( 'Numeric only, for example: 123', 'larder' ),
			'section'     => 'larder_newsletter',
			'type'        => 'number',
			'input_attrs' => array( 'min' => 0, 'step' => 1 ),
		)
	);

	$wp_customize->add_section(
		'larder_social',
		array(
			'title'    => __( 'Social Links', 'larder' ),
			'priority' => 32,
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