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
 * class InvoiceHandler
 */
class InvoiceHandler extends \XoopsPersistableObjectHandler
{
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_invoices', Invoice::class, 'invoice_id', 'invoice_number');
    }

    /**
     * Get all invoices for a user
     *
     * @param int $userId
     * @return Invoice[]
     */
    public function getForUser(int $userId): array
    {
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('user_id', $userId));
        $criteria->setSort('created_at');
        $criteria->setOrder('DESC');
        return $this->getAll($criteria);
    }

    /**
     * Create an invoice for a subscription
     *
     * @param int    $userId
     * @param int    $subId
     * @param float  $subtotal
     * @param float  $taxRate
     * @param float  $discountAmount
     * @param string $currency
     * @param int    $periodStart
     * @param int    $periodEnd
     * @return Invoice
     */
    public function createForSubscription(
        int    $userId,
        int    $subId,
        float  $subtotal,
        float  $taxRate       = 0.0,
        float  $discountAmount= 0.0,
        string $currency      = 'USD',
        int    $periodStart   = 0,
        int    $periodEnd     = 0
    ): Invoice {
        $taxAmount = round($subtotal * $taxRate / 100, 2);
        $total     = round($subtotal + $taxAmount - $discountAmount, 2);

        /** @var Invoice $inv */
        $inv = $this->create();
        $inv->setVar('invoice_number',  $this->generateInvoiceNumber());
        $inv->setVar('user_id',         $userId);
        $inv->setVar('sub_id',          $subId);
        $inv->setVar('status',          Invoice::STATUS_OPEN);
        $inv->setVar('subtotal',        $subtotal);
        $inv->setVar('tax_rate',        $taxRate);
        $inv->setVar('tax_amount',      $taxAmount);
        $inv->setVar('discount_amount', $discountAmount);
        $inv->setVar('total',           $total);
        $inv->setVar('currency',        $currency);
        $inv->setVar('due_date',        strtotime('+14 days'));
        $inv->setVar('period_start',    $periodStart);
        $inv->setVar('period_end',      $periodEnd);
        $inv->setVar('created_at',      time());
        $inv->setVar('updated_at',      time());
        $this->insert($inv);
        return $inv;
    }

    /**
     * Generate a unique invoice number using the configured prefix
     *
     * @return string
     */
    public function generateInvoiceNumber(): string
    {
        $helper = xoops_getModuleHelper('subscriptions');
        $prefix = $helper ? $helper->getConfig('invoice_prefix') : 'INV-';
        $date   = date('Ymd');
        // Count existing invoices today
        $sql    = sprintf(
            "SELECT COUNT(*) FROM %s WHERE invoice_number LIKE '%s%s%%'",
            $this->table,
            $this->db->escape($prefix),
            $date
        );
        $result = $this->db->query($sql);
        $count  = 0;
        if ($result) {
            [$count] = $this->db->fetchRow($result);
        }
        return $prefix . $date . '-' . str_pad((int)$count + 1, 4, '0', STR_PAD_LEFT);
    }
}
