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

namespace GlpiPlugin\Example;

use CommonDBTM;
use CommonGLPI;
use Config as GlpiConfig;

class About extends CommonDBTM
{
    protected static $notable = true;

    public function getTabNameForItem(CommonGLPI $item, $withtemplate = 0)
    {
        if ($item instanceof \Profile && $item->getField('id')) {
            return self::createTabEntry(__s('About'));
        }

        return '';
    }

    public static function displayTabContentForItem(CommonGLPI $item, $tabnum = 1, $withtemplate = 0)
    {
        if ($item instanceof \Profile) {
            $about = new self();
            $about->showAboutInformation();
        }
        return true;
    }

    public function showAboutInformation(): void
    {
        global $CFG_GLPI;

        echo "<div class='spaced'>";
        echo "<table class='tab_cadre_fixe'>";
        echo "<tr><th colspan='2'>" . __s('Server Information') . '</th></tr>';

        echo "<tr class='tab_bg_1'>";
        echo "<td class='b'>" . __s('Server Name') . "</td>";
        echo "<td>";
        $server_name = $_SERVER['SERVER_NAME'] ?? __s('Not available');
        echo htmlspecialchars($server_name);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td class='b'>" . __s('Server URL') . "</td>";
        echo "<td>";
        echo htmlspecialchars($CFG_GLPI['url_base']);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td class='b'>" . __s('GLPI Version') . "</td>";
        echo "<td>";
        echo htmlspecialchars(GLPI_VERSION);
        echo "</td>";
        echo "</tr>";

        echo "<tr class='tab_bg_1'>";
        echo "<td class='b'>" . __s('Plugin Version') . "</td>";
        echo "<td>";
        $plugin = new \Plugin();
        if ($plugin->getFromDBbyDir('example')) {
            echo htmlspecialchars($plugin->fields['version']);
        }
        echo "</td>";
        echo "</tr>";

        echo '</table>';
        echo '</div>';
    }
}
