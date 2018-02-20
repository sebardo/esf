<?php if (empty($inscriptions)): ?>

    <div class="save-ok">
        <h2>No trobat</h2>
    </div>
    <?php return ?>
<?php endif; ?>

<table class="sf_admin_list" style="padding-right: 275px;" cellspacing="0">
    <thead>
    <tr>
        <th>Codi inscripció</th>
        <th>Data inscripció</th>
        <th>Centre</th>
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
    <?php foreach ($inscriptions as $k => $item): ?>
        <?php if ($isNewName($item)): ?>
            <tr>
                <td colspan="99" style="text-decoration: underline">
                    <?php echo $item['student_name']
                        . ' ' . $item['student_primer_apellido']
                        . ' ' . $item['student_segundo_apellido'] . ' ' ?>
                </td>
            </tr>
        <?php endif ?>
        <tr class="sf_admin_row_<?php echo $k % 2 ?>">
            <td>
                <?php echo link_to($item['inscription_code'], 'Inscription/edit/' . $item['id']) ?>
            </td>
            <td><?php echo date('dd/mm/YY', strtotime($item['created_at'])) ?></td>
            <td><?php echo $item['centre_title'] ?></td>
            <td>
                <?php echo date('dd/mm/YY', strtotime($item['starts_at'])) ?> -
                <?php echo date('dd/mm/YY', strtotime($item['ends_at'])) ?>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
