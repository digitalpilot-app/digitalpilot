/*
 * Admin DigitalPilot Scripts
 */
;
(function ($) {
	//"use strict";

	var digitalpilot = {

		// Start Functions
		startAt: function () {
			digitalpilot.Init();
		},

		Init: function () {
			console.log('Init DigitalPilot.app Admin');
		},
	};

	$(document).ready(function () {
		digitalpilot.startAt();
	});
})(jQuery);
