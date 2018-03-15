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
    /**
     * @depends testInsertUser
     */
    public function testInsertUser2() {
		ob_start();
		$_REQUEST['p'] = 'saveUser';
		$_REQUEST['google_id'] = '222';
		$_REQUEST['name'] = "member name";
		$_REQUEST['google_image'] = "https://lh4.googleusercontent.com/-h7_FO3k9sB4/AAAAAAAAAAI/AAAAAAAAAAA/AGi4gfw9MqsLVfHz5xXsoOzA1KIZ1yLwXw/s96-c/photo.jpg";
		$_REQUEST['username'] = "member";
		$_REQUEST['email'] = "member@gmail.com";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'2');
		ob_end_clean();
	}
    public function testInsertProject() {
		ob_start();
		$_REQUEST['p'] = 'saveProject';
		$_REQUEST['name'] = 'test_project';
		$_REQUEST['summary'] = "testSummary";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
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
		$_REQUEST['process_gid'] = 'NaN';
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
    public function testInsertInput() {
		ob_start();
		$_REQUEST['p'] = 'saveInput';
		$_REQUEST['name'] = "testinput";
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
    public function testInsertProjectInput() {
		ob_start();
		$_REQUEST['p'] = 'saveProjectInput';
		$_REQUEST['input_id'] = "1";
		$_REQUEST['project_id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    public function testsaveAllPipeline() {
		ob_start();
		$_REQUEST['p'] = 'saveAllPipeline';
		$_REQUEST['dat'] = '[{"name":"test_pipeline"},{"id":""},{"nodes":{"g-0":[318.6666564941406,106.66666412353516,"1","test_process"]}},{"mainG":[0,0,1]},{"edges":[]},{"summary":""},{"group_id":""},{"perms":"3"},{"pin":"false"},{"pin_order":""},{"publish":"0"},{"pipeline_gid":null},{"rev_comment":""},{"rev_id":0}]';
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
    public function testsavefeedback() {
		ob_start();
		$_REQUEST['p'] = 'savefeedback';
		$_REQUEST['email'] = 'test@gmail.com';
		$_REQUEST['message'] = 'test_message';
		$_REQUEST['url'] = 'https://dolphinnext.umassmed.edu/index.php?np=2';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
    public function testInsertProjectPipeline() {
		ob_start();
		$_REQUEST['p'] = 'saveProjectPipeline';
		$_REQUEST['pipeline_id'] = '1';
		$_REQUEST['project_id'] = '1';
		$_REQUEST['name'] = 'test_run';
		$_REQUEST['summary'] = 'test_sum';
		$_REQUEST['output_dir'] = '';
		$_REQUEST['publish_dir'] = '';
		$_REQUEST['publish_dir_check'] = '';
		$_REQUEST['perms'] = '3';
		$_REQUEST['profile'] = '1';
		$_REQUEST['interdel'] = '';
		$_REQUEST['group_id'] = '';
		$_REQUEST['cmd'] = '';
        $_REQUEST['exec_each'] = "";
        $_REQUEST['exec_all'] = "";
        $_REQUEST['exec_all_settings'] = "";
        $_REQUEST['exec_each_settings'] = "";
        $_REQUEST['exec_next_settings'] = "";
        $_REQUEST['docker_check'] = "";
        $_REQUEST['docker_img'] = "";
        $_REQUEST['docker_opt'] = "";
        $_REQUEST['singu_check'] = "";
        $_REQUEST['singu_img'] = "";
        $_REQUEST['singu_opt'] = "";
        $_REQUEST['amazon_cre_id'] = "";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
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
		$this->assertEquals(json_decode($data)[0]->process_gid,'0');
		ob_end_clean();
	}
    public function testgetMaxPipeline_gid() {
		ob_start();
		$_REQUEST['p'] = 'getMaxPipeline_gid';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->pipeline_gid,'0');
		ob_end_clean();
	}
    public function testinsertPipelineName() {
		ob_start();
		$_REQUEST['p'] = 'savePipelineName';
		$_REQUEST['name'] = 'test_pipeline';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'2');
		ob_end_clean();
	}
    public function testInsertProPipeInput() {
		ob_start();
		$_REQUEST['p'] = 'saveProPipeInput';
		$_REQUEST['input_id'] = "1";
		$_REQUEST['project_id'] = "1";
		$_REQUEST['pipeline_id'] = "1";
		$_REQUEST['project_pipeline_id'] = "1";
		$_REQUEST['g_num'] = "0";
		$_REQUEST['given_name'] = "test_inputparam";
		$_REQUEST['qualifier'] = "test_inputparam";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'1');
		ob_end_clean();
	}
    
    public function testduplicateProcess() {
		ob_start();
		$_REQUEST['p'] = 'duplicateProcess';
		$_REQUEST['name'] = "duplicate_process";
		$_REQUEST['id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'2');
		ob_end_clean();
	}
    /**
     * @depends testduplicateProcess
     */
    public function testcreateProcessRev() {
		ob_start();
		$_REQUEST['p'] = 'createProcessRev';
		$_REQUEST['rev_comment'] = "test_comment";
		$_REQUEST['rev_id'] = "1";
		$_REQUEST['process_gid'] = "1";
		$_REQUEST['id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'3');
		ob_end_clean();
	}
    /**
     * @depends testInsertProPipeInput
     */
    public function testduplicateProjectPipelineInput() {
		ob_start();
		$_REQUEST['p'] = 'duplicateProjectPipelineInput';
		$_REQUEST['new_id'] = "2";
		$_REQUEST['old_id'] = "1";
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)->id,'2');
		ob_end_clean();
	}
    /**
     * @depends testInsertProject
     */
	public function testGetProjects() {
		ob_start();
		$_REQUEST['p'] = 'getProjects';
		$_REQUEST['id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id,'1');
		$this->assertEquals(json_decode($data)[0]->name,'test_project');
		$this->assertEquals(json_decode($data)[0]->summary,'testSummary');
		ob_end_clean();
	}
    /**
     * @depends testInsertUser
     */
    public function testCheckLogin() {
		ob_start();
		$_REQUEST['p'] = 'checkLogin';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->username,'admin');
		ob_end_clean();
	}
    /**
     * @depends testInsertParameter
     */
    public function testgetAllParameters() {
		ob_start();
		$_REQUEST['p'] = 'getAllParameters';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id,'1');
		$this->assertEquals(json_decode($data)[0]->name,'inputDir');
		ob_end_clean();
	}
    /**
     * @depends testInsertParameter
     */
    public function testgetEditDelParameters() {
		ob_start();
		$_REQUEST['p'] = 'getEditDelParameters';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id,'1');
		$this->assertEquals(json_decode($data)[0]->name,'inputDir');
		ob_end_clean();
	}
    /**
     * @depends testInsertGroup
     * @depends testInsertUser
     */
    public function testgetMemberAdd() {
		ob_start();
		$_REQUEST['p'] = 'getMemberAdd';
		$_REQUEST['g_id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id,'2');
		$this->assertEquals(json_decode($data)[0]->username,'member');
		ob_end_clean();
	}
    /**
     * @depends testInsertGroup
     */
    public function testgetGroups() {
		ob_start();
		$_REQUEST['p'] = 'getGroups';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id,'1');
		$this->assertEquals(json_decode($data)[0]->name,'test_group');
		ob_end_clean();
	}
    /**
     * @depends testInsertGroup
     */
    public function testgetUserGroups() {
		ob_start();
		$_REQUEST['p'] = 'getUserGroups';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id,'1');
		$this->assertEquals(json_decode($data)[0]->name,'test_group');
		ob_end_clean();
	}
    /**
     * @depends testInsertUser
     */
    public function testgetUserRole() {
		ob_start();
		$_REQUEST['p'] = 'getUserRole';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->role, null);
		ob_end_clean();
	}
    /**
     * @depends testInsertProjectPipeline
     */
    public function testgetExistProjectPipelines() {
		ob_start();
		$_REQUEST['p'] = 'getExistProjectPipelines';
		$_REQUEST['pipeline_id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id, '1');
		$this->assertEquals(json_decode($data)[0]->pp_name, 'test_run');
		ob_end_clean();
	}
    /**
     * @depends testInsertProjectPipeline
     * @depends testInsertProject
     */
    public function testgetProjectPipelines() {
		ob_start();
		$_REQUEST['p'] = 'getProjectPipelines';
		$_REQUEST['project_id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id, '1');
		$this->assertEquals(json_decode($data)[0]->pp_name, 'test_run');
		$this->assertEquals(json_decode($data)[0]->project_name, 'test_project');
		ob_end_clean();
	}
    /**
     * @depends testInsertProject
     * @depends testInsertInput
     */
    public function testgetProjectInputs() {
		ob_start();
		$_REQUEST['p'] = 'getProjectInputs';
		$_REQUEST['project_id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id, '1');
		$this->assertEquals(json_decode($data)[0]->input_id, '1');
		ob_end_clean();
	}
    /**
     * @depends testInsertProject
     * @depends testInsertInput
     */
    public function testgetProjectInput() {
		ob_start();
		$_REQUEST['p'] = 'getProjectInput';
		$_REQUEST['id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id, '1');
		$this->assertEquals(json_decode($data)[0]->input_id, '1');
		ob_end_clean();
	}
    
    /**
     * @depends testInsertProject
     * @depends testInsertInput
     * @depends testInsertProjectPipeline
     * @depends testInsertProPipeInput
     */
    public function testgetProjectPipelineInputs() {
		ob_start();
		$_REQUEST['p'] = 'getProjectPipelineInputs';
		$_REQUEST['g_num'] = '0';
		$_REQUEST['project_pipeline_id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id, '1');
		$this->assertEquals(json_decode($data)[0]->input_id, '1');
		$this->assertEquals(json_decode($data)[0]->given_name, 'test_inputparam');
		ob_end_clean();
	}
    /**
     * @depends testInsertProject
     * @depends testInsertInput
     * @depends testInsertProjectPipeline
     */
    public function testgetProjectPipelineInputsById() {
		ob_start();
		$_REQUEST['p'] = 'getProjectPipelineInputs';
		$_REQUEST['g_num'] = '0';
		$_REQUEST['project_pipeline_id'] = '1';
		$_REQUEST['id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id, '1');
		$this->assertEquals(json_decode($data)[0]->input_id, '1');
		$this->assertEquals(json_decode($data)[0]->name, 'testinput');
		ob_end_clean();
	}
    /**
     * @depends testInsertInput
     */
    public function testgetInputs() {
		ob_start();
		$_REQUEST['p'] = 'getInputs';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id, '1');
		$this->assertEquals(json_decode($data)[0]->name, 'testinput');
		ob_end_clean();
	}
    /**
     * @depends testInsertInput
     */
    public function testgetInputsById() {
		ob_start();
		$_REQUEST['p'] = 'getInputs';
		$_REQUEST['id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id, '1');
		$this->assertEquals(json_decode($data)[0]->name, 'testinput');
		ob_end_clean();
	}
    /**
     * @depends testInsertInput
     */
    public function testgetInputsById() {
		ob_start();
		$_REQUEST['p'] = 'getInputs';
		$_REQUEST['id'] = '1';
		include('ajaxquery.php');
		$this->assertEquals(json_decode($data)[0]->id, '1');
		$this->assertEquals(json_decode($data)[0]->name, 'testinput');
		ob_end_clean();
	}
    


//else if ($p=="getAllProcessGroups"){
//    $data = $db -> getAllProcessGroups($ownerID);
//}
//else if ($p=="getEditDelProcessGroups"){
//    $data = $db -> getEditDelProcessGroups($ownerID);
//}
    
    
    //    if ($p=="saveRun"){
//	$project_pipeline_id = $_REQUEST['project_pipeline_id'];
//	$profileType = $_REQUEST['profileType'];
//	$profileId = $_REQUEST['profileId'];
//	$amazon_cre_id = $_REQUEST['amazon_cre_id'];
//    ...
//    ...

}






?>
