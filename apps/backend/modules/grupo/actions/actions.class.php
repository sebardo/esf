<?php

/**
 * grupo actions.
 *
 * @package    kids
 * @subpackage grupo
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 2288 2006-10-02 15:22:13Z fabien $
 */
class grupoActions extends autogrupoActions
{
	protected function addFiltersCriteria($c)
	{
		$user = $this->getUser();
		if (!$user->hasCredential('administrador')) {
			$profile = $user->getProfile();
			$c->addJoin(GrupoPeer::CENTRO_ID, SummerFunCenterPeer::ID);
            // JOIN
            $c->addJoin(SummerFunCenterHasProfilePeer::SUMMER_FUN_CENTER_ID, SummerFunCenterPeer::ID);
            // WHERE
            $c->add(SummerFunCenterHasProfilePeer::PROFILE_ID, $profile->getId());
		}

		parent::addFiltersCriteria($c);
	}

    protected function saveGrupo($grupo)
    {
        parent::saveGrupo($grupo);

        # Update many-to-many for "grupo_has_profesors"
        if (isset($_POST['id'])) { # when updating only
            mysql::updateTable(
                'inscription',
                array('grupo_id' => null),
                array('grupo_id' => $grupo->getPrimaryKey())
            );
        }

        if (isset($_POST['inscriptions'])) {
            foreach ($_POST['inscriptions'] as $inscription) {
                mysql::updateTable(
                    'inscription',
                    array('grupo_id' => $grupo->getPrimaryKey()),
                    array('id' => $inscription)
                );
            }
        }
    }

    /**
     * AJAX action - search for inscriptions during group edit
     */

    public function executeGetInscriptions()
    {
        sfConfig::set('sf_web_debug', false);
        $this->getResponse()->setContentType('application/json');


        return $this->renderText(json_encode(array('results' => Inscription::getListForGroup($_GET))));
    }
}
