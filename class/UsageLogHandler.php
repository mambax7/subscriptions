<?php

declare(strict_types=1);

namespace XoopsModules\Subscriptions;

use XoopsDatabase;
use XoopsPersistableObjectHandler;

use function sprintf;

/**
 * Subscriptions Access Control.
 *
 * Enforces subscription-based access to connected modules.
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * class UsageLogHandler.
 */
class UsageLogHandler extends XoopsPersistableObjectHandler
{
    public function __construct(XoopsDatabase $db)
    {
        parent::__construct($db, 'subscriptions_usage_logs', UsageLog::class, 'log_id', 'log_id');
    }

    /**
     * Record a usage event.
     *
     * @param int $subId
     * @param int $userId
     * @param int $moduleId
     * @param string $eventType
     * @param float $quantity
     * @param float $unitPrice
     *
     * @return bool
     */
    public function record(int $subId, int $userId, int $moduleId, string $eventType, float $quantity, float $unitPrice): bool
    {
        /** @var UsageLog $log */
        $log = $this->create();
        $log->setVar('sub_id', $subId);
        $log->setVar('user_id', $userId);
        $log->setVar('module_id', $moduleId);
        $log->setVar('event_type', $eventType);
        $log->setVar('quantity', $quantity);
        $log->setVar('unit_price', $unitPrice);
        $log->setVar('billed', 0);
        $log->setVar('created_at', time());

        return $this->insert($log);
    }

    /**
     * Get unbilled usage total for a subscription.
     *
     * @param int $subId
     *
     * @return float
     */
    public function getUnbilledTotal(int $subId): float
    {
        $sql = sprintf(
            'SELECT SUM(quantity * unit_price) FROM %s WHERE sub_id = %d AND billed = 0',
            $this->table,
            (int) $subId
        );
        $result = $this->db->query($sql);
        if ($result) {
            [$total] = $this->db->fetchRow($result);

            return (float) $total;
        }

        return 0.0;
    }

    /**
     * Get aggregated usage summary for a subscription within a billing period.
     * Groups by event_type so the dashboard can show e.g. "Downloads: 12".
     *
     * @param int $subId
     * @param int $periodStart Unix timestamp
     * @param int $periodEnd Unix timestamp
     *
     * @return array e.g. [['event_type'=>'download','total_qty'=>12,'total_cost'=>'1.20'], ...]
     */
    public function getSummaryForPeriod(int $subId, int $periodStart, int $periodEnd): array
    {
        $sql = sprintf(
            'SELECT event_type,
                    SUM(quantity)              AS total_qty,
                    SUM(quantity * unit_price) AS total_cost,
                    MAX(created_at)            AS last_event
             FROM %s
             WHERE sub_id = %d
               AND created_at >= %d
               AND created_at <= %d
             GROUP BY event_type
             ORDER BY total_qty DESC',
            $this->table,
            (int) $subId,
            (int) $periodStart,
            (int) $periodEnd
        );
        $result = $this->db->query($sql);
        $rows = [];
        if ($result) {
            while ($row = $this->db->fetchArray($result)) {
                $rows[] = [
                    'event_type' => $row['event_type'],
                    'total_qty'  => (float) $row['total_qty'],
                    'total_cost' => round((float) $row['total_cost'], 2),
                    'last_event' => $row['last_event'] > 0 ? date('Y-m-d', (int) $row['last_event']) : '—',
                ];
            }
        }

        return $rows;
    }

    /**
     * Mark all unbilled usage logs for a subscription as billed.
     * Call this after generating a renewal invoice.
     *
     * @param int $subId
     *
     * @return bool
     */
    public function markBilled(int $subId): bool
    {
        $sql = sprintf(
            'UPDATE %s SET billed = 1 WHERE sub_id = %d AND billed = 0',
            $this->table,
            (int) $subId
        );

        return (bool) $this->db->query($sql);
    }

    /**
     * Get total quantity used for a specific event type in the current billing period.
     * Used by getRemainingQuota() in the SDK.
     *
     * @param int $subId
     * @param int $periodStart
     * @param int $periodEnd
     * @param string $eventType Empty string = all types
     *
     * @return float
     */
    public function getUsedQty(int $subId, int $periodStart, int $periodEnd, string $eventType = ''): float
    {
        $sql = sprintf(
            'SELECT SUM(quantity) FROM %s WHERE sub_id = %d AND created_at >= %d AND created_at <= %d%s',
            $this->table,
            (int) $subId,
            (int) $periodStart,
            (int) $periodEnd,
            $eventType !== '' ? sprintf(" AND event_type = '%s'", $this->db->escape($eventType)) : ''
        );
        $result = $this->db->query($sql);
        if ($result) {
            [$qty] = $this->db->fetchRow($result);

            return (float) $qty;
        }

        return 0.0;
    }
}
