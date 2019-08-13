//CONSTANTES
var FIDELITY = "fidelity";
var SPONSORSHIP = "sponsorship";
var SEPARATED = "separated";
var ACCUMULATED = "accumulated";

Event.observe(window, 'load', function() {
	changeTab();
	Event.observe($('auguria_sponsorship_general_module_mode'), 'change', function() {
		changeTab();
	});
});

function changeTab(){

	var mode = $('auguria_sponsorship_general_module_mode').getValue();

	switch(mode){
	case FIDELITY: // Mode Fidélité

		// SHOW Fidélité
		if($('auguria_sponsorship_fidelity-head').up('.section-config')) {
			$('auguria_sponsorship_fidelity-head').up('.section-config').addClassName('active').show();
		}	
		$('auguria_sponsorship_fidelity').show();

		// HIDE Parrainage
		if($('auguria_sponsorship_sponsor-head').up('.section-config')) {
			$('auguria_sponsorship_sponsor-head').up('.section-config').removeClassName('active').hide();
		}
		$('auguria_sponsorship_sponsor').hide();

		// HIDE Accumulé
		if($('auguria_sponsorship_accumulated-head').up('.section-config')) {
			$('auguria_sponsorship_accumulated-head').up('.section-config').removeClassName('active').hide();
		}
		$('auguria_sponsorship_accumulated').hide();

		break;
	case SPONSORSHIP: // Mode Parrainage

		// HIDE Fidélité
		if($('auguria_sponsorship_fidelity-head').up('.section-config')) {
			$('auguria_sponsorship_fidelity-head').up('.section-config').removeClassName('active').hide();
		}
		$('auguria_sponsorship_fidelity').hide();

		// SHOW Parrainage
		if($('auguria_sponsorship_sponsor-head').up('.section-config')) {
			$('auguria_sponsorship_sponsor-head').up('.section-config').addClassName('active').show();
		}
		$('auguria_sponsorship_sponsor').show();

		// HIDE Accumulé
		if($('auguria_sponsorship_accumulated-head').up('.section-config')) {
			$('auguria_sponsorship_accumulated-head').up('.section-config').removeClassName('active').hide();
		}
		$('auguria_sponsorship_accumulated').hide();

		break;
	case SEPARATED: // Fidélité et parrainage (séparé)

		// SHOW Fidélité
		if($('auguria_sponsorship_fidelity-head').up('.section-config')) {
			$('auguria_sponsorship_fidelity-head').up('.section-config').addClassName('active').show();
		}
		$('auguria_sponsorship_fidelity').show();

		// SHOW Parrainage
		if($('auguria_sponsorship_sponsor-head').up('.section-config')) {
			$('auguria_sponsorship_sponsor-head').up('.section-config').addClassName('active').show();
		}
		$('auguria_sponsorship_sponsor').show();

		// HIDE Accumulé
		if($('auguria_sponsorship_accumulated-head').up('.section-config')) {
			$('auguria_sponsorship_accumulated-head').up('.section-config').removeClassName('active').hide();
		}
		$('auguria_sponsorship_accumulated').hide();

		break;
	case ACCUMULATED: // Fidélité et parrainage (accumulé)

		// HIDE Fidélité
		if($('auguria_sponsorship_fidelity-head').up('.section-config')) {
			$('auguria_sponsorship_fidelity-head').up('.section-config').removeClassName('active').hide();
		}
		$('auguria_sponsorship_fidelity').hide();

		// HIDE Parrainage
		if($('auguria_sponsorship_sponsor-head').up('.section-config')) {
			$('auguria_sponsorship_sponsor-head').up('.section-config').removeClassName('active').hide();
		}
		$('auguria_sponsorship_sponsor').hide();

		// SHOW Accumulé
		if($('auguria_sponsorship_accumulated-head').up('.section-config')) {
			$('auguria_sponsorship_accumulated-head').up('.section-config').addClassName('active').show();
		}
		$('auguria_sponsorship_accumulated').show();

		break;
	default:
		mode = "ACCUMULATED";
	break;
	}
}
