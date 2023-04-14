<?php

namespace Pimcore\Model\DataObject\OnlineShopTaxClass;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\OnlineShopTaxClass|false current()
 * @method DataObject\OnlineShopTaxClass[] load()
 * @method DataObject\OnlineShopTaxClass[] getData()
 * @method DataObject\OnlineShopTaxClass[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "EF_OSTC";
protected $className = "OnlineShopTaxClass";


/**
* Filter by taxEntryCombinationType (Tax Entry Combination Type)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTaxEntryCombinationType ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("taxEntryCombinationType")->addListingFilter($this, $data, $operator);
	return $this;
}



}
