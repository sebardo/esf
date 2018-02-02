<?php
if ($sf_params->get('action') == 'edit' || $sf_params->get('action') == 'create') {
	echo select_tag('inscription[is_paid]', options_for_select(InscriptionPeer::getIsPaidNames(), $inscription->getIsPaid()));
} else {
	echo InscriptionPeer::getIsPaidName($inscription->getIsPaid());
}
?>