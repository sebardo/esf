<?php use_helper('Object'); ?>
<select name="<?php echo $name ?>" id="<?php echo $name ?>" style="margin-right: 10px;float:left;">
    <option value="" selected>- <?php echo __('Seleccioneu una escola de procedència') ?> -</option>
    <?php foreach ($centers as $center): ?>

        <?php
        $centerName = null;
        switch ($sf_user->getCulture())
        {
            case 'es':
                $centerName = $center->getTitleEs() ? $center->getTitleEs() : null;
                break;
            case 'ca':
                $centerName = $center->getTitleCa() ? $center->getTitleCa() : null;
                break;
            case 'fr':
                $centerName = $center->getTitleFr() ? $center->getTitleFr() : null;
                break;
            case 'it':
                $centerName = $center->getTitleIt() ? $center->getTitleIt() : null;
                break;
            case 'en':
                $centerName = $center->getTitleEn() ? $center->getTitleEn() : null;
                break;
        }
        ?>

        <?php if ($centerName): ?>
            <option value="<?php echo $center->getId() ?>"><?php echo $centerName ?></option>
        <?php endif; ?>
    <?php endforeach ?>
    <option value="-1"><?php echo __('registration.trans234') ?></option>
</select>