<?php

/**
 * Subclass for performing query and update operations on the 'inscription' table.
 *
 *
 *
 * @package lib.model.inscriptions
 */
class InscriptionPeer extends BaseInscriptionPeer
{
    const NO_SHELTER = 0;
    const MORNING_SHELTER = 1;
    const AFTERNOON_SHELTER = 2;
    const MORNING_AND_AFTERNOON_SHELTER = 3;

    const STATE_ACCEPTED = 0;
    const STATE_WAITING = 1;

    const IS_PAID_0 = 0;
    const IS_PAID_50 = 1;
    const IS_PAID_100 = 2;
    const IS_PAID_TPV = 3;

    const METHOD_PAYMENT_TRANSFER = 0;
    const METHOD_PAYMENT_CASH = 1;
    const METHOD_PAYMENT_RECIBO = 2;
    const METHOD_PAYMENT_TPV = 3;

    const SPLIT_PAYMENT_FALSE = 0;
    const SPLIT_PAYMENT_TRUE = 1;

    public static function getStateFilterArray()
    {
        $statusNamesFilter = array(
            self::STATUS_IMPORTED => 'Importat2',
            self::STATUS_PENDING => 'Pendent',
            self::STATUS_ACCEPTED => 'Acceptat',
            self::STATUS_REJECTED => 'No acceptat',
        );

        return $statusNamesFilter;
    }

    public static function getPaymentMethodFilterArray()
    {
        return self::$methodPaymentMap;
    }

    private static $splitShelterMap = array(
        self::NO_SHELTER => 'No',
        self::MORNING_SHELTER => 'Matí',
        self::AFTERNOON_SHELTER => 'Tarda',
        self::MORNING_AND_AFTERNOON_SHELTER => 'Matí i tarda',
    );

    private static $splitPaymentMap = array(
        self::SPLIT_PAYMENT_TRUE => 'Si, pagar ara el 50%',
        self::SPLIT_PAYMENT_FALSE => 'No, pagar ara el 100%',

    );

    private static $methodPaymentMap = array(
        self::METHOD_PAYMENT_TRANSFER => 'Transferència bancària',
        self::METHOD_PAYMENT_CASH => 'Pagament en efectiu al centre',
        self::METHOD_PAYMENT_RECIBO => 'Pagament amb rebut domiciliat',
        self::METHOD_PAYMENT_TPV => 'Pagament amb targeta bancària',
    );

    private static $stateNamesMap = array(
        self::STATE_ACCEPTED => 'Inscrit',
        self::STATE_WAITING => 'En llista d\'espera',
    );

    private static $isPaidNamesMap = array(
        self::IS_PAID_0 => 'No Pagat',
        self::IS_PAID_50 => '50% Pagat',
        self::IS_PAID_100 => '100% Pagat',
        self::IS_PAID_TPV => 'En espera de pagament amb TPV',

    );

    public static function getShelterNames()
    {
        return self::$splitShelterMap;
    }

    public static function getShelterName($id)
    {
        if (!isset(self::$splitShelterMap[$id])) {

            throw new Exception();

        }
        return self::$splitShelterMap[$id];
    }

    public static function getMethodPaymentNames()
    {
        return self::$methodPaymentMap;
    }

    public static function getMethodPaymentName($id)
    {

        if (!isset(self::$methodPaymentMap[$id])) {
            throw new Exception();
        }
        return __(self::$methodPaymentMap[$id]);

    }

    public static function getIsPaidNames()
    {
        return array(
            self::IS_PAID_0 => 'No Pagat',
            self::IS_PAID_50 => '50% Pagat',
            self::IS_PAID_100 => '100% Pagat'
        );
    }

    public static function getIsPaidName($id)
    {
        if (!isset(self::$isPaidNamesMap[$id])) {
            throw new Exception();
        }
        return self::$isPaidNamesMap[$id];

    }

    public static function getStatesNames()
    {
        return array_map('__', self::$stateNamesMap);
    }


    public static function getStateName($id)
    {
        if (!isset(self::$stateNamesMap[$id])) {
            throw new Exception();
        }
        return self::$stateNamesMap[$id];
    }

    public static function getSplitPaymentName($id)
    {

        if (!isset(self::$splitPaymentMap[$id])) {
            throw new Exception();
        }
        return __(self::$splitPaymentMap[$id]);

    }

    public static function doSelectInscriptionsByCourse($id)
    {
        $c = new Criteria();
        $c->add(self::STUDENT_COURSE_INSCRIPTION, $id);
        $c->add(self::STATE, self::STATE_ACCEPTED);
        $inscriptions = self::doSelect($c);
        return $inscriptions;
    }

    public static function doSelectInscriptionsConfirmedByCourse($id)
    {
        $c = new Criteria();
        $c->add(self::STUDENT_COURSE_INSCRIPTION, $id);
        $c->add(self::STATE, self::STATE_ACCEPTED);

        /** @var Criterion $criterion1 */
        $criterion1 = $c->getNewCriterion(self::IS_PAID, 2, Criteria::LESS_EQUAL);
        /** @var Criterion $criterion2 */
        $criterion2 = $c->getNewCriterion(self::IS_PAID, 3);
        /** @var Criterion $criterion3 */
        $criterion3 = $c->getNewCriterion(self::CREATED_AT, 'TIMESTAMPDIFF(MINUTE, created_at, NOW()) <= 10', Criteria::CUSTOM);
        $criterion2->addAnd($criterion3);

        $criterion1->addOr($criterion2);

        $c->add($criterion1);

        $inscriptions = self::doSelect($c);
        return $inscriptions;
    }

    /*
    public static function getNumberInscriptionAccepted($id)
    {
        $c = new Criteria();
        $c->add(CoursePeer::ID, $id);
        $c->add(InscriptionPeer::STATE, InscriptionPeer::STATE_ACCEPTED);
        $c->addJoin(self::STUDENT_COURSE_INSCRIPTION, CoursePeer::ID);
        $inscriptions = self::doSelect($c);

        return $inscriptions;
    }
    */

    public static function getNumberInscriptionWaiting($id)
    {
        $c = new Criteria();
        $c->add(CoursePeer::ID, $id);
        $c->add(InscriptionPeer::STATE, InscriptionPeer::STATE_WAITING);
        $c->addJoin(self::STUDENT_COURSE_INSCRIPTION, CoursePeer::ID);
        $inscriptions = self::doSelect($c);

        return $inscriptions;
    }

    public static function getCourseByCenter($id)
    {
        $c = new Criteria();
        $c->add(self::SUMMER_FUN_CENTER_ID, $id);
        $c->addJoin(self::WEEK_ID, WeekPeer::ID);

        $c->addAscendingOrderByColumn(WeekPeer::STARTS_AT);
        $courses = self::doSelect($c);

        return $courses;
    }

    public static function getLastCodeInscription()
    {
        $conexion = Propel::getConnection();
        $consulta = 'SELECT MAX(%s) AS max FROM %s';
        $consulta = sprintf($consulta, InscriptionPeer::INSCRIPTION_CODE, InscriptionPeer::TABLE_NAME);
        $sentencia = $conexion->prepareStatement($consulta);
        $resultset = $sentencia->executeQuery();
        $resultset->next();
        $max = $resultset->getInt('max');

        return $max;
    }

    public static function getLastNumInscription()
    {
        $conexion = Propel::getConnection();
        $consulta = 'SELECT MAX(%s) AS max FROM %s';
        $consulta = sprintf($consulta, InscriptionPeer::INSCRIPTION_NUM, InscriptionPeer::TABLE_NAME);
        $sentencia = $conexion->prepareStatement($consulta);
        $resultset = $sentencia->executeQuery();
        $resultset->next();
        $max = $resultset->getInt('max');

        return $max;
    }

    public static function doSelectInsciptionsByInscriptionCode($inscriptionCode)
    {
        $c = new Criteria();
        $c->add(self::INSCRIPTION_CODE, $inscriptionCode);
        $inscriptions = self::doSelect($c);

        return $inscriptions;
    }

    public static function addCriteriaSearchByWeek($c, $weeks)
    {
        $c->addJoin(self::STUDENT_COURSE_INSCRIPTION, CoursePeer::ID);
        $c->add(CoursePeer::ID, $weeks, Criteria::IN);

        return $c;
    }

    public static function retrieveByInscriptionNum($inscriptionNum)
    {
        $con = Propel::getConnection(self::DATABASE_NAME);
        $criteria = new Criteria();
        $criteria->add(InscriptionPeer::INSCRIPTION_NUM, $inscriptionNum);
        $criteria->addAscendingOrderByColumn(InscriptionPeer::INSCRIPTION_CODE);

        return InscriptionPeer::doSelect($criteria, $con);
    }
    
    public static function retrieveByStudentCourseInscription($studentCourseInscription)
    {
        $con = Propel::getConnection(self::DATABASE_NAME);
        $criteria = new Criteria();
        $criteria->add(InscriptionPeer::STUDENT_COURSE_INSCRIPTION, $studentCourseInscription);
        $criteria->addAscendingOrderByColumn(InscriptionPeer::INSCRIPTION_CODE);

        return InscriptionPeer::doSelect($criteria, $con);
    }

    public static function retrieveByInscriptionCode($inscriptionCode)
    {
        $con = Propel::getConnection(self::DATABASE_NAME);
        $criteria = new Criteria();
        $criteria->add(InscriptionPeer::INSCRIPTION_CODE, $inscriptionCode);
        $criteria->addAscendingOrderByColumn(InscriptionPeer::INSCRIPTION_CODE);

        return InscriptionPeer::doSelect($criteria, $con);
    }

    public static function findByIdAndTpvSuffix($number)
    {
        $query = "SELECT id FROM inscription WHERE CONCAT(id, '-', tpv_suffix) = '$number'";
        $con = sfContext::getInstance()->getDatabaseConnection('propel');
        $stmt = $con->prepareStatement($query);

        $resultSet = $stmt->executeQuery(ResultSet::FETCHMODE_ASSOC);
        if ($resultSet->next()) {
            $inscription['id'] = $resultSet->getInt('id');
            return $inscription;
        }

        return null;
    }

    public static function retrieveForSecondPaymentMailing($date)
    {
        $con = Propel::getConnection(self::DATABASE_NAME);
        $criteria = new Criteria();
        $criteria->add(InscriptionPeer::SPLIT_PAYMENT, self::SPLIT_PAYMENT_TRUE);
        $criteria->addJoin(self::STUDENT_COURSE_INSCRIPTION, CoursePeer::ID);
        $criteria->addJoin(CoursePeer::SUMMER_FUN_CENTER_ID, SummerFunCenterPeer::ID);
        $criteria->add(SummerFunCenterPeer::SECOND_PAYMENT_MAILING_DATE, $date);

        return InscriptionPeer::doSelect($criteria, $con);
    }

    public static function updateSplitPayment($inscriptionNum, $value)
    {
        $connection = Propel::getConnection();
        $query = "UPDATE inscription SET split_payment = $value WHERE inscription_num = $inscriptionNum";
        $statement = $connection->prepareStatement($query);
        $resultset = $statement->executeQuery();
    }

    public static function updateSendReminder($inscriptionNum, $value)
    {
        $connection = Propel::getConnection();
        $query = "UPDATE inscription SET is_payment_reminder_sent = $value WHERE inscription_num = $inscriptionNum";
        $statement = $connection->prepareStatement($query);
        $resultset = $statement->executeQuery();
    }

    public static function retrieveByInscriptionNumAndReminderSent($inscriptionNum)
    {
        $con = Propel::getConnection(self::DATABASE_NAME);
        $criteria = new Criteria();
        $criteria->add(InscriptionPeer::INSCRIPTION_NUM, $inscriptionNum);
        $criteria->add(InscriptionPeer::IS_PAYMENT_REMINDER_SENT, 1);

        return InscriptionPeer::doSelect($criteria, $con);
    }
}
