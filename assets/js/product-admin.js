(function ($) {
	'use strict';

	function updateGalleryInput($box) {
		var ids = [];
		$box.find('[data-product-gallery-list] [data-attachment-id]').each(function () {
			ids.push($(this).data('attachment-id'));
		});
		$box.find('[data-product-gallery-input]').val(ids.join(','));
	}

	$(function () {
		var $retailerRows = $('[data-retailer-rows]');

		$('[data-add-retailer]').on('click', function () {
			var index = parseInt($retailerRows.attr('data-next-index'), 10) || 0;
			var template = $('#tmpl-nkt-retailer-row').html().replace(/__INDEX__/g, String(index));
			$retailerRows.append(template);
			$retailerRows.attr('data-next-index', index + 1);
		});

		$(document).on('click', '[data-remove-retailer]', function () {
			var $row = $(this).closest('[data-retailer-row]');
			var wasPrimary = $row.find('input[type="radio"]').is(':checked');
			$row.remove();
			if (wasPrimary) {
				$retailerRows.find('input[type="radio"]').first().prop('checked', true);
			}
		});

		$('.nkt-product-gallery').each(function () {
			var $box = $(this).closest('#nkt-product-gallery');
			var frame;

			$box.on('click', '[data-select-product-gallery]', function () {
				if (frame) {
					frame.open();
					return;
				}

				frame = wp.media({
					title: nktProductAdmin.galleryTitle,
					button: { text: nktProductAdmin.galleryButton },
					multiple: true,
					library: { type: 'image' }
				});

				frame.on('select', function () {
					var $list = $box.find('[data-product-gallery-list]');
					frame.state().get('selection').each(function (attachment) {
						var data = attachment.toJSON();
						if ($list.find('[data-attachment-id="' + data.id + '"]').length) {
							return;
						}
						var imageUrl = data.sizes && data.sizes.thumbnail ? data.sizes.thumbnail.url : data.url;
						$list.append('<li data-attachment-id="' + data.id + '"><img src="' + imageUrl + '" alt=""><button type="button" class="button-link-delete" data-remove-gallery-image aria-label="Remove image">×</button></li>');
					});
					updateGalleryInput($box);
				});

				frame.open();
			});

			$box.on('click', '[data-remove-gallery-image]', function () {
				$(this).closest('[data-attachment-id]').remove();
				updateGalleryInput($box);
			});

			$box.on('click', '[data-clear-product-gallery]', function () {
				$box.find('[data-product-gallery-list]').empty();
				updateGalleryInput($box);
			});
		});
	});
})(jQuery);
