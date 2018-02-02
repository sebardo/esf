<?php
$i = $j = 0;
$hayExcursiones = false;
?>

<?php foreach ($courses as $clave => $course): ?>
    <?php $j++ ?>
    <div class="setmana">
        <?php if ($course->getIsRegistrationOpen()): ?>

            <?php $i++; ?>
            <?php echo checkbox_tag('week' .$i .'alumne' .$id ,  $course->getId() ) ?>
            <?php if (count($course->getInscriptionsByCourse()) < $course->getNumberOfPlaces()): ?>
                <input type="checkbox" name="<?php echo 'placesDisponiblesWeek' .$i .'alumne' .$id?>" id="<?php echo 'placesDisponiblesWeek' .$i .'alumne' . $id ?>" value="1" class="ocultar" checked>
            <?php endif ?>
            <?php
                $label = $course->getWeekWithSchedule() . ' - ' . __('Preu:') . $course->getPrice() .' €';
                if (count($course->getInscriptionsByCourse()) >= $course->getNumberOfPlaces()) {
                    $label .= ' ' . __('Llista d\'espera') . '*';
                }
            ?>
            <?php echo label_for('week' . $i .'alumne' . $id,  $label) ?>

            <?php include_partial('week_services', array('course' => $course, 'id' => $id, 'i' => $i)); ?>

            <?php if ($course->getExcursion()): ?>
                <?php
                    $course->getExcursion()->setCulture($sf_user->getCulture());
                    $hayExcursiones = true;
                ?>

                <div class="acollida-box">
                    <div class="title">
                        <strong>- <?php echo __('Excursió') . ": " ?></strong><?php echo trim($course->getExcursion()->getNombre()) ?>
                    </div>

                    <?php if (trim($course->getExcursion()->getDescripcion())): ?>
                        <div class="description"><?php echo trim($course->getExcursion()->getDescripcion()) ?></div>
                    <?php else: ?>
                        <div class="divider"></div>
                    <?php endif ?>
                </div>

            <?php endif ?>
            <?php if (count($course->getInscriptionsByCourse()) >= $course->getNumberOfPlaces()): ?>
                <div class="espera">* <?php echo __('Els possibles descomptes seran aplicats un cop confirmada la plaça') ?></div>
            <?php endif ?>

            <input type="text" name="preuSetmana<?php echo $i ?>" id="preuSetmana<?php echo $i ?>" value="<?php echo $course->getPrice()?>"  class="ocultar">

        <?php else: ?>

            <input type="checkbox" name="setmanaDisabled" id="setmanaDisabled" disabled>
            <?php echo label_for("setmana",  $course->getWeek()) ?>
            <span> <?php echo __('No queden places') ?>  </span>
            <p class="horari"> <?php if ($course->getSchedule()) echo $course->getSchedule() ?></p>

        <?php endif ?>
    </div>
    <?php if ($j < count($courses)): ?>
        <hr style="color:#EFEFEF; margin: 10px 0"/>
    <?php endif ?>
<?php endforeach ?>

<?php if ($sf_request->hasError('errorSetmanaStudent' . $id)): ?>
    <p class="validation-error"  style="margin-left:95px">&uarr; <?php echo $sf_request->getError('errorSetmanaStudent' .$id) ?> &uarr;</p>
<?php endif ?>

<input type="checkbox" name="nombreSetmanes" id="nombreSetmanes" value="<?php echo $i?>"  class="ocultar" checked>

<script type="text/javascript">
    $('#box-discount').toggle(false);
</script>

<?php if (isset($centre) && $centre->getWeeksDiscount() > 0 && ($centre->getWeeksPercentDiscount() > 0 || $centre->getWeeksAmountDiscount() > 0)): ?>
    <script type="text/javascript">
        $('#box-discount-weeks').text('<?php echo __("Descompte per inscriure a un infant a [1] o més setmanes: [2][3]", array('[1]' => $centre->getWeeksDiscount(), '[2]' => ($centre->getWeeksPercentDiscount() > 0 ? $centre->getWeeksPercentDiscount() : $centre->getWeeksAmountDiscount()), '[3]' => ($centre->getWeeksPercentDiscount() > 0 ? '%' : '€'))) ?>');
        $('#box-discount-weeks').toggle(true);
        $('#box-discount').toggle(true);
    </script>
<?php else: ?>
    <script type="text/javascript">
        $('#box-discount-weeks').toggle(false);
    </script>
<?php endif ?>

<?php if (isset($centre) && $centre->getBrothersDiscount() > 0 && ($centre->getBrothersPercentDiscount() > 0 || $centre->getBrothersAmountDiscount() > 0)): ?>
    <script type="text/javascript">
        $('#box-discount-brothers').text('<?php echo __("Descompte per inscriure a [1] o més germans: [2][3]", array('[1]' => $centre->getBrothersDiscount(), '[2]' => ($centre->getBrothersPercentDiscount() > 0 ? $centre->getBrothersPercentDiscount() : $centre->getBrothersAmountDiscount()), '[3]' => ($centre->getBrothersPercentDiscount() > 0 ? '%' : '€'))) ?>');
        $('#box-discount-brothers').toggle(true);
        $('#box-discount').toggle(true);
    </script>
<?php else: ?>
    <script type="text/javascript">
        $('#box-discount-brothers').toggle(false);
    </script>
<?php endif ?>

<?php if (isset($centre) && ($centre->getKidsAndUsStudentPercentDiscount() > 0 || $centre->getKidsAndUsStudentAmountDiscount() > 0)): ?>
    <script type="text/javascript">
        $('#box-discount-kidsAndUs').text('<?php echo __("Descompte per a alumnes Kids&Us: [1][2]", array('[1]' => ($centre->getKidsAndUsStudentPercentDiscount() > 0 ? $centre->getKidsAndUsStudentPercentDiscount() : $centre->getKidsAndUsStudentAmountDiscount()), '[2]' => ($centre->getKidsAndUsStudentPercentDiscount() > 0 ? '%' : '€'))) ?>');
        $('#box-discount-kidsAndUs').toggle(true);
        $('#box-discount').toggle(true);
    </script>
<?php else: ?>
    <script type="text/javascript">
        $('#box-discount-kidsAndUs').toggle(false);
    </script>
<?php endif ?>

<?php if (isset($centre) && $centre->getCustomDiscount() && (!isset($error) || !$error)): ?>
    <script type="text/javascript">
        jQuery(function($) {
            $('#box-discount-other').html("<?php echo addslashes(preg_replace("/\r|\n/", "", $centre->getCustomDiscount())) ?>").toggle(true);
            $('#box-discount').toggle(true);
        });
    </script>
<?php elseif (!isset($error) || !$error): ?>
    <script type="text/javascript">
        $('#box-discount-other').toggle(false);
    </script>
<?php endif ?>

<?php if (isset($centre) && $centre->getShowExcursionWidget() && $hayExcursiones): ?>
    <hr/>
    <div class="student_field excursion">
        <input type="radio" name="studentExcursion<?php echo $id ?>" value="1"><span><?php echo __("Sí, dono permís per a que l'infant realitzi les excursions") ?></span>
        <br/>
        <input type="radio" name="studentExcursion<?php echo $id ?>" value="0"><span><?php echo __("No, no dono permís per a que l'infant realitzi les excursions") ?></span>
    </div>

    <?php if ($sf_request->hasError('studentExcursion' . $id)): ?>
        <p class="validation-error" style="margin-left:113px;">&uarr; <?php echo $sf_request->getError('studentExcursion'.$id) ?> &uarr;</p>
    <?php endif ?>

<?php endif ?>

<?php if (isset($centre) && $centre->getCustomQuestion()): ?>
    <div class="student_field">
        <?php echo $centre->getCustomQuestion() ?>
        <br/>
        <input type="radio" name="studentCustomQuestion<?php echo $id ?>" value="1"><span><?php echo __("Sí") ?></span>
        <input type="radio" name="studentCustomQuestion<?php echo $id ?>" value="0"><span><?php echo __("No") ?></span>
    </div>

    <?php if ($sf_request->hasError('studentCustomQuestion' . $id)): ?>
        <p class="validation-error" style="margin-left:113px;">&uarr; <?php echo $sf_request->getError('studentCustomQuestion'.$id) ?> &uarr;</p>
    <?php endif ?>
<?php endif ?>

<script type="text/javascript">
    <?php if (isset($centre) && !$centre->getShowBecaWidget()): ?>
        $('div[id^=wbeca]').toggle(false);
        $('p[id^=wbecatext]').toggle(false);
    <?php else: ?>
        $('div[id^=wbeca]').toggle(true);
        $('p[id^=wbecatext]').toggle(true);
    <?php endif ?>
</script>