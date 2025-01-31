<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme UR Courses - Drawers page layout.
 *
 * This layoutfile is based on theme/boost_union/layout/drawers.php
 *
 * @package   theme_urcourses
 * @copyright 2024 John Lane <john.lane@uregina.ca>
 * @copyright based on code from theme_boost_union by Luca Bösch
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/behat/lib.php');
require_once($CFG->dirroot . '/course/lib.php');

require_once($CFG->dirroot . '/theme/boost_union/locallib.php');
require_once($CFG->dirroot . '/theme/urcourses/locallib.php');

// Add activity navigation if the feature is enabled.
$activitynavigation = get_config('theme_boost_union', 'activitynavigation');
if ($activitynavigation == THEME_BOOST_UNION_SETTING_SELECT_YES) {
    $PAGE->theme->usescourseindex = false;
}

$setdarkmode = optional_param('darkmode', null, PARAM_BOOL);
if (!is_null($setdarkmode)) {
    if ($setdarkmode) {
        theme_urcourses_enable_darkmode();
    }
    else {
        theme_urcourses_disable_darkmode();
    }
}

$darkmodeenabled = theme_urcourses_darkmode_enabled();
if ($darkmodeenabled) {
    $PAGE->requires->css('/theme/urcourses/style/darkmode.css?v=2025012700');
}

// Extra css.
$PAGE->requires->css('/theme/urcourses/style/legacy.css?v=2024090200');
$PAGE->requires->css('/theme/urcourses/style/callout.css?v=2024090200');
$PAGE->requires->css('/theme/urcourses/style/alert.css?v=2024090200');
$PAGE->requires->css('/theme/urcourses/style/pullquote.css?v=2024090200');
$PAGE->requires->css('/theme/urcoursesstyle/c4l.css?v=2024090200');

// Add block button in editing mode.
$addblockbutton = $OUTPUT->addblockbutton();

if (isloggedin()) {
    $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);

    if (isguestuser()) {
        $sitehomerighthandblockdrawerserverconfig = get_config('theme_boost_union', 'showsitehomerighthandblockdraweronguestlogin');
    } else {
        $sitehomerighthandblockdrawerserverconfig = get_config('theme_boost_union', 'showsitehomerighthandblockdraweronfirstlogin');
    }

    $isadminsettingyes = ($sitehomerighthandblockdrawerserverconfig == THEME_BOOST_UNION_SETTING_SELECT_YES);
    $blockdraweropen = (get_user_preferences('drawer-open-block', $isadminsettingyes)) == true;
} else {
    $courseindexopen = false;
    $blockdraweropen = false;

    if (get_config('theme_boost_union', 'showsitehomerighthandblockdraweronvisit') == THEME_BOOST_UNION_SETTING_SELECT_YES) {
        $blockdraweropen = true;
    }
}

if (defined('BEHAT_SITE_RUNNING') && get_user_preferences('behat_keep_drawer_closed') != 1) {
    try {
        if (
            get_config('theme_boost_union', 'showsitehomerighthandblockdraweronvisit') === false &&
            get_config('theme_boost_union', 'showsitehomerighthandblockdraweronguestlogin') === false &&
            get_config('theme_boost_union', 'showsitehomerighthandblockdraweronfirstlogin') === false
        ) {
            $blockdraweropen = true;
        }
    } catch (Exception $e) {
        echo $e->getMessage();

        $blockdraweropen = true;
    }
}

$extraclasses = ['uses-drawers'];
if ($courseindexopen) {
    $extraclasses[] = 'drawer-open-index';
}

$blockshtml = $OUTPUT->blocks('side-pre');
$hasblocks = (strpos($blockshtml, 'data-block=') !== false || !empty($addblockbutton));
if (!$hasblocks) {
    $blockdraweropen = false;
}
$courseindex = core_course_drawer();
if (!$courseindex) {
    $courseindexopen = false;
}

$forceblockdraweropen = $OUTPUT->firstview_fakeblocks();

$secondarynavigation = false;
$overflow = '';
if ($PAGE->has_secondary_navigation()) {
    $tablistnav = $PAGE->has_tablist_secondary_navigation();
    $moremenu = new \core\navigation\output\more_menu($PAGE->secondarynav, 'nav-tabs', true, $tablistnav);
    $secondarynavigation = $moremenu->export_for_template($OUTPUT);
    $overflowdata = $PAGE->secondarynav->get_overflow_menu_data();
    if (!is_null($overflowdata)) {
        $overflow = $overflowdata->export_for_template($OUTPUT);
    }
}

// Load the navigation from boost_union primary navigation, the extended version of core primary navigation.
// It includes the smart menus and menu items, for multiple locations.
$primary = new theme_boost_union\output\navigation\primary($PAGE);
$renderer = $PAGE->get_renderer('core');
$primarymenu = $primary->export_for_template($renderer);

// Add special class selectors to improve the Smart menus SCSS selectors.
if (isset($primarymenu['includesmartmenu']) && $primarymenu['includesmartmenu'] == true) {
    $extraclasses[] = 'theme-boost-union-smartmenu';
}
if (isset($primarymenu['bottombar']) && !empty($primarymenu['includesmartmenu'])) {
    $extraclasses[] = 'theme-boost-union-bottombar';
}

// Include the extra classes for the course index modification.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/courseindex.php');

$buildregionmainsettings = !$PAGE->include_region_main_settings_in_header_actions() && !$PAGE->has_secondary_navigation();
// If the settings menu will be included in the header then don't add it here.
$regionmainsettingsmenu = $buildregionmainsettings ? $OUTPUT->region_main_settings_menu() : false;

if ($darkmodeenabled) {
    $extraclasses[] = 'ur-dark-mode';
}

$bodyattributes = $OUTPUT->body_attributes($extraclasses); // In the original layout file, this line is place more above,
                                                           // but we amended $extraclasses and had to move it.

$header = $PAGE->activityheader;
$headercontent = $header->export_for_template($renderer);

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => context_course::instance(SITEID), "escape" => false]),
    'output' => $OUTPUT,
    'sidepreblocks' => $blockshtml,
    'hasblocks' => $hasblocks,
    'bodyattributes' => $bodyattributes,
    'courseindexopen' => $courseindexopen,
    'blockdraweropen' => $blockdraweropen,
    'courseindex' => $courseindex,
    'primarymoremenu' => $primarymenu['moremenu'],
    'secondarymoremenu' => $secondarynavigation ?: false,
    'mobileprimarynav' => $primarymenu['mobileprimarynav'],
    'usermenu' => $primarymenu['user'],
    'langmenu' => $primarymenu['lang'],
    'forceblockdraweropen' => $forceblockdraweropen,
    'regionmainsettingsmenu' => $regionmainsettingsmenu,
    'hasregionmainsettingsmenu' => !empty($regionmainsettingsmenu),
    'overflow' => $overflow,
    'headercontent' => $headercontent,
    'addblockbutton' => $addblockbutton,
];

// Include the template content for the course related hints.
require_once(__DIR__ . '/includes/courserelatedhints.php');

// Include the template content for the block regions.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/blockregions.php');

// Include the content for the back to top button.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/backtotopbutton.php');

// Include the content for the scrollspy.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/scrollspy.php');

// Include the template content for the footnote.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/footnote.php');

// Include the template content for the static pages.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/staticpages.php');

// Include the template content for the footer button.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/footer.php');

// Include the template content for the JavaScript disabled hint.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/javascriptdisabledhint.php');

// Include the template content for the info banners.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/infobanners.php');

// Include the template content for the navbar.
require_once(__DIR__ . '/includes/navbar.php');

// Include the template content for the advertisement tiles, but only if we are on the frontpage.
if ($PAGE->pagelayout == 'frontpage') {
    require_once($CFG->dirroot . '/theme/boost_union/layout/includes/advertisementtiles.php');
}

// Include the template content for the slider, but only if we are on the frontpage.
if ($PAGE->pagelayout == 'frontpage') {
    require_once($CFG->dirroot . '/theme/boost_union/layout/includes/slider.php');
}

// Include the template content for the smart menus.
require_once($CFG->dirroot . '/theme/boost_union/layout/includes/smartmenus.php');

// Render drawers.mustache from theme_boost (which is overridden in theme_boost_union).
echo $OUTPUT->render_from_template('theme_boost/drawers', $templatecontext);
