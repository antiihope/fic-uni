<?php
require_once 'functions.php';

get_header();
pageBanner(array(
    'title' => 'Past Events',
    'subtitle' => 'A recap of our past events.'
));
?>

<div class="container container--narrow page-section">
    <?php
    // Custom query for past events
    $count = 0;
    $pastEvents = new WP_Query(array(
        'paged' => get_query_var('paged', 1),
        'posts_per_page' => 1,
        'post_type' => 'event',
        'meta_key' => 'event_date',
        'orderby' => 'meta_value_num',
        'order' => 'ASC',
        'meta_query' => array(
            array(
                'key' => 'event_date',
                'compare' => '<',
                'value' => date('Ymd'),
                'type' => 'numeric'
            )
        )
    ));
    while ($count < 6 && $pastEvents->have_posts()) {
        $pastEvents->the_post();
        $count++;
        get_template_part('template-parts/content', 'event');
    }


    // because we are using a custom query, the pagination links will not work on its own
    // we need to pass the total number of pages to paginate_links()
    echo paginate_links(
        array(
            'total' => $pastEvents->max_num_pages
        )
    );
    ?>


</div>
<?php
get_footer();
