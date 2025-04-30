<?php

/**
 * PDF Helper Functions
 * 
 * Functions for creating and handling PDF documents
 */

if (!function_exists('generate_pdf')) {
    /**
     * Generate a PDF document
     * 
     * @param array $data Data for the PDF
     * @param string $template Template to use (optional)
     * @return TCPDF PDF object
     */
    function generate_pdf($data = [], $template = 'default')
    {
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('SIPANDA');
        $pdf->SetTitle($data['title'] ?? 'SIPANDA Report');
        $pdf->SetSubject($data['subject'] ?? 'SIPANDA Report');
        $pdf->SetKeywords($data['keywords'] ?? 'SIPANDA, Report, PDF');
        
        // Set default header data
        $pdf->SetHeaderData('', 0, $data['header_title'] ?? 'SIPANDA Report', $data['header_string'] ?? 'Generated on: ' . date('Y-m-d H:i:s'));
        
        // Set header and footer fonts
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        
        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // Add a page
        $pdf->AddPage();
        
        // Set default font
        $pdf->SetFont('dejavusans', '', 10);
        
        return $pdf;
    }
}

if (!function_exists('sentiment_analysis_pdf')) {
    /**
     * Generate a sentiment analysis PDF report
     * 
     * @param array $data Sentiment analysis data
     * @return string Base64 encoded PDF string
     */
    function sentiment_analysis_pdf($data)
    {
        // Prepare data for PDF
        $pdfData = [
            'title' => 'Sentiment Analysis Report',
            'subject' => 'Sentiment Analysis Report',
            'keywords' => 'Sentiment, Analysis, Report',
            'header_title' => 'Sentiment Analysis Report',
            'header_string' => 'Generated on: ' . date('Y-m-d H:i:s')
        ];
        
        // Create PDF
        $pdf = generate_pdf($pdfData);
        
        // Translate sentiment for display
        $sentimentDisplay = $data['sentiment'];
        if ($data['sentiment'] == 'positive') $sentimentDisplay = 'POSITIF';
        if ($data['sentiment'] == 'negative') $sentimentDisplay = 'NEGATIF';
        if ($data['sentiment'] == 'neutral') $sentimentDisplay = 'NETRAL';
        
        // Title
        $pdf->SetFont('dejavusans', 'B', 16);
        $pdf->Cell(0, 10, 'Sentiment Analysis Report', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Analysis text
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Analyzed Text:', 0, 1);
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->MultiCell(0, 10, $data['text'], 0, 'L');
        $pdf->Ln(5);
        
        // Sentiment result
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Sentiment Analysis Results:', 0, 1);
        
        // Sentiment scores
        $pdf->SetFont('dejavusans', '', 10);
        $pdf->Cell(50, 10, 'Sentiment:', 0, 0);
        $pdf->Cell(0, 10, $sentimentDisplay, 0, 1);
        $pdf->Cell(50, 10, 'Positive Score:', 0, 0);
        $pdf->Cell(0, 10, $data['positiveScore'] . '%', 0, 1);
        $pdf->Cell(50, 10, 'Negative Score:', 0, 0);
        $pdf->Cell(0, 10, $data['negativeScore'] . '%', 0, 1);
        $pdf->Ln(5);
        
        // Positive words
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Positive Words:', 0, 1);
        $pdf->SetFont('dejavusans', '', 10);
        
        if (!empty($data['positiveWords'])) {
            foreach ($data['positiveWords'] as $word) {
                $pdf->Cell(0, 8, '- ' . $word, 0, 1);
            }
        } else {
            $pdf->Cell(0, 8, 'No positive words found.', 0, 1);
        }
        $pdf->Ln(5);
        
        // Negative words
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell(0, 10, 'Negative Words:', 0, 1);
        $pdf->SetFont('dejavusans', '', 10);
        
        if (!empty($data['negativeWords'])) {
            foreach ($data['negativeWords'] as $word) {
                $pdf->Cell(0, 8, '- ' . $word, 0, 1);
            }
        } else {
            $pdf->Cell(0, 8, 'No negative words found.', 0, 1);
        }
        
        // Output PDF as string
        return $pdf->Output('sentiment_analysis.pdf', 'S');
    }
} 