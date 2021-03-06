<?php
// auto-generated by sfPropelAdmin
// date: 2014/05/14 10:45:54
?>
<?php use_helper('I18N', 'Date') ?>

<?php use_stylesheet('/sf/sf_admin/css/main') ?>

<div id="sf_admin_container">

<h1><?php echo __('Llistat d\'inscripcions', 
array()) ?></h1>

<div id="sf_admin_header">
<?php include_partial('Inscription/list_header', array('pager' => $pager)) ?>
<?php include_partial('Inscription/list_messages', array('pager' => $pager)) ?>
</div>

<div id="sf_admin_bar">
<?php include_partial('filters', array('filters' => $filters)) ?>
</div>

<div id="sf_admin_content">
    
<?php if ($sf_flash->has('notice')): ?>
<div class="save-ok">
<h2><?php echo __($sf_flash->get('notice')) ?></h2>
</div>
<?php endif; ?>

    
<?php if (!$pager->getNbResults()): ?>
<?php echo __('no result') ?>
<?php else: ?>
<?php include_partial('Inscription/list', array('pager' => $pager)) ?>
<?php endif; ?>
<?php include_partial('list_actions') ?>
</div>

<div id="sf_admin_footer">
<?php include_partial('Inscription/list_footer', array('pager' => $pager)) ?>
</div>

</div>

<div id="dialog" title="<?php echo __('Seleccione columnas a exportar') ?>" style="display:none;font-size:12px">
	<div class="dialog-options">
		<?php echo __('Seleccionar:') ?>
		<a id="export-all" href="#"><?php echo __('Todas') ?></a>&nbsp;|
		<a id="export-none" href="#"><?php echo __('Ninguna') ?></a>
	</div>
	<form id="form-export" action="<?php echo isset($filters["csv-export-link"]) ?  $filters["csv-export-link"] : url_for('@export', array('id'=> 'all'))?>" method="post">
		<div style="max-height: 335px; overflow: auto; margin-bottom: 10px">
			<table class="sf_admin_list">
				<tr>
					<th></th>
					<th><?php echo __('Columna') ?></th>
				</tr>
				<?php foreach ($columns as $key => $value): ?>
				<tr>
					<td style="width:20px"><input type="checkbox" name="columns[<?php echo $key ?>]" value="<?php echo $key ?>"></td>
					<td><?php echo $value ?></td>
				</tr>
				<?php endforeach; ?>
			</table>
		</div>
		<label><?php echo __('Nombre fichero') ?>:</label>
		<input type="text" name="filename" style="width:200px"/>
		<input type="hidden" name="ids" value="<?php echo isset($filters["csv-export-link"]) ? $filters["csv-export-link"] : '' ?>"/>		
	</form>
</div>

<script type="text/javascript">
var selectFileName = "<?php echo __('Debe introducir un nombre para el fichero') ?>";
var minNumColumns = "<?php echo __('Debe seleccionar al menos una columna') ?>";
</script>