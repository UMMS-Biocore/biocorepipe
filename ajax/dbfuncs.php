<?php
require_once("../config/config.php");

class dbfuncs {
    
    private $dbhost = DBHOST;
    private $db = DB;
    private $dbuser = DBUSER;
    private $dbpass = DBPASS;
    private $dbport = DBPORT;
    private $run_path = RUNPATH;
    private $ssh_path = SSHPATH;
    private $ssh_settings = "-oStrictHostKeyChecking=no -q -oChallengeResponseAuthentication=no -oBatchMode=yes -oPasswordAuthentication=no -oConnectTimeout=3";
    private $amz_path = AMZPATH;
    private $amazon = AMAZON;
    private static $link;

    function __construct() {
        if (!isset(self::$link)) {
            self::$link = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->db, $this->dbport);
            // check connection
            if (mysqli_connect_errno()) {
                exit('Connect failed: ' . mysqli_connect_error());
            }
        }
    }

//    function __destruct() {
//        if (isset(self::$link)) {
//            self::$link->close();
//        }
//    }
   function runSQL($sql)
   {
        $link = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->db);
        // check connection
        if (mysqli_connect_errno()) {
                exit('Connect failed: '. mysqli_connect_error());
        }
        $result=self::$link->query($sql);
            $link->close();
       
		if (!$result) {
            trigger_error('Database Error: ' . self::$link->error);
        }
        if ($result && $result!="1")
        {
            return $result;
        }
        return json_encode (json_decode ("{}"));
   }
   function queryTable($sql)
   {
     $data = array();
     if ($res = $this->runSQL($sql))
     {
        while(($row=$res->fetch_assoc()))
        {
            if (isset($row['sname'])){
            $row['sname'] = htmlspecialchars_decode($row['sname'], ENT_QUOTES);
            } else if (isset($row['process_parameter_name'])){
            $row['process_parameter_name'] = htmlspecialchars_decode($row['process_parameter_name'], ENT_QUOTES);
            } 
            $data[]=$row;
        }
         
        $res->close();
     }
     return json_encode($data);
   }
   
   function insTable($sql)
   {
	 $data = array();

     if ($res = $this->runSQL($sql))
     {
		$insertID = self::$link->insert_id;
		$data = array('id' => $insertID);
     }
     return json_encode($data);
   }
    
    function writeLog($project_pipeline_id,$text,$mode){
        $file = fopen("../{$this->run_path}/run{$project_pipeline_id}/log.txt", $mode);//creates new file
        fwrite($file, $text."\n");
        fclose($file);
    }
    //$img: path of image
    function imageCmd($img, $type, $profileType ){
        if ($type == 'singularity'){
            preg_match("/shub:\/\/(.*)/", $img, $matches);
              if ($matches[1] != ''){
                  $imageName = str_replace("/","-",$matches[1]);
                  $image = '~/.dolphinnext/singularity/' + $imageName;
                  if ($profileType == 'local' || $profileType == 'cluster'){
                  $cmd = "mkdir -p ~/.dolphinnext/singularity && cd ~/.dolphinnext/singularity && singularity pull --name ".$imageName.".simg ".$img;
                  return $cmd;
                  }
              }  
        } else if ($type == 'docker'){
            
        }
        
    }
    
    //type:w creates new file
    function createDirFile ($pathDir, $fileName, $type, $text){
        if ($pathDir != ""){
            mkdir("$pathDir", 0755, true);
        }
        if ($fileName != ""){
            $file = fopen("$pathDir/$fileName", $type);
            fwrite($file, $text);
            fclose($file);
            chmod("$pathDir/$fileName", 0755);
        }
    }
    
    //if logArray not exist than send empty ""
    function runCommand ($cmd, $logName, $logArray) {
        $pid_command = popen($cmd, 'r');
        $pid = fread($pid_command, 2096);
        pclose($pid_command);
        if (empty($logArray)){
        $log_array = array($logName => $pid);
        } else {
        $log_array[$logName] = $pid;    
        }
        return $log_array;
    }
    
    //full path for file
    function readFile($path){
        $handle = fopen($path, 'r');
        $content = fread($handle, filesize($path));
        fclose($handle);
        return $content;
    }
    
     //get nextflow input parameters
    function getNextInputs ($executor, $project_pipeline_id, $ownerID ){
        $allinputs = json_decode($this->getProjectPipelineInputs("", $project_pipeline_id, $ownerID));
        $next_inputs="";
        if ($executor === "local"){
            foreach ($allinputs as $inputitem):
                $next_inputs.="--".$inputitem->{'given_name'}." \\\"".$inputitem->{'name'}."\\\" ";
            endforeach;
        } else if ($executor !== "local"){
            foreach ($allinputs as $inputitem):
                $next_inputs.="--".$inputitem->{'given_name'}." \\\\\\\"".$inputitem->{'name'}."\\\\\\\" ";
            endforeach;
        }
        return $next_inputs;
        
    }
    
    //get nextflow executor parameters
    function getNextExecParam($project_pipeline_id,$ownerID){
        $proPipeAll = json_decode($this->getProjectPipelines($project_pipeline_id,"",$ownerID));
        $outdir = $proPipeAll[0]->{'output_dir'};
        $proPipeCmd = $proPipeAll[0]->{'cmd'};
        $jobname = $proPipeAll[0]->{'pp_name'};
        $singu_check = $proPipeAll[0]->{'singu_check'};
        if ($singu_check == "true"){
            $singu_img = $proPipeAll[0]->{'singu_img'};
            $imageCmd =='';
//          $imageCmd = $this->imageCmd($singu_img, 'singularity', $profileType);
        }
        return array($outdir, $proPipeCmd, $jobname, $singu_check, $singu_img, $imageCmd);  
    }
    
    //get username and hostname and exec info for connection
    function getNextConnectExec($profileId,$ownerID, $profileType){
        if ($profileType == "cluster"){
            $cluData=$this->getProfileClusterbyID($profileId, $ownerID);
            $cluDataArr=json_decode($cluData,true);
            $connect = $cluDataArr[0]["username"]."@".$cluDataArr[0]["hostname"];
        } else if ($profileType == "amazon"){
            $cluData=$this->getProfileAmazonbyID($profileId, $ownerID);
            $cluDataArr=json_decode($cluData,true);
            $connect = $cluDataArr[0]["ssh"];
        }
        $ssh_id = $cluDataArr[0]["ssh_id"];
        $next_path = $cluDataArr[0]["next_path"];
        $profileCmd = $cluDataArr[0]["cmd"];
        $executor = $cluDataArr[0]['executor'];
        $next_time = $cluDataArr[0]['next_time'];
        $next_queue = $cluDataArr[0]['next_queue'];
        $next_memory = $cluDataArr[0]['next_memory'];
        $next_cpu = $cluDataArr[0]['next_cpu'];
        $executor_job = $cluDataArr[0]['executor_job'];
        return array($connect, $next_path, $profileCmd, $executor,$next_time, $next_queue, $next_memory, $next_cpu, $executor_job,$ssh_id);
    }
    function getPreCmd ($profileCmd,$proPipeCmd, $imageCmd){
            //combine pre-run cmd
            if (!empty($profileCmd) && !empty($proPipeCmd)){
                $preCmd = "&& ".$profileCmd." && ".$proPipeCmd;
            } else if (!empty($profileCmd)){
                $preCmd = "&& ".$profileCmd;
            } else if (!empty($proPipeCmd)){
                $preCmd = "&& ".$proPipeCmd;
            } else {
                $preCmd ="";
            }
            //combine pre-run cmd with $imageCmd
            if (!empty($preCmd) && !empty($imageCmd)){
                $preCmd = $preCmd." && ".$imageCmd;
            } else if (!empty($preCmd)){
                $preCmd = $preCmd;
            } else if (!empty($imageCmd)){
                $preCmd = "&& ".$imageCmd;
            } else {
                $preCmd ="";
            }
    return $preCmd;
    }
    
    function getNextPathReal($next_path){
        if (!empty($next_path)){
        $next_path_real = "$next_path/nextflow";
        } else {
        $next_path_real  = "nextflow";
        }
    return $next_path_real;
    }
    
    function convertToHoursMins($time) {
        $format = '%d:%s';
        settype($time, 'integer');
        if ($time >= 1440) {
            $time = 1440;
        }
        $hours = floor($time/60);
        $minutes = $time%60;
        if ($minutes < 10) {
            $minutes = '0' . $minutes;
        }
        if ($hours < 10) {
            $hours = '0' . $hours;
        }
        return sprintf($format, $hours, $minutes);
    }
    function cleanName($name){
        $name = str_replace("/","_",$name);
        $name = str_replace(" ","",$name);
        $name = str_replace("(","_",$name);
        $name = str_replace(")","_",$name);
        $name = str_replace("\'","_",$name);
        $name = str_replace("\"","_",$name);
        $name = str_replace("\\","_",$name);
        $name = str_replace("&","_",$name);
        $name = str_replace("<","_",$name);
        $name = str_replace(">","_",$name);
        $name = str_replace("-","_",$name);
        $name = substr($name, 0, 9);
        return $name;
    }
    
    //get all nextflow executor text
    function getExecNextAll($executor, $dolphin_path_real, $next_path_real, $next_inputs,$next_queue, $next_cpu,$next_time,$next_memory,$jobname, $executor_job) {
    //for lsf "bsub -q short -n 1  -W 100 -R rusage[mem=32024]";
        if ($executor == "local"){
            if ($executor_job == 'ignite'){
                $exec_next_all = "cd $dolphin_path_real && $next_path_real $dolphin_path_real/nextflow.nf -process.executor ignite $next_inputs -with-trace > $dolphin_path_real/log.txt ";
            }else {
                $exec_next_all = "cd $dolphin_path_real && $next_path_real $dolphin_path_real/nextflow.nf $next_inputs -with-trace > $dolphin_path_real/log.txt ";
            }
        } else if ($executor == "lsf"){ 
            //convert gb to mb
            settype($next_memory, 'integer');
            $next_memory = $next_memory*1000;
            //-J $jobname
            $jobname = $this->cleanName($jobname);
            $exec_string = "bsub  -q $next_queue -J $jobname -n $next_cpu -W $next_time -R rusage[mem=$next_memory]";
            $exec_next_all = "cd $dolphin_path_real && $exec_string \\\"$next_path_real $dolphin_path_real/nextflow.nf $next_inputs -with-trace > $dolphin_path_real/log.txt\\\"";
        } else if ($executor == "sge"){
            //$next_time is in minutes convert into hours and minutes.
            $next_time = $this->convertToHoursMins($next_time);
            $next_memory = $next_memory."G";
            //-N $jobname
            $jobname = $this->cleanName($jobname);
            $exec_string = "qsub -N $jobname -q $next_queue  -pe smp $next_cpu -l h_rt= $next_time:00 -l h_vmem=$next_memory";
            $exec_next_all = "cd $dolphin_path_real && $exec_string \\\"$next_path_real $dolphin_path_real/nextflow.nf $next_inputs -with-trace > $dolphin_path_real/log.txt\\\"";
        } else if ($executor == "slurm"){
        } else if ($executor == "ignite"){
        }
    return $exec_next_all;
    }
    
    
    function initRun($project_pipeline_id, $configText, $nextText, $profileType, $profileId, $amazon_cre_id, $ownerID){
        //if  $amazon_cre_id is defined append the aws credentials into nextflow.config
        if ($amazon_cre_id != "" ){
            $amz_data = json_decode($this->getAmzbyID($amazon_cre_id, $ownerID));
            foreach($amz_data as $d){
		      $access = $d->amz_acc_key;
              $d->amz_acc_key = trim($this->amazonDecode($access));
		      $secret = $d->amz_suc_key;
		      $d->amz_suc_key = trim($this->amazonDecode($secret));
	         }
            $access_key = $amz_data[0]->{'amz_acc_key'};
            $secret_key = $amz_data[0]->{'amz_suc_key'};
            $default_region = $amz_data[0]->{'amz_def_reg'};
            $configText.= "aws{\n";
            $configText.= "   accessKey = '$access_key'\n";
            $configText.= "   secretKey = '$secret_key'\n";
            $configText.= "   region = '$default_region'\n";
            $configText.= "}\n";
        }
        //rename the log file
        if ($profileType == 'cluster'){
        $this->renameLogSSH($project_pipeline_id,$profileType, $profileId, $ownerID);
        }
        //create folders
        mkdir("../{$this->run_path}/run{$project_pipeline_id}", 0755, true);
        $file = fopen("../{$this->run_path}/run{$project_pipeline_id}/nextflow.nf", 'w');//creates new file
        fwrite($file, $nextText);
        fclose($file);
        chmod("../{$this->run_path}/run{$project_pipeline_id}/nextflow.nf", 0755);
        $file = fopen("../{$this->run_path}/run{$project_pipeline_id}/nextflow.config", 'w');//creates new file
        fwrite($file, $configText);
        fclose($file);
        chmod("../{$this->run_path}/run{$project_pipeline_id}/nextflow.config", 0755);
        if ($profileType == "local") {
//            // get outputdir
//            $proPipeAll = json_decode($this->getProjectPipelines($project_pipeline_id,"",$ownerID));
//            $outdir = $proPipeAll[0]->{'output_dir'};
//            $run_path_real = "$outdir/run{$project_pipeline_id}";
//            //check nextflow file
//            $log_path_server = "../{$this->run_path}/run{$project_pipeline_id}";
//            if (!file_exists($log_path_server."/nextflow.nf")) die(json_encode('Nextflow file is not found!'));
//            if (!file_exists($log_path_server."/nextflow.config")) die(json_encode('Nextflow config file is not found!'));
//            //mkdir and copy nextflow and config file to run directory in local
//            mkdir("$run_path_real", 0755, true);
//            $cmd = "cp $log_path_server/nextflow.nf $run_path_real/nextflow.nf && cp $log_path_server/nextflow.config $run_path_real/nextflow.config";
//            $this->writeLog($project_pipeline_id,$cmd,'w');
//            $pid_command = popen($cmd, 'r');//copy file
//            $pid = fread($pid_command, 2096);
//            pclose($pid_command);
//            chmod("$run_path_real/nextflow.nf", 0755);
        } else if ($profileType == "cluster") {
            // get outputdir
            $proPipeAll = json_decode($this->getProjectPipelines($project_pipeline_id,"",$ownerID));
            $outdir = $proPipeAll[0]->{'output_dir'};
            // get username and hostname for connection
            $cluData=$this->getProfileClusterbyID($profileId, $ownerID);
            $cluDataArr=json_decode($cluData,true);
            $connect = $cluDataArr[0]["username"]."@".$cluDataArr[0]["hostname"];
            $ssh_id = $cluDataArr[0]["ssh_id"];
            //get userpky
            $userpky = "{$this->ssh_path}/{$ownerID}_{$ssh_id}_ssh_pri.pky";
            //check $userpky file exist
            if (!file_exists($userpky)) {
            $this->writeLog($project_pipeline_id,'Private key is not found!','a');
            die(json_encode('Private key is not found!'));
            }
            $run_path_real = "../{$this->run_path}/run{$project_pipeline_id}";
            if (!file_exists($run_path_real."/nextflow.nf")) {
            $this->writeLog($project_pipeline_id,'Nextflow file is not found!','a');
            die(json_encode('Nextflow file is not found!'));
            }
            if (!file_exists($run_path_real."/nextflow.config")) {
            $this->writeLog($project_pipeline_id,'Nextflow config file is not found!','a');
            die(json_encode('Nextflow config file is not found!'));
            }
            $dolphin_path_real = "$outdir/run{$project_pipeline_id}";
            //mkdir and copy nextflow file to run directory in cluster
            $cmd = "ssh {$this->ssh_settings}  -i $userpky $connect \"mkdir -p $dolphin_path_real\" > $run_path_real/log.txt 2>&1 && scp {$this->ssh_settings} -i $userpky $run_path_real/nextflow.nf $run_path_real/nextflow.config $connect:$dolphin_path_real >> $run_path_real/log.txt 2>&1";
            $mkdir_copynext_pid =shell_exec($cmd);
            $this->writeLog($project_pipeline_id,$cmd,'a');
            $log_array = array('mkdir_copynext_pid' => $mkdir_copynext_pid);
            return $log_array;
        } else if ($profileType == "amazon") {
            // get outputdir
            $proPipeAll = json_decode($this->getProjectPipelines($project_pipeline_id,"",$ownerID));
            $outdir = $proPipeAll[0]->{'output_dir'};
            // get username and hostname for connection
            $amzData=$this->getProfileAmazonbyID($profileId, $ownerID);
            $amzDataArr=json_decode($amzData,true);
            $connect = $amzDataArr[0]["ssh"];
            $ssh_id = $amzDataArr[0]["ssh_id"];
            //get userpky
            $userpky = "{$this->ssh_path}/{$ownerID}_{$ssh_id}_ssh_pri.pky";
            //check $userpky file exist
            if (!file_exists($userpky)) {
                $this->writeLog($project_pipeline_id,'Private key is not found!','a');
                die(json_encode('Private key is not found!'));
            }
            $run_path_real = "../{$this->run_path}/run{$project_pipeline_id}";
            if (!file_exists($run_path_real."/nextflow.nf")) {
                $this->writeLog($project_pipeline_id,'Nextflow file is not found!','a');
                die(json_encode('Nextflow file is not found!'));  
            }
            if (!file_exists($run_path_real."/nextflow.config")) {
                $this->writeLog($project_pipeline_id,'Nextflow config file is not found!','a');
                die(json_encode('Nextflow config file is not found!'));
            }
            $dolphin_path_real = "$outdir/run{$project_pipeline_id}";
            //mkdir and copy nextflow file to run directory in cluster
            $cmd = "ssh {$this->ssh_settings}  -i $userpky $connect \"mkdir -p $dolphin_path_real\" > $run_path_real/log.txt 2>&1 && scp {$this->ssh_settings} -i $userpky $run_path_real/nextflow.nf $run_path_real/nextflow.config $connect:$dolphin_path_real >> $run_path_real/log.txt 2>&1";
            $mkdir_copynext_pid =shell_exec($cmd);
            $this->writeLog($project_pipeline_id,$cmd,'a');
            $log_array = array('mkdir_copynext_pid' => $mkdir_copynext_pid);
            return $log_array;
        }
    }
    
    function runCmd($project_pipeline_id, $profileType, $profileId, $log_array, $ownerID)
    {
        if ($profileType == "local") {
//            //get input parameters
//            $allinputs = json_decode($this->getProjectPipelineInputs("", $project_pipeline_id, $ownerID));
//            $next_inputs="";
//            foreach ($allinputs as $inputitem):
//                $next_inputs.="--".$inputitem->{'given_name'}." '".$inputitem->{'name'}."' ";
//            endforeach;
//            // get outputdir  
//            $proPipeAll = json_decode($this->getProjectPipelines($project_pipeline_id,"",$ownerID));
//            $outdir = $proPipeAll[0]->{'output_dir'};
//            $proPipeCmd = $proPipeAll[0]->{'cmd'};
//            $singu_check = $proPipeAll[0]->{'singu_check'};
//            if ($singu_check == "true"){
//                $singu_img = $proPipeAll[0]->{'singu_img'};
//                $imageCmd =='';
////                $imageCmd = $this->imageCmd($singu_img, 'singularity', $profileType);
//            }
//            //profile cmd before nextflow run
//            $locData=$this->getProfileLocalbyID($profileId, $ownerID);
//            $locDataArr=json_decode($locData,true);
//            $next_path = $locDataArr[0]["next_path"];
//            $profileCmd = $locDataArr[0]['cmd'];
//            $executor = $locDataArr[0]['executor'];
//            $next_time = $locDataArr[0]['next_time'];
//            $next_queue = $locDataArr[0]['next_queue'];
//            $next_memory = $locDataArr[0]['next_memory'];
//            $next_cpu = $locDataArr[0]['next_cpu'];
//            //combine pre-run cmd
//            if (!empty($profileCmd) && !empty($proPipeCmd)){
//                $preCmd = "&& ".$profileCmd." && ".$proPipeCmd;
//            } else if (!empty($profileCmd)){
//                $preCmd = "&& ".$profileCmd;
//            } else if (!empty($proPipeCmd)){
//                $preCmd = "&& ".$proPipeCmd;
//            } else {
//                $preCmd ="";
//            }
//            //combine pre-run cmd with $imageCmd
//            if (!empty($preCmd) && !empty($imageCmd)){
//                $preCmd = $preCmd." && ".$imageCmd;
//            } else if (!empty($preCmd)){
//                $preCmd = $preCmd;
//            } else if (!empty($imageCmd)){
//                $preCmd = "&& ".$imageCmd;
//            } else {
//                $preCmd ="";
//            }
//            if (!empty($next_path)){
//                $next_path_real = "$next_path/nextflow";
//            } else {
//                $next_path_real  = "nextflow";
//            }
//            $run_path_real = "$outdir/run{$project_pipeline_id}";
//            chdir('../');
//            $server_dir = getcwd();
//            chdir('ajax');
//            $log_path_server = "$server_dir/{$this->run_path}/run{$project_pipeline_id}";
//            //run command
//            if ($executor == "local"){
//            $exec_next_all = "cd $run_path_real && $next_path_real nextflow.nf $next_inputs -with-trace >> $log_path_server/log.txt 2>&1 ";
//            } else if ($executor == "lsf"){  
//            $exec_string = "bsub -q $next_queue -n $next_cpu -W $next_time -R rusage[mem=$next_memory]";
//            $exec_next_all = "cd $run_path_real && $exec_string \"$next_path_real nextflow.nf $next_inputs -with-trace >> $log_path_server/log.txt 2>&1 \">> $log_path_server/log.txt 2>&1";
//            } else if ($executor == "sge"){
//                
//            } else if ($executor == "slurm"){
//            }
//		    $cmd = "cd $run_path_real $preCmd && $exec_next_all & echo $! &";
//            $this->writeLog($project_pipeline_id, $cmd,'a');
//            $pid_command = popen($cmd, "r" );
//            $pid = fread($pid_command, 2096);
//		    $this->updateRunPid($project_pipeline_id, $pid, $ownerID);
//		    pclose($pid_command);
//            $log_array['next_submit_pid'] = $pid;
//            return json_encode($log_array);
            
        } else if ($profileType == "cluster") {
            //get nextflow executor parameters
            list($outdir, $proPipeCmd, $jobname, $singu_check, $singu_img, $imageCmd) = $this->getNextExecParam($project_pipeline_id,$ownerID);
            //get username and hostname and exec info for connection
            list($connect, $next_path, $profileCmd, $executor, $next_time, $next_queue, $next_memory, $next_cpu, $executor_job, $ssh_id)=$this->getNextConnectExec($profileId,$ownerID, $profileType);
            //get nextflow input parameters
            $next_inputs = $this->getNextInputs($executor, $project_pipeline_id,$ownerID);
            //get cmd before run
            $preCmd = $this->getPreCmd ($profileCmd,$proPipeCmd, $imageCmd);
            //eg. /project/umw_biocore/bin
            $next_path_real = $this->getNextPathReal($next_path);
            
            //get userpky
            $userpky = "{$this->ssh_path}/{$ownerID}_{$ssh_id}_ssh_pri.pky";
            if (!file_exists($userpky)) {
                $this->writeLog($project_pipeline_id,'Private key is not found!','a');
                die(json_encode('Private key is not found!'));
            }
            $run_path_real = "../{$this->run_path}/run{$project_pipeline_id}";
            $dolphin_path_real = "$outdir/run{$project_pipeline_id}";
            //check if files are exist
            $next_exist_cmd= "ssh {$this->ssh_settings} -i $userpky $connect test  -f \"$dolphin_path_real/nextflow.nf\"  && echo \"Nextflow file exists\" || echo \"Nextflow file not exists\" 2>&1 & echo $! &";
            $next_exist = shell_exec($next_exist_cmd);
            $this->writeLog($project_pipeline_id,$next_exist_cmd,'a');
            preg_match("/(.*)Nextflow file(.*)exists(.*)/", $next_exist, $matches);
            $log_array['next_exist'] = $next_exist;
            // if $matches[2] == " ", it means nextflow file is exist 
            if ($matches[2] == " ") {
            $exec_next_all = $this->getExecNextAll($executor, $dolphin_path_real, $next_path_real, $next_inputs, $next_queue,$next_cpu,$next_time,$next_memory, $jobname, $executor_job);
            
            $cmd="ssh {$this->ssh_settings}  -i $userpky $connect \"cd $dolphin_path_real $preCmd && $exec_next_all\" >> $run_path_real/log.txt 2>&1 & echo $! &";
            $next_submit_pid= shell_exec($cmd); //"Job <203477> is submitted to queue <long>.\n"
            $this->writeLog($project_pipeline_id,$cmd,'a');
            if (!$next_submit_pid) {
                $this->writeLog($project_pipeline_id,'Connection failed while running nextflow in the cluster','a');
                die(json_encode('Connection failed while running nextflow in the cluster'));
            }
            $log_array['next_submit_pid'] = $next_submit_pid;
            return json_encode($log_array);
            
            }else if ($matches[2] == " not "){
                for( $i= 0 ; $i < 3 ; $i++ ){
                     sleep(3);
                     $next_exist = shell_exec($next_exist_cmd);
                     preg_match("/(.*)Nextflow file(.*)exists(.*)/", $next_exist, $matches);
                     $log_array['next_exist'] = $next_exist;
                     if ($matches[2] == " ") {
                         $next_submit_pid= shell_exec($cmd); //"Job <203477> is submitted to queue <long>.\n"
                         if (!$next_submit_pid) {
                             $this->writeLog($project_pipeline_id,'Connection failed while running nextflow in the cluster','a');
                             die(json_encode('Connection failed while running nextflow in the cluster'));
                         }
                            $log_array['next_submit_pid'] = $next_submit_pid;
                            return json_encode($log_array);
                     }
                }
                $this->writeLog($project_pipeline_id,'Connection failed while running nextflow in the cluster','a');
                die(json_encode('Connection failed. Nextflow file not exists in cluster'));
            }
        } else if ($profileType == "amazon") {
            //get nextflow executor parameters
            list($outdir, $proPipeCmd, $jobname, $singu_check, $singu_img, $imageCmd) = $this->getNextExecParam($project_pipeline_id,$ownerID);
            //get username and hostname and exec info for connection
            list($connect, $next_path, $profileCmd, $executor, $next_time, $next_queue, $next_memory, $next_cpu, $executor_job, $ssh_id)=$this->getNextConnectExec($profileId,$ownerID, $profileType);
            //get nextflow input parameters
            $next_inputs = $this->getNextInputs($executor, $project_pipeline_id,$ownerID);
            //get cmd before run
            $preCmd = $this->getPreCmd ($profileCmd,$proPipeCmd, $imageCmd);
            //eg. /project/umw_biocore/bin
            $next_path_real = $this->getNextPathReal($next_path);
            //get userpky
            $userpky = "{$this->ssh_path}/{$ownerID}_{$ssh_id}_ssh_pri.pky";
            if (!file_exists($userpky)) {
                $this->writeLog($project_pipeline_id,'Private key is not found!','a');
                die(json_encode('Private key is not found!'));
            }
            $run_path_real = "../{$this->run_path}/run{$project_pipeline_id}";
            $dolphin_path_real = "$outdir/run{$project_pipeline_id}";
            //check if files are exist
            $next_exist_cmd= "ssh {$this->ssh_settings} -i $userpky $connect test  -f \"$dolphin_path_real/nextflow.nf\"  && echo \"Nextflow file exists\" || echo \"Nextflow file not exists\" 2>&1 & echo $! &";
            $next_exist = shell_exec($next_exist_cmd);
            $this->writeLog($project_pipeline_id,$next_exist_cmd,'a');
            preg_match("/(.*)Nextflow file(.*)exists(.*)/", $next_exist, $matches);
            $log_array['next_exist'] = $next_exist;
            // if $matches[2] == " ", it means nextflow file is exist 
            if ($matches[2] == " ") {
            $exec_next_all = $this->getExecNextAll($executor, $dolphin_path_real, $next_path_real, $next_inputs, $next_queue, $next_cpu, $next_time, $next_memory, $jobname, $executor_job);
                
            $cmd="ssh {$this->ssh_settings}  -i $userpky $connect \"cd $dolphin_path_real $preCmd && $exec_next_all\" >> $run_path_real/log.txt 2>&1 & echo $! &";
            $next_submit_pid= shell_exec($cmd); //"Job <203477> is submitted to queue <long>.\n"
            $this->writeLog($project_pipeline_id,$cmd,'a');
            if (!$next_submit_pid) {
                $this->writeLog($project_pipeline_id,'Connection failed while running nextflow in the cluster','a');
                die(json_encode('Connection failed while running nextflow in the cluster'));
            }
            $log_array['next_submit_pid'] = $next_submit_pid;
            return json_encode($log_array);
            
            }else if ($matches[2] == " not "){
                for( $i= 0 ; $i < 3 ; $i++ ){
                     sleep(3);
                     $next_exist = shell_exec($next_exist_cmd);
                     preg_match("/(.*)Nextflow file(.*)exists(.*)/", $next_exist, $matches);
                     $log_array['next_exist'] = $next_exist;
                     if ($matches[2] == " ") {
                         $next_submit_pid= shell_exec($cmd); //"Job <203477> is submitted to queue <long>.\n"
                         if (!$next_submit_pid) {
                             $this->writeLog($project_pipeline_id,'Connection failed while running nextflow in the cluster','a');
                             die(json_encode('Connection failed while running nextflow in the cluster'));
                         }
                         $log_array['next_submit_pid'] = $next_submit_pid;
                         return json_encode($log_array);
                     }
                }
                $this->writeLog($project_pipeline_id,'Connection failed while running nextflow in the cluster','a');
                die(json_encode('Connection failed while running nextflow in the cluster'));
            }
        }
    }
    
    public function generateKeys($ownerID) {
        $cmd = "rm -rf {$this->ssh_path}/.tmp$ownerID && mkdir -p {$this->ssh_path}/.tmp$ownerID && cd {$this->ssh_path}/.tmp$ownerID && ssh-keygen -f tkey -t rsa -N '' > logTemp.txt 2>&1 & echo $! &";
        $log_array = $this->runCommand ($cmd, 'create_key', '');
        if (preg_match("/([0-9]+)(.*)/", $log_array['create_key'])){
             $log_array['create_key_status'] = "active";
        }else {
             $log_array['create_key_status'] = "error";
        }
        return json_encode($log_array);
    }
     public function readGenerateKeys($ownerID) {
        $keyPubPath ="{$this->ssh_path}/.tmp$ownerID/tkey.pub";
        $keyPriPath ="{$this->ssh_path}/.tmp$ownerID/tkey";
        $keyPub = $this->readFile($keyPubPath);
        $keyPri = $this->readFile($keyPriPath);
        $log_array = array('$keyPub' => $keyPub);
        $log_array['$keyPri'] = $keyPri;
         //remove the directory after reading files.
        $cmd = "rm -rf {$this->ssh_path}/.tmp$ownerID 2>&1 & echo $! &";
        $log_remove = $this->runCommand ($cmd, 'remove_key', '');
    return json_encode($log_array);
    }
    function insertKey($id, $key, $type, $ownerID){
            mkdir("{$this->ssh_path}", 0700, true);
        if ($type == 'clu'){
            $file = fopen("{$this->ssh_path}/{$ownerID}_{$id}.pky", 'w');//creates new file
            fwrite($file, $key);
            fclose($file);
            chmod("{$this->ssh_path}/{$ownerID}_{$id}.pky", 0600); 
        } else if ($type == 'amz_pri'){
            $file = fopen("{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky", 'w');//creates new file
            fwrite($file, $key);
            fclose($file);
            chmod("{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky", 0600); 
        } else if ($type == 'amz_pub'){
            $file = fopen("{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky", 'w');//creates new file
            fwrite($file, $key);
            fclose($file);
            chmod("{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky", 0600); 
        } else if ($type == 'ssh_pub'){
            $file = fopen("{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky", 'w');//creates new file
            fwrite($file, $key);
            fclose($file);
            chmod("{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky", 0600); 
        } else if ($type == 'ssh_pri'){
            $file = fopen("{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky", 'w');//creates new file
            fwrite($file, $key);
            fclose($file);
            chmod("{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky", 0600); 
        }
    }
    function readKey($id, $type, $ownerID)
    {
        if ($type == 'clu'){
        $filename = "{$this->ssh_path}/{$ownerID}_{$id}.pky";
        } else if ($type == 'amz_pub' || $type == 'amz_pri'){
        $filename = "{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky";
        } else if ($type == 'ssh_pub' || $type == 'ssh_pri'){
        $filename = "{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky";
        }
        $handle = fopen($filename, 'r');//creates new file
        $content = fread($handle, filesize($filename));
        fclose($handle);
        return $content;
    }
    function delKey($id, $type, $ownerID){
        if ($type == 'clu'){
        $filename = "{$this->ssh_path}/{$ownerID}_{$id}.pky";
        } else if ($type == 'amz_pub' || $type == 'amz_pri'){
        $filename = "{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky";
        } else if ($type == 'ssh_pri' || $type == 'ssh_pub'){
        $filename = "{$this->ssh_path}/{$ownerID}_{$id}_{$type}.pky";
        }
        unlink($filename); 
    }
    
    function amazonEncode($a_key){
        $encrypted_string=openssl_encrypt($a_key,"AES-128-ECB",$this->amazon);
        return $encrypted_string;
    }
    function amazonDecode($a_key){
        $decrypted_string=openssl_decrypt($a_key,"AES-128-ECB",$this->amazon);
        return $decrypted_string;
    }
    //xxx
    function startProAmazon($id,$ownerID){
        $data = json_decode($this->getProfileAmazonbyID($id, $ownerID));
        $amazon_cre_id = $data[0]->{'amazon_cre_id'};
        $amz_data = json_decode($this->getAmzbyID($amazon_cre_id, $ownerID));
        foreach($amz_data as $d){
		$access = $d->amz_acc_key;
        $d->amz_acc_key = trim($this->amazonDecode($access));
		$secret = $d->amz_suc_key;
		$d->amz_suc_key = trim($this->amazonDecode($secret));
	    }
        $access_key = $amz_data[0]->{'amz_acc_key'};
        $secret_key = $amz_data[0]->{'amz_suc_key'};
        $default_region = $amz_data[0]->{'amz_def_reg'};
        $name = $data[0]->{'name'};
        $ssh_id = $data[0]->{'ssh_id'};
        $username = $data[0]->{'username'};
        $image_id = $data[0]->{'image_id'};
        $instance_type = $data[0]->{'instance_type'};
        $subnet_id = $data[0]->{'subnet_id'};
        $shared_storage_id = $data[0]->{'shared_storage_id'};
        $shared_storage_mnt = $data[0]->{'shared_storage_mnt'};
        $keyFile = "{$this->ssh_path}/{$ownerID}_{$ssh_id}_ssh_pub.pky";
        $nodes = $data[0]->{'nodes'};
        $autoscale_check = $data[0]->{'autoscale_check'};
        $autoscale_maxIns = $data[0]->{'autoscale_maxIns'};
        $text= "cloud { \n";
        $text.= "   userName = '$username'\n";
        $text.= "   imageId = '$image_id'\n";
        $text.= "   instanceType = '$instance_type'\n";
        $text.= "   subnetId = '$subnet_id'\n";
        $text.= "   sharedStorageId = '$shared_storage_id'\n";
        $text.= "   sharedStorageMount = '$shared_storage_mnt'\n";
        $text.= "   keyFile = '$keyFile'\n";
        if ($autoscale_check == "true"){
        $text.= "   autoscale {\n";
        $text.= "       enabled = true \n";
        if (!empty($autoscale_maxIns)){
        $text.= "       maxInstances = $autoscale_maxIns\n";
        }
        $text.= "   }\n";
        }
        $text.= "}\n";
        $text.= "aws{\n";
        $text.= "   accessKey = '$access_key'\n";
        $text.= "   secretKey = '$secret_key'\n";
        $text.= "   region = '$default_region'\n";
        $text.= "}\n";
        $this->createDirFile ("{$this->amz_path}/pro_{$id}", "nextflow.config", 'w', $text );
        //start amazon cluster
        $cmd = "cd {$this->amz_path}/pro_{$id} && yes | nextflow cloud create cluster{$id} -c $nodes > logAmzStart.txt 2>&1 & echo $! &";
        $log_array = $this->runCommand ($cmd, 'start_cloud', '');
        //xxx save pid of nextflow cloud create cluster job
        if (preg_match("/([0-9]+)(.*)/", $log_array['start_cloud'])){
            $this->updateAmazonProStatus($id, "waiting", $ownerID);
        }else {
            $this->updateAmazonProStatus($id, "terminated", $ownerID);
        }
        return json_encode($log_array);
    }
    
    function stopProAmazon($id,$ownerID){
        //stop amazon cluster
        $cmd = "cd {$this->amz_path}/pro_{$id} && yes | nextflow cloud shutdown cluster{$id} > logAmzStop.txt 2>&1 & echo $! &";
        $log_array = $this->runCommand ($cmd, 'stop_cloud', '');
        return json_encode($log_array);
    }
    
     function checkAmzStopLog($id,$ownerID){
        //read logAmzStop.txt
        $logPath ="{$this->amz_path}/pro_{$id}/logAmzStop.txt";
        $logAmzStop = $this->readFile($logPath);
        $log_array = array('logAmzStop' => $logAmzStop);
        return json_encode($log_array);
    }
     //read both start and list files
        function readAmzCloudListStart($id){
        //read logAmzCloudList.txt
        $logPath ="{$this->amz_path}/pro_{$id}/logAmzCloudList.txt";
        $logAmzCloudList = $this->readFile($logPath);
        $log_array = array('logAmzCloudList' => $logAmzCloudList);
        //read logAmzStart.txt
        $logPathStart ="{$this->amz_path}/pro_{$id}/logAmzStart.txt";
        $logAmzStart = $this->readFile($logPathStart);
        $log_array['logAmzStart'] = $logAmzStart;
        return $log_array;
    }
    
    
    
    public function checkAmazonStatus($id,$ownerID) {
        //check status 
        $amzStat = json_decode($this->getAmazonStatus($id,$ownerID)); 
        $status = $amzStat[0]->{'status'};
        if ($status == "waiting"){
            //check cloud list
            $log_array = $this->readAmzCloudListStart($id);
            if (preg_match("/running/", $log_array['logAmzCloudList'])){
                $this->updateAmazonProStatus($id, "initiated", $ownerID);
                $log_array['status'] = "initiated";
                return json_encode($log_array);
            } else if (!preg_match("/STATUS/", $log_array['logAmzCloudList']) && (preg_match("/Missing/i", $log_array['logAmzCloudList']) || preg_match("/denied/i", $log_array['logAmzCloudList']) || preg_match("/ERROR/i", $log_array['logAmzCloudList']))){
                $this->updateAmazonProStatus($id, "terminated", $ownerID);
                $log_array['status'] = "terminated";
                return json_encode($log_array);
            }else if (preg_match("/Missing/i", $log_array['logAmzStart']) || preg_match("/denied/i", $log_array['logAmzStart']) || preg_match("/ERROR/i", $log_array['logAmzStart'])  || preg_match("/couldn't/i", $log_array['logAmzStart'])  || preg_match("/help/i", $log_array['logAmzStart']) || preg_match("/wrong/i", $log_array['logAmzStart'])){
                $this->updateAmazonProStatus($id, "terminated", $ownerID);
                $log_array['status'] = "terminated";
                return json_encode($log_array);
            }else {
                //error
                $log_array['status'] = "waiting";
                return json_encode($log_array);
            }
        } else if ($status == "initiated"){
            //check cloud list
            $log_array = $this->readAmzCloudListStart($id);
            if (preg_match("/running/",$log_array['logAmzCloudList']) && preg_match("/STATUS/",$log_array['logAmzCloudList'])){
                //read logAmzStart.txt
                $amzStartPath ="{$this->amz_path}/pro_{$id}/logAmzStart.txt";
                $amzStartLog = $this->readFile($amzStartPath);
                $log_array['$amzStartLog'] = $amzStartLog;
                if (preg_match("/ssh -i(.*)/",$amzStartLog)){
                    preg_match("/ssh -i <(.*)> (.*)/",$amzStartLog, $match);
                    $sshText = $match[2];
                    $log_array['sshText'] = $sshText;
                    $log_array['status'] = "running";
                    $this->updateAmazonProStatus($id, "running", $ownerID);
                    $this->updateAmazonProSSH($id, $sshText, $ownerID);
                    
                return json_encode($log_array);
                } else {
                    $log_array['status'] = "initiated";
                return json_encode($log_array);
                }
            } else if (!preg_match("/running/",$log_array['logAmzCloudList']) && preg_match("/STATUS/",$log_array['logAmzCloudList'])){
                $this->updateAmazonProStatus($id, "terminated", $ownerID);
                $log_array['status'] = "terminated";
                return json_encode($log_array);
            } else {
                $log_array['status'] = "retry";
                return json_encode($log_array);
            }
        } else if ($status == "running"){
            //check cloud list
            $log_array = $this->readAmzCloudListStart($id);
            if (preg_match("/running/",$log_array['logAmzCloudList']) && preg_match("/STATUS/",$log_array['logAmzCloudList'])){
                $log_array['status'] = "running";
                $sshTextArr = json_decode($this->getAmazonProSSH($id, $ownerID));
                $sshText = $sshTextArr[0]->{'ssh'};
                $log_array['sshText'] = $sshText;
                return json_encode($log_array);
            } else if (!preg_match("/running/",$log_array['logAmzCloudList']) && preg_match("/STATUS/",$log_array['logAmzCloudList'])){
                $this->updateAmazonProStatus($id, "terminated", $ownerID);
                $log_array['status'] = "terminated";
                return json_encode($log_array);
            } else {
                $log_array['status'] = "retry";
                return json_encode($log_array);
            }
        } 
        else if ($status == "terminated"){
                $log_array = $this->readAmzCloudListStart($id);
                $log_array['status'] = "terminated";
                return json_encode($log_array);
        } else if ($status == "" ){
//                $this->updateAmazonProStatus($id, "inactive", $ownerID);
                $log_array = array('status' => 'inactive');
                return json_encode($log_array);
        }else if ($status == "inactive"){
                $log_array = array('status' => 'inactive');
                return json_encode($log_array);
        }
    }
    
            //check cloud list
    public function runAmazonCloudCheck($id,$ownerID){
        $cmd = "cd {$this->amz_path}/pro_$id && rm -f logAmzCloudList.txt && nextflow cloud list cluster$id >> logAmzCloudList.txt 2>&1 & echo $! &";
        $log_array = $this->runCommand ($cmd, 'cloudlist', '');
        return json_encode($log_array);
    }
//    ---------------  Users ---------------
    public function getUser($google_id) {
        $sql = "SELECT * FROM users WHERE google_id = '$google_id'";
        return self::queryTable($sql);
    }
    public function getUserLess($google_id) {
        $sql = "SELECT username, name, email, google_image FROM users WHERE google_id = '$google_id'";
        return self::queryTable($sql);
    }
    public function insertUser($google_id, $name, $email, $google_image, $username) {
        $sql = "INSERT INTO users(google_id, name, email, google_image, username, institute, lab, memberdate, date_created, date_modified, perms) VALUES 
			('$google_id', '$name', '$email', '$google_image', '$username', '', '', now() , now(), now(), '3')";
        return self::insTable($sql);
    }
    
    public function updateUser($id, $google_id, $name, $email, $google_image, $username) {
        $sql = "UPDATE users SET id='$id', google_id='$google_id', name='$name', email='$email', google_image='$google_image', username='$username', last_modified_user='$id' WHERE id = '$id'";
        return self::runSQL($sql);
    }
//    ------------- Profiles   ------------
    public function insertSSH($name, $check_userkey, $check_ourkey, $ownerID) {
        $sql = "INSERT INTO ssh(name, check_userkey, check_ourkey, date_created, date_modified, last_modified_user, perms, owner_id) VALUES 
			('$name', '$check_userkey', '$check_ourkey', now() , now(), '$ownerID', '3', '$ownerID')";
        return self::insTable($sql);
    }
    public function updateSSH($id, $name, $check_userkey, $check_ourkey, $ownerID) {
        $sql = "UPDATE ssh SET name='$name', check_userkey='$check_userkey', check_ourkey='$check_ourkey', date_modified = now(), last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
      public function insertAmz($name, $amz_def_reg, $amz_acc_key, $amz_suc_key, $ownerID) {
        $sql = "INSERT INTO amazon_credentials (name, amz_def_reg, amz_acc_key, amz_suc_key, date_created, date_modified, last_modified_user, perms, owner_id) VALUES 
			('$name', '$amz_def_reg', '$amz_acc_key', '$amz_suc_key', now() , now(), '$ownerID', '3', '$ownerID')";
        return self::insTable($sql);
    }
    public function updateAmz($id, $name, $amz_def_reg,$amz_acc_key,$amz_suc_key, $ownerID) {
        $sql = "UPDATE amazon_credentials SET name='$name', amz_def_reg='$amz_def_reg', amz_acc_key='$amz_acc_key', amz_suc_key='$amz_suc_key', date_modified = now(), last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function getAmz($ownerID) {
        $sql = "SELECT * FROM amazon_credentials WHERE owner_id = '$ownerID'";
        return self::queryTable($sql);    
    }
    public function getAmzbyID($id,$ownerID) {
        $sql = "SELECT * FROM amazon_credentials WHERE owner_id = '$ownerID' and id = '$id'";
        return self::queryTable($sql);    
    }
    public function getProfileLocal($ownerID) {
        $sql = "SELECT id, name, executor, next_path, cmd, next_time, next_queue, next_memory, next_cpu, executor_job, job_memory, job_queue, job_time, job_cpu FROM profile_local WHERE owner_id = '$ownerID'";
        return self::queryTable($sql);    
    }
    public function getProfileLocalbyID($id,$ownerID) {
        $sql = "SELECT id, name, executor, next_path, cmd, next_time, next_queue, next_memory, next_cpu, executor_job, job_memory, job_queue, job_time, job_cpu FROM profile_local WHERE owner_id = '$ownerID' and id = '$id'";
        return self::queryTable($sql);    
    }
     public function getSSH($ownerID) {
        $sql = "SELECT * FROM ssh WHERE owner_id = '$ownerID'";
        return self::queryTable($sql);    
    }
    public function getSSHbyID($id,$ownerID) {
        $sql = "SELECT * FROM ssh WHERE owner_id = '$ownerID' and id = '$id'";
        return self::queryTable($sql);    
    }
    public function getProfileClusterbyID($id, $ownerID) {
        $sql = "SELECT * FROM profile_cluster WHERE owner_id = '$ownerID' and id = '$id'";
        return self::queryTable($sql); 
    }
    public function getProfileCluster($ownerID) {
        $sql = "SELECT * FROM profile_cluster WHERE owner_id = '$ownerID'";
        return self::queryTable($sql);    
    }
    public function getProfileAmazon($ownerID) {
        $sql = "SELECT * FROM profile_amazon WHERE owner_id = '$ownerID'";
        return self::queryTable($sql);    
    }
    public function getProfileAmazonbyID($id, $ownerID) {
        $sql = "SELECT p.*, u.username 
        FROM profile_amazon p
        INNER JOIN users u ON p.owner_id = u.id
        WHERE p.owner_id = '$ownerID' and p.id = '$id'";
        return self::queryTable($sql);    
    }
    
    public function insertProfileLocal($name, $executor,$next_path, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $ownerID) {
        $sql = "INSERT INTO profile_local (name, executor, next_path, cmd, next_memory, next_queue, next_time, next_cpu, executor_job, job_memory, job_queue, job_time, job_cpu, owner_id, perms, date_created, date_modified, last_modified_user) VALUES ('$name', '$executor','$next_path', '$cmd', '$next_memory', '$next_queue', '$next_time', '$next_cpu', '$executor_job', '$job_memory', '$job_queue', '$job_time', '$job_cpu', '$ownerID', 3, now(), now(), '$ownerID')";
        return self::insTable($sql);
    }

    public function updateProfileLocal($id, $name, $executor,$next_path, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $ownerID) {
        $sql = "UPDATE profile_local SET name='$name', executor='$executor', next_path='$next_path', cmd='$cmd', next_memory='$next_memory', next_queue='$next_queue', next_time='$next_time', next_cpu='$next_cpu', executor_job='$executor_job', job_memory='$job_memory', job_queue='$job_queue', job_time='$job_time', job_cpu='$job_cpu',  last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    
    public function insertProfileCluster($name, $executor,$next_path, $username, $hostname, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $ssh_id, $ownerID) {
        $sql = "INSERT INTO profile_cluster(name, executor, next_path, username, hostname, cmd, next_memory, next_queue, next_time, next_cpu, executor_job, job_memory, job_queue, job_time, job_cpu, ssh_id, owner_id, perms, date_created, date_modified, last_modified_user) VALUES('$name', '$executor', '$next_path', '$username', '$hostname', '$cmd', '$next_memory', '$next_queue', '$next_time', '$next_cpu', '$executor_job', '$job_memory', '$job_queue', '$job_time', '$job_cpu', '$ssh_id', '$ownerID', 3, now(), now(), '$ownerID')";
        return self::insTable($sql);
    }

    public function updateProfileCluster($id, $name, $executor,$next_path, $username, $hostname, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $ssh_id, $ownerID) {
        $sql = "UPDATE profile_cluster SET name='$name', executor='$executor', next_path='$next_path', username='$username', hostname='$hostname', cmd='$cmd', next_memory='$next_memory', next_queue='$next_queue', next_time='$next_time', next_cpu='$next_cpu', executor_job='$executor_job', job_memory='$job_memory', job_queue='$job_queue', job_time='$job_time', job_cpu='$job_cpu', ssh_id='$ssh_id', last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function insertProfileAmazon($name, $executor, $next_path, $ins_type, $image_id, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $subnet_id, $shared_storage_id,$shared_storage_mnt, $ssh_id, $amazon_cre_id, $ownerID) {
        $sql = "INSERT INTO profile_amazon(name, executor, next_path, instance_type, image_id, cmd, next_memory, next_queue, next_time, next_cpu, executor_job, job_memory, job_queue, job_time, job_cpu, subnet_id, shared_storage_id, shared_storage_mnt, ssh_id, amazon_cre_id, owner_id, perms, date_created, date_modified, last_modified_user) VALUES('$name', '$executor', '$next_path', '$ins_type', '$image_id', '$cmd', '$next_memory', '$next_queue', '$next_time', '$next_cpu', '$executor_job', '$job_memory', '$job_queue', '$job_time', '$job_cpu', '$subnet_id','$shared_storage_id','$shared_storage_mnt','$ssh_id','$amazon_cre_id','$ownerID', 3, now(), now(), '$ownerID')";
        return self::insTable($sql);
    }
    public function updateProfileAmazon($id, $name, $executor, $next_path, $ins_type, $image_id, $cmd, $next_memory, $next_queue, $next_time, $next_cpu, $executor_job, $job_memory, $job_queue, $job_time, $job_cpu, $subnet_id, $shared_storage_id, $shared_storage_mnt, $ssh_id, $amazon_cre_id, $ownerID) {
        $sql = "UPDATE profile_amazon SET name='$name', executor='$executor', next_path='$next_path', instance_type='$ins_type', image_id='$image_id', cmd='$cmd', next_memory='$next_memory', next_queue='$next_queue', next_time='$next_time', next_cpu='$next_cpu', executor_job='$executor_job', job_memory='$job_memory', job_queue='$job_queue', job_time='$job_time', job_cpu='$job_cpu', subnet_id='$subnet_id', shared_storage_id='$shared_storage_id', shared_storage_mnt='$shared_storage_mnt', ssh_id='$ssh_id', amazon_cre_id='$amazon_cre_id', last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function updateProfileAmazonNode($id, $nodes, $autoscale_check, $autoscale_maxIns, $ownerID) {
        $sql = "UPDATE profile_amazon SET nodes='$nodes', autoscale_check='$autoscale_check', autoscale_maxIns='$autoscale_maxIns', last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    
    public function updateAmazonProStatus($id, $status, $ownerID) {
        $sql = "UPDATE profile_amazon SET status='$status', date_modified= now(), last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function updateAmazonProPid($id, $pid, $ownerID) {
        $sql = "UPDATE profile_amazon SET pid='$pid', date_modified= now(), last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function updateAmazonProSSH($id, $sshText, $ownerID) {
        $sql = "UPDATE profile_amazon SET ssh='$sshText', date_modified= now(), last_modified_user ='$ownerID'  WHERE id = '$id'";
		return self::runSQL($sql);
    }
    public function getAmazonProSSH($id, $ownerID) {
        $sql = "SELECT ssh FROM profile_amazon WHERE id = '$id' AND owner_id = '$ownerID'";
		return self::queryTable($sql);
    }
         public function removeAmz($id) {
        $sql = "DELETE FROM amazon_credentials WHERE id = '$id'";
        return self::runSQL($sql);
    }
     public function removeSSH($id) {
        $sql = "DELETE FROM ssh WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProLocal($id) {
        $sql = "DELETE FROM profile_local WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProCluster($id) {
        $sql = "DELETE FROM profile_cluster WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProAmazon($id) {
        $sql = "DELETE FROM profile_amazon WHERE id = '$id'";
        return self::runSQL($sql);
    }
//    ------------- Parameters ------------
    public function getAllParameters($ownerID) {
        if ($ownerID == ""){
        $ownerID ="''";
        } else {
            $userRole = json_decode($this->getUserRole($ownerID))[0]->{'role'};
            if ($userRole == "admin"){
                $sql = "SELECT DISTINCT p.id, p.file_type, p.qualifier, p.name, p.group_id, p.perms FROM parameter p";
                return self::queryTable($sql);
            }
        }
        
		$sql = "SELECT DISTINCT p.id, p.file_type, p.qualifier, p.name, p.group_id, p.perms 
        FROM parameter p
        LEFT JOIN user_group ug ON p.group_id=ug.g_id
        WHERE p.owner_id = '$ownerID' OR p.perms = 63 OR (ug.u_id ='$ownerID' and p.perms = 15)";
		return self::queryTable($sql);
    }
    public function getEditDelParameters($ownerID) {
        if ($ownerID == ""){
            $ownerID ="''";
        }
        $sql = "SELECT * FROM parameter WHERE owner_id = '$ownerID'";
        return self::queryTable($sql);
    }

    public function insertParameter($name, $qualifier, $file_type, $ownerID) {
        $sql = "INSERT INTO parameter(name, qualifier, file_type, owner_id, perms, date_created, date_modified, last_modified_user) VALUES 
			('$name', '$qualifier', '$file_type', '$ownerID', 3, now(), now(), '$ownerID')";
        return self::insTable($sql);
    }

    public function updateParameter($id, $name, $qualifier, $file_type, $ownerID) {
        $sql = "UPDATE parameter SET name='$name', qualifier='$qualifier', last_modified_user ='$ownerID', file_type='$file_type'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    
    public function insertProcessGroup($group_name, $ownerID) {
        $sql = "INSERT INTO process_group (owner_id, group_name, date_created, date_modified, last_modified_user, perms) VALUES ('$ownerID', '$group_name', now(), now(), '$ownerID', 3)";
        return self::insTable($sql);
    }

    public function updateProcessGroup($id, $group_name, $ownerID) {
        $sql = "UPDATE process_group SET group_name='$group_name', last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }

    public function removeParameter($id) {
        $sql = "DELETE FROM parameter WHERE id = '$id'";
        return self::runSQL($sql);
    }

    public function removeProcessGroup($id) {
        $sql = "DELETE FROM process_group WHERE id = '$id'";
        return self::runSQL($sql);
    }

    // --------- Process -----------

//    public function getAllProcesses() {
//        $sql = "SELECT id, name, script FROM process";
//        return self::queryTable($sql);
//    }

    public function getAllProcessGroups($ownerID) {
        $userRole = json_decode($this->getUserRole($ownerID))[0]->{'role'};
        if ($userRole == "admin"){
            $sql = "SELECT DISTINCT pg.id, pg.group_name 
            FROM process_group pg";
            return self::queryTable($sql);
        }
        $sql = "SELECT DISTINCT pg.id, pg.group_name 
        FROM process_group pg
        LEFT JOIN user_group ug ON pg.group_id=ug.g_id
        WHERE pg.owner_id = '$ownerID' OR pg.perms = 63 OR (ug.u_id ='$ownerID' and pg.perms = 15)";
        return self::queryTable($sql);
    }
    public function getEditDelProcessGroups($ownerID) {
        $sql = "SELECT id, group_name FROM process_group WHERE owner_id = '$ownerID'";
        return self::queryTable($sql);
    }
    
    public function insertProcess($name, $process_gid, $summary, $process_group_id, $script, $script_header, $rev_id, $rev_comment, $group, $perms, $publish, $script_mode, $script_mode_header, $ownerID) {
        $sql = "INSERT INTO process(name, process_gid, summary, process_group_id, script, script_header, rev_id, rev_comment, owner_id, date_created, date_modified, last_modified_user, perms, group_id, publish, script_mode, script_mode_header) VALUES ('$name', '$process_gid', '$summary', '$process_group_id', '$script', '$script_header', '$rev_id','$rev_comment', '$ownerID', now(), now(), '$ownerID', '$perms', '$group', '$publish','$script_mode', '$script_mode_header')";
        return self::insTable($sql);
    }

    public function updateProcess($id, $name, $process_gid, $summary, $process_group_id, $script, $script_header, $group, $perms, $publish, $script_mode, $script_mode_header, $ownerID) {
        $sql = "UPDATE process SET name= '$name', process_gid='$process_gid', summary='$summary', process_group_id='$process_group_id', script='$script', script_header='$script_header',  last_modified_user='$ownerID', group_id='$group', perms='$perms', publish='$publish', script_mode='$script_mode', script_mode_header='$script_mode_header' WHERE id = '$id'";
        return self::runSQL($sql);
    }

    public function removeProcess($id) {
        $sql = "DELETE FROM process WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProject($id) {
        $sql = "DELETE FROM project WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeGroup($id) {
        $sql = "DELETE FROM groups WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeUserGroup($id) {
        $sql = "DELETE FROM user_group WHERE g_id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProjectPipeline($id) {
        $sql = "DELETE FROM project_pipeline WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeInput($id) {
        $sql = "DELETE FROM input WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProjectPipelineInput($id) {
        $sql = "DELETE FROM project_pipeline_input WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProjectPipelineInputByPipe($id) {
        $sql = "DELETE FROM project_pipeline_input WHERE project_pipeline_id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProjectPipelineInputByGnum($id, $g_num) {
        $sql = "DELETE FROM project_pipeline_input WHERE project_pipeline_id = '$id' AND g_num = '$g_num'";
        return self::runSQL($sql);
    }
    public function removeProjectPipelineInputbyInputId($id) {
        $sql = "DELETE FROM project_pipeline_input WHERE input_id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProjectInput($id) {
        $sql = "DELETE FROM project_input WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProjectPipelinebyProjectID($id) {
        $sql = "DELETE FROM project_pipeline WHERE project_id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProjectPipelineInputbyProjectID($id) {
        $sql = "DELETE FROM project_pipeline_input WHERE project_id = '$id'";
        return self::runSQL($sql);
    }
    public function removeProjectInputbyProjectID($id) {
        $sql = "DELETE FROM project_input WHERE project_id = '$id'";
        return self::runSQL($sql);
    }
    
    public function removeProcessByProcessGroupID($process_group_id) {
        $sql = "DELETE FROM process WHERE process_group_id = '$process_group_id'";
        return self::runSQL($sql);
    }
//    ------ Groups -------
    public function getAllGroups() {
        $sql = "SELECT id, name FROM groups";
        return self::queryTable($sql);
    }
    public function getGroups($id,$ownerID) {
        $where = ""; 
		if ($id != ""){
			$where = " where g.id = '$id'";
		}
		$sql = "SELECT g.id, g.name, g.date_created, u.username, g.date_modified 
                FROM groups g 
                INNER JOIN users u ON g.owner_id = u.id $where";
		return self::queryTable($sql);
    }
    
    public function getJoinGroups($ownerID) {
        $sql = "SELECT id, name FROM groups WHERE id NOT IN (
		SELECT g_id
		FROM user_group
		WHERE u_id = '$ownerID')";
        return self::queryTable($sql);
    }
    
    public function viewGroupMembers($g_id) {
        $sql = "SELECT id, username
	           FROM users
	           WHERE id in (
		          SELECT u_id
		          FROM user_group
		          WHERE g_id = '$g_id')";
        return self::queryTable($sql);
    }
    
    public function getMemberAdd($g_id) {
        $sql = "SELECT id, username
	           FROM users
	           WHERE id NOT IN (
		          SELECT u_id
		          FROM user_group
		          WHERE g_id = '$g_id')";
        return self::queryTable($sql);
    }
    
    public function getUserGroups($ownerID) {
		$sql = "SELECT g.id, g.name, g.date_created, u.username, g.owner_id, ug.u_id
                FROM groups g 
                INNER JOIN user_group ug ON  ug.g_id =g.id 
                INNER JOIN users u ON u.id = g.owner_id 
                where ug.u_id = '$ownerID'";
		return self::queryTable($sql);
    }
    public function getUserRole($ownerID) {
		$sql = "SELECT role 
                FROM users  
                where id = '$ownerID'";
		return self::queryTable($sql);
    }
    public function insertGroup($name, $ownerID) {
        $sql = "INSERT INTO groups(name, owner_id, date_created, date_modified, last_modified_user, perms) VALUES ('$name', '$ownerID', now(), now(), '$ownerID', 3)";
        return self::insTable($sql);
    }
    public function insertUserGroup($g_id, $u_id, $ownerID) {
        $sql = "INSERT INTO user_group (g_id, u_id, owner_id, date_created, date_modified, last_modified_user, perms) VALUES ('$g_id', '$u_id', '$ownerID', now(), now(), '$ownerID', 3)";
        return self::insTable($sql);
    }

    public function updateGroup($id, $name, $ownerID) {
        $sql = "UPDATE groups SET name= '$name', last_modified_user = '$ownerID', date_modified = now() WHERE id = '$id'";
        return self::runSQL($sql);
    }
    
//    ----------- Projects   ---------
    public function getProjects($id,$ownerID) {
        $where = " where p.owner_id = '$ownerID' OR p.perms = 63 OR (ug.u_id ='$ownerID' and p.perms = 15)"; 
		if ($id != ""){
			$where = " where p.id = '$id' AND (p.owner_id = '$ownerID' OR p.perms = 63 OR (ug.u_id ='$ownerID' and p.perms = 15))";
		}
		$sql = "SELECT DISTINCT p.id, p.name, p.summary, p.date_created, u.username, p.date_modified, IF(p.owner_id='$ownerID',1,0) as own
        FROM project p 
        INNER JOIN users u ON p.owner_id = u.id 
        LEFT JOIN user_group ug ON p.group_id=ug.g_id
        $where";
		return self::queryTable($sql);
    }
    public function insertProject($name, $summary, $ownerID) {
        $sql = "INSERT INTO project(name, summary, owner_id, date_created, date_modified, last_modified_user, perms) VALUES ('$name', '$summary', '$ownerID', now(), now(), '$ownerID', 3)";
        return self::insTable($sql);
    }

    public function updateProject($id, $name, $summary, $ownerID) {
        $sql = "UPDATE project SET name= '$name', summary= '$summary', last_modified_user = '$ownerID', date_modified = now() WHERE id = '$id'";
        return self::runSQL($sql);
    }

//    ----------- Runs     ---------
    public function insertRun($project_pipeline_id, $status, $attempt, $ownerID) {
        $sql = "INSERT INTO run (project_pipeline_id, run_status, attempt, owner_id, perms, date_created, date_modified, last_modified_user) VALUES 
			('$project_pipeline_id', '$status', '$attempt', '$ownerID', 3, now(), now(), '$ownerID')";
        return self::insTable($sql);
    }
    public function insertRunLog($project_pipeline_id, $status, $ownerID) {
        $sql = "INSERT INTO run_log (project_pipeline_id, run_status, owner_id, perms, date_created, date_modified, last_modified_user) VALUES 
			('$project_pipeline_id', '$status', '$ownerID', 3, now(), now(), '$ownerID')";
        return self::insTable($sql);
    }
    //get maximum of $project_pipeline_id
    public function updateRunLog($project_pipeline_id, $status, $duration, $ownerID) {
         $sql = "UPDATE run_log SET run_status='$status', duration='$duration', date_ended= now(), date_modified= now(), last_modified_user ='$ownerID'  WHERE project_pipeline_id = '$project_pipeline_id' ORDER BY id DESC LIMIT 1";
        return self::runSQL($sql);
    }
    public function updateRunStatus($project_pipeline_id, $status, $ownerID) {
        $sql = "UPDATE run SET run_status='$status', date_modified= now(), last_modified_user ='$ownerID'  WHERE project_pipeline_id = '$project_pipeline_id'";
        return self::runSQL($sql);
    }
    public function updateRunAttempt($project_pipeline_id, $attempt, $ownerID) {
        $sql = "UPDATE run SET attempt= '$attempt', date_modified= now(), last_modified_user ='$ownerID'  WHERE project_pipeline_id = '$project_pipeline_id'";
        return self::runSQL($sql);
    }
    public function updateRunPid($project_pipeline_id, $pid, $ownerID) {
        $sql = "UPDATE run SET pid='$pid', date_modified= now(), last_modified_user ='$ownerID'  WHERE project_pipeline_id = '$project_pipeline_id'";
        return self::runSQL($sql);
    }
    public function getRunPid($project_pipeline_id) {
        $sql = "SELECT pid FROM run WHERE project_pipeline_id = '$project_pipeline_id'";
        return self::queryTable($sql);
    }
    public function getRunAttempt($project_pipeline_id) {
        $sql = "SELECT attempt FROM run WHERE project_pipeline_id = '$project_pipeline_id'";
        return self::queryTable($sql);
    }
    public function getServerLog($project_pipeline_id,$ownerID) {
        $path= "../{$this->run_path}/run$project_pipeline_id";
        // get contents of a file into a string
        $filename = "$path/log.txt";
        $handle = fopen($filename, "r");
        $content = fread($handle, filesize($filename));
        fclose($handle);
        return json_encode($content);
    }
    public function getRun($project_pipeline_id,$ownerID) {
        $sql = "SELECT * FROM run WHERE project_pipeline_id = '$project_pipeline_id'";
		return self::queryTable($sql);
    }
    public function getRunStatus($project_pipeline_id,$ownerID) {
        $sql = "SELECT run_status FROM run WHERE project_pipeline_id = '$project_pipeline_id'";
		return self::queryTable($sql);
    }
    public function getAmazonStatus($id,$ownerID) {
        $sql = "SELECT status FROM profile_amazon WHERE id = '$id'";
		return self::queryTable($sql);
    }
    public function getAmazonPid($id,$ownerID) {
        $sql = "SELECT pid FROM profile_amazon WHERE id = '$id'";
		return self::queryTable($sql);
    }
    
    public function checkRunPid($pid,$profileType,$profileId,$ownerID) {
        if ($profileType == 'local'){
            if (file_exists( "/proc/$pid" )){
            return json_encode("running");
            } else {
            return json_encode("completed");  
            }
        } else if ($profileType == 'cluster'){
            $cluData=$this->getProfileClusterbyID($profileId, $ownerID);
            $cluDataArr=json_decode($cluData,true);
            $connect = $cluDataArr[0]["username"]."@".$cluDataArr[0]["hostname"];
            $ssh_id = $cluDataArr[0]["ssh_id"];
            $userpky = "{$this->ssh_path}/{$ownerID}_{$ssh_id}_ssh_pri.pky";
            $check_run = shell_exec("ssh {$this->ssh_settings} -i $userpky $connect 'bjobs' 2>&1 &");
            if (preg_match("/$pid/",$check_run)){
            return json_encode('running');
            } else {
            return json_encode('completed');
            }
        }
    }
    public function renameLogSSH($project_pipeline_id,$profileType, $profileId, $ownerID) {
        if ($profileType == 'cluster'){
            //getRun pid
            $attemptData = json_decode($this->getRunAttempt($project_pipeline_id));
            $attempt = $attemptData[0]->{'attempt'};
            if (empty($attempt) || $attempt == 0 || $attempt == "0"){
                $attempt = "0";
            }
            $proPipeAll = json_decode($this->getProjectPipelines($project_pipeline_id,"",$ownerID));
            $outdir = $proPipeAll[0]->{'output_dir'};
            $dolphin_path_real = "$outdir/run{$project_pipeline_id}";
            
            $cluData=$this->getProfileClusterbyID($profileId, $ownerID);
            $cluDataArr=json_decode($cluData,true);
            $connect = $cluDataArr[0]["username"]."@".$cluDataArr[0]["hostname"];
            $ssh_id = $cluDataArr[0]["ssh_id"];
            $userpky = "{$this->ssh_path}/{$ownerID}_{$ssh_id}_ssh_pri.pky";
            $run_path_real = "../{$this->run_path}/run{$project_pipeline_id}";
            $cmd = "ssh {$this->ssh_settings}  -i $userpky $connect \"mv $dolphin_path_real/log.txt $dolphin_path_real/log$attempt.txt \" 2>&1 & echo $! &";
            $log_array = $this->runCommand ($cmd, 'rename_log', '');
            return json_encode($log_array);
        }
    }
    
    public function getNextflowLog($project_pipeline_id,$profileType,$profileId,$ownerID) {
         if ($profileType == 'cluster'){
            $cluData=$this->getProfileClusterbyID($profileId, $ownerID);
            $cluDataArr=json_decode($cluData,true);
            $connect = $cluDataArr[0]["username"]."@".$cluDataArr[0]["hostname"];
            $ssh_id = $cluDataArr[0]["ssh_id"];
            $userpky = "{$this->ssh_path}/{$ownerID}_{$ssh_id}_ssh_pri.pky";
            if (!file_exists($userpky)) die(json_encode('Private key is not found!'));
            // get outputdir
            $proPipeAll = json_decode($this->getProjectPipelines($project_pipeline_id,"",$ownerID));
            $outdir = $proPipeAll[0]->{'output_dir'};
            $dolphin_path_real = "$outdir/run{$project_pipeline_id}";
            $nextflow_log = shell_exec("ssh {$this->ssh_settings} -i $userpky $connect 'cat $dolphin_path_real/log.txt' 2>&1 &");
             return json_encode($nextflow_log);
        } else if ($profileType == 'amazon'){
            $cluData=$this->getProfileAmazonbyID($profileId, $ownerID);
            $cluDataArr=json_decode($cluData,true);
            $connect = $cluDataArr[0]["ssh"];
            $ssh_id = $cluDataArr[0]["ssh_id"];
            $userpky = "{$this->ssh_path}/{$ownerID}_{$ssh_id}_ssh_pri.pky";
            if (!file_exists($userpky)) die(json_encode('Private key is not found!'));
            // get outputdir
            $proPipeAll = json_decode($this->getProjectPipelines($project_pipeline_id,"",$ownerID));
            $outdir = $proPipeAll[0]->{'output_dir'};
            $dolphin_path_real = "$outdir/run{$project_pipeline_id}";
            $nextflow_log = shell_exec("ssh {$this->ssh_settings} -i $userpky $connect 'cat $dolphin_path_real/log.txt' 2>&1 &");
             return json_encode($nextflow_log);
        }
    }
    
//    ----------- Inputs, Project Inputs   ---------
    
    public function getInputs($id,$ownerID) {
        $where = " where i.owner_id = '$ownerID' OR i.perms = 63 OR (ug.u_id ='$ownerID' and i.perms = 15)"; 
		if ($id != ""){
			$where = " where i.id = '$id' AND (i.owner_id = '$ownerID' OR i.perms = 63 OR (ug.u_id ='$ownerID' and i.perms = 15))";
		}
		$sql = "SELECT DISTINCT i.id, i.name, IF(i.owner_id='$ownerID',1,0) as own
        FROM input i
        LEFT JOIN user_group ug ON i.group_id=ug.g_id 
        $where";
		return self::queryTable($sql);
    }
    public function getProjectInputs($project_id,$ownerID) {
        $where = " where pi.project_id = '$project_id' AND (pi.owner_id = '$ownerID' OR pi.perms = 63 OR (ug.u_id ='$ownerID' and pi.perms = 15))" ; 
		$sql = "SELECT DISTINCT pi.id, i.id as input_id, i.name, IF(pi.owner_id='$ownerID',1,0) as own
                FROM project_input pi
                INNER JOIN input i ON i.id = pi.input_id
                LEFT JOIN user_group ug ON pi.group_id=ug.g_id
                $where";
		return self::queryTable($sql);
    }
    public function getProjectInput($id,$ownerID) {
        $where = " where pi.id = '$id' AND (pi.owner_id = '$ownerID' OR pi.perms = 63)" ; 
		$sql = "SELECT pi.id, i.id as input_id, i.name
                FROM project_input pi
                INNER JOIN input i ON i.id = pi.input_id
                $where";
		return self::queryTable($sql);
    }
    public function insertProjectInput($project_id, $input_id, $ownerID) {
        $sql = "INSERT INTO project_input(project_id, input_id, owner_id, perms, date_created, date_modified, last_modified_user) VALUES 
			('$project_id', '$input_id', '$ownerID', 3, now(), now(), '$ownerID')";
        return self::insTable($sql);
    }
    public function insertInput($name, $ownerID) {
        $sql = "INSERT INTO input(name, owner_id, perms, date_created, date_modified, last_modified_user) VALUES 
			('$name', '$ownerID', 3, now(), now(), '$ownerID')";
        return self::insTable($sql);
    }

    public function updateInput($id, $name, $ownerID) {
        $sql = "UPDATE input SET name='$name', date_modified= now(), last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
     // ------- Project Pipelines  ------
    public function insertProjectPipeline($name, $project_id, $pipeline_id, $summary, $output_dir, $profile, $interdel, $cmd, $exec_each, $exec_all, $exec_all_settings, $exec_each_settings, $docker_check, $docker_img, $singu_check, $singu_img, $exec_next_settings, $docker_opt, $singu_opt, $amazon_cre_id, $publish_dir, $publish_dir_check, $ownerID) {
        $sql = "INSERT INTO project_pipeline(name, project_id, pipeline_id, summary, output_dir, profile, interdel, cmd, exec_each, exec_all, exec_all_settings, exec_each_settings, docker_check, docker_img, singu_check, singu_img, exec_next_settings, docker_opt, singu_opt, amazon_cre_id, publish_dir, publish_dir_check, owner_id, date_created, date_modified, last_modified_user, perms) 
                VALUES ('$name', '$project_id', '$pipeline_id', '$summary', '$output_dir', '$profile', '$interdel', '$cmd', '$exec_each', '$exec_all', '$exec_all_settings', '$exec_each_settings', '$docker_check', '$docker_img', '$singu_check', '$singu_img', '$exec_next_settings', '$docker_opt', '$singu_opt', '$amazon_cre_id', '$publish_dir','$publish_dir_check','$ownerID', now(), now(), '$ownerID', 3)";
        return self::insTable($sql);
    }
    public function updateProjectPipeline($id, $name, $summary, $output_dir, $perms, $profile, $interdel, $cmd, $group_id, $exec_each, $exec_all, $exec_all_settings, $exec_each_settings, $docker_check, $docker_img, $singu_check, $singu_img, $exec_next_settings, $docker_opt, $singu_opt, $amazon_cre_id, $publish_dir, $publish_dir_check, $ownerID) {
        $sql = "UPDATE project_pipeline SET name='$name', summary='$summary', output_dir='$output_dir', perms='$perms', profile='$profile', interdel='$interdel', cmd='$cmd', group_id='$group_id', exec_each='$exec_each', exec_all='$exec_all', exec_all_settings='$exec_all_settings', exec_each_settings='$exec_each_settings', docker_check='$docker_check', docker_img='$docker_img', singu_check='$singu_check', singu_img='$singu_img', exec_next_settings='$exec_next_settings', docker_opt='$docker_opt', singu_opt='$singu_opt', amazon_cre_id='$amazon_cre_id', publish_dir='$publish_dir', publish_dir_check='$publish_dir_check', date_modified= now(), last_modified_user ='$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function getProjectPipelines($id,$project_id,$ownerID) {
		if ($id != ""){
			$where = " where pp.id = '$id' AND (pp.owner_id = '$ownerID' OR pp.perms = 63 OR (ug.u_id ='$ownerID' and pp.perms = 15))";
            $sql = "SELECT DISTINCT pp.id, pp.name as pp_name, pip.id as pip_id, pip.rev_id, pip.name, u.username, pp.summary, pp.project_id, pp.pipeline_id, pp.date_created, pp.date_modified, pp.owner_id, p.name as project_name, pp.output_dir, pp.profile, pp.interdel, pp.group_id, pp.exec_each, pp.exec_all, pp.exec_all_settings, pp.exec_each_settings, pp.perms, pp.docker_check, pp.docker_img, pp.singu_check, pp.singu_img, pp.exec_next_settings, pp.cmd, pp.singu_opt, pp.docker_opt, pp.amazon_cre_id, pp.publish_dir, pp.publish_dir_check, IF(pp.owner_id='$ownerID',1,0) as own
                    FROM project_pipeline pp 
                    INNER JOIN users u ON pp.owner_id = u.id 
                    INNER JOIN project p ON pp.project_id = p.id
                    INNER JOIN biocorepipe_save pip ON pip.id = pp.pipeline_id
                    LEFT JOIN user_group ug ON pp.group_id=ug.g_id
                    $where";    
		} else {
            $where = " where pp.project_id = '$project_id' AND (pp.owner_id = '$ownerID' OR pp.perms = 63 OR (ug.u_id ='$ownerID' and pp.perms = 15))" ; 
            $sql = "SELECT DISTINCT pp.id, pp.name as pp_name, pip.id as pip_id, pip.rev_id, pip.name, u.username, pp.summary, pp.date_modified, IF(pp.owner_id='$ownerID',1,0) as own 
                    FROM project_pipeline pp 
                    INNER JOIN biocorepipe_save pip ON pip.id = pp.pipeline_id
                    INNER JOIN users u ON pp.owner_id = u.id 
                    LEFT JOIN user_group ug ON pp.group_id=ug.g_id
                    $where";    
        }
		return self::queryTable($sql);
    }
    public function getExistProjectPipelines($pipeline_id,$ownerID) {
			$where = " where pp.pipeline_id = '$pipeline_id' AND (pp.owner_id = '$ownerID' OR pp.perms = 63 OR (ug.u_id ='$ownerID' and pp.perms = 15))";
            $sql = "SELECT DISTINCT pp.id, pp.name as pp_name, u.username, pp.date_modified, p.name as project_name
                    FROM project_pipeline pp 
                    INNER JOIN users u ON pp.owner_id = u.id 
                    INNER JOIN project p ON pp.project_id = p.id
                    LEFT JOIN user_group ug ON pp.group_id=ug.g_id
                    $where";    
		return self::queryTable($sql);
    }
    
     // ------- Project Pipeline Inputs  ------
    public function insertProPipeInput($project_pipeline_id, $input_id, $project_id, $pipeline_id, $g_num, $given_name, $qualifier, $ownerID) {
        $sql = "INSERT INTO project_pipeline_input(project_pipeline_id, input_id, project_id, pipeline_id, g_num, given_name, qualifier, owner_id, perms, date_created, date_modified, last_modified_user) VALUES ('$project_pipeline_id', '$input_id', '$project_id', '$pipeline_id', '$g_num', '$given_name', '$qualifier', '$ownerID', 3, now(), now(), '$ownerID')";
        return self::insTable($sql);
    }

    public function updateProPipeInput($id, $project_pipeline_id, $input_id, $project_id, $pipeline_id, $gNum, $given_name, $qualifier, $ownerID) {
        $sql = "UPDATE project_pipeline_input SET project_pipeline_id='$project_pipeline_id', input_id='$input_id', project_id='$project_id', pipeline_id='$pipeline_id', g_num='$g_num', given_name='$given_name', qualifier='$qualifier', last_modified_user ='$ownerID'  WHERE id = $id";
        return self::runSQL($sql);
    } 
    public function duplicateProjectPipelineInput($new_id,$old_id,$ownerID) {
        $sql = "INSERT INTO project_pipeline_input(input_id, project_id, pipeline_id, g_num, given_name, qualifier, project_pipeline_id, owner_id, perms, date_created, date_modified, last_modified_user) 
                SELECT input_id, project_id, pipeline_id, g_num, given_name, qualifier, '$new_id', '$ownerID', '3', now(), now(),'$ownerID'
                FROM project_pipeline_input
                WHERE project_pipeline_id='$old_id'";
        return self::insTable($sql);
    }
    
    public function duplicateProcess($new_process_gid, $new_name, $old_id, $ownerID) {
        $sql = "INSERT INTO process(process_group_id, name, summary, script, script_header, script_mode, script_mode_header, owner_id, perms, date_created, date_modified, last_modified_user, rev_id, process_gid) 
                SELECT process_group_id, '$new_name', summary, script, script_header, script_mode, script_mode_header, '$ownerID', '3', now(), now(),'$ownerID', '0', '$new_process_gid'
                FROM process
                WHERE id='$old_id'";
        return self::insTable($sql);
    }
    public function createProcessRev($new_process_gid, $rev_comment, $rev_id, $old_id, $ownerID) {
        $sql = "INSERT INTO process(process_group_id, name, summary, script, script_header, script_mode, script_mode_header, owner_id, perms, date_created, date_modified, last_modified_user, rev_id, process_gid, rev_comment) 
                SELECT process_group_id, name, summary, script, script_header, script_mode, script_mode_header, '$ownerID', '3', now(), now(),'$ownerID', '$rev_id', '$new_process_gid', '$rev_comment'
                FROM process
                WHERE id='$old_id'";
        return self::insTable($sql);
    }
    public function duplicateProcessParameter($new_pro_id, $old_id, $ownerID){
        $sql = "INSERT INTO process_parameter(process_id, parameter_id, type, sname, operator, closure, reg_ex, owner_id, perms, date_created, date_modified, last_modified_user) 
                SELECT '$new_pro_id', parameter_id, type, sname, operator, closure, reg_ex, '$ownerID', '3', now(), now(),'$ownerID'
                FROM process_parameter
                WHERE process_id='$old_id'";
        return self::insTable($sql);
    }
    
    
    public function getProjectPipelineInputs($g_num, $project_pipeline_id,$ownerID) {
        $where = " where ppi.project_pipeline_id = '$project_pipeline_id' AND (ppi.owner_id = '$ownerID' OR ppi.perms = 63 OR (ug.u_id ='$ownerID' and ppi.perms = 15))" ; 
        if (isset($g_num)){
			 $where = " where ppi.g_num= '$g_num' AND ppi.project_pipeline_id = '$project_pipeline_id' AND (ppi.owner_id = '$ownerID' OR ppi.perms = 63 OR (ug.u_id ='$ownerID' and ppi.perms = 15))" ; 
		}
		$sql = "SELECT DISTINCT ppi.id, i.id as input_id, i.name, ppi.given_name, ppi.g_num
                FROM project_pipeline_input ppi
                INNER JOIN input i ON i.id = ppi.input_id
                LEFT JOIN user_group ug ON ppi.group_id=ug.g_id
                $where";
		return self::queryTable($sql);
    }

    
    public function getProjectPipelineInputsById($id,$ownerID) {
        $where = " where ppi.id= '$id' AND (ppi.owner_id = '$ownerID' OR ppi.perms = 63)" ; 
		$sql = "SELECT ppi.id, i.id as input_id, i.name
                FROM project_pipeline_input ppi
                INNER JOIN input i ON i.id = ppi.input_id
                $where";
		return self::queryTable($sql);
    }

    public function insertProcessParameter($sname, $process_id, $parameter_id, $type, $closure, $operator, $reg_ex, $perms, $group_id, $ownerID) {
        $sql = "INSERT INTO process_parameter(sname, process_id, parameter_id, type, closure, operator, reg_ex, owner_id, date_created, date_modified, last_modified_user, perms, group_id) 
                VALUES ('$sname', '$process_id', '$parameter_id', '$type', '$closure', '$operator', '$reg_ex', '$ownerID', now(), now(), '$ownerID', '$perms', '$group_id')";
        return self::insTable($sql);
    }
    
    public function updateProcessParameter($id, $sname, $process_id, $parameter_id, $type, $closure, $operator, $reg_ex, $perms, $group_id, $ownerID) {
        $sql = "UPDATE process_parameter SET sname='$sname', process_id='$process_id', parameter_id='$parameter_id', type='$type', closure='$closure', operator='$operator', reg_ex='$reg_ex', last_modified_user ='$ownerID', perms='$perms', group_id='$group_id'  WHERE id = '$id'";
        return self::runSQL($sql);
    }

    public function removeProcessParameter($id) {
        $sql = "DELETE FROM process_parameter WHERE id = '$id'";
        return self::runSQL($sql);
    }

    public function removeProcessParameterByParameterID($parameter_id) {
        $sql = "DELETE FROM process_parameter WHERE parameter_id = '$parameter_id'";
        return self::runSQL($sql);
    }

    public function removeProcessParameterByProcessGroupID($process_group_id) {
        $sql = "DELETE process_parameter
                FROM process_parameter 
                JOIN process ON process.id = process_parameter.process_id 
                WHERE process.process_group_id = '$process_group_id'";        
        return self::runSQL($sql);
    }
    public function removeProcessParameterByProcessID($process_id) {
        $sql = "DELETE FROM process_parameter WHERE process_id = '$process_id'";
        return self::runSQL($sql);
    }
    //------- feedback ------
        public function savefeedback($email,$message,$url) {
        $sql = "INSERT INTO feedback(email, message, url, date_created) VALUES 
			('$email', '$message','$url', now())";
        return self::insTable($sql);
        }
// --------- New Pipeline -----------
public function getPublicPipelines() {
        $sql= "SELECT pip.id, pip.name, pip.summary, pip.pin, pip.pin_order
               FROM biocorepipe_save pip
               INNER JOIN (
                SELECT pipeline_gid, MAX(rev_id) rev_id
                FROM biocorepipe_save 
                WHERE pin = 'true' AND perms = 63
                GROUP BY pipeline_gid
                ) b ON pip.rev_id = b.rev_id AND pip.pipeline_gid=b.pipeline_gid ";
     return self::queryTable($sql);
   }
	public function getProcessData($ownerID) {
        if ($ownerID == ""){
            $ownerID ="''";
        } else {
            $userRole = json_decode($this->getUserRole($ownerID))[0]->{'role'};
            if ($userRole == "admin"){
                $sql = "SELECT DISTINCT p.id, p.process_group_id, p.name, p.summary, p.script, p.script_header, p.script_mode, p.script_mode_header, p.rev_id, p.perms, p.group_id, p.publish, IF(p.owner_id='$ownerID',1,0) as own FROM process p ";
                return self::queryTable($sql);
            }
		}
		$sql = "SELECT DISTINCT p.id, p.process_group_id, p.name, p.summary, p.script, p.script_header, p.script_mode, p.script_mode_header, p.rev_id, p.perms, p.group_id, p.publish, IF(p.owner_id='$ownerID',1,0) as own  
        FROM process p
        LEFT JOIN user_group ug ON p.group_id=ug.g_id
        WHERE p.owner_id = '$ownerID' OR p.perms = 63 OR (ug.u_id ='$ownerID' and p.perms = 15)";
		return self::queryTable($sql);
	}
    public function getProcessDataById($id, $ownerID) {
        if ($ownerID == ""){
            $ownerID ="''";
        }else {
            $userRole = json_decode($this->getUserRole($ownerID))[0]->{'role'};
            if ($userRole == "admin"){
                $sql = "SELECT DISTINCT p.id, p.process_group_id, p.name, p.summary, p.script, p.script_header, p.script_mode, p.script_mode_header, p.rev_id, p.perms, p.group_id, p.publish, IF(p.owner_id='$ownerID',1,0) as own FROM process p where p.id = '$id'";
                return self::queryTable($sql);
            }
		}
		$sql = "SELECT DISTINCT p.id, p.process_group_id, p.name, p.summary, p.script, p.script_header, p.script_mode, p.script_mode_header, p.rev_id, p.perms, p.group_id, p.publish, IF(p.owner_id='$ownerID',1,0) as own  
        FROM process p
        LEFT JOIN user_group ug ON p.group_id=ug.g_id
        where p.id = '$id' AND (p.owner_id = '$ownerID' OR p.perms = 63 OR (ug.u_id ='$ownerID' and p.perms = 15))";
		return self::queryTable($sql);
	}
    public function getProcessRevision($process_gid,$ownerID) {
        if ($ownerID != ""){
         $userRole = json_decode($this->getUserRole($ownerID))[0]->{'role'};
            if ($userRole == "admin"){
                $sql = "SELECT DISTINCT p.id, p.rev_id, p.rev_comment, p.last_modified_user, p.date_created, p.date_modified, IF(p.owner_id='$ownerID',1,0) as own  
                FROM process p
                WHERE p.process_gid = '$process_gid'";
                return self::queryTable($sql);
            }
        }
		$sql = "SELECT DISTINCT p.id, p.rev_id, p.rev_comment, p.last_modified_user, p.date_created, p.date_modified, IF(p.owner_id='$ownerID',1,0) as own  
        FROM process p
        LEFT JOIN user_group ug ON p.group_id=ug.g_id
        WHERE p.process_gid = '$process_gid' AND (p.owner_id = '$ownerID' OR p.perms = 63 OR (ug.u_id ='$ownerID' and p.perms = 15))";
		return self::queryTable($sql);
	}
    public function getPipelineRevision($pipeline_gid,$ownerID) {
        if ($ownerID != ""){
                $userRole = json_decode($this->getUserRole($ownerID))[0]->{'role'};
                if ($userRole == "admin"){
                    $sql = "SELECT DISTINCT pip.id, pip.rev_id, pip.rev_comment, pip.last_modified_user, pip.date_created, pip.date_modified, IF(pip.owner_id='$ownerID',1,0) as own FROM biocorepipe_save pip WHERE pip.pipeline_gid = '$pipeline_gid'";
                    return self::queryTable($sql);
                }
            }
		$sql = "SELECT DISTINCT pip.id, pip.rev_id, pip.rev_comment, pip.last_modified_user, pip.date_created, pip.date_modified, IF(pip.owner_id='$ownerID',1,0) as own
        FROM biocorepipe_save pip
        LEFT JOIN user_group ug ON pip.group_id=ug.g_id
        WHERE pip.pipeline_gid = '$pipeline_gid' AND (pip.owner_id = '$ownerID' OR pip.perms = 63 OR (ug.u_id ='$ownerID' and pip.perms = 15))";
		return self::queryTable($sql);
	}
    
    public function getProcessGID($id) {
		$sql = "SELECT  process_gid FROM process WHERE id = '$id'";
		return self::queryTable($sql);
	}
    public function getPipelineGID($id) {
		$sql = "SELECT pipeline_gid FROM biocorepipe_save WHERE id = '$id'";
		return self::queryTable($sql);
	}
	public function getInputsPP($id) {
		$sql = "SELECT parameter_id, sname, id, operator, closure, reg_ex FROM process_parameter where process_id = '$id' and type = 'input'";
		return self::queryTable($sql);
	}
	public function checkPipeline($process_id, $ownerID) {
		$sql = "SELECT id, name FROM biocorepipe_save WHERE (owner_id = '$ownerID') AND nodes LIKE '%\"$process_id\",\"%'";
		return self::queryTable($sql);
	}
    public function checkPipelinePublic($process_id, $ownerID) {
		$sql = "SELECT id, name FROM biocorepipe_save WHERE (owner_id != '$ownerID') AND nodes LIKE '%\"$process_id\",\"%'";
		return self::queryTable($sql);
	}
    public function checkPipelinePerm($process_id, $ownerID) {
		$sql = "SELECT id, name FROM biocorepipe_save WHERE perms>3 AND nodes LIKE '%\"$process_id\",\"%'";
		return self::queryTable($sql);
	}
    public function checkProjectPipePerm($pipeline_id, $ownerID) {
		$sql = "SELECT id, name FROM project_pipeline WHERE perms>3 AND pipeline_id='$pipeline_id'";
		return self::queryTable($sql);
	}
    public function checkParameter($parameter_id, $ownerID) {
		$sql = "SELECT DISTINCT pp.id, p.name 
        FROM process_parameter pp
        INNER JOIN process p ON pp.process_id = p.id
        WHERE (pp.owner_id = '$ownerID') AND pp.parameter_id = '$parameter_id'";
		return self::queryTable($sql);
	}
    public function checkMenuGr($id, $ownerID) {
		$sql = "SELECT DISTINCT pg.id, p.name 
        FROM process p
        INNER JOIN process_group pg ON p.process_group_id = pg.id
        WHERE (pg.owner_id = '$ownerID') AND pg.id = '$id'";
		return self::queryTable($sql);
	}
    public function checkProject($pipeline_id, $ownerID) {
		$sql = "SELECT DISTINCT pp.id, p.name 
        FROM project_pipeline pp
        INNER JOIN project p ON pp.project_id = p.id
        WHERE (pp.owner_id = '$ownerID') AND pp.pipeline_id = '$pipeline_id'";
		return self::queryTable($sql);
	}
    public function checkProjectPublic($pipeline_id, $ownerID) {
		$sql = "SELECT DISTINCT pp.id, p.name 
        FROM project_pipeline pp
        INNER JOIN project p ON pp.project_id = p.id
        WHERE (pp.owner_id != '$ownerID') AND pp.pipeline_id = '$pipeline_id'";
		return self::queryTable($sql);
	}
    public function getMaxProcess_gid() {
		$sql = "SELECT MAX(process_gid) process_gid FROM process";
		return self::queryTable($sql);
	}
    public function getMaxPipeline_gid() {
		$sql = "SELECT MAX(pipeline_gid) pipeline_gid FROM biocorepipe_save";
		return self::queryTable($sql);
	}
    public function getProcess_gid($process_id) {
		$sql = "SELECT process_gid FROM process WHERE id = '$process_id'";
		return self::queryTable($sql);
	}
    public function getPipeline_gid($pipeline_id) {
		$sql = "SELECT pipeline_gid FROM biocorepipe_save WHERE id = '$pipeline_id'";
		return self::queryTable($sql);
	}
    public function getMaxRev_id($process_gid) {
		$sql = "SELECT MAX(rev_id) rev_id FROM process WHERE process_gid = '$process_gid'";
		return self::queryTable($sql);
	}
    public function getMaxPipRev_id($pipeline_gid) {
		$sql = "SELECT MAX(rev_id) rev_id FROM biocorepipe_save WHERE pipeline_gid = '$pipeline_gid'";
		return self::queryTable($sql);
	}
	public function getOutputsPP($id) {
		$sql = "SELECT parameter_id, sname, id, operator, closure, reg_ex FROM process_parameter where process_id = '$id' and type = 'output'";
		return self::queryTable($sql);
	}
	//update if user owns the project
    public function updateProjectGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE project p
        INNER JOIN project_pipeline pp ON p.id=pp.project_id
        SET p.group_id='$group_id', p.perms='$perms', p.date_modified=now(), p.last_modified_user ='$ownerID'  WHERE pp.id = '$id' AND p.perms<'$perms'";
        return self::runSQL($sql);
    }
    public function updateProjectInputGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE project_input pi
        INNER JOIN project_pipeline_input ppi ON pi.input_id=ppi.input_id
        SET pi.group_id='$group_id', pi.perms='$perms', pi.date_modified=now(), pi.last_modified_user ='$ownerID'  WHERE ppi.project_pipeline_id = '$id' and pi.perms<'$perms'";
        return self::runSQL($sql);
    }
    public function updateProjectPipelineInputGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE project_pipeline_input SET group_id='$group_id', perms='$perms', date_modified=now(), last_modified_user ='$ownerID'  WHERE project_pipeline_id = '$id' AND perms<'$perms'";
        return self::runSQL($sql);
    }
    public function updateInputGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE input i
        INNER JOIN project_pipeline_input ppi ON ppi.input_id=i.id
        SET i.group_id='$group_id', i.perms='$perms', i.date_modified=now(), i.last_modified_user ='$ownerID'  WHERE ppi.project_pipeline_id = '$id' and  i.perms<'$perms'";
        return self::runSQL($sql);
    }
    public function updatePipelineGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE biocorepipe_save pi
        INNER JOIN project_pipeline_input ppi ON pi.id=ppi.pipeline_id
        SET pi.group_id='$group_id', pi.perms='$perms', pi.date_modified=now(), pi.last_modified_user ='$ownerID'  WHERE ppi.project_pipeline_id = '$id' AND pi.perms<'$perms'";
        return self::runSQL($sql);
    }
     public function updatePipelineProcessGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "SELECT pip.nodes
        FROM biocorepipe_save pip
        INNER JOIN project_pipeline_input pi ON pip.id=pi.pipeline_id
        WHERE pi.project_pipeline_id = '$id' and pi.owner_id='$ownerID'";
        $nodesArr = json_decode(self::queryTable($sql));
        $nodes = json_decode($nodesArr[0]->{"nodes"});
        foreach ($nodes as $item):
            if ($item[2] !== "inPro" && $item[2] !== "outPro"){
                $proId = $item[2];
                $this->updateParameterGroupPerm($proId, $group_id, $perms, $ownerID);
                $this->updateProcessGroupPerm($proId, $group_id, $perms, $ownerID);
                $this->updateProcessParameterGroupPerm($proId, $group_id, $perms, $ownerID);
                $this->updateProcessGroupGroupPerm($proId, $group_id, $perms, $ownerID);
            }
        endforeach;
     }
    
    //update if user owns the process
    public function updateProcessGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE process SET group_id='$group_id', perms='$perms', date_modified=now(), last_modified_user ='$ownerID'  WHERE id = '$id' and  perms<'$perms'";
        return self::runSQL($sql);
    }
    public function updateProcessParameterGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE process_parameter SET group_id='$group_id', perms='$perms', date_modified=now(), last_modified_user ='$ownerID'  WHERE process_id = '$id' AND perms<'$perms'";
        return self::runSQL($sql);
    }
    public function updateParameterGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE parameter p 
                INNER JOIN process_parameter pp ON p.id=pp.parameter_id
                SET p.group_id='$group_id', p.perms='$perms', p.date_modified=now(), p.last_modified_user ='$ownerID'  WHERE pp.process_id = '$id' and  p.perms<'$perms'";
        return self::runSQL($sql);
    }
    public function updateParameterGroupPermById($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE parameter  
                SET group_id='$group_id', perms='$perms', date_modified=now(), last_modified_user ='$ownerID'  WHERE id = '$id' and perms<'$perms'";
        return self::runSQL($sql);
    }
     public function updateProcessGroupGroupPerm($id, $group_id, $perms, $ownerID) {
        $sql = "UPDATE process_group pg 
                INNER JOIN process p ON pg.id=p.process_group_id
                SET pg.group_id='$group_id', pg.perms='$perms', pg.date_modified=now(), pg.last_modified_user ='$ownerID'  WHERE p.id = '$id' AND pg.perms<'$perms'";
        return self::runSQL($sql);
    }
	public function saveAllPipeline($dat,$ownerID) {
		$obj = json_decode($dat);
		$name =  $obj[0]->{"name"};
        $id = $obj[1]->{"id"};
		$nodes = json_encode($obj[2]->{"nodes"}); 
		$mainG = "{\'mainG\':".json_encode($obj[3]->{"mainG"})."}";
		$edges = "{\'edges\':".json_encode($obj[4]->{"edges"})."}";
        $summary = $obj[5]->{"summary"};
        $group_id = $obj[6]->{"group_id"};
        $perms = $obj[7]->{"perms"};
        $pin = $obj[8]->{"pin"};
        $pin_order = $obj[9]->{"pin_order"};
        $publish = $obj[10]->{"publish"};
        $pipeline_gid = $obj[11]->{"pipeline_gid"};
        $rev_comment = $obj[12]->{"rev_comment"};
        $rev_id = $obj[13]->{"rev_id"};
        settype($rev_id, "integer");
        settype($pipeline_gid, "integer");
        settype($group_id, "integer");
        settype($pin_order, "integer");
        $nodesRaw = $obj[2]->{"nodes"};
        if (!empty($nodesRaw)){
            foreach ($nodesRaw as $item):
                if ($item[2] !== "inPro" && $item[2] !== "outPro" ){
                    $proId = $item[2];
                    $this->updateParameterGroupPerm($proId, $group_id, $perms, $ownerID);
                    $this->updateProcessGroupPerm($proId, $group_id, $perms, $ownerID);
                    $this->updateProcessParameterGroupPerm($proId, $group_id, $perms, $ownerID);
                    $this->updateProcessGroupGroupPerm($proId, $group_id, $perms, $ownerID);
                }
            endforeach;
        }
	    if ($id > 0){
            $sql = "UPDATE biocorepipe_save set name = '$name', edges = '$edges', summary = '$summary', mainG = '$mainG', nodes ='$nodes', date_modified = now(), group_id = '$group_id', perms = '$perms', pin = '$pin', publish = '$publish', pin_order = '$pin_order', last_modified_user = '$ownerID' where id = '$id'";
		}else{
            $sql = "INSERT INTO biocorepipe_save(owner_id, summary, edges, mainG, nodes, name, pipeline_gid, rev_comment, rev_id, date_created, date_modified, last_modified_user, group_id, perms, pin, pin_order, publish) VALUES ('$ownerID', '$summary', '$edges', '$mainG', '$nodes', '$name', '$pipeline_gid', '$rev_comment', '$rev_id', now(), now(), '$ownerID', '$group_id', '$perms', '$pin', '$pin_order', $publish )";
		}
  		return self::insTable($sql);
	}
	public function getSavedPipelines($ownerID) {
        if ($ownerID == ""){
            $ownerID ="''";
        } else {
            $userRole = json_decode($this->getUserRole($ownerID))[0]->{'role'};
            if ($userRole == "admin"){
                $sql = "select DISTINCT pip.id, pip.rev_id, pip.name, pip.summary, pip.date_modified, u.username
                FROM biocorepipe_save pip
                INNER JOIN users u ON pip.owner_id = u.id";
                return self::queryTable($sql);
            }
        }
        $where = " where pip.owner_id = '$ownerID' OR pip.perms = 63 OR (ug.u_id ='$ownerID' and pip.perms = 15)";
		$sql = "select DISTINCT pip.id, pip.rev_id, pip.name, pip.summary, pip.date_modified, u.username
        FROM biocorepipe_save pip
        INNER JOIN users u ON pip.owner_id = u.id
        LEFT JOIN user_group ug ON pip.group_id=ug.g_id
        $where";
		return self::queryTable($sql);
	}
	public function loadPipeline($id,$ownerID) {
            if ($ownerID != ""){
                $userRole = json_decode($this->getUserRole($ownerID))[0]->{'role'};
                if ($userRole == "admin"){
                    $sql = "select pip.*, u.username, IF(pip.owner_id='$ownerID',1,0) as own
                    FROM biocorepipe_save pip 
                    INNER JOIN users u ON pip.owner_id = u.id
                    where pip.id = '$id'";
                    return self::queryTable($sql);
                }
            }
		$sql = "select pip.*, u.username, IF(pip.owner_id='$ownerID',1,0) as own
                FROM biocorepipe_save pip 
                INNER JOIN users u ON pip.owner_id = u.id
                LEFT JOIN user_group ug ON pip.group_id=ug.g_id
                where pip.id = '$id' AND (pip.owner_id = '$ownerID' OR pip.perms = 63 OR (ug.u_id ='$ownerID' and pip.perms = 15))";
	   return self::queryTable($sql);
	}
    public function removePipelineById($id) {
		$sql = "DELETE FROM biocorepipe_save WHERE id = '$id'";
	   return self::runSQL($sql);
	}
    public function updatePipelineName($id, $name) {
        $sql = "UPDATE biocorepipe_save SET name='$name'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function savePipelineDetails($id, $summary,$group_id, $perms, $pin, $pin_order, $publish, $ownerID) {
        $sql = "UPDATE biocorepipe_save SET summary='$summary', group_id='$group_id', publish='$publish', perms='$perms', pin='$pin', pin_order='$pin_order', last_modified_user = '$ownerID'  WHERE id = '$id'";
        return self::runSQL($sql);
    }
    public function insertPipelineName($name,$ownerID) {
        $sql = "INSERT INTO biocorepipe_save(owner_id, name, rev_id, date_created, date_modified, last_modified_user) VALUES 
			('$ownerID','$name', '0', now(), now(), '$ownerID')";
        return self::insTable($sql);
    }
}