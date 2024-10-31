<?php

class widget_cursusdetails extends WP_Widget {

	// Main constructor
	public function __construct() {
		parent::__construct(
			'widget_cursusdetails',
			__( 'Planaday cursusdetails', 'planaday-api' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {
		// Set widget defaults
		$defaults = [
			'title'     => 'Cursus details',
			'toontitel' => '',
		];
		extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

		<?php // Widget Title ?>
        <p><strong>Let op</strong>: Deze details worden enkel getoond bij detail van cursus.</p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'planaday-api' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text"
                   value="<?php echo esc_attr( $title ); ?>"/>
        </p>

		<?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'toontitel' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'toontitel' ) ); ?>" type="checkbox" value="1" <?php checked( '1',
				$toontitel ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'toontitel' ) ); ?>"><?php _e( 'Toon titel cursus',
					'planaday-api' ); ?></label>
        </p>

		<?php
		$toonlabels = isset( $toonlabels ) ? $toonlabels : null;
		?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'toonlabels' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'toonlabels' ) ); ?>" type="checkbox" value="1" <?php checked( '1',
				$toonlabels ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'toonlabels' ) ); ?>"><?php _e( 'Toon labels cursus',
					'planaday-api' ); ?></label>
        </p>

		<?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'available' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'available' ) ); ?>" type="checkbox" value="1" <?php checked( '1',
				$available ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'available' ) ); ?>"><?php _e( 'Toon beschikbare plaatsen',
					'planaday-api' ); ?></label>
        </p>

		<?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'costs' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'costs' ) ); ?>" type="checkbox" value="1" <?php checked( '1',
				$costs ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'costs' ) ); ?>"><?php _e( 'Toon prijs', 'planaday-api' ); ?></label>
        </p>

		<?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'location' ) ); ?>" type="checkbox" value="1" <?php checked( '1',
				$location ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"><?php _e( 'Toon locatie', 'planaday-api' ); ?></label>
        </p>

		<?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'daypartsamount' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'daypartsamount' ) ); ?>" type="checkbox" value="1" <?php checked( '1',
				$daypartsamount ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'daypartsamount' ) ); ?>"><?php _e( 'Toon aantal dagdelen',
					'planaday-api' ); ?></label>
        </p>

		<?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'startdate' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'startdate' ) ); ?>" type="checkbox" value="1" <?php checked( '1',
				$startdate ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'startdate' ) ); ?>"><?php _e( 'Toon startdatum',
					'planaday-api' ); ?></label>
        </p>

	<?php }

// Update widget settings
	public function update( $new_instance, $old_instance ) {
		$instance                   = $old_instance;
		$instance['title']          = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
		$instance['toontitel']      = isset( $new_instance['toontitel'] ) ? 1 : false;
		$instance['toonlabels']     = isset( $new_instance['toonlabels'] ) ? 1 : false;
		$instance['available']      = isset( $new_instance['available'] ) ? 1 : false;
		$instance['costs']          = isset( $new_instance['costs'] ) ? 1 : false;
		$instance['daypartsamount'] = isset( $new_instance['daypartsamount'] ) ? 1 : false;
		$instance['location']       = isset( $new_instance['location'] ) ? 1 : false;
		$instance['startdate']      = isset( $new_instance['startdate'] ) ? 1 : false;

		return $instance;
	}

// Display the widget
	public function widget( $args, $instance ) {
		extract( $args );
		$courseid = null;

		global $wp_query;
		if ( isset( $wp_query->query_vars[ shortcodes::COURSESLUG ] ) ) {
			$courseid = urldecode( $wp_query->query_vars[ shortcodes::COURSESLUG ] );
		}

		$pages = get_pages( array( shortcodes::COURSESLUG ) );
		foreach ( $pages as $page ) {
			if ( has_shortcode( $page->post_content, shortcodes::COURSESLUG ) ) {
				$post_name = $page->post_name;
			}
		}
		if ( $courseid !== ''
		     && $wp_query->query_vars['pagename'] === $post_name ) {
			// Check the widget options
			$title          = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
			$toontitel      = ! empty( $instance['toontitel'] ) ? $instance['toontitel'] : false;
			$toonlabels     = ! empty( $instance['toonlabels'] ) ? $instance['toonlabels'] : false;
			$available      = ! empty( $instance['available'] ) ? $instance['available'] : false;
			$costs          = ! empty( $instance['costs'] ) ? $instance['costs'] : false;
			$daypartsamount = ! empty( $instance['daypartsamount'] ) ? $instance['daypartsamount'] : false;
			$location       = ! empty( $instance['location'] ) ? $instance['location'] : false;
			$startdate      = ! empty( $instance['startdate'] ) ? $instance['startdate'] : false;

			echo $before_widget;
			echo '<br><div class="widget-text wp_widget_plugin_box">';
			if ( $title !== '' ) {
				echo $before_title . $title . $after_title;
			}
			echo '<table id="widget-table-planaday">';

			if ( $toontitel === 1 ) {
				if ( $title !== '' ) {
					echo ( new shortcodes() )->planaday_api_course_part( $courseid, 'title' );
				} else {
					echo $before_title . ( new shortcodes() )->planaday_api_course_part( $courseid,
							'title' ) . $after_title;
				}
			}

			if ( $toonlabels === 1 ) {
				echo ( new shortcodes() )->planaday_api_course_part( $courseid, 'labels' );
			}

			if ( $available === 1 ) {
				echo ( new shortcodes() )->planaday_api_course_part( $courseid, 'available' );
			}

			if ( $costs === 1 ) {
				echo ( new shortcodes() )->planaday_api_course_part( $courseid, 'costs' );
			}

			if ( $daypartsamount === 1 ) {
				echo ( new shortcodes() )->planaday_api_course_part( $courseid, 'daypartsamount' );
			}

			if ( $location === 1 ) {
				echo ( new shortcodes() )->planaday_api_course_part( $courseid, 'location' );
			}

			if ( $startdate === 1 ) {
				echo ( new shortcodes() )->planaday_api_course_part( $courseid, 'startdate' );
			}

			echo '</table></div>';
			echo $after_widget;
		}

	}
}
