<?php

namespace Xfrocks\Api\Controller;

use Xfrocks\Api\Data\Modules;
use Xfrocks\Api\OAuth2\Server;

class Index extends AbstractController
{
    /**
     * @return \Xfrocks\Api\Mvc\Reply\Api
     */
    public function actionGetIndex()
    {
        /** @var Modules $modules */
        $modules = $this->data('Xfrocks\Api:Modules');

        $systemInfo = [];
        $token = $this->session()->getToken();
        if ($token === null) {
            $systemInfo += [
                'oauth/authorize' => $this->app->router('public')->buildLink('account/authorize'),
                'oauth/token' => $this->buildApiLink('oauth/token')
            ];
        } elseif ($token->hasScope(Server::SCOPE_POST)) {
            $systemInfo += [
                'api_revision' => 2016062001,
                'api_modules' => $modules->getVersions()
            ];
            ksort($systemInfo['api_modules']);
        }

        $data = $modules->getDataForApiIndex($this);
        ksort($data['links']);
        ksort($data['post']);
        $data['system_info'] = $systemInfo;

        return $this->api($data);
    }

    protected function getDefaultApiScopeForAction($action)
    {
        return null;
    }
}
