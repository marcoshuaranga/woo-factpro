<?php

namespace EBilling\WP;

use EBilling\WP\AdminPanel\WCSettingsTab;

final class AdminHooks
{
    public static function init()
    {
        add_filter('woocommerce_settings_tabs_array', function ($settings_tabs) {
            $settings_tabs['settings_tab_ebilling'] = WCSettingsTab::getTitle();
        
            return $settings_tabs;
        }, 30);

        add_action('woocommerce_settings_tabs_settings_tab_ebilling', function () {
            woocommerce_admin_fields(WCSettingsTab::getSettings());
        });

        add_action('woocommerce_update_options_settings_tab_ebilling',function () {
            woocommerce_update_options(WCSettingsTab::getSettings());
        });
    }
}
