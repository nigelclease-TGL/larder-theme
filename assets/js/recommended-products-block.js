( function ( blocks, blockEditor, components, data, element, i18n, serverSideRender ) {
	'use strict';

	const el = element.createElement;
	const { registerBlockType } = blocks;
	const { InspectorControls } = blockEditor;
	const { Notice, PanelBody, SelectControl, Spinner, TextControl, TextareaControl, ToggleControl } = components;
	const { useSelect } = data;
	const { __ } = i18n;
	const ServerSideRender = serverSideRender;

	registerBlockType( 'nkt/recommended-products', {
		title: __( 'Recommended Products', 'larder' ),
		description: __( 'Display Kitchen Products linked to this recipe or choose a specific set of products.', 'larder' ),
		category: 'widgets',
		icon: 'cart',
		keywords: [
			__( 'products', 'larder' ),
			__( 'tools', 'larder' ),
			__( 'affiliate', 'larder' ),
		],
		attributes: {
			useLinkedProducts: {
				type: 'boolean',
				default: true,
			},
			productIds: {
				type: 'array',
				default: [],
				items: {
					type: 'number',
				},
			},
			heading: {
				type: 'string',
				default: 'Tools I recommend',
			},
			intro: {
				type: 'string',
				default: '',
			},
			showRetailerButton: {
				type: 'boolean',
				default: true,
			},
		},
		supports: {
			html: false,
			anchor: true,
		},
		edit: function ( props ) {
			const { attributes, setAttributes } = props;
			const products = useSelect( function ( select ) {
				return select( 'core' ).getEntityRecords( 'postType', 'nkt_product', {
					context: 'edit',
					per_page: 100,
					orderby: 'title',
					order: 'asc',
					status: [ 'publish', 'draft', 'pending', 'private' ],
				} );
			}, [] );

			const productOptions = ( products || [] ).map( function ( product ) {
				const rawTitle = product.title && product.title.raw ? product.title.raw : __( 'Untitled product', 'larder' );
				const statusLabel = product.status && 'publish' !== product.status ? ' — ' + product.status : '';

				return {
					label: rawTitle + statusLabel,
					value: String( product.id ),
				};
			} );

			const inspector = el(
				InspectorControls,
				null,
				el(
					PanelBody,
					{
						title: __( 'Products', 'larder' ),
						initialOpen: true,
					},
					el( ToggleControl, {
						label: __( 'Use products linked to this recipe', 'larder' ),
						help: attributes.useLinkedProducts
							? __( 'Selections come from the Recommended Products panel in the post editor.', 'larder' )
							: __( 'Choose a separate product set for this block.', 'larder' ),
						checked: attributes.useLinkedProducts,
						onChange: function ( value ) {
							setAttributes( { useLinkedProducts: value } );
						},
					} ),
					! attributes.useLinkedProducts && null === products
						? el( Spinner )
						: null,
					! attributes.useLinkedProducts && null !== products
						? el( SelectControl, {
							label: __( 'Kitchen Products', 'larder' ),
							multiple: true,
							value: ( attributes.productIds || [] ).map( String ),
							options: productOptions,
							help: __( 'Hold Ctrl on Windows or Command on Mac to select more than one product.', 'larder' ),
							onChange: function ( values ) {
								const selectedValues = Array.isArray( values ) ? values : [ values ];
								setAttributes( {
									productIds: selectedValues.filter( Boolean ).map( function ( value ) {
										return Number( value );
									} ),
								} );
							},
						} )
						: null,
					el( ToggleControl, {
						label: __( 'Show primary retailer button', 'larder' ),
						checked: attributes.showRetailerButton,
						onChange: function ( value ) {
							setAttributes( { showRetailerButton: value } );
						},
					} )
				),
				el(
					PanelBody,
					{
						title: __( 'Section wording', 'larder' ),
						initialOpen: false,
					},
					el( TextControl, {
						label: __( 'Heading', 'larder' ),
						value: attributes.heading,
						onChange: function ( value ) {
							setAttributes( { heading: value } );
						},
					} ),
					el( TextareaControl, {
						label: __( 'Optional introduction', 'larder' ),
						value: attributes.intro,
						onChange: function ( value ) {
							setAttributes( { intro: value } );
						},
					} )
				)
			);

			const linkedNotice = attributes.useLinkedProducts
				? el(
					Notice,
					{
						status: 'info',
						isDismissible: false,
					},
					__( 'This preview uses the products saved in the post’s Recommended Products panel. Save the post after changing those links to refresh the preview.', 'larder' )
				)
				: null;

			return el(
				'div',
				{ className: 'nkt-recommended-products-editor' },
				inspector,
				linkedNotice,
				el( ServerSideRender, {
					block: 'nkt/recommended-products',
					attributes: attributes,
					httpMethod: 'POST',
				} )
			);
		},
		save: function () {
			return null;
		},
	} );
} )(
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.components,
	window.wp.data,
	window.wp.element,
	window.wp.i18n,
	window.wp.serverSideRender
);
