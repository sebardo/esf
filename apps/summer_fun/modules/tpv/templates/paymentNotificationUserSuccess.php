<div id="confirmation-inscription" <?php echo $status == 'ko' ? 'class="confirm-payment"' : '' ?>>
    <?php if ($status == 'ok'): ?>
        <?php if ($payment == 1): ?>
            <h2><?php echo __('Inscripció realitzada correctament') ?></h2>
            <p><?php echo __('Gràcies per inscriure-us a les activitats de l\'English Summer Fun.') ?></p>
            <p><?php echo __("En breus instants rebrà un correu electrònic a l'adreça principal de contacte indicada, on trobareu la informació necessària per formalitzar la reserva.") ?></p>
            <p><?php echo __('Moltes gràcies.') ?></p>
            <img src="/images/logo_pdf.jpg">
            <div id="final_line"></div>
            <a href="<?php echo url_for('@index_' .$sf_user->getCulture())?>" id="submit_step1"><?php echo __('Inici') ?></a>
        <?php else: ?>
            <h2><?php echo __("Pagament realitzat correctament") ?></h2>
            <p><?php echo __("El pagament s'ha processat correctament i la seva inscripció ha quedat confirmada.") ?></p>
            <p><?php echo __('Moltes gràcies.') ?></p>
            <img src="/images/logo_pdf.jpg">
            <div id="final_line"></div>
            <a href="<?php echo url_for('@index_' .$sf_user->getCulture())?>" id="submit_step1"><?php echo __('Inici') ?></a>
        <?php endif ?>
    <?php else: ?>
        <h2><?php echo __('Error en processar el pagament') ?></h2>
        <p><?php echo __("S'ha produït un error en processar el pagament, per aquest motiu la inscripció no ha quedat enregistrada. Si us plau, feu clic al següent botó per tornar a intentar-ho.") ?></p>

        <form method="post" class="confirm-payment-form" action="<?php echo $urlTpv ?>">
            <input type="hidden" name="Ds_SignatureVersion" value="<?php echo $version; ?>"/>
            <input type="hidden" name="Ds_MerchantParameters" value="<?php echo $params; ?>"/>
            <input type="hidden" name="Ds_Signature" value="<?php echo $signature; ?>"/>
            <input type="submit" value="<?php echo __('Pagar ara') ?>"/>
        </form>
    <?php endif ?>
</div>

