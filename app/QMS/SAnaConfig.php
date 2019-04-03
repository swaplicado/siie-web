<?php namespace App\QMS;

use App\ERP\SModel;

class SAnaConfig extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_config';
    protected $table = 'qms_ana_configs';

    protected $fillable = [
                            'id_config',
                            'is_text',
                            'is_deleted',
                            'result',
                            'specification',
                            'group_number',
                            'analysis_id',
                            'item_link_type_id',
                            'item_link_id',
                            'min_value',
                            'max_value',
                            'created_by_id',
                            'updated_by_id',
                          ];

    /**
     * [analysis description]
     * Return object SAnalysis
     * @return SAnalysis
    */
    public function analysis()
    {
        return $this->belongsTo('App\QMS\SAnalysis', 'analysis_id');
    }

    /**
     * [userCreation description]
     * Return object User
     * @return User
    */
    public function userCreation()
    {
        return $this->belongsTo('App\User', 'created_by_id');
    }

    /**
     * [userUpdate description]
     * Return object User
     * @return User
     */
    public function userUpdate()
    {
        return $this->belongsTo('App\User', 'updated_by_id');
    }

    public function isValid()
    {
        $lResult = \DB::connection(session('db_configuration')->getConnCompany())
                ->table('qms_ana_configs as qac')
                ->where('analysis_id', $this->analysis_id)
                ->where('item_link_type_id', $this->item_link_type_id)
                ->where('item_link_id', $this->item_link_id)
                ->where('is_deleted', false);

        if ($this->id_config != null) {
            $lResult = $lResult->where('id_config', '!=', $this->id_config);
        }
                
        $lResult = $lResult->get();

        return sizeof($lResult) == 0;
    }
}
