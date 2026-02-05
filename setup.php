<?php

/**
 * -------------------------------------------------------------------------
 * Example plugin for GLPI
 * -------------------------------------------------------------------------
 *
 * LICENSE
 *
 * This file is part of Example.
 *
 * Example is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Example is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Example. If not, see <http://www.gnu.org/licenses/>.
 * -------------------------------------------------------------------------
 * @copyright Copyright (C) 2006-2022 by Example plugin team.
 * @license   GPLv2 https://www.gnu.org/licenses/gpl-2.0.html
 * @link      https://github.com/pluginsGLPI/example
 * -------------------------------------------------------------------------
 */
use Glpi\Plugin\Hooks;
use GlpiPlugin\Glpigeneral\About;
use GlpiPlugin\Glpigeneral\Computer;
use GlpiPlugin\Glpigeneral\Config;
use GlpiPlugin\Glpigeneral\DeviceCamera;
use GlpiPlugin\Glpigeneral\Dropdown;
use GlpiPlugin\Glpigeneral\Example;
use GlpiPlugin\Glpigeneral\Filters\ComputerModelFilter;
use GlpiPlugin\Glpigeneral\ItemForm;
use GlpiPlugin\Glpigeneral\Profile;
use GlpiPlugin\Glpigeneral\RuleTestCollection;
use GlpiPlugin\Glpigeneral\Showtabitem;

use function Safe\define;

define('PLUGIN_GLPIGENERAL_VERSION', '0.1.0');

// Minimal GLPI version, inclusive
define('PLUGIN_GLPIGENERAL_MIN_GLPI', '11.0.0');
// Maximum GLPI version, exclusive
define('PLUGIN_GLPIGENERAL_MAX_GLPI', '11.0.99');

/**
 * Init hooks of the plugin.
 * REQUIRED
 *
 * @return void
 */
function plugin_init_example()
{
    global $PLUGIN_HOOKS,$CFG_GLPI;

    // Params : plugin name - string type - ID - Array of attributes
    // No specific information passed so not needed
    //Plugin::registerClass(Example::getType(),
    //                      array('classname'              => Example::class,
    //                        ));

    Plugin::registerClass(Config::class, ['addtabon' => 'Config']);

    // Params : plugin name - string type - ID - Array of attributes
    Plugin::registerClass(Dropdown::class);

    $types = ['Central', 'Computer', 'ComputerDisk', 'Notification', 'Phone',
        'Preference', 'Profile', 'Supplier'];
    Plugin::registerClass(
        Example::class,
        ['notificationtemplates_types' => true,
            'addtabon'                 => $types,
            'link_types'               => true],
    );

    Plugin::registerClass(
        RuleTestCollection::class,
        ['rulecollections_types' => true],
    );

    Plugin::registerClass(
        DeviceCamera::class,
        ['device_types' => true],
    );

    if (version_compare(GLPI_VERSION, '9.1', 'ge') && class_exists(Example::class)) {
        Link::registerTag(Example::$tags);
    }
    // Display a menu entry ?
    Plugin::registerClass(Profile::class, ['addtabon' => ['Profile']]);
    Plugin::registerClass(About::class, ['addtabon' => ['Profile']]);
    if (Example::canView()) { // Right set in change_profile hook
        $PLUGIN_HOOKS['menu_toadd']['glpigeneral'] = ['plugins' => Example::class,
            'tools'                                         => Example::class];

        // Old menu style
        //       $PLUGIN_HOOKS['menu_entry']['glpigeneral'] = 'front/example.php';
        //
        //       $PLUGIN_HOOKS['submenu_entry']['glpigeneral']['options']['optionname']['title'] = "Search";
        //       $PLUGIN_HOOKS['submenu_entry']['glpigeneral']['options']['optionname']['page']  = '/plugins/example/front/example.php';
        //       $PLUGIN_HOOKS['submenu_entry']['glpigeneral']['options']['optionname']['links']['search'] = '/plugins/example/front/example.php';
        //       $PLUGIN_HOOKS['submenu_entry']['glpigeneral']['options']['optionname']['links']['add']    = '/plugins/example/front/example.form.php';
        //       $PLUGIN_HOOKS['submenu_entry']['glpigeneral']['options']['optionname']['links']['config'] = '/plugins/example/index.php';
        //       $PLUGIN_HOOKS['submenu_entry']['glpigeneral']['options']['optionname']['links']["<img  src='".$CFG_GLPI["root_doc"]."/pics/menu_showall.png' title='".__s('Show all')."' alt='".__s('Show all')."'>"] = '/plugins/example/index.php';
        //       $PLUGIN_HOOKS['submenu_entry']['glpigeneral']['options']['optionname']['links'][__s('Test link', 'glpigeneral')] = '/plugins/example/index.php';

        $PLUGIN_HOOKS[Hooks::HELPDESK_MENU_ENTRY]['glpigeneral']      = true;
        $PLUGIN_HOOKS[Hooks::HELPDESK_MENU_ENTRY_ICON]['glpigeneral'] = 'fas fa-puzzle-piece';
    }

    // Config page
    if (Session::haveRight('config', UPDATE)) {
        $PLUGIN_HOOKS['config_page']['glpigeneral'] = 'front/config.php';
    }

    // Init session
    //$PLUGIN_HOOKS['init_session']['glpigeneral'] = 'plugin_init_session_example';
    // Change profile
    $PLUGIN_HOOKS['change_profile']['glpigeneral'] = 'plugin_change_profile_glpigeneral';
    // Change entity
    //$PLUGIN_HOOKS['change_entity']['glpigeneral'] = 'plugin_change_entity_example';

    // Item action event // See define.php for defined ITEM_TYPE
    $PLUGIN_HOOKS[Hooks::PRE_ITEM_UPDATE]['glpigeneral'] = [Computer::class => 'plugin_pre_item_update_glpigeneral'];
    $PLUGIN_HOOKS[hooks::ITEM_UPDATE]['glpigeneral']     = [Computer::class => 'plugin_item_update_glpigeneral'];

    $PLUGIN_HOOKS[Hooks::ITEM_EMPTY]['glpigeneral'] = [Computer::class => 'plugin_item_empty_glpigeneral'];

    // Restrict right
    $PLUGIN_HOOKS[Hooks::ITEM_CAN]['glpigeneral']     = [Computer::class => [Example::class, 'item_can']];
    $PLUGIN_HOOKS['add_default_where']['glpigeneral'] = [Computer::class => [Example::class, 'add_default_where']];

    // Example using a method in class
    $PLUGIN_HOOKS[Hooks::PRE_ITEM_ADD]['glpigeneral'] = [Computer::class => [Example::class,
        'pre_item_add_computer']];
    $PLUGIN_HOOKS[Hooks::POST_PREPAREADD]['glpigeneral'] = [Computer::class => [Example::class,
        'post_prepareadd_computer']];
    $PLUGIN_HOOKS[Hooks::ITEM_ADD]['glpigeneral'] = [Computer::class => [Example::class,
        'item_add_computer']];

    $PLUGIN_HOOKS[Hooks::PRE_ITEM_DELETE]['glpigeneral'] = [Computer::class => 'plugin_pre_item_delete_glpigeneral'];
    $PLUGIN_HOOKS[Hooks::ITEM_DELETE]['glpigeneral']     = [Computer::class => 'plugin_item_delete_glpigeneral'];

    // Example using the same function
    $PLUGIN_HOOKS[Hooks::PRE_ITEM_PURGE]['glpigeneral'] = [Computer::class => 'plugin_pre_item_purge_glpigeneral',
        'Phone'                                                        => 'plugin_pre_item_purge_glpigeneral'];
    $PLUGIN_HOOKS[Hooks::ITEM_PURGE]['glpigeneral'] = [Computer::class => 'plugin_item_purge_glpigeneral',
        'Phone'                                                    => 'plugin_item_purge_glpigeneral'];

    // Example with 2 different functions
    $PLUGIN_HOOKS[Hooks::PRE_ITEM_RESTORE]['glpigeneral'] = [Computer::class => 'plugin_pre_item_restore_glpigeneral',
        'Phone'                                                          => 'plugin_pre_item_restore_glpigeneral2'];
    $PLUGIN_HOOKS[Hooks::ITEM_RESTORE]['glpigeneral'] = [Computer::class => 'plugin_item_restore_glpigeneral'];

    // Add event to GLPI core itemtype, event will be raised by the plugin.
    // See plugin_glpigeneral_uninstall for cleanup of notification
    $PLUGIN_HOOKS[Hooks::ITEM_GET_EVENTS]['glpigeneral']
                                  = ['NotificationTargetTicket' => 'plugin_glpigeneral_get_events'];

    // Add datas to GLPI core itemtype for notifications template.
    $PLUGIN_HOOKS[Hooks::ITEM_GET_DATA]['glpigeneral']
                                  = ['NotificationTargetTicket' => 'plugin_glpigeneral_get_datas'];

    $PLUGIN_HOOKS[Hooks::ITEM_TRANSFER]['glpigeneral'] = 'plugin_item_transfer_glpigeneral';

    // function to populate planning
    // No more used since GLPI 0.84
    // $PLUGIN_HOOKS['planning_populate']['glpigeneral'] = 'plugin_planning_populate_example';
    // Use instead : add class to planning types and define populatePlanning in class
    $CFG_GLPI['planning_types'][] = Example::class;

    //function to display planning items
    // No more used sinc GLPi 0.84
    // $PLUGIN_HOOKS['display_planning']['glpigeneral'] = 'plugin_display_planning_example';
    // Use instead : displayPlanningItem of the specific itemtype

    // Massive Action definition
    $PLUGIN_HOOKS['use_massive_action']['glpigeneral'] = 1;

    $PLUGIN_HOOKS['assign_to_ticket']['glpigeneral'] = 1;

    // Add specific files to add to the header : javascript or css
    $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['glpigeneral'] = 'glpigeneral.js';
    $PLUGIN_HOOKS[Hooks::ADD_CSS]['glpigeneral']        = 'glpigeneral.css';

    // Add specific tags to the header
    $PLUGIN_HOOKS[Hooks::ADD_HEADER_TAG]['glpigeneral'] = [
        [
            'tag'        => 'meta',
            'properties' => [
                'name'    => 'robots',
                'content' => 'noindex, nofollow',
            ],
        ],
        [
            'tag'        => 'link',
            'properties' => [
                'rel'   => 'alternate',
                'type'  => 'application/rss+xml',
                'title' => 'The company RSS feed',
                'href'  => 'https://example.org/feed.xml',
            ],
        ],
    ];

    // Add specific files to add to the header into anonymous page : javascript or css
    $PLUGIN_HOOKS[Hooks::ADD_CSS_ANONYMOUS_PAGE]['glpigeneral']               = 'glpigeneral_anonymous.css';
    $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT_MODULE_ANONYMOUS_PAGE]['glpigeneral'] = 'mymodule_anonymous.js';
    $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT_ANONYMOUS_PAGE]['glpigeneral']        = 'glpigeneral_anonymous.js';

    // Add specific tags to the header into anonymous page
    $PLUGIN_HOOKS[Hooks::ADD_HEADER_TAG_ANONYMOUS_PAGE]['glpigeneral'] = [
        [
            'tag'        => 'meta',
            'properties' => [
                'name'    => 'robots',
                'content' => 'noindex, nofollow',
            ],
        ],
        [
            'tag'        => 'link',
            'properties' => [
                'rel'   => 'alternate',
                'type'  => 'application/rss+xml',
                'title' => 'The company RSS feed',
                'href'  => 'https://example.org/feed.xml',
            ],
        ],
    ];

    // request more attributes from ldap
    //$PLUGIN_HOOKS['retrieve_more_field_from_ldap']['glpigeneral']="plugin_retrieve_more_field_from_ldap_glpigeneral";

    // Retrieve others datas from LDAP
    //$PLUGIN_HOOKS['retrieve_more_data_from_ldap']['glpigeneral']="plugin_retrieve_more_data_from_ldap_example";

    // Reports
    $PLUGIN_HOOKS['reports']['glpigeneral'] = ['report.php' => 'New Report',
        'report.php?other'                              => 'New Report 2'];

    // Stats
    $PLUGIN_HOOKS['stats']['glpigeneral'] = ['stat.php' => 'New stat',
        'stat.php?other'                            => 'New stats 2', ];

    $PLUGIN_HOOKS[Hooks::POST_INIT]['glpigeneral'] = 'plugin_glpigeneral_postinit';

    $PLUGIN_HOOKS['status']['glpigeneral'] = 'plugin_glpigeneral_Status';

    $PLUGIN_HOOKS[Hooks::DISPLAY_CENTRAL]['glpigeneral'] = 'plugin_glpigeneral_display_central';
    $PLUGIN_HOOKS[Hooks::DISPLAY_LOGIN]['glpigeneral']   = 'plugin_glpigeneral_display_login';
    $PLUGIN_HOOKS[Hooks::INFOCOM]['glpigeneral']         = 'plugin_glpigeneral_infocom_hook';

    // pre_show and post_show for tabs and items,
    // see GlpiPlugin\Glpigeneral\Showtabitem class for implementation explanations
    $PLUGIN_HOOKS[Hooks::PRE_SHOW_TAB]['glpigeneral']   = [Showtabitem::class, 'pre_show_tab'];
    $PLUGIN_HOOKS[Hooks::POST_SHOW_TAB]['glpigeneral']  = [Showtabitem::class, 'post_show_tab'];
    $PLUGIN_HOOKS[Hooks::PRE_SHOW_ITEM]['glpigeneral']  = [Showtabitem::class, 'pre_show_item'];
    $PLUGIN_HOOKS[Hooks::POST_SHOW_ITEM]['glpigeneral'] = [Showtabitem::class, 'post_show_item'];

    $PLUGIN_HOOKS[Hooks::PRE_ITEM_FORM]['glpigeneral']  = [ItemForm::class, 'preItemForm'];
    $PLUGIN_HOOKS[Hooks::POST_ITEM_FORM]['glpigeneral'] = [ItemForm::class, 'postItemForm'];

    $PLUGIN_HOOKS[Hooks::PRE_ITIL_INFO_SECTION]['glpigeneral']  = [ItemForm::class, 'preSection'];
    $PLUGIN_HOOKS[Hooks::POST_ITIL_INFO_SECTION]['glpigeneral'] = [ItemForm::class, 'postSection'];

    // Add new actions to timeline
    $PLUGIN_HOOKS[Hooks::TIMELINE_ACTIONS]['glpigeneral'] = [
        ItemForm::class, 'timelineActions',
    ];

    // declare this plugin as an import plugin for Computer itemtype
    $PLUGIN_HOOKS['import_item']['glpigeneral'] = [Computer::class => ['Plugin']];

    // add additional informations on Computer::showForm
    $PLUGIN_HOOKS[Hooks::AUTOINVENTORY_INFORMATION]['glpigeneral'] = [
        Computer::class => [Computer::class, 'showInfo'],
    ];

    $PLUGIN_HOOKS[Hooks::FILTER_ACTORS]['glpigeneral'] = 'plugin_glpigeneral_filter_actors';

    // add new cards to dashboard grid
    $PLUGIN_HOOKS['dashboard_types']['glpigeneral'] = [Example::class, 'dashboardTypes'];
    $PLUGIN_HOOKS['dashboard_cards']['glpigeneral'] = [Example::class, 'dashboardCards'];

    // Dashboard filter
    $PLUGIN_HOOKS[Hooks::DASHBOARD_FILTERS]['glpigeneral'] = [
        ComputerModelFilter::class,
    ];

    // Icon in the impact analysis
    $PLUGIN_HOOKS[Hooks::SET_ITEM_IMPACT_ICON]['glpigeneral'] = 'plugin_glpigeneral_set_impact_icon';
}


/**
 * Get the name and the version of the plugin
 * REQUIRED
 *
 * @return array
 */
function plugin_version_example()
{
    return [
        'name'         => 'Plugin Example',
        'version'      => PLUGIN_GLPIGENERAL_VERSION,
        'author'       => 'Example plugin team',
        'license'      => 'GPLv2+',
        'homepage'     => 'https://github.com/pluginsGLPI/example',
        'requirements' => [
            'glpi' => [
                'min' => PLUGIN_GLPIGENERAL_MIN_GLPI,
                'max' => PLUGIN_GLPIGENERAL_MAX_GLPI,
            ],
        ],
    ];
}


/**
 * Check pre-requisites before install
 * OPTIONNAL, but recommanded
 *
 * @return boolean
 */
function plugin_glpigeneral_check_prerequisites()
{
    return !false;
}

/**
 * Check configuration process
 *
 * @param boolean $verbose Whether to display message on failure. Defaults to false
 *
 * @return boolean
 */
function plugin_glpigeneral_check_config($verbose = false)
{
    if (true) { // Your configuration check
        return true;
    }

    if ($verbose) {
        echo __s('Installed / not configured', 'glpigeneral');
    }
    return false;
}
