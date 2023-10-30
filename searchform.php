<!-- Search form will be visible if  javascript is not enabled -->
<!-- wordpress autmaticaly detects this page template and you can use it by call get_search_form() function -->

<form action="<?php echo esc_url(site_url('/')); ?>" method="get" class="search-form">
    <label class="headline headline--medium" for="s">Perform a New Search:</label>
    <div class="search-form-row">

        <input class="s" type="search" name="s" id="s" autocomplete="off" value="<?php the_search_query(); ?>" placeholder="What are you looking for?">
        <input type="submit" value="Search" class="search-submit">
    </div>

</form>