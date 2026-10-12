document.querySelectorAll('.reservation-countdown').forEach((countdown) => {
	const expiresAt = Date.parse(`${countdown.dataset.expiresAt.replace(' ', 'T')}Z`);
	const stockEmpty = countdown.dataset.stockEmpty === 'true';
	let reloadStarted = false;
	let timerId;

	const updateCountdown = () => {
		if (Number.isNaN(expiresAt)) {
			countdown.textContent = 'Durée de réservation indisponible.';
			window.clearInterval(timerId);
			return;
		}

		const remainingSeconds = Math.max(0, Math.ceil((expiresAt - Date.now()) / 1000));
		const minutes = Math.floor(remainingSeconds / 60);
		const seconds = String(remainingSeconds % 60).padStart(2, '0');
		const prefix = stockEmpty ? 'Derniers exemplaires : ' : 'Réservation valable encore : ';
		countdown.textContent = `${prefix}${minutes}:${seconds}`;

		if (remainingSeconds === 0 && !reloadStarted) {
			reloadStarted = true;
			window.clearInterval(timerId);
			window.location.reload();
		}
	};

	updateCountdown();
	timerId = window.setInterval(updateCountdown, 1000);
});
