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
 * Test student info external function.
 *
 * @module  theme_urcourses
 * @author  2024 John Lane <john.lane@uregina.ca>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_urcourses\external;

use \core_external\external_api;
use \core_external\external_function_parameters;
use \core_external\external_value;
use \core_external\external_single_structure;

require_once($CFG->dirroot . '/theme/urcourses/locallib.php');
require_once($CFG->dirroot . "/user/lib.php");

defined('MOODLE_INTERNAL') || die();

class create_test_student extends external_api {
    public static function execute_parameters() {
        return new external_function_parameters([]);
    }

    public static function execute_returns() {
        return new external_value(PARAM_BOOL);
    }

    public static function execute() {
        global $CFG, $DB, $USER;

        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        if (!theme_urcourses_can_create_test_student($USER->id)) {
            throw new \moodle_exception('teststudentnotallowed', 'theme_urcourses');
        }

        $email = "$USER->username+urstudent@uregina.ca";

        if ($user = $DB->get_record('user', ['email' => $email])) {
            throw new \moodle_exception('teststudentexists', 'theme_urcourses');
        }

        $user = new \stdClass();
        $user->email = $email;
        $user->username = "$USER->username-urstudent";
        $user->firstname = $USER->firstname;
        $user->lastname = "$USER->lastname (urstudent)";
        $user->auth = 'manual';
        $user->mnethostid = $CFG->mnet_localhost_id;
        $user->confirmed = 1;
        $user->timecreated = time();
        $user->id = user_create_user($user, false, true);

        $authplugin = get_auth_plugin($user->auth);

        if (!$authplugin->can_change_password()) {
            throw new \moodle_exception('teststudentcoultnotsetpassword', 'theme_urcourses');
        }

        $password = generate_password();
        if (!$authplugin->user_update_password($user, $password)) {
            throw new \moodle_exception('teststudentcoultnotsetpassword', 'theme_urcourses');
        }

        unset_user_preference('create_password', $user); // Prevent cron from generating the password.
        set_user_preference('auth_forcepasswordchange', 1, $user);

        $supportuser = \core_user::get_support_user();
        $site = get_site();

        $a = new \stdClass();
        $a->firstname   = $user->firstname;
        $a->lastname    = $user->lastname;
        $a->sitename    = format_string($site->fullname);
        $a->username    = $user->username;
        $a->newpassword = $password;
        $a->link        = $CFG->wwwroot .'/login';
        $a->signoff     = generate_email_signoff();

        $message = get_string('newtestaccount', 'theme_urcourses', $a);
        $subject  = format_string($site->fullname) .': '. get_string('newtestuser','theme_urcourses');

        $issent = email_to_user($user, $supportuser, $subject, $message);
        if (!$issent) {
            throw new \moodle_exception('teststudentcouldnotemail', 'theme_urcourses');
        }

        return true;
    }
}