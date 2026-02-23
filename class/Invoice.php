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
 * class Invoice
 */
class Invoice extends \XoopsObject
{
    const STATUS_DRAFT          = 'draft';
    const STATUS_OPEN           = 'open';
    const STATUS_PAID           = 'paid';
    const STATUS_VOID           = 'void';
    const STATUS_UNCOLLECTIBLE  = 'uncollectible';

    public function __construct()
    {
        $this->initVar('invoice_id',      XOBJ_DTYPE_INT,    null,  false);
        $this->initVar('invoice_number',  XOBJ_DTYPE_TXTBOX, '',    false);
        $this->initVar('user_id',         XOBJ_DTYPE_INT,    0,     true);
        $this->initVar('sub_id',          XOBJ_DTYPE_INT,    0,     false);
        $this->initVar('status',          XOBJ_DTYPE_TXTBOX, self::STATUS_OPEN, false);
        $this->initVar('subtotal',        XOBJ_DTYPE_OTHER,  '0.00',false);
        $this->initVar('tax_rate',        XOBJ_DTYPE_OTHER,  '0.00',false);
        $this->initVar('tax_amount',      XOBJ_DTYPE_OTHER,  '0.00',false);
        $this->initVar('discount_amount', XOBJ_DTYPE_OTHER,  '0.00',false);
        $this->initVar('total',           XOBJ_DTYPE_OTHER,  '0.00',false);
        $this->initVar('currency',        XOBJ_DTYPE_TXTBOX, 'USD', false);
        $this->initVar('due_date',        XOBJ_DTYPE_INT,    0,     false);
        $this->initVar('paid_at',         XOBJ_DTYPE_INT,    0,     false);
        $this->initVar('period_start',    XOBJ_DTYPE_INT,    0,     false);
        $this->initVar('period_end',      XOBJ_DTYPE_INT,    0,     false);
        $this->initVar('notes',           XOBJ_DTYPE_TXTAREA,'',    false);
        $this->initVar('created_at',      XOBJ_DTYPE_INT,    0,     false);
        $this->initVar('updated_at',      XOBJ_DTYPE_INT,    0,     false);
    }

    /**
     * Get line items for this invoice
     *
     * @return InvoiceItem[]
     */
    public function getItems(): array
    {
        $helper = Helper::getInstance();
        $handler  = $helper->getHandler('InvoiceItem');
        $criteria = new \Criteria('invoice_id', $this->getVar('invoice_id'));
        return $handler->getAll($criteria);
    }

    /**
     * Mark invoice as paid
     *
     * @return bool
     */
    public function markPaid(): bool
    {
        $helper = Helper::getInstance();
        $this->setVar('status',  self::STATUS_PAID);
        $this->setVar('paid_at', time());
        $handler = $helper->getHandler('Invoice');
        return $handler->insert($this);
    }
}
