CREATE TABLE public.user_mst ( 
    user_id  	numeric(7,0) NOT NULL,
    user_name	varchar(25) NOT NULL,
    is_active	char(1) NOT NULL DEFAULT 'Y'::bpchar,
    user_desc	varchar(500) NOT NULL,
    password 	varchar(500) NOT NULL,
    role     	varchar(100) NOT NULL 
    )
;