<?php
/**
 * Easy Scheduled Site Launch
 *
 * Provides a coming soon page with customizable options like launch date, message,
 * logo upload, countdown timer, background color, and template selection.
 *
 * @package    Easy_Scheduled_Site_Launch
 * @subpackage Easy_Scheduled_Site_Launch/includes
 * @author     Prince Kumar
 * @license    GPL-2.0-or-later
 * @link       https://teckut.com/
 * @since      1.0.0
 */

/**
 * Easy Scheduled Site Launch Plugin.
 *
 * @link       https://teckut.com/
 * @since      1.0.0
 *
 * @package    Easy_Scheduled_Site_Launch
 */
class Easy_Scheduled_Site_Launch {


	/**
	 * Launch date option name.
	 *
	 * @var string
	 */
	private $launch_option = 'essl_launch_date';

	/**
	 * Coming soon message option name.
	 *
	 * @var string
	 */
	private $message_option = 'essl_coming_soon_message';

	/**
	 * Logo URL option name.
	 *
	 * @var string
	 */
	private $logo_option = 'essl_logo_url';

	/**
	 * Countdown timer option name.
	 *
	 * @var string
	 */
	private $countdown_option = 'essl_enable_countdown';

	/**
	 * Template choice option name.
	 *
	 * @var string
	 */
	private $template_option = 'essl_template_choice';

	/**
	 * Background color option name.
	 *
	 * @var string
	 */
	private $color_option = 'essl_background_color';

	/**
	 * Enable/disable plugin option name.
	 *
	 * @var string
	 */
	private $enable_option = 'essl_enable';


	/**
	 * Constructor.
	 */
	public function __construct() {
		 add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'template_redirect', array( $this, 'check_launch_date' ) );
	}

	/**
	 * Add settings page.
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Easy Scheduled Site Launch', 'easy-scheduled-site-launch' ),
			__( 'Easy Scheduled Site Launch', 'easy-scheduled-site-launch' ),
			'manage_options',
			'scheduled-launch',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting( 'essl_settings_group', $this->launch_option, array( 'sanitize_callback' => 'sanitize_text_field' ) );
		register_setting( 'essl_settings_group', $this->message_option, array( 'sanitize_callback' => 'sanitize_textarea_field' ) );
		register_setting( 'essl_settings_group', $this->logo_option, array( 'sanitize_callback' => 'esc_url_raw' ) );
		register_setting(
			'essl_settings_group',
			$this->countdown_option,
			array(
				'sanitize_callback' => function ( $value ) {
					return ( 'yes' === $value ) ? 'yes' : 'no';
				},
			)
		);
		register_setting(
			'essl_settings_group',
			$this->template_option,
			array(
				'sanitize_callback' => function ( $value ) {
					return in_array( $value, array( 'simple', 'professional' ), true ) ? $value : 'professional';
				},
			)
		);
		register_setting( 'essl_settings_group', $this->color_option, array( 'sanitize_callback' => 'sanitize_hex_color' ) );
		register_setting(
			'essl_settings_group',
			$this->enable_option,
			array(
				'sanitize_callback' => function ( $value ) {
					return ( 'yes' === $value ) ? 'yes' : 'no';
				},
			)
		);
	}

	/**
	 * Enqueue admin scripts and styles for the plugin settings page.
	 *
	 * Loads WordPress color picker, media uploader, and custom inline scripts.
	 *
	 * @param string $hook The current admin page hook.
	 * @return void
	 */
	public function enqueue_admin_scripts( $hook ) {
		if ( 'settings_page_scheduled-launch' !== $hook ) {
			return;
		}
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
		wp_add_inline_script( 'wp-color-picker', 'jQuery(document).ready(function($){ $(".essl-color-field").wpColorPicker(); });' );
		wp_enqueue_media();
	}

	/**
	 * Admin settings page.
	 */
	public function settings_page() {
		$launch_date = get_option( $this->launch_option, '' );
		$message     = get_option( $this->message_option, __( 'Our website is coming soon! Stay tuned.', 'easy-scheduled-site-launch' ) );
		$logo_url    = get_option( $this->logo_option, '' );
		$countdown   = get_option( $this->countdown_option, 'yes' );
		$template    = get_option( $this->template_option, 'professional' );
		$color       = get_option( $this->color_option, '#f0f0f0' );
		$enabled     = get_option( $this->enable_option, 'yes' );
		?>
<div class="wrap" style="overflow:hidden;">
	<h1 style="display:inline-block;"><?php esc_html_e( 'Easy Scheduled Site Launch', 'easy-scheduled-site-launch' ); ?></h1>
	<div style="float:right; margin-top:5px;">
		<a href="https://teckut.com/easy-scheduled-site-launch" target="_blank" class="button button-primary" style="margin-right:5px;">
			<?php esc_html_e( 'Documentation', 'easy-scheduled-site-launch' ); ?>
		</a>
		<a href="https://teckut.com/contact-us/" target="_blank" class="button button-secondary">
			<?php esc_html_e( 'Support', 'easy-scheduled-site-launch' ); ?>
		</a>
	</div>

	<div style="clear:both;"></div>
	<hr style="margin-top:15px; margin-bottom:25px;">
			<form method="post" action="options.php">
				<?php settings_fields( 'essl_settings_group' ); ?>
				<?php do_settings_sections( 'essl_settings_group' ); ?>

				<table class="form-table" role="presentation">
					<tbody>
						<tr>
							<th scope="row">
								<label><?php esc_html_e( 'Enable Plugin', 'easy-scheduled-site-launch' ); ?>
									<span class="essl-tooltip" title="Enable or disable the coming soon page.">?</span>
								</label>
							</th>
							<td>
								<select name="<?php echo esc_attr( $this->enable_option ); ?>">
									<option value="yes" <?php selected( $enabled, 'yes' ); ?>><?php esc_html_e( 'Yes', 'easy-scheduled-site-launch' ); ?></option>
									<option value="no" <?php selected( $enabled, 'no' ); ?>><?php esc_html_e( 'No', 'easy-scheduled-site-launch' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Launch Date & Time', 'easy-scheduled-site-launch' ); ?>
								<span class="essl-tooltip" title="Set the date and time when your website will go live.">?</span>
							</th>
							<td><input type="datetime-local" name="<?php echo esc_attr( $this->launch_option ); ?>" value="<?php echo esc_attr( $launch_date ); ?>"></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Coming Soon Message', 'easy-scheduled-site-launch' ); ?>
								<span class="essl-tooltip" title="This message will display on the coming soon page.">?</span>
							</th>
							<td><textarea name="<?php echo esc_attr( $this->message_option ); ?>" rows="4" style="width:100%;"><?php echo esc_textarea( $message ); ?></textarea></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Upload Logo', 'easy-scheduled-site-launch' ); ?>
								<span class="essl-tooltip" title="Upload your company logo to display on the coming soon page.">?</span>
							</th>
							<td>
								<input type="text" id="essl_logo_url" name="<?php echo esc_attr( $this->logo_option ); ?>" value="<?php echo esc_attr( $logo_url ); ?>" style="width:60%;">
								<button type="button" class="button" id="essl_upload_logo"><?php esc_html_e( 'Upload Logo', 'easy-scheduled-site-launch' ); ?></button>
								<button type="button" class="button" id="essl_remove_logo"><?php esc_html_e( 'Remove Logo', 'easy-scheduled-site-launch' ); ?></button>
								<div style="margin-top:10px;">
									<img id="essl_logo_preview" src="<?php echo esc_url( $logo_url ); ?>" style="max-height:80px;<?php echo $logo_url ? '' : 'display:none;'; ?>">
								</div>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Background Color', 'easy-scheduled-site-launch' ); ?>
								<span class="essl-tooltip" title="Select the background color for your coming soon page.">?</span>
							</th>
							<td><input type="text" id="essl_background_color" name="<?php echo esc_attr( $this->color_option ); ?>" value="<?php echo esc_attr( $color ); ?>" class="essl-color-field"></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Enable Countdown Timer', 'easy-scheduled-site-launch' ); ?>
								<span class="essl-tooltip" title="Display a countdown timer until the launch date.">?</span>
							</th>
							<td>
								<select name="<?php echo esc_attr( $this->countdown_option ); ?>">
									<option value="yes" <?php selected( $countdown, 'yes' ); ?>><?php esc_html_e( 'Yes', 'easy-scheduled-site-launch' ); ?></option>
									<option value="no" <?php selected( $countdown, 'no' ); ?>><?php esc_html_e( 'No', 'easy-scheduled-site-launch' ); ?></option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Template', 'easy-scheduled-site-launch' ); ?>
								<span class="essl-tooltip" title="Choose between a simple or professional template.">?</span>
							</th>
							<td>
								<select name="<?php echo esc_attr( $this->template_option ); ?>">
									<option value="simple" <?php selected( $template, 'simple' ); ?>><?php esc_html_e( 'Simple', 'easy-scheduled-site-launch' ); ?></option>
									<option value="professional" <?php selected( $template, 'professional' ); ?>><?php esc_html_e( 'Professional', 'easy-scheduled-site-launch' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>

				<?php submit_button( __( 'Save Settings', 'easy-scheduled-site-launch' ) ); ?>
			</form>
		</div>

		<style>
			.essl-tooltip {
				display: inline-block;
				background: #030303ff;
				color: #fff;
				border-radius: 50%;
				width: 18px;
				height: 18px;
				text-align: center;
				line-height: 18px;
				font-weight: bold;
				cursor: help;
				margin-left: 5px;
			}

			.essl-tooltip:hover {
				background: #1f373eff;
			}
		</style>

		<script>
			jQuery(document).ready(function($) {
				// Upload Logo
				$('#essl_upload_logo').on('click', function(e) {
					e.preventDefault();
					var frame = wp.media({
						title: 'Select Logo',
						button: {
							text: 'Use this Logo'
						},
						multiple: false
					});
					frame.on('select', function() {
						var attachment = frame.state().get('selection').first().toJSON();
						$('#essl_logo_url').val(attachment.url);
						$('#essl_logo_preview').attr('src', attachment.url).show();
						$('#essl_remove_logo').show();
					});
					frame.open();
				});
				// Remove Logo
				$('#essl_remove_logo').on('click', function(e) {
					e.preventDefault();
					$('#essl_logo_url').val('');
					$('#essl_logo_preview').hide();
					$(this).hide();
				});
				if (!$('#essl_logo_url').val()) {
					$('#essl_remove_logo').hide();
				}
			});
		</script>
		<?php
	}

	/**
	 * Display coming soon page.
	 */
	public function check_launch_date() {
		$enabled = get_option( $this->enable_option, 'yes' );
		if ( 'yes' !== $enabled ) {
			return;
		}
		if ( is_user_logged_in() && current_user_can( 'manage_options' ) ) {
			return;
		}

		$launch_date = get_option( $this->launch_option, '' );
		if ( empty( $launch_date ) ) {
			return;
		}

		$message   = get_option( $this->message_option, 'Our website is coming soon! Stay tuned.' );
		$logo_url  = get_option( $this->logo_option, '' );
		$countdown = get_option( $this->countdown_option, 'yes' );
		$template  = get_option( $this->template_option, 'professional' );
		$color     = get_option( $this->color_option, '#f0f0f0' );

		$launch_ts  = strtotime( str_replace( 'T', ' ', $launch_date ) );
		$current_ts = current_time( 'timestamp' );
		if ( $current_ts > $launch_ts ) {
			return;
		}

		$countdown_html = ( 'yes' === $countdown ) ? '<div id="countdown" class="countdown"></div>' : '';
		$date_js = gmdate( 'Y-m-d H:i:s', $launch_ts );

		if ( 'simple' === $template ) {
			$this->render_simple_template( $logo_url, $message, $countdown_html, $launch_ts, $date_js );
		} else {
			$this->render_professional_template( $logo_url, $message, $countdown_html, $launch_ts, $date_js, $color );
		}
	}

	/**
	 * Render the "Professional" coming soon template.
	 *
	 * Outputs the HTML for the professional coming soon page, including the logo,
	 * message, countdown timer, launch date, and background color.
	 *
	 * @param string $logo_url       URL of the uploaded logo.
	 * @param string $message        Coming soon message text.
	 * @param string $countdown_html HTML content for the countdown timer.
	 * @param int    $launch_ts      Launch timestamp (Unix timestamp).
	 * @param string $date_js        Launch date formatted for JavaScript Date object.
	 * @param string $color          Background color in HEX format.
	 * @return void
	 */
	private function render_professional_template( $logo_url, $message, $countdown_html, $launch_ts, $date_js, $color ) {
		wp_die(
			'
<!DOCTYPE html>
<html>
<head>
	<title>Coming Soon</title>
	<style>
		body { 
			font-family: Montserrat, sans-serif; 
			display:flex; 
			flex-direction:column; 
			align-items:center; 
			justify-content:center; 
			height:100vh; 
			text-align:center; 
			background:' . esc_attr( $color ) . '; 
			color:#fff; 
		}
		.logo { max-width:150px; margin-bottom:20px; }
		.countdown { font-size:28px; font-weight:bold; margin-top:15px; }
		h1 { font-size:48px; margin-bottom:15px; }
		p { font-size:20px; margin-bottom:20px; }
	</style>
</head>
<body>
	' . ( $logo_url ? '<img src="' . esc_url( $logo_url ) . '" class="logo" alt="Logo">' : '' ) . '
	<h1>🚀 Coming Soon</h1>
	<p>' . nl2br( esc_html( $message ) ) . '</p>
	' . wp_kses_post( $countdown_html ) . '
	<script>
		var launchDate = new Date("' . esc_js( $date_js ) . '").getTime();
		var countdownEl = document.getElementById("countdown");
		if(countdownEl){
			setInterval(function(){
				var now = new Date().getTime();
				var distance = launchDate - now;
				if(distance < 0){
					countdownEl.innerHTML = "We are live now!";
					return;
				}
				var d = Math.floor(distance/(1000*60*60*24));
				var h = Math.floor((distance%(1000*60*60*24))/(1000*60*60));
				var m = Math.floor((distance%(1000*60*60))/(1000*60));
				var s = Math.floor((distance%(1000*60))/1000);
				countdownEl.innerHTML = d + "d " + h + "h " + m + "m " + s + "s";
			}, 1000);
		}
	</script>
</body>
</html>',
			esc_html__( 'Coming Soon', 'easy-scheduled-site-launch' ),
			array( 'response' => 503 )
		);
	}

	/**
	 * Render the "Simple" coming soon template.
	 *
	 * Outputs the HTML for the simple coming soon page, including the logo,
	 * message, countdown timer, and launch date.
	 *
	 * @param string $logo_url       URL of the uploaded logo.
	 * @param string $message        Coming soon message text.
	 * @param string $countdown_html HTML content for the countdown timer.
	 * @param int    $launch_ts      Launch timestamp (Unix timestamp).
	 * @param string $date_js        Launch date formatted for JavaScript Date object.
	 * @return void
	 */
	private function render_simple_template( $logo_url, $message, $countdown_html, $launch_ts, $date_js ) {
		wp_die(
			'
<!DOCTYPE html>
<html>
<head>
	<title>Coming Soon</title>
	<style>
		body { 
			font-family: Arial, sans-serif; 
			text-align:center; 
			padding:100px 20px; 
			background:#f0f0f0; 
			color:#333;
		}
		.logo { max-width:200px; margin:0 auto 20px auto; display:block; }
		h1 { font-size:36px; margin-bottom:20px; }
		p { font-size:18px; margin-bottom:20px; }
		.countdown { font-size:22px; font-weight:bold; margin-top:15px; }
	</style>
</head>
<body>
	' . ( esc_url( $logo_url ) ? '<img src="' . esc_url( $logo_url ) . '" class="logo" alt="Logo">' : '' ) . '
	<h1>🚀 Coming Soon</h1>
	<p>' . nl2br( esc_html( $message ) ) . '</p>
	' . wp_kses_post( $countdown_html ) . '
	<script>
		var launchDate = new Date("' . esc_js( $date_js ) . '").getTime();
		var countdownEl = document.getElementById("countdown");
		if(countdownEl){
			setInterval(function(){
				var now = new Date().getTime();
				var distance = launchDate - now;
				if(distance < 0){
					countdownEl.innerHTML = "We are live now!";
					return;
				}
				var d = Math.floor(distance/(1000*60*60*24));
				var h = Math.floor((distance%(1000*60*60*24))/(1000*60*60));
				var m = Math.floor((distance%(1000*60*60))/(1000*60));
				var s = Math.floor((distance%(1000*60))/1000);
				countdownEl.innerHTML = d + "d " + h + "h " + m + "m " + s + "s";
			}, 1000);
		}
	</script>
</body>
</html>',
			esc_html__( 'Coming Soon', 'easy-scheduled-site-launch' ),
			array( 'response' => 503 )
		);
	}
}

new Easy_Scheduled_Site_Launch();
