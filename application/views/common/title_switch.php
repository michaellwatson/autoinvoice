<?php 

if($formID == 13){

	if($tKey==0 && $tCategory==32){ ?>
	<div class="container">
		<a href="javascript:void(0)" class="clsDownloadReportPdf btn btn-primary"><img src="<?php echo base_url('assets/images/icons/pdf_icon.png'); ?>" /> Download SBA Report (no attachments)</a>
		<!-- <a href="<?php //echo base_url('Post/gen_pdf/1/'.$listing['ad_id'].'/pdf/?template=SBA');?>" class="btn btn-primary">Download Report</a> -->
		<a href="javascript:void(0)" class="btn btn-primary clsDownloadReportPdfWithAttachment"><img src="<?php echo base_url('assets/images/icons/pdf_icon.png'); ?>" /> Download SBA Report (Full)</a>

		<a href="javascript:void(0)" class="btn btn-primary clsLinkVersionControl">Version Control</a>
	</div>
<?php }

	else if($tKey==0 && $tCategory==56){
?>
	<div class="container">
		<a href="javascript:void(0)" class="clsDownloadFRAEWReportPdf btn btn-primary"><img src="<?php echo base_url('assets/images/icons/pdf_icon.png'); ?>" /> Download FRAEW Report (no attachments)</a>
		<a href="javascript:void(0)" class="btn btn-primary clsDownloadReportPdfWithAttachment"><img src="<?php echo base_url('assets/images/icons/pdf_icon.png'); ?>" /> Download FRAEW Report (Full)</a>
		<a href="javascript:void(0)" class="btn btn-primary clsLinkVersionControl">Version Control</a>

	</div>
<?php
	}
	else if($tKey==0 && $tCategory==14){

?>
		<div class="container">
			<a href="<?php echo base_url('Post/gen_pdf/1/'.$listing['ad_id'].'/pdf'); ?>" class="btn btn-primary">
				<img src="<?php echo base_url('assets/images/icons/pdf_icon.png'); ?>" /> Download Desktop Assessment Report
			</a>
		</div>
<?php
	}
}
?>

<div class="<?php echo (isset($_SESSION['two-col']) && (int)$_SESSION['two-col'] == 1) ? 'container-fluid' : 'container';?> solid headings" style="margin-bottom:15px;margin-top:20px;">
	<div class="col-sm-12 col-md-12 text-left">
		<h3><?php echo ucfirst($ad_name);?></h3> 
	</div>
</div>

<label class="headings-mobile"><?php echo strtoupper($ad_name);?></label> 