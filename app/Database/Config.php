<?php namespace App\Database;

  /**
   *
   */
  class Config {

    /**
     * Return an array with the databases that the system contains.
     * Utilized by the migrations.
     *
     * @return Array with the name of databases.
     */
    public static function getDataBases()
    {
        $lDataBases = array();

        $i = 0;
        $lDataBases[$i++] = 'siie_sap';
        $lDataBases[$i++] = 'siie_gsc';
        // $lDataBases[$i++] = 'siie_saporis';
        // $lDataBases[$i++] = 'siie_gs';

        return $lDataBases;
    }

    /**
     * Return the connection of the system database.
     *
     * @return String name of connection
     */
    public static function getConnSys()
    {
        return 'ssystem';
    }

    /**
     * Return the connection of the company database.
     *
     * @return String name of connection
     */
    public static function getConnCompany()
    {
        return 'siie';
    }
  }
