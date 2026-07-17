document.addEventListener('DOMContentLoaded', () => {
	const recipeCard = document.querySelector('.wprm-recipe-container');
	const recipeContent = document.querySelector('.recipe-content');
	const recipeArticle = document.querySelector('.recipe-article');
	const progressBar = document.querySelector('.nkt-reading-progress span');
	const printButtons = document.querySelectorAll('[data-print-recipe]');
	const cookModeButtons = document.querySelectorAll('[data-cook-mode]');
	const shareButtons = document.querySelectorAll('[data-share-recipe]');
	const shareStatus = document.querySelector('[data-share-status]');
	let wakeLock = null;
	let shareStatusTimer = null;

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

	const announceShareStatus = (message) => {
		if (!shareStatus) {
			return;
		}

		window.clearTimeout(shareStatusTimer);
		shareStatus.textContent = message;
		shareStatusTimer = window.setTimeout(() => {
			shareStatus.textContent = '';
		}, 3500);
	};

	const shareRecipe = async () => {
		const shareData = {
			title: document.title,
			text: document.querySelector('.recipe-intro')?.textContent.trim() || '',
			url: window.location.href,
		};

		if (navigator.share) {
			try {
				await navigator.share(shareData);
				announceShareStatus('Recipe shared.');
				return;
			} catch (error) {
				if (error?.name === 'AbortError') {
					return;
				}
			}
		}

		try {
			await navigator.clipboard.writeText(window.location.href);
			announceShareStatus('Recipe link copied.');
		} catch (error) {
			window.prompt('Copy this recipe link:', window.location.href);
		}
	};

	shareButtons.forEach((button) => button.addEventListener('click', shareRecipe));

	const updateReadingProgress = () => {
		if (!recipeArticle || !progressBar) {
			return;
		}

		const articleTop = recipeArticle.getBoundingClientRect().top + window.scrollY;
		const articleHeight = recipeArticle.offsetHeight;
		const viewportHeight = window.innerHeight;
		const availableDistance = Math.max(articleHeight - viewportHeight, 1);
		const travelledDistance = window.scrollY - articleTop;
		const progress = Math.min(1, Math.max(0, travelledDistance / availableDistance));
		progressBar.style.transform = `scaleX(${progress})`;
	};

	if (recipeArticle && progressBar) {
		updateReadingProgress();
		window.addEventListener('scroll', updateReadingProgress, { passive: true });
		window.addEventListener('resize', updateReadingProgress);
	}

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