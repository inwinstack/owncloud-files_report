<table class="">
    <thead>
        <tr>
            <th><?php p($l->t('Owner')) ?></th>
            <th><?php p($l->t('Reporter')) ?></th>
            <th><?php p($l->t('FileName')) ?></th>
            <th><?php p($l->t('Action')) ?></th>
            <th><?php p($l->t('Download')) ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach($_['reports'] as $report) {
                print('<tr>');
                print('<td>'.$report['owner'].'</td>');
                print('<td>'.$report['reporter'].'</td>');
                print('<td>'.$report['file_path'].'</td>');
                print('</tr>');
            
            }
        ?> 
    </tbody>

</table>
