<?php
ob_start();
session_start();

include_once 'dbconnect.php';

if (isset($_SESSION['user']) != "") {
    // select logged in user information
    $res = $conn->query("SELECT * FROM users WHERE id=" . $_SESSION['user']);
    $userRow = mysqli_fetch_array($res, MYSQLI_ASSOC);
}

//registration
if (isset($_POST['signup'])) {

    $uname = trim($_POST['uname']); // get posted data and remove whitespace
    $email = trim($_POST['email']);
    $upass = trim($_POST['pass']);

    // hash password with SHA256;
    $password = hash('sha256', $upass);

    // check email exist or not
    $stmt = $conn->prepare("SELECT email FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    $count = $result->num_rows;

    if ($count == 0) { // if email is not found add user


        $stmts = $conn->prepare("INSERT INTO users(username,email,password) VALUES(?, ?, ?)");
        $stmts->bind_param("sss", $uname, $email, $password);
        $res = $stmts->execute();//get result
        $stmts->close();

        $user_id = mysqli_insert_id($conn);
        if ($user_id > 0) {
            $_SESSION['user'] = $user_id; // set session and redirect to index page
            if (isset($_SESSION['user'])) {
                print_r($_SESSION);
                header("Location: index.php");
                exit;
            }

        } else {
            $errTyp = "danger";
            $errMSG = "Something went wrong, please try again.";
        }

    } else {
        $errTyp = "warning";
        $errMSG = "Email is already used.";
    }

}

//login
if (isset($_POST['btn-login'])) {
    $email = $_POST['email'];
    $upass = $_POST['pass'];

    $password = hash('sha256', $upass); // password hashing using SHA256
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email= ?");
    $stmt->bind_param("s", $email);
    //execute query
    $stmt->execute();
    //get result
    $res = $stmt->get_result();
    $stmt->close();

    $row = mysqli_fetch_array($res, MYSQLI_ASSOC);

    $count = $res->num_rows;
    if ($count == 1 && $row['password'] == $password) {
        $_SESSION['user'] = $row['id'];
        header("Location: index.php");
    } elseif ($count == 1) {
        $errMSG = "Bad password!";
    } else $errMSG = "User not found!";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gutoom?</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style>
        body {
            background: pink;
        }
        
        a {
            color:black;
        }
        a:hover {
            color:gray;
        }
        .img {
            height: 100%;
            width: 100%
        }
        
        .bold {
            font-weight: 900;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-sm bg-light navbar-light">
        <div class="container">
            <a class="navbar-brand d-none d-md-block" href="index.php">
                <img src="images/logo.png" style="height:auto; width:20%">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse bold" id="collapsibleNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="categories.php">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="modal" data-target="#aboutModal">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="modal" data-target="#contactModal">Contact</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <?php
                    if (isset($_SESSION['user']) == "") {
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="#" data-toggle="modal" data-target="#loginModal">Login</a>';
                        echo '</li>';
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="#" data-toggle="modal" data-target="#registerModal">Register</a>';
                        echo '</li>';
                    }else{
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="#">'.$userRow['email'].'</a>';
                        echo '</li>';
                        echo '<li class="nav-item">';
                        echo '<a class="nav-link" href="logout.php?logout">Logout</a>';
                        echo '</li>';
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>



    <div class="container" style="margin-top:30px">
        <!-- Check if there's an error -->
        <?php
        if (isset($errMSG)) {

            ?>
            <div class="form-group">
                <div class="alert alert-danger text-center">
                    <?php echo $errMSG; ?>
                </div>
            </div>
            <?php
        }
        ?>
        <!-- Greet the user when login -->
        <?php
            if (isset($_SESSION['user']) != "") {
                echo "<h3 class='text-center'>Welcome, ".$userRow['username']."</h3>";
            }
        ?>
        <div class="row">
            <div class="col-12">
                <h2 class="bold text-center">Giuseppe Pizzeria & Sicilian Roast</h2>
                <hr>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <a href="guisepe.php">
                    <img src="images/restaurants/guise.png"  style="height:auto; width:100%"> 
                </a>
            </div>
            <div class="col-4">
            <h1 >Italian Restaurant </h1>
            <h5 >Contact Details</h5>
            <h5 >+63 038 502 4255</h5>
            <h5 >Monday to Sunday </h5>  
            <h5>11:00 am - 11:00 pm </h5>


            <div class="container">
  <!-- Button to Open the Modal -->
  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
   View Menu
  </button>

  <!-- The Modal -->
  <div class="modal" id="myModal">
    <div class="modal-dialog">
      <div class="modal-content">
      
        <!-- Modal Header -->
        <div class="modal-header">
          <h1 class="bold text-center">Giuseppe Pizzeria</h1>
          <button type="button" class="close" data-dismiss="modal">×</button>
        </div>
        
        <!-- Modal body -->
        <div class="modal-body">


          <h2 class="bold text-center">Menu</h2>
        <hr>
        <div class="row">
            <div class="col-12">
                <div id="dish" class="carousel slide" data-ride="carousel">
                    <ul class="carousel-indicators">
                        <li data-target="#dish" data-slide-to="0" class="active"></li>
                        <li data-target="#dish" data-slide-to="1"></li>
                        <li data-target="#dish" data-slide-to="2"></li>
                        <li data-target="#dish" data-slide-to="3"></li>
                        <li data-target="#dish" data-slide-to="4"></li>

                    </ul>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="img" src="images/restaurants/menu1.jpg" alt="Menu1s" width="1100" height="500">
                        </div>
                        <div class="carousel-item">
                            <img class="img" src="images/restaurants/menu2.jpg" alt="Menu2" width="1100" height="500">
                        </div>
                        <div class="carousel-item">
                            <img class="img" src="images/restaurants/menu3.jpg" alt="Menu3" width="1100" height="500">
                            
                        </div>
                         <div class="carousel-item">
                            <img class="img" src="images/restaurants/menu4.jpg" alt="Menu4" width="1100" height="500">
                            
                        </div>
                         <div class="carousel-item">
                            <img class="img" src="images/restaurants/menu5.jpg" alt="Menu5 " width="1100" height="500">
                            
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#dish" data-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </a>
                    <a class="carousel-control-next" href="#dish" data-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </a>
                </div>
            </div>
        </div>

          <h2 class="bold text-center">Reviews</h2>
         <script src="https://apps.elfsight.com/p/platform.js" defer></script>
<div class="elfsight-app-80229266-7382-4c5f-bd78-8b83c31cc208"></div>
        </div>
        
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
            </div>
    </div>
  </div>
  
</div>
<br>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#basicExampleModal">
  Reserve A table Now
</button>

<!-- Modal -->
<div class="modal fade" id="basicExampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Reservation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <center><form method="post" action="//submit.form" onSubmit="return validateForm();">
<div style="width: 400px;">
</div>
<div style="padding-bottom: 18px;font-size : 24px;">Reservation Details</div>
<div style="display: flex; padding-bottom: 18px;width : 450px;">
<div style=" margin-left : 0; margin-right : 1%; width : 49%;">First name<span style="color: red;"> *</span><br/>
<input type="text" id="data_2" name="data_2" style="width: 100%;" class="form-control"/>
</div>
<div style=" margin-left : 1%; margin-right : 0; width : 49%;">Last name<span style="color: red;"> *</span><br/>
<input type="text" id="data_3" name="data_3" style="width: 100%;" class="form-control"/>
</div>
</div><div style="padding-bottom: 18px;">Phone<span style="color: red;"> *</span><br/>
<input type="text" id="data_4" name="data_4" style="width : 450px;" class="form-control"/>
</div>
<div style="padding-bottom: 18px;">Email<span style="color: red;"> *</span><br/>
<input type="text" id="data_5" name="data_5" style="width : 450px;" class="form-control"/>
</div>
<div style="padding-bottom: 18px;">Time and Date<span style="color: red;"> *</span><br/>
<input type="text" id="data_6" name="data_6" style="width : 250px;" class="form-control"/>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.4.0/pikaday.min.js" type="text/javascript"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/pikaday/1.4.0/css/pikaday.min.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">new Pikaday({ field: document.getElementById('data_6') });</script>
<div style="padding-bottom: 18px;">Number of adults<span style="color: red;"> *</span><br/>
<input type="number" id="data_8" name="data_8" style="width : 250px;" class="form-control"/>
</div>
<div style="padding-bottom: 18px;">Number of children<br/>
<input type="number" id="data_9" name="data_9" style="width : 250px;" class="form-control"/>
</div>
<div style="padding-bottom: 18px;">Notes to the Restaurant<br/>
<textarea id="data_10" false name="data_10" style="width : 450px;" rows="6" class="form-control"></textarea>
</div>
<div style="padding-bottom: 18px;"><input name="skip_Submit" value="Submit" type="submit"/></div>
<div>
</div>
</form>
</center>

<script type="text/javascript">
function validateForm() {
if (isEmpty(document.getElementById('data_2').value.trim())) {
alert('First name is required!');
return false;
}
if (isEmpty(document.getElementById('data_3').value.trim())) {
alert('Last name is required!');
return false;
}
if (isEmpty(document.getElementById('data_4').value.trim())) {
alert('Phone is required!');
return false;
}
if (isEmpty(document.getElementById('data_5').value.trim())) {
alert('Email is required!');
return false;
}
if (!validateEmail(document.getElementById('data_5').value.trim())) {
alert('Email must be a valid email address!');
return false;
}
if (isEmpty(document.getElementById('data_6').value.trim())) {
alert('Time and Date is required!');
return false;
}
if (isEmpty(document.getElementById('data_8').value.trim())) {
alert('Number of adults is required!');
return false;
}
return true;
}
function isEmpty(str) { return (str.length === 0 || !str.trim()); }
function validateEmail(email) {
var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,15}(?:\.[a-z]{2})?)$/i;
return isEmpty(email) || re.test(email);
}


</script>

        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>

<h1 class="col-10" >MAP</h1>

 <div class="row">
            <div class="col-6">
      <div class="mapouter"><div class="gmap_canvas"><iframe width="348" height="389" id="gmap_canvas" src="https://maps.google.com/maps?q=Paseo%20Saturnino%2C%20Cebu%20City%2C%206000%20Cebu&t=k&z=15&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://www.embedgooglemap.net">embedgooglemap.net</a></div><style>.mapouter{position:relative;text-align:right;height:389px;width:348px;}.gmap_canvas {overflow:hidden;background:none!important;height:389px;width:348px;}</style></div>
   
    </div>
</div>
















</div>








        




        <!--Footer End Tags --> 
      </div>
    </div>
  </div>
  
</div>
          </div>
        </div>
        <hr>
    </div>



    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login to your account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" autocomplete="off">
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Email" required/>
                            <input type="password" name="pass" class="form-control mt-2" placeholder="Password" required/>
                        </div>
                        <hr>
                        <button type="submit" name="btn-login" class="btn btn-block btn-light bold" style="background:pink !important; border:0 !important">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create an account</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" autocomplete="off">
                        <div class="form-group">
                            <input type="text" name="uname" class="form-control" placeholder="Enter a username..." required/>
                            <input type="email" name="email" class="form-control mt-1" placeholder="Enter your email..." required/>
                            <input type="password" name="pass" class="form-control mt-1" placeholder="Enter a password..." required/>
                        </div>
                        <hr>
                        <button type="submit" name="signup" id="reg" class="btn btn-block btn-light bold" style="background:pink !important; border:0 !important">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- About Modal -->
    <div class="modal fade" id="aboutModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">About Us</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                Imagine going out on a date with your loved one to find out that your favorite restaurant is full to the brim and you are force to settle with the lowest rated restaurant in town with close to no people at all, is that the perfect date you imagined?
                <br><br>
                With our application we could help you score <span class="bold">BIG</span> and take the home run!
                <br><br>By booking ahead on special nights using our application.
                Gutoom allows people to make online table reservations at their favorite restaurants in a seamless, hassle-free manner and help out customers in deciding the right dinning/outing for their specific taste. People prefer to go out knowing that they have reservation, instead of incurring risk of not getting a table at their desired place. In this online restaurant table reservation, customers are ensured that they will have a great experience on the restaurant they like, it guarantees the customers that they will receive his table at the time and place they planned and will not have to go through the troubles of waiting until a table is available or being put on a waiting list, or worst needing to find another place to eat because the one chosen won’t be able to serve them. Customers can choose a restaurant based on location, timing, cuisine, and number of guests.
                <hr>
                <h4 class="text-center">MEET THE TEAM</h4>
                <img src="images/developers.png" style="height:auto; width:100%">
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact Us</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6>Phone Number: <span class="bold">(0955) 610 9996</span></h6>
                    <h6>Telephone Number: <span class="bold">2727-8701</span></h6>
                     <h3>Leave a message</h3>

 
       
        
       Name:<br> 
            <input type="text" placeholder="Enter your name"  required="required"required><br><br>
       Email: <br>
            <input   type="text" placeholder="Enter your email" required><br><br>
       Message:<br>
           <textarea rows="4" cols="50" name="comment" placeholder="Enter text here..." form="usrform">
</textarea>
            <br>
        Gender:   
             <input type="radio" name="gender" value="male"> Male
            <input type="radio" name="gender" value="female"> Female<br>
            
            
            <a href="#" id="submit" type="submit" onclick="myFunction('submit');" class="btn btn-sm animated-button victoria-four">Submit</a> 
            <a href="#" id="cancel" onclick="myFunction('cancel');"   class="btn btn-sm animated-button victoria-four">Cancel</a> 
           
        <script>
function myFunction(x) {
    
    if(x == "submit"){
        alert("Message Successfully Send");
    window.location="Home.html"
    
    }else{
        alert("Message Canceled");
     window.location="Contact.html"
   
    }
  
}
</script>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="jumbotron text-center" style="margin-bottom:0">
        <p>GUTOOM? Website, Copyright © 2019</p>
    </div>

</body>

</html>