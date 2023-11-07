<?php

/**
 * Registers the search route for the Uni API.
 */
add_action('rest_api_init', 'uniRegisterSearch');
/**
 * Registers the search route for the Uni API.
 */
function uniRegisterSearch()
{

    register_rest_route('uni/v1', 'search', array(
        'methods' => WP_REST_SERVER::READABLE,
        'callback' => 'uniSearchResults'
    ));
}


/**
 * uniSearchResults function
 * 
 * This function performs a search query across multiple post types
 * and returns an array of results grouped by post type.
 *
 * @param array $data The search term to be used in the query.
 * @return array An array of search results grouped by post type.
 */
function uniSearchResults($data)
{
    // uni/v1/search?term= 5151
    $mainQuery = new WP_Query(array(
        'post_type' => array('professor', 'post', 'page', 'program', 'event'),
        's' => sanitize_text_field($data['term'])
    ));

    $results = array(
        'generalInfo' => array(),
        'professors' => array(),
        'programs' => array(),
        'events' => array()
    );

    while ($mainQuery->have_posts()) {
        $mainQuery->the_post();

        if (get_post_type() === 'post' or get_post_type() === 'page') {
            array_push($results['generalInfo'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'postType' => get_post_type(),
                'authorName' => get_the_author()
            ));
        }
        if (get_post_type() === 'professor') {
            array_push($results['professors'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'image' => get_the_post_thumbnail_url(0, 'professorLandscape'),
                'meta' => array(
                    'relatedPrograms' => get_field('related_programs')
                )
            ));
        }
        if (get_post_type() === 'program') {
            array_push($results['programs'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'id' => get_the_id()
            ));
        }

        if (get_post_type() === 'event') {
            $eventDate = new DateTime(get_field('event_date'));
            $description = null;
            if (has_excerpt()) {
                $description = get_the_excerpt();
            } else {
                $description = wp_trim_words(get_the_content(), 18);
            }
            array_push($results['events'], array(
                'title' => get_the_title(),
                'permalink' => get_the_permalink(),
                'month' => $eventDate->format('M'),
                'day' => $eventDate->format('d'),
                'description' => $description
            ));
        }
    }


    // Check if there are any programs in the search results
    if ($results['programs']) {
        // Create an array to hold the meta queries for related programs
        $programsMetaQuery = array('relation' => 'OR');

        // Loop through each program in the search results
        foreach ($results['programs'] as $item) {
            // Add a meta query for each related program
            array_push($programsMetaQuery, array(
                'key' => 'related_programs',
                'compare' => 'LIKE',
                'value' => '"' . $item['id'] . '"'
            ));
        }

        // Query for posts that have a relationship with any of the related programs
        $programRelationshipQuery = new WP_Query(array(
            'post_type' => array('professor', 'event'),
            'meta_query' => $programsMetaQuery
        ));

        // Loop through each post in the query results
        while ($programRelationshipQuery->have_posts()) {

            $programRelationshipQuery->the_post();

            // If the post is an event, add it to the events array
            if (get_post_type() === 'event') {
                $eventDate = new DateTime(get_field('event_date'));
                $description = null;
                if (has_excerpt()) {
                    $description = get_the_excerpt();
                } else {
                    $description = wp_trim_words(get_the_content(), 18);
                }
                array_push($results['events'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'month' => $eventDate->format('M'),
                    'day' => $eventDate->format('d'),
                    'description' => $description
                ));
            }

            // If the post is a professor, add it to the professors array
            if (get_post_type() === 'professor') {
                array_push($results['professors'], array(
                    'title' => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'image' => get_the_post_thumbnail_url(0, 'professorLandscape')
                ));
            }
        }

        // Remove any duplicates from the professors and events arrays
        $results['professors'] = array_values(array_unique($results['professors'], SORT_REGULAR));
        $results['events'] = array_values(array_unique($results['events'], SORT_REGULAR));
    }

    $hold = $results['professors'];

    if ($results['professors'] && !$results['programs']) {
        // Loop through each professor in the search results
        foreach ($results['professors'] as $item) {
            $programId = $item['meta']['relatedPrograms'][0]->ID;
            $program = get_post($programId);
            $programData = array(
                'title' => $program->post_title,
                'permalink' => get_the_permalink($programId)
            );
            $results['programs'][] = $programData;
        }


        // Remove any duplicates from the programs array
        $results['programs'] = array_values(array_unique($results['programs'], SORT_REGULAR));
    }
    return $results;
}
