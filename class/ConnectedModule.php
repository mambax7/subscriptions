<?php

namespace XoopsModules\Subscriptions;
/**
 * Subscriptions Access Control
 *
 * Enforces subscription-based access to connected modules.
 *
 * @package    subscriptions
 * @subpackage class
 */

// defined('XOOPS_ROOT_PATH') || die('Restricted access');


/**
 * class ConnectedModule
 */
class ConnectedModule extends \XoopsObject
{
    public function __construct()
    {
        $this->initVar('module_id',   XOBJ_DTYPE_INT,    null, false);
        $this->initVar('dirname',     XOBJ_DTYPE_TXTBOX, '',   true, 50);
        $this->initVar('name',        XOBJ_DTYPE_TXTBOX, '',   true, 150);
        $this->initVar('description', XOBJ_DTYPE_TXTAREA,'',   false);
        $this->initVar('owner_uid',   XOBJ_DTYPE_INT,    0,    false);
        $this->initVar('is_active',   XOBJ_DTYPE_INT,    1,    false);
        $this->initVar('webhook_url', XOBJ_DTYPE_TXTBOX, '',   false, 500);
        $this->initVar('api_key',     XOBJ_DTYPE_TXTBOX, '',   false, 64);
        $this->initVar('config',      XOBJ_DTYPE_TXTAREA,'',   false);
        $this->initVar('created_at',  XOBJ_DTYPE_INT,    0,    false);
    }
}
