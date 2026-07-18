document.addEventListener('DOMContentLoaded', () => {
	const header = document.querySelector('.site-header');
	const menuToggle = document.querySelector('.menu-toggle');
	const navigation = document.querySelector('.primary-navigation');
	const searchToggle = document.querySelector('.search-toggle');
	const searchDialog = document.querySelector('.search-dialog');
	const searchPanel = searchDialog?.querySelector('.search-dialog__panel');
	const searchInput = searchDialog?.querySelector('input[type="search"]');
	const searchCloseButtons = searchDialog?.querySelectorAll('[data-search-close]') || [];
	let lastSearchFocus = null;
	let lastMenuFocus = null;

	const focusableElements = (container) => [...container.querySelectorAll(
		'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
	)].filter((element) => element.offsetParent !== null && !element.hasAttribute('inert'));

	const updateHeader = () => {
		header?.classList.toggle('is-scrolled', window.scrollY > 20);
	};

	const closeMenu = (restoreFocus = false) => {
		if (!menuToggle || !navigation) return;
		const wasOpen = navigation.classList.contains('is-open');
		navigation.classList.remove('is-open');
		menuToggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('menu-is-open');
		if (restoreFocus && wasOpen && lastMenuFocus instanceof HTMLElement) lastMenuFocus.focus();
	};

	const openMenu = () => {
		if (!menuToggle || !navigation) return;
		closeSearch(false);
		lastMenuFocus = document.activeElement;
		navigation.classList.add('is-open');
		menuToggle.setAttribute('aria-expanded', 'true');
		document.body.classList.add('menu-is-open');
		if (window.innerWidth <= 900) {
			window.setTimeout(() => focusableElements(navigation)[0]?.focus(), 30);
		}
	};

	const isSearchOpen = () => searchDialog?.classList.contains('is-open');

	const openSearch = () => {
		if (!searchDialog || !searchToggle) return;
		closeMenu();
		lastSearchFocus = document.activeElement;
		searchDialog.removeAttribute('inert');
		searchDialog.classList.add('is-open');
		searchDialog.setAttribute('aria-hidden', 'false');
		searchToggle.setAttribute('aria-expanded', 'true');
		document.body.classList.add('search-is-open');
		window.setTimeout(() => (searchInput || searchPanel)?.focus(), 50);
	};

	const closeSearch = (restoreFocus = true) => {
		if (!searchDialog || !searchToggle || !isSearchOpen()) return;
		searchDialog.classList.remove('is-open');
		searchDialog.setAttribute('aria-hidden', 'true');
		searchDialog.setAttribute('inert', '');
		searchToggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('search-is-open');
		if (restoreFocus && lastSearchFocus instanceof HTMLElement) lastSearchFocus.focus();
	};

	const trapFocus = (event, container) => {
		if (event.key !== 'Tab' || !container) return;
		const focusable = focusableElements(container);
		if (!focusable.length) {
			event.preventDefault();
			container.focus?.();
			return;
		}
		const first = focusable[0];
		const last = focusable[focusable.length - 1];
		if (event.shiftKey && document.activeElement === first) {
			event.preventDefault();
			last.focus();
		} else if (!event.shiftKey && document.activeElement === last) {
			event.preventDefault();
			first.focus();
		}
	};

	updateHeader();
	window.addEventListener('scroll', updateHeader, { passive: true });

	menuToggle?.addEventListener('click', () => {
		if (!navigation) return;
		if (navigation.classList.contains('is-open')) closeMenu(true);
		else openMenu();
	});

	navigation?.querySelectorAll('a').forEach((link) => link.addEventListener('click', () => closeMenu()));
	searchToggle?.addEventListener('click', openSearch);
	searchCloseButtons.forEach((button) => button.addEventListener('click', () => closeSearch()));

	document.addEventListener('click', (event) => {
		if (!navigation?.classList.contains('is-open')) return;
		if (navigation.contains(event.target) || menuToggle?.contains(event.target)) return;
		closeMenu();
	});

	document.addEventListener('keydown', (event) => {
		if (isSearchOpen()) trapFocus(event, searchPanel);
		else if (navigation?.classList.contains('is-open') && window.innerWidth <= 900) trapFocus(event, navigation);

		if (event.key === 'Escape') {
			if (isSearchOpen()) closeSearch();
			else closeMenu(true);
		}
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth > 900) closeMenu();
	});
});
