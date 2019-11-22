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
            <a class="navbar-brand d-none d-md-block" href="#">
                <img src="images/logo.png" style="height:auto; width:20%">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse bold" id="collapsibleNavbar">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
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
        <!-- Dishes carousel -->
        <h2 class="bold">Popular Dishes</h2>
        <hr>
        <div class="row">
            <div class="col-12">
                <div id="dish" class="carousel slide" data-ride="carousel">
                    <ul class="carousel-indicators">
                        <li data-target="#dish" data-slide-to="0" class="active"></li>
                        <li data-target="#dish" data-slide-to="1"></li>
                        <li data-target="#dish" data-slide-to="2"></li>
                    </ul>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="img" src="images/3.png" alt="Los Angeles" width="1100" height="500">
                            <div class="carousel-caption">
                                <h3>Family Meals</h3>
                                <p>Enjoy a great selection of dishes perfect for your family.</p>
                                <br>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img class="img" src="images/2.png" alt="Chicago" width="1100" height="500">
                            <div class="carousel-caption">
                                <h3>Exquisite Pizzas</h3>
                                <p>Have a taste of pizzas with a wide variety of flavors.</p>
                                <br>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img class="img" src="images/1.png" alt="New York" width="1100" height="500">
                            <div class="carousel-caption">
                                <h3>Tempting Seafood</h3>
                                <p>Eat like a king with perfectly-cooked seafood.</p>
                                <br>
                            </div>
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
        <!-- Restaurants carousel -->
        <h2 class="bold">Popular Restaurants</h2>
        <hr>
        <div class="row">
            <div class="col-12">
                <div id="restaurant" class="carousel slide" data-ride="carousel">
                    <ul class="carousel-indicators">
                        <li data-target="#restaurant" data-slide-to="0" class="active"></li>
                        <li data-target="#restaurant" data-slide-to="1"></li>
                        <li data-target="#restaurant" data-slide-to="2"></li>
                    </ul>
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="img" src="images/r3.png" alt="Los Angeles" width="1100" height="500">
                            <div class="carousel-caption">
                                <h3>Bar Pintxos</h3>
                                <p>The food, first and foremost, is spectacular.</p>
                                <br>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img class="img" src="images/r2.png" alt="Chicago" width="1100" height="500">
                            <div class="carousel-caption">
                                <h3>Benjarong Royal Thai</h3>
                                <p>Experience gourmet like never before.</p>
                                <br>
                            </div>
                        </div>
                        <div class="carousel-item">
                            <img class="img" src="images/r1.png" alt="New York" width="1100" height="500">
                            <div class="carousel-caption">
                                <h3>Canton Road</h3>
                                <p>Majestic food regarded as masterpieces await.</p>
                                <br>
                            </div>
                        </div>
                    </div>
                    <a class="carousel-control-prev" href="#restaurant" data-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </a>
                    <a class="carousel-control-next" href="#restaurant" data-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </a>
                </div>
            </div>
        </div>
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


    <div class="jumbotron text-center" style="margin-bottom:0">
        <p>GUTOOM? Website, Copyright © 2019</p>
    </div>

</body>

</html>