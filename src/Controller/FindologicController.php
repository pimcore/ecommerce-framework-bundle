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

use Pimcore\Controller\FrontendController;
use Pimcore\Controller\KernelControllerEventInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

/**
 * Class FindologicController
 *
 * Routing see routing.yaml
 *
 * @internal
 */
class FindologicController extends FrontendController implements KernelControllerEventInterface
{
    public function onKernelControllerEvent(ControllerEvent $event): void
    {
        $this->checkPermission('bundle_ecommerce_back-office_order');
    }

    /**
     * create xml output for findologic
     */
    public function exportAction(Request $request): Response
    {
        // init
        $start = (int)$request->get('start');
        $count = (int)$request->get('count', 200);
        $shopKey = $request->get('shopkey');

        $db = \Pimcore\Db::getConnection();

        if ($request->get('id')) {
            $query = "SELECT SQL_CALC_FOUND_ROWS id, data FROM {$this->getExportTableName()} WHERE shop_key = :shop_key and id = :id LIMIT {$start}, {$count}";
            $items = $db->fetchAllAssociative($query, ['shop_key' => $shopKey, 'id' => (int) $request->get('id')]);
        }
        // load export items
        elseif ($request->get('type')) {
            $query = "SELECT SQL_CALC_FOUND_ROWS id, data FROM {$this->getExportTableName()} WHERE shop_key = :shop_key and `type` = :type LIMIT {$start}, {$count}";
            $items = $db->fetchAllAssociative($query, ['shop_key' => $shopKey, 'type' => $request->get('type')]);
        } else {
            $query = "SELECT SQL_CALC_FOUND_ROWS id, data FROM {$this->getExportTableName()} WHERE shop_key = :shop_key LIMIT {$start}, {$count}";
            $items = $db->fetchAllAssociative($query, ['shop_key' => $shopKey]);
        }

        // get counts
        $indexCount = $db->fetchOne('SELECT FOUND_ROWS()');
        $itemCount = count($items);

        // create xml header
        $xml = <<<XML
<?xml version="1.0"?>
<findologic version="0.9">
    <items start="{$start}" count="{$itemCount}" total="{$indexCount}">
XML;

        // add items
        $transmitIds = [];
        foreach ($items as $row) {
            $xml .= $row['data'];

            $transmitIds[] = $row['id'];
        }

        // complete xml
        $xml .= <<<XML
    </items>
</findologic>
XML;

        // output
        if ($request->get('validate')) {
            $doc = new \DOMDocument();
            $doc->loadXML($xml);

            $response = new Response();
            var_dump($doc->schemaValidate('bundles/pimcoreecommerceframework/vendor/findologic/export.xsd'));
        } else {
            $response = new Response($xml);
            $response->headers->set('Content-Type', 'text/xml');

            // mark items as transmitted
            if ($transmitIds) {
                $db->executeQuery(sprintf(
                    'UPDATE %1$s SET last_transmit = now() WHERE id in(%2$s)',
                    $this->getExportTableName(),
                    implode(',', $transmitIds)
                ));
            }
        }

        return $response;
    }

    protected function getExportTableName(): string
    {
        return 'ecommerceframework_productindex_export_findologic';
    }
}
