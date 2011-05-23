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
		 
		
		
		<!-- WINNERS -->
		<div class="wide_section">
			<img id="winner" src="templates/BuzzBee/images/bg_youWin.jpg" usemap="#winner" border="0" width="854" height="376" alt="" />
			<map id="winner" name="You Win">
			<area shape="rect" coords="251,125,555,203" href="code.php?code=<?php echo $c['Code']; ?>" alt="claim prize" title=""    />
			</map>
			<div class="text_winner_wrapper">
				<p class="text_winner">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Use the following confirmation code to claim your prize.
				<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Code will expire after (number) days </p>
				<p class="text_winner_no"><?php echo $c['Code']; ?></p>
				</div>
			</div>
		
		
			</div>
		
	<span style="clear:both"></span>
	<div class="section_middle">&nbsp;
		<div class="text_know"></div><span style="clear:both"></span>
		<div class="arrow3"></div><span style="clear:both"></span>
		<div class="arrow4"></div><span style="clear:both"></span>
		</div>
	
	
<?php include('foot.php'); ?>