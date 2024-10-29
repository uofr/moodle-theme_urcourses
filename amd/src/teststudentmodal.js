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
 * External functions repository for theme_urcourses_default.
 *
 * @module  theme_urcourses
 * @author  2024 John Lane <john.lane@uregina.ca>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import * as Repository from 'theme_urcourses/repository';
import {get_string as getString} from 'core/str';
import Templates from 'core/templates';
import Modal from 'core/modal';
import ModalEvents from 'core/modal_events';
import ModalSaveCancel from 'core/modal_save_cancel';
import Notification from 'core/notification';

const SELECTORS = {
    CREATE_TEST_STUDENT: '#user-action-menu a[data-action="createteststudent"]',
    RESET_TEST_STUDENT: '#user-action-menu a[data-action="resetteststudent"]'
};

const TEMPLATES = {
    CREATE_TEST_STUDENT_MODAL: 'theme_urcourses/create_test_student_modal',
    RESET_TEST_STUDENT_MODAL: 'theme_urcourses/reset_test_student_modal'
};

const init = (root) => {
    registerEventListeners($(root));
};

const registerEventListeners = (root) => {
    root.on('click', SELECTORS.CREATE_TEST_STUDENT, async (e) => {
        e.preventDefault();
        try {
            const testStudent = await Repository.testAccountInfo();
            const modal = await createStudentModal(testStudent);
            modal.getRoot().on(ModalEvents.save, async () => {
                try {
                    const confirmModal = await confirmCreateStudentModal(testStudent);
                    confirmModal.getRoot().on(ModalEvents.save, async () => {
                        try {
                            const result = await Repository.createTestStudent(testStudent);
                            if (result) {
                                await showStatusPopup(
                                    getString('createsuccess_title', 'theme_urcourses'),
                                    getString('createsuccess_body', 'theme_urcourses')
                                );
                            } else {
                                await showStatusPopup(
                                    getString('createfail_title', 'theme_urcourses'),
                                    getString('createfail_body', 'theme_urcourses')
                                );
                            }
                        } catch (error) {
                            Notification.exception(error);
                        }
                    });
                } catch (error) {
                    Notification.exception(error);
                }
            });
        } catch (error) {
            Notification.exception(error);
        }
    });

    root.on('click', SELECTORS.RESET_TEST_STUDENT, async (e) => {
        e.preventDefault();
        try {
            const testStudent = await Repository.testAccountInfo();
            const modal = await resetStudentModal(testStudent);
            modal.getRoot().on(ModalEvents.save, async () => {
                try {
                    const confirmModal = await confirmResetStudentModal(testStudent);
                    confirmModal.getRoot().on(ModalEvents.save, async () => {
                        try {
                            const result = await Repository.resetTestStudent(testStudent);
                            if (result) {
                                await showStatusPopup(
                                    getString('resetsuccess_title', 'theme_urcourses'),
                                    getString('resetsuccess_body', 'theme_urcourses')
                                );
                            } else {
                                await showStatusPopup(
                                    getString('resetfail_title', 'theme_urcourses'),
                                    getString('resetfail_body', 'theme_urcourses')
                                );
                            }
                        } catch (error) {
                            Notification.exception(error);
                        }
                    });
                } catch (error) {
                    Notification.exception(error);
                }
            });
        } catch (error) {
            Notification.exception(error);
        }
    });
};

const createStudentModal = (testStudent) => {
    return ModalSaveCancel.create({
        title: getString('createmodal_title', 'theme_urcourses'),
        body: Templates.render(TEMPLATES.CREATE_TEST_STUDENT_MODAL, testStudent),
        removeOnClose: true,
        buttons: {
            save: getString('createmodal_button', 'theme_urcourses')
        },
        show: true
    });
};

const confirmCreateStudentModal = (testStudent) => {
    return ModalSaveCancel.create({
        title: getString('createmodal_title', 'theme_urcourses'),
        body: getString('createmodal_confirm', 'theme_urcourses', testStudent.username),
        removeOnClose: true,
        buttons: {
            save: getString('createmodal_button', 'theme_urcourses')
        },
        show: true
    });
};

const resetStudentModal = (testStudent) => {
    return ModalSaveCancel.create({
        title: getString('resetmodal_title', 'theme_urcourses'),
        body: Templates.render(TEMPLATES.RESET_TEST_STUDENT_MODAL, testStudent),
        removeOnClose: true,
        buttons: {
            save: getString('resetmodal_button', 'theme_urcourses')
        },
        show: true
    });
};

const confirmResetStudentModal = (testStudent) => {
    return ModalSaveCancel.create({
        title: getString('resetmodal_title', 'theme_urcourses'),
        body: getString('resetmodal_confirm', 'theme_urcourses', testStudent.email),
        removeOnClose: true,
        buttons: {
            save: getString('resetmodal_button', 'theme_urcourses')
        },
        show: true
    });
};

const showStatusPopup = (title, body) => {
    return Modal.create({
        title: title,
        body: body,
        removeOnClose: true,
        show: true
    });
};

export default {
    init: init,
};