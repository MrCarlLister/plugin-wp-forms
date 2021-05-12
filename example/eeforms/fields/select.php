
<?php
if($options = $elm['options']){ ?>
<label><?=$label;?> <?=$requiredLabel;?></label>
    <select name="<?=$name;?>" <?=$required;?>>
        <?php foreach($options as $option):?>
            <option value="<?=$option['option_value'];?>"><?=$option['option_label'];?></option>
        <?php endforeach;?>
    </select>
<?php }

