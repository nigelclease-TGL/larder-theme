( function ( blocks, blockEditor, data, element, i18n ) {
	'use strict';

	const el = element.createElement;
	const { registerBlockType } = blocks;
	const { useBlockProps } = blockEditor;
	const { useSelect } = data;
	const { __ } = i18n;

	registerBlockType( 'nkt/recommended-products', {
		apiVersion: 2,
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
			const blockProps = useBlockProps( {
				className: 'nkt-recommended-products-editor',
			} );
			const fieldStyle = {
				display: 'block',
				marginTop: '12px',
			};
			const inputStyle = {
				boxSizing: 'border-box',
				display: 'block',
				marginTop: '5px',
				maxWidth: '100%',
				width: '100%',
			};
			const productOptions = ( products || [] ).map( function ( product ) {
				const rawTitle = product.title && product.title.raw ? product.title.raw : __( 'Untitled product', 'larder' );
				const statusLabel = product.status && 'publish' !== product.status ? ' — ' + product.status : '';

				return el(
					'option',
					{
						key: product.id,
						value: String( product.id ),
					},
					rawTitle + statusLabel
				);
			} );

			return el(
				'div',
				blockProps,
				el( 'strong', null, __( 'Recommended Products', 'larder' ) ),
				el(
					'p',
					{ style: { marginBottom: '10px' } },
					attributes.useLinkedProducts
						? __( 'This block will display the Kitchen Products selected in the post’s Recommended Products panel.', 'larder' )
						: __( 'This block will display the Kitchen Products selected below.', 'larder' )
				),
				el(
					'label',
					{ style: fieldStyle },
					el( 'input', {
						type: 'checkbox',
						checked: !! attributes.useLinkedProducts,
						onChange: function ( event ) {
							setAttributes( { useLinkedProducts: event.target.checked } );
						},
					} ),
					' ',
					__( 'Use products linked to this recipe', 'larder' )
				),
				! attributes.useLinkedProducts
					? el(
						'label',
						{ style: fieldStyle },
						__( 'Kitchen Products', 'larder' ),
						el(
							'select',
							{
								multiple: true,
								size: 6,
								style: inputStyle,
								value: ( attributes.productIds || [] ).map( String ),
								onChange: function ( event ) {
									const values = Array.from( event.target.selectedOptions ).map( function ( option ) {
										return Number( option.value );
									} );
									setAttributes( { productIds: values } );
								},
							},
							productOptions
						),
						el( 'small', null, __( 'Hold Ctrl on Windows or Command on Mac to select more than one product.', 'larder' ) )
					)
					: null,
				el(
					'label',
					{ style: fieldStyle },
					__( 'Heading', 'larder' ),
					el( 'input', {
						type: 'text',
						style: inputStyle,
						value: attributes.heading || '',
						onChange: function ( event ) {
							setAttributes( { heading: event.target.value } );
						},
					} )
				),
				el(
					'label',
					{ style: fieldStyle },
					__( 'Optional introduction', 'larder' ),
					el( 'textarea', {
						rows: 4,
						style: inputStyle,
						value: attributes.intro || '',
						onChange: function ( event ) {
							setAttributes( { intro: event.target.value } );
						},
					} )
				),
				el(
					'label',
					{ style: fieldStyle },
					el( 'input', {
						type: 'checkbox',
						checked: !! attributes.showRetailerButton,
						onChange: function ( event ) {
							setAttributes( { showRetailerButton: event.target.checked } );
						},
					} ),
					' ',
					__( 'Show primary retailer button', 'larder' )
				),
				el(
					'p',
					{ style: { marginTop: '12px' } },
					__( 'The full product card is rendered on the public recipe page and in Preview.', 'larder' )
				)
			);
		},
		save: function () {
			return null;
		},
	} );
} )(
	window.wp.blocks,
	window.wp.blockEditor,
	window.wp.data,
	window.wp.element,
	window.wp.i18n
);
