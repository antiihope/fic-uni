<?php
require_once 'functions.php';

get_header();
pageBanner(array(
    'title' => 'Search results',
    'subtitle' => 'You searched for &ldquo;' . esc_html(get_search_query(false)) . '&rdquo;'
));
?>

<div class="container container--narrow page-section">
    <?php
    if (have_posts()) {
        $count = 0;
        while ($count < 10 && have_posts()) {
            the_post();
            $count++;
            get_template_part('template-parts/content', get_post_type());
        }

        echo paginate_links();
    } else {
        echo '<h2 class="headline headline--small-plus">No results match that search.</h2>';
    }
    get_search_form();

    ?>




</div>
<?php
get_footer();
