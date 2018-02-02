<div id="confirmation-inscription" class="launch-payment">
    <p><?php echo image_tag("spin.gif", array('width' => 56)); ?></p>
    <p><?php echo __('A continuació serà redirigit a la passarel·la de pagament.') ?></p>
    <p><?php echo __('Si us plau, esperi...') ?></p>

    <form method="post" class="confirm-payment-form" action="<?php echo $urlTpv ?>" id="payment-form">
        <input type="hidden" name="Ds_SignatureVersion" value="<?php echo $version; ?>"/>
        <input type="hidden" name="Ds_MerchantParameters" value="<?php echo $params; ?>"/>
        <input type="hidden" name="Ds_Signature" value="<?php echo $signature; ?>"/>
    </form>
</div>

<script type="text/javascript">

    jQuery(function($) {
        timer = setInterval(function() { launchForm() }, 4000);
    });

    function launchForm()
    {
        clearInterval(timer);
        $('#payment-form').submit();
    }

</script>


