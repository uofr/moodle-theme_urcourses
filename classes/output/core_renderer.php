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
}