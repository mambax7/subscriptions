<?php

/**
 * PHPStan Bootstrap File for the Realestate module
 *
 * Provides stubs for XOOPS core classes that PHPStan needs to understand.
 * This file is only loaded during static analysis, not at runtime.
 */

// Define XOOPS constants if not defined
if (!defined('XOOPS_ROOT_PATH')) {
    define('XOOPS_ROOT_PATH', dirname(__DIR__, 2));
    define('XOOPS_URL', 'http://localhost');
    define('XOOPS_VAR_PATH', XOOPS_ROOT_PATH . '/xoops_data');
    define('XOOPS_PATH', XOOPS_ROOT_PATH . '/xoops_lib');
    define('XOOPS_UPLOAD_PATH', XOOPS_ROOT_PATH . '/uploads');
    define('XOOPS_UPLOAD_URL', XOOPS_URL . '/uploads');
    define('XOOPS_THEME_PATH', XOOPS_ROOT_PATH . '/themes');
    define('XOOPS_THEME_URL', XOOPS_URL . '/themes');
    define('XOOPS_GROUP_ADMIN', 1);
    define('XOOPS_GROUP_USERS', 2);
    define('XOOPS_GROUP_ANONYMOUS', 3);
}

// XoopsObject data types
if (!defined('XOBJ_DTYPE_INT')) {
    define('XOBJ_DTYPE_INT', 1);
    define('XOBJ_DTYPE_TXTBOX', 2);
    define('XOBJ_DTYPE_TXTAREA', 3);
    define('XOBJ_DTYPE_URL', 4);
    define('XOBJ_DTYPE_EMAIL', 5);
    define('XOBJ_DTYPE_ARRAY', 6);
    define('XOBJ_DTYPE_OTHER', 7);
    define('XOBJ_DTYPE_SOURCE', 8);
    define('XOBJ_DTYPE_STIME', 9);
    define('XOBJ_DTYPE_MTIME', 10);
    define('XOBJ_DTYPE_LTIME', 11);
    define('XOBJ_DTYPE_FLOAT', 12);
    define('XOBJ_DTYPE_SIMPLE_ARRAY', 13);
    define('XOBJ_DTYPE_UNICODE_TXTBOX', 14);
    define('XOBJ_DTYPE_UNICODE_TXTAREA', 15);
    define('XOBJ_DTYPE_UNICODE_URL', 16);
    define('XOBJ_DTYPE_UNICODE_EMAIL', 17);
    define('XOBJ_DTYPE_UNICODE_ARRAY', 18);
}

// Language constants used across the module
if (!defined('_GUESTS')) {
    define('_GUESTS', 'Guests');
}

// Stub classes for static analysis
if (!class_exists('XoopsDatabase')) {
    /**
     * @phpstan-type QueryResult resource|false
     */
    abstract class XoopsDatabase
    {
        /** @return string */
        abstract public function prefix(string $table);

        /** @return string */
        abstract public function quoteString(string $str);

        /** @return string */
        abstract public function escape(string $str);

        /** @return resource|false */
        abstract public function query(string $sql);

        /** @return resource|false */
        abstract public function queryF(string $sql);

        /**
         * @param resource $result
         * @return array<string, mixed>|false
         */
        abstract public function fetchArray($result);

        /**
         * @param resource $result
         * @return array<int, mixed>|false
         */
        abstract public function fetchRow($result);

        /** @param resource $result */
        abstract public function getRowsNum($result): int;

        abstract public function getInsertId(): int;

        abstract public function getAffectedRows(): int;

        /** @param resource $result */
        abstract public function freeRecordSet($result): void;
    }
}

if (!class_exists('XoopsMySQLDatabase')) {
    class XoopsMySQLDatabase extends XoopsDatabase
    {
        public function prefix(string $table) { return $table; }
        public function quoteString(string $str) { return "'" . $str . "'"; }
        public function escape(string $str) { return $str; }
        public function query(string $sql) { return false; }
        public function queryF(string $sql) { return false; }
        public function fetchArray($result) { return false; }
        public function fetchRow($result) { return false; }
        public function getRowsNum($result): int { return 0; }
        public function getInsertId(): int { return 0; }
        public function getAffectedRows(): int { return 0; }
        public function freeRecordSet($result): void {}
    }
}

if (!class_exists('XoopsDatabaseFactory')) {
    class XoopsDatabaseFactory
    {
        /** @return XoopsDatabase */
        public static function getDatabaseConnection()
        {
            throw new \RuntimeException('Stub method');
        }
    }
}

if (!class_exists('XoopsObject')) {
    class XoopsObject
    {
        /** @var array<string, mixed> */
        protected $vars = [];

        /** @var array<string, mixed> */
        protected $cleanVars = [];

        public function __construct() {}

        /** @param mixed $default */
        public function initVar(string $key, int $dataType, $default = null, bool $required = false, $maxLength = null, string $options = ''): void {}

        /** @return mixed */
        public function getVar(string $key, string $format = 's') {}

        /** @param mixed $value */
        public function setVar(string $key, $value, bool $notGpc = false): bool { return true; }

        /** @param array<string, mixed> $values */
        public function setVars(array $values, bool $notGpc = false): void {}

        /** @return array<string, mixed> */
        public function getVars(): array { return $this->vars; }

        public function cleanVars(): bool { return true; }

        /** @return array<string, mixed> */
        public function toArray(): array { return []; }

        public function isNew(): bool { return true; }

        public function setNew(): void {}

        public function unsetNew(): void {}
    }
}

if (!class_exists('XoopsObjectHandler')) {
    abstract class XoopsObjectHandler
    {
        /** @var XoopsDatabase */
        protected $db;

        public function __construct(XoopsDatabase $db) { $this->db = $db; }

        /** @return XoopsObject */
        abstract public function create(bool $isNew = true);

        /** @return XoopsObject|null */
        abstract public function get(int $id);

        abstract public function insert(XoopsObject $obj, bool $force = false): bool;

        abstract public function delete(XoopsObject $obj, bool $force = false): bool;
    }
}

if (!class_exists('XoopsPersistableObjectHandler')) {
    class XoopsPersistableObjectHandler extends XoopsObjectHandler
    {
        public function create(bool $isNew = true) { return new XoopsObject(); }

        public function get(int $id) { return null; }

        public function insert(XoopsObject $obj, bool $force = false): bool { return true; }

        public function delete(XoopsObject $obj, bool $force = false): bool { return true; }

        /** @return array<int, XoopsObject> */
        public function getObjects($criteria = null, bool $idAsKey = false, bool $asObject = true): array { return []; }

        /** @return int */
        public function getCount($criteria = null): int { return 0; }

        /** @return array<int, XoopsObject> */
        public function getAll($criteria = null, $fields = null, bool $asObject = true, bool $idAsKey = true): array { return []; }

        /** @param mixed $fieldvalue */
        public function updateAll(string $fieldname, $fieldvalue, $criteria = null, bool $force = false): bool { return true; }

        public function deleteAll($criteria = null, bool $force = false, bool $asObject = false): bool { return true; }
    }
}

if (!class_exists('CriteriaElement')) {
    abstract class CriteriaElement
    {
        public function setSort(string $sort): void {}
        public function setOrder(string $order): void {}
        public function setLimit(int $limit): void {}
        public function setStart(int $start): void {}
        public function setGroupby(string $group): void {}
        abstract public function render(): string;
        abstract public function renderWhere(): string;
    }
}

if (!class_exists('Criteria')) {
    class Criteria extends CriteriaElement
    {
        /** @param mixed $value */
        public function __construct(string $column, $value = '', string $operator = '=', string $prefix = '', string $function = '') {}
        public function render(): string { return ''; }
        public function renderWhere(): string { return ''; }
    }
}

if (!class_exists('CriteriaCompo')) {
    class CriteriaCompo extends CriteriaElement
    {
        /** @param CriteriaElement|null $criteria */
        public function __construct($criteria = null) {}
        public function add(CriteriaElement $criteria, string $condition = 'AND'): void {}
        public function render(): string { return ''; }
        public function renderWhere(): string { return ''; }
    }
}

if (!class_exists('XoopsModule')) {
    class XoopsModule extends XoopsObject
    {
        /** @return mixed */
        public function getVar(string $key, string $format = 's') {}
        /** @return mixed */
        public function getInfo(string $key = '') { return null; }
        public function loadInfo(string $dirname, bool $verbose = true): bool { return true; }
    }
}

if (!class_exists('XoopsUser')) {
    class XoopsUser extends XoopsObject
    {
        public function isAdmin(int $moduleId = 0): bool { return false; }
        /** @return array<int, int> */
        public function getGroups(): array { return []; }
        public function isGuest(): bool { return true; }
        public function uid(): int { return 0; }
    }
}

if (!class_exists('XoopsSecurity')) {
    class XoopsSecurity
    {
        public function check(bool $clearIfValid = true, $token = null, $name = null): bool { return true; }
        public function createToken(int $timeout = 0, $name = null): string { return ''; }
        public function validateToken($token = null, bool $clearIfValid = true, $name = null): bool { return true; }
        public function getTokenHTML($name = null): string { return ''; }
    }
}

if (!class_exists('MyTextSanitizer')) {
    class MyTextSanitizer
    {
        /** @return static */
        public static function getInstance() { return new static(); }
        public function htmlSpecialChars(string $text): string { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
    }
}

if (!class_exists('XoopsCache')) {
    class XoopsCache
    {
        /** @return mixed */
        public static function read(string $key) { return false; }
        /** @param mixed $value */
        public static function write(string $key, $value, int $duration = 0): bool { return true; }
        public static function delete(string $key): bool { return true; }
    }
}

// Function stubs
if (!function_exists('xoops_getHandler')) {
    /** @return XoopsObjectHandler|XoopsPersistableObjectHandler|null */
    function xoops_getHandler(string $name, bool $optional = false) { return null; }
}

if (!function_exists('xoops_getModuleHandler')) {
    /** @return XoopsPersistableObjectHandler|null */
    function xoops_getModuleHandler(string $name, $dirname = null, bool $optional = false) { return null; }
}

if (!function_exists('redirect_header')) {
    function redirect_header(string $url, int $time = 3, string $message = '', bool $addRedirect = true): void {}
}

if (!function_exists('xoops_loadLanguage')) {
    function xoops_loadLanguage(string $name, $dirname = null, $language = null): bool { return true; }
}

if (!function_exists('xoops_load')) {
    function xoops_load(string $name, $dirname = null): bool { return true; }
}
