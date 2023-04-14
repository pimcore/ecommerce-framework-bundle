<?php

namespace Pimcore\Model\DataObject\OnlineShopOrderItem;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\OnlineShopOrderItem|false current()
 * @method DataObject\OnlineShopOrderItem[] load()
 * @method DataObject\OnlineShopOrderItem[] getData()
 * @method DataObject\OnlineShopOrderItem[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "EF_OSOI";
protected $className = "OnlineShopOrderItem";


/**
* Filter by orderState (Order Item State)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOrderState ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("orderState")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by product (Produkt)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProduct ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("product")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productNumber (Produktnummer)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductNumber ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productNumber")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by productName (Produktname)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByProductName ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("productName")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by amount (Amount)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByAmount ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("amount")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by totalNetPrice (NetPrice)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTotalNetPrice ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("totalNetPrice")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by totalPrice (Price)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByTotalPrice ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("totalPrice")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by comment (Comment)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByComment ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("comment")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by subItems (Subitems)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySubItems ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("subItems")->addListingFilter($this, $data, $operator);
	return $this;
}



}
