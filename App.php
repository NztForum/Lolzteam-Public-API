<?php

namespace Xfrocks\Api;

use Xfrocks\Api\OAuth2\Server;

class App extends \XF\Pub\App
{
    /**
     * @param mixed $action
     * @param array $params
     * @param mixed $responseType
     * @return \XF\Mvc\RouteMatch
     */
    public function getErrorRoute($action, array $params = [], $responseType = 'html')
    {
        return new \XF\Mvc\RouteMatch('Xfrocks:Error', $action, $params, $responseType);
    }

    /**
     * @param \XF\Mvc\Reply\AbstractReply|null $reply
     * @return array
     */
    public function getGlobalTemplateData(\XF\Mvc\Reply\AbstractReply $reply = null)
    {
        $data = parent::getGlobalTemplateData($reply);

        $data['isApi'] = true;
        $data['apiRouterType'] = Listener::$routerType;

        return $data;
    }

    /**
     * @return void
     */
    public function initializeExtra()
    {
        parent::initializeExtra();

        $container = $this->container;

        $container['app.classType'] = 'Api';

        $container->extend('extension', function (\XF\Extension $extension) {
            $extension->addListener('dispatcher_match', ['Xfrocks\Api\Listener', 'apiOnlyDispatcherMatch']);

            return $extension;
        });

        $container->extend('extension.classExtensions', function (array $classExtensions) {
            $xfClasses = [
                'ControllerPlugin\Error',
                'Entity\User',
                'Image\Gd',
                'Image\Imagick',
                'Mvc\Dispatcher',
                'Mvc\Renderer\Json',
                'Session\Session',
                'Template\Templater',
            ];

            foreach ($xfClasses as $xfClass) {
                $extendBase = 'XF\\' . $xfClass;
                if (!isset($classExtensions[$extendBase])) {
                    $classExtensions[$extendBase] = [];
                }

                $extendClass = 'Xfrocks\Api\\' . 'XF\\ApiOnly\\' . $xfClass;
                $classExtensions[$extendBase][] = $extendClass;
            }

            return $classExtensions;
        });

        $container['request'] = function (\XF\Container $c) {
            /** @var Server $apiServer */
            $apiServer = $this->container('api.server');
            /** @var \Symfony\Component\HttpFoundation\Request $apiRequest */
            $apiRequest = $apiServer->container('request');

            $request = new \XF\Http\Request(
                $c['inputFilterer'],
                $apiRequest->request->all() + $apiRequest->query->all(),
                $_FILES,
                []
            );

            return $request;
        };

        $container->extend('request.paths', function (array $paths) {
            // move base directory up one level for URL building
            // TODO: make the change directly at XF\Http\Request::getBaseUrl
            $apiDirNameRegEx = '#' . preg_quote(Listener::$apiDirName, '#') . '/$#';
            $paths['full'] = preg_replace($apiDirNameRegEx, '', $paths['full']);
            $paths['base'] = preg_replace($apiDirNameRegEx, '', $paths['base']);

            return $paths;
        });

        $container->extend('request.pather', function ($pather) {
            return function ($url, $modifier = 'full') use ($pather) {
                // always use canonical/full URL in api context
                if ($modifier !== 'canonical') {
                    $modifier = 'full';
                }

                return $pather($url, $modifier);
            };
        });
    }

    /**
     * @param \XF\Session\Session $session
     * @return void
     */
    protected function onSessionCreation(\XF\Session\Session $session)
    {
        /** @var Server $apiServer */
        $apiServer = $this->container('api.server');
        $accessToken = $apiServer->parseRequest();

        /** @var \Xfrocks\Api\XF\ApiOnly\Session\Session $apiSession */
        $apiSession = $session;
        $apiSession->setToken($accessToken ? $accessToken->getXfToken() : null);
    }

    /**
     * @return void
     */
    protected function updateModeratorCaches()
    {
        // no op
    }

    /**
     * @return void
     */
    protected function updateUserCaches()
    {
        // no op
    }
}
