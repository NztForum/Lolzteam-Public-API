<?php

namespace Xfrocks\Api\Controller;

use XF\Entity\LinkForum;
use XF\Entity\Node;
use XF\Tree;
use Xfrocks\Api\Transform\TransformContext;

class Navigation extends AbstractController
{
    /**
     * @return \Xfrocks\Api\Mvc\Reply\Api
     * @throws \XF\Mvc\Reply\Exception
     */
    public function actionGetIndex()
    {
        $params = $this
            ->params()
            ->define('parent', 'str');

        $elements = $this->getElements($params['parent']);

        $data = [
            'elements' => $elements
        ];

        return $this->api($data);
    }

    /**
     * @param int|null $parent
     * @return array
     * @throws \XF\Mvc\Reply\Exception
     */
    protected function getElements($parent)
    {
        if (is_numeric($parent)) {
            if ($parent > 0) {
                /** @var Node $parentNode */
                $parentNode = $this->assertRecordExists('XF:Node', $parent, [], 'bdapi_navigation_element_not_found');
                $expectedParentNodeId = $parentNode->node_id;
            } else {
                $parentNode = null;
                $expectedParentNodeId = 0;
            }
        } else {
            $parentNode = null;
            $expectedParentNodeId = null;
        }

        /** @var \XF\Repository\Node $nodeRepo */
        $nodeRepo = $this->repository('XF:Node');
        $nodeList = $nodeRepo->getNodeList();

        $elements = [];

        $tree = new Tree($nodeList, 'parent_node_id');
        $forumIds = [];
        $forums = null;

        foreach ($tree->getFlattened(0) as $item) {
            if ($item['record']->node_type_id == 'Forum') {
                $forumIds[] = $item['record']->node_id;
            }
        }

        if (count($forumIds) > 0) {
            $forums = $this->em()->findByIds('XF:Forum', $forumIds);
        }

        $arrangeOptions = [
            'expectedParentNodeId' => $expectedParentNodeId,
            'forums' => $forums
        ];

        $this->arrangeElements(
            $elements,
            $tree,
            is_int($expectedParentNodeId) ? $expectedParentNodeId : 0,
            $arrangeOptions
        );

        return $elements;
    }

    /**
     * @param array $elements
     * @param Tree $tree
     * @param mixed $parentNodeId
     * @param array $options
     * @return void
     */
    protected function arrangeElements(array &$elements, Tree $tree, $parentNodeId, array &$options = [])
    {
        $this->params()->getTransformContext()->onTransformedCallbacks[] = function ($context, &$data) use ($tree) {
            /** @var TransformContext $context */
            $source = $context->getSource();
            if (!($source instanceof \XF\Entity\AbstractNode)) {
                return;
            }

            $node = $source->Node;
            if ($node === null) {
                return;
            }

            $data['navigation_type'] = strtolower($node->node_type_id);
            $data['navigation_id'] = $source->node_id;
            $data['navigation_parent_id'] = $node->parent_node_id;

            $data['has_sub_elements'] = count($tree->children($source->node_id)) > 0;
            if ($data['has_sub_elements'] === true) {
                if (!isset($data['links'])) {
                    $data['links'] = [];
                }

                $data['links']['sub-elements'] = $this->buildApiLink(
                    'navigation',
                    null,
                    ['parent' => $data['navigation_id']]
                );
            }
        };

        foreach ($tree->children($parentNodeId) as $item) {
            $element = null;

            /** @var Node $node */
            $node = $item->record;

            switch ($node->node_type_id) {
                case 'Category':
                    /** @var \XF\Entity\Category|null $category */
                    $category = $this->em()->instantiateEntity(
                        'XF:Category',
                        ['node_id' => $node->node_id],
                        ['Node' => $node]
                    );

                    if ($category !== null) {
                        $element = $this->transformEntityLazily($category);
                    }
                    break;
                case 'Forum':
                    if (isset($options['forums'][$node->node_id])) {
                        $element = $this->transformEntityLazily($options['forums'][$node->node_id]);
                    }
                    break;
                case 'LinkForum':
                    /** @var LinkForum|null $linkForum */
                    $linkForum = $this->em()->instantiateEntity(
                        'XF:LinkForum',
                        ['node_id' => $node->node_id],
                        ['Node' => $node]
                    );

                    if ($linkForum !== null) {
                        $element = $this->transformEntityLazily($linkForum);
                    }
                    break;
                case 'Page':
                    /** @var \XF\Entity\Page|null $page */
                    $page = $this->em()->instantiateEntity(
                        'XF:Page',
                        ['node_id' => $node->node_id],
                        ['Node' => $node]
                    );

                    if ($page !== null) {
                        $element = $this->transformEntityLazily($page);
                    }
                    break;
            }

            if ($element !== null) {
                $elements[] = $element;
            }
        }
    }
}
