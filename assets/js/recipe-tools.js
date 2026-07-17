document.addEventListener('DOMContentLoaded', () => {
	const recipeCard = document.querySelector('.wprm-recipe-container');
	const recipeContent = document.querySelector('.recipe-content');
	const printButtons = document.querySelectorAll('[data-print-recipe]');
	const cookModeButtons = document.querySelectorAll('[data-cook-mode]');
	let wakeLock = null;

	if (recipeCard && !document.getElementById('recipe-card')) {
		const recipeAnchor = document.createElement('span');
		recipeAnchor.id = 'recipe-card';
		recipeAnchor.className = 'recipe-anchor';
		recipeAnchor.setAttribute('aria-hidden', 'true');
		recipeCard.before(recipeAnchor);
	}

	if (recipeContent) {
		recipeContent.querySelectorAll('h2, h3').forEach((heading) => {
			if (heading.closest('.wprm-recipe-container, .nkt-recipe-share-card')) {
				return;
			}

			heading.classList.add('nkt-section-heading');
			const headingText = heading.textContent.trim().toLowerCase();
			const sectionName = headingText
				.replace(/[^a-z0-9]+/g, '-')
				.replace(/^-|-$/g, '');

			if (sectionName) {
				heading.dataset.recipeSection = sectionName;
			}

			if (headingText === 'contents') {
				heading.classList.add('nkt-toc-heading');
				const panel = heading.closest('.wp-block-group') || heading.parentElement;
				panel?.classList.add('nkt-toc-panel');

				let tocList = heading.nextElementSibling;
				while (tocList && !tocList.matches('ul, ol')) {
					tocList = tocList.nextElementSibling;
				}

				if (!tocList && panel) {
					tocList = panel.querySelector('ul, ol');
				}

				tocList?.classList.add('nkt-toc-list');
			}
		});
	}

	printButtons.forEach((button) => button.addEventListener('click', () => window.print()));

	const updateCookModeButtons = (enabled) => {
		cookModeButtons.forEach((button) => {
			button.setAttribute('aria-pressed', String(enabled));
			button.textContent = enabled ? 'Exit cook mode' : 'Cook mode';
		});
	};

	const requestWakeLock = async () => {
		if (!('wakeLock' in navigator) || document.visibilityState !== 'visible') {
			return;
		}

		try {
			wakeLock = await navigator.wakeLock.request('screen');
		} catch (error) {
			wakeLock = null;
		}
	};

	const setCookMode = async (enabled) => {
		document.body.classList.toggle('cook-mode', enabled);
		updateCookModeButtons(enabled);

		try {
			window.sessionStorage.setItem('nktCookMode', enabled ? '1' : '0');
		} catch (error) {
			// Session storage is optional.
		}

		if (enabled) {
			await requestWakeLock();
		} else if (wakeLock) {
			try {
				await wakeLock.release();
			} catch (error) {
				// The lock may already have been released by the browser.
			}
			wakeLock = null;
		}
	};

	let cookModeEnabled = false;
	try {
		cookModeEnabled = window.sessionStorage.getItem('nktCookMode') === '1';
	} catch (error) {
		cookModeEnabled = false;
	}

	if (cookModeEnabled) {
		setCookMode(true);
	} else {
		updateCookModeButtons(false);
	}

	cookModeButtons.forEach((button) => {
		button.addEventListener('click', () => setCookMode(!document.body.classList.contains('cook-mode')));
	});

	document.addEventListener('visibilitychange', () => {
		if (document.visibilityState === 'visible' && document.body.classList.contains('cook-mode') && !wakeLock) {
			requestWakeLock();
		}
	});

	document.querySelectorAll('.wprm-recipe-ingredient').forEach((ingredient) => {
		ingredient.setAttribute('tabindex', '0');
		ingredient.setAttribute('role', 'checkbox');
		ingredient.setAttribute('aria-checked', 'false');

		const toggle = () => {
			const checked = ingredient.classList.toggle('is-checked');
			ingredient.setAttribute('aria-checked', String(checked));
		};

		ingredient.addEventListener('click', toggle);
		ingredient.addEventListener('keydown', (event) => {
			if (event.key === ' ' || event.key === 'Enter') {
				event.preventDefault();
				toggle();
			}
		});
	});
});
