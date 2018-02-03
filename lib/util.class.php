<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marc
 * Date: 26/03/13
 * Time: 18:48
 * To change this template use File | Settings | File Templates.
 */

class util extends sfActions
{
    public static function generarPdf($inscripciones)
    {
        $pdf = new sfTCPDF();

        $pdf->SetCreator('Kids&Us');
        $pdf->SetAuthor('Kids&Us');
        $pdf->SetTitle('Kids&Us');
        $pdf->SetSubject('Kids&Us');
        $pdf->SetKeywords('Kids&Us');

        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);

// set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
        //$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, 38, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(10);

        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
        $pdf->setLanguageArray('ca');
        $pdf->setFontSubsetting(true);
        $pdf->setPrintFooter(true);

// set font

        $pdf->SetFont(sfTCPDF::FONT, sfTCPDF::FONT_STYLE, sfTCPDF::FONT_SIZE);

        $pdf->setTypeFooter(sfContext::getInstance()->getI18N()->__('Document per entregar a l\'escola un cop signat.'));
    	
        list($pdf, $mailCentre) = Util::pdf($pdf, $inscripciones);

        return array($pdf, $mailCentre);
    }

    
    public static function pdf($pdf, $inscripciones)
    {
    	$discountAmount = 0;
    	$discountPercent = 0;
    	$aplicarDescento =  true;
        $lineBreakHeight = 6;
        for ($i = 1; $i <= count($inscripciones); $i++)
        {
            $pdf->SetFont(sfTCPDF::FONT, sfTCPDF::FONT_STYLE, sfTCPDF::FONT_SIZE);
            $preu[$i] = 0;
            $inscCode[$i] = 0;
            $discounts[$i] = 0;

            /** @var Inscription $insc */
            $insc = InscriptionPeer::retrieveByPK($inscripciones[$i][1]);

            $discountPercent = $insc->getDiscountPercent();

            if ($insc->getState() == 1) {
            	$aplicarDescento = false;
            }

            $pdf->setTypeHeader($insc->getInscriptionCode(), sfContext::getInstance()->getI18N()->__('fullInscripcio'), sfContext::getInstance()->getI18N()->__('fullInscripcio2'));
            $pdf->AddPage();

            $inscCode[$i]=$insc->getInscriptionCode();

			// PRIMERA LINEA
            $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('DADES PERSONALS') . ":", 0, 0, 'L', 0, '', 0, false, 'M', 'M');

            if ($insc->getStudentPhoto()) {
            	$image =  sfConfig::get('app_inscripcion_imagen_directorio') . $insc->getStudentPhoto();

            	$pdf->SetXY(175, 35);
            	$pdf->Image($image, '', '', 0, 0, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
           	else {
           	}

           	$pdf->Ln(10);

            // SEGUNDA LINEA
            $text = sfContext::getInstance()->getI18N()->__('Nom complet:');
            $width = util::getTextWidth($pdf, $text);
            $pdf->Cell($width, sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($insc->getStudentPhoto() ? 100 - $width : 100, sfTCPDF::FONT_HEIGHT, $insc->getFullStudentName(), array('B' => array('dash' => 1, 'width' => 0, 'cap' => 'butt', 'join' => 'miter')), 0, 'C', 0, '', 0, false, 'C', 'M');

            $text = sfContext::getInstance()->getI18N()->__('Data de naixement:');
            $width = util::getTextWidth($pdf, $text);
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($insc->getStudentPhoto() ? 50 - $width : 0, sfTCPDF::FONT_HEIGHT, date("d-m-Y", strtotime($insc->getStudentBirthDate())), array('B' => array('dash' => 1, 'width' => 0)), 0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

            // TERCERA LINEA
            $text = sfContext::getInstance()->getI18N()->__('Domicili:');
            $width = util::getTextWidth($pdf, $text);
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($insc->getStudentPhoto() ? 100 - $width : 100, sfTCPDF::FONT_HEIGHT, $insc->getStudentAddress() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            $text = sfContext::getInstance()->getI18N()->__('Codi Postal:');
            $width = util::getTextWidth($pdf, $text);
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($insc->getStudentPhoto() ? 50 - $width : 0, sfTCPDF::FONT_HEIGHT, $insc->getStudentZip() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

            // CUARTA LINEA
            $text = sfContext::getInstance()->getI18N()->__('Població:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($insc->getProvincia() ? 100 : 0, sfTCPDF::FONT_HEIGHT, $insc->getStudentCity() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            if ($insc->getProvincia()) {
            	$text = sfContext::getInstance()->getI18N()->__('Província') . ":";
            	$pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            	$pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getProvincia()->getNombre(), array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            }
            $pdf->Ln($lineBreakHeight);

            // QUINTA LINEA
            $text = sfContext::getInstance()->getI18N()->__('Nom i cognoms pare/mare/tutor:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getFullFatherName() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

            // SEXTA LINEA
            $text = sfContext::getInstance()->getI18N()->__('DNI:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(25, sfTCPDF::FONT_HEIGHT, $insc->getFatherDni() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            $text = sfContext::getInstance()->getI18N()->__('Mòbil:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(25, sfTCPDF::FONT_HEIGHT, $insc->getFatherPhone() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            $text = sfContext::getInstance()->getI18N()->__('e-mail:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getFatherMail() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

            // SÉPTIMA LINEA
            $text = sfContext::getInstance()->getI18N()->__('Nom i cognoms pare/mare/tutor:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getFullMotherName() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

            // OCATAVA LINEA
            $text = sfContext::getInstance()->getI18N()->__('DNI:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(25, sfTCPDF::FONT_HEIGHT, $insc->getMotherDni() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            $text = sfContext::getInstance()->getI18N()->__('Mòbil:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(25, sfTCPDF::FONT_HEIGHT, $insc->getMotherPhone() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            $text = sfContext::getInstance()->getI18N()->__('e-mail:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getMotherMail() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

            // SEPARADOR
            $pdf->Cell(0, 0, '', array('B'=>array('dash'=>0,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(8);

            // LINEA 9
            $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('DADES ACADÈMIQUES') . ":", 0, 0, 'L', 0, '', 0, false, 'M', 'M');
            $pdf->Ln($lineBreakHeight);

            // LINEA 10

            if ($insc->getKidsAndUsCenter())
            {
                $text = sfContext::getInstance()->getI18N()->__('origin_kids_and_us_center') . ':';
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getKidsAndUsCenter()->getName(), array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
                $pdf->Ln($lineBreakHeight);
            }

            $text = sfContext::getInstance()->getI18N()->__('Escola de procedència:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getSummerFunCenter() ? $insc->getSummerFunCenter()->getCenterName() : $insc->getSummerFunCenterOther(), array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);


            $entity = SchoolYearPeer::doSelectOneById($insc->getSchoolYearId());
            $text = sfContext::getInstance()->getI18N()->__('school_year') . ':';
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $entity->getName(), array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

			// LINEA 11
            if ($insc->getIsStudentKidAndUs()) {
                $text = sfContext::getInstance()->getI18N()->__('registration.trans168');
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->Cell(50, sfTCPDF::FONT_HEIGHT, $insc->getStudentSchoolYear() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            }

            $text = sfContext::getInstance()->getI18N()->__('Alumne KidsUs') . ":";
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getIsStudentKidAndUs() == 1 ? sfContext::getInstance()->getI18N()->__('Sí') : sfContext::getInstance()->getI18N()->__('No'), array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(8);

            // SEPARADOR
            $pdf->Cell(0, 0, '', array('B'=>array('dash'=>0,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(8);

            // LINEA 12
            $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('DADES MÈDIQUES') . ":", 0, 0, 'L', 0, '', 0, false, 'M', 'M');
            $pdf->Ln($lineBreakHeight);

            // LINEA 13 - Alergias
            $text = sfContext::getInstance()->getI18N()->__('Té algun tipus de malaltia o al·lèrgia?') . ":";
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');

            $text = sfContext::getInstance()->getI18N()->__('Sí');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('alergia_si' . $i, 5, $insc->getStudentAllergies() ? true : false);

            $text = sfContext::getInstance()->getI18N()->__('No');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('alergia_no' . $i, 5, $insc->getStudentAllergies() ? false : true);

            $text = sfContext::getInstance()->getI18N()->__('Especificar:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getStudentAllergiesDescription() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

            // LINEA 14 - Discapacidad
            $text = sfContext::getInstance()->getI18N()->__('Té alguna discapacitat') . ":";
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');

            $text = sfContext::getInstance()->getI18N()->__('Sí');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('discapacidad_si' . $i, 5, $insc->getStudentDisability() != null);

            $text = sfContext::getInstance()->getI18N()->__('No');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('discapacidad_no' . $i, 5, $insc->getStudentDisability() == null);

            $text = sfContext::getInstance()->getI18N()->__('Grau') . ":";
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(20, sfTCPDF::FONT_HEIGHT, $insc->getStudentDisabilityLevel() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            $text = sfContext::getInstance()->getI18N()->__('Quina') . ":";
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getStudentDisability() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

            if (sfContext::getInstance()->getUser()->getCulture() != 'fr') {
                // LINEA 15 - Tarjeta Sanitaria
                $text = sfContext::getInstance()->getI18N()->__('Núm. targeta sanitària') . ":";
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->Cell(50, sfTCPDF::FONT_HEIGHT, $insc->getStudentNumTarjetaSanitaria(), array('B' => array('dash' => 1, 'width' => 0)), 0, 'C', 0, '', 0, false, 'C', 'M');
                $text = sfContext::getInstance()->getI18N()->__('Companyia') . ":";
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getStudentTarjetaSanitariaCompanyia() ? $insc->getStudentTarjetaSanitariaCompanyia() : 'CATSALUT', array('B' => array('dash' => 1, 'width' => 0)), 0, 'C', 0, '', 0, false, 'C', 'M');
                $pdf->Ln($lineBreakHeight);
            }

            $text = sfContext::getInstance()->getI18N()->__('registration.trans237') . ":";
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');

            $text = sfContext::getInstance()->getI18N()->__('Sí');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('vaccinated_si' . $i, 5, $insc->getIsVaccinated() == 1);

            $text = sfContext::getInstance()->getI18N()->__('No');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('vaccinated_no' . $i, 5, $insc->getIsVaccinated() == 0);
            $pdf->Ln($lineBreakHeight + 2);

            // LINEA 16
            $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('DADES INSCRIPCIÓ'), 0, 0, 'L', 0, '', 0, false, 'M', 'M');
            $pdf->Ln($lineBreakHeight);

            // LINEA 17
            $studentInscrCenter = SummerFunCenterPeer::doSelectOneByCourseId($insc->getStudentCourseInscription());
            $text = sfContext::getInstance()->getI18N()->__('Centre:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $studentInscrCenter->getTitle() , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln($lineBreakHeight);

            // LINEA 18
            /*
            $text = sfContext::getInstance()->getI18N()->__('Servei d\'acollida:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');

            $text = sfContext::getInstance()->getI18N()->__('Sí');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('acogida_si' . $i, 5, $insc->getShelter() == 1);

            $text = sfContext::getInstance()->getI18N()->__('No');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('acogida_no' . $i, 5, $insc->getShelter() == 0);
            $pdf->Ln($lineBreakHeight);
            */

            if (sfContext::getInstance()->getUser()->getCulture() != 'fr')
            {
                // LINEA 19
                $text = sfContext::getInstance()->getI18N()->__("Hi ha amics de l'infant que assistiran al casal?") . ":";
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');

                $text = sfContext::getInstance()->getI18N()->__('Sí');
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->CheckBox('amigos_si' . $i, 5, $insc->getStudentFriends());

                $text = sfContext::getInstance()->getI18N()->__('No');
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->CheckBox('amigos_no' . $i, 5, !$insc->getStudentFriends());
                $pdf->Ln($lineBreakHeight);

                // LINEA 20
                $text = sfContext::getInstance()->getI18N()->__('En cas afirmatiu, indiqueu nom i cognoms:');
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');

                if ($insc->getStudentFriends()) {
                    if (strlen($insc->getStudentFriends()) > 100) {
                        $pdf->MultiCell(0, 0, $insc->getStudentFriends(), 0, 'L', 0, 1, $pdf->getX(), $pdf->getY() - 1, true);
                        $isMultiCell = true;
                    }
                    else {
                        $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getStudentFriends(), array('B'=>array('dash'=>1,'width'=>0)), 0, 'L', 0, '', 1, false, 'M', 'M');
                    }
                }
                else {
                    $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $insc->getStudentFriends(), array('B'=>array('dash'=>1,'width'=>0)), 0, 'L', 0, '', 1, false, 'M', 'M');
                }
            }


            if ($insc->getStudentComments()) {
                $pdf->Ln(isset($isMultiCell) ? $lineBreakHeight - 2 : $lineBreakHeight);
                $isMultiCell = true;
                $text = sfContext::getInstance()->getI18N()->__("Altres aspectes a tenir en compte") . ":";
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->MultiCell(0, 0, $insc->getStudentComments(), 0, 'L', 0, 1, $pdf->getX(), $pdf->getY() - 1, true);
            }


            if ($insc->getCustomQuestion() && ($insc->getCustomQuestionAnswer() == 1 || $insc->getCustomQuestionAnswer() == 0))
            {
                $pdf->Ln(isset($isMultiCell) ? 1 : $lineBreakHeight - 2);
                $isMultiCell = true;
                $text = $insc->getCustomQuestion() . ":";

                $pdf->MultiCell(0, 0, $text, 0, '', 0, 1, '', '', true);
                $pdf->Ln(2);

                $text = sfContext::getInstance()->getI18N()->__('Sí');
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->CheckBox('custom_si' . $i, 5, $insc->getCustomQuestionAnswer() == 1);

                $text = sfContext::getInstance()->getI18N()->__('No');
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->CheckBox('custom_no' . $i, 5, $insc->getCustomQuestionAnswer() == 0);
            }


            $pdf->Ln(isset($isMultiCell) ? 7 : 9);

            // LINEA 20 - Semanas
            $widthColumnWeeks = 100;
            $pdf->Cell($widthColumnWeeks, sfTCPDF::FONT_HEIGHT, sfContext::getInstance()->getI18N()->__('Setmanes'), array('LTRB' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 0, false, 'M', 'T');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, sfContext::getInstance()->getI18N()->__("Serveis"), array('LTRB' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 0, false, 'M', 'T');
            $missatgeEspera = 0;
            $hayExcursiones = false;
            for ($j = 1; $j <= count($inscripciones[$i]); $j++)
            {
                $weekInscription = InscriptionPeer::retrieveByPK($inscripciones[$i][$j]);

                $discounts[$i] += $weekInscription->getDiscount();
                $discountAmount += $weekInscription->getDiscount();

                $curs = CoursePeer::getCourseByInscrptionId($inscripciones[$i][$j]);
                if ($curs->getExcursion()) {
                	$hayExcursiones = true;
                }
                $espera = '';

                if ($weekInscription->getState() == 0) {
                    $preu[$i] = $curs->getPrice() + $preu[$i];
                }
                else {
                    $espera = '* ';
                    $missatgeEspera = 1;
                }

                $week = $curs->getWeek();
                $pdf->setCellPadding(0);

                $pdf->Ln(5);

                if ($curs->getSchedule() != '') {
                    //$pdf->MultiCell($widthColumnWeeks, sfTCPDF::FONT_HEIGHT, sfContext::getInstance()->getI18N()->__( '%espera% %week% (%curs%)', array('%espera%'=>$espera, '%week%'=>$week, '%curs%'=>$curs->getSchedule())), array('LR'=>array('dash'=>0,'width'=>0)), '', 0, 1, '', '', true);
                    //$pdf->Ln(3);
                    $pdf->Cell($widthColumnWeeks, sfTCPDF::FONT_HEIGHT, sfContext::getInstance()->getI18N()->__( '%espera% %week% (%curs%)', array('%espera%'=>$espera, '%week%'=>$week, '%curs%'=>$curs->getSchedule())), array('LR'=>array('dash'=>0,'width'=>0)), 0, 'C', 0, '', 1, false, 'M', 'T');
                }
                else {
                    $pdf->Cell($widthColumnWeeks, sfTCPDF::FONT_HEIGHT, $espera . $week, array('LR' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 1, false, 'M', 'T');
                }

                if (count($weekInscription->getInscriptionServiceSchedules()))
                {
                    $isFirst = true;
                    foreach ($weekInscription->getInscriptionServiceSchedules() as $inscriptionServiceSchedule)
                    {
                        $schedule = $inscriptionServiceSchedule->getServiceSchedule();
                        $schedule->setCulture(sfContext::getInstance()->getUser()->getCulture());

                        $service = $schedule->getService();
                        $service->setCulture(sfContext::getInstance()->getUser()->getCulture());

                        $label = $service->getName() . ' (' . $schedule->getName() . ')';

                        if ($isFirst) {
                            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $label, array('LR' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 0, false, 'M', 'T');
                            $isFirst = false;
                        }
                        else {
                            $pdf->Ln(5);
                            $pdf->Cell($widthColumnWeeks, sfTCPDF::FONT_HEIGHT, '', array('LR' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 0, false, 'M', 'T');
                            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $label, array('LR' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 0, false, 'M', 'T');
                        }
                    }
                }
                else {
                    $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, '-', array('LR' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 0, false, 'M', 'T');
                }

                $pdf->Ln(0);
                $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, '', array('B' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 0, false, 'M', 'T');

                /*
                if ($label == '') {
                    $label = sfContext::getInstance()->getI18N()->__("No");
                }
                else {
                    $label = substr($label, 0, -3);
                }
                */
                //$pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $label, array('LTRB' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 0, false, 'M', 'T');
            }

            if ($missatgeEspera)
            {
                $pdf->Ln(5);
                $text = '* '. sfContext::getInstance()->getI18N()->__('Llista d\'espera') . '. ' . sfContext::getInstance()->getI18N()->__('Els possibles descomptes seran aplicats un cop confirmada la plaça');
                $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'C', 0, '', 0, false, 'M', 'C');
            }

            if ($studentInscrCenter->getShowExcursionWidget() && $hayExcursiones)
            {
                $pdf->Ln($lineBreakHeight);
                if ($insc->getStudentExcursion() == 1) {
                    $text = sfContext::getInstance()->getI18N()->__("Sí, dono permís per a que l'infant realitzi les excursions");
                }
                else {
                    $text = sfContext::getInstance()->getI18N()->__("No, no dono permís per a que l'infant realitzi les excursions");
                }
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            }

            $pdf->SetY(245);
            $pdf->Cell(0, 0, '', array('B'=>array('dash'=>0,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            $pdf->setCellPaddings(0,0,0,0);
            $pdf->Ln(5);

            $pdf->MultiCell(0, 5, sfContext::getInstance()->getI18N()->__('condicions_pdf'), 0, 'L', 0, 0, '', '', true);
            $pdf->Ln(11);

            $text = sfContext::getInstance()->getI18N()->__('Nom i cognoms del pare, mare o tutor legal:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, sfTCPDF::FONT_HEIGHT, '' , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(6);

            $text = sfContext::getInstance()->getI18N()->__('DNI:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(20, sfTCPDF::FONT_HEIGHT, '' , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            $pdf->setX(60);
            $text = sfContext::getInstance()->getI18N()->__('Data:');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(25, sfTCPDF::FONT_HEIGHT, '   /      /   ' , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

            $pdf->setX(120);
            $pdf->Cell(40, sfTCPDF::FONT_HEIGHT, sfContext::getInstance()->getI18N()->__('Signatura'), 0, 0, 'C', 0, '', 0, false, 'M', 'B');
        }

        $pdf->setTypeHeader(null,sfContext::getInstance()->getI18N()->__('fullInscripcio'),sfContext::getInstance()->getI18N()->__('fullInscripcio2'));        
        $pdf->AddPage();
        $pdf->Cell(28, 0, sfContext::getInstance()->getI18N()->__('DADES DE LA INSCRIPCIÓ'), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
        /** @var SummerFunCenter $studentInscrCenter */
        $studentInscrCenter = SummerFunCenterPeer::doSelectOneByCourseId($insc->getStudentCourseInscription());
        $pdf->Ln($lineBreakHeight);
        
        $text = sfContext::getInstance()->getI18N()->__('Centre:');
        $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
        $pdf->Cell(90, sfTCPDF::FONT_HEIGHT, $studentInscrCenter->getTitle() , array('B'=>array('dash' => 1,'width' => 0)),0, 'C', 0, '', 0, false, 'C', 'M');
        $pdf->Ln($lineBreakHeight);
        
        $text = sfContext::getInstance()->getI18N()->__('Número d\'inscripcions:');
        $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
        $pdf->Cell(10, sfTCPDF::FONT_HEIGHT, count($inscripciones), array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
       
        if ($studentInscrCenter->getShowBecaWidget())
        {
        	$pdf->Ln($lineBreakHeight);
        
	        $text = sfContext::getInstance()->getI18N()->__("Sol·licita ajut econòmic, beca?") . ":";
	        $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
	        
	        $text = sfContext::getInstance()->getI18N()->__('Sí');
	        $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
	        $pdf->CheckBox('beca_si' . $i, 5, $insc->getBeca() == 1);
	        
	        $text = sfContext::getInstance()->getI18N()->__('No');
	        $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
	        $pdf->CheckBox('beca_no' . $i, 5, $insc->getBeca() == 0);
        }

        $altura = 40;

        $pdf->setXY(130, $altura);
        $pdf->setCellPadding(5);
        $pdf->Cell(64, 80, '', array('LTRB'=>array('dash'=>0,'width'=>0)), 0, 'C', 0, '', 0, false, 'T', 'T');
        $total = 0;
        for ($j = 1; $j <= count($inscripciones); $j++)
        {
            $total += $preu[$j];

            $pdf->setXY(135, $altura);
            $pdf->SetFont(sfTCPDF::FONT, 'N', sfTCPDF::FONT_SIZE);
            $pdf->setCellPadding(2);
            $pdf->Cell(24, 0,  sfContext::getInstance()->getI18N()->__('Inscripció').' '.$inscCode[$j],array('B'=>array('dash'=>1,'width'=>0)), 0, 'L', 0, '', 0, false, 'T', 'T');
            $pdf->SetFont('arial', 'B', sfTCPDF::FONT_SIZE);
            $pdf->Cell(30, 0, number_format($preu[$j], 2, ',', '.') . ' €',array('B'=>array('dash'=>1,'width'=>0)), 0, 'R', 0, '', 0, false, 'T', 'T');
            $pdf->SetFont(sfTCPDF::FONT, 'B', sfTCPDF::FONT_SIZE);
            $altura += 7;

            if ($discounts[$j] > 0 && $aplicarDescento) {
                $pdf->setXY(135, $altura);
                $pdf->SetFont(sfTCPDF::FONT, 'N', sfTCPDF::FONT_SIZE);
                $pdf->setCellPadding(2);
                $pdf->Cell(24, 0, '  ' . sfContext::getInstance()->getI18N()->__('Descuento'), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'T', 'T');
                $pdf->SetFont('arial', 'B', sfTCPDF::FONT_SIZE);
                $pdf->Cell(30, 0, '-' . number_format($discounts[$j], 2, ',', '.') . ' €', array('B' => array('dash' => 1, 'width' => 0)), 0, 'R', 0, '', 0, false, 'T', 'T');
                $pdf->SetFont(sfTCPDF::FONT, 'B', sfTCPDF::FONT_SIZE);
                $altura += 7;
            }

            $services = array();
            foreach ($inscripciones[$j] as $inscNum)
            {
                $weekInscription = InscriptionPeer::retrieveByPK($inscNum);
                
                foreach ($weekInscription->getInscriptionServiceSchedules() as $inscriptionServiceSchedule)
                {
                    $service = $inscriptionServiceSchedule->getServiceSchedule()->getService();

                    //if (!in_array($service->getId(), $services)) {

                        $service->setCulture(sfContext::getInstance()->getUser()->getCulture());
                        $servicePrice = $weekInscription->getState() == 0 ? $service->getPrice() : 0;
                        $serviceName = $service->getName();
                        if (strlen($serviceName) > 25) {
                            $serviceName = substr($serviceName, 0, 23) . '...';
                        }
                        $pdf->setXY(135, $altura);
                        $pdf->SetFont(sfTCPDF::FONT, 'N', sfTCPDF::FONT_SIZE);
                        $pdf->setCellPadding(2);
                        $pdf->Cell(24, 0, '  ' . $serviceName, array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'T', 'T');
                        $pdf->SetFont('arial', 'B', sfTCPDF::FONT_SIZE);
                        $pdf->Cell(30, 0, number_format($servicePrice, 2, ',', '.') . ' €', array('B' => array('dash' => 1, 'width' => 0)), 0, 'R', 0, '', 0, false, 'T', 'T');
                        $pdf->SetFont(sfTCPDF::FONT, 'B', sfTCPDF::FONT_SIZE);
                        $altura += 7;

                        array_push($services, $service->getId());

                        if ($weekInscription->getState() == 0) {
                            $total += $service->getPrice();
                        }
                    //}
                }
               
            }
             
        }
        $pdf->SetFont(sfTCPDF::FONT, 'B', sfTCPDF::FONT_SIZE);

        if ($discountAmount > 0 && $aplicarDescento)
        {
            /*
        	$pdf->setXY(135, 100);
        	$pdf->Cell(24, 0, sfContext::getInstance()->getI18N()->__('Descompte'), array('T'=>array('dash'=>0,'width'=>0)), 0, 'L', 0, '', 0, false, 'T', 'T');
        	$pdf->SetFont('arial', 'B', sfTCPDF::FONT_SIZE);
        	$pdf->Cell(30, 0, $discountAmount . ' €',array('T'=>array('dash'=>0,'width'=>0)), 0, 'R', 0, '', 0, false, 'T', 'T');
        	$pdf->SetFont(sfTCPDF::FONT, 'B', sfTCPDF::FONT_SIZE);
        	*/

        	$pdf->setXY(135, 105);
        	$pdf->Cell(24, 0, sfContext::getInstance()->getI18N()->__('TOTAL'), '', 0, 'L', 0, '', 0, false, 'T', 'T');
        	$pdf->SetFont('arial', 'B', sfTCPDF::FONT_SIZE);
        	$pdf->Cell(30, 0, number_format($total - $discountAmount, 2) . ' €', '', 0, 'R', 0, '', 0, false, 'T', 'T');
        	$pdf->SetFont(sfTCPDF::FONT, 'B', sfTCPDF::FONT_SIZE);
        }
        else {
        	$pdf->setXY(135, 110);
        	$pdf->Cell(24, 0, sfContext::getInstance()->getI18N()->__('TOTAL'), array('T'=>array('dash'=>0,'width'=>0)), 0, 'L', 0, '', 0, false, 'T', 'T');
        	$pdf->SetFont('arial', 'B', sfTCPDF::FONT_SIZE);
        	$pdf->Cell(30, 0, $total . ' €',array('T'=>array('dash'=>0,'width'=>0)), 0, 'R', 0, '', 0, false, 'T', 'T');
        	$pdf->SetFont(sfTCPDF::FONT, 'B', sfTCPDF::FONT_SIZE);
        }
        
        $pdf->SetFont(sfTCPDF::FONT, 'N', sfTCPDF::FONT_SIZE);



        $pdf->SetY(120);
        $pdf->Cell(0, 0, '', array('B'=>array('dash'=>0,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');

        $pdf->Ln(11);

        if (sfContext::getInstance()->getUser()->getCulture() != 'fr') {

            $paymentMode = sfContext::getInstance()->getI18N()->__('MODALITAT DE PAGAMENT');

            if ($insc->getMethodPayment() != InscriptionPeer::METHOD_PAYMENT_TPV && $insc->getMethodPayment() != InscriptionPeer::METHOD_PAYMENT_RECIBO) {
                $paymentMode .= '*';
            }

            $pdf->Cell(0, 0, $paymentMode, 0, 0, 'L', 0, '', 0, false, 'M', 'C');
            $pdf->Ln($lineBreakHeight);

            if ($insc->getMethodPayment() == InscriptionPeer::METHOD_PAYMENT_TRANSFER)
            {
                $text = sfContext::getInstance()->getI18N()->__('Transferència bancària:');
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
                $pdf->Cell(0, 0, $studentInscrCenter->getAccountNumber(), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
            }
            elseif ($insc->getMethodPayment() == InscriptionPeer::METHOD_PAYMENT_CASH) {
                $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('Pagament en efectiu al centre'), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
            }
            elseif ($insc->getMethodPayment() == InscriptionPeer::METHOD_PAYMENT_RECIBO) {
                $text = sfContext::getInstance()->getI18N()->__('Pagament amb rebut domiciliat');
                $pdf->Cell(util::getTextWidth($pdf, $text), 0, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'C');

                $culture = sfContext::getInstance()->getUser()->getCulture();
                switch ($culture)
                {
                    case 'es';
                    {
                        if ($studentInscrCenter->getReciboDomiciliadoTxtEs()) {
                            $pdf->Ln($lineBreakHeight);
                            $pdf->Cell(0, 0, $studentInscrCenter->getReciboDomiciliadoTxtEs(), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
                        }
                        break;
                    }
                    case 'ca';
                    {
                        if ($studentInscrCenter->getReciboDomiciliadoTxtCa()) {
                            $pdf->Ln($lineBreakHeight);
                            $pdf->Cell(0, 0, $studentInscrCenter->getReciboDomiciliadoTxtCa(), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
                        }
                        break;
                    }
                    case 'it';
                    {
                        if ($studentInscrCenter->getReciboDomiciliadoTxtIt()) {
                            $pdf->Ln($lineBreakHeight);
                            $pdf->Cell(0, 0, $studentInscrCenter->getReciboDomiciliadoTxtIt(), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
                        }
                        break;
                    }
                }
            }
            else {
                $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('TPV'), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
            }

            $pdf->Ln($lineBreakHeight);
            if ($insc->getSplitPayment() == 0)
            {
                $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('Desitjo fer el 100% del pagament.'), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
            }
            else {
                if ($studentInscrCenter->getSecondPaymentDate()) {
                    $date = DateTime::createFromFormat('Y-m-d', $studentInscrCenter->getSecondPaymentDate());
                    $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('registration.trans230', array('%importe%' => ($total/2) , '%date%' => $date->format('d/m/Y'))), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
                }
            }

            if ($insc->getMethodPayment() != InscriptionPeer::METHOD_PAYMENT_TPV && $insc->getMethodPayment() != InscriptionPeer::METHOD_PAYMENT_RECIBO) {
                $pdf->Ln($lineBreakHeight);
                $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('pagament'), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
            }
        }
        else
        {
            $pdf->Cell(0, 0, sfContext::getInstance()->getI18N()->__('payment_fr'), 0, 0, 'L', 0, '', 0, false, 'M', 'C');

            $pdf->Ln($lineBreakHeight);

            $text = "Désirez recevoir une attestation fiscale pour frais de garde à la fin de la semaine?";
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');

            $text = sfContext::getInstance()->getI18N()->__('Sí');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('certificated1', 5, $insc->getCertificated() == 1);

            $text = sfContext::getInstance()->getI18N()->__('No');
            $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->CheckBox('certificated2', 5, $insc->getCertificated() == 0);

            if ($insc->getCertificated()) {
                $pdf->Ln($lineBreakHeight);
                $text = "À quel nom?";
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');

                $text = $insc->getCertificatedName();
                $pdf->Cell(util::getTextWidth($pdf, $text), sfTCPDF::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            }
        }



        $pdf->SetY(200);
        $pdf->Cell(0, 0, '', array('B'=>array('dash'=>0,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');


        $pdf->Ln($lineBreakHeight);
        $pdf->setCellPadding(0);

        $pdf->MultiCell(0, 5, sfContext::getInstance()->getI18N()->__('condicions_pdf'), 0, 'L', 0, 0, '', '', true);


        $pdf->Ln(17);
        $pdf->Cell(80, 0, sfContext::getInstance()->getI18N()->__('Nom i cognoms del pare, mare o tutor legal:'), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
        $pdf->Cell(0, 0, '' , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
        $pdf->Ln(9);
        $pdf->Cell(8, 0, sfContext::getInstance()->getI18N()->__('DNI:'), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
        $pdf->Cell(20, 0, '' , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');
        $pdf->setX(60);
        $pdf->Cell(11, 0, sfContext::getInstance()->getI18N()->__('Data:'), 0, 0, 'L', 0, '', 0, false, 'M', 'C');
        $pdf->Cell(25, 0, '   /      /   ' , array('B'=>array('dash'=>1,'width'=>0)),0, 'C', 0, '', 0, false, 'C', 'M');


        $pdf->setX(120);
        $pdf->Cell(40, 0, sfContext::getInstance()->getI18N()->__('Signatura'), 0, 0, 'C', 0, '', 0, false, 'M', 'L');

        return array($pdf,$studentInscrCenter->getId());
    }
    
    public static function getTextWidth($pdf, $text) 
    {
    	return $pdf->GetStringWidth($text, sfTCPDF::FONT, sfTCPDF::FONT_STYLE, sfTCPDF::FONT_SIZE) + sfTCPDF::SANGRADO;
    }

    public static function enviarPdf($pdf, $llistaCorreus, $idCentre)
    {
        require_once('lib/phpMailer/phpmailer.class.php');
        $centre = SummerFunCenterPeer::retrieveByPK($idCentre);
        sfLoader::loadHelpers('Partial');
        $attachment = $pdf->Output('', 'S');
        $missatge = get_partial('inscription/confirmation_mail_message', array('centre' => $centre));

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
        $mail->Subject = sfContext::getInstance()->getI18N()->__('Inscripció English Summer Fun');
        $mail->Body = $missatge;
        $mail->AltBody = $missatge;
        $mail->CharSet = 'UTF-8';
        $mail->WordWrap = 50;
        $mail->IsHTML(true);
        for ($i = 1; $i <= count($llistaCorreus); $i++) {
            for ($j = 1; $j <= 2; $j++) {
                if (isset($llistaCorreus[$i][$j])) {
                    $mail->AddAddress($llistaCorreus[$i][$j]);
                }
            }
        }

        if (sfContext::getInstance()->getUser()->getCulture() == 'ca') {

            $nomFitxer = "inscripcio-esf.pdf";
            $fitxerCondicions = "pdf/condicions-generals-esf.pdf";

            if ($centre->getInscriptionConditionsTermsPdfCa() != null) {

                $fitxerCondicions = "uploads/summer-fun/center/pdf-conditions/ca/" . $centre->getInscriptionConditionsTermsPdfCa();

            }
            $fitxerCondicionsNom = "condicions-generals-esf.pdf";

        } else if (sfContext::getInstance()->getUser()->getCulture() == 'es') {

            $nomFitxer = "inscripcion-esf.pdf";
            $fitxerCondicions = "pdf/condiciones-generales-esf.pdf";

            if ($centre->getInscriptionConditionsTermsPdfEs() != null) {

                $fitxerCondicions = "uploads/summer-fun/center/pdf-conditions/es/" . $centre->getInscriptionConditionsTermsPdfEs();

            }
            $fitxerCondicionsNom = "condiciones-generales-esf.pdf";


        } else if (sfContext::getInstance()->getUser()->getCulture() == 'it') {

            $nomFitxer = "registrazione-esf.pdf";
            $fitxerCondicions = "pdf/condizioni-generali-esf.pdf";


            if ($centre->getInscriptionConditionsTermsPdfIt() != null) {

                $fitxerCondicions = "uploads/summer-fun/center/pdf-conditions/it/" . $centre->getInscriptionConditionsTermsPdfIt();

            }
            $fitxerCondicionsNom = "condizioni-generali-esf.pdf";


        } else if (sfContext::getInstance()->getUser()->getCulture() == 'fr') {
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
    }
}