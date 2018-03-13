use biocorepipe;
ALTER TABLE biocorepipe_save MODIFY rev_id int(11);
ALTER TABLE biocorepipe_save MODIFY rev_comment varchar(20);
ALTER TABLE biocorepipe_save MODIFY pipeline_gid int(11);
ALTER TABLE biocorepipe_save MODIFY publish int(2);

ALTER TABLE biocorepipe_save MODIFY edges text;
ALTER TABLE biocorepipe_save MODIFY mainG text;
ALTER TABLE biocorepipe_save MODIFY nodes text;
ALTER TABLE feedback MODIFY `email` varchar(256);
ALTER TABLE feedback MODIFY `message` text;
ALTER TABLE feedback MODIFY `url` varchar(256);

ALTER TABLE process MODIFY `process_group_id` int(11);
ALTER TABLE process MODIFY `publish` int(2);
ALTER TABLE process MODIFY `rev_id` int(11);
ALTER TABLE process MODIFY `rev_comment` varchar(20);
ALTER TABLE process MODIFY `process_gid` int(11);

ALTER TABLE process_parameter MODIFY `operator` varchar(50);
ALTER TABLE process_parameter MODIFY `closure` varchar(256);

ALTER TABLE profile_amazon MODIFY `status` varchar(15);
ALTER TABLE profile_amazon MODIFY `pid` int(11);
ALTER TABLE profile_amazon MODIFY `ssh` varchar(256);
ALTER TABLE profile_amazon MODIFY `nodes` varchar(10);
ALTER TABLE profile_amazon MODIFY `autoscale_check` varchar(6);
ALTER TABLE profile_amazon MODIFY `autoscale_maxIns` varchar(10);
ALTER TABLE profile_amazon MODIFY `next_path` varchar(256);
ALTER TABLE profile_amazon MODIFY `amazon_cre_id` int(11);
ALTER TABLE profile_amazon MODIFY `ssh_id` int(11);
ALTER TABLE profile_amazon MODIFY `default_region` varchar(256);
ALTER TABLE profile_amazon MODIFY `access_key` varchar(256);
ALTER TABLE profile_amazon MODIFY `secret_key` varchar(256);
ALTER TABLE profile_amazon MODIFY `instance_type` varchar(256);
ALTER TABLE profile_amazon MODIFY `image_id` varchar(256);
ALTER TABLE profile_amazon MODIFY `shared_storage_mnt` varchar(256);
ALTER TABLE profile_amazon MODIFY `shared_storage_id` varchar(256);
ALTER TABLE profile_amazon MODIFY `subnet_id` varchar(256);
ALTER TABLE profile_amazon MODIFY `executor` varchar(25);
ALTER TABLE profile_amazon MODIFY `job_time` varchar(25);
ALTER TABLE profile_amazon MODIFY `job_queue` varchar(25);
ALTER TABLE profile_amazon MODIFY `job_cpu` varchar(25);
ALTER TABLE profile_amazon MODIFY `job_memory` varchar(25);
ALTER TABLE profile_amazon MODIFY `executor_job` varchar(25);
ALTER TABLE profile_amazon MODIFY `next_time` varchar(25);
ALTER TABLE profile_amazon MODIFY `next_queue` varchar(25);
ALTER TABLE profile_amazon MODIFY `next_cpu` varchar(25);
ALTER TABLE profile_amazon MODIFY `next_memory` varchar(25);
ALTER TABLE profile_amazon MODIFY `cmd` varchar(500);

ALTER TABLE profile_cluster MODIFY `username` varchar(256);
ALTER TABLE profile_cluster MODIFY `hostname` varchar(256);
ALTER TABLE profile_cluster MODIFY `ssh_id` int(11);
ALTER TABLE profile_cluster MODIFY `next_path` varchar(256);
ALTER TABLE profile_cluster MODIFY `executor` varchar(25);
ALTER TABLE profile_cluster MODIFY `job_time` varchar(25);
ALTER TABLE profile_cluster MODIFY `job_cpu` varchar(25);
ALTER TABLE profile_cluster MODIFY `job_memory` varchar(25);
ALTER TABLE profile_cluster MODIFY `executor_job` varchar(25);
ALTER TABLE profile_cluster MODIFY `next_time` varchar(25);
ALTER TABLE profile_cluster MODIFY `next_queue` varchar(25);
ALTER TABLE profile_cluster MODIFY `next_cpu` varchar(25);
ALTER TABLE profile_cluster MODIFY `next_memory` varchar(25);
ALTER TABLE profile_cluster MODIFY `cmd` varchar(500);

ALTER TABLE profile_local MODIFY `next_path` varchar(256);
ALTER TABLE profile_local MODIFY `executor` varchar(25);
ALTER TABLE profile_local MODIFY `job_time` varchar(25);
ALTER TABLE profile_local MODIFY `job_cpu` varchar(25);
ALTER TABLE profile_local MODIFY `job_memory` varchar(25);
ALTER TABLE profile_local MODIFY `executor_job` varchar(25);
ALTER TABLE profile_local MODIFY `next_time` varchar(25);
ALTER TABLE profile_local MODIFY `next_queue` varchar(25);
ALTER TABLE profile_local MODIFY `next_cpu` varchar(25);
ALTER TABLE profile_local MODIFY `next_memory` varchar(25);
ALTER TABLE profile_local MODIFY `cmd` varchar(500);


ALTER TABLE project_pipeline MODIFY `output_dir` varchar(256);
ALTER TABLE project_pipeline MODIFY `cmd` varchar(500);
ALTER TABLE project_pipeline MODIFY `profile` varchar(30);
ALTER TABLE project_pipeline MODIFY `interdel` varchar(6);
ALTER TABLE project_pipeline MODIFY `exec_next_settings` varchar(300);
ALTER TABLE project_pipeline MODIFY `exec_each` varchar(6);
ALTER TABLE project_pipeline MODIFY `exec_all` varchar(6);
ALTER TABLE project_pipeline MODIFY `exec_all_settings` text;
ALTER TABLE project_pipeline MODIFY `exec_each_settings` text;
ALTER TABLE project_pipeline MODIFY `singu_img` varchar(256);
ALTER TABLE project_pipeline MODIFY `singu_opt` varchar(500);
ALTER TABLE project_pipeline MODIFY `docker_img` varchar(256);
ALTER TABLE project_pipeline MODIFY `docker_opt` varchar(500);
ALTER TABLE project_pipeline MODIFY `singu_check` varchar(6);
ALTER TABLE project_pipeline MODIFY `docker_check` varchar(6);

ALTER TABLE project_pipeline_input MODIFY `g_num` int(11);
ALTER TABLE project_pipeline_input MODIFY `given_name` varchar(256);

ALTER TABLE run MODIFY `pid` int(11);
ALTER TABLE run MODIFY `run_status` varchar(11);

ALTER TABLE run_log MODIFY `run_status` varchar(20);
ALTER TABLE run_log MODIFY `duration` varchar(30);

ALTER TABLE ssh MODIFY `check_ourkey` varchar(6);
ALTER TABLE ssh MODIFY `check_userkey` varchar(6);


ALTER TABLE users MODIFY `institute` varchar(45);
ALTER TABLE users MODIFY `lab` varchar(45);
ALTER TABLE users MODIFY `google_image` varchar(255);




























