<?php

/**
 * tpv actions.
 *
 * @package    kids
 * @subpackage tpv
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
class tpvActions extends sfActions
{
    /**
     * @param $paymentNumber Número de pago (1 o 2)
     * @throws sfError404Exception
     */
    private function preTpvPayment($paymentNumber, $inscriptionId = null)
    {
        if (!$inscriptionId) {
            $inscriptionId = $this->getRequestParameter('inscriptionId');
        }
        /** @var Inscription $inscription */
        $inscription = InscriptionPeer::retrieveByPK($inscriptionId);
        $this->forward404Unless($inscription);

        if ($inscription->getMethodPayment() != InscriptionPeer::METHOD_PAYMENT_TPV) {
            $this->forward404('El método de pago no es TPV');
        }

        if ($paymentNumber == 1 && $inscription->getIsPaid() != InscriptionPeer::IS_PAID_TPV) {
            $this->forward404('Error: Primer pago con estado pagado');
        }

        if ($paymentNumber == 2 && $inscription->getIsPaid() == InscriptionPeer::IS_PAID_TPV) {
            $this->forward404('Error: Segundo pago sin primer pago confirmado');
        }

        $amount = $inscription->getPendingAmountFromAllInscriptions();
        if ($amount == 0) {
            $this->forward404('Error: No hay importe pendiente de pago');
        }

        if ($paymentNumber == 1 && $inscription->getSplitPayment()) {
            $amount = $amount / 2;
        }

        $amountToPay = number_format(round($amount, 2), 2);
        $amountToPay = str_replace('.', '', $amountToPay);
        $amountToPay = str_replace(',', '', $amountToPay);

        if ($inscription->getTpvSuffix()) {
            $inscription->setTpvSuffix($inscription->getTpvSuffix() + 1);
        }
        else {
            $inscription->setTpvSuffix(1);
        }

        $inscription->save();

        // Asignamos aquí porque se usa en la acción de segundo pago (función executeSecondPayment())
        $this->inscription = $inscription;
        
        $merchantTransactionType = 0;
        $merchantCurrency = 978;
        $merchantTerminal = 1;
        $merchantCode = $inscription->getCourse()->getSummerFunCenter()->getMerchantCode();
        $merchantKey = $inscription->getCourse()->getSummerFunCenter()->getMerchantKey();
        $this->urlTpv = $inscription->getCourse()->getSummerFunCenter()->getUrlTpv();

        $urlResponse = $this->getController()->genUrl('@tpv_payment_notification?payment=' . $paymentNumber . '&number=' . $inscription->getId() . '-' . $inscription->getTpvSuffix(), true);

        sfLoader::loadHelpers(array('Url'));

        $tpv = new RedsysAPI();
        $tpv->setParameter("DS_MERCHANT_AMOUNT", $amountToPay);
        $tpv->setParameter("DS_MERCHANT_ORDER", $inscription->getId() . '-' . $inscription->getTpvSuffix());
        $tpv->setParameter("DS_MERCHANT_MERCHANTCODE", $merchantCode);
        $tpv->setParameter("DS_MERCHANT_CURRENCY", $merchantCurrency);
        $tpv->setParameter("DS_MERCHANT_TRANSACTIONTYPE", $merchantTransactionType);
        $tpv->setParameter("DS_MERCHANT_TERMINAL", $merchantTerminal);
        $tpv->setParameter("DS_MERCHANT_MERCHANTURL", $urlResponse);
        $tpv->setParameter("DS_MERCHANT_URLOK", url_for("@user_payment_notification?status=ok&payment=$paymentNumber&number=" . $inscription->getId() . '-' . $inscription->getTpvSuffix(), true));
        $tpv->setParameter("DS_MERCHANT_URLKO", url_for("@user_payment_notification?status=ko&payment=$paymentNumber&number=" . $inscription->getId() . '-' . $inscription->getTpvSuffix(), true));

        $this->version="HMAC_SHA256_V1";
        $this->params = $tpv->createMerchantParameters();
        $this->signature = $tpv->createMerchantSignature($merchantKey);
    }

    /**
     * Acción ejecutada trás finalizar la inscripción si se ha elegido como forma de pago TPV.
     * Se prepara el formulario que se enviará a la pasarela de pago y se le informa al usuario que se va a proceder al pago.
     * 
     * @throws sfError404Exception
     */

    public function executeLaunchPayment()
    {
        $this->preTpvPayment(1);
    }

    /**
     * Url callback para informar de la transacción al usuario
     * @throws sfError404Exception
     */

    public function executePaymentNotificationUser()
    {
        $inscription = InscriptionPeer::findByIdAndTpvSuffix($this->getRequestParameter('number'));

        if (!$inscription) {
            $this->forward404();
        }

        $this->status = $this->getRequestParameter('status');
        $this->payment = intval($this->getRequestParameter('payment'));

        if ($this->status == 'ko') {
            $this->preTpvPayment($this->payment, $inscription['id']);
        }
    }

    /**
     * Acción ejecutada por el TPV para confirmar o rechazar la transacción
     * @return string
     */

    public function executePaymentNotificationTpv()
    {
        //error_log('Recibiendo notificacion TPV ' . date('d/m/Y H:i:s'));
        //error_log(print_r($_POST, 1));

        $responseCodesOK = array("000", "001", "002", "099", "0000", "0001", "0002", "0099");
        $number = $this->getRequestParameter('number');
        $payment = $this->getRequestParameter('payment'); // 1 o 2 en función de si es el primer o el segundo pago.

        $tpvHelper = new RedsysAPI();
//        $version = $this->getRequestParameter('Ds_SignatureVersion');
        $data = $this->getRequestParameter('Ds_MerchantParameters');
        $tpvHelper->decodeMerchantParameters($data);

        // error_log("POST params: " . print_r($_POST, 1));

        if ($number == $tpvHelper->getParameter('Ds_Order'))
        {
            $inscription = InscriptionPeer::findByIdAndTpvSuffix($number);
            if ($inscription)
            {
                // Get full object
                /** @var Inscription $inscription */
                $inscription = InscriptionPeer::retrieveByPK($inscription['id']);
                $signature = $tpvHelper->createMerchantSignatureNotif($inscription->getCourse()->getSummerFunCenter()->getMerchantKey(), $data);

                if ($signature == $this->getRequestParameter('Ds_Signature'))
                {
                    if (in_array($tpvHelper->getParameter('Ds_Response'), $responseCodesOK)) {
                        $result = 'OK';
                    }
                    else {
                        $result = 'KO. Response KO: ' . $tpvHelper->getParameter('Ds_Response');
                    }
                }
                else {
                    $result = 'KO. Signature does not match';
                }

                $inscriptions = InscriptionPeer::retrieveByInscriptionNum($inscription->getInscriptionNum());
                $inscriptionsPdf = array();
                $emailsToSendPdf = array();
                $contInscription = 0;
                $contWeek = 1;
                $inscriptionCode = null;
                /** @var Inscription $i */
                foreach ($inscriptions as $i)
                {
                    if ($payment == 1) {
                        // First payment
                        if ($result == 'OK') {

                            if (!isset($inscriptionCode) || $i->getInscriptionCode() != $inscriptionCode) {
                                $contInscription++;
                                $contWeek = 1;
                            }
                            else {
                                $contWeek++;
                            }

                            $inscriptionCode = $i->getInscriptionCode();

                            $inscriptionsPdf[$contInscription][$contWeek] = $i->getId();
                            if (!count($emailsToSendPdf)) {
                                if ($i->getIsFatherMailMain() && $i->getFatherMail()) {
                                    $emailsToSendPdf[1][1] = $i->getFatherMail();
                                }
                                if ($i->getIsMotherMailMain() && $i->getMotherMail()) {
                                    $emailsToSendPdf[1][2] = $i->getFatherMail();
                                }
                            }
                            $amount = $i->getPendingAmount();
                            if ($i->getSplitPayment()) {
                                $amount = $amount / 2;
                                $i->setIsPaid(InscriptionPeer::IS_PAID_50); // Pago 50%
                            }
                            else {
                                $i->setIsPaid(InscriptionPeer::IS_PAID_100); // Pago 100%
                            }
                            $i->setAmountFirstPayment($amount);
                            $i->setFirstPaymentDate(date('Y-m-d'));
                        }

                        $i->setTpvFirstPaymentResponse($result);
                    }
                    else {
                        // Second payment
                        if ($result == 'OK') {
                            $i->setAmountSecondPayment($i->getPendingAmount());
                            $i->setIsPaid(InscriptionPeer::IS_PAID_100); // Marcamos como 100% pagado
                        }
                        $i->setTpvSecondPaymentResponse($result);
                        $i->setSecondPaymentDate(date('Y-m-d'));
                    }

                    $i->save();
                }

                if (count($inscriptionsPdf) && count($emailsToSendPdf)) {
                    list($pdfGenerated, $idCentre) = util::generarPdf($inscriptionsPdf);
                    util::enviarPdf($pdfGenerated, $emailsToSendPdf, $idCentre);
                }
            }
            else {
                $result = 'KO. No inscription (id + TPV suffix) found: ' . $tpvHelper->getParameter('Ds_Order');
            }
        }
        else {
            $result = 'KO. Ds_Order does not match. Parameter: ' . $number . ' . Request: ' . $tpvHelper->getParameter('Ds_Order');
        }

        error_log('Resultado de la notificación: ' . $result);

        return sfView::NONE;
    }

    /**
     * Muestra la pantalla resumen del segundo pago
     */

    public function executeSecondPayment()
    {
        $this->preTpvPayment(2);

        $this->inscriptions = InscriptionPeer::retrieveByInscriptionNum($this->inscription->getInscriptionNum());
    }

    /**
     * Acción mailing para recordatorio del segundo pago
     */

    public function executeSecondPaymentTpvMailing()
    {
        if ($this->getRequestParameter('pass') != sfConfig::get('app_cron_key')) {
            $this->forward404();
        }

        $date = $this->getRequestParameter('date');
        if (!$date || $date == 1) {
            $date = date('Y-m-d');
        }

        $logFile = sfConfig::get('sf_log_dir') . DIRECTORY_SEPARATOR . 'TPV_MAILING_REMINDER.log';
        $log = fopen($logFile, "a");
        if (!$log) {
            throw new Exception("Unable to create log file");
        }
        fwrite($log, "######################################################\r\n");
        fwrite($log, "Inicio envio recordatorios segundo pago TPV " . $date ."\r\n");


        $culture = $this->getUser()->getCulture();
        $inscriptions = InscriptionPeer::retrieveForSecondPaymentMailing($date);
        $processedInscriptions = array();

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = sfConfig::get('app_email_host');
        $mail->Port = sfConfig::get('app_email_port');
        $mail->Username = sfConfig::get('app_email_user');
        $mail->Password = sfConfig::get('app_email_password');
        $mail->FromName = 'Kids&Us';
        $mail->Subject = sfContext::getInstance()->getI18N()->__('Recordatorio segundo pago');
        $mail->CharSet = 'UTF-8';
        $mail->WordWrap = 50;
        $mail->IsHTML(true);

        sfLoader::loadHelpers('Partial');

        /** @var Inscription $inscription */
        foreach ($inscriptions as $inscription)
        {
            if (!in_array($inscription->getInscriptionNum(), $processedInscriptions) &&
                $inscription->getPendingAmount() > 0 &&
                $inscription->getIsPaid() == InscriptionPeer::IS_PAID_50 &&
                !$inscription->getIsPaymentReminderSent())
            {
                // 27/05/16 - Añadimos este control para evitar envíos en inscripciones relacionadas donde alguna de ellas ya se ha enviado.
                // Este caso ya no se puede dar porque se ha cambiado para que se actualice el flag en todas las incripciones cuando se envia el recordatorio.
                // Pero se añade por si se realizan reenvios antiguos, cuando sólo se actualizaba el flag para la inscripción que se procesaba el envío.
                $sentInscriptions = InscriptionPeer::retrieveByInscriptionNumAndReminderSent($inscription->getInscriptionNum());
                if (count($sentInscriptions)) {
                    continue;
                }

                $this->getUser()->setCulture($inscription->getCulture() ? $inscription->getCulture() : 'ca');

                /** @var Course $course */
                $course = CoursePeer::retrieveByPKWithI18n($inscription->getStudentCourseInscription());
                if (!$course->getCulture()) {
                    $course->setCulture($this->getUser()->getCulture());
                }

                $mail->From = $course->getSummerFunCenter()->getMail() ? $course->getSummerFunCenter()->getMail() : 'info@kidsandus.es';
                $mail->AddReplyTo($mail->From, 'Kids&Us');

                /** @var SummerFunCenter $center */
                $center = $course->getSummerFunCenter();

                $textTPV = $center->getSecondPaymentMailingBody();
                $textNOTPV = $center->getSecondPaymentMailingBodyNoTpv();

                if ($inscription->getMethodPayment() == InscriptionPeer::METHOD_PAYMENT_TPV && !trim($textTPV)) {
                    continue;
                }

                if ($inscription->getMethodPayment() != InscriptionPeer::METHOD_PAYMENT_TPV && !trim($textNOTPV)) {
                    continue;
                }

                $mail->Body = get_partial('tpv/second_payment_reminder_email', array('inscription' => $inscription, 'center' => $center));
                $mail->AltBody = $mail->Body;

                $mail->ClearAddresses();

                if ($inscription->getFatherMail()) {
                    $mail->AddAddress($inscription->getFatherMail());
                }

                if ($inscription->getIsMotherMailMain() && $inscription->getMotherMail()) {
                    $mail->AddAddress($inscription->getMotherMail());
                }

                if ($mail->Send()) {
                    InscriptionPeer::updateSendReminder($inscription->getInscriptionNum(), 1);
                    fwrite($log, "Envio a {$inscription->getFatherMail()}. Inscripción número: {$inscription->getInscriptionCode()}. Curso: {$course->getId()} Centro: {$course->getSummerFunCenter()->getTitle()} ({$course->getSummerFunCenter()->getId()}) \r\n");
                }

                array_push($processedInscriptions, $inscription->getInscriptionNum());
            }
        }

        fwrite($log, "Fin envios " . date('d/m/Y') ."\r\n");
        fwrite($log, "######################################################\r\n");

        $this->getUser()->setCulture($culture);

        return sfView::NONE;
    }

    /**
     * Acción mailing para recordatorio del segundo pago
     */

    public function executeAdjustSecondPayment()
    {
        if ($this->getRequestParameter('pass') != sfConfig::get('app_cron_key')) {
            $this->forward404();
        }

        $ids = array(385, 391, 414, 418, 426, 466, 486, 522, 525, 551, 597, 618, 645, 660, 667, 717, 724, 782, 796, 817, 894);

        foreach ($ids as $id)
        {
            $inscription = InscriptionPeer::retrieveByPK($id);
            if ($inscription) {
                $inscriptions = InscriptionPeer::retrieveByInscriptionNum($inscription->getInscriptionNum());
                foreach ($inscriptions as $inscription)
                {
                    $inscription->setTpvSecondPaymentResponse('OK. Ajuste manual');
                    $inscription->setAmountSecondPayment($inscription->getPendingAmount());
                    $inscription->setIsPaid(2); // Marcamos como 100% pagado

                    $inscription->save();
                }
            }
        }

        return sfView::NONE;
    }
}
