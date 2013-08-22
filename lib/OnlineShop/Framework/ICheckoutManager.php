<?php

interface OnlineShop_Framework_ICheckoutManager {

    /**
     * @abstract
     * @return array(OnlineShop_Framework_ICheckoutStep)
     */
    public function getCheckoutSteps();

    /**
     * @abstract
     * @param  $stepname
     * @return OnlineShop_Framework_ICheckoutStep
     */
    public function getCheckoutStep($stepname);

    /**
     * @abstract
     * @return OnlineShop_Framework_ICheckoutStep
     */
    public function getCurrentStep();

    /**
     * @abstract
     * @return OnlineShop_Framework_ICart
     */
    public function getCart();

    /**
     * @abstract
     * @param OnlineShop_Framework_ICheckoutStep $step
     * @param  $data
     * @return bool
     */
    public function commitStep(OnlineShop_Framework_ICheckoutStep $step, $data);

    /**
     * @abstract
     * @return bool
     */
    public function isFinished();

    /**
     * @return OnlineShop_Framework_AbstractPaymentInformation
     */
    public function startOrderPayment();

    /**
     * @param OnlineShop_Framework_Impl_Checkout_Payment_Status $status
     * @return OnlineShop_Framework_AbstractOrder
     */
    public function commitOrderPayment(OnlineShop_Framework_Impl_Checkout_Payment_Status $status);

    /**
     * @abstract
     * @return OnlineShop_Framework_AbstractOrder
     */
    public function commitOrder();

    /**
     * @abstract
     * @return bool
     */
    public function isCommitted();

    /**
     * @return OnlineShop_Framework_ICheckoutPayment|null
     */
    public function getPayment();

    /**
     * @return void
     */
    public function cleanUpPendingOrders();
}
