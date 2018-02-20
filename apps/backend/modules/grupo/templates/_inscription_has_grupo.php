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
$response->addJavascript('/js/jquery-1.11.2.min.js');
?>

<style>
    #sf_admin_container .ins-search select {
        width: 100%;
    }

    #sf_admin_container .ins-search input {
        width: 98%;
    }
</style>
<fieldset>
    <legend><?php echo __('Buscar:') ?></legend>
    <div class="form-row ins-search">
        <label for="ins-filter-week"><?php echo __('Setmanes:') ?></label>
        <select id="ins-filter-week" autocomplete="off">
            <option value=""></option>
            <?php foreach (CoursePeer::getArrayWeeksForInscriptionFilter() as $week): ?>
                <option value="<?php echo $week['id'] ?>"><?php echo $week['nombre'] ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-row ins-search">
        <label for="ins-filter-dni"><?php echo __('Padre DNI:') ?></label>
        <input type="text" id="ins-filter-dni" autocomplete="off">
    </div>

    <div class="form-row ins-search">
        <label for="ins-filter-inscription"><?php echo __('Codi inscripció') ?></label>
        <input type="text" id="ins-filter-inscription" autocomplete="off">
    </div>
    <div class="form-row ins-search">
        <label for="ins-filter-name"><?php echo __('Nom, cognoms') ?></label>
        <input type="text" id="ins-filter-name" autocomplete="off">
    </div>
</fieldset>

<div>
    <div style="float: left">
        <div style="font-weight: bold; padding-bottom: 0.5em">No assignat</div>
        <select id="unassigned_inscriptions" multiple="multiple" class="sf_admin_multiple" size="10">
            <option disabled>Busca a dalt</option>
        </select>
    </div>
    <div style="float: left">
        <input name="commit" src="/sf/sf_admin/images/next.png" style="border: 0"
               onclick="double_list_move('unassigned_inscriptions', 'assigned_inscriptions'); return false;"
               alt="Next" type="image"><br>
        <input name="commit" src="/sf/sf_admin/images/previous.png" style="border: 0"
               onclick="double_list_move('assigned_inscriptions', 'unassigned_inscriptions'); return false;"
               alt="Previous" type="image">
    </div>
    <div style="float: left">
        <div style="font-weight: bold; padding-bottom: 0.5em">Assignat</div>
        <select name="inscriptions[]" id="assigned_inscriptions"
                multiple="multiple" class="sf_admin_multiple-selected">
            <?php foreach (Inscription::getAssignedToGrupo($sf_params->get('id')) as $inscription): ?>
                <option value="<?php echo $inscription['id'] ?>"><?php echo $inscription['text'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <br style="clear: both">
    <p style="font-size: small;color:#bbb">Icona de bloqueig significa "Ja està assignat"</p>
</div>

<script type="text/javascript">
  $(document).ready(function () {
    var delay = (function () {
      var timer = 0
      return function (callback, ms) {
        clearTimeout(timer)
        timer = setTimeout(callback, ms)
      }
    })()

    $('.ins-search input, .ins-search select').on('change keyup', function () {
      delay(function () {
        $.ajax(
          {
            url: '<?php echo url_for('grupo/getInscriptions') ?>?',

            data: {
              week: $('#ins-filter-week').val(),
              dni: $('#ins-filter-dni').val(),
              inscription: $('#ins-filter-inscription').val(),
              name: $('#ins-filter-name').val()
            },
            success: function (response) {
              $('#unassigned_inscriptions option').remove()

              if ($.isEmptyObject(response.results)) {
                $('<option/>', {
                  value: '',
                  text: 'No s\'han trobat resultats',
                  disabled: true
                }).appendTo('#unassigned_inscriptions')
                return
              }

              $.each(response.results, function () {
                $('<option/>', {
                  value: this.id,
                  text: this.text
                }).appendTo('#unassigned_inscriptions')
              })
            }
          }
        )
      }, 300)
    })
  })
</script>