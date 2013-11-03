#!/usr/bin/python3

import sys, time
from optparse import OptionParser
import postgresql.driver as pg_driver

files = ["create_db.sql", "fill_db.py"] #add files to run here
conn = None #DB connection to be used throught setupe process

def check_ver():
	if sys.version_info < (3, 3):
	    raise "To run setup use python 3.3 or higher"

def parse_opts():
	opt_parser = OptionParser()
	opt_parser.add_option("--host", dest="db_host", default="127.0.0.1",
		help="host name or ip of target postgres instance")
	opt_parser.add_option("-p", "--port", dest="db_port", default="5432",
		help="port of target postgres instance")
	opt_parser.add_option("--db", dest="db_name", default="postgres",
		help="target db on target postgres instance")
	opt_parser.add_option("-u", "--user", dest="db_user", default="postgres",
		help="password for target postgres instance")
	opt_parser.add_option("--pass", dest="db_pass", default="111",
		help="password for target postgres instance")

	(options, args) = opt_parser.parse_args()
	print("Running with options: ", options.__dict__)
	time.sleep(1)
	return options.__dict__

def setup_con(opts):
	print("CONNECTING TO DB")
	conn = pg_driver.connect(user = opts["db_user"],
			host = opts["db_host"], port = opts["db_port"],
			password = opts["db_pass"])
	print("CONNECTION: SUCCESS!")
	return conn

def run_sql(file_name):
	global conn
	sql_file = open(file_name, "r", encoding="utf-8-sig")
	sql_script = sql_file.read()
	sql_file.close()
	print("EXECUTING SQL FILE:", file_name)
	for sql_command in sql_script.split('--CMD'):
		print("EXECUTING SQL STATEMENT:", sql_command)
		conn.query(sql_command)
	print("FINISHED EXECUTION OF SQL FILE:", file_name)

def run_py(file_name):
	print("EXECUTING PYTHON FILE:", file_name)
	exec(compile(open(file_name).read(), file_name, 'exec'))
	print("FINISHED EXECUTION OF PYTHON FILE:", file_name)

def main():
	global conn
	global files
	check_ver()
	opts = parse_opts()
	conn = setup_con(opts)

	try:
		for file_name in files:
			if file_name.endswith(".sql"):
				run_sql(file_name)
			elif file_name.endswith(".py"):
				run_py(file_name)
			else:
				print("USUPPORTED FILE: ", file_name)
				exit(1)
	finally:
		conn.close()

#script start here
if __name__ == "__main__":
	main()
