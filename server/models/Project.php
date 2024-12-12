<?php
use RedBeanPHP\Facade as RedBean;

/**
 * @api {OBJECT} Project Project
 * @apiVersion 4.11.0
 * @apiGroup Data Structures
 * @apiParam {Number} id Id of the project.
 * @apiParam {String} name Name of the project.
 */

class Project extends DataStore {
    const TABLE = 'project';

    public static function getProps() {
        return [
            'name',
            'sharedTicketList',
        ];
    }

    public function getDefaultProps() {
        return [
        ];
    }

    public static function getAllProjectNames() {
        $projectsList = RedBean::findAll(Project::TABLE);
        $projectsNameList = [];

        foreach($projectsList as $project) {
            $projectsNameList[] = [
                'id' => $project->id,
                'name' => $project->name
            ];
        }

        return $projectsNameList;
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
