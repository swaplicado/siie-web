<?php namespace App\ERP;

use Illuminate\Database\Eloquent\Model;

class SMonth extends Model {

  protected $connection = 'siie';
  protected $primaryKey = 'id_month';
  protected $table = "erpu_months";
  protected $fillable = ['id_month', 'month', 'is_closed', 'is_deleted', 'year_id', 'created_by_id', 'updated_by_id'];

    /**
     * [__construct description]
     *
     * @param integer $iMonth  initializate mounth
     * @param integer $iYearId initializate year
     */
    public function __construct($iMonth = 0, $iYearId = 0)
    {
        $attributes = array();
        $attributes['month'] = $iMonth;
        $attributes['is_closed'] = false;
        $attributes['is_deleted'] = false;
        $attributes['year_id'] = $iYearId;
        $attributes['created_by_id'] = \Auth::user()->id;
        $attributes['updated_by_id'] = \Auth::user()->id;

        parent::__construct($attributes);
    }

  /**
   * [year description]
   * Return object SYear
   * @return SYear
   */
  public function year()
  {
    return $this->belongsTo('App\ERP\SYear');
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

  /**
   * [scopeSearch description]
   * To search in a query
   * @param  string $query   query to do
   * @param  integer $iFilter type of filter
   * @param  integer $iYearId where clause
   * @return query          
   */
  public function scopeSearch($query, $iFilter, $iYearId)
  {
      switch ($iFilter) {
        case \Config::get('scsys.FILTER.ACTIVES'):
          return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.ACTIVE'))
                      ->where('year_id', '=', $iYearId);
          break;

        case \Config::get('scsys.FILTER.DELETED'):
          return $query->where('is_deleted', '=', "".\Config::get('scsys.STATUS.DEL'))
                        ->where('year_id', '=', $iYearId);
          break;

        case \Config::get('scsys.FILTER.ALL'):
          return $query->where('year_id', '=', $iYearId);
          break;

        default:
          return $query->where('year_id', '=', $iYearId);
          break;
      }
  }
}
