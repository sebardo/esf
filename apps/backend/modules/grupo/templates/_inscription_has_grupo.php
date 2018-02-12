<?php if ($type === 'list'): ?>
    <?php $inscriptions = Inscription::getAssignedToGrupo($grupo->getId()) ?>
    <?php if (empty($inscriptions)) return ?>

    <p class="expand-inscription-list" onclick="jQuery(this).next('.grupo-inscription-list').slideToggle()">
        Mostrar (<?php echo count($inscriptions) ?>)
    </p>
    <ul class="grupo-inscription-list">
        <?php foreach ($inscriptions as $inscription): ?>
            <li><?php echo $inscription['text'] ?></li>
        <?php endforeach; ?>
    </ul>
    <?php return ?>
<?php endif ?>


<?php
$response = sfContext::getInstance()->getResponse();
$response->addStylesheet(sfConfig::get('sf_admin_web_dir') . '/css/select2.min.css');
$response->addStylesheet(sfConfig::get('sf_admin_web_dir') . '/css/select2.fixes.css');
$response->addJavascript('/js/jquery-1.11.2.min.js');
$response->addJavascript(sfConfig::get('sf_admin_web_dir') . '/js/select2.min.js');
$response->addJavascript(sfConfig::get('sf_admin_web_dir') . '/js/select2.l18n.ca.js');
?>

<select class="groupo-inscriptions" name="inscriptions[]" multiple="multiple" style="width: 500px">
    <?php foreach (Inscription::getAssignedToGrupo($sf_params->get('id')) as $inscription): ?>
        <option value="<?php echo $inscription['id'] ?>" selected="selected"><?php echo $inscription['text'] ?></option>
    <?php endforeach; ?>
</select>
<p style="font-size: small;color:#bbb">Icona de bloqueig significa "Ja està assignat"</p>

<script type="text/javascript">
  $(document).ready(function () {
    $('.groupo-inscriptions').select2({
      minimumInputLength: 2,
      closeOnSelect: false,
      placeholder: '<?php echo __('Comenceu a escriure per cercar') ?>',
      ajax: {
        delay: 250,
        url: '<?php echo url_for('grupo/getInscriptions')?>',
        dataType: 'json'
      },
      language: 'ca'
    })
  })
</script>