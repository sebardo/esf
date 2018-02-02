<?php use_helper('Object'); ?>
<select name="<?php echo $name ?>" id="<?php echo $name ?>" style="margin-right: 10px">
    <option value="" selected>- <?php echo __('school_year') ?> -</option>
    <?php foreach ($schoolYears as $schoolYear): ?>
        <option value="<?php echo $schoolYear->getId() ?>"><?php echo $schoolYear->getName() ?></option>
    <?php endforeach ?>
</select>
