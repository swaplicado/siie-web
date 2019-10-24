<?php namespace App\QMS;

use App\ERP\SModel;

class SCertConfig extends SModel {

    protected $connection = 'siie';
    protected $primaryKey = 'id_cert_configuration';
    protected $table = 'qms_cert_configurations';

    protected $fillable = [
                            'id_cert_configuration',
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
                ->table('qms_cert_configurations as qcc')
                ->where('analysis_id', $this->analysis_id)
                ->where('item_link_type_id', $this->item_link_type_id)
                ->where('item_link_id', $this->item_link_id)
                ->where('is_deleted', false);

        if ($this->id_cert_configuration != null) {
            $lResult = $lResult->where('id_cert_configuration', '!=', $this->id_cert_configuration);
        }
                
        $lResult = $lResult->get();

        return sizeof($lResult) == 0;
    }
}
