<?php
$classHome = $classActivities = $classCenters = $classInscriptions = "";
$module = $sf_params->get('module');
$action = $sf_params->get('action');
$locale = $sf_user->getCulture();

switch ($module)
{
    case 'static':
        if($action == "homepage") $classHome = "active";
        else $classActivities = "active";
        break;
    
    case 'centers':
        $classCenters = "active";
        break;
    case 'inscription';
        $classInscriptions = "active";
        break;
}
?>
<?php if(($module != "centers") || ($action == "zones")): ?>
    <ul id="navigation" <?php echo $locale == 'fr' ? 'style="left:619px"' : '' ?>>
        <li class="nav1" <?php echo $locale == 'fr' ? 'style="display:none"' : '' ?>><?php echo link_to_i18n(__("Inici"), '@index', array('title' => __("Inici"), 'class' => "$classHome")); ?></li>
        <li class="nav2" <?php echo $locale == 'fr' ? 'style="display:none"' : '' ?>><?php echo link_to_i18n(__("Activitats"), '@party_time', array('title' => __("Activitats"), 'class' => "$classActivities")); ?></li>
        <li class="nav3"><?php echo link_to_i18n(__("On?"), '@centers', array('title' => __("On?"), 'class' => "$classCenters")); ?></li>
		<li class="nav4"><?php echo link_to_i18n(__("Inscripcions"), '@inscription_step1', array('title' => __("Inscripcions"), 'class' => "$classInscriptions")); ?></li>
		<li class="nav5" <?php echo $locale == 'fr' ? 'style="display:none"' : '' ?>><a href="http://blog.englishsummerfun.com/<?php echo $locale == 'es' ? $locale : ($locale == 'ca' ? $locale : 'es') ?>" class="" title="Blog">Blog</a></li>
    </ul>
<?php endif; ?>