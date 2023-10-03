DROP TABLE IF EXISTS signup;
CREATE TABLE `signup` (
  `s_no` int(11) NOT NULL AUTO_INCREMENT,
  `bizname` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `code` text NOT NULL,
  `username` text NOT NULL DEFAULT 'demo',
  PRIMARY KEY (`s_no`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO signup VALUES ('1','Excel Tech Essentials','info@essentialtech.site','254725887269','1TZgp9u32Ecf/NMgVWNnpqt7u7d2rTZwyKITAq7XS70IriSHJO0/w34w5YZHCGKkOGqu+liGjVQGLfUjHOlm6qfCg2EuCUWEyhscKvA16igPH1b/E8VAkqdHSRoL4jCZ','demo');
INSERT INTO signup VALUES ('2','Sweet savour perfumes ','njorogeoffice@gmail.com','254718509240','bI8wGQBQhDkFQTkavH86mCojqP639dUfgM5863arKnXStRhdBt7Gb3oxqAP/698IJ177qPFEOy/HsG56A4Nhf9aC2kRqsilteqmYvnQ1cbRGdbNPyNdLX3E1QGYPUjHi','demo');
INSERT INTO signup VALUES ('3','Oneclin ','peterwano@gmail.com','254710422071','jVWmjvNaVdu9oH0aySSqvEbOKHDHXjXbZUaXGEb8HZRr3DI9VR5stIzu1LDOMVJmvzAZ1zGO3pNXN9uvSyaegrp+lGkSbdSZsiI0WKsFIxoNuuPDJSoq37JP9G0lYx4Q','demo');
INSERT INTO signup VALUES ('4','Fashion','timnmburu1@gmail.com','254733440443','O3q7ahhg6mS4Yl6LfMJkwyqpIYUA0+5IsLi9Zwpj3cabTeZqZLbo+mhnwIGt0zT3VF30ZCs+WBuOc0+2jsXrc24MUhqQjBqsJ+r6K1bhaN8vKuUt1sqgWs98LeUsulkB','demo');
INSERT INTO signup VALUES ('5','Cute girl ','ceciliandindah@gmail.com','254798297323','CZvgYJgkS0IuRTm5RvCLyjIgGJp2cSnHnOypjlOLr+KTcvHsWLmp7bxhIFaqEirgvBpPXIxPMSFKrjU4QxG8CjqP2kr0GO614ITQofcC54O0fxaPbtpPSZoOUbY23VNU','demo');
