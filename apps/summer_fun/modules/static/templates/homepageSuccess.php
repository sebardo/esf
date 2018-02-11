<?php $culture = $sf_user->getCulture(); ?>
<div id="container-home">
    <div class="content-home">      
        <div class="content-intro">    
            <?php echo image_tag("summer_fun/details/home_2018.jpg", array('alt' => '', 'class' => 'main-image')); ?>
            <div class="intro">
                <h2><?php echo __("ENGLISH SUMMER FUN[1]ESTIU [2]", array('[1]' => '<br /><span>', '[2]' => date('Y') . "</span>")); ?></h2>
                <p><?php echo __("home_description_1", array('[1]' => '&amp;')); ?></p>
                <p><?php echo __("home_description_2", array('[1]' => '&amp;', '[2]' => '"')); ?></p>
                <p class="nm" style="float:right"><?php echo link_to_i18n(__("On?"), '@centers', array('title' => __("On seran?"), 'class' => 'where')); ?></p>
            </div>
            <br class="clear" />
        </div>
        <div class="box home1">
            <h3><?php echo __("home_1_title"); ?></h3>
            <p><?php echo __("home_1_description"); ?></p>
        </div>
        <div class="box home2">
            <h3><?php echo __("home_2_title"); ?></h3>
            <p><?php echo __("home_2_description"); ?></p>
        </div>
        <div class="box home3">
            <h3<?php echo $sf_user->getCulture() == 'it' ? ' class="it"':''; ?>><?php echo __("home_3_title"); ?></h3>
            <p><?php echo __("home_3_description"); ?></p>
        </div>
        <br class="clear" />
        
        <a href="<?php echo $url_kids; ?>" class="centers" target="_blank"><?php echo __("Coneix els centres Kids[1]Us", array('[1]' => '&')); ?></a>
        
    </div>
</div>