<?php

/**
 * Main class exposing Dropout functionalities.
 * Methods can be called from outside using the notation Dropout::method( params )
 */
class Dropout {
	
	public static $stopwords = array(
		'about', 'after', 'all', 'also', 'am', 'an', 'and', 'another', 'any', 'are', 'as', 'at', 'be',
        'because', 'been', 'before', 'being', 'between', 'both', 'but', 'by', 'came', 'can',
        'come', 'could', 'did', 'do', 'each', 'for', 'from', 'get', 'got', 'has', 'had',
        'he', 'have', 'her', 'here', 'him', 'himself', 'his', 'how', 'if', 'in', 'into',
        'is', 'it', 'like', 'make', 'many', 'me', 'might', 'more', 'most', 'much', 'must',
        'my', 'never', 'now', 'of', 'on', 'only', 'or', 'other', 'our', 'out', 'over',
        'said', 'same', 'see', 'should', 'since', 'some', 'still', 'such', 'take', 'than',
        'that', 'the', 'their', 'them', 'then', 'there', 'these', 'they', 'this', 'those',
        'through', 'to', 'too', 'under', 'up', 'very', 'was', 'way', 'we', 'well', 'were',
        'what', 'where', 'which', 'while', 'who', 'with', 'would', 'you', 'your',
        'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '$', '1',
        '2', '3', '4', '5', '6', '7', '8', '9', '0', '_'
	);
	
	/**
	 * Measure text similarity between two posts using Jaccard/Tanimoto/Dice coefficient.
	 * 
	 * @param mixed $post_id_a post ID
	 * @param mixed $post_id_b post ID
	 * @return double A number between 1 (identity) and 0 (total diversity).
	 */
	public static function similarity( $post_id_a, $post_id_b ) {
		
		// Transform each post in a bag of words
		$bow_a = self::post_2_bag_of_words( $post_id_a );
		$bow_b = self::post_2_bag_of_words( $post_id_b );
		
		if( empty( $bow_a ) || empty( $bow_b ) ){
			return 0;
		}
		
		// Compute Tanimoto
		$intersection = array_unique( array_intersect( $bow_a, $bow_b ) );
		$union        = array_unique( array_merge( $bow_a, $bow_b ) );
		
		return count( $intersection ) / count( $union );
	}
	
	/**
	 * Transform post content in a bag of words
	 * 
	 * @param $post_id post ID
	 * @return array Bag of words
	 */
	public static function post_2_bag_of_words( $post_id ) {
		
		$post = get_post( $post_id );
		
		if( is_null($post) ){
			return array();
		}
		
		$post_content  = $post->post_content;
		$clean_content = strtolower( wp_strip_all_tags( $post_content ) );
		
		$clean_content = str_replace( array(".", ",", ":", ";", "!", "?", "'", '"', "(", ")" ), "", $clean_content );
		
		$tokens = array_unique( explode( " ", $clean_content ) );   // No punctuation
		
		return array_diff( $tokens, self::$stopwords );             // No common words
	}
}

