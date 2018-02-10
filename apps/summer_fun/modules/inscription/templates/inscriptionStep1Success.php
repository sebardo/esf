<?php use_helper('Validation') ?>


<?php $culture = $sf_user->getCulture(); ?>
<?php use_helper('Object'); ?>
<?php if ($sf_user->getAttribute('step2')){

    $mostrar_formulari="ocultar";
    $mostrar_boto_modificar="mostrar";
    $mostrar_confirm="ocultar";


}else if (!$sf_user->getAttribute('step2')){
    $mostrar_formulari="mostrar";
    $mostrar_boto_modificar="ocultar";
    $mostrar_confirm="ocultar";

}else if ($sf_user->getAttribute('confirm')){

    $mostrar_formulari="ocultar";
    $mostrar_boto_modificar="ocultar";
    $mostrar_confirm="mostrar";
}?>

<div id="container-inscriptions">




    <div class="content_inscriptions">



        <?php echo form_tag('@inscription_step1_' . $sf_user->getCulture(), array('name' => 'inscriptionStep1', 'id' => 'inscriptionStep1', 'class' => $mostrar_formulari, 'enctype' => 'multipart/form-data')); ?>

        <h2><?php echo __('Inscripcions') ?></h2>
        <p><?php  echo __('Per inscriure el vostre fill / la vostra filla a les activitats de l\'English Summer Fun, seguiu les indicacions i ompliu les dades del formulari.')?></p>


        <h3><?php echo __('Dades del centre on realitzarà l\'English Summer Fun')?></h3>

        <?php echo label_for("centre",  __("Centre:"), array('id'=>'labelCentre')) ?>
        <?php include_partial('select_centers', array('name' => 'center', 'default' => __('default'),'centers'=> $centers)); ?>

        <div style="margin-top:10px;">
            <?php echo label_for("inscripcions",  __("Inscripcions:"),array('id'=>'labelCentre')) ?>
            <?php echo select_tag('inscripcions', options_for_select(array(1,2,3,4,5,6), $sf_user->getAttribute('inscriptions',1)-1)) ?>
        </div>
        
        <div id="box-discount" style="display:none">
        	<p style="margin-bottom:2px"><?php echo __('Descomptes') ?>:</p>
        	<ul style="margin-top:2px">
        		<li id="box-discount-brothers"></li>
        		<li id="box-discount-weeks"></li>
        		<li id="box-discount-kidsAndUs"></li>
        		<li id="box-discount-other">
                    <?php if (isset($center2) && $center2->getCustomDiscount()): ?>
                        <?php echo $center2->getCustomDiscount() ?>
                    <?php endif ?>
                </li>
        	</ul>
        </div>



        <?php if (!$error) { $courses=null; $center2=null; $centre=null;}

        if ($sf_user->getAttribute('show1')){
            $show1=1;
            $show2=$sf_user->getAttribute('show2');
            $show3=$sf_user->getAttribute('show3');
            $show4=$sf_user->getAttribute('show4');
            $show5=$sf_user->getAttribute('show5');
            $show6=$sf_user->getAttribute('show6');

        }


        ?>
        <?php include_partial('student_fields',array('id' => 1,'show'=>$show1, 'error'=>$error, 'courses'=>$courses, 'kidsAndUsCenters' => $kidsAndUsCenters, 'different_parents'=> 0, 'center'=>$center2, 'provincias' => $provincias, 'photo' => isset($studentPhoto1) ? $studentPhoto1 : null, 'summerFunCenters' => $summerFunCenters, 'schoolYears' => $schoolYears)) ?>
        <?php include_partial('student_fields',array('id' => 2,'show'=>$show2, 'error'=>$error, 'courses'=>$courses, 'kidsAndUsCenters' => $kidsAndUsCenters, 'different_parents' => $sf_user->getAttribute('differentParents2',0),'center'=>$center2, 'provincias' => $provincias, 'photo' => isset($studentPhoto2) ? $studentPhoto2 : null, 'summerFunCenters' => $summerFunCenters, 'schoolYears' => $schoolYears)) ?>
        <?php include_partial('student_fields',array('id' => 3,'show'=>$show3, 'error'=>$error, 'courses'=>$courses, 'kidsAndUsCenters' => $kidsAndUsCenters, 'different_parents' => $sf_user->getAttribute('differentParents3',0),'center'=>$center2, 'provincias' => $provincias, 'photo' => isset($studentPhoto3) ? $studentPhoto3 : null, 'summerFunCenters' => $summerFunCenters, 'schoolYears' => $schoolYears)) ?>
        <?php include_partial('student_fields',array('id' => 4,'show'=>$show4, 'error'=>$error, 'courses'=>$courses, 'kidsAndUsCenters' => $kidsAndUsCenters, 'different_parents' => $sf_user->getAttribute('differentParents4',0),'center'=>$center2, 'provincias' => $provincias, 'photo' => isset($studentPhoto4) ? $studentPhoto4 : null, 'summerFunCenters' => $summerFunCenters, 'schoolYears' => $schoolYears)) ?>
        <?php include_partial('student_fields',array('id' => 5,'show'=>$show5, 'error'=>$error, 'courses'=>$courses, 'kidsAndUsCenters' => $kidsAndUsCenters, 'different_parents' => $sf_user->getAttribute('differentParents5',0),'center'=>$center2, 'provincias' => $provincias, 'photo' => isset($studentPhoto5) ? $studentPhoto5 : null, 'summerFunCenters' => $summerFunCenters, 'schoolYears' => $schoolYears)) ?>
        <?php include_partial('student_fields',array('id' => 6,'show'=>$show6, 'error'=>$error, 'courses'=>$courses, 'kidsAndUsCenters' => $kidsAndUsCenters, 'different_parents' => $sf_user->getAttribute('differentParents6',0),'center'=>$center2, 'provincias' => $provincias, 'photo' => isset($studentPhoto6) ? $studentPhoto6 : null, 'summerFunCenters' => $summerFunCenters, 'schoolYears' => $schoolYears)) ?>

        <?php if ($sf_user->getCulture() == 'fr'): ?>
            <h3></h3>
            <p>Si vous désirez recevoir une attestation fiscale pour frais de garde à la fin de la semaine, nous vous prions de nous le communiquer à l’avance en cochant la boîte ci-dessous.</p>
            <?php echo radiobutton_tag("certificated", 1, false) ?><span><?php echo __('Sí') ?></span>
            <?php echo radiobutton_tag("certificated", 0, true) ?><span><?php echo __('No') ?></span>
            <div id="certTextWidget" style="display: none;">
                <p>À quel nom?</p>
                <?php echo input_tag('certificatedName', null, array('class' => 'prova')) ?>
                <?php if ($sf_request->hasError('certificatedName')): ?>
                    <p class="validation-error">&uarr; <?php echo $sf_request->getError('certificatedName') ?> &uarr;</p>
                <?php endif ?>
            </div>

        <?php endif ?>

        <h3><?php echo $sf_user->getCulture() != 'fr' ? __('Modalitat de pagament') : __('payment') ?></h3>

        <?php if ($sf_user->getCulture() != 'it' && (!isset($center2) || $center2->getShowBecaWidget())): ?>
        <div id="beca-widget">    
            <div class="student-disability">
                <label class="student_beca" for="isStudentBeca">*<?php echo __('Sol·licita ajut econòmic, beca?') ?>:</label>
                <?php echo radiobutton_tag("isStudentBeca", 1, false) ?><span><?php echo __('Sí') ?></span>
                <?php echo radiobutton_tag("isStudentBeca", 0, true) ?><span><?php echo __('No') ?></span>
            </div>
            <p><?php echo __("Recordeu que es poden demanar ajuts per a 10 dies de casal, 2 setmanes. Si s'inscriu l'infant més setmanes, el pagament s'haurà de fer en el període ordinari.") ?></p>
        </div>
        <?php endif ?>

        <?php if ($sf_user->getCulture() != 'fr'): ?>

            <p><?php echo __('Per fer efectiva la inscripció a l\'English Summer Fun, confirmeu si us plau la modalitat de pagament que preferiu.' ) ?></p>

            <div id="modalitat_pagament">

                <?php if (isset($center2)) {

                    include_partial('modalitat_pagament', array('center'=>$center2));

                 } ?>
            </div>
            <br/>
            <span><?php echo __('Indiqueu si voleu fraccionar el pagament:' ) ?></span>
            <?php echo radiobutton_tag('fraccionar', 1, false) ?><span><?php echo __('Sí, pagar ara el 50%')?></span>
            <?php echo radiobutton_tag('fraccionar', 0, true) ?><span><?php echo __('No, pagar ara el 100%')?></span>

        <?php else: ?>

            <p><?php echo __('payment_fr') ?></p>

        <?php endif ?>

        <div id="final_line"></div>
        <div id="privacy_policy">

            <?php echo checkbox_tag( 'privacyPolicy', 1,false) ?>
            <?php echo __('He llegit i accepto les')  ?> <a id="a-cond-generales" href="#" target="_blank"><?php echo __('condicions generals') ?></a> <?php echo __('i la') ?> <span id ="linkPrivacyPolicy"><?php echo __('política de privacitat') ?> </span>

            <?php if ($sf_request->hasError('privacyPolicy')): ?>
                <p class="validation-error error-privacitat">&uarr; <?php echo $sf_request->getError('privacyPolicy') ?> &uarr;</p>
            <?php endif ?>

            <div id="privacy_policy_text" class="ocultar">
                <img id="privacy_close"src="/images/close.png" alt="close">
                <div>
                <span class="bold"><?php echo __('PROTECCIÓ DE DADES DE CARÀCTER PERSONAL.')?></span> <?php echo __('privacity_police')?>
             </div>
            </div>
        </div>
        <div class="wrapper-btn">
            <p id="address-tpv">
                <?php if (isset($center2)): ?>
                    <?php echo $center2->getAddressTpv() ?>
                <?php endif ?>
            </p>
            <div id="submit_step1"><?php echo __('Següent Pas') ?></div>
        </div>









        </form>
        
        <form id="form-photo" action="<?php echo url_for("@inscripcions_upload_photo") ?>" enctype="multipart/form-data" method="post" style="display:none">
        	<input type="file" id="studentPhoto" value="" name="studentPhoto" accept="image/*">
        	<input type="hidden" id="studentId" name="studentId" value=""/>
        </form>


        <!--[if lt IE 9]>
        <p class="browsehappy"><?php echo __('You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.') ?></p>
        <![endif]-->

<?php

    /** PANTALLA DE CONFIRMACIÓN **/

?>
    <div id="inscriptionStep2" class="<?php echo $mostrar_boto_modificar ?>">


      <h2><?php echo __('Confirmació') ?></h2>
      <p><?php echo __('Revisa que les dades de la inscripció siguin correctes.')?></p>

      <h3><?php echo __('Informació general') ?></h3>

      <div id="informacio_general">
         <div id="informacio_general_esquerra">
             <div><span class="label"><?php echo __('Centre:')?></span> <?php echo $center2 ?></div>
             <div><span class="label"><?php echo __('Inscripcions:')?></span> <?php echo isset($inscripcions) ? $inscripcions + 1 : 0 ?></div>
             <?php if ($sf_user->getCulture() != 'fr'): ?>
                <div><span class="label"><?php echo __('Pagament:')?></span> <?php echo InscriptionPeer::getMethodPaymentName($payment) ?></div>
                <div><span class="label"><?php echo __('Fraccionar pagament:')?></span> <?php echo InscriptionPeer::getSplitPaymentName($fraccionar) ?></div>
             <?php endif ?>
         </div>

         <div id="informacio_general_dreta">
            <?php if (isset($inscripcions)): ?>
                <?php $total = 0; ?>
                <?php for ($i = 1; $i <= $inscripcions + 1; $i++): ?>
                    <?php $total += ${"subTotalInscripcioAlumne$i"}; ?>
                    <div><span class="bold"><?php echo __('Inscripció') .' '.$i ?></span> <span class="preu"><?php echo number_format(${"subTotalInscripcioAlumne$i"}, 2, ',', '.') ?> €</span></div>
                    <?php if (isset(${"discountPercentRegistrationStudent$i"}) && ${"discountPercentRegistrationStudent$i"} > 0): ?>
                        <div><span class="bold">&nbsp;&nbsp;<?php echo __('Descuento') ?></span> <span class="preu">- <?php echo number_format(${'subTotalInscripcioAlumne'.$i} * (${"discountPercentRegistrationStudent$i"} / 100), 2, ',', '.') ?> €</span></div>
                        <?php $total -= ${'subTotalInscripcioAlumne'.$i} * (${"discountPercentRegistrationStudent$i"} / 100); ?>
                    <?php elseif (isset(${"discountAmountRegistrationStudent$i"}) && ${"discountAmountRegistrationStudent$i"} > 0): ?>
                        <div><span class="bold">&nbsp;&nbsp;<?php echo __('Descuento') ?></span> <span class="preu">- <?php echo number_format(${'discountAmountRegistrationStudent'.$i}, 2, ',', '.') ?> €</span></div>
                        <?php $total -= ${"discountAmountRegistrationStudent$i"}; ?>
                    <?php endif ?>

                    <?php if (isset(${"amountServices$i"})): ?>
                        <div><span class="bold">&nbsp;&nbsp;<?php echo __('Servicios añadidos') ?></span> <span class="preu"><?php echo number_format(${"amountServices$i"}, 2, ',', '.') ?> €</span></div>
                        <?php $total += ${"amountServices$i"}; ?>
                    <?php endif ?>
                <?php endfor ?>
            <?php endif ?>

            <div class="totals">
                <div>
                  <span class="bold"><?php echo __('Total a pagar')?>:</span> <span class="preu bold"><?php echo isset($total) ? number_format($total, 2, ',', '.') : '0,00' ?> €</span>
                </div>
             </div>
         </div>
          <?php if ($payment == InscriptionPeer::METHOD_PAYMENT_TPV): ?>
          <?php if ($center2 && $center2->getAddressTpv()): ?>
          <div class="address-tpv">
              <?php echo $center2->getAddressTpv() ?>
          </div>
          <?php endif ?>
          <?php endif ?>
      </div>

	  <?php if (isset($inscripcions)): ?>
      <?php for ($i=1;$i<=$inscripcions+1;$i++){?>
      <div class="dades_alumne clear">

            <h3><?php echo __('Dades de l\'alumne') .$i ?></h3>

            <div class="dades_alumne_esquerra">
				<div class="profile-image profile-image-step2">
					<img src="<?php echo sfConfig::get('app_inscripcion_imagen_directorio_temporal_template') . ${'studentPhoto' . $i} ?>"/>
				</div>
                <div><span class="label"><?php echo __('Nom') ?>:</span> <?php echo ${'studentName'.$i} ?></div>
                <div><span class="label"><?php echo __('Cognom 1') ?>:</span> <?php echo ${'studentPrimerApellido'.$i} ?></div>
                <?php if ($sf_user->getCulture() != 'fr'): ?>
                    <div><span class="label"><?php echo __('Cognom 2') ?>:</span> <?php echo ${'studentSegundoApellido'.$i} ?></div>
                <?php endif ?>
                <div><span class="label"><?php echo __('Data de naixement:')?></span> <?php echo ${'studentBirthDate'.$i} ?></div>
                <div><span class="label"><?php echo __('Adreça:')?></span> <?php echo ${'studentAddress'.$i} ?></div>
                <div><span class="label"><?php echo __('Codi postal:')?></span> <?php echo ${'studentZip'.$i} ?></div>
                <div><span class="label"><?php echo __('Població:') ?></span> <?php echo isset(${'studentCity'.$i}) ? ${'studentCity'.$i} : '' ?></div>
                <?php if ($sf_user->getCulture() != 'fr'): ?>
                    <div><span class="label"><?php echo __('Província') ?>:</span> <?php echo isset(${'studentProvinciaName'.$i}) ? utf8_encode(${'studentProvinciaName'.$i}) : '' ?></div>
                <?php endif ?>

                <div><span class="label"><?php echo __('registration.trans239')?>:</span> <?php echo ${'isStudentKidAndUs'.$i} == 1 ? __('Si') : __('No') ?></div>
                
                <?php if (${'isStudentKidAndUs'.$i} == 1): ?>
                    <div><span class="label"><?php echo __('registration.trans168')?></span> <?php echo !empty(${'studentSchoolYear'.$i}) ? ${'studentSchoolYear'.$i} : ${'studentSchoolYearOther'.$i} ?></div>
                <?php endif ?>
                
                <?php if (isset(${"kidsAndUsCenter$i"}) && ${'isStudentKidAndUs'.$i} == 1): ?>
                    <div class="full-height"><span class="label"><?php echo __('origin_kids_and_us_center') . ':'?></span> <?php echo KidsAndUsCenterPeer::getNameById(${"kidsAndUsCenter$i"}) ?></div>
                <?php endif; ?>

                <div class="full-height"><span class="label"><?php echo __('summer_fun_center') . ':'?></span> <?php echo (${"summerFunCenter$i"} && ${"summerFunCenter$i"} != -1) ? SummerFunCenterPeer::getTitleById(${"summerFunCenter$i"}) : ${"summerFunCenterOther$i"} ?></div>

                <div class="full-height"><span class="label"><?php echo __('school_year') . ':'?></span> <?php echo SchoolYearPeer::getTitleById(${"schoolYear$i"}) ?></div>
                
                <div class="full-height">
                	<span class="label"><?php echo __('Té alguna discapacitat') ?>:</span> <?php echo ${'isStudentDisability'.$i} == 1 ? __('Si') : __('No') ?>
                	<?php if (${'isStudentDisability' . $i} == 1): ?>
	                	<?php if (!empty(${'studentDisabilityLevel' . $i})): ?>
	                	<b style="margin-left:10px;"><?php echo __('Grau') ?>:</b> <?php echo ${'studentDisabilityLevel' . $i} ?>&nbsp;%
	                	<?php endif ?>
	                	<?php if (!empty(${'studentDisabilityComment' . $i})): ?>
	                	<b style="margin-left:10px;"><?php echo __('Quina') ?>:</b> <?php echo ${'studentDisabilityComment' . $i} ?>
	                	<?php endif ?>
                	<?php endif ?>
                </div>

                <div class="full-height"><span class="label"><?php echo __('Al·lèrgies, intoleràncies o malalties cròniques:')?></span>

                    <?php if (${'studentAllergies'.$i}=='true' && ${'studentAllergiesDescription'.$i} == ''){

                        echo __('Sí');

                     }else{ ?>
                    <?php echo (${'studentAllergies'.$i}=='false' ?  __('No') :   ${'studentAllergiesDescription'.$i})  ?>

                        <?php } ?>
                </div>
                <?php if ($culture != 'fr'): ?>
                <div class="full-height"><span class="label"><?php echo __('Núm. targeta sanitària')  . ":" ?></span> <?php echo ${'studentNumTarjetaSanitaria'.$i} ?> <?php if (!empty(${'studentTarjetaSanitariaCompanyia'.$i})): ?><?php echo "(" . ${'studentTarjetaSanitariaCompanyia'.$i} . ")" ?><?php endif ?></div>
                <?php endif ?>
                <div class="full-height"><span class="label"><?php echo __('Amics que vindran al casal:')?></span> <?php echo ${'studentFriends'.$i} ?></div>

                <?php if ($sf_user->getCulture()!='it' && $center2 && $center2->getShowBecaWidget()){ ?>
                <div><span class="label"><?php echo __('Beca:') ?></span> <?php echo ${'isStudentBeca'.$i} == 1 ? __('Si') : __('No') ?></div>
                <?php } ?>

                <?php if (!empty(${"studentComments$i"})): ?>
                    <div class="full-height"><span class="label"><?php echo __('Altres aspectes a tenir en compte')?>:</span> <div><?php echo ${"studentComments$i"} ?></div></div>
                <?php endif ?>

                <div class="full-height"><span class="label"><?php echo __('registration.trans237')?></span>
                    <?php if (${'studentIsVaccinated' . $i} == 1): ?>
                        <?php echo __('Sí'); ?>
                    <?php else: ?>
                        <?php echo __('No'); ?>
                    <?php endif ?>
                </div>

            </div>
            <div class="dades_alumne_dreta">
                <h4><?php echo __('Setmanes de la inscripció') ?></h4>

                <?php $z = 0; ?>
                <?php foreach ($courses as $course): ?>
                    <?php if ($course->getIsRegistrationOpen()): $z++; ?>
                        <div><input type="checkbox" name="setmanaDisabled" disabled <?php echo isset(${'week'.$z.'alumne'.$i}) ? 'checked="true"' : '' ?>>
                        <?php echo __('Setmana ')?> <span class="lowcase"><?php echo $course->getWeek(); ?></span></div>
                        <p class="horari2"> <?php if ($course->getSchedule()) echo $course->getSchedule() ?></p>
                        <?php foreach ($course->getCourseHasServicess() as $courseService): ?>
                            <?php $service = $courseService->getService(); ?>
                            <?php if (isset($service)): ?>
                                <?php $service->setCulture($sf_user->getCulture()) ?>
                                <div class="acollida-confirm">
                                    <div class="widgets">
                                        <?php foreach ($service->getServiceSchedules() as $schedule): ?>
                                            <?php $schedule->setCulture($sf_user->getCulture()); ?>
                                            <?php if (isset(${'week' . $z . 'student' . $i . 'service' . $service->getId() . 'schedule' . $schedule->getId()})): ?>
                                                <input type="checkbox" disabled checked="checked"><?php echo $service->getName() . ' (' . $schedule->getName() . ')' ?><br/>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            <?php endif ?>
                        <?php endforeach ?>
                    <?php endif ?>
                <?php endforeach ?>
            
            
                <?php if (isset(${'studentExcursion'.$i})): ?>
                    <?php if (${'studentExcursion'.$i} == 1): ?>
                    <?php echo __("Sí, dono permís per a que l'infant realitzi les excursions")?>
                    <?php else : ?>
                    <?php echo __("No, no dono permís per a que l'infant realitzi les excursions")?>
                    <?php endif ?>
                <?php endif ?>

                <?php if (isset(${'studentCustomQuestion'.$i}) && isset($center2)): ?>
                    <?php if (isset(${'studentExcursion'.$i})): ?><br/><?php endif ?>
                    <?php echo $center2->getCustomQuestion() ?>
                    <?php if (${'studentCustomQuestion'.$i} == 1): ?>
                    <?php echo __("Sí")?>
                    <?php else : ?>
                    <?php echo __("No")?>
                    <?php endif ?>
                <?php endif ?>

            </div>

      <div class="dades_pares clear">

           <?php if (isset(${'student'.$i.'DifferentParents'}) || $i == 1){?>

             <?php if (isset($student2DifferentParents)){?>

              <h3><?php  echo __('Dades dels pares/tutors legals de l\'alumne') .' '.$i?> </h3>

              <?php }else{ ?>
              <h3><?php  echo __('Dades dels pares/tutors legals')?> </h3>
              <?php } ?>
                <div class="dades_pares_esquerra">

                    <h5><?php echo __('Dades del pare/mare/tutor legal')?></h5>
                    <div><span class="label_pare"><?php echo __('Nom')?>:</span> <?php echo ${'fatherName'.$i} ?></div>
                    <div><span class="label_pare"><?php echo __('Cognom 1')?>:</span> <?php echo ${'fatherPrimerApellido'.$i} ?></div>
               <?php if ($culture != 'fr'): ?>
                    <div><span class="label_pare"><?php echo __('Cognom 2')?>:</span> <?php echo ${'fatherSegundoApellido'.$i} ?></div>
               <?php endif ?>
                    <div class="doble"><span class="label_pare"><?php echo __('Telèfon:')?></span> <?php echo ${'fatherPhone'.$i} ?></div>
                    <div><span class="label_pare"><?php echo __('DNI:')?></span> <?php echo ${'fatherDni'.$i} ?></div>
                    <div><span class="label_pare"><?php echo __('Email:')?></span> <?php echo ${'fatherMail'.$i} ?></div>
                    <?php if (isset(${'fatherMail'.$i.'Principal'})){ ?>

                              <div class="mail_principal">(<?php echo __('Principal de contacte')?>)</div>

                    <?php } ?>
                </div>
                <div class="dades_pares_dreta">

                    <h5><?php echo __('Dades del pare/mare/tutor legal')?></h5>
                    <div><span class="label_pare"><?php echo __('Nom')?>:</span> <?php echo ${'motherName'.$i} ?></div>
                    <div><span class="label_pare"><?php echo __('Cognom 1')?>:</span> <?php echo ${'motherPrimerApellido'.$i} ?></div>
               <?php if ($culture != 'fr'): ?>
                    <div><span class="label_pare"><?php echo __('Cognom 2')?>:</span> <?php echo ${'motherSegundoApellido'.$i} ?></div>
               <?php endif ?>
                    <div class="doble"><span class="label_pare"><?php echo __('Telèfon:')?></span> <?php echo ${'motherPhone'.$i} ?></div>
                    <div><span class="label_pare"><?php echo __('DNI:')?></span> <?php echo ${'motherDni'.$i} ?></div>
                    <div><span class="label_pare"><?php echo __('Email:')?></span> <?php echo ${'motherMail'.$i} ?></div>
                    <?php if (isset(${'motherMail'.$i.'Principal'})){ ?>

                    <div class="mail_principal">(<?php echo __('Principal de contacte')?>)</div>

                    <?php } ?>

                </div>

            <?php }else { ?>
                <p><?php echo __('Les dades dels pares o tutors legals son iguals que el primer fill/a.') ?></p>

            <?php } ?>
      </div>

    <?php if ($sf_user->getCulture() == 'fr'): ?>
        <h3></h3>
        <div style="height: 44px">
            <span class="label" style="width: 282px;">Désirez recevoir une attestation fiscale pour frais de garde à la fin de la semaine?</span>
            <?php echo ${'certificated'} == 1 ? __('Sí') : __('No') ?>
        </div>

        <?php if (${'certificated'} == 1): ?>
        <div>
            <span class="label" style="width: 85px;">À quel nom?:</span>  <?php echo ${'certificatedName'} ?></span>
        </div>
        <?php endif; ?>


    <?php endif; ?>

<?php }?>
<?php endif ?>


      </div>


        <div class="<?php echo $mostrar_boto_modificar ?>" id="return_step1" ><?php echo __('Modificar') ?></div>
        <div class="<?php echo $mostrar_boto_modificar ?>" id="confirm_button" ><?php echo __('Confirmar') ?></div>

    </div>


    </div>
<?php /* 
    <div id="confirm" class="<?php echo $mostrar_confirm ?>">
        <p>has estat inscrit</p>

    </div>
*/?>   

    </div>

</div>

    <?php //$sf_user->setAttribute('prova','hola2') ?>
    <?php //echo $sf_user->getAttribute('prova') ?>

</div>


<div id="help-image-box" style="display:none;position:fixed;left:50%;top:50%">
	<img id="help-image-box-close" src="/images/close.png" alt="close">
    <div>
       <ul>
       		<li><?php echo __('Tomar una foto frontal del niño – se puede hacer con cualquier dispositivo móvil.') ?></li>
       		<li><?php echo __('Guardar / descargar la imagen en el escritorio.') ?></li>
       		<li><?php echo __('En el momento de realizar la inscripción, seleccionarla e iniciar el proceso de carga. El archivo deberá estar en formato de imagen: jpg/png.') ?></li>
       </ul>
    </div>
</div>


<script type="text/javascript">
	var txtSelCentro = "<?php echo __('Seleccione primero un centro por favor') ?>";
    jQuery(function($) {

        $('.origin_center').append($('<option>', {
            value: '',
            text: '<?php echo  __('registration.trans234') ?>',
        }));
        
        $('#inscripcions').change(function(){
//            $('.origin_center').append($('<option>', {
//                value: '',
//                text: '<?php echo  __('registration.trans234') ?>',
//            }));
        });
        
        
       
        if($('#summerFunCenterOther1').val() != ''){
            $('#summerFunCenter1').find('option[text="<?php echo  __('registration.trans234') ?>"]').val();
        }
        
        $('.mail').bind('copy paste', function(e) {
            e.preventDefault();
        });

        <?php for($i=2;$i<7;$i++){ ?>

        $('#student<?php echo $i ?>DifferentParents').change(function(){

           if($(this).is(':checked')){

              $('#fathers<?php echo $i ?>').show();

           }else{
               $('#fathers<?php echo $i ?>').hide();
           }


        });
    <?php } ?>

        $('#linkPrivacyPolicy').click(function(){
            $('#privacy_policy_text').show();

        });

        $('#privacy_close').click(function(){
            $('#privacy_policy_text').hide();

        });

        $('#center').change(function()
        {
            var url = '<?php echo url_for('@setmanes_inscripcions_centre'); ?>';
            var idCentre = $('#center').val();
            var url3 = '<?php echo url_for('@inscripcions_modalitat_pagament'); ?>';

            $('.setmanes_preu1').load(url, { idCentre: idCentre, id: 1 });
            $('.setmanes_preu2').load(url, { idCentre: idCentre, id: 2 });
            $('.setmanes_preu3').load(url, { idCentre: idCentre, id: 3 });
            $('.setmanes_preu4').load(url, { idCentre: idCentre, id: 4 });
            $('.setmanes_preu5').load(url, { idCentre: idCentre, id: 5 });
            $('.setmanes_preu6').load(url, { idCentre: idCentre, id: 6 });

            $('#modalitat_pagament').load(url3, {idCentre: idCentre});

            $.ajax({
                url: '<?php echo url_for('@inscription_cond_generales'); ?>',
                data: 'id=' + idCentre,
                success: function(data) {
                    if (data) {
                        data = JSON.parse(data);
                        $('#a-cond-generales').attr('href', data.pdf);
                        $('#beca-widget').toggle(data.showBeca == 1);
                        if(data.showVaccination){
                            $('.vaccination').fadeIn('slow');
                        }else{
                            $('.vaccination').fadeOut('slow');
                        }
                    }else{
                        $('.vaccination').fadeOut('slow');
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        $('#a-cond-generales').click(function(e) {
            if ($(this).attr('href') == '#') {
                e.preventDefault();
                alert(txtSelCentro);
            }
        });

        $('#inscripcions').change(function(){


            var numInscr =parseInt($(this).val())+1;


            if (numInscr==1){
                $('#student2').hide();
                $('#student3').hide();
                $('#student4').hide();
                $('#student5').hide();
                $('#student6').hide();

            }   else if(numInscr==2){


                $('#student2').show();
                $('#student3').hide();
                $('#student4').hide();
                $('#student5').hide();
                $('#student6').hide();



            }else if (numInscr==3){

                $('#student2').show();
                $('#student3').show();
                $('#student4').hide();
                $('#student5').hide();
                $('#student6').hide();

            }else if (numInscr==4){

                $('#student2').show();
                $('#student3').show();
                $('#student4').show();
                $('#student5').hide();
                $('#student6').hide();

            }else if (numInscr==5){

                $('#student2').show();
                $('#student3').show();
                $('#student4').show();
                $('#student5').show();
                $('#student6').hide();

            }else if (numInscr==6){

                $('#student2').show();
                $('#student3').show();
                $('#student4').show();
                $('#student5').show();
                $('#student6').show();

            }

            $.ajax({

                url: 'change_number_students/' + numInscr


            });

        });





        $('#submit_step1').click(function(){


        <?php $sf_user->setAttribute('step2',1);?>



            $('#inscriptionStep1').submit();

        });

        $('#return_step1').click(function(){

            <?php $sf_user->setAttribute('step2',0);?>


            $('#inscriptionStep1').show();
            $('#inscriptionStep2').hide();

            $('#return_step1').hide();

        });

        $('#confirm_button').click(function(){
              //  console.log('prova2');

            $.ajax({

                url: '<?php echo url_for('@inscription_step2_confirm'); ?>',
                    complete: function(){
                    $('#inscriptionStep1').hide();
                    $('#inscriptionStep2').hide();

                    $('#return_step1').hide();
                    $('#inscriptionStep1').submit();
                }

            });
        });

        $('input[name^=isStudentDisability]').click(function(e) {
            var id = $(this).attr('name').substr($(this).attr('name').length - 1);
            $('#box-student-disability-' + id).css('visibility', $(this).val() == 1 ? 'visible' : 'hidden');
        });

        $('input[name^=isStudentDisability]:checked').each(function(e) {
            var id = $(this).attr('name').substr($(this).attr('name').length - 1);
            $('#box-student-disability-' + id).css('visibility', $(this).val() == 1 ? 'visible' : 'hidden');
        });

        $('input[name^=studentDisabilityLevel]').on('keyup', function(e) {
        	 if (/\D/g.test(this.value))
             {
                 this.value = this.value.replace(/\D/g, '');
             }                    
        });

        $('a[id^=link-profile-image]').on('click', function(e) {
            e.preventDefault();
        	var id = $(this).attr('id').substr($(this).attr('id').length - 1);
			$('#studentId').val(id);
			$('#studentPhoto').trigger('click');
        });
        
        $('a[id^=link-profile-help]').on('click', function(e) {
            $('#help-image-box').show();
            e.preventDefault();
        });

        $('#help-image-box-close').click(function() {
        	$('#help-image-box').hide();
        });

        $('#studentPhoto').on('change', function(e) {
        	$('#form-photo').ajaxSubmit({
                beforeSend: function() {
                	$('#profile-image-error' + $('#studentId').val()).text('<?php echo __('Espere, si us plau') ?>...');
                },
                success: function(data) {
                    //console.log(data);
                    data = JSON.parse(data);
                    if (data.status == "OK") {
                    	$('#profile-image-error' + $('#studentId').val()).text('');
                    	$('#profile-image' + $('#studentId').val()).attr('src', '<?php echo sfConfig::get('app_inscripcion_imagen_directorio_temporal_template') ?>' + data.message);
                        $('input[name=studentPhoto' + $('#studentId').val() + ']').val(data.message);
                    }
                    else {
                        $('#profile-image-error' + $('#studentId').val()).text(data.message);
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

        $('input[name^=studentPhoto]').each(function(e) {
        	if ($(this).val())
        	{
        		var id = $(this).attr('id').substr($(this).attr('id').length - 1);
        		$('#profile-image' + id).attr('src', '<?php echo sfConfig::get('app_inscripcion_imagen_directorio_temporal_template') ?>' + $(this).val());
        	}
        });

        $('input[name=certificated]').change(function(e)
        {
            if ($('input[name=certificated]:checked').val() == 1) {
                $('#certTextWidget').toggle(true);
            }
            else {
                $('#certTextWidget').toggle(false);
            }
        });

        if ($('input[name=certificated]:checked').val() == 1) {
            $('#certTextWidget').toggle(true);
        }

        $('select[id^="studentSchoolYear"]').change(function(e) {
            var id = $(this).attr('id').substr($(this).attr('name').length - 1);
            if ($(this).val() == '0')
            {
                $('#studentSchoolYearOther' + id).toggle(true);
                $('label[for=studentSchoolYearOther' + id + ']').toggle(true);
            }
            else {
                $('#studentSchoolYearOther' + id).toggle(false).val('');
                $('label[for=studentSchoolYearOther' + id + ']').toggle(false);
            }
        }).trigger('change');

        $('select[id^="summerFunCenter"]').change(function(e) {
            var id = $(this).attr('id').substr($(this).attr('name').length - 1);
            if ($(this).val() == -1)
            {
                $('#summerFunCenterOther' + id).toggle(true);
                $('label[for=summerFunCenterOther' + id + ']').toggle(true);
            }
            else {
                $('#summerFunCenterOther' + id).toggle(false).val('');
                $('label[for=summerFunCenterOther' + id + ']').toggle(false);
            }
        }).trigger('change');


        $('input[type=radio][name^=isStudentKidAndUs]').change(function() {
            var id = $(this).attr('name').substr($(this).attr('name').length - 1);
            $('#kidsAndUsCenterWidget' + id).toggle($(this).val() == 1);
            $('#kidsAndUsSchoolYear' + id).toggle($(this).val() == 1);
        }).each(function() {
            if ($(this).is(':checked')) {
                var id = $(this).attr('name').substr($(this).attr('name').length - 1);
                $('#kidsAndUsCenterWidget' + id).toggle($(this).val() == 1);
                $('#kidsAndUsSchoolYear' + id).toggle($(this).val() == 1);
            }
        });

    });

</script>