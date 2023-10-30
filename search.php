<!-- 
    Template is used to display search results when javascript is disabled
    or when the search form is submitted outside of the search page. 
    for example http://localhost:5051/?s=lorem
    also see "page-search.php" for the template used when javascript is enabled
 -->
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
    // here we display results if any 
    // or a message if no results were found
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
    get_search_form(); // get the actual html form  - searchform.php

    ?>




</div>
<?php
get_footer();
