<?php

/**
 * Inscription actions.
 *
 * @package    kids
 * @subpackage Inscription
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
require_once('plugins/sfTCPDFPlugin/lib/TCPDFFicha.class.php');
require_once('lib/util.class.php');
require_once "plugins/php_writeexcel/class.writeexcel_workbook.inc.php";
require_once "plugins/php_writeexcel/class.writeexcel_worksheet.inc.php";
require_once "plugins/fpdi151/fpdi.php";

class InscriptionActions extends autoInscriptionActions
{
    public function executeList()
    {
        $this->setVar('columns', $this->getArrayExportColumns());
        parent::executeList();
    }

    public function executeStudents()
    {
        // deep trim
        $filters = empty($_GET['filters']) ? array() : array_map(function ($val) {
            return is_array($val) ? array_map('trim', $val) : trim($val);
        }, $_GET['filters']);

        list($query, $boundValues) = $this->_filterStudents($filters);
        $this->setVar(
            'totalStudents',
            mysql::getOne("
                SELECT COUNT(DISTINCT student_name, student_primer_apellido, student_segundo_apellido)
                FROM ($query) subq",
                $boundValues
            )
        );
        $query = str_replace('-- calc --', 'SQL_CALC_FOUND_ROWS', $query);

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
        $offset = $page * 100;
        $query .= " ORDER BY student_name LIMIT 100 OFFSET {$offset}";
        $inscriptions = mysql::getAll($query, $boundValues);
        $numberOfResults = mysql::getOne('SELECT FOUND_ROWS()');
        $this->setVar('inscriptions', $inscriptions);
        $this->setVar('columns', $this->getArrayExportColumns());
        $this->setVar('page', $page);
        $this->setVar('filters', $filters);
        $this->setVar('totalRows', $numberOfResults);

    }

    public function executeExportstudents()
    {
        $filters = empty($_GET['filters']) ? array() : array_map('trim', $_GET['filters']);

        list($query, $boundValues) = $this->_filterStudents($filters);
        $inscripcions = mysql::getAll($query, $boundValues);
        $this->setVar('inscriptions', $inscripcions);
        

        $maxRowsExcel = 3000;
        $selectedColumns = $this->getRequestParameter('columns');
        $columns = $this->getArrayExportColumns();
        $this->setVar('inscripcions', $inscripcions);
        $this->setVar('columns', $columns);
        $this->setVar('selectedColumns', $selectedColumns);
        $this->setVar('maxRowsExcel', $maxRowsExcel);

        set_time_limit(10000);

        $filename = $this->getRequestParameter('filename');
        if (!$filename) {
            if (count($inscripcions) <= $maxRowsExcel) {
                $filename = "export.xls";
            } else {
                $filename = "export.zip";
            }
        } else {
            if (count($inscripcions) <= $maxRowsExcel) {
                $filename .= ".xls";
            } else {
                $filename .= ".zip";
            }
        }
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);


        if (count($inscripcions) <= $maxRowsExcel) {
            $this->getResponse()->setContentType('application/x-msexcel; name="' . $filename . '"');
        } else {
            $this->getResponse()->setContentType('application/zip; name="' . $filename . '"');
        }

        $this->getResponse()->setHttpHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
        $this->setLayout(false);
    }

    /**
     * @var myBackendSummerFun
     */
    private $backend = null;
    private $prova = null;

    public function executeGeneratePdf()
    {
        $user = $this->getUser();
        $user->getCulture();

        $this->getUser()->setCulture($user->getCulture());

        $idInscription = $this->getRequestParameter('id');
        $this->forward404Unless($idInscription);
        $insc = InscriptionPeer::retrieveByPK($idInscription);
        $this->forward404Unless($insc);
        $inscriptions = InscriptionPeer::doSelectInsciptionsByInscriptionCode($insc->getInscriptionCode());

        $i = 0;

        foreach ($inscriptions as $inscription) {
            $i++;

            $pdf[1][$i] = $inscription->getId();
            $this->logMessage('id: ' . $inscription->getId(), 'debug');
            $this->logMessage('pdf1[1][' . $i . ']: ' . $pdf[1][$i], 'debug');
        }

        list($pdfGenerated, $mail) = util::generarPdf($pdf);

        $pdfGenerated->Output('Inscription.pdf', 'I');
		
		die();
    }

    public function executeSendEmail()
    {
                
        $user = $this->getUser();
        $user->getCulture();

        $this->getUser()->setCulture($user->getCulture());
       
        $idInscription = $this->getRequestParameter('id');
        $inscripcio = InscriptionPeer::retrieveByPK($idInscription);
        $centre = $inscripcio->getSummerFunCenter();
        $idCentre = $centre->getId();
        $this->forward404Unless($inscripcio);
        
       
        $pdf[1][1] = $inscripcio->getId();
        $mailsEnviar[1][1] = $inscripcio->getFatherMail();

         
        list($pdfGenerated, $idCentre) = util::generarPdf($pdf);
        util::enviarPdf($pdfGenerated, $mailsEnviar, $idCentre);

        $this->setFlash('notice', 'Inscription email has been sent');
        
        $this->redirect($this->getRequest()->getReferer());        
    }
    
    public function executeMarcarPagat()
    {
        $idInscription = $this->getRequestParameter('id');
        $this->forward404Unless($idInscription);
        $insc = InscriptionPeer::retrieveByPK($idInscription);
        $this->forward404Unless($insc);
        $inscriptions = InscriptionPeer::doSelectInsciptionsByInscriptionCode($insc->getInscriptionCode());

        foreach ($inscriptions as $inscription) {
            if ($inscription->getSplitPayment() == 1 && $inscription->getIsPaid() == 0) {
                $inscription->setIsPaid(1);
            } else if ($inscription->getSplitPayment() == 1 && $inscription->getIsPaid() == 1) {
                $inscription->setIsPaid(2);
            } else {
                $inscription->setIsPaid(2);
            }

            $inscription->save();
        }

        $this->redirect($this->getRequest()->getReferer());
    }

    public function preExecute()
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
        $this->prova = $profile->getId();
        // Si el usuari eś admin ok
        if ($user->hasCredential('administrador')) {
            return;
        } else {
            $this->backend = new myBackendSummerFun($this);
        }
    }

    public function addFiltersCriteria($c)
    {
        $user = $this->getUser();

        // Si el usuari eś admin ok
        if (!$user->hasCredential('administrador')) {
            $c->addJoin(InscriptionPeer::STUDENT_COURSE_INSCRIPTION, CoursePeer::ID);
            $c->addJoin(CoursePeer::SUMMER_FUN_CENTER_ID, SummerFunCenterPeer::ID);
            // JOIN
            $c->addJoin(SummerFunCenterHasProfilePeer::SUMMER_FUN_CENTER_ID, SummerFunCenterPeer::ID);
            // WHERE
            $c->add(SummerFunCenterHasProfilePeer::PROFILE_ID, $user->getProfile()->getId());
        }

        if (isset($this->filters['profesor_id']) && $this->filters['profesor_id'] !== '') {
            $c->addJoin(InscriptionPeer::GRUPO_ID, GrupoPeer::ID);
            $c->addJoin(GrupoHasProfesorPeer::GRUPO_ID, GrupoPeer::ID);
            $c->add(GrupoHasProfesorPeer::PROFESOR_ID, $this->filters['profesor_id']);
            unset($this->filters['profesor_id']);
        }

        $c->add(InscriptionPeer::IS_PAID, 3, Criteria::ALT_NOT_EQUAL);

        parent::addFiltersCriteria($c);

        if (isset($this->filters['week']) && $this->filters['week'] !== '') {
            InscriptionPeer::addCriteriaSearchByWeek($c, $this->filters['week']);
        }

        if (isset($this->filters['centers']) && $this->filters['centers'] !== '') {
            $c->addJoin(InscriptionPeer::STUDENT_COURSE_INSCRIPTION, CoursePeer::ID);
            $c->addJoin(CoursePeer::SUMMER_FUN_CENTER_ID, SummerFunCenterPeer::ID);
            $c->add(SummerFunCenterPeer::ID, $this->filters['centers']);
        }

        $profile = $user->getProfile();
        $this->prova = $profile->getId();

        if (isset($this->filters['csv-export'])) {
            $idList = array();
            $results = InscriptionPeer::doSelect($c);
            foreach ($results as $result) {
                $idList[] = $result->getId();
            }
            $this->getUser()->setAttribute('ids', $idList);
        } else {
            $this->getUser()->setAttribute('ids', 0);
        }
    }

    public function executeExport()
    {
        $selectedColumns = $this->getRequestParameter('columns');
        $columns = $this->getArrayExportColumns();
        $user = $this->getUser();
        $id = $this->getRequestParameter('id');
        $ids = $this->getRequestParameter('ids');

        if (isset($id)) {
            if ($this->getUser()->getAttribute('ids') == 0) {
                $c = new Criteria();

                if (!$user->hasCredential('administrador')) {
                    $profile = $user->getProfile();
                    $c->addJoin(InscriptionPeer::STUDENT_COURSE_INSCRIPTION, CoursePeer::ID);
                    $c->addJoin(CoursePeer::SUMMER_FUN_CENTER_ID, SummerFunCenterPeer::ID);
                    // JOIN
                    $c->addJoin(SummerFunCenterHasProfilePeer::SUMMER_FUN_CENTER_ID, SummerFunCenterPeer::ID);
                    // WHERE
                    $c->add(SummerFunCenterHasProfilePeer::PROFILE_ID, $profile->getId());
                }

                $inscripcions = InscriptionPeer::doSelect($c);
            } else {
                $inscripcions = InscriptionPeer::retrieveByPKs($this->getUser()->getAttribute('ids'));
            }
        }

        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
        set_time_limit(10000);

        $maxRowsExcel = 3000;

        $this->setVar('inscripcions', $inscripcions);
        $this->setVar('ids', $ids);
        $this->setVar('columns', $columns);
        $this->setVar('selectedColumns', $selectedColumns);
        $this->setVar('maxRowsExcel', $maxRowsExcel);

        $filename = $this->getRequestParameter('filename');
        if (!$filename) {
            if (count($inscripcions) <= $maxRowsExcel) {
                $filename = "export.xls";
            } else {
                $filename = "export.zip";
            }
        } else {
            if (count($inscripcions) <= $maxRowsExcel) {
                $filename .= ".xls";
            } else {
                $filename .= ".zip";
            }
        }

        if (count($inscripcions) <= $maxRowsExcel) {
            $this->getResponse()->setContentType('application/x-msexcel; name="' . $filename . '"');
        } else {
            $this->getResponse()->setContentType('application/zip; name="' . $filename . '"');
        }

        $this->getResponse()->setHttpHeader('Content-Disposition', 'inline; filename="' . $filename . '"');
        $this->setLayout(false);
    }


    /**
     * Realiza el export de las fotos de los alumnos.
     */

    public function executeExportPicture()
    {
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
        set_time_limit(10000);

        $this->processSort();
        $this->processFilters();

        $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/inscription/filters');

        $c = new Criteria();
        $this->addSortCriteria($c);
        $this->addFiltersCriteria($c);

        $inscriptions = InscriptionPeer::doSelect($c);

        $baseDir = sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . sfConfig::get('sf_web_dir_name') . DIRECTORY_SEPARATOR . sfConfig::get('sf_upload_dir_name') . DIRECTORY_SEPARATOR . 'summer-fun' . DIRECTORY_SEPARATOR . 'student' . DIRECTORY_SEPARATOR;
        $tmpDir = $baseDir . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

        $arrayImages = array();
        foreach ($inscriptions as $inscription) {
            $image = $baseDir . $inscription->getStudentPhoto();
            $extension = strtolower(pathinfo($inscription->getStudentPhoto(), PATHINFO_EXTENSION));
            if (!in_array($image, $arrayImages) && file_exists($image)) {
                $nombre = $inscription->getStudentName() . '-' . $inscription->getStudentPrimerApellido() . '-' . $inscription->getStudentSegundoApellido();
                $arrayImages[$this->createSlug($nombre) . '.' . $extension] = $image;
            }
        }

        $zip = new ZipArchive();
        $zipName = uniqid() . '.zip';
        if ($zip->open($tmpDir . $zipName, ZIPARCHIVE::CREATE) !== true) {
            $this->forward404('You are not allow to visit this page');
        }

        foreach ($arrayImages as $key => $image) {
            $zip->addFile($image, $key);
        }

        $zip->close();

        $this->getResponse()->clearHttpHeaders();
        $this->getResponse()->setHttpHeader('Pragma: public', true);
        $this->getResponse()->setContentType('application/octet-stream');
        $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment; filename="' . $this->getContext()->getI18N()->__('alumnes') . '-' . date('d/m/Y') . '.zip"');
        $this->getResponse()->sendHttpHeaders();
        if (count($arrayImages)) {
            $this->getResponse()->setContent(readfile($tmpDir . $zipName));
        }

        return sfView::NONE;
    }

    public function executeRotateImage()
    {
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException('Invalid request');
        }

        $response['status'] = 'KO';

        $this->forward404Unless($id = $this->getRequestParameter('id'));
        $inscription = InscriptionPeer::retrieveByPK($id);
        $this->forward404Unless($inscription);

        $degrees = $this->getRequestParameter('degrees');
        if (!is_numeric($degrees)) {
            $degrees = 90;
        }

        if ($inscription->getStudentPhoto() && file_exists(sfConfig::get('app_inscripcion_imagen_directorio') . $inscription->getStudentPhoto())) {
            $imagePath = sfConfig::get('app_inscripcion_imagen_directorio') . $inscription->getStudentPhoto();
            $extension = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
            $newImage = uniqid() . '.' . $extension;
            $newImagePath = sfConfig::get('app_inscripcion_imagen_directorio') . $newImage;

            if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
                $source = imagecreatefromjpeg($imagePath);
            } elseif (strtolower($extension) == 'png') {
                $source = imagecreatefrompng($imagePath);
            } else {
                $source = imagecreatefromgif($imagePath);
            }

            $rotate = imagerotate($source, $degrees, 0);

            if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
                imagejpeg($rotate, $newImagePath, 90);
            } elseif (strtolower($extension) == 'png') {
                imagepng($rotate, $newImagePath, 9);
            } elseif (strtolower($extension) == 'gif') {
                imagegif($rotate, $newImagePath);
            }

            if ($inscription->getInscriptionCode() && $inscription->getInscriptionCode() != 0) {
                $inscriptions = InscriptionPeer::doSelectInsciptionsByInscriptionCode($inscription->getInscriptionCode());
                foreach ($inscriptions as $insc) {
                    $insc->setStudentPhoto($newImage);
                    $insc->save();
                }
            }

            imagedestroy($source);
            imagedestroy($rotate);

            $response['status'] = 'OK';
            $response['image'] = '/' . sfConfig::get('sf_upload_dir_name') . '/summer-fun/student/' . $newImage;
        }

        return $this->renderText(json_encode($response));
    }

    public function executeUploadImage()
    {
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException('Invalid request');
        }

        $this->forward404Unless($id = $this->getRequestParameter('id'));
        $inscription = InscriptionPeer::retrieveByPK($id);
        $this->forward404Unless($inscription);

        $response['status'] = 'KO';
        $response['message'] = $this->getContext()->getI18N()->__('error_subiendo_imagen');

        if ($this->getRequest()->hasFiles()) {
            foreach ($this->getRequest()->getFileNames() as $uploadedFile) {
                if ($uploadedFile == 'studentPhoto') {
                    $fileName = $this->getRequest()->getFileName($uploadedFile);
                    //$fileSize  = $this->getRequest()->getFileSize($uploadedFile);
                    //$fileType  = $this->getRequest()->getFileType($uploadedFile);
                    //$fileError = $this->getRequest()->hasFileError($uploadedFile);
                    $uploadDir = sfConfig::get('app_inscripcion_imagen_directorio');

                    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                    $imageName = uniqid() . '.' . $extension;

                    if (strtolower($extension) != 'jpg' && strtolower($extension) != 'jpeg' && strtolower($extension) == 'png' && strtolower($extension) == 'gif') {
                        $response['message'] = $this->getContext()->getI18N()->__('error_tipo_fichero');
                        break;
                    }

                    if ($request->moveFile($uploadedFile, $uploadDir . $imageName)) {
                        if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
                            $imagen = imagecreatefromjpeg($uploadDir . $imageName);
                        } elseif (strtolower($extension) == 'png') {
                            $imagen = imagecreatefrompng($uploadDir . $imageName);
                        } else {
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
                        } elseif ($height < $minHeight) {
                            $response['message'] = $this->getContext()->getI18N()->__('imagen_alto_minimo_menor', array('%min_height%' => $minHeight));
                        } else {
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
                            $thumbnailImagen = imagecreatetruecolor($minWidth, $minHeight);
                            imagecopyresampled($thumbnailImagen, $imagen, $dx, $dy, 0, 0, $thumbnailWidth, $thumbnailHeight, $width, $height);

                            if (strtolower($extension) == 'jpg' || strtolower($extension) == 'jpeg') {
                                imagejpeg($thumbnailImagen, $uploadDir . $imageName, 90);
                            } elseif (strtolower($extension) == 'png') {
                                imagepng($thumbnailImagen, $uploadDir . $imageName, 9);
                            } elseif (strtolower($extension) == 'gif') {
                                imagegif($thumbnailImagen, $uploadDir . $imageName);
                            }

                            imagedestroy($imagen);
                            imagedestroy($thumbnailImagen);

                            if ($inscription->getStudentPhoto() && file_exists($uploadDir . $inscription->getStudentPhoto())) {
                                unlink($uploadDir . $inscription->getStudentPhoto());
                            }

                            $inscriptions = InscriptionPeer::doSelectInsciptionsByInscriptionCode($inscription->getInscriptionCode());
                            foreach ($inscriptions as $insc) {
                                $insc->setStudentPhoto($imageName);
                                $insc->save();
                            }

                            $response['status'] = 'OK';
                            $response['message'] = '/' . sfConfig::get('sf_upload_dir_name') . '/summer-fun/student/' . $imageName;
                        }
                    }
                }
            }
        }

        return $this->renderText(json_encode($response));
    }

    /**
     * Function used to create a slug associated to an "ugly" string.
     *
     * @param string $string the string to transform.
     *
     * @return string the resulting slug.
     */
    private function createSlug($string)
    {

        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', '/' => '-', ' ' => '-'
        );

        // -- Remove duplicated spaces
        $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

        // -- Returns the slug
        return strtolower(strtr($string, $table));
    }

    protected function saveInscription($inscription)
    {
        if (!$inscription->getId()) {
            //fix code incription use current year
            $codeInscription = InscriptionPeer::getLastCodeInscription(); //busquem l'ultim nº d'inscripció
            $anyo = date('y');
            if ($codeInscription == 0 || strlen($codeInscription) < 2 || substr($codeInscription, 0, 2) != $anyo) {
                $codeInscription = $anyo . "0000";
            }
            $inscription->setInscriptionCode($codeInscription + 1);
        }

        parent::saveInscription($inscription);

        $course = CoursePeer::retrieveByPK($inscription->getStudentCourseInscription());
        if ($course) {
            $totalPrice = $course->getPrice();
            $totalDiscount = 0;

            $services = array();
            foreach ($inscription->getInscriptionServiceSchedules() as $inscriptionServiceSchedule) {
                $serviceSchedule = $inscriptionServiceSchedule->getServiceSchedule();
                $serviceSchedule->setCulture($inscription->getCulture());
                $service = $serviceSchedule->getService();
                $service->setCulture($inscription->getCulture());

                if (!in_array($service->getId(), $services)) {
                    $totalPrice += $service->getPrice();
                    array_push($services, $service->getId());
                }
            }

            $totalDiscount = 0;
            if ($inscription->getDiscountPercent() > 0) {
                $totalDiscount = $totalPrice * ($inscription->getDiscountPercent() / 100);
                $totalPrice = $totalPrice - $totalDiscount;
            }

            if ($totalPrice != $inscription->getPrice()) {
                $inscription->setPrice($totalPrice);
                $inscription->setDiscount($totalDiscount);

                $inscription->save();
            }
        }

        // Actualizamos los registros relacionados. Si se han actualizado datos (ej. datos del padre, telefonos, alergías del alumno) hay que actualizar también las inscripciones relacionadas del mismo alumno.

        if ($inscription->getInscriptionCode() && $inscription->getInscriptionCode() != 0) {
            $inscriptions = InscriptionPeer::doSelectInsciptionsByInscriptionCode($inscription->getInscriptionCode());
            /** @var Inscription $insc */
            foreach ($inscriptions as $insc) {
                if ($insc->getId() != $inscription->getId()) {
                    $insc->setBeca($inscription->getBeca());
                    $insc->setStudentAllergiesDescription($inscription->getStudentAllergiesDescription());
                    $insc->setStudentAllergies($inscription->getStudentAllergies());
                    $insc->setSplitPayment($inscription->getSplitPayment());
                    $insc->setKidsAndUsCenter($inscription->getKidsAndUsCenter());
                    $insc->setMethodPayment($inscription->getMethodPayment());
                    $insc->setStudentNumTarjetaSanitaria($inscription->getStudentNumTarjetaSanitaria());
                    $insc->setStudentTarjetaSanitariaCompanyia($inscription->getStudentTarjetaSanitariaCompanyia());
                    $insc->setIsStudentKidAndUs($inscription->getIsStudentKidAndUs());
                    $insc->setStudentDisabilityLevel($inscription->getStudentDisabilityLevel());
                    $insc->setStudentComments($inscription->getStudentComments());
                    $insc->setIsStudentDisability($inscription->getIsStudentDisability());
                    $insc->setStudentDisability($inscription->getStudentDisability());
                    $insc->setStudentFriends($inscription->getStudentFriends());
                    $insc->setStudentSchoolYear($inscription->getStudentSchoolYear());
                    $insc->setStudentName($inscription->getStudentName());
                    $insc->setStudentPrimerApellido($inscription->getStudentPrimerApellido());
                    $insc->setStudentSegundoApellido($inscription->getStudentSegundoApellido());
                    $insc->setStudentBirthDate($inscription->getStudentBirthDate());
                    $insc->setStudentAddress($inscription->getStudentAddress());
                    $insc->setStudentZip($inscription->getStudentZip());
                    $insc->setStudentCity($inscription->getStudentCity());
                    $insc->setFatherName($inscription->getFatherName());
                    $insc->setFatherPrimerApellido($inscription->getFatherPrimerApellido());
                    $insc->setFatherSegundoApellido($inscription->getFatherSegundoApellido());
                    $insc->setFatherPhone($inscription->getFatherPhone());
                    $insc->setFatherDni($inscription->getFatherDni());
                    $insc->setFatherMail($inscription->getFatherMail());
                    $insc->setMotherName($inscription->getMotherName());
                    $insc->setMotherPrimerApellido($inscription->getMotherPrimerApellido());
                    $insc->setMotherSegundoApellido($inscription->getMotherSegundoApellido());
                    $insc->setMotherPhone($inscription->getMotherPhone());
                    $insc->setMotherDni($inscription->getMotherDni());
                    $insc->setMotherMail($inscription->getMotherMail());
                    $insc->setStudentExcursion($inscription->getStudentExcursion());
                    $insc->save();
                }
            }
        }
    }


    private function getArrayExportColumns()
    {
        $translator = $this->getContext()->getI18N();
        $columns = array();
        $columns[] = $translator->__('Data inscripció');
        $columns[] = $translator->__('Codi inscripció');
        $columns[] = $translator->__('Activitat');
        $columns[] = $translator->__('Preu');
        $columns[] = $translator->__('Estat inscripció');
        $columns[] = $translator->__('Estat pagament');
        $columns[] = $translator->__('Modalitat de pagament');
        $columns[] = $translator->__('Pagament fraccionat');
        $columns[8] = $translator->__('Nom infant');
        $columns[9] = $translator->__('Cognom 1 infant');
        $columns[10] = $translator->__('Cognom 2 infant');
        $columns[] = $translator->__('Data de naixement');
        $columns[] = $translator->__('Adreça');
        $columns[] = $translator->__('Codi postal');
        $columns[] = $translator->__('Població');
        $columns[] = $translator->__('registration.trans168');
        $columns[] = $translator->__('Amics');
        $columns[] = $translator->__('Té alguna discapacitat?');
        $columns[] = $translator->__('Grau discapacitat');
        $columns[] = $translator->__('Descripció discapacitat');
        $columns[] = $translator->__('Té alguna al·lèrgia?');
        $columns[] = $translator->__('Descripció al·lèrgia');
        $columns[] = $translator->__('registration.vaccinated');
        $columns[] = $translator->__('S\'acull a una beca?');
        $columns[] = $translator->__('origin_kids_and_us_center');
        $columns[] = $translator->__('summer_fun_center');
        $columns[] = $translator->__('school_year');
        $columns[] = $translator->__('Alumne Kids&Us');
        $columns[] = $translator->__('Nom pare/mare/tutor');
        $columns[] = $translator->__('Cognom 1 pare/mare/tutor');
        $columns[] = $translator->__('Cognom 2 pare/mare/tutor');
        $columns[] = $translator->__('Telèfon tutor/a');
        $columns[] = $translator->__('DNI pare/mare/tutor');
        $columns[] = $translator->__('E-mail pare/mare/tutor');
        $columns[] = $translator->__('E-mail principal?');
        $columns[] = $translator->__('Nom pare/mare/tutor');
        $columns[] = $translator->__('Cognom 1 pare/mare/tutor');
        $columns[] = $translator->__('Cognom 2 pare/mare/tutor');
        $columns[] = $translator->__('Telèfon tutor/a');
        $columns[] = $translator->__('DNI pare/mare/tutor ');
        $columns[] = $translator->__('E-mail pare/mare/tutor');
        $columns[] = $translator->__('E-mail principal?');
        $columns[] = $translator->__('Companyia');
        $columns[] = $translator->__('Núm. targeta sanitària');
        $columns[] = $translator->__('Grup');
        $columns[] = $translator->__('Professors');
        $columns[] = substr($translator->__('Pregunta personalitzada:'), 0, -1);
        $columns[] = $translator->__('Serveis addicionals');
        $columns[] = substr($translator->__('Altres aspectes a tenir en compte:'), 0, -1);
        $columns[] = substr($translator->__('Centre Inscripció:'), 0, -1);

        return $columns;
    }


    public function executeGenerateCarnets()
    {
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
        set_time_limit(10000);

        $pdf = new FPDI();
        $pdf->setPageUnit('mm');
        $pdf->SetMargins(0, 0, -1, false);
        $pdf->SetPrintHeader(false);
        $pdf->setPageOrientation('P', false, 0);
        $pdf->setFont('Arial', '', 10);

        $template = realpath(sfConfig::get('sf_web_dir') . DIRECTORY_SEPARATOR . "carnets" . DIRECTORY_SEPARATOR . "template.pdf");

        $pdf->setSourceFile($template);
        $tplIdx = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tplIdx);

        $this->processSort();
        $this->processFilters();

        $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/inscription/filters');

        $c = new Criteria();
        $this->addSortCriteria($c);
        $this->addFiltersCriteria($c);

        $inscriptions = InscriptionPeer::doSelect($c);

        $baseImagesPath = sfConfig::get('sf_upload_dir') . DIRECTORY_SEPARATOR . 'summer-fun' . DIRECTORY_SEPARATOR . 'student';
        $i = 0;
        $fotos = array();
        foreach ($inscriptions as $inscription) {
            if (in_array($inscription->getStudentPhoto(), $fotos)) {
                continue;
            }

            if ($i % 8 == 0) {
                $pdf->AddPage('P', array(($size['h']), ($size['w'])));
                $pdf->useTemplate($tplIdx, 0, 0);
            }

            $posXImage = 0;
            $posYImage = 0;
            $posXName = 0;
            $posXName = 0;

            switch ($i % 8) {
                case 0:
                    $posXImage = 64.382;
                    $posYImage = 26.635;
                    $posXName = 41.5;
                    $posYName = 73;
                    break;
                case 1:
                    $posXImage = 161.043;
                    $posYImage = 26.635;
                    $posXName = 138;
                    $posYName = 73;
                    break;
                case 2:
                    $posXImage = 64.382;
                    $posYImage = 90.664;
                    $posXName = 41.5;
                    $posYName = 137.039;
                    break;
                case 3:
                    $posXImage = 161.043;
                    $posYImage = 90.664;
                    $posXName = 138;
                    $posYName = 137.039;
                    break;
                case 4:
                    $posXImage = 64.382;
                    $posYImage = 153.811;
                    $posXName = 41.5;
                    $posYName = 200.715;
                    break;
                case 5:
                    $posXImage = 161.043;
                    $posYImage = 153.811;
                    $posXName = 138;
                    $posYName = 200.715;
                    break;
                case 6:
                    $posXImage = 64.382;
                    $posYImage = 215.9;
                    $posXName = 41.5;
                    $posYName = 262.98;
                    break;
                case 7:
                    $posXImage = 161.043;
                    $posYImage = 215.9;
                    $posXName = 138;
                    $posYName = 262.98;
                    break;
            }

            if ($inscription->getStudentPhoto()) {
                $studentPhoto = $baseImagesPath . DIRECTORY_SEPARATOR . $inscription->getStudentPhoto();

                if (file_exists($studentPhoto)) {
                    $pdf->Image($studentPhoto, $posXImage, $posYImage, 35.101);
                }

                $fotos[] = $inscription->getStudentPhoto();
            }

            $pdf->setXY($posXName, $posYName);
            $pdf->Cell(58.854, 5, strtoupper($inscription->getFullStudentName()), 0, 0, 'R');

            $i++;
        }

        $pdf->Output('carnets.pdf', 'I');

        return sfView::NONE;
    }

    public function executePdfFicha()
    {
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
        set_time_limit(10000);

        $pdf = new TCPDFFichaListado();
        $pdf->SetMargins(5, 35, 5);
        $pdf->setPageOrientation('L');
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont(TCPDFFicha::FONT, TCPDFFicha::FONT_STYLE, TCPDFFicha::FONT_SIZE);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        $this->processSort();
        $this->processFilters();

        $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/inscription/filters');

        $c = new Criteria();
        $this->addSortCriteria($c);
        $this->addFiltersCriteria($c);

        $inscriptions = InscriptionPeer::doSelect($c);
        $imageY = 0;
        $cont = 0;
        $imagesPath = sfConfig::get('app_inscripcion_imagen_directorio');

        foreach ($inscriptions as $inscription) {
            $course = CoursePeer::retrieveByPKWithI18n($inscription->getStudentCourseInscription());
            if ($cont != 7) {
                $imageY += 25;
            } else {
                $cont = 0;
                $imageY = 25;
                $pdf->AddPage();
            }

            if ($inscription->getStudentPhoto()) {
                $image = $imagesPath . $inscription->getStudentPhoto();
                if (file_exists($image)) {
                    $pdf->Image($image, 5, $imageY, 18);
                }
            }

            $y = $imageY;
            $pdf->setXY(25, $imageY + 2);
            $pdf->Cell($pdf->headersWidth[0] - 20, TCPDFFicha::FONT_HEIGHT, $inscription->getFullStudentName(), 0, 0, 'L', 0, '', 1, false, 'C', 'M');

            $pdf->setXY(25, $imageY + 9);
            $pdf->Cell($pdf->headersWidth[0] - 20, TCPDFFicha::FONT_HEIGHT, $course->getWeek(), 0, 0, 'L', 0, '', 1, false, 'C', 'M');

            $pdf->setXY(25, $imageY + 14);
            $pdf->Cell($pdf->headersWidth[0] - 20, TCPDFFicha::FONT_HEIGHT, $course->getSchedule(), 0, 0, 'L', 0, '', 1, false, 'C', 'M');

            $pdf->setXY(5 + $pdf->headersWidth[0], $y - 4);
            $pdf->MultiCell($pdf->headersWidth[1], 5, $inscription->getStudentAllergiesDescription(), 0, 'C', 0, 1, '', '', true);

            $pdf->setXY(5 + $pdf->headersWidth[0] + $pdf->headersWidth[1], $y);
            $pdf->Cell($pdf->headersWidth[2], TCPDFFicha::FONT_HEIGHT, $inscription->getGrupo() ? $inscription->getGrupo() : '', 0, 0, 'C', 0, '', 1, false, 'C', 'M');

            $pdf->setXY(5 + $pdf->headersWidth[0] + $pdf->headersWidth[1] + $pdf->headersWidth[2], $y);
            $pdf->MultiCell($pdf->headersWidth[3], 5, $inscription->getTextServices(), 0, '', 0, 1, '', '', true);

            $pdf->setXY(5 + $pdf->headersWidth[0] + $pdf->headersWidth[1] + $pdf->headersWidth[2] + $pdf->headersWidth[3], $y);
            $phones = '';

            if ($inscription->getFatherPhone()) {
                $phones = $inscription->getFatherPhone();
            }

            if ($inscription->getMotherPhone()) {
                if ($phones != '') {
                    $phones .= ' / ';
                }
                $phones .= $inscription->getMotherPhone();
            }

            $pdf->Cell($pdf->headersWidth[4], TCPDFFicha::FONT_HEIGHT, $phones, 0, 0, 'C', 0, '', 1, false, 'C', 'M');
            $pdf->Ln(17);
            $pdf->Cell(0, TCPDFFicha::FONT_HEIGHT, '', array('B' => array('dash' => 0, 'width' => 0)), 0, 'C', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(7);
            $cont++;
        }

        $pdf->Output('fichas.pdf', 'I');
		die();
    }


    public function executePdfFichaComplete()
    {
        error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
        set_time_limit(10000);

        $pdf = new TCPDFFicha();
        $pdf->SetMargins(10, 35, 10);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont(TCPDFFicha::FONT, TCPDFFicha::FONT_STYLE, TCPDFFicha::FONT_SIZE);

        $this->processSort();
        $this->processFilters();

        $this->filters = $this->getUser()->getAttributeHolder()->getAll('sf_admin/inscription/filters');

      	/*$c = new Criteria();
        $this->addSortCriteria($c);
        $this->addFiltersCriteria($c);

        $inscriptions = InscriptionPeer::doSelect($c);*/
		$idInscription = $this->getRequestParameter('id');
       	$this->forward404Unless($idInscription);
       	$insc = InscriptionPeer::retrieveByPK($idInscription);
       	$this->forward404Unless($insc);
       	$inscriptions = InscriptionPeer::doSelectInsciptionsByInscriptionCode($insc->getInscriptionCode());
        $maxWidth = 140;

        foreach ($inscriptions as $inscription) {
            $pdf->AddPage();

            if ($inscription->getStudentPhoto()) {
                $image = sfConfig::get('app_inscripcion_imagen_directorio') . $inscription->getStudentPhoto();
                if (file_exists($image)) {
                    $pdf->Image($image, 160, 35, 38);
                }
            }

            // SEGUNDA LINEA. NOMBRE
            $text = sfContext::getInstance()->getI18N()->__('Nom complet:');
            $width = $pdf->getTextWidth($text);
            $pdf->Cell($width, TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($maxWidth - $width, TCPDFFicha::FONT_HEIGHT, $inscription->getFullStudentName(), array('B' => array('dash' => 1, 'width' => 0, 'cap' => 'butt', 'join' => 'miter')), 0, 'L', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(7);

            // TERCERA LINEA. FECHA DE NACIMIENTO
            $text = sfContext::getInstance()->getI18N()->__('Data de naixement:');
            $width = $pdf->getTextWidth($text);
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($maxWidth - $width, TCPDFFicha::FONT_HEIGHT, date("d-m-Y", strtotime($inscription->getStudentBirthDate())), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(7);

            // TERCERA LINEA. GRUPO
            $text = sfContext::getInstance()->getI18N()->__('Grup:');
            $width = $pdf->getTextWidth($text);
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($maxWidth - $width, TCPDFFicha::FONT_HEIGHT, $inscription->getGrupo() ? $inscription->getGrupo() : '', array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(7);

            // CUARTA LINEA. PROFESOR
            $text = sfContext::getInstance()->getI18N()->__('Professors:');
            $width = $pdf->getTextWidth($text);
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $i = 0;
            if ($inscription->getGrupo()) {
                $teachers = $inscription->getGrupo()->getGrupoHasProfesors();
                foreach ($teachers as $teacher) {
                    if ($i > 0) {
                        $pdf->Ln(7);
                        $pdf->SetX(30);
                    }
                    $pdf->Cell($maxWidth - $width, TCPDFFicha::FONT_HEIGHT, $teacher->getProfesor()->getNombre(), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');
                    $i++;
                }
            }
            if ($i == 0) {
                $pdf->Cell($maxWidth - $width, TCPDFFicha::FONT_HEIGHT, '', array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');
            }
            $pdf->Ln(7);

            // QUINTA LINEA. HORARIO
            $curso = CoursePeer::getCourseByInscrptionId($inscription->getId());
            $text = sfContext::getInstance()->getI18N()->__('Horari:');
            $width = $pdf->getTextWidth($text);
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($maxWidth - $width, TCPDFFicha::FONT_HEIGHT, $curso->getSchedule(), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(7);


            // SEXTA LINEA - Alergias
            $text = sfContext::getInstance()->getI18N()->__('Alergias:');
            $width = $pdf->getTextWidth($text);
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell($maxWidth - $width, TCPDFFicha::FONT_HEIGHT, $inscription->getStudentAllergiesDescription(), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(7);


            // SEPTIMA LINEA. SERVICIO DE ACOGIA
            $text = sfContext::getInstance()->getI18N()->__('Serveis:');
            $width = $pdf->getTextWidth($text);
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, TCPDFFicha::FONT_HEIGHT, $inscription->getTextServices(), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(7);


            // LINEA 8. NOMBRE PADRE Y TELEFONO
            $text = sfContext::getInstance()->getI18N()->__('Nom i cognoms pare/mare/tutor:');
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(80, TCPDFFicha::FONT_HEIGHT, $inscription->getFullFatherName(), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');

            $text = sfContext::getInstance()->getI18N()->__('Mòbil:');
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, TCPDFFicha::FONT_HEIGHT, $inscription->getFatherPhone(), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');
            $pdf->Ln(7);

            // LINEA 9. NOMBRE MADRE Y TELEFONO
            $text = sfContext::getInstance()->getI18N()->__('Nom i cognoms pare/mare/tutor:');
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(80, TCPDFFicha::FONT_HEIGHT, $inscription->getFullMotherName(), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');

            $text = sfContext::getInstance()->getI18N()->__('Mòbil:');
            $pdf->Cell($pdf->getTextWidth($text), TCPDFFicha::FONT_HEIGHT, $text, 0, 0, 'L', 0, '', 0, false, 'M', 'B');
            $pdf->Cell(0, TCPDFFicha::FONT_HEIGHT, $inscription->getMotherPhone(), array('B' => array('dash' => 1, 'width' => 0)), 0, 'L', 0, '', 0, false, 'C', 'M');
        }

        $pdf->Output('fichas.pdf', 'I');
		die();
    }

    /**
     * Ajax. Devuelve ids de los horarios de servicios de un centro. Ej. 1|2
     * @return sfView
     */

    public function executeServiceSchedulesCourse()
    {
        $course = CoursePeer::retrieveByPKWithI18n($this->getRequestParameter('id'));
        $type = $this->getRequestParameter('type');
        $result = '';

        if ($course) {
            foreach ($course->getCourseHasServicess() as $courseService) {
                foreach ($courseService->getService()->getServiceSchedules() as $schedule) {
                    if ($type == 1) {
                        $result .= $schedule->getId() . '|';
                    } else {
                        $result .= '<option value="' . $schedule->getId() . '">' . $schedule . '</option>';
                    }
                }
            }

            if ($result != '' && $type == 1) {
                $result = substr($result, 0, -1);
            }
        }

        return $this->renderText($result);
    }


    private function _filterStudents($filters)
    {
        $boundValues = array();
        $query = "
            SELECT    -- calc -- 
                      inscription.*
                      , sfci18n.title AS centre_title
                      , kauc.name AS kids_centre_title
                      , course.starts_at 
                      , course.ends_at 
                      , syi18n.name as school_year
                    
            FROM      inscription
            
            LEFT JOIN summer_fun_center_i18n sfci18n ON summer_fun_center_id = sfci18n.id AND sfci18n.culture = 'ca'
            LEFT JOIN school_year_i18n syi18n on school_year_id = syi18n.id AND syi18n.culture = 'ca'
            LEFT JOIN kids_and_us_center kauc ON kids_and_us_center_id = kauc.id 
            LEFT JOIN course ON course.id = student_course_inscription 
            
            WHERE     1";

        if (empty($filters)) return array($query . " AND student_name <> ''", $boundValues);

        if (!empty($filters['name'])) {
            $parts = explode(' ', $filters['name']); // try to parse 'Name Middle SecondName'
            $innerSearch = array();
            foreach ($parts as $k => $part) {
                $innerSearch[] = "(
                        student_name LIKE :name{$k}
                        OR student_primer_apellido LIKE  :name{$k} 
                        OR student_segundo_apellido LIKE :name{$k}
                    )";
                $boundValues["name{$k}"] = '%' . $part . '%';
            }
            if ($innerSearch) {
                $query .= ' AND ' . implode(' AND ', $innerSearch);
            }
        }

        if (!empty($filters['father_name'])) {
            $parts = explode(' ', $filters['father_name']); // try to parse 'Name Middle SecondName'
            $innerSearch = array();
            foreach ($parts as $k => $part) {
                $innerSearch[] = "(
                        father_name LIKE :father_name{$k}
                        OR father_primer_apellido LIKE :father_name{$k} 
                        OR father_primer_apellido LIKE :father_name{$k}
                    )";
                $boundValues["father_name{$k}"] = '%' . $part . '%';
            }
            if ($innerSearch) {
                $query .= ' AND ' . implode(' AND ', $innerSearch);
            }
        }

        if (!empty($filters['mother_name'])) {
            $parts = explode(' ', $filters['mother_name']); // try to parse 'Name Middle SecondName'
            $innerSearch = array();
            foreach ($parts as $k => $part) {
                $innerSearch[] = "(
                        mother_name LIKE :mother_name{$k}
                        OR mother_primer_apellido LIKE :mother_name{$k} 
                        OR mother_primer_apellido LIKE :mother_name{$k}
                    )";
                $boundValues["mother_name{$k}"] = '%' . $part . '%';
            }
            if ($innerSearch) {
                $query .= ' AND ' . implode(' AND ', $innerSearch);
            }
        }
        if (!empty($filters['parent_email'])) {
            $query .= " AND ( mother_mail LIKE :parent_email OR father_mail LIKE :parent_email) ";
            $boundValues["parent_email"] = '%' . $filters['parent_email'] . '%';
        }

        if (!empty($filters['dni'])) {
            $query .= ' AND ( father_dni LIKE :dni OR mother_dni LIKE :dni )';
            $boundValues['dni'] = '%' . $filters['dni'] . '%';
        }

        if (!empty($filters['inscription_code'])) {
            $query .= ' AND inscription_code = :inscription_code';
            $boundValues['inscription_code'] = $filters['inscription_code'];
        }

        if (!empty($filters['course_id'])) {
            $query .= ' AND course.id = :course_id';
            $boundValues['course_id'] = $filters['course_id'];
        }

        if (!empty($filters['center_id'])) {
            $query .= ' AND inscription.summer_fun_center_id = :center_id';
            $boundValues['center_id'] = $filters['center_id'];
        }

        if (!empty($filters['professor_id'])) {
            $query .= ' AND grupo_id IN (select grupo_id from grupo_has_profesor where profesor_id = :professor_id)';
            $boundValues['professor_id'] = $filters['professor_id'];
        }

        if (isset($filters['is_paid']) && ($filters['is_paid'] === '1' || $filters['is_paid'] === '0')) {
            if ($filters['is_paid'] === '1') {
                $query .= ' AND is_paid IS NOT NULL';
            } elseif ($filters['is_paid'] === '“') {
                $query .= ' AND is_paid IS NULL';
            }
        }

        if (!empty($filters['inscription_date']['from'])) {
            $query .= ' AND inscription.created_at >= :created_from';
            $boundValues['created_from'] = date('Y-m-d', strtotime($filters['inscription_date']['from']));
        }


        if (!empty($filters['inscription_date']['to'])) {
            $query .= ' AND inscription.created_at <= :created_to';
            $boundValues['created_to'] = date('Y-m-d', strtotime($filters['inscription_date']['to']));
        }

        return array($query, $boundValues);
    }
}

