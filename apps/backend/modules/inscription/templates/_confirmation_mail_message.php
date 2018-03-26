

<style>
    p{
        font: arial;
        font-size: 12px;;
    }
ul{
    margin-left:50px;
    list-style-type: disc;
}

</style>


<?php if ($centre->{'getInscriptionConfirmationMail'.$sf_user->getCulture()}()!=null){ ?>

     <?php echo $centre->{'getInscriptionConfirmationMail'.$sf_user->getCulture()}() ?>


<?php echo image_tag($sf_user->getCulture().'/logo.png',array('absolute'=>true, 'width'=>'205px;')) ?>

<?php }else { ?>



        <p><?php echo __('Benvolguts pares,') ?></p>
        <p><?php echo __('Gràcies per la vostra inscripció a les activitats de l\'English Summer Fun.') ?></p>
        <p><?php  echo __('Per formalitzar la reserva, cal que seguiu els passos següents:') ?></p>
        <ul>
            <li><?php echo __('Descarregueu els PDF.') ?></li>
            <li><?php echo __('Imprimiu-los.') ?></li>
            <li><?php echo __('Realitzeu el pagament segons condicions.') ?></li>
            <li><?php echo __('Signeu el document.') ?></li>
            <li><?php echo __('Presenteu-lo al centre juntament amb una fotografia del nen i una fotocopia de la targeta sanitària, abans del 24 d\'abril.') ?></li>

        </ul>
        <p><?php echo __('El termini per fer el pagament, en la modalitat seleccionada (import total o fragmentat), és de 5 dies hàbils a partir de la recepció d’aquest missatge. En cas que el feu per transferència bancària, haureu de presentar el comprovant de pagament al centre, juntament amb el document PDF de la inscripció signat, una fotografia del nen i una fotocopia de la targeta sanitària abans del 24 d\'abril.') ?></p>
        <p><?php echo __('Recordeu que la reserva de plaça només serà efectiva un cop s’hagi efectuat aquest pagament.') ?></p>
        <p><?php echo __('Moltes gràcies un cop més per confiar en Kids&Us!') ?></p>
        <p><?php  echo __('Atentament, ') ?></p>

        <?php echo image_tag($sf_user->getCulture().'/logo.png',array('absolute'=>true, 'width'=>'205px;')) ?>

        <?php if ($sf_user->getCulture()=='ca' || $sf_user->getCulture()=='es') { ?>

                <p><?php  echo __('T. (+34) 902 93 40 23, ') ?></p>


                 <a href="mailto:info@kidsandus.es">info@kidsandus.es</a>

          <?php } ?>

<?php } ?>

