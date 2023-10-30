<!-- 
    controls-path: "/program/"
 -->
<?php
require_once 'functions.php';

get_header();

pageBanner(array(
    'title' => 'All Programs',
    'subtitle' => 'There is something for everyone. Have a look around.'
));
?>

<div class="container container--narrow page-section">
    <ul class="link-list min-list">

        <?php
        $count = 0;
        while ($count < 6 && have_posts()) {
            the_post();
            $count++;
        ?>
            <li>
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </li>
        <?php
        }


        echo paginate_links();
        ?>


    </ul>

</div>
<?php
get_footer();
