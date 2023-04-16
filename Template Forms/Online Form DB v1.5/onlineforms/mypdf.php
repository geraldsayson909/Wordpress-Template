<?php
require_once 'tcpdf/tcpdf.php';


class MYPDF extends TCPDF{
	public function createPdf($formname, $body, $file=null){
		//settings

		$l = Array();

		// PAGE META DESCRIPTORS --------------------------------------

		$l['a_meta_charset'] = 'UTF-8';
		$l['a_meta_dir'] = 'ltr';
		$l['a_meta_language'] = 'en';

		// TRANSLATIONS --------------------------------------
		$l['w_page'] = 'page';


		$this->SetCreator(PDF_CREATOR);
		$this->SetTitle($formname);
		$this->SetSubject($formname);
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);
		$this->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM);
		$this->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->setJPEGQuality(100);
		$this->setLanguageArray($l);

		$this->AddPage();
		$this->SetFont('helvetica', '', 15);
		$this->writeHTML($body, true, 0, true, 0);
		$this->Output($file, 'F');
	}
}
?>
