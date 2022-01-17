# Bbase-helper 
Php Library for Dbase
### installation 
```bash
composer require Dez64ru/dbase-helper
```
### Example 
```php
    require_once __DIR__ . 'vendor/autoload.php';
    
    use dBaseHelper\DB;

    $db = new DB();
    $fieldset = [
        ['idx', DB::TYPE_NUMBER, 3, 0],
        ['name', DB::TYPE_STRING, 120],
        ['weight', DB::TYPE_FLOAT, 5, 2],
        ['birth', DB::TYPE_DATE],
        ['alive', DB::TYPE_BOOL]
    ];
    
    $idx = 1;
    
    $db->create('other-list.dbf', $fieldset)
        ->addRow([$idx++, 'Sarah Connor', 55.4, strtotime('10/05/1975'), false])
        ->addRow([$idx++, 'BoJack Horseman', 105.3, strtotime('05/06/1964'), true])
    ;
    
    $db->deleteRow(1)->commit(); //Terminator killed Sarah
    
    $db->updateRow(['weight' => 102.2]); //BoJack lost weight
    
    $db->fromArray([ //Add other ppls to the party
        [$idx++, 'Amy Pond', 56, strtotime('07/23/1986'), null],
        [$idx++, 'Mr. Peanutbutter', 35, strtotime('05/11/1970'), true],
    ]);
    
    $amyBirth = date('m-d-Y', $db->getCol(2, 'birth'));
    echo "Amy Pond was born in {$amyBirth}\n";
    
    var_dump($db->toArray());
    
    unset($db);
```
