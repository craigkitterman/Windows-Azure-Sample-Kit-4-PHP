<?php include('head.php'); ?>
		 
		
		
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