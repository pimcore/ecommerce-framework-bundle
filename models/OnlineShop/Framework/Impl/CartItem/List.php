<?php

class OnlineShop_Framework_Impl_CartItem_List extends \Pimcore\Model\Listing\AbstractListing {

    /**
     * @var array
     */
    public $cartItems;

    /**
     * @var array
     */
    protected $order = array('ASC');

    /**
     * @var array
     */
    protected $orderKey = array('`addedDateTimestamp`');

    /**
     * @var array
     * @return boolean
     */
    public function isValidOrderKey($key) {
        if($key == "productId" || $key == "cartId" || $key == "count" || $key == "itemKey" || $key == "addedDateTimestamp") {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getCartItems() {
        if(empty($this->cartItems)) {
            $this->load();
        }
        return $this->cartItems;
    }

    /**
     * @param array $cartItems
     * @return void
     */
    public function setCartItems($cartItems) {
        $this->cartItems = $cartItems;
    }

    /**
     * @param string $className
     */
    public function setCartItemClassName( $className )
    {
        $this->getResource()->setClassName( $className );
    }

}
