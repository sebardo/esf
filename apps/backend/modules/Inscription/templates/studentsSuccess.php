<?php use_stylesheet('/sf/sf_admin/css/main') ?>
<?php use_stylesheet('ui-lightness/jquery-ui-1.10.4.custom.min.css') ?>
<?php use_javascript('/js/jquery-1.11.2.min.js') ?>
<?php use_javascript('jquery-ui/jquery-ui-1.10.4.custom.min.js') ?>
<?php use_javascript('/js/list-incription.js') ?>

<div id="sf_admin_container">

    <h1><?php echo __('Llistat d\'estudiants') ?></h1>

    <?php $e = function ($array, $key) {
        if (!isset($array[$key])) return '';

        return htmlspecialchars($array[$key]);
    } ?>
    <div class="sf_admin_filters" id="student-filters">
        <form action="<?php echo url_for('Inscription/students') ?>" method="get">
            <fieldset>
                <h2>Filtres</h2>

                <div class="form-row form-row-top">
                    <label for="inscription_code"><?php echo __('Codi inscripció:') ?></label>
                    <div class="content">
                        <input id="inscription_code" name="filters[inscription_code]"
                               value="<?php echo $e($filters, 'inscription_code') ?>">
                    </div>

                    <label for="filters_center_id"><?php echo __('Centre Inscripció:') ?></label>
                    <div class="content">
                        <?php echo select_tag('filters[center_id]',
                            options_for_select(SummerFunCenterPeer::getArrayCenters(),
                                $e($filters, 'center_id'),
                                array('include_blank' => true)),
                            array('style' => "min-width: 300px")
                        ) ?>
                    </div>
                </div>

                <div class="form-row form-row-top">
                    <label for="filters_course_id">
                        <?php echo __('Activitat:') ?></label>
                    <div class="content">
                        <?php use_helper('Object') ?>

                        <?php echo select_tag('filters[course_id]',
                            objects_for_select(
                                CoursePeer::getCoursesByProfile(),
                                'getId',
                                '__toString',
                                $e($filters, 'course_id'),
                                'include_blank=true'), array('style' => 'min-width: 700px')); ?>
                    </div>
                </div>

                <div class="form-row form-row-top">
                    <label for="filters_student_name"><?php echo __('Cercar estudiants (nom, cognoms)') ?></label>
                    <div class="content">
                        <input id="filters_student_name" name="filters[name]"
                               value="<?php echo $e($filters, 'name') ?>">
                    </div>

                    <label for="dni"><?php echo __('DNI del pares:') ?></label>
                    <div class="content">
                        <input id="dni" name="filters[dni]"
                               value="<?php echo $e($filters, 'dni') ?>">
                    </div>
                </div>

                <div class="form-row form-row-top">

                    <label for="filters_father_name"><?php echo __('Nom del pare:') ?></label>
                    <div class="content">
                        <input id="filters_father_name" name="filters[father_name]"
                               value="<?php echo $e($filters, 'father_name') ?>">
                    </div>

                    <label for="filters_mother_name"><?php echo __('Nom del mare:') ?></label>
                    <div class="content">
                        <input id="filters_mother_name" name="filters[mother_name]"
                               value="<?php echo $e($filters, 'mother_name') ?>">
                    </div>

                </div>

                <div class="form-row form-row-top">
                    <label for="filters_parent_email"><?php echo __('Email:') ?></label>
                    <div class="content">
                        <input id="filters_tutor_email" name="filters[parent_email]"
                               value="<?php echo $e($filters, 'parent_email') ?>">
                    </div>

                    <label for="filters_professor_id"><?php echo __('Professor:') ?></label>
                    <div class="content">
                        <?php echo select_tag('filters[professor_id]', objects_for_select(
                            ProfesorPeer::doSelectOrderByNombreAndProfile(),
                            'getId',
                            '__toString',
                            $e($filters, 'professor_id'),
                            'include_blank=true')) ?>
                    </div>
                </div>

                <div class="form-row form-row-top">
                    <label for="filters_is_paid"><?php echo __('Estat pagament:') ?></label>
                    <div class="content">
                        <?php echo select_tag('filters[is_paid]', options_for_select(array(1 => __('yes'), 0 => __('no')), $e($filters, 'is_paid'), array('include_custom' => __("yes or no"),)), array()) ?>
                    </div>

                    <label for="filters_inscription_date" style="width: 18em !important">
                        <?php echo __('Data de naixement:') ?></label>
                    <div class="content">
                        <?php echo input_date_range_tag('filters[inscription_date]',
                            isset($filters['inscription_date']) ? $filters['inscription_date'] : null,
                            array('rich' => true, 'calendar_button_img' => '/sf/sf_admin/images/date.png'))
                        ?>
                    </div>
                </div>

            </fieldset>


            <ul class="sf_admin_actions">
                <input class="sf_admin_action_reset_filter" value="<?php echo __('reset') ?>" onclick="document.location.href='<?php echo url_for('Inscription/students') ?>';" type="button">
                <li><?php echo submit_tag(__('filter'), 'class=sf_admin_action_filter') ?></li>
                <li>
                <li>
                    <input value="<?php echo __('Exportar') ?>" name="export" class="sf_admin_action_export" id="export" type="submit">
                </li>
            </ul>
        </form>
    </div>

    <div id="sf_admin_content">
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
    $('#student-list').on('click', '.expand-next-block', function () {
      var collapsibleRows = []
      var nextRow = $(this).closest('.user-row').next()
      do {
        collapsibleRows.push(nextRow)
        nextRow = nextRow.next()
      } while (nextRow.length && !nextRow.hasClass('user-row'))

      $.each(collapsibleRows, function () {
        $(this).toggle()
      })
      $(this).toggleClass('expanded')
    })


    $('.user-row').each(function () {
      var nextRow = $(this).next()
      var count = 0
      do {
        nextRow = nextRow.next()
        count++
      } while (nextRow.length && !nextRow.hasClass('user-row'))

      $('.student-count', this).html(' (' + count + ' <?php echo __('inscripcions')?>)')
    })
  })
</script>


<style>
    #student-list tbody tr {
        display: none;
    }

    #student-list tbody tr.user-row {
        display: table-row;
    }

    .user-row td {
        font-weight: bold;
    }

    .expand-next-block {
        cursor: pointer;
        width: 16px;
        height: 16px;
        background-image: url("/images/plus.png");
        display: inline-block;
    }

    .expand-next-block.expanded {
        background-image: url("/images/minus.png");
    }

    .form-row-top .content {
        float: left;
        padding-left: 0 !important;
        margin-right: 10px;
    }

    span.student-count {
        font-weight: normal;
    }
</style>