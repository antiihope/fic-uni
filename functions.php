<?php
require_once 'includes/html_helpers.php';
require get_theme_file_path('/includes/like-route.php');
require get_theme_file_path('/includes/search-route.php');




function uni_custom_rest()
{
    // tells wordpress that we want to add a new field to the rest api
    register_rest_field('post', 'authorName', array(
        'get_callback' => function () {
            return get_the_author();
        }
    ));
    // on note post type, add a new field called userNoteCount
    register_rest_field('note', 'userNoteCount', array(
        'get_callback' => function () {
            return count_user_posts(get_current_user_id(), 'note');
        }
    ));
}

add_action('rest_api_init', 'uni_custom_rest'); // hook into rest api initialization

function pageBanner($args = [])
{
    // this function will be called in the header of each page
    // it will display a banner with the page title and subtitle
    // if no title or subtitle is provided, it will use the default values
    // if no photo is provided, it will use the default photo
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }
    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if (!$args['photo']) {
        if (get_field('page_banner_background_image') and !is_archive() and !is_home()) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];
        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }

?>
    <div class="page-banner">
        <div class="page-banner__bg-image" style="background-image: url(
            <?php
            echo $args['photo'];
            ?>">
        </div>
        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title">
                <?php
                echo $args['title'];
                ?>
            </h1>
            <div class="page-banner__intro">
                <p>
                    <?php
                    echo $args['subtitle'];
                    ?>
                </p>
            </div>
        </div>
    </div>
<?php }



// course specifics
function uni_files()
{
    // load css and js files
    wp_enqueue_script('main-uni-js', get_theme_file_uri('/build/index.js'), array('jquery'), '1.0', true);
    wp_enqueue_style('custom-google', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('uni_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('uni_extra_styles', get_theme_file_uri('/build/index.css'));


    // pass data to javascript, here we pass the root url and a nonce
    wp_localize_script('main-uni-js', 'uniData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest'),
    ));
}


add_action('wp_enqueue_scripts', 'uni_files'); // hook into wp_enqueue_scripts


function uni_features()
{
    // register navigation menus
    register_nav_menu('headerMenuLocation', 'Header Menu Location');
    register_nav_menu('footLocationOne',  'Footer Location One');
    register_nav_menu('footerLocationTwo', 'Footer Location Two');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('professorLandscape', 400, 260, true);
    add_image_size('professorPortrait', 480, 650, true);
    add_image_size('pageBanner', 1500, 350, true);
}


add_action('after_setup_theme', 'uni_features');

function university_adjust_queries($query)
{
    // this function will be called before wordpress runs the query
    // we can use it to modify the query
    // we will use it to sort the programs alphabetically
    // we will also use it to sort the events by event date



    if (!is_admin() and is_post_type_archive('program') and $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }


    if (!is_admin() and is_post_type_archive('event') and $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_num');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key' => 'event_date',
                'compare' => '>=',
                'value' => $today,
                'type' => 'numeric'
            )
        ));
    }
}

add_action('pre_get_posts', 'university_adjust_queries'); // hook into pre_get_posts, means before getting posts, run this function


function redirectSubsToFrontend()
{
    // this function will be called when a user logs in
    // if the user is a subscriber, redirect them to the homepage
    // a subscriber should not be able to access the admin area
    $ourCurrentUser = wp_get_current_user();
    if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}

add_action('admin_init', 'redirectSubsToFrontend'); // hook into admin_init, means when the admin area is initialized, run this function



function noSubsAdminBar()
{
    // this function will be called when a user logs in
    // if the user is a subscriber, hide the admin bar
    // a subscriber should not be able to access the admin area
    $ourCurrentUser = wp_get_current_user();
    if (count($ourCurrentUser->roles) == 1 and $ourCurrentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}

add_action('wp_loaded', 'noSubsAdminBar'); // hook into wp_loaded, means when wordpress is loaded, run this function



// customize login screen
add_filter(
    'login_headerurl',
    function () {
        return esc_url(site_url('/'));
    }
);



add_action('login_enqueue_scripts', 'ourLoginCSS');
function ourLoginCSS()
{
    wp_enqueue_style('custom-google', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('uni_main_styles', get_theme_file_uri('/build/style-index.css'));
    wp_enqueue_style('uni_extra_styles', get_theme_file_uri('/build/index.css'));
}


add_filter(
    'login_headertitle',
    function () {
        // change the title of the login screen
        return get_bloginfo('name');
    }
);



// force note posts to be private


function makeNotePrivate($data, $postarr)
{
    // this function will be called when a post is inserted or updated
    // if the post type is note, make it private
    // if the post type is note and the post status is not trash, make it private
    // if the user has more than 4 notes, don't allow them to create a new note

    if ($data['post_type'] == 'note') {
        if (count_user_posts(get_current_user_id(), 'note') > 4 and !$postarr['ID']) {
            die("You have reached your note limit.");
        }

        $data['post_content'] = sanitize_textarea_field($data['post_content']);
        $data['post_title'] = sanitize_text_field($data['post_title']);
    }

    if ($data['post_type'] == 'note' and $data['post_status'] != 'trash') {
        $data['post_status'] = 'private';
    }
    return $data;
}

add_filter('wp_insert_post_data', 'makeNotePrivate', 10, 2); // hook into wp_insert_post_data, means when a post is inserted or updated, run this function
