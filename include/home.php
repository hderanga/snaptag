<html>
    <head>
        <title>Home page</title>
    </head>
  <body>
      <h3>Categories</h3>
      <br />
      <?php
$data = category::FindAll(null,"catname", array(),  "A",  0,  0, array(_ACTIVE));
foreach ($data as $data_obj){
    $catname=$data_obj->getCatname();
    ?> 
    <table>
    <tr>
    <td><?= $catname; ?></td>
    </tr>
    </table>
        
<?php } ?>
      <br />
    <a href="index.php?page=addItems">Post stuffs</a>
  </body  
</html>