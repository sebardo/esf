<div id="header">
    <h1>
        <?php echo image_link('summer_fun/logos/kidsandus_2018.png', '@index', array('alt' => 'Kidsandus'), array('title' => 'Kidsandus')); ?>
    </h1>
    <div id="idiomes">
        <?php echo link_to("català", '@index_ca', array('title' => "versió en català")) ?> |
        <?php echo link_to("español", '@index_es', array('title' => "versión en español")) ?> |
        <?php echo link_to("italiano", '@index_it', array('title' => "versione italiana")) ?>
    </div>
    <?php include_partial('global/navigation'); ?>
</div>