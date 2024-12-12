<?php
use Respect\Validation\Validator as DataValidator;


/**
 * @api {post} /ticket/add-tag Add tag
 * @apiVersion 4.11.0
 *
 * @apiName Add tag
 *
 * @apiGroup Ticket
 *
 * @apiDescription This path attaches a new tag to a ticket.
 *
 * @apiPermission staff1
 *
 * @apiParam {String} userId The number of the ticket which the tag is going to be attached.
 * @apiParam {String} projectId The id of the tag to attach.
 *
 * @apiUse NO_PERMISSION
 * @apiUse INVALID_TICKET
 * @apiUse INVALID_TAG
 * @apiUse TAG_EXISTS
 *
 * @apiSuccess {Object} data Empty object
 *
 */

class AddTagController extends Controller {
    const PATH = '/add-project';
    const METHOD = 'POST';

    public function validations() {
        return [
            'permission' => 'staff_1',
            'requestData' => [
                'userId' => [
                    'validation' => DataValidator::dataStoreId('user'),
                    'error' => ERRORS::INVALID_USER
                ],
                'projectId' => [
                    'validation' => DataValidator::dataStoreId('project'),
                    'error' => ERRORS::INVALID_USER
                ]
            ]
        ];
    }

    public function handler() {
        
        $user = User::getDataStore(Controller::request('userId'));
        
        $project = Project::getDataStore(Controller::request('projectId'));

        Response::respondSuccess();
    }
}
