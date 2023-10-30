<!-- 
    static search page, 
    when user clicks the search icon but javascript is disabled, they will be redirected to this page
    also see "search.php" for the template that displays search results when javascript is disabled
 -->

<?php


get_header();

while (have_posts()) {
    the_post();
    // we didn't pass any arguments to pageBanner() because we want to use the default values
    pageBanner();
?>

    <div class="container container--narrow page-section">



        <div class="generic-content">
            <?php
            get_search_form();
            ?>

        </div>
    </div>

    <div class=" page-section page-section--beige">
        <div class="container container--narrow generic-content">
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia voluptates vero vel temporibus aliquid possimus, facere accusamus modi. Fugit saepe et autem, laboriosam earum reprehenderit illum odit nobis, consectetur dicta. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos molestiae, tempora alias atque vero officiis sit commodi ipsa vitae impedit odio repellendus doloremque quibusdam quo, ea veniam, ad quod sed.</p>

            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Officia voluptates vero vel temporibus aliquid possimus, facere accusamus modi. Fugit saepe et autem, laboriosam earum reprehenderit illum odit nobis, consectetur dicta. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quos molestiae, tempora alias atque vero officiis sit commodi ipsa vitae impedit odio repellendus doloremque quibusdam quo, ea veniam, ad quod sed.</p>
        </div>
    </div>
<?php
}

get_footer();
?>