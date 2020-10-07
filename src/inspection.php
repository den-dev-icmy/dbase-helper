<?php return;

/**
 * @author Bushaev Denis aka Dez64ru <dez64ru@gmail.com>
 * Dummy-file for correct inspection support
 */

define('DBASE_VERSION', 'Version of dbase');
define('DBASE_RDONLY', 0);
define('DBASE_RDWR', 2);
define('DBASE_TYPE_DBASE', 0);
define('DBASE_TYPE_FOXPRO', 1);

/**
 * Adds a record to a database
 * @link https://www.php.net/manual/function.dbase-add-record.php
 * @param resource $database
 * @param array $data
 * @return bool
 */
function dbase_add_record(resource $database, array $data): bool
{
}

/**
 * Closes a database
 * @link https://www.php.net/manual/en/function.dbase-close.php
 * @param resource $database
 * @return bool
 */
function dbase_close(resource $database): bool
{
}

/**
 * Creates a database
 * @link https://www.php.net/manual/en/function.dbase-create.php
 * @param string $path
 * @param array $fields
 * @param int $type
 * @return resource
 */
function dbase_create(string $path, array $fields, int $type = DBASE_TYPE_DBASE): resource
{
}

/**
 * Deletes a record from a database
 * @linkhttps://www.php.net/manual/en/function.dbase-delete-record.php
 * @param resource $database
 * @param int $number
 * @return bool
 */
function dbase_delete_record(resource $database, int $number): bool
{
}

/**
 * Gets the header info of a database
 * @link https://www.php.net/manual/en/function.dbase-get-header-info.php
 * @param resource $database
 * @return array
 */
function dbase_get_header_info(resource $database): array
{
}

/**
 * Gets a record from a database as an associative array
 * @link https://www.php.net/manual/en/function.dbase-get-record-with-names.php
 * @param resource $database
 * @param int $number
 * @return array
 */
function dbase_get_record_with_names(resource $database, int $number): array
{
}

/**
 * Gets a record from a database as an indexed array
 * @link https://www.php.net/manual/en/function.dbase-get-record.php
 * @param resource $database
 * @param int $number
 * @return array
 */
function dbase_get_record(resource $database, int $number): array
{
}

/**
 * Gets the number of fields of a database
 * @link https://www.php.net/manual/en/function.dbase-numfields.php
 * @param resource $database
 * @return int
 */
function dbase_numfields(resource $database): int
{
}

/**
 * Gets the number of records in a database
 * @link https://www.php.net/manual/en/function.dbase-numrecords.php
 * @param resource $database
 * @return int
 */
function dbase_numrecords(resource $database): int
{
}

/**
 * Opens a database
 * @link https://www.php.net/manual/en/function.dbase-open.php
 * @param string $path
 * @param int $mode
 * @return resource
 */
function dbase_open(string $path, int $mode): resource
{
}

/**
 * Packs a database
 * @link https://www.php.net/manual/en/function.dbase-pack.php
 * @param resource $database
 * @return bool
 */
function dbase_pack(resource $database): bool
{
}

/**
 * Replaces a record in a database
 * @link https://www.php.net/manual/en/function.dbase-replace-record.php
 * @param resource $database
 * @param array $data
 * @param int $number
 * @return bool
 */
function dbase_replace_record(resource $database, array $data, int $number): bool
{
}