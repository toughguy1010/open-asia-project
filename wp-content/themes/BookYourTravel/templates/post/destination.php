<?php
$categories = get_categories();

if ($categories) {
?>
    <div class="destionation-post-wrap">
        <?php
        foreach ($categories as $category) {
        ?>
            <div class="destionation-post-item">
                <a href="<?= get_category_link($category->term_id) ?>">
                    <?=  $category->name ?>(<?=  $category->count ?>)
                </a>
            </div>
        <?php
        }
        ?>
    </div>

<?php
}
?>