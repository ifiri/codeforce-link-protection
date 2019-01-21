<div class="wrap">
    <h1><?php _e('Links List', 'tea-link-protection') ?></h1>

    <div class="metabox-holder columns-2">
        <div class="meta-box-sortables ui-sortable">
            <form method="post" class="tlp-form tlp-form_type_list">
                <?php 
                    $ui_elements['LinksListTable']->search_box(__('Find', 'tea-link-protection'), 'tlp-links-search');
                    $ui_elements['LinksListTable']->display(); 
                ?>
                
                <input type="hidden" name="page" value="<?php echo $_GET['page'] ?>">
            </form>
        </div>
    </div>
</div>