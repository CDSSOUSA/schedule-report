<?php

namespace App\Models\Schedule;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'tb_school_schedule';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields        = [];



    public function getTimeDayWeekOcupation(int $diaSemana, int $idSerie, int $posicao)
    {

        $result = $this->select(
            'p.name, 
            h.id_allocation, 
            pd.color, 
            pd.id_teacher,
            d.abbreviation,
            d.icone,
            h.id,
            h.position,
            h.dayWeek'
        )
            ->from('tb_school_schedule h')
            ->join('tb_allocation ap', 'h.id_allocation = ap.id')
            ->join('tb_teacher_discipline pd', 'ap.id_teacher_discipline = pd.id')
            ->join('tb_discipline d', 'pd.id_discipline = d.id')
            ->join('tb_teacher p', 'pd.id_teacher = p.id')
            ->where('h.dayWeek', $diaSemana)
            ->where('h.id_series', $idSerie)
            ->where('h.position', $posicao)
            ->where('h.id_year_school', session('session_idYearSchool'))
            ->get()->getResult();
        return $result;
    } 

    public function geSerieSchedule(int $idSerie)
    {
        return $this->select('d.icone, d.description, d.abbreviation, p.name, td.color, ' . $this->table . '.position, ' . $this->table . '.dayWeek')
            //->from('tb_school_schedule h')
            ->join('tb_allocation a', $this->table . '.id_allocation = a.id')
            ->join('tb_teacher_discipline td', 'a.id_teacher_discipline = td.id')
            ->join('tb_discipline d', 'td.id_discipline = d.id')
            //->join('tb_teacher_discipline pd', 'ap.id_teacher_discipline = pd.id')
            //->join('tb_discipline d', 'pd.id_discipline = d.id')
            ->join('tb_teacher p', 'td.id_teacher = p.id')
            ->where($this->table . '.id_series', $idSerie)
            ->where($this->table . '.status', 'A')
            ->where($this->table . '.id_year_school', session('session_idYearSchool'))
            //->groupBy('td.id_discipline')
            ->get()->getResult();


        // SELECT count(*) as total, td.description  FROM tb_school_schedule tss
        // JOIN tb_allocation ta ON tss.id_allocation = ta.id
        // JOIN tb_teacher_discipline ttd ON ta.id_teacher_discipline = ttd.id
        // JOIN tb_discipline td ON ttd.id_discipline = td.id 
        // WHERE tss.id_series = 1
        // GROUP BY ttd.id_discipline ; 


    }




}
