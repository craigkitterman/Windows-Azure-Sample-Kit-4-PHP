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
?>

<div class="section2_wrapper">
		<div class="peep"></div>
		<div class="left_column2">
			<div class="text_hood"></div><span style="clear:both"></span>
			<div class="arrow5"></div><span style="clear:both"></span>
			<div class="userInput2">
				<p>Winning prizes is fun but for those of you Web developers out there, you might be curious be about what makes this site tick. This site is powered by Windows Azure for PHP. Check out the following resources to learn more about Windows Azure for PHP and get started developing your own site. </p>
				<div class="link_list">
					<ul><li><a href="#">item1</a></li>
					<li><a href="#">item2</a></li>
					<li><a href="#">item3</a></li>
					<li><a href="#">item4</a></li>
					</ul></div>
				</div>
				<div class="clear_both"></div>
				<div class="architect_title"></div>
				<div class="userInput3">
					<p>DotD is comprised of several pieces which fit together to create the overall experience (See diagram above).</p>
					<ul>
					<li>Visitors – Independent operators outside the application</li>
					<li>Storage – Portion of Windows Azure responsible for containing all data related to Tables, Queues, and Blobs.</li>
					<li>Web Roles – Point of interaction of the application with visitors. Web Roles provide a visual interface into the data in storage and basic application logic.</li>
					<li>Worker Roles – Worker roles sit in the background processing events, managing data, and provide load balancing for scale out. The diagram shows two Worker Roles, one for managing the applications “scalability” (adding/removing Web roles) and one for asynchronously processing some of the applications tasks in the background (another way to achieve scalability)</li>
					<li>Content Delivery Network (CDN) – Global content distribution that provides fast content delivery based on visitor location. </li>
					</ul>
					<p>Each of these parts is essential to the performance and scalability of DotD and will be discussed in more detail in later sections (Except the visitors!), but for now let’s take a quick look at why each piece was chosen for the architecture.</p>
					</div>
			</div>
		<div class="right_column2">
			<div class="image_diagram2"></div>
			</div>
		</div>
        </div>
</div>
</body>

</html>
