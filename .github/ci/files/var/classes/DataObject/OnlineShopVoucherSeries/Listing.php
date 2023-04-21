<?php

namespace Pimcore\Model\DataObject\OnlineShopVoucherSeries;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\OnlineShopVoucherSeries|false current()
 * @method DataObject\OnlineShopVoucherSeries[] load()
 * @method DataObject\OnlineShopVoucherSeries[] getData()
 * @method DataObject\OnlineShopVoucherSeries[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "EF_OSVS";
protected $className = "OnlineShopVoucherSeries";


/**
* Filter by name (Name)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByName ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("name")->addListingFilter($this, $data, $operator);
	return $this;
}



}
