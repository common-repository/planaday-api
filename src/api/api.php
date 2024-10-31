<?php

class api
{
    private $_className;

    public function __construct()
    {
        $this->_className = 'planaday-api';
    }

    /**
     * @return api
     */
    public static function planaday_api_get_instance()
    {
        static $instance;
        if ( $instance === null ) {
            $instance = new self();
        }
        return $instance;
    }

    /**
     * Load all classes
     */
    protected function planaday_api_load_classes()
    {
		require_once __DIR__ . '/classes/client.php';
		require_once __DIR__ . '/classes/menu.php';
		require_once __DIR__ . '/classes/settings.php';
		require_once __DIR__ . '/classes/shortcodes.php';
		require_once __DIR__ . '/classes/payment.php';

        foreach (glob(__DIR__ . '/classes/settings/*.php') as $path) {
            require_once $path;
        }
        foreach (glob(__DIR__ . '/classes/shortcodes/*.php') as $path) {
            require_once $path;
        }
        foreach (glob(__DIR__ . '/classes/widget/*.php') as $path) {
            require_once $path;
        }
    }

    /**
     * Setup
     */
    public function planaday_api_plugin_setup()
    {
        $this->planaday_api_load_classes();

        // Load shortcodes
	    (new Shortcodes())->planaday_api_add_shortcodes();

        // hook add_query_vars function into query_vars
        add_filter('query_vars', [$this, 'planaday_api_add_query_vars']);
        add_filter('rewrite_rules_array', [$this, 'planaday_api_add_rewrite_rules']);

        add_action('save_booking', 'planaday_check_booking', 10, 1);
        do_action('planaday_check_booking');

        // Next only for admins
        if (is_admin()) {
            add_action('admin_menu', [menu::planaday_api_get_instance(), 'planaday_api_add_menu_page']);
            add_action('admin_init', [$this, 'planaday_api_options_update']);
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'planaday_api_action_links']);
        }
    }

    /**
     * @param $aVars
     * @return array
     */
    public function planaday_api_add_query_vars($aVars)
    {
        $aVars[] = shortcodes::COURSESLUG;
        return $aVars;
    }

    /**
     * @param $aRules
     * @return array
     */
    public function planaday_api_add_rewrite_rules($aRules)
    {
        $pages = get_pages([shortcodes::COURSESLUG]);
        foreach ($pages as $page) {
            if (has_shortcode($page->post_content, shortcodes::COURSESLUG)) {
                $post_name = $page->post_name;
            }
        }
        $aNewRules = [$post_name . '/(\d+)/([^/]*)/?' => 'index.php?pagename=' . $post_name . '&' . shortcodes::COURSESLUG . '=$matches[1]'];
        $aRules = $aNewRules + $aRules;
        return $aRules;
    }

    /**
     * @param $links
     * @return array
     */
    public function planaday_api_action_links($links)
    {
        $links[] = '<a href="' . esc_url(admin_url('admin.php?page=planaday-api/index.php')) . '">' . __('Settings', $this->_className) . '</a>';
        return $links;
    }

    /**
     *
     */
    public function planaday_api_options_update()
    {
        register_setting('planaday-api-general', 'planaday-api-general', [settings_general::planaday_api_get_instance(), 'planaday_api_validate']);
        register_setting('planaday-api-css', 'planaday-api-css', [settings_css::planaday_api_get_instance(), 'planaday_api_css_validate']);
        register_setting('planaday-api-payment', 'planaday-api-payment', [settings_payment::planaday_api_get_instance(), 'planaday_api_payment_validate']);
    }
}
