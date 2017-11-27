<?php namespace App\SUtils;

class SConnectionUtils {

    /**
     * make the reconnection to database.
     *
     * @param  int  $sConnectionName
     * @param  int  $sHost
     * @param  int  $sDataBase
     * @param  int  $sUser
     * @param  int  $sPassword
     *
     * @return list of App\SYS\UserCompany
     */
    public static function reconnectDataBase($sConnectionName, $bDefault, $sHost, $sDataBase, $sUser, $sPassword)
    {
        if ($sHost != NULL)
        {
          \Config::set('database.connections.'.$sConnectionName.'.host', $sHost);
        }

        if ($sDataBase != NULL)
        {
          \Config::set('database.connections.'.$sConnectionName.'.database', $sDataBase);
        }

        if ($sUser != NULL)
        {
          \Config::set('database.connections.'.$sConnectionName.'.username', $sUser);
        }

        if ($sPassword != NULL)
        {
          \Config::set('database.connections.'.$sConnectionName.'.password', $sPassword);
        }

        if ($bDefault)
        {
            \Config::set('database.default', $sConnectionName);
        }

        \DB::reconnect($sConnectionName);
    }

    public static function reconnectCompany($sDataBase = '')
    {
        $connection = session()->has('db_configuration') ? session('db_configuration')->getConnCompany() : '';
        \Config::set('database.connections.'.$connection.'.database', session()->has('company') ? session('company')->database_name : $sDataBase);
    }

}
