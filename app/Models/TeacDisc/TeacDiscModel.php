<?php

namespace App\Models\TeacDisc;

use CodeIgniter\Model;

class TeacDiscModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_teacher_discipline';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields        = [];




    public function getTeacherDisciplineById(int $id)
    {
        $a = $this->select(
            't.name, 
            d.description,
            ' . $this->table . '.id,
            ' . $this->table . '.id_teacher,
            ' . $this->table . '.amount,
            ' . $this->table . '.color,
            ' . $this->table . '.id_year_school,           
            d.abbreviation,
            d.icone'
        )
            ->join('tb_teacher t', 't.id =' . $this->table . '.id_teacher')
            ->join('tb_discipline d', 'd.id =' . $this->table . '.id_discipline')           
            ->where($this->table . '.id_teacher', $id)
            ->orderBy('t.name')
            ->get()->getResultObject();
        //dd($a);
        return $a;
    }





}
