<?php include('head.php'); ?>
		 
		
		<div class="wide_section">
			<h2>New Product</h2>
                        <form enctype="multipart/form-data" action="" method="post">
                                <input type="hidden" name="PrevTitle" value="<?php echo @$prev_title;?>"/>
                                <b>Title:</b>&nbsp;&nbsp;<input type="text" size="30" name="Title" value="<?php echo @$p->Title;?>"/>
                                <br/><b>Num Products:</b> <input type="text" size="10" name="NumProducts" value="<?php echo @$p->NumProducts;?>" />
                                <br/><b>Start Date:</b> <input type="text" size="10" name="StartDate" /> - Format Example: May 15th 2011
                                <br/><b>End Date:</b> <input type="text" size="10" name="EndDate" /> - Format Example: May 22nd 2011
                                <br/><b>Valid Days:</b> <label><input type="checkbox" name="ValidDays[]" value="Mon"/> Mon</label> 
                                                        <label><input type="checkbox" name="ValidDays[]" value="Tue"/> Tue</label>
                                                        <label><input type="checkbox" name="ValidDays[]" value="Wed"/> Wed</label>
                                                        <label><input type="checkbox" name="ValidDays[]" value="Thu"/> Thurs</label>
                                                        <label><input type="checkbox" name="ValidDays[]" value="Fri"/> Fri</label>
                                <br/><b>Description:</b>
                                <br/><textarea name="Description"><?php echo @$p->Description;?></textarea>
                                <br/><input type="file" name="Image" />
                                <br/><input type="submit" name="Cancel" value="Cancel"/> <input type="submit" value="Add Product" />
                        </form>
                </div>
		
	<span style="clear:both"></span>
	<div class="section_middle">&nbsp;
		<div class="text_know"></div><span style="clear:both"></span>
		<div class="arrow3"></div><span style="clear:both"></span>
		<div class="arrow4"></div><span style="clear:both"></span>
		</div>
	
	
<?php include('foot.php'); ?>