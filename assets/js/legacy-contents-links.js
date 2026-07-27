document.addEventListener('DOMContentLoaded', () => {
	const recipeContent = document.querySelector('.single-recipe .recipe-content');
	if (!recipeContent) {
		return;
	}

	const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	const normalise = (value) => value
		.trim()
		.toLowerCase()
		.normalize('NFD')
		.replace(/[\u0300-\u036f]/g, '')
		.replace(/&/g, ' and ')
		.replace(/\brecipe\s*[-:–—]?\s*/g, '')
		.replace(/[^a-z0-9]+/g, ' ')
		.trim();

	const makeId = (heading, index) => {
		const slug = normalise(heading.textContent).replace(/\s+/g, '-') || `section-${index + 1}`;
		let candidate = heading.id || `recipe-${slug}`;
		let suffix = 2;

		while (document.getElementById(candidate) && document.getElementById(candidate) !== heading) {
			candidate = `recipe-${slug}-${suffix}`;
			suffix += 1;
		}

		heading.id = candidate;
		return candidate;
	};

	const headings = Array.from(recipeContent.querySelectorAll('h2, h3')).filter((heading) => {
		return !heading.closest('.nkt-toc-panel, .wprm-recipe-container, .nkt-recipe-share-card, .nkt-pinterest-save');
	});

	const headingEntries = headings.map((heading, index) => ({
		heading,
		label: normalise(heading.textContent),
		id: makeId(heading, index),
	}));

	const recipeCard = document.getElementById('recipe-card') || document.querySelector('.wprm-recipe-container');
	if (recipeCard && !recipeCard.id) {
		recipeCard.id = 'recipe-card';
	}

	const findHeadingMatch = (linkLabel) => {
		const exact = headingEntries.find((entry) => entry.label === linkLabel);
		if (exact) {
			return exact;
		}

		return headingEntries.find((entry) => {
			return entry.label.includes(linkLabel) || linkLabel.includes(entry.label);
		});
	};

	const scrollToTarget = (target, hash) => {
		const header = document.querySelector('.site-header, header.site-header');
		const adminBar = document.getElementById('wpadminbar');
		const offset = (header?.offsetHeight || 0) + (adminBar?.offsetHeight || 0) + 24;
		const top = target.getBoundingClientRect().top + window.scrollY - offset;

		window.scrollTo({
			top: Math.max(0, top),
			behavior: prefersReducedMotion ? 'auto' : 'smooth',
		});

		window.history.replaceState(null, '', hash);
	};

	const tocPanels = Array.from(recipeContent.querySelectorAll('.nkt-toc-panel'));

	tocPanels.forEach((panel) => {
		panel.querySelectorAll('a').forEach((link) => {
			const linkLabel = normalise(link.textContent);
			if (!linkLabel) {
				return;
			}

			let target = null;
			let targetHash = '';

			if ((linkLabel === 'recipe' || linkLabel.startsWith('recipe ')) && recipeCard) {
				target = recipeCard;
				targetHash = '#recipe-card';
			} else {
				const match = findHeadingMatch(linkLabel);
				if (match) {
					target = match.heading;
					targetHash = `#${match.id}`;
				}
			}

			if (!target || !targetHash) {
				return;
			}

			link.href = targetHash;
			link.addEventListener('click', (event) => {
				event.preventDefault();
				scrollToTarget(target, targetHash);
			});
		});
	});
});
