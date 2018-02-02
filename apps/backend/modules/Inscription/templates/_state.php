<?php
if ($sf_params->get('action') == 'edit' || $sf_params->get('action') == 'create' || $sf_params->get('action') == 'filter')  {
	echo select_tag('inscription[state]', options_for_select(InscriptionPeer::getStatesNames(), $inscription->getState()));
} else {
	echo InscriptionPeer::getStateName($inscription->getState());
}
?>