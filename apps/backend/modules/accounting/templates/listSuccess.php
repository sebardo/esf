<div id="sf_admin_container">
	<h1><?php echo __('Comptabilitat') ?></h1>
	<div id="sf_admin_header">
		<?php include_partial('filters', array('filters' => $filters)) ?>
	</div>

	<div id="sf_admin_content" style="margin:0;overflow:auto">
		<table cellspacing="0" class="sf_admin_list">
			<thead>
				<tr>
					<?php include_partial('list_th_tabular') ?>
				</tr>
			</thead>

			<tbody>
				<?php $i = 1; foreach ($pager->getResultsAsRowset() as $inscription): $odd = fmod(++$i, 2) ?>
					<tr class="sf_admin_row_<?php echo $odd ?>">					
						<?php include_partial('list_td_tabular', array('inscription' => $inscription, 'child' => false)) ?>
						<?php include_partial('list_td_actions', array('inscription' => $inscription, 'child' => false)) ?>
					</tr>
					<?php include_partial('list_tr_children', array('inscription' => $inscription, 'inscriptions' => $inscriptions)) ?>
				<?php endforeach; ?>
			</tbody>

            <tfoot>
                <tr>
                    <th colspan="19">
                        <div class="float-right">
                            <?php if ($pager->haveToPaginate()): ?>
                                <?php echo link_to(image_tag(sfConfig::get('sf_admin_web_dir').'/images/first.png', array('align' => 'absmiddle', 'alt' => __('First'), 'title' => __('First'))), 'accounting/list?page=1') ?>
                                <?php echo link_to(image_tag(sfConfig::get('sf_admin_web_dir').'/images/previous.png', array('align' => 'absmiddle', 'alt' => __('Previous'), 'title' => __('Previous'))), 'accounting/list?page='.$pager->getPreviousPage()) ?>

                                <?php foreach ($pager->getLinks() as $page): ?>
                                    <?php echo link_to_unless($page == $pager->getPage(), $page, 'accounting/list?page='.$page) ?>
                                <?php endforeach; ?>

                                <?php echo link_to(image_tag(sfConfig::get('sf_admin_web_dir').'/images/next.png', array('align' => 'absmiddle', 'alt' => __('Next'), 'title' => __('Next'))), 'accounting/list?page='.$pager->getNextPage()) ?>
                                <?php echo link_to(image_tag(sfConfig::get('sf_admin_web_dir').'/images/last.png', array('align' => 'absmiddle', 'alt' => __('Last'), 'title' => __('Last'))), 'accounting/list?page='.$pager->getLastPage()) ?>
                            <?php endif; ?>
                        </div>
                        <?php echo format_number_choice('[0] no result|[1] 1 result|(1,+Inf] %1% results', array('%1%' => $pager->getNbResults()), $pager->getNbResults()) ?>
                    </th>
                </tr>
            </tfoot>
		</table>
		<?php foreach ($totals as $total): ?>
			<?php if ($total['NUM_REG'] > 0): ?>
			<div style="float:right;">
				<table class="sf_admin_list table_totals">
					<tbody>
						<tr>
							<td><?php echo __('Import total') ?>:</td>
							<td style="color:green"><?php echo number_format($total['IMPORTE_TOTAL_A_PAGAR'], 2, ',', '.') ?></td>
						</tr>
						<tr>
							<td><?php echo __('Importe pendent de cobrar') ?>:</td>
							<td style="color:<?php echo $total['IMPORTE_TOTAL_PENDIENTE'] > 0 ? 'red' : ($total['IMPORTE_TOTAL_PENDIENTE'] == 0 ? 'green' : 'orange') ?>"><?php echo number_format($total['IMPORTE_TOTAL_PENDIENTE'], 2, ',', '.') ?></td>
						</tr>
                        <tr>
                            <td><?php echo __('number_of_becas') ?>:</td>
                            <td style="color:green"><?php echo number_format($total['CANTIDAD_BECAS'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('amount_becas') ?>:</td>
                            <td style="color:green"><?php echo number_format($total['IMPORTE_BECA'], 2, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td><?php echo __('amount_first_payment') ?>:</td>
                            <td style="color:green"><?php echo number_format($total['IMPORTE_PRIMER_PAGO'], 2, ',', '.') ?></td>
                        </tr>
					</tbody>
				</table>
			</div>
			<?php endif; ?>
		<?php break; ?>
		<?php endforeach;?>
	</div>
</div>

<div id="dialog" title="<?php echo __('Seleccione columnas a exportar') ?>" style="display:none;font-size:12px">
    <div class="dialog-options">
        <?php echo __('Seleccionar:') ?>
        <a id="export-all" href="#"><?php echo __('Todas') ?></a>&nbsp;|
        <a id="export-none" href="#"><?php echo __('Ninguna') ?></a>
    </div>
    <form id="form-export" action="<?php echo url_for('accounting/export') ?>" method="post">
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
        <label><?php echo __('Nombre fichero') ?>:</label>
        <input type="text" name="filename" style="width:200px"/>
        <input type="hidden" name="ids" value="<?php echo isset($filters["csv-export-link"]) ? $filters["csv-export-link"] : '' ?>"/>
    </form>
</div>

<script type="text/javascript">

    var routeUpdate = '<?php echo url_for('accounting/update') ?>';
    var markAsPaidQuestion = '<?php echo __('mark_as_paid_question') ?>';
    var markAsHalfPaidQuestion = '<?php echo __('mark_as_half_paid_question') ?>';
    var selectFileName = "<?php echo __('Debe introducir un nombre para el fichero') ?>";
    var minNumColumns = "<?php echo __('Debe seleccionar al menos una columna') ?>";
    var wrongDiscounts = "<?php echo __('wrong_discounts') ?>";

    jQuery(function($) {
        $('td img[id^=trigger]').toggle(false);
        $('td img[id^=trigger]').click(function (e) {
            var id = $(this).attr('id').split("_").pop();
            if ($('#' + id).is(':disabled')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    });

</script>