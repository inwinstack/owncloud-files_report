<table class="grid">
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
         ?>
               <tr id="<?php echo $report['id'] ?>">
                 <td id='owner'><?php echo $report['owner'] ?></td>
                 <td id='reporter'><?php echo $report['reporter'] ?> </td>
                 <td id='filename' data="<?php echo $report['file_path'] ?>"><?php echo $report['file_name'] ?></td>
                 <td id='action'>
                    <select>
                        <option value='1'><?php p($l->t('reason1'))?></option>
                        <option value='2'><?php p($l->t('reason2')) ?></option>
                        <option value='3'><?php p($l->t('reason3')) ?></option>
                        <option value='cancel'><?php p($l->t('Cancel')) ?></option>
                    </select>
                </td>
                <td id='download'></td>

              </tr>
       <?php     
            }
        ?> 
    </tbody>

</table>
