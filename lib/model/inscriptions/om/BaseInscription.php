<?php


abstract class BaseInscription extends BaseObject  implements Persistent {


	
	protected static $peer;


	
	protected $id;


	
	protected $created_at;


	
	protected $inscription_code = 0;


	
	protected $student_name;


	
	protected $student_primer_apellido;


	
	protected $student_segundo_apellido;


	
	protected $student_birth_date;


	
	protected $student_address;


	
	protected $student_zip;


	
	protected $student_city;


	
	protected $student_school_year;


	
	protected $student_friends;


	
	protected $is_student_disability = false;


	
	protected $student_disability;


	
	protected $student_allergies = false;


	
	protected $student_allergies_description;


	
	protected $father_name;


	
	protected $father_primer_apellido;


	
	protected $father_segundo_apellido;


	
	protected $father_phone;


	
	protected $father_dni;


	
	protected $father_mail;


	
	protected $state = 0;


	
	protected $is_father_mail_main = true;


	
	protected $mother_name;


	
	protected $mother_primer_apellido;


	
	protected $mother_segundo_apellido;


	
	protected $mother_phone;


	
	protected $mother_dni;


	
	protected $mother_mail;


	
	protected $is_mother_mail_main = false;


	
	protected $split_payment = false;


	
	protected $beca;


	
	protected $student_course_inscription;


	
	protected $is_paid = 0;


	
	protected $method_payment = 0;


	
	protected $student_provincia;


	
	protected $student_num_tarjeta_sanitaria;


	
	protected $student_tarjeta_sanitaria_companyia;


	
	protected $is_student_kid_and_us;


	
	protected $student_disability_level;


	
	protected $student_comments;


	
	protected $grupo_id;


	
	protected $student_excursion;


	
	protected $price;


	
	protected $discount;


	
	protected $discount_percent;


	
	protected $discount_amount;


	
	protected $student_photo;


	
	protected $inscription_num;


	
	protected $custom_question;


	
	protected $custom_question_answer;


	
	protected $amount_beca;


	
	protected $amount_first_payment;


	
	protected $amount_second_payment;


	
	protected $second_payment_date;


	
	protected $first_payment_date;


	
	protected $certificated;


	
	protected $certificatedname;


	
	protected $tpv_suffix;


	
	protected $tpv_first_payment_response;


	
	protected $tpv_second_payment_response;


	
	protected $culture;


	
	protected $is_payment_reminder_sent = false;


	
	protected $kids_and_us_center_id;


	
	protected $summer_fun_center_id;


	
	protected $summer_fun_center_other;


	
	protected $school_year_id;


	
	protected $is_vaccinated;
        
        
        protected $vaccination_file;

	
	protected $aCourse;

	
	protected $aProvincia;

	
	protected $aGrupo;

	
	protected $aKidsAndUsCenter;

	
	protected $aSummerFunCenter;

	
	protected $aSchoolYear;

	
	protected $collInscriptionServiceSchedules;

	
	protected $lastInscriptionServiceScheduleCriteria = null;

	
	protected $alreadyInSave = false;

	
	protected $alreadyInValidation = false;

	
	public function getId()
	{

		return $this->id;
	}

	
	public function getCreatedAt($format = 'Y-m-d H:i:s')
	{

		if ($this->created_at === null || $this->created_at === '') {
			return null;
		} elseif (!is_int($this->created_at)) {
						$ts = strtotime($this->created_at);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse value of [created_at] as date/time value: " . var_export($this->created_at, true));
			}
		} else {
			$ts = $this->created_at;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	
	public function getInscriptionCode()
	{

		return $this->inscription_code;
	}

	
	public function getStudentName()
	{

		return $this->student_name;
	}

	
	public function getStudentPrimerApellido()
	{

		return $this->student_primer_apellido;
	}

	
	public function getStudentSegundoApellido()
	{

		return $this->student_segundo_apellido;
	}

	
	public function getStudentBirthDate($format = 'Y-m-d')
	{

		if ($this->student_birth_date === null || $this->student_birth_date === '') {
			return null;
		} elseif (!is_int($this->student_birth_date)) {
						$ts = strtotime($this->student_birth_date);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse value of [student_birth_date] as date/time value: " . var_export($this->student_birth_date, true));
			}
		} else {
			$ts = $this->student_birth_date;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	
	public function getStudentAddress()
	{

		return $this->student_address;
	}

	
	public function getStudentZip()
	{

		return $this->student_zip;
	}

	
	public function getStudentCity()
	{

		return $this->student_city;
	}

	
	public function getStudentSchoolYear()
	{

		return $this->student_school_year;
	}

	
	public function getStudentFriends()
	{

		return $this->student_friends;
	}

	
	public function getIsStudentDisability()
	{

		return $this->is_student_disability;
	}

	
	public function getStudentDisability()
	{

		return $this->student_disability;
	}

	
	public function getStudentAllergies()
	{

		return $this->student_allergies;
	}

	
	public function getStudentAllergiesDescription()
	{

		return $this->student_allergies_description;
	}

	
	public function getFatherName()
	{

		return $this->father_name;
	}

	
	public function getFatherPrimerApellido()
	{

		return $this->father_primer_apellido;
	}

	
	public function getFatherSegundoApellido()
	{

		return $this->father_segundo_apellido;
	}

	
	public function getFatherPhone()
	{

		return $this->father_phone;
	}

	
	public function getFatherDni()
	{

		return $this->father_dni;
	}

	
	public function getFatherMail()
	{

		return $this->father_mail;
	}

	
	public function getState()
	{

		return $this->state;
	}

	
	public function getIsFatherMailMain()
	{

		return $this->is_father_mail_main;
	}

	
	public function getMotherName()
	{

		return $this->mother_name;
	}

	
	public function getMotherPrimerApellido()
	{

		return $this->mother_primer_apellido;
	}

	
	public function getMotherSegundoApellido()
	{

		return $this->mother_segundo_apellido;
	}

	
	public function getMotherPhone()
	{

		return $this->mother_phone;
	}

	
	public function getMotherDni()
	{

		return $this->mother_dni;
	}

	
	public function getMotherMail()
	{

		return $this->mother_mail;
	}

	
	public function getIsMotherMailMain()
	{

		return $this->is_mother_mail_main;
	}

	
	public function getSplitPayment()
	{

		return $this->split_payment;
	}

	
	public function getBeca()
	{

		return $this->beca;
	}

	
	public function getStudentCourseInscription()
	{

		return $this->student_course_inscription;
	}

	
	public function getIsPaid()
	{

		return $this->is_paid;
	}

	
	public function getMethodPayment()
	{

		return $this->method_payment;
	}

	
	public function getStudentProvincia()
	{

		return $this->student_provincia;
	}

	
	public function getStudentNumTarjetaSanitaria()
	{

		return $this->student_num_tarjeta_sanitaria;
	}

	
	public function getStudentTarjetaSanitariaCompanyia()
	{

		return $this->student_tarjeta_sanitaria_companyia;
	}

	
	public function getIsStudentKidAndUs()
	{

		return $this->is_student_kid_and_us;
	}

	
	public function getStudentDisabilityLevel()
	{

		return $this->student_disability_level;
	}

	
	public function getStudentComments()
	{

		return $this->student_comments;
	}

	
	public function getGrupoId()
	{

		return $this->grupo_id;
	}

	
	public function getStudentExcursion()
	{

		return $this->student_excursion;
	}

	
	public function getPrice()
	{

		return $this->price;
	}

	
	public function getDiscount()
	{

		return $this->discount;
	}

	
	public function getDiscountPercent()
	{

		return $this->discount_percent;
	}

	
	public function getDiscountAmount()
	{

		return $this->discount_amount;
	}

	
	public function getStudentPhoto()
	{

		return $this->student_photo;
	}

	
	public function getInscriptionNum()
	{

		return $this->inscription_num;
	}

	
	public function getCustomQuestion()
	{

		return $this->custom_question;
	}

	
	public function getCustomQuestionAnswer()
	{

		return $this->custom_question_answer;
	}

	
	public function getAmountBeca()
	{

		return $this->amount_beca;
	}

	
	public function getAmountFirstPayment()
	{

		return $this->amount_first_payment;
	}

	
	public function getAmountSecondPayment()
	{

		return $this->amount_second_payment;
	}

	
	public function getSecondPaymentDate($format = 'Y-m-d')
	{

		if ($this->second_payment_date === null || $this->second_payment_date === '') {
			return null;
		} elseif (!is_int($this->second_payment_date)) {
						$ts = strtotime($this->second_payment_date);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse value of [second_payment_date] as date/time value: " . var_export($this->second_payment_date, true));
			}
		} else {
			$ts = $this->second_payment_date;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	
	public function getFirstPaymentDate($format = 'Y-m-d')
	{

		if ($this->first_payment_date === null || $this->first_payment_date === '') {
			return null;
		} elseif (!is_int($this->first_payment_date)) {
						$ts = strtotime($this->first_payment_date);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse value of [first_payment_date] as date/time value: " . var_export($this->first_payment_date, true));
			}
		} else {
			$ts = $this->first_payment_date;
		}
		if ($format === null) {
			return $ts;
		} elseif (strpos($format, '%') !== false) {
			return strftime($format, $ts);
		} else {
			return date($format, $ts);
		}
	}

	
	public function getCertificated()
	{

		return $this->certificated;
	}

	
	public function getCertificatedname()
	{

		return $this->certificatedname;
	}

	
	public function getTpvSuffix()
	{

		return $this->tpv_suffix;
	}

	
	public function getTpvFirstPaymentResponse()
	{

		return $this->tpv_first_payment_response;
	}

	
	public function getTpvSecondPaymentResponse()
	{

		return $this->tpv_second_payment_response;
	}

	
	public function getCulture()
	{

		return $this->culture;
	}

	
	public function getIsPaymentReminderSent()
	{

		return $this->is_payment_reminder_sent;
	}

	
	public function getKidsAndUsCenterId()
	{

		return $this->kids_and_us_center_id;
	}

	
	public function getSummerFunCenterId()
	{

		return $this->summer_fun_center_id;
	}

	
	public function getSummerFunCenterOther()
	{

		return $this->summer_fun_center_other;
	}

	
	public function getSchoolYearId()
	{

		return $this->school_year_id;
	}

	
	public function getIsVaccinated()
	{

		return $this->is_vaccinated;
	}
        
        
        public function getVaccinationFile()
	{

		return $this->vaccination_file;
	}

	
	public function setId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->id !== $v) {
			$this->id = $v;
			$this->modifiedColumns[] = InscriptionPeer::ID;
		}

	} 
	
	public function setCreatedAt($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse date/time value for [created_at] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->created_at !== $ts) {
			$this->created_at = $ts;
			$this->modifiedColumns[] = InscriptionPeer::CREATED_AT;
		}

	} 
	
	public function setInscriptionCode($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->inscription_code !== $v || $v === 0) {
			$this->inscription_code = $v;
			$this->modifiedColumns[] = InscriptionPeer::INSCRIPTION_CODE;
		}

	} 
	
	public function setStudentName($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_name !== $v) {
			$this->student_name = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_NAME;
		}

	} 
	
	public function setStudentPrimerApellido($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_primer_apellido !== $v) {
			$this->student_primer_apellido = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_PRIMER_APELLIDO;
		}

	} 
	
	public function setStudentSegundoApellido($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_segundo_apellido !== $v) {
			$this->student_segundo_apellido = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_SEGUNDO_APELLIDO;
		}

	} 
	
	public function setStudentBirthDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse date/time value for [student_birth_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->student_birth_date !== $ts) {
			$this->student_birth_date = $ts;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_BIRTH_DATE;
		}

	} 
	
	public function setStudentAddress($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_address !== $v) {
			$this->student_address = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_ADDRESS;
		}

	} 
	
	public function setStudentZip($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_zip !== $v) {
			$this->student_zip = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_ZIP;
		}

	} 
	
	public function setStudentCity($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_city !== $v) {
			$this->student_city = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_CITY;
		}

	} 
	
	public function setStudentSchoolYear($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_school_year !== $v) {
			$this->student_school_year = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_SCHOOL_YEAR;
		}

	} 
	
	public function setStudentFriends($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_friends !== $v) {
			$this->student_friends = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_FRIENDS;
		}

	} 
	
	public function setIsStudentDisability($v)
	{

		if ($this->is_student_disability !== $v || $v === false) {
			$this->is_student_disability = $v;
			$this->modifiedColumns[] = InscriptionPeer::IS_STUDENT_DISABILITY;
		}

	} 
	
	public function setStudentDisability($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_disability !== $v) {
			$this->student_disability = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_DISABILITY;
		}

	} 
	
	public function setStudentAllergies($v)
	{

		if ($this->student_allergies !== $v || $v === false) {
			$this->student_allergies = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_ALLERGIES;
		}

	} 
	
	public function setStudentAllergiesDescription($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_allergies_description !== $v) {
			$this->student_allergies_description = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_ALLERGIES_DESCRIPTION;
		}

	} 
	
	public function setFatherName($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->father_name !== $v) {
			$this->father_name = $v;
			$this->modifiedColumns[] = InscriptionPeer::FATHER_NAME;
		}

	} 
	
	public function setFatherPrimerApellido($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->father_primer_apellido !== $v) {
			$this->father_primer_apellido = $v;
			$this->modifiedColumns[] = InscriptionPeer::FATHER_PRIMER_APELLIDO;
		}

	} 
	
	public function setFatherSegundoApellido($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->father_segundo_apellido !== $v) {
			$this->father_segundo_apellido = $v;
			$this->modifiedColumns[] = InscriptionPeer::FATHER_SEGUNDO_APELLIDO;
		}

	} 
	
	public function setFatherPhone($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->father_phone !== $v) {
			$this->father_phone = $v;
			$this->modifiedColumns[] = InscriptionPeer::FATHER_PHONE;
		}

	} 
	
	public function setFatherDni($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->father_dni !== $v) {
			$this->father_dni = $v;
			$this->modifiedColumns[] = InscriptionPeer::FATHER_DNI;
		}

	} 
	
	public function setFatherMail($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->father_mail !== $v) {
			$this->father_mail = $v;
			$this->modifiedColumns[] = InscriptionPeer::FATHER_MAIL;
		}

	} 
	
	public function setState($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->state !== $v || $v === 0) {
			$this->state = $v;
			$this->modifiedColumns[] = InscriptionPeer::STATE;
		}

	} 
	
	public function setIsFatherMailMain($v)
	{

		if ($this->is_father_mail_main !== $v || $v === true) {
			$this->is_father_mail_main = $v;
			$this->modifiedColumns[] = InscriptionPeer::IS_FATHER_MAIL_MAIN;
		}

	} 
	
	public function setMotherName($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->mother_name !== $v) {
			$this->mother_name = $v;
			$this->modifiedColumns[] = InscriptionPeer::MOTHER_NAME;
		}

	} 
	
	public function setMotherPrimerApellido($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->mother_primer_apellido !== $v) {
			$this->mother_primer_apellido = $v;
			$this->modifiedColumns[] = InscriptionPeer::MOTHER_PRIMER_APELLIDO;
		}

	} 
	
	public function setMotherSegundoApellido($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->mother_segundo_apellido !== $v) {
			$this->mother_segundo_apellido = $v;
			$this->modifiedColumns[] = InscriptionPeer::MOTHER_SEGUNDO_APELLIDO;
		}

	} 
	
	public function setMotherPhone($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->mother_phone !== $v) {
			$this->mother_phone = $v;
			$this->modifiedColumns[] = InscriptionPeer::MOTHER_PHONE;
		}

	} 
	
	public function setMotherDni($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->mother_dni !== $v) {
			$this->mother_dni = $v;
			$this->modifiedColumns[] = InscriptionPeer::MOTHER_DNI;
		}

	} 
	
	public function setMotherMail($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->mother_mail !== $v) {
			$this->mother_mail = $v;
			$this->modifiedColumns[] = InscriptionPeer::MOTHER_MAIL;
		}

	} 
	
	public function setIsMotherMailMain($v)
	{

		if ($this->is_mother_mail_main !== $v || $v === true) {
			$this->is_mother_mail_main = $v;
			$this->modifiedColumns[] = InscriptionPeer::IS_MOTHER_MAIL_MAIN;
		}

	} 
	
	public function setSplitPayment($v)
	{

		if ($this->split_payment !== $v || $v === false) {
			$this->split_payment = $v;
			$this->modifiedColumns[] = InscriptionPeer::SPLIT_PAYMENT;
		}

	} 
	
	public function setBeca($v)
	{

		if ($this->beca !== $v) {
			$this->beca = $v;
			$this->modifiedColumns[] = InscriptionPeer::BECA;
		}

	} 
	
	public function setStudentCourseInscription($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->student_course_inscription !== $v) {
			$this->student_course_inscription = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_COURSE_INSCRIPTION;
		}

		if ($this->aCourse !== null && $this->aCourse->getId() !== $v) {
			$this->aCourse = null;
		}

	} 
	
	public function setIsPaid($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->is_paid !== $v || $v === 0) {
			$this->is_paid = $v;
			$this->modifiedColumns[] = InscriptionPeer::IS_PAID;
		}

	} 
	
	public function setMethodPayment($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->method_payment !== $v || $v === 0) {
			$this->method_payment = $v;
			$this->modifiedColumns[] = InscriptionPeer::METHOD_PAYMENT;
		}

	} 
	
	public function setStudentProvincia($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->student_provincia !== $v) {
			$this->student_provincia = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_PROVINCIA;
		}

		if ($this->aProvincia !== null && $this->aProvincia->getId() !== $v) {
			$this->aProvincia = null;
		}

	} 
	
	public function setStudentNumTarjetaSanitaria($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_num_tarjeta_sanitaria !== $v) {
			$this->student_num_tarjeta_sanitaria = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_NUM_TARJETA_SANITARIA;
		}

	} 
	
	public function setStudentTarjetaSanitariaCompanyia($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_tarjeta_sanitaria_companyia !== $v) {
			$this->student_tarjeta_sanitaria_companyia = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_TARJETA_SANITARIA_COMPANYIA;
		}

	} 
	
	public function setIsStudentKidAndUs($v)
	{

		if ($this->is_student_kid_and_us !== $v) {
			$this->is_student_kid_and_us = $v;
			$this->modifiedColumns[] = InscriptionPeer::IS_STUDENT_KID_AND_US;
		}

	} 
	
	public function setStudentDisabilityLevel($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_disability_level !== $v) {
			$this->student_disability_level = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_DISABILITY_LEVEL;
		}

	} 
	
	public function setStudentComments($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_comments !== $v) {
			$this->student_comments = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_COMMENTS;
		}

	} 
	
	public function setGrupoId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->grupo_id !== $v) {
			$this->grupo_id = $v;
			$this->modifiedColumns[] = InscriptionPeer::GRUPO_ID;
		}

		if ($this->aGrupo !== null && $this->aGrupo->getId() !== $v) {
			$this->aGrupo = null;
		}

	} 
	
	public function setStudentExcursion($v)
	{

		if ($this->student_excursion !== $v) {
			$this->student_excursion = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_EXCURSION;
		}

	} 
	
	public function setPrice($v)
	{

		if ($this->price !== $v) {
			$this->price = $v;
			$this->modifiedColumns[] = InscriptionPeer::PRICE;
		}

	} 
	
	public function setDiscount($v)
	{

		if ($this->discount !== $v) {
			$this->discount = $v;
			$this->modifiedColumns[] = InscriptionPeer::DISCOUNT;
		}

	} 
	
	public function setDiscountPercent($v)
	{

		if ($this->discount_percent !== $v) {
			$this->discount_percent = $v;
			$this->modifiedColumns[] = InscriptionPeer::DISCOUNT_PERCENT;
		}

	} 
	
	public function setDiscountAmount($v)
	{

		if ($this->discount_amount !== $v) {
			$this->discount_amount = $v;
			$this->modifiedColumns[] = InscriptionPeer::DISCOUNT_AMOUNT;
		}

	} 
	
	public function setStudentPhoto($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->student_photo !== $v) {
			$this->student_photo = $v;
			$this->modifiedColumns[] = InscriptionPeer::STUDENT_PHOTO;
		}

	} 
	
	public function setInscriptionNum($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->inscription_num !== $v) {
			$this->inscription_num = $v;
			$this->modifiedColumns[] = InscriptionPeer::INSCRIPTION_NUM;
		}

	} 
	
	public function setCustomQuestion($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->custom_question !== $v) {
			$this->custom_question = $v;
			$this->modifiedColumns[] = InscriptionPeer::CUSTOM_QUESTION;
		}

	} 
	
	public function setCustomQuestionAnswer($v)
	{

		if ($this->custom_question_answer !== $v) {
			$this->custom_question_answer = $v;
			$this->modifiedColumns[] = InscriptionPeer::CUSTOM_QUESTION_ANSWER;
		}

	} 
	
	public function setAmountBeca($v)
	{

		if ($this->amount_beca !== $v) {
			$this->amount_beca = $v;
			$this->modifiedColumns[] = InscriptionPeer::AMOUNT_BECA;
		}

	} 
	
	public function setAmountFirstPayment($v)
	{

		if ($this->amount_first_payment !== $v) {
			$this->amount_first_payment = $v;
			$this->modifiedColumns[] = InscriptionPeer::AMOUNT_FIRST_PAYMENT;
		}

	} 
	
	public function setAmountSecondPayment($v)
	{

		if ($this->amount_second_payment !== $v) {
			$this->amount_second_payment = $v;
			$this->modifiedColumns[] = InscriptionPeer::AMOUNT_SECOND_PAYMENT;
		}

	} 
	
	public function setSecondPaymentDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse date/time value for [second_payment_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->second_payment_date !== $ts) {
			$this->second_payment_date = $ts;
			$this->modifiedColumns[] = InscriptionPeer::SECOND_PAYMENT_DATE;
		}

	} 
	
	public function setFirstPaymentDate($v)
	{

		if ($v !== null && !is_int($v)) {
			$ts = strtotime($v);
			if ($ts === -1 || $ts === false) { 				throw new PropelException("Unable to parse date/time value for [first_payment_date] from input: " . var_export($v, true));
			}
		} else {
			$ts = $v;
		}
		if ($this->first_payment_date !== $ts) {
			$this->first_payment_date = $ts;
			$this->modifiedColumns[] = InscriptionPeer::FIRST_PAYMENT_DATE;
		}

	} 
	
	public function setCertificated($v)
	{

		if ($this->certificated !== $v) {
			$this->certificated = $v;
			$this->modifiedColumns[] = InscriptionPeer::CERTIFICATED;
		}

	} 
	
	public function setCertificatedname($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->certificatedname !== $v) {
			$this->certificatedname = $v;
			$this->modifiedColumns[] = InscriptionPeer::CERTIFICATEDNAME;
		}

	} 
	
	public function setTpvSuffix($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->tpv_suffix !== $v) {
			$this->tpv_suffix = $v;
			$this->modifiedColumns[] = InscriptionPeer::TPV_SUFFIX;
		}

	} 
	
	public function setTpvFirstPaymentResponse($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tpv_first_payment_response !== $v) {
			$this->tpv_first_payment_response = $v;
			$this->modifiedColumns[] = InscriptionPeer::TPV_FIRST_PAYMENT_RESPONSE;
		}

	} 
	
	public function setTpvSecondPaymentResponse($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->tpv_second_payment_response !== $v) {
			$this->tpv_second_payment_response = $v;
			$this->modifiedColumns[] = InscriptionPeer::TPV_SECOND_PAYMENT_RESPONSE;
		}

	} 
	
	public function setCulture($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->culture !== $v) {
			$this->culture = $v;
			$this->modifiedColumns[] = InscriptionPeer::CULTURE;
		}

	} 
	
	public function setIsPaymentReminderSent($v)
	{

		if ($this->is_payment_reminder_sent !== $v || $v === false) {
			$this->is_payment_reminder_sent = $v;
			$this->modifiedColumns[] = InscriptionPeer::IS_PAYMENT_REMINDER_SENT;
		}

	} 
	
	public function setKidsAndUsCenterId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->kids_and_us_center_id !== $v) {
			$this->kids_and_us_center_id = $v;
			$this->modifiedColumns[] = InscriptionPeer::KIDS_AND_US_CENTER_ID;
		}

		if ($this->aKidsAndUsCenter !== null && $this->aKidsAndUsCenter->getId() !== $v) {
			$this->aKidsAndUsCenter = null;
		}

	} 
	
	public function setSummerFunCenterId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->summer_fun_center_id !== $v) {
			$this->summer_fun_center_id = $v;
			$this->modifiedColumns[] = InscriptionPeer::SUMMER_FUN_CENTER_ID;
		}

		if ($this->aSummerFunCenter !== null && $this->aSummerFunCenter->getId() !== $v) {
			$this->aSummerFunCenter = null;
		}

	} 
	
	public function setSummerFunCenterOther($v)
	{

						if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->summer_fun_center_other !== $v) {
			$this->summer_fun_center_other = $v;
			$this->modifiedColumns[] = InscriptionPeer::SUMMER_FUN_CENTER_OTHER;
		}

	} 
	
	public function setSchoolYearId($v)
	{

						if ($v !== null && !is_int($v) && is_numeric($v)) {
			$v = (int) $v;
		}

		if ($this->school_year_id !== $v) {
			$this->school_year_id = $v;
			$this->modifiedColumns[] = InscriptionPeer::SCHOOL_YEAR_ID;
		}

		if ($this->aSchoolYear !== null && $this->aSchoolYear->getId() !== $v) {
			$this->aSchoolYear = null;
		}

	} 
	
	public function setIsVaccinated($v)
	{

		if ($this->is_vaccinated !== $v) {
			$this->is_vaccinated = $v;
			$this->modifiedColumns[] = InscriptionPeer::IS_VACCINATED;
		}

	} 
        
        public function setVaccinationFile($v)
	{
                if ($v !== null && !is_string($v)) {
			$v = (string) $v; 
		}

		if ($this->vaccination_file !== $v) {
			$this->vaccination_file = $v;
			$this->modifiedColumns[] = InscriptionPeer::VACCINATION_FILE;
		}

	} 
	
	public function hydrate(ResultSet $rs, $startcol = 1)
	{
		try {

			$this->id = $rs->getInt($startcol + 0);

			$this->created_at = $rs->getTimestamp($startcol + 1, null);

			$this->inscription_code = $rs->getInt($startcol + 2);

			$this->student_name = $rs->getString($startcol + 3);

			$this->student_primer_apellido = $rs->getString($startcol + 4);

			$this->student_segundo_apellido = $rs->getString($startcol + 5);

			$this->student_birth_date = $rs->getDate($startcol + 6, null);

			$this->student_address = $rs->getString($startcol + 7);

			$this->student_zip = $rs->getString($startcol + 8);

			$this->student_city = $rs->getString($startcol + 9);

			$this->student_school_year = $rs->getString($startcol + 10);

			$this->student_friends = $rs->getString($startcol + 11);

			$this->is_student_disability = $rs->getBoolean($startcol + 12);

			$this->student_disability = $rs->getString($startcol + 13);

			$this->student_allergies = $rs->getBoolean($startcol + 14);

			$this->student_allergies_description = $rs->getString($startcol + 15);

			$this->father_name = $rs->getString($startcol + 16);

			$this->father_primer_apellido = $rs->getString($startcol + 17);

			$this->father_segundo_apellido = $rs->getString($startcol + 18);

			$this->father_phone = $rs->getString($startcol + 19);

			$this->father_dni = $rs->getString($startcol + 20);

			$this->father_mail = $rs->getString($startcol + 21);

			$this->state = $rs->getInt($startcol + 22);

			$this->is_father_mail_main = $rs->getBoolean($startcol + 23);

			$this->mother_name = $rs->getString($startcol + 24);

			$this->mother_primer_apellido = $rs->getString($startcol + 25);

			$this->mother_segundo_apellido = $rs->getString($startcol + 26);

			$this->mother_phone = $rs->getString($startcol + 27);

			$this->mother_dni = $rs->getString($startcol + 28);

			$this->mother_mail = $rs->getString($startcol + 29);

			$this->is_mother_mail_main = $rs->getBoolean($startcol + 30);

			$this->split_payment = $rs->getBoolean($startcol + 31);

			$this->beca = $rs->getBoolean($startcol + 32);

			$this->student_course_inscription = $rs->getInt($startcol + 33);

			$this->is_paid = $rs->getInt($startcol + 34);

			$this->method_payment = $rs->getInt($startcol + 35);

			$this->student_provincia = $rs->getInt($startcol + 36);

			$this->student_num_tarjeta_sanitaria = $rs->getString($startcol + 37);

			$this->student_tarjeta_sanitaria_companyia = $rs->getString($startcol + 38);

			$this->is_student_kid_and_us = $rs->getBoolean($startcol + 39);

			$this->student_disability_level = $rs->getString($startcol + 40);

			$this->student_comments = $rs->getString($startcol + 41);

			$this->grupo_id = $rs->getInt($startcol + 42);

			$this->student_excursion = $rs->getBoolean($startcol + 43);

			$this->price = $rs->getFloat($startcol + 44);

			$this->discount = $rs->getFloat($startcol + 45);

			$this->discount_percent = $rs->getFloat($startcol + 46);

			$this->discount_amount = $rs->getFloat($startcol + 47);

			$this->student_photo = $rs->getString($startcol + 48);

			$this->inscription_num = $rs->getInt($startcol + 49);

			$this->custom_question = $rs->getString($startcol + 50);

			$this->custom_question_answer = $rs->getBoolean($startcol + 51);

			$this->amount_beca = $rs->getFloat($startcol + 52);

			$this->amount_first_payment = $rs->getFloat($startcol + 53);

			$this->amount_second_payment = $rs->getFloat($startcol + 54);

			$this->second_payment_date = $rs->getDate($startcol + 55, null);

			$this->first_payment_date = $rs->getDate($startcol + 56, null);

			$this->certificated = $rs->getBoolean($startcol + 57);

			$this->certificatedname = $rs->getString($startcol + 58);

			$this->tpv_suffix = $rs->getInt($startcol + 59);

			$this->tpv_first_payment_response = $rs->getString($startcol + 60);

			$this->tpv_second_payment_response = $rs->getString($startcol + 61);

			$this->culture = $rs->getString($startcol + 62);

			$this->is_payment_reminder_sent = $rs->getBoolean($startcol + 63);

			$this->kids_and_us_center_id = $rs->getInt($startcol + 64);

			$this->summer_fun_center_id = $rs->getInt($startcol + 65);

			$this->summer_fun_center_other = $rs->getString($startcol + 66);

			$this->school_year_id = $rs->getInt($startcol + 67);

			$this->is_vaccinated = $rs->getBoolean($startcol + 68);

			$this->vaccination_file = $rs->getString($startcol + 69);

			$this->resetModified();

			$this->setNew(false);

						return $startcol + 70; 
		} catch (Exception $e) {
			throw new PropelException("Error populating Inscription object", $e);
		}
	}

	
	public function delete($con = null)
	{

    foreach (sfMixer::getCallables('BaseInscription:delete:pre') as $callable)
    {
      $ret = call_user_func($callable, $this, $con);
      if ($ret)
      {
        return;
      }
    }


		if ($this->isDeleted()) {
			throw new PropelException("This object has already been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(InscriptionPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			InscriptionPeer::doDelete($this, $con);
			$this->setDeleted(true);
			$con->commit();
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	

    foreach (sfMixer::getCallables('BaseInscription:delete:post') as $callable)
    {
      call_user_func($callable, $this, $con);
    }

  }
	
	public function save($con = null)
	{

    foreach (sfMixer::getCallables('BaseInscription:save:pre') as $callable)
    {
      $affectedRows = call_user_func($callable, $this, $con);
      if (is_int($affectedRows))
      {
        return $affectedRows;
      }
    }


    if ($this->isNew() && !$this->isColumnModified(InscriptionPeer::CREATED_AT))
    {
      $this->setCreatedAt(time());
    }

		if ($this->isDeleted()) {
			throw new PropelException("You cannot save an object that has been deleted.");
		}

		if ($con === null) {
			$con = Propel::getConnection(InscriptionPeer::DATABASE_NAME);
		}

		try {
			$con->begin();
			$affectedRows = $this->doSave($con);
			$con->commit();
    foreach (sfMixer::getCallables('BaseInscription:save:post') as $callable)
    {
      call_user_func($callable, $this, $con, $affectedRows);
    }

			return $affectedRows;
		} catch (PropelException $e) {
			$con->rollback();
			throw $e;
		}
	}

	
	protected function doSave($con)
	{
		$affectedRows = 0; 		if (!$this->alreadyInSave) {
			$this->alreadyInSave = true;


												
			if ($this->aCourse !== null) {
				if ($this->aCourse->isModified() || $this->aCourse->getCurrentCourseI18n()->isModified()) {
					$affectedRows += $this->aCourse->save($con);
				}
				$this->setCourse($this->aCourse);
			}

			if ($this->aProvincia !== null) {
				if ($this->aProvincia->isModified()) {
					$affectedRows += $this->aProvincia->save($con);
				}
				$this->setProvincia($this->aProvincia);
			}

			if ($this->aGrupo !== null) {
				if ($this->aGrupo->isModified()) {
					$affectedRows += $this->aGrupo->save($con);
				}
				$this->setGrupo($this->aGrupo);
			}

			if ($this->aKidsAndUsCenter !== null) {
				if ($this->aKidsAndUsCenter->isModified()) {
					$affectedRows += $this->aKidsAndUsCenter->save($con);
				}
				$this->setKidsAndUsCenter($this->aKidsAndUsCenter);
			}

			if ($this->aSummerFunCenter !== null) {
				if ($this->aSummerFunCenter->isModified() || $this->aSummerFunCenter->getCurrentSummerFunCenterI18n()->isModified()) {
					$affectedRows += $this->aSummerFunCenter->save($con);
				}
				$this->setSummerFunCenter($this->aSummerFunCenter);
			}

			if ($this->aSchoolYear !== null) {
				if ($this->aSchoolYear->isModified() || $this->aSchoolYear->getCurrentSchoolYearI18n()->isModified()) {
					$affectedRows += $this->aSchoolYear->save($con);
				}
				$this->setSchoolYear($this->aSchoolYear);
			}


						if ($this->isModified()) {
				if ($this->isNew()) {
					$pk = InscriptionPeer::doInsert($this, $con);
					$affectedRows += 1; 										 										 
					$this->setId($pk);  
					$this->setNew(false);
				} else {
					$affectedRows += InscriptionPeer::doUpdate($this, $con);
				}
				$this->resetModified(); 			}

			if ($this->collInscriptionServiceSchedules !== null) {
				foreach($this->collInscriptionServiceSchedules as $referrerFK) {
					if (!$referrerFK->isDeleted()) {
						$affectedRows += $referrerFK->save($con);
					}
				}
			}

			$this->alreadyInSave = false;
		}
		return $affectedRows;
	} 
	
	protected $validationFailures = array();

	
	public function getValidationFailures()
	{
		return $this->validationFailures;
	}

	
	public function validate($columns = null)
	{
		$res = $this->doValidate($columns);
		if ($res === true) {
			$this->validationFailures = array();
			return true;
		} else {
			$this->validationFailures = $res;
			return false;
		}
	}

	
	protected function doValidate($columns = null)
	{
		if (!$this->alreadyInValidation) {
			$this->alreadyInValidation = true;
			$retval = null;

			$failureMap = array();


												
			if ($this->aCourse !== null) {
				if (!$this->aCourse->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aCourse->getValidationFailures());
				}
			}

			if ($this->aProvincia !== null) {
				if (!$this->aProvincia->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aProvincia->getValidationFailures());
				}
			}

			if ($this->aGrupo !== null) {
				if (!$this->aGrupo->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aGrupo->getValidationFailures());
				}
			}

			if ($this->aKidsAndUsCenter !== null) {
				if (!$this->aKidsAndUsCenter->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aKidsAndUsCenter->getValidationFailures());
				}
			}

			if ($this->aSummerFunCenter !== null) {
				if (!$this->aSummerFunCenter->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSummerFunCenter->getValidationFailures());
				}
			}

			if ($this->aSchoolYear !== null) {
				if (!$this->aSchoolYear->validate($columns)) {
					$failureMap = array_merge($failureMap, $this->aSchoolYear->getValidationFailures());
				}
			}


			if (($retval = InscriptionPeer::doValidate($this, $columns)) !== true) {
				$failureMap = array_merge($failureMap, $retval);
			}


				if ($this->collInscriptionServiceSchedules !== null) {
					foreach($this->collInscriptionServiceSchedules as $referrerFK) {
						if (!$referrerFK->validate($columns)) {
							$failureMap = array_merge($failureMap, $referrerFK->getValidationFailures());
						}
					}
				}


			$this->alreadyInValidation = false;
		}

		return (!empty($failureMap) ? $failureMap : true);
	}

	
	public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = InscriptionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->getByPosition($pos);
	}

	
	public function getByPosition($pos)
	{
		switch($pos) {
			case 0:
				return $this->getId();
				break;
			case 1:
				return $this->getCreatedAt();
				break;
			case 2:
				return $this->getInscriptionCode();
				break;
			case 3:
				return $this->getStudentName();
				break;
			case 4:
				return $this->getStudentPrimerApellido();
				break;
			case 5:
				return $this->getStudentSegundoApellido();
				break;
			case 6:
				return $this->getStudentBirthDate();
				break;
			case 7:
				return $this->getStudentAddress();
				break;
			case 8:
				return $this->getStudentZip();
				break;
			case 9:
				return $this->getStudentCity();
				break;
			case 10:
				return $this->getStudentSchoolYear();
				break;
			case 11:
				return $this->getStudentFriends();
				break;
			case 12:
				return $this->getIsStudentDisability();
				break;
			case 13:
				return $this->getStudentDisability();
				break;
			case 14:
				return $this->getStudentAllergies();
				break;
			case 15:
				return $this->getStudentAllergiesDescription();
				break;
			case 16:
				return $this->getFatherName();
				break;
			case 17:
				return $this->getFatherPrimerApellido();
				break;
			case 18:
				return $this->getFatherSegundoApellido();
				break;
			case 19:
				return $this->getFatherPhone();
				break;
			case 20:
				return $this->getFatherDni();
				break;
			case 21:
				return $this->getFatherMail();
				break;
			case 22:
				return $this->getState();
				break;
			case 23:
				return $this->getIsFatherMailMain();
				break;
			case 24:
				return $this->getMotherName();
				break;
			case 25:
				return $this->getMotherPrimerApellido();
				break;
			case 26:
				return $this->getMotherSegundoApellido();
				break;
			case 27:
				return $this->getMotherPhone();
				break;
			case 28:
				return $this->getMotherDni();
				break;
			case 29:
				return $this->getMotherMail();
				break;
			case 30:
				return $this->getIsMotherMailMain();
				break;
			case 31:
				return $this->getSplitPayment();
				break;
			case 32:
				return $this->getBeca();
				break;
			case 33:
				return $this->getStudentCourseInscription();
				break;
			case 34:
				return $this->getIsPaid();
				break;
			case 35:
				return $this->getMethodPayment();
				break;
			case 36:
				return $this->getStudentProvincia();
				break;
			case 37:
				return $this->getStudentNumTarjetaSanitaria();
				break;
			case 38:
				return $this->getStudentTarjetaSanitariaCompanyia();
				break;
			case 39:
				return $this->getIsStudentKidAndUs();
				break;
			case 40:
				return $this->getStudentDisabilityLevel();
				break;
			case 41:
				return $this->getStudentComments();
				break;
			case 42:
				return $this->getGrupoId();
				break;
			case 43:
				return $this->getStudentExcursion();
				break;
			case 44:
				return $this->getPrice();
				break;
			case 45:
				return $this->getDiscount();
				break;
			case 46:
				return $this->getDiscountPercent();
				break;
			case 47:
				return $this->getDiscountAmount();
				break;
			case 48:
				return $this->getStudentPhoto();
				break;
			case 49:
				return $this->getInscriptionNum();
				break;
			case 50:
				return $this->getCustomQuestion();
				break;
			case 51:
				return $this->getCustomQuestionAnswer();
				break;
			case 52:
				return $this->getAmountBeca();
				break;
			case 53:
				return $this->getAmountFirstPayment();
				break;
			case 54:
				return $this->getAmountSecondPayment();
				break;
			case 55:
				return $this->getSecondPaymentDate();
				break;
			case 56:
				return $this->getFirstPaymentDate();
				break;
			case 57:
				return $this->getCertificated();
				break;
			case 58:
				return $this->getCertificatedname();
				break;
			case 59:
				return $this->getTpvSuffix();
				break;
			case 60:
				return $this->getTpvFirstPaymentResponse();
				break;
			case 61:
				return $this->getTpvSecondPaymentResponse();
				break;
			case 62:
				return $this->getCulture();
				break;
			case 63:
				return $this->getIsPaymentReminderSent();
				break;
			case 64:
				return $this->getKidsAndUsCenterId();
				break;
			case 65:
				return $this->getSummerFunCenterId();
				break;
			case 66:
				return $this->getSummerFunCenterOther();
				break;
			case 67:
				return $this->getSchoolYearId();
				break;
			case 68:
				return $this->getIsVaccinated();
				break;
                        case 69:
				return $this->getVaccinationFile();
				break;
			default:
				return null;
				break;
		} 	}

	
	public function toArray($keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = InscriptionPeer::getFieldNames($keyType);
		$result = array(
			$keys[0] => $this->getId(),
			$keys[1] => $this->getCreatedAt(),
			$keys[2] => $this->getInscriptionCode(),
			$keys[3] => $this->getStudentName(),
			$keys[4] => $this->getStudentPrimerApellido(),
			$keys[5] => $this->getStudentSegundoApellido(),
			$keys[6] => $this->getStudentBirthDate(),
			$keys[7] => $this->getStudentAddress(),
			$keys[8] => $this->getStudentZip(),
			$keys[9] => $this->getStudentCity(),
			$keys[10] => $this->getStudentSchoolYear(),
			$keys[11] => $this->getStudentFriends(),
			$keys[12] => $this->getIsStudentDisability(),
			$keys[13] => $this->getStudentDisability(),
			$keys[14] => $this->getStudentAllergies(),
			$keys[15] => $this->getStudentAllergiesDescription(),
			$keys[16] => $this->getFatherName(),
			$keys[17] => $this->getFatherPrimerApellido(),
			$keys[18] => $this->getFatherSegundoApellido(),
			$keys[19] => $this->getFatherPhone(),
			$keys[20] => $this->getFatherDni(),
			$keys[21] => $this->getFatherMail(),
			$keys[22] => $this->getState(),
			$keys[23] => $this->getIsFatherMailMain(),
			$keys[24] => $this->getMotherName(),
			$keys[25] => $this->getMotherPrimerApellido(),
			$keys[26] => $this->getMotherSegundoApellido(),
			$keys[27] => $this->getMotherPhone(),
			$keys[28] => $this->getMotherDni(),
			$keys[29] => $this->getMotherMail(),
			$keys[30] => $this->getIsMotherMailMain(),
			$keys[31] => $this->getSplitPayment(),
			$keys[32] => $this->getBeca(),
			$keys[33] => $this->getStudentCourseInscription(),
			$keys[34] => $this->getIsPaid(),
			$keys[35] => $this->getMethodPayment(),
			$keys[36] => $this->getStudentProvincia(),
			$keys[37] => $this->getStudentNumTarjetaSanitaria(),
			$keys[38] => $this->getStudentTarjetaSanitariaCompanyia(),
			$keys[39] => $this->getIsStudentKidAndUs(),
			$keys[40] => $this->getStudentDisabilityLevel(),
			$keys[41] => $this->getStudentComments(),
			$keys[42] => $this->getGrupoId(),
			$keys[43] => $this->getStudentExcursion(),
			$keys[44] => $this->getPrice(),
			$keys[45] => $this->getDiscount(),
			$keys[46] => $this->getDiscountPercent(),
			$keys[47] => $this->getDiscountAmount(),
			$keys[48] => $this->getStudentPhoto(),
			$keys[49] => $this->getInscriptionNum(),
			$keys[50] => $this->getCustomQuestion(),
			$keys[51] => $this->getCustomQuestionAnswer(),
			$keys[52] => $this->getAmountBeca(),
			$keys[53] => $this->getAmountFirstPayment(),
			$keys[54] => $this->getAmountSecondPayment(),
			$keys[55] => $this->getSecondPaymentDate(),
			$keys[56] => $this->getFirstPaymentDate(),
			$keys[57] => $this->getCertificated(),
			$keys[58] => $this->getCertificatedname(),
			$keys[59] => $this->getTpvSuffix(),
			$keys[60] => $this->getTpvFirstPaymentResponse(),
			$keys[61] => $this->getTpvSecondPaymentResponse(),
			$keys[62] => $this->getCulture(),
			$keys[63] => $this->getIsPaymentReminderSent(),
			$keys[64] => $this->getKidsAndUsCenterId(),
			$keys[65] => $this->getSummerFunCenterId(),
			$keys[66] => $this->getSummerFunCenterOther(),
			$keys[67] => $this->getSchoolYearId(),
			$keys[68] => $this->getIsVaccinated(),
			$keys[69] => $this->getVaccinationFile(),
		);
		return $result;
	}

	
	public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
	{
		$pos = InscriptionPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
		return $this->setByPosition($pos, $value);
	}

	
	public function setByPosition($pos, $value)
	{
		switch($pos) {
			case 0:
				$this->setId($value);
				break;
			case 1:
				$this->setCreatedAt($value);
				break;
			case 2:
				$this->setInscriptionCode($value);
				break;
			case 3:
				$this->setStudentName($value);
				break;
			case 4:
				$this->setStudentPrimerApellido($value);
				break;
			case 5:
				$this->setStudentSegundoApellido($value);
				break;
			case 6:
				$this->setStudentBirthDate($value);
				break;
			case 7:
				$this->setStudentAddress($value);
				break;
			case 8:
				$this->setStudentZip($value);
				break;
			case 9:
				$this->setStudentCity($value);
				break;
			case 10:
				$this->setStudentSchoolYear($value);
				break;
			case 11:
				$this->setStudentFriends($value);
				break;
			case 12:
				$this->setIsStudentDisability($value);
				break;
			case 13:
				$this->setStudentDisability($value);
				break;
			case 14:
				$this->setStudentAllergies($value);
				break;
			case 15:
				$this->setStudentAllergiesDescription($value);
				break;
			case 16:
				$this->setFatherName($value);
				break;
			case 17:
				$this->setFatherPrimerApellido($value);
				break;
			case 18:
				$this->setFatherSegundoApellido($value);
				break;
			case 19:
				$this->setFatherPhone($value);
				break;
			case 20:
				$this->setFatherDni($value);
				break;
			case 21:
				$this->setFatherMail($value);
				break;
			case 22:
				$this->setState($value);
				break;
			case 23:
				$this->setIsFatherMailMain($value);
				break;
			case 24:
				$this->setMotherName($value);
				break;
			case 25:
				$this->setMotherPrimerApellido($value);
				break;
			case 26:
				$this->setMotherSegundoApellido($value);
				break;
			case 27:
				$this->setMotherPhone($value);
				break;
			case 28:
				$this->setMotherDni($value);
				break;
			case 29:
				$this->setMotherMail($value);
				break;
			case 30:
				$this->setIsMotherMailMain($value);
				break;
			case 31:
				$this->setSplitPayment($value);
				break;
			case 32:
				$this->setBeca($value);
				break;
			case 33:
				$this->setStudentCourseInscription($value);
				break;
			case 34:
				$this->setIsPaid($value);
				break;
			case 35:
				$this->setMethodPayment($value);
				break;
			case 36:
				$this->setStudentProvincia($value);
				break;
			case 37:
				$this->setStudentNumTarjetaSanitaria($value);
				break;
			case 38:
				$this->setStudentTarjetaSanitariaCompanyia($value);
				break;
			case 39:
				$this->setIsStudentKidAndUs($value);
				break;
			case 40:
				$this->setStudentDisabilityLevel($value);
				break;
			case 41:
				$this->setStudentComments($value);
				break;
			case 42:
				$this->setGrupoId($value);
				break;
			case 43:
				$this->setStudentExcursion($value);
				break;
			case 44:
				$this->setPrice($value);
				break;
			case 45:
				$this->setDiscount($value);
				break;
			case 46:
				$this->setDiscountPercent($value);
				break;
			case 47:
				$this->setDiscountAmount($value);
				break;
			case 48:
				$this->setStudentPhoto($value);
				break;
			case 49:
				$this->setInscriptionNum($value);
				break;
			case 50:
				$this->setCustomQuestion($value);
				break;
			case 51:
				$this->setCustomQuestionAnswer($value);
				break;
			case 52:
				$this->setAmountBeca($value);
				break;
			case 53:
				$this->setAmountFirstPayment($value);
				break;
			case 54:
				$this->setAmountSecondPayment($value);
				break;
			case 55:
				$this->setSecondPaymentDate($value);
				break;
			case 56:
				$this->setFirstPaymentDate($value);
				break;
			case 57:
				$this->setCertificated($value);
				break;
			case 58:
				$this->setCertificatedname($value);
				break;
			case 59:
				$this->setTpvSuffix($value);
				break;
			case 60:
				$this->setTpvFirstPaymentResponse($value);
				break;
			case 61:
				$this->setTpvSecondPaymentResponse($value);
				break;
			case 62:
				$this->setCulture($value);
				break;
			case 63:
				$this->setIsPaymentReminderSent($value);
				break;
			case 64:
				$this->setKidsAndUsCenterId($value);
				break;
			case 65:
				$this->setSummerFunCenterId($value);
				break;
			case 66:
				$this->setSummerFunCenterOther($value);
				break;
			case 67:
				$this->setSchoolYearId($value);
				break;
			case 68:
				$this->setIsVaccinated($value);
				break;
                        case 69:
				$this->setVaccinationFile($value);
				break;
		} 	}

	
	public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
	{
		$keys = InscriptionPeer::getFieldNames($keyType);

		if (array_key_exists($keys[0], $arr)) $this->setId($arr[$keys[0]]);
		if (array_key_exists($keys[1], $arr)) $this->setCreatedAt($arr[$keys[1]]);
		if (array_key_exists($keys[2], $arr)) $this->setInscriptionCode($arr[$keys[2]]);
		if (array_key_exists($keys[3], $arr)) $this->setStudentName($arr[$keys[3]]);
		if (array_key_exists($keys[4], $arr)) $this->setStudentPrimerApellido($arr[$keys[4]]);
		if (array_key_exists($keys[5], $arr)) $this->setStudentSegundoApellido($arr[$keys[5]]);
		if (array_key_exists($keys[6], $arr)) $this->setStudentBirthDate($arr[$keys[6]]);
		if (array_key_exists($keys[7], $arr)) $this->setStudentAddress($arr[$keys[7]]);
		if (array_key_exists($keys[8], $arr)) $this->setStudentZip($arr[$keys[8]]);
		if (array_key_exists($keys[9], $arr)) $this->setStudentCity($arr[$keys[9]]);
		if (array_key_exists($keys[10], $arr)) $this->setStudentSchoolYear($arr[$keys[10]]);
		if (array_key_exists($keys[11], $arr)) $this->setStudentFriends($arr[$keys[11]]);
		if (array_key_exists($keys[12], $arr)) $this->setIsStudentDisability($arr[$keys[12]]);
		if (array_key_exists($keys[13], $arr)) $this->setStudentDisability($arr[$keys[13]]);
		if (array_key_exists($keys[14], $arr)) $this->setStudentAllergies($arr[$keys[14]]);
		if (array_key_exists($keys[15], $arr)) $this->setStudentAllergiesDescription($arr[$keys[15]]);
		if (array_key_exists($keys[16], $arr)) $this->setFatherName($arr[$keys[16]]);
		if (array_key_exists($keys[17], $arr)) $this->setFatherPrimerApellido($arr[$keys[17]]);
		if (array_key_exists($keys[18], $arr)) $this->setFatherSegundoApellido($arr[$keys[18]]);
		if (array_key_exists($keys[19], $arr)) $this->setFatherPhone($arr[$keys[19]]);
		if (array_key_exists($keys[20], $arr)) $this->setFatherDni($arr[$keys[20]]);
		if (array_key_exists($keys[21], $arr)) $this->setFatherMail($arr[$keys[21]]);
		if (array_key_exists($keys[22], $arr)) $this->setState($arr[$keys[22]]);
		if (array_key_exists($keys[23], $arr)) $this->setIsFatherMailMain($arr[$keys[23]]);
		if (array_key_exists($keys[24], $arr)) $this->setMotherName($arr[$keys[24]]);
		if (array_key_exists($keys[25], $arr)) $this->setMotherPrimerApellido($arr[$keys[25]]);
		if (array_key_exists($keys[26], $arr)) $this->setMotherSegundoApellido($arr[$keys[26]]);
		if (array_key_exists($keys[27], $arr)) $this->setMotherPhone($arr[$keys[27]]);
		if (array_key_exists($keys[28], $arr)) $this->setMotherDni($arr[$keys[28]]);
		if (array_key_exists($keys[29], $arr)) $this->setMotherMail($arr[$keys[29]]);
		if (array_key_exists($keys[30], $arr)) $this->setIsMotherMailMain($arr[$keys[30]]);
		if (array_key_exists($keys[31], $arr)) $this->setSplitPayment($arr[$keys[31]]);
		if (array_key_exists($keys[32], $arr)) $this->setBeca($arr[$keys[32]]);
		if (array_key_exists($keys[33], $arr)) $this->setStudentCourseInscription($arr[$keys[33]]);
		if (array_key_exists($keys[34], $arr)) $this->setIsPaid($arr[$keys[34]]);
		if (array_key_exists($keys[35], $arr)) $this->setMethodPayment($arr[$keys[35]]);
		if (array_key_exists($keys[36], $arr)) $this->setStudentProvincia($arr[$keys[36]]);
		if (array_key_exists($keys[37], $arr)) $this->setStudentNumTarjetaSanitaria($arr[$keys[37]]);
		if (array_key_exists($keys[38], $arr)) $this->setStudentTarjetaSanitariaCompanyia($arr[$keys[38]]);
		if (array_key_exists($keys[39], $arr)) $this->setIsStudentKidAndUs($arr[$keys[39]]);
		if (array_key_exists($keys[40], $arr)) $this->setStudentDisabilityLevel($arr[$keys[40]]);
		if (array_key_exists($keys[41], $arr)) $this->setStudentComments($arr[$keys[41]]);
		if (array_key_exists($keys[42], $arr)) $this->setGrupoId($arr[$keys[42]]);
		if (array_key_exists($keys[43], $arr)) $this->setStudentExcursion($arr[$keys[43]]);
		if (array_key_exists($keys[44], $arr)) $this->setPrice($arr[$keys[44]]);
		if (array_key_exists($keys[45], $arr)) $this->setDiscount($arr[$keys[45]]);
		if (array_key_exists($keys[46], $arr)) $this->setDiscountPercent($arr[$keys[46]]);
		if (array_key_exists($keys[47], $arr)) $this->setDiscountAmount($arr[$keys[47]]);
		if (array_key_exists($keys[48], $arr)) $this->setStudentPhoto($arr[$keys[48]]);
		if (array_key_exists($keys[49], $arr)) $this->setInscriptionNum($arr[$keys[49]]);
		if (array_key_exists($keys[50], $arr)) $this->setCustomQuestion($arr[$keys[50]]);
		if (array_key_exists($keys[51], $arr)) $this->setCustomQuestionAnswer($arr[$keys[51]]);
		if (array_key_exists($keys[52], $arr)) $this->setAmountBeca($arr[$keys[52]]);
		if (array_key_exists($keys[53], $arr)) $this->setAmountFirstPayment($arr[$keys[53]]);
		if (array_key_exists($keys[54], $arr)) $this->setAmountSecondPayment($arr[$keys[54]]);
		if (array_key_exists($keys[55], $arr)) $this->setSecondPaymentDate($arr[$keys[55]]);
		if (array_key_exists($keys[56], $arr)) $this->setFirstPaymentDate($arr[$keys[56]]);
		if (array_key_exists($keys[57], $arr)) $this->setCertificated($arr[$keys[57]]);
		if (array_key_exists($keys[58], $arr)) $this->setCertificatedname($arr[$keys[58]]);
		if (array_key_exists($keys[59], $arr)) $this->setTpvSuffix($arr[$keys[59]]);
		if (array_key_exists($keys[60], $arr)) $this->setTpvFirstPaymentResponse($arr[$keys[60]]);
		if (array_key_exists($keys[61], $arr)) $this->setTpvSecondPaymentResponse($arr[$keys[61]]);
		if (array_key_exists($keys[62], $arr)) $this->setCulture($arr[$keys[62]]);
		if (array_key_exists($keys[63], $arr)) $this->setIsPaymentReminderSent($arr[$keys[63]]);
		if (array_key_exists($keys[64], $arr)) $this->setKidsAndUsCenterId($arr[$keys[64]]);
		if (array_key_exists($keys[65], $arr)) $this->setSummerFunCenterId($arr[$keys[65]]);
		if (array_key_exists($keys[66], $arr)) $this->setSummerFunCenterOther($arr[$keys[66]]);
		if (array_key_exists($keys[67], $arr)) $this->setSchoolYearId($arr[$keys[67]]);
		if (array_key_exists($keys[68], $arr)) $this->setIsVaccinated($arr[$keys[68]]);
		if (array_key_exists($keys[69], $arr)) $this->setVaccinationFile($arr[$keys[69]]);
	}

	
	public function buildCriteria()
	{
		$criteria = new Criteria(InscriptionPeer::DATABASE_NAME);

		if ($this->isColumnModified(InscriptionPeer::ID)) $criteria->add(InscriptionPeer::ID, $this->id);
		if ($this->isColumnModified(InscriptionPeer::CREATED_AT)) $criteria->add(InscriptionPeer::CREATED_AT, $this->created_at);
		if ($this->isColumnModified(InscriptionPeer::INSCRIPTION_CODE)) $criteria->add(InscriptionPeer::INSCRIPTION_CODE, $this->inscription_code);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_NAME)) $criteria->add(InscriptionPeer::STUDENT_NAME, $this->student_name);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_PRIMER_APELLIDO)) $criteria->add(InscriptionPeer::STUDENT_PRIMER_APELLIDO, $this->student_primer_apellido);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_SEGUNDO_APELLIDO)) $criteria->add(InscriptionPeer::STUDENT_SEGUNDO_APELLIDO, $this->student_segundo_apellido);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_BIRTH_DATE)) $criteria->add(InscriptionPeer::STUDENT_BIRTH_DATE, $this->student_birth_date);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_ADDRESS)) $criteria->add(InscriptionPeer::STUDENT_ADDRESS, $this->student_address);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_ZIP)) $criteria->add(InscriptionPeer::STUDENT_ZIP, $this->student_zip);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_CITY)) $criteria->add(InscriptionPeer::STUDENT_CITY, $this->student_city);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_SCHOOL_YEAR)) $criteria->add(InscriptionPeer::STUDENT_SCHOOL_YEAR, $this->student_school_year);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_FRIENDS)) $criteria->add(InscriptionPeer::STUDENT_FRIENDS, $this->student_friends);
		if ($this->isColumnModified(InscriptionPeer::IS_STUDENT_DISABILITY)) $criteria->add(InscriptionPeer::IS_STUDENT_DISABILITY, $this->is_student_disability);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_DISABILITY)) $criteria->add(InscriptionPeer::STUDENT_DISABILITY, $this->student_disability);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_ALLERGIES)) $criteria->add(InscriptionPeer::STUDENT_ALLERGIES, $this->student_allergies);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_ALLERGIES_DESCRIPTION)) $criteria->add(InscriptionPeer::STUDENT_ALLERGIES_DESCRIPTION, $this->student_allergies_description);
		if ($this->isColumnModified(InscriptionPeer::FATHER_NAME)) $criteria->add(InscriptionPeer::FATHER_NAME, $this->father_name);
		if ($this->isColumnModified(InscriptionPeer::FATHER_PRIMER_APELLIDO)) $criteria->add(InscriptionPeer::FATHER_PRIMER_APELLIDO, $this->father_primer_apellido);
		if ($this->isColumnModified(InscriptionPeer::FATHER_SEGUNDO_APELLIDO)) $criteria->add(InscriptionPeer::FATHER_SEGUNDO_APELLIDO, $this->father_segundo_apellido);
		if ($this->isColumnModified(InscriptionPeer::FATHER_PHONE)) $criteria->add(InscriptionPeer::FATHER_PHONE, $this->father_phone);
		if ($this->isColumnModified(InscriptionPeer::FATHER_DNI)) $criteria->add(InscriptionPeer::FATHER_DNI, $this->father_dni);
		if ($this->isColumnModified(InscriptionPeer::FATHER_MAIL)) $criteria->add(InscriptionPeer::FATHER_MAIL, $this->father_mail);
		if ($this->isColumnModified(InscriptionPeer::STATE)) $criteria->add(InscriptionPeer::STATE, $this->state);
		if ($this->isColumnModified(InscriptionPeer::IS_FATHER_MAIL_MAIN)) $criteria->add(InscriptionPeer::IS_FATHER_MAIL_MAIN, $this->is_father_mail_main);
		if ($this->isColumnModified(InscriptionPeer::MOTHER_NAME)) $criteria->add(InscriptionPeer::MOTHER_NAME, $this->mother_name);
		if ($this->isColumnModified(InscriptionPeer::MOTHER_PRIMER_APELLIDO)) $criteria->add(InscriptionPeer::MOTHER_PRIMER_APELLIDO, $this->mother_primer_apellido);
		if ($this->isColumnModified(InscriptionPeer::MOTHER_SEGUNDO_APELLIDO)) $criteria->add(InscriptionPeer::MOTHER_SEGUNDO_APELLIDO, $this->mother_segundo_apellido);
		if ($this->isColumnModified(InscriptionPeer::MOTHER_PHONE)) $criteria->add(InscriptionPeer::MOTHER_PHONE, $this->mother_phone);
		if ($this->isColumnModified(InscriptionPeer::MOTHER_DNI)) $criteria->add(InscriptionPeer::MOTHER_DNI, $this->mother_dni);
		if ($this->isColumnModified(InscriptionPeer::MOTHER_MAIL)) $criteria->add(InscriptionPeer::MOTHER_MAIL, $this->mother_mail);
		if ($this->isColumnModified(InscriptionPeer::IS_MOTHER_MAIL_MAIN)) $criteria->add(InscriptionPeer::IS_MOTHER_MAIL_MAIN, $this->is_mother_mail_main);
		if ($this->isColumnModified(InscriptionPeer::SPLIT_PAYMENT)) $criteria->add(InscriptionPeer::SPLIT_PAYMENT, $this->split_payment);
		if ($this->isColumnModified(InscriptionPeer::BECA)) $criteria->add(InscriptionPeer::BECA, $this->beca);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_COURSE_INSCRIPTION)) $criteria->add(InscriptionPeer::STUDENT_COURSE_INSCRIPTION, $this->student_course_inscription);
		if ($this->isColumnModified(InscriptionPeer::IS_PAID)) $criteria->add(InscriptionPeer::IS_PAID, $this->is_paid);
		if ($this->isColumnModified(InscriptionPeer::METHOD_PAYMENT)) $criteria->add(InscriptionPeer::METHOD_PAYMENT, $this->method_payment);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_PROVINCIA)) $criteria->add(InscriptionPeer::STUDENT_PROVINCIA, $this->student_provincia);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_NUM_TARJETA_SANITARIA)) $criteria->add(InscriptionPeer::STUDENT_NUM_TARJETA_SANITARIA, $this->student_num_tarjeta_sanitaria);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_TARJETA_SANITARIA_COMPANYIA)) $criteria->add(InscriptionPeer::STUDENT_TARJETA_SANITARIA_COMPANYIA, $this->student_tarjeta_sanitaria_companyia);
		if ($this->isColumnModified(InscriptionPeer::IS_STUDENT_KID_AND_US)) $criteria->add(InscriptionPeer::IS_STUDENT_KID_AND_US, $this->is_student_kid_and_us);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_DISABILITY_LEVEL)) $criteria->add(InscriptionPeer::STUDENT_DISABILITY_LEVEL, $this->student_disability_level);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_COMMENTS)) $criteria->add(InscriptionPeer::STUDENT_COMMENTS, $this->student_comments);
		if ($this->isColumnModified(InscriptionPeer::GRUPO_ID)) $criteria->add(InscriptionPeer::GRUPO_ID, $this->grupo_id);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_EXCURSION)) $criteria->add(InscriptionPeer::STUDENT_EXCURSION, $this->student_excursion);
		if ($this->isColumnModified(InscriptionPeer::PRICE)) $criteria->add(InscriptionPeer::PRICE, $this->price);
		if ($this->isColumnModified(InscriptionPeer::DISCOUNT)) $criteria->add(InscriptionPeer::DISCOUNT, $this->discount);
		if ($this->isColumnModified(InscriptionPeer::DISCOUNT_PERCENT)) $criteria->add(InscriptionPeer::DISCOUNT_PERCENT, $this->discount_percent);
		if ($this->isColumnModified(InscriptionPeer::DISCOUNT_AMOUNT)) $criteria->add(InscriptionPeer::DISCOUNT_AMOUNT, $this->discount_amount);
		if ($this->isColumnModified(InscriptionPeer::STUDENT_PHOTO)) $criteria->add(InscriptionPeer::STUDENT_PHOTO, $this->student_photo);
		if ($this->isColumnModified(InscriptionPeer::INSCRIPTION_NUM)) $criteria->add(InscriptionPeer::INSCRIPTION_NUM, $this->inscription_num);
		if ($this->isColumnModified(InscriptionPeer::CUSTOM_QUESTION)) $criteria->add(InscriptionPeer::CUSTOM_QUESTION, $this->custom_question);
		if ($this->isColumnModified(InscriptionPeer::CUSTOM_QUESTION_ANSWER)) $criteria->add(InscriptionPeer::CUSTOM_QUESTION_ANSWER, $this->custom_question_answer);
		if ($this->isColumnModified(InscriptionPeer::AMOUNT_BECA)) $criteria->add(InscriptionPeer::AMOUNT_BECA, $this->amount_beca);
		if ($this->isColumnModified(InscriptionPeer::AMOUNT_FIRST_PAYMENT)) $criteria->add(InscriptionPeer::AMOUNT_FIRST_PAYMENT, $this->amount_first_payment);
		if ($this->isColumnModified(InscriptionPeer::AMOUNT_SECOND_PAYMENT)) $criteria->add(InscriptionPeer::AMOUNT_SECOND_PAYMENT, $this->amount_second_payment);
		if ($this->isColumnModified(InscriptionPeer::SECOND_PAYMENT_DATE)) $criteria->add(InscriptionPeer::SECOND_PAYMENT_DATE, $this->second_payment_date);
		if ($this->isColumnModified(InscriptionPeer::FIRST_PAYMENT_DATE)) $criteria->add(InscriptionPeer::FIRST_PAYMENT_DATE, $this->first_payment_date);
		if ($this->isColumnModified(InscriptionPeer::CERTIFICATED)) $criteria->add(InscriptionPeer::CERTIFICATED, $this->certificated);
		if ($this->isColumnModified(InscriptionPeer::CERTIFICATEDNAME)) $criteria->add(InscriptionPeer::CERTIFICATEDNAME, $this->certificatedname);
		if ($this->isColumnModified(InscriptionPeer::TPV_SUFFIX)) $criteria->add(InscriptionPeer::TPV_SUFFIX, $this->tpv_suffix);
		if ($this->isColumnModified(InscriptionPeer::TPV_FIRST_PAYMENT_RESPONSE)) $criteria->add(InscriptionPeer::TPV_FIRST_PAYMENT_RESPONSE, $this->tpv_first_payment_response);
		if ($this->isColumnModified(InscriptionPeer::TPV_SECOND_PAYMENT_RESPONSE)) $criteria->add(InscriptionPeer::TPV_SECOND_PAYMENT_RESPONSE, $this->tpv_second_payment_response);
		if ($this->isColumnModified(InscriptionPeer::CULTURE)) $criteria->add(InscriptionPeer::CULTURE, $this->culture);
		if ($this->isColumnModified(InscriptionPeer::IS_PAYMENT_REMINDER_SENT)) $criteria->add(InscriptionPeer::IS_PAYMENT_REMINDER_SENT, $this->is_payment_reminder_sent);
		if ($this->isColumnModified(InscriptionPeer::KIDS_AND_US_CENTER_ID)) $criteria->add(InscriptionPeer::KIDS_AND_US_CENTER_ID, $this->kids_and_us_center_id);
		if ($this->isColumnModified(InscriptionPeer::SUMMER_FUN_CENTER_ID)) $criteria->add(InscriptionPeer::SUMMER_FUN_CENTER_ID, $this->summer_fun_center_id);
		if ($this->isColumnModified(InscriptionPeer::SUMMER_FUN_CENTER_OTHER)) $criteria->add(InscriptionPeer::SUMMER_FUN_CENTER_OTHER, $this->summer_fun_center_other);
		if ($this->isColumnModified(InscriptionPeer::SCHOOL_YEAR_ID)) $criteria->add(InscriptionPeer::SCHOOL_YEAR_ID, $this->school_year_id);
		if ($this->isColumnModified(InscriptionPeer::IS_VACCINATED)) $criteria->add(InscriptionPeer::IS_VACCINATED, $this->is_vaccinated);
		if ($this->isColumnModified(InscriptionPeer::VACCINATION_FILE)) $criteria->add(InscriptionPeer::VACCINATION_FILE, $this->vaccination_file);

		return $criteria;
	}

	
	public function buildPkeyCriteria()
	{
		$criteria = new Criteria(InscriptionPeer::DATABASE_NAME);

		$criteria->add(InscriptionPeer::ID, $this->id);

		return $criteria;
	}

	
	public function getPrimaryKey()
	{
		return $this->getId();
	}

	
	public function setPrimaryKey($key)
	{
		$this->setId($key);
	}

	
	public function copyInto($copyObj, $deepCopy = false)
	{

		$copyObj->setCreatedAt($this->created_at);

		$copyObj->setInscriptionCode($this->inscription_code);

		$copyObj->setStudentName($this->student_name);

		$copyObj->setStudentPrimerApellido($this->student_primer_apellido);

		$copyObj->setStudentSegundoApellido($this->student_segundo_apellido);

		$copyObj->setStudentBirthDate($this->student_birth_date);

		$copyObj->setStudentAddress($this->student_address);

		$copyObj->setStudentZip($this->student_zip);

		$copyObj->setStudentCity($this->student_city);

		$copyObj->setStudentSchoolYear($this->student_school_year);

		$copyObj->setStudentFriends($this->student_friends);

		$copyObj->setIsStudentDisability($this->is_student_disability);

		$copyObj->setStudentDisability($this->student_disability);

		$copyObj->setStudentAllergies($this->student_allergies);

		$copyObj->setStudentAllergiesDescription($this->student_allergies_description);

		$copyObj->setFatherName($this->father_name);

		$copyObj->setFatherPrimerApellido($this->father_primer_apellido);

		$copyObj->setFatherSegundoApellido($this->father_segundo_apellido);

		$copyObj->setFatherPhone($this->father_phone);

		$copyObj->setFatherDni($this->father_dni);

		$copyObj->setFatherMail($this->father_mail);

		$copyObj->setState($this->state);

		$copyObj->setIsFatherMailMain($this->is_father_mail_main);

		$copyObj->setMotherName($this->mother_name);

		$copyObj->setMotherPrimerApellido($this->mother_primer_apellido);

		$copyObj->setMotherSegundoApellido($this->mother_segundo_apellido);

		$copyObj->setMotherPhone($this->mother_phone);

		$copyObj->setMotherDni($this->mother_dni);

		$copyObj->setMotherMail($this->mother_mail);

		$copyObj->setIsMotherMailMain($this->is_mother_mail_main);

		$copyObj->setSplitPayment($this->split_payment);

		$copyObj->setBeca($this->beca);

		$copyObj->setStudentCourseInscription($this->student_course_inscription);

		$copyObj->setIsPaid($this->is_paid);

		$copyObj->setMethodPayment($this->method_payment);

		$copyObj->setStudentProvincia($this->student_provincia);

		$copyObj->setStudentNumTarjetaSanitaria($this->student_num_tarjeta_sanitaria);

		$copyObj->setStudentTarjetaSanitariaCompanyia($this->student_tarjeta_sanitaria_companyia);

		$copyObj->setIsStudentKidAndUs($this->is_student_kid_and_us);

		$copyObj->setStudentDisabilityLevel($this->student_disability_level);

		$copyObj->setStudentComments($this->student_comments);

		$copyObj->setGrupoId($this->grupo_id);

		$copyObj->setStudentExcursion($this->student_excursion);

		$copyObj->setPrice($this->price);

		$copyObj->setDiscount($this->discount);

		$copyObj->setDiscountPercent($this->discount_percent);

		$copyObj->setDiscountAmount($this->discount_amount);

		$copyObj->setStudentPhoto($this->student_photo);

		$copyObj->setInscriptionNum($this->inscription_num);

		$copyObj->setCustomQuestion($this->custom_question);

		$copyObj->setCustomQuestionAnswer($this->custom_question_answer);

		$copyObj->setAmountBeca($this->amount_beca);

		$copyObj->setAmountFirstPayment($this->amount_first_payment);

		$copyObj->setAmountSecondPayment($this->amount_second_payment);

		$copyObj->setSecondPaymentDate($this->second_payment_date);

		$copyObj->setFirstPaymentDate($this->first_payment_date);

		$copyObj->setCertificated($this->certificated);

		$copyObj->setCertificatedname($this->certificatedname);

		$copyObj->setTpvSuffix($this->tpv_suffix);

		$copyObj->setTpvFirstPaymentResponse($this->tpv_first_payment_response);

		$copyObj->setTpvSecondPaymentResponse($this->tpv_second_payment_response);

		$copyObj->setCulture($this->culture);

		$copyObj->setIsPaymentReminderSent($this->is_payment_reminder_sent);

		$copyObj->setKidsAndUsCenterId($this->kids_and_us_center_id);

		$copyObj->setSummerFunCenterId($this->summer_fun_center_id);

		$copyObj->setSummerFunCenterOther($this->summer_fun_center_other);

		$copyObj->setSchoolYearId($this->school_year_id);

		$copyObj->setIsVaccinated($this->is_vaccinated);
                
                $copyObj->setVaccinationFile($this->vaccination_file);


		if ($deepCopy) {
									$copyObj->setNew(false);

			foreach($this->getInscriptionServiceSchedules() as $relObj) {
				$copyObj->addInscriptionServiceSchedule($relObj->copy($deepCopy));
			}

		} 

		$copyObj->setNew(true);

		$copyObj->setId(NULL); 
	}

	
	public function copy($deepCopy = false)
	{
				$clazz = get_class($this);
		$copyObj = new $clazz();
		$this->copyInto($copyObj, $deepCopy);
		return $copyObj;
	}

	
	public function getPeer()
	{
		if (self::$peer === null) {
			self::$peer = new InscriptionPeer();
		}
		return self::$peer;
	}

	
	public function setCourse($v)
	{


		if ($v === null) {
			$this->setStudentCourseInscription(NULL);
		} else {
			$this->setStudentCourseInscription($v->getId());
		}


		$this->aCourse = $v;
	}


	
	public function getCourse($con = null)
	{
		if ($this->aCourse === null && ($this->student_course_inscription !== null)) {
						include_once 'lib/model/inscriptions/om/BaseCoursePeer.php';

			$this->aCourse = CoursePeer::retrieveByPK($this->student_course_inscription, $con);

			
		}
		return $this->aCourse;
	}

	
	public function setProvincia($v)
	{


		if ($v === null) {
			$this->setStudentProvincia(NULL);
		} else {
			$this->setStudentProvincia($v->getId());
		}


		$this->aProvincia = $v;
	}


	
	public function getProvincia($con = null)
	{
		if ($this->aProvincia === null && ($this->student_provincia !== null)) {
						include_once 'lib/model/inscriptions/om/BaseProvinciaPeer.php';

			$this->aProvincia = ProvinciaPeer::retrieveByPK($this->student_provincia, $con);

			
		}
		return $this->aProvincia;
	}

	
	public function setGrupo($v)
	{


		if ($v === null) {
			$this->setGrupoId(NULL);
		} else {
			$this->setGrupoId($v->getId());
		}


		$this->aGrupo = $v;
	}


	
	public function getGrupo($con = null)
	{
		if ($this->aGrupo === null && ($this->grupo_id !== null)) {
						include_once 'lib/model/inscriptions/om/BaseGrupoPeer.php';

			$this->aGrupo = GrupoPeer::retrieveByPK($this->grupo_id, $con);

			
		}
		return $this->aGrupo;
	}

	
	public function setKidsAndUsCenter($v)
	{


		if ($v === null) {
			$this->setKidsAndUsCenterId(NULL);
		} else {
			$this->setKidsAndUsCenterId($v->getId());
		}


		$this->aKidsAndUsCenter = $v;
	}


	
	public function getKidsAndUsCenter($con = null)
	{
		if ($this->aKidsAndUsCenter === null && ($this->kids_and_us_center_id !== null)) {
						include_once 'lib/model/inscriptions/om/BaseKidsAndUsCenterPeer.php';

			$this->aKidsAndUsCenter = KidsAndUsCenterPeer::retrieveByPK($this->kids_and_us_center_id, $con);

			
		}
		return $this->aKidsAndUsCenter;
	}

	
	public function setSummerFunCenter($v)
	{


		if ($v === null) {
			$this->setSummerFunCenterId(NULL);
		} else {
			$this->setSummerFunCenterId($v->getId());
		}


		$this->aSummerFunCenter = $v;
	}


	
	public function getSummerFunCenter($con = null)
	{
		if ($this->aSummerFunCenter === null && ($this->summer_fun_center_id !== null)) {
						include_once 'lib/model/summerFun/om/BaseSummerFunCenterPeer.php';

			$this->aSummerFunCenter = SummerFunCenterPeer::retrieveByPK($this->summer_fun_center_id, $con);

			
		}
		return $this->aSummerFunCenter;
	}

	
	public function setSchoolYear($v)
	{


		if ($v === null) {
			$this->setSchoolYearId(NULL);
		} else {
			$this->setSchoolYearId($v->getId());
		}


		$this->aSchoolYear = $v;
	}


	
	public function getSchoolYear($con = null)
	{
		if ($this->aSchoolYear === null && ($this->school_year_id !== null)) {
						include_once 'lib/model/inscriptions/om/BaseSchoolYearPeer.php';

			$this->aSchoolYear = SchoolYearPeer::retrieveByPK($this->school_year_id, $con);

			
		}
		return $this->aSchoolYear;
	}

	
	public function initInscriptionServiceSchedules()
	{
		if ($this->collInscriptionServiceSchedules === null) {
			$this->collInscriptionServiceSchedules = array();
		}
	}

	
	public function getInscriptionServiceSchedules($criteria = null, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionServiceSchedulePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collInscriptionServiceSchedules === null) {
			if ($this->isNew()) {
			   $this->collInscriptionServiceSchedules = array();
			} else {

				$criteria->add(InscriptionServiceSchedulePeer::INSCRIPTION_ID, $this->getId());

				InscriptionServiceSchedulePeer::addSelectColumns($criteria);
				$this->collInscriptionServiceSchedules = InscriptionServiceSchedulePeer::doSelect($criteria, $con);
			}
		} else {
						if (!$this->isNew()) {
												

				$criteria->add(InscriptionServiceSchedulePeer::INSCRIPTION_ID, $this->getId());

				InscriptionServiceSchedulePeer::addSelectColumns($criteria);
				if (!isset($this->lastInscriptionServiceScheduleCriteria) || !$this->lastInscriptionServiceScheduleCriteria->equals($criteria)) {
					$this->collInscriptionServiceSchedules = InscriptionServiceSchedulePeer::doSelect($criteria, $con);
				}
			}
		}
		$this->lastInscriptionServiceScheduleCriteria = $criteria;
		return $this->collInscriptionServiceSchedules;
	}

	
	public function countInscriptionServiceSchedules($criteria = null, $distinct = false, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionServiceSchedulePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		$criteria->add(InscriptionServiceSchedulePeer::INSCRIPTION_ID, $this->getId());

		return InscriptionServiceSchedulePeer::doCount($criteria, $distinct, $con);
	}

	
	public function addInscriptionServiceSchedule(InscriptionServiceSchedule $l)
	{
		$this->collInscriptionServiceSchedules[] = $l;
		$l->setInscription($this);
	}


	
	public function getInscriptionServiceSchedulesJoinServiceSchedule($criteria = null, $con = null)
	{
				include_once 'lib/model/inscriptions/om/BaseInscriptionServiceSchedulePeer.php';
		if ($criteria === null) {
			$criteria = new Criteria();
		}
		elseif ($criteria instanceof Criteria)
		{
			$criteria = clone $criteria;
		}

		if ($this->collInscriptionServiceSchedules === null) {
			if ($this->isNew()) {
				$this->collInscriptionServiceSchedules = array();
			} else {

				$criteria->add(InscriptionServiceSchedulePeer::INSCRIPTION_ID, $this->getId());

				$this->collInscriptionServiceSchedules = InscriptionServiceSchedulePeer::doSelectJoinServiceSchedule($criteria, $con);
			}
		} else {
									
			$criteria->add(InscriptionServiceSchedulePeer::INSCRIPTION_ID, $this->getId());

			if (!isset($this->lastInscriptionServiceScheduleCriteria) || !$this->lastInscriptionServiceScheduleCriteria->equals($criteria)) {
				$this->collInscriptionServiceSchedules = InscriptionServiceSchedulePeer::doSelectJoinServiceSchedule($criteria, $con);
			}
		}
		$this->lastInscriptionServiceScheduleCriteria = $criteria;

		return $this->collInscriptionServiceSchedules;
	}


  public function __call($method, $arguments)
  {
    if (!$callable = sfMixer::getCallable('BaseInscription:'.$method))
    {
      throw new sfException(sprintf('Call to undefined method BaseInscription::%s', $method));
    }

    array_unshift($arguments, $this);

    return call_user_func_array($callable, $arguments);
  }


} 
