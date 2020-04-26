<?php

class bdApi_XenForo_ControllerPublic_Error extends XFCP_bdApi_XenForo_ControllerPublic_Error
{
    public function actionAuthorizeGuest()
    {
        $requestPaths = XenForo_Application::get('requestPaths');
        $social = $this->_input->filterSingle('social', XenForo_Input::STRING);
        switch ($social) {
            case 'facebook':
                $facebookLink = XenForo_Link::buildPublicLink('full:register/facebook', null, array(
                    'reg' => 1,
                    'redirect' => $requestPaths['fullUri'],
                ));
                return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $facebookLink);
            case 'twitter':
                $twitterLink = XenForo_Link::buildPublicLink('full:register/twitter', null, array(
                    'reg' => 1,
                    'redirect' => $requestPaths['fullUri'],
                ));
                return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, $twitterLink);
        }

        /* @var $oauth2Model bdApi_Model_OAuth2 */
        $oauth2Model = $this->getModelFromCache('bdApi_Model_OAuth2');

        $clientModel = $oauth2Model->getClientModel();

        $clientId = $this->_input->filterSingle('client_id', XenForo_Input::STRING);
        $client = $clientModel->getClientById($clientId);
        if (empty($client)) {
            return $this->responseError(new XenForo_Phrase(
                'bdapi_authorize_error_client_x_not_found',
                array('client' => $clientId)
            ), 404);
        }

        $authorizeParams = $this->_input->filter($oauth2Model->getAuthorizeParamsInputFilter());
        $redirectParams = $authorizeParams;
        $redirectParams['timestamp'] = time() + bdApi_Option::get('authorizeBypassSecs');
        $redirectParams['hash'] = bdApi_Crypt::encryptTypeOne(
            serialize($authorizeParams),
            $redirectParams['timestamp']
        );
        $redirect = XenForo_Link::buildPublicLink('account/authorize', null, $redirectParams);

        $viewParams = array(
            'client' => $client,
            'authorizeParams' => $authorizeParams,

            'social' => $social,
            'redirect' => $redirect,
        );

        $view = $this->responseView('bdApi_ViewPublic_Account_Authorize', 'bdapi_error_authorize_guest', $viewParams);
        $view->responseCode = 403;

        return $view;
    }
}
