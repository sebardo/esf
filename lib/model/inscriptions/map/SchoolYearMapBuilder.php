<?php



class SchoolYearMapBuilder {

	
	const CLASS_NAME = 'lib.model.inscriptions.map.SchoolYearMapBuilder';

	
	private $dbMap;

	
	public function isBuilt()
	{
		return ($this->dbMap !== null);
	}

	
	public function getDatabaseMap()
	{
		return $this->dbMap;
	}

	
	public function doBuild()
	{
		$this->dbMap = Propel::getDatabaseMap('propel');

		$tMap = $this->dbMap->addTable('school_year');
		$tMap->setPhpName('SchoolYear');

		$tMap->setUseIdGenerator(true);

		$tMap->addPrimaryKey('ID', 'Id', 'int', CreoleTypes::INTEGER, true, null);

		$tMap->addColumn('ORDEN', 'Orden', 'int', CreoleTypes::INTEGER, false, null);

	} 
} 