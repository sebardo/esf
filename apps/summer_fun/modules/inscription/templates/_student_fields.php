<?php if ($show){

    $class="mostrar";


 }else{
    $class="ocultar";

}?>

<div id="student<?php echo $id?>" class="<?php echo $class ?>">
    <h3><?php echo __("Dades de l'alumne") . " " . $id?></h3>

    <div class="setmanes_acollida">
        <div class="setmanes">
            <p><?php echo __('Setmanes disponibles')?>:</p>
            <div class="setmanes_preu<?php echo $id?>">
                <?php if ($error): ?>
                    <?php include_partial('weeks', array('courses' => $courses,'id' => $id,'centre' => $center, 'error' => $error)); ?>
                <?php endif ?>
            </div>
        </div>
    </div>

    <div class="student_data">
    	
    	<div class="profile-image">
			<img id="profile-image<?php echo $id ?>" src="/images/summer_fun/profile-placeholder.png"/>
			<a id="link-profile-image<?php echo $id ?>" href=""><?php echo __('Pujar foto') ?></a>
			<a id="link-profile-help<?php echo $id ?>" href=""><?php echo __('Com fer-ho?') ?></a>
			<div id="profile-image-error<?php echo $id?>" class="error"><?php if ($sf_request->hasError('studentPhoto' . $id)): ?>&uarr; <?php echo $sf_request->getError('studentPhoto' . $id) ?> &uarr;<?php endif ?></div>
		</div>
    
        <?php include_partial('text_field', array('field_name' => 'studentName' . $id, 'field_label' => __('Nom') . ":", 'obligatori' => true, 'class'=>'prova','size'=>50));?>
        <?php include_partial('text_field', array('field_name' => 'studentPrimerApellido' . $id, 'field_label' => __('Cognom 1')  . ":", 'obligatori' => true, 'class'=>'prova','size'=>50));?>
<?php if ($sf_user->getCulture() != 'fr'): ?>
        <?php include_partial('text_field', array('field_name' => 'studentSegundoApellido' . $id, 'field_label' => __('Cognom 2')  . ":", 'obligatori' => true, 'class'=>'prova','size'=>50));?>
<?php endif ?>

        <div class="student_field">

       <label><?php echo '*'.__('Data de naixement:') ?></label>
            <?php echo input_date_tag('studentBirthDate' . $id, null, array('rich' => true, 'culture' => $sf_user->getCulture(), 'format' => 'd/m/Y')) ?><span> (<?php echo __('dd/mm/aaaa') ?>)</span>

        <?php if ($sf_request->hasError('studentBirthDate'.$id)): ?>
        <p class="validation-error">&uarr; <?php echo $sf_request->getError('studentBirthDate' .$id) ?> &uarr;</p>
        <?php endif ?>
        </div>
		
        <?php include_partial('text_field', array('field_name' => 'studentAddress' . $id, 'field_label' => __('Adreça:'), 'obligatori' => true, 'class'=>'prova','size'=>50, 'help' => __('Carrer, Número, Pis, Porta')));?>

        <?php include_partial('text_field', array('field_name' => 'studentZip' . $id, 'field_label' => __('Codi postal:'), 'obligatori' => true, 'class'=>'prova','size'=>15));?>

        <?php include_partial('text_field', array('field_name' => 'studentCity' . $id, 'field_label' => __('Població:'), 'obligatori' => true, 'class'=>'prova','size'=>50));?>
<?php if ($sf_user->getCulture() != 'fr'): ?>
        <?php echo label_for("studentProvincia$id",  "*" . __("Província:"), array('id' => 'labelProvincia')) ?>
        <?php include_partial("select_provincias", array('name' => "studentProvincia$id", 'provincias' => $provincias, 'id' => $id)); ?>
<?php endif ?>

        <div class="student_field">
			<label class="prova" for="isStudentKidAndUs<?php echo $id?>">*<?php echo __('Alumne KidsUs' ) ?>:</label>
			<?php echo radiobutton_tag("isStudentKidAndUs$id", 1, true) ?><span><?php echo __('Sí') ?></span>
        	<?php echo radiobutton_tag("isStudentKidAndUs$id", 0, false) ?><span><?php echo __('No') ?></span>
		</div>

        <div class="student_field" id="kidsAndUsSchoolYear<?php echo $id ?>">
            <?php echo label_for("studentSchoolYear$id",  '*' . __('registration.trans168')) ?>
            <?php include_partial('completed_course', array('name' => 'studentSchoolYear' . $id)); ?>

            <div class="other">
                <?php echo label_for("studentSchoolYearOther$id",  __('registration.trans234'), array('style' => 'width: 50px; margin-left: 10px')) ?>
                <?php echo input_tag("studentSchoolYearOther$id", null, array('class' => 'prova', 'style'=>'width:175px;')) ?>
            </div>
        </div>

        <?php if ($sf_request->hasError("studentSchoolYear$id")): ?>
            <div class="validation-error" style="margin-left:192px;">&uarr; <?php echo $sf_request->getError("studentSchoolYear$id") ?> &uarr;</div>
        <?php endif ?>

        <div id="kidsAndUsCenterWidget<?php echo $id ?>" class="student_field">
            <?php echo label_for("kidsAndUsCenter$id", '*' . __('origin_kids_and_us_center') . ':') ?>
            <?php include_partial('kids_and_us_centers', array('name' => 'kidsAndUsCenter' . $id, 'default' => __('default'),'centers'=> $kidsAndUsCenters, 'id' => $id)); ?>


            <?php if ($sf_request->hasError('kidsAndUsCenter'.$id)): ?>
                <p class="validation-error" style="margin-left: 192px; clear: both">&uarr; <?php echo $sf_request->getError('kidsAndUsCenter'.$id) ?> &uarr;</p>
            <?php endif ?>
        </div>

        <div class="student_field">
            <?php echo label_for("summerFunCenter$id", '*' . __('summer_fun_center') . ':') ?>
            <?php include_partial('summer_fun_centers', array('name' => 'summerFunCenter' . $id, 'default' => __('default'),'centers'=> $summerFunCenters, 'id' => $id)); ?>
            <div class="other">
                <?php echo label_for("summerFunCenterOther$id",  __('registration.trans234'), array('style' => 'width: 50px')) ?>
                <?php echo input_tag("summerFunCenterOther$id", null, array('class' => 'prova', 'style'=>'width:175px;')) ?>
            </div>
        </div>

        <?php if ($sf_request->hasError('summerFunCenter'.$id)): ?>
        <p class="validation-error" style="margin-left:192px;">&uarr; <?php echo $sf_request->getError('summerFunCenter'.$id) ?> &uarr;</p>
        <?php endif ?>
        <?php if ($sf_request->hasError('summerFunCenterOther'.$id)): ?>
        <p class="validation-error" style="margin-left:192px;">&uarr; <?php echo $sf_request->getError('summerFunCenterOther'.$id) ?> &uarr;</p>
        <?php endif ?>

        <div class="student_field">
            <?php echo label_for("schoolYear$id", '*' . __('school_year') . ':') ?>
            <?php include_partial('school_years', array('name' => 'schoolYear' . $id, 'default' => __('default'), 'schoolYears'=> $schoolYears, 'id' => $id)); ?>
        </div>

        <?php if ($sf_request->hasError('schoolYear'.$id)): ?>
            <p class="validation-error" style="margin-left:192px;">&uarr; <?php echo $sf_request->getError('schoolYear'.$id) ?> &uarr;</p>
        <?php endif ?>

<?php if ($sf_user->getCulture() != 'fr'): ?>
        <?php include_partial('text_field', array('field_name' => 'studentFriends' . $id, 'field_label' => __('Amics que vindran al casal:'), 'obligatori' => false, 'class'=>'prova','class_input'=>'widthFriends'));?>
<?php endif ?>
        
        <div class="student-disability">
			<label class="prova" for="isStudentDisability<?php echo $id?>">*<?php echo __('Té alguna discapacitat') ?>:</label>
			<?php echo radiobutton_tag("isStudentDisability$id", 1, false) ?><span><?php echo __('Sí') ?></span>
        	<?php echo radiobutton_tag("isStudentDisability$id", 0, true) ?><span><?php echo __('No') ?></span>
        	<div id="box-student-disability-<?php echo $id ?>" style="margin-left:10px;visibility:hidden">
        		<div style="float:left">
	        		<label for="studentDisabilityLevel<?php echo $id ?>" class="auto"><?php echo __('Grau' ) ?>:</label>
	        		<?php echo input_tag("studentDisabilityLevel$id", null, array('size' => '10')) ?> <span>&nbsp;%</span>
	        		<?php if ($sf_request->hasError('studentDisabilityLevel' . $id)): ?>
	        		<p class="validation-error" style="clear:both; margin-left:59px" >&uarr; <?php echo $sf_request->getError('studentDisabilityLevel' . $id) ?> &uarr;</p>
	        		<?php endif ?>
        		</div>
        		<div style="float:left">
	        		<label for="studentDisabilityComment<?php echo $id ?>" class="auto" style="margin-left:15px"><?php echo __('Quina' ) ?>:</label>
	        		<?php echo input_tag("studentDisabilityComment$id", null, array('size' => '52')) ?>
	        		<?php if ($sf_request->hasError('studentDisabilityComment' . $id)): ?>
	        		<p class="validation-error" style="clear:both; margin-left:59px" >&uarr; <?php echo $sf_request->getError('studentDisabilityComment' . $id) ?> &uarr;</p>
	        		<?php endif ?>
        		</div>
        	</div>
		</div>
<?php if ($sf_user->getCulture() != 'fr'): ?>
        <div class="student-card">
			<label for="studentNumTarjetaSanitaria<?php echo $id ?>"><?php echo '*' . __('Núm. targeta sanitària') ?>:</label>
			<input type="text" size="20" id="studentNumTarjetaSanitaria<?php echo $id ?>" name="studentNumTarjetaSanitaria<?php echo $id ?>" style="margin-right:10px"></input>
			<label for="studentTarjetaSanitariaCompanyia<?php echo $id ?>" class="companyia"><?php echo '*' . __('Companyia') ?>:</label>
			<input type="text" id="studentTarjetaSanitariaCompanyia<?php echo $id ?>" name="studentTarjetaSanitariaCompanyia<?php echo $id ?>" size="30"></input>
		</div>
<?php endif ?>
        <div>
            <?php if ($sf_request->hasError('studentNumTarjetaSanitaria'.$id)): ?>
                <p class="validation-error" style="margin-left: 192px; float: left">&uarr; <?php echo $sf_request->getError('studentNumTarjetaSanitaria'.$id) ?> &uarr;</p>
            <?php endif ?>

            <?php if ($sf_request->hasError('studentTarjetaSanitariaCompanyia'.$id)): ?>
                <p class="validation-error" style="margin-left: 440px;">&uarr; <?php echo $sf_request->getError('studentTarjetaSanitariaCompanyia'.$id) ?> &uarr;</p>
            <?php endif ?>
        </div>

        <p style="clear: both"><?php echo __('Indiqueu si l\'infant té al·lèrgies, intoleràncies o malalties cròniques:') ?></p>
        <div><?php echo radiobutton_tag('studentAllergies' . $id, 'false', true) ?><span><?php echo __('No')?></span></div>
        <?php echo radiobutton_tag('studentAllergies' .$id, 'true', false) ?><span><?php echo __('Sí, indiqueu quines:')?></span>
        
        <?php echo input_tag('studentAllergiesDescription' . $id, null, array('class' => 'prova','style'=>'width:722px;')) ?>
        <?php if ($sf_request->hasError('studentAllergiesDescription' . $id)): ?>
        <p class="validation-error" >&uarr; <?php echo $sf_request->getError('studentAllergiesDescription' . $id) ?> &uarr;</p>
        <?php endif ?>
        
        <p><?php echo __('Altres aspectes a tenir en compte') ?>:</p>
        <?php echo textarea_tag('studentComments' . $id, '',  array('class' => 'textAreaDisability', 'maxlength' => 500)) ?>

        <p>*<?php echo __('registration.trans237') ?>:</p>
        <div><?php echo radiobutton_tag('studentIsVaccinated' . $id, 1, true) ?><span><?php echo __('Sí')?></span></div>
        <?php echo radiobutton_tag('studentIsVaccinated' . $id, 0, false) ?><span><?php echo __('No')?></span>
        
    
            <div class="vaccination" style="display: none">
                <label>
                    Upload vaccination file:
                </label>
                <input type="file" name="studentVaccinationFile<?php echo $id ?>" id="studentVaccinationFile<?php echo $id ?>"/>
            </div>
        
        <p><?php echo __('registration.trans238') ?></p>

    </div>

    <div class="parents clear">

      <?php if  ($id == 1){ ?>

            <?php include_partial('fathers_fields' ,array ('id'=>$id,'show'=>1)) ?>


      <?php }else { ?>

                <div class="different_parents">
                    <?php echo checkbox_tag('student'.$id .'DifferentParents', 1, false) ?>
                    <p><?php echo __('Activeu en cas que les dades dels pares o tutors siguin diferents que el primer fill/a.') ?></p>
                </div>

                <?php include_partial('fathers_fields' ,array ('id'=>$id, 'show'=>$sf_user->getAttribute('differentParents'.$id))) ?>


      <?php } ?>




    </div>
</div>

<?php echo input_hidden_tag("studentPhoto$id", $photo) ?>