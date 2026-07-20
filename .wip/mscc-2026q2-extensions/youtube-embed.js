/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

// Ensure YouTube API types are available.
declare const YT: any;

/**
 * YouTubeEmbed Class.
 */
export default class YouTubeEmbed extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
	 */
	private player: any;
	private playerElement: HTMLElement | null;
	private endScreenBackgroundElement: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.playerElement = this.querySelector( '.youtube-embed__player' );
		this.endScreenBackgroundElement = this.querySelector( '.youtube-embed__end-screen-background' );

		// Add event.
		this.addEventListener( 'click', () => this.replayVideo() );
	}

	// Lifecycle callback: when the element is connected to the DOM
	connectedCallback(): void {
		// Load YouTube API asynchronously and initialize the player.
		this.loadYouTubeAPI().then( () => this.onYouTubeIframeAPIReady() );
	}

	/**
	 * Loads the YouTube IFrame API script asynchronously.
	 *
	 * @private
	 */
	private loadYouTubeAPI(): Promise<void> {
		// Return new promise.
		return new Promise( ( resolve, reject ) => {
			// If the API is already loaded, resolve immediately.
			if ( window.YT && window.YT.Player ) {
				resolve();

				// Return.
				return;
			}

			// Check if the script is already being loaded.
			const existingScript: HTMLScriptElement | null = document.querySelector<HTMLScriptElement>(
				'script[src="https://www.youtube.com/iframe_api"]',
			);

			// Check if existing script is present.
			if ( existingScript ) {
				// If script is already present, listen for the API readiness.
				const checkYT: NodeJS.Timeout = setInterval( () => {
					// Check if YT and Player is available.
					if ( window.YT && window.YT.Player ) {
						clearInterval( checkYT );
						resolve();
					}
				}, 50 );

				// Return.
				return;
			}

			// Dynamically load the YouTube IFrame API script.
			const script: HTMLScriptElement = document.createElement( 'script' );
			script.src = 'https://www.youtube.com/iframe_api';
			script.async = true;

			// Handle script load and error events.
			script.onload = () => {
				// Check YT.
				const checkYT: NodeJS.Timeout = setInterval( () => {
					// Check if YT and Player exists.
					if ( window.YT && window.YT.Player ) {
						clearInterval( checkYT );
						resolve();
					}
				}, 50 );
			};
			script.onerror = () => reject( new Error( 'Failed to load YouTube API script.' ) );

			// Append the script to the document.
			document.head.appendChild( script );
		} );
	}

	/**
	 * Called by the YouTube API when it is ready.
	 *
	 * @private
	 */
	private onYouTubeIframeAPIReady(): void {
		// Get iframe.
		const iframe: HTMLIFrameElement | null = this.querySelector<HTMLIFrameElement>( '.youtube-embed__player iframe' );

		// Check if player element is present.
		if ( ! this.playerElement || ! iframe ) {
			// Early return.
			return;
		}

		// Get video id.
		const videoId: string | null = this.extractVideoId( iframe.src );

		// Check if video id is present.
		if ( ! videoId ) {
			// Early return.
			return;
		}

		// Initialize player.
		this.player = new YT.Player( this.playerElement, {
			videoId,
			events: {
				onStateChange: this.onPlayerStateChange.bind( this ),
			},
		} );

		// Add end screen thumbnail.
		this.addEndScreenThumbnail( videoId );
	}

	/**
	 * Add end screen thumbnail.
	 *
	 * @param {string} videoID Video id.
	 *
	 * @private
	 */
	private addEndScreenThumbnail( videoID: string = '' ): void {
		// Set video thumbnail as cover image.
		const videoThumbnail: string = `https://i.ytimg.com/vi_webp/${ videoID }/maxresdefault.webp`;

		// Check end screen background element.
		if ( this.endScreenBackgroundElement ) {
			// Set the background image.
			this.endScreenBackgroundElement.style.backgroundImage = `url(${ videoThumbnail })`;
		}
	}

	/**
	 * Handle YouTube player state changes.
	 *
	 * @param {Event}  event      Event.
	 * @param {Object} event.data Event Data.
	 *
	 * @private
	 */
	private onPlayerStateChange( event: { data: number } ): void {
		// Check if the video has ended.
		if ( event.data === YT.PlayerState.ENDED ) {
			// Set 'ended' attribute to true.
			this.setAttribute( 'ended', 'true' );
		}
	}

	/**
	 * Replay the video and reset UI.
	 *
	 * @private
	 */
	private replayVideo(): void {
		// Set 'ended' attribute to false
		this.setAttribute( 'ended', 'false' );

		// Check if player exists.
		if ( this.player ) {
			// Replay the video.
			this.player.seekTo( 0 );
			this.player.playVideo();
		}
	}

	/**
	 * Extract Video id.
	 *
	 * @param {string} iframeSrc Iframe Source.
	 *
	 * @private
	 */
	private extractVideoId( iframeSrc: string ): string | null {
		// Try getting path parts.
		try {
			// Get url and path parts.
			const url: URL = new URL( iframeSrc );
			const pathParts: string[] = url.pathname.split( '/' );

			// Check if path parts includes embed.
			if ( pathParts.includes( 'embed' ) ) {
				// Get path parts.
				return pathParts[ pathParts.indexOf( 'embed' ) + 1 ];
			}

			// Return null.
			return null;
		} catch ( error ) {
			// If error parsing iframe.
			return null;
		}
	}
}

// Define the custom element
customElements.define( 'lb-youtube-embed', YouTubeEmbed );
