<?php use_helper('Object'); ?>
<select name="<?php echo $name ?>" id="<?php echo $name ?>" style="margin-right: 10px;float:left">
    <option value="" selected>- <?php echo __('Seleccioneu un centre de procedència') ?> -</option>
    <?php foreach ($centers as $center): ?>
        <option value="<?php echo $center->getId() ?>"><?php echo $center->getName() ?></option>
    <?php endforeach ?>
</select>