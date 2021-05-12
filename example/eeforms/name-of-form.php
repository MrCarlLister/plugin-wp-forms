

    <?php
    if(is_array($fields))
    {
        ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <img src='https://dummyimage.com/800x310.jpg' alt='' class="w-100 my-3" />
                    <form class="form-spacer max-width-400 mx-auto" action="/wp-admin/admin-post.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="catch_submission">
                        <input type="hidden" name="redirect" value="/thank-you">
                        <input type="hidden" name="formid" value="<?=$id;?>">

                        
                    
                            <?php
                                foreach($fields as $elm)
                                {
                                    $field_type = $elm['input_type'];
                                    $label = $elm['input_label'];
                                    $name = $elm['input_column_name'];
                                    $placeholder = $elm['placeholder'];
                                    $required = ( $elm['required'] ) ? 'required' : '';
                                    $requiredLabel = ( $elm['required'] ) ? '<span class="font-bold">(required)</span>' : '<span class="font-bold">(optional)</span>';
                                    echo '<div class="col-12">';
                                        include(locate_template('eeforms/fields/'.$field_type.'.php'));
                                    echo '</div>';
                                }
                            ?>
                
                        <div class="col-12"><input type="submit" class="btn btn-primary"></div>
                        
                    </form>
                </div>
            </div>
        </div>
        
        <?php
    }
    ?>



<?php //dd($fields);