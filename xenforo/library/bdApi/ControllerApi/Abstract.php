<?php

abstract class bdApi_ControllerApi_Abstract extends XenForo_ControllerPublic_Abstract
{
    const FIELDS_FILTER_NONE = 0;
    const FIELDS_FILTER_INCLUDE = 0x01;
    const FIELDS_FILTER_EXCLUDE = 0x02;

    const SPAM_RESULT_ALLOWED = 'allowed';
    const SPAM_RESULT_MODERATED = 'moderated';
    const SPAM_RESULT_DENIED = 'denied';

    protected $_fieldsFilterType = false;
    protected $_fieldsFilterInclude = array();
    protected $_fieldsFilterExclude = array();
    protected $_fieldsFilterExcludeHasWildcards = false;
    protected $_fieldsFilterDefaults = array();

    private $_startTime = 0.0;

    public function actionOptions()
    {
        $cors = $this->_request->getHeader('Access-Control-Request-Method');
        if (!empty($cors)) {
            return $this->responseData('bdApi_ViewApi_Helper_Options');
        }

        $action = $this->_input->filterSingle('action', XenForo_Input::STRING);
        $action = str_replace(array('-', '/'), ' ', utf8_strtolower($action));
        $action = str_replace(' ', '', utf8_ucwords($action));

        $methods = array();

        /* @var $fc XenForo_FrontController */
        $fc = XenForo_Application::get('_bdApi_fc');

        XenForo_Application::set('_bdApi_disableBatch', true);

        foreach (array(
                     'Get',
                     'Post',
                     'Put'
                 ) as $method) {
            $controllerMethod = sprintf('action%s%s', $method, $action);

            if (is_callable(array($this, $controllerMethod))) {
                $method = utf8_strtoupper($method);

                bdApi_Input::bdApi_resetFilters();

                $routeMatch = new XenForo_RouteMatch(
                    $this->_routeMatch->getControllerName(),
                    sprintf('%s-%s', $method, $action)
                );

                $response = null;
                $responseIsNoPermission = false;
                try {
                    $response = $fc->dispatch($routeMatch);
                } catch (XenForo_ControllerResponse_Exception $responseException) {
                    $response = $responseException->getControllerResponse();
                } catch (Exception $e) {
                    // ignore
                }

                /** @noinspection PhpUndefinedMethodInspection */
                if ($response !== null
                    && $response instanceof XenForo_ControllerResponse_Error
                    && is_object($response->errorText)
                    && $response->errorText instanceof XenForo_Phrase
                    && $response->errorText->getPhraseName() === 'do_not_have_permission'
                ) {
                    $responseIsNoPermission = true;
                }

                $params = bdApi_Input::bdApi_getFilters();
                foreach (array_keys($params) as $paramKey) {
                    if (in_array($paramKey, array(
                        'fields_filter_prefix',
                        'fields_include',
                        'fields_exclude',
                        'limit',
                        'locale',
                        'page',
                    ), true)) {
                        // system wide params, ignore
                        unset($params[$paramKey]);
                        continue;
                    }

                    if (!isset($_GET[$paramKey])
                        && $this->_input->inRequest($paramKey)
                    ) {
                        // apparently this param is set by the route class
                        unset($params[$paramKey]);
                        continue;
                    }
                }

                if (count($params) === 0 && $responseIsNoPermission) {
                    continue;
                }

                ksort($params);
                $methods[$method]['parameters'] = array_values($params);
            }
        }

        $allowedMethods = array_keys($methods);
        $allowedMethods[] = 'OPTIONS';
        $this->_response->setHeader('Allow', implode(',', $allowedMethods));

        return $this->responseData('bdApi_ViewApi_Helper_Options', $methods);
    }

    public function addExtraDataForResponse($key, $value)
    {
        $extraData = $this->_request->getParam(__CLASS__);
        if (!is_array($extraData)) {
            $extraData = array();
        }

        $extraData[$key] = $value;
        $this->_request->setParam(__CLASS__, $extraData);
    }

    /**
     * Builds are response with specified data. Basically it's the same
     * XenForo_ControllerPublic_Abstract::responseView() but with the
     * template name removed so only view name and data array is available.
     * Also, the data has some rules enforced to make a good response.
     *
     * @param string $viewName
     * @param array $data
     *
     * @return XenForo_ControllerResponse_View
     */
    public function responseData($viewName, array $data = array())
    {
        $extraData = $this->_request->getParam(__CLASS__);
        if (is_array($extraData)) {
            $data += $extraData;
        }

        return parent::responseView($viewName, 'DEFAULT', $data);
    }

    /**
     * Filters param `limit` and `page` from request input.
     *
     * @param array $pageNavParams
     * @param string $limitVarName
     * @param string $pageVarName
     * @return array
     */
    public function filterLimitAndPage(&$pageNavParams = array(), $limitVarName = 'limit', $pageVarName = 'page')
    {
        $limitDefault = bdApi_Option::get('paramLimitDefault');
        $limit = $limitDefault;
        $limitInput = $this->_input->filterSingle($limitVarName, XenForo_Input::STRING);
        if (strlen($limitInput) > 0) {
            $limit = intval($limitInput);

            $limitMax = bdApi_Option::get('paramLimitMax');
            if ($limitMax > 0) {
                $limit = min($limitMax, $limit);
            }
        }
        $limit = max(1, $limit);
        if ($limit - $limitDefault !== 0) {
            $pageNavParams[$limitVarName] = $limit;
        }

        $page = $this->_input->filterSingle($pageVarName, XenForo_Input::UINT);
        $pageMax = bdApi_Option::get('paramPageMax');
        if ($pageMax > 0) {
            $page = min(bdApi_Option::get('paramPageMax'), $page);
        }
        $page = max(1, $page);

        return array(intval($limit), intval($page));
    }

    /**
     * Filters data for many resources.
     * This method name had been prefixed with "_" before it was updated to public visibility.
     * The name is kept for backward compatibility.
     *
     * @param array $resourcesData
     * @param array $prefixes
     * @return array
     */
    public function _filterDataMany(array $resourcesData, array $prefixes = array())
    {
        $filtered = array();

        foreach ($resourcesData as $key => $resourceData) {
            $filtered[$key] = $this->_filterDataSingle($resourceData, $prefixes);
        }

        return $filtered;
    }

    /**
     * Filters data for one resource.
     * This method name had been prefixed with "_" before it was updated to public visibility.
     * The name is kept for backward compatibility.
     *
     * @param array $resourceData
     * @param array $prefixes
     * @return array
     */
    public function _filterDataSingle(array $resourceData, array $prefixes = array())
    {
        $this->_prepareFieldsFilter();

        if ($this->_fieldsFilterType === self::FIELDS_FILTER_NONE) {
            return $resourceData;
        }

        $filtered = array();
        foreach (array_keys($resourceData) as $field) {
            if (substr($field, 0, 1) === '_') {
                continue;
            }

            $hasChild = is_array($resourceData[$field]);

            if (!is_int($field) && $this->_isFieldExcluded($field, $prefixes, $hasChild)) {
                continue;
            }

            if ($hasChild && count($resourceData[$field]) > 0) {
                $_prefixes = $prefixes;
                if (!is_int($field)) {
                    $_prefixes[] = $field;
                }
                $_filtered = $this->_filterDataSingle($resourceData[$field], $_prefixes);
                if (count($_filtered) > 0) {
                    $filtered[$field] = $_filtered;
                }
            } else {
                $filtered[$field] = $resourceData[$field];
            }
        }

        return $filtered;
    }

    /**
     * Checks if a field is specifically requested to be included.
     * This method name had been prefixed with "_" before it was updated to public visibility.
     * The name is kept for backward compatibility.
     *
     * @param string $field
     * @param array $prefixes
     * @param bool $hasChild
     * @return bool
     */
    public function _isFieldIncluded($field, array $prefixes = array(), $hasChild = true)
    {
        $this->_prepareFieldsFilter();

        if (!($this->_fieldsFilterType & self::FIELDS_FILTER_INCLUDE)) {
            return false;
        }

        $this->_prepareFieldAndPrefixes($field, $prefixes);

        $pattern = $field;
        if (count($prefixes)) {
            $pattern = sprintf('%s.%s', implode('.', $prefixes), $field);
        }
        $patternAndDot = null;
        $patternAndDotLength = 0;
        if ($hasChild) {
            $patternAndDot = $pattern . '.';
            $patternAndDotLength = strlen($patternAndDot);
        }

        foreach ($this->_fieldsFilterInclude as $_field) {
            if ($_field === $pattern
                || ($patternAndDotLength > 0
                    && substr($_field, 0, $patternAndDotLength) === $patternAndDot
                )
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks if a field is specifically requested to be excluded.
     * This method name had been prefixed with "_" before it was updated to public visibility.
     * The name is kept for backward compatibility.
     *
     * @param string $field
     * @param array $prefixes
     * @param bool $hasChild
     * @return bool
     */
    public function _isFieldExcluded($field, array $prefixes = array(), $hasChild = true)
    {
        $this->_prepareFieldsFilter();
        $this->_prepareFieldAndPrefixes($field, $prefixes);

        if ($this->_fieldsFilterType & self::FIELDS_FILTER_INCLUDE) {
            if ($this->_isFieldIncluded($field, $prefixes, $hasChild)) {
                return false;
            }

            $includeDefault = false;
            $_prefixes = $prefixes;
            while (true) {
                $_prefixesStr = count($_prefixes) === 0
                    ? ''
                    : (
                    count($_prefixes) == 1
                        ? $_prefixes[0]
                        : implode('.', $_prefixes)
                    );
                if (isset($this->_fieldsFilterDefaults[$_prefixesStr])) {
                    if ($this->_fieldsFilterDefaults[$_prefixesStr]) {
                        $includeDefault = true;
                    }
                    break;
                }

                if (empty($_prefixes)) {
                    break;
                } else {
                    array_pop($_prefixes);
                }
            }
            if (!$includeDefault) {
                return true;
            }
        }

        if (!($this->_fieldsFilterType & self::FIELDS_FILTER_EXCLUDE)) {
            return false;
        }

        $pattern = $field;
        if (count($prefixes)) {
            $pattern = sprintf('%s.%s', implode('.', $prefixes), $field);
        }
        $wildcardPattern = null;
        if ($this->_fieldsFilterExcludeHasWildcards) {
            $wildcardPattern = sprintf('*.%s', $field);
        }

        foreach ($this->_fieldsFilterExclude as $_field) {
            if ($_field === $pattern) {
                return true;
            }

            if ($wildcardPattern !== null
                && $_field === $wildcardPattern
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $field
     * @param array $prefixes
     */
    protected function _prepareFieldAndPrefixes(&$field, array &$prefixes)
    {
        if (strpos($field, '.') !== false) {
            $fieldParts = explode('.', $field);
            $field = array_pop($fieldParts);
            foreach ($fieldParts as $fieldPart) {
                $prefixes[] = $fieldPart;
            }
        }
    }

    protected function _prepareFieldsFilter()
    {
        if ($this->_fieldsFilterType === false) {
            $this->_fieldsFilterType = self::FIELDS_FILTER_NONE;

            // use values from this request specifically
            $filterPrefix = $this->_input->filterSingle('fields_filter_prefix', XenForo_Input::STRING);
            $filterPrefixLength = strlen($filterPrefix);
            $include = $this->_input->filterSingle('fields_include', XenForo_Input::STRING);
            $exclude = $this->_input->filterSingle('fields_exclude', XenForo_Input::STRING);
            if ($filterPrefixLength > 0 && empty($include) && empty($exclude)) {
                // use values from $_GET if a prefix is specified
                $include = filter_input(INPUT_GET, 'fields_include');
                $exclude = filter_input(INPUT_GET, 'fields_exclude');
            }

            if (!empty($include)) {
                $this->_fieldsFilterType |= self::FIELDS_FILTER_INCLUDE;
                foreach (explode(',', $include) as $field) {
                    $field = trim($field);
                    if ($filterPrefixLength > 0) {
                        if (substr($field, 0, $filterPrefixLength) !== $filterPrefix) {
                            continue;
                        } else {
                            $field = substr($field, $filterPrefixLength);
                        }
                    }

                    $prefixes = explode('.', $field);
                    $_field = array_pop($prefixes);
                    $_prefixes = implode('.', $prefixes);
                    if ($_field === '*') {
                        $this->_fieldsFilterDefaults[$_prefixes] = true;
                        if (!empty($_prefixes)) {
                            $this->_fieldsFilterInclude[] = $_prefixes;
                        }
                    } else {
                        $this->_fieldsFilterInclude[] = $field;
                        $this->_fieldsFilterDefaults[$field] = true;
                        if (!isset($this->_fieldsFilterDefaults[$_prefixes])) {
                            $this->_fieldsFilterDefaults[$_prefixes] = false;
                        }
                    }
                }
            }

            if (!empty($exclude)) {
                $this->_fieldsFilterType |= self::FIELDS_FILTER_EXCLUDE;
                foreach (explode(',', $exclude) as $field) {
                    $field = trim($field);
                    if ($filterPrefixLength > 0) {
                        if (substr($field, 0, $filterPrefixLength) !== $filterPrefix) {
                            continue;
                        } else {
                            $field = substr($field, $filterPrefixLength);
                        }
                    }

                    $this->_fieldsFilterExclude[] = $field;

                    if (strpos($field, '*') !== false) {
                        $this->_fieldsFilterExcludeHasWildcards = true;
                    }
                }
            }
        }
    }

    /**
     * Try to check submitted data for spam.
     * <code>$data</code> should have <code>'content'</code>
     * and <code>'content_type'</code> for optimal operation.
     *
     * @param array $data
     * @return string one of the SPAM_RESULT_* constants
     */
    protected function _spamCheck(array $data)
    {
        if (XenForo_Application::$versionId < 1020000) {
            return self::SPAM_RESULT_ALLOWED;
        }

        /** @var XenForo_Model_SpamPrevention $spamModel */
        $spamModel = $this->getModelFromCache('XenForo_Model_SpamPrevention');
        $spamResult = self::SPAM_RESULT_ALLOWED;

        if ($spamModel->visitorRequiresSpamCheck()) {
            if (isset($data['content'])) {
                switch ($spamModel->checkMessageSpam($data['content'], $data, $this->_request)) {
                    case XenForo_Model_SpamPrevention::RESULT_ALLOWED:
                        $spamResult = self::SPAM_RESULT_ALLOWED;
                        break;
                    case XenForo_Model_SpamPrevention::RESULT_MODERATED:
                        $spamResult = self::SPAM_RESULT_MODERATED;
                        break;
                    case XenForo_Model_SpamPrevention::RESULT_DENIED:
                        $spamResult = self::SPAM_RESULT_DENIED;
                        break;
                }
            }

            switch ($spamResult) {
                case self::SPAM_RESULT_MODERATED:
                case self::SPAM_RESULT_DENIED:
                    if (isset($data['content_type'])) {
                        $contentId = null;
                        if (isset($data['content_id'])) {
                            $contentId = $data['content_id'];
                        }

                        $spamModel->logSpamTrigger($data['content_type'], $contentId);
                    }
                    break;
            }
        }

        return $spamResult;
    }


    /**
     * Gets the required scope for a controller action. By default,
     * all API GET actions will require the read scope, POST actions will require
     * the post scope.
     *
     * Special case: if no OAuth token is specified (the session
     * will be setup as guest), GET actions won't require the read scope anymore.
     * That means guest-permission API requests will have the read scope
     * automatically.
     *
     * @param string $action
     *
     * @return string required scope. One of the SCOPE_* constant in
     * bdApi_Model_OAuth2
     */
    protected function _getScopeForAction($action)
    {
        if (strpos($action, 'Post') === 0) {
            return bdApi_Model_OAuth2::SCOPE_POST;
        } elseif (strpos($action, 'Put') === 0) {
            // TODO: separate scope?
            return bdApi_Model_OAuth2::SCOPE_POST;
        } elseif (strpos($action, 'Delete') === 0) {
            // TODO: separate scope?
            return bdApi_Model_OAuth2::SCOPE_POST;
        } else {
            return bdApi_Model_OAuth2::SCOPE_READ;
        }
    }

    /**
     * Helper to check for the required scope and throw an exception
     * if it could not be found.
     * @param $scope
     * @throws XenForo_ControllerResponse_Exception
     * @throws Zend_Exception
     */
    protected function _assertRequiredScope($scope)
    {
        if (empty($scope)) {
            // no scope is required
            return;
        }

        /* @var $session bdApi_Session */
        $session = XenForo_Application::get('session');

        if (!$session->checkScope($scope)) {
            $oauthTokenText = $session->getOAuthTokenText();

            if (empty($oauthTokenText)) {
                /** @var bdApi_Model_OAuth2 $oauth2Model */
                $oauth2Model = XenForo_Model::create('bdApi_Model_OAuth2');
                $controllerResponse = $oauth2Model->getServer()->getErrorControllerResponse($this);

                if (empty($controllerResponse)) {
                    $controllerResponse = $this->responseError(
                        new XenForo_Phrase('bdapi_authorize_error_invalid_or_expired_access_token'),
                        403
                    );
                }
            }

            if (empty($controllerResponse)) {
                $controllerResponse = $this->responseError(new XenForo_Phrase(
                    'bdapi_authorize_error_scope_x_not_granted',
                    array('scope' => $scope)
                ), 403);
            }

            throw $this->responseException($controllerResponse);
        }
    }

    protected function _assertAdminPermission($permissionId)
    {
        $this->_assertRequiredScope(bdApi_Model_OAuth2::SCOPE_MANAGE_SYSTEM);

        if (!XenForo_Visitor::getInstance()->hasAdminPermission($permissionId)) {
            throw $this->responseException($this->responseNoPermission());
        }
    }

    protected function _assertValidToken()
    {
        $session = bdApi_Data_Helper_Core::safeGetSession();
        if (empty($session)) {
            throw $this->responseException($this->responseNoPermission());
        }

        $clientId = $session->getOAuthClientId();
        if (empty($clientId)) {
            throw $this->responseException($this->responseNoPermission());
        }
    }

    protected function _assertViewingPermissions($action)
    {
        if ($action !== 'Options') {
            parent::_assertViewingPermissions($action);
        }
    }

    protected function _assertBoardActive($action)
    {
        parent::_assertBoardActive($action);

        if (strtoupper($this->_request->getMethod()) !== 'GET'
            && XenForo_Application::isRegistered('_bdCloudServerHelper_readonly')
        ) {
            $response = $this->responseError(new XenForo_Phrase('bdcsh_forum_is_currently_read_only'), 503);
            throw $this->responseException($response);
        }
    }

    public function responseNoPermission()
    {
        return $this->responseReroute('bdApi_ControllerApi_Error', 'no-permission');
    }

    protected function _assertRegistrationRequired()
    {
        if (!XenForo_Visitor::getUserId()) {
            throw $this->responseException($this->responseReroute(
                'bdApi_ControllerApi_Error',
                'registration-required'
            ));
        }
    }

    protected function _preDispatch($action)
    {
        $requiredScope = $this->_getScopeForAction($action);
        $this->_assertRequiredScope($requiredScope);

        parent::_preDispatch($action);
    }

    protected function _setupSession($action)
    {
        if (XenForo_Application::isRegistered('session')) {
            return;
        }

        bdApi_Session::startApiSession($this->_request);
    }


    public function updateSessionActivity($controllerResponse, $controllerName, $action)
    {
        if (!bdApi_Option::get('trackSession')) {
            return;
        }

        if (!$this->_request->isGet()) {
            return;
        }

        $session = bdApi_Data_Helper_Core::safeGetSession();
        if (empty($session)) {
            return;
        }

        $visitorUserId = XenForo_Visitor::getUserId();
        if ($visitorUserId === 0) {
            return;
        }

        if ($controllerResponse instanceof XenForo_ControllerResponse_Reroute) {
            return;
        } elseif ($controllerResponse instanceof XenForo_ControllerResponse_Redirect) {
            return;
        }

        $params = $this->_request->getUserParams();
        if (!empty($params['_isApiJob'])) {
            return;
        }

        $this->_prepareSessionActivityForApi($controllerName, $action, $params);

        /** @var XenForo_Model_User $userModel */
        $userModel = $this->getModelFromCache('XenForo_Model_User');
        $userModel->updateSessionActivity(
            $visitorUserId,
            $this->_request->getClientIp(false),
            $controllerName,
            $action,
            'valid',
            $params
        );
    }

    protected function _prepareSessionActivityForApi(&$controllerName, &$action, array &$params)
    {
        $controllerName = 'bdApi_ControllerApi_Index';
        $action = '';
        $params = array();

        $session = bdApi_Data_Helper_Core::safeGetSession();
        if (!empty($session)) {
            $params['client_id'] = $session->getOAuthClientId();
        }
    }

    protected function _checkCsrf($action)
    {
        if (isset(self::$_executed['csrf'])) {
            return;
        }
        self::$_executed['csrf'] = true;

        $session = bdApi_Data_Helper_Core::safeGetSession();
        $client = $session->getOAuthClient();
        if (!empty($client) && !empty($client['_isPublicSessionClient'])) {
            // only check csrf if public session token is being used
            $this->_checkCsrfFromToken();
        }
    }


    protected function _preDispatchFirst($action)
    {
        $this->_startTime = microtime(true);

        parent::_preDispatchFirst($action);
    }

    protected function _postDispatch($controllerResponse, $controllerName, $action)
    {
        if ($controllerResponse instanceof XenForo_ControllerResponse_Error) {
            if ($controllerResponse->responseCode === 200) {
                // enforce response code 400 unless specified otherwise
                $controllerResponse->responseCode = 400;
            }
        }

        $this->_logRequest($controllerResponse, $controllerName, $action);

        parent::_postDispatch($controllerResponse, $controllerName, $action);
    }

    protected function _logRequest($controllerResponse, $controller, $action)
    {
        $responseTime = $this->_startTime !== 0.0 ? microtime(true) - $this->_startTime : null;

        if ($controllerResponse instanceof XenForo_ControllerResponse_Abstract) {
            if ($controllerResponse instanceof XenForo_ControllerResponse_Redirect) {
                $responseCode = 301;
                $responseOutput = array_merge($controllerResponse->redirectParams, array(
                    'redirectType' => $controllerResponse->redirectType,
                    'redirectMessage' => $controllerResponse->redirectMessage,
                    'redirectUri' => $controllerResponse->redirectTarget,
                ));
            } else {
                $responseCode = $controllerResponse->responseCode;
                $responseOutput = $this->_getResponseOutput($controllerResponse);
            }
        } else {
            $responseCode = $this->_response->getHttpResponseCode();
            $responseOutput = $controllerResponse;
        }

        if ($responseOutput !== false) {
            $requestUri = $this->_request->getRequestUri();
            $requestUri = preg_replace('#/index.php\?/?(.+?)&#', '/$1?', $requestUri);
            $requestUri = preg_replace('#\?.*$#', '', $requestUri);

            if (!is_array($responseOutput)) {
                $responseOutput = array('raw' => $responseOutput);
            }
            $responseOutput['_controller'] = $controller;
            $responseOutput['_action'] = $action;

            /* @var $logModel bdApi_Model_Log */
            $logModel = $this->getModelFromCache('bdApi_Model_Log');
            $logModel->logRequest(
                $this->_request->getMethod(),
                $requestUri,
                $this->_request->getParams(),
                $responseCode,
                $responseOutput,
                array(
                    'response_time' => $responseTime,
                )
            );
        }

        return true;
    }

    protected function _getResponseOutput(XenForo_ControllerResponse_Abstract $controllerResponse)
    {
        $responseOutput = array();

        if ($controllerResponse instanceof XenForo_ControllerResponse_View) {
            $responseOutput = $controllerResponse->params;
        } elseif ($controllerResponse instanceof XenForo_ControllerResponse_Error) {
            $responseOutput = array('error' => $controllerResponse->errorText);
        } elseif ($controllerResponse instanceof XenForo_ControllerResponse_Exception) {
            $responseOutput = $this->_getResponseOutput($controllerResponse->getControllerResponse());
        } elseif ($controllerResponse instanceof XenForo_ControllerResponse_Message) {
            $responseOutput = array('message' => $controllerResponse->message);
        } elseif ($controllerResponse instanceof XenForo_ControllerResponse_Reroute) {
            return false;
        }

        return $responseOutput;
    }

    protected function _setDeprecatedHeaders($newMethod, $newLink)
    {
        $this->_response->setHeader('X-Api-Deprecated', sprintf(
            'newMethod=%s, newLink=%s',
            strtoupper($newMethod),
            $newLink
        ), true);
    }
}
