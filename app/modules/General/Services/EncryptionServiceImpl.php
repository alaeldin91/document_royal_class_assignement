<?php

  namespace App\Modules\General\Services;
  use Illuminate\Support\Facades\Config;

   class EncryptionServiceImpl implements EncryptionService
  {

    public  function encrypt($data, $module)
    {
      $key = Config::get("modules.$module.encryption_key");
     
      return openssl_encrypt($data, 'AES-256-CBC', $key, 0, substr($key, 0, 16));
    }

    public static function decrypt($data, $module)
    {
        $key = Config::get("modules.$module.encryption_key");
      
        return openssl_decrypt($data, 'AES-256-CBC', $key, 0, substr($key, 0, 16));
    }

  }
  