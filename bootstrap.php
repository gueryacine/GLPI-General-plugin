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

/**
 * Bootstrap file to locate and include GLPI's main includes file
 * This works regardless of where the plugin is physically located
 */

// Check if GLPI_ROOT is already defined (when called from GLPI)
if (defined('GLPI_ROOT')) {
    include_once GLPI_ROOT . '/inc/includes.php';
    return;
}

// Try to find GLPI root using common paths
$possible_paths = [
    // Standard relative path (when plugin is in GLPI's plugins directory)
    __DIR__ . '/../../../inc/includes.php',

    // Common installation paths
    '/srv/glpi/glpi/inc/includes.php',
    '/var/www/html/glpi/inc/includes.php',
    '/usr/share/glpi/inc/includes.php',
    '/opt/glpi/inc/includes.php',
];

// Try each path
foreach ($possible_paths as $path) {
    $resolved_path = realpath($path);
    if ($resolved_path && file_exists($resolved_path)) {
        include_once $resolved_path;
        return;
    }
}

// If we get here, we couldn't find GLPI
die(
    'ERROR: Could not locate GLPI installation. ' .
    'Please ensure this plugin is installed in GLPI\'s plugins directory, ' .
    'or update the $possible_paths array in ' . __FILE__
);
