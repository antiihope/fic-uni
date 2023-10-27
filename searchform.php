<form action="<?php echo esc_url(site_url('/')); ?>" method="get" class="search-form">
    <label class="headline headline--medium" for="s">Perform a New Search:</label>
    <div class="search-form-row">

        <input class="s" type="search" name="s" id="s" autocomplete="off" value="<?php the_search_query(); ?>" placeholder="What are you looking for?">
        <input type="submit" value="Search" class="search-submit">
    </div>

</form>