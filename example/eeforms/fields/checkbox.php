
<?php
if($options = $elm['options']){ ?>
<?=$label;?>
        <?php foreach($options as $option):?>
            <label><input type="<?=$field_type;?>" name=<?=$name;?> value="<?=$option['option_value'];?>"><?=$option['option_label'];?></label>
        <?php endforeach;?>
<?php }

