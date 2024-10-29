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
        return new external_function_parameters([
            'userid' => new external_value(PARAM_INT),
            'username' => new external_value(PARAM_TEXT),
            'email' => new external_value(PARAM_TEXT)
        ]);
    }

    public static function execute_returns() {
        return new external_value(PARAM_BOOL);
    }

    public static function execute($userid, $username, $email) {
        global $CFG, $DB, $USER;

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'userid' => $userid,
                'username' => $username,
                'email' => $email
            ]
        );

        $context = \context_user::instance($USER->id);
        self::validate_context($context);

        if (!theme_urcourses_can_create_test_student($USER->id)) {
            return false;
        }

        $email = "$USER->username+urstudent@uregina.ca";

        if ($user = $DB->get_record('user', ['email' => $email])) {
            return false;
        }
        else {
            $user = new \stdClass();
            $user->email = $email;
            $user->username = "$USER->username-urstudent";
            $user->firstname = $USER->firstname;
            $user->lastname = "$USER->lastname (urstudent)";
            $user->auth = 'manual';

            $newuserid = user_create_user($user, false, true);
            $newuser = $DB->get_record('user', ['id' => $newuserid]);

            $site = get_site();
            $supportuser = \core_user::get_support_user();

            $userauth = get_auth_plugin($newuser->auth);
            if (!$userauth->can_reset_password() || !is_enabled_auth($newuser->auth)) {
                trigger_error("Attempt to reset user password for user $newuser->username with Auth $newuser->auth.");
                return false;
            }

            $newpassword = generate_password();

            if (!$userauth->user_update_password($newuser, $newpassword)) {
                trigger_error("cannotsetpassword");
            }

            $a = new \stdClass();
            $a->firstname   = $newuser->firstname;
            $a->lastname    = $newuser->lastname;
            $a->sitename    = format_string($site->fullname);
            $a->username    = $newuser->username;
            $a->newpassword = $newpassword;
            $a->link        = $CFG->wwwroot .'/login';
            $a->signoff     = generate_email_signoff();

            $message = get_string('newtestaccount', 'theme_urcourses', $a);
            $subject  = format_string($site->fullname) .': '. get_string('newtestuser','theme_urcourses');

            unset_user_preference('create_password', $newuser); // Prevent cron from generating the password.

            $issent = email_to_user($newuser, $supportuser, $subject, $message);
            if (!$issent) {
                trigger_error("Could not send email to $newuser->username");
            }

            set_user_preference('auth_forcepasswordchange', 1, $newuser);

            return true;
        }
    }
}