<?php
use Respect\Validation\Validator as DataValidator;

DataValidator::with('CustomValidations', true);
/**
 * @api {post} /system/add-project Add project
 * @apiVersion 4.11.0
 *
 * @apiName Add project
 *
 * @apiGroup System
 *
 * @apiDescription This path create a new project.
 *
 * @apiPermission staff3
 *
 * @apiParam {String} name Name of the new project.
 *
 * @apiUse NO_PERMISSION
 *
 * @apiSuccess {Object} data Empty object
 *
 */

class AddProjectController extends Controller {
    const PATH = '/add-project';
    const METHOD = 'POST';

    public function validations() {
        return [
            'permission' => 'staff_3',
            'requestData' => [
                'name' => [
                    'validation' => DataValidator::AllOf(
                        DataValidator::notBlank()->length(LengthConfig::MIN_LENGTH_NAME, LengthConfig::MAX_LENGTH_NAME),
                        DataValidator::ValidProjectName()    
                    ),
                    'error' => ERRORS::INVALID_NAME
                ]
            ]
        ];
    }

    public function handler() {
        $name = Controller::request('name', true);

        $projectInstance = new Project();

        $projectInstance->setProperties([
            'name' => $name ,
        ]);
        $projectInstance->store();

        Log::createLog('ADD_PROJECT', $name);

        Response::respondSuccess();

    }
}
