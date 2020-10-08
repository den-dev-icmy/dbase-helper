<?php

namespace dBaseHelper;

use Exception;

/**
 * @author Denis Bushaev aka Dez64ru <dez64ru@gmail.com>
 * Class dBaseHelper
 *
 * @property int $cursor Current cursor position
 * @property resource $file Current database file handler
 * @property int $lastId Last added/updated row index
 */
class DB
{
    const RETURN_INDEXED = 0;
    const RETURN_ASSOC = 1;

    const TYPE_DATE = 'D';
    const TYPE_NUMBER = 'N';
    const TYPE_FLOAT = 'F';
    const TYPE_STRING = 'C';
    const TYPE_BOOL = 'L';

    private $fileEncoding = 'UTF-8';
    private $filePath;
    private $fileMode;

    private $file;

    private $cursor = 1;
    private $lastInsertedId;

    private $config = [
        'encode' => true, //Encode to UTF from file encoding
        'trim' => true //Remove trailing and doubled spaces from values
    ];

    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->config, $config);
    }

    private function __prepareData(array $data, bool $revert = false): array
    {
        $headers = dbase_get_header_info($this->file);

        foreach ($data as $key => $value) {
            $type = $headers[array_search($key, array_keys($data))]['type'];

            switch ($type) {
                case 'number':
                    $data[$key] = intval($value);
                    break;
                case 'float':
                    $data[$key] = floatval($value);
                    break;
                case 'character':
                    if (!$revert) break;
                    if ($this->config['encode']) $data[$key] = mb_convert_encoding($value, 'UTF-8', $this->fileEncoding);
                    if ($this->config['trim']) $data[$key] = preg_replace('#\s+#', ' ', trim($value));
                    break;
                case 'date':
                    if (!$revert) {
                        $data[$key] = date('Ymd', $value);
                    } else {
                        $year = substr($value, 0, 4);
                        $month = substr($value, 4, 2);
                        $day = substr($value, 6, 2);
                        $data[$key] = strtotime("$month/$day/$year");
                    }
                    break;
                case 'boolean':
                    if (!$revert) {
                        if (null === $value) $data[$key] = '?';
                        else $data[$key] = boolval($value) ? 'Y' : 'F';
                    } else {
                        if (null !== $value) $data[$key] = in_array($value, ['Y', 'T']);
                    }
                    break;
            }
        }

        unset($data['deleted']);

        return $data;
    }

    /**
     * Opens database
     * @param string $path Path to database file
     * @param int $mode Mode of opening @link https://www.php.net/manual/function.dbase-open.php#refsect1-function.dbase-open-parameters
     * @param string $fileEncoding Encoding of source database file
     * @return self
     * @throws Exception
     */
    public function open(string $path, int $mode = DBASE_RDWR, string $fileEncoding = 'UTF-8'): self
    {
        if (!file_exists($path)) throw new Exception("File \"$path\" not exists");
        $this->file = dbase_open($path, $mode);
        if (false === $this->file) throw new Exception("Cannot open database \"$path\", maybe its broken");

        $this->filePath = $path;
        $this->fileMode = $mode;
        $this->fileEncoding = $fileEncoding;

        return $this;
    }

    public function lastInsertedId()
    {
        return $this->lastInsertedId;
    }

    /**
     * Closes database
     * @return void
     * @throws Exception
     */
    public function close()
    {
        if (!$this->file) throw new Exception("Before close database you need open its ;-)");
        dbase_close($this->file);
        unset($this->file);
    }

    /**
     * Returns array data of database
     * @param int $return Return type [Handler::RETURN_INDEXED || Handler::RETURN_ASSOC]
     * @return array|null
     * @throws Exception
     */
    public function toArray(int $return = self::RETURN_ASSOC): ?array
    {
        if (!$this->file) throw new Exception("Before convert to array, you need opens database");
        $arr = [];

        while ($item = $this->next($return)) {
            $arr[] = $item;
        }

        return $arr;
    }

    /**
     * Adds new row from array
     * @param array $data Array of rows
     * @return self
     * @throws Exception
     */
    public function fromArray(array $data)
    {
        if (!$this->file) throw new Exception("Before add new rows you must open database");
        foreach ($data as $item) $this->addRow($item);

        return $this;
    }

    /**
     * Returns row of database by its number
     * @param int $num Index of row
     * @param int $return Return type [Handler::RETURN_INDEXED || Handler::RETURN_ASSOC]
     * @return array|null
     * @throws Exception
     */
    public function getRow(?int $num = null, int $return = self::RETURN_ASSOC): ?array
    {
        if (!$this->file) throw new Exception("You need open database before try get access");
        if ($num === null) $num = dbase_numrecords($this->file);
        if ($num < 1) throw new Exception("Number of row cannot be smaller than 1");
        $maxNum = dbase_numrecords($this->file);
        if ($num > $maxNum) throw new Exception("Number of row (passed $num) greater than last row ($maxNum) of database");

        $row = $return === self::RETURN_ASSOC ? dbase_get_record_with_names($this->file, $num) : dbase_get_record($this->file, $num);

        return $this->__prepareData($row, true);
    }

    /**
     * Directly gets column by row number and column name
     * @param null|int $rowNum Row number
     * @param string $colName Column name
     * @return string|string[]|null
     * @throws Exception
     */
    public function getCol(?int $rowNum = null, string $colName)
    {
        $columnNames = array_column(dbase_get_header_info($this->file), 'name');
        if (!in_array($colName, $columnNames)) throw new Exception("Column name \"$colName\" not exists in this database");
        return $this->getRow($rowNum, self::RETURN_ASSOC)[$colName];
    }

    /**
     * Iterator
     * @param int $return Return type [Handler::RETURN_INDEXED || Handler::RETURN_ASSOC]
     * @return null|array
     * @throws Exception
     */
    public function next(int $return = self::RETURN_ASSOC): ?array
    {
        if (!$this->file) throw new Exception("Before try to get iterator, you need opens database");
        if ($this->cursor > dbase_numrecords($this->file)) return null;

        return $this->getRow($this->cursor++, $return);
    }

    /**
     * Set cursor position
     * @param int $pos Cursor position
     * @return self
     * @throws Exception
     */
    public function setCursor(int $pos = 1): self
    {
        if (!$this->file) throw new Exception("Before try to control cursor, you need opens database");
        if ($pos < 1) throw new Exception("Cursor cannot be smaller than 1");
        $maxPos = dbase_numrecords($this->file);
        if ($pos > $maxPos) throw new Exception("Cursor (passed $pos) cannot be grater than database last row ($maxPos)");
        $this->cursor = 0;

        return $this;
    }

    /**
     * Reset cursor position
     * @return self
     * @throws Exception
     */
    public function resetCursor(): self
    {
        if (!$this->file) throw new Exception("Before try to control cursor, you need opens database");
        $this->cursor = 1;

        return $this;
    }

    /**
     * Create new database file
     * @param string $path Path to databse file
     * @param array $fields Fieldset @link https://www.php.net/manual/intro.dbase.php (types of fields)
     * @param int $type Type of database (DBASE_TYPE_DBASE or DBASE_TYPE_FOXPRO) @link https://www.php.net/manual/function.dbase-create.php#refsect1-function.dbase-create-parameters
     * @return self
     * @throws Exception
     */
    public function create(string $path, array $fields, int $type = DBASE_TYPE_DBASE): self
    {
        $this->file = dbase_create($path, $fields, $type);
        if (!$this->file) throw new Exception("Cannot create database ($path)");

        $this->filePath = $path;
        $this->fileMode = DBASE_RDWR;
        return $this;
    }

    /**
     * Marks the entry for deletion
     * @param int $num Index of deletion row
     * @return self
     * @throws Exception
     */
    public function deleteRow(int $num)
    {
        if (!$this->file) throw new Exception("Before delete row, you needs open database");
        if (1 > $num) throw new Exception("Number of row cannot be smaller than 1");
        $maxRow = dbase_numrecords($this->file);
        if ($num > $maxRow) throw new Exception("Passed index of row ($num) cannot be greater than max index of row ($maxRow) in database");
        dbase_delete_record($this->file, $num);

        return $this;
    }

    /**
     * Commit all deleted rows
     * @return self
     * @throws Exception
     */
    public function commit(): self
    {
        if (!$this->file) throw new Exception("Before, you needs open database");
        dbase_pack($this->file);
        $this->close();
        $this->open($this->filePath, $this->fileMode, $this->fileEncoding);

        return $this;
    }

    /**
     * Add new row at the end of database. After add you can $db->
     * @param array $data <b>Indexed</b> array
     * @return self
     * @throws Exception
     */
    public function addRow(array $data): self
    {
        $data = $this->__prepareData($data);

        if (!$this->file) throw new Exception("Before add row you must opens database");
        if (!dbase_add_record($this->file, $data)) throw new Exception("Error while adding row");

        $this->lastInsertedId = dbase_numrecords($this->file);

        return $this;
    }

    /**
     * Update row by index
     * @param array $data <b>Indexed</b> array
     * @param int $num Index of the row to be modified
     * @return self
     * @throws Exception
     */
    public function updateRow(array $data, ?int $num = null)
    {
        if (!$this->file) throw new Exception("Before update row you must opens database");

        $maxRow = dbase_numrecords($this->file);
        if (null === $num) $num = $maxRow;

        $data = array_values(array_merge($this->getRow($num), $data));
        $data = $this->__prepareData($data);

        if (1 > $num) throw new Exception("Number of row cannot be smaller than 1");
        if ($num > $maxRow) throw new Exception("Passed index of row ($num) cannot be greater than max index of row ($maxRow) in database");
        if (!dbase_replace_record($this->file, $data, $num)) throw new Exception("Error while update row");

        return $this;
    }

    public function __destruct()
    {
        if ($this->file) $this->close();
    }
}