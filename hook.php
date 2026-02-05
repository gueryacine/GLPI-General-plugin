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

// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

use GlpiPlugin\Glpigeneral\Example;

function plugin_change_profile_glpigeneral()
{
    // Some logic that runs when the profile is changed
}


// Define dropdown relations
function plugin_glpigeneral_getDatabaseRelations()
{
    return ['glpi_plugin_glpigeneral_dropdowns' => ['glpi_plugin_example' => 'plugin_glpigeneral_dropdowns_id']];
}


// Define Dropdown tables to be manage in GLPI :
function plugin_glpigeneral_getDropdown()
{
    // Table => Name
    return [Dropdown::class => __s('Plugin Example Dropdown', 'glpigeneral')];
}



////// SEARCH FUNCTIONS ///////(){

// Define Additionnal search options for types (other than the plugin ones)
function plugin_glpigeneral_getAddSearchOptions($itemtype)
{
    $sopt = [];
    if ($itemtype == 'Computer') {
        // Just for example, not working...
        $sopt[1001]['table']     = 'glpi_plugin_glpigeneral_dropdowns';
        $sopt[1001]['field']     = 'name';
        $sopt[1001]['linkfield'] = 'plugin_glpigeneral_dropdowns_id';
        $sopt[1001]['name']      = __s('Example plugin', 'glpigeneral');
    }
    return $sopt;
}

function plugin_glpigeneral_getAddSearchOptionsNew($itemtype)
{
    $options = [];
    if ($itemtype == 'Computer') {
        //Just for example, not working
        $options[] = [
            'id'        => '1002',
            'table'     => 'glpi_plugin_glpigeneral_dropdowns',
            'field'     => 'name',
            'linkfield' => 'plugin_glpigeneral_dropdowns_id',
            'name'      => __s('Example plugin new', 'glpigeneral'),
        ];
    }
    return $options;
}

// See also GlpiPlugin\Glpigeneral\Example::getSpecificValueToDisplay()
function plugin_glpigeneral_giveItem($type, $ID, $data, $num)
{
    $searchopt = &Search::getOptions($type);
    $table     = $searchopt[$ID]['table'];
    $field     = $searchopt[$ID]['field'];

    switch ($table . '.' . $field) {
        case 'glpi_plugin_glpigeneral_examples.name':
            $out = "<a href='" . Toolbox::getItemTypeFormURL(Example::class) . '?id=' . $data['id'] . "'>";
            $out .= $data[$num][0]['name'];
            if ($_SESSION['glpiis_ids_visible'] || empty($data[$num][0]['name'])) {
                $out .= ' (' . $data['id'] . ')';
            }
            $out .= '</a>';

            return $out;
    }

    return '';
}


function plugin_glpigeneral_displayConfigItem($type, $ID, $data, $num)
{
    $searchopt = &Search::getOptions($type);
    $table     = $searchopt[$ID]['table'];
    $field     = $searchopt[$ID]['field'];

    // Example of specific style options
    // No need of the function if you do not have specific cases
    switch ($table . '.' . $field) {
        case 'glpi_plugin_glpigeneral_examples.name':
            return ' style="background-color:#DDDDDD;" ';
    }

    return '';
}


function plugin_glpigeneral_addDefaultJoin($type, $ref_table, &$already_link_tables)
{
    // Example of default JOIN clause
    // No need of the function if you do not have specific cases
    switch ($type) {
        //       case Example::class :
        case 'MyType':
            return Search::addLeftJoin(
                $type,
                $ref_table,
                $already_link_tables,
                'newtable',
                'linkfield',
            );
    }

    return '';
}


function plugin_glpigeneral_addDefaultSelect($type)
{
    // Example of default SELECT item to be added
    // No need of the function if you do not have specific cases
    switch ($type) {
        //       case Example::class :
        case 'MyType':
            return "`mytable`.`myfield` = 'myvalue' AS MYNAME, ";
    }

    return '';
}


function plugin_glpigeneral_addDefaultWhere($type)
{
    // Example of default WHERE item to be added
    // No need of the function if you do not have specific cases
    switch ($type) {
        //       case Example::class :
        case 'MyType':
            return " `mytable`.`myfield` = 'myvalue' ";
    }

    return '';
}


function plugin_glpigeneral_addLeftJoin($type, $ref_table, $new_table, $linkfield)
{
    // Example of standard LEFT JOIN  clause but use it ONLY for specific LEFT JOIN
    // No need of the function if you do not have specific cases
    switch ($new_table) {
        case 'glpi_plugin_glpigeneral_dropdowns':
            return " LEFT JOIN `$new_table` ON (`$ref_table`.`$linkfield` = `$new_table`.`id`) ";
    }

    return '';
}


function plugin_glpigeneral_forceGroupBy($type)
{
    switch ($type) {
        case Example::class:
            // Force add GROUP BY IN REQUEST
            return true;
    }
    return false;
}


function plugin_glpigeneral_addWhere($link, $nott, $type, $ID, $val, $searchtype)
{
    $searchopt = &Search::getOptions($type);
    $table     = $searchopt[$ID]['table'];
    $field     = $searchopt[$ID]['field'];

    Search::makeTextSearch($val, $nott);

    // Example of standard Where clause but use it ONLY for specific Where
    // No need of the function if you do not have specific cases
    switch ($table . '.' . $field) {
        /*case "glpi_plugin_example.name" :
          $ADD = "";
          if ($nott && $val!="NULL") {
             $ADD = " OR `$table`.`$field` IS NULL";
          }
          return $link." (`$table`.`$field` $SEARCH ".$ADD." ) ";*/
        case 'glpi_plugin_glpigeneral_examples.serial':
            return $link . " `$table`.`$field` = '$val' ";
    }

    return '';
}


// This is not a real example because the use of Having condition in this case is not suitable
function plugin_glpigeneral_addHaving($link, $nott, $type, $ID, $val, $num)
{
    $searchopt = &Search::getOptions($type);
    $table     = $searchopt[$ID]['table'];
    $field     = $searchopt[$ID]['field'];

    $SEARCH = Search::makeTextSearch($val, $nott);

    // Example of standard Having clause but use it ONLY for specific Having
    // No need of the function if you do not have specific cases
    switch ($table . '.' . $field) {
        case 'glpi_plugin_example.serial':
            $ADD = '';
            if (($nott && $val != 'NULL')
                || $val == '^$') {
                $ADD = " OR ITEM_$num IS NULL";
            }

            return " $link ( ITEM_" . $num . $SEARCH . " $ADD ) ";
    }

    return '';
}


function plugin_glpigeneral_addSelect($type, $ID, $num)
{
    $searchopt = &Search::getOptions($type);

    // Example of standard Select clause but use it ONLY for specific Select
    // No need of the function if you do not have specific cases
    // switch ($table.".".$field) {
    //    case "glpi_plugin_example.name" :
    //       return $table.".".$field." AS ITEM_$num, ";
    // }
    return '';
}


function plugin_glpigeneral_addOrderBy($type, $ID, $order, $key = 0)
{
    $searchopt = &Search::getOptions($type);

    // Example of standard OrderBy clause but use it ONLY for specific order by
    // No need of the function if you do not have specific cases
    // switch ($table.".".$field) {
    //    case "glpi_plugin_example.name" :
    //       return " ORDER BY $table.$field $order ";
    // }
    return '';
}


//////////////////////////////
////// SPECIFIC MODIF MASSIVE FUNCTIONS ///////


// Define actions :
function plugin_glpigeneral_MassiveActions($type)
{
    switch ($type) {
        // New action for core and other plugin types : name = plugin_PLUGINNAME_actionname
        case 'Computer':
            return [Example::class . MassiveAction::CLASS_ACTION_SEPARATOR . 'DoIt' => __s('plugin_glpigeneral_DoIt', 'glpigeneral')];

            // Actions for types provided by the plugin are included inside the classes
    }

    return [];
}


// How to display specific update fields ?
// options must contain at least itemtype and options array
function plugin_glpigeneral_MassiveActionsFieldsDisplay($options = [])
{
    //$type,$table,$field,$linkfield

    $table     = $options['options']['table'];
    $field     = $options['options']['field'];

    if ($table == getTableForItemType($options['itemtype'])) {
        // Table fields
        switch ($table . '.' . $field) {
            case 'glpi_plugin_glpigeneral_examples.serial':
                echo __s('Not really specific - Just for example', 'glpigeneral');

                // Dropdown::showYesNo($linkfield);
                // Need to return true if specific display
                return true;
        }
    } else {
        // Linked Fields
        switch ($table . '.' . $field) {
            case 'glpi_plugin_glpigeneral_dropdowns.name':
                echo __s('Not really specific - Just for example', 'glpigeneral');

                // Need to return true if specific display
                return true;
        }
    }
    // Need to return false on non display item
    return false;
}


// How to display specific search fields or dropdown ?
// options must contain at least itemtype and options array
// MUST Use a specific AddWhere & $tab[X]['searchtype'] = 'equals'; declaration
function plugin_glpigeneral_searchOptionsValues($options = [])
{
    $table = $options['searchoption']['table'];
    $field = $options['searchoption']['field'];

    // Table fields
    switch ($table . '.' . $field) {
        case 'glpi_plugin_glpigeneral_examples.serial':
            echo __s('Not really specific - Use your own dropdown - Just for example', 'glpigeneral');
            Dropdown::show(
                getItemTypeForTable($options['searchoption']['table']),
                ['value'       => $options['value'],
                    'name'     => $options['name'],
                    'comments' => 0],
            );

            // Need to return true if specific display
            return true;
    }

    return false;
}


//////////////////////////////

// Hook done on before update item case
function plugin_pre_item_update_glpigeneral($item)
{
    /* Manipulate data if needed
    if (!isset($item->input['comment'])) {
       $item->input['comment'] = addslashes($item->fields['comment']);
    }
    $item->input['comment'] .= addslashes("\nUpdate: ".date('r'));
    */
    Session::addMessageAfterRedirect(__s('Pre Update Computer Hook', 'glpigeneral'), true);
}


// Hook done on update item case
function plugin_item_update_glpigeneral($item)
{
    Session::addMessageAfterRedirect(sprintf(__s('Update Computer Hook (%s)', 'glpigeneral'), implode(',', $item->updates)), true);

    return true;
}


// Hook done on get empty item case
function plugin_item_empty_glpigeneral($item)
{
    if (empty($_SESSION['Already displayed "Empty Computer Hook"'])) {
        Session::addMessageAfterRedirect(__s('Empty Computer Hook', 'glpigeneral'), true);
        $_SESSION['Already displayed "Empty Computer Hook"'] = true;
    }

    return true;
}


// Hook done on before delete item case
function plugin_pre_item_delete_glpigeneral($object)
{
    // Manipulate data if needed
    Session::addMessageAfterRedirect(__s('Pre Delete Computer Hook', 'glpigeneral'), true);
}


// Hook done on delete item case
function plugin_item_delete_glpigeneral($object)
{
    Session::addMessageAfterRedirect(__s('Delete Computer Hook', 'glpigeneral'), true);

    return true;
}


// Hook done on before purge item case
function plugin_pre_item_purge_glpigeneral($object)
{
    // Manipulate data if needed
    Session::addMessageAfterRedirect(__s('Pre Purge Computer Hook', 'glpigeneral'), true);
}


// Hook done on purge item case
function plugin_item_purge_glpigeneral($object)
{
    Session::addMessageAfterRedirect(__s('Purge Computer Hook', 'glpigeneral'), true);

    return true;
}


// Hook done on before restore item case
function plugin_pre_item_restore_glpigeneral($item)
{
    // Manipulate data if needed
    Session::addMessageAfterRedirect(__s('Pre Restore Computer Hook', 'glpigeneral'));
}


// Hook done on before restore item case
function plugin_pre_item_restore_glpigeneral2($item)
{
    // Manipulate data if needed
    Session::addMessageAfterRedirect(__s('Pre Restore Phone Hook', 'glpigeneral'));
}


// Hook done on restore item case
function plugin_item_restore_glpigeneral($item)
{
    Session::addMessageAfterRedirect(__s('Restore Computer Hook', 'glpigeneral'));

    return true;
}


// Hook done on restore item case
function plugin_item_transfer_glpigeneral($parm)
{
    //TRANS: %1$s is the source type, %2$d is the source ID, %3$d is the destination ID
    Session::addMessageAfterRedirect(sprintf(
        __s('Transfer Computer Hook %1$s %2$d -> %3$d', 'glpigeneral'),
        $parm['type'],
        $parm['id'],
        $parm['newID'],
    ));

    return false;
}

// Do special actions for dynamic report
function plugin_glpigeneral_dynamicReport($parm)
{
    if ($parm['item_type'] == Example::class) {
        // Do all what you want for export depending on $parm
        echo 'Personalized export for type ' . $parm['display_type'];
        echo 'with additional datas : <br>';
        echo 'Single data : add1 <br>';
        print $parm['add1'] . '<br>';
        echo 'Array data : add2 <br>';
        Html::printCleanArray($parm['add2']);

        // Return true if personalized display is done
        return true;
    }

    // Return false if no specific display is done, then use standard display
    return false;
}


// Add parameters to Html::printPager in search system
function plugin_glpigeneral_addParamFordynamicReport($itemtype)
{
    if ($itemtype == Example::class) {
        // Return array data containing all params to add : may be single data or array data
        // Search config are available from session variable
        return ['add1' => $_SESSION['glpisearch'][$itemtype]['order'],
            'add2'     => ['tutu' => 'Second Add',
                'Other Data']];
    }

    // Return false or a non array data if not needed
    return false;
}


/**
 * Plugin install process
 *
 * @return boolean
 */
function plugin_glpigeneral_install()
{
    global $DB;

    $migration = new Migration(PLUGIN_GLPIGENERAL_VERSION);
    Config::setConfigurationValues('plugin:Glpigeneral', [
        'configuration' => false,
        'show_server_info' => true,
    ]);

    // Adds the right(s) to all pre-existing profiles with no access by default
    ProfileRight::addProfileRights([Example::$rightname]);

    // Grants full access to profiles that can update the Config (super-admins)
    $migration->addRight(Example::$rightname, ALLSTANDARDRIGHT, [Config::$rightname => UPDATE]);

    $default_charset   = DBConnection::getDefaultCharset();
    $default_collation = DBConnection::getDefaultCollation();
    $default_key_sign  = DBConnection::getDefaultPrimaryKeySignOption();

    if (!$DB->tableExists('glpi_plugin_glpigeneral_examples')) {
        $query = "CREATE TABLE `glpi_plugin_glpigeneral_examples` (
                  `id` int {$default_key_sign} NOT NULL auto_increment,
                  `name` varchar(255) default NULL,
                  `serial` varchar(255) NOT NULL,
                  `plugin_glpigeneral_dropdowns_id` int {$default_key_sign} NOT NULL default '0',
                  `is_deleted` tinyint NOT NULL default '0',
                  `is_template` tinyint NOT NULL default '0',
                  `template_name` varchar(255) default NULL,
                PRIMARY KEY (`id`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->doQuery($query);

        $query = "INSERT INTO `glpi_plugin_glpigeneral_examples`
                       (`id`, `name`, `serial`, `plugin_glpigeneral_dropdowns_id`, `is_deleted`,
                        `is_template`, `template_name`)
                VALUES (1, 'example 1', 'serial 1', 1, 0, 0, NULL),
                       (2, 'example 2', 'serial 2', 2, 0, 0, NULL),
                       (3, 'example 3', 'serial 3', 1, 0, 0, NULL)";
        $DB->doQuery($query);
    }

    if (!$DB->tableExists('glpi_plugin_glpigeneral_dropdowns')) {
        $query = "CREATE TABLE `glpi_plugin_glpigeneral_dropdowns` (
                  `id` int {$default_key_sign} NOT NULL auto_increment,
                  `name` varchar(255) default NULL,
                  `comment` text,
                PRIMARY KEY  (`id`),
                KEY `name` (`name`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->doQuery($query);

        $query = "INSERT INTO `glpi_plugin_glpigeneral_dropdowns`
                       (`id`, `name`, `comment`)
                VALUES (1, 'dp 1', 'comment 1'),
                       (2, 'dp2', 'comment 2')";

        $DB->doQuery($query);
    }

    if (!$DB->tableExists('glpi_plugin_glpigeneral_devicecameras')) {
        $query = "CREATE TABLE `glpi_plugin_glpigeneral_devicecameras` (
                  `id` int {$default_key_sign} NOT NULL AUTO_INCREMENT,
                  `designation` varchar(255) DEFAULT NULL,
                  `comment` text,
                  `manufacturers_id` int {$default_key_sign} NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `designation` (`designation`),
                  KEY `manufacturers_id` (`manufacturers_id`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->doQuery($query);
    }

    if (!$DB->tableExists('glpi_plugin_glpigeneral_items_devicecameras')) {
        $query = "CREATE TABLE `glpi_plugin_glpigeneral_items_devicecameras` (
                  `id` int {$default_key_sign} NOT NULL AUTO_INCREMENT,
                  `items_id` int {$default_key_sign} NOT NULL DEFAULT '0',
                  `itemtype` varchar(255) DEFAULT NULL,
                  `plugin_glpigeneral_devicecameras_id` int {$default_key_sign} NOT NULL DEFAULT '0',
                  `is_deleted` tinyint NOT NULL DEFAULT '0',
                  `is_dynamic` tinyint NOT NULL DEFAULT '0',
                  PRIMARY KEY (`id`),
                  KEY `computers_id` (`items_id`),
                  KEY `plugin_glpigeneral_devicecameras_id` (`plugin_glpigeneral_devicecameras_id`),
                  KEY `is_deleted` (`is_deleted`),
                  KEY `is_dynamic` (`is_dynamic`)
               ) ENGINE=InnoDB DEFAULT CHARSET={$default_charset} COLLATE={$default_collation} ROW_FORMAT=DYNAMIC;";

        $DB->doQuery($query);
    }

    // To be called for each task the plugin manage
    // task in class
    CronTask::Register(Example::class, 'Sample', DAY_TIMESTAMP, ['param' => 50]);
    return true;
}


/**
 * Plugin uninstall process
 *
 * @return boolean
 */
function plugin_glpigeneral_uninstall()
{
    global $DB;

    $config = new Config();
    $config->deleteConfigurationValues('plugin:Glpigeneral', ['configuration' => false]);

    ProfileRight::deleteProfileRights([Example::$rightname]);

    $notif   = new Notification();
    $notif->deleteByCriteria([
        'itemtype' => 'Ticket',
        'event'    => 'plugin_example',
        'FIELDS'   => 'id',
    ]);
    // Old version tables
    if ($DB->tableExists('glpi_dropdown_plugin_example')) {
        $query = 'DROP TABLE `glpi_dropdown_plugin_example`';
        $DB->doQuery($query);
    }
    if ($DB->tableExists('glpi_plugin_example')) {
        $query = 'DROP TABLE `glpi_plugin_example`';
        $DB->doQuery($query);
    }
    // Current version tables
    if ($DB->tableExists('glpi_plugin_glpigeneral_example')) {
        $query = 'DROP TABLE `glpi_plugin_glpigeneral_example`';
        $DB->doQuery($query);
    }
    if ($DB->tableExists('glpi_plugin_glpigeneral_dropdowns')) {
        $query = 'DROP TABLE `glpi_plugin_glpigeneral_dropdowns`;';
        $DB->doQuery($query);
    }
    if ($DB->tableExists('glpi_plugin_glpigeneral_devicecameras')) {
        $query = 'DROP TABLE `glpi_plugin_glpigeneral_devicecameras`;';
        $DB->doQuery($query);
    }
    if ($DB->tableExists('glpi_plugin_glpigeneral_items_devicecameras')) {
        $query = 'DROP TABLE `glpi_plugin_glpigeneral_items_devicecameras`;';
        $DB->doQuery($query);
    }

    return true;
}


function plugin_glpigeneral_AssignToTicket($types)
{
    $types[Example::class] = 'Example';

    return $types;
}


function plugin_glpigeneral_get_events(NotificationTargetTicket $target)
{
    $target->events['plugin_example'] = __s('Example event', 'glpigeneral');
}


function plugin_glpigeneral_get_datas(NotificationTargetTicket $target)
{
    $target->data['##ticket.example##'] = __s('Example datas', 'glpigeneral');
}


function plugin_glpigeneral_postinit()
{
    global $CFG_GLPI;

    // All plugins are initialized, so all types are registered
    //foreach (Infocom::getItemtypesThatCanHave() as $type) {
    // do something
    //}
}


/**
 * Hook to add more data from ldap
 * fields from plugin_retrieve_more_field_from_ldap_glpigeneral
 *
 * @param $datas   array
 *
 * @return array
 **/
function plugin_retrieve_more_data_from_ldap_example(array $datas)
{
    return $datas;
}


/**
 * Hook to add more fields from LDAP
 *
 * @param $fields   array
 *
 * @return array
 **/
function plugin_retrieve_more_field_from_ldap_glpigeneral($fields)
{
    return $fields;
}

// Check to add to status page
function plugin_glpigeneral_Status($param)
{
    // Do checks (no check for example)
    $ok = true;
    echo 'example plugin: example';

    if ($ok) {
        echo '_OK';
    } else {
        echo '_PROBLEM';
        // Only set ok to false if trouble (global status)
        $param['ok'] = false;
    }
    echo "\n";
    return $param;
}

function plugin_glpigeneral_display_central()
{
    echo "<tr><th colspan='2'>";
    echo "<div style='text-align:center; font-size:2em'>";
    echo __s('Plugin example displays on central page', 'glpigeneral');
    echo '</div>';
    echo '</th></tr>';
}

function plugin_glpigeneral_display_login()
{
    echo "<div style='text-align:center; font-size:2em'>";
    echo __s('Plugin example displays on login page', 'glpigeneral');
    echo '</div>';
}

function plugin_glpigeneral_infocom_hook($params)
{
    echo "<tr><th colspan='4'>";
    echo __s('Plugin example displays on central page', 'glpigeneral');
    echo '</th></tr>';
}

function plugin_glpigeneral_filter_actors(array $params = []): array
{
    $itemtype = $params['params']['itemtype'];

    // remove users_id = 1 for assignee list
    if ($itemtype == 'Ticket' && $params['params']['actortype'] == 'assign') {
        foreach ($params['actors'] as $index => &$actor) {
            if ($actor['type'] == 'user' && $actor['items_id'] == 1) {
                unset($params['actors'][$index]);
            }
        }
    }

    return $params;
}

function plugin_glpigeneral_set_impact_icon(array $params)
{
    /** @var array $CFG_GLPI */
    global $CFG_GLPI;

    $itemtype = $params['itemtype'];
    $items_id = $params['items_id'];

    $item = getItemForItemtype($itemtype);
    if ($item instanceof Computer && $item->getFromDB($items_id)) {
        return $CFG_GLPI['root_doc'] . '/plugins/example/public/computer_icon.svg';
    }

    return null;
}
