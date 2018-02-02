<?php use_helper('Object'); ?>
<select name="<?php echo $name ?>" id="<?php echo $name ?>">
	<option value="" selected>- <?php echo __('Seleccioneu la província') ?> -</option>
    <?php foreach ($provincias as $provincia): ?>
        <option value="<?php echo $provincia->getId() ?>"><?php echo $provincia->getNombre() ?></option>
    <?php endforeach; ?>

</select>
<?php if ($sf_request->hasError("studentProvincia$id")): ?>
<p class="validation-error" style="margin-left:177px">&uarr; <?php echo $sf_request->getError("studentProvincia$id") ?> &uarr;</p>
<?php endif ?>
