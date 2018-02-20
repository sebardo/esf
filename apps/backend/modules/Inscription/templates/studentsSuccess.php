<?php use_stylesheet('/sf/sf_admin/css/main') ?>
<?php use_javascript('/js/jquery-1.11.2.min.js') ?>

<div id="sf_admin_container">

    <h1><?php echo __('Llistat d\'inscripcions') ?></h1>

    <div id="sf_admin_content">
        <div class="sf_admin_filters" id="student-filters">
            <fieldset>
                <h2>Filtres</h2>

                <div class="form-row form-row-top">
                    <label for="filters_inscription_code">
                        Buscar estudientes (nom, apellidos):
                    </label>
                    <div class="content">
                        <input id="student_name">
                    </div>
                </div>
                <div class="form-row form-row-top">
                    <label for="filters_inscription_code">
                        DNI de padres:
                    </label>
                    <div class="content">
                        <input id="dni">
                    </div>
                </div>
            </fieldset>
            <ul class="sf_admin_actions">
                <li><input value="Exportar" class="sf_admin_action_export" id="export" type="button"></li>
            </ul>
        </div>

        <div id="student-list"></div>
    </div>
</div>

<script>
  $(document).ready(function () {
    var delay = (function () {
      var timer = 0
      return function (callback, ms) {
        clearTimeout(timer)
        timer = setTimeout(callback, ms)
      }
    })()

    $('#student-filters input').on('change keyup', function () {
      delay(function () {
        $.ajax(
          {
            url: '<?php echo url_for('Inscription/studentsList') ?>?',
            data: {
              name: $('#student_name').val(),
              dni: $('#dni').val()
            },
            success: function (response) {
              $('#student-list').html(response)
            }
          }
        )
      }, 300)

    })
  })
</script>