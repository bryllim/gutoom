
# Gutoom?

Imagine going out on a date with your love one to find out that your favorite restaurant is full to the brim and you are force to settle with the lowest rated restaurant in town with close to no people at all, is that the perfect date you imagined? With our application we could help you score BIG and take the home run! By booking a head on special nights using our application.

  

Gutoom allows people to make online table reservations at their favorite restaurants in a seamless, hassle-free manner and help out customers in deciding the right dinning/outing for their specific taste. People prefer to go out knowing that they have reservation, instead of incurring risk of not getting a table at they’re desired place. In this online restaurant table reservation, customers are ensured that they will have a great experience on the restaurant they like, it guarantees the customers that they will receive his table at the time and place they planned and will not have to go through the troubles of waiting until a table is available or being put on a waiting list, or worst needing to find another place to eat because the one chosen won’t be able to serve them. Customers can choose a restaurant based on location, timing, cuisine, and number of guests..

**Admin Credentials**
Email: admin@gutom.com
Password: 123

### Steps to Send Mail From Localhost XAMPP Using Gmail:

1.  Open XAMPP Installation Directory.
2.  Go to C:\xampp\php and open the php.ini file.
3.  Find [mail function] by pressing ctrl + f.
4.  Search and pass the following values:  
    
    SMTP=smtp.gmail.com
    smtp_port=587
    sendmail_from  =  arnneleighbasle@gmail.com
    sendmail_path  =  "\"C:\xampp\sendmail\sendmail.exe\" -t"
    
5.  Now, go to C:\xampp\sendmail and open sendmail.ini file.
6.  Find [sendmail] by pressing ctrl + f.
7.  Search and pass the following values  
        
    smtp_server=smtp.gmail.com
    smtp_port=587
    error_logfile=error.log
    debug_logfile=debug.log
    auth_username=arnneleighbasle@gmail.com
    auth_password=arnneleigh
