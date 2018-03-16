<?php
error_reporting(E_ERROR);
error_reporting(E_ALL);
ini_set('report_errors','on');

require_once("../ajax/dbfuncs.php");

$db = new dbfuncs();

if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "";
$google_id = isset($_SESSION['google_id']) ? $_SESSION['google_id'] : "";


$id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : "";
$p = isset($_REQUEST["p"]) ? $_REQUEST["p"] : "";

if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
}

 if ($p=="saveRun"){
	$project_pipeline_id = $_REQUEST['project_pipeline_id'];
	$profileType = $_REQUEST['profileType'];
	$profileId = $_REQUEST['profileId'];
	$amazon_cre_id = $_REQUEST['amazon_cre_id'];
	$nextTextRaw = $_REQUEST['nextText'];
    $nextText = urldecode($nextTextRaw);
	$configTextRaw = $_REQUEST['configText'];
    $configText = urldecode($configTextRaw);
    $status = "init";
    //create file and folders
    $log_array = $db ->initRun($project_pipeline_id, $configText, $nextText, $profileType, $profileId, $amazon_cre_id, $ownerID);
    //run the script
    $data = $db->runCmd($project_pipeline_id, $profileType, $profileId, $log_array, $ownerID);
    //add run into run table
    //check if $project_pipeline_id already exits
    $checkRun = $db->getRun($project_pipeline_id,$ownerID);
    $checkarray = json_decode($checkRun,true); 
    $ppId = $checkarray[0]["project_pipeline_id"];
    $attempt = $checkarray[0]["attempt"];
    settype($attempt, 'integer');
    if (empty($attempt)){
        $attempt = 0;
    }
    $attempt = $attempt +1;
    if (!empty($ppId)) {
        $db->updateRunAttempt($project_pipeline_id, $attempt, $ownerID);    
        $db->updateRunStatus($project_pipeline_id, $status, $ownerID);    
        $db->insertRunLog($project_pipeline_id, $status, $ownerID);
        
    } else {
        $db->insertRun($project_pipeline_id, $status, "1", $ownerID);
        $db->insertRunLog($project_pipeline_id, $status, $ownerID);
    }

}
else if ($p=="getServerLog"){
	$project_pipeline_id = $_REQUEST['project_pipeline_id'];
    $data = $db -> getServerLog($project_pipeline_id,$ownerID);
}
else if ($p=="getNextflowLog"){
	$project_pipeline_id = $_REQUEST['project_pipeline_id'];
    $profileType = $_REQUEST['profileType'];
	$profileId = $_REQUEST['profileId'];
    if ($profileType == 'local') {
    $data = $db -> getNextflowLog($project_pipeline_id,$profileType,"","");
    } else if ($profileType == 'cluster' || $profileType == 'amazon') {
    $data = $db -> getNextflowLog($project_pipeline_id,$profileType,$profileId,$ownerID);
    }
}
else if ($p=="getRun"){
	$project_pipeline_id = $_REQUEST['project_pipeline_id'];
    $data = $db -> getRun($project_pipeline_id,$ownerID);
}

else if ($p=="checkRunPid"){
	$pid = $_REQUEST['pid'];
	$profileType = $_REQUEST['profileType'];
	$profileId = $_REQUEST['profileId'];
    if ($profileType == 'local') {
    $data = $db -> checkRunPid($pid,$profileType,"","");
    } else if ($profileType == 'cluster') {
    $data = $db -> checkRunPid($pid,$profileType,$profileId,$ownerID);
    }
}
else if ($p=="updateRunPid"){
	$pid = $_REQUEST['pid'];
	$project_pipeline_id = $_REQUEST['project_pipeline_id'];
    $data = $db -> updateRunPid($project_pipeline_id, $pid, $ownerID);
}
else if ($p=="updateRunStatus"){
	$project_pipeline_id = $_REQUEST['project_pipeline_id'];
	$run_status = $_REQUEST['run_status'];
	$duration = $_REQUEST['duration'];
    $db -> updateRunLog($project_pipeline_id, $run_status, $duration, $ownerID);
    $data = $db -> updateRunStatus($project_pipeline_id, $run_status, $ownerID);
}
else if ($p=="getRunStatus"){
	$project_pipeline_id = $_REQUEST['project_pipeline_id'];
    $data = $db -> getRunStatus($project_pipeline_id, $ownerID);
}
else if ($p=="startProAmazon"){
	$nodes = $_REQUEST['nodes'];
	$autoscale_check = $_REQUEST['autoscale_check'];
	$autoscale_maxIns = $_REQUEST['autoscale_maxIns'];
    $db -> updateProfileAmazonNode($id,$nodes,$autoscale_check,$autoscale_maxIns,$ownerID);
    $data = $db -> startProAmazon($id,$ownerID);
}
else if ($p=="stopProAmazon"){
    $data = $db -> stopProAmazon($id,$ownerID);
}
else if ($p=="checkAmzStopLog"){
    $data = $db -> checkAmzStopLog($id,$ownerID);
}
else if ($p=="checkAmazonStatus"){
	$profileId = $_REQUEST['profileId'];
    $data = $db -> checkAmazonStatus($profileId,$ownerID);
}
else if ($p=="runAmazonCloudCheck"){
	$profileId = $_REQUEST['profileId'];
    $data = $db -> runAmazonCloudCheck($profileId,$ownerID);
}
else if ($p=="getAllParameters"){
    $data = $db -> getAllParameters($ownerID);
}
else if ($p=="getEditDelParameters"){
    $data = $db -> getEditDelParameters($ownerID);
}
else if ($p=="savefeedback"){
	$email = $_REQUEST['email'];
	$message = $_REQUEST['message'];
	$url = $_REQUEST['url'];
    $data = $db -> savefeedback($email,$message,$url);
}

else if ($p=="getAllGroups"){
    $data = $db -> getAllGroups();
}
else if ($p=="getJoinGroups"){
    $data = $db -> getJoinGroups($ownerID);
}
else if ($p=="viewGroupMembers"){
	$g_id = $_REQUEST['g_id'];
    $data = $db -> viewGroupMembers($g_id);
}
else if ($p=="getMemberAdd"){
	$g_id = $_REQUEST['g_id'];
    $data = $db -> getMemberAdd($g_id);
}
else if ($p=="getProjects"){
    $data = $db -> getProjects($id,$ownerID);
}
else if ($p=="getGroups"){
    $data = $db -> getGroups($id,$ownerID);
}
else if ($p=="getUserGroups"){
    $data = $db -> getUserGroups($ownerID);
}
else if ($p=="getUserRole"){
    $data = $db -> getUserRole($ownerID);
}
else if ($p=="getExistProjectPipelines"){
    $pipeline_id = $_REQUEST['pipeline_id'];
    $data = $db -> getExistProjectPipelines($pipeline_id,$ownerID);
}
else if ($p=="getProjectPipelines"){
    $project_id = $_REQUEST['project_id'];
    $data = $db -> getProjectPipelines($id,$project_id,$ownerID);
}
else if ($p=="getProjectInputs"){
    $project_id = $_REQUEST['project_id'];
    $data = $db -> getProjectInputs($project_id,$ownerID);
}
else if ($p=="getProjectInput"){
    $data = $db -> getProjectInput($id,$ownerID);
}
else if ($p=="getProjectPipelineInputs"){
    $g_num = $_REQUEST['g_num'];
//    settype($g_num, 'integer');
    $project_pipeline_id = $_REQUEST['project_pipeline_id'];
    if (!empty($id)) {
    $data = $db->getProjectPipelineInputsById($id,$ownerID);
        
    } else {
    $data = $db->getProjectPipelineInputs($g_num, $project_pipeline_id,$ownerID);
    }
}
else if ($p=="getInputs"){
    $data = $db -> getInputs($id,$ownerID);
}
else if ($p=="getAllProcessGroups"){
    $data = $db -> getAllProcessGroups($ownerID);
}
else if ($p=="getEditDelProcessGroups"){
    $data = $db -> getEditDelProcessGroups($ownerID);
}
else if ($p=="removeParameter"){
    $db->removeProcessParameterByParameterID($id);
    $data = $db->removeParameter($id);
}
else if ($p=="removeProcessGroup"){
    $db->removeProcessParameterByProcessGroupID($id);
    $db->removeProcessByProcessGroupID($id);
    $data = $db->removeProcessGroup($id);
}
else if ($p=="removePipelineById"){   
	$data = $db -> removePipelineById($id);
}
else if ($p=="removeProcess"){   
    $db->removeProcessParameterByProcessID($id);
	$data = $db -> removeProcess($id);
}
else if ($p=="removeProject"){   
    $db -> removeProjectPipelineInputbyProjectID($id);
    $db -> removeProjectPipelinebyProjectID($id);
    $db -> removeProjectInputbyProjectID($id);
    $data = $db -> removeProject($id);
}
else if ($p=="removeGroup"){   
    $db -> removeUserGroup($id);
    $data = $db -> removeGroup($id);
}
else if ($p=="removeProjectPipeline"){  
    $db -> removeProjectPipelineInputByPipe($id);
    $data = $db -> removeProjectPipeline($id);
}
else if ($p=="removeProjectPipelineInputByGnum"){  
    $g_num = $_REQUEST['g_num'];
    $data = $db -> removeProjectPipelineInputByGnum($id,$g_num);
}
else if ($p=="removeProjectInput"){   
    $input_id = $_REQUEST['input_id'];
    $db -> removeProjectPipelineInputbyInputId($input_id);
    $db -> removeProjectInput($id);
    $data = $db -> removeInput($input_id);
}
else if ($p=="removeInput"){   
    $data = $db -> removeInput($id);
}
else if ($p=="removeProLocal"){   
    $data = $db -> removeProLocal($id);
}
else if ($p=="removeProCluster"){  
    $db->delKey($id, "clu", $ownerID);
    $data = $db -> removeProCluster($id);
}
else if ($p=="removeProAmazon"){   
    $db->delKey($id, "amz_pri", $ownerID);
    $db->delKey($id, "amz_pub", $ownerID);
    $data = $db -> removeProAmazon($id);
}
else if ($p=="removeProjectPipelineInput"){   
    $data = $db -> removeProjectPipelineInput($id);
}
else if ($p=="removeProcessParameter"){   
	$data = $db -> removeProcessParameter($id);
}
else if ($p=="saveParameter"){
    $name = $_REQUEST['name'];
    $qualifier = $_REQUEST['qualifier'];
    $file_type = $_REQUEST['file_type'];
    if (!empty($id)) {
       $data = $db->updateParameter($id, $name, $qualifier, $file_type, $ownerID);
    } else {
       $data = $db->insertParameter($name, $qualifier, $file_type, $ownerID);
    }
}
else if ($p=="getAmz")
{
    if (!empty($id)) {
    $data = json_decode($db->getAmzbyID($id, $ownerID));
    foreach($data as $d){
		$access = $d->amz_acc_key;
        $d->amz_acc_key = trim($db->amazonDecode($access));
		$secret = $d->amz_suc_key;
		$d->amz_suc_key = trim($db->amazonDecode($secret));
	}
	$data=json_encode($data);
    } else {
    $data = $db->getAmz($ownerID);
    }
}
else if ($p=="getSSH")
{
    if (!empty($id)) {
    $data = json_decode($db->getSSHbyID($id, $ownerID));
    foreach($data as $d){
        $d->prikey = $db->readKey($id, 'ssh_pri', $ownerID);
        $d->pubkey = $db->readKey($id, 'ssh_pub', $ownerID);
	}
	$data=json_encode($data);
    } else {
    $data = $db->getSSH($ownerID);
    }
}
else if ($p=="removeSSH")
{
    $db->delKey($id, "ssh_pri", $ownerID);
    $db->delKey($id, "ssh_pub", $ownerID);
    $data = $db->removeSSH($id);
}
else if ($p=="removeAmz")
{
    $data = $db->removeAmz($id);
}
else if ($p=="generateKeys")
{
    $data = $db->generateKeys($ownerID);
}
else if ($p=="readGenerateKeys")
{
    $data = $db->readGenerateKeys($ownerID);
}
else if ($p=="getProfileLocal")
{
//    if (!empty($id)) {
//    $data = $db->getProfileLocalbyID($id, $ownerID);
//    } else {
//    $data = $db->getProfileLocal($ownerID);
//    }
}
else if ($p=="getProfileCluster")
{
    if (!empty($id)) {
        $data = $db->getProfileClusterbyID($id, $ownerID);
    } else {
    $data = $db->getProfileCluster($ownerID);
    }
}
else if ($p=="getProfileAmazon")
{
    if (!empty($id)) {
    $data = $db->getProfileAmazonbyID($id, $ownerID);
    } else {
    $data = $db->getProfileAmazon($ownerID);
    }
}
else if ($p=="updateAmazonProStatus"){
    $status = $_REQUEST['status'];
    $data = $db->updateAmazonProStatus($id, $status, $ownerID);
}
//else if ($p=="saveProfileLocal"){
//    $name = $_REQUEST['name'];
//    $executor = $_REQUEST['executor'];
//    $next_path = $_REQUEST['next_path'];
//    $cmd = $_REQUEST['cmd'];
//    $next_memory = $_REQUEST['next_memory'];
//    $next_queue = $_REQUEST['next_queue'];
//    $next_time = $_REQUEST['next_time'];
//    $next_cpu = $_REQUEST['next_cpu'];
//    $executor_job = $_REQUEST['executor_job'];
//    $job_memory = $_REQUEST['job_memory'];
//    $job_queue = $_REQUEST['job_queue'];
//    $job_time = $_REQUEST['job_time'];
//    $job_cpu = $_REQUEST['job_cpu'];
//    if (!empty($id)) {
//       $data = $db->updateProfileLocal($id, $name, $executor,$next_path, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $ownerID);
//    } else {
//       $data = $db->insertProfileLocal($name, $executor,$next_path, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $ownerID);
//    }
//}
else if ($p=="saveSSHKeys"){
    $name = $_REQUEST['name'];
    $check_userkey = $_REQUEST['check_userkey'];
    $check_ourkey = $_REQUEST['check_ourkey'];
    $prikeyRaw = $_REQUEST['prikey'];
    $pubkeyRaw = $_REQUEST['pubkey'];
    $prikey = urldecode($prikeyRaw);
    $pubkey = urldecode($pubkeyRaw);
    
    if (!empty($id)) {
       $data = $db->updateSSH($id, $name, $check_userkey,$check_ourkey, $ownerID);
       $db->insertKey($id, $prikey, "ssh_pri", $ownerID);
       $db->insertKey($id, $pubkey, "ssh_pub", $ownerID);
    } else {
       $data = $db->insertSSH($name, $check_userkey,$check_ourkey, $ownerID);
       $idArray = json_decode($data,true);
       $id = $idArray["id"];
       $db->insertKey($id, $prikey, "ssh_pri", $ownerID);
       $db->insertKey($id, $pubkey, "ssh_pub", $ownerID);
    }
}
else if ($p=="saveAmzKeys"){
    $name = $_REQUEST['name'];
    $amz_def_reg = $_REQUEST['amz_def_reg'];
    $amz_acc_keyRaw = $_REQUEST['amz_acc_key'];
    $amz_suc_keyRaw = $_REQUEST['amz_suc_key'];
    $amz_acc_key = $db->amazonEncode($amz_acc_keyRaw);
    $amz_suc_key = $db->amazonEncode($amz_suc_keyRaw);
    if (!empty($id)) {
       $data = $db->updateAmz($id, $name, $amz_def_reg,$amz_acc_key,$amz_suc_key, $ownerID);
    } else {
       $data = $db->insertAmz($name, $amz_def_reg,$amz_acc_key,$amz_suc_key, $ownerID);
    }
}
else if ($p=="saveProfileCluster"){
    $name = $_REQUEST['name'];
    $executor = $_REQUEST['executor'];
    $cmd = $_REQUEST['cmd'];
    $next_memory = $_REQUEST['next_memory'];
    $next_queue = $_REQUEST['next_queue'];
    $next_time = $_REQUEST['next_time'];
    $next_cpu = $_REQUEST['next_cpu'];
    $executor_job = $_REQUEST['executor_job'];
    $job_memory = $_REQUEST['job_memory'];
    $job_queue = $_REQUEST['job_queue'];
    $job_time = $_REQUEST['job_time'];
    $job_cpu = $_REQUEST['job_cpu'];
    $username = $_REQUEST['username'];
    $hostname = $_REQUEST['hostname'];
    $next_path = $_REQUEST['next_path'];
    $ssh_id = $_REQUEST['ssh_id'];
    if (!empty($id)) {
       $data = $db->updateProfileCluster($id, $name, $executor,$next_path, $username, $hostname, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $ssh_id, $ownerID);
    } else {
       $data = $db->insertProfileCluster($name, $executor,$next_path, $username, $hostname, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $ssh_id, $ownerID);
    }
}
else if ($p=="saveProfileAmazon"){
    $name = $_REQUEST['name'];
    $executor = $_REQUEST['executor'];
    $cmd = $_REQUEST['cmd'];
    $next_memory = $_REQUEST['next_memory'];
    $next_queue = $_REQUEST['next_queue'];
    $next_time = $_REQUEST['next_time'];
    $next_cpu = $_REQUEST['next_cpu'];
    $executor_job = $_REQUEST['executor_job'];
    $job_memory = $_REQUEST['job_memory'];
    $job_queue = $_REQUEST['job_queue'];
    $job_time = $_REQUEST['job_time'];
    $job_cpu = $_REQUEST['job_cpu'];
    $ins_type = $_REQUEST['ins_type'];
    $image_id = $_REQUEST['image_id'];
    $subnet_id = $_REQUEST['subnet_id'];
    $shared_storage_id = $_REQUEST['shared_storage_id'];
    $shared_storage_mnt = $_REQUEST['shared_storage_mnt'];
    $next_path = $_REQUEST['next_path'];
    $ssh_id = $_REQUEST['ssh_id'];
    $amazon_cre_id = $_REQUEST['amazon_cre_id'];
    if (!empty($id)) {
       $data = $db->updateProfileAmazon($id, $name, $executor, $next_path, $ins_type, $image_id, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $subnet_id, $shared_storage_id,$shared_storage_mnt, $ssh_id, $amazon_cre_id, $ownerID);
    } else {
       $data = $db->insertProfileAmazon($name, $executor, $next_path, $ins_type, $image_id, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $subnet_id, $shared_storage_id,$shared_storage_mnt, $ssh_id, $amazon_cre_id, $ownerID);
    }
}
else if ($p=="saveInput"){
    $name = $_REQUEST['name'];
    if (!empty($id)) {
       $data = $db->updateInput($id, $name, $ownerID);
    } else {
       $data = $db->insertInput($name, $ownerID);
    }
}
else if ($p=="saveProPipeInput"){
    $input_id = $_REQUEST['input_id'];
    $project_id = $_REQUEST['project_id'];
    $pipeline_id = $_REQUEST['pipeline_id'];
    $project_pipeline_id = $_REQUEST['project_pipeline_id'];
    $g_num = $_REQUEST['g_num'];
    $given_name = $_REQUEST['given_name'];
    $qualifier = $_REQUEST['qualifier'];
    if (!empty($id)) {
       $data = $db->updateProPipeInput($id, $project_pipeline_id, $input_id, $project_id, $pipeline_id, $g_num, $given_name,$qualifier, $ownerID);
    } else {
       $data = $db->insertProPipeInput($project_pipeline_id, $input_id, $project_id, $pipeline_id, $g_num, $given_name,$qualifier, $ownerID);
    }
}
else if ($p=="saveProjectInput"){
    $input_id = $_REQUEST['input_id'];
    $project_id = $_REQUEST['project_id'];
    $data = $db->insertProjectInput($project_id, $input_id, $ownerID);
}
else if ($p=="saveUser"){
    $google_id = $_REQUEST['google_id'];
    $name = $_REQUEST['name'];
    $email = $_REQUEST['email'];
    $google_image = $_REQUEST['google_image'];
    $username = $_REQUEST['username'];
    //check if Google ID already exits
    $checkUser = $db->getUser($google_id);
    $checkarray = json_decode($checkUser,true); 
    if (!empty($checkarray)){
        $id = $checkarray[0]["id"];
    } else {
        $id = "";
    }
    if (!empty($id)) {
        $data = $db->updateUser($id, $google_id, $name, $email, $google_image, $username);    
    } else {
        $data = $db->insertUser($google_id, $name, $email, $google_image, $username);  
    }
}
else if ($p=="checkLogin"){
    if (!empty($google_id)) {
       $data = $db->getUserLess($google_id);
    }else {
       $_SESSION['ownerID'] = "";
       $_SESSION['username'] = "";
       $_SESSION['google_id'] = "";
	   $errAr = array('error' => 1);
	   $data = json_encode($errAr);
    }
}
else if ($p=="saveProcessGroup"){
    $group_name = $_REQUEST['group_name'];
    if (!empty($id)) {
       $data = $db->updateProcessGroup($id, $group_name, $ownerID);
    } else {
       $data = $db->insertProcessGroup($group_name, $ownerID);
    }
}
else if ($p=="saveProcess"){
    $name = $_REQUEST['name'];
    $process_gid = $_REQUEST['process_gid'];
    $summary = $_REQUEST['summary'];
    $process_group_id = $_REQUEST['process_group_id'];
    $script = htmlspecialchars(urldecode($_REQUEST['script']), ENT_QUOTES);
    $script_header = htmlspecialchars(urldecode($_REQUEST['script_header']), ENT_QUOTES);
    $script_mode = $_REQUEST['script_mode'];
    $script_mode_header = $_REQUEST['script_mode_header'];
    $rev_id = $_REQUEST['rev_id']; 
    $rev_comment = $_REQUEST['rev_comment']; 
    $group_id = $_REQUEST['group']; 
    $perms = $_REQUEST['perms']; 
    $publish = $_REQUEST['publish']; 
    settype($rev_id, 'integer');
    settype($group_id, 'integer');
    settype($process_gid, "integer");
    if (!empty($id)) {
        $data = $db->updateProcess($id, $name, $process_gid, $summary, $process_group_id, $script, $script_header, $group_id, $perms, $publish, $script_mode, $script_mode_header, $ownerID);
        if ($perms !== "3"){
            $db->updateProcessGroupGroupPerm($id, $group_id, $perms, $ownerID);
        }
    } else {
        $data = $db->insertProcess($name, $process_gid, $summary, $process_group_id, $script, $script_header, $rev_id, $rev_comment, $group_id, $perms, $publish, $script_mode, $script_mode_header, $ownerID);
        if ($perms !== "3"){
            $obj = json_decode($data,true);
            $id = $obj["id"];
            $db->updateProcessGroupGroupPerm($id, $group_id, $perms, $ownerID);
        }
    }
}
else if ($p=="saveProject"){
    $name = $_REQUEST['name'];
    $summary = $_REQUEST['summary'];
    if (!empty($id)) {
        $data = $db->updateProject($id, $name, $summary, $ownerID);
    } else {
        $data = $db->insertProject($name, $summary, $ownerID);
    }
}
else if ($p=="saveGroup"){
    $name = $_REQUEST['name'];
    $data = $db->insertGroup($name, $ownerID);
    $idArray = json_decode($data,true);
    $g_id = $idArray["id"];
    $db->insertUserGroup($g_id, $ownerID, $ownerID);
}
else if ($p=="saveUserGroup"){
    $u_id = $_REQUEST['u_id'];
    $g_id = $_REQUEST['g_id'];
    $data = $db->insertUserGroup($g_id, $u_id, $ownerID);
}
else if ($p=="duplicateProjectPipelineInput"){
    $new_id = $_REQUEST['new_id'];
    $old_id = $_REQUEST['old_id'];
    $data = $db->duplicateProjectPipelineInput($new_id, $old_id, $ownerID);
}
else if ($p=="duplicateProcess"){
    $new_process_gid = $_REQUEST['process_gid'];
    $new_name = $_REQUEST['name'];
    $old_id = $_REQUEST['id'];
    $data = $db->duplicateProcess($new_process_gid, $new_name, $old_id, $ownerID);
    $idArray = json_decode($data,true);
    $new_pro_id = $idArray["id"];
    $db->duplicateProcessParameter($new_pro_id, $old_id, $ownerID);
}
else if ($p=="createProcessRev"){
    $rev_comment = $_REQUEST['rev_comment'];
    $rev_id = $_REQUEST['rev_id'];
    $new_process_gid = $_REQUEST['process_gid'];
    $old_id = $_REQUEST['id'];
    $data = $db->createProcessRev($new_process_gid, $rev_comment, $rev_id, $old_id, $ownerID);
    $idArray = json_decode($data,true);
    $new_pro_id = $idArray["id"];
    $db->duplicateProcessParameter($new_pro_id, $old_id, $ownerID);
}
else if ($p=="saveProjectPipeline"){
    $pipeline_id = $_REQUEST['pipeline_id'];
    $project_id = $_REQUEST['project_id'];
    $name = $_REQUEST['name'];
    $summary = $_REQUEST['summary'];
    $output_dir = $_REQUEST['output_dir'];
    $publish_dir = $_REQUEST['publish_dir'];
    $publish_dir_check = $_REQUEST['publish_dir_check'];
    $perms = $_REQUEST['perms'];
    $profile = $_REQUEST['profile'];
    $interdel = $_REQUEST['interdel'];
    $cmd = urldecode($_REQUEST['cmd']);
    $group_id = $_REQUEST['group_id'];
    $exec_each = $_REQUEST['exec_each'];
    $exec_all = $_REQUEST['exec_all'];
    $exec_all_settings = $_REQUEST['exec_all_settings'];
    $exec_each_settings = $_REQUEST['exec_each_settings'];
    $exec_next_settings = $_REQUEST['exec_next_settings'];
    $docker_check = $_REQUEST['docker_check'];
    $docker_img = $_REQUEST['docker_img'];
    $docker_opt = $_REQUEST['docker_opt'];
    $singu_check = $_REQUEST['singu_check'];
    $singu_img = $_REQUEST['singu_img'];
    $singu_opt = $_REQUEST['singu_opt'];
    $amazon_cre_id = $_REQUEST['amazon_cre_id'];
    settype($group_id, 'integer');
    settype($amazon_cre_id, 'integer');
        if (!empty($id)) {
        $data = $db->updateProjectPipeline($id, $name, $summary, $output_dir, $perms, $profile, $interdel, $cmd, $group_id, $exec_each, $exec_all, $exec_all_settings, $exec_each_settings, $docker_check, $docker_img, $singu_check, $singu_img, $exec_next_settings, $docker_opt, $singu_opt, $amazon_cre_id, $publish_dir, $publish_dir_check, $ownerID);
            if ($perms !== "3"){
            $db->updateProjectGroupPerm($id, $group_id, $perms, $ownerID);
            $db->updateProjectInputGroupPerm($id, $group_id, $perms, $ownerID);
            $db->updateProjectPipelineInputGroupPerm($id, $group_id, $perms, $ownerID);
            $db->updateInputGroupPerm($id, $group_id, $perms, $ownerID);
            $db->updatePipelineGroupPerm($id, $group_id, $perms, $ownerID);
            $db->updatePipelineProcessGroupPerm($id, $group_id, $perms, $ownerID);
            }
    } else {
        $data = $db->insertProjectPipeline($name, $project_id, $pipeline_id, $summary, $output_dir, $profile, $interdel, $cmd, $exec_each, $exec_all, $exec_all_settings, $exec_each_settings, $docker_check, $docker_img, $singu_check, $singu_img, $exec_next_settings, $docker_opt, $singu_opt, $amazon_cre_id, $publish_dir, $publish_dir_check, $ownerID);
    }
}
else if ($p=="saveProcessParameter"){
    $sname = htmlspecialchars(urldecode($_REQUEST['sname']), ENT_QUOTES);
    $closure = htmlspecialchars(urldecode($_REQUEST['closure']), ENT_QUOTES);
    $reg_ex = htmlspecialchars(urldecode($_REQUEST['reg_ex']), ENT_QUOTES);
    $operator = $_REQUEST['operator'];
    $process_id = $_REQUEST['process_id'];
    $parameter_id = $_REQUEST['parameter_id'];
    $type = $_REQUEST['type'];
    $perms = $_REQUEST['perms'];
    $group_id= $_REQUEST['group'];
    settype($group_id, 'integer');
    if (!empty($id)) {
        $data = $db->updateProcessParameter($id, $sname, $process_id, $parameter_id, $type, $closure, $operator, $reg_ex, $perms, $group_id, $ownerID);
        if ($perms !== "3"){
            $db->updateParameterGroupPermById($parameter_id, $group_id, $perms, $ownerID);
        }
    } else {
        $data = $db->insertProcessParameter($sname, $process_id, $parameter_id, $type, $closure, $operator, $reg_ex, $perms, $group_id, $ownerID);
        if ($perms !== "3"){
            $db->updateParameterGroupPermById($parameter_id, $group_id, $perms, $ownerID);
        }
    }
}
else if ($p=="getProcessData")
{
    if (isset($_REQUEST['process_id'])) {
        $process_id = $_REQUEST['process_id'];
        $data = $db->getProcessDataById($process_id, $ownerID);
    }else {
        $data = $db->getProcessData($ownerID);
    }
}
else if ($p=="getProcessRevision")
{
	$id = $_REQUEST['process_id'];
    $process_gidAr =$db->getProcessGID($id);
    $checkarray = json_decode($process_gidAr,true); 
    $process_gid = $checkarray[0]["process_gid"];
    $data = $db->getProcessRevision($process_gid,$ownerID);
}
else if ($p=="getPipelineRevision")
{
	$pipeline_id = $_REQUEST['pipeline_id'];
    $pipeline_gid = json_decode($db->getPipelineGID($pipeline_id))[0]->{'pipeline_gid'};
    $data = $db->getPipelineRevision($pipeline_gid,$ownerID);
}
else if ($p=="getPublicPipelines")
{
    $data = $db->getPublicPipelines($ownerID);
}
else if ($p=="checkPipeline")
{
	$process_id = $_REQUEST['process_id'];
    $data = $db->checkPipeline($process_id, $ownerID);
}
else if ($p=="checkPipelinePublic")
{
	$process_id = $_REQUEST['process_id'];
    $data = $db->checkPipelinePublic($process_id, $ownerID);
}
else if ($p=="checkPipelinePerm")
{
	$process_id = $_REQUEST['process_id'];
    $data = $db->checkPipelinePerm($process_id, $ownerID);
}
else if ($p=="checkProjectPipePerm")
{
	$pipeline_id = $_REQUEST['pipeline_id'];
    $data = $db->checkProjectPipePerm($pipeline_id, $ownerID);
}
else if ($p=="checkProject")
{
	$pipeline_id = $_REQUEST['pipeline_id'];
    $data = $db->checkProject($pipeline_id, $ownerID);
}
else if ($p=="checkProjectPublic")
{
	$pipeline_id = $_REQUEST['pipeline_id'];
    $data = $db->checkProjectPublic($pipeline_id, $ownerID);
}
else if ($p=="checkParameter")
{
	$parameter_id = $_REQUEST['parameter_id'];
    $data = $db->checkParameter($parameter_id, $ownerID);
}
else if ($p=="checkMenuGr")
{
    $data = $db->checkMenuGr($id, $ownerID);
}
else if ($p=="getMaxProcess_gid")
{
    $data = $db->getMaxProcess_gid();
}
else if ($p=="getMaxPipeline_gid")
{
    $data = $db->getMaxPipeline_gid();
}
else if ($p=="getProcess_gid")
{
    $process_id = $_REQUEST['process_id'];
    $data = $db->getProcess_gid($process_id);
}
else if ($p=="getPipeline_gid")
{
    $pipeline_id = $_REQUEST['pipeline_id'];
    $data = $db->getPipeline_gid($pipeline_id);
}
else if ($p=="getMaxRev_id")
{
    $process_gid = $_REQUEST['process_gid'];
    $data = $db->getMaxRev_id($process_gid);
}
else if ($p=="getMaxPipRev_id")
{
    $pipeline_gid = $_REQUEST['pipeline_gid'];
    $data = $db->getMaxPipRev_id($pipeline_gid);
}
else if ($p=="getInputsPP")
{
	$process_id = $_REQUEST['process_id'];
    $data = $db->getInputsPP($process_id);
}
else if ($p=="getOutputsPP")
{
	$process_id = $_REQUEST['process_id'];
    $data = $db->getOutputsPP($process_id);
}
else if ($p=="saveAllPipeline")
{
	$dat = $_REQUEST['dat'];
    $data = $db->saveAllPipeline($dat,$ownerID);
}
else if ($p=="savePipelineDetails")
{
	$summary = $_REQUEST['summary'];
	$group_id = $_REQUEST['group_id'];
	$perms = $_REQUEST['perms'];
	$pin = $_REQUEST['pin'];
	$pin_order = $_REQUEST['pin_order'];
	$publish = $_REQUEST['publish'];
    settype($group_id, 'integer');
    $data = $db->savePipelineDetails($id,$summary,$group_id,$perms,$pin,$pin_order,$publish,$ownerID);
}
else if ($p=="savePipelineName"){
    $name = $_REQUEST['name'];
    if (!empty($id)) {
        $data = $db->updatePipelineName($id, $name);
    } else {
        $data = $db->insertPipelineName($name,$ownerID);
    }
}
else if ($p=="getSavedPipelines")
{
    $data = $db->getSavedPipelines($ownerID);
}
else if ($p=="loadPipeline")
{
	$id = $_REQUEST['id'];
    $data = $db->loadPipeline($id,$ownerID);
}
if (!headers_sent()) {
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Content-type: application/json');
echo $data;
exit;
}else{
   echo $data;
}