<?php
namespace app\components;

use Yii;
use rudissaar\fpdf\FPDFPlus;

class MyFPDFPlus extends FPDFPlus
{
    // Page footer
    public function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','',10);
        $this->Cell(70, 10, Yii::t('app', 'Created with <{appName}>', ['appName' => Yii::$app->name]), 0, 0, 'L', false, 'https://www.diggin-data.de/cost-splitting');
        // Page number
        $this->Cell(50,10, Yii::t('app', 'Page {page} of {nb}', ['page' => $this->PageNo()]),0,0,'C');
        // Date
        $this->Cell(50,10, Yii::$app->formatter->asDateTime(time()), 0, 0, 'R');
    }

    // Colored table
    public function FancyTable($header, $data, $w)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(0,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
        // $w = array(40, 35, 40, 45);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],1,utf8_decode($header[$i]),1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        // $this->Cell(array_sum($w),0,'','T');
        // $this->Ln();
        $fill = true;
        foreach($data as $row)
        {
            $this->SetFont('','B');
            $this->Cell($w[0],6,utf8_decode($row[0]),'LR',0,'L',$fill);
            $this->SetFont('');
            $this->Cell($w[1],6,utf8_decode($row[1]),'LR',0,'L',$fill);
            // $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
            // $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
    }
}