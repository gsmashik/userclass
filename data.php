<?php include("Database.php");?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
  <button onclick="printDiv('table')" ></button>  

  <table class="table table-light" id="table">
      <thead class="thead-light">
          <tr>
          <th>Id</th>
              <th>name</th>
          </tr>
      </thead>
      <tbody>
          <?php
          $crud = new Crud();

          ///////////// Get Data ///////////////////////////////
          $users = $crud->getRows('user',array('order_by'=>'id DESC',"select" => 'usercode')); // Get Row Function Check

          if(!empty($users)){ $count = 0; foreach($users as $user){ $count++;?>
            <tr>
                <td><?php echo $count; ?></td>
          
                <td><?php echo $user['usercode']; ?></td>
         
          
                </tr>
            <?php } }   
            //////////////////////// Delete Function Check  /////////////////// ?>
        <?php $crud->delete('user',array("usercode" => "olee"));  

        ////////////////// Check Fata Was Deleted ///////////////////////
        $condition = array('usercode' => "olee" ) ;
        $user_data = array("email" => "gsm@gmail.com","age" => 26);
        $crud->update("user",$user_data ,$condition);
        //////////////////// Insert DataCheck ////////////////
///////////////////// Check Insert Function /////////////////
        $insert_data = array("usercode"=>"dgdg","email" => "oitguj@gmail.com");
        $crud->insert("user",$insert_data);

        ?>   
      </tbody>
      <tfoot>
          <tr>
              <th>#</th>
          </tr>
      </tfoot>
  </table>

  <script>
  function printDiv(dividName) {
     var printContents = document.getElementById(dividName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;

}
  </script>
</body>
</html>