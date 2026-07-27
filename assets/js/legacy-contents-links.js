document.addEventListener('DOMContentLoaded', () => {
	const recipeContent = document.querySelector('.single-recipe .recipe-content');
	if (!recipeContent) {
		return;
	}

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
		if (heading.id) {
			return heading.id;
		}

		const slug = normalise(heading.textContent).replace(/\s+/g, '-') || `section-${index + 1}`;
		let candidate = `recipe-${slug}`;
		let suffix = 2;
		while (document.getElementById(candidate)) {
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
	const tocPanels = Array.from(recipeContent.querySelectorAll('.nkt-toc-panel'));

	tocPanels.forEach((panel) => {
		panel.querySelectorAll('a').forEach((link) => {
			const currentHash = link.hash ? decodeURIComponent(link.hash.slice(1)) : '';
			if (currentHash && document.getElementById(currentHash)) {
				return;
			}

			const linkLabel = normalise(link.textContent);
			if (!linkLabel) {
				return;
			}

			if ((linkLabel === 'recipe' || linkLabel.startsWith('recipe ')) && recipeCard) {
				link.href = '#recipe-card';
				return;
			}

			const match = headingEntries.find((entry) => {
				return entry.label === linkLabel || entry.label.includes(linkLabel) || linkLabel.includes(entry.label);
			});

			if (match) {
				link.href = `#${match.id}`;
			}
		});
	});
});
