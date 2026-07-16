document.addEventListener('DOMContentLoaded', () => {
	const header = document.querySelector('.site-header');
	const menuToggle = document.querySelector('.menu-toggle');
	const navigation = document.querySelector('.primary-navigation');
	const searchToggle = document.querySelector('.search-toggle');
	const searchDialog = document.querySelector('.search-dialog');
	const searchPanel = searchDialog?.querySelector('.search-dialog__panel');
	const searchInput = searchDialog?.querySelector('input[type="search"]');
	const searchCloseButtons = searchDialog?.querySelectorAll('[data-search-close]') || [];
	let lastFocusedElement = null;

	const updateHeader = () => {
		header?.classList.toggle('is-scrolled', window.scrollY > 20);
	};

	const closeMenu = (restoreFocus = false) => {
		if (!menuToggle || !navigation) return;
		const wasOpen = navigation.classList.contains('is-open');
		navigation.classList.remove('is-open');
		menuToggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('menu-is-open');
		if (restoreFocus && wasOpen) menuToggle.focus();
	};

	const isSearchOpen = () => searchDialog?.classList.contains('is-open');

	const openSearch = () => {
		if (!searchDialog || !searchToggle) return;
		closeMenu();
		lastFocusedElement = document.activeElement;
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
		searchToggle.setAttribute('aria-expanded', 'false');
		document.body.classList.remove('search-is-open');
		if (restoreFocus && lastFocusedElement instanceof HTMLElement) {
			lastFocusedElement.focus();
		}
	};

	const trapSearchFocus = (event) => {
		if (event.key !== 'Tab' || !isSearchOpen() || !searchPanel) return;

		const focusable = [...searchPanel.querySelectorAll(
			'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
		)].filter((element) => element.offsetParent !== null);

		if (!focusable.length) {
			event.preventDefault();
			searchPanel.focus();
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
		closeSearch(false);
		const isOpen = navigation.classList.toggle('is-open');
		menuToggle.setAttribute('aria-expanded', String(isOpen));
		document.body.classList.toggle('menu-is-open', isOpen);
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
		trapSearchFocus(event);
		if (event.key === 'Escape') {
			if (isSearchOpen()) {
				closeSearch();
			} else {
				closeMenu(true);
			}
		}
	});

	window.addEventListener('resize', () => {
		if (window.innerWidth > 900) closeMenu();
	});
});
