<?php

get_header();

while (have_posts()) {
    the_post();
    pageBanner();
?>

    <div class="container container--narrow page-section">


        <?php
        $parent_id = wp_get_post_parent_id(get_the_ID());
        if ($parent_id) {
        ?>
            <div class="metabox metabox--position-up metabox--with-home-link">
                <p>
                    <a class="metabox__blog-home-link" href="<?php echo get_permalink($parent_id); ?>">
                        <i class="fa fa-home" aria-hidden="true">
                        </i> Back to <?php echo get_the_title($parent_id); ?></a> <span class="metabox__main">
                        <?php the_title(); ?>
                    </span>
                </p>
            </div>
        <?php
        }
        ?>

        <?php
        // get the array of pages that are related to the current page
        // we added the relation through a page attribute 'parent page' in the admin panel
        $related_pages = get_pages(array('child_of' => get_the_ID()));

        if ($parent_id or $related_pages) { ?>


            <div class="page-links">
                <h2 class="page-links__title"><a href="<?php
                                                        echo get_permalink($parent_id); ?>
            ">
                        <?php
                        echo get_the_title($parent_id);

                        ?>
                    </a></h2>
                <ul class="min-list">
                    <?php
                    // if the current page has a parent page, then we want to show the children of the parent page
                    // otherwise we want to show the children of the current page
                    if ($parent_id) {
                        $find_children_of = $parent_id;
                    } else {
                        $find_children_of = get_the_ID();
                    }
                    // list the children of the current page
                    wp_list_pages(array(
                        'title_li' => NULL,
                        'child_of' => $find_children_of,
                        'sort_column' => 'menu_order'
                    ));
                    ?>
                </ul>
            </div>
        <?php }; ?>

        <div class="generic-content">
            <?php the_content(); ?>
        </div>
    </div>

    <div class="page-section page-section--beige">
        <div class="container container--narrow generic-content">
        </div>
    </div>
<?php
}

get_footer();
?>