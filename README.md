# Overview

This is a sample AWS Elastic Beanstalk application from Intacct Advantage 2016.  This a sample app not considered production ready.

Recommended reading
- [EB worker environments](https://docs.aws.amazon.com/elasticbeanstalk/latest/dg/using-features-managing-env-tiers.html)
- [S3 event notifications](http://docs.aws.amazon.com/AmazonS3/latest/dev/NotificationHowTo.html)

## Sample Workflow

- Intacct puts DDS csv files in an S3 bucket
- S3 bucket event puts a message in SQS queue
- Elastic Beanstalk worker environment daemon processes message
- Worker gets CSV from S3, processes the columns and rows, and then loads the data to an RDS instance

# EB Configuration

## Container Options

- Document root: `/wwwroot`
- Max execution time: `Depends`

## Environment Properties

If using the [built-in data tier RDS](built-in data tier RDS) with EB, these are auto set for you.
- `RDS_HOSTNAME`
- `RDS_PORT`
- `RDS_DB_NAME`
- `RDS_USERNAME`
- `RDS_PASSWORD`

## Worker Configuration
- HTTP path: `/ddsloader.php`
- MIME type: `application/json`
- HTTP connections: `Depends`

# DML Mapping

The file `dds_dml_mapping.php` contains a simple PHP array used for the DML mapping.  The `CUSTOMER` DML was designed for use with a PostgreSQL in RDS.