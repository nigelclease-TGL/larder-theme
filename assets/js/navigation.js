document.addEventListener('DOMContentLoaded', () => {
	const header = document.querySelector('.site-header');
	const toggle = document.querySelector('.menu-toggle');
	const navigation = document.querySelector('.primary-navigation');

	const updateHeader = () => {
		header?.classList.toggle('is-scrolled', window.scrollY > 20);
	};

	updateHeader();
	window.addEventListener('scroll', updateHeader, { passive: true });

	if (!toggle || !navigation) {
		return;
	}

	toggle.addEventListener('click', () => {
		const isOpen = navigation.classList.toggle('is-open');
		toggle.setAttribute('aria-expanded', String(isOpen));
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			navigation.classList.remove('is-open');
			toggle.setAttribute('aria-expanded', 'false');
		}
	});
});
