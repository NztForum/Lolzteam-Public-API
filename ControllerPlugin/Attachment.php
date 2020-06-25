<?php

namespace Xfrocks\Api\ControllerPlugin;

use XF\Attachment\Manipulator;
use XF\ControllerPlugin\AbstractPlugin;
use XF\PrintableException;
use Xfrocks\Api\Controller\AbstractController;
use Xfrocks\Api\Entity\Token;
use Xfrocks\Api\Transform\TransformContext;
use Xfrocks\Api\XF\ApiOnly\Session\Session;

class Attachment extends AbstractPlugin
{
    /**
     * @param string $hash
     * @param string $contentType
     * @param mixed $context
     * @param string $formField
     * @return \Xfrocks\Api\Mvc\Reply\Api
     * @throws PrintableException
     * @throws \XF\Mvc\Reply\Exception
     */
    public function doUpload($hash, $contentType, $context, $formField = 'file')
    {
        /** @var \XF\Repository\Attachment $attachRepo */
        $attachRepo = $this->repository('XF:Attachment');
        $handler = $attachRepo->getAttachmentHandler($contentType);

        if ($handler === null) {
            throw new PrintableException('Invalid content type.');
        }

        if (!$handler->canManageAttachments($context, $error)) {
            throw $this->controller->exception($this->controller->noPermission($error));
        }

        $manipulator = new Manipulator($handler, $attachRepo, $context, $hash);

        if (!$manipulator->canUpload($uploadErrors)) {
            throw $this->controller->exception($this->controller->error($uploadErrors));
        }

        /** @var AbstractController $controller */
        $controller = $this->controller;
        $params = $controller->params();

        /** @var \XF\Http\Upload|null $file */
        $file = $params[$formField];
        if ($file === null) {
            throw $this->controller->errorException(\XF::phrase('uploaded_file_failed_not_found'));
        }

        $attachment = $manipulator->insertAttachmentFromUpload($file, $error);
        if (!$attachment) {
            throw $this->controller->exception($this->controller->noPermission($error));
        }

        return $controller->api(['attachment' => $controller->transformEntityLazily($attachment)]);
    }

    /**
     * @param array $contentData
     * @return string
     */
    public function getAttachmentTempHash(array $contentData = [])
    {
        /** @var AbstractController $controller */
        $controller = $this->controller;
        $params = $controller->params();

        $prefix = '';
        $inputHash = $params['attachment_hash'];

        if ($inputHash !== '') {
            $prefix = sprintf('hash%s', $inputHash);
        } elseif (isset($contentData['post_id'])) {
            $prefix = sprintf('post%d', $contentData['post_id']);
        } elseif (isset($contentData['thread_id'])) {
            $prefix = sprintf('thread%d', $contentData['thread_id']);
        } elseif (isset($contentData['forum_id'])) {
            $prefix = sprintf('node%d', $contentData['forum_id']);
        } elseif (isset($contentData['node_id'])) {
            $prefix = sprintf('node%d', $contentData['node_id']);
        } elseif (isset($contentData['message_id'])) {
            $prefix = sprintf('message%d', $contentData['message_id']);
        } elseif (isset($contentData['conversation_id'])) {
            $prefix = sprintf('conversation%d', $contentData['conversation_id']);
        }

        /** @var Session $session */
        $session = $this->session();
        $token = $session->getToken();

        return md5(sprintf(
            'prefix%s_client%s_visitor%d_salt%s',
            $prefix,
            $token !== null ? $token->client_id : '',
            \XF::visitor()->user_id,
            $this->app->config('globalSalt')
        ));
    }
}
