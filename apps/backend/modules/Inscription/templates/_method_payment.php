<?php
if ($sf_params->get('action') == 'edit' || $sf_params->get('action') == 'create') {
	echo select_tag('inscription[method_payment]', options_for_select(InscriptionPeer::getMethodPaymentNames(), $inscription->getMethodPayment()));
} else {
	echo InscriptionPeer::getMethodPaymentName($inscription->getMethodPayment());
}
?>