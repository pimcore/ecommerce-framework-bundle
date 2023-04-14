<?php

namespace Pimcore\Model\DataObject\OnlineShopVoucherToken;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\OnlineShopVoucherToken|false current()
 * @method DataObject\OnlineShopVoucherToken[] load()
 * @method DataObject\OnlineShopVoucherToken[] getData()
 * @method DataObject\OnlineShopVoucherToken[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "EF_OSVT";
protected $className = "OnlineShopVoucherToken";


/**
* Filter by tokenId (Token ID)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTokenId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("tokenId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by token (Token)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByToken ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("token")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by voucherSeries (Voucher Series)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVoucherSeries ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("voucherSeries")->addListingFilter($this, $data, $operator);
	return $this;
}



}
