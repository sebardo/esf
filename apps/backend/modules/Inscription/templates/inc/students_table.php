<?php if (empty($inscriptions)): ?>

    <div class="save-ok">
        <h2>No trobat</h2>
    </div>
    <?php return ?>
<?php endif; ?>
<table class="sf_admin_list -inscription-report" style="padding-right: 275px;" cellspacing="0">
    <thead>
    <tr>
        <th style="width: 2em">No.</th>
        <th>Codi inscripció</th>
        <th>Data inscripció</th>
        <th>Centre</th>
        <th>Centre Kids&Us de procedència</th>
        <th>Setmana</th>
    </tr>
    </thead>
    <tbody>
    <?php $name = array('first' => null, 'second' => null, 'surname' => null) ?>
    <?php
    $isNewName = function ($now) use (&$name) {
        if (strtolower($name['first']) === strtolower($now['student_name'])
            && strtolower($name['second']) === strtolower($now['student_primer_apellido'])
            && strtolower($name['surname']) === strtolower($now['student_segundo_apellido'])) {
            return false;
        }

        $name['first'] = $now['student_name'];
        $name['second'] = $now['student_primer_apellido'];
        $name['surname'] = $now['student_segundo_apellido'];
        return true;
    }
    ?>
    <?php $no = 1 ?>
    <?php foreach ($inscriptions as $k => $item): ?>
        <?php if ($isNewName($item)): ?>
            <tr class="user-row">
                <td colspan="99">
                    <img src="/images/plus.png" class="expand-next-block">
                    <?php $student = htmlspecialchars(
                        $item['student_name']
                        . ' ' . $item['student_primer_apellido']
                        . ' ' . $item['student_segundo_apellido']
                    ) ?>
                    <a href="<?php echo url_for('Inscription/students') ?>?filters[name]=<?php echo $student ?>">
                        <?php echo $student ?>
                    </a>
                </td>
            </tr>
            <?php $no = 1 ?>
        <?php endif ?>

        <tr class="sf_admin_row_<?php echo $k % 2 ?>">
            <td style="text-align: center"><?php echo $no++ ?></td>
            <td>
                <?php echo link_to($item['inscription_code'], 'Inscription/edit?id=' . $item['id']) ?>
            </td>
            <td><?php echo date('d/m/y', strtotime($item['created_at'])) ?></td>
            <td><?php echo $item['centre_title'] ?></td>
            <td><?php echo $item['kids_centre_title'] ?></td>
            <td>
                <?php echo date('d/m/y', strtotime($item['starts_at'])) ?> -
                <?php echo date('d/m/y', strtotime($item['ends_at'])) ?>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
    <tfoot>
    <tr>
        <th colspan="99">
            <?php if ($totalRows > 100): ?>
                <div class="float-right">
                    <?php
                    $pageUrl = function ($page) {
                        $params = $_GET;
                        unset($params['page']);
                        $params['page'] = $page;
                        return basename($_SERVER['PHP_SELF']) . '?' . http_build_query($params);
                    }
                    ?>
                    <a href="<?php echo $pageUrl(0) ?>"><img title="First" src="/sf/sf_admin/images/first.png"></a>
                    <a href="<?php echo $pageUrl($page - 1) ?>">
                        <img title="Previous" src="/sf/sf_admin/images/previous.png">
                    </a>
                    <span><?php echo $page + 1 ?></span>
                    <a href="<?php echo $pageUrl($page + 1) ?>">
                        <img alt="Next" title="Next" src="/sf/sf_admin/images/next.png">
                    </a>
                    <a href="<?php echo $pageUrl(floor($totalRows / 100)) ?>">
                        <img alt="Last" title="Last" src="/sf/sf_admin/images/last.png">
                    </a>
                </div>
            <?php endif ?>
            <?php echo $totalRows ?> resultats
        </th>
    </tr>
    </tfoot>
</table>
