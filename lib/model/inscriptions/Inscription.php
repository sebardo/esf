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
        $extraSearch = '';
        $boundValues = array();

        if (!empty($search['week'])) {
            $extraSearch .= ' AND student_course_inscription = :week';
            $boundValues['week'] = $search['week'];
        }


        if (!empty($search['name'])) {
            $parts = explode(' ', trim($search['name'])); // try to parse 'Name Middle SecondName'
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
                $extraSearch .= ' AND ' . implode(' AND ', $innerSearch);
            }
        }


        if (!empty($search['inscription'])) {
            $extraSearch .= ' AND inscription_code like :inscription';
            $boundValues['inscription'] = '%' . $search['inscription'] . '%';
        }


        if (!empty($search['dni'])) {
            $extraSearch .= ' AND father_dni like :dni';
            $boundValues['dni'] = '%' . $search['dni'] . '%';
        }

        $user = sfContext::getInstance()->getUser();
        if (!$user->hasCredential('administrador')) {
            $extraSearch .= " AND course.summer_fun_center_id IN (
                      SELECT summer_fun_center_id 
                      FROM summer_fun_center_has_profile WHERE profile_id = :user_id 
                    )";
            $boundValues['user_id'] = $user->getAttribute('id');
        }

        # !!!IMPORTANT!!! Select clause mirrors that of Inscription::getAssignedToGrupo()
        $query = "
            SELECT    inscription.id
                    , inscription_code
                    , student_name
                    , student_primer_apellido
                    , student_segundo_apellido
                    , father_dni 
                    , grupo_id
                    , course.starts_at 
                    , course.ends_at 
                    
            FROM      inscription
            
            LEFT JOIN course ON course.id = student_course_inscription 
            
            WHERE   1
                    {$extraSearch}
            LIMIT 200";

        $inscriptions = mysql::getAll($query, $boundValues);
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
                    
            FROM      inscription
            
            LEFT JOIN course ON course.id = student_course_inscription 
            
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
