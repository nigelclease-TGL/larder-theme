document.addEventListener('DOMContentLoaded', () => {
	const emitConversion = (eventName, label = '', location = '', destination = '') => {
		if (!eventName) return;

		const detail = {
			event_name: eventName,
			event_label: label,
			event_location: location,
			destination,
		};

		window.dispatchEvent(new CustomEvent('nkt:conversion', { detail }));

		if (Array.isArray(window.dataLayer)) {
			window.dataLayer.push({
				event: 'nkt_conversion',
				...detail,
			});
		}
	};

	document.addEventListener('click', (event) => {
		const target = event.target instanceof Element ? event.target.closest('[data-nkt-event]') : null;
		if (!target) return;

		const location = target.closest('[data-nkt-location]')?.dataset.nktLocation || '';
		let destination = '';

		if (target instanceof HTMLAnchorElement) {
			try {
				const url = new URL(target.href, window.location.href);
				destination = `${url.hostname}${url.pathname}`;
			} catch (error) {
				destination = '';
			}
		}

		emitConversion(
			target.dataset.nktEvent || '',
			target.dataset.nktLabel || target.textContent.trim(),
			location,
			destination
		);
	});

	document.querySelectorAll('.mc4wp-form').forEach((form) => {
		form.addEventListener('submit', () => {
			const location = form.closest('[data-nkt-location]')?.dataset.nktLocation || 'newsletter';
			emitConversion('newsletter_submit', 'Mailchimp form', location);
		});
	});

	document.addEventListener('wpcf7mailsent', () => {
		emitConversion('contact_form_success', 'Contact Form 7', 'contact_page');
	});
});
