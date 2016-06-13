<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Plugin\Maker;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Eccube\Common\Constant;

class Maker
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
        $this->legacyEvent = new MakerLegacy($app);
    }

    public function onResponseAdminProductNewBefore(FilterResponseEvent $event)
    {
        $this->legacyEvent->onRenderAdminProductNewBefore($event);
    }

    public function onResponseAdminProductEditBefore(FilterResponseEvent $event)
    {
        $this->legacyEvent->onRenderAdminProductEditBefore($event);
    }

    public function onResponseAdminProductEditAfter()
    {
        $this->legacyEvent->onAdminProductEditAfter();
    }

    public function onResponseProductsDetailBefore(FilterResponseEvent $event)
    {
        $this->legacyEvent->onRenderProductsDetailBefore($event);
    }

    public function onRenderAdminProductNewBefore(FilterResponseEvent $event)
    {
        if ($this->supportNewHookPoint()) {
            return;
        }
        $this->legacyEvent->onRenderAdminProductNewBefore($event);
    }

    public function onRenderAdminProductEditBefore(FilterResponseEvent $event)
    {
        if ($this->supportNewHookPoint()) {
            return;
        }
        $this->legacyEvent->onRenderAdminProductEditBefore($event);
    }

    public function onAdminProductEditAfter()
    {
        if ($this->supportNewHookPoint()) {
            return;
        }
        $this->legacyEvent->onAdminProductEditAfter();
    }

    public function onRenderProductsDetailBefore(FilterResponseEvent $event)
    {
        if ($this->supportNewHookPoint()) {
            return;
        }
        $this->legacyEvent->onRenderProductsDetailBefore($event);
    }

    /**
     * @return bool v3.0.9以降のフックポイントに対応しているか？
     */
    private function supportNewHookPoint()
    {
        return version_compare('3.0.9', Constant::VERSION, '<=');
    }

}
