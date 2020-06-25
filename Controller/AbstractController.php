<?php

namespace Xfrocks\Api\Controller;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\Mvc\ParameterBag;
use XF\Mvc\Reply;
use XF\Mvc\Reply\AbstractReply;
use XF\Mvc\Reply\Redirect;
use Xfrocks\Api\Data\Params;
use Xfrocks\Api\Listener;
use Xfrocks\Api\OAuth2\Server;
use Xfrocks\Api\Repository\Log;
use Xfrocks\Api\Transform\LazyTransformer;
use Xfrocks\Api\Transform\TransformContext;

class AbstractController extends \XF\Pub\Controller\AbstractController
{
    /**
     * @var Params|null
     */
    protected $apiParams = null;

    /**
     * @param ParameterBag $params
     * @return \Xfrocks\Api\Mvc\Reply\Api
     */
    public function actionOptionsGeneric(ParameterBag $params)
    {
        $data = [
            'action' => $params->get('action'),
            'class' => get_class($this),
        ];

        return $this->api($data);
    }

    /**
     * @param array $data
     * @return \Xfrocks\Api\Mvc\Reply\Api
     */
    public function api(array $data)
    {
        return new \Xfrocks\Api\Mvc\Reply\Api($data);
    }

    /**
     * @param string|null $scope
     * @return void
     * @throws \XF\Mvc\Reply\Exception
     */
    public function assertApiScope($scope)
    {
        if ($scope === null || strlen($scope) === 0) {
            return;
        }

        $session = $this->session();
        if (!$session->hasScope($scope)) {
            throw $this->errorException(\XF::phrase('do_not_have_permission'), 403);
        }
    }

    /**
     * @param string $linkUrl
     * @return void
     * @throws Reply\Exception
     */
    public function assertCanonicalUrl($linkUrl)
    {
        $responseType = $this->responseType;
        $this->responseType = 'html';
        $exception = null;

        try {
            parent::assertCanonicalUrl($linkUrl);
        } catch (Reply\Exception $exceptionReply) {
            $reply = $exceptionReply->getReply();
            if ($reply instanceof Redirect) {
                /** @var Redirect $redirect */
                $redirect = $reply;
                $url = $redirect->getUrl();
                if (preg_match('#^https?://.+(https?://.+)$#', $url, $matches) === 1) {
                    // because we are unable to modify XF\Http\Request::getBaseUrl,
                    // parent::assertCanonicalUrl will prepend the full base path incorrectly.
                    // And because we don't want to parse the request params ourselves
                    // we will take care of the extraneous prefix here
                    $alteredUrl = $matches[1];

                    if ($alteredUrl === $this->request->getRequestUri()) {
                        // skip redirecting, if it happens to be the current request URI
                        $exceptionReply = null;
                    } else {
                        $redirect->setUrl($alteredUrl);
                    }
                }
            }

            $exception = $exceptionReply;
        } catch (\Exception $e) {
            $exception = $e;
        }

        $this->responseType = $responseType;
        if ($exception !== null) {
            throw $exception;
        }
    }

    /**
     * @return void
     * @throws Reply\Exception
     */
    protected function assertValidToken()
    {
        if ($this->session()->getToken() === null) {
            throw $this->exception($this->noPermission());
        }
    }

    /**
     * @param string $link
     * @param mixed $data
     * @param array $parameters
     * @return string
     */
    public function buildApiLink($link, $data = null, array $parameters = [])
    {
        return $this->app->router(Listener::$routerType)->buildLink($link, $data, $parameters);
    }

    /**
     * @param mixed $action
     * @param ParameterBag $params
     * @return void
     */
    public function checkCsrfIfNeeded($action, ParameterBag $params)
    {
        // no op
    }

    public function filter($key, $type = null, $default = null)
    {
        throw new \InvalidArgumentException('AbstractController::params() must be used to parse params.');
    }

    /**
     * @param string $type
     * @param array|int $whereId
     * @param string|null $phraseKey
     * @return LazyTransformer
     */
    public function findAndTransformLazily($type, $whereId, $phraseKey = null)
    {
        $finder = $this->finder($type);
        $finder->whereId($whereId);

        $sortByList = null;
        $isSingle = true;
        if (is_array($whereId)) {
            $primaryKey = $finder->getStructure()->primaryKey;
            if (is_array($primaryKey) && count($primaryKey) === 1) {
                $primaryKey = reset($primaryKey);
            }
            if (!is_array($primaryKey)) {
                $isSingle = false;
                $sortByList = $whereId;
            } else {
                // TODO: implement this
                throw new \RuntimeException('Compound primary key is not supported');
            }
        }

        $lazyTransformer = new LazyTransformer($this);
        $lazyTransformer->setFinder($finder);

        if ($sortByList !== null) {
            $lazyTransformer->addCallbackFinderPostFetch(function ($entities) use ($sortByList) {
                /** @var \XF\Mvc\Entity\ArrayCollection $arrayCollection */
                $arrayCollection = $entities;
                $entities = $arrayCollection->sortByList($sortByList);

                return $entities;
            });
        }

        $lazyTransformer->addCallbackPostTransform(function ($data) use ($isSingle, $phraseKey) {
            if (!$isSingle) {
                return $data;
            }

            if (count($data) === 1) {
                return $data[0];
            }

            if ($phraseKey === null) {
                $phraseKey = 'requested_page_not_found';
            }

            throw $this->exception($this->notFound(\XF::phrase($phraseKey)));
        });

        return $lazyTransformer;
    }

    /**
     * @return Params
     */
    public function params()
    {
        if ($this->apiParams === null) {
            $this->apiParams = new Params($this);
        }

        return $this->apiParams;
    }

    /**
     * @param mixed $action
     * @param ParameterBag $params
     * @return void
     * @throws Reply\Exception
     */
    public function preDispatch($action, ParameterBag $params)
    {
        parent::preDispatch($action, $params);

        $this->apiParams = null;

        $addOnId = 'Xfrocks/Api';
        $addOnCache = $this->app->container('addon.cache');
        if (!isset($addOnCache[$addOnId])) {
            throw $this->errorException('The API is currently disabled.', 500);
        }
        if (\XF::$debugMode) {
            $addOn = $this->app->addOnManager()->getById($addOnId);
            if ($addOn->isJsonVersionNewer()) {
                throw $this->errorException('Please update the API add-on.', 500);
            }
        }

        $scope = $this->getDefaultApiScopeForAction($action);
        $this->assertApiScope($scope);
    }

    /**
     * @return \Xfrocks\Api\XF\ApiOnly\Session\Session
     */
    public function session()
    {
        /** @var \Xfrocks\Api\XF\ApiOnly\Session\Session $session */
        $session = parent::session();
        return $session;
    }

    /**
     * @param array $data
     * @param string $key
     * @param Entity $entity
     * @return LazyTransformer
     */
    public function transformEntityIfNeeded(array &$data, $key, $entity)
    {
        $lazyTransformer = $this->transformEntityLazily($entity);
        $lazyTransformer->addCallbackPreTransform(function ($context) use ($key) {
            /** @var TransformContext $context */
            if ($context->selectorShouldExcludeField($key)) {
                return null;
            }

            return $context->getSubContext($key, null, null);
        });
        $data[$key] = $lazyTransformer;

        return $lazyTransformer;
    }

    /**
     * @param Entity $entity
     * @return LazyTransformer
     */
    public function transformEntityLazily($entity)
    {
        $lazyTransformer = new LazyTransformer($this);
        $lazyTransformer->setEntity($entity);
        return $lazyTransformer;
    }

    /**
     * @param Finder $finder
     * @return LazyTransformer
     */
    public function transformFinderLazily($finder)
    {
        $lazyTransformer = new LazyTransformer($this);
        $lazyTransformer->setFinder($finder);
        return $lazyTransformer;
    }

    /**
     * @param mixed $viewClass
     * @param mixed $templateName
     * @param array $params
     * @return Reply\View
     */
    public function view($viewClass = '', $templateName = '', array $params = [])
    {
        if ($viewClass !== '') {
            $viewClass = \XF::stringToClass($viewClass, '%s\%s\View\%s', 'Pub');
        }

        return parent::view($viewClass, $templateName, $params);
    }

    /**
     * @param mixed $action
     * @param ParameterBag $params
     * @param AbstractReply $reply
     * @param mixed $viewState
     * @return false
     */
    protected function canUpdateSessionActivity($action, ParameterBag $params, AbstractReply &$reply, &$viewState)
    {
        return false;
    }

    /**
     * @param mixed $action
     * @param ParameterBag $params
     * @param AbstractReply $reply
     * @return void
     */
    public function postDispatch($action, ParameterBag $params, Reply\AbstractReply &$reply)
    {
        $this->logRequest($reply);

        parent::postDispatch($action, $params, $reply);
    }

    /**
     * @param AbstractReply $reply
     * @return void
     */
    protected function logRequest(AbstractReply $reply)
    {
        $requestMethod = $this->request()->getServer('REQUEST_METHOD');
        $requestUri = $this->request()->getRequestUri();

        $responseOutput = $this->getControllerResponseOutput($reply, $responseCode);
        if ($responseOutput === false) {
            return;
        }

        $requestData = $this->request()->getInputForLogs();

        /** @var Log $logRepo */
        $logRepo = $this->repository('Xfrocks\Api:Log');
        $logRepo->logRequest($requestMethod, $requestUri, $requestData, $responseCode, $responseOutput);
    }

    /**
     * @param AbstractReply|Reply\Exception $reply
     * @param int $responseCode
     * @return array|false
     */
    protected function getControllerResponseOutput($reply, &$responseCode)
    {
        if ($reply instanceof AbstractReply) {
            $responseCode = $reply->getResponseCode();
        }

        if ($reply instanceof Redirect) {
            $responseCode = 301;
            $responseOutput = [
                'redirectType' => $reply->getType(),
                'redirectMessage' => $reply->getMessage(),
                'redirectUri' => $reply->getUrl()
            ];
        } elseif ($reply instanceof Reply\View) {
            $responseOutput = $reply->getParams();
        } elseif ($reply instanceof Reply\Error) {
            $responseOutput = ['errors' => $reply->getErrors()];
        } elseif ($reply instanceof Reply\Exception) {
            $responseOutput = $this->getControllerResponseOutput($reply->getReply(), $responseCode);
        } elseif ($reply instanceof Reply\Message) {
            $responseOutput = ['message' => $reply->getMessage()];
        } else {
            return false;
        }

        return $responseOutput;
    }

    /**
     * @param string $action
     * @return string|null
     */
    protected function getDefaultApiScopeForAction($action)
    {
        if (strpos($action, 'Post') === 0) {
            return Server::SCOPE_POST;
        } elseif (strpos($action, 'Put') === 0) {
            // TODO: separate scope?
            return Server::SCOPE_POST;
        } elseif (strpos($action, 'Delete') === 0) {
            // TODO: separate scope?
            return Server::SCOPE_POST;
        } elseif ($this->options()->bdApi_restrictAccess) {
            return Server::SCOPE_READ;
        }

        return null;
    }
}
