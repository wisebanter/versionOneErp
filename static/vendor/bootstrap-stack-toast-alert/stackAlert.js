/*!
 * stackAlert.js
 * Plugin jQuery per creare alert impilati animati, stile Bootstrap
 * Creato da FVLogika
 */

(function ($) {
	/**
	 * Verifica se una determinata classe CSS è già presente in qualche foglio di stile.
	 * Questo evita che il plugin inietti stili duplicati.
	 */
	function cssClassExists(selector) {
		for (const sheet of document.styleSheets) {
			try {
				const rules = sheet.cssRules || sheet.rules;
				for (const rule of rules) {
					if (rule.selectorText === selector) return true;
				}
			} catch {
				// Ignora fogli esterni con restrizioni CORS (es. Bootstrap CDN)
				continue;
			}
		}
		return false;
	}

	/**
	 * Inietta il CSS necessario per gli alert impilati, solo se non già presente.
	 * Include animazione nativa via `opacity` e `transform`, senza @keyframes.
	 */
	function injectStyles() {
		if ($('#stackAlertStyles').length || cssClassExists('.alert-pro')) return;

		const css = `
			.alert-pro {
				border-style: solid;
				border-width: 2px 5px;
				opacity: 0;
				transform: translateY(-12px);
				transition: opacity 0.4s ease, transform 0.4s ease;
			}
			.alert-pro.showing {
				opacity: 1;
				transform: translateY(0);
			}
		`;

		$('<style>', {
			id: 'stackAlertStyles',
			type: 'text/css',
			text: css
		}).appendTo('head');
	}

	/**
	 * Metodo principale: genera un alert impilato visibile sullo schermo.
	 * Supporta configurazione tramite oggetto o via shorthand con 1-4 parametri.
	 */
	$.fn.stackAlert = function () {
		// Inietta gli stili alla prima chiamata
		injectStyles();

		const defaults = {
			message:	'Alert generico',	// Testo dell’alert
			type:		'secondary',		// Tipo (Bootstrap): info, success, danger, etc.
			timeout:	5000,			// Durata (ms) prima della chiusura automatica
			position:	'top-right'		// Posizione sullo schermo
		};

		let settings;

		// Supporto shorthand es: stackAlert("Messaggio", "danger", 3000, "bottom-left")
		if (typeof arguments[0] === 'string') {
			settings = {
				message:	arguments[0],
				type:		 arguments[1] ?? defaults.type,
				timeout:	arguments[2] ?? defaults.timeout,
				position: arguments[3] ?? defaults.position
			};
		} else {
			// Configurazione via oggetto es: { message: "...", type: "...", ... }
			settings = $.extend({}, defaults, arguments[0]);
		}

		/**
		 * Mappa dei tipi con icone associate (Font Awesome).
		 * Aggiunte anche primary, secondary, light e dark.
		 */
		const icons = {
			info:		'fa-circle-info',
			success:	'fa-circle-check',
			warning:	'fa-triangle-exclamation',
			danger:		'fa-circle-exclamation',
			primary:	'fa-star',
			secondary:	'fa-circle',
			light:		'fa-sun',
			dark:		'fa-moon'
		};

		// ID univoco per l’alert (usato per manipolazione DOM)
		const alertId = 'alert-' + Date.now();

		// ID del contenitore per questa posizione
		const containerId = 'alert-stack-' + settings.position;
		let $container = $('#' + containerId);

		// Posizioni supportate con coordinate CSS
		const positions = {
			'top-right':	{ top: '20px', right: '20px' },
			'bottom-right':	{ bottom: '20px', right: '20px' },
			'top-left':	{ top: '20px', left: '20px' },
			'bottom-left':	{ bottom: '20px', left: '20px' },
			'top-center':	{ top: '20px', left: '50%', transform: 'translateX(-50%)' },
			'bottom-center':{ bottom: '20px', left: '50%', transform: 'translateX(-50%)' }
		};

		// Se il contenitore non esiste, lo crea
		if (!$container.length) {
			$container = $('<div>', {
				id: containerId,
				css: $.extend({
					position: 'fixed',
					zIndex: 1050,
					minWidth: '25%'
				}, positions[settings.position])
			}).appendTo('body');

			// Centra il contenitore se necessario
			if (positions[settings.position].transform) {
				$container.css('transform', positions[settings.position].transform);
			}
		}

		// Costruisce l’HTML dell’alert
		const alertHtml = `
			<div id="${alertId}"
					 class="alert alert-${settings.type} alert-dismissible fade show alert-pro shadow"
					 role="alert"
					 style="margin-bottom: 10px;">
				<i class="fa-solid ${icons[settings.type] ?? icons['secondary']} me-2"></i>
				${settings.message}
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Chiudi"></button>
			</div>
		`;

		// Inserisce l’alert nel contenitore
		$container.append(alertHtml);

		// Attiva l’animazione (aggiunge classe .showing dopo breve delay)
		setTimeout(() => {
			$('#' + alertId).addClass('showing');
		}, 50);

		// Chiude automaticamente dopo timeout, se > 0
		if (settings.timeout > 0) {
			setTimeout(() => {
				$('#' + alertId).alert('close');
			}, settings.timeout);
		}

		return this; // Per supportare chaining jQuery
	};

	/**
	 * Metodo statico: rimuove tutti gli alert impilati da qualsiasi posizione.
	 */
	$.fn.stackAlert.closeAll = function () {
		$('[id^="alert-stack-"]').remove();
	};

	/**
	 * Rimuove tutti gli alert di una posizione specifica (es. "top-left")
	 */
	$.fn.stackAlert.closePosition = function (position) {
		$('#alert-stack-' + position).remove();
	};
})(jQuery);
