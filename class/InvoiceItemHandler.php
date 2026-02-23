<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Invoice class
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * class InvoiceItemHandler
 */
class InvoiceItemHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_invoice_items', InvoiceItem::class, 'item_id', 'description');
    }

    /**
     * Add a line item to an invoice
     *
     * @param int    $invoiceId
     * @param string $description
     * @param float  $quantity
     * @param float  $unitPrice
     * @return InvoiceItem
     */
    public function addItem(int $invoiceId, string $description, float $quantity, float $unitPrice): InvoiceItem
    {
        $amount = round($quantity * $unitPrice, 2);
        /** @var InvoiceItem $item */
        $item = $this->create();
        $item->setVar('invoice_id',  $invoiceId);
        $item->setVar('description', $description);
        $item->setVar('quantity',    $quantity);
        $item->setVar('unit_price',  $unitPrice);
        $item->setVar('amount',      $amount);
        $this->insert($item);
        return $item;
    }
}


