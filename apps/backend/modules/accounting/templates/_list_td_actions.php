<td nowrap>
	<?php if ($inscription['NUM_REG'] == 1): ?>
	<ul class="sf_admin_td_actions">
  		<li><a id="edit-<?php echo $inscription['id'] ?>" href="#"><img src="/sf/sf_admin/images/edit_icon.png" title="<?php echo __("Editar") ?>" alt="<?php echo __("Editar") ?>"></a></li>
                <li><a id="save-<?php echo $inscription['id'] ?>" href="#">
                        <img style="width: 20px;margin-left: -2px;margin-bottom: -2px;margin-right: -2px;" src="/sfAdminDashPlugin/images/icon_toolbar/filesave.png" title="<?php echo __("Guardar") ?>" alt="<?php echo __("Guardar") ?>"></a></li>
		<li class="td-split-<?php echo $inscription['id'] ?>" <?php echo !$inscription['split_payment'] ? 'style="display:none"' : '' ?>>
                    <a id="mark-half-paid-<?php echo $inscription['id'] ?>" href="#" data-all="0" style="text-decoration: none">
                        <img style="width: 23px;  margin-left: -2px; margin-bottom: -2px;margin-right: -2px;" src="/sf/sf_admin/images/half2.png" title="<?php echo __("mark_half_as_paid") ?>" alt="<?php echo __("mark_as_paid") ?>">
                    </a>
                </li>
		<li>
                    <a id="mark-paid-<?php echo $inscription['id'] ?>" href="#" data-all="0" style="text-decoration: none">
                        <img style="width: 23px;  margin-left: -2px; margin-bottom: -2px;margin-right: -2px;" src="/sf/sf_admin/images/100.png" title="<?php echo __("mark_as_paid") ?>" alt="<?php echo __("mark_as_paid") ?>">
                    </a>
                </li>
	</ul>
	<?php else: ?>
	<ul class="sf_admin_td_actions" style="height:18px">
            <li>
                <a id="mark-paid-<?php echo $inscription['id'] ?>" href="#" data-all="1" style="text-decoration: none">
                    <img style="width: 23px;  margin-left: -2px; margin-bottom: -2px;margin-right: -2px;" src="/sf/sf_admin/images/100.png" title="<?php echo __("mark_as_paid") ?>" alt="<?php echo __("mark_as_paid") ?>">
                </a>
            </li>
            <li class="td-split-<?php echo $inscription['id'] ?>" <?php echo !$inscription['split_payment'] ? 'style="display:none"' : '' ?>>
                <a id="mark-half-paid-<?php echo $inscription['id'] ?>" href="#" data-all="1" style="text-decoration: none">
                    <img style="width: 23px;  margin-left: -2px; margin-bottom: -2px;margin-right: -2px;" src="/sf/sf_admin/images/half2.png" title="<?php echo __("mark_half_as_paid") ?>" alt="<?php echo __("mark_as_paid") ?>">
                </a>
            </li>
	</ul>
	<?php endif; ?>
</td>