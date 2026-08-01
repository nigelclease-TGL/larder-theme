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
		.replace(/[^a-z0-9]+/g, ' ')
		.trim();

	const slugify = (value) => normalise(value).replace(/\s+/g, '-');
	const excludedSelector = [
		'.wprm-recipe-container',
		'.nkt-recipe-share-card',
		'.nkt-pinterest-save',
		'.nkt-recipe-guide',
		'.recipe-share',
	].join(', ');

	const contentsHeadings = Array.from(recipeContent.querySelectorAll('h2, h3')).filter((heading) => {
		return normalise(heading.textContent) === 'contents';
	});

	const findContentsPanel = (heading) => {
		const candidates = [];
		let current = heading.parentElement;

		while (current && current !== recipeContent) {
			if (current.matches('nav, section, .nkt-toc-panel, .wp-block-group, .wp-block-cover, .wp-block-columns')) {
				candidates.push(current);
			}
			current = current.parentElement;
		}

		const containsNoArticleHeadings = (candidate) => {
			return !Array.from(candidate.querySelectorAll('h2, h3')).some((candidateHeading) => {
				return candidateHeading !== heading && normalise(candidateHeading.textContent) !== 'contents';
			});
		};

		return candidates.find((candidate) => candidate.querySelector('ul, ol') && containsNoArticleHeadings(candidate))
			|| candidates.find(containsNoArticleHeadings)
			|| heading.parentElement;
	};

	const existingPanels = Array.from(new Set(contentsHeadings.map(findContentsPanel).filter(Boolean)));
	const isInsideExistingPanel = (heading) => existingPanels.some((panel) => panel.contains(heading));

	const makeId = (heading, index) => {
		const slug = slugify(heading.textContent) || `section-${index + 1}`;
		let candidate = heading.id || `recipe-${slug}`;
		let suffix = 2;

		while (document.getElementById(candidate) && document.getElementById(candidate) !== heading) {
			candidate = `recipe-${slug}-${suffix}`;
			suffix += 1;
		}

		heading.id = candidate;
		return candidate;
	};

	let recipeTarget = document.getElementById('recipe-card');
	if (!recipeTarget) {
		recipeTarget = recipeContent.querySelector('.wprm-recipe-container');
		if (recipeTarget) {
			recipeTarget.id = 'recipe-card';
		}
	}

	let recipeCardIncluded = false;
	const sectionEntries = Array.from(recipeContent.querySelectorAll('h2'))
		.filter((heading) => {
			return !isInsideExistingPanel(heading)
				&& !heading.closest(excludedSelector)
				&& normalise(heading.textContent) !== 'contents';
		})
		.map((heading, index) => {
			const label = heading.textContent.trim();
			const normalisedLabel = normalise(label);
			const isRecipeCardHeading = [
				'recipe',
				'recipe card',
				'the recipe',
				'printable recipe',
			].includes(normalisedLabel);

			if (isRecipeCardHeading && recipeTarget) {
				recipeCardIncluded = true;
				return {
					id: recipeTarget.id,
					label: 'Recipe Card',
					sourceHeading: heading,
					target: recipeTarget,
				};
			}

			return {
				id: makeId(heading, index),
				label,
				sourceHeading: heading,
				target: heading,
			};
		});

	if (recipeTarget && !recipeCardIncluded) {
		sectionEntries.push({
			id: recipeTarget.id,
			label: 'Recipe Card',
			sourceHeading: null,
			target: recipeTarget,
		});
	}

	const uniqueEntries = sectionEntries.filter((entry, index, entries) => {
		return entries.findIndex((candidate) => candidate.id === entry.id) === index;
	});

	if (!uniqueEntries.length) {
		existingPanels.forEach((panel) => panel.remove());
		return;
	}

	const makeUniqueContentsHeadingId = () => {
		let candidate = 'recipe-contents';
		let suffix = 2;

		while (document.getElementById(candidate)) {
			candidate = `recipe-contents-${suffix}`;
			suffix += 1;
		}

		return candidate;
	};

	const contentsPanel = document.createElement('nav');
	const contentsHeading = document.createElement('h2');
	const contentsList = document.createElement('ul');
	const contentsHeadingId = makeUniqueContentsHeadingId();

	contentsPanel.className = 'nkt-toc-panel';
	contentsPanel.dataset.nktGenerated = 'true';
	contentsPanel.setAttribute('aria-labelledby', contentsHeadingId);
	contentsHeading.className = 'nkt-toc-heading';
	contentsHeading.id = contentsHeadingId;
	contentsHeading.textContent = 'Contents';
	contentsList.className = 'nkt-toc-list';

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

	uniqueEntries.forEach((entry) => {
		const item = document.createElement('li');
		const link = document.createElement('a');
		const hash = `#${entry.id}`;

		link.href = hash;
		link.textContent = entry.label;
		link.addEventListener('click', (event) => {
			event.preventDefault();
			scrollToTarget(entry.target, hash);
		});

		item.append(link);
		contentsList.append(item);
	});

	contentsPanel.append(contentsHeading, contentsList);

	if (existingPanels.length) {
		existingPanels[0].replaceWith(contentsPanel);
		existingPanels.slice(1).forEach((panel) => panel.remove());
		return;
	}

	const firstSectionHeading = uniqueEntries.find((entry) => entry.sourceHeading)?.sourceHeading;
	const insertionTarget = firstSectionHeading || recipeTarget;

	if (insertionTarget) {
		insertionTarget.before(contentsPanel);
	} else {
		recipeContent.prepend(contentsPanel);
	}
});
