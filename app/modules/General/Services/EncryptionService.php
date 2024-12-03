<?php

  namespace App\Modules\General\Services;

  interface  EncryptionService
  {
    
    public  function encrypt($data, $module);
    public static function decrypt($data, $module);
    
  }