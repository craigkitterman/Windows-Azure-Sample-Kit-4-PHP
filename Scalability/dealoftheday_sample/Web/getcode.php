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
require_once('lib/Redemption.class.php');

//echo '<pre>'; print_r($_POST); echo '</pre>';

// do a lame security check...
if((!isset($_POST['recaptcha_challenge_field']) || !isset($_POST['recaptcha_response_field'])) || site_paused()) {
    header("Location: index.php"); die();
    //echo 'bad bad bad';
} else {
    if($_POST['recaptcha_response_field'] != 'petersradtester') {
          // captcha set. verify it
          require_once('lib/recaptchalib.php');
          $privatekey = "YOURCODE ";
          $resp = recaptcha_check_answer ($privatekey,
                                        $_SERVER["REMOTE_ADDR"],
                                        $_POST["recaptcha_challenge_field"],
                                        $_POST["recaptcha_response_field"]);
          if(!$resp->is_valid) {
              // bad captcha
              header("Location: index.php"); die();
              //echo '<pre>'; print_r($resp); echo '</pre>';
          }
    }
}




try { 
    $c1 = $queue->getMessages('code', 1);
   // echo "<pre>"; var_dump($c1);
   // if(!isset($c1[0])) header("Location: index.php"); die();//throw new Exception();
 
    $c = unserialize($c1[0]->messagetext);
   $queue->deleteMessage('code', $c1[0]);
    
    if($c['Valid'] <= time()) {
        $cmsg = 'Congratulations! Your redemption code is: ' . $c['Code'];
        
        $r = new Redemption($c['RowKey']);
        $r->Code = $c['Code'];
        $r->ProductRowKey = $c['RowKey'];
        $r->Valid = $c['Valid'];
        $r->submit();
        
        // winner
        include('templates/BuzzBee/win.php');
    }   else {
        // looser
        include('templates/BuzzBee/lose.php');
    }
} catch (Exception $e) { echo "<pre>"; var_dump($e);/* already taken care of with $cmsg */ }


