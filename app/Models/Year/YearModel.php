<?php

namespace App\Models\Year;

use App\Models\Allocation\AllocationModel;
use App\Models\Schedule\ScheduleModel;
use App\Models\Series\SeriesModel;
use App\Models\TeacDisc\TeacDiscModel;
use CodeIgniter\Model;

class YearModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_year_school';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields        = [];




    public function getYearById(int $id)
    {

        return $this->where('id', $id)->find()[0];
    }

    public function getLastYear()
    {
        return $this->select('id, description')
                    ->where('status','A')
                    ->get()
                    ->getResultObject();

    }



}
