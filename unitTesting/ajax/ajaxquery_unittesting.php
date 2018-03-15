<?php
if (!isset($_SESSION) || !is_array($_SESSION)) session_start();
$_SESSION['ownerID'] = '1';
$_SESSION['username'] = 'admin';
$_SESSION['google_id'] = '111';
$ownerID = isset($_SESSION['ownerID']) ? $_SESSION['ownerID'] : "";
$google_id = isset($_SESSION['google_id']) ? $_SESSION['google_id'] : "";
$username = isset($_SESSION['username']) ? $_SESSION['username'] : "";


chdir('ajax/');
use PHPUnit\Framework\TestCase;


class ajaxQueryTest extends TestCase
{
/**
* @beforeClass
*/
    public function testInsertUser() {
		ob_start();
		$_REQUEST['p'] = 'saveUser';
		$_REQUEST['google_id'] = '111';
		$_REQUEST['name'] = "onur yukselen";
		$_REQUEST['google_image'] = "https://lh4.googleusercontent.com/-h7_FO3k9sB4/AAAAAAAAAAI/AAAAAAAAAAA/AGi4gfw9MqsLVfHz5xXsoOzA1KIZ1yLwXw/s96-c/photo.jpg";
		$_REQUEST['username'] = "admin";
		$_REQUEST['email'] = "admin@gmail.com";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testInsertProject() {
		ob_start();
		$_REQUEST['p'] = 'saveProject';
		$_REQUEST['name'] = 'testProject';
		$_REQUEST['summary'] = "testSummary";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testInsertParameter() {
		ob_start();
		$_REQUEST['p'] = 'saveParameter';
		$_REQUEST['name'] = "inputDir";
		$_REQUEST['qualifier'] = "val";
		$_REQUEST['file_type'] = "inputDir";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
        //***discuss
//    public function testInsertSSH() {
//		ob_start();
//		$_REQUEST['p'] = 'saveSSHKeys';
//		$_REQUEST['name'] = "dockerKey";
//		$_REQUEST['check_userkey'] = "on";
//		$_REQUEST['check_ourkey'] = "";
////		$_REQUEST['prikey'] = "";
////		$_REQUEST['pubkey'] = "";
//		include('ajaxquery.php');
//		$this->assertEquals(json_decode($data)->id,'1');
//		ob_end_clean();
//	}
    public function testInsertAmz() {
		ob_start();
		$_REQUEST['p'] = 'saveAmzKeys';
		$_REQUEST['name'] = "amzKey";
		$_REQUEST['amz_def_reg'] = "test1";
		$_REQUEST['amz_acc_key'] = "test2";
		$_REQUEST['amz_suc_key'] = "test3";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
    public function testInsertProfileCluster() {
		ob_start();
		$_REQUEST['p'] = 'saveProfileCluster';
		$_REQUEST['name'] = "localtest";
		$_REQUEST['cmd'] = "source /etc/profile";
		$_REQUEST['executor'] = "local";
		$_REQUEST['next_memory'] = "";
		$_REQUEST['next_queue'] = "";
		$_REQUEST['next_time'] = "";
		$_REQUEST['next_cpu'] = "";
		$_REQUEST['executor_job'] = "local";
		$_REQUEST['job_memory'] = "";
		$_REQUEST['job_queue'] = "";
		$_REQUEST['job_time'] = "";
		$_REQUEST['job_cpu'] = "";
		$_REQUEST['username'] = "docker";
		$_REQUEST['hostname'] = "localhost";
		$_REQUEST['next_path'] = "";
		$_REQUEST['ssh_id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'2');
		ob_end_clean();
	}
    public function testInsertProfileAmazon() {
		ob_start();
		$_REQUEST['p'] = 'saveProfileAmazon';
		$_REQUEST['name'] = "amazontest";
		$_REQUEST['cmd'] = "";
		$_REQUEST['executor'] = "local";
		$_REQUEST['next_memory'] = "";
		$_REQUEST['next_queue'] = "";
		$_REQUEST['next_time'] = "";
		$_REQUEST['next_cpu'] = "";
		$_REQUEST['executor_job'] = "local";
		$_REQUEST['job_memory'] = "";
		$_REQUEST['job_queue'] = "";
		$_REQUEST['job_time'] = "";
		$_REQUEST['job_cpu'] = "";
		$_REQUEST['ins_type'] = "";
		$_REQUEST['image_id'] = "";
		$_REQUEST['subnet_id'] = "";
		$_REQUEST['shared_storage_id'] = "";
		$_REQUEST['shared_storage_mnt'] = "";
		$_REQUEST['next_path'] = "";
		$_REQUEST['ssh_id'] = "1";
		$_REQUEST['amazon_cre_id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testsaveAllPipeline() {
		ob_start();
		$_REQUEST['p'] = 'saveAllPipeline';
		$_REQUEST['dat'] = '[{"id":"3","edges":"{\'edges\':[]}","mainG":"{\'mainG\':[0,0,1]}","nodes":"{\"g-0\":[255.6666717529297,136.6666717529297,\"1\",\"Build_index\"]}","pin_order":"0","pin":"false","publish":"0","name":"ss","script_header":null,"script_mode":null,"owner_id":"1","group_id":"0","perms":"3","date_created":"2018-03-15 00:40:47","date_modified":"2018-03-15 00:40:48","last_modified_user":"1","rev_id":"0","rev_comment":"","pipeline_gid":"2","summary":"","username":"onuryukselen","own":"1"}]';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
    public function testInsertInput() {
		ob_start();
		$_REQUEST['p'] = 'saveInput';
		$_REQUEST['name'] = "testinput";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    //Database Error: Cannot add or update a child row: a foreign key constraint fails (`biocorepipe`.`project_pipeline_input`, CONSTRAINT `project_pipeline_input_ibfk_3` FOREIGN KEY (`pipeline_id`) REFERENCES `biocorepipe_save` (`id`))
    
//    public function testInsertProPipeInput() {
//		ob_start();
//		$_REQUEST['p'] = 'saveProPipeInput';
//		$_REQUEST['input_id'] = "1";
//		$_REQUEST['project_id'] = "1";
//		$_REQUEST['pipeline_id'] = "1";
//		$_REQUEST['project_pipeline_id'] = "1";
//		$_REQUEST['g_num'] = "1";
//		$_REQUEST['given_name'] = "test_inputparam";
//		$_REQUEST['qualifier'] = "test_inputparam";
//		include('ajaxquery.php');
//		$this->assertEquals(json_decode($data)->id,'1');
//		ob_end_clean();
//	}
    public function testInsertProjectInput() {
		ob_start();
		$_REQUEST['p'] = 'saveProjectInput';
		$_REQUEST['input_id'] = "1";
		$_REQUEST['project_id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    //not working
//    public function testCheckLoginDecline() {
//		ob_start();
//		$_REQUEST['p'] = 'checkLogin';
//        unset($google_id);
//		include('ajaxquery.php');
//		$this->assertEquals(json_decode($data)->error,'1');
//		ob_end_clean();
//	}
    public function testInsertProcessGroup() {
		ob_start();
		$_REQUEST['p'] = 'saveProcessGroup';
		$_REQUEST['group_name'] = 'test_menu';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testInsertProcess() {
		ob_start();
		$_REQUEST['p'] = 'saveProcess';
		$_REQUEST['name'] = 'test_process';
		$_REQUEST['process_gid'] = '1';
		$_REQUEST['summary'] = 'test_summary';
		$_REQUEST['process_group_id'] = '1';
		$_REQUEST['script'] = 'test_script';
		$_REQUEST['script_header'] = 'test_script_header';
		$_REQUEST['script_mode'] = 'perl';
		$_REQUEST['script_mode_header'] = 'python';
		$_REQUEST['rev_id'] = '';
		$_REQUEST['rev_comment'] = '';
		$_REQUEST['group'] = '';
		$_REQUEST['perms'] = '3';
		$_REQUEST['publish'] = 'false';
		$_REQUEST['publish'] = 'false';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testInsertGroup() {
		ob_start();
		$_REQUEST['p'] = 'saveGroup';
		$_REQUEST['name'] = 'test_group';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testInsertUserGroup() {
		ob_start();
		$_REQUEST['p'] = 'saveUserGroup';
		$_REQUEST['u_id'] = '1';
		$_REQUEST['g_id'] = '2';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'2');
		ob_end_clean();
	}
    
    //Database Error: Cannot add or update a child row: a foreign key constraint fails (`biocorepipe`.`project_pipeline`, CONSTRAINT `project_pipeline_ibfk_2` FOREIGN KEY (`pipeline_id`) REFERENCES `biocorepipe_save` (`id`))
//    public function testInsertProjectPipeline() {
//		ob_start();
//		$_REQUEST['p'] = 'saveProjectPipeline';
//		$_REQUEST['pipeline_id'] = '1';
//		$_REQUEST['project_id'] = '1';
//		$_REQUEST['name'] = 'test_run';
//		$_REQUEST['summary'] = 'test_sum';
//		$_REQUEST['output_dir'] = '';
//		$_REQUEST['publish_dir'] = '';
//		$_REQUEST['publish_dir_check'] = '';
//		$_REQUEST['perms'] = '3';
//		$_REQUEST['profile'] = '1';
//		$_REQUEST['interdel'] = '';
//		$_REQUEST['group_id'] = '';
//		$_REQUEST['cmd'] = '';
//        $_REQUEST['exec_each'] = "";
//        $_REQUEST['exec_all'] = "";
//        $_REQUEST['exec_all_settings'] = "";
//        $_REQUEST['exec_each_settings'] = "";
//        $_REQUEST['exec_next_settings'] = "";
//        $_REQUEST['docker_check'] = "";
//        $_REQUEST['docker_img'] = "";
//        $_REQUEST['docker_opt'] = "";
//        $_REQUEST['singu_check'] = "";
//        $_REQUEST['singu_img'] = "";
//        $_REQUEST['singu_opt'] = "";
//        $_REQUEST['amazon_cre_id'] = "";
//		include('ajaxquery.php');
//		$this->assertEquals(json_decode($data)->id,'1');
//		ob_end_clean();
//	}

    
    public function testInsertProcessParameter() {
		ob_start();
		$_REQUEST['p'] = 'saveProcessParameter';
		$_REQUEST['sname'] = 'test_input';
		$_REQUEST['closure'] = '';
		$_REQUEST['reg_ex'] = '';
		$_REQUEST['operator'] = '';
		$_REQUEST['process_id'] = '1';
		$_REQUEST['parameter_id'] = '1';
		$_REQUEST['type'] = 'input';
        $_REQUEST['perms'] = "3";
        $_REQUEST['group'] = "";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}

    public function testgetMaxProcess_gid() {
		ob_start();
		$_REQUEST['p'] = 'getMaxProcess_gid';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
    public function testgetMaxPipeline_gid() {
		ob_start();
		$_REQUEST['p'] = 'getMaxPipeline_gid';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
    public function testinsertPipelineName() {
		ob_start();
		$_REQUEST['p'] = 'savePipelineName';
		$_REQUEST['name'] = 'test_pipeline';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
 
    
    
    
    
    
    
//    to be modified section:
    
//    else if ($p=="duplicateProjectPipelineInput"){
//    $new_id = $_REQUEST['new_id'];
//    $old_id = $_REQUEST['old_id'];
//    $data = $db->duplicateProjectPipelineInput($new_id, $old_id, $ownerID);
//}
//else if ($p=="duplicateProcess"){
//    $new_process_gid = $_REQUEST['process_gid'];
//    $new_name = $_REQUEST['name'];
//    $old_id = $_REQUEST['id'];
//    $data = $db->duplicateProcess($new_process_gid, $new_name, $old_id, $ownerID);
//    $idArray = json_decode($data,true);
//    $new_pro_id = $idArray["id"];
//    $db->duplicateProcessParameter($new_pro_id, $old_id, $ownerID);
//}
//    else if ($p=="createProcessRev"){
//    $rev_comment = $_REQUEST['rev_comment'];
//    $rev_id = $_REQUEST['rev_id'];
//    $new_process_gid = $_REQUEST['process_gid'];
//    $old_id = $_REQUEST['id'];
//    $data = $db->createProcessRev($new_process_gid, $rev_comment, $rev_id, $old_id, $ownerID);
//    $idArray = json_decode($data,true);
//    $new_pro_id = $idArray["id"];
//    $db->duplicateProcessParameter($new_pro_id, $old_id, $ownerID);
//}
    
//    if ($p=="saveRun"){
//	$project_pipeline_id = $_REQUEST['project_pipeline_id'];
//	$profileType = $_REQUEST['profileType'];
//	$profileId = $_REQUEST['profileId'];
//	$amazon_cre_id = $_REQUEST['amazon_cre_id'];
//    ...
//    ...
    
    

    
    
//    /**
//     * @afterClass
//     */
	public function testGetProjects() {
		ob_start();
		$_REQUEST['p'] = 'getProjects';
		$_REQUEST['id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id,'1');
		$this->assertEquals(json_decode($data)[0]->name,'testProject');
		$this->assertEquals(json_decode($data)[0]->summary,'testSummary');
		ob_end_clean();
	}
    
    
        //after insert user
    public function testCheckLogin() {
		ob_start();
		$_REQUEST['p'] = 'checkLogin';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}

}






?>
