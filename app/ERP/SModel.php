<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class SModel extends Model
{

    public function save(array $options = array())
    {
        $error = session('utils')->validateIsLocked($this);

        if (sizeof($error) > 0)
        {
          return $error;
        }

        parent::save($options);
        session('lock')->releaseLock($this);

        return array();
    }
}
