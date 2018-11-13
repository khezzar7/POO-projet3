<?php
$search = null;
$category_id = null;


try {

  $pdo = new PDO('mysql:host=localhost;dbname=poo_projet3', 'root','',$options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
} catch (PDOException $e) {
  var_dump($e);
}
//Récupètation des catégories triées par nom croissant ASC (DESC pour décroissant)
$query= $pdo->prepare('SELECT * FROM category ORDER BY name asc');
$query->execute();
$categories = $query->fetchAll(PDO::FETCH_OBJ);


// if(isset($_GET['submit'])){
//   $search = $_GET['search'];
//   //la clause LIKE permet de faire une recherche full text
//   $query = $pdo->prepare('SELECT * FROM proverb WHERE body LIKE :search');
//   $query->execute(['search' => '%'.$search.'%']);
//   $rows = $query->FetchAll(PDO::FETCH_OBJ);
// //  var_dump($rows);
// }
//Récupérer les proverbes liés à la catégorie séléctionné
$category_id= intval($_GET['category']);
if(isset($_GET['category'])) $category_id= $_GET['category'];
 $search ='';
 if(isset($_GET['search'])) $search = $_GET['search'];


 if($category_id === 0){
   //l'utilisateur n'a choisi aucune categorie sélectionné
   //on ne filtre pas par categorie
   $sq = 'SELECT * FROM proverb WHERE body LIKE :search';
   $query = $pdo->prepare($sq);
 $query->execute(['search' => '%'.$search.'%']);
 }

 else {
   $sq='SELECT proverb.id AS proverb_id, body FROM proverb_category
   JOIN proverb ON proverb.id = proverb_category.proverb_id
   WHERE category_id = :category_id AND body LIKE :search';
    $query = $pdo->prepare($sq);
   $query->execute([
     ':category_id'=>$category_id,
     ':search'=>'%'.$search.'%'
   ]);
 }//fin du if else
$rows = $query->fetchAll(PDO::FETCH_OBJ);
//var_dump($rows);
  ?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Projet3</title>
  </head>
  <body>
    <h1>Projet 3</h1>
    <form>
      <?php if(isset($_GET['submit'])):?>
        <input type="text" name="search" value="<?php echo $_GET['search']?>">
      <?php else:?>
        <input type="text" name="search" value="">
      <?php endif;?>

      <select name="category">
        <option value="0">Choisir une catégorie</option>
        <?php foreach($categories as $cat):?>
          <option <?php if($cat->id ==$category_id) echo'selected';?>
            value="<?php echo $cat->id;?>">
            <?php echo ucfirst( $cat->name);?>
          </option>
        <?php endforeach;?>
      </select>
      <input type="submit" name="submit">
    </form>
    <?php if(isset($rows) && sizeof($rows)> 0):?>
      <h2>
        <strong><?php echo sizeof($rows);?></strong> Proverbe(s) trouvé(s) pour:
        <strong>
          <?php echo $search?>
        </strong></h2>
      <?php foreach($rows as $row):?>
      <article><?php echo $row->body;?></article>
      <?php endforeach ?>
    <?php else:?>
      <h2>Auncun proverbe trouvé pour:<strong><?php echo $search?></strong></h2>
    <?php endif;?>
  </body>
</html>
