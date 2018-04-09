<?php

/**
 * static actions.
 *
 * @package    el_divendres
 * @subpackage static
 * @author     Thaira SL
 * @version    SVN: $Id: actions.class.php 2692 2006-11-15 21:03:55Z fabien $
 */
require_once('lib/util.class.php');

require_once(dirname(__FILE__).'/../../../../../plugins/sfTCPDFPlugin/lib/sfTCPDF.class.php');

class inscriptionActions extends sfActions
{
	public function executeInscriptionStep1()
	{
        /*
        $this->centers = SummerFunCenterPeer::getCentersIsRegistrationOpen();
        $this->provincias = ProvinciaPeer::doSelectAll();
        $this->origin_centers = KidsAndUsCenterPeer::doSelectAll();
        */

        if ($this->getRequest()->getMethod() != sfRequest::POST){
            if (!isset($this->payment)) $this->payment=0;
            if (!isset($this->fraccionar)) $this->fraccionar=0;

            $this->getUser()->setAttribute('step2',0);
            $this->getUser()->setAttribute('confirm',0);

            $this->show1=1;
            $this->show2=$this->show3=$this->show4=$this->show5=$this->show6=0;
            $this->error=0;

            return sfView::SUCCESS;
        } else {

            $this->getDifferentsParents();
            
            $this->inscripcions = $this->getRequestParameter('inscripcions');
            $this->center = $this->getRequestParameter('center');

            $this->center2 = SummerFunCenterPeer::doSelectOneById($this->center);
                
            $this->center_procedencia = SummerFunCenterPeer::doSelectOneById($this->center);

            $this->payment = $this->getRequestParameter('payment', 0);
            $this->fraccionar = $this->getRequestParameter('fraccionar', 0);
            $this->privacyPolicy = $this->getRequestParameter('privacyPolicy');

            $this->courses = CoursePeer::getCourseByCenter($this->center);
            $this->carregaDadesInscripció($this->inscripcions, $this->provincias);

            $this->getUser()->setAttribute('step2', 1);
            $this->show1 = 1;
            $this->show2 = $this->show3 = $this->show4 = $this->show5 = $this->show6 = 0;
            $this->error = 1;

            if ($this->getUser()->getAttribute('confirm') == 1)
            {
                $culture = $this->getUser()->getCulture();
                list($pdf, $mailsEnviar) = $this->saveInscriptions();

                // Carlos. 13/03/15. Integración TPV. Recuperamos la primera inscripción para ver la forma de pago.
                $firstInscription = InscriptionPeer::retrieveByPK($pdf[1][1]);

                if ($firstInscription->getMethodPayment() == InscriptionPeer::METHOD_PAYMENT_TPV) {
                    $this->redirect("@tpv_launch_payment?inscriptionId=" . $firstInscription->getId());
                }

                list($pdfGenerated, $idCentre) = util::generarPdf($pdf);
                util::enviarPdf($pdfGenerated, $mailsEnviar, $idCentre);
                $this->redirect("@inscription_confirm_$culture");
            }
            else {
               return sfView::SUCCESS;
            }
        }
    }


    public function handleErrorInscriptionStep1()
	{
        $this->error = 1;

        $this->getDifferentsParents();

        if (!isset($this->payment)) $this->payment=0;
        if (!isset($this->fraccionar)) $this->fraccionar=0;

        $this->center = $this->getRequestParameter('center');
        $this->courses = CoursePeer::getCourseByCenter($this->center);

        $inscripcions = $this->getRequestParameter('inscripcions');
        $this->getVisibilityInscriptions($inscripcions);

        $this->centers = SummerFunCenterPeer::getCentersIsRegistrationOpen();

        return sfView::SUCCESS;
	}

	public function executeError404()
	{

	}

    public function validateInscriptionStep1()
    {
        $this->centers = SummerFunCenterPeer::getCentersIsRegistrationOpen();
        $this->schoolYears = SchoolYearPeer::doSelectAllByI18n();
        $this->kidsAndUsCenters = KidsAndUsCenterPeer::doSelectAll();
        $this->summerFunCenters = SummerFunCenterPeer::doSelectAllByI18n();

        $this->provincias = ProvinciaPeer::doSelectAll();



        if ($this->getRequest()->getMethod() != sfRequest::POST) {
            return sfView::SUCCESS;
        }
        else {
            $plazasDisponibles = true;
            $this->inscripcions = $this->getRequestParameter('inscripcions');
            $this->center = $this->getRequestParameter('center');

            /** @var SummerFunCenter $summerFunCenter */
            $this->center2 = SummerFunCenterPeer::doSelectOneById($this->center);
            $this->center_procedencia = SummerFunCenterPeer::doSelectOneById($this->center);

            $this->payment = $this->getRequestParameter('payment', 0);
            $this->fraccionar = $this->getRequestParameter('fraccionar', 0);
            $this->privacyPolicy = $this->getRequestParameter('privacyPolicy');

            $this->courses = CoursePeer::getCourseByCenter($this->center);
            $this->carregaDadesInscripció($this->inscripcions, $this->provincias);

            if (!$this->center) {
                $this->getRequest()->setError('centre', $this->getContext()->getI18N()->__('Seleccioneu un centre'));
            }

            if ($this->getUser()->getCulture() == 'fr') {
                if ($this->certificated && !$this->certificatedName) {
                    $this->getRequest()->setError('certificatedName', $this->getContext()->getI18N()->__('Camp Obligatori'));
                }
            }

            $this->totalInscripcio = 0;
            for ($i = 1; $i <= $this->inscripcions + 1; $i++) {

                $this->{'amountServices' . $i} = 0;

                $fields = array("studentName$i", "studentPrimerApellido$i", "studentBirthDate$i", "studentZip$i", "studentCity$i", "studentAddress$i", "schoolYear$i", "studentPhoto$i");

                if ($this->getUser()->getCulture() != 'fr') {
                    $fields[] = 'studentSegundoApellido' . $i;
                    $fields[] = 'studentProvincia' . $i;
                }

                foreach ($fields as $field) {
                    if ($this->$field == null) {
                        $this->getRequest()->setError($field, $this->getContext()->getI18N()->__('Camp Obligatori'));
                    }
                }

                if ($this->{"isStudentDisability$i"} == 1) {
                    if (empty($this->{"studentDisabilityLevel$i"})) {
                        $this->getRequest()->setError("studentDisabilityLevel$i", $this->getContext()->getI18N()->__('Camp Obligatori'));
                    }
                    if (empty($this->{"studentDisabilityComment$i"})) {
                        $this->getRequest()->setError("studentDisabilityComment$i", $this->getContext()->getI18N()->__('Camp Obligatori'));
                    }
                }

                if (empty($this->{"studentNumTarjetaSanitaria$i"}) && $this->getUser()->getCulture() != 'fr') {
                    $this->getRequest()->setError("studentNumTarjetaSanitaria$i", $this->getContext()->getI18N()->__('Camp Obligatori'));
                }

                if (empty($this->{"studentTarjetaSanitariaCompanyia$i"}) && $this->getUser()->getCulture() != 'fr') {
                    $this->getRequest()->setError("studentTarjetaSanitariaCompanyia$i", $this->getContext()->getI18N()->__('Camp Obligatori'));
                }

                if ($this->{'isStudentKidAndUs' . $i} == '1')
                {
                    if ($this->{'kidsAndUsCenter' . $i} == null) {
                        $this->getRequest()->setError('kidsAndUsCenter' . $i, $this->getContext()->getI18N()->__('Camp Obligatori'));
                    }

                    if (($this->{"studentSchoolYear$i"} == null || $this->{"studentSchoolYear$i"} == '0') && $this->{"studentSchoolYearOther$i"} == null) {
                        $this->getRequest()->setError("studentSchoolYear$i", $this->getContext()->getI18N()->__('registration.trans228'));
                    }
                }

                if ($this->{"summerFunCenterOther$i"} == null) {
                    if ($this->{"summerFunCenter$i"} == null || $this->{"summerFunCenter$i"} == -1) {
                        $this->getRequest()->setError('summerFunCenterOther' . $i, $this->getContext()->getI18N()->__('summer_fun_center_other_error'));
                    }
                }
                

                $cultureOld = $this->getUser()->getCulture();
                $this->getUser()->setCulture('es');

                $validadorDataCorrecte = new sfDateValidator();
                $validadorDataCorrecte->initialize($this->getContext());

                if (!$validadorDataCorrecte->execute($this->{'studentBirthDate' . $i}, $this->{'studentBirthDate' . $i}))
                {
                    $this->getUser()->setCulture($cultureOld);
                    $this->getRequest()->setError('studentBirthDate' . $i, $this->getContext()->getI18N()->__('La data no és correcta'));
                }

                $this->getUser()->setCulture($cultureOld);

                if (isset($this->nombreSetmanes))
                {
                    $trobat = 0;
                    $this->{'subTotalInscripcioAlumne' . $i} = 0;

                    for ($j = 1; $j <= $this->nombreSetmanes; $j++)
                    {
                        if (isset($this->{'week' . $j . 'alumne' . $i}))
                        {
                            $trobat = 1;

                            /** @var Course $course */
                            $course = CoursePeer::retrieveByPKWithI18n($this->{'week' . $j . 'alumne' . $i});

                            if ($course->getExcursion() && $this->center2->getShowExcursionWidget()) {
                                if (!isset($this->{"studentExcursion$i"}) || ($this->{"studentExcursion$i"} != 0 && $this->{"studentExcursion$i"} != 1)) {
                                    $this->getRequest()->setError("studentExcursion$i", $this->getContext()->getI18N()->__('Camp Obligatori'));
                                }
                            }

                            if ($this->center2->getCustomQuestion()) {
                                if (!isset($this->{"studentCustomQuestion$i"}) || ($this->{"studentCustomQuestion$i"} != 0 && $this->{"studentCustomQuestion$i"} != 1)) {
                                    $this->getRequest()->setError("studentCustomQuestion$i", $this->getContext()->getI18N()->__('Camp Obligatori'));
                                }
                            }
                            //valida si hay plazas


                            if (isset($this->{'placesDisponiblesWeek' . $j . 'alumne' . $i})) {
                                $this->{'subTotalInscripcioAlumne' . $i} = $course->getPrice() + $this->{'subTotalInscripcioAlumne' . $i};
                            }
                            else {
                                $this->{'subTotalInscripcioAlumne' . $i} += 0;
                            }

                            $services = array();
                            foreach ($course->getCourseHasServicess() as $courseService) {
                                $service = $courseService->getService();

                                foreach ($service->getServiceSchedules() as $schedule) {
                                    if (isset($this->{'week' . $j . 'student' . $i . 'service' . $service->getId() . 'schedule' . $schedule->getId()}) && !in_array($service->getId(), $services)) {

                                        if (isset($this->{'placesDisponiblesWeek' . $j . 'alumne' . $i})) {
                                            $this->{'amountServices' . $i} += $service->getPrice();
                                            array_push($services, $service->getId());
                                        }
                                        else {
                                            $this->{'amountServices' . $i} += 0;
                                        }
                                    }
                                }
                            }

                            if (!isset($this->{'placesDisponiblesWeek' . $j . 'alumne' . $i})) {
                                $plazasDisponibles = false;
                            }
                        }
                    }

                    if (!$trobat) {
                        $this->getRequest()->setError('errorSetmanaStudent' . $i, $this->getContext()->getI18N()->__('Selecciona una setmana'));
                        $this->logMessage('error', 'debug');
                    }
                }

                if (!isset($this->privacyPolicy)) {
                    $this->getRequest()->setError('privacyPolicy', $this->getContext()->getI18N()->__('Ha d\'acceptar la política de privacitat'));
                }

                if ($i == 1 || isset($this->{'student' . $i . 'DifferentParents'})) {

                    foreach (array('fatherName' . $i, 'fatherPrimerApellido' . $i, 'fatherDni' . $i, 'fatherPhone' . $i) as $field) {

                        if ($this->$field == null) {
                            $this->getRequest()->setError($field, $this->getContext()->getI18N()->__('Camp Obligatori'));
                        }

                        $validadorMailCorrecte = new sfEmailValidator();
                        $validadorMailCorrecte->initialize($this->getContext(), array('strict' => true));

                        if (!$validadorMailCorrecte->execute($this->{'fatherMail' . $i}, $this->{'fatherMail' . $i})) {
                            $this->getRequest()->setError('fatherMail' . $i, $this->getContext()->getI18N()->__('L\'Email no és correcte'));
                        }

                        $validadorMailsIguals = new sfCompareValidator();
                        $validadorMailsIguals->initialize($this->getContext(), array('check' => 'fatherMail' . $i));

                        if (!$validadorMailsIguals->execute($this->{'fatherMail' . $i . 'Validation'}, $this->{'fatherMail' . $i . 'Validation'})) {

                            $this->getRequest()->setError('fatherMail' . $i . 'Validation', $this->getContext()->getI18N()->__('Els Emails no són iguals'));
                        }

                        if ((!isset($this->{'fatherMail' . $i . 'Principal'}))) {
                            $this->getRequest()->setError('mailPrincipal' . $i, $this->getContext()->getI18N()->__('S\'ha de seleccionar l\'e-mail com a principal'));
                        }

                        if ($this->{'motherMail' . $i} != '') {
                            if (!$validadorMailCorrecte->execute($this->{'motherMail' . $i}, $this->{'motherMail' . $i})) {
                                $this->getRequest()->setError('motherMail' . $i, $this->getContext()->getI18N()->__('L\'Email no és correcte'));
                            }

                            $validadorMailsIguals->initialize($this->getContext(), array('check' => 'motherMail' . $i));

                            if (!$validadorMailsIguals->execute($this->{'motherMail' . $i . 'Validation'}, $this->{'motherMail' . $i . 'Validation'})) {

                                $this->getRequest()->setError('motherMail' . $i . 'Validation', $this->getContext()->getI18N()->__('Els Emails no són iguals'));
                            }

                        }
                    }
                }
            }

            if (!$this->getRequest()->hasErrors() && $plazasDisponibles && isset($this->nombreSetmanes))
            {
                $this->calculateDiscounts(true);
            }

            if (!$plazasDisponibles && $this->payment == InscriptionPeer::METHOD_PAYMENT_TPV)
            {
                $this->getRequest()->setError("payment", $this->getContext()->getI18N()->__('Forma de pagament no acceptada per a inscripcions en llista espera'));
            }

            return !$this->getRequest()->hasErrors();
        }
    }

    public function executeSetmanesCentre()
    {
        sfLoader::loadHelpers('Partial');

        $idCentre = $this->getRequestParameter('idCentre');
        $this->id = $this->getRequestParameter('id');

        $courses = CoursePeer::getCourseByCenter($idCentre);
        $centre = SummerFunCenterPeer::doSelectOneById($idCentre);

        $this->setVar('courses', $courses);
        $this->setVar('centre', $centre);

    	return $this->renderText(get_partial('weeks', array('courses' => $courses, 'id' => $this->id, 'centre' => $centre)));
    }

    public function executeInscriptionStep2Confirm()
    {
        $this->getUser()->setAttribute('confirm',1);

        return sfView::NONE;
    }

    public function executeChangeViewStudents()
    {
        $inscripcions = $this->getRequestParameter('inscriptions');

        switch($inscripcions)
        {
            case 1:

                $this->getUser()->setAttribute('show1',1);
                $this->getUser()->setAttribute('show2',0);
                $this->getUser()->setAttribute('show3',0);
                $this->getUser()->setAttribute('show4',0);
                $this->getUser()->setAttribute('show5',0);
                $this->getUser()->setAttribute('show6',0);
                $this->getUser()->setAttribute('inscriptions',1);

                break;
            case 2:
                $this->getUser()->setAttribute('show1',1);
                $this->getUser()->setAttribute('show2',1);
                $this->getUser()->setAttribute('show3',0);
                $this->getUser()->setAttribute('show4',0);
                $this->getUser()->setAttribute('show5',0);
                $this->getUser()->setAttribute('show6',0);
                $this->getUser()->setAttribute('inscriptions',2);

                break;
            case 3:
                $this->getUser()->setAttribute('show1',1);
                $this->getUser()->setAttribute('show2',1);
                $this->getUser()->setAttribute('show3',1);
                $this->getUser()->setAttribute('show4',0);
                $this->getUser()->setAttribute('show5',0);
                $this->getUser()->setAttribute('show6',0);
                $this->getUser()->setAttribute('inscriptions',3);

                break;
            case 4:
                $this->getUser()->setAttribute('show1',1);
                $this->getUser()->setAttribute('show2',1);
                $this->getUser()->setAttribute('show3',1);
                $this->getUser()->setAttribute('show4',1);
                $this->getUser()->setAttribute('show5',0);
                $this->getUser()->setAttribute('show6',0);
                $this->getUser()->setAttribute('inscriptions',4);

                break;
            case 5:
                $this->getUser()->setAttribute('show1',1);
                $this->getUser()->setAttribute('show2',1);
                $this->getUser()->setAttribute('show3',1);
                $this->getUser()->setAttribute('show4',1);
                $this->getUser()->setAttribute('show5',1);
                $this->getUser()->setAttribute('show6',0);
                $this->getUser()->setAttribute('inscriptions',5);

            case 6:
                $this->getUser()->setAttribute('show1',1);
                $this->getUser()->setAttribute('show2',1);
                $this->getUser()->setAttribute('show3',1);
                $this->getUser()->setAttribute('show4',1);
                $this->getUser()->setAttribute('show5',1);
                $this->getUser()->setAttribute('show6',1);
                $this->getUser()->setAttribute('inscriptions',6);
        }

        return sfView::NONE;

    }

    private function carregaDadesInscripció($inscripcions, $provincias = array())
    {
        $this->nombreSetmanes = $this->getRequestParameter('nombreSetmanes');

        if ($this->getUser()->getCulture() == 'fr')
        {
            $this->certificated = $this->getRequestParameter('certificated');
            $this->certificatedName = $this->getRequestParameter('certificatedName', null);
        }

        for ($i = 1; $i <= $inscripcions + 1; $i++)
        {
            $this->{'studentName'.$i} = $this->getRequestParameter('studentName' .$i);
            $this->{'studentPrimerApellido'.$i} = $this->getRequestParameter('studentPrimerApellido' .$i);
            $this->{'studentSegundoApellido'.$i} = $this->getRequestParameter('studentSegundoApellido' .$i);
            $this->{'studentBirthDate'.$i} = $this->getRequestParameter('studentBirthDate' .$i);
            $this->{'studentAddress'.$i} = $this->getRequestParameter('studentAddress' .$i);
            $this->{'studentZip'.$i} = $this->getRequestParameter('studentZip' .$i);
            $this->{'studentCity'.$i} = $this->getRequestParameter('studentCity' .$i);
            $this->{'studentProvinciaName'.$i} = $this->getNombreProvincia($provincias, $this->getRequestParameter('studentProvincia' .$i));
            $this->{'studentProvincia'.$i} = $this->getRequestParameter('studentProvincia' .$i);
            $this->{'kidsAndUsCenter'.$i} = $this->getRequestParameter('kidsAndUsCenter' .$i);
            $this->{'summerFunCenter'.$i} = $this->getRequestParameter('summerFunCenter' .$i);
            $this->{'summerFunCenterOther'.$i} = $this->getRequestParameter('summerFunCenterOther' .$i);
            $this->{'studentSchoolYear'.$i} = $this->getRequestParameter('studentSchoolYear' .$i);
            $this->{'studentSchoolYearOther'.$i} = $this->getRequestParameter('studentSchoolYearOther' .$i);
            $this->{'studentFriends'.$i} = $this->getRequestParameter('studentFriends' .$i);
            $this->{'studentAllergies'.$i} = $this->getRequestParameter('studentAllergies' .$i);
            $this->{'studentAllergiesDescription'.$i} = $this->getRequestParameter('studentAllergiesDescription' .$i);
            $this->{'isStudentBeca' . $i} = $this->getRequestParameter('isStudentBeca');
            $this->{"schoolYear$i"} = $this->getRequestParameter("schoolYear$i");

            for ($j = 1; $j <= $this->nombreSetmanes; $j++)
            {
                $this->{'week' . $j . 'alumne' . $i} = $this->getRequestParameter('week'. $j . 'alumne' . $i);
                $this->{'preuSetmana' . $j} = $this->getRequestParameter('preuSetmana' . $j);
                $this->{'placesDisponiblesWeek' . $j . 'alumne' . $i} = $this->getRequestParameter('placesDisponiblesWeek' . $j . 'alumne' . $i);
                $this->{'week' . $j . 'shelterMorning' . $i} = $this->getRequestParameter('week' . $j . 'shelterMorning' . $i);
                $this->{'week' . $j . 'shelterAfternoon' . $i} = $this->getRequestParameter('week' . $j . 'shelterAfternoon' . $i);

                foreach ($this->courses as $course)
                {
                    if ($course->getId() == $this->{'week' . $j . 'alumne' . $i})
                    {
                        foreach ($course->getCourseHasServicess() as $courseService)
                        {
                            $service = $courseService->getService();
                            if (isset($service)) {
                                foreach ($service->getServiceSchedules() as $schedule) {
                                    $this->{'week' . $j . 'student' . $i . 'service' . $service->getId() . 'schedule' . $schedule->getId()} = $this->getRequestParameter('week' . $j . 'student' . $i . 'service' . $service->getId() . 'schedule' . $schedule->getId());
                                }
                            }
                        }
                    }
                }
            }

            if ($i != 1) {
                $this->{'student'.$i.'DifferentParents'} = $this->getRequestParameter('student'.$i.'DifferentParents');
            }

            $this->{'fatherName'.$i} = $this->getRequestParameter('fatherName' .$i);
            $this->{'fatherPrimerApellido'.$i} = $this->getRequestParameter('fatherPrimerApellido' .$i);
            $this->{'fatherSegundoApellido'.$i} = $this->getRequestParameter('fatherSegundoApellido' .$i);
            $this->{'fatherPhone'.$i} = $this->getRequestParameter('fatherPhone' .$i);
            $this->{'fatherDni'.$i} = $this->getRequestParameter('fatherDni' .$i);
            $this->{'fatherMail'.$i} = $this->getRequestParameter('fatherMail' .$i);
            $this->{'fatherMail'.$i .'Validation'} = $this->getRequestParameter('fatherMail' .$i .'Validation');
            $this->{'fatherMail'.$i .'Principal'} = $this->getRequestParameter('fatherMail' .$i .'Principal');
            $this->{'motherName'.$i} = $this->getRequestParameter('motherName' .$i);
            $this->{'motherPrimerApellido'.$i} = $this->getRequestParameter('motherPrimerApellido' .$i);
            $this->{'motherSegundoApellido'.$i} = $this->getRequestParameter('motherSegundoApellido' .$i);
            $this->{'motherPhone'.$i} = $this->getRequestParameter('motherPhone' .$i);
            $this->{'motherDni'.$i} = $this->getRequestParameter('motherDni' .$i);
            $this->{'motherMail'.$i} = $this->getRequestParameter('motherMail' .$i);
            $this->{'motherMail'.$i .'Validation'} = $this->getRequestParameter('motherMail' .$i .'Validation');
            $this->{'motherMail'.$i .'Principal'} = $this->getRequestParameter('motherMail' .$i .'Principal');
            $this->{'studentNumTarjetaSanitaria'. $i} = $this->getRequestParameter('studentNumTarjetaSanitaria' . $i);
            $this->{'studentTarjetaSanitariaCompanyia'. $i} = $this->getRequestParameter('studentTarjetaSanitariaCompanyia' . $i);
            $this->{'isStudentKidAndUs'. $i} = $this->getRequestParameter('isStudentKidAndUs' . $i);
            $this->{'isStudentDisability'. $i} = $this->getRequestParameter('isStudentDisability' . $i);
            $this->{'studentDisabilityLevel'. $i} = $this->getRequestParameter('studentDisabilityLevel' . $i);
            $this->{'studentDisabilityComment'. $i} = $this->getRequestParameter('studentDisabilityComment' . $i);
            $this->{'studentComments'. $i} = $this->getRequestParameter('studentComments' . $i);
            $this->{'studentExcursion'. $i} = $this->getRequestParameter('studentExcursion' . $i);
            $this->{'studentPhoto'. $i} = $this->getRequestParameter('studentPhoto' . $i);
            $this->{'studentCustomQuestion'. $i} = $this->getRequestParameter('studentCustomQuestion' . $i);
            $this->{'studentIsVaccinated'.$i} = $this->getRequestParameter('studentIsVaccinated' . $i);
            if(isset($_FILES['studentVaccinationFile'.$i])){
                $uploadDir = sfConfig::get('app_inscripcion_imagen_directorio');
                $filename = basename($_FILES['studentVaccinationFile'.$i]['name']);
                $fichero_subido = $uploadDir . $filename;
                if (move_uploaded_file($_FILES['studentVaccinationFile'.$i]['tmp_name'], $fichero_subido)) {
                    $_SESSION['studentVaccinationFile'.$i] = $filename;
                    $this->{'studentVaccinationFile'.$i} = $filename;
                } else {
                    $this->{'studentVaccinationFile'.$i} = '';
                }
                
            }else{
                $this->{'studentVaccinationFile'.$i} = '';
            }
        }
    }

    private function getVisibilityInscriptions($inscripcions)
    {
        switch($inscripcions+1){
            case 1:
                $this->show1=1;
                $this->show2=$this->show3=$this->show4=$this->show5=$this->show6=0;
                break;
            case 2:
                $this->show2=$this->show1=1;
                $this->show3=$this->show4=$this->show5=$this->show6=0;
                break;
            case 3:
                $this->show3=$this->show2=$this->show1=1;
                $this->show4=$this->show5=$this->show6=0;
                break;
            case 4:
                $this->show2=$this->show1=$this->show3=$this->show4=1;
                $this->show5=$this->show6=0;
                break;
            case 5:
                $this->show2=$this->show1=$this->show3=$this->show4=$this->show5=1;
                $this->show6=0;
            case 6:
                $this->show2=$this->show1=$this->show3=$this->show4=$this->show5=$this->show6=1;
        }
    }

    private function getDifferentsParents()
    {
        for ($i = 2; $i < 7; $i++)
        {
            ${'student'.$i.'DifferentParents'} = $this->getRequestParameter('student'.$i.'DifferentParents');

            if (isset(${'student'.$i.'DifferentParents'}) ){
                $this->getUser()->setAttribute('differentParents'.$i,1);
            }
            else {
                $this->getUser()->setAttribute('differentParents'.$i,0);
            }
        }
    }

    public function executeInscriptionConfirm()
    {
        $this->getUser()->getAttributeHolder()->remove('differentParents1');
        $this->getUser()->getAttributeHolder()->remove('differentParents2');
        $this->getUser()->getAttributeHolder()->remove('differentParents3');
        $this->getUser()->getAttributeHolder()->remove('differentParents4');
        $this->getUser()->getAttributeHolder()->remove('differentParents5');
        $this->getUser()->getAttributeHolder()->remove('differentParents6');
        $this->getUser()->getAttributeHolder()->remove('step2');
        $this->getUser()->getAttributeHolder()->remove('inscriptions1');
        $this->getUser()->getAttributeHolder()->remove('inscriptions2');
        $this->getUser()->getAttributeHolder()->remove('inscriptions3');
        $this->getUser()->getAttributeHolder()->remove('inscriptions4');
        $this->getUser()->getAttributeHolder()->remove('inscriptions5');
        $this->getUser()->getAttributeHolder()->remove('inscriptions6');
        $this->getUser()->getAttributeHolder()->remove('show1');
        $this->getUser()->getAttributeHolder()->remove('show2');
        $this->getUser()->getAttributeHolder()->remove('show3');
        $this->getUser()->getAttributeHolder()->remove('show4');
        $this->getUser()->getAttributeHolder()->remove('show5');
        $this->getUser()->getAttributeHolder()->remove('show6');
        $this->getUser()->getAttributeHolder()->remove('confirm');
        $this->getUser()->getAttributeHolder()->remove('inscriptions');
    }

    public function executeServeiAcollidaCenter()
    {
        sfLoader::loadHelpers('Partial');
        $idCentre = $this->getRequestParameter('idCentre');
        $this->id = $this->getRequestParameter('id');
        $centre = SummerFunCenterPeer::retrieveByPKWithI18n($idCentre);
        return $this->renderText(get_partial('servei_acollida',array('center' => $centre,'id'=>$this->id)));
    }

    public function executeModalitatPagament()
    {
        sfLoader::loadHelpers('Partial');
        $idCentre = $this->getRequestParameter('idCentre');
        $centre = SummerFunCenterPeer::retrieveByPKWithI18n($idCentre);
        return $this->renderText(get_partial('modalitat_pagament',array('center' => $centre)));
    }

    private function saveInscriptions()
    {

        $numInscripcion = InscriptionPeer::getLastNumInscription();
        $numInscripcion++;

        $this->calculateDiscounts();

        for ($i = 1; $i <= $this->inscripcions + 1; $i++)
        {
            if (isset($this->nombreSetmanes))
            {
                $codeInscription = InscriptionPeer::getLastCodeInscription(); //busquem l'ultim nº d'inscripció

                $anyo = date('y');

                if ($codeInscription == 0 || strlen($codeInscription) < 2 || substr($codeInscription, 0, 2) != $anyo) {
                    $codeInscription = $anyo . "0000";
                }

                $x = 0;
                for ($j = 1; $j <= $this->nombreSetmanes; $j++)
                {
                    if (isset($this->{'week' . $j . 'alumne' . $i}))
                    {
                        $course = CoursePeer::retrieveByPKWithI18n($this->{'week' . $j . 'alumne' . $i});
                        if (!$course) {
                            continue;
                        }

                        $x++;
                        $inscripcio = new Inscription();
                        $inscripcio->setStudentCourseInscription($this->{'week' . $j . 'alumne' . $i});


                        // Gestionamos la foto
                        if (!empty($this->{'studentPhoto' . $i})) {
                            $imageName = $this->{'studentPhoto' . $i};
                            $uploadDirTmp = sfConfig::get('app_inscripcion_imagen_directorio_temporal');
                            $uploadDir = sfConfig::get('app_inscripcion_imagen_directorio');
                            if (file_exists($uploadDirTmp . $imageName)) {
                                if (!file_exists($uploadDir . $imageName)) {
                                    rename($uploadDirTmp . $imageName, $uploadDir . $imageName);
                                }
                            }

                            $inscripcio->setStudentPhoto($imageName);
                        }

                        $finalPrice = $course->getPrice();


                        $state = isset($this->{'placesDisponiblesWeek' . $j . 'alumne' . $i}) ? 0 : 1;
                        $inscripcio->setState($state);

                        if ($state == 0)
                        {
                            if ($this->{'discountPercentRegistrationStudent' . $i} > 0) {
                                $discount = $this->{'discountPercentRegistrationStudent' . $i};
                                $inscripcio->setDiscountPercent($discount);
                                $inscripcio->setDiscountAmount(0);
                                $discount = $finalPrice * ($discount / 100);
                                $finalPrice -= $discount;
                                $inscripcio->setDiscount($discount);
                            }

                            if ($this->{'discountAmountRegistrationStudent' . $i} > 0) {
                                $discount =$this->{'discountAmountRegistrationStudent' . $i};
                                $inscripcio->setDiscountAmount($discount);
                                $inscripcio->setDiscountPercent(0);
                                $finalPrice -= $discount;
                                $inscripcio->setDiscount($discount);
                            }
                        }

                        $services = array();
                        foreach ($course->getCourseHasServicess() as $courseService)
                        {
                            $service = $courseService->getService();
                            foreach ($service->getServiceSchedules() as $schedule)
                            {
                                if (isset($this->{'week' . $j . 'student' . $i . 'service' . $service->getId() . 'schedule' . $schedule->getId()})) {
                                    if ($service->getPrice() > 0 && !in_array($service->getId(), $services)) {
                                        $finalPrice += $service->getPrice();
                                        array_push($services, $service->getId());
                                    }

                                    $inscriptionServiceSchedule = new InscriptionServiceSchedule();
                                    $inscriptionServiceSchedule->setInscription($inscripcio);
                                    $inscriptionServiceSchedule->setServiceScheduleId($schedule->getId());

                                    $inscriptionServiceSchedule->save();
                                }
                            }
                        }

                        $inscripcio->setPrice($finalPrice);

                        $inscripcio->setStudentName($this->{'studentName' . $i});
                        $inscripcio->setStudentPrimerApellido($this->{'studentPrimerApellido' . $i});
                        $inscripcio->setStudentSegundoApellido($this->{'studentSegundoApellido' . $i});

                        $fecha = DateTime::createFromFormat('d/m/Y', $this->{'studentBirthDate' . $i});
                        $inscripcio->setStudentBirthDate($fecha->format('Y-m-d'));


                        $inscripcio->setStudentAddress($this->{'studentAddress' . $i});
                        $inscripcio->setStudentZip($this->{'studentZip' . $i});
                        $inscripcio->setStudentCity($this->{'studentCity' . $i});
                        $inscripcio->setStudentProvincia($this->{'studentProvincia' . $i});

                        if ($this->{"studentSchoolYear$i"} && $this->{"studentSchoolYear$i"} != '0') {
                            $inscripcio->setStudentSchoolYear($this->{"studentSchoolYear$i"});
                        }
                        else {
                            $inscripcio->setStudentSchoolYear($this->{"studentSchoolYearOther$i"});
                        }

                        $inscripcio->setStudentFriends($this->{'studentFriends' . $i});

                        if (!empty($this->{'studentNumTarjetaSanitaria' . $i})) {
                            $inscripcio->setStudentNumTarjetaSanitaria($this->{'studentNumTarjetaSanitaria' . $i});
                            if (!empty($this->{'studentTarjetaSanitariaCompanyia' . $i})) {
                                $inscripcio->setStudentTarjetaSanitariaCompanyia($this->{'studentTarjetaSanitariaCompanyia' . $i});
                            } else {
                                $inscripcio->setStudentTarjetaSanitariaCompanyia('CATSALUT');
                            }
                        }

                        $inscripcio->setIsStudentKidAndUs($this->{'isStudentKidAndUs' . $i} == 1);
                        $inscripcio->setStudentExcursion($this->{'studentExcursion' . $i} == 1);

                        if (isset($this->{'studentCustomQuestion' . $i}) && $this->center2->getCustomQuestion()) {
                            $inscripcio->setCustomQuestion($this->center2->getCustomQuestion());
                            if ($this->{'studentCustomQuestion' . $i} == 1) {
                                $inscripcio->setCustomQuestionAnswer(true);
                            } else {
                                $inscripcio->setCustomQuestionAnswer(false);
                            }
                        }

                        if ($this->{'isStudentDisability' . $i} == 1) {
                            $inscripcio->setIsStudentDisability(true);
                            if (!empty($this->{'studentDisabilityLevel' . $i})) {
                                $inscripcio->setStudentDisabilityLevel($this->{'studentDisabilityLevel' . $i});
                            }
                            if (!empty($this->{'studentDisabilityComment' . $i})) {
                                $inscripcio->setStudentDisability($this->{'studentDisabilityComment' . $i});
                            }
                        } else {
                            $inscripcio->setIsStudentDisability(false);
                        }

                        if (!empty($this->{'studentComments' . $i})) {
                            $inscripcio->setStudentComments($this->{'studentComments' . $i});
                        }

                        if ($this->{'studentAllergies' . $i} == 'true') {

                            $inscripcio->setStudentAllergies(1);

                            if ($this->{'studentAllergiesDescription' . $i} == '') {
                                $inscripcio->setStudentAllergiesDescription('Sí');
                            } else {
                                $inscripcio->setStudentAllergiesDescription($this->{'studentAllergiesDescription' . $i});
                            }
                        }

                        $inscripcio->setBeca($this->{'isStudentBeca' . $i} == 1 ? 1 : 0);

                        $z = 0;

                        if ($i == 1) {
                            $inscripcio->setFatherName($this->{'fatherName' . $i});
                            $inscripcio->setFatherPrimerApellido($this->{'fatherPrimerApellido' . $i});
                            $inscripcio->setFatherSegundoApellido($this->{'fatherSegundoApellido' . $i});
                            $inscripcio->setFatherPhone($this->{'fatherPhone' . $i});
                            $inscripcio->setFatherDni($this->{'fatherDni' . $i});
                            $inscripcio->setFatherMail($this->{'fatherMail' . $i});
                            $mailPrincipal = isset($this->{'fatherMail' . $i . 'Principal'}) ? 1 : 0;

                            if ($mailPrincipal) {
                                $z++;
                                $mailsEnviar[$i][$z] = $this->{'fatherMail' . $i};
                            }
                            $inscripcio->setIsFatherMailMain($mailPrincipal);

                            $inscripcio->setMotherName($this->{'motherName' . $i});
                            $inscripcio->setMotherPrimerApellido($this->{'motherPrimerApellido' . $i});
                            $inscripcio->setMotherSegundoApellido($this->{'motherSegundoApellido' . $i});
                            $inscripcio->setMotherPhone($this->{'motherPhone' . $i});
                            $inscripcio->setMotherDni($this->{'motherDni' . $i});
                            $inscripcio->setMotherMail($this->{'motherMail' . $i});
                            $mailPrincipal = isset($this->{'motherMail' . $i . 'Principal'}) ? 1 : 0;

                            if ($mailPrincipal) {
                                $z++;
                                $mailsEnviar[$i][$z] = $this->{'motherMail' . $i};
                            }

                            $inscripcio->setIsMotherMailMain($mailPrincipal);
                        } else {

                            if (isset($this->{'student' . $i . 'DifferentParents'})) {
                                $inscripcio->setFatherName($this->{'fatherName' . $i});
                                $inscripcio->setFatherPrimerApellido($this->{'fatherPrimerApellido' . $i});
                                $inscripcio->setFatherSegundoApellido($this->{'fatherSegundoApellido' . $i});
                                $inscripcio->setFatherPhone($this->{'fatherPhone' . $i});
                                $inscripcio->setFatherDni($this->{'fatherDni' . $i});
                                $inscripcio->setFatherMail($this->{'fatherMail' . $i});
                                $mailPrincipal = isset($this->{'fatherMail' . $i . 'Principal'}) ? 1 : 0;

                                if ($mailPrincipal) {
                                    $z++;
                                    $mailsEnviar[$i][$z] = $this->{'fatherMail' . $i};

                                }

                                $inscripcio->setIsFatherMailMain($mailPrincipal);

                                $inscripcio->setMotherName($this->{'motherName' . $i});
                                $inscripcio->setMotherPrimerApellido($this->{'motherPrimerApellido' . $i});
                                $inscripcio->setMotherSegundoApellido($this->{'motherSegundoApellido' . $i});
                                $inscripcio->setMotherPhone($this->{'motherPhone' . $i});
                                $inscripcio->setMotherDni($this->{'motherDni' . $i});
                                $inscripcio->setMotherMail($this->{'motherMail' . $i});
                                $mailPrincipal = isset($this->{'motherMail' . $i . 'Principal'}) ? 1 : 0;

                                if ($mailPrincipal) {
                                    $z++;
                                    $mailsEnviar[$i][$z] = $this->{'motherMail' . $i};
                                }
                                $inscripcio->setIsMotherMailMain($mailPrincipal);
                            } else {
                                $inscripcio->setFatherName($this->{'fatherName1'});
                                $inscripcio->setFatherPrimerApellido($this->{'fatherPrimerApellido1'});
                                $inscripcio->setFatherSegundoApellido($this->{'fatherSegundoApellido1'});
                                $inscripcio->setFatherPhone($this->{'fatherPhone1'});
                                $inscripcio->setFatherDni($this->{'fatherDni1'});
                                $inscripcio->setFatherMail($this->{'fatherMail1'});
                                $mailPrincipal = isset($this->{'fatherMail1Principal'}) ? 1 : 0;
                                $inscripcio->setIsFatherMailMain($mailPrincipal);

                                $inscripcio->setMotherName($this->{'motherName1'});
                                $inscripcio->setMotherPrimerApellido($this->{'motherPrimerApellido1'});
                                $inscripcio->setMotherSegundoApellido($this->{'motherSegundoApellido1'});
                                $inscripcio->setMotherPhone($this->{'motherPhone1'});
                                $inscripcio->setMotherDni($this->{'motherDni1'});
                                $inscripcio->setMotherMail($this->{'motherMail1'});
                                $mailPrincipal = isset($this->{'motherMail1Principal'}) ? 1 : 0;
                                $inscripcio->setIsMotherMailMain($mailPrincipal);
                            }
                        }

                        $splitPayment = $this->fraccionar == 1 ? 1 : 0;
                        $inscripcio->setSplitPayment($splitPayment);

                        if ($this->{'kidsAndUsCenter' . $i}) {
                            $inscripcio->setKidsAndUsCenter(KidsAndUsCenterPeer::doSelectById($this->{'kidsAndUsCenter' . $i}));
                        }

                        if ($this->{'summerFunCenter' . $i} && $this->{'summerFunCenter' . $i} != -1) {
                            $inscripcio->setSummerFunCenter(SummerFunCenterPeer::doSelectOneById($this->{'summerFunCenter' . $i}));
                        }
                        else {
                            $inscripcio->setSummerFunCenterOther($this->{'summerFunCenterOther' . $i});
                        }

                        $inscripcio->setSchoolYear(SchoolYearPeer::doSelectOneById($this->{"schoolYear$i"}));

                        $inscripcio->setIsPaid(0);
                        $inscripcio->setMethodPayment($this->payment);
                        if ($inscripcio->getMethodPayment() == InscriptionPeer::METHOD_PAYMENT_TPV) {
                            $inscripcio->setIsPaid(3);
                        }
                        $inscripcio->setInscriptionCode($codeInscription + 1);
                        $inscripcio->setInscriptionNum($numInscripcion);
                        $inscripcio->setCulture($this->getUser()->getCulture());
                        $inscripcio->setIsVaccinated($this->{'studentIsVaccinated' . $i} == 1 ? 1 : 0);
                        
                        // Gestionamos el archivo
                        if(isset($_SESSION['studentVaccinationFile'.$i]) && $_SESSION['studentVaccinationFile'.$i] != ''){
                            $inscripcio->setVaccinationFile($_SESSION['studentVaccinationFile'.$i]);
                        }else{
                            $inscripcio->setVaccinationFile('');
                        }
                      
                        if ($this->getUser()->getCulture() == 'fr') {
                            $inscripcio->setCertificated($this->certificated);
                            $inscripcio->setCertificatedname($this->certificatedName);
                        }

                        $inscripcio->save();

                        $pdf[$i][$x] = $inscripcio->getId();
                    }

                } // END FOR WEEKS
            }
        }

        return array($pdf, $mailsEnviar);
    }

    public function enviarPdf($pdf, $llistaCorreus, $idCentre)
    {
        require_once('lib/phpMailer/phpmailer.class.php');
        $centre = SummerFunCenterPeer::retrieveByPK($idCentre);
        sfLoader::loadHelpers('Partial');
        $attachment = $pdf->Output('', 'S');
        $missatge = get_partial('confirmation_mail_message', array('centre' => $centre));

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;                  // enable SMTP authentication
        $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
        $mail->Host = "smtp.gmail.com";      // sets GMAIL as the SMTP server
        $mail->Port = 465;                   // set the SMTP port
        $mail->Username = sfConfig::get('app_email_user');  // GMAIL username
        $mail->Password = sfConfig::get('app_email_password'); // GMAIL password
        $mail->From = strlen($centre->getMail()) != 0 ? $centre->getMail() : 'info@kidsandus.es';
        $mail->AddReplyTo($mail->From, 'Kids&Us');
        $mail->Helo = "www.kidsandus.es.mx";
        $mail->FromName = 'Kids&Us';
        $mail->Subject = $this->getContext()->getI18N()->__('Inscripció English Summer Fun');
        $mail->Body = $missatge;
        $mail->AltBody = $missatge;
        $mail->CharSet = 'UTF-8';
        $mail->WordWrap = 50;
        $mail->IsHTML(true);
        for ($i = 1; $i <= count($llistaCorreus); $i++) {
            for ($j = 1; $j <= 2; $j++) {
                $mail->AddAddress($llistaCorreus[$i][$j]);
            }
        }

        if ($this->getUser()->getCulture() == 'ca') {

            $nomFitxer = "inscripcio-esf.pdf";
            $fitxerCondicions = "pdf/condicions-generals-esf.pdf";

            if ($centre->getInscriptionConditionsTermsPdfCa() != null) {

                $fitxerCondicions = "uploads/summer-fun/center/pdf-conditions/ca/" . $centre->getInscriptionConditionsTermsPdfCa();

            }
            $fitxerCondicionsNom = "condicions-generals-esf.pdf";

        } else if ($this->getUser()->getCulture() == 'es') {

            $nomFitxer = "inscripcion-esf.pdf";
            $fitxerCondicions = "pdf/condiciones-generales-esf.pdf";

            if ($centre->getInscriptionConditionsTermsPdfEs() != null) {

                $fitxerCondicions = "uploads/summer-fun/center/pdf-conditions/es/" . $centre->getInscriptionConditionsTermsPdfEs();

            }
            $fitxerCondicionsNom = "condiciones-generales-esf.pdf";


        } else if ($this->getUser()->getCulture() == 'it') {

            $nomFitxer = "registrazione-esf.pdf";
            $fitxerCondicions = "pdf/condizioni-generali-esf.pdf";


            if ($centre->getInscriptionConditionsTermsPdfIt() != null) {

                $fitxerCondicions = "uploads/summer-fun/center/pdf-conditions/it/" . $centre->getInscriptionConditionsTermsPdfIt();

            }
            $fitxerCondicionsNom = "condizioni-generali-esf.pdf";


        } else if ($this->getUser()->getCulture() == 'fr') {
            $nomFitxer = "inscription-esf.pdf";
            $fitxerCondicions = "pdf/termes-et-conditions-esf.pdf";

            if ($centre->getInscriptionConditionsTermsPdfFr() != null) {
                $fitxerCondicions = "uploads/summer-fun/center/pdf-conditions/fr/" . $centre->getInscriptionConditionsTermsPdfFr();
            }

            $fitxerCondicionsNom = "termes-et-conditions-esf.pdf";
        } else {
            $nomFitxer = "inscripcion-esf.pdf";
            $fitxerCondicions = "pdf/condiciones-generales-esf.pdf";
            $fitxerCondicionsNom = "condiciones-generales-esf.pdf";
        }

        $mail->AddStringAttachment($attachment, $nomFitxer);

        $mail->AddAttachment($fitxerCondicions, $fitxerCondicionsNom);

        $mail->Send();


        return sfView::NONE;
    }


	public function executeTest(){

    	$this->getUser()->setCulture('ca');
	    $inscripcions[1][1]=100;
	
	    list($pdf,$idCentre)=util::generarPdf($inscripcions);
	    $mailsEnviar[1][1]="metalrockero@gmail.com";
	    $mailsEnviar[1][2]=null;
	    $this->enviarPdf($pdf, $mailsEnviar, $idCentre);
	
	  //  $attachment=$pdf->Output('uploads/test.pdf', 'F');
	    return sfView::NONE;
	
	}
	
	private function getNombreProvincia($provincias, $id) 
	{
		foreach ($provincias as $provincia) {
			if ($provincia->getId() == $id) {
				return $provincia->getNombre();
			}
		}
		
		return "";
	}
	
	public function executeUploadPhoto()
	{
		$request = $this->getRequest();
		if (!$request->isXmlHttpRequest()) {
			throw $this->createNotFoundException('Invalid request');
		}
		
		$response['status'] = 'KO';
		$response['message'] = $this->getContext()->getI18N()->__('error_subiendo_imagen');
		
		if ($this->getRequest()->hasFiles())
		{
			foreach ($this->getRequest()->getFileNames() as $uploadedFile)
			{
				if ($uploadedFile == 'studentPhoto')
				{
					$fileName  = $this->getRequest()->getFileName($uploadedFile);
					//$fileSize  = $this->getRequest()->getFileSize($uploadedFile);
					//$fileType  = $this->getRequest()->getFileType($uploadedFile);
					//$fileError = $this->getRequest()->hasFileError($uploadedFile);
					$uploadDir = sfConfig::get('app_inscripcion_imagen_directorio_temporal');
					
					$extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
					$imageName = uniqid() . '.' . $extension;
					
					if (strtolower($extension) != 'jpg' && strtolower($extension) != 'jpeg' && strtolower($extension) == 'png' && strtolower($extension) == 'gif') {
						$response['message'] = $this->getContext()->getI18N()->__('error_tipo_fichero');
						break;
					}
					
					if ($request->moveFile($uploadedFile, $uploadDir . $imageName))
					{
						if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
							$imagen = imagecreatefromjpeg($uploadDir . $imageName);
						} 
						elseif (strtolower($extension) == 'png') {
							$imagen = imagecreatefrompng($uploadDir . $imageName);
						} 
						else {
							$imagen = imagecreatefromgif($uploadDir . $imageName);
						}
						
						$width = imagesx($imagen);
						$height = imagesy($imagen);
						
						if (!$width) {
							$response['message'] = $this->getContext()->getI18N()->__('imagen_no_valida');
						}
						
						$minHeight = sfConfig::get('app_inscripcion_imagen_min_width');
						$minWidth = sfConfig::get('app_inscripcion_imagen_min_height');
						
						if ($width < $minWidth) {
							$response['message'] = $this->getContext()->getI18N()->__('imagen_ancho_minimo_menor', array('%min_width%' => $minWidth));
						} 
						elseif ($height < $minHeight) {
							$response['message'] = $this->getContext()->getI18N()->__('imagen_alto_minimo_menor', array('%min_height%' => $minHeight));
						} 
						else {
							/*
							 // Escalamos la imagen
							$imageAscpectRatio = $width / $height;
							$thumbnailAspectRatio = $minWidth / $minHeight;
							if ($width <= $minWidth && $height <= $minHeight) {
								$thumbnailWidth = $width;
								$thumbnailHeight = $height;
							} 
							elseif ($thumbnailAspectRatio > $imageAscpectRatio) {
								$thumbnailWidth = (int) ($minHeight * $imageAscpectRatio);
								$thumbnailHeight = $minHeight;
							} 
							else {
								$thumbnailWidth = $minWidth;
								$thumbnailHeight = (int) ($minWidth / $imageAscpectRatio);
							}
						
							$thumbnailImagen = imagecreatetruecolor($thumbnailWidth, $thumbnailHeight);
							imagecopyresampled($thumbnailImagen, $imagen, 0, 0, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
						
							if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
								imagejpeg($thumbnailImagen, $uploadDir . $imageName, 90);
							}
							elseif (strtolower($extension) == 'png') {
								imagepng($thumbnailImagen, $uploadDir . $imageName, 9);
							}
							elseif (strtolower($extension) == 'gif') {
								imagegif($thumbnailImagen, $uploadDir . $imageName);
							}
						
							imagedestroy($imagen);
							imagedestroy($thumbnailImagen);
						
							$response['status'] = 'OK';
							$response['message'] = $imageName;
							*/
							
							// Clip imagen
							if ($minWidth / $minHeight > $width / $height) {
								$thumbnailHeight = ($height / $width) * $minWidth;
								$thumbnailWidth = $minWidth;
							} else {
								$thumbnailWidth = ($width / $height) * $minHeight;
								$thumbnailHeight = $minHeight;
							}
							$dx = ($minWidth / 2) - ($thumbnailWidth / 2);
							$dy = ($minHeight / 2) - ($thumbnailHeight / 2);
							$thumbnailImagen  = imagecreatetruecolor($minWidth, $minHeight);
							imagecopyresampled($thumbnailImagen, $imagen, $dx, $dy, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);
							
							if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
								imagejpeg($thumbnailImagen, $uploadDir . $imageName, 90);
							}
							elseif (strtolower($extension) == 'png') {
								imagepng($thumbnailImagen, $uploadDir . $imageName, 9);
							}
							elseif (strtolower($extension) == 'gif') {
								imagegif($thumbnailImagen, $uploadDir . $imageName);
							}
							
							imagedestroy($imagen);
							imagedestroy($thumbnailImagen);
							
							$response['status'] = 'OK';
							$response['message'] = $imageName;
						}
					}
				}
			}
		}
		
		return $this->renderText(json_encode($response));
	}
	
	/**
	 * Recupera el nombre del pdf de condiciones generales para un centro
	 */
	
	public function executeCondicionesCentre()
	{
		$centroId = $this->getRequestParameter('id');
        /** @var SummerFunCenter $centro */
		$centro = SummerFunCenterPeer::retrieveByPK($centroId);
		if ($centro)
		{
			$pdf = "pdf/condiciones-generales-esf.pdf";
			if ($this->getUser()->getCulture() == 'ca')
			{
				$pdf = "pdf/condicions-generals-esf.pdf";
				if ($centro->getInscriptionConditionsTermsPdfCa()) {
					$pdf = "uploads/summer-fun/center/pdf-conditions/ca/" . $centro->getInscriptionConditionsTermsPdfCa();
				}
			
			}
			else if ($this->getUser()->getCulture() == 'es') {
				if ($centro->getInscriptionConditionsTermsPdfEs()){
					$pdf="uploads/summer-fun/center/pdf-conditions/es/" . $centro->getInscriptionConditionsTermsPdfEs();
				}
			}
			else if ($this->getUser()->getCulture() == 'it')
			{
				$pdf = "pdf/condizioni-generali-esf.pdf";
				if ($centro->getInscriptionConditionsTermsPdfIt()) {
					$pdf = "uploads/summer-fun/center/pdf-conditions/it/" . $centro->getInscriptionConditionsTermsPdfIt();
				}
			}

            $result['showBeca'] = 0;
            if ($centro->getShowBecaWidget()) {
                $result['showBeca'] = 1;
            }
            
            $result['showVaccination'] = 0;
            if ($centro->getIsVaccination()) {
                $result['showVaccination'] = 1;
            }

            $result['pdf'] = $pdf;
			
			return $this->renderText(json_encode($result));
		}
		
		return $this->renderText("");
	}

    private function calculateDiscounts($total = false)
    {
        $numBrothers = $this->inscripcions + 1;
        for ($i = 1; $i <= $this->inscripcions + 1; $i++)
        {
            $this->{'discountPercentRegistrationStudent' . $i} = 0;
            $this->{'discountAmountRegistrationStudent' . $i} = 0;

            if ($this->{'isStudentKidAndUs'. $i} == 1)
            {
                if ($this->center2->getKidsAndUsStudentAmountDiscount() > 0) {
                    if ($total) {
                        if (isset($this->nombreSetmanes)) {
                            for ($j = 1; $j <= $this->nombreSetmanes; $j++) {
                                if (isset($this->{'week' . $j . 'alumne' . $i})) {
                                    $this->{'discountAmountRegistrationStudent' . $i} += $this->center2->getKidsAndUsStudentAmountDiscount();
                                }
                            }
                        }
                    }
                    else {
                        $this->{'discountAmountRegistrationStudent' . $i} += $this->center2->getKidsAndUsStudentAmountDiscount();
                    }
                }
                elseif ($this->center2->getKidsAndUsStudentPercentDiscount()) {
                    $this->{'discountPercentRegistrationStudent' . $i} = $this->center2->getKidsAndUsStudentPercentDiscount();
                }
            }


            $numberOfWeeks = 0;
            if (isset($this->nombreSetmanes))
            {
                for ($j = 1; $j <= $this->nombreSetmanes; $j++) {
                    if (isset($this->{'week' . $j . 'alumne' . $i}) ) {
                        $numberOfWeeks++;
                    }
                }
            }

            if ($this->center2->getWeeksDiscount() > 0 && ($this->center2->getWeeksPercentDiscount() > 0 || $this->center2->getWeeksAmountDiscount() > 0) && $numberOfWeeks >= $this->center2->getWeeksDiscount())
            {
                if ($this->center2->getWeeksPercentDiscount()) {
                    $this->{'discountPercentRegistrationStudent' . $i} += $this->center2->getWeeksPercentDiscount();
                }
                elseif ($this->center2->getWeeksAmountDiscount()) {

                    if ($total) {
                        if (isset($this->nombreSetmanes)) {
                            for ($j = 1; $j <= $this->nombreSetmanes; $j++) {
                                if (isset($this->{'week' . $j . 'alumne' . $i})) {
                                    $this->{'discountAmountRegistrationStudent' . $i} += $this->center2->getWeeksAmountDiscount();
                                }
                            }
                        }
                    }
                    else {
                        $this->{'discountAmountRegistrationStudent' . $i} += $this->center2->getWeeksAmountDiscount();
                    }

                }
            }

            if ($this->center2->getBrothersDiscount() > 0 && ($this->center2->getBrothersPercentDiscount() > 0 || $this->center2->getBrothersAmountDiscount() > 0) && $numBrothers >= $this->center2->getBrothersDiscount())
            {
                if ($this->center2->getBrothersPercentDiscount()) {
                    $this->{'discountPercentRegistrationStudent' . $i} += $this->center2->getBrothersPercentDiscount();
                }
                elseif ($this->center2->getBrothersAmountDiscount()) {
                    if ($total) {
                        if (isset($this->nombreSetmanes)) {
                            for ($j = 1; $j <= $this->nombreSetmanes; $j++) {
                                if (isset($this->{'week' . $j . 'alumne' . $i})) {
                                    $this->{'discountAmountRegistrationStudent' . $i} += $this->center2->getBrothersAmountDiscount();
                                }
                            }
                        }
                    }
                    else {
                        $this->{'discountAmountRegistrationStudent' . $i} += $this->center2->getBrothersAmountDiscount();
                    }
                }
            }
        }
    }
}
