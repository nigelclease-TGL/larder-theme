document.addEventListener('DOMContentLoaded', () => {
	const printButtons = document.querySelectorAll('[data-print-recipe]');
	const cookModeButtons = document.querySelectorAll('[data-cook-mode]');
	let wakeLock = null;

	printButtons.forEach((button) => button.addEventListener('click', () => window.print()));

	const setCookMode = async (enabled) => {
		document.body.classList.toggle('cook-mode', enabled);
		cookModeButtons.forEach((button) => {
			button.setAttribute('aria-pressed', String(enabled));
			button.textContent = enabled ? 'Exit cook mode' : 'Cook mode';
		});

		try {
			if (enabled && 'wakeLock' in navigator) {
				wakeLock = await navigator.wakeLock.request('screen');
			} else if (wakeLock) {
				await wakeLock.release();
				wakeLock = null;
			}
		} catch (error) {
			// Cook mode remains useful even when Wake Lock is unavailable.
		}
	};

	cookModeButtons.forEach((button) => {
		button.addEventListener('click', () => setCookMode(!document.body.classList.contains('cook-mode')));
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
