<?php namespace App\SUtils;

class SLotsUtils
{
    protected $webhost;
    protected $webusername;
    protected $webpassword;
    protected $webdbname;
    protected $webcon;

    /**
     * receive the name of host to connect
     * can be a IP or name of host
     *
     * @param string $sHost
     */
    function __construct()
    {
        $this->webhost = env("SIIE_HOST", "");
        $this->webusername = env("SIIE_DB_USER", "");
        $this->webdbname = env("SIIE_DB_NAME", "");
        $this->webcon = mysqli_connect($this->webhost, $this->webusername, env("SIIE_DB_PASS", ""), $this->webdbname);
        $this->webcon->set_charset("utf8");

        if (mysqli_connect_errno()) {
            echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
        }
    }

    /**
     * Obtiene un arreglo de objetos con los lotes correspondientes a la cadena c
     * 
     * @param string $sLot
     * @return array<object>
     */
    public function getSiieLot($sLot)
    {
        $sql = "SELECT 
                *
            FROM
                trn_lot
            WHERE
                lot = ".$sLot."
            ORDER BY ts_new DESC;";

        $result = $this->webcon->query($sql);

        if ($result->num_rows > 0) {
            $lSiieLots = [];
            // output data of each row
            while ($lot = $result->fetch_assoc()) {
                $lSiieLots[] = (object) $lot;
            }

            return $lSiieLots;
        }

        return [];
    }

    /**
     * Obtiene un arreglo de objetos con los lotes correspondientes a la cadena c
     * 
     * @param string $sLot
     * @return array<object>
     */
    public function getSiieLotWithOutPP($sLot)
    {
        $sql = "SELECT 
                l.*, u.unit, u.symbol, i.item_key, i.item
            FROM
                trn_lot l 
                INNER JOIN erp.itmu_item i ON l.id_item = i.id_item
                INNER JOIN erp.itmu_unit u ON l.id_unit = u.id_unit
            WHERE
                l.lot = '".$sLot."' AND i.item NOT LIKE '%CONCENTRADOS%' AND NOT l.b_del 
            ORDER BY l.ts_new DESC;";

        $result = $this->webcon->query($sql);

        if ($result->num_rows > 0) {
            $lSiieLots = [];
            // output data of each row
            while ($lot = $result->fetch_assoc()) {
                $lSiieLots[] = (object) $lot;
            }

            return $lSiieLots;
        }

        return [];
    }

    public function closeConnection()
    {
        $this->webcon->close();
    }
}
