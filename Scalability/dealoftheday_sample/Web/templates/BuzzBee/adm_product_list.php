<?php include('head.php'); ?>
		 
		
		<div class="wide_section">
			<br/>Next Code Queued for: <?php echo $next; ?>
                        <br/><br/><a href="adm_product.php">New Product</a>
                        <div  id="product_list">
                            <table border="1">
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Title</th>
                                    <th>NumProducts</th>
                                    <th>Actions</th>
                                </tr>
                                                        <?php
                                                                // Fetch list of entities
                                                                //$entities = $storageClient->retrieveEntities('contacts', 'Contact');

                                                                // List entities

                                                                foreach ($products as $entity) { 
                                                        ?>
                                                        <tr <?php if($entity->getPartitionKey() == $deal->Name && $entity->getRowKey() == $deal->Value) echo 'class="current_deal"'; ?>>
                                                            <td>Image</td>
                                                            <td><strong><?php echo $entity->Title; ?></strong></td>
                                                                <td><?php echo $entity->NumProducts; ?></td> 
                                                                <td>
                                                                <form method="get" action="adm_product.php">
                                                                        <input type="hidden" name="p" value="<?php echo $entity->getPartitionKey(); ?>" />
                                                                        <input type="hidden" name="r" value="<?php echo $entity->getRowKey(); ?>" />
                                                                        <!--<input name="action" type="submit" value="Delete" />--> <input name="action" type="submit" value="Edit" /> 
                                                                </form>
                                                                </td>
                                                        </tr>
                                                        <?php
                                                                }
                                                        ?>
                                                </table>
                        </div>
                </div>
		
	<span style="clear:both"></span>
	<div class="section_middle">&nbsp;
		<div class="text_know"></div><span style="clear:both"></span>
		<div class="arrow3"></div><span style="clear:both"></span>
		<div class="arrow4"></div><span style="clear:both"></span>
		</div>
	
	
<?php include('foot.php'); ?>