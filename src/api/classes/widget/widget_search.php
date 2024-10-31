<?php

class widget_search extends WP_Widget {
	// Main constructor
	public function __construct() {
		parent::__construct(
			'widget_search',
			__( 'Planaday zoeken', 'planaday-api' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {
		// Set widget defaults
		$defaults = [
			'searchtitle'  => 'Cursus zoeken',
			'toonlocaties' => 'Toon locaties',
		];
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
        <p>Je kunt altijd deze zoekformulier laten zien.</p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'searchtitle' ) ); ?>"><?php _e( 'Widget Title',
					'planaday-api' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'searchtitle' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'searchtitle' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $searchtitle ); ?>"/>
        </p>

		<?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'toonlocaties' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'toonlocaties' ) ); ?>" type="checkbox" value="1" <?php checked( '1',
				$toonlocaties ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'toonlocaties' ) ); ?>"><?php _e( 'Toon locatie select',
					'planaday-api' ); ?></label>
        </p>

	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance                = $old_instance;
		$instance['searchtitle'] = isset( $new_instance['searchtitle'] ) ? wp_strip_all_tags( $new_instance['searchtitle'] ) : '';

		return $instance;
	}

	// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );

		// Check the widget options
		$searchtitle  = isset( $instance['searchtitle'] ) ? apply_filters( 'widget_title', $instance['searchtitle'] ) : '';
		$toonlocaties = isset( $instance['toonlocaties'] ) ? apply_filters( 'toonlocaties', $instance['toonlocaties'] ) : '';

		echo $before_widget;
		echo '<div class="widget-text wp_widget_plugin_box">';
		echo $before_title . $searchtitle . $after_title;
		echo '<table id="widget-table-planaday">';
		$pages = get_pages( array( 'coursesearch' ) );
		foreach ( $pages as $page ) {
			if ( has_shortcode( $page->post_content, 'coursesearch' ) ) {
				$post_name = $page->post_name;
			}
		}

		echo '<form action="/' . $post_name . '" method="post" novalidate="novalidate" name="padbooking">';
		echo '<p class="pad-p-widget">';
		echo '<span>';
		echo (new shortcodes())->planaday_api_inputfield( "q", $_POST["q"], "Zoek op naam", 100, $errors["q"] );
		echo '</span></p>';

		if ( $toonlocaties ) {
			echo '<p class="pad-p-widget">';
			echo '<span>';
			echo (new shortcodes())->planaday_api_location_list_select( $args );
			echo '</span></p>';
		}

		echo '<p class="pad-p-widget"><input value="Zoek cursus" type="submit"><span class="ajax-loader" style="width: 100%"></span></p>';
		echo '</form>';
		echo '</table></div>';
		echo $after_widget;
	}
}

