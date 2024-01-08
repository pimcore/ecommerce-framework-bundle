<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Bundle\EcommerceFrameworkBundle\Controller;

use Pimcore\Controller\KernelControllerEventInterface;
use Pimcore\Controller\UserAwareController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class ConfigController
 *
 * @Route("/config")
 *
 * @internal
 */
class ConfigController extends UserAwareController implements KernelControllerEventInterface
{
    /**
     * ConfigController constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(private RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onKernelControllerEvent(ControllerEvent $event): void
    {
        $this->checkPermission('bundle_ecommerce_back-office_order');
    }

    /**
     * @Route("/js-config", name="pimcore_ecommerceframework_config_jsconfig", methods={"GET"})
     *
     * @return Response
     */
    public function jsConfigAction(): Response
    {
        $config = $this->getParameter('pimcore_ecommerce.pimcore.config');

        $orderList = $config['menu']['order_list'];
        if ($orderList['route']) {
            $orderList['route'] = $this->router->generate($orderList['route']);
        } elseif ($orderList['path']) {
            $orderList['route'] = $orderList['path'];
        }

        unset($orderList['path']);

        $config['menu']['order_list'] = $orderList;

        $javascript = 'pimcore.registerNS("pimcore.bundle.EcommerceFramework.config");' . PHP_EOL;

        $javascript .= 'pimcore.bundle.EcommerceFramework.config = ';
        $javascript .= json_encode($config) . ';';

        $response = new Response($javascript);
        $response->headers->set('Content-Type', 'application/javascript');

        return $response;
    }
}
