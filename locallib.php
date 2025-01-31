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
 * Theme UR Courses - Local library
 *
 * @package    theme_urcourses
 * @copyright  2023 Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/***************************************************************
 * EXTENSION POINT:
 * Add whatever UR Courses local functions you need here.
 **************************************************************/

function theme_urcourses_enable_darkmode() {
    return set_user_preference('theme_urcourses_darkmode', true);
}

function theme_urcourses_disable_darkmode() {
    return unset_user_preference('theme_urcourses_darkmode');
}

function theme_urcourses_darkmode_enabled() {
    return get_user_preferences('theme_urcourses_darkmode', false);
}

function theme_urcourses_can_create_test_student($userid) {
    global $DB;

    $teacherroleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
    $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager']);
    $designerroleid = $DB->get_field('role', 'id', ['shortname' => 'instdesigner']);
    $isteacher = $DB->record_exists('role_assignments', ['userid' => $userid, 'roleid' => $teacherroleid]);
    $ismanager = $DB->record_exists('role_assignments', ['userid' => $userid, 'roleid' => $managerroleid]);
    $isdesigner = $DB->record_exists('role_assignments', ['userid' => $userid, 'roleid' => $designerroleid]);

    return ($isteacher|| $ismanager || $isdesigner || is_siteadmin());
}

function theme_urcourses_has_test_student_account($username) {
    global $DB;

    $email = "$username+urstudent@uregina.ca";
    return $DB->record_exists('user', ['email' => $email]);
}

function theme_urcourses_create_darkmode_link() {
    global $PAGE;

    $darkmodeenabled = theme_urcourses_darkmode_enabled();

    $darkmodelink = new stdClass();
    $darkmodelink->divider = false;
    $darkmodelink->itemtype = 'link';
    $darkmodelink->link = true;
    $darkmodelink->pixicon = $darkmodeenabled ? 'lightmode' : 'darkmode';
    $darkmodelink->pixplugin = 'theme_urcourses';
    $darkmodelink->title = theme_urcourses_darkmode_enabled()
        ? get_string('disabledarkmode', 'theme_urcourses')
        : get_string('enabledarkmode', 'theme_urcourses');
    $darkmodelink->titleidentifier = 'darkmode,theme_urcourses';
    $darkmodelink->url = new moodle_url($PAGE->url, ['darkmode' => !$darkmodeenabled]);

    return $darkmodelink;
}

function theme_urcourses_create_teststudent_link($hasteststudentaccount) {
    global $USER;

    $studentaccountlink = new \stdClass();
    $studentaccountlink->attributes = [
        [
            'name' => 'data-action',
            'value' => $hasteststudentaccount ? 'resetteststudent' : 'createteststudent' 
        ]
    ];
    $studentaccountlink->divider = false;
    $studentaccountlink->itemtype = 'link';
    $studentaccountlink->link = true;
    $studentaccountlink->pixicon = 'i/user';
    $studentaccountlink->title = $hasteststudentaccount
        ? get_string('modifyteststudent', 'theme_urcourses')
        : get_string('createteststudent', 'theme_urcourses');
    $studentaccountlink->titleidentifier = 'studentaccount,theme_urcourses';
    $studentaccountlink->url = '#';

    return $studentaccountlink;
}

function theme_urcourses_add_custom_user_menu_items($usermenuitems, $customitems) {
    $itemcount = count($usermenuitems);
    $preferenceskey = 0;

    foreach($usermenuitems as $key => $item) {
        if (isset($item->title) && $item->title == 'Preferences') {
            $preferenceskey = $key;
            break;
        }
    }
    $insertpoint = $preferenceskey + 1;

    return array_merge(
        array_slice($usermenuitems, 0, $insertpoint),
        $customitems,
        array_slice($usermenuitems, $insertpoint, $itemcount)
    );
}

function theme_urcourses_get_course_related_hints() {
    global $COURSE, $DB, $OUTPUT, $PAGE;

    if (!$PAGE->context->contextlevel == CONTEXT_COURSE) {
        return '';
    }

    $course = get_course($COURSE->id);
    if (empty($course->idnumber)) {
        return '';
    }

    $enrolments = $DB->get_records_sql("SELECT * FROM ur_crn_map WHERE courseid = '$course->idnumber' ORDER BY semester DESC");
    $coursehint_enrol = new \theme_urcourses\output\coursehint_enrol($enrolments, );

    return $OUTPUT->render($coursehint_enrol);
}

// 01 - 04: 10 (Winter)
// 05 - 08: 20 (Spring/Summer)
// 09 - 12: 30 (Fall)
function theme_urcourses_get_current_semester() {
    $now = \core\di::get(\core\clock::class)->now();
    $month = $now->format('m');
    $year = $now->format('Y');

    if ($month >= 1 && $month <= 4) {
        $semester = 10;
    } else if ($month >= 5 && $month <= 8) {
        $semester = 20;
    } else if ($month >= 9 && $month <= 12) {
        $semester = 30;
    }

    return "$year$semester";
}

function theme_urcourses_get_semester_string($semestercode) {
    $year = substr($semestercode, 0, 4);
    $semester = substr($semestercode, -2);
    switch ($semester) {
        case '10':
            return $year . ' ' . get_string('winter', 'theme_urcourses');
        case '20':
            return $year . ' ' . get_string('springsummer', 'theme_urcourses');
        case '30':
            return $year . ' ' . get_string('fall', 'theme_urcourses');
        default:
            return '';
    }
}
