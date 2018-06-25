# php-curl-s3-AWS4-HMAC-SHA256

Standalone library to demonstrate encoding a file to PUT to AWS's latest S3 endpoint which requires SHA256

This file can be run with `php put_s3_test.php` and will require the following environmental variables to be set:

```
S3_KEY
S3_SECRET
S3_REGION
S3_BUCKET
```

It will `PUT` the sample file, `index.html` to your bucket's root.  

## Amazon's guidance

From https://docs.aws.amazon.com/AmazonS3/latest/API/sigv4-auth-using-authorization-header.html

Example authorization headers:

```
Authorization: AWS4-HMAC-SHA256 
Credential=AKIAIOSFODNN7EXAMPLE/20130524/us-east-1/s3/aws4_request, 
SignedHeaders=host;range;x-amz-date,
Signature=fe5f80f77d5fa3beca038a248ff027d0445342fe2855ddc963176630326f1024
```

Complete S3 objectPut reference: https://docs.aws.amazon.com/AmazonS3/latest/API/RESTObjectPUT.html
