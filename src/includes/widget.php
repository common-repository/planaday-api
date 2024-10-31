<?php

class Planaday_Widget_Cursusdetails extends WP_Widget {

    // Main constructor
    public function __construct() {
        die('remove not needed anymore');
        parent::__construct(
            'Planaday_widget_cursusdetails',
            __( 'Planaday cursusdetails', 'planaday-api' ),
            array(
                'customize_selective_refresh' => true,
            )
        );
    }

    // The widget form (for the backend )
    public function form( $instance ) {
        // Set widget defaults
        $defaults = array(
            'title'    => __("Cursus details", "planaday-api"),
            'toontitel' => '',
        );
        extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

        <?php ?>
        <p><strong>Let op</strong>: Deze details worden enkel getoond bij detail van cursus.</p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php __( 'Widget Title', 'planaday-api' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>

        <?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'toonlabels' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'toonlabels' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $toonlabels ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'toonlabels' ) ); ?>"><?php __( 'Toon label cursus', 'planaday-api' ); ?></label>
        </p>

        <?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'toontitel' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'toontitel' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $toontitel ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'toontitel' ) ); ?>"><?php __( 'Toon titel cursus', 'planaday-api' ); ?></label>
        </p>

        <?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'available' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'available' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $available ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'available' ) ); ?>"><?php __( 'Toon beschikbare plaatsen', 'planaday-api' ); ?></label>
        </p>

        <?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'costs' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'costs' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $costs ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'costs' ) ); ?>"><?php __( 'Toon prijs', 'planaday-api' ); ?></label>
        </p>

        <?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'location' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $location ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'location' ) ); ?>"><?php __( 'Toon locatie', 'planaday-api' ); ?></label>
        </p>

        <?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'daypartsamount' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'daypartsamount' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $daypartsamount ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'daypartsamount' ) ); ?>"><?php __( 'Toon aantal dagdelen', 'planaday-api' ); ?></label>
        </p>

        <?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'startdate' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'startdate' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $startdate ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'startdate' ) ); ?>"><?php __( 'Toon startdatum', 'planaday-api' ); ?></label>
        </p>

    <?php }

// Update widget settings
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';
        $instance['toontitel'] = isset( $new_instance['toontitel'] ) ? 1 : false;
        $instance['toonlabels'] = isset( $new_instance['toonlabels'] ) ? 1 : false;
        $instance['available'] = isset( $new_instance['available'] ) ? 1 : false;
        $instance['costs'] = isset( $new_instance['costs'] ) ? 1 : false;
        $instance['daypartsamount'] = isset( $new_instance['daypartsamount'] ) ? 1 : false;
        $instance['location'] = isset( $new_instance['location'] ) ? 1 : false;
        $instance['startdate'] = isset( $new_instance['startdate'] ) ? 1 : false;
        return $instance;
    }

// Display the widget
    public function widget( $args, $instance ) {
        extract( $args );

        global $wp_query;
        if (isset($wp_query->query_vars[shortcodes::COURSESLUG])) {
            $courseid = urldecode($wp_query->query_vars[shortcodes::COURSESLUG]);
        }
        $pages = get_pages(array(shortcodes::COURSESLUG));
        foreach($pages as $page) {
            if (has_shortcode($page->post_content, shortcodes::COURSESLUG)) {
                $post_name = $page->post_name;
            }
        }
        if ($courseid != '' && $wp_query->query_vars['pagename'] == $post_name) {
            // Check the widget options
            $title = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
            $toontitel = ! empty( $instance['toontitel'] ) ? $instance['toontitel'] : false;
            $toolabels = ! empty( $instance['toonlabels'] ) ? $instance['toonlabels'] : false;
            $available = ! empty( $instance['available'] ) ? $instance['available'] : false;
            $costs = ! empty( $instance['costs'] ) ? $instance['costs'] : false;
            $daypartsamount = ! empty( $instance['daypartsamount'] ) ? $instance['daypartsamount'] : false;
            $location = ! empty( $instance['location'] ) ? $instance['location'] : false;
            $startdate = ! empty( $instance['startdate'] ) ? $instance['startdate'] : false;

            echo $before_widget;
            echo '<br><div class="widget-text wp_widget_plugin_box">';
            if ( $title != '' ) {
                echo $before_title . $title . $after_title;
            }
            echo '<table id="widget-table-planaday">';
            if ( $toontitel ) {
                if ($toontitel && $title != '') {
                    echo  (new shortcodes())->planaday_api_course_part($courseid,'title');
                } else {
                    echo $before_title .  (new shortcodes())->planaday_api_course_part($courseid,'title') . $after_title;
                }
            }
            if ( $toonlabels ) {
                echo  (new shortcodes())->planaday_api_course_part($courseid,'labels');
            }
            if ( $available ) {
                echo  (new shortcodes())->planaday_api_course_part($courseid,'available');
            }
            if ( $costs ) {
                echo  (new shortcodes())->planaday_api_course_part($courseid,'costs');
            }
            if ( $daypartsamount ) {
                echo  (new shortcodes())->planaday_api_course_part($courseid, 'daypartsamount');
            }
            if ( $location ) {
                echo  (new shortcodes())->planaday_api_course_part($courseid,'location');
            }
            if ( $startdate ) {
                echo  (new shortcodes())->planaday_api_course_part($courseid,'startdate');
            }
            echo '</table></div>';
            echo $after_widget;
        }

    }
}

class Planaday_Search_Widget extends WP_Widget {
    // Main constructor
    public function __construct() {
        parent::__construct(
            'Planaday_search_widget',
            __( 'Planaday zoeken', 'planaday-api' ),
            array(
                'customize_selective_refresh' => true,
            )
        );
    }
    // The widget form (for the backend )
    public function form( $instance ) {
        // Set widget defaults
        $defaults = array(
            'searchtitle'    => 'Cursus zoeken',
            'toonlocaties'    => 'Toon locaties',
        );
        extract( wp_parse_args( ( array ) $instance, $defaults ) ); ?>

        <?php ?>
        <p>Je kunt altijd dit zoekformulier laten zien.</p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'searchtitle' ) ); ?>"><?php _e( 'Widget Title', 'planaday-api' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'searchtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'searchtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $searchtitle ); ?>" />
        </p>

        <?php ?>
        <p>
            <input id="<?php echo esc_attr( $this->get_field_id( 'toonlocaties' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'toonlocaties' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $toonlocaties ); ?> />
            <label for="<?php echo esc_attr( $this->get_field_id( 'toonlocaties' ) ); ?>"><?php _e( 'Toon locatie select', 'planaday-api' ); ?></label>
        </p>

    <?php }

    // Update widget settings
    public function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['searchtitle'] = isset( $new_instance['searchtitle'] ) ? wp_strip_all_tags( $new_instance['searchtitle'] ) : '';
        return $instance;
    }

    // Display the widget
    public function widget( $args, $instance ) {
        extract( $args );

        // Check the widget options
        $searchtitle = isset( $instance['searchtitle'] ) ? apply_filters( 'widget_title', $instance['searchtitle'] ) : '';
        $toonlocaties = isset( $instance['toonlocaties'] ) ? apply_filters( 'toonlocaties', $instance['toonlocaties'] ) : '';

        echo $before_widget;
        echo '<div class="widget-text wp_widget_plugin_box">';
        echo $before_title . $searchtitle . $after_title;
        echo '<table id="widget-table-planaday">';
        $pages = get_pages(array('coursesearch'));
        foreach($pages as $page) {
            if (has_shortcode($page->post_content, 'coursesearch')) {
                $post_name = $page->post_name;
            }
        }

        echo '<form action="/' . $post_name . '" method="post" novalidate="novalidate" name="padbooking">';
        echo '<p>';
        echo '<span>';
        echo  (new shortcodes())->planaday_api_inputfield("q", $_POST["q"], "Zoek op naam", 100, $errors["q"]);
        echo '</span></p>';

        if ( $toonlocaties ) {
            echo '<p>';
            echo '<span>';
            echo  (new shortcodes())->planaday_api_location_list_select($args);
            echo '</span></p>';
        }

        echo '<p><input value="Zoek cursus" type="submit"><span class="ajax-loader" style="width: 100%"></span></p>';
        echo '</form>';
        echo '</table></div>';
        echo $after_widget;
    }
}

