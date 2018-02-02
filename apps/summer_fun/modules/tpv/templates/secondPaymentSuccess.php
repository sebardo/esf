<div id="confirmation-inscription" class="second-payment">
    <h2><?php echo __("Confirmació") ?></h2>
    <p><?php echo __("Revisi les dades mostrades a continuació i feu clic al botó per procedir al pagament dels imports pendents.") ?></p>

    <table>
        <thead>
            <tr>
                <th><?php echo __('Codi inscripció') ?></th>
                <th><?php echo __('Alumne') ?></th>
                <th><?php echo __('Setmana') ?></th>
                <th><?php echo __('Centre') ?></th>
                <th><?php echo __('Import pagat') ?></th>
                <th><?php echo __('Import pendent') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($inscriptions as $insc): ?>
                <tr>
                    <td><?php echo $insc->getInscriptionCode() ?></td>
                    <td><?php echo $insc->getFullStudentName() ?></td>
                    <td><?php echo $insc->getCourse()->getWeek() ?></td>
                    <td><?php echo $insc->getCourse()->getSummerFunCenter()->getCenterName() ?></td>
                    <td><?php echo $insc->getAmountFirstPayment() ?> €</td>
                    <td><?php echo $insc->getPendingAmount() ?> €</td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>

    <div class="total">
        <span><?php echo __("Importe total a pagar") ?>: <strong><?php echo $inscription->getPendingAmountFromAllInscriptions() ?> €</strong></span>
    </div>

    <form method="post" class="confirm-payment-form" action="<?php echo $urlTpv ?>" style="text-align: right; margin-top: 20px">
        <input type="hidden" name="Ds_SignatureVersion" value="<?php echo $version; ?>"/>
        <input type="hidden" name="Ds_MerchantParameters" value="<?php echo $params; ?>"/>
        <input type="hidden" name="Ds_Signature" value="<?php echo $signature; ?>"/>
        <input type="submit" value="<?php echo __('Pagar ara') ?>"/>
    </form>
</div>