<?php
// No direct access, please
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'GeneratePress_Information_Customize_Control' ) ) :
/**
 * Add a control to display simple text
 */
class GeneratePress_Information_Customize_Control extends WP_Customize_Control {
	public $type = 'gp_information_control';

	public $description = '';
	public $notice = '';

	public function enqueue() {
		wp_enqueue_style( 'gp-info-control', trailingslashit( plugin_dir_url( __FILE__ ) )  . 'css/information-control.css', false, GP_PREMIUM_VERSION );
	}

	public function to_json() {
		parent::to_json();
		$this->json['description'] = $this->description;
		$this->json['notice'] = $this->notice;
	}

	public function content_template() {
		?>
		<# if ( data.notice ) { #>
			<div class="gp-info-notice gp-info-notice-info">
		<# } #>

		<# if ( data.label ) { #>
			<span class="customize-control-title">{{ data.label }}</span>
		<# } #>
		<# if ( data.description ) { #>
			<p>{{{ data.description }}}</p>
		<# } #>

		<# if ( data.notice ) { #>
			</div>
		<# } #>
		<?php
	}
}
endif;
