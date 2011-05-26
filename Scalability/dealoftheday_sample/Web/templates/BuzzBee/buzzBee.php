<?php include('head.php'); ?>
		
	
		<div class="giveaway_wrapper">
                        <div class="warning_img"></div>
			<div class="giveaway_image"><img src="<?php echo "http://az28935.vo.msecnd.net/product/" . $p->Title;//$p->Image;?>" alt="<?php echo $p->Title; ?>" width="415"/></div>
			<!-- 
			<div class="giveaway_image"><img src="images\give_mints.jpg" /></div>
			<div class="giveaway_image"><img src="images\give_kit.jpg" /></div>
			<div class="giveaway_image"><img src="images\give_screwDriver.jpg" /></div>
			-->
		</div>
			
		<div class="right_column">
		
			<!-- PLAY button and CAPCHA -->
			<form class="play_form" action="getcode.php" method="post"> 
			<input type="image" class="play_image" src="templates/BuzzBee/images/button_play.jpg" alt="Submit button">
			
			<div class="capcha_image">  
				<div id="recaptcha_widget">     
					<div id="recaptcha_image" style="padding-left:8px;"></div>    
					<div class="recaptcha_only_if_incorrect_sol" style="color:red">&nbsp;&nbsp;Incorrect please try again</div>     
					<span class="recaptcha_only_if_image">&nbsp;&nbsp;Enter the words above:</span>    
					<span class="recaptcha_only_if_audio">&nbsp;&nbsp;Enter the numbers you hear:</span>     
					<br/>&nbsp;&nbsp;<input type="text" id="recaptcha_response_field" name="recaptcha_response_field" />     
					<div><a href="javascript:Recaptcha.reload()"></a></div>
					<div class="capcha_button_wrapper"> 
						<a href="javascript:Recaptcha.switch_type('image')"><div class="capcha_refresh"></div></a>
						<a href="javascript:Recaptcha.switch_type('audio')"><div class="capcha_audio"></div></a>
						<a href="javascript:Recaptcha.showhelp()"><div class="capcha_help"></div></a>						
						</div>     
					<div class="capcha_text" target="_blank">Security check provided by <a href="http://www.google.com/recaptcha" target="_blank">reCAPTCHA</a>.</div>
				</div> 
			
				<script type="text/javascript" src="http://www.google.com/recaptcha/api/challenge?k=YOURCODE"></script>  
				<noscript><iframe src="http://www.google.com/recaptcha/api/noscript?k=YOURCODE" height="84" width="189" frameborder="0"></iframe><br />
				<textarea name="recaptcha_challenge_field" rows="3" cols="40"></textarea>    
				<input type="hidden" name="recaptcha_response_field" value="manual_challenge" />  
				</noscript>
				</div>
			</form>
			
			<div style="clear:both"></div>
			<div class="section_center">
				<div class="text_something"></div>
				<div class="text_azure"></div>
				<div class="arrow2"></div>  
				</div>
			
			
			<div class="comments_list_wrapper">(x) Comments | <a href="#" onclick="toggleComments()">Leave a Comment</a>
				<!-- input form for comments -->	
				<div class="comments_list form_bg" id="comment_form" style="display: none;">
					<form class="comment_form" action="" method="post">
						<div class="input_header">Leave a Comment</div>
						<div class="input_label">Name: </div>
						<div><input class="input_name" type="text" name="Name" /></div>
						<div class="input_label comment_label" >Comment:</div>
						<div><textarea  class="input_comments" name="Comment" rows="4" cols="50"></textarea>
						<span style="clear:both;"></span></div>
						<input type="hidden" name="DoYouLikeHoney" value="" />
						<input class="input_button" type="submit" value="Submit" />
						</form>
					</div>
					
				<!-- List of comments -->	
				<div class="comments_list" id="comment_list" style="display: block">
				                                  
                                    <?php 
                                            foreach($comments as $c) {                    
                                                    echo "\n<div class=\"acomment\" style=\"border-bottom: 1px dotted #000;\">";
                                                    echo "<span class=\"comment_author\">" . $c->Name . "</span>"; 
                                                    echo "<span class='comment_date'>" . $c->getTimestamp()->format('l, F Y, G:i') . "</span>";
                                                   // echo "<p class='comment'>" . $c->Text; . "</p>";
                                                    echo "<p class='comment'>" . $c->Text . "</p>";
                                                    if(isset($_SESSION['ValidUser'])) {
                                                            echo "<br/><a href='?action=delete_comment&p={$c->getPartitionKey()}&r={$c->getRowKey()}'>Delete Comment</a>";
                                                    }
                                                    echo "</div>";
                                            }
                                    ?>

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