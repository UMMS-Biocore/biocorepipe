use biocorepipe;
ALTER TABLE process_parameter 
ADD reg_ex varchar(100) DEFAULT NULL AFTER closure;

ALTER TABLE process 
ADD script_mode varchar(20) DEFAULT NULL AFTER script,
ADD script_header text DEFAULT NULL AFTER script_mode,
ADD script_mode_header varchar(20) DEFAULT NULL AFTER script_header;

ALTER TABLE biocorepipe_save 
ADD script_mode varchar(20) DEFAULT NULL AFTER name,
ADD script_header text DEFAULT NULL AFTER name;
