<?php
/**
 * Plugin Name:       Dropout
 * Plugin URI:        http://pieroit.org/portfolio
 * Description:       Simple recommendation engine
 * Version:           1.0.0
 * Author:            pieroit
 * Author URI:        http://pieroit.org/portfolio
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       dropout
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The core plugin class
 */
require plugin_dir_path( __FILE__ ) . '/class-dropout.php';

/**
 * Build recommendations at post save
 */
function dropout_update_recommendations( $post_id ) {
	
	if( !in_array( get_post_type( $post_id ), array( 'post', 'page') ) ){
		return;
	}
	
	// Take latest 50 posts
	$latest_posts = wp_get_recent_posts(
		array(
			'numberposts' => 10,
			'post_type'   => array( 'post', 'page' )
		)
	);
	$similarities = array();
	
	foreach( $latest_posts as $another_post ){
		
		if( $another_post['ID'] !== $post_id ) {
			
			// Measure similarity
			$similarity = Dropout::similarity( $post_id, $another_post['ID'] );
			$similarities[ $another_post['ID'] ] = $similarity;

			// Update similarities for the other post
			$already_computed_similarities = get_post_meta( $another_post['ID'], 'dropout_recommendations', true );
			$already_computed_similarities[$post_id] = $similarity;
			update_post_meta( $another_post['ID'], 'dropout_recommendations', $already_computed_similarities );
		}
	}
	
	// Save recommendations for this post
	update_post_meta( $post_id, 'dropout_recommendations', $similarities );
}
add_action( 'save_post', 'dropout_update_recommendations' );

/**
 * Add recommendations to every post
 */
function dropout_get_recommendations( $content ) {
	
	$recommendations = get_post_meta( get_the_ID(), 'dropout_recommendations', true );
	if( empty( $recommendations ) ) {
		return $content;
	}
	
	arsort( $recommendations );                                 // Sort by similarity

	$html = '<h3>See also</h3><ul>';
	foreach( $recommendations as $recom_id => $similarity ) {
		$recom_post = get_post( $recom_id );
		if($recom_post){
			$html .= '<li><a href="' . get_permalink($recom_post->ID) . '">' . $recom_post->post_title . '</a> (' . $similarity .')</li>';
		}
	}
	$html .= '</ul>';
	
	return $content . $html;
}
add_filter( 'the_content', 'dropout_get_recommendations' );
