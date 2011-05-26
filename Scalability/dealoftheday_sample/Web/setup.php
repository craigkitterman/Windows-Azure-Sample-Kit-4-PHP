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
 *
 * 
 * 
 * @todo Make setup smarter
 **/

require_once('include.php');
require_once('storage_integrity_check.php');

if(isset($_POST['Pause'])) {
    pause_site();
}

if(isset($_POST['Resume'])) {
    resume_site();
}

?>
<p>Your storage has been initialized and is ready to use</p>
<form action="" method="post">
    <input type="submit" name="Pause" value="Pause Site"/> <input type="submit" name="Resume" value="Resume Site"/>
</form>
<br/><br/><p><strong>Having this file public is dangerous! When you no longer need it please remove it from your deployment.</strong></p>