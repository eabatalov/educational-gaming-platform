CREATE TABLE public.user_mst ( 
    user_id  	numeric(7,0) NOT NULL,
    user_name	varchar(25) NOT NULL,
    is_active	char(1) NOT NULL DEFAULT 'Y'::bpchar,
    user_desc	varchar(500) NOT NULL,
    password 	varchar(500) NOT NULL,
    role     	varchar(100) NOT NULL 
    )
;
ALTER TABLE "public"."user_mst"
	ADD COLUMN "id" serial NOT NULL

CREATE TABLE "public"."friends" ( 
	"requestor"	numeric(7,0) NULL,
	"acceptor" 	numeric(7,0) NULL 
	)
ALTER TABLE "public"."friends"
	ADD CONSTRAINT "fdk_user_mst"
	FOREIGN KEY("requestor")
	REFERENCES "public"."user_mst"("user_id")

ALTER TABLE "public"."friends"
	ADD CONSTRAINT "fk_user_mst"
	FOREIGN KEY("requestor")
	REFERENCES "public"."user_mst"("user_id")
ALTER TABLE "public"."friends"
	ADD CONSTRAINT "fk_user_mst"

ALTER TABLE "public"."friends"
	DROP CONSTRAINT "fdk_user_mst" CASCADE 
	
ALTER TABLE "public"."friends"
	ADD CONSTRAINT "fk_user_mst_req"
	FOREIGN KEY("requestor")
	REFERENCES "public"."user_mst"("user_id")	
	
