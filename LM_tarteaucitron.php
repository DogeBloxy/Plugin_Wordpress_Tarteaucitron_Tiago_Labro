<?php
/*
Plugin Name: Tarte au Citron
Plugin URI: https://tarteaucitron.io
Description: Plugin open-source, permettant la gestion de cookies.
Version: 1.0
Author: Tiago Labro
Author URI: https://dogebloxy.github.io/CV-numerique/
License: GPL2
*/

function lm_enqueue_tarteaucitron_script()
{
  wp_enqueue_script('tarteaucitron', plugin_dir_url(__FILE__) . '/tarteaucitron/tarteaucitron.min.js', array(), null, true);

  $options = get_option('lm_tarteaucitron_settings');
    $hashtag = isset($options['hashtag']) ? $options['hashtag'] : '#tarteaucitron';
    $highPrivacy = isset($options['highPrivacy']) ? $options['highPrivacy'] : 'true';
    $acceptAllCta = isset($options['AcceptAllCta']) ? $options['AcceptAllCta'] : 'true';
    $orientation = isset($options['orientation']) ? $options['orientation'] : 'middle';
    $adblocker = isset($options['adblocker']) ? $options['adblocker'] : 'false';
    $showAlertSmall = isset($options['showAlertSmall']) ? $options['showAlertSmall'] : 'false';
    $cookieslist = isset($options['cookieslist']) ? $options['cookieslist'] : 'false';

    wp_add_inline_script(
        'tarteaucitron',
        'tarteaucitron.init({
            privacyUrl: "",
            bodyPosition: "top", 
            hashtag: "' . esc_js($hashtag) . '",
            cookieName: "tarteaucitron",
            orientation: "' . esc_js($orientation) . '",
            groupServices: true,
            showDetailsOnClick: true,
            serviceDefaultState: "wait",                    
            showAlertSmall: ' . esc_js($showAlertSmall) . ',
            cookieslist: ' . esc_js($cookieslist) . ',
            closePopup: true,
            showIcon: true,
            iconPosition: "BottomRight",
            adblocker: ' . esc_js($adblocker) . ',              
            DenyAllCta: true,
            AcceptAllCta: ' . esc_js($acceptAllCta) . ',
            highPrivacy: ' . esc_js($highPrivacy) . ',
            alwaysNeedConsent: false,
            "handleBrowserDNTRequest": false,
            "removeCredit": false,
            "moreInfoLink": true,
            "useExternalCss": false,
            "useExternalJs": false,        
            "readmoreLink": "",
            "mandatory": true,
            "mandatoryCta": false,
            "googleConsentMode": true,
            "partnersList": true,
        });'
    );
}

function lm_add_admin_menu()
{
    add_menu_page(
        'Plugin Parameters',
        'Tarte Au Citron',
        'manage_options',
        'lm-tarteaucitron',
        'lm_tarteaucitron_options_page'
    );
}
add_action('admin_menu', 'lm_add_admin_menu');


function lm_tarteaucitron_options_page()
{
?>
    <div class="wrap">
        <h1>Tarteaucitron.js parameters</h1>
        <p>Fill in the contents of the Tarteaucitron.js initialization scripts.</p>
        <form method="post" action="options.php">
            <?php
            settings_fields('lm_tarteaucitron_settings_group');
            do_settings_sections('lm_tarteaucitron');
            submit_button();
            ?>
        </form>
    </div>
<?php
}


function lm_tarteaucitron_settings_init()
{
    register_setting('lm_tarteaucitron_settings_group', 'lm_tarteaucitron_settings');

    add_settings_section('lm_tarteaucitron_main', 'Parameters', null, 'lm_tarteaucitron');

    lm_add_text_field_with_description('hashtag', 'Hashtag', 'Automatically open the panel with the hashtag');

    lm_add_select_field_with_description('highPrivacy', 'High Privacy', ['false' => 'Non', 'true' => 'Oui'], 'Disabling the auto consent feature on navigation ?');
    lm_add_select_field_with_description('AcceptAllCta', 'Accept All CTA', ['false' => 'Non', 'true' => 'Oui'], 'Show the accept all button when highPrivacy on');
    lm_add_select_field_with_description('orientation', 'Orientation', ['top' => 'Top', 'bottom' => 'Bottom', 'middle' => 'Middle', 'popup' => 'Popup', 'banner' => 'Banner'], 'Define where the big banner will be.');
    lm_add_select_field_with_description('adblocker', 'Adblocker', ['false' => 'False', 'true' => 'True'], 'Display a message if an adblocker is detected');
    lm_add_select_field_with_description('showAlertSmall', 'Show Alert Small', ['false' => 'False', 'true' => 'True'], 'Show the small banner on bottom/top right ?');
    lm_add_select_field_with_description('cookieslist', 'Cookies List', ['false' => 'False', 'true' => 'True'], 'Display the list of cookies installed ?');
}
add_action('admin_init', 'lm_tarteaucitron_settings_init');

function lm_add_text_field_with_description($key, $label, $description)
{
    add_settings_field(
        $key,
        $label,
        function () use ($key, $description) {
            $options = get_option('lm_tarteaucitron_settings');
            $value = isset($options[$key]) ? esc_attr($options[$key]) : '#tarteaucitron';
            echo "<input type='text' name='lm_tarteaucitron_settings[$key]' value='$value' />";
            echo "<span class='description' style='margin-left: 100px;'>$description</span>";
        },
        'lm_tarteaucitron',
        'lm_tarteaucitron_main'
    );
}

function lm_add_select_field_with_description($key, $label, $options_array, $description)
{
    add_settings_field(
        $key,
        $label,
        function () use ($key, $options_array, $description) {
            $options = get_option('lm_tarteaucitron_settings');
            $value = isset($options[$key]) ? esc_attr($options[$key]) : '';
            echo "<select name='lm_tarteaucitron_settings[$key]'>";
            foreach ($options_array as $opt_value => $opt_label) {
                $selected = ($value === $opt_value) ? 'selected' : '';
                echo "<option value='$opt_value' $selected>$opt_label</option>";
            }
            echo "</select>";
            echo "<span class='description' style='margin-left: 200px;'>$description</span>";
        },
        'lm_tarteaucitron',
        'lm_tarteaucitron_main'
    );
}

add_action('wp_enqueue_scripts', 'lm_enqueue_tarteaucitron_script');
add_action('admin_menu', 'lm_add_admin_menu');
add_action('admin_init', 'lm_tarteaucitron_settings_init');
