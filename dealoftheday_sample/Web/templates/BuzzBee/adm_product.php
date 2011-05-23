<?php
/**
 *    Copyright 2011 Microsoft Corporation
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * 
 * @category  Microsoft
 * @package   DealOfTheDay
 * @author    BuzzBee
 * @link http://www.buzzbee.biz/
 * @copyright 2011 Copyright Microsoft Corporation. All Rights Reserved
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 **/


include('head.php'); ?>
		 
		
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