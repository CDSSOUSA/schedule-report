<?php

namespace App\Controllers\Report\Schedule;


use App\Models\Year\YearModel;
use App\Models\Allocation\AllocationModel;
use App\Controllers\BaseController;
use App\Libraries\SchedulePDF;
use App\Models\Schedule\ScheduleModel;
use App\Models\Series\SeriesModel;

//use FPDF;
//use FPDF2;

class Schedule extends BaseController
{
    private string $encode = 'ISO-8859-1';
    public function schedule(string $shift)
    {
        // $teacher = new TeacDiscModel();
        // $teacherData = $teacher->getTeacherDisciplineById($idSerie);


        // $name = $teacherData[0]->name;
        //$year = session('session_idYearSchool');

        // // $clasfication = $seriesData[0]->classification;
        // // $shift = $seriesData[0]->shift;
        // // $idYearSchool = $seriesData[0]->id_year_school;

        $yearSchool = new YearModel();
        $yearLast = $yearSchool->getLastYear();

        //dd($yearLast);
       
        $year = $yearSchool->getYearById($yearLast[0]->id);
        
        $year = $yearLast[0]->id;
        $yearDescription =$yearLast[0]->description;
        //$yearDescription = 1;

        $LINE_HEIGHT = 7.4;
        $CELL_WIDTH = 30;

        $pdf = new SchedulePDF();
        $data = new AllocationModel();
        //$resultManha = $data->getAllocationTeacherOcupationByShift($idSerie, 'M');

        //if ($resultManha) {
        $sizeReport = mb_strtoupper($shift) === 'M' ? 'A3' : 'A4';
        $orietationReport = mb_strtoupper($shift) === 'M' ? 'L' : 'P';

        $pdf->AddPage($orietationReport, $sizeReport);
        $pdf->AliasNbPages();
        $pdf->SetLeftMargin(15);
        $pdf->SetRightMargin(15);
        $pdf->SetFillColor(200, 200, 200);

        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Ln(1);
        //$title = $name . ' - ' . turno('M') . ' :: ' . $yearDescription;
        $title = turno($shift) . ' :: ' . $yearDescription;


        /* CABECALHO DA TERMO */
        $pdf->SetFont('Courier', 'B', 10);
        $textoTituloFicha = 'QUADRO DE HORÁRIO :: ' . $title;
        $pdf->SetFont('Courier', 'B', 9);
        //$ficha->SetY(80);
        $pdf->SetX(15);
        $pdf->MultiCell(0, 4, mb_convert_encoding($textoTituloFicha,$this->encode), 0, 'L');
        $pdf->Ln(1);
        $pdf->SetTitle(mb_convert_encoding('QUADRO DE HORÁRIO :: ' . $title, $this->encode));
        /* FIM */
        $pdf->SetFont('Courier', 'B', 8);
        //ob_end_clean();
        //ob_get_clean();
        $pdf->Cell($CELL_WIDTH - 10, $LINE_HEIGHT, 'DIAS', 'TBLR', 0, 'C', 1);
        $pdf->Cell($CELL_WIDTH - 10, $LINE_HEIGHT, 'AULAS', 'TBLR', 0, 'C', 1);

        $series = new SeriesModel();
        $dataSeries = $series->getSeries($shift);

        foreach ($dataSeries as $item) {

            $pdf->SetFillColor(236, 126, 49);

            $pdf->Cell($CELL_WIDTH, $LINE_HEIGHT, ($item->description . mb_convert_encoding('º',$this->encode) . $item->classification), 'TBLR', 0, 'C', 1);
        }
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Ln($LINE_HEIGHT + 2);
        $data = new ScheduleModel();
        $dataSchedule = '-';

        for ($dw = 2; $dw < 7; $dw++) {
            // $pdf->Cell( $CELL_WIDTH , $LINE_HEIGHT, diaSemanaExtenso($dw), 'TBLR', 0, 'C', 1);
            for ($ps = 1; $ps < 7; $ps++) {

                $dayShow = '';
                $LINE_HEIGHT_CELL = $LINE_HEIGHT;
                $rowColor = $dw % 2 == 0 ? 0 : 1;
                if ($ps == 3) {
                    //$LINE_HEIGHT_CELL = 15; 
                    $dayShow = diaSemanaExtenso($dw);
                    //$rowColor = 0;
                }

                $borderShow = 'L';
                if ($ps == 1) {
                    $borderShow = 'TL';
                } else if ($ps == 6) {

                    $borderShow = 'BL';
                }
                //$borderShow = $ps == 1 ? 'TL' : 'L';     

                //$pdf->SetFillColor(0, 200, 0);
                $pdf->Cell($CELL_WIDTH - 10, $LINE_HEIGHT_CELL, mb_convert_encoding($dayShow,$this->encode), $borderShow, 0, 'C', $rowColor);
                $pdf->Cell($CELL_WIDTH - 10, $LINE_HEIGHT, $ps . mb_convert_encoding('ª',$this->encode), 'TBLR', 0, 'C', $rowColor);
                //$pdf->Ln($LINE_HEIGHT);

                foreach ($dataSeries as $item) {
                    $resultManha = $data->getTimeDayWeekOcupation($dw, $item->id, $ps);
                    foreach ($resultManha as $iten) {

                        if ($iten->position == $ps && $iten->dayWeek == $dw) {
                            $pdf->SetTextColorHexa($iten->color);
                            //$pdf->Image(base_url() . "/assets/img/{$item->icone}", 15, 5, 45); // importa uma imagem

                            $dataSchedule = mb_convert_encoding(abbreviationTeacher($iten->name),$this->encode) . "-" . mb_convert_encoding($iten->abbreviation,$this->encode);
                        }
                    }
                    $pdf->Cell($CELL_WIDTH, $LINE_HEIGHT, $dataSchedule, 1, 0, 'C', $rowColor);
                    //$pdf->Cell(40, 5, ' ', 1, 0, 'C', 0);
                    $pdf->SetTextColor(0, 0, 0);
                    $dataSchedule = '-';
                }
                $pdf->Ln($LINE_HEIGHT);
            }
        }

        $pdf->Ln($LINE_HEIGHT + 2);

        // for ($ps = 1; $ps < 7; $ps++) {
        //     $dataTicketAula = $ps . "ª Aula \n" . translateSchedule($ps, 'M');
        //     $pdf->Cell($CELL_WIDTH, $LINE_HEIGHT,  mb_convert_enconding($dataTicketAula), 'TBLR', 0, 'C', 1);
        //     //$pdf->Ln(6);
        //     $data = new AllocationModel();
        //     $resultManha = $data->getAllocationTeacherOcupation($idSerie);

        //     //dd($result);

        //     //dd($result);



        //     //$pdf->Cell(40, 5, $pos, 'TBLR', 0, 'C', 1);
        //     //dd($result);
        //     $dataSchedule = '';

        //     for ($dw = 2; $dw < 7; $dw++) {

        //         foreach ($resultManha as $item) {
        //             if ($item->position == $ps && $item->dayWeek == $dw && $item->shift == 'M') {

        //                 $pdf->SetTextColorHexa($item->color);
        //                 //$pdf->Image(base_url() . "/assets/img/{$item->icone}", 15, 5, 45); // importa uma imagem

        //                 $dataSchedule = mb_convert_enconding($item->nameDiscipline) . "\n" . mb_convert_enconding($item->description . 'º') . $item->classification . "-" . mb_convert_enconding(turno($item->shift));
        //             }
        //         }
        //         //$pdf->SetFillColor(0, 169, 169);
        //         $pdf->Cell($CELL_WIDTH, $LINE_HEIGHT, $dataSchedule, 1, 0, 'C', 0);
        //         //$pdf->Cell(40, 5, ' ', 1, 0, 'C', 0);
        //         $pdf->SetTextColor(0, 0, 0);
        //         $dataSchedule = '';
        //     }
        //     //$pdf->SetFillColor(0,0,0);


        //     $pdf->Ln($LINE_HEIGHT);
        // }
        //}

        //$resultTarde = $data->getAllocationTeacherOcupationByShift($idSerie, 'T');

        //if ($resultTarde) {

        // $pdf->AddPage('L', 'A4');
        // $pdf->SetLeftMargin(15);
        // $pdf->SetRightMargin(15);
        // $pdf->SetFillColor(200, 200, 200);

        // $pdf->SetFont('Arial', 'B', 9);
        // //$this->pdf->SetWidths(array(40, 40, 40));

        // //$this->pdf->Row(array('f name', 'l name', 'email'));

        // //$this->pdf->Row(array('john', 'doe', 'admin@gmail.com'));

        // // $pdf->Cell(40, 5, 'Total By Date:', 'TB', 0, 'L', '1');
        // // $pdf->Cell(40, 5, date("d-m-y"), 'B', 0, 'L', 0);
        // // $pdf->Cell(40, 5, 'By OpenGisCRM :', 'TB', 0, 'L', '1');
        // // $pdf->Cell(40, 5, 'https://opengiscrm.com/', 'B', 0, 'L', 0);
        // $pdf->Ln(15);
        // $title = turno('T') . ' :: ' . $yearDescription;


        // /* CABECALHO DA TERMO */
        // $pdf->SetFont('Courier', 'B', 12);
        // $textoTituloFicha = 'QUADRO DE HORÁRIO :: ' . $title;
        // $pdf->SetFont('Courier', 'B', 10);
        // //$ficha->SetY(80);
        // $pdf->SetX(15);
        // $pdf->MultiCell(0, 4, mb_convert_enconding($textoTituloFicha), 0, 'L');
        // $pdf->Ln(5);
        // $pdf->SetTitle(mb_convert_enconding($textoTituloFicha));
        // /* FIM */

        // //ob_end_clean();
        // //ob_get_clean();
        // $pdf->Cell(40, $LINE_HEIGHT, 'DIAS/AULAS', 'TBLR', 0, 'C', 1);

        // for ($dw = 2; $dw < 7; $dw++) {
        //     $pdf->Cell(40, $LINE_HEIGHT, diaSemanaExtenso($dw), 'TBLR', 0, 'C', 1);
        // }

        // $pdf->Ln($LINE_HEIGHT + 2);

        // for ($ps = 1; $ps < 7; $ps++) {
        //     $dataTicketAula = $ps . "ª Aula \n" . translateSchedule($ps, 'T');
        //     $pdf->Cell(40, $LINE_HEIGHT,  mb_convert_enconding($dataTicketAula), 'TBLR', 0, 'C', 1);
        //     //$pdf->Ln(6);
        //     $data = new AllocationModel();
        //     $result = $data->getAllocationTeacherOcupation($idSerie);

        //     //dd($result);

        //     //dd($result);



        //     //$pdf->Cell(40, 5, $pos, 'TBLR', 0, 'C', 1);
        //     //dd($result);
        //     $dataSchedule = '';

        //     for ($dw = 2; $dw < 7; $dw++) {

        //         foreach ($result as $item) {
        //             if ($item->position == $ps && $item->dayWeek == $dw && $item->shift == 'T') {



        //                 $pdf->SetTextColorHexa($item->color);
        //                 //$pdf->Image(base_url() . "/assets/img/{$item->icone}", 15, 5, 45); // importa uma imagem

        //                 $dataSchedule = mb_convert_enconding($item->nameDiscipline) . "\n" . mb_convert_enconding($item->description . 'º') . $item->classification . "-" . mb_convert_enconding(turno($item->shift));
        //             }
        //         }
        //         //$pdf->SetFillColor(0, 169, 169);
        //         $pdf->Cell(40, $LINE_HEIGHT, $dataSchedule, 1, 0, 'C', 0);
        //         //$pdf->Cell(40, 5, ' ', 1, 0, 'C', 0);
        //         $pdf->SetTextColor(0, 0, 0);
        //         $dataSchedule = '';
        //     }
        //     //$pdf->SetFillColor(0,0,0);


        //     $pdf->Ln($LINE_HEIGHT);
        //}
        //}

        //$pdf->Ln(6);

        ob_get_clean();
        $pdf->Output(convert_accented_characters(mb_convert_encoding('QUADRO DE HORÁRIO :: ' . $title,$this->encode)) . '.pdf', 'I');
        exit;
    }
}
