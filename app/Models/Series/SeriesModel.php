<?php

namespace App\Models\Series;

use App\Models\Allocation\AlloccationModel;
use App\Models\Schedule\ScheduleModel;
use CodeIgniter\Model;

class SeriesModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_series';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields        = [];    

    public function getSeries(string $shift): array
    {
        $this->where('shift', $shift)
            ->where('status', 'A')
            ->orderBy('description');
        $result = $this->findAll();
        return !is_null($result) ? $result : [];
    }

    public function getDescription(int $id)
    {
        $return = $this->select('description,classification,shift,status,id_year_school,id')
            ->where('id', $id)
            ->get()
            ->getResult();
            
        return $return;
    }

  
  

   
}
