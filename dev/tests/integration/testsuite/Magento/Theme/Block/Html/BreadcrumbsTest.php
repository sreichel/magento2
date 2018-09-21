<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Block\Html;

use Magento\Framework\App\State;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\Theme\Block\Html\Breadcrumbs;

/**
 * @magentoAppArea frontend
 */
class BreadcrumbsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Breadcrumbs
     */
    private $block;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    protected function setUp()
    {
        Bootstrap::getObjectManager()->get(State::class)->setAreaCode('frontend');
        $this->block = Bootstrap::getObjectManager()
            ->get(LayoutInterface::class)
            ->createBlock(Breadcrumbs::class);
        $this->serializer = Bootstrap::getObjectManager()->get(SerializerInterface::class);
    }

    public function testAddCrumb()
    {
        $this->assertEmpty($this->block->toHtml());
        $info = ['label' => 'test label', 'title' => 'test title', 'link' => 'test link'];
        $this->block->addCrumb('test', $info);
        $html = $this->block->toHtml();
        $this->assertContains('test label', $html);
        $this->assertContains('test title', $html);
        $this->assertContains('test link', $html);
    }

    public function testGetCrumb()
    {
        $this->assertEmpty($this->block->toHtml());
        $this->assertEquals($this->block->getCrumbs(), []);
        $info = ['label' => '1', 'title' => '1', 'link' => '1', 'first' => null, 'last' => null, 'readonly' => null];
        $this->block->addCrumb('test', $info);
        $this->assertEquals($this->block->getCrumbs(), $info);
    }

    public function testAddCrumbAfterExisting()
    {
        $this->assertEmpty($this->block->toHtml());

        $crumbs = [];
        $crumbs['test1'] = ['label' => '1', 'title' => '1', 'link' => '1'];
        $crumbs['test2'] = ['label' => '2', 'title' => '2', 'link' => '2'];
        $crumbs['test3'] = ['label' => '3', 'title' => '3', 'link' => '3'];

        foreach ($crumbs as &$crumb) {
            $crumb += ['first' => null, 'last' => null, 'readonly' => null];
        }

        $this->block->addCrumb('test1', $crumbs['test1']);
        $this->block->addCrumb('test2', $crumbs['test2']);
        $this->block->addCrumbAfter('test3', $crumbs['test3'], 'test1');

        $result = [];
        $result['test1'] = $crumbs['test1'];
        $result['test3'] = $crumbs['test3'];
        $result['test2'] = $crumbs['test2'];
        $this->assertEquals($this->block->getCrumbs(), $result);
    }

    public function testAddCrumbAfterNonExisting()
    {
        $this->assertEmpty($this->block->toHtml());

        $crumbs = [];
        $crumbs['test1'] = ['label' => '1', 'title' => '1', 'link' => '1'];
        $crumbs['test2'] = ['label' => '2', 'title' => '2', 'link' => '2'];
        $crumbs['test3'] = ['label' => '3', 'title' => '3', 'link' => '3'];

        foreach ($crumbs as &$crumb) {
            $crumb += ['first' => null, 'last' => null, 'readonly' => null];
        }

        $this->block->addCrumb('test1', $crumbs['test1']);
        $this->block->addCrumb('test2', $crumbs['test2']);
        $this->block->addCrumbAfter('test3', $crumbs['test3'], 'na');
        $this->assertEquals($this->block->getCrumbs(), $crumbs);
    }

    public function testAddCrumbBeforeExisting()
    {
        $this->assertEmpty($this->block->toHtml());

        $crumbs = [];
        $crumbs['test1'] = ['label' => '1', 'title' => '1', 'link' => '1'];
        $crumbs['test2'] = ['label' => '2', 'title' => '2', 'link' => '2'];
        $crumbs['test3'] = ['label' => '3', 'title' => '3', 'link' => '3'];

        foreach ($crumbs as &$crumb) {
            $crumb += ['first' => null, 'last' => null, 'readonly' => null];
        }

        $this->block->addCrumb('test1', $crumbs['test1']);
        $this->block->addCrumb('test2', $crumbs['test2']);
        $this->block->addCrumbBefore('test3', $crumbs['test3'], 'test2');

        $result = [];
        $result['test1'] = $crumbs['test1'];
        $result['test3'] = $crumbs['test3'];
        $result['test2'] = $crumbs['test2'];
        $this->assertEquals($this->block->getCrumbs(), $result);
    }

    public function testAddCrumbBeforeNonExisting()
    {
        $this->assertEmpty($this->block->toHtml());

        $crumbs = [];
        $crumbs['test1'] = ['label' => '1', 'title' => '1', 'link' => '1'];
        $crumbs['test2'] = ['label' => '2', 'title' => '2', 'link' => '2'];
        $crumbs['test3'] = ['label' => '3', 'title' => '3', 'link' => '3'];

        foreach ($crumbs as &$crumb) {
            $crumb += ['first' => null, 'last' => null, 'readonly' => null];
        }

        $this->block->addCrumb('test1', $crumbs['test1']);
        $this->block->addCrumb('test2', $crumbs['test2']);
        $this->block->addCrumbBefore('test3', $crumbs['test3'], 'na');
        $this->assertEquals($this->block->getCrumbs(), $crumbs);
    }

    public function testRemoveCrumb()
    {
        $this->assertEmpty($this->block->toHtml());
        $info = ['label' => 'test label', 'title' => 'test title', 'link' => 'test link'];
        $this->block->addCrumb('test', $info);
        $this->block->removeCrumb('test');
        $this->assertEmpty($this->block->toHtml());
        $this->assertEquals($this->block->getCrumbs(), []);
    }

    public function testGetCacheKeyInfo()
    {
        $crumbs = ['test' => ['label' => 'test label', 'title' => 'test title', 'link' => 'test link']];
        foreach ($crumbs as $crumbName => &$crumb) {
            $this->block->addCrumb($crumbName, $crumb);
            $crumb += ['first' => null, 'last' => null, 'readonly' => null];
        }

        $cacheKeyInfo = $this->block->getCacheKeyInfo();
        $crumbsFromCacheKey = $this->serializer->unserialize(base64_decode($cacheKeyInfo['crumbs']));
        $this->assertEquals($crumbs, $crumbsFromCacheKey);
    }
}
