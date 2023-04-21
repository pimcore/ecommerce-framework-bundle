<?php

namespace Pimcore\Model\DataObject\OnlineShopOrder;

use Pimcore\Model;
use Pimcore\Model\DataObject;

/**
 * @method DataObject\OnlineShopOrder|false current()
 * @method DataObject\OnlineShopOrder[] load()
 * @method DataObject\OnlineShopOrder[] getData()
 * @method DataObject\OnlineShopOrder[] getObjects()
 */

class Listing extends DataObject\Listing\Concrete
{
protected $classId = "EF_OSO";
protected $className = "OnlineShopOrder";


/**
* Filter by ordernumber (Ordernumber)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOrdernumber ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("ordernumber")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by orderState (OrderState)
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
* Filter by orderdate (Orderdate)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByOrderdate ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("orderdate")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by items (Items)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByItems ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("items")->addListingFilter($this, $data, $operator);
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
* Filter by customerOrderData (Customer Order Data)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomerOrderData ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customerOrderData")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by voucherTokens (Voucher Tokens)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByVoucherTokens ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("voucherTokens")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by giftItems (Gift Items)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByGiftItems ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("giftItems")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by subTotalNetPrice (SubTotalNetPrice)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySubTotalNetPrice ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("subTotalNetPrice")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by subTotalPrice (SubTotalPrice)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySubTotalPrice ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("subTotalPrice")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by totalNetPrice (TotalNetPrice)
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
* Filter by totalPrice (TotalPrice)
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
* Filter by currency (Currency)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCurrency ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("currency")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by cartId (Cart ID)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCartId ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("cartId")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by successorOrder (Successor Order)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterBySuccessorOrder ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("successorOrder")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by cartHash (Cart Hash)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCartHash ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("cartHash")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by customer (Customer)
* @param mixed $data
* @param string $operator SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomer ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customer")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by customerFirstname (Firstname)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomerFirstname ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customerFirstname")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by customerLastname (Lastname)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomerLastname ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customerLastname")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by customerCompany (Company)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomerCompany ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customerCompany")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by customerStreet (Street)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomerStreet ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customerStreet")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by customerZip (Zip)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomerZip ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customerZip")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by customerCity (City)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomerCity ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customerCity")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by customerCountry (Country)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomerCountry ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customerCountry")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by customerEmail (Email)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByCustomerEmail ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("customerEmail")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by deliveryFirstname (Firstname)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDeliveryFirstname ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("deliveryFirstname")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by deliveryLastname (Lastname)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDeliveryLastname ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("deliveryLastname")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by deliveryCompany (Company)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDeliveryCompany ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("deliveryCompany")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by deliveryStreet (Street)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDeliveryStreet ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("deliveryStreet")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by deliveryZip (Zip)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDeliveryZip ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("deliveryZip")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by deliveryCity (City)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDeliveryCity ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("deliveryCity")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by deliveryCountry (Country)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByDeliveryCountry ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("deliveryCountry")->addListingFilter($this, $data, $operator);
	return $this;
}

/**
* Filter by paymentReference (Payment Ref.)
* @param string|int|float|array|Model\Element\ElementInterface $data  comparison data, can be scalar or array (if operator is e.g. "IN (?)")
* @param string $operator  SQL comparison operator, e.g. =, <, >= etc. You can use "?" as placeholder, e.g. "IN (?)"
* @return $this
*/
public function filterByPaymentReference ($data, $operator = '='): static
{
	$this->getClass()->getFieldDefinition("paymentReference")->addListingFilter($this, $data, $operator);
	return $this;
}



}
