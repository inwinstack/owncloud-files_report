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
                        <option value='1'><?php p($l->t('include bad words or graphs'))?></option>
                        <option value='2'><?php p($l->t('uncomfortable file')) ?></option>
                        <option value='3'><?php p($l->t('should not be on custom cloud')) ?></option>
                        <option value='4'><?php p($l->t('spam file')) ?></option>
                        <option value='cancel'><?php p($l->t('Cancel')) ?></option>
                    </select>
                </td>
                <td id='download'>
                    <a><?php p($l->t('Download')) ?></a>
                </td>

              </tr>
       <?php     
            }
        ?> 
    </tbody>

</table>
