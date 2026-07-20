<?php
/**
 * Exo-Functions Add-on: Override native WP YouTube Embed. 
 * Description: 
 * 	Wraps native YouTube Embed with custom element whach toggles the display of the video thumbnail when the video has ended. 
 * 	Effectively covering the Recommended videos UX.
 * Author: Imran Sayed
 * Author URI: https://www.youtube.com/@Codeytek
 * Version: 1.0.0
 */

add_filter( 'embed_oembed_html', MSCC . '\\modify_youtube_embed_url', 10, 3 );
add_filter( 'render_block', MSCC . '\\enqueue_script_on_youtube_block_render', 10, 2 );

$plugin_url = plugin_dir_url( __FILE__ );
  
  /**
 * Modify YouTube Embed URL.
 *
 * @param string $embed Embed.
 * @param string $url   URL.
 *
 * @return mixed|string
 */
function modify_youtube_embed_url( string $embed = '', string $url = '' ): mixed {
	// Check if the url has you-tube.
	if ( str_contains( $url, 'youtube.com' ) !== false || str_contains( $url, 'youtu.be' ) !== false ) {
		$embed = str_replace( 'youtube.com/watch?v=', 'youtube.com/embed/', $url );
		$embed = add_query_arg( 'rel', '0', $embed );
		$embed = add_query_arg( 'showinfo', '0', $embed );
		$embed = add_query_arg( 'enablejsapi', 'true', $embed );
		$embed = '
		<lb-youtube-embed class="youtube-embed" ended="false">
			<div class="youtube-embed__player">
				<iframe width="560" height="315" src="' . esc_url( $embed ) . '" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
			</div>
			<div class="youtube-embed__end-screen-overlay">
			    <div class="youtube-embed__end-screen-background"></div>
			    <button class="youtube-embed__end-screen-button" aria-label="Play" title="Play">
			        <svg height="100%" version="1.1" viewBox="0 0 68 48" width="100%"><path class="ytp-large-play-button-bg" d="M66.52,7.74c-0.78-2.93-2.49-5.41-5.42-6.19C55.79,.13,34,0,34,0S12.21,.13,6.9,1.55 C3.97,2.33,2.27,4.81,1.48,7.74C0.06,13.05,0,24,0,24s0.06,10.95,1.48,16.26c0.78,2.93,2.49,5.41,5.42,6.19 C12.21,47.87,34,48,34,48s21.79-0.13,27.1-1.55c2.93-0.78,4.64-3.26,5.42-6.19C67.94,34.95,68,24,68,24S67.94,13.05,66.52,7.74z" fill="#f00"></path><path d="M 45,24 27,14 27,34" fill="#fff"></path></svg>
			    </button>
			</div>
		</lb-youtube-embed>
		';
	}

	// return embed.
	return $embed;
}

/**
 * Enqueue script and style for YouTube embed block.
 *
 * @param string               $block_content The rendered block content.
 * @param array<string, mixed> $block         The parsed block data, containing attributes and block details.
 *
 * @return string The modified block content.
 */
function enqueue_script_on_youtube_block_render( string $block_content = '', array $block = [] ): string {
	// Check if it's a YouTube embed block.
	if (
		isset( $block['blockName'], $block['attrs'] ) &&
		is_array( $block['attrs'] ) &&
		'core/embed' === $block['blockName'] &&
		isset( $block['attrs']['providerNameSlug'] ) &&
		'youtube' === $block['attrs']['providerNameSlug']
	) {
		// Get assets version.
		$assets_version = get_assets_version();

		// Enqueue styles and scripts.
		wp_enqueue_script(
			'youtube-embed-js',
			$plugin_url . 'youtube-embed.js',
			[],
			$assets_version,
			true
		);
		wp_enqueue_style(
			'youtube-embed-style',
			$plugin_url . 'youtube-embed-style.css',
			[],
			$assets_version
		);
	}

	// Return the original or modified block content.
	return $block_content;
}
?>