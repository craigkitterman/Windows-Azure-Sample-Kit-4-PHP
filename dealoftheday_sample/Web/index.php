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
 * @author    Ben Lobaugh <a-beloba@microsoft.com>
 * @copyright 2011 Copyright Microsoft Corporation. All Rights Reserved
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 **/

require_once('include.php');
$comment_submitted = 0;
$default_product = false;



// If the site is paused there is not need to do any extra work
if(site_paused()) {
    // When does next game start?
    $timer->mark('start_next_game_time');
    if($queue->hasMessages('code')) {
        try {
            $game = $queue->peekMessages('code', 1);
            if(!isset($game[0])) throw new Exception();
            $game = unserialize($game[0]->messagetext);
            $next_game = date('l jS \of F Y h:i:s A', $game['Valid']);
             include('templates/BuzzBee/paused.php'); exit();
        } catch (Exception $e) { $next_game = "New game coming soon!"; }
    } else {
        //$next_game = "No currently scheduled games";
        include('templates/BuzzBee/paused.php'); exit();
    }
    $timer->mark('stop_next_game_time');
    
 
} else {
    // The site is not pause. Show everything
    $timer->mark('start_code_q_check');
    try { 
        // Get the current product on the queue
        $p = $queue->peekMessages('code', 1);
        if(!isset($p[0])) throw new Exception(); //var_dump(unserialize($p[0]->messagetext));
        $p = unserialize($p[0]->messagetext); //var_dump($p);
        $timer->mark('stop_code_q_check');
        $timer->mark('start_get_product');

        $p = $table->retrieveEntityById('Product', $p['PartitionKey'], $p['RowKey']);

    } catch (Exception $e) { //var_dump($e);
        $default_product = true;
            $p =  new Product();
            $p->Title = 'New Product Soon!';
            $p->Description = 'We are working hard to bring you a great new product. Check back soon!';
            $p->Image = 'gates.jpg';
            $p->setRowKey('default');
            pause_site(); // either there are no more products or the site is overwhelmed

    }

    $timer->mark('stop_get_product');

    // Does the user want to submit a new coment?
    if(isset($_POST['Name']) && isset($_POST['Comment']) && $_POST['DoYouLikeHoney'] == '' && $_POST['Name'] != '' && $_POST['Comment'] != '') {
           $timer->mark('start_add_comment');
        $c = new Comment($p->RowKey);
        $c->Name = $_POST['Name'];
        $c->Text = $_POST['Comment'];
        new WorkItem(WL_NEW_COMMENT, $c); 
        $comment_submitted = 1;
        $timer->mark('stop_add_comment'); 
        header("Location: index.php");
    }

    if(isset($_GET['action']) && $_GET['action'] == 'delete_comment') {
        $a = array('PartitionKey'=>$_GET['p'], 'RowKey'=>$_GET['r']);
        new WorkItem(WL_DEL_COMMENT, $a);
        header("Location: index.php");
    }

    $timer->mark('start_get_comments');
    $comments = $table->retrieveEntities('Comment', "PartitionKey eq '{$p->getRowKey()}'"); 
    $timer->mark('stop_get_comments');


    $timer->mark('start_template');
    include('templates/BuzzBee/buzzBee.php');
    $timer->mark('stop_template');
}

