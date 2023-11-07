<?php

add_action('rest_api_init', 'uniLikeRoutes'); // hook into rest api initialization

function uniLikeRoutes($args = [])
{
    // Create two new routes for managing likes
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'POST',
        'callback' => 'createLike'
    ));
    register_rest_route('university/v1', 'manageLike', array(
        'methods' => 'DELETE',
        'callback' => 'deleteLike'
    ));
}

function createLike($data)
{
    // check if user is logged in
    if (!is_user_logged_in()) {
        die("Only logged in users can create a like.");
    }


    // check if the professor id is valid
    $professor = sanitize_text_field($data['professorId']);
    $existsQuery = new WP_Query(array(
        'author' => get_current_user_id(),
        'post_type' => 'like',
        'meta_query' => array(
            array(
                'key' => 'liked_professor_id',
                'compare' => '=',
                'value' => $professor
            )
        )
    ));

    // if the professor id is valid, then create a new like
    if ($existsQuery->found_posts  == 0 and get_post_type($professor) == 'professor') {
        return wp_insert_post(array(
            'post_type' => 'like',
            'post_status' => 'publish',
            'post_title' => 'like professor',
            'meta_input' => array(
                'liked_professor_id' => $professor
            )
        ));
    } else {
        // if the professor id is invalid, then throw an error
        die("Invalid professor id ");
    }
}

function deleteLike($data)
{
    $likeId = sanitize_text_field($data['like']); // get the id of the like to be deleted


    // check if the user is logged in and if the user is the author of the like
    if (get_current_user_id() == get_post_field('post_author', $likeId) and get_post_type($likeId) == 'like') {


        wp_delete_post($likeId, true); // delete the like
        return 'Congrats, like deleted.';
    } else {
        die("You do not have permission to delete that.");
    }
}
