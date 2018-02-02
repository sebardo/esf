<div>

<div id="confirmation-inscription">

    <h2><?php echo __('Inscripció realitzada correctament') ?></h2>

    <p><?php echo __('Gràcies per inscriure-us a les activitats de l\'English Summer Fun.') ?></p>

    <p><?php echo __('En breus instants rebreu un correu electrònic a l\'adreça principal de contacte indicada on trobareu la informació necessària per a formalitzar la reserva i realitzar el pagament.') ?></p>
    <p><?php echo __('Recordeu que fins no fer efectiu el pagament no es formalitzarà la reserva de la plaça.') ?></p>
    <p><?php echo __('Moltes gràcies.') ?></p>

    <img src="/images/logo_pdf.jpg">

    <div id="final_line"></div>


    <a href="<?php echo url_for('@index_' .$sf_user->getCulture())?>" id="submit_step1"><?php echo __('Inici') ?></a>
</div>
</div>