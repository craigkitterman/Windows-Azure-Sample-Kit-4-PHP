<div class="section2_wrapper">
		<div class="peep"></div>
		<div class="left_column2">
			<div class="text_hood"></div><span style="clear:both"></span>
			<div class="arrow5"></div><span style="clear:both"></span>
			<div class="userInput2">
				<p>Winning prizes is fun but for those of you Web developers out there, you might be curious be about what makes this site tick. This site is built with PHP and takes advantage of Windows Azure scalability. It means that when spikes of traffic happen, it automatically scales out by extending the number of “instances”. Check out the following resources to learn how we designed this site and get started developing your own site:</p>
				<div class="link_list">
					<ul>
  <li><a href="http://azurephp.interoperabilitybridges.com/articles/introduction-to-the-%E2%80%9Cdeal-of-the-day%E2%80%9D%E2%80%93a-php-sample-scaling-application">An introduction to the “Deal of the Day” – A PHP sample application</a></li>

  <li><a href="http://azurephp.interoperabilitybridges.com/articles/scaling-php-applications-on-windows-azure-part-i-performance-metrics">Understanding Performance Metrics</a></li>

  <li><a href="http://azurephp.interoperabilitybridges.com/articles/scaling-php-applications-on-windows-azure-part-ii-role-management">Role Management</a> </li>
</ul>
                                </div>
			</div>
				<div class="clear_both"></div>
				<div class="architect_title"></div>
				<div class="userInput3">
                                    <p>The “Deal of the Day” PHP Sample is comprised of several pieces which fit together to create the overall experience (See diagram above).</p>

                                    <ul>
                                      <li>Storage –responsible for containing all business data (product information &amp; images, comments) and monitoring data (diagnostic information). All data is stored in Windows Azure Tables, Queues, and Blobs. </li>

                                      <li>Web Roles – Point of interaction of the application with visitors. Number of active Web Roles varies depending on the load. They are all the same, running the core of the applications logic, producing the user interface (HTML) and handling user inputs. All Web Roles share the storage elements described above. </li>

                                      <li>Worker Roles – Worker roles sit in the background processing events, managing data, and provide load balancing for scale out. The diagram shows two Worker Roles, one for managing the applications “scalability” (adding/removing Web roles) and one for asynchronously processing some of the applications tasks in the background (another way to achieve scalability) </li>

                                      <li>Content Delivery Network (CDN) – Global content distribution that provides fast content delivery based on visitor location. </li>
                                    </ul>

                                    <p>You can take a look at the <a href="stats.php">status page</a> to get a few stats about the application.

                                    <br/>Each of these parts is essential to the performance and scalability of the “Deal of the Day” and to learn more, check out the <a href="http://azurephp.interoperabilitybridges.com/articles/introduction-to-the-%E2%80%9Cdeal-of-the-day%E2%80%9D%E2%80%93a-php-sample-scaling-application">Introduction to the “Deal of the Day” – A&#160; PHP scaling sample application</a></p>

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
