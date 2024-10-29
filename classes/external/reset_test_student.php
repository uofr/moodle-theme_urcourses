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

defined('MOODLE_INTERNAL') || die();

class reset_test_student extends external_api {
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
        global $DB, $USER;

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

        if ($params['email'] != "$USER->username+urstudent@uregina.ca") {
            return false;
        }

        if ($user = $DB->get_record('user', ['id' => $params['userid']])) {
            return  setnew_password_and_mail($user);
        }
    }
}