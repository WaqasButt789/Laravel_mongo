<?php
    namespace App\Services;
    use MongoDB\Client as mongo;
    use Illuminate\Http\Request;
       
    class DataBaseConnectionService{
     
        function connection($table)   
        {
        $collection= (new mongo)->meta->$table;       
        return $collection;                      
        }   
    }


