<?php
use Respect\Validation\Validator as DataValidator;
DataValidator::with('CustomValidations', true);

/**
 * @api {post} /article/edit Edit article
 * @apiVersion 4.10.0
 *
 * @apiName Edit a article
 *
 * @apiGroup Article
 *
 * @apiDescription This path edits an article.
 *
 * @apiPermission staff2
 *
 * @apiParam {Number} articleId Id of the article.
 * @apiParam {Number} topicId Id of the topic of the article. Optional.
 * @apiParam {String} content The new content of the article. Optional.
 * @apiParam {String} title The new title of the article. Optional.
 * @apiParam {Number} position The new position of the article. Optional.
 * @apiParam {Number} images The number of images in the content
 * @apiParam image_i The image file of index `i` (mutiple params accepted)
 *
 * @apiUse NO_PERMISSION
 * @apiUse INVALID_TOPIC
 * @apiUse INVALID_FILE
 * @apiUse INVALID_TITLE
 * @apiUse CONTENT_ALREADY_USED
 * @apiUse TITLE_ALREADY_USED
 * 
 * @apiSuccess {Object} data Empty object
 *
 */

class EditArticleController extends Controller {
    const PATH = '/edit';
    const METHOD = 'POST';

    public function validations() {
        return [
            'permission' => 'staff_2',
            'requestData' => [
                'articleId' => [
                    'validation' => DataValidator::dataStoreId('article'),
                    'error' => ERRORS::INVALID_TOPIC
                ],
                'title' => [
                    'validation' => DataValidator::OneOf(
                        DataValidator::notBlank()->length(LengthConfig::MIN_LENGTH_NAME, LengthConfig::MAX_LENGTH_NAME),
                        DataValidator::nullType()    
                    ),      
                    'error' => ERRORS::INVALID_TITLE
                ],
                'content' => [
                    'validation' => DataValidator::oneOf(
                        DataValidator::content(),
                        DataValidator::nullType()
                    ),
                    'error' => ERRORS::INVALID_CONTENT
                ]
            ]
        ];
    }

    public function handler() {
        $topicId = Controller::request('topicId');
        $content = Controller::request('content', true);
        $title = Controller::request('title');
        
        $article = Article::getDataStore(Controller::request('articleId'));
        $createdArticleTookByTitle = Article::getDataStore($title, 'title');
        $createdArticleTookByContent = Article::getDataStore($content, 'content');

        if(!$createdArticleTookByTitle->isNull() && $article->title !== $createdArticleTookByTitle->title){
            throw new RequestException(ERRORS::TITLE_ALREADY_USED);
        }

        if(!$createdArticleTookByContent->isNull() && $article->content !== $createdArticleTookByContent->content){
            throw new RequestException(ERRORS::CONTENT_ALREADY_USED);
        }

        if ($topicId) {
            $newArticleTopic = Topic::getDataStore($topicId);

            if (!$newArticleTopic->isNull()) {
                $article->topic = $newArticleTopic;
            } else {
                throw new RequestException(ERRORS::INVALID_TOPIC);
                return;
            }
        }

        if($content) {
            $fileUploader = FileUploader::getInstance();
            $fileUploader->setPermission(FileManager::PERMISSION_ARTICLE);

            $imagePaths = $this->uploadImages(true);

            $article->content = $this->replaceWithImagePaths($imagePaths, $content);
        }

        if($title) {
            $article->title = $title;
        }

        if(Controller::request('position')) {
            $article->position = Controller::request('position');
        }

        $article->lastEdited = Date::getCurrentDate();

        $article->store();

        Log::createLog('EDIT_ARTICLE', $article->title);

        Response::respondSuccess();
    }
}
