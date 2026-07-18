document.addEventListener('DOMContentLoaded', () => {
	const recipeCard = document.querySelector('.wprm-recipe-container');
	const recipeContent = document.querySelector('.recipe-content');
	const recipeArticle = document.querySelector('.recipe-article');
	const progressBar = document.querySelector('.nkt-reading-progress span');
	const printButtons = document.querySelectorAll('[data-print-recipe]');
	const cookModeButtons = document.querySelectorAll('[data-cook-mode]');
	const shareButtons = document.querySelectorAll('[data-share-recipe]');
	const guideButtons = document.querySelectorAll('[data-toggle-recipe-guide]');
	const resetIngredientButtons = document.querySelectorAll('[data-reset-ingredients]');
	const recipeGuide = document.querySelector('[data-recipe-guide]');
	const recipeToc = document.querySelector('[data-recipe-toc]');
	const shareStatus = document.querySelector('[data-share-status]');
	const backToTopButton = document.querySelector('[data-back-to-top]');
	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const sectionHeadings = [];
	const tocLinks = new Map();
	let wakeLock = null;
	let statusTimer = null;
	let scrollTicking = false;

	const announceStatus = (message) => {
		if (!shareStatus) {
			return;
		}

		window.clearTimeout(statusTimer);
		shareStatus.textContent = message;
		statusTimer = window.setTimeout(() => {
			shareStatus.textContent = '';
		}, 3500);
	};

	if (recipeCard && !document.getElementById('recipe-card')) {
		const recipeAnchor = document.createElement('span');
		recipeAnchor.id = 'recipe-card';
		recipeAnchor.className = 'recipe-anchor';
		recipeAnchor.setAttribute('aria-hidden', 'true');
		recipeCard.before(recipeAnchor);
	}

	const makeUniqueHeadingId = (heading, index) => {
		if (heading.id) {
			return heading.id;
		}

		const baseSlug = heading.textContent
			.trim()
			.toLowerCase()
			.normalize('NFD')
			.replace(/[\u0300-\u036f]/g, '')
			.replace(/[^a-z0-9]+/g, '-')
			.replace(/^-|-$/g, '') || `section-${index + 1}`;
		let candidate = `recipe-${baseSlug}`;
		let suffix = 2;

		while (document.getElementById(candidate)) {
			candidate = `recipe-${baseSlug}-${suffix}`;
			suffix += 1;
		}

		heading.id = candidate;
		return candidate;
	};

	if (recipeContent) {
		const headings = Array.from(recipeContent.querySelectorAll('h2, h3'));
		const mainHeadings = headings.filter((heading) => {
			if (heading.closest('.wprm-recipe-container, .nkt-recipe-share-card')) {
				return false;
			}

			return heading.tagName === 'H2';
		});

		headings.forEach((heading, index) => {
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

			if (mainHeadings.includes(heading) && headingText !== 'contents') {
				makeUniqueHeadingId(heading, index);
				sectionHeadings.push({
					target: heading,
					label: heading.textContent.trim(),
				});
			}
		});
	}

	const recipeAnchor = document.getElementById('recipe-card');
	if (recipeAnchor) {
		sectionHeadings.push({
			target: recipeAnchor,
			label: 'Recipe card',
		});
	}

	if (recipeToc && recipeGuide && sectionHeadings.length > 1) {
		sectionHeadings.forEach((section, index) => {
			const item = document.createElement('li');
			const link = document.createElement('a');
			const number = document.createElement('span');
			const label = document.createElement('span');

			link.href = `#${section.target.id}`;
			number.className = 'nkt-recipe-guide__number';
			number.textContent = String(index + 1).padStart(2, '0');
			label.className = 'nkt-recipe-guide__label';
			label.textContent = section.label;
			link.append(number, label);
			item.append(link);
			recipeToc.append(item);
			tocLinks.set(section.target, link);

			link.addEventListener('click', () => {
				if (window.matchMedia('(max-width: 760px)').matches) {
					recipeGuide.hidden = true;
					guideButtons.forEach((button) => button.setAttribute('aria-expanded', 'false'));
				}
			});
		});

		guideButtons.forEach((button) => {
			button.hidden = false;
			button.addEventListener('click', () => {
				const willOpen = recipeGuide.hidden;
				recipeGuide.hidden = !willOpen;
				guideButtons.forEach((guideButton) => guideButton.setAttribute('aria-expanded', String(willOpen)));
			});
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

	const shareRecipe = async () => {
		const shareData = {
			title: document.title,
			text: document.querySelector('.recipe-intro')?.textContent.trim() || '',
			url: window.location.href,
		};

		if (navigator.share) {
			try {
				await navigator.share(shareData);
				announceStatus('Recipe shared.');
				return;
			} catch (error) {
				if (error?.name === 'AbortError') {
					return;
				}
			}
		}

		try {
			await navigator.clipboard.writeText(window.location.href);
			announceStatus('Recipe link copied.');
		} catch (error) {
			window.prompt('Copy this recipe link:', window.location.href);
		}
	};

	shareButtons.forEach((button) => button.addEventListener('click', shareRecipe));

	const ingredients = Array.from(document.querySelectorAll('.wprm-recipe-ingredient'));
	const recipePostId = recipeArticle?.dataset.recipePostId || window.location.pathname;
	const ingredientStorageKey = `nktRecipeChecklist:${recipePostId}`;
	let savedIngredientIndexes = [];

	try {
		const savedValue = window.localStorage.getItem(ingredientStorageKey);
		savedIngredientIndexes = savedValue ? JSON.parse(savedValue) : [];
		if (!Array.isArray(savedIngredientIndexes)) {
			savedIngredientIndexes = [];
		}
	} catch (error) {
		savedIngredientIndexes = [];
	}

	const saveIngredientState = () => {
		const checkedIndexes = ingredients
			.map((ingredient, index) => (ingredient.classList.contains('is-checked') ? index : null))
			.filter((index) => index !== null);

		try {
			window.localStorage.setItem(ingredientStorageKey, JSON.stringify(checkedIndexes));
		} catch (error) {
			// Local storage is optional.
		}
	};

	const setIngredientChecked = (ingredient, checked) => {
		ingredient.classList.toggle('is-checked', checked);
		ingredient.setAttribute('aria-checked', String(checked));
	};

	if (ingredients.length) {
		resetIngredientButtons.forEach((button) => {
			button.hidden = false;
			button.addEventListener('click', () => {
				ingredients.forEach((ingredient) => setIngredientChecked(ingredient, false));
				try {
					window.localStorage.removeItem(ingredientStorageKey);
				} catch (error) {
					// Local storage is optional.
				}
				announceStatus('Ingredient checklist reset.');
			});
		});
	}

	ingredients.forEach((ingredient, index) => {
		ingredient.setAttribute('tabindex', '0');
		ingredient.setAttribute('role', 'checkbox');
		setIngredientChecked(ingredient, savedIngredientIndexes.includes(index));

		const toggle = () => {
			setIngredientChecked(ingredient, !ingredient.classList.contains('is-checked'));
			saveIngredientState();
		};

		ingredient.addEventListener('click', toggle);
		ingredient.addEventListener('keydown', (event) => {
			if (event.key === ' ' || event.key === 'Enter') {
				event.preventDefault();
				toggle();
			}
		});
	});

	const updateReadingExperience = () => {
		if (recipeArticle && progressBar) {
			const articleTop = recipeArticle.getBoundingClientRect().top + window.scrollY;
			const articleHeight = recipeArticle.offsetHeight;
			const viewportHeight = window.innerHeight;
			const availableDistance = Math.max(articleHeight - viewportHeight, 1);
			const travelledDistance = window.scrollY - articleTop;
			const progress = Math.min(1, Math.max(0, travelledDistance / availableDistance));
			progressBar.style.transform = `scaleX(${progress})`;
		}

		if (backToTopButton) {
			backToTopButton.hidden = window.scrollY < 900;
		}

		if (sectionHeadings.length) {
			let currentSection = sectionHeadings[0].target;
			sectionHeadings.forEach((section) => {
				if (section.target.getBoundingClientRect().top <= 180) {
					currentSection = section.target;
				}
			});

			tocLinks.forEach((link, target) => {
				if (target === currentSection) {
					link.setAttribute('aria-current', 'true');
				} else {
					link.removeAttribute('aria-current');
				}
			});
		}

		scrollTicking = false;
	};

	const requestReadingUpdate = () => {
		if (scrollTicking) {
			return;
		}

		scrollTicking = true;
		window.requestAnimationFrame(updateReadingExperience);
	};

	updateReadingExperience();
	window.addEventListener('scroll', requestReadingUpdate, { passive: true });
	window.addEventListener('resize', requestReadingUpdate);

	backToTopButton?.addEventListener('click', () => {
		window.scrollTo({
			top: 0,
			behavior: prefersReducedMotion ? 'auto' : 'smooth',
		});
	});
});
