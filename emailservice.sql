Create Database email_service;
use email_service;


create table merchants(

 merchant_name varchar(30),
 merchant_id int auto_increment,
 merchant_email varchar(30),
 merchant_password varchar(20),
 token varchar(50),
 merchant_status int ,
 image blob,
 create_at datetime default NULL,
 current_at datetime default NULL,
 primary key(merchant_id)
 );



create table cards(

 card_id int auto_increment,
 card_no int(11),
 merchant_id int,
 cvc int(4),
 credit float(7,2),
 valid_from date,
 valid_through date,
 primary key (card_id),
 foreign key(merchant_id) references merchants (merchant_id)
 );
 


create table responses(

response_id int auto_increment,
response_status varchar(10), 
response_description varchar(255),
response_error varchar(20),
primary key (response_id)

);


create table admin(

admin_id int auto_increment,
admin_name varchar(15),
email varchar(15),
primary key (admin_id)
);


create table secondaryusers(

user_id int auto_increment,
user_name varchar(20),
user_email varchar(30),
token varchar(50),
email_permission boolean,
list_view_permission boolean,
merchant_id int,
payment_permission boolean,
primary key (user_id),
foreign key(merchant_id) references merchants(merchant_id)
);


create table requests (
request_id int auto_increment ,
response_id int unique,
merchant_id int,
email_subject varchar(50),
email_from varchar(20),
send_to varchar(50),
cc varchar(50),
bcc varchar(50),
email_body varchar(500),
primary key (request_id),
foreign key(merchant_id) references merchants(merchant_id),
foreign key(response_id) references responses(response_id)
);








 
 
 
 