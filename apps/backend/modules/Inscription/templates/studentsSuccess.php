<?php use_stylesheet('/sf/sf_admin/css/main') ?>
<?php use_stylesheet('ui-lightness/jquery-ui-1.10.4.custom.min.css') ?>
<?php use_javascript('/js/jquery-1.11.2.min.js') ?>
<?php use_javascript('jquery-ui/jquery-ui-1.10.4.custom.min.js') ?>
<?php use_javascript('/js/list-incription.js') ?>

<div id="sf_admin_container">

    <h1><?php echo __('Llistat d\'inscripcions') ?></h1>

    <?php $e = function ($array, $key) {
        if (!isset($array[$key])) return '';

        return htmlspecialchars($array[$key]);
    } ?>
    <div id="sf_admin_content">
        <div class="sf_admin_filters" id="student-filters">
            <form action="<?php echo url_for('Inscription/students') ?>" method="get">
                <fieldset>
                    <h2>Filtres</h2>

                    <div class="form-row form-row-top">
                        <label for="student_name">Buscar estudientes (nom, apellidos):</label>
                        <div class="content">
                            <input id="student_name" name="filters[name]"
                                   value="<?php echo $e($filters, 'name') ?>">
                        </div>

                        <label for="dni">DNI de padres:</label>
                        <div class="content">
                            <input id="dni" name="filters[dni]"
                                   value="<?php echo $e($filters, 'dni') ?>">
                        </div>
                    </div>
                    <div class="form-row form-row-top">

                        <label for="father_name">Nom de padre:</label>
                        <div class="content">
                            <input id="father_name" name="filters[father_name]"
                                   value="<?php echo $e($filters, 'father_name') ?>">
                        </div>

                        <label for="mother_name">Nom de madre:</label>
                        <div class="content">
                            <input id="mother_name" name="filters[mother_name]"
                                   value="<?php echo $e($filters, 'mother_name') ?>">
                        </div>

                    </div>

                    <div class="form-row form-row-top">
                        <label for="inscription_code">Codi inscripció:</label>
                        <div class="content">
                            <input id="inscription_code" name="filters[inscription_code]"
                                   value="<?php echo $e($filters, 'inscription_code') ?>">
                        </div>
                    </div>
                </fieldset>
                <ul class="sf_admin_actions">
                    <input class="sf_admin_action_reset_filter" value="Netejar" onclick="document.location.href='<?php echo url_for('Inscription/students') ?>';" type="button">
                    <li><?php echo submit_tag(__('filter'), 'class=sf_admin_action_filter') ?></li>
                    <li><input value="Exportar" name="export" class="sf_admin_action_export" id="export" type="submit">
                    </li>
                </ul>
            </form>
        </div>

        <div id="student-list">
            <?php require 'inc/students_table.php' ?>
        </div>
    </div>
</div>


<div id="dialog" title="<?php echo __('Seleccione columnas a exportar') ?>" style="display:none;font-size:12px">
    <div class="dialog-options">
        <?php echo __('Seleccionar:') ?>
        <a id="export-all" href="#"><?php echo __('Todas') ?></a>&nbsp;|
        <a id="export-none" href="#"><?php echo __('Ninguna') ?></a>
    </div>
    <form id="form-export" action="<?php echo url_for('Inscription/exportstudents') . '/id/all?' . $_SERVER['QUERY_STRING'] ?>" method="post">
        <div style="max-height: 335px; overflow: auto; margin-bottom: 10px">
            <table class="sf_admin_list">
                <tr>
                    <th></th>
                    <th><?php echo __('Columna') ?></th>
                </tr>
                <?php foreach ($columns as $key => $value): ?>
                    <tr>
                        <td style="width:20px">
                            <input type="checkbox" name="columns[<?php echo $key ?>]" value="<?php echo $key ?>"></td>
                        <td><?php echo $value ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <label><?php echo __('Nombre fichero') ?>:</label>
        <input type="text" name="filename" style="width:200px"/>
        <input type="hidden" name="ids" value="<?php echo isset($filters["csv-export-link"]) ? $filters["csv-export-link"] : '' ?>"/>
    </form>
</div>

<script type="text/javascript">
  var selectFileName = "<?php echo __('Debe introducir un nombre para el fichero') ?>"
  var minNumColumns = "<?php echo __('Debe seleccionar al menos una columna') ?>"
</script>

<script>
  $(document).ready(function () {
    var delay = (function () {
      var timer = 0
      return function (callback, ms) {
        clearTimeout(timer)
        timer = setTimeout(callback, ms)
      }
    })()

    $('#student-filters input').on('input', function () {
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

    $('#student-list').on('click', '.expand-next-block', function () {
      var collapsibleRows = []
      var nextRow = $(this).closest('.user-row').next()
      do {
        collapsibleRows.push(nextRow)
        nextRow = nextRow.next()
        console.log(nextRow)
      } while (nextRow.length && !nextRow.hasClass('user-row'))

      $.each(collapsibleRows, function () {
        $(this).toggle()
      })

    })
  })
</script>


<style>
    #student-list tbody tr {
        display: none;
    }
    #student-list tbody  tr.user-row {
        display: table-row;
    }
    .user-row td {
        font-weight: bold;
    }

    .expand-next-block {
        cursor: pointer;
    }

    .form-row-top .content {
        float: left;
        padding-left: 0 !important;
        margin-right: 10px;
    }
</style>