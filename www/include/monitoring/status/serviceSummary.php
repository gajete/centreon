<?
/**
Oreon is developped with GPL Licence 2.0 :
http://www.gnu.org/licenses/gpl.txt
Developped by : Julien Mathis - Romain Le Merlus

The Software is provided to you AS IS and WITH ALL FAULTS.
OREON makes no representation and gives no warranty whatsoever,
whether express or implied, and without limitation, with regard to the quality,
safety, contents, performance, merchantability, non-infringement or suitability for
any particular or intended purpose of the Software found on the OREON web site.
In no event will OREON be liable for any direct, indirect, punitive, special,
incidental or consequential damages however they may arise and even if OREON has
been previously advised of the possibility of such damages.

For information : contact@oreon-project.org
*/

	if (!isset($oreon))
		exit();

	unset($TabLca);
	$TabLca = getLcaHostByName($pearDB);
	
	$h_data = array();
	$h_status = array();
	$svc_data = array();
	$tab_color = array(0=>"list_one", 1=>"list_two");

	$counter_host = 0;	
	foreach ($host_status as $key => $data){
		if ($oreon->user->admin || !$isRestreint || ($isRestreint && isset($TabLca["LcaHost"][$data["host_name"]]))){	
			$service_data_str = NULL;
			$h_data[$data["host_name"]] = "<a href='./oreon.php?p=201&o=hd&host_name=".$data["host_name"]."'>".$data["host_name"]."</a>";
			$h_status[$data["host_name"]]=array("current_state"=>$data["current_state"], "color"=>$oreon->optGen["color_".strtolower($data["current_state"])]);
			# define class
			isset($host_status[$data["host_name"]]) && $host_status[$data["host_name"]]["current_state"] == "DOWN" ? $h_class[$data["host_name"]] = "list_down" : $h_class[$data["host_name"]] = $tab_color[++$counter_host % 2];
			# defined service tab
			$tab_svc = array();
			if (isset($tab_host_service[$data["host_name"]]))
				foreach ($tab_host_service[$data["host_name"]] as $key_svc => $data_svc){
					if (!isset($tab_svc[$service_status[$data["host_name"]."_".$key_svc]["current_state"]]))				
						$tab_svc[$service_status[$data["host_name"]."_".$key_svc]["current_state"]] = 1;
					else
						$tab_svc[$service_status[$data["host_name"]."_".$key_svc]["current_state"]]++;
					$svc_data[$data["host_name"]] = $service_data_str;
				}
			if (isset($tab_svc["OK"]) && $tab_svc["OK"] != 0)
				$service_data_str = "<span style='background:".$oreon->optGen["color_ok"]."'>".$tab_svc["OK"]." <a href='./oreon.php?p=2020202&host_name=".$data["host_name"]."&status=OK'>OK</a></span> ";
			if (isset($tab_svc["WARNING"]) && $tab_svc["WARNING"] != 0)
				$service_data_str .= "<span style='background:".$oreon->optGen["color_warning"]."'>".$tab_svc["WARNING"]." <a href='./oreon.php?p=2020202&host_name=".$data["host_name"]."&o=svc_warning'>WARNING</a></span> ";
			if (isset($tab_svc["CRITICAL"]) && $tab_svc["CRITICAL"] != 0)
				$service_data_str .= "<span style='background:".$oreon->optGen["color_critical"]."'>".$tab_svc["CRITICAL"]." <a href='./oreon.php?p=2020202&host_name=".$data["host_name"]."&o=svc_critical'>CRITICAL</a></span> ";
			if (isset($tab_svc["PENDING"]) && $tab_svc["PENDING"] != 0)
				$service_data_str .= "<span style='background:".$oreon->optGen["color_pending"]."'>".$tab_svc["PENDING"]." <a href='./oreon.php?p=2020202&host_name=".$data["host_name"]."&o=svcpb'>PENDING</a></span> ";
			if (isset($tab_svc["UNKNOWN"]) && $tab_svc["UNKNOWN"] != 0)
				$service_data_str .= "<span style='background:".$oreon->optGen["color_unknown"]."'>".$tab_svc["UNKNOWN"]." <a href='./oreon.php?p=2020202&host_name=".$data["host_name"]."&o=svc_unknown'>UNKNOWN</a></span> ";
			$svc_data[$data["host_name"]] = $service_data_str;
		}
	}

	# Smarty template Init
	$tpl = new Smarty();
	$tpl = initSmartyTpl($path, $tpl, "/templates/");
	$tpl->assign("refresh", $oreon->optGen["oreon_refresh"]);
	$tpl->assign("p", $p);
	$tpl->assign("h_data", $h_data);
	$tpl->assign("h_class", $h_class);
	$tpl->assign("h_status", $h_status);
	$tpl->assign("svc_data", $svc_data);
	$tpl->assign("lang", $lang);
	$tpl->display("serviceSummary.ihtml");
?>