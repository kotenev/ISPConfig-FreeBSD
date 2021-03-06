<?php
/*
Copyright (c) 2005 - 2009, Till Brehm, projektfarm Gmbh
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.
    * Neither the name of ISPConfig nor the names of its contributors
      may be used to endorse or promote products derived from this software without
      specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/


/******************************************
* Begin Form configuration
******************************************/

$tform_def_file = "form/mail_ml_membership.tform.php";

/******************************************
* End Form configuration
******************************************/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

//* Check permissions for module
$app->auth->check_module_permissions('mail');

// Loading classes
$app->uses('tpl,tform,tform_actions');
$app->load('tform_actions');

class page_action extends tform_actions {


	function onShowNew() {
		global $app, $conf;

		//TODO: Add ml members limit
		// we will check only users, not admins
// 		if($_SESSION["s"]["user"]["typ"] == 'user') {
// 			if(!$app->tform->checkClientLimit('limit_ml_membership')) {
// 				$app->error($app->tform->wordbook["limit_ml_membership_txt"]);
// 			}
// 			if(!$app->tform->checkResellerLimit('limit_ml_membership')) {
// 				$app->error('Reseller: '.$app->tform->wordbook["limit_ml_membership_txt"]);
// 			}
// 		}

		parent::onShowNew();
	}

	function onShowEnd() {
		global $app, $conf;

		if($this->id > 0) {
			//* we are editing a existing record
			$app->tpl->setVar("edit_disabled", 1);


			$sql = "SELECT mailinglist_id, email FROM mail_ml_membership WHERE subscriber_id = ?";
			$mlID = $app->db->queryOneRecord($sql, $this->id);
			if($mlID['mailinglist_id']) $app->tpl->setVar("mailinglist_id_value", $mlID["mailinglist_id"]);
			if($mlID['email']) $app->tpl->setVar("email_value", $mlID["email"]);
		} else {
			$app->tpl->setVar("edit_disabled", 0);
		}

		parent::onShowEnd();
	}

	function onBeforeInsert() {
		global $app, $conf;

		// Set the server id of the mailinglist members = server ID of mail domain.
		$domain = $app->db->queryOneRecord("SELECT server_id FROM mail_mailinglist WHERE mailinglist_id = ?", $this->dataRecord["mailinglist_id"]);

		$this->dataRecord["server_id"] = $domain['server_id'];
	}

}

$app->tform_actions = new page_action;
$app->tform_actions->onLoad();

?>
