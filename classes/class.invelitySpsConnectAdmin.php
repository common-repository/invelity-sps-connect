<?php

class InvelitySpsConnectAdmin
{
    private $launcher;
    private $acctivationMessage;
    private $options;

    /**
     * Adds menu items and page
     * Gets options from database
     */
    public function __construct(InvelitySpsConnect $launcher)
    {
        $this->launcher = $launcher;
        if (is_admin()) {
            add_action('admin_menu', [$this, 'add_plugin_page']);
            add_action('admin_init', [$this, 'page_init']);
            add_action('admin_enqueue_scripts', [$this, 'loadMainAdminAssets']);
        }
        $this->options = get_option('invelity_sps_connect_options');

    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        add_submenu_page(
            'invelity-plugins',
            __('Sps connect', $this->launcher->getPluginSlug()),
            __('Sps connect', $this->launcher->getPluginSlug()),
            'manage_options',
            'invelity-sps-connect',
            [$this, 'create_admin_page']
        );
    }

    public function loadMainAdminAssets()
    {
        wp_register_style('invelity-sps-connect-admin-css', $this->launcher->getPluginUrl() . 'assets/css/invelity-sps-connect-admin.css', [], '1.0.0');
        wp_enqueue_style('invelity-sps-connect-admin-css');
    }

    private function getRemoteAd()
    {
        $invelitySpsConnectad = get_transient('invelity-sps-connect-ad');
        if (!$invelitySpsConnectad) {
            $response = '';
            try {
                $query = esc_url_raw(add_query_arg([], 'https://licenses.invelity.com/plugins/invelity-sps-connect/invelityad.json'));
                $response = wp_remote_get($query, ['timeout' => 2, 'sslverify' => false]);
                $response = wp_remote_retrieve_body($response);
                if (!$response && file_exists(plugin_dir_path(__FILE__) . '../json/invelityad.json')) {
                    $response = file_get_contents(plugin_dir_path(__FILE__) . '../json/invelityad.json');
                }
            } catch (Exception $e) {

            }
            if (!$response) {
                $response = '{}';
            }

            set_transient('invelity-sps-connect-ad', $response, 86400);/*Day*/
//            set_transient('invelity-ikros-invoices-ad', $response, 300);/*5 min*/
            $invelitySpsConnectad = $response;
        }
        return json_decode($invelitySpsConnectad, true);
    }


    public
    function create_admin_page()
    {
        // Set class property
        $this->options = get_option('invelity_sps_connect_options');
        ?>
        <div class="wrap invelity-plugins-namespace">
            <h2>
                <?= $this->launcher->getPluginName() ?>
            </h2>

            <form method="post" action="<?= admin_url() ?>options.php">
                <div>
                    <?php
                    settings_fields('invelity_sps_connect_options_group');
                    do_settings_sections('invelity-sps-connect-setting-admin');
                    submit_button();
                    ?>
                </div>
                <div>
                    <?php
                    $adData = $this->getRemoteAd();
                    if ($adData) {
                        ?>
                        <a href="<?= $adData['adDestination'] ?>" target="_blank">
                            <img src="<?= $adData['adImage'] ?>">
                        </a>
                        <?php
                    }
                    ?>
                </div>
            </form>

        </div>
        <?php
    }

    /**
     * Register individual setting options and option sections
     */
    public
    function page_init()
    {
        register_setting(
            'invelity_sps_connect_options_group', // Option group
            'invelity_sps_connect_options', // Option name
            [$this, 'sanitize'] // Sanitize
        );

        add_settings_section(
            'setting_section_1', // ID
            __('Connection settings', $this->launcher->getPluginSlug()), // Title
            [$this, 'print_section_info'], // Callback
            'invelity-sps-connect-setting-admin' // Page
        );


        add_settings_field(
            'username',
            __('Username', $this->launcher->getPluginSlug()),
            [$this, 'username_callback'],
            'invelity-sps-connect-setting-admin',
            'setting_section_1'
        );
        add_settings_field(
            'password',
            __('Password', $this->launcher->getPluginSlug()),
            [$this, 'password_callback'],
            'invelity-sps-connect-setting-admin',
            'setting_section_1'
        );
        add_settings_field(
            'insurvalue',
            __('Insurance value', $this->launcher->getPluginSlug()),
            [$this, 'insurvalue_callback'],
            'invelity-sps-connect-setting-admin',
            'setting_section_1'
        );
        add_settings_field(
            'notifytype',
            __('Notify type', $this->launcher->getPluginSlug()),
            [$this, 'notifytype_callback'],
            'invelity-sps-connect-setting-admin',
            'setting_section_1'
        );
        add_settings_field(
            'productdesc',
            __('Products description', $this->launcher->getPluginSlug()),
            [$this, 'productdesc_callback'],
            'invelity-sps-connect-setting-admin',
            'setting_section_1'
        );
        add_settings_field(
            'returnshipment',
            __('Return shipment', $this->launcher->getPluginSlug()),
            [$this, 'returnshipment_callback'],
            'invelity-sps-connect-setting-admin',
            'setting_section_1'
        );
        add_settings_field(
            'saturdayshipment',
            __('Saturday shipment', $this->launcher->getPluginSlug()),
            [$this, 'saturdayshipment_callback'],
            'invelity-sps-connect-setting-admin',
            'setting_section_1'
        );

        add_settings_field(
            'servicename',
            __('Service name', $this->launcher->getPluginSlug()),
            [$this, 'servicename_callback'],
            'invelity-sps-connect-setting-admin',
            'setting_section_1'
        );
        add_settings_field(
            'units',
            __('Units', $this->launcher->getPluginSlug()),
            [$this, 'units_callback'],
            'invelity-sps-connect-setting-admin',
            'setting_section_1'
        );

    }


    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public
    function sanitize(
        $input
    ) {
        $new_input = [];

        if (isset($input['username'])) {
            $new_input['username'] = sanitize_text_field($input['username']);
        }
        if (isset($input['password'])) {
            $new_input['password'] = sanitize_text_field($input['password']);
        }
        if (isset($input['insurvalue'])) {
            $new_input['insurvalue'] = sanitize_text_field($input['insurvalue']);
        }
        if (isset($input['notifytype'])) {
            $new_input['notifytype'] = sanitize_text_field($input['notifytype']);
        }
        if (isset($input['productdesc'])) {
            $new_input['productdesc'] = substr(sanitize_text_field($input['productdesc']), 0, 149);
        }
        if (isset($input['returnshipment'])) {
            $new_input['returnshipment'] = sanitize_text_field($input['returnshipment']);
        }
        if (isset($input['saturdayshipment'])) {
            $new_input['saturdayshipment'] = sanitize_text_field($input['saturdayshipment']);
        }
        if (isset($input['servicename'])) {
            $new_input['servicename'] = sanitize_text_field($input['servicename']);
        }
        if (isset($input['units'])) {
            $new_input['units'] = sanitize_text_field($input['units']);
        }


        return $new_input;
    }

    /**
     * Print the Section text
     */
    public
    function print_section_info()
    {
        print __('Enter your settings below:', $this->launcher->getPluginSlug());
    }


    public
    function username_callback()
    {
        printf(
            '<input type="text" id="username" name="invelity_sps_connect_options[username]" value="%s" />',
            isset($this->options['username']) ? esc_attr($this->options['username']) : ''
        );
        ?>
        <p class="info">
            <?= __('Input your Sps username given to you by SPS s.r.o.', $this->launcher->getPluginSlug()) ?>
        </p>
        <?php
    }

    public
    function password_callback()
    {
        printf(
            '<input type="text" id="password" name="invelity_sps_connect_options[password]" value="%s" />',
            isset($this->options['password']) ? esc_attr($this->options['password']) : ''
        );
        ?>
        <p class="info">
            <?= __('Input your Sps password given to you by SPS s.r.o.', $this->launcher->getPluginSlug()) ?>
        </p>
        <?php
    }

    public
    function insurvalue_callback()
    {
        printf(
            '<input type="number" id="insurvalue" name="invelity_sps_connect_options[insurvalue]" value="%s" step="0.01" min="0.1" />',
            isset($this->options['insurvalue']) ? esc_attr($this->options['insurvalue']) : ''
        );
        ?>
        <p class="info">
            <?= __('Leave 0 to use insurance equal to order value (min is 0.1)', $this->launcher->getPluginSlug()) ?>
        </p>
        <?php
    }

    public
    function notifytype_callback()
    {
        ?>
        <select id="notifytype" name="invelity_sps_connect_options[notifytype]">
            <option value="0" <?= (isset($this->options['notifytype']) && $this->options['notifytype'] == '0') ? 'selected' : '' ?>>Žiadna</option>
            <option value="1" <?= (isset($this->options['notifytype']) && $this->options['notifytype'] == '1') ? 'selected' : '' ?>>E-mail</option>
            <option value="2" <?= (isset($this->options['notifytype']) && $this->options['notifytype'] == '2') ? 'selected' : '' ?>>Sms notifikácia</option>
            <option value="3" <?= (isset($this->options['notifytype']) && $this->options['notifytype'] == '3') ? 'selected' : '' ?>>Aj email aj sms notifikácia</option>
        </select>
        <?php
    }

    public
    function productdesc_callback()
    {
        printf(
            '<textarea id="productdesc" name="invelity_sps_connect_options[productdesc]"/>%s</textarea>',
            isset($this->options['productdesc']) ? esc_attr($this->options['productdesc']) : ''
        );
    }

    public
    function returnshipment_callback()
    {
        printf(
            '<input type="number" id="returnshipment" name="invelity_sps_connect_options[returnshipment]" value="%s" min="0" max="1" />',
            isset($this->options['returnshipment']) ? esc_attr($this->options['returnshipment']) : ''
        );
    }

    public
    function saturdayshipment_callback()
    {
        printf(
            '<input type="number" id="saturdayshipment" name="invelity_sps_connect_options[saturdayshipment]" value="%s" min="0" max="1" />',
            isset($this->options['saturdayshipment']) ? esc_attr($this->options['saturdayshipment']) : ''
        );
    }

    public
    function servicename_callback()
    {
        ?>
        <select id="servicename" name="invelity_sps_connect_options[servicename]">
            <option value="expres" <?= (isset($this->options['servicename']) && $this->options['servicename'] == 'expres') ? 'selected' : '' ?>>expres</option>
            <option value="0900" <?= (isset($this->options['servicename']) && $this->options['servicename'] == '0900') ? 'selected' : '' ?>>0900</option>
            <option value="1200" <?= (isset($this->options['servicename']) && $this->options['servicename'] == '1200') ? 'selected' : '' ?>>1200</option>
            <option value="export" <?= (isset($this->options['servicename']) && $this->options['servicename'] == 'export') ? 'selected' : '' ?>>export</option>
        </select>
        <?php
    }

    public
    function units_callback()
    {
        ?>
        <select id="units" name="invelity_sps_connect_options[units]">
            <option value="kg" <?= (isset($this->options['units']) && $this->options['units'] == 'kg') ? 'selected' : '' ?>>kg</option>
            <option value="boxa" <?= (isset($this->options['units']) && $this->options['units'] == 'boxa') ? 'selected' : '' ?>>boxa</option>
            <option value="boxb" <?= (isset($this->options['units']) && $this->options['units'] == 'boxb') ? 'selected' : '' ?>>boxb</option>
            <option value="boxc" <?= (isset($this->options['units']) && $this->options['units'] == 'boxc') ? 'selected' : '' ?>>boxc</option>
            <option value="winebox3" <?= (isset($this->options['units']) && $this->options['units'] == 'winebox3') ? 'selected' : '' ?>>winebox3</option>
            <option value="winebox6" <?= (isset($this->options['units']) && $this->options['units'] == 'winebox6') ? 'selected' : '' ?>>winebox6</option>
            <option value="winebox12" <?= (isset($this->options['units']) && $this->options['units'] == 'winebox12') ? 'selected' : '' ?>>winebox12</option>
        </select>
        <?php
    }


}