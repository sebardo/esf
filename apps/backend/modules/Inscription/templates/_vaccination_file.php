<?php

if ($inscription->getVaccinationFile() != '')
{
	echo '<a target="_blank" href="/uploads/summer-fun/student/'.$inscription->getVaccinationFile().'">'.$inscription->getVaccinationFile().'</a>';
}
else {
	echo '';
}

