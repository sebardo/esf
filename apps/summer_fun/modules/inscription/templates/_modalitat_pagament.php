<?php if (isset($center)): ?>

	<?php $selected = false ?>
	<?php if ($center->getTransferPayment()): $selected = true ?>
	    <div><?php echo radiobutton_tag('payment', 0, true) ?><span><?php echo __('Transferència bancària')?></span></div>
	<?php endif ?>
	
	<?php if ($center->getCashPayment()): ?>
	    <div><?php echo radiobutton_tag('payment', 1, $selected ? false : true) ?><span><?php echo __('Pagament en efectiu al centre')?></span></div>
	    <?php $selected = true ?>
	<?php endif ?>
	
	<?php if ($center->getReciboDomiciliadoPayment()): ?>
        <div><?php echo radiobutton_tag('payment', 2, $selected ? false : true) ?><span><?php echo __('Pagament amb rebut domiciliat')?></span></div>
        <?php
            $culture = $sf_user->getCulture();
            switch ($culture)
            {
                case 'es';
                {
                    if ($center->getReciboDomiciliadoTxtEs()) {
                        echo '<div style="margin-left:20px">' . $center->getReciboDomiciliadoTxtEs() . '</div>';
                    }
                    break;
                }
                case 'ca';
                {
                    if ($center->getReciboDomiciliadoTxtCa()) {
                        echo '<div style="margin-left:20px">' . $center->getReciboDomiciliadoTxtCa() . '</div>';
                    }
                    break;
                }
                case 'it';
                {
                    if ($center->getReciboDomiciliadoTxtIt()) {
                        echo '<div style="margin-left:20px">' . $center->getReciboDomiciliadoTxtIt() . '</div>';
                    }
                    break;
                }
                case 'fr';
                {
                    if ($center->getReciboDomiciliadoTxtFr()) {
                        echo '<div style="margin-left:20px">' . $center->getReciboDomiciliadoTxtFr() . '</div>';
                    }
                    break;
                }
            }
        ?>
	<?php endif ?>

    <?php if ($center->getTpvPayment() && $center->getMerchantCode() && $center->getMerchantKey() && $center->getUrlTpv() && $center->getAddressTpv()): $selected = true ?>
        <div><?php echo radiobutton_tag('payment', 3, true) ?><span><?php echo __('TPV')?></span></div>
        <?php if ($sf_request->hasError('payment')): ?>
            <p class="validation-error" style="margin: 5px 0 0 10px">&uarr; <?php echo $sf_request->getError('payment') ?> &uarr;</p>
        <?php endif ?>
        <script type="text/javascript">
            $('#address-tpv').text('<?php echo $center->getAddressTpv() ?>');
        </script>
    <?php else: ?>
        <script type="text/javascript">
            $('#address-tpv').text('');
        </script>
    <?php endif ?>
<?php endif ?>
