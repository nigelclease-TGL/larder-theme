document.addEventListener('DOMContentLoaded', () => {
	const header = document.querySelector('.site-header');
	const menuToggle = document.querySelector('.menu-toggle');
	const navigation = document.querySelector('.primary-navigation');
	const searchToggle = document.querySelector('.search-toggle');
	const searchDialog = document.querySelector('.search-dialog');
	const searchInput = searchDialog?.querySelector('input[type="search"]');
	const searchCloseButtons = searchDialog?.querySelectorAll('[data-search-close]') || [];
	let lastFocusedElement = null;

	const updateHeader = () => {
		header?.classList.toggle('is-scrolled', window.scrollY > 20);
	};

	const closeMenu = () => {
		if (!menuToggle || !navigation) return;
		navigation.classList.remove('is-open');
		menuToggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('menu-is-open');
	};

	const openSearch = () => {
		if (!searchDialog || !searchToggle) return;
		lastFocusedElement = document.activeElement;
		searchDialog.classList.add('is-open');
		searchDialog.setAttribute('aria-hidden', 'false');
		searchToggle.setAttribute('aria-expanded', 'true');
		document.body.classList.add('search-is-open');
		window.setTimeout(() => searchInput?.focus(), 50);
	};

	const closeSearch = () => {
		if (!searchDialog || !searchToggle) return;
		searchDialog.classList.remove('is-open');
		searchDialog.setAttribute('aria-hidden', 'true');
		searchToggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('search-is-open');
		lastFocusedElement?.focus();
	};

	updateHeader();
	window.addEventListener('scroll', updateHeader, { passive: true });

	menuToggle?.addEventListener('click', () => {
		if (!navigation) return;
		const isOpen = navigation.classList.toggle('is-open');
		menuToggle.setAttribute('aria-expanded', String(isOpen));
		document.body.classList.toggle('menu-is-open', isOpen);
	});

	searchToggle?.addEventListener('click', openSearch);
	searchCloseButtons.forEach((button) => button.addEventListener('click', closeSearch));

	document.addEventListener('click', (event) => {
		if (!navigation?.classList.contains('is-open')) return;
		if (navigation.contains(event.target) || menuToggle?.contains(event.target)) return;
		closeMenu();
	});

	document.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			closeMenu();
			closeSearch();
		}
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth > 900) closeMenu();
	});
});