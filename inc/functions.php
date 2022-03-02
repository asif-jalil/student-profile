<?php

  define( 'DB_NAME', '.\database\file.txt' );
  function seed() {
    $students = [
      [
        'id'     => '1',
        'fname'  => 'Asif',
        'lname'  => 'Jalil',
        'class'  => '18',
        'roll'   => '141335',
        'mobile' => '01719836117',
        'proPic' => "database/images/1.jpg",
      ],

      [
        'id'     => '2',
        'fname'  => 'Saif',
        'lname'  => 'Jalil',
        'class'  => '8',
        'roll'   => '10',
        'mobile' => '01719836117',
        'proPic' => "database/images/2.jpg",
      ],

      [
        'id'     => '3',
        'fname'  => 'Fahad',
        'lname'  => 'Al Bin',
        'class'  => '18',
        'roll'   => '141313',
        'mobile' => '0171923546',
        'proPic' => "database/images/3.jpg",
      ],

      [
        'id'     => '4',
        'fname'  => 'Arifuzzaman',
        'lname'  => 'Limon',
        'class'  => '18',
        'roll'   => '131305',
        'mobile' => '0192245732',
        'proPic' => "database/images/4.jpg",
      ],
      [
        'id'     => '5',
        'fname'  => 'Akash',
        'lname'  => 'Rahman',
        'class'  => '10',
        'roll'   => '12',
        'mobile' => '01262999111',
        'proPic' => "database/images/5.jpg",
      ],
      [
        'id'     => '6',
        'fname'  => 'Nirob',
        'lname'  => 'Khan',
        'class'  => '7',
        'roll'   => '1',
        'mobile' => '01262999111',
        'proPic' => "database/images/5.jpg",
      ],

    ];

    $serializeData = serialize( $students );
    file_put_contents( DB_NAME, $serializeData );
    return true;
  }

  function generateReport() {
    $serializeData = file_get_contents( DB_NAME );
    $students      = unserialize( $serializeData );
    foreach ( $students as $student ) {
      $noOfStudent = serialNo();
    ?>
<tr>
  <td><?php echo $noOfStudent; ?></td>
  <td><?php echo $student['fname']; ?></td>
  <td><?php echo $student['lname']; ?></td>
  <td><?php echo $student['class']; ?></td>
  <td><?php echo $student['roll']; ?></td>
  <td><?php echo $student['mobile']; ?></td>
  <td><?php printf( '<img src="%s" width="50" height="50">', $student['proPic'] ); ?></td>
  <?php
    if(isAdmin()):
  ?>
  <td><?php printf( '<a href="./?task=view&id=%s" class="btn btn-info"><i class="fas fa-eye"></i> View</a> <a href="./?task=edit&id=%s" class="btn btn-success"><i class="fas fa-pencil-alt"></i> Edit</a> <a href="./?task=delete&id=%s" class="delete btn btn-danger"><i
        class="fas fa-trash-alt"></i> Delete</a>', $student['id'], $student['id'], $student['id'] ); ?></td>
  <?php
    endif;
  ?>

  <?php
    if(isEditor()):
  ?>
  <td>
    <?php printf( '<a href="./?task=view&id=%s" class="btn btn-info"><i class="fas fa-eye"></i> View</a> <a href="/?task=edit&id=%s" class="btn btn-success"><i class="fas fa-pencil-alt"></i> Edit</a>', $student['id'], $student['id'] ); ?>
  </td>
  <?php
    endif;
  ?>
</tr>
<?php
  }
  }

  function serialNo() {
    static $i;
    $i = $i ?? 0;
    $i++;
    return $i;
  }

  function newStudent() {
    $serializeData = file_get_contents( DB_NAME );
    $students      = unserialize( $serializeData );
    $_id           = max( array_column( $students, 'id' ) );

    foreach ( $students as $student ) {
      $noOfStudent = serialNo();
      $count       = count( $students );

      static $studentCount;
      $studentCount = $studentCount ?? $count;
      $studentCount--;

      if ( $studentCount < ( $count - 3 ) ) {
        break;
      }
    ?>
<tr>
  <td><?php echo $noOfStudent; ?></td>
  <td><?php echo $students[$studentCount]['fname']; ?></td>
  <td><?php echo $students[$studentCount]['lname']; ?></td>
  <td><?php echo $students[$studentCount]['class']; ?></td>
  <td><?php echo $students[$studentCount]['roll']; ?></td>
  <td><?php echo $students[$studentCount]['mobile']; ?></td>
  <td><?php printf( '<img src="%s" width="50" height="50">', $students[$studentCount]['proPic'] ); ?></td>
  <?php
    if(isAdmin()):
  ?>
  <td><?php printf( '<a href="/?task=view&id=%s" class="btn btn-info"><i class="fas fa-eye"></i> View</a> <a href="/?task=edit&id=%s" class="tab-remain btn btn-success"><i class="fas fa-pencil-alt"></i> Edit</a> <a href="/?task=delete&id=%s" class="delete btn btn-danger"><i
        class="fas fa-trash-alt"></i> Delete</a>', $students[$studentCount]['id'], $students[$studentCount]['id'], $students[$studentCount]['id'] ); ?></td>
  <?php
    endif;
  ?>

  <?php
    if(isEditor()):
  ?>
  <td>
    <?php printf( '<a href="/?task=view&id=%s" class="btn btn-info"><i class="fas fa-eye"></i> View</a> <a href="/?task=edit&id=%s" class="tab-remain btn btn-success"><i class="fas fa-pencil-alt"></i> Edit</a>', $students[$studentCount]['id'], $students[$studentCount]['id'] ); ?>
  </td>
  <?php
    endif;
  ?>
</tr>

<?php
  }
  }

  // function newSerialNo() {
  //   static $i;
  //   $i = $i ?? 0;
  //   $i++;
  //   return $i;
  // }

  function totalStudent() {
    $serializeData = file_get_contents( DB_NAME );
    $students      = unserialize( $serializeData );
    return count( $students );
  }

  function addStudent( $fname, $lname, $class, $roll, $phone, $imgName ) {
    $found         = false;
    $serializeData = file_get_contents( DB_NAME );
    $students      = unserialize( $serializeData );

    foreach ( $students as $student ) {
      if ( $student['class'] == $class && $student['roll'] == $roll ) {
        $found = true;
        break;
      }
    }

    if ( !$found ) {
      $newID      = newID();
      $newStudent = [
        'id'     => $newID,
        'fname'  => $fname,
        'lname'  => $lname,
        'class'  => $class,
        'roll'   => $roll,
        'mobile' => $phone,
        'proPic' => $imgName,
      ];
      array_push( $students, $newStudent );
      $serializeData = serialize( $students );
      file_put_contents( DB_NAME, $serializeData );
      return true;
    }
    return false;
  }

  function newID() {
    $serializeData = file_get_contents( DB_NAME );
    $students      = unserialize( $serializeData );
    $_id           = max( array_column( $students, 'id' ) );
    return $_id + 1;
  }

  function getStudent( $id ) {
    $serializeData = file_get_contents( DB_NAME );
    $students      = unserialize( $serializeData );
    foreach ( $students as $student ) {
      if ( $id == $student['id'] ) {
        return $student;
      }
    }
    return false;
  }

  function updateStudent( $id, $fname, $lname, $class, $roll, $phone, $imgName ) {
    $found         = false;
    $serializeData = file_get_contents( DB_NAME );
    $students      = unserialize( $serializeData );

    foreach ( $students as $student ) {
      if ( $roll == $student['roll'] && $class == $student['class'] && $id != $student['id'] ) {
        $found = true;
        break;
      }
    }

    if ( !$found ) {
      $students[$id - 1]['fname']  = $fname;
      $students[$id - 1]['lname']  = $lname;
      $students[$id - 1]['class']  = $class;
      $students[$id - 1]['roll']   = $roll;
      $students[$id - 1]['mobile'] = $phone;
      if ( $_FILES['pro-pic']['size'] != 0 ) {
        $students[$id - 1]['proPic'] = $imgName;
      }

      $serializeData = serialize( $students );
      file_put_contents( DB_NAME, $serializeData );
      return true;
    }
    return false;
  }

  function deleteStudent( $id ) {
    $serializeData = file_get_contents( DB_NAME );
    $students      = unserialize( $serializeData );
    unset( $students[$id - 1] );
    $serializeData = serialize( $students );
    file_put_contents( DB_NAME, $serializeData );
    return true;
  }

  function getSearchStudent( $class, $roll ) {
    $serializeData = file_get_contents( DB_NAME );
    $students      = unserialize( $serializeData );
    foreach ( $students as $student ) {
      if ( $class == $student['class'] && $roll == $student['roll'] ) {
        return $student;
      }
    }
    return false;
}

function isAdmin(){
  if('admin'==$_SESSION['role']){
    return true;
  }
  return false;
}

function isEditor(){
  if('editor'==$_SESSION['role']){
    return true;
  }
  return false;
}