<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use XoopsObject;

/**
 * Subscriptions Invoice class.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class InvoiceItem.
 */
class InvoiceItem extends XoopsObject
{
    public function __construct()
    {
        $this->initVar('item_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('invoice_id', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('description', XOBJ_DTYPE_TXTBOX, '', true, 255);
        $this->initVar('quantity', XOBJ_DTYPE_OTHER, '1.00', false);
        $this->initVar('unit_price', XOBJ_DTYPE_OTHER, '0.00', false);
        $this->initVar('amount', XOBJ_DTYPE_OTHER, '0.00', false);
    }
}
