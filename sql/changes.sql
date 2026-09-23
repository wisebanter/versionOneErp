DROP TABLE IF EXISTS system_credentials;
CREATE TABLE system_credentials(
	_cid INTEGER PRIMARY KEY auto_increment,
	reff_number VARCHAR(10) UNIQUE NULL  DEFAULT NULL,
	descriptins VARCHAR(80) NULL  DEFAULT NULL,
	credentials TEXT NULL DEFAULT NULL
); 


-- 22a245db5ca963be.227bd30bf6f162bb12efead65a558fec5b77848a6395fda3a1fb5d22c7bf79f9
TRUNCATE system_credentials;
INSERT INTO system_credentials(reff_number, descriptins, credentials) VALUES
('REF001', 'contact information', REVERSE(TO_BASE64(CONCAT('{"',REVERSE(TO_BASE64("infomail")),'":"',REVERSE(TO_BASE64("info@emanswift.com")),'", "',REVERSE(TO_BASE64("helpline")),'":"',REVERSE(TO_BASE64("256701997708")),'", "',REVERSE(TO_BASE64("administrator")),'":"',REVERSE(TO_BASE64("256788589818")),'"}')))),
('REF002', 'Email configuration', REVERSE(TO_BASE64(CONCAT('{"',REVERSE(TO_BASE64("accessKey")),'":"',REVERSE(TO_BASE64("mailsender757@gmail.com")),'", "',REVERSE(TO_BASE64("secretkey")),'":"',REVERSE(TO_BASE64("gwjyaqzkzwqddyyt")),'", "',REVERSE(TO_BASE64("senderName")),'":"',REVERSE(TO_BASE64("administrator")),'", "',REVERSE(TO_BASE64("senderEmail")),'":"',REVERSE(TO_BASE64("admin@gmail.com")),'"}')))),
('REF003', 'SMS configuration', REVERSE(TO_BASE64(CONCAT('{"',REVERSE(TO_BASE64("accessKey")),'":"',REVERSE(TO_BASE64("pembefahad487@gmail.com")),'", "',REVERSE(TO_BASE64("secretkey")),'":"',REVERSE(TO_BASE64("Wisebanter@22")),'"}')))),
('REF004', 'LivePay configuration', REVERSE(TO_BASE64(CONCAT('{"',REVERSE(TO_BASE64("accessKey")),'":"',REVERSE(TO_BASE64("MPPubK-7882ef526d5417df042d80400defbb16-X")),'", "',REVERSE(TO_BASE64("secretkey")),'":"',REVERSE(TO_BASE64("MPSecK-7b233b4cb707b7c00b1d8103bda935b4-X")),'"}'))));
