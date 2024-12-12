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
 * Theme UR Courses - Language pack
 *
 * @package    theme_urcourses
 * @copyright  2023 Daniel Poggenpohl <daniel.poggenpohl@fernuni-hagen.de> and Alexander Bias <bias@alexanderbias.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Let codechecker ignore some sniffs for this file as it is perfectly well ordered, just not alphabetically.
// phpcs:disable moodle.Files.LangFilesOrdering.UnexpectedComment
// phpcs:disable moodle.Files.LangFilesOrdering.IncorrectOrder

// General.
$string['pluginname'] = 'UR Courses';
$string['choosereadme'] = 'UR Courses theme.';
$string['configtitle'] = 'UR Courses';
$string['settingsoverview_buc_desc'] = 'UR Courses theme settings.';

// Settings: General settings tab.
// ... Section: Inheritance.
$string['inheritanceheading'] = 'Inheritance';
$string['inheritanceinherit'] = 'Inherit';
$string['inheritanceduplicate'] = 'Duplicate';
$string['inheritanceoptionsexplanation'] = 'Most of the time, inheriting will be perfectly fine. However, it may happen that imperfect code is integrated into Boost Union which prevents simple SCSS inheritance for particular Boost Union features. If you encounter any issues with Boost Union features which seem not to work in UR Courses as well, try to switch this setting to \'Dupliate\' and, if this solves the problem, report an issue on Github (see the README.md file for details how to report an issue).';
// ... ... Setting: Pre SCSS inheritance setting.
$string['prescssinheritancesetting'] = 'Pre SCSS inheritance';
$string['prescssinheritancesetting_desc'] = 'With this setting, you control if the pre SCSS code from Boost Union should be inherited or duplicated.';
// ... ... Setting: Extra SCSS inheritance setting.
$string['extrascssinheritancesetting'] = 'Extra SCSS inheritance';
$string['extrascssinheritancesetting_desc'] = 'With this setting, you control if the extra SCSS code from Boost Union should be inherited or duplicated.';

// Privacy API.
$string['privacy:metadata'] = 'The UR Courses theme does not store any personal data about any user.';

/**************************************************************
 * EXTENSION POINT:
 * Add your language strings for your settings here.
 *************************************************************/

// Dark Mode
$string['enabledarkmode'] = 'Enable dark mode';
$string['disabledarkmode'] = 'Disable dark mode';

// UR Student Account
$string['resetmodal_title'] = 'Reset Password for Test Student Account';
$string['resetmodal_button'] = 'Reset Password';
$string['resetmodal_confirm'] = 'Are you sure you want to reset the password for your test student account <strong>{$a}</strong>?';
$string['resetmodal_date'] = 'Account Created On';
$string['resetsuccess_title'] = 'Password Reset';
$string['resetsuccess_body'] = 'Your test student password has been reset.';
$string['resetfail_title'] = 'Password Reset Failed';
$string['resetfail_body'] = 'Test student password reset failed.';

$string['createmodal_title'] = 'Create Test Student Account';
$string['createmodal_button'] = 'Create';
$string['createmodal_intro'] = 'The following test user account will be created:';
$string['createmodal_email'] = 'Email';
$string['createmodal_username'] = 'Username';
$string['createmodal_helptext'] = 'You can enrol this account to test and experience a course as a student.';
$string['createmodal_confirmtext'] = 'Would you like to create the test student account?';
$string['createmodal_confirm'] = 'Are you sure you want to create the test student account {$a}?';
$string['createsuccess_title'] = 'Test Student Account Created';
$string['createsuccess_body'] = 'Your test student account has been created.';
$string['createfail_title'] = 'Test Student Account Failed';
$string['createfail_body'] = 'Test student account creation failed.';

$string['enrolurstudent'] = 'Enrol test student account';
$string['unenrolurstudent'] = 'Unenrol test student account';
$string['createteststudent'] = 'Create test student';
$string['modifyteststudent'] = 'Modify test student';
$string['newtestuser'] = 'New test student account';
$string['newtestaccount'] = 'Hi {$a->firstname},

Your new test student account at \'{$a->sitename}\' has been created,
and you have been issued with a new temporary password.

Your current login information is:
   username: {$a->username}
   password: {$a->newpassword}

Please login to \'{$a->sitename}\' to test the new account:
   {$a->link}

In most mail programs, this should appear as a blue link
which you can just click on.  If that doesn\'t work,
then cut and paste the address into the address
line at the top of your web browser window.

Cheers from the \'{$a->sitename}\' administrator,
{$a->signoff}';

$string['feedback_label'] = 'Feedback on UR Courses';
$string['feedback_modal_header'] = 'Feedback';
$string['feedback_modal_body'] = 'Give us your feedback on UR Courses.';
$string['feedback_modal_problem'] = 'Report a problem';
$string['feedback_modal_problem_label'] = 'What went wrong?';
$string['feedback_modal_suggestion'] = 'Make a suggestion';
$string['feedback_modal_suggestion_label'] = 'What can we do better?';
$string['feedback_modal_back'] = 'Go back';

// Errors.

$string['teststudentnotallowed'] = 'You do not have permission to create a test student.';
$string['teststudenteditnotallowed'] = 'You do not have permission to edit a test student account.';
$string['teststudentexists'] = 'You already have a test student account.';
$string['teststudentcouldnotemail'] = 'Failed to send test student account email.';
$string['teststudentcoultnotsetpassword'] = 'Could not set new password for test student.';
