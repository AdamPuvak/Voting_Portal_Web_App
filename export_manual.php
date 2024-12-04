<?php
require_once 'tcpdf/tcpdf.php';

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Nastavení informací o dokumentu
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('Návod');
$pdf->SetSubject('Návod');
$pdf->SetKeywords('TCPDF, PDF, návod');

$pdf->SetFont('dejavusans', '', 12);
$pdf->AddPage();
$manual_content = '<h3>' . $lang["manual"] . '</h3>' . $_POST['content'];
$pdf->writeHTML($manual_content, true, false, true, false, '');
$filename = 'manual_' . date('YmdHis') . '.pdf';
$pdf->Output($filename, 'F');

echo $filename;
?>
