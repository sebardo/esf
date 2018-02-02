<?php
echo count(InscriptionPeer::doSelectInscriptionsConfirmedByCourse($course->getId()));