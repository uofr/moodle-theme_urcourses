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
 * Theme UR Courses - Core renderer
 *
 * @package    theme_urcourses
 */

namespace theme_urcourses\output;

use moodle_url;

/**
 * Extending the core_renderer interface.
 *
 * @package    theme_urcourses
 */
class core_renderer extends \theme_boost_union\output\core_renderer {
    /**
     * Renders the login form.
     *
     * This renderer function is copied and modified from /lib/classes/output/core_renderer.php
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        global $CFG, $SITE;

        $context = $form->export_for_template($this);

        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string(
            $SITE->fullname,
            true,
            ['context' => \context_course::instance(SITEID), "escape" => false]
        );

        // Check if the local login form is enabled.
        $loginlocalloginsetting = get_config('theme_boost_union', 'loginlocalloginenable');
        $showlocallogin = ($loginlocalloginsetting != false) ? $loginlocalloginsetting : THEME_BOOST_UNION_SETTING_SELECT_YES;
        if ($showlocallogin == THEME_BOOST_UNION_SETTING_SELECT_YES) {
            // Add marker to show the local login form to template context.
            $context->showlocallogin = true;
        }

        // Check if the local login intro is enabled.
        $loginlocalshowintrosetting = get_config('theme_boost_union', 'loginlocalshowintro');
        $showlocalloginintro = ($loginlocalshowintrosetting != false) ?
            $loginlocalshowintrosetting : THEME_BOOST_UNION_SETTING_SELECT_NO;
        if ($showlocalloginintro == THEME_BOOST_UNION_SETTING_SELECT_YES) {
            // Add marker to show the local login intro to template context.
            $context->showlocalloginintro = true;
        }

        // Check if the IDP login intro is enabled.
        $loginidpshowintrosetting = get_config('theme_boost_union', 'loginidpshowintro');
        $showidploginintro = ($loginidpshowintrosetting != false) ?
                $loginidpshowintrosetting : THEME_BOOST_UNION_SETTING_SELECT_YES;
        if ($showidploginintro == THEME_BOOST_UNION_SETTING_SELECT_YES) {
            // Add marker to show the IDP login intro to template context.
            $context->showidploginintro = true;
        }

        // Custom context
        $casurl = new moodle_url('index.php', ['authCAS' => 'CAS']);
        $context->newstudenturl = 'https://novapp.cc.uregina.ca/perl/studentlookup.cgi';
        $context->forgotpasswordurl = 'https://novapp.cc.uregina.ca/perl/resetpass.cgi';
        $context->activateurl = 'https://novapp.cc.uregina.ca/perl/activate.cgi';
        $context->casurl = $casurl->out();

        return $this->render_from_template('core/loginform', $context);
    }

    /**
     * Wrapper for header elements.
     *
     * This renderer function is copied and modified from /lib/classes/output/core_renderer.php
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        $pagetype = $this->page->pagetype;
        $homepage = get_home_page();
        $homepagetype = null;
        // Add a special case since /my/courses is a part of the /my subsystem.
        if ($homepage == HOMEPAGE_MY || $homepage == HOMEPAGE_MYCOURSES) {
            $homepagetype = 'my-index';
        } else if ($homepage == HOMEPAGE_SITE) {
            $homepagetype = 'site-index';
        }
        if (
            $this->page->include_region_main_settings_in_header_actions() &&
                !$this->page->blocks->is_block_present('settings')
        ) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(\html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new \stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->instructors = $this->get_instructors($this->page->course->id);
        $header->headeractions = $this->page->get_header_actions();

        // Add the course header image for rendering.
        if ($this->page->pagelayout == 'course' && (get_config('theme_boost_union', 'courseheaderimageenabled')
                        == THEME_BOOST_UNION_SETTING_SELECT_YES)) {
            // If course header images are activated, we get the course header image url
            // (which might be the fallback image depending on the course settings and theme settings).
            $header->courseheaderimageurl = theme_boost_union_get_course_header_image_url();
            // Additionally, get the course header image height.
            $header->courseheaderimageheight = get_config('theme_boost_union', 'courseheaderimageheight');
            // Additionally, get the course header image position.
            $header->courseheaderimageposition = get_config('theme_boost_union', 'courseheaderimageposition');
            // Additionally, get the template context attributes for the course header image layout.
            $courseheaderimagelayout = get_config('theme_boost_union', 'courseheaderimagelayout');
            switch($courseheaderimagelayout) {
                case THEME_BOOST_UNION_SETTING_COURSEIMAGELAYOUT_HEADINGABOVE:
                    $header->courseheaderimagelayoutheadingabove = true;
                    $header->courseheaderimagelayoutstackedclass = '';
                    break;
                case THEME_BOOST_UNION_SETTING_COURSEIMAGELAYOUT_STACKEDDARK:
                    $header->courseheaderimagelayoutheadingabove = false;
                    $header->courseheaderimagelayoutstackedclass = 'dark';
                    break;
                case THEME_BOOST_UNION_SETTING_COURSEIMAGELAYOUT_STACKEDLIGHT:
                    $header->courseheaderimagelayoutheadingabove = false;
                    $header->courseheaderimagelayoutstackedclass = 'light';
                    break;
            }
        }

        if (!empty($pagetype) && !empty($homepagetype) && $pagetype == $homepagetype) {
            $header->welcomemessage = \core\user::welcome_message();
        }
        return $this->render_from_template('core/full_header', $header);
    }

    private function get_instructors($courseid) {
        global $CFG, $DB;
        if ($courseid == SITEID) {
            return '';
        }

        $users = [];
        $instructors = [];
        $alreadylisted = [];
        $context = \context_course::instance($courseid);
        $roles = $DB->get_records_list('role', 'shortname', ['teacher', 'editingteacher']);
        $userfields = 'u.id,u.picture,u.firstname,u.lastname,u.firstnamephonetic,u.lastnamephonetic,u.middlename,u.alternatename,u.imagealt,u.email';

        foreach ($roles as $role) {
            if ($role) {
                $users = array_merge($users, get_role_users(roleid: $role->id, context: $context, fields: $userfields));
            }
        }

        foreach ($users as $user) {
            if (!in_array($user->id, $alreadylisted)) {
                $instructorurl = new moodle_url('/user/view.php', ['id' => $user->id, 'course' => $courseid]);
                $instructor = new \stdClass();
                $instructor->fullname = fullname($user);
                $instructor->picturesrc = "$CFG->wwwroot/user/pix.php/$user->id/f2.jpg";
                $instructor->instructorurl = $instructorurl->out();
                $instructors[] = $instructor;
                $alreadylisted[] = $user->id;
            }
        }

        return $instructors;
    }
}