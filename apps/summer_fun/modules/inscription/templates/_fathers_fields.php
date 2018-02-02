<?php if ($show){

    $class="mostrar";


}else{
    $class="ocultar";

}?>

<div id="fathers<?php echo $id?>" class="<?php echo $class ?>">

<div class="father">
    <h3><?php echo __('Pare / mare / tutor legal')?></h3>

    <?php include_partial('text_field', array('field_name' => 'fatherName' . $id, 'field_label' => __('Nom')  . ":", 'obligatori' => true, 'class_input'=>'widthParents'));?>
    <?php include_partial('text_field', array('field_name' => 'fatherPrimerApellido' . $id, 'field_label' => __('Cognom 1')  . ":", 'obligatori' => true, 'class_input'=>'widthParents'));?>
<?php if ($sf_user->getCulture() != 'fr'): ?>
    <?php include_partial('text_field', array('field_name' => 'fatherSegundoApellido' . $id, 'field_label' => __('Cognom 2')  . ":", 'obligatori' => false, 'class_input'=>'widthParents'));?>
<?php endif ?>
    <?php include_partial('text_field', array('field_name' => 'fatherPhone' . $id, 'field_label' => __('Telèfon:'), 'obligatori' => true, 'class_input'=>'widthParents'));?>
    <?php include_partial('text_field', array('field_name' => 'fatherDni' . $id, 'field_label' => __('DNI:'), 'obligatori' => true, 'class_input'=>'widthParents'));?>

    <?php include_partial('mail_field', array('field_name' => 'fatherMail' . $id, 'field_label' => __('Email:'), 'obligatori' => true, 'class'=>'widthParents'));?>


    <?php if ($sf_request->hasError('mailPrincipal' .$id)): ?>
    <p class="validation-error" style="margin-left: 145px;">&uarr; <?php echo $sf_request->getError('mailPrincipal' .$id) ?> &uarr;</p>
    <?php endif ?>


</div>
<div class="mother">
    <h3><?php echo __('Pare / mare / tutor legal')?></h3>

    <?php include_partial('text_field', array('field_name' => 'motherName' . $id, 'field_label' => __('Nom')  . ":", 'obligatori' => false, 'class_input'=>'widthParents'));?>
    <?php include_partial('text_field', array('field_name' => 'motherPrimerApellido' . $id, 'field_label' => __('Cognom 1')  . ":", 'obligatori' => false, 'class_input'=>'widthParents'));?>
<?php if ($sf_user->getCulture() != 'fr'): ?>
    <?php include_partial('text_field', array('field_name' => 'motherSegundoApellido' . $id, 'field_label' => __('Cognom 2')  . ":", 'obligatori' => false, 'class_input'=>'widthParents'));?>
<?php endif ?>
    <?php include_partial('text_field', array('field_name' => 'motherPhone' . $id, 'field_label' => __('Telèfon:'), 'obligatori' => false, 'class_input'=>'widthParents'));?>
    <?php include_partial('text_field', array('field_name' => 'motherDni' . $id, 'field_label' => __('DNI:'), 'obligatori' => false, 'class_input'=>'widthParents'));?>

    <?php include_partial('mail_field', array('field_name' => 'motherMail' . $id, 'field_label' => __('Email:'), 'obligatori' => false, 'class'=>'widthParents'));?>
</div>

</div>