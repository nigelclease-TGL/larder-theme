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
	const uppercaseLetterPattern = /[A-ZÀ-ÖØ-Þ]/;
	const lowercaseLetterPattern = /[a-zà-öø-ÿ]/;
	const firstLetterPattern = /[A-Za-zÀ-ÖØ-öø-ÿ]/;
	const protectedAcronyms = new Set([
		'ai',
		'bbq',
		'css',
		'eu',
		'faq',
		'faqs',
		'html',
		'pdf',
		'seo',
		'uk',
		'us',
		'usa',
		'wprm',
	]);
	const protectedAcronymPattern = /\b(?:ai|bbq|css|eu|faqs?|html|pdf|seo|uk|us|usa|wprm)\b/gi;

	const sentenceCaseHeading = (heading) => {
		const headingText = heading.textContent.trim();
		if (
			!headingText
			|| !uppercaseLetterPattern.test(headingText)
			|| lowercaseLetterPattern.test(headingText)
			|| protectedAcronyms.has(headingText.toLowerCase())
		) {
			return;
		}

		const textNodes = [];
		const collectTextNodes = (node) => {
			node.childNodes.forEach((child) => {
				if (child.nodeType === Node.TEXT_NODE) {
					textNodes.push(child);
				} else {
					collectTextNodes(child);
				}
			});
		};

		collectTextNodes(heading);
		let firstLetterCapitalised = false;

		textNodes.forEach((textNode) => {
			let value = textNode.nodeValue.toLocaleLowerCase('en-GB');
			value = value.replace(protectedAcronymPattern, (acronym) => acronym.toLocaleUpperCase('en-GB'));

			if (!firstLetterCapitalised && firstLetterPattern.test(value)) {
				value = value.replace(firstLetterPattern, (letter) => letter.toLocaleUpperCase('en-GB'));
				firstLetterCapitalised = true;
			}

			textNode.nodeValue = value;
		});

		heading.dataset.nktSentenceCase = 'true';
	};

	Array.from(recipeContent.querySelectorAll('h2')).forEach((heading) => {
		if (!heading.closest(excludedSelector)) {
			sentenceCaseHeading(heading);
		}
	});

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

	const makeElementId = (element, preferredId) => {
		if (element.id && (!document.getElementById(element.id) || document.getElementById(element.id) === element)) {
			return element.id;
		}

		let candidate = preferredId;
		let suffix = 2;

		while (document.getElementById(candidate) && document.getElementById(candidate) !== element) {
			candidate = `${preferredId}-${suffix}`;
			suffix += 1;
		}

		element.id = candidate;
		return candidate;
	};

	const recipeCards = Array.from(recipeContent.querySelectorAll('.wprm-recipe-container'));
	const existingPrimaryRecipeAnchor = document.getElementById('recipe-card');
	const recipeEntries = recipeCards.map((card, index) => {
		let target = card;

		if (
			index === 0
			&& existingPrimaryRecipeAnchor
			&& recipeContent.contains(existingPrimaryRecipeAnchor)
			&& existingPrimaryRecipeAnchor !== card
		) {
			target = existingPrimaryRecipeAnchor;
		}

		const preferredId = index === 0 ? 'recipe-card' : `recipe-card-${index + 1}`;
		const id = makeElementId(target, preferredId);
		const recipeNameElement = card.querySelector('.wprm-recipe-name, .wprm-recipe-title, [data-recipe-name]');
		const recipeName = recipeNameElement?.textContent.trim() || card.dataset.recipeName?.trim() || '';
		const label = recipeCards.length === 1
			? 'Recipe card'
			: recipeName
				? `Recipe: ${recipeName}`
				: `Recipe card ${index + 1}`;

		return {
			card,
			id,
			label,
			sourceHeading: null,
			target,
		};
	});

	const usedRecipeEntries = new Set();
	const findRecipeEntryForHeading = (heading) => {
		const followingRecipe = recipeEntries.find((entry) => {
			return !usedRecipeEntries.has(entry)
				&& Boolean(heading.compareDocumentPosition(entry.card) & Node.DOCUMENT_POSITION_FOLLOWING);
		});

		return followingRecipe || recipeEntries.find((entry) => !usedRecipeEntries.has(entry)) || null;
	};

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

			if (isRecipeCardHeading && recipeEntries.length) {
				const matchingRecipeEntry = findRecipeEntryForHeading(heading);
				if (matchingRecipeEntry) {
					usedRecipeEntries.add(matchingRecipeEntry);
					return {
						...matchingRecipeEntry,
						sourceHeading: heading,
					};
				}
			}

			return {
				id: makeId(heading, index),
				label,
				sourceHeading: heading,
				target: heading,
			};
		});

	recipeEntries.forEach((entry) => {
		if (!usedRecipeEntries.has(entry)) {
			sectionEntries.push(entry);
		}
	});

	const uniqueEntries = sectionEntries
		.filter((entry, index, entries) => {
			return entries.findIndex((candidate) => candidate.id === entry.id) === index;
		})
		.sort((firstEntry, secondEntry) => {
			if (firstEntry.target === secondEntry.target) {
				return 0;
			}

			const position = firstEntry.target.compareDocumentPosition(secondEntry.target);
			if (position & Node.DOCUMENT_POSITION_FOLLOWING) {
				return -1;
			}
			if (position & Node.DOCUMENT_POSITION_PRECEDING) {
				return 1;
			}
			return 0;
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
	const insertionTarget = firstSectionHeading || recipeEntries[0]?.target;

	if (insertionTarget) {
		insertionTarget.before(contentsPanel);
	} else {
		recipeContent.prepend(contentsPanel);
	}
});
