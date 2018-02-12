<?php

/**
 * Subclass for representing a row from the 'inscription' table.
 *
 *
 *
 * @package lib.model.inscriptions
 */
class Inscription extends BaseInscription
{
	public function getFullStudentName()
	{
		$fullName = $this->student_name;

		if (trim($this->student_primer_apellido)) {
			$fullName = $fullName . " " . trim($this->student_primer_apellido);
		}

		if (trim($this->student_segundo_apellido)) {
			$fullName = $fullName . " " . trim($this->student_segundo_apellido);
		}

		return $fullName;
	}

	public function getFullFatherName()
	{
		$fullName = $this->father_name;

		if (trim($this->father_primer_apellido)) {
			$fullName = $fullName . " " . trim($this->father_primer_apellido);
		}

		if (trim($this->father_segundo_apellido)) {
			$fullName = $fullName . " " . trim($this->father_segundo_apellido);
		}

		return $fullName;
	}

	public function getFullMotherName()
	{
		$fullName = $this->mother_name;

		if (trim($this->mother_primer_apellido)) {
			$fullName = $fullName . " " . trim($this->mother_primer_apellido);
		}

		if (trim($this->mother_segundo_apellido)) {
			$fullName = $fullName . " " . trim($this->mother_segundo_apellido);
		}

		return $fullName;
	}

    public function getPendingAmount()
    {
        return $this->getPrice() - $this->getAmountBeca() - $this->getAmountFirstPayment() - $this->getAmountSecondPayment();
    }

    public function getPendingAmountFromAllInscriptions()
    {
        $inscriptions = InscriptionPeer::retrieveByInscriptionNum($this->getInscriptionNum());
        $amount = 0;
        foreach ($inscriptions as $inscription)
        {
            $amount += $inscription->getPendingAmount();
        }

        return $amount;
    }

    public function getTextServices()
    {
        $label = '';

        foreach ($this->getInscriptionServiceSchedules() as $inscriptionServiceSchedule)
        {
            $schedule = $inscriptionServiceSchedule->getServiceSchedule();
            if ($schedule) {
                $schedule->setCulture(sfContext::getInstance()->getUser()->getCulture());

                $service = $schedule->getService();
                $service->setCulture(sfContext::getInstance()->getUser()->getCulture());

                $label .= $service->getName() . ' (' . $schedule->getName() . ') / ';
            }
            elseif (isset($service)) {
                $label .= $service->getName();
            }
        }

        if ($label != '') {
            $label = substr($label, 0, -3);
        }

        return $label;
    }

    public static function getListForGroup($search)
    {
        if (!isset($_GET['term'])) return array();

        # !!!IMPORTANT!!! Select clause mirrors that of Inscription::getAssignedToGrupo()
        $inscriptions = mysql::getAll("
            SELECT    inscription.id
                    , inscription_code
                    , student_name
                    , student_primer_apellido
                    , student_segundo_apellido
                    , father_dni 
                    , grupo_id
                    , course.starts_at 
                    , course.ends_at 
                    -- , course_names.schedule 
                    
            FROM      inscription
            
            LEFT JOIN course ON course.id = student_course_inscription 
            -- LEFT JOIN course_names ON course_names.id = student_course_inscription 
            
            WHERE      student_name LIKE :search
                    OR student_primer_apellido LIKE  :search 
                    OR student_segundo_apellido LIKE :search 
                    OR inscription_code LIKE :search 
                    OR father_dni LIKE :search 
            LIMIT 200",
            array('search' => '%' . $search . '%')
        );

        return self::_decorateInscriptionText($inscriptions, true);
    }

    public static function getAssignedToGrupo($grupo_id)
    {
        if (empty($grupo_id)) return array();

        # !!!IMPORTANT!!! Select clause mirrors that of Inscription::getListForGroup()
        $inscriptions = mysql::getAll("
            SELECT    inscription.id
                    , inscription_code
                    , student_name
                    , student_primer_apellido
                    , student_segundo_apellido
                    , father_dni 
                    , grupo_id 
                    , course.starts_at 
                    , course.ends_at 
                    -- , course_names.schedule 
                    
            FROM      inscription
            
            LEFT JOIN course ON course.id = student_course_inscription 
            -- LEFT JOIN course_names ON course_names.id = student_course_inscription 
            
            WHERE      grupo_id = ?",
            $grupo_id
        );

        return self::_decorateInscriptionText($inscriptions, false);
    }

    private static function _decorateInscriptionText($inscriptions, $denote_assigned)
    {
        $result = array();

        foreach ($inscriptions as $i) {
            $text = sprintf("[%s] %s %s %s - %s (%s-%s)",
                $i['inscription_code'],
                $i['student_name'],
                $i['student_primer_apellido'],
                $i['student_segundo_apellido'],
                $i['father_dni'],
                date("d/m/Y", strtotime($i['starts_at'])),
                date("d/m/Y", strtotime($i['ends_at']))
            );

            if ($denote_assigned && $i['grupo_id']) {
                $text = '🔒 ' . $text;
            }

            $result[] = array(
                'id' => $i['id'],
                'text' => $text
            );
        }
        return $result;
    }
}
