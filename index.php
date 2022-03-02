<?php
  session_start();
  require_once 'inc/functions.php';
  $seedInfo             = '';
  $fileTypeError        = '';
  $_SESSION['loggedin'] = $_SESSION['loggedin'] ?? '';

  $task = $_GET['task'] ?? 'dashboard';
  if ( 'seed' == $task ) {
    seed();
    $seedInfo = 'Sedding Successfuly';
  }

  $fname   = '';
  $lname   = '';
  $class   = '';
  $roll    = '';
  $phone   = '';
  $imgName = '';
  $error   = $_GET['error'] ?? '';

  $fileType = [
    'image/png',
    'image/jpg',
    'image/jpeg',
  ];

  if ( isset( $_POST['save'] ) ) {
    $fname     = FILTER_INPUT( INPUT_POST, 'fname', FILTER_SANITIZE_STRING );
    $lname     = FILTER_INPUT( INPUT_POST, 'lname', FILTER_SANITIZE_STRING );
    $class     = FILTER_INPUT( INPUT_POST, 'class', FILTER_SANITIZE_STRING );
    $roll      = FILTER_INPUT( INPUT_POST, 'roll', FILTER_SANITIZE_STRING );
    $phone     = FILTER_INPUT( INPUT_POST, 'phone', FILTER_SANITIZE_STRING );
    $id        = FILTER_INPUT( INPUT_POST, 'id', FILTER_SANITIZE_STRING );
    $imagePath = "database/images/" . $_FILES['pro-pic']['name'];
    $ext       = pathinfo( $imagePath, PATHINFO_EXTENSION );
    // $file      = basename( $imagePath, "." . $ext );

    if ( $id ) {
      if ( $fname != '' && $lname != '' && $class != '' && $roll != '' && $phone != '' ) {
        $imgName = "database/images/" . $class . $roll . '.' . $ext;
        $result  = updateStudent( $id, $fname, $lname, $class, $roll, $phone, $imgName );
        if ( $result ) {
          if ( $_FILES['pro-pic'] ) {
            if ( in_array( $_FILES['pro-pic']['type'], $fileType ) ) {
              move_uploaded_file( $_FILES['pro-pic']['tmp_name'], $imgName );
            }
          }
          header( "location: /?task=view&id=$id" );
        } else {
          $error = 1;
        }
      }

    } else {
      if ( $fname != '' && $lname != '' && $class != '' && $roll != '' && $phone != '' && $_FILES['pro-pic']['size'] != 0 ) {
        $imgName = "database/images/" . $class . $roll . '.' . $ext;
        $result  = addStudent( $fname, $lname, $class, $roll, $phone, $imgName );
        if ( $result ) {
          if ( $_FILES['pro-pic'] ) {
            if ( in_array( $_FILES['pro-pic']['type'], $fileType ) ) {
              move_uploaded_file( $_FILES['pro-pic']['tmp_name'], $imgName );
            }
          }
          header( 'location: /?task=dashboard&error=-1' );
        } else {
          $error = 1;
        }
      }
    }
  }

  if ( 'delete' == $task ) {
    if(!isAdmin()){
      header('location:index.php');
      return;
    }
    $id = FILTER_INPUT( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
    deleteStudent( $id );
    if ( deleteStudent( $id ) ) {
      header( 'location: /?task=all' );
    }
  }

  if ( isset( $_POST['search'] ) ) {
    $class = FILTER_INPUT( INPUT_POST, 'class', FILTER_SANITIZE_STRING );
    $roll  = FILTER_INPUT( INPUT_POST, 'roll', FILTER_SANITIZE_STRING );
    if ( $class != '' && $roll != '' ) {
      $task    = 'search';
      $student = getSearchStudent( $class, $roll );
    }
  }

  if ( isset( $_POST['logout'] ) ) {
    $_SESSION['loggedin'] = false;
    $_SESSION['user']     = false;
    $_SESSION['role']     = false;
    session_destroy();
    header('location: index.php');
  }

  if('edit'==$task || 'add'==$task || 'all'==$task || 'view'==$task){
    if(!(isAdmin() || isEditor())){
      header('location:index.php');
      return;
    }
  }

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CRUD</title>
  <!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,300italic,700,700italic"> -->
  <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/bootstrap-confirm-delete.css">
  <link rel="stylesheet" href="assets/css/main.css">
</head>

<body>

  <div class="container-fluid px-0">
    <nav class="navbar navbar-expand navbar-light bg-light">
      <a class="navbar-brand" href="#">LOGO</a>
      <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
        <li class="nav-link active">Hello,
          <?php
            if ( true == $_SESSION['loggedin'] ) {
              printf( "%s ( %s )", ucfirst( $_SESSION['user'] ), ucfirst( $_SESSION['role'] ) );
            } else {
              echo 'Stranger';
            }
          ?>
        </li>
        <?php
          if ( false == $_SESSION['loggedin'] ):
        ?>
        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
        <?php
          endif;
        ?>
      </ul>
    </nav>
  </div>

  <?php
    if ( $_SESSION['loggedin'] == false ):
  ?>
  <div class="container mt-5">
    <div class="row justify-content-center">
      <div class="col-3 mb-4">
        <div class="card">
          <div class="card-header">
            <b>Search Student</b>
          </div>
          <div class="card-body">
            <form method="POST">
              <div class="form-group input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"> <i class="fas fa-university"></i> </span>
                </div>
                <input name="class" class="form-control" placeholder="Class" type="text">
              </div> <!-- form-group// -->

              <div class="form-group input-group">
                <div class="input-group-prepend">
                  <span class="input-group-text"> <i class="fas fa-user-graduate"></i> </span>
                </div>
                <input name="roll" class="form-control" placeholder="Roll" type="text">
              </div> <!-- form-group// -->

              <button type="submit" name="search" class="btn btn-primary">Search</button>
            </form>
          </div>
        </div>
      </div> <!-- col end  -->

      <div class="w-100"></div>

      <?php
        if ( 'search' == $task ):
          if ( $student ):
        ?>
      <div class="col-4 ">
        <div class="card">
          <div class="card-header alert alert-success">
            Student found
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-12 text-center">
                <img src="<?php echo $student['proPic']; ?>" class="mb-4" width="100px" height="100px" style="border-radius:50%">
              </div>
              <div class="col-5 mb-1">
                <h6>Name</h6>
              </div>
              <div class="col-7 mb-1">
                <span class="text-secondary"><?php printf( "%s %s", $student['fname'], $student['lname'] ); ?></span>
              </div>

              <div class="col-5 mb-1">
                <h6>Class</h6>
              </div>
              <div class="col-7 mb-1">
                <span class="text-secondary"><?php echo $student['class']; ?></span>
              </div>

              <div class="col-5 mb-1">
                <h6>Roll</h6>
              </div>
              <div class="col-7 mb-1">
                <span class="text-secondary"><?php echo $student['roll']; ?></span>
              </div>

              <div class="col-5 mb-1">
                <h6>Mobile NO.</h6>
              </div>
              <div class="col-7 mb-1">
                <span class="text-secondary"><?php echo $student['mobile']; ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php
          else:
        ?>
      <div class="col-4">
        <div class="card-header alert alert-danger">
          <i class="fas fa-exclamation-triangle"></i> Student didn't found!
        </div>
      </div>
      <?php
        endif;
        endif;
      ?>
    </div>
  </div>
  <?php
    else:
  ?>
  <div class="container-fluid mt-5">
    <div class="row">
      <div class="col-2">
        <div class="nav flex-column nav-pills" id="v-pills-tab">
          <a class="nav-link<?php if ( 'dashboard' == $task ) {echo " active";} ?>" id="dashboard-tab" href="./?task=dashboard"><span class="profile-tab-icon"><i
                class="fas fa-user"></i></span>
            <span class="d-none d-lg-inline-block">Dashboard</span></a>
          <a class="nav-link<?php if ( 'all' == $task ) {echo " active";} ?>" id="all-student-tab" href="./?task=all"><span class="profile-tab-icon"><i
                class="fas fa-users"></i></span>
            <span class="d-none d-lg-inline-block">All Students</span></a>
          <a class="nav-link<?php if ( 'add' == $task ) {echo " active";} ?>" id="add-student-tab" href="./?task=add"><span class="profile-tab-icon"><i
                class="fas fa-user-plus"></i></span>
            <span class="d-none d-lg-inline-block">Add Student</span></a>
          <a class="nav-link<?php if ( 'logout' == $task ) {echo " active";} ?>" id="logout-tab" href="./?task=logout"><span class="profile-tab-icon"><i
                class="fas fa-sign-out"></i></span>
            <span class="d-none d-lg-inline-block">Logout</span></a>
        </div>
      </div>
      <!-- col end -->

      <div class="col-lg-10">
        <div class="px-4">
          <!-- dashboard start -->
          <?php
            if ( 'dashboard' == $task ):
          ?>
          <h2>Dashboard</h2>
          <hr>

          <?php
            if ( $error == -1 ):
          ?>
          <div class="alert alert-success mb-5">
            New student added successfully
          </div>
          <?php
            endif;
          ?>

          <div class="row mb-5">
            <div class="col-4">
              <div class="card">
                <div class="card-body">
                  <h4 class="card-title">Total Students</h4>
                  <p class="card-text"><?php echo totalStudent(); ?></p>
                  <a href="./?task=all" class="btn button-clear" id="view-all">View All</a>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <h3>New 3 Students</h3>
              <table class="table table-hover">
                <thead class="thead-dark">
                  <tr>
                    <th>NO.</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Class</th>
                    <th>Roll</th>
                    <th>Mobile NO.</th>
                    <th>Image</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    newStudent();
                  ?>
                </tbody>
              </table>
            </div>
          </div> <!-- row end -->
          <?php
            endif;
          ?>
          <!-- dashboard end -->

          <!-- all student end -->
          <?php
            if ( 'all' == $task ):
            ?>
          <h2>All Students</h2>
          <?php
              if(isAdmin()):
            ?>
          <a href="./?task=seed">Seed</a>
          <?php
              endif;
            ?>
          <hr>
          <div class="row">
            <div class="col-12">
              <table class="table table-hover">
                <thead class="thead-dark">
                  <tr>
                    <th>NO.</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Class</th>
                    <th>Roll</th>
                    <th>Mobile NO.</th>
                    <th>Image</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    generateReport();
                  ?>
                </tbody>
              </table>
            </div>
          </div> <!-- row end -->
          <?php
            endif;
          ?>
          <!-- all student end -->

          <!-- add student start -->
          <?php
            if ( 'add' == $task ):
          ?>
          <h2>Add Student</h2>
          <hr>

          <?php
            if ( $error == 1 ):
          ?>
          <div class="alert alert-danger mb-5">
            <i class="fas fa-exclamation-triangle"></i> <b>Duplicate found:</b> Class and roll should not be matched
          </div>
          <?php
            endif;
          ?>

          <div class="row">
            <div class="col-5">
              <form method="POST" enctype="multipart/form-data">
                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                  </div>
                  <input name="fname" class="form-control" placeholder="First Name" type="text" value="<?php echo $fname; ?>">
                </div> <!-- form-group// -->

                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                  </div>
                  <input name="lname" class="form-control" placeholder="Last Name" type="text" value="<?php echo $lname; ?>">
                </div> <!-- form-group// -->

                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-university"></i> </span>
                  </div>
                  <input name="class" class="form-control" placeholder="Class" type="text" value="<?php echo $class; ?>">
                </div> <!-- form-group// -->

                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-user-graduate"></i> </span>
                  </div>
                  <input name="roll" class="form-control" placeholder="Roll" type="text" value="<?php echo $roll; ?>">
                </div> <!-- form-group// -->

                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-mobile-alt"></i> </span>
                  </div>
                  <input name="phone" class="form-control" placeholder="Mobile Number" type="text" value="<?php echo $phone; ?>">
                </div> <!-- form-group// -->

                <div class="form-group">
                  <label for="pro-pic">Profile Picture</label>
                  <input name="pro-pic" type="file" class="form-control-file" id="pro-pic" value="<?php echo $imgName; ?>">
                </div>

                <button type="submit" name="save" class="btn btn-primary">Save</button>
              </form>
            </div>
          </div>
          <?php
            endif;
          ?>
          <!-- add student end -->

          <!-- logout start -->
          <?php
             if ( 'logout' == $task ):
            ?>
          <h2>Logout</h2>
          <hr>
          <p>Are you sure to logout?</p>
          <form method="POST">
            <input type="hidden" name="logout" value="1">
            <button class="btn btn-danger">Logout</button>
            <a href="./?task=dashboard" class="btn btn-success">Go to Dashboard</a>
          </form>
          <?php
            endif;
           ?>
          <!-- logout end -->

          <!-- seeding status start -->
          <?php
            if ( 'seed' == $task && seed() ):
          ?>
          <div class="row mb-5">
            <div class="col-4">
              <div class="alert alert-success mb-5">
                <?php
                  echo $seedInfo;
                ?>
              </div>
            </div>
          </div> <!-- row end -->
          <?php
            endif;
          ?>
          <!-- seeding status end -->

          <!-- edit student start -->
          <?php
            if ( 'edit' == $task ):
              $id      = FILTER_INPUT( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
              $student = getStudent( $id );
              if ( $student ):
            ?>
          <h2>Edit Student</h2>
          <hr>

          <?php
                if ( $error == 1 ):
              ?>
          <div class="alert alert-danger mb-5">
            <i class="fas fa-exclamation-triangle"></i> <b>Duplicate found:</b> Class and roll should not be matched
          </div>
          <?php
              endif;
            ?>

          <div class="row mb-5">
            <div class="col-5">
              <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                  </div>
                  <input name="fname" class="form-control" placeholder="First Name" type="text" value="<?php if ( $error == 1 ) {echo $fname;} else {echo $student['fname'];} ?>">
                </div> <!-- form-group// -->
                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fa fa-user"></i> </span>
                  </div>
                  <input name="lname" class="form-control" placeholder="Last Name" type="text" value="<?php if ( $error == 1 ) {echo $lname;} else {echo $student['lname'];} ?>">
                </div> <!-- form-group// -->

                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-university"></i> </span>
                  </div>
                  <input name="class" class="form-control" placeholder="Class" type="text" value="<?php if ( $error == 1 ) {echo $class;} else {echo $student['class'];} ?>">
                </div> <!-- form-group// -->
                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-user-graduate"></i> </span>
                  </div>
                  <input name="roll" class="form-control" placeholder="Roll" type="text" value="<?php if ( $error == 1 ) {echo $roll;} else {echo $student['roll'];} ?>">
                </div> <!-- form-group// -->

                <div class="form-group input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"> <i class="fas fa-mobile-alt"></i> </span>
                  </div>
                  <input name="phone" class="form-control" placeholder="Mobile Number" type="text"
                    value="<?php if ( $error == 1 ) {echo $phone;} else {echo $student['mobile'];} ?>">
                </div> <!-- form-group// -->

                <div class="form-group">
                  <label for="pro-pic">Profile Picture</label>
                  <input name="pro-pic" type="file" class="form-control-file" id="pro-pic">
                </div>

                <button type="submit" name="save" class="btn btn-primary">Update</button>
              </form>
            </div>
          </div>
          <?php
            endif;
            endif;
          ?>
          <!-- edit student end -->

          <!-- view student start -->
          <?php
            if ( 'view' == $task ):
              $id      = FILTER_INPUT( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
              $student = getStudent( $id );
              if ( $student ):
            ?>
          <h3>Personal Information</h3>
          <hr>
          <div class="row">
            <div class="col-3">
              <div class="single-pro-pic">
                <img src="<?php echo $student['proPic']; ?>" class="mb-3" width="100%" height="100%" style="border-radius:50%">
              </div>
            </div> <!-- col end -->

            <div class="col-9">
              <div class="profile-info">
                <div class="badge badge-info mb-2 py-2 px-3">Hello</div>
                <h4><span class="text-secondary">I'm</span> <span class="text-dark"><?php printf( "%s %s", $student['fname'], $student['lname'] ); ?></span></h4>
                <hr>
                <div class="row">
                  <div class="col-2 mb-1">
                    <h6>Name</h6>
                  </div>
                  <div class="col-10 mb-1">
                    <span class="text-secondary"><?php printf( "%s %s", $student['fname'], $student['lname'] ); ?></span>
                  </div>

                  <div class="col-2 mb-1">
                    <h6>Class</h6>
                  </div>
                  <div class="col-10 mb-1">
                    <span class="text-secondary"><?php echo $student['class']; ?></span>
                  </div>

                  <div class="col-2 mb-1">
                    <h6>Roll</h6>
                  </div>
                  <div class="col-10 mb-1">
                    <span class="text-secondary"><?php echo $student['roll']; ?></span>
                  </div>

                  <div class="col-2 mb-1">
                    <h6>Mobile NO.</h6>
                  </div>
                  <div class="col-10 mb-1">
                    <span class="text-secondary"><?php echo $student['mobile']; ?></span>
                  </div>
                </div>
                <div class="mt-4">
                  <a href="./?task=edit&id=<?php echo $id; ?>" class="btn btn-success"><i class="fas fa-pencil-alt"></i> Edit</a>
                  <a href="./?task=delete&id=<?php echo $id; ?>" class="delete btn btn-danger"><i class="fas fa-trash-alt"></i> Delete</a>
                </div>
              </div>
            </div>
          </div>
          <?php
              endif;
              endif;
            ?>
          <!-- view student end -->
        </div>
      </div> <!-- col end -->
    </div> <!-- row end -->
  </div> <!-- container end -->
  <?php
    endif;
  ?>




  <script src="assets/js/jquery-3.5.1.min.js"></script>
  <script src="assets/js/popper.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/bootstrap-confirm-delete.js"></script>
  <script src="assets/js/mian.js"></script>
</body>

</html>