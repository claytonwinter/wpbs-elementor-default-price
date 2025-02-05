<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Typography;

class WPBS_Default_Price_Widget extends Widget_Base {

	/**
	 * Get widget name.
	 */
	public function get_name() {
		return 'wpbs_default_price';
	}

	/**
	 * Get widget title.
	 */
	public function get_title() {
		return __( 'WPBS Default Price', 'wpbs-elementor-default-price' );
	}

	/**
	 * Get widget icon.
	 */
	public function get_icon() {
		return 'eicon-price-table';
	}

	/**
	 * Get widget categories.
	 */
	public function get_categories() {
		return [ 'general' ];
	}

	/**
	 * Register widget controls.
	 */
	protected function _register_controls() {

		// -----------------------------
		// Content Tab
		// -----------------------------
		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'wpbs-elementor-default-price' ),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		// Build the calendar options array using only the calendar name.
		$calendar_options = [];
		if ( function_exists( 'wpbs_get_calendars' ) ) {
			// Only fetch active calendars.
			$calendars = wpbs_get_calendars( array( 'status' => 'active' ) );
			if ( is_array( $calendars ) && ! empty( $calendars ) ) {
				foreach ( $calendars as $calendar ) {
					$calendar_id   = $calendar->get( 'id' );
					$calendar_name = $calendar->get( 'name' );
					if ( empty( $calendar_name ) ) {
						$calendar_name = sprintf( __( 'Calendar #%d', 'wpbs-elementor-default-price' ), $calendar_id );
					}
					// Display in the dropdown as: Calendar Name (ID: 12)
					$calendar_options[ $calendar_id ] = sprintf( '%s (ID: %d)', $calendar_name, $calendar_id );
				}
			}
		}

		$this->add_control(
			'calendar_id',
			[
				'label'       => __( 'Select Calendar', 'wpbs-elementor-default-price' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '',
				'options'     => $calendar_options,
				'description' => __( 'Select the calendar for which you want to display the default price.', 'wpbs-elementor-default-price' ),
			]
		);

		// Add a control to allow the editor to choose the HTML tag for the price output.
		$this->add_control(
			'price_html_tag',
			[
				'label'       => __( 'Price HTML Tag', 'wpbs-elementor-default-price' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => 'h2',
				'options'     => [
					'h1'  => 'H1',
					'h2'  => 'H2',
					'h3'  => 'H3',
					'h4'  => 'H4',
					'h5'  => 'H5',
					'h6'  => 'H6',
					'p'   => 'P',
					'div' => 'div',
				],
				'description' => __( 'Choose the HTML tag for the price output so you can use global styling (for example, a heading style).', 'wpbs-elementor-default-price' ),
			]
		);

		$this->end_controls_section();

		// -----------------------------
		// Style Tab - Price
		// -----------------------------
		$this->start_controls_section(
			'style_price_section',
			[
				'label' => __( 'Price', 'wpbs-elementor-default-price' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'price_typography',
				'label'    => __( 'Typography', 'wpbs-elementor-default-price' ),
				'selector' => '{{WRAPPER}} .wpbs-default-price',
			]
		);
		$this->add_control(
			'price_color',
			[
				'label'     => __( 'Text Color', 'wpbs-elementor-default-price' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpbs-default-price' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the front end.
	 */
	protected function render() {
		$settings    = $this->get_settings_for_display();
		$calendar_id = ! empty( $settings['calendar_id'] ) ? intval( $settings['calendar_id'] ) : 0;

		if ( ! $calendar_id ) {
			echo esc_html__( 'No Calendar selected.', 'wpbs-elementor-default-price' );
			return;
		}

		// Retrieve the default price using WPBS functions if available.
		if ( function_exists( 'wpbs_get_calendar_meta' ) ) {
			$default_price = wpbs_get_calendar_meta( $calendar_id, 'default_price', true );
		} else {
			$default_price = get_post_meta( $calendar_id, 'default_price', true );
		}

		// Get the currency symbol using WPBS function.
		if ( function_exists( 'wpbs_get_currency_symbol' ) ) {
			$currency_symbol = wpbs_get_currency_symbol( $currency );
		} else {
			$currency_symbol = '$';
		}
		if ( empty( $currency_symbol ) ) {
			$currency_symbol = '$';
		}

		// Ensure the price is numeric.
		$price_value = floatval( $default_price );

		// Format the price: no decimals if integer; two decimals if there is a fractional part.
		if ( $price_value == intval( $price_value ) ) {
			$formatted_price = number_format( $price_value, 0, '.', ',' );
		} else {
			$formatted_price = number_format( $price_value, 2, '.', ',' );
		}

		// Build the final price output: currency symbol concatenated with the formatted price.
		$final_price = $currency_symbol . $formatted_price;

		// Get the desired HTML tag for the price output.
		$price_html_tag = ! empty( $settings['price_html_tag'] ) ? $settings['price_html_tag'] : 'h2';

		// Output only the formatted price.
		?>
		<div class="wpbs-default-price-widget">
			<<?php echo esc_attr( $price_html_tag ); ?> class="wpbs-default-price">
				<?php echo esc_html( $final_price ); ?>
			</<?php echo esc_attr( $price_html_tag ); ?>>
		</div>
		<?php
	}

	/**
	 * Render widget output in the editor.
	 */
	protected function _content_template() {}
}
